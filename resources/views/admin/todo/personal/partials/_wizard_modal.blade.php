<style>
    :root {
        --wiz-dark: #16324f;
        --wiz-green: #74b91f;
        --wiz-bg: #f8fafc;
        --wiz-border: #e2e8f0;
        --wiz-text: #334155;
        --wiz-muted: #64748b;
        --wiz-danger: #ef4444;
        --wiz-warning: #f59e0b;
    }

    #customBladeWizard {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(22, 50, 79, 0.72);
        z-index: 1050;
        backdrop-filter: blur(4px);
        font-family: Arial, sans-serif;
    }

    .wiz-wrapper {
        display: flex;
        flex-direction: column;
        width: 98vw;
        max-width: 1640px;
        height: 94vh;
        margin: 3vh auto;
        background: #fff;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    }

    .wiz-header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 24px;
        background: #fff;
        border-bottom: 1px solid var(--wiz-border);
        gap: 18px;
    }
    .wiz-header-top h2 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 900;
        color: var(--wiz-dark);
        white-space: nowrap;
    }

    .wiz-progress-container { flex: 1; margin: 0 10px; }
    .wiz-progress-labels {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        font-weight: bold;
        color: var(--wiz-muted);
        margin-bottom: 5px;
        text-transform: uppercase;
    }
    .wiz-progress-bar {
        width: 100%;
        height: 8px;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
    }
    .wiz-progress-fill {
        height: 100%;
        background: var(--wiz-green);
        width: 0%;
        transition: width 0.35s ease;
    }

    .wiz-body {
        display: flex;
        flex: 1;
        overflow: hidden;
        background: var(--wiz-bg);
    }

    .wiz-sidebar-left {
        width: 265px;
        background: #fff;
        border-right: 1px solid var(--wiz-border);
        padding: 15px;
        overflow-y: auto;
    }

    .wiz-nav-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        text-align: left;
        padding: 12px;
        background: transparent;
        border: 1px solid transparent;
        border-radius: 12px;
        color: var(--wiz-muted);
        cursor: pointer;
        transition: all 0.2s;
        margin-bottom: 6px;
    }
    .wiz-nav-btn:hover { background: #f1f5f9; }
    .wiz-nav-btn.active {
        background: rgba(116, 185, 31, 0.1);
        border-color: rgba(116, 185, 31, 0.32);
        color: var(--wiz-dark);
    }
    .wiz-nav-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        border-radius: 8px;
        font-weight: bold;
        flex-shrink: 0;
    }
    .wiz-nav-btn.active .wiz-nav-icon {
        background: var(--wiz-green);
        color: #fff;
    }
    .wiz-nav-text { font-size: 13px; font-weight: 800; line-height: 1.2; }
    .wiz-nav-sub { font-size: 10px; font-weight: normal; opacity: 0.8; margin-top: 2px; }

    .wiz-main-center {
        flex: 1;
        padding: 26px;
        overflow-y: auto;
    }

    .wiz-step-container { display: none; }
    .wiz-step-container.active { display: block; }

    .wiz-section {
        background: #fff;
        border: 1px solid var(--wiz-border);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .wiz-section-title {
        font-size: 15px;
        font-weight: 800;
        color: var(--wiz-dark);
        margin-bottom: 18px;
        border-bottom: 1px solid var(--wiz-border);
        padding-bottom: 10px;
        text-transform: uppercase;
    }
    .wiz-section-subtitle {
        font-size: 12px;
        font-weight: 800;
        color: var(--wiz-green);
        margin: 20px 0 12px;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .wiz-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 16px;
    }
    .wiz-grid-2 { grid-template-columns: repeat(2, 1fr); }
    .wiz-grid-3 { grid-template-columns: repeat(3, 1fr); }
    .wiz-grid-4 { grid-template-columns: repeat(4, 1fr); }

    .wiz-form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .wiz-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--wiz-dark);
    }
    .wiz-hint {
        font-size: 10px;
        color: var(--wiz-muted);
        margin-top: -2px;
    }

    .wiz-input,
    .wiz-select,
    .wiz-textarea {
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 13px;
        color: var(--wiz-text);
        width: 100%;
        background: #fff;
    }
    .wiz-textarea {
        min-height: 90px;
        resize: vertical;
    }
    .wiz-input:focus,
    .wiz-select:focus,
    .wiz-textarea:focus {
        outline: none;
        border-color: var(--wiz-green);
        box-shadow: 0 0 0 3px rgba(116, 185, 31, 0.2);
    }

    .wiz-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .wiz-chip {
        position: relative;
        cursor: pointer;
    }
    .wiz-chip input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .wiz-chip span {
        display: inline-block;
        padding: 8px 16px;
        border: 1px solid #cbd5e1;
        border-radius: 999px;
        font-size: 12px;
        font-weight: bold;
        color: var(--wiz-muted);
        background: #fff;
        transition: all 0.2s;
    }
    .wiz-chip input:checked ~ span {
        background: rgba(116, 185, 31, 0.1);
        border-color: var(--wiz-green);
        color: var(--wiz-dark);
    }

    .wiz-sidebar-right {
        width: 325px;
        background: #fff;
        border-left: 1px solid var(--wiz-border);
        display: flex;
        flex-direction: column;
    }
    .wiz-right-header {
        padding: 15px;
        border-bottom: 1px solid var(--wiz-border);
        background: #f8fafc;
        font-weight: 800;
        font-size: 13px;
        color: var(--wiz-dark);
        text-transform: uppercase;
    }
    .wiz-right-content {
        padding: 15px;
        overflow-y: auto;
        flex: 1;
    }

    .wiz-missing-block {
        border: 1px solid var(--wiz-border);
        border-radius: 12px;
        margin-bottom: 12px;
        overflow: hidden;
    }
    .wiz-missing-header {
        background: #f8fafc;
        padding: 10px 15px;
        font-size: 12px;
        font-weight: bold;
        color: var(--wiz-dark);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .wiz-missing-body {
        padding: 14px;
        background: #fff;
    }
    .wiz-missing-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        padding: 8px 0;
        border-bottom: 1px dashed #e2e8f0;
        color: var(--wiz-danger);
        font-weight: bold;
        cursor: pointer;
    }
    .wiz-missing-item:last-child { border-bottom: none; }
    .wiz-missing-item.filled {
        color: var(--wiz-green);
        cursor: default;
    }

    .wiz-btn-primary,
    .wiz-btn-secondary,
    .wiz-btn-danger {
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.2s;
    }
    .wiz-btn-primary {
        background: var(--wiz-green);
        color: #fff;
    }
    .wiz-btn-primary:hover { background: #68a61c; }

    .wiz-btn-secondary {
        background: #fff;
        color: var(--wiz-dark);
        border: 1px solid var(--wiz-border);
    }
    .wiz-btn-secondary:hover { background: #f1f5f9; }

    .wiz-btn-danger {
        background: #fee2e2;
        color: #ef4444;
        border: 1px solid #fecaca;
        padding: 8px 12px;
        font-size: 11px;
    }

    .wiz-dynamic-card {
        border: 1px solid var(--wiz-border);
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 15px;
        position: relative;
        background: #fff;
    }

    .wiz-card-title {
        font-size: 12px;
        font-weight: 900;
        color: var(--wiz-green);
        margin-bottom: 12px;
        text-transform: uppercase;
    }

    .wiz-soft-box {
        border: 1px solid var(--wiz-border);
        border-radius: 12px;
        background: #f8fafc;
        padding: 14px;
        margin-top: 12px;
    }

    .wiz-check-line {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 700;
        color: var(--wiz-dark);
        margin-bottom: 10px;
        cursor: pointer;
    }

    .wiz-footer-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
        border-top: 1px solid var(--wiz-border);
        margin-top: 16px;
    }

    .d-none { display: none !important; }

    @media (max-width: 1200px) {
        .wiz-sidebar-right { display: none; }
    }

    .wiz-product-row {
        border: 1px solid var(--wiz-border);
        border-radius: 14px;
        padding: 14px;
        background: #fff;
        margin-bottom: 12px;
    }

    .wiz-product-row-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .wiz-product-name {
        font-size: 13px;
        font-weight: 900;
        color: var(--wiz-dark);
    }

    .wiz-role-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 10px;
        font-weight: 800;
        border-radius: 999px;
        padding: 4px 9px;
        border: 1px solid var(--wiz-border);
        background: #f8fafc;
        color: var(--wiz-muted);
    }

    .wiz-limit-warning {
        margin-top: 10px;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid #fecaca;
        background: #fff1f2;
        color: #b91c1c;
        font-size: 12px;
        font-weight: 700;
    }
    .wiz-collapse-toggle{
        width:100%;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:10px;
        padding:10px 12px;
        border:1px solid var(--wiz-border);
        border-radius:10px;
        background:#fff;
        color:var(--wiz-dark);
        font-size:12px;
        font-weight:800;
        cursor:pointer;
    }

    .wiz-collapse-toggle:hover{
        background:#f8fafc;
    }

    .wiz-collapsed{
        display:none !important;
    }

    .wiz-right-stats{
        display:grid;
        grid-template-columns:repeat(3,1fr);
        gap:10px;
        margin-bottom:14px;
    }

    .wiz-stat-box{
        border:1px solid var(--wiz-border);
        background:#fff;
        border-radius:12px;
        padding:10px;
        text-align:center;
    }

    .wiz-stat-box strong{
        display:block;
        font-size:18px;
        color:var(--wiz-dark);
        margin-bottom:4px;
    }

    .wiz-stat-box span{
        font-size:11px;
        color:var(--wiz-muted);
        font-weight:700;
    }

    .wiz-product-toolbar{
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:10px;
        margin-bottom:12px;
    }

    .wiz-product-row-headline{
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        margin-bottom:12px;
    }

    .wiz-product-actions{
        display:flex;
        align-items:center;
        gap:8px;
    }

    .wiz-mini-btn{
        border:1px solid var(--wiz-border);
        background:#fff;
        color:var(--wiz-dark);
        padding:7px 10px;
        border-radius:8px;
        font-size:11px;
        font-weight:800;
        cursor:pointer;
    }

    .wiz-mini-btn:hover{
        background:#f8fafc;
    }

    .wiz-mini-btn-danger{
        border-color:#fecaca;
        background:#fff1f2;
        color:#b91c1c;
    }

    .wiz-product-body{
        display:block;
    }

    .wiz-product-row.is-collapsed .wiz-product-body{
        display:none;
    }

    .wiz-product-fillbar{
        display:flex;
        align-items:center;
        gap:10px;
        margin-top:10px;
    }

    .wiz-product-fillbar-track{
        flex:1;
        height:8px;
        border-radius:999px;
        background:#e2e8f0;
        overflow:hidden;
    }

    .wiz-product-fillbar-value{
        height:100%;
        background:var(--wiz-green);
        width:0%;
        transition:width .25s ease;
    }

    .wiz-product-fillbar-text{
        font-size:11px;
        font-weight:800;
        color:var(--wiz-muted);
        min-width:68px;
        text-align:right;
    }

    .wiz-section-head{
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        margin-bottom:18px;
        padding-bottom:10px;
        border-bottom:1px solid var(--wiz-border);
    }

    .wiz-section-head .wiz-section-title{
        margin:0;
        padding:0;
        border:none;
    }

    .wiz-section-counter{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        min-width:64px;
        padding:6px 10px;
        border-radius:999px;
        border:1px solid var(--wiz-border);
        background:#f8fafc;
        color:var(--wiz-dark);
        font-size:11px;
        font-weight:900;
        line-height:1;
        white-space:nowrap;
    }

    .wiz-section-counter.is-complete{
        background:rgba(116, 185, 31, 0.12);
        border-color:rgba(116, 185, 31, 0.35);
        color:#3f6212;
    }

    .wiz-section-counter.is-empty{
        background:#fff7ed;
        border-color:#fed7aa;
        color:#9a3412;
    }


    .wiz-nav-content{
        flex:1;
        min-width:0;
    }

    .wiz-nav-topline{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:10px;
    }

    .wiz-step-counter{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        min-width:52px;
        padding:4px 8px;
        border-radius:999px;
        border:1px solid var(--wiz-border);
        background:#fff;
        color:var(--wiz-dark);
        font-size:10px;
        font-weight:900;
        line-height:1;
        white-space:nowrap;
        flex-shrink:0;
    }

    .wiz-step-counter.is-complete{
        background:rgba(116, 185, 31, 0.12);
        border-color:rgba(116, 185, 31, 0.35);
        color:#3f6212;
    }

    .wiz-step-counter.is-empty{
        background:#fff7ed;
        border-color:#fed7aa;
        color:#9a3412;
    }

    .wiz-step-progress{
        width:100%;
        height:6px;
        border-radius:999px;
        background:#e2e8f0;
        overflow:hidden;
        margin-top:8px;
    }

    .wiz-step-progress-bar{
        height:100%;
        width:0%;
        background:var(--wiz-green);
        transition:width .25s ease;
    }

    .wiz-missing-header{
        background:#f8fafc;
        padding:10px 15px;
        font-size:12px;
        font-weight:bold;
        color:var(--wiz-dark);
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:10px;
        cursor:pointer;
    }

    .wiz-missing-header:hover{
        background:#f1f5f9;
    }

    .wiz-missing-toggle{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        width:24px;
        height:24px;
        border-radius:8px;
        border:1px solid var(--wiz-border);
        background:#fff;
        color:var(--wiz-muted);
        flex-shrink:0;
    }

    .wiz-missing-block.is-collapsed .wiz-missing-body{
        display:none;
    }

    .wiz-missing-block.is-collapsed .wiz-missing-toggle i{
        transform:rotate(-90deg);
    }

    .wiz-missing-toggle i{
        transition:transform .2s ease;
    }

    .wiz-notice{
        display:flex;
        align-items:flex-start;
        gap:12px;
        padding:12px 14px;
        margin-bottom:16px;
        border-radius:12px;
        border:1px solid #bfdbfe;
        background:#eff6ff;
        color:#1e3a8a;
    }

    .wiz-notice-icon{
        flex-shrink:0;
        width:22px;
        height:22px;
        display:flex;
        align-items:center;
        justify-content:center;
        border-radius:999px;
        background:#dbeafe;
        color:#1d4ed8;
        margin-top:1px;
    }

    .wiz-notice-content{
        flex:1;
        min-width:0;
    }

    .wiz-notice-title{
        font-size:12px;
        font-weight:900;
        margin-bottom:4px;
        color:#1e3a8a;
    }

    .wiz-notice-text{
        font-size:12px;
        line-height:1.55;
        color:#1e40af;
    }

    .wiz-notice.warning{
        border-color:#fcd34d;
        background:#fffbeb;
        color:#92400e;
    }

    .wiz-notice.warning .wiz-notice-icon{
        background:#fef3c7;
        color:#d97706;
    }

    .wiz-notice.warning .wiz-notice-title,
    .wiz-notice.warning .wiz-notice-text{
        color:#92400e;
    }

    .wiz-notice.success{
        border-color:#bbf7d0;
        background:#f0fdf4;
        color:#166534;
    }

    .wiz-notice.success .wiz-notice-icon{
        background:#dcfce7;
        color:#16a34a;
    }

    .wiz-notice.success .wiz-notice-title,
    .wiz-notice.success .wiz-notice-text{
        color:#166534;
    }
    .wiz-focus-highlight{
        animation:wizFocusPulse 1.6s ease;
        position:relative;
        z-index:2;
    }

    .wiz-quick-products{
        display:flex;
        flex-wrap:wrap;
        gap:10px;
        margin-bottom:16px;
    }

    .wiz-quick-product-btn{
        border:1px solid var(--wiz-border);
        background:#fff;
        color:var(--wiz-dark);
        padding:10px 14px;
        border-radius:999px;
        font-size:12px;
        font-weight:900;
        line-height:1;
        cursor:pointer;
        transition:all .2s ease;
        box-shadow:0 1px 2px rgba(0,0,0,.04);
    }

    .wiz-quick-product-btn:hover{
        background:#f8fafc;
        border-color:rgba(116, 185, 31, 0.45);
        color:var(--wiz-green);
    }

    .wiz-quick-product-btn.is-active{
        background:rgba(116, 185, 31, 0.1);
        border-color:rgba(116, 185, 31, 0.45);
        color:var(--wiz-dark);
    }

    .wiz-quick-product-btn.is-missing{
        opacity:.55;
    }

    @keyframes wizFocusPulse{
        0%   { box-shadow:0 0 0 0 rgba(116,185,31,.45); background:#f0fdf4; }
        50%  { box-shadow:0 0 0 8px rgba(116,185,31,.12); background:#f0fdf4; }
        100% { box-shadow:0 0 0 0 rgba(116,185,31,0); background:''; }
    }




</style>
 
<div id="customBladeWizard">
    <div class="wiz-wrapper">

        <div class="wiz-header-top">
            <h2>
                <i class="feather icon-zap text-success"></i>
                Fachwizard
            </h2>

            <div class="wiz-progress-container">
                <div class="wiz-progress-labels">
                    <span>Fortschritt</span>
                    <span id="wizGlobalProgressText">0/0 Pflichtfelder</span>
                </div>
                <div class="wiz-progress-bar">
                    <div class="wiz-progress-fill" id="wizGlobalProgressBar"></div>
                </div>
            </div>

            <button type="button" class="btn btn-light btn-sm" onclick="closeBladeWizard()">
                <i class="feather icon-x"></i>
                Schließen
            </button>
        </div>

        <div class="wiz-body">

            {{-- LEFT SIDEBAR --}}
            <aside class="wiz-sidebar-left">
                <button type="button" class="wiz-nav-btn active" data-target="1" onclick="navToWizardStep(1)">
                    <div class="wiz-nav-icon"><i class="feather icon-user"></i></div>
                    <div class="wiz-nav-content">
                        <div class="wiz-nav-topline">
                            <div>
                                <div class="wiz-nav-text">Projektstart</div>
                                <div class="wiz-nav-sub">Kontakt, Standort, Interesse</div>
                            </div>
                            <div class="wiz-step-counter" data-step-counter="1">0/0</div>
                        </div>
                        <div class="wiz-step-progress">
                            <div class="wiz-step-progress-bar" data-step-progress="1"></div>
                        </div>
                    </div>
                </button>

                <button type="button" class="wiz-nav-btn" data-target="2" onclick="navToWizardStep(2)">
                    <div class="wiz-nav-icon"><i class="feather icon-home"></i></div>
                    <div class="wiz-nav-content">
                        <div class="wiz-nav-topline">
                            <div>
                                <div class="wiz-nav-text">Gebäudeprofil</div>
                                <div class="wiz-nav-sub">Grunddaten & Nutzung</div>
                            </div>
                            <div class="wiz-step-counter" data-step-counter="2">0/0</div>
                        </div>
                        <div class="wiz-step-progress">
                            <div class="wiz-step-progress-bar" data-step-progress="2"></div>
                        </div>
                    </div>
                </button>

                <button type="button" class="wiz-nav-btn" data-target="3" onclick="navToWizardStep(3)">
                    <div class="wiz-nav-icon"><i class="feather icon-layers"></i></div>
                    <div class="wiz-nav-content">
                        <div class="wiz-nav-topline">
                            <div>
                                <div class="wiz-nav-text">Gebäudehülle</div>
                                <div class="wiz-nav-sub">Bauteile & Dämmung</div>
                            </div>
                            <div class="wiz-step-counter" data-step-counter="3">0/0</div>
                        </div>
                        <div class="wiz-step-progress">
                            <div class="wiz-step-progress-bar" data-step-progress="3"></div>
                        </div>
                    </div>
                </button>

                <button type="button" class="wiz-nav-btn" data-target="4" onclick="navToWizardStep(4)">
                    <div class="wiz-nav-icon"><i class="feather icon-sun"></i></div>
                    <div class="wiz-nav-content">
                        <div class="wiz-nav-topline">
                            <div>
                                <div class="wiz-nav-text">Dach & PV</div>
                                <div class="wiz-nav-sub">Flächen, Montage, Speicher</div>
                            </div>
                            <div class="wiz-step-counter" data-step-counter="4">0/0</div>
                        </div>
                        <div class="wiz-step-progress">
                            <div class="wiz-step-progress-bar" data-step-progress="4"></div>
                        </div>
                    </div>
                </button>

                <button type="button" class="wiz-nav-btn" data-target="5" onclick="navToWizardStep(5)">
                    <div class="wiz-nav-icon"><i class="feather icon-thermometer"></i></div>
                    <div class="wiz-nav-content">
                        <div class="wiz-nav-topline">
                            <div>
                                <div class="wiz-nav-text">Heizung & WP</div>
                                <div class="wiz-nav-sub">Bestand, Rohrsystem, Räume</div>
                            </div>
                            <div class="wiz-step-counter" data-step-counter="5">0/0</div>
                        </div>
                        <div class="wiz-step-progress">
                            <div class="wiz-step-progress-bar" data-step-progress="5"></div>
                        </div>
                    </div>
                </button>

                <button type="button" class="wiz-nav-btn" data-target="6" onclick="navToWizardStep(6)">
                    <div class="wiz-nav-icon"><i class="feather icon-zap"></i></div>
                    <div class="wiz-nav-content">
                        <div class="wiz-nav-topline">
                            <div>
                                <div class="wiz-nav-text">Elektro & Netz</div>
                                <div class="wiz-nav-sub">Zählerschrank & Netz</div>
                            </div>
                            <div class="wiz-step-counter" data-step-counter="6">0/0</div>
                        </div>
                        <div class="wiz-step-progress">
                            <div class="wiz-step-progress-bar" data-step-progress="6"></div>
                        </div>
                    </div>
                </button>

                <button type="button" class="wiz-nav-btn" data-target="7" onclick="navToWizardStep(7)">
                    <div class="wiz-nav-icon"><i class="feather icon-truck"></i></div>
                    <div class="wiz-nav-content">
                        <div class="wiz-nav-topline">
                            <div>
                                <div class="wiz-nav-text">E-Mobilität</div>
                                <div class="wiz-nav-sub">Fahrzeuge & Wallbox</div>
                            </div>
                            <div class="wiz-step-counter" data-step-counter="7">0/0</div>
                        </div>
                        <div class="wiz-step-progress">
                            <div class="wiz-step-progress-bar" data-step-progress="7"></div>
                        </div>
                    </div>
                </button>

                <button type="button" class="wiz-nav-btn" data-target="8" onclick="navToWizardStep(8)">
                    <div class="wiz-nav-icon"><i class="feather icon-check-circle"></i></div>
                    <div class="wiz-nav-content">
                        <div class="wiz-nav-topline">
                            <div>
                                <div class="wiz-nav-text">Abschluss</div>
                                <div class="wiz-nav-sub">Unterlagen & Freigabe</div>
                            </div>
                            <div class="wiz-step-counter" data-step-counter="8">0/0</div>
                        </div>
                        <div class="wiz-step-progress">
                            <div class="wiz-step-progress-bar" data-step-progress="8"></div>
                        </div>
                    </div>
                </button>
            </aside>

            {{-- MAIN CONTENT --}}
            <div class="wiz-main-center">
                <form id="bladeWizardForm">

                    {{-- STEP 1 --}}
                    <section class="wiz-step-container active" id="wiz-step-1">

                        <div class="wiz-section">
                            <div class="wiz-section-head">
                                <div class="wiz-section-title">Kontaktperson</div>
                                <div class="wiz-section-counter">0/0</div>
                            </div>

                           
                            <div class="wiz-chips mb-3">
                                <label class="wiz-chip">
                                    <input type="radio" name="salutation" value="Frau" checked data-step="1">
                                    <span>Frau</span>
                                </label>
                                <label class="wiz-chip">
                                    <input type="radio" name="salutation" value="Herr" data-step="1">
                                    <span>Herr</span>
                                </label>
                                <label class="wiz-chip">
                                    <input type="radio" name="salutation" value="Divers" data-step="1">
                                    <span>Divers</span>
                                </label>
                            </div>

                            <div class="wiz-grid wiz-grid-4">
                                <div class="wiz-form-group">
                                    <label class="wiz-label">Vorname *</label>
                                    <input type="text" name="first_name" class="wiz-input req-field" data-step="1">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Nachname *</label>
                                    <input type="text" name="last_name" class="wiz-input req-field" data-step="1">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">E-Mail *</label>
                                    <input type="email" name="email" class="wiz-input req-field" data-step="1">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Telefon / Mobil</label>
                                    <input type="text" name="phone" class="wiz-input" data-step="1">
                                </div>
                            </div>

                            <div class="wiz-form-group">
                                <label class="wiz-label">Notiz Kontaktperson</label>
                                <textarea name="notice_contact_person" class="wiz-textarea" data-step="1" placeholder="Zusätzliche Notiz zu Kontaktperson..."></textarea>
                            </div>
                        </div>

                        <div class="wiz-section">
                            <div class="wiz-section-head">
                                <div class="wiz-section-title">Objektstandort</div>
                                <div class="wiz-section-counter">0/0</div>
                            </div>

                            <div class="wiz-form-group" style="margin-bottom:18px;">
                                <label class="wiz-label">Adresse suchen *</label>
                                <input
                                    type="text"
                                    id="wiz-address-search"
                                    class="wiz-input"
                                    autocomplete="off"
                                    placeholder="Adresse suchen, z. B. Musterstraße 12, 44135 Dortmund">
                                <div class="wiz-hint">
                                    Adresse auswählen, dann werden Straße, Hausnummer, PLZ und Ort automatisch ausgefüllt.
                                </div>
                            </div>

                            <div class="wiz-grid wiz-grid-4">
                                <div class="wiz-form-group">
                                    <label class="wiz-label">Straße *</label>
                                    <input
                                        type="text"
                                        id="wiz-street-autocomplete"
                                        name="street"
                                        class="wiz-input req-field"
                                        data-step="1"
                                        autocomplete="off"
                                        placeholder="Straße">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Hausnummer *</label>
                                    <input
                                        type="text"
                                        id="wiz-address-no"
                                        name="address_no"
                                        class="wiz-input req-field"
                                        data-step="1"
                                        placeholder="Hausnummer">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">PLZ *</label>
                                    <input
                                        type="text"
                                        id="wiz-postcode"
                                        name="postcode"
                                        class="wiz-input req-field"
                                        data-step="1"
                                        placeholder="PLZ">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Ort *</label>
                                    <input
                                        type="text"
                                        id="wiz-city"
                                        name="city"
                                        class="wiz-input req-field"
                                        data-step="1"
                                        placeholder="Ort">
                                </div>
                            </div>

                            <input type="hidden" id="wiz-latitude" name="latitude" value="">
                            <input type="hidden" id="wiz-longitude" name="longitude" value="">
                            <input type="hidden" id="wiz-country" name="country" value="">
                            <input type="hidden" id="wiz-place-id" name="google_place_id" value="">

                            <div class="wiz-form-group">
                                <label class="wiz-label">Notiz Objektstandort</label>
                                <textarea
                                    name="notice_object_location"
                                    class="wiz-textarea"
                                    data-step="1"
                                    placeholder="Zusätzliche Notiz zum Objektstandort..."></textarea>
                            </div>
                        </div>

                        <div class="wiz-section">
                            <div class="wiz-section-head">
                                <div class="wiz-section-title">Kundeninteresse / Produktzuweisung</div>
                                <div class="wiz-section-counter">0/0</div>
                            </div>

                            <div class="wiz-notice warning">
                                <div class="wiz-notice-icon">
                                    <i class="feather icon-alert-triangle"></i>
                                </div>
                                <div class="wiz-notice-content">
                                    <div class="wiz-notice-title">Wichtig</div>
                                    <div class="wiz-notice-text">
                                        Produkte und Services möglichst vollständig auswählen, damit die weiteren Schritte korrekt eingeblendet werden.
                                    </div>
                                </div>
                            </div>

                            <div class="wiz-form-group" style="margin-bottom:16px;">
                                <label class="wiz-label">Produkte aus Datenbank wählen</label>
                                <div class="wiz-hint">
                                    Schnellauswahl: Ein Klick fügt das Produkt direkt hinzu und setzt den Service möglichst auf „complete“.
                                </div>
                            </div>

                            <div class="wiz-quick-products" id="wiz-quick-products">
                                <button type="button" class="wiz-quick-product-btn" data-product-key="PHOTOVOLTAIK">PHOTOVOLTAIK</button>
                                <button type="button" class="wiz-quick-product-btn" data-product-key="WÄRMEPUMPE">WÄRMEPUMPE</button>
                                <button type="button" class="wiz-quick-product-btn" data-product-key="WALLBOX">WALLBOX</button>
                                <button type="button" class="wiz-quick-product-btn" data-product-key="FENSTER">FENSTER</button>
                                <button type="button" class="wiz-quick-product-btn" data-product-key="TÜR">TÜR</button>
                                <button type="button" class="wiz-quick-product-btn" data-product-key="BATTERIESPEICHER">BATTERIESPEICHER</button>
                            </div>

                            <div class="wiz-grid wiz-grid-2" style="margin-bottom:14px;">
                                <div class="wiz-form-group">
                                    <label class="wiz-label">Produkt hinzufügen</label>
                                    <select id="wiz-product-adder" class="wiz-select">
                                        <option value="">Produkt wählen...</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <div class="wiz-soft-box" style="min-height:44px; display:flex; align-items:center;">
                                        <span id="wiz-product-summary" style="font-size:12px; font-weight:700; color:var(--wiz-muted);">
                                            Noch kein Produkt hinzugefügt
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div id="wiz-product-assignments-wrap" style="margin-top:20px; display:block;">
                                <div class="wiz-section-subtitle" style="margin-top:0;">Zugeordnete Produkte</div>
                                <div id="wiz-product-assignments"></div>
                            </div>

                            <div class="wiz-grid wiz-grid-3" style="margin-top:18px;">
                                <div class="wiz-form-group">
                                    <label class="wiz-label">Kaufinteresse</label>
                                    <select name="periority" class="wiz-select" data-step="1">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Hoch">Hoch</option>
                                        <option value="Mittel">Mittel</option>
                                        <option value="Niedrig">Niedrig</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Vor-Ort-Termin vereinbart</label>
                                    <select name="appointment_confirmed" class="wiz-select" data-step="1">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Ja">Ja</option>
                                        <option value="Nein">Nein</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Termin Datum</label>
                                    <input type="date" name="appointment" class="wiz-input" data-step="1">
                                </div>
                            </div>

                            <div class="wiz-form-group">
                                <label class="wiz-label">Notiz Kundeninteresse / Produktzuweisung</label>
                                <textarea name="notice_customer_interest_products" class="wiz-textarea" data-step="1" placeholder="Zusätzliche Notiz zu Interesse, Produkten oder Service-Zuweisung..."></textarea>
                            </div>
                        </div>

                        <div class="wiz-section">
                            <div class="wiz-section-head">
                                <div class="wiz-section-title">Verbrauchsdaten & Preise</div>
                                <div class="wiz-section-counter">0/0</div>
                            </div>

                            <div class="wiz-notice">
                                <div class="wiz-notice-icon">
                                    <i class="feather icon-bar-chart-2"></i>
                                </div>
                                <div class="wiz-notice-content">
                                    <div class="wiz-notice-title">Berechnungsbasis</div>
                                    <div class="wiz-notice-text">
                                        Genaue Verbrauchs- und Preisdaten verbessern die Auswertung und die spätere Angebotsqualität.
                                    </div>
                                </div>
                            </div>

                            <div class="wiz-grid wiz-grid-4">
                                <div class="wiz-form-group">
                                    <label class="wiz-label">Haushaltsstromverbrauch (kWh/a)</label>
                                    <input type="number" name="total_electricity_consumption" class="wiz-input" data-step="1" placeholder="z. B. 3500">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Strompreis Bezug (€/kWh)</label>
                                    <input type="number" step="0.0001" name="electricity_price" value="0.35" class="wiz-input" data-step="1" placeholder="z. B. 0.35">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Einspeisevergütung (€/kWh)</label>
                                    <input type="number" step="0.0001" name="feed_in_tariff" value="0.08" class="wiz-input" data-step="1" placeholder="z. B. 0.08">
                                </div>

                                <div class="wiz-form-group wiz-form-group--empty"></div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Aktueller Energieträger Heizung</label>
                                    <select name="heating_energy_source" id="heating_energy_source" class="wiz-select" data-step="1">
                                        <option value="">Bitte wählen...</option>
                                        <option value="district_heating">Fernwärme</option>
                                        <option value="natural_gas_h">Erdgas H</option>
                                        <option value="natural_gas_l">Erdgas L</option>
                                        <option value="heating_oil">Heizöl EL</option>
                                        <option value="liquid_gas">Flüssiggas</option>
                                        <option value="pellets">Pellets</option>
                                        <option value="firewood_soft">Scheitholz weich</option>
                                        <option value="firewood_hard">Scheitholz hart</option>
                                        <option value="direct_electric">Direktstromheizung</option>
                                        <option value="heat_pump">Wärmepumpe Bestand</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Jährlicher Heizungsverbrauch</label>
                                    <input type="number" step="0.01" name="annual_heating_energy_consumption" id="annual_heating_energy_consumption" class="wiz-input" data-step="1" placeholder="z. B. 18000">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Einheit Heizungsverbrauch</label>
                                    <select name="heating_energy_unit" id="heating_energy_unit" class="wiz-select" data-step="1">
                                        <option value="">Bitte zuerst Energieträger wählen...</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label" id="old_heating_price_label">Preis Altanlage</label>
                                    <input type="number" step="0.0001" name="old_heating_price" id="old_heating_price" value="0.11" class="wiz-input" data-step="1" placeholder="Preis passend zur Einheit">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Preis Altanlage umgerechnet (€/kWh)</label>
                                    <input type="number" step="0.0001" name="old_heating_price_per_kwh" id="old_heating_price_per_kwh" class="wiz-input" data-step="1" readonly placeholder="Automatisch berechnet">
                                </div>
                            </div>

                            <div class="wiz-form-group">
                                <label class="wiz-label">Notiz Verbrauchsdaten & Preise</label>
                                <textarea name="notice_consumption_prices" class="wiz-textarea" data-step="1" placeholder="Zusätzliche Notiz zu Verbrauch, Tarifen oder Preisannahmen..."></textarea>
                            </div>
                        </div>
                    </section>

                    {{-- STEP 2 --}}
                    <section class="wiz-step-container" id="wiz-step-2">
                        <div class="wiz-section">
                            <div class="wiz-section-head">
                                <div class="wiz-section-title">Gebäudeprofil</div>
                                <div class="wiz-section-counter">0/0</div>
                            </div>

                            <div class="wiz-notice">
                                <div class="wiz-notice-icon">
                                    <i class="feather icon-home"></i>
                                </div>
                                <div class="wiz-notice-content">
                                    <div class="wiz-notice-title">Gebäudedaten</div>
                                    <div class="wiz-notice-text">
                                        Diese Angaben werden für Planung, Förderlogik und Produktauslegung verwendet.
                                    </div>
                                </div>
                            </div>

                            <div class="wiz-grid wiz-grid-4">
                                <div class="wiz-form-group">
                                    <label class="wiz-label">Gebäudetyp *</label>
                                    <select name="building_type" class="wiz-select req-field" data-step="2">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Einfamilienhaus">Einfamilienhaus</option>
                                        <option value="Mehrfamilienhaus">Mehrfamilienhaus</option>
                                        <option value="Gewerbe">Gewerbe</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group wiz-company-wrap d-none">
                                    <label class="wiz-label">Firmenname</label>
                                    <input type="text" name="object_name" class="wiz-input" data-step="2">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Baujahr *</label>
                                    <input type="number" name="house_year" class="wiz-input req-field" data-step="2">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Wohneinheiten *</label>
                                    <input type="number" name="number_we" class="wiz-input req-field" data-step="2">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Anzahl Geschosse *</label>
                                    <input type="number" name="number_stories" class="wiz-input req-field" data-step="2">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Wohnfläche (m²) *</label>
                                    <input type="number" name="living_space" class="wiz-input req-field" data-step="2">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Bauzustand</label>
                                    <select name="building_condition" class="wiz-select" data-step="2">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Bestand">Bestand</option>
                                        <option value="Teilmodernisiert">Teilmodernisiert</option>
                                        <option value="Kernsaniert">Kernsaniert</option>
                                        <option value="Neubau">Neubau</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Nutzungsform</label>
                                    <select name="usage_type" class="wiz-select" data-step="2">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Eigennutzung">Eigennutzung</option>
                                        <option value="Vermietung">Vermietung</option>
                                        <option value="Gemischt">Gemischt</option>
                                    </select>
                                </div>
                            </div>

                            <div class="wiz-section-subtitle">Mehrfamilienhaus / Förderdaten</div>

                            <div class="wiz-grid wiz-grid-4">
                                <div class="wiz-form-group">
                                    <label class="wiz-label">Anzahl Eigentümer</label>
                                    <input type="number" name="owner_count" class="wiz-input" data-step="2">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Davon selbstbewohnt (WE)</label>
                                    <input type="number" name="owner_occupied_units" class="wiz-input" data-step="2">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Davon vermietet (WE)</label>
                                    <input type="number" name="rented_units" class="wiz-input" data-step="2">
                                </div>
                            </div>

                            <div class="wiz-grid wiz-grid-4">
                                <div class="wiz-form-group">
                                    <label class="wiz-label">Eigentümer ≤ 40.000 €/a</label>
                                    <input type="number" name="owners_below_40k" class="wiz-input" data-step="2">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Eigentümer > 40.000 €/a</label>
                                    <input type="number" name="owners_above_40k" class="wiz-input" data-step="2">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Selbstbewohnt ≤ 40.000 €</label>
                                    <input type="number" name="owner_occupied_below_40k" class="wiz-input" data-step="2">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Selbstbewohnt > 40.000 €</label>
                                    <input type="number" name="owner_occupied_above_40k" class="wiz-input" data-step="2">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Vermietet ≤ 40.000 €</label>
                                    <input type="number" name="rented_below_40k" class="wiz-input" data-step="2">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Vermietet > 40.000 €</label>
                                    <input type="number" name="rented_above_40k" class="wiz-input" data-step="2">
                                </div>
                            </div>

                            <div class="wiz-form-group">
                                <label class="wiz-label">Notiz Gebäudeprofil</label>
                                <textarea name="notice_building_profile" class="wiz-textarea" data-step="2" placeholder="Zusätzliche Notiz zum Gebäudeprofil..."></textarea>
                            </div>
                        </div>
                    </section>

                    {{-- STEP 3 --}}
                    <section class="wiz-step-container" id="wiz-step-3">
                        <div class="wiz-section">
                            <div class="wiz-section-head">
                                <div class="wiz-section-title">Gebäudehülle</div>
                                <div class="wiz-section-counter">0/0</div>
                            </div>

                            <div class="wiz-notice">
                                <div class="wiz-notice-icon">
                                    <i class="feather icon-layers"></i>
                                </div>
                                <div class="wiz-notice-content">
                                    <div class="wiz-notice-title">Bauteile & Dämmung</div>
                                    <div class="wiz-notice-text">
                                        Je präziser die Werte zu Gebäudehülle und Fenstern sind, desto besser werden Heizlast und Empfehlung.
                                    </div>
                                </div>
                            </div>

                            <div class="wiz-section-subtitle">Geometrie</div>
                            <div class="wiz-grid wiz-grid-4">
                                <div class="wiz-form-group">
                                    <label class="wiz-label">Gebäudelänge (m)</label>
                                    <input type="number" name="building_length" class="wiz-input" data-step="3">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Gebäudebreite (m)</label>
                                    <input type="number" name="building_width" class="wiz-input" data-step="3">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Fassadenhöhe (m)</label>
                                    <input type="number" name="facade_height" class="wiz-input" data-step="3">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Fensterfläche gesamt (m²)</label>
                                    <input type="number" name="total_window_area" class="wiz-input" data-step="3">
                                </div>
                            </div>

                            <div class="wiz-section-subtitle">Außenwand / Fassade</div>
                            <div class="wiz-grid wiz-grid-4">
                                <div class="wiz-form-group">
                                    <label class="wiz-label">Außenwand / Mauerwerk</label>
                                    <select name="masonry" class="wiz-select" data-step="3">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Poroton">Poroton</option>
                                        <option value="Vollstein">Vollstein</option>
                                        <option value="Beton">Beton</option>
                                        <option value="Holzbau">Holzbau</option>
                                        <option value="Unklar">Unklar</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Mauerwerk Dicke (cm)</label>
                                    <input type="number" name="masonry_thickness" class="wiz-input" data-step="3">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Fassadendämmung Material</label>
                                    <select name="insolation_type" class="wiz-select" data-step="3">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Keine / Ungedämmt">Keine / Ungedämmt</option>
                                        <option value="EPS / Styropor">EPS / Styropor</option>
                                        <option value="Steinwolle">Steinwolle</option>
                                        <option value="Holzfaser">Holzfaser</option>
                                        <option value="PIR/PUR">PIR/PUR</option>
                                        <option value="Unklar">Unklar</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Fassadendämmung Stärke (cm)</label>
                                    <input type="number" name="external_insulation_thickness" class="wiz-input" data-step="3">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Fassadendämmung Jahr</label>
                                    <input type="number" name="insolation_age" class="wiz-input" data-step="3">
                                </div>
                            </div>

                            <div class="wiz-section-subtitle">Dach / Keller</div>
                            <div class="wiz-grid wiz-grid-4">
                                <div class="wiz-form-group">
                                    <label class="wiz-label">Dachdämmung Material</label>
                                    <select name="roof_insulation_type" class="wiz-select" data-step="3">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Keine / Ungedämmt">Keine / Ungedämmt</option>
                                        <option value="Glaswolle/Klemmfilz">Glaswolle/Klemmfilz</option>
                                        <option value="Aufsparrendämmung (PUR)">Aufsparrendämmung (PUR)</option>
                                        <option value="Holzfaser">Holzfaser</option>
                                        <option value="Unklar">Unklar</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Dachdämmung Stärke (cm)</label>
                                    <input type="number" name="roof_insulation_thickness" class="wiz-input" data-step="3">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Dachdämmung Jahr</label>
                                    <input type="number" name="roof_insulation_year" class="wiz-input" data-step="3">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Keller/Boden Dämmmaterial</label>
                                    <select name="basement_insulation_type" class="wiz-select" data-step="3">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Keine / Ungedämmt">Keine / Ungedämmt</option>
                                        <option value="XPS (Perimeter)">XPS (Perimeter)</option>
                                        <option value="EPS">EPS</option>
                                        <option value="Unklar">Unklar</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Kellerdämmung Stärke (cm)</label>
                                    <input type="number" name="basement_insulation_thickness" class="wiz-input" data-step="3">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Kellerdämmung Jahr</label>
                                    <input type="number" name="basement_insulation_year" class="wiz-input" data-step="3">
                                </div>
                            </div>

                            <div class="wiz-section-subtitle">Fenster & Lüftung</div>
                            <div class="wiz-grid wiz-grid-4">
                                <div class="wiz-form-group">
                                    <label class="wiz-label">Fensterverglasung</label>
                                    <select name="window_glazing" class="wiz-select" data-step="3">
                                        <option value="">Bitte wählen...</option>
                                        <option value="1-fach">1-fach</option>
                                        <option value="2-fach">2-fach</option>
                                        <option value="3-fach">3-fach</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Fensterrahmen</label>
                                    <select name="window_frame" class="wiz-select" data-step="3">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Kunststoff">Kunststoff</option>
                                        <option value="Holz">Holz</option>
                                        <option value="Alu">Alu</option>
                                        <option value="Holz-Alu">Holz-Alu</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Fenster Baujahr / Tauschjahr</label>
                                    <input type="text" name="window_year" class="wiz-input" data-step="3">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Lüftungssituation</label>
                                    <select name="ventilation_type" class="wiz-select" data-step="3">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Fensterlüftung">Fensterlüftung</option>
                                        <option value="Zuluft / Abluft">Zuluft / Abluft</option>
                                        <option value="Lüftungsanlage mit WRG">Lüftungsanlage mit WRG</option>
                                    </select>
                                </div>
                            </div>

                            <div class="wiz-form-group">
                                <label class="wiz-label">Notiz Gebäudehülle</label>
                                <textarea name="notice_building_envelope" class="wiz-textarea" data-step="3" placeholder="Zusätzliche Notiz zu Gebäudehülle, Dämmung oder Fenstern..."></textarea>
                            </div>
                        </div>
                    </section>

                    {{-- STEP 4 --}}
                    <section class="wiz-step-container" id="wiz-step-4">
                        <div class="wiz-section">
                            <div class="wiz-section-head">
                                <div class="wiz-section-title">Dachflächen & Eignung</div>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div class="wiz-section-counter">0/0</div>
                                    <button type="button" class="wiz-btn-secondary btn-sm" onclick="addWizRoof()">
                                        <i class="feather icon-plus"></i>
                                        Dach hinzufügen
                                    </button>
                                </div>
                            </div>

                            <div class="wiz-notice warning">
                                <div class="wiz-notice-icon">
                                    <i class="feather icon-sun"></i>
                                </div>
                                <div class="wiz-notice-content">
                                    <div class="wiz-notice-title">Dachprüfung</div>
                                    <div class="wiz-notice-text">
                                        Flächen, Ausrichtung, Verschattung und mögliche Einschränkungen bitte möglichst genau erfassen.
                                    </div>
                                </div>
                            </div>

                            <div id="wiz-roofs-container"></div>

                            <div class="wiz-section-subtitle">PV / Speicher Rahmenparameter</div>
                            <div class="wiz-grid wiz-grid-4">
                                <div class="wiz-form-group">
                                    <label class="wiz-label">Traufhöhe (m)</label>
                                    <input type="number" name="roof_height" class="wiz-input" data-step="4">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Kabelweg DC</label>
                                    <select name="dc_cable_route" class="wiz-select" data-step="4">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Freier Schacht vorhanden">Freier Schacht vorhanden</option>
                                        <option value="Außen an Fassade">Außen an Fassade</option>
                                        <option value="Muss gebohrt werden">Muss gebohrt werden</option>
                                        <option value="Unklar">Unklar</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Bestands-PV</label>
                                    <select name="pv_existing" class="wiz-select" data-step="4">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Keine">Keine</option>
                                        <option value="Vorhanden">Vorhanden</option>
                                        <option value="Erweiterung">Erweiterung</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Speicherwunsch</label>
                                    <select name="storage_preference" class="wiz-select" data-step="4">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Kein Speicher">Kein Speicher</option>
                                        <option value="Optional">Optional</option>
                                        <option value="Fest gewünscht">Fest gewünscht</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Notstrom</label>
                                    <select name="backup_power" class="wiz-select" data-step="4">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Nicht relevant">Nicht relevant</option>
                                        <option value="Optional">Optional</option>
                                        <option value="Fest gewünscht">Fest gewünscht</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Baukosten PV/Speicher (€)</label>
                                    <input type="number" name="pv_investment_costs" class="wiz-input" data-step="4">
                                </div>
                            </div>

                            <div class="wiz-form-group">
                                <label class="wiz-label">Notiz Dach & PV</label>
                                <textarea name="notice_roof_pv" class="wiz-textarea" data-step="4" placeholder="Zusätzliche Notiz zu Dachflächen, PV oder Speicher..."></textarea>
                            </div>
                        </div>
                    </section>

                    {{-- STEP 5 --}}
                    <section class="wiz-step-container" id="wiz-step-5">
                        <div class="wiz-section">
                            <div class="wiz-section-head">
                                <div class="wiz-section-title">Bestandsheizung & Wärmepumpe</div>
                                <div class="wiz-section-counter">0/0</div>
                            </div>

                            <div class="wiz-notice">
                                <div class="wiz-notice-icon">
                                    <i class="feather icon-thermometer"></i>
                                </div>
                                <div class="wiz-notice-content">
                                    <div class="wiz-notice-title">Heizungsdaten</div>
                                    <div class="wiz-notice-text">
                                        Bestand, Vorlauf und Verteilung bilden die Grundlage für die Wärmepumpenprüfung.
                                    </div>
                                </div>
                            </div>

                            <div class="wiz-grid wiz-grid-4">
                                <div class="wiz-form-group">
                                    <label class="wiz-label">Aktuelle Heiztechnik</label>
                                    <select name="heating_system_type" class="wiz-select" data-step="5">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Gas">Gas</option>
                                        <option value="Öl">Öl</option>
                                        <option value="Pellets">Pellets</option>
                                        <option value="Stromdirekt">Stromdirekt</option>
                                        <option value="Wärmepumpe">Wärmepumpe</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Leistung Alt-Heizung (kW)</label>
                                    <input type="number" name="old_heating_power" class="wiz-input" data-step="5">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Wärmeverteilung</label>
                                    <select name="heat_distribution" class="wiz-select" data-step="5">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Fußbodenheizung">Fußbodenheizung</option>
                                        <option value="Heizkörper">Heizkörper</option>
                                        <option value="Gemischt">Gemischt</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Vorlauftemperatur (°C)</label>
                                    <input type="number" name="flow_temperature" class="wiz-input" data-step="5">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Separater WW-Speicher?</label>
                                    <select name="hot_water_generation" class="wiz-select" data-step="5">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Ja">Ja</option>
                                        <option value="Nein (Durchlauferhitzer/Kombi)">Nein (Durchlauferhitzer/Kombi)</option>
                                        <option value="Unklar">Unklar</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">WW-Speicher Volumen (L)</label>
                                    <input type="number" name="hot_water_tank_liters" class="wiz-input" data-step="5">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Aufstellort Außen</label>
                                    <select name="installation_location" class="wiz-select" data-step="5">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Ja, Garten/Hof">Ja, Garten/Hof</option>
                                        <option value="Nein">Nein</option>
                                        <option value="Unklar">Unklar</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Erdarbeiten für WP nötig</label>
                                    <select name="groundwork" class="wiz-select" data-step="5">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Ja">Ja</option>
                                        <option value="Nein">Nein</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Leitungslänge Außen → Innen (m)</label>
                                    <input type="number" name="heat_pump_pipe_length" class="wiz-input" data-step="5">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Kellerdeckenhöhe (m)</label>
                                    <input type="number" name="basement_ceiling_height" class="wiz-input" data-step="5">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Einbringung Türbreite (cm)</label>
                                    <input type="number" name="door_width_for_installation" class="wiz-input" data-step="5">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Baukosten Wärmepumpe (€)</label>
                                    <input type="number" name="heat_pump_investment_costs" class="wiz-input" data-step="5">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Förderung WP (%)</label>
                                    <input type="number" name="heat_pump_subsidy_percent" value="55" class="wiz-input" data-step="5">
                                </div>
                            </div>

                            <div class="wiz-section-subtitle">Rohrsystem & Wasser</div>
                            <div class="wiz-grid wiz-grid-4">
                                <div class="wiz-form-group">
                                    <label class="wiz-label">Rohrmaterial Hauptleitung</label>
                                    <select name="pipe_system_material" class="wiz-select" data-step="5">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Kupfer">Kupfer</option>
                                        <option value="Kunststoff / MSVR">Kunststoff / MSVR</option>
                                        <option value="Edelstahl">Edelstahl</option>
                                        <option value="C-Stahl">C-Stahl</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Zirkulationsleitung</label>
                                    <select name="circulation_line" class="wiz-select" data-step="5">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Ja">Ja</option>
                                        <option value="Nein">Nein</option>
                                        <option value="Unklar">Unklar</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Dimension Heizung (VL/RL)</label>
                                    <input type="text" name="heating_pipe_dimension" class="wiz-input" data-step="5" placeholder="z.B. 28x1.5 mm (DN25)">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Dimension Wasser (Warm/Kalt)</label>
                                    <input type="text" name="water_pipe_dimension" class="wiz-input" data-step="5" placeholder="z.B. 22x1.0 mm (DN20)">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Dimension Zirkulation</label>
                                    <input type="text" name="circulation_pipe_dimension" class="wiz-input" data-step="5" placeholder="z.B. 15x1.0 mm (DN12)">
                                </div>
                            </div>

                            <div class="wiz-form-group">
                                <label class="wiz-label">Notiz Bestandsheizung & Wärmepumpe</label>
                                <textarea name="notice_heating_heatpump" class="wiz-textarea" data-step="5" placeholder="Zusätzliche Notiz zu Bestandsheizung oder Wärmepumpe..."></textarea>
                            </div>
                        </div>

                        <div class="wiz-section">
                            <div class="wiz-section-head">
                                <div class="wiz-section-title">Raumdaten / Heizlast / Fenster / Türen</div>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div class="wiz-section-counter">0/0</div>
                                    <button type="button" class="wiz-btn-secondary btn-sm" onclick="addWizRoom()">
                                        <i class="feather icon-plus"></i>
                                        Raum hinzufügen
                                    </button>
                                </div>
                            </div>

                            <div class="wiz-notice">
                                <div class="wiz-notice-icon">
                                    <i class="feather icon-grid"></i>
                                </div>
                                <div class="wiz-notice-content">
                                    <div class="wiz-notice-title">Raumdaten</div>
                                    <div class="wiz-notice-text">
                                        Zusätzliche Räume helfen bei genauerer Heizlast und technischer Bewertung.
                                    </div>
                                </div>
                            </div>

                            <div id="wiz-rooms-container"></div>

                            <div class="wiz-form-group">
                                <label class="wiz-label">Notiz Raumdaten / Heizlast / Fenster / Türen</label>
                                <textarea name="notice_room_heatingload_windows_doors" class="wiz-textarea" data-step="5" placeholder="Zusätzliche Notiz zu Räumen, Heizlast, Fenstern oder Türen..."></textarea>
                            </div>
                        </div>
                    </section>

                    {{-- STEP 6 --}}
                    <section class="wiz-step-container" id="wiz-step-6">
                        <div class="wiz-section">
                            <div class="wiz-section-head">
                                <div class="wiz-section-title">Elektroverteilung & Netz</div>
                                <div class="wiz-section-counter">0/0</div>
                            </div>

                            <div class="wiz-notice warning">
                                <div class="wiz-notice-icon">
                                    <i class="feather icon-zap"></i>
                                </div>
                                <div class="wiz-notice-content">
                                    <div class="wiz-notice-title">Elektroprüfung</div>
                                    <div class="wiz-notice-text">
                                        Angaben zu Zählerschrank, SLS und Reservekapazität bitte besonders sorgfältig prüfen.
                                    </div>
                                </div>
                            </div>

                            <div class="wiz-grid wiz-grid-4">
                                <div class="wiz-form-group">
                                    <label class="wiz-label">Maßnahme Zählerschrank *</label>
                                    <select name="meter_cabinet_action" class="wiz-select req-field" data-step="6">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Belassen">Belassen</option>
                                        <option value="Ertüchtigung">Ertüchtigung</option>
                                        <option value="Neu">Neu</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Größe Zählerschrank</label>
                                    <select name="cabinet_size" class="wiz-select" data-step="6">
                                        <option value="">Bitte wählen...</option>
                                        <option value="800x1100 (Standard)">800x1100 (Standard)</option>
                                        <option value="1100x1100 (Groß)">1100x1100 (Groß)</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">SLS-Schalter (A) *</label>
                                    <select name="sls_switch" class="wiz-select req-field" data-step="6">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Nicht vorhanden">Nicht vorhanden</option>
                                        <option value="35A">35A</option>
                                        <option value="50A">50A</option>
                                        <option value="63A">63A</option>
                                        <option value="Unklar">Unklar</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">APZ-Feld vorhanden?</label>
                                    <select name="apz_field" class="wiz-select" data-step="6">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Ja">Ja</option>
                                        <option value="Nein">Nein</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">AC Überspannungsschutz</label>
                                    <select name="ac_surge_protection" class="wiz-select" data-step="6">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Vorhanden">Vorhanden</option>
                                        <option value="Muss nachgerüstet werden">Muss nachgerüstet werden</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">§14a EnWG fähig</label>
                                    <select name="enwg_14a_ready" class="wiz-select" data-step="6">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Ja, vorbereitet">Ja, vorbereitet</option>
                                        <option value="Nein, muss nachgerüstet werden">Nein, muss nachgerüstet werden</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Anzahl Zähler</label>
                                    <input type="number" name="meter_count" class="wiz-input" data-step="6">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Netzreserve / Hausanschluss</label>
                                    <select name="grid_reserve" class="wiz-select" data-step="6">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Ausreichend">Ausreichend</option>
                                        <option value="Prüfen">Prüfen</option>
                                        <option value="Kritisch">Kritisch</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Technik-Standort</label>
                                    <select name="installation_location_power" class="wiz-select" data-step="6">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Keller">Keller</option>
                                        <option value="Erdgeschoss">Erdgeschoss</option>
                                        <option value="Garage">Garage</option>
                                        <option value="Sonstiges">Sonstiges</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Netzwerk / Internet</label>
                                    <select name="network_wlan" class="wiz-select" data-step="6">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Vorhanden">Vorhanden</option>
                                        <option value="Nicht vorhanden">Nicht vorhanden</option>
                                        <option value="Geplant">Geplant</option>
                                    </select>
                                </div>
                            </div>

                            <div class="wiz-chips">
                                <label class="wiz-chip">
                                    <input type="checkbox" name="tenant_model" value="1" data-step="6">
                                    <span>Mieterstrommodell</span>
                                </label>
                                <label class="wiz-chip">
                                    <input type="checkbox" name="load_management" value="1" data-step="6">
                                    <span>Lastmanagement</span>
                                </label>
                            </div>

                            <div class="wiz-form-group">
                                <label class="wiz-label">Notiz Elektroverteilung & Netz</label>
                                <textarea name="notice_electrical_grid" class="wiz-textarea" data-step="6" placeholder="Zusätzliche Notiz zu Zählerschrank, Netz oder Elektroverteilung..."></textarea>
                            </div>
                        </div>
                    </section>

                    {{-- STEP 7 --}}
                    <section class="wiz-step-container" id="wiz-step-7">
                        <div class="wiz-section">
                            <div class="wiz-section-head">
                                <div class="wiz-section-title">E-Mobilität & Wallbox</div>
                                <div class="wiz-section-counter">0/0</div>
                            </div>

                            <div class="wiz-notice">
                                <div class="wiz-notice-icon">
                                    <i class="feather icon-truck"></i>
                                </div>
                                <div class="wiz-notice-content">
                                    <div class="wiz-notice-title">Mobilität</div>
                                    <div class="wiz-notice-text">
                                        Angaben zu Fahrzeugzahl, Ladeort und Leitungsweg helfen bei der korrekten Auslegung der Wallbox.
                                    </div>
                                </div>
                            </div>

                            <div class="wiz-chips mb-3">
                                <label class="wiz-chip">
                                    <input type="radio" name="electric_car" value="Ja" data-step="7">
                                    <span>E-Auto vorhanden</span>
                                </label>
                                <label class="wiz-chip">
                                    <input type="radio" name="electric_car_plan" value="Geplant" data-step="7">
                                    <span>Geplant</span>
                                </label>
                                <label class="wiz-chip">
                                    <input type="radio" name="electric_car" value="Nein" checked data-step="7">
                                    <span>Kein E-Auto</span>
                                </label>
                            </div>

                            <div class="wiz-grid wiz-grid-4">
                                <div class="wiz-form-group">
                                    <label class="wiz-label">Fahrleistung (km/a)</label>
                                    <input type="number" name="car_kilo" class="wiz-input" data-step="7">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Anzahl Fahrzeuge</label>
                                    <input type="number" name="electric_car_count" class="wiz-input" data-step="7">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Wallboxen geplant</label>
                                    <input type="number" name="wallbox_count" class="wiz-input" data-step="7">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Gewünschte Ladeleistung</label>
                                    <select name="charging_power" class="wiz-select" data-step="7">
                                        <option value="">Bitte wählen...</option>
                                        <option value="11 kW (Standard)">11 kW (Standard)</option>
                                        <option value="22 kW (Genehmigungspflichtig)">22 kW (Genehmigungspflichtig)</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Ladeort</label>
                                    <select name="wallbox_location" class="wiz-select" data-step="7">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Garage">Garage</option>
                                        <option value="Carport">Carport</option>
                                        <option value="Außenwand">Außenwand</option>
                                        <option value="Stellplatz (Frei)">Stellplatz (Frei)</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Zugangskontrolle</label>
                                    <select name="access_control" class="wiz-select" data-step="7">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Ja, gewünscht">Ja, gewünscht</option>
                                        <option value="Nein, frei zugänglich">Nein, frei zugänglich</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Starkstrom vorhanden</label>
                                    <select name="heavy_current_cable" class="wiz-select" data-step="7">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Ja">Ja</option>
                                        <option value="Nein">Nein</option>
                                        <option value="Unklar">Unklar</option>
                                    </select>
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Leitungsweg / Länge</label>
                                    <input type="text" name="network_cable" class="wiz-input" data-step="7">
                                </div>
                            </div>

                            <div class="wiz-chips">
                                <label class="wiz-chip">
                                    <input type="checkbox" name="groundwork" value="1" data-step="7">
                                    <span>Erdarbeiten erforderlich</span>
                                </label>
                                <label class="wiz-chip">
                                    <input type="checkbox" name="bidirectional_car" value="1" data-step="7">
                                    <span>Bidirektionales Laden (V2G)</span>
                                </label>
                            </div>

                            <div class="wiz-form-group">
                                <label class="wiz-label">Notiz E-Mobilität & Wallbox</label>
                                <textarea name="notice_emobility_wallbox" class="wiz-textarea" data-step="7" placeholder="Zusätzliche Notiz zu E-Mobilität oder Wallbox..."></textarea>
                            </div>
                        </div>
                    </section>

                    {{-- STEP 8 --}}
                    <section class="wiz-step-container" id="wiz-step-8">
                        <div class="wiz-section">
                            <div class="wiz-section-head">
                                <div class="wiz-section-title">Unterlagen & Abschluss</div>
                                <div class="wiz-section-counter">0/0</div>
                            </div>

                            <div class="wiz-notice success">
                                <div class="wiz-notice-icon">
                                    <i class="feather icon-check-circle"></i>
                                </div>
                                <div class="wiz-notice-content">
                                    <div class="wiz-notice-title">Abschlussprüfung</div>
                                    <div class="wiz-notice-text">
                                        Vor dem Speichern bitte Unterlagen, Zusatzdaten und Abschlussbemerkung noch einmal kontrollieren.
                                    </div>
                                </div>
                            </div>

                            <div class="wiz-section-subtitle">Checkliste</div>
                            <div class="wiz-grid wiz-grid-3">
                                <label class="wiz-check-line"><input type="checkbox" name="documents_invoices" value="1"> Verbrauchsrechnungen vorliegend</label>
                                <label class="wiz-check-line"><input type="checkbox" name="documents_roof_images" value="1"> Dachbilder vorliegend</label>
                                <label class="wiz-check-line"><input type="checkbox" name="documents_meter_images" value="1"> Zählerschrankbilder vorliegend</label>
                                <label class="wiz-check-line"><input type="checkbox" name="documents_window_images" value="1"> Fensterbilder vorliegend</label>
                                <label class="wiz-check-line"><input type="checkbox" name="documents_heating_images" value="1"> Heizungs-/Technikbilder vorliegend</label>
                                <label class="wiz-check-line"><input type="checkbox" name="documents_facade_images" value="1"> Fassadenbilder vorliegend</label>
                                <label class="wiz-check-line"><input type="checkbox" name="site_visit_needed" value="1"> Vor-Ort-Termin nötig</label>
                                <label class="wiz-check-line"><input type="checkbox" name="ready_for_offer" value="1"> Planungsbereit (Daten komplett)</label>
                            </div>

                            <div class="wiz-section-subtitle">Zusatzdaten für Bad / Küche</div>
                            <div class="wiz-grid wiz-grid-3">
                                <div class="wiz-form-group">
                                    <label class="wiz-label">Anzahl Bäder</label>
                                    <input type="number" name="bathroom_count" class="wiz-input" data-step="8">
                                </div>

                                <div class="wiz-form-group">
                                    <label class="wiz-label">Anzahl Badewannen</label>
                                    <input type="number" name="bathtub_count" class="wiz-input" data-step="8">
                                </div>
                            </div>

                            <div class="wiz-form-group">
                                <label class="wiz-label">Abschlussbemerkung</label>
                                <textarea name="note" class="wiz-textarea" data-step="8" placeholder="Zusätzliche Notizen für Angebot, Planung oder Nachverfolgung..."></textarea>
                            </div>

                            <div class="wiz-form-group">
                                <label class="wiz-label">Notiz Unterlagen & Abschluss</label>
                                <textarea name="notice_documents_completion" class="wiz-textarea" data-step="8" placeholder="Zusätzliche Notiz zu Unterlagen oder Abschluss..."></textarea>
                            </div>

                            <div class="wiz-footer-actions">
                                <button type="button" class="wiz-btn-secondary" onclick="navToWizardStep(Math.max(1, currentWizStep - 1))">
                                    <i class="feather icon-chevron-left"></i>
                                    Zurück
                                </button>

                                <button type="button" class="wiz-btn-primary" id="wizFinalSubmitBtn" onclick="submitBladeWizard()">
                                    <i class="feather icon-save"></i>
                                    Daten speichern & In Termin übernehmen
                                </button>
                            </div>
                        </div>
                    </section>

                </form>
            </div>

            {{-- RIGHT SIDEBAR --}}
            <aside class="wiz-sidebar-right">
                <div class="wiz-right-header">
                    <i class="feather icon-check-square text-success"></i>
                    Datenpflege
                </div>
                <div class="wiz-right-content" id="wiz-missing-container"></div>
            </aside>

        </div>
    </div>
</div>

@push('scripts')
<script
    src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&libraries=places&v=weekly&callback=initWizardAddressAutocomplete"
    async
    defer>
</script>
<script>
    window.WIZARD_PRODUCTS = @json($wizardProducts ?? []);
    window.authEmployeeId = @json(auth()->id());
    window.csrfToken = @json(csrf_token());

    window.WIZARD_ROUTES = {
        productServicesBase: @json(url('/calendar/products')),
    };
</script>

<script>
(() => {
    let currentWizStep = 1;
    let roofCount = 0;
    let roomCount = 0;
    let activeWizardSteps = [1, 8];

    const WIZ_STEPS = [
        { id: 1, title: 'Projektstart' },
        { id: 2, title: 'Gebäudeprofil' },
        { id: 3, title: 'Gebäudehülle' },
        { id: 4, title: 'Dach & PV' },
        { id: 5, title: 'Heizung & WP' },
        { id: 6, title: 'Elektro & Netz' },
        { id: 7, title: 'E-Mobilität' },
        { id: 8, title: 'Abschluss' }
    ];

    const WIZ_PRODUCT_STEP_MAP = {
        default: [1, 8],

        'PHOTOVOLTAIK': [1, 2, 4, 6, 8],
        'PHOTOVOLTAIK & BATTERIE': [1, 2, 4, 6, 8],
        'BATTERIESPEICHER': [1, 2, 4, 6, 8],
        'SOLAR CARPORT': [1, 2, 4, 6, 8],

        'WÄRMEPUMPE': [1, 2, 3, 5, 6, 8],
        'WARTUNG WÄRMEPUMPE': [1, 5, 8],
        'HEIZLASTBERECHNUNG': [1, 2, 3, 5, 8],

        'WALLBOX': [1, 6, 7, 8],
        'ZÄHLERSCHRANK': [1, 6, 8],
        'UNTERVERTEILUNG': [1, 6, 8],
        'ELEKTROROHRINSTALLATION': [1, 6, 8],

        'BADPLANUNG': [1, 2, 8],
        'BADSANIERUNG': [1, 2, 8],
        'KÜCHE': [1, 2, 8],
        'SANITÄR ROHRINSTALLATION': [1, 2, 5, 8],
        'FLIESEN': [1, 2, 8],
        'PARKETT': [1, 2, 8],
        'TAPETE': [1, 2, 8],

        'FENSTER': [1, 2, 3, 8],
        'TÜR': [1, 2, 3, 8],
        'INSEKTENSCHUTZ': [1, 2, 3, 8],
        'MARKISE': [1, 2, 4, 8],
        'ROLLLADEN': [1, 2, 3, 8],
        'SONNENSCHUTZ': [1, 2, 3, 4, 8],

        'SOFTWARE': [1, 8],
        'SOFTWARE HOUSE': [1, 8],
        'NEWS': [1, 8],
        'SHK-KLEINAUFTRÄGE': [1, 2, 5, 6, 8]
    };

    const WIZ_QUICK_PRODUCT_KEYS = [
        'PHOTOVOLTAIK',
        'WÄRMEPUMPE',
        'WALLBOX',
        'FENSTER',
        'TÜR',
        'BATTERIESPEICHER'
    ];

    function normalizeSearchText(value) {
        return String(value ?? '')
            .trim()
            .toUpperCase()
            .replace(/Ä/g, 'AE')
            .replace(/Ö/g, 'OE')
            .replace(/Ü/g, 'UE')
            .replace(/ß/g, 'SS');
    }

    function getProductSearchTokens(product) {
        return [
            product?.name,
            product?.article_group,
            product?.title,
            product?.product,
            product?.category
        ]
            .filter(Boolean)
            .map(normalizeSearchText)
            .join(' | ');
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function valOrNull(selector) {
        const $el = $(selector);
        if (!$el.length) return null;

        const value = $el.val();
        return value === '' || value === undefined ? null : value;
    }

    function boolNum(selector) {
        return $(selector).is(':checked') ? 1 : 0;
    }

    function normalizeProductName(product) {
        return product?.name || product?.article_group || product?.title || `Produkt #${product?.id ?? ''}`;
    }

    function normalizeServiceName(service) {
        return service?.name || service?.phase_section || service?.title || `Service #${service?.id ?? ''}`;
    }

    function normalizeEmployeeName(employee) {
        return employee?.name || [employee?.firstname, employee?.lastname].filter(Boolean).join(' ') || `Mitarbeiter #${employee?.id ?? ''}`;
    }

    function employeeOptionLabel(employee, roleType = '') {
        const name = normalizeEmployeeName(employee);
        if (roleType === 'internal') return `${name} (Innendienst)`;
        if (roleType === 'external') return `${name} (Außendienst)`;
        return name;
    }

    function normalizeProductKey(product) {
        return String(
            product?.name ||
            product?.article_group ||
            product?.title ||
            ''
        ).trim().toUpperCase();
    }

    function getWizardProducts() {
        return Array.isArray(window.WIZARD_PRODUCTS) ? window.WIZARD_PRODUCTS : [];
    }

    function findWizardProduct(productId) {
        return getWizardProducts().find(product => String(product.id) === String(productId)) || null;
    }

    function findWizardProductByQuickKey(productKey) {
        const wanted = normalizeSearchText(productKey);
        const products = getWizardProducts();

        const exact = products.find(product => {
            const key = normalizeSearchText(
                product?.name ||
                product?.article_group ||
                product?.title ||
                ''
            );
            return key === wanted;
        });

        if (exact) return exact;

        const contains = products.find(product => {
            const haystack = getProductSearchTokens(product);

            if (wanted === 'BATTERIESPEICHER') {
                return haystack.includes('BATTERIESPEICHER') || haystack.includes('BATTERIE');
            }

            if (wanted === 'PHOTOVOLTAIK') {
                return haystack.includes('PHOTOVOLTAIK') || haystack.includes('PV');
            }

            if (wanted === 'TÜR') {
                return haystack.includes('TUER') || haystack.includes('TÜR');
            }

            return haystack.includes(wanted);
        });

        return contains || null;
    }

    function updateQuickProductButtonsState() {
        $('.wiz-quick-product-btn').each(function () {
            const wantedKey = $(this).data('product-key');
            const product = findWizardProductByQuickKey(wantedKey);

            $(this).removeClass('is-active is-missing');

            if (!product) {
                $(this).addClass('is-missing');
                return;
            }

            const exists = $(`.wiz-product-row[data-product-id="${product.id}"]`).length > 0;
            if (exists) {
                $(this).addClass('is-active');
            }
        });
    }

    function getProductSteps(product) {
        const key = normalizeProductKey(product);
        return WIZ_PRODUCT_STEP_MAP[key] || WIZ_PRODUCT_STEP_MAP.default;
    }

    function getSelectedWizardProducts() {
        return $('.wiz-product-row').map(function () {
            const productId = $(this).data('product-id');
            return findWizardProduct(productId);
        }).get().filter(Boolean);
    }

    function getActiveWizardStepsFromProducts() {
        const selectedProducts = getSelectedWizardProducts();

        if (!selectedProducts.length) {
            return [1, 8];
        }

        const steps = new Set();

        selectedProducts.forEach(product => {
            getProductSteps(product).forEach(stepId => steps.add(stepId));
        });

        return Array.from(steps).sort((a, b) => a - b);
    }

    function getFirstVisibleWizardStep() {
        return activeWizardSteps.length ? activeWizardSteps[0] : 1;
    }

    function isStepVisible(stepId) {
        return activeWizardSteps.includes(Number(stepId));
    }

    function getNextVisibleStep(currentStep) {
        const sorted = [...activeWizardSteps].sort((a, b) => a - b);
        const index = sorted.indexOf(Number(currentStep));

        if (index === -1) return getFirstVisibleWizardStep();
        return sorted[index + 1] || sorted[index] || getFirstVisibleWizardStep();
    }

    function getPrevVisibleStep(currentStep) {
        const sorted = [...activeWizardSteps].sort((a, b) => a - b);
        const index = sorted.indexOf(Number(currentStep));

        if (index === -1) return getFirstVisibleWizardStep();
        return sorted[index - 1] || sorted[0] || getFirstVisibleWizardStep();
    }

    function goToWizStep(step) {
        step = Number(step);

        if (!isStepVisible(step)) {
            step = getFirstVisibleWizardStep();
        }

        currentWizStep = step;

        $('.wiz-nav-btn').removeClass('active');
        $(`.wiz-nav-btn[data-target="${step}"]`).addClass('active');

        $('.wiz-step-container').each(function () {
            const stepId = parseInt((this.id || '').replace('wiz-step-', ''), 10);

            if (stepId === step && isStepVisible(stepId)) {
                $(this).addClass('active').show();
            } else {
                $(this).removeClass('active').hide();
            }
        });

        if (window.feather) feather.replace();
    }

    function navToWizardStep(step) {
        step = Number(step);
        if (!isStepVisible(step)) return;
        goToWizStep(step);
    }

    function updateWizardStepRequiredState() {
        $('.req-field').each(function () {
            const stepId = parseInt($(this).data('step'), 10);

            if (isStepVisible(stepId)) {
                $(this).data('wizard-required-active', '1');
            } else {
                $(this).data('wizard-required-active', '0');

                if (!['checkbox', 'radio'].includes(($(this).attr('type') || '').toLowerCase())) {
                    $(this).removeClass('wiz-focus-highlight');
                }
            }
        });
    }

    function applyWizardStepVisibility() {
        activeWizardSteps = getActiveWizardStepsFromProducts();

        $('.wiz-nav-btn').each(function () {
            const stepId = parseInt($(this).data('target'), 10);
            $(this).toggle(isStepVisible(stepId));
        });

        $('.wiz-step-container').each(function () {
            const stepId = parseInt((this.id || '').replace('wiz-step-', ''), 10);

            if (isStepVisible(stepId)) {
                $(this).removeClass('wiz-step-hidden');
            } else {
                $(this).removeClass('active').addClass('wiz-step-hidden').hide();
            }
        });

        if (!isStepVisible(currentWizStep)) {
            currentWizStep = getFirstVisibleWizardStep();
        }

        goToWizStep(currentWizStep);
        updateWizardStepRequiredState();
    }

    function updateStepCounters() {
        WIZ_STEPS.forEach(step => {
            const $stepContainer = $(`#wiz-step-${step.id}`);
            if (!$stepContainer.length) return;

            const $counter = $(`[data-step-counter="${step.id}"]`);
            const $bar = $(`[data-step-progress="${step.id}"]`);

            if (!isStepVisible(step.id)) {
                $counter.text('—').removeClass('is-complete is-empty');
                $bar.css('width', '0%');
                return;
            }

            const $fields = $stepContainer.find('input, select, textarea').filter(function () {
                const type = ($(this).attr('type') || '').toLowerCase();
                if (type === 'hidden') return false;
                if ($(this).is(':disabled')) return false;
                return true;
            });

            let total = 0;
            let filled = 0;

            $fields.each(function () {
                const $field = $(this);
                const type = ($field.attr('type') || '').toLowerCase();
                const name = $field.attr('name');

                if (!name) return;

                if (type === 'radio') {
                    if ($stepContainer.find(`input[type="radio"][name="${name}"]`).first()[0] !== this) return;
                    total++;
                    if ($stepContainer.find(`input[type="radio"][name="${name}"]:checked`).length) filled++;
                    return;
                }

                if (type === 'checkbox') {
                    total++;
                    if ($field.is(':checked')) filled++;
                    return;
                }

                total++;
                const value = $field.val();

                if (value !== null && value !== undefined && String(value).trim() !== '') {
                    filled++;
                }
            });

            const percent = total > 0 ? Math.round((filled / total) * 100) : 0;

            $counter.text(`${filled}/${total}`).removeClass('is-complete is-empty');

            if (filled === 0) {
                $counter.addClass('is-empty');
            } else if (total > 0 && filled === total) {
                $counter.addClass('is-complete');
            }

            $bar.css('width', `${percent}%`);
        });
    }

    function updateSectionCounters() {
        $('.wiz-section').each(function () {
            const $section = $(this);
            const firstFieldStep = parseInt($section.find('[data-step]').first().data('step'), 10);

            if (firstFieldStep && !isStepVisible(firstFieldStep)) {
                return;
            }

            const $fields = $section.find('input, select, textarea').filter(function () {
                const type = ($(this).attr('type') || '').toLowerCase();
                if (type === 'hidden') return false;
                if ($(this).is(':disabled')) return false;
                return true;
            });

            let total = 0;
            let filled = 0;

            $fields.each(function () {
                const $field = $(this);
                const type = ($field.attr('type') || '').toLowerCase();
                const name = $field.attr('name');

                if (!name) return;

                if (type === 'radio') {
                    if ($section.find(`input[type="radio"][name="${name}"]`).first()[0] !== this) return;
                    total++;
                    if ($section.find(`input[type="radio"][name="${name}"]:checked`).length) filled++;
                    return;
                }

                if (type === 'checkbox') {
                    total++;
                    if ($field.is(':checked')) filled++;
                    return;
                }

                total++;
                const value = $field.val();

                if (value !== null && value !== undefined && String(value).trim() !== '') {
                    filled++;
                }
            });

            const $counter = $section.find('.wiz-section-counter').first();
            if (!$counter.length) return;

            $counter.text(`${filled}/${total}`).removeClass('is-complete is-empty');

            if (filled === 0) {
                $counter.addClass('is-empty');
            } else if (total > 0 && filled === total) {
                $counter.addClass('is-complete');
            }
        });
    }

    function toggleCompanyField() {
        const buildingType = $('select[name="building_type"]').val();

        if (buildingType === 'Gewerbe') {
            $('.wiz-company-wrap').removeClass('d-none');
        } else {
            $('.wiz-company-wrap').addClass('d-none');
            $('input[name="object_name"]').val('');
        }
    }

    function populateWizardProductAdder() {
        const $adder = $('#wiz-product-adder');
        if (!$adder.length) return;

        let html = '<option value="">Produkt wählen...</option>';

        getWizardProducts().forEach(product => {
            html += `<option value="${escapeHtml(product.id)}">${escapeHtml(normalizeProductName(product))}</option>`;
        });

        $adder.html(html);

        if ($adder.data('select2')) {
            $adder.select2('destroy');
        }

        $adder.select2({
            width: '100%',
            placeholder: 'Produkt wählen...',
            dropdownParent: $('#customBladeWizard')
        });
    }

    function buildServiceOptionsForProduct(services = [], selectedId = '') {
        let html = '<option value="">Service wählen...</option>';

        services.forEach(service => {
            const serviceName = normalizeServiceName(service);
            const selected = String(selectedId) === String(service.id) ? 'selected' : '';
            html += `<option value="${escapeHtml(service.id)}" ${selected}>${escapeHtml(serviceName)}</option>`;
        });

        return html;
    }

    function buildEmployeeOptionsFromService(employees = [], roleType = '', selectedId = '') {
        let html = `<option value="">${roleType === 'external' ? 'Außendienst wählen...' : 'Innendienst wählen...'}</option>`;

        employees.forEach(employee => {
            const selected = String(selectedId) === String(employee.id) ? 'selected' : '';
            html += `<option value="${escapeHtml(employee.id)}" ${selected}>${escapeHtml(employeeOptionLabel(employee, roleType))}</option>`;
        });

        return html;
    }

    function getProductServicesUrl(productId) {
        return `${window.WIZARD_ROUTES.productServicesBase}/${productId}/services`;
    }

    function getServiceEmployeesUrl(productId, serviceId) {
        return `${window.WIZARD_ROUTES.productServicesBase}/${productId}/services/${serviceId}/employees`;
    }

    function loadServicesForProduct(productId) {
        return $.ajax({
            url: getProductServicesUrl(productId),
            type: 'GET',
            dataType: 'json'
        });
    }

    function loadEmployeesForService(productId, serviceId) {
        return $.ajax({
            url: getServiceEmployeesUrl(productId, serviceId),
            type: 'GET',
            dataType: 'json'
        });
    }

    function initRowSelect2($scope) {
        $scope.find('.wiz-product-service, .wiz-product-outside, .wiz-product-inside').each(function () {
            const $el = $(this);

            if ($el.data('select2')) {
                $el.select2('destroy');
            }

            $el.select2({
                width: '100%',
                dropdownParent: $('#customBladeWizard')
            });
        });
    }

    function updateProductSummary() {
        const count = $('.wiz-product-row').length;
        $('#wiz-product-summary').text(
            count ? `${count} Produkt(e) hinzugefügt` : 'Noch kein Produkt hinzugefügt'
        );
    }

    function updateSingleProductProgress($row) {
        const $fields = $row.find('.req-field');
        const total = $fields.length || 1;
        let filled = 0;

        $fields.each(function () {
            if ($(this).val()) filled++;
        });

        const percent = Math.round((filled / total) * 100);
        $row.find('.wiz-product-fillbar-value').css('width', `${percent}%`);
        $row.find('.wiz-product-fillbar-text').text(`${filled}/${total}`);
    }

    function refreshEmployeeSelectsForRow($row, keepSelected = false) {
        const productId = $row.data('product-id');
        const serviceId = $row.find('.wiz-product-service').val() || '';

        const $inside = $row.find('.wiz-product-inside');
        const $outside = $row.find('.wiz-product-outside');

        const oldInside = keepSelected ? ($inside.val() || '') : '';
        const oldOutside = keepSelected ? ($outside.val() || '') : '';

        if (!productId || !serviceId) {
            if ($inside.data('select2')) $inside.select2('destroy');
            if ($outside.data('select2')) $outside.select2('destroy');

            $inside.html(buildEmployeeOptionsFromService([], 'internal', ''));
            $outside.html(buildEmployeeOptionsFromService([], 'external', ''));

            $inside.select2({ width: '100%', dropdownParent: $('#customBladeWizard') });
            $outside.select2({ width: '100%', dropdownParent: $('#customBladeWizard') });

            updateSingleProductProgress($row);
            return;
        }

        $row.addClass('is-loading');

        loadEmployeesForService(productId, serviceId)
            .done(function (res) {
                const internalEmployees = Array.isArray(res.internal_employees) ? res.internal_employees : [];
                const externalEmployees = Array.isArray(res.external_employees) ? res.external_employees : [];

                const selectedInside = keepSelected && oldInside ? oldInside : (res.auto_internal_employee_id || '');
                const selectedOutside = keepSelected && oldOutside ? oldOutside : (res.auto_external_employee_id || '');

                if ($inside.data('select2')) $inside.select2('destroy');
                if ($outside.data('select2')) $outside.select2('destroy');

                $inside.html(buildEmployeeOptionsFromService(internalEmployees, 'internal', selectedInside));
                $outside.html(buildEmployeeOptionsFromService(externalEmployees, 'external', selectedOutside));

                $inside.select2({ width: '100%', dropdownParent: $('#customBladeWizard') });
                $outside.select2({ width: '100%', dropdownParent: $('#customBladeWizard') });

                $inside.val(String(selectedInside)).trigger('change.select2');
                $outside.val(String(selectedOutside)).trigger('change.select2');

                updateSingleProductProgress($row);
                updateWizProgress();
            })
            .fail(function (xhr) {
                console.error(xhr.responseText || xhr);
                Swal.fire('Fehler', 'Mitarbeiter konnten nicht geladen werden.', 'error');
            })
            .always(function () {
                $row.removeClass('is-loading');
            });
    }

    function addProductRow(productId) {
        if (!productId) return;

        const exists = $(`.wiz-product-row[data-product-id="${productId}"]`).length;
        if (exists) {
            Swal.fire('Schon vorhanden', 'Dieses Produkt wurde bereits hinzugefügt.', 'info');
            return;
        }

        const product = findWizardProduct(productId);
        if (!product) return;

        const productName = normalizeProductName(product);

        const rowHtml = `
            <div class="wiz-dynamic-card wiz-product-row" data-product-id="${escapeHtml(productId)}">
                <div class="wiz-product-row-headline">
                    <div>
                        <div class="wiz-card-title" style="margin-bottom:4px;">${escapeHtml(productName)}</div>
                        <div style="font-size:11px; color:var(--wiz-muted); font-weight:700;">
                            Direkte Produktzuweisung
                        </div>
                    </div>

                    <div class="wiz-product-actions">
                        <button type="button" class="wiz-mini-btn wiz-product-collapse-btn">Einklappen</button>
                        <button type="button" class="wiz-mini-btn wiz-mini-btn-danger wiz-product-remove-btn">Löschen</button>
                    </div>
                </div>

                <div class="wiz-product-body">
                    <div class="wiz-grid wiz-grid-3" style="margin-bottom:0;">
                        <div class="wiz-form-group">
                            <label class="wiz-label">Service *</label>
                            <select class="wiz-select wiz-product-service req-field" data-step="1">
                                <option value="">Service wird geladen...</option>
                            </select>
                        </div>

                        <div class="wiz-form-group">
                            <label class="wiz-label">Innendienst</label>
                            <select class="wiz-select wiz-product-inside" data-step="1">
                                <option value="">Bitte zuerst Service wählen...</option>
                            </select>
                        </div>

                        <div class="wiz-form-group">
                            <label class="wiz-label">Außendienst *</label>
                            <select class="wiz-select wiz-product-outside req-field" data-step="1">
                                <option value="">Bitte zuerst Service wählen...</option>
                            </select>
                        </div>
                    </div>

                    <div class="wiz-product-fillbar">
                        <div class="wiz-product-fillbar-track">
                            <div class="wiz-product-fillbar-value"></div>
                        </div>
                        <div class="wiz-product-fillbar-text">0/2</div>
                    </div>
                </div>
            </div>
        `;

        $('#wiz-product-assignments').append(rowHtml);

        const $row = $(`.wiz-product-row[data-product-id="${productId}"]`);

        initRowSelect2($row);
        updateProductSummary();
        applyWizardStepVisibility();
        updateSingleProductProgress($row);
        updateWizProgress();
        updateQuickProductButtonsState();

        loadServicesForProduct(productId)
            .done(function (res) {
                const services = Array.isArray(res.services) ? res.services : [];
                const $service = $row.find('.wiz-product-service');

                const defaultCompleteService = services.find(service => {
                    const raw = [
                        service?.name,
                        service?.phase_section,
                        service?.title
                    ].filter(Boolean).join(' ');

                    return normalizeSearchText(raw).includes('COMPLETE');
                });

                const selectedServiceId = defaultCompleteService
                    ? String(defaultCompleteService.id)
                    : (services.length ? String(services[0].id) : '');

                if ($service.data('select2')) {
                    $service.select2('destroy');
                }

                $service.html(buildServiceOptionsForProduct(services, selectedServiceId));

                $service.select2({
                    width: '100%',
                    dropdownParent: $('#customBladeWizard')
                });

                if (selectedServiceId) {
                    $service.val(selectedServiceId).trigger('change');
                } else {
                    updateSingleProductProgress($row);
                    updateWizProgress();
                }

                updateQuickProductButtonsState();
            })
            .fail(function (xhr) {
                console.error(xhr.responseText || xhr);
                Swal.fire('Fehler', 'Services konnten nicht geladen werden.', 'error');
            });
    }

    function addWizRoof() {
        roofCount++;

        const html = `
            <div class="wiz-dynamic-card wiz-roof-card" data-roof-index="${roofCount}">
                <div class="wiz-product-row-headline">
                    <div>
                        <div class="wiz-card-title" style="margin-bottom:4px;">Dach ${roofCount}</div>
                        <div style="font-size:11px; color:var(--wiz-muted); font-weight:700;">
                            Dachfläche erfassen
                        </div>
                    </div>
                    <div class="wiz-product-actions">
                        <button type="button" class="wiz-mini-btn wiz-roof-collapse-btn">Einklappen</button>
                        <button type="button" class="wiz-mini-btn wiz-mini-btn-danger wiz-roof-remove-btn">Löschen</button>
                    </div>
                </div>

                <div class="wiz-roof-body">
                    <div class="wiz-grid wiz-grid-4">
                        <div class="wiz-form-group">
                            <label class="wiz-label">Bezeichnung</label>
                            <input type="text" class="wiz-input wiz-roof-designation" placeholder="z.B. Hauptdach">
                        </div>

                        <div class="wiz-form-group">
                            <label class="wiz-label">Ausrichtung</label>
                            <select class="wiz-select wiz-roof-orientation">
                                <option value="">Bitte wählen...</option>
                                <option value="Nord">Nord</option>
                                <option value="Nordost">Nordost</option>
                                <option value="Ost">Ost</option>
                                <option value="Südost">Südost</option>
                                <option value="Süd">Süd</option>
                                <option value="Südwest">Südwest</option>
                                <option value="West">West</option>
                                <option value="Nordwest">Nordwest</option>
                            </select>
                        </div>

                        <div class="wiz-form-group">
                            <label class="wiz-label">Neigung (°)</label>
                            <input type="number" class="wiz-input wiz-roof-pitch">
                        </div>

                        <div class="wiz-form-group">
                            <label class="wiz-label">Fläche (m²)</label>
                            <input type="number" class="wiz-input wiz-roof-area">
                        </div>

                        <div class="wiz-form-group">
                            <label class="wiz-label">Dachart</label>
                            <select class="wiz-select wiz-roof-type">
                                <option value="">Bitte wählen...</option>
                                <option value="Satteldach">Satteldach</option>
                                <option value="Pultdach">Pultdach</option>
                                <option value="Flachdach">Flachdach</option>
                                <option value="Walmdach">Walmdach</option>
                                <option value="Sonstiges">Sonstiges</option>
                            </select>
                        </div>

                        <div class="wiz-form-group">
                            <label class="wiz-label">Eindeckung</label>
                            <input type="text" class="wiz-input wiz-roof-covering-name" placeholder="z.B. Ziegel">
                        </div>

                        <div class="wiz-form-group">
                            <label class="wiz-label">Verschattung</label>
                            <select class="wiz-select wiz-roof-shadow">
                                <option value="">Bitte wählen...</option>
                                <option value="Keine">Keine</option>
                                <option value="Leicht">Leicht</option>
                                <option value="Mittel">Mittel</option>
                                <option value="Stark">Stark</option>
                            </select>
                        </div>

                        <div class="wiz-form-group">
                            <label class="wiz-label">Notiz</label>
                            <input type="text" class="wiz-input wiz-roof-notes">
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#wiz-roofs-container').append(html);
        updateWizProgress();
        if (window.feather) feather.replace();
    }

    function addWizRoom() {
        roomCount++;

        const html = `
            <div class="wiz-dynamic-card wiz-room-card" data-room-index="${roomCount}">
                <div class="wiz-product-row-headline">
                    <div>
                        <div class="wiz-card-title" style="margin-bottom:4px;">Raum ${roomCount}</div>
                        <div style="font-size:11px; color:var(--wiz-muted); font-weight:700;">
                            Raumdaten erfassen
                        </div>
                    </div>
                    <div class="wiz-product-actions">
                        <button type="button" class="wiz-mini-btn wiz-room-collapse-btn">Einklappen</button>
                        <button type="button" class="wiz-mini-btn wiz-mini-btn-danger wiz-room-remove-btn">Löschen</button>
                    </div>
                </div>

                <div class="wiz-room-body">
                    <div class="wiz-grid wiz-grid-4">
                        <div class="wiz-form-group">
                            <label class="wiz-label">Raumname</label>
                            <input type="text" class="wiz-input wiz-room-name" placeholder="z.B. Wohnzimmer">
                        </div>

                        <div class="wiz-form-group">
                            <label class="wiz-label">Fläche (m²)</label>
                            <input type="number" class="wiz-input wiz-room-area">
                        </div>

                        <div class="wiz-form-group">
                            <label class="wiz-label">Heizkörper</label>
                            <select class="wiz-select wiz-room-heating">
                                <option value="">Bitte wählen...</option>
                                <option value="Heizkörper">Heizkörper</option>
                                <option value="Fußbodenheizung">Fußbodenheizung</option>
                                <option value="Beides">Beides</option>
                                <option value="Keins">Keins</option>
                            </select>
                        </div>

                        <div class="wiz-form-group">
                            <label class="wiz-label">Fensteranzahl</label>
                            <input type="number" class="wiz-input wiz-room-windows">
                        </div>

                        <div class="wiz-form-group">
                            <label class="wiz-label">Außenwand</label>
                            <select class="wiz-select wiz-room-outer-wall">
                                <option value="">Bitte wählen...</option>
                                <option value="Ja">Ja</option>
                                <option value="Nein">Nein</option>
                            </select>
                        </div>

                        <div class="wiz-form-group">
                            <label class="wiz-label">Solltemperatur (°C)</label>
                            <input type="number" class="wiz-input wiz-room-target-temp">
                        </div>

                        <div class="wiz-form-group">
                            <label class="wiz-label">Tür vorhanden</label>
                            <select class="wiz-select wiz-room-door">
                                <option value="">Bitte wählen...</option>
                                <option value="Ja">Ja</option>
                                <option value="Nein">Nein</option>
                            </select>
                        </div>

                        <div class="wiz-form-group">
                            <label class="wiz-label">Notiz</label>
                            <input type="text" class="wiz-input wiz-room-note">
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#wiz-rooms-container').append(html);
        updateWizProgress();
        if (window.feather) feather.replace();
    }

    function collectRoofs() {
        const roofs = [];

        $('#wiz-roofs-container .wiz-roof-card').each(function () {
            const $card = $(this);

            roofs.push({
                designation: $card.find('.wiz-roof-designation').val() || null,
                roof_orientation: $card.find('.wiz-roof-orientation').val() || null,
                roof_pitch: $card.find('.wiz-roof-pitch').val() || null,
                roof_area: $card.find('.wiz-roof-area').val() || null,
                roof_type: $card.find('.wiz-roof-type').val() || null,
                roof_covering_name: $card.find('.wiz-roof-covering-name').val() || null,
                shading: $card.find('.wiz-roof-shadow').val() || null,
                notes: $card.find('.wiz-roof-notes').val() || null
            });
        });

        return roofs;
    }

    function collectRooms() {
        const rooms = [];

        $('#wiz-rooms-container .wiz-room-card').each(function () {
            const $card = $(this);

            rooms.push({
                name: $card.find('.wiz-room-name').val() || null,
                area: $card.find('.wiz-room-area').val() || null,
                heating: $card.find('.wiz-room-heating').val() || null,
                windows: $card.find('.wiz-room-windows').val() || null,
                outer_wall: $card.find('.wiz-room-outer-wall').val() || null,
                target_temp: $card.find('.wiz-room-target-temp').val() || null,
                door: $card.find('.wiz-room-door').val() || null,
                note: $card.find('.wiz-room-note').val() || null
            });
        });

        return rooms;
    }

    function collectProductAssignments() {
        const assignments = [];

        $('.wiz-product-row').each(function () {
            assignments.push({
                product_id: $(this).data('product-id') || null,
                service_id: $(this).find('.wiz-product-service').val() || null,
                employee_id: $(this).find('.wiz-product-inside').val() || null,
                field_employee: $(this).find('.wiz-product-outside').val() || null
            });
        });

        return assignments;
    }

    function getAssignedEmployeeIds() {
        const ids = [];

        $('.wiz-product-inside, .wiz-product-outside').each(function () {
            const value = $(this).val();
            if (value) ids.push(String(value));
        });

        return [...new Set(ids)];
    }

    function enforceEmployeeLimit() {
        $('.wiz-limit-warning').remove();

        const assignedIds = getAssignedEmployeeIds();

        if (assignedIds.length <= 2) return true;

        $('#wiz-product-assignments-wrap').append(`
            <div class="wiz-limit-warning">
                Es sind maximal 2 unterschiedliche Mitarbeiter insgesamt erlaubt. Bitte nur einen Innendienst und einen Außendienst auswählen.
            </div>
        `);

        return false;
    }

    function buildRightPanelHeader() {
        return `
            <div id="wiz-right-panel-inner">
                <div class="wiz-right-stats">
                    <div class="wiz-stat-box">
                        <strong id="wiz-stat-total">0</strong>
                        <span>Gesamt</span>
                    </div>
                    <div class="wiz-stat-box">
                        <strong id="wiz-stat-filled">0</strong>
                        <span>Gefüllt</span>
                    </div>
                    <div class="wiz-stat-box">
                        <strong id="wiz-stat-missing">0</strong>
                        <span>Fehlt</span>
                    </div>
                </div>
                <div id="wiz-right-missing-list"></div>
            </div>
        `;
    }

    function updateWizProgress() {
        let totalReq = $('.req-field').filter(function () {
            return $(this).data('wizard-required-active') == '1';
        }).length;

        let filledReq = 0;

        $('#wiz-missing-container').html(buildRightPanelHeader());
        $('#wiz-right-missing-list').empty();

        WIZ_STEPS.forEach(step => {
            if (!isStepVisible(step.id)) return;

            const $fieldsInStep = $(`.req-field[data-step="${step.id}"]`).filter(function () {
                return $(this).data('wizard-required-active') == '1';
            });

            if (!$fieldsInStep.length) return;

            let missingHtml = '';

            $fieldsInStep.each(function () {
                const $field = $(this);
                const isCheckbox = ($field.attr('type') || '').toLowerCase() === 'checkbox';
                const value = $field.val();
                const isFilled = isCheckbox ? $field.is(':checked') : (value !== '' && value !== null);

                if (isFilled) {
                    filledReq++;
                    return;
                }

                let label = '';

                if ($field.closest('.wiz-form-group').find('.wiz-label').length) {
                    label = $field.closest('.wiz-form-group').find('.wiz-label').first().text().replace('*', '').trim();
                } else {
                    label = $field.closest('label').text().replace('*', '').trim();
                }

                if (!label) {
                    label = $field.attr('name') || 'Feld';
                }

                const fieldName = $field.attr('name') || '';
                const fieldId = $field.attr('id') || '';

                missingHtml += `
                    <div class="wiz-missing-item"
                        data-target-step="${step.id}"
                        data-target-name="${escapeHtml(fieldName)}"
                        data-target-id="${escapeHtml(fieldId)}">
                        <span>${escapeHtml(label)}</span>
                        <i class="feather icon-edit-2"></i>
                    </div>
                `;
            });

            if (missingHtml) {
                $('#wiz-right-missing-list').append(`
                    <div class="wiz-missing-block" data-step-block="${step.id}">
                        <div class="wiz-missing-header">
                            <span>Schritt ${step.id}: ${escapeHtml(step.title)}</span>
                            <span class="wiz-missing-toggle">
                                <i class="feather icon-chevron-down"></i>
                            </span>
                        </div>
                        <div class="wiz-missing-body">${missingHtml}</div>
                    </div>
                `);
            }
        });

        if (totalReq === 0) totalReq = 1;

        const percent = Math.round((filledReq / totalReq) * 100);

        $('#wizGlobalProgressText').text(`${filledReq}/${totalReq} Pflichtfelder`);
        $('#wizGlobalProgressBar').css('width', `${percent}%`);

        $('#wiz-stat-total').text(totalReq);
        $('#wiz-stat-filled').text(filledReq);
        $('#wiz-stat-missing').text(totalReq - filledReq);

        if (percent === 100) {
            $('#wiz-right-missing-list').html(`
                <div style="text-align:center; padding:22px; color:var(--wiz-green); font-weight:bold;">
                    <i class="feather icon-check-circle" style="font-size:24px;"></i><br>
                    Alle Pflichtfelder ausgefüllt
                </div>
            `);
        }

        $('.wiz-product-row').each(function () {
            updateSingleProductProgress($(this));
        });

        updateSectionCounters();
        updateStepCounters();

        if (window.feather) feather.replace();
    }

    function focusWizardField(step, fieldName = '', fieldId = '') {
        navToWizardStep(step);

        setTimeout(() => {
            let $target = $();

            if (fieldId) {
                try {
                    $target = $('#' + CSS.escape(fieldId));
                } catch (error) {
                    $target = $('#' + fieldId);
                }
            }

            if (!$target.length && fieldName) {
                $target = $(`#wiz-step-${step}`).find(`[name="${fieldName}"]`).first();
            }

            if (!$target.length) return;

            const $scrollBox = $('.wiz-main-center');
            const targetTop = $target.offset().top;
            const scrollTop = $scrollBox.scrollTop();
            const boxTop = $scrollBox.offset().top;

            $scrollBox.animate({
                scrollTop: scrollTop + (targetTop - boxTop) - 120
            }, 300);

            $target.addClass('wiz-focus-highlight');

            if ($target.is('input, textarea, select')) {
                $target.trigger('focus');
            }

            setTimeout(() => {
                $target.removeClass('wiz-focus-highlight');
            }, 1800);
        }, 120);
    }

    function openWizard() {
    $('#customBladeWizard').fadeIn(200);

    const form = document.getElementById('bladeWizardForm');
    if (form) form.reset();

    currentWizStep = 1;
    roofCount = 0;
    roomCount = 0;

    $('#wiz-roofs-container').html('');
    $('#wiz-rooms-container').html('');
    $('#wiz-missing-container').html('');
    $('#wiz-product-assignments').html('');
    $('#wiz-product-summary').text('Noch kein Produkt hinzugefügt');
    $('.wiz-company-wrap').addClass('d-none');

    populateWizardProductAdder();
    applyWizardStepVisibility();
    goToWizStep(getFirstVisibleWizardStep());
    updateWizProgress();
    updateQuickProductButtonsState();

    setTimeout(() => {
        if (typeof window.initWizardAddressAutocomplete === 'function') {
                window.initWizardAddressAutocomplete();
            }
            if (typeof window.fixPacContainerPosition === 'function') {
                window.fixPacContainerPosition();
            }
        }, 300);
    }

    function closeBladeWizard() {
        $('#customBladeWizard').fadeOut(200);
    }

    function submitBladeWizard() {
        let invalid = false;

        $('.req-field').filter(function () {
            return $(this).data('wizard-required-active') == '1';
        }).each(function () {
            const isCheckbox = ($(this).attr('type') || '').toLowerCase() === 'checkbox';

            if (isCheckbox) {
                if (!$(this).is(':checked')) invalid = true;
            } else {
                if ($(this).val() === '' || $(this).val() === null) invalid = true;
            }
        });

        if (!enforceEmployeeLimit()) {
            Swal.fire('Zu viele Mitarbeiter', 'Es dürfen maximal 2 unterschiedliche Mitarbeiter insgesamt zugewiesen werden.', 'warning');
            return;
        }

        if (invalid) {
            Swal.fire('Unvollständig', 'Bitte füllen Sie alle Pflichtfelder (*) aus. Prüfen Sie rechts die Datenpflege.', 'warning');
            return;
        }

        const roofs = collectRoofs();
        const rooms = collectRooms();
        const productAssignments = collectProductAssignments();

        const payload = {
            _token: window.csrfToken || $('meta[name="csrf-token"]').attr('content'),

            salutation: $('input[name="salutation"]:checked').val() || null,
            first_name: valOrNull('input[name="first_name"]'),
            last_name: valOrNull('input[name="last_name"]'),
            email: valOrNull('input[name="email"]'),
            phone: valOrNull('input[name="phone"]'),

            street: valOrNull('input[name="street"]'),
            address_no: valOrNull('input[name="address_no"]'),
            postcode: valOrNull('input[name="postcode"]'),
            city: valOrNull('input[name="city"]'),

            periority: valOrNull('select[name="periority"]'),
            appointment_confirmed: valOrNull('select[name="appointment_confirmed"]'),
            appointment: valOrNull('input[name="appointment"]'),

            total_electricity_consumption: valOrNull('input[name="total_electricity_consumption"]'),
            annual_heating_energy_consumption: valOrNull('input[name="annual_heating_energy_consumption"]'),
            heating_energy_unit: valOrNull('select[name="heating_energy_unit"]'),
            electricity_price: valOrNull('input[name="electricity_price"]'),
            feed_in_tariff: valOrNull('input[name="feed_in_tariff"]'),
            old_heating_price: valOrNull('input[name="old_heating_price"]'),

            building_type: valOrNull('select[name="building_type"]'),
            object_name: valOrNull('input[name="object_name"]'),
            house_year: valOrNull('input[name="house_year"]'),
            number_we: valOrNull('input[name="number_we"]'),
            number_stories: valOrNull('input[name="number_stories"]'),
            living_space: valOrNull('input[name="living_space"]'),
            building_condition: valOrNull('select[name="building_condition"]'),
            usage_type: valOrNull('select[name="usage_type"]'),

            owner_count: valOrNull('input[name="owner_count"]'),
            owner_occupied_units: valOrNull('input[name="owner_occupied_units"]'),
            rented_units: valOrNull('input[name="rented_units"]'),
            owners_below_40k: valOrNull('input[name="owners_below_40k"]'),
            owners_above_40k: valOrNull('input[name="owners_above_40k"]'),
            owner_occupied_below_40k: valOrNull('input[name="owner_occupied_below_40k"]'),
            owner_occupied_above_40k: valOrNull('input[name="owner_occupied_above_40k"]'),
            rented_below_40k: valOrNull('input[name="rented_below_40k"]'),
            rented_above_40k: valOrNull('input[name="rented_above_40k"]'),

            building_length: valOrNull('input[name="building_length"]'),
            building_width: valOrNull('input[name="building_width"]'),
            facade_height: valOrNull('input[name="facade_height"]'),
            total_window_area: valOrNull('input[name="total_window_area"]'),
            masonry: valOrNull('select[name="masonry"]'),
            masonry_thickness: valOrNull('input[name="masonry_thickness"]'),
            insolation_type: valOrNull('select[name="insolation_type"]'),
            external_insulation_thickness: valOrNull('input[name="external_insulation_thickness"]'),
            insolation_age: valOrNull('input[name="insolation_age"]'),
            roof_insulation_type: valOrNull('select[name="roof_insulation_type"]'),
            roof_insulation_thickness: valOrNull('input[name="roof_insulation_thickness"]'),
            roof_insulation_year: valOrNull('input[name="roof_insulation_year"]'),
            basement_insulation_type: valOrNull('select[name="basement_insulation_type"]'),
            basement_insulation_thickness: valOrNull('input[name="basement_insulation_thickness"]'),
            basement_insulation_year: valOrNull('input[name="basement_insulation_year"]'),
            window_glazing: valOrNull('select[name="window_glazing"]'),
            window_frame: valOrNull('select[name="window_frame"]'),
            window_year: valOrNull('input[name="window_year"]'),
            ventilation_type: valOrNull('select[name="ventilation_type"]'),

            roof_height: valOrNull('input[name="roof_height"]'),
            dc_cable_route: valOrNull('select[name="dc_cable_route"]'),
            pv_existing: valOrNull('select[name="pv_existing"]'),
            storage_preference: valOrNull('select[name="storage_preference"]'),
            backup_power: valOrNull('select[name="backup_power"]'),
            pv_investment_costs: valOrNull('input[name="pv_investment_costs"]'),

            heating_system_type: valOrNull('select[name="heating_system_type"]'),
            old_heating_power: valOrNull('input[name="old_heating_power"]'),
            heat_distribution: valOrNull('select[name="heat_distribution"]'),
            flow_temperature: valOrNull('input[name="flow_temperature"]'),
            hot_water_generation: valOrNull('select[name="hot_water_generation"]'),
            hot_water_tank_liters: valOrNull('input[name="hot_water_tank_liters"]'),
            installation_location: valOrNull('select[name="installation_location"]'),
            groundwork: valOrNull('select[name="groundwork"]'),
            heat_pump_pipe_length: valOrNull('input[name="heat_pump_pipe_length"]'),
            basement_ceiling_height: valOrNull('input[name="basement_ceiling_height"]'),
            door_width_for_installation: valOrNull('input[name="door_width_for_installation"]'),
            heat_pump_investment_costs: valOrNull('input[name="heat_pump_investment_costs"]'),
            heat_pump_subsidy_percent: valOrNull('input[name="heat_pump_subsidy_percent"]'),

            pipe_system_material: valOrNull('select[name="pipe_system_material"]'),
            circulation_line: valOrNull('select[name="circulation_line"]'),
            heating_pipe_dimension: valOrNull('input[name="heating_pipe_dimension"]'),
            water_pipe_dimension: valOrNull('input[name="water_pipe_dimension"]'),
            circulation_pipe_dimension: valOrNull('input[name="circulation_pipe_dimension"]'),

            meter_cabinet_action: valOrNull('select[name="meter_cabinet_action"]'),
            cabinet_size: valOrNull('select[name="cabinet_size"]'),
            sls_switch: valOrNull('select[name="sls_switch"]'),
            apz_field: valOrNull('select[name="apz_field"]'),
            ac_surge_protection: valOrNull('select[name="ac_surge_protection"]'),
            enwg_14a_ready: valOrNull('select[name="enwg_14a_ready"]'),
            meter_count: valOrNull('input[name="meter_count"]'),
            grid_reserve: valOrNull('select[name="grid_reserve"]'),
            installation_location_power: valOrNull('select[name="installation_location_power"]'),
            network_wlan: valOrNull('select[name="network_wlan"]'),
            tenant_model: boolNum('input[name="tenant_model"]'),
            load_management: boolNum('input[name="load_management"]'),

            electric_car: $('input[name="electric_car"]:checked').val() || null,
            electric_car_plan: $('input[name="electric_car_plan"]:checked').val() || null,
            car_kilo: valOrNull('input[name="car_kilo"]'),
            electric_car_count: valOrNull('input[name="electric_car_count"]'),
            wallbox_count: valOrNull('input[name="wallbox_count"]'),
            charging_power: valOrNull('select[name="charging_power"]'),
            wallbox_location: valOrNull('select[name="wallbox_location"]'),
            access_control: valOrNull('select[name="access_control"]'),
            heavy_current_cable: valOrNull('select[name="heavy_current_cable"]'),
            network_cable: valOrNull('input[name="network_cable"]'),
            groundwork_checkbox: boolNum('input[name="groundwork"]'),
            bidirectional_car: boolNum('input[name="bidirectional_car"]'),

            documents_invoices: boolNum('input[name="documents_invoices"]'),
            documents_roof_images: boolNum('input[name="documents_roof_images"]'),
            documents_meter_images: boolNum('input[name="documents_meter_images"]'),
            documents_window_images: boolNum('input[name="documents_window_images"]'),
            documents_heating_images: boolNum('input[name="documents_heating_images"]'),
            documents_facade_images: boolNum('input[name="documents_facade_images"]'),
            site_visit_needed: boolNum('input[name="site_visit_needed"]'),
            ready_for_offer: boolNum('input[name="ready_for_offer"]'),
            bathroom_count: valOrNull('input[name="bathroom_count"]'),
            bathtub_count: valOrNull('input[name="bathtub_count"]'),
            note: valOrNull('textarea[name="note"]'), 

            roofs,
            rooms,
            product_assignments: productAssignments
        };

        const $btn = $('#wizFinalSubmitBtn');
        const originalText = $btn.html();

        $btn.prop('disabled', true).html('<i class="feather icon-loader"></i> Speichere...');

        $.ajax({
            url: "{{ route('wizard.lead.store') }}",
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: function (response) {
                $btn.prop('disabled', false).html(originalText);

                if (response.success) {
                    if (typeof window.openCalendarFromWizard === 'function') {
                        window.openCalendarFromWizard(response);
                    } else {
                        closeBladeWizard();
                        Swal.fire('Erfolgreich', 'Wizard-Daten wurden gespeichert.', 'success');
                    }
                } else {
                    Swal.fire('Fehler', response.message || 'Speichern fehlgeschlagen.', 'error');
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html(originalText);
                console.error(xhr.responseText);
                Swal.fire('Serverfehler', 'Fehler beim Speichern. Prüfen Sie die Konsole und Controller-Validierung.', 'error');
            },
            complete: function () {
                if (window.feather) feather.replace();
            }
        });
    }

    $(document).on('change', '#wiz-product-adder', function () {
        const productId = $(this).val();

        if (productId) {
            addProductRow(productId);
            $(this).val('').trigger('change');
        }
    });

    $(document).on('change', '.wiz-product-service', function () {
        const $row = $(this).closest('.wiz-product-row');
        refreshEmployeeSelectsForRow($row, false);
        updateWizProgress();
    });

    $(document).on('click', '.wiz-product-remove-btn', function () {
        $(this).closest('.wiz-product-row').remove();
        updateProductSummary();
        applyWizardStepVisibility();
        updateQuickProductButtonsState();
        updateWizProgress();
    });

    $(document).on('click', '.wiz-product-collapse-btn', function () {
        const $row = $(this).closest('.wiz-product-row');
        $row.toggleClass('is-collapsed');
        $(this).text($row.hasClass('is-collapsed') ? 'Ausklappen' : 'Einklappen');
    });

    $(document).on('click', '.wiz-roof-remove-btn', function () {
        $(this).closest('.wiz-roof-card').remove();
        updateWizProgress();
    });

    $(document).on('click', '.wiz-room-remove-btn', function () {
        $(this).closest('.wiz-room-card').remove();
        updateWizProgress();
    });

    $(document).on('click', '.wiz-roof-collapse-btn', function () {
        const $card = $(this).closest('.wiz-roof-card');
        $card.find('.wiz-roof-body').toggle();
        $(this).text($card.find('.wiz-roof-body').is(':visible') ? 'Einklappen' : 'Ausklappen');
    });

    $(document).on('click', '.wiz-room-collapse-btn', function () {
        const $card = $(this).closest('.wiz-room-card');
        $card.find('.wiz-room-body').toggle();
        $(this).text($card.find('.wiz-room-body').is(':visible') ? 'Einklappen' : 'Ausklappen');
    });

    $(document).on('click', '.wiz-missing-item', function () {
        const step = parseInt($(this).data('target-step'), 10);
        const fieldName = $(this).data('target-name') || '';
        const fieldId = $(this).data('target-id') || '';

        focusWizardField(step, fieldName, fieldId);
    });

    $(document).on('click', '.wiz-missing-header', function (event) {
        if ($(event.target).closest('.wiz-missing-item').length) return;
        $(this).closest('.wiz-missing-block').toggleClass('is-collapsed');
    });

    $(document).on('click', '.wiz-quick-product-btn', function () {
        const productKey = $(this).data('product-key');
        const product = findWizardProductByQuickKey(productKey);

        if (!product) {
            Swal.fire('Nicht gefunden', `Kein passendes Produkt für "${productKey}" in der Datenbank gefunden.`, 'warning');
            return;
        }

        addProductRow(product.id);

        const $adder = $('#wiz-product-adder');
        if ($adder.length) {
            $adder.val(String(product.id)).trigger('change.select2');
            setTimeout(() => {
                $adder.val('').trigger('change.select2');
            }, 50);
        }

        updateQuickProductButtonsState();
    });

    $(document).on('input change', '.wiz-input, .wiz-select, .wiz-chip input, .wiz-textarea', function () {
        updateWizProgress();
    });

    $(document).on('change', '.wiz-product-inside, .wiz-product-outside', function () {
        if (!enforceEmployeeLimit()) {
            $(this).val('').trigger('change');
        }
        updateWizProgress();
    });

    $(document).on('change', 'select[name="building_type"]', function () {
        toggleCompanyField();
        updateWizProgress();
    });

    $(document).on('change', 'select[name="meter_cabinet_action"]', function () {
        if ($(this).val() !== 'Neu') {
            $('select[name="cabinet_size"]').val('');
        }
        updateWizProgress();
    });

    $(document).on('change', 'select[name="hot_water_generation"]', function () {
        if ($(this).val() !== 'Ja') {
            $('input[name="hot_water_tank_liters"]').val('');
        }
        updateWizProgress();
    });

    $(document).ready(function () {
        populateWizardProductAdder();
        applyWizardStepVisibility();
        updateWizProgress();
        updateQuickProductButtonsState();

        if (window.feather) feather.replace();
    });

    window.openWizard = openWizard;
    window.closeWizard = closeBladeWizard;
    window.closeBladeWizard = closeBladeWizard;
    window.navToWizardStep = navToWizardStep;
    window.goToWizStep = goToWizStep;
    window.submitBladeWizard = submitBladeWizard;
    window.getNextVisibleStep = getNextVisibleStep;
    window.getPrevVisibleStep = getPrevVisibleStep;
    window.addWizRoof = addWizRoof;
    window.addWizRoom = addWizRoom;
    window.collectRoofs = collectRoofs;
    window.collectRooms = collectRooms;
    window.updateWizProgress = updateWizProgress;

    Object.defineProperty(window, 'currentWizStep', {
        get() {
            return currentWizStep;
        },
        set(value) {
            currentWizStep = Number(value) || 1;
        }
    });
})();
</script>

<script>
(() => {
    const serviceTranslations = {
        'service wählen...': 'Service wählen...',
        'complete': 'Komplett',
        'montage': 'Montage',
        'product': 'Produkt',
        'plan': 'Planung',
        'maintenance': 'Wartung',
        'repair': 'Reparatur',
        'reclaim': 'Rückgewinnung',
        'others': 'Sonstiges'
    };

    function translateServiceText(text) {
        const key = String(text || '').trim().toLowerCase();
        return serviceTranslations[key] || text;
    }

    function translateSelect2Results(scope = document) {
        scope.querySelectorAll('.select2-results__option').forEach(option => {
            const original = option.textContent.trim();
            const translated = translateServiceText(original);

            if (translated !== original) {
                option.textContent = translated;
            }
        });
    }

    function translateSelect2Selections(scope = document) {
        scope.querySelectorAll('.select2-selection__rendered').forEach(rendered => {
            const original = rendered.textContent.trim();
            const translated = translateServiceText(original);

            if (translated !== original) {
                rendered.textContent = translated;
                rendered.setAttribute('title', translated);
            }
        });
    }

    function translateAllSelect2(scope = document) {
        translateSelect2Results(scope);
        translateSelect2Selections(scope);
    }

    $(document).on('select2:open', function () {
        setTimeout(() => {
            translateAllSelect2(document);
        }, 0);
    });

    $(document).on('select2:select select2:close change', '.wiz-product-service, .wiz-product-inside, .wiz-product-outside, #wiz-product-adder', function () {
        setTimeout(() => {
            translateAllSelect2(document);
        }, 0);
    });

    const observer = new MutationObserver(() => {
        translateAllSelect2(document);
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    $(document).ready(function () {
        translateAllSelect2(document);
    });

    window.translateAllSelect2 = translateAllSelect2;
})();
</script>

<script>
function normalizeServiceName(service) {
    const raw = service?.name || service?.phase_section || service?.title || `Service #${service?.id ?? ''}`;

    const map = {
        'complete': 'Komplett',
        'montage': 'Montage',
        'product': 'Produkt',
        'plan': 'Planung',
        'maintenance': 'Wartung',
        'repair': 'Reparatur',
        'reclaim': 'Rückgewinnung',
        'others': 'Sonstiges'
    };

    const key = String(raw).trim().toLowerCase();
    return map[key] || raw;
}
</script>

<script>
(() => {
    const sourceSelect = document.getElementById('heating_energy_source');
    const unitSelect = document.getElementById('heating_energy_unit');
    const priceLabel = document.getElementById('old_heating_price_label');
    const priceInput = document.getElementById('old_heating_price');
    const normalizedPriceInput = document.getElementById('old_heating_price_per_kwh');

    const sourceConfig = {
        district_heating: {
            label: 'Fernwärme',
            units: [
                { value: 'kWh Wärme', text: 'kWh Wärme' },
                { value: 'MWh Wärme', text: 'MWh Wärme' }
            ],
            defaultUnit: 'kWh Wärme',
            priceLabel: 'Preis Fernwärme (€/kWh)',
            defaultPrice: 0.11,
            factorToKwh: {
                'kWh Wärme': 1,
                'MWh Wärme': 1000
            }
        },
        natural_gas_h: {
            label: 'Erdgas H',
            units: [
                { value: 'm³ Erdgas H', text: 'm³ Erdgas H' },
                { value: 'kWh Wärme', text: 'kWh Wärme' }
            ],
            defaultUnit: 'm³ Erdgas H',
            priceLabel: 'Gaspreis (€/m³)',
            defaultPrice: 1.10,
            factorToKwh: {
                'm³ Erdgas H': 10.5,
                'kWh Wärme': 1
            }
        },
        natural_gas_l: {
            label: 'Erdgas L',
            units: [
                { value: 'm³ Erdgas L', text: 'm³ Erdgas L' },
                { value: 'kWh Wärme', text: 'kWh Wärme' }
            ],
            defaultUnit: 'm³ Erdgas L',
            priceLabel: 'Gaspreis (€/m³)',
            defaultPrice: 1.00,
            factorToKwh: {
                'm³ Erdgas L': 8.5,
                'kWh Wärme': 1
            }
        },
        heating_oil: {
            label: 'Heizöl EL',
            units: [
                { value: 'Liter Heizöl EL', text: 'Liter Heizöl EL' },
                { value: 'kWh Wärme', text: 'kWh Wärme' }
            ],
            defaultUnit: 'Liter Heizöl EL',
            priceLabel: 'Heizölpreis (€/Liter)',
            defaultPrice: 1.10,
            factorToKwh: {
                'Liter Heizöl EL': 10,
                'kWh Wärme': 1
            }
        },
        liquid_gas: {
            label: 'Flüssiggas',
            units: [
                { value: 'kg Flüssiggas', text: 'kg Flüssiggas' },
                { value: 'Liter Flüssiggas', text: 'Liter Flüssiggas' },
                { value: 'kWh Wärme', text: 'kWh Wärme' }
            ],
            defaultUnit: 'kg Flüssiggas',
            priceLabel: 'Flüssiggaspreis (€/kg)',
            defaultPrice: 1.40,
            factorToKwh: {
                'kg Flüssiggas': 12.8,
                'Liter Flüssiggas': 6.6,
                'kWh Wärme': 1
            }
        },
        pellets: {
            label: 'Pellets',
            units: [
                { value: 'kg Pellets', text: 'kg Pellets' },
                { value: 't Pellets', text: 't Pellets' },
                { value: 'kWh Wärme', text: 'kWh Wärme' }
            ],
            defaultUnit: 'kg Pellets',
            priceLabel: 'Pelletspreis (€/kg)',
            defaultPrice: 0.35,
            factorToKwh: {
                'kg Pellets': 4.8,
                't Pellets': 4800,
                'kWh Wärme': 1
            }
        },
        firewood_soft: {
            label: 'Scheitholz weich',
            units: [
                { value: 'kg Scheitholz weich', text: 'kg Scheitholz weich' },
                { value: 'Raummeter Scheitholz weich', text: 'Raummeter Scheitholz weich' }
            ],
            defaultUnit: 'Raummeter Scheitholz weich',
            priceLabel: 'Holzpreis (€/Raummeter)',
            defaultPrice: 85,
            factorToKwh: {
                'kg Scheitholz weich': 4.0,
                'Raummeter Scheitholz weich': 1400
            }
        },
        firewood_hard: {
            label: 'Scheitholz hart',
            units: [
                { value: 'kg Scheitholz hart', text: 'kg Scheitholz hart' },
                { value: 'Raummeter Scheitholz hart', text: 'Raummeter Scheitholz hart' }
            ],
            defaultUnit: 'Raummeter Scheitholz hart',
            priceLabel: 'Holzpreis (€/Raummeter)',
            defaultPrice: 110,
            factorToKwh: {
                'kg Scheitholz hart': 4.2,
                'Raummeter Scheitholz hart': 1900
            }
        },
        direct_electric: {
            label: 'Direktstromheizung',
            units: [
                { value: 'kWh Strom direkt', text: 'kWh Strom direkt' }
            ],
            defaultUnit: 'kWh Strom direkt',
            priceLabel: 'Strompreis Heizung (€/kWh)',
            defaultPrice: 0.35,
            factorToKwh: {
                'kWh Strom direkt': 1
            }
        },
        heat_pump: {
            label: 'Wärmepumpe Bestand',
            units: [
                { value: 'kWh Strom Wärmepumpe', text: 'kWh Strom Wärmepumpe' },
                { value: 'kWh Wärme', text: 'kWh Wärme' }
            ],
            defaultUnit: 'kWh Strom Wärmepumpe',
            priceLabel: 'Strompreis Wärmepumpe (€/kWh)',
            defaultPrice: 0.35,
            factorToKwh: {
                'kWh Strom Wärmepumpe': 1,
                'kWh Wärme': 1
            }
        }
    };

    function fillUnits(sourceKey) {
        const cfg = sourceConfig[sourceKey];

        unitSelect.innerHTML = '';

        if (!cfg) {
            unitSelect.innerHTML = '<option value="">Bitte zuerst Energieträger wählen...</option>';
            priceLabel.textContent = 'Preis Altanlage';
            priceInput.value = '';
            normalizedPriceInput.value = '';
            return;
        }

        cfg.units.forEach(unit => {
            const option = document.createElement('option');
            option.value = unit.value;
            option.textContent = unit.text;
            if (unit.value === cfg.defaultUnit) {
                option.selected = true;
            }
            unitSelect.appendChild(option);
        });

        priceLabel.textContent = cfg.priceLabel;
        priceInput.value = cfg.defaultPrice;
        calculateNormalizedPrice();
    }

    function calculateNormalizedPrice() {
        const sourceKey = sourceSelect.value;
        const unit = unitSelect.value;
        const rawPrice = parseFloat(priceInput.value);

        if (!sourceKey || !unit || isNaN(rawPrice)) {
            normalizedPriceInput.value = '';
            return;
        }

        const cfg = sourceConfig[sourceKey]
        const factor = cfg.factorToKwh?.[unit];

        if (!factor || factor <= 0) {
            normalizedPriceInput.value = '';
            return;
        }

        const pricePerKwh = rawPrice / factor;
        normalizedPriceInput.value = pricePerKwh.toFixed(4);
    }

    sourceSelect?.addEventListener('change', () => fillUnits(sourceSelect.value));
    unitSelect?.addEventListener('change', calculateNormalizedPrice);
    priceInput?.addEventListener('input', calculateNormalizedPrice);

    if (sourceSelect?.value) {
        fillUnits(sourceSelect.value);
    }
})();
</script>

<script>
(() => {
    let wizAddressAutocomplete = null;
    let wizAddressGeocoder = null;
    let wizardAddressEventsBound = false;

   function el(id) {
        // 1. Prioritize finding the ID strictly inside the currently VISIBLE wizard.
        // This ignores hidden duplicates and background page conflicts.
        let $node = $('#customBladeWizard:visible').find('#' + $.escapeSelector(id));
        
        if ($node.length) {
            return $node[0];
        }

        // 2. Fallback: If it's not in a visible modal, grab the LAST injected instance in the DOM
        let $all = $('#' + $.escapeSelector(id));
        return $all.length ? $all.last()[0] : null;
    }

    function setValue(id, value) {
        const node = el(id);
        if (!node) return;
        node.value = value ?? '';
        node.dispatchEvent(new Event('input', { bubbles: true }));
        node.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function getAddressComponent(components, type) {
        if (!Array.isArray(components)) return null;
        return components.find(item => Array.isArray(item.types) && item.types.includes(type)) || null;
    }

    function getComponentValue(components, types, key = 'long_name') {
        if (!Array.isArray(components)) return '';

        for (const type of types) {
            const item = getAddressComponent(components, type);
            if (item && item[key]) {
                return item[key];
            }
        }

        return '';
    }

    function getCityFromComponents(components) {
        return (
            getComponentValue(components, ['locality']) ||
            getComponentValue(components, ['postal_town']) ||
            getComponentValue(components, ['sublocality']) ||
            getComponentValue(components, ['sublocality_level_1']) ||
            getComponentValue(components, ['administrative_area_level_3']) ||
            getComponentValue(components, ['administrative_area_level_2']) ||
            ''
        );
    }

    function extractHouseNumber(formattedAddress = '', route = '') {
        const address = String(formattedAddress || '').trim();
        const street = String(route || '').trim();

        if (!address) return '';

        if (street) {
            const safeStreet = street.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

            const afterStreet = address.match(new RegExp(`${safeStreet}\\s+([0-9]+[a-zA-Z\\-\\/]*)`, 'i'));
            if (afterStreet?.[1]) return afterStreet[1].trim();

            const beforeStreet = address.match(new RegExp(`([0-9]+[a-zA-Z\\-\\/]*)\\s+${safeStreet}`, 'i'));
            if (beforeStreet?.[1]) return beforeStreet[1].trim();
        }

        const generic = address.match(/\b([0-9]+[a-zA-Z\\-\\/]*)\b/);
        return generic?.[1] ? generic[1].trim() : '';
    }

    function fillFieldsFromPlace(place) {
        if (!place) return;

        const components = Array.isArray(place.address_components) ? place.address_components : [];

        let street = getComponentValue(components, ['route']) || '';
        let houseNumber =
            getComponentValue(components, ['street_number']) ||
            getComponentValue(components, ['premise']) ||
            '';

        const postcode = getComponentValue(components, ['postal_code']) || '';
        const city = getCityFromComponents(components);
        const country = getComponentValue(components, ['country']) || '';
        const placeId = place.place_id || '';

        const lat = typeof place.geometry?.location?.lat === 'function'
            ? String(place.geometry.location.lat())
            : '';

        const lng = typeof place.geometry?.location?.lng === 'function'
            ? String(place.geometry.location.lng())
            : '';

        if (!street && place.name) {
            street = String(place.name).trim();
        }

        if (!houseNumber) {
            houseNumber = extractHouseNumber(place.formatted_address || '', street);
        }

        setValue('wiz-street-autocomplete', street);
        setValue('wiz-address-no', houseNumber);
        setValue('wiz-postcode', postcode);
        setValue('wiz-city', city);
        setValue('wiz-country', country);
        setValue('wiz-place-id', placeId);
        setValue('wiz-latitude', lat);
        setValue('wiz-longitude', lng);

        const searchInput = el('wiz-address-search');
        if (searchInput) {
            searchInput.value = place.formatted_address || '';
        }

        if (typeof window.updateWizProgress === 'function') {
            window.updateWizProgress();
        }
    }

    function geocodeByPlaceId(placeId) {
        return new Promise(resolve => {
            if (!placeId || !wizAddressGeocoder) {
                resolve(null);
                return;
            }

            wizAddressGeocoder.geocode({ placeId }, (results, status) => {
                if (status === 'OK' && Array.isArray(results) && results.length) {
                    resolve(results[0]);
                    return;
                }
                resolve(null);
            });
        });
    }

    async function handlePlaceChanged() {
        if (!wizAddressAutocomplete) return;

        const place = wizAddressAutocomplete.getPlace();
        if (!place) return;

        let finalPlace = place;

        const missingCoreData =
            !Array.isArray(place.address_components) ||
            !getComponentValue(place.address_components, ['route']) ||
            !getComponentValue(place.address_components, ['postal_code']) ||
            !getCityFromComponents(place.address_components);

        if (missingCoreData && place.place_id) {
            const geocoded = await geocodeByPlaceId(place.place_id);
            if (geocoded) {
                finalPlace = geocoded;
            }
        }

        fillFieldsFromPlace(finalPlace);
        setTimeout(fixPacContainerPosition, 30);
    }

    function clearAddressFields() {
        setValue('wiz-street-autocomplete', '');
        setValue('wiz-address-no', '');
        setValue('wiz-postcode', '');
        setValue('wiz-city', '');
        setValue('wiz-country', '');
        setValue('wiz-place-id', '');
        setValue('wiz-latitude', '');
        setValue('wiz-longitude', '');
    }

    function fixPacContainerPosition() {
        const input = el('wiz-address-search');
        
        // Google Maps appends multiple .pac-container elements if initialized multiple times.
        // We MUST target the last one added to the DOM to ensure we style the active one.
        const pacs = document.querySelectorAll('.pac-container');
        const pac = pacs[pacs.length - 1];

        if (!input || !pac) return;

        const rect = input.getBoundingClientRect();
        pac.style.position = 'fixed';
        pac.style.left = rect.left + 'px';
        pac.style.top = (rect.bottom + 4) + 'px';
        pac.style.width = rect.width + 'px';
        pac.style.zIndex = '999999';
    }
    function bindInputEvents() {
        if (wizardAddressEventsBound) return;

        $(document).on('input', '#wiz-address-search', function () {
            if (!this.value.trim()) {
                clearAddressFields();
            }
            setTimeout(fixPacContainerPosition, 60);
        });

        $('.wiz-main-center').on('scroll', function () {
            fixPacContainerPosition();
        });

        $(window).on('resize', function () {
            fixPacContainerPosition();
        });

        wizardAddressEventsBound = true;
    }

    function initWizardAddressAutocomplete() {
        const input = el('wiz-address-search');

        if (!input) {
            console.warn('wiz-address-search not found');
            return;
        }

        if (
            !window.google ||
            !google.maps ||
            !google.maps.places ||
            typeof google.maps.places.Autocomplete !== 'function'
        ) {
            console.warn('Google Places Autocomplete not ready');
            return;
        }

        if (!wizAddressGeocoder && typeof google.maps.Geocoder === 'function') {
            wizAddressGeocoder = new google.maps.Geocoder();
        }

        if (wizAddressAutocomplete) {
            google.maps.event.clearInstanceListeners(wizAddressAutocomplete);
        }

        wizAddressAutocomplete = new google.maps.places.Autocomplete(input, {
            types: ['address'],
            componentRestrictions: { country: ['de'] },
            fields: ['address_components', 'formatted_address', 'geometry', 'name', 'place_id']
        });

        wizAddressAutocomplete.addListener('place_changed', handlePlaceChanged);

        input.addEventListener('focus', () => {
            setTimeout(fixPacContainerPosition, 100);
        });

        bindInputEvents();

        setTimeout(fixPacContainerPosition, 200);
    }

    window.initWizardAddressAutocomplete = initWizardAddressAutocomplete;
    window.fixPacContainerPosition = fixPacContainerPosition;
})();
</script>
@endpush