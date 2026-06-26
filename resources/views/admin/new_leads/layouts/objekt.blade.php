<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    :root {
        --slate-50: #f8fafc; --slate-100: #f1f5f9; --slate-200: #e2e8f0; --slate-300: #cbd5e1;
        --slate-400: #94a3b8; --slate-500: #64748b; --slate-600: #475569; --slate-700: #334155;
        --slate-800: #1e293b; --slate-900: #0f172a;
        --blue-50: #eff6ff; --blue-200: #bfdbfe; --blue-600: #74b2d4; --blue-700: #1d4ed8;
        --indigo-50: #eef2ff; --indigo-100: #e0e7ff; --indigo-500: #6366f1; --indigo-600: #4f46e5;
        --orange-50: #fff7ed; --orange-500: #f97316;
        --purple-50: #faf5ff; --purple-500: #a855f7;
        --emerald-50: #ecfdf5; --emerald-500: #10b981; --emerald-600: #059669;
        --wizard-bg: #ffffff;
        --border-radius: 1rem;
    }

    .obj-wizard-container { font-family: 'Inter', sans-serif; color: var(--slate-800); width: 100%; }
    .hidden { display: none !important; }

    /* Layout Shell */
    .wizard-card {
        background-color: var(--wizard-bg); border-radius: var(--border-radius);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); 
        border: 1px solid var(--slate-200); display: flex; flex-direction: column; min-height: 720px; overflow: hidden;
    }

    /* Navigation */
    .wizard-tabs { background-color: var(--slate-50); border-bottom: 1px solid var(--slate-200); padding: 1.25rem; display: flex; gap: 0.75rem; overflow-x: auto; scrollbar-width: none; }
    .wizard-tabs::-webkit-scrollbar { display: none; }
    
    .tab-btn {
        flex: 1; min-width: 160px; text-align: left; padding: 1rem; border-radius: 0.75rem;
        border: 2px solid transparent; background: transparent; cursor: pointer; transition: all 0.2s ease-in-out;
    }
    .tab-btn:hover:not(.active) { background-color: var(--slate-100); }
    .tab-btn.active { background-color: white; border-color: var(--blue-600); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1); }

    .tab-header-flex { display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.5rem; }
    .tab-icon-circle { display: flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 50%; font-size: 0.75rem; font-weight: 700; background-color: var(--slate-200); color: var(--slate-600); }
    .tab-btn.active .tab-icon-circle { background-color: var(--blue-50); color: var(--blue-600); }
    .tab-label { font-weight: 600; font-size: 0.875rem; color: var(--slate-700); }

    .progress-track { width: 100%; height: 0.4rem; border-radius: 1rem; background-color: var(--slate-200); margin-top: 5px; overflow: hidden; }
    .progress-fill { height: 100%; background-color: var(--blue-600); width: 0%; transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1); }

    /* Forms 2-Column Grid */
    .step-wrapper { flex: 1; padding: 2rem; overflow-y: auto; position: relative; }
    .form-columns { display: grid; grid-template-columns: 1fr 1fr; gap: 2.5rem; }
    .form-column { display: flex; flex-direction: column; gap: 1.25rem; }
    .form-group { display: flex; flex-direction: column; gap: 0.4rem; }
    .form-full-width { grid-column: span 2; margin-top: 1rem; }
    .form-label { font-size: 0.875rem; font-weight: 600; color: var(--slate-600); }
    .form-section-title { font-size: 0.75rem; font-weight: 800; color: var(--blue-600); text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem; margin-bottom: 0.5rem; }

    /* Inputs */
    .control-field { padding: 0.625rem; border: 1px solid var(--slate-300); border-radius: 0.5rem; width: 100%; font-size: 0.95rem; transition: all 0.2s; }
    .control-field:focus { outline: none; border-color: var(--blue-600); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15); }
    .input-group { display: flex; }
    .input-group .control-field { border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: none; }
    .input-group-addon { background-color: var(--slate-50); border: 1px solid var(--slate-300); padding: 0.625rem 0.8rem; color: var(--slate-500); border-top-right-radius: 0.5rem; border-bottom-right-radius: 0.5rem; font-size: 0.875rem; }

    /* Tooltips */
    .has-tooltip { position: relative; cursor: help; border-bottom: 1px dotted var(--slate-300); }
    .has-tooltip::after {
        content: attr(data-tooltip); position: absolute; bottom: 125%; left: 50%; transform: translateX(-50%);
        background: var(--slate-900); color: white; padding: 0.6rem; border-radius: 0.5rem; font-size: 0.7rem;
        visibility: hidden; opacity: 0; transition: 0.2s; width: 180px; text-align: center; z-index: 100; font-weight: 400;
    }
    .has-tooltip:hover::after { visibility: visible; opacity: 1; bottom: 140%; }

    /* Sticky Footer (Used inside Partials) */
    .wizard-footer { 
        padding: 1.5rem 2rem; 
        background-color: var(--slate-50); 
        border-top: 1px solid var(--slate-100); 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        position: sticky;
        bottom: -2rem; 
        margin: 2rem -2rem -2rem -2rem; 
        z-index: 10;
    }

    /* Section Dividers with Badges */
    .section-divider {
        display: flex;
        align-items: center;
        margin: 2rem 0 1.25rem 0;
    }
    .section-divider:first-child {
        margin-top: 0; /* Remove top margin for the very first section */
    }
    .section-divider::after {
        content: '';
        flex: 1;
        border-bottom: 2px dashed var(--slate-200);
        margin-left: 1rem;
    }
    .section-badge {
        background-color: var(--slate-50);
        color: var(--slate-700);
        padding: 0.4rem 0.8rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        border: 1px solid var(--slate-300);
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }
    /* Optional: Highlight specific badge colors */
    .badge-blue { background-color: var(--blue-50); color: var(--blue-700); border-color: var(--blue-200); }
    .badge-emerald { background-color: var(--emerald-50); color: var(--emerald-700); border-color: var(--emerald-200); }
    .badge-orange { background-color: var(--orange-50); color: var(--orange-700); border-color: var(--orange-200); }

    /* Buttons */
    .btn-wizard { display: inline-flex; align-items: center; justify-content: center; padding: 0.7rem 1.5rem; font-size: 0.875rem; font-weight: 600; border-radius: 0.5rem; cursor: pointer; transition: all 0.2s; border: 1px solid transparent; gap: 0.5rem; }
    .btn-primary-blue { background-color: var(--blue-600); color: #fff; }
    .btn-primary-blue:hover { background-color: var(--blue-700); transform: translateY(-1px); }
    .btn-primary-emerald { background-color: var(--emerald-600); color: #fff; }
    .btn-primary-emerald:hover { background-color: var(--emerald-500); transform: translateY(-1px); }
    .btn-secondary { background-color: #fff; border-color: var(--slate-300); color: var(--slate-600); }
    .btn-outline-indigo { background: #fff; border: 2px dashed var(--indigo-200); color: var(--indigo-600); width: 100%; margin-top: 1rem; }
    .btn-outline-indigo:hover { background: var(--indigo-50); border-color: var(--indigo-500); }

    /* Inputs & Attached Badges */
    .control-field { padding: 0.625rem; border: 1px solid var(--slate-300); border-radius: 0.5rem; width: 100%; font-size: 0.95rem; transition: all 0.2s; }
    .control-field:focus { outline: none; border-color: var(--blue-600); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15); }
    
    /* FIX: Force input and badge to stay on the same line */
    .input-group { 
        display: flex; 
        flex-wrap: nowrap; 
        align-items: stretch; 
        width: 100%; 
    }
    .input-group .control-field { 
        flex: 1 1 auto; 
        width: 1%; /* Crucial to prevent flex blowout */
        border-top-right-radius: 0; 
        border-bottom-right-radius: 0; 
        border-right: none; 
    }
    .input-group-addon { 
        display: flex; 
        align-items: center; 
        white-space: nowrap; /* Prevents text wrapping */
        background-color: var(--slate-50); 
        border: 1px solid var(--slate-300); 
        padding: 0 0.8rem; 
        color: var(--slate-600); 
        border-top-right-radius: 0.5rem; 
        border-bottom-right-radius: 0.5rem; 
        font-size: 0.875rem; 
        font-weight: 600;
    }
    /* Responsive */
    @media (max-width: 992px) { .form-columns { grid-template-columns: 1fr; } .form-full-width { grid-column: span 1; } }
</style>

<style>
    /* CSS for Missing Data Sidebar */
.missing-sidebar {
    position: fixed;
    top: 0;
    left: -320px; /* Hidden by default */
    width: 320px;
    height: 100vh;
    background: #fff;
    z-index: 1050;
    transition: left 0.3s ease-in-out;
    display: flex;
    flex-direction: column;
    border-right: 1px solid var(--slate-200);
}
.missing-sidebar.is-open {
    left: 0;
}
.missing-sidebar .sidebar-header {
    padding: 1rem;
    background: #fef2f2; /* Light red */
    border-bottom: 1px solid #fee2e2;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.missing-sidebar .sidebar-body {
    padding: 1rem;
    overflow-y: auto;
    flex: 1;
}
.missing-item {
    font-size: 0.85rem;
    padding: 0.5rem;
    border-bottom: 1px solid var(--slate-100);
    cursor: pointer;
    transition: background 0.2s;
}
.missing-item:hover {
    background: var(--slate-50);
    color: var(--blue-600);
}
.missing-group-title {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--slate-500);
    margin-top: 1rem;
    margin-bottom: 0.5rem;
    border-bottom: 1px dashed var(--slate-200);
}
</style>
<style id="wizard-matching-ui">
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

    :root{
        --slate-50:#f8fafc;
        --slate-100:#f1f5f9;
        --slate-200:#e2e8f0;
        --slate-300:#cbd5e1;
        --slate-400:#94a3b8;
        --slate-500:#64748b;
        --slate-600:#475569;
        --slate-700:#334155;
        --slate-800:#1e293b;
        --slate-900:#0f172a;

        --blue-50:#eff6ff;
        --blue-100:#dbeafe;
        --blue-200:#bfdbfe;
        --blue-600:#74b2d4;
        --blue-700:#1d4ed8;

        --indigo-50:#eef2ff;
        --indigo-100:#e0e7ff;
        --indigo-200:#c7d2fe;
        --indigo-500:#6366f1;
        --indigo-600:#4f46e5;

        --orange-50:#fff7ed;
        --orange-200:#fed7aa;
        --orange-500:#f97316;

        --purple-50:#faf5ff;
        --purple-200:#e9d5ff;
        --purple-500:#a855f7;

        --emerald-50:#ecfdf5;
        --emerald-100:#d1fae5;
        --emerald-200:#a7f3d0;
        --emerald-500:#10b981;
        --emerald-600:#059669;

        --danger-50:#fef2f2;
        --danger-100:#fee2e2;
        --danger-200:#fecaca;
        --danger-600:#dc2626;

        --wizard-bg:#ffffff;
        --border-radius:20px;
        --radius-lg:16px;
        --radius-md:12px;

        --shadow-sm:0 4px 10px rgba(15,23,42,0.05);
        --shadow-md:0 10px 30px rgba(15,23,42,0.08);
        --shadow-lg:0 16px 40px rgba(15,23,42,0.12);
    }

    .obj-wizard-container{
        font-family:'Inter',sans-serif;
        color:var(--slate-800);
        width:100%;
    }

    .hidden{display:none !important;}

    .wizard-card{
        background:var(--wizard-bg);
        border-radius:28px;
        border:1px solid var(--slate-200);
        box-shadow:var(--shadow-md);
        display:flex;
        flex-direction:column;
        min-height:720px;
        overflow:hidden;
        position:relative;
    }

    .wizard-tabs{
        background:linear-gradient(180deg,var(--slate-50) 0%, #ffffff 100%);
        border-bottom:1px solid var(--slate-200);
        padding:1.25rem;
        display:flex;
        gap:0.85rem;
        overflow-x:auto;
        scrollbar-width:none;
    }
    .wizard-tabs::-webkit-scrollbar{display:none;}

    .tab-btn{
        flex:1;
        min-width:170px;
        text-align:left;
        padding:1rem;
        border-radius:18px;
        border:1px solid transparent;
        background:rgba(255,255,255,0.7);
        cursor:pointer;
        transition:all .22s ease;
        box-shadow:0 1px 2px rgba(15,23,42,0.02);
        position:relative;
    }
    .tab-btn:hover:not(.active){
        background:#fff;
        border-color:var(--slate-200);
        transform:translateY(-1px);
        box-shadow:var(--shadow-sm);
    }
    .tab-btn.active{
        background:#fff;
        border-color:var(--blue-200);
        box-shadow:0 10px 24px rgba(37,99,235,0.10);
    }

    .tab-header-flex{
        display:flex;
        align-items:center;
        gap:.65rem;
        margin-bottom:.55rem;
    }

    .tab-icon-circle{
        width:1.95rem;
        height:1.95rem;
        border-radius:999px;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:.78rem;
        font-weight:800;
        background:var(--slate-200);
        color:var(--slate-600);
        flex-shrink:0;
    }
    .tab-btn.active .tab-icon-circle{
        background:var(--blue-50);
        color:var(--blue-600);
    }

    .tab-label{
        font-weight:700;
        font-size:.9rem;
        color:var(--slate-700);
    }

    .progress-track{
        width:100%;
        height:.42rem;
        border-radius:999px;
        background:var(--slate-200);
        margin-top:6px;
        overflow:hidden;
    }

    .progress-fill{
        height:100%;
        background:var(--blue-600);
        width:0%;
        transition:width .45s cubic-bezier(.4,0,.2,1);
    }

    .tab-meta{
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-top:6px;
        gap:8px;
    }

    .tab-meta small{
        font-size:10px;
        line-height:1;
    }

    .tab-meta-label{
        color:var(--slate-400);
        font-weight:700;
    }

    .tab-meta-count{
        color:var(--slate-700);
        font-weight:800;
    }

    .step-wrapper{
        flex:1;
        padding:2rem;
        overflow-y:auto;
        position:relative;
        background:#fff;
    }

    .form-columns{
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:2.5rem;
    }
    .form-column{
        display:flex;
        flex-direction:column;
        gap:1.25rem;
    }
    .form-group{
        display:flex;
        flex-direction:column;
        gap:.4rem;
    }
    .form-full-width{
        grid-column:span 2;
        margin-top:1rem;
    }

    .form-label{
        font-size:.875rem;
        font-weight:600;
        color:var(--slate-600);
    }

    .form-section-title{
        font-size:.75rem;
        font-weight:800;
        color:var(--blue-600);
        text-transform:uppercase;
        letter-spacing:.06em;
        border-bottom:1px solid var(--slate-200);
        padding-bottom:.5rem;
        margin-bottom:.5rem;
    }

    .control-field{
        padding:.7rem .8rem;
        border:1px solid var(--slate-300);
        border-radius:.7rem;
        width:100%;
        font-size:.95rem;
        transition:all .2s;
        background:#fff;
    }
    .control-field:focus{
        outline:none;
        border-color:var(--blue-600);
        box-shadow:0 0 0 4px rgba(37,99,235,.12);
    }

    .input-group{
        display:flex;
        flex-wrap:nowrap;
        align-items:stretch;
        width:100%;
    }
    .input-group .control-field{
        flex:1 1 auto;
        width:1%;
        border-top-right-radius:0;
        border-bottom-right-radius:0;
        border-right:none;
    }
    .input-group-addon{
        display:flex;
        align-items:center;
        white-space:nowrap;
        background:var(--slate-50);
        border:1px solid var(--slate-300);
        padding:0 .8rem;
        color:var(--slate-600);
        border-top-right-radius:.7rem;
        border-bottom-right-radius:.7rem;
        font-size:.875rem;
        font-weight:700;
    }

    .has-tooltip{
        position:relative;
        cursor:help;
        border-bottom:1px dotted var(--slate-300);
        width:fit-content;
        max-width:100%;
    }
    .has-tooltip::after{
        content:attr(data-tooltip);
        position:absolute;
        bottom:125%;
        left:50%;
        transform:translateX(-50%);
        background:var(--slate-900);
        color:#fff;
        padding:.65rem .75rem;
        border-radius:.7rem;
        font-size:.72rem;
        visibility:hidden;
        opacity:0;
        transition:.2s;
        width:220px;
        text-align:center;
        z-index:100;
        font-weight:500;
        box-shadow:var(--shadow-md);
        line-height:1.35;
    }
    .has-tooltip:hover::after{
        visibility:visible;
        opacity:1;
        bottom:140%;
    }

    .wizard-footer{
        padding:1.5rem 2rem;
        background:var(--slate-50);
        border-top:1px solid var(--slate-100);
        display:flex;
        justify-content:space-between;
        align-items:center;
        position:sticky;
        bottom:-2rem;
        margin:2rem -2rem -2rem -2rem;
        z-index:10;
    }

    .section-divider{
        display:flex;
        align-items:center;
        margin:2rem 0 1.25rem;
    }
    .section-divider:first-child{margin-top:0;}
    .section-divider::after{
        content:'';
        flex:1;
        border-bottom:2px dashed var(--slate-200);
        margin-left:1rem;
    }

    .section-badge{
        background:var(--slate-50);
        color:var(--slate-700);
        padding:.45rem .85rem;
        border-radius:.7rem;
        font-size:.75rem;
        font-weight:800;
        text-transform:uppercase;
        letter-spacing:.05em;
        display:inline-flex;
        align-items:center;
        gap:.45rem;
        border:1px solid var(--slate-300);
        box-shadow:0 1px 2px rgba(0,0,0,.02);
    }
    .badge-blue{background:var(--blue-50);color:var(--blue-700);border-color:var(--blue-200);}
    .badge-emerald{background:var(--emerald-50);color:var(--emerald-600);border-color:var(--emerald-200);}
    .badge-orange{background:var(--orange-50);color:#c2410c;border-color:var(--orange-200);}
    .badge-indigo{background:var(--indigo-50);color:var(--indigo-600);border-color:var(--indigo-200);}
    .badge-purple{background:var(--purple-50);color:var(--purple-500);border-color:var(--purple-200);}

    .btn-wizard{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:.75rem 1.5rem;
        font-size:.875rem;
        font-weight:700;
        border-radius:.75rem;
        cursor:pointer;
        transition:all .2s;
        border:1px solid transparent;
        gap:.5rem;
    }
    .btn-primary-blue{background:var(--blue-600);color:#fff;}
    .btn-primary-blue:hover{background:var(--blue-700);transform:translateY(-1px);}
    .btn-primary-emerald{background:var(--emerald-600);color:#fff;}
    .btn-primary-emerald:hover{background:var(--emerald-500);transform:translateY(-1px);}
    .btn-secondary{background:#fff;border-color:var(--slate-300);color:var(--slate-600);}
    .btn-secondary:hover{background:var(--slate-50);}
    .btn-outline-indigo{
        background:#fff;
        border:2px dashed var(--indigo-200);
        color:var(--indigo-600);
        width:100%;
        margin-top:1rem;
    }
    .btn-outline-indigo:hover{
        background:var(--indigo-50);
        border-color:var(--indigo-500);
    }

    /* ===== Missing sidebar matching design ===== */
    .wizard-toolbar{
        display:flex;
        justify-content:flex-end;
        margin-bottom:1rem;
    }

    .missing-toggle-btn{
        display:inline-flex;
        align-items:center;
        gap:.5rem;
        border:none;
        background:#fff;
        color:var(--danger-600);
        padding:.7rem 1rem;
        border-radius:.85rem;
        font-weight:700;
        font-size:.85rem;
        border:1px solid var(--danger-200);
        box-shadow:var(--shadow-sm);
        cursor:pointer;
        transition:all .2s ease;
    }
    .missing-toggle-btn:hover{
        background:var(--danger-50);
        transform:translateY(-1px);
    }
    .missing-toggle-btn.is-success{
        color:var(--emerald-600);
        border-color:var(--emerald-200);
    }
    .missing-toggle-btn.is-success:hover{
        background:var(--emerald-50);
    }

    .missing-sidebar{
        position:fixed;
        top:0;
        left:-360px;
        width:360px;
        height:100vh;
        background:#fff;
        z-index:1050;
        transition:left .28s ease-in-out;
        display:flex;
        flex-direction:column;
        border-right:1px solid var(--slate-200);
        box-shadow:var(--shadow-lg);
    }
    .missing-sidebar.is-open{left:0;}

    .missing-sidebar .sidebar-header{
        padding:1rem 1rem 1rem 1.1rem;
        background:linear-gradient(180deg,var(--danger-50) 0%, #fff 100%);
        border-bottom:1px solid var(--danger-100);
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:1rem;
    }

    .sidebar-title-wrap{
        display:flex;
        flex-direction:column;
        gap:.2rem;
    }
    .sidebar-title{
        margin:0;
        font-size:1rem;
        font-weight:800;
        color:var(--danger-600);
        display:flex;
        align-items:center;
        gap:.5rem;
    }
    .sidebar-subtitle{
        font-size:.78rem;
        color:var(--slate-500);
        font-weight:600;
    }

    .sidebar-close-btn{
        width:34px;
        height:34px;
        border-radius:10px;
        border:1px solid var(--slate-200);
        background:#fff;
        color:var(--slate-500);
        display:inline-flex;
        align-items:center;
        justify-content:center;
        cursor:pointer;
        transition:.2s;
    }
    .sidebar-close-btn:hover{
        background:var(--slate-50);
        color:var(--slate-700);
    }

    .missing-sidebar .sidebar-body{
        padding:1rem;
        overflow-y:auto;
        flex:1;
        background:var(--slate-50);
    }

    .missing-empty{
        padding:1rem;
        border-radius:16px;
        background:#fff;
        border:1px solid var(--emerald-200);
        color:var(--emerald-600);
        font-size:.9rem;
        font-weight:700;
        box-shadow:var(--shadow-sm);
        display:flex;
        align-items:center;
        gap:.6rem;
    }

    .missing-group-card{
        background:#fff;
        border:1px solid var(--slate-200);
        border-radius:16px;
        box-shadow:var(--shadow-sm);
        overflow:hidden;
        margin-bottom:14px;
    }

    .missing-group-head{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        padding:.9rem 1rem;
        background:linear-gradient(180deg,var(--slate-50) 0%, #fff 100%);
        border-bottom:1px solid var(--slate-100);
        cursor:pointer;
        transition:.2s ease;
    }
    .missing-group-head:hover{
        background:var(--blue-50);
    }

    .missing-group-left{
        min-width:0;
    }

    .missing-group-title{
        font-size:.84rem;
        font-weight:800;
        color:var(--slate-800);
        margin:0;
        display:flex;
        align-items:center;
        gap:.5rem;
    }

    .missing-group-meta{
        margin-top:4px;
        font-size:.72rem;
        color:var(--slate-500);
        font-weight:700;
    }

    .missing-open-tab{
        flex-shrink:0;
        background:#fff;
        color:var(--blue-600);
        border:1px solid var(--blue-200);
        border-radius:10px;
        font-size:.72rem;
        font-weight:800;
        padding:.45rem .7rem;
        cursor:pointer;
        transition:.2s;
    }
    .missing-open-tab:hover{
        background:var(--blue-50);
    }

    .missing-items{
        padding:.45rem;
    }

    .missing-item{
        font-size:.82rem;
        padding:.72rem .8rem;
        border-radius:12px;
        cursor:pointer;
        transition:all .18s;
        display:flex;
        align-items:center;
        gap:.55rem;
        color:var(--slate-700);
        font-weight:600;
    }
    .missing-item:hover{
        background:var(--slate-50);
        color:var(--blue-600);
    }

    .missing-item-icon{
        width:1.45rem;
        height:1.45rem;
        border-radius:999px;
        display:flex;
        align-items:center;
        justify-content:center;
        background:var(--danger-50);
        color:var(--danger-600);
        flex-shrink:0;
        font-size:.72rem;
    }

    .missing-badge{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        min-width:22px;
        height:22px;
        padding:0 7px;
        border-radius:999px;
        background:var(--danger-50);
        border:1px solid var(--danger-200);
        color:var(--danger-600);
        font-size:.72rem;
        font-weight:800;
    }

    @media (max-width: 992px){
        .form-columns{grid-template-columns:1fr;}
        .form-full-width{grid-column:span 1;}
    }

    @media (max-width: 768px){
        .missing-sidebar{
            width:92vw;
            left:-92vw;
        }

        .wizard-tabs{
            padding:1rem;
            gap:.7rem;
        }

        .step-wrapper{
            padding:1.25rem;
        }
    }
.missing-group-card{
    background:#fff;
    border:1px solid var(--slate-200);
    border-radius:16px;
    box-shadow:var(--shadow-sm);
    overflow:hidden;
    margin-bottom:14px;
}

.missing-group-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:.9rem 1rem;
    background:linear-gradient(180deg,var(--slate-50) 0%, #fff 100%);
    border-bottom:1px solid var(--slate-100);
    cursor:pointer;
    transition:.2s ease;
}

.missing-group-head:hover{
    background:var(--blue-50);
}

.missing-group-left{
    min-width:0;
    flex:1;
}

.missing-group-title{
    font-size:.84rem;
    font-weight:800;
    color:var(--slate-800);
    margin:0;
    display:flex;
    align-items:center;
    gap:.5rem;
}

.missing-group-meta{
    margin-top:4px;
    font-size:.72rem;
    color:var(--slate-500);
    font-weight:700;
}

.missing-group-actions{
    display:flex;
    align-items:center;
    gap:.55rem;
    flex-shrink:0;
}

.missing-collapse-icon{
    width:28px;
    height:28px;
    border-radius:10px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    color:var(--slate-500);
    background:#fff;
    border:1px solid var(--slate-200);
    transition:all .2s ease;
}

.missing-group-card.is-collapsed .missing-collapse-icon i{
    transform:rotate(-90deg);
}

.missing-collapse-icon i{
    transition:transform .2s ease;
}

.missing-items{
    padding:.45rem;
    display:block;
}

.missing-group-card.is-collapsed .missing-items{
    display:none;
}

</style>

<div class="obj-wizard-container">

<div id="missingDataSidebar" class="missing-sidebar">
    <div class="sidebar-header">
        <div class="sidebar-title-wrap">
            <h6 class="sidebar-title">
                <i class="feather icon-alert-circle"></i>
                Fehlende Daten
            </h6>
            <div class="sidebar-subtitle">Direkt zum Tab oder Feld springen</div>
        </div>

        <button type="button" class="sidebar-close-btn" onclick="toggleMissingSidebar()">
            <i class="feather icon-x"></i>
        </button>
    </div>

    <div class="sidebar-body" id="missingDataList"></div>
</div>

<div class="wizard-toolbar">
    <button class="missing-toggle-btn" onclick="toggleMissingSidebar()" id="toggleMissingBtn">
        <i class="feather icon-check-square"></i>
        Fehlendes anzeigen
    </button>
</div>
    <div class="wizard-card">
        <nav class="wizard-tabs">
            @php 
                $tabs = [
                    ['label' => 'Übersicht', 'color' => 'emerald', 'icon' => 'feather icon-eye'],
                    ['label' => 'Objektdaten', 'color' => 'blue', 'icon' => '1'],
                    ['label' => 'Dach', 'color' => 'indigo', 'icon' => '2'],
                    ['label' => 'Heizung', 'color' => 'orange', 'icon' => '3'],
                    ['label' => 'E-Mobilität', 'color' => 'purple', 'icon' => '4'],
                    ['label' => 'Energie', 'color' => 'blue', 'icon' => '5']
                ];
            @endphp
            @foreach($tabs as $i => $tab)
            <button class="tab-btn {{ $i==0 ? 'active' : '' }}" onclick="goToStep({{ $i + 1 }})" id="nav-{{ $i + 1 }}">
                <div class="tab-header-flex">
                    <span class="tab-icon-circle">{!! $i==0 ? '<i class="'.$tab['icon'].'"></i>' : $tab['icon'] !!}</span>
                    <span class="tab-label">{{ $tab['label'] }}</span>
                </div>
                <div class="progress-track"><div class="progress-fill" id="step{{ $i + 1 }}-progress" style="{{ $i==0 ? 'width:100%; background:var(--emerald-500)' : '' }}"></div></div>
                <div class="tab-meta">
                    <small class="tab-meta-label">{{ $i==0 ? 'Status' : 'Fortschritt' }}</small>
                    <small class="tab-meta-count" id="step{{ $i + 1 }}-count">{{ $i==0 ? 'Bereit' : '0/0' }}</small>
                </div>
            </button>
            @endforeach
        </nav>

        <div class="step-wrapper">
            <div id="step-1" class="step-content-box">@include('admin.new_leads.layouts.partials.overview', ['alternative' => $alternative, 'roofs' => $roofs])</div>
            <div id="step-2" class="step-content-box hidden">@include('admin.new_leads.layouts.partials.object_data', ['alternative' => $alternative])</div>
            <div id="step-3" class="step-content-box hidden">@include('admin.new_leads.layouts.partials.roof_info', ['alternative' => $alternative, 'roofs' => $roofs])</div>
            <div id="step-4" class="step-content-box hidden">@include('admin.new_leads.layouts.partials.heating_info', ['alternative' => $alternative])</div>
            <div id="step-5" class="step-content-box hidden">@include('admin.new_leads.layouts.partials.e_mobility', ['alternative' => $alternative])</div>
            <div id="step-6" class="step-content-box hidden">@include('admin.new_leads.layouts.partials.energy_usage', ['alternative' => $alternative])</div>
        </div>
        
        </div>
</div>
