<style>
    :root {
        color-scheme: light;
        --canvas: #f4f6f3;
        --surface: #ffffff;
        --surface-soft: #f8faf8;
        --ink: #1c2925;
        --muted: #687670;
        --line: #e1e8e3;
        --line-strong: #d1dcd5;
        --brand: #176b55;
        --brand-dark: #105541;
        --brand-mark: #176b55;
        --brand-on: #ffffff;
        --brand-soft: #e9f3ee;
        --success: #176548;
        --success-soft: #e9f4ed;
        --success-hover: #12543c;
        --danger: #a44343;
        --danger-soft: #fbefee;
        --warning: #8a651c;
        --warning-soft: #f8f2e5;
        --nav-ink: #58665f;
        --line-soft: #edf1ee;
        --field-bg: #ffffff;
        --chart-expense: #d48a82;
        --progress-track: #e9efeb;
        --attention-ink: #775a26;
        --attention-border: #ebdfc2;
        --empty-ink: #77847d;
        --empty-title: #34443c;
        --table-ink: #34423b;
        --field-label: #526159;
        --surface-shadow: #173b2c0a;
        --sidebar-shadow: #172b211a;
        --sidebar-overlay: #17231f66;
        --radius: 12px;
    }

    html[data-theme="dark"] {
        color-scheme: dark;
        --canvas: #111714;
        --surface: #19211d;
        --surface-soft: #202a24;
        --ink: #e6eee8;
        --muted: #a2afa7;
        --line: #303b34;
        --line-strong: #46544b;
        --brand: #81c6a5;
        --brand-dark: #a0d8ba;
        --brand-mark: #216b50;
        --brand-on: #10251a;
        --brand-soft: #263b30;
        --success: #8bd0a5;
        --success-soft: #20382a;
        --success-hover: #72b88e;
        --danger: #eea19b;
        --danger-soft: #402927;
        --warning: #dfc17e;
        --warning-soft: #3b3425;
        --nav-ink: #c0cbc4;
        --line-soft: #2a342e;
        --field-bg: #131a16;
        --chart-expense: #d18f87;
        --progress-track: #303b34;
        --attention-ink: #e2c98f;
        --attention-border: #53472c;
        --empty-ink: #a4b0a8;
        --empty-title: #e4ece6;
        --table-ink: #d3ddd6;
        --field-label: #bdc9c1;
        --surface-shadow: #00000030;
        --sidebar-shadow: #00000066;
        --sidebar-overlay: #00000099;
    }

    *, *::before, *::after { box-sizing: border-box; }
    body { margin: 0; color: var(--ink); font: 14px/1.5 Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; -webkit-font-smoothing: antialiased; }
    button, input, select, textarea { font: inherit; }
    a { color: inherit; text-decoration: none; }
    button:focus-visible, a:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible, summary:focus-visible { outline: 3px solid #176b5540; outline-offset: 2px; }

    .app-body { min-height: 100vh; background: var(--canvas); }
    .shell { min-height: 100vh; }
    .sidebar { position: fixed; z-index: 20; inset: 0 auto 0 0; display: flex; width: 252px; flex-direction: column; padding: 24px 16px 18px; overflow-y: auto; background: var(--surface); border-right: 1px solid var(--line); }
    .brand { display: inline-flex; align-items: center; gap: 10px; padding: 2px 10px 27px; color: var(--ink); font-size: 18px; font-weight: 750; letter-spacing: -.6px; }
    .brand-mark { display: grid; width: 34px; height: 34px; place-items: center; color: white; background: var(--brand-mark); border-radius: 10px; }
    .brand-mark svg { width: 18px; height: 18px; }
    .brand-name-accent { color: var(--brand); }
    .nav-label { padding: 16px 12px 8px; color: var(--muted); font-size: 10px; font-weight: 750; letter-spacing: 1.2px; text-transform: uppercase; }
    .nav { display: grid; gap: 3px; }
    .nav a { display: flex; min-height: 41px; align-items: center; gap: 11px; padding: 0 11px; color: var(--nav-ink); border-radius: 8px; font-size: 13px; font-weight: 600; transition: background .15s ease, color .15s ease; }
    .nav a:hover { color: var(--ink); background: var(--surface-soft); }
    .nav a.active { color: var(--brand-dark); background: var(--brand-soft); }
    .nav a svg { width: 17px; height: 17px; flex: 0 0 auto; stroke-width: 1.8; }
    .nav a.active svg { color: var(--brand); }
    .sidebar-foot { margin-top: auto; padding-top: 22px; }
    .class-chip { margin: 0 3px 15px; padding: 12px; background: var(--surface-soft); border: 1px solid var(--line); border-radius: 9px; }
    .class-chip small, .user-meta small { display: block; color: var(--muted); font-size: 11px; }
    .class-chip strong { display: block; margin-top: 3px; font-size: 13px; }
    .profile { display: flex; align-items: center; gap: 10px; padding: 13px 5px 0; border-top: 1px solid var(--line); }
    .avatar { display: grid; width: 35px; height: 35px; flex: 0 0 auto; place-items: center; color: var(--brand-dark); background: var(--brand-soft); border-radius: 9px; font-size: 13px; font-weight: 700; }
    .profile .user-meta { min-width: 0; flex: 1; }
    .user-meta strong { display: block; overflow: hidden; font-size: 12px; text-overflow: ellipsis; white-space: nowrap; }
    .main { width: min(100% - 252px, 1560px); min-height: 100vh; margin-left: 252px; padding: 0 38px 48px; }
    .topbar { display: flex; min-height: 92px; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 25px; border-bottom: 1px solid var(--line); }
    .topbar-leading { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .crumb { margin-bottom: 3px; color: var(--muted); font-size: 11px; }
    .page-title { margin: 0; font-size: 21px; font-weight: 720; letter-spacing: -.55px; line-height: 1.2; }
    .top-actions { display: flex; align-items: center; gap: 9px; }
    .date-pill { padding: 8px 11px; color: var(--nav-ink); background: var(--surface); border: 1px solid var(--line); border-radius: 8px; font-size: 11px; }
    .icon-button { display: grid; width: 37px; height: 37px; place-items: center; color: var(--nav-ink); background: var(--surface); border: 1px solid var(--line); border-radius: 8px; cursor: pointer; }
    .icon-button:hover { color: var(--brand); border-color: var(--line-strong); }
    .theme-toggle { display: grid; width: 37px; height: 37px; flex: 0 0 auto; place-items: center; color: var(--nav-ink); background: var(--surface); border: 1px solid var(--line); border-radius: 8px; cursor: pointer; }
    .theme-toggle:hover { color: var(--brand); border-color: var(--line-strong); }
    .theme-icon { width: 17px; height: 17px; }
    .theme-icon-sun { display: none; }
    html[data-theme="dark"] .theme-icon-moon { display: none; }
    html[data-theme="dark"] .theme-icon-sun { display: block; }
    .menu-toggle, .sidebar-backdrop { display: none; }
    .sidebar-backdrop { padding: 0; border: 0; cursor: pointer; }
    .welcome { display: flex; align-items: center; justify-content: space-between; gap: 18px; margin: 0 0 22px; }
    .welcome p { margin: 0; color: var(--muted); font-size: 13px; }

    .button { display: inline-flex; min-height: 38px; align-items: center; justify-content: center; gap: 7px; padding: 8px 13px; color: var(--brand-on); background: var(--brand); border: 1px solid var(--brand); border-radius: 8px; font-size: 12px; font-weight: 680; cursor: pointer; transition: background .15s ease, border-color .15s ease; }
    .button:hover { background: var(--brand-dark); border-color: var(--brand-dark); }
    .button.secondary { color: var(--ink); background: var(--surface); border-color: var(--line-strong); }
    .button.secondary:hover { color: var(--brand-dark); background: var(--surface-soft); border-color: #adc7b9; }
    .button.danger { color: var(--danger); background: var(--surface); border-color: #e6c6c3; }
    .button.danger:hover { background: var(--danger-soft); }
    .button.green { color: var(--brand-on); background: var(--success); border-color: var(--success); }
    .button.green:hover { background: var(--success-hover); border-color: var(--success-hover); }
    .button.small { min-height: 31px; padding: 6px 9px; font-size: 11px; }
    .button:disabled { opacity: .5; cursor: not-allowed; }

    .stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 16px; }
    .stat, .panel { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); }
    .stat { min-height: 112px; padding: 16px; }
    .stat-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; color: var(--nav-ink); font-size: 11px; font-weight: 620; }
    .stat-icon { display: inline-grid; width: 29px; height: 29px; flex: 0 0 auto; place-items: center; color: var(--brand); background: var(--brand-soft); border-radius: 8px; }
    .stat-icon.green { color: var(--success); background: var(--success-soft); }
    .stat-icon.red { color: var(--danger); background: var(--danger-soft); }
    .stat-icon.amber { color: var(--warning); background: var(--warning-soft); }
    .stat-icon svg { width: 15px; height: 15px; }
    .stat-value, .amount-large { color: var(--ink); font-size: 23px; font-weight: 720; letter-spacing: -.7px; line-height: 1.2; }
    .stat-value { margin: 13px 0 4px; }
    .stat-denominator { font-size: 14px; font-weight: 500; }
    .stat-note { color: var(--muted); font-size: 10px; }
    .positive { color: var(--success) !important; }
    .negative { color: var(--danger) !important; }
    .grid-main { display: grid; grid-template-columns: minmax(0, 1.55fr) minmax(270px, 1fr); gap: 14px; margin-bottom: 15px; }
    .two-column { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
    .panel { min-width: 0; padding: 18px; }
    .panel-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 16px; }
    .panel-title { color: var(--ink); font-size: 14px; font-weight: 690; letter-spacing: -.15px; }
    .panel-sub { margin-top: 4px; color: var(--muted); font-size: 11px; line-height: 1.5; }
    .section { margin-bottom: 16px; }
    .section-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 10px; }
    .section-head h2 { margin: 0; color: var(--ink); font-size: 14px; font-weight: 700; letter-spacing: -.15px; }
    .link { color: var(--brand); font-size: 12px; font-weight: 650; }
    .link:hover { color: var(--brand-dark); text-decoration: underline; text-underline-offset: 3px; }
    .muted { color: var(--muted); }
    .activity-list { display: grid; }
    .activity-row { display: flex; min-width: 0; align-items: center; gap: 11px; padding: 11px 0; border-bottom: 1px solid var(--line-soft); }
    .activity-row:last-child { border-bottom: 0; }
    .activity-symbol { display: grid; width: 33px; height: 33px; flex: 0 0 auto; place-items: center; color: var(--brand); background: var(--brand-soft); border-radius: 9px; font-weight: 700; }
    .activity-symbol.expense { color: var(--danger); background: var(--danger-soft); }
    .activity-info { min-width: 0; flex: 1; }
    .activity-info strong { display: block; overflow: hidden; color: var(--ink); font-size: 12px; font-weight: 650; text-overflow: ellipsis; white-space: nowrap; }
    .activity-info small { display: block; margin-top: 3px; color: var(--muted); font-size: 10px; line-height: 1.45; }
    .activity-amount { color: var(--ink); font-size: 12px; font-weight: 690; text-align: right; white-space: nowrap; }
    .chart-legend { display: flex; flex-wrap: wrap; gap: 12px; color: var(--muted); font-size: 10px; }
    .legend-dot { display: inline-block; width: 7px; height: 7px; margin-right: 5px; background: var(--brand); border-radius: 50%; }
    .legend-dot.expense { background: var(--chart-expense); }
    .chart { position: relative; display: flex; height: 180px; align-items: flex-end; gap: clamp(8px, 2vw, 18px); padding: 12px 6px 0; border-bottom: 1px solid var(--line); }
    .chart::before, .chart::after { position: absolute; right: 0; left: 0; border-top: 1px solid var(--line-soft); content: ""; }
    .chart::before { top: 33%; }
    .chart::after { top: 66%; }
    .month { position: relative; z-index: 1; display: flex; height: 100%; flex: 1; align-items: flex-end; justify-content: center; gap: 4px; }
    .bar { width: min(13px, 38%); min-height: 2px; background: var(--brand); border-radius: 3px 3px 0 0; }
    .bar.expense { background: var(--chart-expense); }
    .chart-labels { display: flex; justify-content: space-around; padding-top: 8px; color: var(--muted); font-size: 10px; }
    .progress { height: 7px; overflow: hidden; background: var(--progress-track); border-radius: 8px; }
    .progress span { display: block; height: 100%; background: var(--brand); border-radius: inherit; }
    .progress-foot { display: flex; justify-content: space-between; gap: 10px; margin-top: 8px; color: var(--muted); font-size: 10px; }
    .progress-summary { display: flex; align-items: baseline; gap: 8px; margin: 18px 0 10px; }
    .progress-details { margin-top: 20px; padding-top: 15px; border-top: 1px solid var(--line); }
    .attention { display: flex; align-items: flex-start; gap: 10px; margin-top: 10px; padding: 12px 14px; color: var(--attention-ink); background: var(--warning-soft); border: 1px solid var(--attention-border); border-radius: 9px; font-size: 12px; }
    .attention strong { color: var(--attention-ink); }
    .empty { padding: 27px 15px; color: var(--empty-ink); border: 1px dashed var(--line-strong); border-radius: 9px; font-size: 12px; text-align: center; }
    .empty b { display: block; margin-bottom: 4px; color: var(--empty-title); font-size: 13px; }
    .toolbar { display: flex; align-items: center; gap: 9px; flex-wrap: wrap; }
    .field, select, textarea { width: 100%; padding: 9px 11px; color: var(--ink); background: var(--field-bg); border: 1px solid var(--line-strong); border-radius: 8px; outline: none; }
    .field:focus, select:focus, textarea:focus { border-color: #6ca68e; box-shadow: 0 0 0 3px #176b5514; }
    .field::placeholder, textarea::placeholder { color: var(--muted); }
    .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 13px; }
    .form-field label { display: block; margin-bottom: 5px; color: var(--field-label); font-size: 11px; font-weight: 650; }
    .form-field.full { grid-column: 1 / -1; }
    .form-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 14px; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; min-width: 650px; border-collapse: collapse; }
    th { padding: 10px; color: var(--muted); border-bottom: 1px solid var(--line); font-size: 9px; font-weight: 750; letter-spacing: .8px; text-align: left; text-transform: uppercase; }
    td { padding: 12px 10px; color: var(--table-ink); border-bottom: 1px solid var(--line-soft); font-size: 11px; vertical-align: middle; }
    tr:last-child td { border-bottom: 0; }
    .badge { display: inline-flex; align-items: center; padding: 4px 7px; color: var(--success); background: var(--success-soft); border-radius: 5px; font-size: 9px; font-weight: 720; white-space: nowrap; }
    .badge.red { color: var(--danger); background: var(--danger-soft); }
    .badge.yellow { color: var(--warning); background: var(--warning-soft); }
    .flash, .error { margin-bottom: 15px; padding: 11px 13px; border-radius: 8px; font-size: 12px; }
    .flash { color: var(--success); background: var(--success-soft); border: 1px solid var(--line-strong); }
    .error { margin-top: 9px; color: var(--danger); background: var(--danger-soft); border: 1px solid var(--line-strong); }
    .pagination { display: flex; justify-content: center; gap: 7px; padding-top: 16px; }
    .pagination a, .pagination span { display: inline-flex; min-width: 31px; height: 31px; align-items: center; justify-content: center; padding: 0 8px; color: var(--nav-ink); background: var(--surface); border: 1px solid var(--line); border-radius: 7px; font-size: 11px; }
    .pagination nav { display: flex; gap: 6px; }
    .pagination span[aria-current="page"] { color: var(--brand-on); background: var(--brand); border-color: var(--brand); }
    .pagination span[aria-disabled="true"] { color: var(--muted); background: var(--surface-soft); }
    .report-card { display: flex; justify-content: space-between; gap: 15px; padding: 13px 0; border-bottom: 1px solid var(--line-soft); }
    .report-card:last-child { border-bottom: 0; }
    .report-month-info { min-width: 0; }
    .report-download { display: inline-block; margin-top: 6px; }
    .report-totals { display: grid; align-content: start; gap: 4px; text-align: right; white-space: nowrap; }
    .inquiry-row { align-items: flex-start; flex-wrap: wrap; }
    .inquiry-row .activity-info { flex: 1 1 calc(100% - 48px); }
    .inquiry-message { overflow-wrap: anywhere; white-space: pre-line; }
    .inquiry-date { margin-left: auto; }
    .inquiry-form textarea { min-width: 240px; flex: 1; }
    .inline-form { display: inline; }
    .account-stack { display: grid; max-width: 900px; gap: 14px; }
    .account-form-section + .account-form-section { margin-top: 22px; padding-top: 20px; border-top: 1px solid var(--line); }
    .account-section-head { margin-bottom: 13px; }
    .account-section-head h2 { margin: 0; color: var(--ink); font-size: 13px; font-weight: 700; }
    .account-section-head p { margin: 4px 0 0; color: var(--muted); font-size: 11px; }
    .account-actions { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 19px; padding-top: 15px; border-top: 1px solid var(--line); }
    .account-utility-grid { display: grid; grid-template-columns: minmax(220px, .8fr) minmax(300px, 1.2fr); gap: 14px; }
    .account-utility-grid .panel { display: flex; flex-direction: column; align-items: flex-start; }
    .account-utility-grid .account-panel-copy { flex: 1; margin: 5px 0 15px; color: var(--muted); font-size: 11px; line-height: 1.6; }
    .account-help-form { display: grid; width: 100%; gap: 10px; }

    .guest-body { display: grid; min-height: 100vh; place-items: center; padding: 24px; color: var(--ink); background: var(--canvas); }
    .guest-card, .guest-body .card, .guest-body .login { width: min(100%, 480px); padding: clamp(24px, 5vw, 34px); background: var(--surface); border: 1px solid var(--line); border-radius: 14px; box-shadow: 0 12px 36px var(--surface-shadow); }
    .guest-body .login { width: min(100%, 420px); }
    .guest-body .register-card { width: min(100%, 560px); }
    .guest-body .brand { margin: 0 0 27px; padding: 0; }
    .guest-body .eyebrow { color: var(--brand); font-size: 10px; font-weight: 750; letter-spacing: 1px; text-transform: uppercase; }
    .guest-body h1 { margin: 7px 0 8px; color: var(--ink); font-size: 25px; font-weight: 730; letter-spacing: -.65px; line-height: 1.2; }
    .guest-body .intro, .guest-body .muted { margin: 0 0 21px; color: var(--muted); font-size: 12px; line-height: 1.6; }
    .guest-body .field { margin-bottom: 12px; }
    .guest-body .field label { display: block; margin: 0 0 5px; color: var(--field-label); font-size: 11px; font-weight: 650; }
    .guest-body .field input, .guest-body .field select { width: 100%; padding: 10px 11px; color: var(--ink); background: var(--field-bg); border: 1px solid var(--line-strong); border-radius: 8px; }
    .guest-body .field input[readonly] { color: var(--muted); background: var(--surface-soft); }
    .guest-body .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0 12px; }
    .guest-body .hidden { display: none !important; }
    .guest-body .button { width: 100%; margin-top: 5px; }
    .guest-body .error { margin: 0 0 14px; }
    .guest-body .foot { margin-top: 20px; padding-top: 15px; color: var(--muted); border-top: 1px solid var(--line); font-size: 10px; line-height: 1.65; text-align: center; }
    .guest-body .foot a, .guest-body .setup-link { color: var(--brand); font-weight: 650; }
    .guest-body .setup-link { display: block; margin-top: 14px; font-size: 11px; text-align: center; }
    .guest-body .class-box { margin-bottom: 17px; padding: 12px; background: var(--surface-soft); border: 1px solid var(--line); border-radius: 9px; }
    .guest-body .class-box small { display: block; margin-bottom: 3px; color: var(--muted); font-size: 10px; }
    .guest-body .class-box strong { font-size: 13px; }
    .guest-body .remember-field { display: flex; align-items: center; gap: 8px; color: var(--nav-ink); font-size: 11px; }
    .guest-body .remember-field input { accent-color: var(--brand); }
    .class-code { color: var(--brand); font-size: 26px; font-weight: 740; letter-spacing: 2px; }
    .guest-theme-toggle { position: fixed; top: 20px; right: 20px; }

    @media (max-width: 1100px) {
        .main { width: calc(100% - 226px); margin-left: 226px; padding-right: 24px; padding-left: 24px; }
        .sidebar { width: 226px; }
        .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-main { grid-template-columns: 1fr; }
    }
    @media (max-width: 760px) {
        .sidebar { width: min(290px, 86vw); transform: translateX(-105%); transition: transform .2s ease; box-shadow: 12px 0 32px var(--sidebar-shadow); }
        .sidebar.open { transform: translateX(0); }
        .sidebar-backdrop.open { position: fixed; z-index: 15; inset: 0; display: block; background: var(--sidebar-overlay); }
        .main { width: 100%; margin: 0; padding: 0 18px 32px; }
        .topbar { min-height: 76px; margin-bottom: 21px; }
        .menu-toggle { display: grid; }
        .page-title { font-size: 19px; }
        .crumb, .date-pill { display: none; }
        .top-actions { gap: 6px; }
        .welcome { align-items: flex-start; flex-direction: column; gap: 12px; margin-bottom: 18px; }
        .stats { gap: 9px; }
        .stat { min-height: 102px; padding: 13px; }
        .stat-value { font-size: 19px; }
        .stat-top { font-size: 10px; }
        .panel { padding: 15px; }
        .grid-main, .two-column { grid-template-columns: 1fr; gap: 10px; }
        .form-grid, .guest-body .grid { grid-template-columns: 1fr; }
        .form-field.full { grid-column: auto; }
        .progress-foot { flex-direction: column; gap: 3px; }
        .account-utility-grid { grid-template-columns: 1fr; }
        .account-actions { align-items: flex-start; flex-direction: column; }
        .report-card { align-items: flex-start; flex-direction: column; gap: 8px; }
        .report-totals { display: flex; flex-wrap: wrap; gap: 14px; text-align: left; }
    }
    @media (max-width: 420px) {
        .main { padding-right: 13px; padding-left: 13px; }
        .topbar-leading { gap: 8px; }
        .stats { grid-template-columns: 1fr 1fr; }
        .stat { padding: 11px; }
        .stat-icon { width: 25px; height: 25px; }
        .section-head { align-items: flex-start; }
        .guest-body { padding: 14px; }
        .guest-theme-toggle { top: 10px; right: 10px; }
    }
</style>
