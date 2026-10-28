<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DuesInvoice;
use App\Models\Inquiry;
use App\Models\KasClass;
use App\Models\KasPayment;
use App\Models\KasTransaction;
use App\Models\SuperAdminInvitation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KasKelasController extends Controller
{
    public function login(): mixed
    {
        return view('auth.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau kata sandi tidak cocok.'])->onlyInput('email');
        }

        if (! $request->user()->is_active) {
            Auth::logout();

            return back()->withErrors(['email' => 'Akun ini dinonaktifkan. Hubungi super admin kelas.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function register(): mixed
    {
        return view('auth.register');
    }

    public function createAccount(Request $request): RedirectResponse
    {
        $accountType = $request->validate(['account_type' => ['required', Rule::in(['super_admin', 'user'])]])['account_type'];
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:10', 'confirmed'],
        ]);

        if ($accountType === 'super_admin') {
            $auditAction = 'superadmin.mendaftar';
            $classData = $request->validate([
                'class_name' => ['required', 'string', 'max:120'],
                'monthly_dues' => ['required', 'integer', 'min:0', 'max:100000000'],
                'initial_balance' => ['required', 'integer', 'min:0', 'max:1000000000'],
            ]);
            $user = DB::transaction(function () use ($classData, $data): User {
                $kasClass = KasClass::create([
                    'name' => $classData['class_name'],
                    'class_code' => $this->generateUniqueClassCode(),
                    'monthly_dues' => $classData['monthly_dues'],
                    'initial_balance' => $classData['initial_balance'],
                ]);

                return User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'role' => 'super_admin',
                    'kas_class_id' => $kasClass->id,
                    'is_active' => true,
                ]);
            });
        } else {
            $auditAction = 'siswa.mendaftar';
            $request->merge(['class_code' => Str::upper((string) $request->input('class_code'))]);
            $studentData = $request->validate([
                'class_code' => ['required', 'string', 'size:10', Rule::exists('kas_classes', 'class_code')],
            ]);
            $kasClass = KasClass::query()->where('class_code', $studentData['class_code'])->firstOrFail();
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'student',
                'kas_class_id' => $kasClass->id,
                'is_active' => true,
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();
        $this->audit($request, $auditAction, $user);

        return redirect()->route('dashboard')->with('success', $accountType === 'super_admin' ? 'Kelas dan akun super admin berhasil dibuat.' : 'Akun berhasil dibuat dan bergabung ke kelas.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function dashboard(): mixed
    {
        $class = $this->currentClass();
        $user = request()->user();
        $transactions = $class?->transactions()->where('is_cancelled', false)->latest('occurred_on')->get() ?? collect();
        $students = $class?->members()->count() ?? 0;
        $paid = $class ? DuesInvoice::where('kas_class_id', $class->id)->where('period', now()->format('Y-m'))->whereHas('payments', fn ($query) => $query->where('status', 'verified'))->distinct('student_id')->count('student_id') : 0;
        $myPaidInvoices = $user->isSuperAdministrator() ? 0 : $user->duesInvoices()->whereHas('payments', fn ($query) => $query->where('status', 'verified'))->count();
        $myUnpaidInvoices = $user->isSuperAdministrator() ? 0 : $user->duesInvoices()->whereDoesntHave('payments', fn ($query) => $query->where('status', 'verified'))->count();
        $weekStart = now()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();
        $unpaidStudents = collect();

        if (request()->user()->isClassAdministrator() && $class) {
            $unpaidStudents = $class->members()
                ->whereDoesntHave('duesInvoices.payments', fn ($query) => $query->where('status', 'verified')->whereBetween('paid_at', [$weekStart, $weekEnd]))
                ->orderBy('name')
                ->get();
        }

        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');
        $monthly = collect(range(5, 0))->map(function (int $ago) use ($class): array {
            $month = now()->startOfMonth()->subMonths($ago);
            $query = KasTransaction::query()->where('occurred_on', '>=', $month)->where('occurred_on', '<', $month->copy()->addMonth())->where('is_cancelled', false);
            if ($class) {
                $query->where('kas_class_id', $class->id);
            } else {
                $query->whereRaw('1 = 0');
            }

            return ['label' => $month->translatedFormat('M'), 'income' => (int) (clone $query)->where('type', 'income')->sum('amount'), 'expense' => (int) (clone $query)->where('type', 'expense')->sum('amount')];
        });

        return view('app.dashboard', compact('class', 'transactions', 'students', 'paid', 'income', 'expense', 'monthly', 'unpaidStudents', 'weekStart', 'weekEnd', 'myPaidInvoices', 'myUnpaidInvoices'));
    }

    public function transactions(Request $request): mixed
    {
        $query = $this->currentClass()?->transactions()->with('creator')->latest('occurred_on');
        if ($query) {
            $query->when($request->filled('q'), fn ($q) => $q->where(fn ($inner) => $inner->where('description', 'like', '%'.$request->string('q').'%')->orWhere('category', 'like', '%'.$request->string('q').'%')))
                ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
                ->when($request->filled('month'), fn ($q) => $q->whereMonth('occurred_on', substr($request->string('month'), 5, 2))->whereYear('occurred_on', substr($request->string('month'), 0, 4)));
        }
        $transactions = $query?->paginate(12)->withQueryString() ?? collect();

        return view('app.transactions', compact('transactions'));
    }

    public function storeTransaction(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['income', 'expense'])], 'category' => ['required', 'string', 'max:80'],
            'description' => ['required', 'string', 'max:255'], 'amount' => ['required', 'integer', 'min:1', 'max:1000000000'],
            'occurred_on' => ['required', 'date'], 'receipt' => ['nullable', 'image', 'max:5120'],
        ]);
        $class = $this->currentClass();
        abort_unless($class, 422, 'Kelas belum disiapkan.');
        $path = $request->file('receipt')?->store('bukti-transaksi', 'local');
        unset($data['receipt']);
        $transaction = KasTransaction::create([...$data, 'kas_class_id' => $class->id, 'created_by' => $request->user()->id, 'receipt_path' => $path]);
        $this->audit($request, 'transaksi.dibuat', $transaction, ['nominal' => $transaction->amount]);

        return back()->with('success', 'Transaksi berhasil dicatat.');
    }

    public function cancelTransaction(Request $request, KasTransaction $transaction): RedirectResponse
    {
        abort_unless($transaction->kas_class_id === $request->user()->kas_class_id, 404);
        $data = $request->validate(['reason' => ['required', 'string', 'min:5', 'max:500']]);
        abort_if($transaction->is_cancelled, 422, 'Transaksi sudah dibatalkan.');
        $transaction->update(['is_cancelled' => true, 'cancellation_reason' => $data['reason']]);
        $this->audit($request, 'transaksi.dibatalkan', $transaction, ['alasan' => $data['reason']]);

        return back()->with('success', 'Transaksi dibatalkan dan tetap tercatat dalam riwayat audit.');
    }

    public function updateTransaction(Request $request, KasTransaction $transaction): RedirectResponse
    {
        abort_unless($transaction->kas_class_id === $request->user()->kas_class_id && ! $transaction->is_cancelled, 404);
        $data = $request->validate([
            'type' => ['required', Rule::in(['income', 'expense'])], 'category' => ['required', 'string', 'max:80'],
            'description' => ['required', 'string', 'max:255'], 'amount' => ['required', 'integer', 'min:1', 'max:1000000000'], 'occurred_on' => ['required', 'date'],
            'reason' => ['required', 'string', 'min:5', 'max:500'], 'receipt' => ['nullable', 'image', 'max:5120'],
        ]);
        $before = $transaction->only(['type', 'category', 'description', 'amount', 'occurred_on']);
        $updates = collect($data)->except(['reason', 'receipt'])->all();
        if ($request->file('receipt')) {
            $updates['receipt_path'] = $request->file('receipt')->store('bukti-transaksi', 'local');
        }
        $transaction->update($updates);
        $this->audit($request, 'transaksi.diedit', $transaction, ['alasan' => $data['reason'], 'sebelum' => $before, 'sesudah' => $transaction->only(array_keys($before))]);

        return back()->with('success', 'Transaksi berhasil diperbarui dan perubahan tercatat di audit.');
    }

    public function payments(): mixed
    {
        $class = $this->currentClass();
        $members = $class?->members()->withCount([
            'duesInvoices',
            'duesInvoices as paid_dues_count' => fn ($query) => $query->whereHas('payments', fn ($paymentQuery) => $paymentQuery->where('status', 'verified')),
            'duesInvoices as unpaid_dues_count' => fn ($query) => $query->whereDoesntHave('payments', fn ($paymentQuery) => $paymentQuery->where('status', 'verified')),
        ])->orderBy('name')->get() ?? collect();
        $invoices = $class ? DuesInvoice::with(['student', 'payments' => fn ($query) => $query->latest('id')])->where('kas_class_id', $class->id)->latest()->paginate(12) : collect();

        return view('app.payments', compact('class', 'members', 'invoices'));
    }

    public function classAdmins(): mixed
    {
        $class = $this->currentClass();
        $members = $class?->members()->orderBy('role')->orderBy('name')->get() ?? collect();

        return view('app.class-admins', compact('class', 'members'));
    }

    public function setMemberRole(Request $request, User $user): RedirectResponse
    {
        abort_unless(in_array($user->role, ['admin', 'student'], true) && $user->kas_class_id === $request->user()->kas_class_id, 404);
        $data = $request->validate(['role' => ['required', Rule::in(['admin', 'student'])]]);
        abort_if(! $user->is_active && $data['role'] === 'admin', 422, 'Aktifkan akun user sebelum menjadikannya admin.');
        abort_if($user->role === $data['role'], 422, 'User sudah memiliki peran tersebut.');
        $previousRole = $user->role;
        $user->update(['role' => $data['role']]);
        $this->audit($request, $data['role'] === 'admin' ? 'admin.ditambahkan' : 'admin.dicabut', $user, ['peran_sebelumnya' => $previousRole, 'peran_baru' => $data['role']]);

        return back()->with('success', $data['role'] === 'admin' ? 'User kelas berhasil dijadikan admin.' : 'Hak admin dicabut; akun tetap menjadi user kelas.');
    }

    public function regenerateClassCode(Request $request): RedirectResponse
    {
        $class = $this->currentClass();
        abort_unless($class, 422, 'Kelas belum disiapkan.');
        $class->update(['class_code' => $this->generateUniqueClassCode($class->id)]);
        $this->audit($request, 'kelas.kode_diganti', $class);

        return back()->with('success', 'Kode kelas baru dibuat. Kode sebelumnya tidak berlaku lagi.');
    }

    public function inviteSuperAdmin(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255', 'unique:users,email']]);
        $class = $this->currentClass();
        abort_unless($class, 422, 'Kelas belum disiapkan.');
        $email = Str::lower($data['email']);
        abort_if(User::query()->whereRaw('LOWER(email) = ?', [$email])->exists(), 422, 'Email tersebut sudah memiliki akun.');
        $token = Str::random(64);
        $invitation = SuperAdminInvitation::create([
            'kas_class_id' => $class->id,
            'invited_by' => $request->user()->id,
            'email' => $email,
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addDay(),
        ]);
        $this->audit($request, 'superadmin.diundang', $invitation, ['email' => $invitation->email]);

        return back()
            ->with('success', 'Undangan super admin dibuat. Bagikan tautan ini hanya kepada penerima yang dituju.')
            ->with('invited_email', $invitation->email)
            ->with('registration_url', route('super-admin.register', $token));
    }

    public function showSuperAdminRegistration(string $token): mixed
    {
        $invitation = $this->activeSuperAdminInvitation($token);

        return view('auth.register-super-admin', compact('invitation', 'token'));
    }

    public function registerSuperAdmin(Request $request, string $token): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'password' => ['required', 'string', 'min:10', 'confirmed'],
        ]);
        $invitation = $this->activeSuperAdminInvitation($token);
        $user = DB::transaction(function () use ($invitation, $data): User {
            $lockedInvitation = SuperAdminInvitation::query()->lockForUpdate()->findOrFail($invitation->id);
            abort_if($lockedInvitation->accepted_at || $lockedInvitation->expires_at->isPast(), 404);
            abort_if(User::where('email', $lockedInvitation->email)->exists(), 422, 'Email undangan sudah memiliki akun.');

            $user = User::create([
                'name' => $data['name'],
                'email' => $lockedInvitation->email,
                'password' => $data['password'],
                'role' => 'super_admin',
                'kas_class_id' => $lockedInvitation->kas_class_id,
                'is_active' => true,
            ]);
            $lockedInvitation->update(['accepted_at' => now()]);

            return $user;
        });
        Auth::login($user);
        $request->session()->regenerate();
        $this->audit($request, 'superadmin.mendaftar', $user);

        return redirect()->route('dashboard')->with('success', 'Akun super admin berhasil dibuat untuk kelas Anda.');
    }

    public function updateClassAdmin(Request $request, User $user): RedirectResponse
    {
        $this->authorizeClassAdmin($request, $user);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:10', 'confirmed'],
        ]);
        $before = $user->only(['name', 'email']);
        $updates = ['name' => $data['name'], 'email' => $data['email']];
        if (! empty($data['password'])) {
            $updates['password'] = Hash::make($data['password']);
        }
        $user->update($updates);
        $this->audit($request, 'admin.diperbarui', $user, ['sebelum' => $before, 'sesudah' => $user->only(['name', 'email'])]);

        return back()->with('success', 'Data admin berhasil diperbarui.');
    }

    public function toggleClassAdmin(Request $request, User $user): RedirectResponse
    {
        $this->authorizeClassAdmin($request, $user);
        $data = $request->validate(['is_active' => ['required', 'boolean']]);
        $user->update(['is_active' => $data['is_active']]);
        $this->audit($request, $data['is_active'] ? 'admin.diaktifkan' : 'admin.dinonaktifkan', $user);

        return back()->with('success', $data['is_active'] ? 'Akun admin diaktifkan.' : 'Akun admin dinonaktifkan.');
    }

    public function generateInvoices(Request $request): RedirectResponse
    {
        $data = $request->validate(['period' => ['required', 'date_format:Y-m'], 'amount' => ['required', 'integer', 'min:1', 'max:100000000'], 'due_on' => ['nullable', 'date']]);
        $class = $this->currentClass();
        foreach ($class->members()->get() as $member) {
            DuesInvoice::firstOrCreate(['student_id' => $member->id, 'period' => $data['period']], ['kas_class_id' => $class->id, 'amount' => $data['amount'], 'due_on' => $data['due_on'] ?? null]);
        }
        $class->update(['monthly_dues' => $data['amount']]);
        $this->audit($request, 'tagihan.dibuat', $class, ['periode' => $data['period'], 'nominal' => $data['amount']]);

        return back()->with('success', 'Tagihan periode berhasil dibuat untuk anggota kelas ini.');
    }

    public function recordPayment(Request $request, DuesInvoice $invoice): RedirectResponse
    {
        abort_unless($invoice->kas_class_id === $request->user()->kas_class_id, 404);

        DB::transaction(function () use ($invoice, $request): void {
            $lockedInvoice = DuesInvoice::query()->with('student')->lockForUpdate()->findOrFail($invoice->id);
            abort_if($lockedInvoice->payments()->where('status', 'verified')->exists(), 422, 'Tagihan ini sudah tercatat lunas.');

            $payment = $lockedInvoice->payments()->where('status', 'pending')->lockForUpdate()->first();

            if ($payment) {
                $payment->update([
                    'verified_by' => $request->user()->id,
                    'status' => 'verified',
                    'paid_at' => now(),
                ]);
            } else {
                $payment = KasPayment::create([
                    'dues_invoice_id' => $lockedInvoice->id,
                    'student_id' => $lockedInvoice->student_id,
                    'verified_by' => $request->user()->id,
                    'amount' => $lockedInvoice->amount,
                    'status' => 'verified',
                    'paid_at' => now(),
                ]);
            }

            KasTransaction::create([
                'kas_class_id' => $lockedInvoice->kas_class_id,
                'created_by' => $request->user()->id,
                'type' => 'income',
                'category' => 'Iuran anggota',
                'description' => 'Pembayaran iuran '.$lockedInvoice->period.' — '.$lockedInvoice->student->name,
                'amount' => $payment->amount,
                'occurred_on' => today(),
            ]);

            $this->audit($request, 'pembayaran.dicatat', $payment, ['nominal' => $payment->amount]);
        });

        return back()->with('success', 'Pembayaran kas berhasil dicatat dan ditambahkan ke pemasukan.');
    }

    public function myDues(Request $request): mixed
    {
        $invoices = DuesInvoice::with(['payments' => fn ($query) => $query->latest('id')])->where('student_id', $request->user()->id)->latest()->get();

        return view('app.my-dues', compact('invoices'));
    }

    public function report(Request $request): mixed
    {
        $class = $this->currentClass();
        $transactions = $class?->transactions()->where('is_cancelled', false)->orderBy('occurred_on')->get() ?? collect();
        $months = $transactions->groupBy(fn ($item) => $item->occurred_on->format('Y-m'));

        return view('app.reports', compact('class', 'transactions', 'months'));
    }

    public function export(Request $request): StreamedResponse
    {
        $month = $request->validate(['month' => ['nullable', 'date_format:Y-m']])['month'] ?? null;
        $query = $this->currentClass()?->transactions()->with('creator')->where('is_cancelled', false)->orderBy('occurred_on');

        if ($query && $month) {
            $monthStart = Carbon::createFromFormat('!Y-m', $month);
            $query->where('occurred_on', '>=', $monthStart->toDateString())
                ->where('occurred_on', '<', $monthStart->copy()->addMonth()->toDateString());
        }

        $transactions = $query?->get() ?? collect();
        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');
        $periodLabel = $month ? Carbon::createFromFormat('!Y-m', $month)->translatedFormat('F Y') : 'Semua periode';

        return response()->streamDownload(function () use ($transactions, $income, $expense, $periodLabel): void {
            $out = fopen('php://output', 'w');
            $safeSpreadsheetValue = static fn (string $value): string => preg_match('/^\s*[=+\-@]/u', $value) ? "'".$value : $value;
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Rekap kas', $periodLabel], ';');
            fputcsv($out, ['Total pemasukan (Rp)', $income], ';');
            fputcsv($out, ['Total pengeluaran (Rp)', $expense], ';');
            fputcsv($out, ['Saldo bersih (Rp)', $income - $expense], ';');
            fputcsv($out, ['Jumlah transaksi', $transactions->count()], ';');
            fputcsv($out, [], ';');
            fputcsv($out, ['Tanggal', 'Jenis', 'Kategori', 'Deskripsi', 'Nominal (Rp)', 'Dicatat oleh'], ';');
            foreach ($transactions as $transaction) {
                fputcsv($out, [
                    $transaction->occurred_on->format('Y-m-d'),
                    $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                    $safeSpreadsheetValue($transaction->category),
                    $safeSpreadsheetValue($transaction->description),
                    $transaction->amount,
                    $safeSpreadsheetValue($transaction->creator->name),
                ], ';');
            }
            fclose($out);
        }, 'laporan-kas-'.($month ?? now()->format('Y-m-d')).'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function activity(): mixed
    {
        $logs = AuditLog::with('user')->where('kas_class_id', request()->user()->kas_class_id)->latest()->paginate(20);

        return view('app.activity', compact('logs'));
    }

    public function inquiries(): mixed
    {
        $inquiries = Inquiry::query()
            ->with('user')
            ->where('kas_class_id', request()->user()->kas_class_id)
            ->latest()
            ->paginate(15);

        return view('app.inquiries', compact('inquiries'));
    }

    public function account(): mixed
    {
        return view('app.account');
    }

    public function updateAccount(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', Rule::unique('users')->ignore($request->user()->id)], 'password' => ['nullable', 'string', 'min:10', 'confirmed']]);
        $updates = ['name' => $data['name'], 'email' => $data['email']];
        if (! empty($data['password'])) {
            $updates['password'] = Hash::make($data['password']);
        }
        $request->user()->update($updates);
        $this->audit($request, 'akun.diperbarui', $request->user());

        return back()->with('success', 'Pengaturan akun berhasil diperbarui.');
    }

    public function storeInquiry(Request $request): RedirectResponse
    {
        $data = $request->validate(['message' => ['required', 'string', 'min:5', 'max:2000']]);
        $class = $this->currentClass();
        if ($class) {
            Inquiry::create(['kas_class_id' => $class->id, 'user_id' => $request->user()->id, 'message' => $data['message']]);
        }

        return back()->with('success', 'Pertanyaan atau laporan terkirim kepada bendahara.');
    }

    public function receipt(Request $request, string $kind, int $id): mixed
    {
        abort_unless(in_array($kind, ['transaksi', 'pembayaran'], true), 404);
        $path = $kind === 'transaksi'
            ? KasTransaction::where('kas_class_id', $request->user()->kas_class_id)->findOrFail($id)->receipt_path
            : $this->authorizedPayment($request, $id)->receipt_path;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path));
    }

    private function authorizedPayment(Request $request, int $id): KasPayment
    {
        $payment = KasPayment::with('duesInvoice')->findOrFail($id);
        abort_unless($request->user()->isClassAdministrator() && $payment->duesInvoice->kas_class_id === $request->user()->kas_class_id, 404);

        return $payment;
    }

    private function currentClass(): ?KasClass
    {
        return KasClass::find(request()->user()?->kas_class_id);
    }

    private function authorizeClassAdmin(Request $request, User $user): void
    {
        abort_unless($user->role === 'admin' && $user->kas_class_id === $request->user()->kas_class_id, 404);
    }

    private function activeSuperAdminInvitation(string $token): SuperAdminInvitation
    {
        $invitation = SuperAdminInvitation::query()->with('kasClass')->where('token_hash', hash('sha256', $token))->firstOrFail();
        abort_if($invitation->accepted_at || $invitation->expires_at->isPast(), 404);

        return $invitation;
    }

    private function generateUniqueClassCode(?int $ignoreClassId = null): string
    {
        do {
            $code = Str::upper(Str::random(10));
            $query = KasClass::query()->where('class_code', $code);
            if ($ignoreClassId !== null) {
                $query->where('id', '!=', $ignoreClassId);
            }
        } while ($query->exists());

        return $code;
    }

    private function audit(Request $request, string $action, mixed $subject, array $details = []): void
    {
        AuditLog::create(['user_id' => $request->user()->id, 'kas_class_id' => $request->user()->kas_class_id, 'action' => $action, 'subject_type' => is_object($subject) ? $subject::class : null, 'subject_id' => $subject->id ?? null, 'details' => $details]);
    }
}
