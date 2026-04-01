<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MasterSet Laravel Integration</title>
    <!-- FontAwesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SortableJS for Drag and Drop -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <!-- Chart.js for Beautiful Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- html2pdf for PDF Generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap');

        :root {
            --bg-body: #f0f7ff;
            --bg-white: #ffffff;
            --text-main: #2c3e50;
            --text-light: #94a3b8; /* slate-400 */
            --primary: #74b2d4;
            --primary-light: #e3effb;
            --primary-dark: #5a9cb9;
            --accent: #93c21c;
            --accent-light: rgba(147, 194, 28, 0.1);
            --border-color: #c0d8ea;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --radius-sm: 0.5rem;   /* 8px */
            --radius-md: 0.75rem;  /* 12px */
            --radius-lg: 1rem;     /* 16px */
            --radius-xl: 1.5rem;   /* 24px */
            --radius-2xl: 2rem;    /* 32px */
            --radius-full: 9999px;
            --danger: #ef4444;
            --danger-light: #fee2e2;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            padding-bottom: 2rem;
        }

        /* --- Animations --- */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes spinner { to { transform: rotate(360deg); } }
        .fade-in { animation: fadeIn 0.3s ease-out; }
        .loader {
            width: 20px; height: 20px;
            border: 2px solid #e5e7eb;
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spinner 0.6s linear infinite;
        }
        .hidden { display: none !important; }

        /* --- Scrollbar --- */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 10px; }

        /* --- Navigation --- */
        nav {
            background: var(--bg-white);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            box-shadow: var(--shadow-sm);
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
        }

        .logo-box {
            background: var(--primary);
            color: white;
            padding: 0.5rem;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-md);
        }
        .logo-box svg { width: 24px; height: 24px; }

        .brand-title { font-size: 1.25rem; font-weight: 900; letter-spacing: -0.05em; color: var(--text-main); }
        .brand-subtitle { font-size: 10px; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; margin-left: 0.5rem; color: var(--text-main); }
        
        /* --- Quick Menu (New) --- */
        .quick-menu-wrapper { position: relative; display: inline-block; }
        .btn-quick-menu {
            display: flex; align-items: center; gap: 0.5rem;
            font-size: 0.75rem; font-weight: 900;
            color: var(--text-light); text-decoration: none;
            padding: 0.5rem 1rem; border-radius: var(--radius-md);
            transition: all 0.2s;
            background: transparent;
            border: 1px solid transparent;
        }
        .btn-quick-menu:hover, .quick-menu-wrapper:hover .btn-quick-menu {
            background: #f8fafc; color: var(--primary); border-color: #f1f5f9;
        }

        .quick-strip {
            position: absolute; top: 100%; right: 0;
            margin-top: 0.5rem;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            padding: 0.5rem;
            display: flex; flex-direction: column; gap: 0.25rem;
            opacity: 0; visibility: hidden; transform: translateY(10px);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 100;
            min-width: 50px;
        }

        .quick-menu-wrapper:hover .quick-strip {
            opacity: 1; visibility: visible; transform: translateY(0);
        }

        .qs-link {
            width: 40px; height: 40px;
            display: flex; align-items: center; justify-content: center;
            border-radius: var(--radius-md);
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.2s;
            font-size: 1rem;
        }
        .qs-link:hover { background: var(--primary-light); color: var(--primary); }
        .qs-link.qs-danger:hover { background: var(--danger-light); color: var(--danger); }

        .qs-divider { height: 1px; background: #f1f5f9; margin: 0.25rem 0; width: 100%; }

        /* --- Container --- */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* --- Buttons --- */
        .btn {
            border: none;
            cursor: pointer;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s;
            font-weight: 900;
        }
        .btn:active { transform: scale(0.98); }

        .btn-icon { width: 40px; height: 40px; border-radius: var(--radius-md); background: white; border: 1px solid var(--border-color); color: var(--text-light); }
        .btn-icon:hover { color: var(--primary); border-color: var(--primary); }

        .btn-primary {
            background: var(--primary);
            color: white;
            padding: 1rem 2rem;
            border-radius: var(--radius-lg);
            box-shadow: 0 10px 15px -3px rgba(116, 178, 212, 0.3);
        }
        .btn-primary:hover { background: var(--primary-dark); }

        .btn-secondary {
            background: var(--primary-light);
            color: var(--primary);
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius-md);
            font-size: 0.75rem;
        }
        .btn-secondary:hover { background: var(--primary); color: white; }

        .btn-accent {
            background: var(--accent-light);
            color: var(--accent);
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius-md);
            font-size: 0.75rem;
        }
        .btn-accent:hover { background: var(--accent); color: white; }

        .btn-danger { color: #cbd5e1; background: transparent; width: 40px; height: 40px; border-radius: var(--radius-md); }
        .btn-danger:hover { color: var(--danger); background: var(--danger-light); }

        .btn-small { width: 32px; height: 32px; border-radius: 8px; font-size: 0.75rem; color: var(--text-light); background: transparent; }
        .btn-small:hover { background: white; color: var(--text-main); }

        .btn-icon-small {
            width: 24px; height: 24px; border-radius: 6px; font-size: 0.6rem; color: #cbd5e1; background: transparent; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;
        }
        .btn-icon-small:hover { color: var(--primary); border-color: var(--primary); }

        /* --- Dashboard Grid --- */
        .dashboard-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; }
        .search-wrapper { position: relative; }
        .search-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-light); pointer-events: none;}
        .search-input {
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            outline: none;
            width: 300px;
            font-weight: 700;
            color: var(--text-main);
            transition: border-color 0.2s;
        }
        .search-input:focus { border-color: var(--primary); }

        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .card-group {
            background: white;
            padding: 2rem;
            border-radius: var(--radius-2xl);
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: var(--shadow-sm);
        }
        .card-group:hover { border-color: var(--primary); box-shadow: var(--shadow-xl); transform: translateY(-4px); }
        .group-icon {
            width: 64px; height: 64px;
            background: var(--bg-body);
            color: var(--primary);
            border-radius: var(--radius-lg);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.5rem;
            transition: background 0.2s, color 0.2s;
        }
        .card-group:hover .group-icon { background: var(--primary); color: white; }
        .group-title { font-size: 1.25rem; font-weight: 900; margin-bottom: 0.5rem; }
        .group-meta { font-size: 10px; font-weight: 900; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em; }

        /* --- Set List --- */
        .list-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .list-nav { display: flex; align-items: center; gap: 1rem; }
        
        .list-grid { display: grid; gap: 1rem; }
        .card-set {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-2xl);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s;
            cursor: pointer;
        }
        .card-set:hover { border-color: var(--primary); box-shadow: var(--shadow-xl); }

        .set-info { display: flex; align-items: center; gap: 1.5rem; }
        .set-icon {
            width: 64px; height: 64px;
            background: #f8fafc;
            color: #cbd5e1;
            border-radius: var(--radius-lg);
            display: flex; align-items: center; justify-content: center;
        }
        .card-set:hover .set-icon { color: var(--primary); }

        .pill-container { display: flex; gap: 0.5rem; margin-top: 0.5rem; }
        .pill {
            font-size: 10px; font-weight: 800;
            padding: 4px 8px; border-radius: 6px;
            display: flex; align-items: center; gap: 6px;
        }
        .pill-blue { background: #f0f7ff; border: 1px solid rgba(192, 216, 234, 0.5); color: var(--text-main); }
        .pill-gray { background: #f8fafc; border: 1px solid #f1f5f9; color: var(--text-main); }
        .pill-green { background: rgba(147, 194, 28, 0.1); border: 1px solid rgba(147, 194, 28, 0.2); color: var(--text-main); }
        
        .dot { width: 8px; height: 8px; border-radius: 50%; }
        .bg-blue { background: var(--primary); }
        .bg-gray { background: #94a3b8; }
        .bg-green { background: var(--accent); }

        /* --- Editor Layout --- */
        .editor-header {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-2xl);
            border: 1px solid var(--border-color);
            position: sticky;
            top: 85px;
            z-index: 40;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
        }
        .input-title { font-size: 1.5rem; font-weight: 900; width: 100%; border: none; outline: none; background: transparent; color: var(--text-main); margin-bottom: 0.25rem;}
        .input-desc { font-size: 0.875rem; font-weight: 700; width: 100%; border: none; outline: none; background: transparent; color: var(--text-light); }

        .editor-grid { display: grid; grid-template-columns: 8fr 4fr; gap: 2rem; }
        .section-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.5rem; }
        .section-title { font-size: 1.25rem; font-weight: 900; display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem; }
        .section-subtitle { font-size: 0.75rem; font-weight: 700; color: var(--text-light); }

        /* --- Sidebar Summary --- */
        .summary-card {
            background: white;
            padding: 2rem;
            border-radius: var(--radius-2xl);
            border: 1px solid var(--border-color);
            position: sticky;
            top: 200px;
            box-shadow: var(--shadow-xl);
        }
        .summary-title { font-size: 10px; font-weight: 900; color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.2em; margin-bottom: 2rem; }
        
        .summary-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .label-col { display: flex; flex-direction: column; }
        .label-main { font-size: 0.875rem; font-weight: 700; color: #64748b; }
        .label-badge { font-size: 10px; font-weight: 900; padding: 2px 6px; border-radius: 4px; width: fit-content; margin-top: 4px; }
        .val-text { font-size: 1.125rem; font-weight: 900; }

        .total-section { padding-top: 2rem; border-top: 1px solid #f1f5f9; margin-top: 1rem; }
        .total-label { font-size: 10px; font-weight: 900; color: var(--primary); text-transform: uppercase; letter-spacing: 0.1em; }
        .total-value { font-size: 2.25rem; font-weight: 900; letter-spacing: -0.05em; }
        
        .progress-bar { height: 8px; width: 100%; background: #f1f5f9; border-radius: 99px; overflow: hidden; display: flex; margin-top: 1.5rem; }
        .progress-segment { height: 100%; }

        /* --- Component Items --- */
        .comp-item {
            background: white;
            border-radius: var(--radius-2xl);
            border: 1px solid var(--border-color);
            overflow: hidden;
            margin-bottom: 1rem;
            box-shadow: var(--shadow-sm);
        }
        .comp-main { padding: 1.25rem; display: flex; align-items: center; justify-content: space-between; }
        .comp-left { display: flex; align-items: center; gap: 1.25rem; }
        .handle { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; color: #cbd5e1; cursor: grab; }
        .handle:active { cursor: grabbing; color: var(--primary); }
        .comp-icon { width: 48px; height: 48px; background: #f0f7ff; color: var(--primary); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; }
        .comp-percent { font-size: 9px; font-weight: 900; background: #f0f7ff; color: var(--primary); padding: 2px 6px; border-radius: 4px; border: 1px solid var(--border-color); margin-left: 0.5rem; }
        
        .qty-control { display: flex; align-items: center; background: #f8fafc; padding: 4px; border-radius: 12px; border: 1px solid #f1f5f9; }
        .qty-input { width: 40px; text-align: center; background: transparent; border: none; font-weight: 900; font-size: 0.875rem; outline: none; }

        /* New Price Input Styles */
        .price-control { display: flex; align-items: center; background: transparent; border-bottom: 2px solid #f1f5f9; transition: border-color 0.2s; }
        .price-control:focus-within { border-color: var(--primary); }
        .price-input { width: 60px; text-align: right; background: transparent; border: none; font-weight: 900; font-size: 0.75rem; outline: none; color: var(--text-main); }
        .price-control span { font-size: 0.75rem; font-weight: 900; color: #94a3b8; padding-left: 2px; }

        .price-control-sm { display: flex; align-items: center; background: white; padding: 2px 6px; border-radius: 6px; border: 1px solid #e2e8f0; }
        .price-input-sm { width: 50px; text-align: right; background: transparent; border: none; font-weight: 700; font-size: 0.75rem; outline: none; }
        .price-control-sm span { font-size: 0.75rem; font-weight: 700; color: #94a3b8; margin-left: 2px; }


        .sub-list { /* Container for sub items */ }
        .sub-item {
            padding: 0.75rem 1.25rem;
            margin-left: 3rem;
            border-top: 1px solid #f8fafc;
            background: rgba(248, 250, 252, 0.5);
            display: flex; align-items: center; justify-content: space-between;
        }
        .sub-handle { width: 24px; display: flex; justify-content: center; color: #cbd5e1; cursor: grab; margin-right: 1rem; font-size: 10px; }
        .sub-handle:active { cursor: grabbing; }

        .add-sub-btn {
            width: 100%;
            padding: 0.75rem;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--primary);
            background: white;
            border: none;
            border-top: 1px solid #f8fafc;
            cursor: pointer;
        }
        .add-sub-btn:hover { background: #f8fafc; }

        /* --- Labor Table --- */
        .labor-table-wrap { background: white; border-radius: var(--radius-2xl); border: 1px solid var(--border-color); overflow: hidden; }
        .labor-table { width: 100%; border-collapse: collapse; }
        .labor-table th { text-align: left; padding: 1.5rem; font-size: 10px; font-weight: 900; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.1em; border-bottom: 1px solid var(--border-color); background: rgba(248, 250, 252, 0.8); }
        .labor-table td { padding: 1.5rem; border-bottom: 1px solid #f1f5f9; }
        .labor-table tr:last-child td { border-bottom: none; }
        .avatar-group { display: flex; align-items: center; gap: 1rem; }
        .avatar-wrap { position: relative; }
        .avatar { width: 40px; height: 40px; border-radius: 12px; object-fit: cover; }
        .status-dot { position: absolute; bottom: -4px; right: -4px; width: 14px; height: 14px; background: var(--accent); border: 2px solid white; border-radius: 50%; }
        
        /* Labor percentage badge */
        .labor-percent-badge {
            font-size: 11px; font-weight: 900;
            background: rgba(147, 194, 28, 0.1); color: var(--accent);
            padding: 4px 8px; border-radius: 6px;
            display: inline-block;
        }

        /* --- Breadcrumbs --- */
        .breadcrumbs { display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; font-weight: 700; color: #94a3b8; margin-bottom: 2rem; }
        .crumb-link { cursor: pointer; transition: color 0.2s; display: flex; align-items: center; gap: 0.25rem; }
        .crumb-link:hover { color: var(--primary); }
        .crumb-sep { font-size: 0.625rem; color: #cbd5e1; }
        .crumb-active { color: var(--primary); cursor: default; background: #f0f7ff; padding: 2px 8px; border-radius: 6px; }

        /* --- Modal --- */
        .modal-backdrop {
            position: fixed; inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 200;
            display: flex; align-items: center; justify-content: center;
            padding: 1rem;
        }
        .modal-window {
            background: white;
            border-radius: var(--radius-2xl);
            width: 100%; max-width: 600px;
            max-height: 85vh;
            display: flex; flex-direction: column;
            box-shadow: var(--shadow-xl);
            overflow: hidden;
        }
        .modal-window.pdf-preview-window { max-width: 900px; height: 95vh; } /* Wider for PDF Preview */

        .modal-header { padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: rgba(248, 250, 252, 0.5); }
        .modal-title { font-size: 1.25rem; font-weight: 900; }
        .modal-search { padding: 1rem; border-bottom: 1px solid var(--border-color); background: white; }
        .modal-body { padding: 1.5rem; overflow-y: auto; background: rgba(248, 250, 252, 0.3); }

        .catalog-item { background: white; border: 1px solid var(--border-color); border-radius: var(--radius-xl); padding: 1.25rem; margin-bottom: 1rem; transition: border-color 0.2s; }
        .catalog-item:hover { border-color: var(--primary); }
        .supplier-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-top: 1rem; }
        .supplier-btn {
            background: #f8fafc; border: 1px solid #f1f5f9;
            padding: 0.75rem; border-radius: var(--radius-lg);
            text-align: left; cursor: pointer; transition: all 0.2s;
            display: flex; flex-direction: column;
        }
        .supplier-btn:hover { background: var(--primary); color: white; border-color: var(--primary); }
        .supplier-btn:hover span { color: rgba(255,255,255,0.8); }

        .labor-group { background: white; border: 1px solid var(--border-color); border-radius: var(--radius-2xl); padding: 1.25rem; margin-bottom: 1.5rem; }
        .labor-header { display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 1rem; align-items: center;}
        .labor-avatars { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .labor-btn-avatar {
            width: 40px; height: 40px; border-radius: 50%; border: 2px solid white; box-shadow: var(--shadow-md);
            cursor: pointer; overflow: hidden; transition: transform 0.2s; padding: 0;
        }
        .labor-btn-avatar:hover { transform: scale(1.1); border-color: var(--accent); }
        .labor-btn-avatar img { width: 100%; height: 100%; object-fit: cover; }

        /* --- PDF Report Styles --- */
        .pdf-page {
            background: white;
            padding: 3rem;
            color: #1e293b;
            font-family: 'Inter', sans-serif;
            position: relative;
        }
        .pdf-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--primary); padding-bottom: 1.5rem; margin-bottom: 2rem; }
        .pdf-brand { font-size: 2rem; font-weight: 900; letter-spacing: -0.05em; color: #0f172a; }
        .pdf-meta { text-align: right; }
        .pdf-meta h2 { font-size: 1rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 0.25rem; }
        .pdf-meta p { font-size: 1.25rem; font-weight: 900; }

        .pdf-summary-grid { display: grid; grid-template-columns: 250px 1fr; gap: 3rem; margin-bottom: 3rem; align-items: center; }
        .pdf-stats-card { background: #f8fafc; padding: 1.5rem; border-radius: 1rem; border: 1px solid #e2e8f0; }
        .pdf-stat-row { display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #e2e8f0; }
        .pdf-stat-row:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .pdf-stat-label { font-size: 0.8rem; font-weight: 700; color: #64748b; }
        .pdf-stat-val { font-size: 1rem; font-weight: 900; }
        
        .pdf-section-title { font-size: 1rem; font-weight: 900; text-transform: uppercase; color: var(--primary); border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem; margin-bottom: 1rem; margin-top: 2rem; }
        
        .pdf-table { width: 100%; border-collapse: collapse; font-size: 0.8rem; }
        .pdf-table th { text-align: left; padding: 0.75rem; background: #f1f5f9; font-weight: 900; color: #475569; }
        .pdf-table td { padding: 0.75rem; border-bottom: 1px solid #e2e8f0; }
        .pdf-table tr:last-child td { border-bottom: none; }
        
        .pdf-total-box { margin-top: 3rem; text-align: right; background: #f0f7ff; padding: 2rem; border-radius: 1rem; }
        .pdf-total-label { font-size: 0.875rem; font-weight: 700; color: var(--primary); text-transform: uppercase; }
        .pdf-total-val { font-size: 2.5rem; font-weight: 900; color: #0f172a; line-height: 1; margin-top: 0.5rem; }

        /* --- Tooltip --- */
        .tooltip-wrap { position: relative; }
        .tooltip-text {
            position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%) translateY(-5px);
            background: #1e293b; color: white; padding: 4px 8px; border-radius: 6px; font-size: 10px; font-weight: 700;
            white-space: nowrap; pointer-events: none; opacity: 0; transition: all 0.2s; margin-bottom: 5px; z-index: 10;
        }
        .tooltip-wrap:hover .tooltip-text { opacity: 1; transform: translateX(-50%) translateY(0); }

        /* --- Status Toast --- */
        .toast {
            font-size: 10px; font-weight: 900;
            background: rgba(147, 194, 28, 0.1); color: var(--accent);
            padding: 6px 12px; border-radius: 99px; border: 1px solid rgba(147, 194, 28, 0.2);
            text-transform: uppercase;
        }
        .toast.error { background: rgba(239, 68, 68, 0.1); color: var(--danger); border-color: rgba(239, 68, 68, 0.2); }
        /* --- Editor Tabs --- */
        .editor-tabs {
        display: inline-flex;
        gap: 0.5rem;
        background: #f8fafc;
        border: 1px solid var(--border-color);
        padding: 0.35rem;
        border-radius: var(--radius-lg);
        }

        .editor-tab-btn {
        border: 0;
        cursor: pointer;
        font-family: inherit;
        font-weight: 900;
        font-size: 0.75rem;
        padding: 0.65rem 1rem;
        border-radius: calc(var(--radius-lg) - 0.25rem);
        background: transparent;
        color: var(--text-light);
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        }

        .editor-tab-btn:hover {
        background: white;
        color: var(--text-main);
        box-shadow: var(--shadow-sm);
        }

        .editor-tab-btn.active {
        background: var(--primary);
        color: white;
        box-shadow: 0 10px 15px -3px rgba(116, 178, 212, 0.25);
        }

        :root{
        --task: #a855f7; /* purple */
        --task-light: rgba(168, 85, 247, 0.10);
        }

        /* add to existing pill styles */
        .pill-purple { background: var(--task-light); border: 1px solid rgba(168, 85, 247, 0.22); color: var(--text-main); }
        .bg-purple { background: var(--task); }


        @media (max-width: 1024px) {
            .editor-grid { grid-template-columns: 1fr; }
            .grid-cards { grid-template-columns: 1fr; }
            .summary-card { position: static; margin-bottom: 2rem; }
        }
    </style>
</head>
<body>

    <nav>
        <div class="nav-brand" onclick="app.navigate('dashboard')">
            <div class="logo-box">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <span class="brand-title">MASTER<span style="color:var(--primary)">SET</span></span>
                <span class="brand-subtitle">PRO-Manager</span>
            </div>
        </div>
        <div style="display:flex; align-items:center; gap:1rem;">
            <!-- Back Button Container -->
            <div id="nav-back-btn" class="hidden">
                <button onclick="app.navigate('dashboard')" class="btn btn-secondary" style="padding: 0.5rem 1rem;">
                    <i class="fas fa-arrow-left"></i> Zurück zum Dashboard
                </button>
            </div>
            
            <div style="height: 24px; width: 1px; background: var(--border-color);"></div>

            <!-- Quick Menu -->
            <div class="quick-menu-wrapper">
                <a href="javascript:void(0);" class="btn-quick-menu">
                    <i class="fas fa-th"></i> QUICK MENU
                </a>
                <div class="quick-strip">
                    <a href="javascript:void(0);" class="qs-link" title="Alle Apps" data-toggle="tooltip"><i class="fas fa-th"></i></a>
                    <a href="{{ url('/')}}" class="qs-link" title="Dashboard" data-toggle="tooltip"><i class="fas fa-home"></i></a>
                    <a href="javascript:void(0);" class="qs-link" title="Suche" data-toggle="tooltip"><i class="fas fa-search"></i></a>
                    <a href="{{ url('admin/chat')}}" class="qs-link" title="Nachrichten" data-toggle="tooltip"><i class="fas fa-comment-alt"></i></a>
                    <a href="{{ url('tasks/calendar/personal')}}" class="qs-link" title="Kalender" data-toggle="tooltip"><i class="fas fa-calendar-alt"></i></a>
                    <a href="{{ url('admin/todo/personal?tab=my') }}" class="qs-link" title="Aufgaben" data-toggle="tooltip"><i class="fas fa-check-square"></i></a>
                    <div class="qs-divider"></div>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="qs-link qs-danger" title="Logout" data-toggle="tooltip"><i class="fas fa-power-off"></i></a>
                </div>
            </div>

            <!-- Logout Form Helper (Hidden) -->
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>

            <div style="height: 24px; width: 1px; background: var(--border-color);"></div>

            <div id="loading-spinner" class="loader hidden"></div>
            <div id="status-toast" class="toast hidden">SYNC OK</div>
        </div>
    </nav>

    <main id="app-container" class="container">
        <!-- Dynamic Content -->
    </main>

    <!-- Global Modal -->
    <div id="modal-container" class="modal-backdrop hidden">
        <div id="default-modal-window" class="modal-window fade-in">
            <div class="modal-header">
                <h3 id="modal-title" class="modal-title" style="display:flex; align-items:center; gap:0.75rem;">
                    <!-- Icon injects here -->
                    <span>Titel</span>
                </h3>
                <button onclick="ui.closeModal()" class="btn btn-icon"><i class="fas fa-times"></i></button>
            </div>
            <div id="modal-search-box" class="modal-search">
                <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="modal-search-input" class="search-input" style="width:100%" placeholder="Suchen...">
                </div>
            </div>
            <div id="modal-content" class="modal-body custom-scrollbar">
                <!-- Dynamic List -->
            </div>
        </div>
    </div>

    <!-- PDF Preview Modal -->
    <div id="pdf-modal-container" class="modal-backdrop hidden">
        <div class="modal-window pdf-preview-window fade-in">
            <div class="modal-header">
                <h3 class="modal-title" style="color:var(--primary);">
                    <i class="fas fa-file-pdf"></i> Report Preview
                </h3>
                <div style="display:flex; gap:1rem;">
                    <button onclick="ui.downloadPDF()" class="btn btn-primary"><i class="fas fa-download"></i> DOWNLOAD PDF</button>
                    <button onclick="document.getElementById('pdf-modal-container').classList.add('hidden')" class="btn btn-icon"><i class="fas fa-times"></i></button>
                </div>
            </div>
            <div class="modal-body custom-scrollbar" style="background:#e2e8f0; padding: 2rem; display:flex; justify-content:center;">
                <!-- The Report Paper -->
                <div id="pdf-report-content" class="pdf-page" style="width: 210mm; min-height: 297mm; box-shadow: var(--shadow-xl);">
                    <!-- Dynamic Content -->
                </div>
            </div>
        </div>
    </div>

    <script>
/**
 * MasterSet PRO-Manager — Full Script (Material + Labor + Tasks by Stages)
 * - Keeps your existing endpoints + UI behavior
 * - Adds full "Aufgabe" tab with:
 *   - Stage filter + search
 *   - Task option browser (stages → phases → activities)
 *   - Add tasks to set (dedupe)
 *   - Edit task hours
 *   - Assign/Change stage per task
 *   - Sort tasks within each stage (SortableJS)
 */

(() => {
  // ===========================================================================
  // State
  // ===========================================================================
  const state = {
    view: 'dashboard',
    groups: [],
    sets: [],
    selectedGroup: null,
    editingSet: null,

    laborOptions: [],
    catalog: [],
    pickerContext: null,

    groupSearch: '',
    editorTab: 'material',

    // Tasks
    taskOptions: [],     // stages -> phases -> activities (from /tasks/options)
    taskSearch: '',
    selectedStageId: null, // stage filter for options panel
  };

  // ===========================================================================
  // Helpers
  // ===========================================================================
  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

    const escapeHtml = (s) =>
        String(s ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const toNum = (v, fallback = 0) => {
        const n = parseFloat(v);
        return Number.isFinite(n) ? n : fallback;
    };

    const b64EncodeJson = (obj) => {
    const json = JSON.stringify(obj ?? {});
    return btoa(
        encodeURIComponent(json).replace(/%([0-9A-F]{2})/g, (_, p) =>
        String.fromCharCode(parseInt(p, 16))
        )
    );
    };

    const b64DecodeJson = (b64) => {
    const json = decodeURIComponent(
        Array.prototype.map.call(atob(b64), (c) =>
        '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
        ).join('')
    );
    return JSON.parse(json);
    };


  const uniqBy = (arr, keyFn) => {
    const seen = new Set();
    const out = [];
    for (const x of arr) {
      const k = keyFn(x);
      if (seen.has(k)) continue;
      seen.add(k);
      out.push(x);
    }
    return out;
  };

  const normalizeSet = (set) => {
    const s = set || {};
    s.components = Array.isArray(s.components) ? s.components : [];
    s.labor = Array.isArray(s.labor) ? s.labor : [];
    s.tasks = Array.isArray(s.tasks) ? s.tasks : [];
    s.stats = s.stats || {
      mainTotal: 0, subTotal: 0, laborTotal: 0, total: 0,
      mainPct: 0, subPct: 0, laborPct: 0,
    };
    // Normalize nested arrays
    s.components.forEach(c => {
      c.subComponents = Array.isArray(c.subComponents) ? c.subComponents : [];
      c.qty = toNum(c.qty, 0);
      c.unit_price = toNum(c.unit_price, 0);
    });
    s.labor.forEach(l => {
      l.hours = toNum(l.hours, 0);
      l.rate = toNum(l.rate, 0);
    });
    s.tasks.forEach((t, i) => {
      t.hours = toNum(t.hours, 0);
      t.sort_order = Number.isFinite(+t.sort_order) ? +t.sort_order : i;
    });
    return s;
  };

  const getGroupId = () => {
    const fromSelected = state.selectedGroup?.id;
    const fromSet = state.editingSet?.article_group_id;
    return fromSelected || fromSet || null;
  };

  const findStageById = (stageId) => (state.taskOptions || []).find(s => String(s.id) === String(stageId)) || null;

  const ensureSelectedStage = () => {
    // If selectedStageId is missing, pick first stage in options
    if (!state.selectedStageId && state.taskOptions?.length) {
      state.selectedStageId = state.taskOptions[0].id;
    }
  };

  // ===========================================================================
  // API
  // ===========================================================================
  const api = {
    async request(endpoint, method = 'GET', data = null) {
      ui.showLoading(true);
      try {
        const url = `${window.location.origin}/admin/master-sets${endpoint}`;
        const options = {
          method,
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]')?.content || '',
          },
        };
        if (data) options.body = JSON.stringify(data);

        const res = await fetch(url, options);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const json = await res.json();
        ui.showStatus('SYNC OK');
        return json;
      } catch (e) {
        console.error('API Error:', e);
        ui.showStatus('ERROR', true);
        return null;
      } finally {
        ui.showLoading(false);
      }
    },

    async getGroups(q = '') {
      const res = await this.request(`/groups?q=${encodeURIComponent(q)}`);
      if (!res) return;
      state.groups = res.data || [];
      if (state.view === 'dashboard' && $('#groups-grid')) ui.updateGroupGrid();
      else ui.render();
    },

    async getSets(groupId) {
      const res = await this.request(`/data?article_group_id=${groupId}`);
      if (!res) return;
      state.sets = res.data || [];
      ui.render();
    },

    async loadSet(id) {
      const res = await this.request(`/${id}`);
      if (!res) return;
      state.editingSet = normalizeSet(res.data);

      // If tasks options already loaded, ensure stage filter sensible
      ensureSelectedStage();

      ui.recalculateLocalStats();
      ui.render();
    },

    async searchCatalog(q = '') {
      const groupId = getGroupId() || '';
      const res = await this.request(`/catalog?q=${encodeURIComponent(q)}&article_group_id=${groupId}`);
      if (!res) return;
      state.catalog = res.data || [];
      ui.renderCatalogList();
    },

    async loadLaborOptions() {
      const res = await this.request('/labor/options');
      if (!res) return;
      state.laborOptions = res.data || [];
    },

    async loadTaskOptions(q = '') {
      const groupId = getGroupId();
      if (!groupId) return;

      const res = await this.request(`/tasks/options?article_group_id=${groupId}&q=${encodeURIComponent(q)}`);
      if (!res) return;

      state.taskOptions = res.data || [];
      ensureSelectedStage();

      // If "Aufgabe" tab is open, refresh just that area
      if (state.view === 'editor' && state.editorTab === 'aufgabe') {
        ui.renderTasksTab();
      }
    },

    async saveSet() {
      const s = state.editingSet;
      if (!s) return;

      if (!s.name) return alert('Bitte geben Sie einen Namen ein.');
      const groupId = s.article_group_id || state.selectedGroup?.id || null;
      if (!groupId) return alert('Fehler: Keine Artikelgruppe zugewiesen.');

      // Keep tasks ordered by stage + sort_order
      const tasks = (s.tasks || [])
        .map((t, i) => ({ ...t, sort_order: Number.isFinite(+t.sort_order) ? +t.sort_order : i }))
        .sort((a, b) => {
          const sa = String(a.stage_id ?? '');
          const sb = String(b.stage_id ?? '');
          if (sa !== sb) return sa.localeCompare(sb);
          return (+a.sort_order) - (+b.sort_order);
        });

      const payload = {
        article_group_id: groupId,
        name: s.name,
        description: s.description,
        status: s.status || 'Published',

        components: (s.components || []).map(c => ({
          product_id: c.product_id,
          distributor_price_id: c.distributor_price_id,
          qty: c.qty,
          unit_price: c.unit_price,
          description: c.description,
          subComponents: (c.subComponents || []).map(sub => ({
            product_id: sub.product_id,
            distributor_price_id: sub.distributor_price_id,
            qty: sub.qty,
            unit_price: sub.unit_price,
            description: sub.description,
          })),
        })),

        labor: (s.labor || []).map(l => ({
          department_position_id: l.department_position_id,
          hours: l.hours,
        })),

        tasks: tasks.map((t, i) => ({
          stage_id: t.stage_id ?? null,
          task_phase_id: t.task_phase_id ?? null,
          phase_activity_id: t.phase_activity_id, // required
          hours: t.hours ?? 0,
          sort_order: (t.sort_order ?? i),

          // optional snapshots
          stage_name: t.stage_name ?? null,
          phase_name: t.phase_name ?? null,
          title: t.title ?? null,
          description: t.description ?? null,
          duration: t.duration ?? null,
          duration_type: t.duration_type ?? null,
          notes: t.notes ?? null,
          priority: t.priority ?? null,
          percent: t.percent ?? null,
        })),
      };

      let res;
      if (s.id) res = await this.request(`/${s.id}`, 'PUT', payload);
      else res = await this.request('', 'POST', payload);

      if (res && res.status === 'ok') {
        if (state.selectedGroup?.id) await this.getSets(state.selectedGroup.id);
        app.navigate('groupList');
        ui.showStatus('GESPEICHERT');
      }
    },

    async deleteSet(id) {
      const res = await this.request(`/${id}`, 'DELETE');
      if (res && res.status === 'ok') {
        if (state.selectedGroup?.id) await this.getSets(state.selectedGroup.id);
        app.navigate('groupList');
        ui.showStatus('GELÖSCHT');
      }
    },
  };

  // ===========================================================================
  // App Controller
  // ===========================================================================
  const app = {
    navigate(view) {
      state.view = view;

      if (view === 'dashboard') {
        state.groupSearch = '';
        api.getGroups();
      }

      if (view === 'groupList' && state.selectedGroup?.id) {
        api.getSets(state.selectedGroup.id);
      }

      ui.render();
    },

    selectGroup(group) {
      state.selectedGroup = group;
      this.navigate('groupList');
    },

    searchGroups(q) {
      state.groupSearch = q;
      api.getGroups(q);
    },

    setEditorTab(tab) {
      state.editorTab = tab;
      ui.render();
    },

    async editSet(id) {
      state.view = 'editor';
      state.editorTab = 'material';
      ui.render(); // paint shell fast

      // Load everything needed for editor
      await Promise.all([
        api.loadSet(id),
        api.loadLaborOptions(),
        api.loadTaskOptions(''),
      ]);
    },

    async createNewSet() {
      if (!state.selectedGroup) return alert('Bitte wählen Sie zuerst eine Artikelgruppe aus.');

      state.editingSet = normalizeSet({
        article_group_id: state.selectedGroup.id,
        name: '',
        description: '',
        components: [],
        labor: [],
        tasks: [],
      });

      state.view = 'editor';
      state.editorTab = 'material';
      ui.recalculateLocalStats();
      ui.render();

      await Promise.all([
        api.loadLaborOptions(),
        api.loadTaskOptions(''),
      ]);
    },
  };

  // ===========================================================================
  // UI
  // ===========================================================================
  const ui = {
    // ------------------------------
    // Dashboard / Lists
    // ------------------------------
    getGroupGridHTML() {
      const items = (state.groups || []).map(g => `
        <div onclick="app.selectGroup({id:${g.id},name:'${escapeHtml(g.article_group)}'})" class="card-group">
          <div class="group-icon"><i class="fas fa-layer-group fa-lg"></i></div>
          <h3 class="group-title">${escapeHtml(g.article_group)}</h3>
          <p class="group-meta">${g.master_sets_count || 0} MASTERSETS</p>
        </div>
      `).join('');

      return items || `
        <div style="grid-column: 1/-1; text-align:center; padding:4rem; color:#cbd5e1; font-weight:700;">
          Keine Bereiche gefunden
        </div>
      `;
    },

    updateGroupGrid() {
      const grid = $('#groups-grid');
      if (grid) grid.innerHTML = this.getGroupGridHTML();
    },

    getBreadcrumbsHTML() {
      let html = `<div class="breadcrumbs">`;
      html += `<span onclick="app.navigate('dashboard')" class="crumb-link"><i class="fas fa-home"></i> Home</span>`;

      if (state.view === 'groupList' || state.view === 'editor') {
        html += `<span class="crumb-sep"><i class="fas fa-chevron-right"></i></span>`;

        if (state.view === 'groupList') {
          html += `<span class="crumb-active">${escapeHtml(state.selectedGroup?.name || 'Gruppe')}</span>`;
        } else {
          html += `<span onclick="app.navigate('groupList')" class="crumb-link">${escapeHtml(state.selectedGroup?.name || 'Gruppe')}</span>`;
          html += `<span class="crumb-sep"><i class="fas fa-chevron-right"></i></span>`;
          html += `<span class="crumb-active">${escapeHtml(state.editingSet?.name || 'Neues Set')}</span>`;
        }
      }
      html += `</div>`;
      return html;
    },

    // ------------------------------
    // Main render
    // ------------------------------
    render() {
      // Top nav back button
      const navBtn = $('#nav-back-btn');
      if (navBtn) {
        if (state.view === 'dashboard') navBtn.classList.add('hidden');
        else navBtn.classList.remove('hidden');
      }

      const container = $('#app-container');
      if (!container) return;
      container.innerHTML = '';

      if (state.view === 'dashboard') {
        container.innerHTML = `
          <header class="dashboard-header">
            <div>
              <h2 class="brand-title" style="font-size:2rem; margin-bottom:0.25rem;">Business Units</h2>
              <p style="color:#94a3b8; font-weight:500;">Wählen Sie einen Bereich zur Verwaltung</p>
            </div>
            <div class="search-wrapper">
              <i class="fas fa-search search-icon"></i>
              <input type="text" oninput="app.searchGroups(this.value)" class="search-input"
                placeholder="Bereich suchen..." value="${escapeHtml(state.groupSearch || '')}">
            </div>
          </header>
          <div id="groups-grid" class="grid-cards">${this.getGroupGridHTML()}</div>
        `;
        return;
      }

      if (state.view === 'groupList') {
        const setList = Array.isArray(state.sets) ? state.sets : (state.sets?.data || []);
        let content = this.getBreadcrumbsHTML();

        content += `
          <div class="list-header">
            <div class="list-nav">
              <button onclick="app.navigate('dashboard')" class="btn btn-icon"><i class="fas fa-chevron-left"></i></button>
              <div>
                <h2 style="font-size:1.5rem; font-weight:900; color:var(--text-main);">${escapeHtml(state.selectedGroup?.name || '')}</h2>
                <p style="font-size:0.75rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.1em;">
                  MasterSet Übersicht
                </p>
              </div>
            </div>
            <button onclick="app.createNewSet()" class="btn btn-primary">
              <i class="fas fa-plus"></i> NEUES SET ERSTELLEN
            </button>
          </div>

          <div class="list-grid">
            ${setList.map(s => {
             const total = (s.stats?.total || 1);
            const mainPct  = ((s.stats?.mainCost || 0) / total * 100).toFixed(0);
            const subPct   = ((s.stats?.subCost  || 0) / total * 100).toFixed(0);
            const laborPct = ((s.stats?.labor    || 0) / total * 100).toFixed(0);

            // NEW: tasks count (supports multiple possible keys)
            const taskCount =
            (s.stats?.taskCount ?? s.stats?.tasksCount ?? s.tasks_count ?? s.tasksCount ?? 0);

            return `
                <div onclick="app.editSet(${s.id})" class="card-set">
                    <div class="set-info">
                    <div class="set-icon"><i class="fas fa-box-open fa-lg"></i></div>
                    <div>
                        <h4 style="font-weight:900; font-size:1.125rem; color:var(--text-main); margin-bottom:0.5rem;">
                        ${escapeHtml(s.name || 'Unbenanntes Set')}
                        </h4>

                        <div class="pill-container">
                        <div class="pill pill-blue">
                            <div class="dot bg-blue"></div>
                            <span>${s.stats?.mainCount || 0} Main (${mainPct}%)</span>
                        </div>

                        <div class="pill pill-gray">
                            <div class="dot bg-gray"></div>
                            <span>${s.stats?.subCount || 0} Sub (${subPct}%)</span>
                        </div>

                        <div class="pill pill-green">
                            <div class="dot bg-green"></div>
                            <span>${s.stats?.laborCount || 0} Pers. (${laborPct}%)</span>
                        </div>

                        <!-- NEW: Tasks -->
                        <div class="pill pill-purple">
                            <div class="dot bg-purple"></div>
                            <span>${taskCount} Tasks</span>
                        </div>
                        </div>
                    </div>
                    </div>

                    <div style="text-align:right;">
                    <p style="font-size:0.625rem; font-weight:900; color:#cbd5e1; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.25rem;">
                        Set Gesamtwert
                    </p>
                    <p style="font-size:1.75rem; font-weight:900; color:var(--text-main);">
                        ${(s.stats?.total || 0).toLocaleString('de-DE', { style:'currency', currency:'EUR' })}
                    </p>
                    </div>
                </div>
            `;

            }).join('')}
          </div>
        `;

        container.innerHTML = content;
        return;
      }

      if (state.view === 'editor') {
        this.renderEditor(container);
      }
    },

    // ------------------------------
    // Editor
    // ------------------------------
    renderEditor(container) {
      if (!state.editingSet) {
        container.innerHTML = `${this.getBreadcrumbsHTML()}
          <div style="padding:3rem; text-align:center; color:#94a3b8; font-weight:900;">
            Lade Set...
          </div>`;
        return;
      }

      const s = state.editingSet;

      let content = this.getBreadcrumbsHTML();
      content += `
        <div class="editor-header">
          <div style="flex:1; margin-right:2rem;">
            <input type="text" oninput="state.editingSet.name = this.value" class="input-title"
              value="${escapeHtml(s.name || '')}" placeholder="Set Name eingeben...">
            <input type="text" oninput="state.editingSet.description = this.value" class="input-desc"
              value="${escapeHtml(s.description || '')}" placeholder="Interne Beschreibung...">
          </div>
          <div style="display:flex; gap:1rem;">
            <button onclick="ui.openReport()" class="btn btn-secondary"><i class="fas fa-file-pdf"></i> PDF EXPORT</button>
            ${s.id ? `<button onclick="ui.deleteConfirm(${s.id})" class="btn btn-danger"><i class="fas fa-trash"></i></button>` : ''}
            <button onclick="api.saveSet()" class="btn btn-primary">SET SPEICHERN</button>
          </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
          <div class="editor-tabs">
            <button class="editor-tab-btn ${state.editorTab === 'material' ? 'active' : ''}" onclick="app.setEditorTab('material')">
              <i class="fas fa-cubes"></i> Material
            </button>
            <button class="editor-tab-btn ${state.editorTab === 'aufgabe' ? 'active' : ''}" onclick="app.setEditorTab('aufgabe')">
              <i class="fas fa-list-check"></i> Aufgabe
            </button>
          </div>
          <div style="font-size:0.75rem; font-weight:800; color:#94a3b8;">
            Aktiver Tab: ${state.editorTab === 'material' ? 'Material' : 'Aufgabe'}
          </div>
        </div>

        <div class="editor-grid">
          <div style="display:flex; flex-direction:column; gap:2.5rem;">
            ${
              state.editorTab === 'material'
                ? `
                  <section>
                    <div class="section-header">
                      <div>
                        <h3 class="section-title" style="color:var(--primary);"><i class="fas fa-cubes"></i> Komponenten</h3>
                        <p class="section-subtitle">Haupt- und Unterartikel des Sets (Drag & Drop)</p>
                      </div>
                      <div style="display:flex; gap:0.75rem;">
                        <button onclick="ui.openCatalog('main')" class="btn btn-icon" title="Katalog durchsuchen"><i class="fas fa-search"></i></button>
                        <button onclick="ui.openCatalog('main')" class="btn btn-secondary">+ ARTIKEL HINZUFÜGEN</button>
                      </div>
                    </div>
                    <div id="comp-list"></div>
                  </section>

                  <section>
                    <div class="section-header">
                      <div>
                        <h3 class="section-title" style="color:var(--accent);"><i class="fas fa-user-clock"></i> Personalaufwand</h3>
                        <p class="section-subtitle">Zugeordnete Positionen & Arbeitsstunden</p>
                      </div>
                      <button onclick="ui.openLaborPicker()" class="btn btn-accent">+ PERSONAL / POSITION</button>
                    </div>

                    <div class="labor-table-wrap">
                      <table class="labor-table">
                        <thead>
                          <tr>
                            <th>Position / Mitarbeiter</th>
                            <th style="text-align:center;">Anteil</th>
                            <th style="text-align:center;">Stunden</th>
                            <th style="text-align:right;">Kosten</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody id="labor-body"></tbody>
                      </table>
                    </div>
                  </section>
                `
                : `
                  <section>
                    <div class="section-header">
                      <div>
                        <h3 class="section-title" style="color:var(--text-main);"><i class="fas fa-list-check"></i> Aufgaben</h3>
                        <p class="section-subtitle">Aufgaben nach Stages auswählen und dem Set zuweisen</p>
                      </div>
                      <div style="display:flex; gap:0.75rem;">
                        <button onclick="ui.reloadTaskOptions()" class="btn btn-icon" title="Optionen aktualisieren"><i class="fas fa-rotate"></i></button>
                        <button onclick="ui.openTaskPicker()" class="btn btn-secondary"><i class="fas fa-plus"></i> AUFGABE HINZUFÜGEN</button>
                      </div>
                    </div>

                    <!-- Tasks UI Mount -->
                    <div id="tasks-tab-mount"></div>
                  </section>
                `
            }
          </div>

          <div>
            <div class="summary-card">
              <h4 class="summary-title">Kalkulationsübersicht</h4>

              <div class="summary-row">
                <div class="label-col">
                  <span class="label-main">Hauptartikel</span>
                  <span class="label-badge bg-blue" style="background:#f0f7ff; color:var(--primary);">${s.stats.mainPct}% Anteil</span>
                </div>
                <span class="val-text">${s.stats.mainTotal.toLocaleString('de-DE', {style:'currency', currency:'EUR'})}</span>
              </div>

              <div class="summary-row">
                <div class="label-col">
                  <span class="label-main">Zubehör / Sub</span>
                  <span class="label-badge" style="background:#f8fafc; color:#94a3b8;">${s.stats.subPct}% Anteil</span>
                </div>
                <span class="val-text">${s.stats.subTotal.toLocaleString('de-DE', {style:'currency', currency:'EUR'})}</span>
              </div>

              <div class="summary-row">
                <div class="label-col">
                  <span class="label-main">Personal</span>
                  <span class="label-badge" style="background:rgba(147, 194, 28, 0.1); color:var(--accent);">${s.stats.laborPct}% Anteil</span>
                </div>
                <span class="val-text">${s.stats.laborTotal.toLocaleString('de-DE', {style:'currency', currency:'EUR'})}</span>
              </div>

              <div class="total-section">
                <div style="display:flex; justify-content:space-between; align-items:flex-end;">
                  <span class="total-label">Gesamtwert Set</span>
                  <span class="total-value">${s.stats.total.toLocaleString('de-DE', {style:'currency', currency:'EUR'})}</span>
                </div>

                <div class="progress-bar">
                  <div class="progress-segment" style="background:var(--primary); width: ${s.stats.mainPct}%"></div>
                  <div class="progress-segment" style="background:#94a3b8; width: ${s.stats.subPct}%"></div>
                  <div class="progress-segment" style="background:var(--accent); width: ${s.stats.laborPct}%"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      `;

      container.innerHTML = content;

      if (state.editorTab === 'material') {
        this.renderComponentItems();
        this.renderLaborItems();
      } else {
        this.renderTasksTab();
      }
    },

    // ------------------------------
    // Material: components
    // ------------------------------
    renderComponentItems() {
      const list = $('#comp-list');
      if (!list) return;

      const comps = state.editingSet.components || [];
      list.innerHTML = comps.map((c, idx) => `
        <div class="comp-item" data-index="${idx}">
          <div class="comp-main">
            <div class="comp-left">
              <div class="handle"><i class="fas fa-grip-vertical"></i></div>
              <div class="comp-icon"><i class="fas fa-cube"></i></div>
              <div>
                <div style="display:flex; align-items:center;">
                  <p style="font-weight:900; color:var(--text-main); font-size:0.875rem;">${escapeHtml(c.product_name)}</p>
                  <span class="comp-percent">${escapeHtml(c.percentOfTotal)}%</span>
                </div>

                <div style="display:flex; align-items:center; gap:0.5rem; margin-top:0.25rem;">
                  <span style="font-size:0.625rem; font-weight:700; color:#94a3b8; text-transform:uppercase;">
                    ${escapeHtml(c.distributor_name)} •
                  </span>

                  <div class="price-control">
                    <input type="number" step="0.01" onchange="ui.updatePrice(${idx}, null, this.value)"
                      class="price-input" value="${escapeHtml(c.unit_price)}">
                    <span>€</span>
                  </div>

                  <button onclick="ui.refreshPrice(${idx}, null)" class="btn-icon-small" title="Preis aktualisieren">
                    <i class="fas fa-sync-alt"></i>
                  </button>
                </div>
              </div>
            </div>

            <div style="display:flex; align-items:center; gap:1.5rem;">
              <div class="qty-control">
                <button onclick="ui.updateQty(${idx}, null, ${toNum(c.qty) - 1})" class="btn-small"><i class="fas fa-minus"></i></button>
                <input type="number" onchange="ui.updateQty(${idx}, null, this.value)" class="qty-input" value="${escapeHtml(c.qty)}">
                <button onclick="ui.updateQty(${idx}, null, ${toNum(c.qty) + 1})" class="btn-small"><i class="fas fa-plus"></i></button>
              </div>

              <button onclick="ui.removeComp(${idx})" class="btn-danger"><i class="fas fa-trash"></i></button>
            </div>
          </div>

          <div id="sub-list-${idx}" class="sub-list">
            ${(c.subComponents || []).map((sub, sIdx) => `
              <div class="sub-item" data-main-index="${idx}" data-sub-index="${sIdx}">
                <div style="display:flex; align-items:center; gap:1rem;">
                  <div class="sub-handle"><i class="fas fa-grip-lines"></i></div>
                  <div>
                    <div style="display:flex; align-items:center; gap:0.5rem;">
                      <span style="font-size:0.75rem; font-weight:700; color:#64748b;">${escapeHtml(sub.product_name)}</span>
                      <span style="font-size:0.5rem; font-weight:900; background:white; color:#94a3b8; padding:1px 4px; border:1px solid #e2e8f0; border-radius:4px;">
                        ${escapeHtml(sub.percentOfTotal)}%
                      </span>
                    </div>
                  </div>
                </div>

                <div style="display:flex; align-items:center; gap:1rem;">
                  <div class="price-control-sm">
                    <input type="number" step="0.01" onchange="ui.updatePrice(${idx}, ${sIdx}, this.value)"
                      class="price-input-sm" value="${escapeHtml(sub.unit_price)}">
                    <span>€</span>
                  </div>

                  <button onclick="ui.refreshPrice(${idx}, ${sIdx})" class="btn-icon-small" title="Preis aktualisieren">
                    <i class="fas fa-sync-alt"></i>
                  </button>

                  <div style="width:1px; height:20px; background:#e2e8f0;"></div>

                  <input type="number" onchange="ui.updateQty(${idx}, ${sIdx}, this.value)" class="qty-input"
                    style="background:white; border:1px solid #e2e8f0; border-radius:6px; font-size:0.75rem;"
                    value="${escapeHtml(sub.qty)}">

                  <button onclick="ui.removeComp(${idx}, ${sIdx})" class="btn-danger" style="width:24px; height:24px; font-size:10px;">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
            `).join('')}
          </div>

          <button onclick="ui.openCatalog('sub', ${idx})" class="add-sub-btn">+ UNTER-ARTIKEL VERKNÜPFEN</button>
        </div>
      `).join('');

      if (!comps.length) {
        list.innerHTML = `
          <div style="padding:4rem; text-align:center; border:2px dashed var(--border-color); border-radius:var(--radius-2xl); color:#cbd5e1; font-weight:700; font-style:italic;">
            Noch keine Artikel hinzugefügt
          </div>`;
        return;
      }

      // Sort main components
      new Sortable(list, {
        handle: '.handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: (evt) => {
          const item = state.editingSet.components.splice(evt.oldIndex, 1)[0];
          state.editingSet.components.splice(evt.newIndex, 0, item);
          ui.renderComponentItems();
        }
      });

      // Sort sub components per main component
      state.editingSet.components.forEach((c, idx) => {
        const subEl = $(`#sub-list-${idx}`);
        if (!subEl) return;
        new Sortable(subEl, {
          handle: '.sub-handle',
          animation: 150,
          onEnd: (evt) => {
            const subItem = state.editingSet.components[idx].subComponents.splice(evt.oldIndex, 1)[0];
            state.editingSet.components[idx].subComponents.splice(evt.newIndex, 0, subItem);
            ui.renderComponentItems();
          }
        });
      });
    },

    // ------------------------------
    // Labor
    // ------------------------------
    renderLaborItems() {
      const body = $('#labor-body');
      if (!body) return;

      body.innerHTML = (state.editingSet.labor || []).map((l, idx) => `
        <tr>
          <td>
            <div class="avatar-group">
              <div class="avatar-wrap">
                <img src="${escapeHtml(l.avatar || ('https://i.pravatar.cc/150?u=' + l.employee_id))}" class="avatar">
                <div class="status-dot"></div>
              </div>
              <div>
                <div style="display:flex; align-items:center; gap:0.5rem;">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    style="width:14px; height:14px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                  </svg>
                  <p style="font-size:0.875rem; font-weight:900; color:var(--text-main);">${escapeHtml(l.position_name || 'Position')}</p>
                </div>
                <p style="font-size:0.625rem; font-weight:700; color:#94a3b8; text-transform:uppercase;">
                  ${escapeHtml(l.employee_name)} • ${escapeHtml(l.department_name)}
                </p>
              </div>
            </div>
          </td>

          <td style="text-align:center;">
            <span class="labor-percent-badge">${escapeHtml(l.percentOfTotal)}%</span>
          </td>

          <td style="text-align:center;">
            <div class="qty-control" style="display:inline-flex;">
              <input type="number" onchange="ui.updateLaborHours(${idx}, this.value)" class="qty-input" style="width:50px;"
                value="${escapeHtml(l.hours)}">
              <span style="font-size:10px; font-weight:900; color:#cbd5e1; padding-right:8px;">h</span>
            </div>
          </td>

          <td style="text-align:right; font-weight:900; color:var(--text-main);">
            ${(toNum(l.rate) * toNum(l.hours)).toLocaleString('de-DE', {style:'currency', currency:'EUR'})}
          </td>

          <td style="text-align:right;">
            <button onclick="ui.removeLabor(${idx})" class="btn-danger"><i class="fas fa-trash-alt"></i></button>
          </td>
        </tr>
      `).join('');
    },

    // ------------------------------
    // Tasks (Stages)
    // ------------------------------
    reloadTaskOptions() {
      api.loadTaskOptions(state.taskSearch || '');
      ui.showStatus('TASKS SYNC');
    },

    openTaskPicker() {
      // Use existing modal as a Task Picker
      state.pickerContext = { type: 'task' };

      const modal = $('#modal-container');
      const titleEl = $('#modal-title');
      const searchBox = $('#modal-search-box');
      const contentEl = $('#modal-content');
      const searchInput = $('#modal-search-input');

      titleEl.innerHTML = `<i class="fas fa-list-check" style="color:var(--primary)"></i> <span>Aufgaben wählen (Stages)</span>`;
      searchBox.classList.remove('hidden');

      searchInput.value = state.taskSearch || '';
      searchInput.oninput = (e) => {
        state.taskSearch = e.target.value;
        api.loadTaskOptions(state.taskSearch);
      };

      // Render options in modal
      contentEl.innerHTML = this.getTaskOptionsHTML({ mode: 'modal' });

      modal.classList.remove('hidden');
    },

    renderTasksTab() {
      const mount = $('#tasks-tab-mount');
      if (!mount) return;

      ensureSelectedStage();

      // Two-column layout: options (left) + selected (right)
      mount.innerHTML = `
        <div style="display:grid; grid-template-columns: 1fr 1.2fr; gap:1.5rem;">
          <!-- Options -->
          <div style="background:white; border:1px solid var(--border-color); border-radius:var(--radius-2xl); padding:1.25rem;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
              <div>
                <div style="font-weight:900; color:var(--text-main);">Aufgaben Optionen</div>
                <div style="font-size:0.75rem; font-weight:700; color:#94a3b8;">Stages → Phasen → Activities</div>
              </div>
              <button class="btn btn-icon" onclick="ui.openTaskPicker()" title="Vollansicht">
                <i class="fas fa-up-right-from-square"></i>
              </button>
            </div>

            <div style="display:flex; gap:0.75rem; align-items:center; margin-bottom:1rem;">
              <select id="tasks-stage-filter" class="search-input" style="width: 50%; padding-left:1rem;">
                ${(state.taskOptions || []).map(st => `
                  <option value="${escapeHtml(st.id)}" ${String(st.id)===String(state.selectedStageId) ? 'selected' : ''}>
                    ${escapeHtml(st.name || st.title || ('Stage #' + st.id))}
                  </option>
                `).join('')}
              </select>

              <div class="search-wrapper" style="width: 50%;">
                <i class="fas fa-search search-icon"></i>
                <input id="tasks-search" type="text" class="search-input" style="width:100%;"
                  placeholder="Aufgabe suchen..." value="${escapeHtml(state.taskSearch || '')}">
              </div>
            </div>

            <div id="tasks-options-panel" style="max-height: 520px; overflow:auto; padding-right:6px;">
              ${this.getTaskOptionsHTML({ mode: 'inline' })}
            </div>
          </div>

          <!-- Selected tasks -->
          <div style="background:white; border:1px solid var(--border-color); border-radius:var(--radius-2xl); padding:1.25rem;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
              <div>
                <div style="font-weight:900; color:var(--text-main);">Zugewiesene Aufgaben</div>
                <div style="font-size:0.75rem; font-weight:700; color:#94a3b8;">Sortieren per Drag & Drop (pro Stage)</div>
              </div>
              <div class="pill pill-gray" style="margin:0;">
                <div class="dot bg-gray"></div>
                <span>${(state.editingSet.tasks || []).length} Tasks</span>
              </div>
            </div>

            <div id="tasks-selected-panel">
              ${this.getSelectedTasksHTML()}
            </div>
          </div>
        </div>
      `;

      // Wire stage filter + search
      const stageSel = $('#tasks-stage-filter');
      if (stageSel) {
        stageSel.onchange = (e) => {
          state.selectedStageId = e.target.value;
          ui.renderTasksTab();
        };
      }

      const search = $('#tasks-search');
      if (search) {
        search.oninput = (e) => {
          state.taskSearch = e.target.value;
          // live load options
          api.loadTaskOptions(state.taskSearch);
          // optimistic local refresh (options will re-render after API)
          ui.renderTasksTab();
        };
      }

      // Make selected tasks sortable per stage
      this.bindSelectedTasksSortables();
    },

    getTaskOptionsHTML({ mode }) {
    const stages = Array.isArray(state.taskOptions) ? state.taskOptions : [];
    const stageId = state.selectedStageId;
    const stage = stageId ? findStageById(stageId) : null;

    if (!stages.length) {
        return `
        <div style="padding:2rem; text-align:center; color:#cbd5e1; font-weight:900;">
            Keine Task-Optionen gefunden
        </div>
        `;
    }

    const listStages =
        mode === 'modal'
        ? stages
        : (stage ? [stage] : [stages[0]]);

    return listStages.map((st) => {
        const stName = st.stage || st.name || st.title || ('Stage #' + st.id);

        const phases = Array.isArray(st.phases)
        ? st.phases
        : (Array.isArray(st.task_phases) ? st.task_phases : []);

        return `
        <div class="catalog-item" style="margin-bottom:1rem;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
            <div style="font-weight:900; color:var(--text-main); display:flex; align-items:center; gap:0.5rem;">
                <span style="width:10px; height:10px; border-radius:999px; background:var(--primary); display:inline-block;"></span>
                ${escapeHtml(stName)}
            </div>
            <span style="font-size:0.625rem; font-weight:900; color:#cbd5e1; text-transform:uppercase;">
                ${phases.length} Phasen
            </span>
            </div>

            ${
            phases.length
                ? phases.map((ph) => {
                    const phName = ph.phase_name || ph.name || ph.title || ('Phase #' + ph.id);

                    const acts = Array.isArray(ph.activities)
                    ? ph.activities
                    : (Array.isArray(ph.phase_activities) ? ph.phase_activities : []);

                    if (!acts.length) return '';

                    return `
                    <div style="border-top:1px solid #f1f5f9; padding-top:0.75rem; margin-top:0.75rem;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                        <div style="font-size:0.75rem; font-weight:900; color:#64748b;">
                            ${escapeHtml(phName)}
                        </div>
                        <div style="font-size:10px; font-weight:900; color:#94a3b8; text-transform:uppercase;">
                            ${acts.length} Activities
                        </div>
                        </div>

                        <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        ${
                            acts.map((act) => {
                            const actId = act.id ?? act.phase_activity_id;
                            const title = act.title || act.name || ('Activity #' + actId);
                            const desc = act.description || '';
                            const dur = act.duration != null ? act.duration : '';
                            const durType = act.duration_type || '';
                            const defHours = act.hours != null
                                ? act.hours
                                : (act.default_hours != null ? act.default_hours : 0);

                            const payload = {
                                stage_id: st.id,
                                stage_name: stName,
                                task_phase_id: ph.id,
                                phase_name: phName,
                                phase_activity_id: actId,
                                title,
                                description: desc,
                                duration: dur,
                                duration_type: durType,
                                notes: act.notes || null,
                                priority: act.priority || null,
                                percent: act.percent || null,
                                hours: defHours,
                            };

                            const encoded = b64EncodeJson(payload);

                            return `
                                <button
                                class="supplier-btn"
                                style="border-radius: var(--radius-lg);"
                                onclick="ui.addTaskFromEncoded('${encoded}')"
                                title="Hinzufügen"
                                >
                                <span style="font-size:0.75rem; font-weight:900;">${escapeHtml(title)}</span>
                                <span style="font-size:0.625rem; font-weight:700; color:#94a3b8;">
                                    ${desc ? escapeHtml(desc).slice(0, 80) : '—'}
                                </span>
                                <span style="font-size:0.625rem; font-weight:900; color:#94a3b8; text-transform:uppercase;">
                                    Std: ${escapeHtml(defHours)} ${dur ? `• Dauer: ${escapeHtml(dur)} ${escapeHtml(durType)}` : ''}
                                </span>
                                </button>
                            `;
                            }).join('')
                        }
                        </div>
                    </div>
                    `;
                }).join('')
                : `
                <div style="padding:1rem; color:#cbd5e1; font-weight:900; text-align:center;">
                    Keine Phasen / Activities in dieser Stage
                </div>
                `
            }
        </div>
        `;
    }).join('');
    },

   addTaskFromEncoded(encoded) {
  let payload;
  try {
    payload = b64DecodeJson(encoded);
  } catch (e) {
    ui.showStatus('PAYLOAD ERROR', true);
    return;
  }

  // Dedupe by phase_activity_id (global)
  const exists = (state.editingSet.tasks || []).some(
    (t) => String(t.phase_activity_id) === String(payload.phase_activity_id)
  );
  if (exists) {
    ui.showStatus('DUPLIKAT', true);
    return;
  }

  const nextOrder = (state.editingSet.tasks || []).length;

  state.editingSet.tasks.push({
    stage_id: payload.stage_id ?? null,
    stage_name: payload.stage_name ?? null,
    task_phase_id: payload.task_phase_id ?? null,
    phase_name: payload.phase_name ?? null,
    phase_activity_id: payload.phase_activity_id,
    title: payload.title ?? null,
    description: payload.description ?? null,
    duration: payload.duration ?? null,
    duration_type: payload.duration_type ?? null,
    notes: payload.notes ?? null,
    priority: payload.priority ?? null,
    percent: payload.percent ?? null,
    hours: toNum(payload.hours, 0),
    sort_order: nextOrder,
  });

  ui.showStatus('TASK HINZUGEFÜGT');
  ui.renderTasksTab();
},


    getSelectedTasksHTML() {
      const tasks = Array.isArray(state.editingSet?.tasks) ? state.editingSet.tasks : [];
      if (!tasks.length) {
        return `
          <div style="padding:3rem; text-align:center; border:2px dashed var(--border-color); border-radius:var(--radius-2xl); color:#cbd5e1; font-weight:900;">
            Noch keine Aufgaben zugewiesen
          </div>
        `;
      }

      // Group tasks by stage_id
      const byStage = new Map();
      tasks.forEach((t, idx) => {
        const key = String(t.stage_id ?? 'null');
        if (!byStage.has(key)) byStage.set(key, []);
        byStage.get(key).push({ t, idx });
      });

      const stageLabel = (stageKey) => {
        if (stageKey === 'null') return 'Ohne Stage';
        const stage = findStageById(stageKey);
        return stage?.name || stage?.title || (tasks.find(x => String(x.stage_id) === String(stageKey))?.stage_name) || ('Stage #' + stageKey);
      };

      const stageOrder = Array.from(byStage.keys()).sort((a, b) => a.localeCompare(b));

      return stageOrder.map(stageKey => {
        const group = (byStage.get(stageKey) || [])
          .sort((a, b) => (toNum(a.t.sort_order, 0) - toNum(b.t.sort_order, 0)));

        return `
          <div style="border:1px solid #f1f5f9; border-radius:var(--radius-xl); padding:1rem; margin-bottom:1rem;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
              <div style="font-weight:900; color:var(--text-main); display:flex; align-items:center; gap:0.5rem;">
                <span style="width:10px; height:10px; border-radius:999px; background:var(--accent); display:inline-block;"></span>
                ${escapeHtml(stageLabel(stageKey))}
              </div>
              <div style="font-size:10px; font-weight:900; color:#94a3b8; text-transform:uppercase;">
                ${group.length} Tasks
              </div>
            </div>

            <div class="tasks-stage-list" data-stage="${escapeHtml(stageKey)}" style="display:flex; flex-direction:column; gap:0.5rem;">
              ${group.map(({ t, idx }) => `
                <div class="catalog-item task-row" data-task-index="${idx}" style="margin:0; border-radius: var(--radius-xl);">
                  <div style="display:flex; justify-content:space-between; gap:1rem; align-items:flex-start;">
                    <div style="display:flex; gap:0.75rem; align-items:flex-start;">
                      <div class="handle task-handle" title="Sortieren" style="margin-top:2px;">
                        <i class="fas fa-grip-vertical"></i>
                      </div>
                      <div>
                        <div style="font-weight:900; color:var(--text-main);">${escapeHtml(t.title || 'Untitled')}</div>
                        <div style="font-size:0.75rem; font-weight:700; color:#94a3b8;">
                          ${escapeHtml(t.phase_name || '—')} • ID: ${escapeHtml(t.phase_activity_id)}
                        </div>
                        ${t.description ? `<div style="font-size:0.75rem; font-weight:700; color:#64748b; margin-top:0.25rem;">${escapeHtml(t.description).slice(0,120)}</div>` : ''}
                      </div>
                    </div>

                    <div style="display:flex; gap:0.75rem; align-items:center;">
                      <select onchange="ui.changeTaskStage(${idx}, this.value)" class="search-input" style="width:170px; padding-left:0.75rem;">
                        <option value="">Ohne Stage</option>
                        ${(state.taskOptions || []).map(st => `
                          <option value="${escapeHtml(st.id)}" ${String(st.id)===String(t.stage_id) ? 'selected' : ''}>
                            ${escapeHtml(st.stage || st.name || st.title || ('Stage #' + st.id))}
                          </option>
                        `).join('')}
                      </select>

                      <div class="qty-control" style="display:inline-flex;">
                        <input type="number" min="0" step="0.25" onchange="ui.updateTaskHours(${idx}, this.value)"
                          class="qty-input" style="width:70px;" value="${escapeHtml(t.hours ?? 0)}">
                        <span style="font-size:10px; font-weight:900; color:#cbd5e1; padding-right:8px;">h</span>
                      </div>

                      <button onclick="ui.removeTask(${idx})" class="btn-danger" title="Entfernen">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </div>
                  </div>
                </div>
              `).join('')}
            </div>
          </div>
        `;
      }).join('');
    },

    bindSelectedTasksSortables() {
      $$('.tasks-stage-list').forEach(stageListEl => {
        new Sortable(stageListEl, {
          handle: '.task-handle',
          animation: 150,
          onEnd: () => {
            // Recompute sort_order inside each stage group based on current DOM order
            const allStageLists = $$('.tasks-stage-list');
            allStageLists.forEach(listEl => {
              const idxs = $$('.task-row', listEl).map(el => toNum(el.getAttribute('data-task-index'), -1)).filter(i => i >= 0);
              idxs.forEach((taskIndex, order) => {
                if (state.editingSet.tasks[taskIndex]) {
                  state.editingSet.tasks[taskIndex].sort_order = order;
                }
              });
            });
            ui.showStatus('SORTIERT');
          }
        });
      });
    },

    updateTaskHours(taskIndex, val) {
      const t = state.editingSet.tasks?.[taskIndex];
      if (!t) return;
      t.hours = Math.max(0, toNum(val, 0));
      ui.renderTasksTab();
    },

    changeTaskStage(taskIndex, stageId) {
      const t = state.editingSet.tasks?.[taskIndex];
      if (!t) return;

      if (!stageId) {
        t.stage_id = null;
        t.stage_name = null;
      } else {
        const st = findStageById(stageId);
        t.stage_id = stageId;
        t.stage_name = st?.name || st?.title || t.stage_name || null;
      }

      // Reset order within stage
      t.sort_order = 9999;
      ui.renderTasksTab();
    },

    removeTask(taskIndex) {
      if (!Array.isArray(state.editingSet.tasks)) return;
      state.editingSet.tasks.splice(taskIndex, 1);
      // Re-index sort_order
      state.editingSet.tasks.forEach((t, i) => (t.sort_order = i));
      ui.showStatus('ENTFERNT');
      ui.renderTasksTab();
    },

    // ------------------------------
    // Catalog + Labor modal actions
    // ------------------------------
    openCatalog(type, mainIdx = null) {
      state.pickerContext = { type, mainIdx };

      const modal = $('#modal-container');
      const titleEl = $('#modal-title');
      const searchBox = $('#modal-search-box');
      const searchInput = $('#modal-search-input');

      titleEl.innerHTML = type === 'main'
        ? `<i class="fas fa-cubes" style="color:var(--primary)"></i> <span>Haupt-Artikel wählen</span>`
        : `<i class="fas fa-link" style="color:var(--primary)"></i> <span>Unter-Artikel verknüpfen</span>`;

      searchBox.classList.remove('hidden');
      searchInput.value = '';
      searchInput.oninput = (e) => api.searchCatalog(e.target.value);

      modal.classList.remove('hidden');
      api.searchCatalog('');
    },

    renderCatalogList() {
      const list = $('#modal-content');
      if (!list) return;

      list.innerHTML = (state.catalog || []).map(p => `
        <div class="catalog-item">
          <div style="display:flex; justify-content:space-between; margin-bottom:0.75rem;">
            <h4 style="font-weight:900; color:var(--text-main);">${escapeHtml(p.name)}</h4>
            <span style="font-size:0.625rem; font-weight:900; color:#cbd5e1; text-transform:uppercase;">${escapeHtml(p.category || 'Artikel')}</span>
          </div>
          <div class="supplier-grid">
            ${(p.suppliers || []).map(s => `
              <button
                onclick="ui.addFromCatalog(${p.id}, '${escapeHtml(String(p.name)).replace(/&#039;/g, "\\'")}', ${s.distributor_price_id}, ${s.distributor_id}, '${escapeHtml(String(s.name)).replace(/&#039;/g, "\\'")}', ${toNum(s.unit_price, 0)})"
                class="supplier-btn">
                <span style="font-size:0.625rem; font-weight:900; color:#94a3b8; text-transform:uppercase;">${escapeHtml(s.name)}</span>
                <span style="font-size:0.875rem; font-weight:900;">${toNum(s.unit_price, 0).toFixed(2)} €</span>
              </button>
            `).join('')}
          </div>
        </div>
      `).join('');

      if (!state.catalog?.length) {
        list.innerHTML = `<div style="text-align:center; padding:2rem; color:#cbd5e1; font-weight:700;">Keine Artikel gefunden...</div>`;
      }
    },

    addFromCatalog(pId, pName, dpId, dId, dName, price) {
      const item = {
        product_id: pId,
        product_name: pName,
        distributor_price_id: dpId,
        distributor_id: dId,
        distributor_name: dName,
        unit_price: toNum(price, 0),
        qty: 1,
        subComponents: [],
      };

      if (state.pickerContext?.type === 'main') {
        state.editingSet.components.push(item);
      } else if (state.pickerContext?.type === 'sub') {
        state.editingSet.components[state.pickerContext.mainIdx].subComponents.push(item);
      }

      this.closeModal();
      this.recalculateLocalStats();
      this.render();
    },

    openLaborPicker() {
      state.pickerContext = { type: 'labor' };

      const modal = $('#modal-container');
      const titleEl = $('#modal-title');
      const searchBox = $('#modal-search-box');
      const contentEl = $('#modal-content');

      titleEl.innerHTML = `<i class="fas fa-users" style="color:var(--accent)"></i> <span>Personal nach Positionen</span>`;
      searchBox.classList.add('hidden');

      contentEl.innerHTML = (state.laborOptions || []).map(pos => `
        <div class="labor-group">
          <div class="labor-header">
            <h4 style="font-weight:900; font-size:1rem; color:var(--text-main);">${escapeHtml(pos.position_name)}</h4>
            <span style="font-size:0.625rem; font-weight:900; background:#f1f5f9; color:#94a3b8; padding:2px 8px; border-radius:4px; text-transform:uppercase;">
              ${(pos.employees || []).length} Mitarbeiter
            </span>
          </div>

          <div class="labor-avatars">
            ${(pos.employees || []).map(emp => {
              const payload = {
                ...emp,
                position_name: pos.position_name,
              };
              const json = escapeHtml(JSON.stringify(payload));
              return `
                <div class="tooltip-wrap">
                  <button class="labor-btn-avatar" onclick="ui.addLaborFromJson('${json.replace(/'/g, '&#039;')}')">
                    <img src="${escapeHtml(emp.avatar || ('https://i.pravatar.cc/150?u=' + emp.employee_id))}">
                  </button>
                  <div class="tooltip-text">${escapeHtml(emp.employee_name)}</div>
                </div>
              `;
            }).join('')}
          </div>
        </div>
      `).join('');

      modal.classList.remove('hidden');
    },

    addLaborFromJson(json) {
      let l;
      try { l = JSON.parse(json); } catch (e) { return; }

      state.editingSet.labor.push({
        department_position_id: l.department_position_id,
        employee_id: l.employee_id,
        employee_name: l.employee_name,
        position_name: l.position_name,
        department_name: l.department_name,
        avatar: l.avatar,
        rate: toNum(l.hourly_rate, 0),
        hours: 8,
      });

      this.closeModal();
      this.recalculateLocalStats();
      this.render();
    },

    // ------------------------------
    // Updates: qty/price/labor
    // ------------------------------
    updateQty(mIdx, sIdx, val) {
      const n = Math.max(0, toNum(val, 0));
      if (sIdx === null) state.editingSet.components[mIdx].qty = n;
      else state.editingSet.components[mIdx].subComponents[sIdx].qty = n;
      this.recalculateLocalStats();
      this.render();
    },

    updatePrice(mIdx, sIdx, val) {
      const n = Math.max(0, toNum(val, 0));
      if (sIdx === null) state.editingSet.components[mIdx].unit_price = n;
      else state.editingSet.components[mIdx].subComponents[sIdx].unit_price = n;
      this.recalculateLocalStats();
      this.render();
    },

    async refreshPrice(mIdx, sIdx) {
      const comp = (sIdx === null)
        ? state.editingSet.components[mIdx]
        : state.editingSet.components[mIdx].subComponents[sIdx];

      if (!comp?.distributor_price_id) return;

      ui.showLoading(true);
      const groupId = getGroupId() || '';
      const res = await api.request(`/catalog?q=${encodeURIComponent(comp.product_name)}&article_group_id=${groupId}`);
      ui.showLoading(false);

      if (!res?.data) return;

      const product = res.data.find(p => p.id === comp.product_id);
      if (!product) return alert('Artikel wurde im Katalog nicht gefunden.');

      const supplier = (product.suppliers || []).find(s => s.distributor_price_id === comp.distributor_price_id);
      if (!supplier) return alert('Dieser Lieferanten-Preis wurde im Katalog nicht mehr gefunden.');

      const price = toNum(supplier.unit_price, 0);
      if (sIdx === null) state.editingSet.components[mIdx].unit_price = price;
      else state.editingSet.components[mIdx].subComponents[sIdx].unit_price = price;

      ui.recalculateLocalStats();
      ui.render();
      ui.showStatus('PREIS AKTUALISIERT');
    },

    removeComp(mIdx, sIdx = null) {
      if (sIdx === null) state.editingSet.components.splice(mIdx, 1);
      else state.editingSet.components[mIdx].subComponents.splice(sIdx, 1);
      this.recalculateLocalStats();
      this.render();
    },

    updateLaborHours(idx, val) {
      state.editingSet.labor[idx].hours = Math.max(0, toNum(val, 0));
      this.recalculateLocalStats();
      this.render();
    },

    removeLabor(idx) {
      state.editingSet.labor.splice(idx, 1);
      this.recalculateLocalStats();
      this.render();
    },

    // ------------------------------
    // Stats
    // ------------------------------
    recalculateLocalStats() {
      if (!state.editingSet) return;

      let main = 0, sub = 0, labor = 0;

      (state.editingSet.components || []).forEach(c => {
        main += (toNum(c.unit_price) * toNum(c.qty));
        (c.subComponents || []).forEach(s => {
          sub += (toNum(s.unit_price) * toNum(s.qty));
        });
      });

      (state.editingSet.labor || []).forEach(l => {
        labor += (toNum(l.rate) * toNum(l.hours));
      });

      const total = main + sub + labor;

      (state.editingSet.components || []).forEach(c => {
        c.percentOfTotal = total > 0 ? ((toNum(c.unit_price) * toNum(c.qty)) / total * 100).toFixed(1) : '0.0';
        (c.subComponents || []).forEach(s => {
          s.percentOfTotal = total > 0 ? ((toNum(s.unit_price) * toNum(s.qty)) / total * 100).toFixed(1) : '0.0';
        });
      });

      (state.editingSet.labor || []).forEach(l => {
        l.percentOfTotal = total > 0 ? ((toNum(l.rate) * toNum(l.hours)) / total * 100).toFixed(1) : '0.0';
      });

      state.editingSet.stats = {
        mainTotal: main,
        subTotal: sub,
        laborTotal: labor,
        total,

        mainPct: total > 0 ? (main / total * 100).toFixed(0) : 0,
        subPct: total > 0 ? (sub / total * 100).toFixed(0) : 0,
        laborPct: total > 0 ? (labor / total * 100).toFixed(0) : 0,
      };
    },

    // ------------------------------
    // Report + PDF
    // ------------------------------
   openReport() {
        const s = state.editingSet;
        if (!s) return;

        const container = document.querySelector('#pdf-report-content');
        const today = new Date().toLocaleDateString('de-DE');

        // ---- Tasks helpers (NEW) ----
        const tasks = Array.isArray(s.tasks) ? s.tasks.slice() : [];

        // sort: stage_id then sort_order
        tasks.sort((a, b) => {
            const sa = String(a.stage_id ?? '');
            const sb = String(b.stage_id ?? '');
            if (sa !== sb) return sa.localeCompare(sb);
            return (toNum(a.sort_order, 0) - toNum(b.sort_order, 0));
        });

        const stageLabel = (stageId, fallbackName) => {
            if (!stageId) return 'Ohne Stage';
            const st = findStageById(stageId);
            return st?.name || st?.title || fallbackName || ('Stage #' + stageId);
        };

        const totalTaskHours = tasks.reduce((sum, t) => sum + toNum(t.hours, 0), 0);

        // group tasks by stage label
        const taskGroups = new Map();
        for (const t of tasks) {
            const label = stageLabel(t.stage_id, t.stage_name);
            if (!taskGroups.has(label)) taskGroups.set(label, []);
            taskGroups.get(label).push(t);
        }

        // ---- Build PDF HTML ----
        let html = `
            <div class="pdf-header">
            <div class="pdf-brand">Nuri <span style="color:var(--primary)">Head</span></div>
            <div class="pdf-meta">
                <h2>Master Set Report</h2>
                <p>${today}</p>
            </div>
            </div>

            <div style="margin-bottom:2rem;">
            <h1 style="font-size:2.5rem; font-weight:900; line-height:1.2; margin-bottom:0.5rem;">
                ${escapeHtml(s.name || 'Unbenanntes Set')}
            </h1>
            <p style="color:#64748b; font-size:1rem;">
                ${escapeHtml(s.description || 'Keine Beschreibung')}
            </p>
            </div>

            <div class="pdf-summary-grid">
            <div><canvas id="pdfChart"></canvas></div>
            <div class="pdf-stats-card">
                <div class="pdf-stat-row">
                <span class="pdf-stat-label">Hauptartikel</span>
                <span class="pdf-stat-val" style="color:var(--primary)">
                    ${s.stats.mainTotal.toLocaleString('de-DE', {style:'currency', currency:'EUR'})}
                </span>
                </div>
                <div class="pdf-stat-row">
                <span class="pdf-stat-label">Zubehör / Sub</span>
                <span class="pdf-stat-val" style="color:#94a3b8">
                    ${s.stats.subTotal.toLocaleString('de-DE', {style:'currency', currency:'EUR'})}
                </span>
                </div>
                <div class="pdf-stat-row">
                <span class="pdf-stat-label">Personal</span>
                <span class="pdf-stat-val" style="color:var(--accent)">
                    ${s.stats.laborTotal.toLocaleString('de-DE', {style:'currency', currency:'EUR'})}
                </span>
                </div>

                <!-- NEW: Tasks quick info -->
                <div class="pdf-stat-row">
                <span class="pdf-stat-label">Aufgaben (Anzahl)</span>
                <span class="pdf-stat-val">${tasks.length}</span>
                </div>
                <div class="pdf-stat-row">
                <span class="pdf-stat-label">Aufgaben (Stunden)</span>
                <span class="pdf-stat-val">${totalTaskHours.toFixed(2)} h</span>
                </div>

                <div style="margin-top:1rem; padding-top:1rem; border-top:2px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                <span class="pdf-stat-label" style="font-size:10px; text-transform:uppercase;">Gesamt</span>
                <span class="pdf-stat-val" style="font-size:1.25rem;">
                    ${s.stats.total.toLocaleString('de-DE', {style:'currency', currency:'EUR'})}
                </span>
                </div>
            </div>
            </div>

            <div class="pdf-section-title">Komponenten Details</div>
            <table class="pdf-table">
            <thead>
                <tr>
                <th>Artikel / Position</th>
                <th style="text-align:center">Menge</th>
                <th style="text-align:right">Einzel</th>
                <th style="text-align:right">Gesamt</th>
                </tr>
            </thead>
            <tbody>
        `;

        (s.components || []).forEach(c => {
            html += `
            <tr>
                <td style="font-weight:900;">${escapeHtml(c.product_name)}</td>
                <td style="text-align:center">${escapeHtml(c.qty)}</td>
                <td style="text-align:right">${toNum(c.unit_price).toFixed(2)}€</td>
                <td style="text-align:right">${(toNum(c.unit_price) * toNum(c.qty)).toFixed(2)}€</td>
            </tr>
            `;
            (c.subComponents || []).forEach(sub => {
            html += `
                <tr>
                <td style="padding-left:1.5rem; color:#64748b;">↳ ${escapeHtml(sub.product_name)}</td>
                <td style="text-align:center; color:#64748b;">${escapeHtml(sub.qty)}</td>
                <td style="text-align:right; color:#64748b;">${toNum(sub.unit_price).toFixed(2)}€</td>
                <td style="text-align:right; color:#64748b;">${(toNum(sub.unit_price) * toNum(sub.qty)).toFixed(2)}€</td>
                </tr>
            `;
            });
        });

        html += `
            </tbody>
            </table>

            <div class="pdf-section-title">Personal Details</div>
            <table class="pdf-table">
            <thead>
                <tr>
                <th>Mitarbeiter / Position</th>
                <th style="text-align:center">Stunden</th>
                <th style="text-align:right">Rate</th>
                <th style="text-align:right">Kosten</th>
                </tr>
            </thead>
            <tbody>
        `;

        (s.labor || []).forEach(l => {
            html += `
            <tr>
                <td>
                <div style="font-weight:900;">${escapeHtml(l.position_name)}</div>
                <div style="font-size:0.7rem; color:#64748b;">
                    ${escapeHtml(l.employee_name)} • ${escapeHtml(l.department_name)}
                </div>
                </td>
                <td style="text-align:center">${escapeHtml(l.hours)}</td>
                <td style="text-align:right">${toNum(l.rate).toFixed(2)}€</td>
                <td style="text-align:right">${(toNum(l.rate) * toNum(l.hours)).toFixed(2)}€</td>
            </tr>
            `;
        });

        html += `
            </tbody>
            </table>

            <!-- NEW: Tasks section -->
            <div class="pdf-section-title">Aufgaben Details</div>
        `;

        if (!tasks.length) {
            html += `
            <div style="padding:1rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; color:#64748b; font-weight:700;">
                Keine Aufgaben zugewiesen
            </div>
            `;
        } else {
            // Render group by stage as separate tables
            for (const [label, group] of taskGroups.entries()) {
            const groupHours = group.reduce((sum, t) => sum + toNum(t.hours, 0), 0);

            html += `
                <div style="margin-top:1rem; font-weight:900; color:#0f172a; display:flex; justify-content:space-between; align-items:center;">
                <span>${escapeHtml(label)}</span>
                <span style="font-size:0.8rem; color:#64748b;">${group.length} Tasks • ${groupHours.toFixed(2)} h</span>
                </div>

                <table class="pdf-table" style="margin-top:0.5rem;">
                <thead>
                    <tr>
                    <th>Aufgabe</th>
                    <th>Phase</th>
                    <th style="text-align:center">Stunden</th>
                    <th>Notiz</th>
                    </tr>
                </thead>
                <tbody>
            `;

            group.forEach(t => {
                html += `
                <tr>
                    <td style="font-weight:900;">${escapeHtml(t.title || 'Untitled')}</td>
                    <td>${escapeHtml(t.phase_name || '—')}</td>
                    <td style="text-align:center">${toNum(t.hours).toFixed(2)} h</td>
                    <td style="color:#64748b;">
                    ${escapeHtml((t.notes || t.description || '')).slice(0, 120) || '—'}
                    </td>
                </tr>
                `;
            });

            html += `
                </tbody>
                </table>
            `;
            }
        }

        html += `
            <div class="pdf-total-box">
            <div class="pdf-total-label">Gesamtwert Master Set</div>
            <div class="pdf-total-val">
                ${s.stats.total.toLocaleString('de-DE', {style:'currency', currency:'EUR'})}
            </div>
            <div style="margin-top:0.5rem; font-weight:900; color:#64748b;">
                Aufgaben gesamt: ${tasks.length} • ${totalTaskHours.toFixed(2)} h
            </div>
            </div>
        `;

        container.innerHTML = html;
        document.querySelector('#pdf-modal-container')?.classList.remove('hidden');

        // Chart stays the same (cost split)
        setTimeout(() => {
            const el = document.querySelector('#pdfChart');
            if (!el) return;
            const ctx = el.getContext('2d');
            new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Main', 'Sub', 'Labor'],
                datasets: [{
                data: [s.stats.mainTotal, s.stats.subTotal, s.stats.laborTotal],
                backgroundColor: ['#74b2d4', '#94a3b8', '#93c21c'],
                borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                cutout: '70%'
            }
            });
        }, 100);
        },


    downloadPDF() {
      const element = $('#pdf-report-content');
      if (!element) return;
      const opt = {
        margin: 0,
        filename: `MasterSet_${state.editingSet?.name || 'export'}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
      };
      html2pdf().set(opt).from(element).save();
    },

    // ------------------------------
    // Global UI controls
    // ------------------------------
    closeModal() {
      $('#modal-container')?.classList.add('hidden');
    },

    showLoading(show) {
      $('#loading-spinner')?.classList.toggle('hidden', !show);
    },

    showStatus(text, error = false) {
      const t = $('#status-toast');
      if (!t) return;
      t.innerText = text;
      t.classList.toggle('error', error);
      t.classList.remove('hidden');
      setTimeout(() => t.classList.add('hidden'), 2000);
    },

    deleteConfirm(id) {
      if (confirm('Möchten Sie dieses MasterSet unwiderruflich löschen?')) api.deleteSet(id);
    },
    };

  // ===========================================================================
  // Expose to window for inline onclick handlers
  // ===========================================================================
  window.state = state;
  window.api = api;
  window.app = app;
  window.ui = ui;

  // ===========================================================================
  // Boot
  // ===========================================================================
  window.addEventListener('DOMContentLoaded', () => {
    api.getGroups();
  });
})();
</script>

</body>
</html>
