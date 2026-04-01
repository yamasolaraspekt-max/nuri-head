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
      <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
      <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

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

        /* --- Folder List Styles --- */
        .folder-grid {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .folder-card {
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            overflow: hidden;
            transition: all 0.2s;
        }

        .folder-card:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--primary);
        }

        .folder-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem;
            cursor: pointer;
            background: white;
            border-left: 6px solid transparent; /* Color injects here */
        }

        .folder-header:hover {
            background: #f8fafc;
        }

        .folder-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            flex: 1;
        }

        .folder-title-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .folder-name {
            font-size: 1.125rem;
            font-weight: 900;
            color: var(--text-main);
        }

        .folder-count-badge {
            background: #f1f5f9;
            color: var(--text-light);
            font-size: 0.75rem;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 99px;
        }

        .folder-stats-row {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .f-stat {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            background: #f8fafc;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }

        .f-stat i { font-size: 0.7rem; }
        .f-stat.blue i { color: var(--primary); }
        .f-stat.green i { color: var(--accent); }
        .f-stat.purple i { color: #a855f7; }
        .f-stat.orange i { color: #f59e0b; }

        .folder-actions {
            display: flex;
            gap: 0.5rem;
            opacity: 0.5;
            transition: opacity 0.2s;
        }

        .folder-card:hover .folder-actions {
            opacity: 1;
        }

        /* Accordion Content */
        .folder-content {
            background: #f8fafc;
            border-top: 1px solid var(--border-color);
            padding: 1rem;
            display: none; /* Hidden by default */
        }

        .folder-content.open {
            display: block;
            animation: fadeIn 0.2s ease-out;
        }

        .folder-empty-msg {
            text-align: center;
            padding: 1.5rem;
            font-size: 0.85rem;
            color: #94a3b8;
            font-weight: 700;
        }
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
        /* --- Wizard Styles --- */
        .wizard-layout { display: flex; height: calc(100vh - 150px); gap: 1.5rem; overflow: hidden; }
        .wizard-sidebar { width: 260px; background: white; border: 1px solid var(--border-color); border-radius: var(--radius-xl); display: flex; flex-direction: column; }
        .wizard-main { flex: 1; background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-xl); padding: 1.5rem; overflow-y: auto; display: flex; flex-direction: column; gap: 1rem; }

        .wiz-section-btn { padding: 1rem; text-align: left; background: transparent; border: none; border-bottom: 1px solid #f1f5f9; cursor: pointer; font-weight: 700; color: var(--text-light); transition: all 0.2s; }
        .wiz-section-btn:hover { background: #f0f7ff; color: var(--primary); }
        .wiz-section-btn.active { background: var(--primary); color: white; }

        .phase-card { background: white; border: 1px solid var(--border-color); border-radius: var(--radius-lg); margin-bottom: 1rem; box-shadow: var(--shadow-sm); overflow: hidden; }
        .phase-header { background: #f1f5f9; padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center; cursor: move; }
        .phase-title-input { background: transparent; border: none; font-weight: 900; font-size: 1rem; color: var(--text-main); width: 100%; outline: none; }
        .phase-body { padding: 0.5rem; min-height: 50px; background: white; }

        .activity-item { background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 0.75rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.75rem; transition: transform 0.1s; }
        .activity-item:hover { border-color: var(--primary); transform: translateX(2px); }
        .act-handle { color: #cbd5e1; cursor: grab; }
        .act-input { border: none; background: transparent; font-size: 0.875rem; font-weight: 600; color: var(--text-main); width: 100%; outline: none; }
        .act-meta { font-size: 0.75rem; color: #94a3b8; display: flex; gap: 0.5rem; align-items: center; }

        .ghost { opacity: 0.5; background: #f0f9ff; border: 2px dashed var(--primary); }
        /* Update existing .card-group */
          .card-group {
              /* ... keep existing styles ... */
              position: relative; /* Essential for positioning the button */
              overflow: hidden;   /* Keeps the button from spilling out if rounded */
          }

          /* Add new style for the settings button */
          .card-settings-btn {
              position: absolute;
              top: 1rem;
              right: 1rem;
              width: 32px;
              height: 32px;
              background: rgba(255, 255, 255, 0.8);
              border: 1px solid var(--border-color);
              border-radius: var(--radius-md);
              color: var(--text-light);
              display: flex;
              align-items: center;
              justify-content: center;
              transition: all 0.2s;
              z-index: 10; /* Ensure it sits above other content */
          }

          .card-settings-btn:hover {
              background: var(--primary);
              color: white;
              border-color: var(--primary);
              box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
          }

          .stage-badge{
            font-size: 10px;
            font-weight: 900;
            padding: 4px 8px;
            border-radius: 999px;
            background: rgba(116,178,212,.12);
            border: 1px solid rgba(116,178,212,.25);
            color: var(--primary-dark);
            white-space: nowrap;
          }

          /* --- Phase Collapse --- */
          .phase-actions { display:flex; gap:6px; align-items:center; }
          .phase-collapse-btn { cursor:pointer; }

          /* Hide activities + footer when collapsed */
          .phase-card.is-collapsed .phase-body { display:none !important; }
          .phase-card.is-collapsed .phase-footer { display:none !important; }

          /* Optional: make header look tighter when collapsed */
          .phase-card.is-collapsed .phase-header { border-bottom: 0; }
          .pill-orange{ background: rgba(245, 158, 11, .12); border: 1px solid rgba(245, 158, 11, .25); }
          .bg-orange{ background: #f59e0b; }
          /* --- Folder List Styles --- */
          .folder-grid { display: flex; flex-direction: column; gap: 1rem; }

          .folder-card {
              background: white;
              border-radius: var(--radius-lg);
              border: 1px solid var(--border-color);
              overflow: hidden;
              transition: all 0.2s;
          }
          .folder-card:hover { box-shadow: var(--shadow-md); border-color: var(--primary); }

          .folder-header {
              display: flex; justify-content: space-between; align-items: center;
              padding: 1.25rem; cursor: pointer; background: white;
              border-left: 6px solid transparent; /* Color injects here */
          }
          .folder-header:hover { background: #f8fafc; }

          .folder-info { display: flex; flex-direction: column; gap: 0.5rem; flex: 1; }

          .folder-title-row { display: flex; align-items: center; gap: 0.75rem; }
          .folder-name { font-size: 1.125rem; font-weight: 900; color: var(--text-main); }
          .folder-count-badge {
              background: #f1f5f9; color: var(--text-light);
              font-size: 0.75rem; font-weight: 800; padding: 2px 8px; border-radius: 99px;
          }

          .folder-stats-row { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }

          .f-stat {
              display: flex; align-items: center; gap: 4px;
              font-size: 0.75rem; font-weight: 700; color: #64748b;
              background: #f8fafc; padding: 2px 6px; border-radius: 4px; border: 1px solid #e2e8f0;
          }
          .f-stat i { font-size: 0.7rem; }
          .f-stat.blue i { color: var(--primary); }
          .f-stat.green i { color: var(--accent); }
          .f-stat.purple i { color: #a855f7; }
          .f-stat.orange i { color: #f59e0b; }

          .folder-actions { display: flex; gap: 0.5rem; opacity: 0.5; transition: opacity 0.2s; }
          .folder-card:hover .folder-actions { opacity: 1; }

          /* Accordion Content */
          .folder-content {
              background: #f8fafc; border-top: 1px solid var(--border-color);
              padding: 1rem; display: none; /* Hidden by default */
          }
          .folder-content.open { display: block; animation: fadeIn 0.2s ease-out; }

          .folder-empty-msg {
              text-align: center; padding: 1.5rem; font-size: 0.85rem; color: #94a3b8; font-weight: 700;
          }

        @media (max-width: 1024px) {
            .editor-grid { grid-template-columns: 1fr; }
            .grid-cards { grid-template-columns: 1fr; }
            .summary-card { position: static; margin-bottom: 2rem; }
        }
    </style>


    <style>
      .twiz-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;display:flex;align-items:center;justify-content:center;padding:16px}
      .twiz-modal{background:#fff;border-radius:14px;max-width:980px;width:100%;max-height:92vh;overflow:hidden;box-shadow:0 20px 50px rgba(0,0,0,.25)}
      .twiz-header{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid #eee}
      .twiz-title{font-weight:700}
      .twiz-close{border:0;background:transparent;font-size:18px}
      .twiz-steps{display:flex;gap:8px;padding:10px 16px;border-bottom:1px solid #f0f0f0;flex-wrap:wrap}
      .twiz-step{font-size:12px;padding:6px 10px;border-radius:999px;background:#f3f4f6}
      .twiz-step.active{background:#2563eb;color:#fff}
      .twiz-body{padding:16px;overflow:auto;max-height:68vh}
      .twiz-footer{display:flex;justify-content:space-between;padding:12px 16px;border-top:1px solid #eee}
      .twiz-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:10px}
      .twiz-chip{display:flex;gap:10px;align-items:center;border:1px solid #e5e7eb;border-radius:12px;padding:10px 12px;background:#fff}
      .twiz-chip input{transform:scale(1.1)}
      .twiz-hint{font-size:12px;color:#6b7280;margin:6px 0 10px}
      .twiz-review{border:1px solid #eee;border-radius:12px;padding:12px;background:#fafafa}
      .twiz-act{border:1px solid #eee;border-radius:12px;padding:12px;margin-bottom:10px}
      .twiz-act .row{display:flex;gap:10px}
      .twiz-act .row > div{flex:1}
    </style>

    <style>
    /* ===== Missing Bootstrap-like utilities (minimal) ===== */
    .d-none{display:none !important;}
    .d-flex{display:flex !important;}
    .justify-content-between{justify-content:space-between !important;}
    .align-items-center{align-items:center !important;}
    .mb-2{margin-bottom:.5rem !important;}
    .mt-2{margin-top:.5rem !important;}
    .text-muted{color:#94a3b8 !important; font-size:12px;}

    .form-group label, .form-row label{display:block; font-weight:800; color:#64748b; font-size:.85rem; margin-bottom:.35rem;}
    .form-row{display:flex; gap:.75rem;}
    .form-row .col{flex:1;}

    .form-control{
      width:100%;
      padding:.7rem .9rem;
      border:1px solid var(--border-color);
      border-radius:12px;
      outline:none;
      font-weight:800;
      color:var(--text-main);
      background:#fff;
    }
    .form-control:focus{border-color:var(--primary); box-shadow:0 0 0 3px rgba(116,178,212,.15);}

    .btn-sm{padding:.45rem .7rem; border-radius:10px; font-weight:900; font-size:.75rem;}
    .btn-outline-primary{
      background:transparent; border:1px solid var(--primary);
      color:var(--primary); cursor:pointer;
    }
    .btn-outline-primary:hover{background:var(--primary); color:#fff;}
    .btn-outline-secondary{
      background:transparent; border:1px solid #cbd5e1;
      color:#475569; cursor:pointer;
    }
    .btn-outline-secondary:hover{background:#f1f5f9;}
    .dashboard-header{
        display:flex;
        justify-content:space-between;
        align-items:flex-end;
        gap:1.25rem;
        flex-wrap:wrap;
      }

      .dash-left{ min-width:260px; }
      .brand-title{ font-size:2rem; margin:0 0 .25rem 0; }
      .brand-sub{ color:#94a3b8; font-weight:500; margin:0; }

      .dash-right{
        display:flex;
        align-items:center;
        gap:.75rem;
        flex-wrap:wrap;
      }

      .search-wrapper{ min-width:260px; }

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

    <div id="prompt-modal" class="modal-backdrop hidden" style="z-index: 300;">
        <div class="modal-window fade-in" style="max-width: 400px; height: auto; overflow: visible;">
            <div class="modal-header">
                <h3 id="prompt-title" class="modal-title" style="color:var(--text-main);">Eingabe</h3>
                <button onclick="ui.closePrompt()" class="btn btn-icon"><i class="fas fa-times"></i></button>
            </div>
            <div style="padding: 1.5rem;">
                <label id="prompt-label" style="display:block; margin-bottom:0.5rem; font-weight:700; color:#64748b; font-size:0.875rem;">Name</label>
                <input type="text" id="prompt-input" class="search-input" style="width:100%; border:2px solid var(--primary-light);" autocomplete="off">
                
                <div style="display:flex; justify-content:flex-end; gap:0.5rem; margin-top:1.5rem;">
                    <button onclick="ui.closePrompt()" class="btn btn-secondary">Abbrechen</button>
                    <button id="prompt-confirm-btn" class="btn btn-primary">Speichern</button>
                </div>
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

    <div id="link-modal" class="modal-backdrop hidden" style="z-index: 320;">
      <div class="modal-window fade-in" style="max-width: 720px; height: auto; overflow: visible;">
        <div class="modal-header">
          <h3 class="modal-title" style="display:flex; align-items:center; gap:.75rem;">
            <i class="fas fa-link" style="color:var(--primary)"></i>
            <span>Link hinzufügen</span>
          </h3>
          <button onclick="wizard.closeLinkModal()" class="btn btn-icon"><i class="fas fa-times"></i></button>
        </div>

        <div style="padding: 1.5rem; background: rgba(248, 250, 252, 0.3);">
          <label style="display:block; margin-bottom:.5rem; font-weight:800; color:#64748b; font-size:.85rem;">
            URL
          </label>

          <div style="display:flex; gap:.75rem; align-items:center;">
            <input id="link-input" type="text" class="search-input"
              style="width:100%; border:2px solid var(--primary-light);" placeholder="https://..."
              autocomplete="off">
            <button id="link-save-btn" class="btn btn-primary" style="padding:.85rem 1.25rem;">
              <i class="fas fa-check"></i> Speichern
            </button>
          </div>

          <div id="link-preview" style="margin-top:1rem;"></div>

          <div style="display:flex; justify-content:flex-end; gap:.5rem; margin-top:1.25rem;">
            <button onclick="wizard.closeLinkModal()" class="btn btn-secondary">Abbrechen</button>
          </div>
        </div>
      </div>
    </div>


   <div id="taskWizardModal" class="twiz-backdrop d-none">
      <div class="twiz-modal">
        <div class="twiz-header">
          <div class="twiz-title">Phasen-Assistent</div>
          <button class="twiz-close" type="button" onclick="TaskWizard.close()">✕</button>
        </div>

        <div class="twiz-steps">
          <div class="twiz-step" data-step="1">1) Phase + Aktivitäten</div>
          <div class="twiz-step" data-step="2">2) Stages</div>
          <div class="twiz-step" data-step="3">3) Phasen-Sektionen</div>
          <div class="twiz-step" data-step="4">4) Produkte</div>
          <div class="twiz-step" data-step="5">5) Prüfen & Speichern</div>
        </div>

        <div class="twiz-body">
          {{-- STEP 1 --}}
          <div class="twiz-pane" data-pane="1">
            <div class="form-group mb-2">
              <label>Phasenname *</label>
              <input class="form-control" id="tw_phase_name" placeholder="z.B. Installation" />
            </div>

            <hr/>

            <div class="d-flex justify-content-between align-items-center mb-2">
              <strong>Aktivitäten</strong>
              <button type="button" class="btn btn-sm btn-outline-primary" onclick="TaskWizard.addActivityRow()">+ Aktivität hinzufügen</button>
            </div>

            <div id="tw_activities"></div>
          </div>

          {{-- STEP 2 --}}
          <div class="twiz-pane d-none" data-pane="2">
            <label>Stage(s) auswählen *</label>
            <div class="twiz-hint">Nur eindeutige Stage-Namen werden angezeigt.</div>

            <div id="tw_stages" class="twiz-grid">
              @php
                // ✅ Unique by stage name (case-insensitive)
                $stages = \App\Models\Stage::query()
                  ->orderBy('sort_order')
                  ->get()
                  ->unique(fn($x) => mb_strtolower(trim($x->stage ?? '')))
                  ->values();
              @endphp

              @foreach($stages as $s)
                <label class="twiz-chip">
                  <input type="checkbox" value="{{ $s->id }}" class="tw_stage_cb">
                  <span>{{ $s->stage }}</span>
                </label>
              @endforeach
            </div>
          </div>

          {{-- STEP 3 --}}
          <div class="twiz-pane d-none" data-pane="3">
            <label>Phasen-Sektionen auswählen *</label>
            <div class="twiz-hint">Anzeige ist deutsch – gespeichert werden technische Keys.</div>

            @php
              $defaultSections = ['complete','montage','repair','maintenance','plan','product'];
              $secLabels = [
                'complete'    => 'Abschluss',
                'montage'     => 'Montage',
                'repair'      => 'Reparatur',
                'maintenance' => 'Wartung',
                'plan'        => 'Planung',
                'product'     => 'Produkt',
              ];
            @endphp

            <div class="twiz-grid">
              @foreach($defaultSections as $sec)
                <label class="twiz-chip">
                  <input type="checkbox" value="{{ $sec }}" class="tw_section_cb">
                  <span>{{ $secLabels[$sec] ?? ucfirst($sec) }}</span>
                </label>
              @endforeach
            </div>
          </div>

          {{-- STEP 4 --}}
          <div class="twiz-pane d-none" data-pane="4">
            <label>Produkte (Artikelgruppen) auswählen *</label>
            <div class="twiz-hint">Suche + Auswahl wie bisher.</div>

            <input class="form-control mb-2" id="tw_product_search"
                  placeholder="Produkte suchen..." oninput="TaskWizard.filterProducts(this.value)" />

            <div id="tw_products" class="twiz-grid">
              @php($products = \App\Models\ArticleGroup::query()->orderBy('article_group')->get())
              @foreach($products as $p)
                <label class="twiz-chip twiz-product" data-name="{{ strtolower($p->article_group) }}">
                  <input type="checkbox" value="{{ $p->id }}" class="tw_product_cb">
                  <span>{{ $p->article_group }}</span>
                </label>
              @endforeach
            </div>
          </div>

          {{-- STEP 5 --}}
          <div class="twiz-pane d-none" data-pane="5">
            <div id="tw_review" class="twiz-review"></div>
            <div id="tw_save_result" class="mt-2"></div>
          </div>
        </div>

        <div class="twiz-footer">
          <button type="button" class="btn btn-outline-secondary" onclick="TaskWizard.prev()">Zurück</button>
          <button type="button" class="btn btn-primary" id="tw_next_btn" onclick="TaskWizard.next()">Weiter</button>
        </div>
      </div>
    </div>

    <!-- after #taskWizardModal -->
    <div id="activity-modal" class="modal-backdrop hidden" style="z-index: 340;">
      <div class="modal-window fade-in" style="max-width: 520px; height:auto; overflow:visible;">
        <div class="modal-header">
          <h3 class="modal-title" style="display:flex; align-items:center; gap:.75rem;">
            <i class="fas fa-plus" style="color:var(--primary)"></i>
            <span>Neue Activity</span>
          </h3>
          <button type="button" onclick="wizard.closeActivityModal()" class="btn btn-icon">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div style="padding:1.25rem;">
          <div class="form-group mb-2">
            <label>Titel *</label>
            <input id="am_title" class="form-control" placeholder="z.B. Kabel ziehen">
          </div>

          <div class="form-row mb-2">
            <div class="col">
              <label>Dauer</label>
              <input id="am_duration" class="form-control" placeholder="00:00" value="00:00">
            </div>
          </div>

          <div class="form-group mb-2">
            <label>Notiz</label>
            <input id="am_notes" class="form-control" placeholder="...">
          </div>

          <div style="display:flex; justify-content:flex-end; gap:.5rem; margin-top:1rem;">
            <button type="button" class="btn btn-outline-secondary" onclick="wizard.closeActivityModal()">Abbrechen</button>
            <button type="button" class="btn btn-primary" onclick="wizard.saveActivityModal()">
              <i class="fas fa-check"></i> Speichern
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- then your big <script> -->
<div id="desc-modal" class="modal-backdrop hidden" style="z-index: 360;">
  <div class="modal-window fade-in" style="max-width: 980px; height: 90vh;">
    <div class="modal-header">
      <h3 class="modal-title" style="display:flex; align-items:center; gap:.75rem;">
        <i class="fas fa-pen-nib" style="color:var(--primary)"></i>
        <span id="desc-modal-title">Beschreibung</span>
      </h3>
      <div style="display:flex; gap:.5rem;">
        <button class="btn btn-secondary" onclick="descUI.addVariant()">
          <i class="fas fa-plus"></i> Variante
        </button>
        <button class="btn btn-icon" onclick="descUI.close()"><i class="fas fa-times"></i></button>
      </div>
    </div>

    <div style="padding: 1rem; border-bottom:1px solid var(--border-color); display:flex; gap:1rem; align-items:center;">
      <div style="flex:1;">
        <label style="display:block; font-weight:900; font-size:12px; color:#64748b; margin-bottom:6px;">Kontext</label>
        <select id="desc-context" class="form-control" onchange="descUI.changeContext(this.value)">
          <option value="angebot">Angebot</option>
          <option value="auftrag">Auftrag</option>
          <option value="project">Projekt</option>
          <option value="invoice">Rechnung</option>
          <option value="internal">Intern</option>
        </select>
      </div>

      <div style="flex:2;">
        <div style="font-weight:900; color:#0f172a;" id="desc-product-line">—</div>
        <div style="font-size:12px; font-weight:700; color:#94a3b8;" id="desc-fallback-hint">—</div>
      </div>
    </div>

    <div class="modal-body" style="display:grid; grid-template-columns: 320px 1fr; gap:1rem; height:100%;">
      <!-- left: variants list -->
      <div style="background:#fff; border:1px solid var(--border-color); border-radius:16px; padding:12px; overflow:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
          <div style="font-weight:900; color:#0f172a;">Varianten</div>
          <button class="btn btn-icon" title="Sortieren (Drag)" style="width:36px;height:36px;">
            <i class="fas fa-grip-vertical"></i>
          </button>
        </div>

        <div id="desc-variants"></div>
      </div>

      <!-- right: quill editor -->
      <div style="background:#fff; border:1px solid var(--border-color); border-radius:16px; overflow:hidden; display:flex; flex-direction:column;">
        <div style="padding:10px; border-bottom:1px solid #f1f5f9; display:flex; gap:.75rem; align-items:center;">
          <input id="desc-variant-title" class="form-control" placeholder="Titel (z.B. Standard / Kurz / Technisch)" style="flex:1;">
          <button class="btn btn-primary" onclick="descUI.saveCurrent()">
            <i class="fas fa-save"></i> Speichern
          </button>
          <button class="btn btn-danger" onclick="descUI.deleteCurrent()" title="Löschen">
            <i class="fas fa-trash"></i>
          </button>
        </div>

        <div id="desc-quill" style="flex:1; background:#fff;"></div>

        <div style="padding:10px; border-top:1px solid #f1f5f9; display:flex; gap:.5rem; justify-content:flex-end;">
          <button class="btn btn-secondary" onclick="descUI.applyFallbackToEditor()">
            <i class="fas fa-wand-magic-sparkles"></i> Fallback übernehmen
          </button>
        </div>
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
    groupSets: [],   
    groupSetsForGroupId: null,      
    setsTab: 'all',        
    selectedGroup: null,
    editingSet: null,

    laborOptions: [],
    catalog: [],
    pickerContext: null,

    groupSearch: '',
    editorTab: 'aufgabe',

    // Tasks
    taskOptions: [],     // stages -> phases -> activities (from /tasks/options)
    taskSearch: '',
    selectedStageId: null, // stage filter for options panel

       // Checklists
    checklistOptions: [],   // list of available checklists (from /checklists/options)
    checklistSearch: '',
     
    
  };

  const CHECKLIST_TRIGGERS = [
    { value: 'start',      label: 'Start' },
    { value: 'middle',     label: 'Mitte' },
    { value: 'end',        label: 'Ende' },
    { value: 'review',     label: 'Überprüfung' },
    { value: 'acceptance', label: 'Abnahme' },
    { value: 'qa',         label: 'Qualitätssicherung' },
  ];


  // ===========================================================================
  // Helpers
  // ===========================================================================
  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const PHOTO_REQUIRED_SENTINEL = '__REQUIRED__';

  const isPhotoRequired = (act) => String(act?.photo || '') === PHOTO_REQUIRED_SENTINEL || !!act?.photo_required;
  const hasPhoto = (act) => (!!act?.photo && String(act.photo) !== PHOTO_REQUIRED_SENTINEL) || !!act?.has_photo;


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
    s.checklists  = Array.isArray(s.checklists)  ? s.checklists  : [];
    s.stats = s.stats || {
      mainTotal: 0, subTotal: 0, laborTotal: 0, total: 0,
      mainPct: 0, subPct: 0, laborPct: 0,
    };
    // Normalize nested arrays
   s.components.forEach(c => {
      c.subComponents = Array.isArray(c.subComponents) ? c.subComponents : [];
      c.qty = toNum(c.qty, 0);
      c.unit_price = toNum(c.unit_price, 0);

      // NEW: keep original distributor price
      c.base_unit_price = toNum(
        c.base_unit_price ?? c.distributor_unit_price ?? c.original_unit_price ?? c.unit_price,
        c.unit_price
      );
      c.is_price_overridden = !!c.is_price_overridden;

      c.subComponents.forEach(sub => {
        sub.qty = toNum(sub.qty, 0);
        sub.unit_price = toNum(sub.unit_price, 0);

        // NEW: keep original distributor price
        sub.base_unit_price = toNum(
          sub.base_unit_price ?? sub.distributor_unit_price ?? sub.original_unit_price ?? sub.unit_price,
          sub.unit_price
        );
        sub.is_price_overridden = !!sub.is_price_overridden;
      });
    });

    s.checklists.forEach((c, i) => {
      c.sort_order = Number.isFinite(+c.sort_order) ? +c.sort_order : i;
      c.title = c.title ?? c.name ?? '';
      c.items_count = Number.isFinite(+c.items_count) ? +c.items_count : (Array.isArray(c.items) ? c.items.length : 0);
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
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Accept': 'application/json',
          },
        };
        if (data) options.body = JSON.stringify(data);

        const res = await fetch(url, options);

        // Try to parse JSON even on errors
        const text = await res.text();
        let json = null;
        try { json = text ? JSON.parse(text) : null; } catch (e) {}

        if (!res.ok) {
          console.error('[API ERROR]', method, url, res.status, json || text);
          const msg =
            json?.message ||
            (json?.errors ? Object.values(json.errors).flat().join(' · ') : '') ||
            `HTTP ${res.status}`;
          ui.showStatus(msg || 'ERROR', true);
          return null;
        }

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


    async deleteGroupSet(id) {
        // DELETE /admin/master-sets/group-sets/{id}
        return await this.request(`/group-sets/${id}`, 'DELETE');
      },
    async getGroupSets(groupId) {
      if (!groupId) return;

      const res = await this.request(`/group-sets?article_group_id=${groupId}`);
      if (!res) return;

      const payload = res.data ?? res;

      state.groupSets =
        Array.isArray(payload) ? payload :
        Array.isArray(payload?.data) ? payload.data :
        Array.isArray(payload?.group_sets) ? payload.group_sets :
        Array.isArray(payload?.groupSets) ? payload.groupSets :
        [];

      // ✅ remember which group this list belongs to
      state.groupSetsForGroupId = String(groupId);

      ui.render();
    },



    // ✅ ADD under api.getGroups(...)
      async createGroup(payload) {
        // POST /admin/master-sets/groups
        return await this.request(`/groups`, 'POST', payload);
      },

      async updateGroup(id, payload) {
        // PUT /admin/master-sets/groups/{id}
        return await this.request(`/groups/${id}`, 'PUT', payload);
      },

      async deleteGroup(id) {
        // DELETE /admin/master-sets/groups/{id}
        return await this.request(`/groups/${id}`, 'DELETE');
      },

     async getSets(groupId) {
        const res = await this.request(`/data?article_group_id=${groupId}`);
        if (!res) return;

        const payload = res.data ?? null;

        // ✅ sets
        const setsArr =
          Array.isArray(payload) ? payload :
          Array.isArray(payload?.data) ? payload.data :
          Array.isArray(payload?.sets) ? payload.sets :
          Array.isArray(res?.sets) ? res.sets :
          [];

        state.sets = setsArr;

        // ✅ IMPORTANT:
        // Do NOT clear/overwrite state.groupSets here unless the backend actually returns them.
        const gsArr =
          Array.isArray(res?.group_sets) ? res.group_sets :
          Array.isArray(res?.groupSets) ? res.groupSets :
          Array.isArray(res?.meta?.group_sets) ? res.meta.group_sets :
          Array.isArray(res?.meta?.groupSets) ? res.meta.groupSets :
          Array.isArray(payload?.group_sets) ? payload.group_sets :
          Array.isArray(payload?.groupSets) ? payload.groupSets :
          Array.isArray(payload?.group_sets_data) ? payload.group_sets_data :
          null; // <- key change

        if (Array.isArray(gsArr)) {
          state.groupSets = gsArr;
          state.groupSetsForGroupId = String(groupId); // ✅ keep in sync
        }

        ui.render();
      },



    async loadChecklistItems(checklistId) {
      const res = await this.request(`/checklists/${checklistId}/items`, 'GET');
      return res?.data || [];
    },




    async loadChecklistOptions(q = '') {
      const groupId = getGroupId();
      if (!groupId) return;

      const res = await this.request(`/checklists/options?article_group_id=${groupId}&q=${encodeURIComponent(q)}`);
      if (!res) return;

      state.checklistOptions = res.data || [];

      if (state.view === 'editor' && state.editorTab === 'checklists') {
        ui.renderChecklistsTab();
      }
    },

    async validateChecklistAttach(payload) {
      // optional endpoint you added
      return await this.request(`/checklists/validate`, 'POST', payload);
    },

    
    async requestTaskWizard(endpoint, method = 'GET', data = null) {
      ui.showLoading(true);
      try {
        const url = `${window.location.origin}/admin/task-wizard${endpoint}`;
        const options = {
          method,
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
          },
        };
        if (data) options.body = JSON.stringify(data);

        const res = await fetch(url, options);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return await res.json();
      } catch (e) {
        console.error('TaskWizard API Error:', e);
        ui.showStatus('ERROR', true);
        return null;
      } finally {
        ui.showLoading(false);
      }
    },


    async getDistributorPrice(distributorPriceId) {
      return await this.request(`/distributor-price/${distributorPriceId}`);
    },


    async getGroups(q = '') {
      const res = await this.request(`/groups?q=${encodeURIComponent(q)}`);
      if (!res) return;
      state.groups = res.data || [];
      if (state.view === 'dashboard' && $('#groups-grid')) ui.updateGroupGrid();
      else ui.render();
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
            qualification_id: l.qualification_id || null, // Send the qualification ID
            // Keep these null if you are fully switching to qualifications
            department_position_id: null, 
            hours: l.hours,
            hourly_rate: l.rate // Ensure your backend accepts this snapshot if you want to save the specific price used
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

        checklists: (s.checklists || [])
          .map((c, i) => ({
            id: c.id ?? null, // optional (you validate it)
            maintenance_checklist_id: c.maintenance_checklist_id ?? c.id, // be tolerant

            trigger: (c.trigger || 'start'),
            is_required: (c.is_required !== undefined) ? !!c.is_required : true,
            sort_order: Number.isFinite(+c.sort_order) ? +c.sort_order : i,

            checklist_title_snapshot: c.title ?? null,
            checklist_type_snapshot: c.type ?? null,
          }))
          .sort((a,b)=> (+a.sort_order) - (+b.sort_order)),


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

  // ==============================
// Description Designer (Quill)
// ==============================
const descUI = {
  componentId: null,
  context: 'angebot',
  variants: [],
  current: null,
  fallback: { component_description: '', product_short_description: '' },
  quill: null,
  sortable: null,

  isValidDelta(delta) {
  if (!delta || typeof delta !== 'object') return false;
  if (!Array.isArray(delta.ops)) return false;
  // Every op.insert must be string OR object (image/etc), never null/undefined
  return delta.ops.every(op => op && op.insert !== null && op.insert !== undefined);
},

setEditorFromVariant(v) {
  this.ensureQuill();

  const titleEl = document.getElementById('desc-variant-title');
  if (titleEl) titleEl.value = v?.title || '';

  // 1) Try delta if valid
  if (this.isValidDelta(v?.delta)) {
    try {
      this.quill.setContents(v.delta);
      return;
    } catch (e) {
      console.warn('Quill setContents failed, fallback to HTML:', e);
    }
  }

  // 2) Fallback to HTML
  const html = (v?.html && String(v.html).trim()) ? String(v.html) : '<p><br></p>';
  try {
    this.quill.setContents([]); // clear
    this.quill.clipboard.dangerouslyPasteHTML(html);
  } catch (e) {
    // 3) Last fallback: plain text
    const text = (v?.text && String(v.text).trim()) ? String(v.text) : '';
    this.quill.setText(text);
  }
},


  ensureQuill() {
    if (this.quill) return;

    // Enable rich formatting
    const toolbar = [
      [{ font: [] }, { size: [] }],
      ['bold','italic','underline','strike'],
      [{ color: [] }, { background: [] }],
      [{ script: 'sub' }, { script: 'super' }],
      [{ header: 1 }, { header: 2 }, 'blockquote', 'code-block'],
      [{ list: 'ordered' }, { list: 'bullet' }, { indent: '-1' }, { indent: '+1' }],
      [{ direction: 'rtl' }, { align: [] }],
      ['link','image'],
      ['clean']
    ];

    this.quill = new Quill('#desc-quill', {
      theme: 'snow',
      modules: { toolbar }
    });
  },

  open(componentId, productLabel = '') {
    this.componentId = String(componentId);
    this.context = $('#desc-context')?.value || 'angebot';

    $('#desc-modal-title').textContent = 'Beschreibung Designer';
    $('#desc-product-line').textContent = productLabel || `Component #${this.componentId}`;

    this.ensureQuill();
    this.current = null;
    this.variants = [];
    // normalize variants (remove broken delta like insert:null)
    this.variants = (this.variants || []).map(v => {
      const vv = { ...v };
      if (!this.isValidDelta(vv.delta)) vv.delta = null;
      // guarantee html exists as fallback
      if (!vv.html && vv.delta == null) vv.html = '<p><br></p>';
      return vv;
    });


    document.getElementById('desc-modal')?.classList.remove('hidden');
    this.load();
  },

  close() {
    document.getElementById('desc-modal')?.classList.add('hidden');
    this.componentId = null;
    this.current = null;
    this.variants = [];
    if (this.quill) this.quill.setText('');
  },

 async load() {
  if (!this.componentId) return;

  // prevent out-of-order responses overwriting newer state
  const token = (this._loadToken = (this._loadToken || 0) + 1);

  try {
    const url = `/components/${this.componentId}/descriptions?context=${encodeURIComponent(this.context ?? '')}`;
    const res = await api.request(url, 'GET');

    // ignore stale response
    if (token !== this._loadToken) return;

    const data = res?.data;
    if (!data) {
      ui.showStatus('ERROR', true);
      return;
    }

    this.variants = Array.isArray(data.variants) ? data.variants : [];
    this.fallback = data.fallback || { component_description: '', product_short_description: '' };

    const fallbackText = String(
      (this.fallback.component_description || this.fallback.product_short_description || '')
    ).trim();

    const hintEl = document.querySelector('#desc-fallback-hint'); // avoid $ ambiguity
    if (hintEl) {
      hintEl.textContent = fallbackText
        ? `Fallback verfügbar (${fallbackText.length} Zeichen)`
        : 'Kein Fallback gefunden';
    }

    this.renderVariants();

    const first = this.variants[0];
    const firstId = first?.id ?? null;

    if (firstId != null) this.selectVariant(String(firstId));
    else this.clearEditor();
  } catch (err) {
    // ignore stale errors too
    if (token !== this._loadToken) return;

    console.error('Description load failed:', err);
    ui.showStatus('ERROR', true);
    this.variants = [];
    this.fallback = { component_description: '', product_short_description: '' };
    this.clearEditor();
  }
},

  changeContext(ctx) {
    this.context = String(ctx || 'angebot');
    this.current = null;
    this.load();
  },

  clearEditor() {
    $('#desc-variant-title').value = '';
    this.current = null;
    if (this.quill) this.quill.setText('');
  },

  renderVariants() {
    const box = document.getElementById('desc-variants');
    if (!box) return;

    if (!this.variants.length) {
      box.innerHTML = `
        <div style="padding:1.25rem; text-align:center; color:#94a3b8; font-weight:900;">
          Keine Varianten — erstellen Sie eine neue.
        </div>
      `;
    } else {
      box.innerHTML = this.variants
        .slice()
        .sort((a,b)=> (a.sort_order ?? 0) - (b.sort_order ?? 0))
        .map(v => `
          <div
            class="catalog-item desc-variant-row"
            data-id="${v.id}"
            onclick="descUI.selectVariant(${v.id})"
            style="margin:0; cursor:pointer; border-color:${this.current?.id === v.id ? 'var(--primary)' : 'var(--border-color)'}"
          >
            <div style="display:flex; justify-content:space-between; gap:.75rem;">
              <div style="min-width:0;">
                <div style="font-weight:900; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                  ${escapeHtml(v.title || 'Ohne Titel')}
                </div>
                <div style="font-size:11px; font-weight:800; color:#94a3b8; margin-top:4px;">
                  #${v.id} · ${escapeHtml(v.context || this.context)}
                </div>
              </div>
              <div class="handle" style="width:28px;height:28px;"><i class="fas fa-grip-vertical"></i></div>
            </div>
          </div>
        `).join('');
    }

    // Bind sortable reorder
    if (this.sortable) { try { this.sortable.destroy(); } catch(e){} }
    const wrap = document.getElementById('desc-variants');
    this.sortable = new Sortable(wrap, {
      animation: 150,
      handle: '.handle',
      ghostClass: 'ghost',
      onEnd: () => this.persistReorder()
    });
  },

  selectVariant(id) {
    const v = this.variants.find(x => String(x.id) === String(id));
    if (!v) return;

    this.current = v;
    this.setEditorFromVariant(v);   // ✅ safe delta/html/text loading
    this.renderVariants();
  },


  async addVariant() {
    if (!this.componentId) return;

    const payload = {
      context: this.context,
      title: 'Standard',
      delta: null,
      html: null,
      text: null,
    };

    const res = await api.request(`/components/${this.componentId}/descriptions`, 'POST', payload);
    if (!res?.data) return ui.showStatus('ERROR', true);

    this.variants.push(res.data);
    this.renderVariants();
    this.selectVariant(res.data.id);
    ui.showStatus('VARIANTE ERSTELLT');
  },

  applyFallbackToEditor() {
    this.ensureQuill();
    const fb = (this.fallback.component_description || this.fallback.product_short_description || '').trim();
    if (!fb) return ui.showStatus('NO FALLBACK', true);

    // Simple fallback insert (plain text). If you want HTML fallback, you can set innerHTML.
    this.quill.setText(fb);
  },

  async saveCurrent() {
    if (!this.current?.id) return ui.showStatus('NO VARIANT', true);

    const title = ($('#desc-variant-title').value || '').trim();
    const delta = this.quill.getContents();
    const text  = this.quill.getText();
    const html  = this.quill.root.innerHTML;

    const res = await api.request(`/component-descriptions/${this.current.id}`, 'PUT', {
      title,
      delta,
      text,
      html,
    });

    if (!res?.data) return ui.showStatus('ERROR', true);

    // update local
    const idx = this.variants.findIndex(x => String(x.id) === String(this.current.id));
    if (idx !== -1) this.variants[idx] = res.data;
    this.current = res.data;

    this.renderVariants();
    ui.showStatus('GESPEICHERT');
  },

  async deleteCurrent() {
    if (!this.current?.id) return;

    if (!confirm('Variante löschen?')) return;

    const id = this.current.id;
    const res = await api.request(`/component-descriptions/${id}`, 'DELETE');
    if (!res) return ui.showStatus('ERROR', true);

    this.variants = this.variants.filter(x => String(x.id) !== String(id));
    this.current = null;
    this.renderVariants();
    this.clearEditor();
    ui.showStatus('GELÖSCHT');
  },

  async persistReorder() {
    if (!this.componentId) return;

    const rows = Array.from(document.querySelectorAll('#desc-variants .desc-variant-row'));
    const ordered = rows.map(r => parseInt(r.getAttribute('data-id'), 10)).filter(Boolean);

    // keep local sort_order in sync
    ordered.forEach((id, i) => {
      const v = this.variants.find(x => x.id === id);
      if (v) v.sort_order = i;
    });

    await api.request(`/components/${this.componentId}/descriptions/reorder`, 'POST', {
      context: this.context,
      ordered_ids: ordered
    });

    ui.showStatus('SORTIERT');
  }
};

// expose
window.descUI = descUI;


  const TaskWizard = {
  step: 1,
  open() {
    document.getElementById('taskWizardModal')?.classList.remove('d-none');
    this.step = 1;
    this._renderStep();
  },
  close() {
    document.getElementById('taskWizardModal')?.classList.add('d-none');
  },
  next() { this.step = Math.min(5, this.step + 1); this._renderStep(); },
  prev() { this.step = Math.max(1, this.step - 1); this._renderStep(); },

  addActivityRow() {
    const wrap = document.getElementById('tw_activities');
    if (!wrap) return;
    const id = `tw_act_${Date.now()}`;
    wrap.insertAdjacentHTML('beforeend', `
      <div class="twiz-act" data-id="${id}">
        <div class="row">
          <div>
            <label>Titel *</label>
            <input class="form-control tw_act_title" placeholder="z.B. Kabel ziehen">
          </div>
          <div>
            <label>Dauer</label>
            <input class="form-control tw_act_duration" placeholder="00:00" value="00:00">
          </div>
        </div>
        <div class="mt-2">
          <label>Notiz</label>
          <input class="form-control tw_act_notes" placeholder="...">
        </div>
      </div>
    `);
  },

  filterProducts(q) {
    const term = String(q || '').toLowerCase();
    document.querySelectorAll('#tw_products .twiz-product').forEach(el => {
      const name = (el.getAttribute('data-name') || '').toLowerCase();
      el.style.display = name.includes(term) ? '' : 'none';
    });
  },

  _renderStep() {
    // panes
    document.querySelectorAll('#taskWizardModal .twiz-pane').forEach(p => {
      p.classList.toggle('d-none', String(p.dataset.pane) !== String(this.step));
    });
    // step pills
    document.querySelectorAll('#taskWizardModal .twiz-step').forEach(s => {
      s.classList.toggle('active', String(s.dataset.step) === String(this.step));
    });

    const nextBtn = document.getElementById('tw_next_btn');
    if (nextBtn) nextBtn.textContent = (this.step === 5) ? 'Speichern' : 'Weiter';

    if (this.step === 5) this._buildReview();
  },

  _buildReview() {
    const phaseName = document.getElementById('tw_phase_name')?.value?.trim() || '';
    const acts = Array.from(document.querySelectorAll('#tw_activities .twiz-act')).map(row => ({
      title: row.querySelector('.tw_act_title')?.value?.trim() || '',
      duration: row.querySelector('.tw_act_duration')?.value?.trim() || '',
      notes: row.querySelector('.tw_act_notes')?.value?.trim() || '',
    })).filter(x => x.title);

    const stages = Array.from(document.querySelectorAll('.tw_stage_cb:checked')).map(x => x.value);
    const sections = Array.from(document.querySelectorAll('.tw_section_cb:checked')).map(x => x.value);
    const products = Array.from(document.querySelectorAll('.tw_product_cb:checked')).map(x => x.value);

    const box = document.getElementById('tw_review');
    if (!box) return;

    box.innerHTML = `
      <div style="font-weight:900; margin-bottom:6px;">Review</div>
      <div class="text-muted">Phase: <strong>${escapeHtml(phaseName)}</strong></div>
      <div class="text-muted">Activities: <strong>${acts.length}</strong></div>
      <div class="text-muted">Stages: <strong>${stages.length}</strong></div>
      <div class="text-muted">Sections: <strong>${sections.length}</strong></div>
      <div class="text-muted">Produkte: <strong>${products.length}</strong></div>
    `;
  },
};

// expose
window.TaskWizard = TaskWizard;


  // ===========================================================================
  // App Controller
  // ===========================================================================
    const app = {
      // ---------------------------------------------
      // Navigation
      // ---------------------------------------------
      async navigate(view) {
        state.view = view;

        try {
          if (view === 'dashboard') {
            state.groupSearch = '';
            await api.getGroups('');
          }

          if (view === 'groupList' && state.selectedGroup?.id) {
            await Promise.all([
              api.getSets(state.selectedGroup.id),
              api.getGroupSets(state.selectedGroup.id),
            ]);
          }
        } finally {
          ui.render();
        }
      },

       


      // ---------------------------------------------
      // Tabs
      // ---------------------------------------------
         
      async setSetsTab(tab) {
          state.setsTab = (tab === 'group') ? 'group' : 'all';

          // 1. Trigger render immediately to show tab switch UI
          ui.render();

          // 2. Lazy load data if needed
          if (state.setsTab === 'group' && state.selectedGroup?.id) {
              const gid = String(state.selectedGroup.id);
              const loadedFor = String(state.groupSetsForGroupId || '');

              if (!loadedFor || loadedFor !== gid || !(state.groupSets?.length > 0)) {
                  await api.getGroupSets(state.selectedGroup.id);
                  // api.getGroupSets calls ui.render() on success, so the view will update with data
              }
          }
      },

      setEditorTab(tab) {
        state.editorTab = tab;
        ui.render();
      },

      // ---------------------------------------------
      // Groups
      // ---------------------------------------------
      async selectGroup(group) {
        state.selectedGroup = group;
        await this.navigate('groupList');
      },

      async searchGroups(q) {
        state.groupSearch = q ?? '';
        await api.getGroups(state.groupSearch);
        ui.render();
      },

      // ---------------------------------------------
      // Editor
      // ---------------------------------------------
      async editSet(id) {
        state.view = 'editor';
        state.editorTab = 'aufgabe';
        ui.render(); // paint shell fast

        await Promise.all([
          api.loadSet(id),
          api.loadLaborOptions(),
          api.loadTaskOptions(''),
          api.loadChecklistOptions(''),
        ]);

        ui.render();
      },

      async createNewSet() {
        if (!state.selectedGroup?.id) {
          alert('Bitte wählen Sie zuerst eine Artikelgruppe aus.');
          return;
        }

        state.editingSet = normalizeSet({
          article_group_id: state.selectedGroup.id,
          name: '',
          description: '',
          components: [],
          labor: [],
          tasks: [],
        });

        state.view = 'editor';
        state.editorTab = 'personal';

        ui.recalculateLocalStats();
        ui.render();

        await Promise.all([
          api.loadLaborOptions(),
          api.loadTaskOptions(''),
          api.loadChecklistOptions(''),
        ]);

        ui.render();
      },
    };

    // ===========================================================================
  // Wizard
  // ===========================================================================
    const wizard = {
      data: [], // Stores the full tree
      activeSectionId: null,

        // --- Collapse state (per section) ---
        _collapsedPhases: new Set(),

        _collapsedStorageKey() {
          const groupId = state?.selectedGroup?.id || 'nogroup';
          const sectionId = this.activeSectionId || 'nosection';
          return `wizCollapsed:${groupId}:${sectionId}`;
        },

        loadCollapsedState() {
          this._collapsedPhases = new Set();
          try {
            const raw = localStorage.getItem(this._collapsedStorageKey());
            const arr = raw ? JSON.parse(raw) : [];
            (arr || []).forEach(id => this._collapsedPhases.add(String(id)));
          } catch (e) {
            this._collapsedPhases = new Set();
          }
        },

        saveCollapsedState() {
          try {
            localStorage.setItem(
              this._collapsedStorageKey(),
              JSON.stringify(Array.from(this._collapsedPhases))
            );
          } catch (e) {}
        },

        isPhaseCollapsed(phaseId) {
          return this._collapsedPhases.has(String(phaseId));
        },

        togglePhaseCollapse(phaseId, ev) {
          if (ev) ev.stopPropagation(); // prevents drag-start on phase header

          const key = String(phaseId);
          if (this._collapsedPhases.has(key)) this._collapsedPhases.delete(key);
          else this._collapsedPhases.add(key);

          this.saveCollapsedState();

          // Re-render so the body/footer hides + sortables rebind safely
          this.renderPhases();
        },

        async addActivityAfter(afterActivityId) {
            const after = this.findActivityById(afterActivityId);
            if (!after) return ui.showStatus('NOT FOUND', true);

            const phaseId = after.phase_id;
            const title = prompt('Titel der neuen Aufgabe:', 'Neue Aufgabe');
            if (!title) return;

            const payload = {
              product_id: state.selectedGroup.id,
              section_id: this.activeSectionId,
              phase_id: phaseId,
              after_activity_id: afterActivityId,
              title: title,
              duration: '00:00',
            };

            const res = await api.requestTaskWizard('/wizard/activity/at', 'POST', payload);
            if (!res?.data) return ui.showStatus('ERROR', true);

            // ✅ realtime local insert right after clicked row
            const newAct = res.data;
            this.insertActivityLocal(newAct, { phaseId, afterActivityId });

            // ✅ re-render + keep sortables stable
            this.renderPhases();

            // ✅ refresh editor-side options cache too
            api.loadTaskOptions(state.taskSearch || '');

            ui.showStatus('ACTIVITY ADDED');
          },



      async init() {
          if (!state.selectedGroup) return;
          ui.showLoading(true);
          // Fetch Tree
          const res = await api.request(`/wizard/tree/${state.selectedGroup.id}`);
          ui.showLoading(false);
          
          if (res && res.data) {
              this.data = res.data;
              this.renderSections();
          }
      },

      _linkActivityId: null,
      _linkPreviewTimer: null,

      closeLinkModal() {
        document.getElementById('link-modal')?.classList.add('hidden');
        this._linkActivityId = null;
      },

      async openLinkModal(activityId) {
        const act = this.findActivityById(activityId);
        if (!act) return;

        this._linkActivityId = activityId;

        const modal = document.getElementById('link-modal');
        const input = document.getElementById('link-input');
        const preview = document.getElementById('link-preview');
        const saveBtn = document.getElementById('link-save-btn');

        input.value = (act.link || '').trim();
        preview.innerHTML = '';

        const renderPreviewCard = (data) => {
          const img = data?.image ? `
            <img src="${escapeHtml(data.image)}" style="width:120px; height:80px; object-fit:cover; border-radius:12px; border:1px solid #e2e8f0;">
          ` : `
            <div style="width:120px; height:80px; border-radius:12px; border:1px solid #e2e8f0; background:#f8fafc; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-weight:900;">
              ${escapeHtml((data?.host || 'URL').slice(0,4).toUpperCase())}
            </div>
          `;

          preview.innerHTML = `
            <div style="display:flex; gap:1rem; padding:1rem; background:white; border:1px solid var(--border-color); border-radius:16px;">
              ${img}
              <div style="flex:1; min-width:0;">
                <div style="font-weight:900; color:#0f172a; margin-bottom:4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                  ${escapeHtml(data?.title || data?.host || 'Link')}
                </div>
                <div style="font-size:.8rem; color:#64748b; font-weight:700; margin-bottom:6px; max-height:40px; overflow:hidden;">
                  ${escapeHtml(data?.description || '')}
                </div>
                <a href="${escapeHtml(data?.url || input.value)}" target="_blank"
                  style="font-size:.75rem; font-weight:900; color:var(--primary); text-decoration:none;">
                  Öffnen <i class="fas fa-arrow-up-right-from-square"></i>
                </a>
              </div>
            </div>
          `;
        };

        const fetchPreview = async (url) => {
          const u = (url || '').trim();
          if (!u) return (preview.innerHTML = '');

          // call your backend preview endpoint (recommended)
          const res = await api.request(`/wizard/activity/link-preview?url=${encodeURIComponent(u)}`, 'GET');
          if (res?.ok) renderPreviewCard(res.data);
          else {
            // fallback: minimal preview
            preview.innerHTML = `
              <div style="padding:1rem; background:#fff; border:1px solid #e2e8f0; border-radius:16px; color:#64748b; font-weight:700;">
                Vorschau nicht verfügbar — wird trotzdem gespeichert.
              </div>
            `;
          }
        };

        input.oninput = () => {
          clearTimeout(this._linkPreviewTimer);
          this._linkPreviewTimer = setTimeout(() => fetchPreview(input.value), 450);
        };

        saveBtn.onclick = async () => {
          const url = input.value.trim();
          const res = await api.request(`/wizard/activity/${activityId}`, 'PUT', { link: url || null });
          if (!res) return ui.showStatus('ERROR', true);

          // ✅ realtime local update (no reload)
          act.link = url || null;

          this.refreshActivityRow(activityId);
          this.closeLinkModal();

          // ✅ refresh task options cache too
          api.loadTaskOptions(state.taskSearch || '');

          ui.showStatus('LINK SAVED');
        };

        // initial preview
        if (input.value) fetchPreview(input.value);

        modal?.classList.remove('hidden');
      },



      renderSections() {
          const list = document.getElementById('wiz-sections-list');
          if(!list) return;

          list.innerHTML = this.data.map(sec => `
              <button onclick="wizard.selectSection(${sec.id})" 
                  class="wiz-section-btn ${this.activeSectionId === sec.id ? 'active' : ''}">
                  <i class="fas fa-folder" style="margin-right:8px;"></i> ${escapeHtml(sec.label)}
              </button>
          `).join('');

          // Auto select first
          if (!this.activeSectionId && this.data.length > 0) {
              this.selectSection(this.data[0].id);
          }
      },

        findActivityById(activityId) {
          for (const sec of (this.data || [])) {
            const phasesRaw = Array.isArray(sec.task_phases) ? sec.task_phases : (Array.isArray(sec.taskPhases) ? sec.taskPhases : []);
            for (const ph of phasesRaw) {
              const acts = Array.isArray(ph.activities) ? ph.activities : [];
              for (const act of acts) {
                if (String(act.id) === String(activityId)) return act;
              }
            }
          }
          return null;
        },

        getSectionById(sectionId) {
          return (this.data || []).find(s => String(s.id) === String(sectionId)) || null;
        },

        getActiveSection() {
          return this.getSectionById(this.activeSectionId);
        },

        getSectionPhases(section) {
          if (!section) return [];
          if (Array.isArray(section.task_phases)) return section.task_phases;
          if (Array.isArray(section.taskPhases)) return section.taskPhases;
          return [];
        },

        getPhaseActivities(phase) {
          if (!phase) return [];
          if (Array.isArray(phase.activities)) return phase.activities;
          if (Array.isArray(phase.phase_activities)) return phase.phase_activities;
          return [];
        },

        setPhaseActivities(phase, activities) {
          if (!phase) return;
          if (Array.isArray(phase.activities)) phase.activities = activities;
          else if (Array.isArray(phase.phase_activities)) phase.phase_activities = activities;
          else phase.activities = activities;
        },

        findPhaseById(phaseId) {
          const section = this.getActiveSection();
          const phases = this.getSectionPhases(section);
          return phases.find(p => String(p.id) === String(phaseId)) || null;
        },

        /**
         * Remove activity from local tree (no reload)
         * Returns { act, phase, phaseId } or null
         */
        removeActivityLocal(activityId) {
          const section = this.getActiveSection();
          const phases = this.getSectionPhases(section);

          for (const ph of phases) {
            const acts = this.getPhaseActivities(ph);
            const idx = acts.findIndex(a => String(a.id) === String(activityId));
            if (idx !== -1) {
              const [act] = acts.splice(idx, 1);
              // Re-index local orders
              acts.forEach((a, i) => (a.sort_order = i));
              this.setPhaseActivities(ph, acts);
              return { act, phase: ph, phaseId: ph.id };
            }
          }
          return null;
        },

        /**
         * Insert activity into local tree (no reload)
         * If afterActivityId is provided, inserts right after it in the same phase.
         */
        insertActivityLocal(newAct, { phaseId, afterActivityId = null } = {}) {
          const ph = this.findPhaseById(phaseId || newAct?.phase_id);
          if (!ph) return false;

          const acts = this.getPhaseActivities(ph);

          if (afterActivityId) {
            const at = acts.findIndex(a => String(a.id) === String(afterActivityId));
            const pos = at === -1 ? acts.length : at + 1;
            acts.splice(pos, 0, newAct);
          } else {
            acts.push(newAct);
          }

          // Re-index local orders
          acts.forEach((a, i) => (a.sort_order = i));
          this.setPhaseActivities(ph, acts);
          return true;
        },

        /**
         * Optional: update server sort_order for one phase after local change
         */
        async syncPhaseActivityOrder(phaseId) {
          const ph = this.findPhaseById(phaseId);
          if (!ph) return;

          const body = document.getElementById(`phase-body-${String(phaseId)}`);
          if (!body) return;

          const items = Array.from(body.querySelectorAll('.activity-item')).map((row, idx) => ({
            id: row.dataset.id,
            phase_id: String(phaseId),
            sort_order: idx,
          }));

          if (!items.length) return;

          await api.request('/wizard/activity/reorder', 'POST', { items });
        },

        refreshActivityRow(activityId) {
          const act = this.findActivityById(activityId);
          const el = document.querySelector(`.activity-item[data-id="${activityId}"]`);
          if (!act || !el) return;
          el.outerHTML = this.getActivityHTML(act);
        },

        async togglePhotoRequired(activityId) {
          const act = this.findActivityById(activityId);
          if (!act) return;

          const current = String(act.photo || '');
          const alreadyHasFile = (current && current !== PHOTO_REQUIRED_SENTINEL);

          // If real filename exists, keep it; clicking just marks it as "active" already
          if (alreadyHasFile) {
            ui.showStatus('PHOTO OK');
            return;
          }

          const next = (current === PHOTO_REQUIRED_SENTINEL) ? null : PHOTO_REQUIRED_SENTINEL;

          const res = await api.request(`/wizard/activity/${activityId}`, 'PUT', { photo: next });
          if (!res) return ui.showStatus('ERROR', true);

          // ✅ realtime local update (no reload)
          act.photo = next;
          act.photo_required = (next === PHOTO_REQUIRED_SENTINEL);
          act.has_photo = false;

          this.refreshActivityRow(activityId);

          // ✅ also refresh task options so editor sees new stuff without page reload
          api.loadTaskOptions(state.taskSearch || '');
          ui.showStatus(next ? 'PHOTO REQUIRED' : 'PHOTO OPTIONAL');
        },


      selectSection(id) {
          this.activeSectionId = id;
          this.renderSections(); // Update active class
          this.loadCollapsedState();
          const section = this.data.find(s => s.id === id);
          document.getElementById('wiz-active-section-title').innerText = section ? section.label : '';
          document.getElementById('btn-add-phase').classList.remove('hidden');
          
          this.renderPhases();
      },

      renderPhases() {
        const container = document.getElementById('wiz-phases-container');
        if (!container) return;

        const section = this.data.find(s => String(s.id) === String(this.activeSectionId));
        if (!section) return;

        const phasesRaw = Array.isArray(section.task_phases)
          ? section.task_phases
          : (Array.isArray(section.taskPhases) ? section.taskPhases : []);

        const phases = phasesRaw.slice().sort((a, b) => {
          const ao = Number.isFinite(+a.order) ? +a.order : 999999;
          const bo = Number.isFinite(+b.order) ? +b.order : 999999;
          return ao - bo;
        });

        if (!phases.length) {
          container.innerHTML = `
            <div style="padding:2.5rem; text-align:center; color:#cbd5e1; font-weight:900;">
              Keine Phasen in dieser Sektion
            </div>
          `;
          return;
        }

        container.innerHTML = phases.map(phase => {
          const stageName =
            phase.stage_name ||
            phase.stage?.stage ||
            phase.stage?.name ||
            phase.stage ||
            (phase.stage_id ? ('Stage #' + phase.stage_id) : '—');

          const actsRaw = Array.isArray(phase.activities)
            ? phase.activities
            : (Array.isArray(phase.phase_activities) ? phase.phase_activities : []);

          const acts = actsRaw.slice().sort((a, b) => {
            const ao = Number.isFinite(+a.sort_order) ? +a.sort_order : 999999;
            const bo = Number.isFinite(+b.sort_order) ? +b.sort_order : 999999;
            return ao - bo;
          });

          const collapsed = this.isPhaseCollapsed(phase.id);

          return `
            <div class="phase-card ${collapsed ? 'is-collapsed' : ''}" data-id="${escapeHtml(phase.id)}">
              <div class="phase-header">
                <div style="display:flex; align-items:center; gap:0.5rem; width:100%; min-width:0;">
                  <i class="fas fa-grip-lines handle" style="color:#94a3b8;"></i>

                  <span class="stage-badge" title="Stage" style="flex:0 0 auto;">
                    <i class="fas fa-flag" style="margin-right:6px;"></i>
                    ${escapeHtml(stageName)}
                  </span>

                  <input
                    type="text"
                    class="phase-title-input"
                    value="${escapeHtml(phase.phase_name || '')}"
                    onchange="wizard.updatePhase(${escapeHtml(phase.id)}, this.value)"
                    style="min-width:0;"
                  />
                </div>

                <div class="phase-actions">
                  <!-- ✅ Collapse/Expand -->

                    <button
                      class="btn-icon-small phase-action-btn"
                      title="Activity hinzufügen"
                      onclick="wizard.openActivityModal({ phaseId: ${escapeHtml(phase.id)} }); event.stopPropagation();"
                    >
                      <i class="fas fa-plus"></i>
                    </button>

                  <button
                    class="btn-icon-small phase-collapse-btn phase-action-btn"
                    title="${collapsed ? 'Phase öffnen' : 'Phase einklappen'}"
                    onclick="wizard.togglePhaseCollapse(${escapeHtml(phase.id)}, event)"
                  >
                    <i class="fas ${collapsed ? 'fa-chevron-down' : 'fa-chevron-up'}"></i>
                  </button>

                  <!-- Delete -->
                  <button
                    onclick="wizard.deletePhase(${escapeHtml(phase.id)}); event.stopPropagation();"
                    class="btn-icon-small phase-action-btn"
                    style="color:var(--danger); border-color:transparent;"
                    title="Phase löschen"
                  >
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>

              <div class="phase-body" id="phase-body-${escapeHtml(phase.id)}">
                ${acts.length
                  ? acts.map(act => this.getActivityHTML(act)).join('')
                  : `<div style="padding:1rem; color:#cbd5e1; font-weight:900; text-align:center;">
                      Keine Activities
                    </div>`
                }
              </div>

              <div class="phase-footer" style="padding:0.5rem; background:#f8fafc; border-top:1px solid #f1f5f9; text-align:center;">
                <button
                  onclick="wizard.openActivityModal({ phaseId: ${escapeHtml(phase.id)} })"
                  class="btn-small"
                  style="width:100%; text-align:center; color:var(--primary);"
                >
                  <i class="fas fa-plus"></i> Activity hinzufügen
                </button>

              </div>
            </div>
          `;
        }).join('');

        this.initSortables();
      },
 
      getActivityHTML(act) {
        const photoActive = isPhotoRequired(act) || hasPhoto(act);
        const linkActive  = !!(act.link && String(act.link).trim().length);

        return `
          <div class="activity-item" data-id="${act.id}" data-phase="${act.phase_id}">
            <i class="fas fa-grip-vertical act-handle"></i>

            <div style="flex:1;">
              <input type="text" class="act-input" value="${escapeHtml(act.title)}"
                onchange="wizard.updateActivity(${act.id}, 'title', this.value)" placeholder="Aufgabe...">

              <div class="act-meta">
                <span class="stage-badge" style="padding:2px 6px;">
                  ${escapeHtml(act.stage_name || '—')}
                </span>

                <i class="far fa-clock"></i>
                <input type="text" value="${escapeHtml(act.duration || '')}" placeholder="00:00"
                  style="border:none; width:60px; font-size:10px;"
                  onchange="wizard.updateActivity(${act.id}, 'duration', this.value)">

                <span style="margin:0 6px;">|</span>

                <input type="text" value="${escapeHtml(act.notes || '')}" placeholder="Notiz..."
                  style="border:none; flex:1; font-size:10px;"
                  onchange="wizard.updateActivity(${act.id}, 'notes', this.value)">
              </div>
            </div>

            <div style="display:flex; gap:6px; align-items:center;">
              <!-- ✅ Photo required toggle -->
              <button
                onclick="wizard.togglePhotoRequired(${act.id})"
                title="${photoActive ? 'Photo: required/has photo' : 'Photo required'}"
                class="btn-icon-small"
                style="
                  ${photoActive ? 'color:var(--primary); border-color:rgba(116,178,212,.55); background:rgba(116,178,212,.08);' : ''}
                ">
                <i class="fas fa-camera"></i>
              </button>

              <!-- ✅ Link modal -->
              <button
                onclick="wizard.openLinkModal(${act.id})"
                title="Link hinzufügen"
                class="btn-icon-small"
                style="
                  ${linkActive ? 'color:var(--primary); border-color:rgba(116,178,212,.55); background:rgba(116,178,212,.08);' : ''}
                ">
                <i class="fas fa-link"></i>
              </button>

              <button onclick="wizard.cloneActivity(${act.id})" title="Duplizieren" class="btn-icon-small">
                <i class="fas fa-copy"></i>
              </button>

              <button onclick="wizard.deleteActivity(${act.id})" title="Löschen" class="btn-icon-small" style="color:var(--danger);">
                <i class="fas fa-times"></i>
              </button>

              <button
                onclick="wizard.openActivityModal({ phaseId: ${act.phase_id}, afterActivityId: ${act.id} })"
                title="Neue Activity direkt hier einfügen"
                class="btn-icon-small"
              >
                <i class="fas fa-plus"></i>
              </button>

            </div>
          </div>
        `;
      },

      initSortables() {
        const container = document.getElementById('wiz-phases-container');
        if (!container) return;

        const getSection = () =>
          (this.data || []).find(s => String(s.id) === String(this.activeSectionId)) || null;

        const getPhases = (section) => {
          if (!section) return [];
          if (Array.isArray(section.task_phases)) return section.task_phases;
          if (Array.isArray(section.taskPhases)) return section.taskPhases;
          return [];
        };

        const patchLocalPhaseOrder = (ids) => {
          const section = getSection();
          const phases = getPhases(section);
          if (!phases.length) return;

          const map = new Map(phases.map(p => [String(p.id), p]));
          const ordered = ids.map(id => map.get(String(id))).filter(Boolean);

          if (Array.isArray(section.task_phases)) section.task_phases = ordered;
          else if (Array.isArray(section.taskPhases)) section.taskPhases = ordered;
        };

        const patchLocalActivitiesMove = (activityId, fromPhaseId, toPhaseId, toIndex) => {
          const section = getSection();
          const phases = getPhases(section);

          const from = phases.find(p => String(p.id) === String(fromPhaseId));
          const to   = phases.find(p => String(p.id) === String(toPhaseId));
          if (!from || !to) return;

          if (!Array.isArray(from.activities)) from.activities = [];
          if (!Array.isArray(to.activities)) to.activities = [];

          const idx = from.activities.findIndex(a => String(a.id) === String(activityId));
          if (idx === -1) return;

          const [act] = from.activities.splice(idx, 1);
          act.phase_id = toPhaseId;

          const safeIndex = Math.max(0, Math.min(toIndex, to.activities.length));
          to.activities.splice(safeIndex, 0, act);

          from.activities.forEach((a, i) => (a.sort_order = i));
          to.activities.forEach((a, i) => (a.sort_order = i));
        };

        // 1) Phases sortable
        if (this._phasesSortable) {
          try { this._phasesSortable.destroy(); } catch (e) {}
          this._phasesSortable = null;
        }

        this._phasesSortable = new Sortable(container, {
          handle: '.phase-header',
          animation: 150,
          ghostClass: 'ghost',

          // ✅ IMPORTANT: don’t start dragging when clicking phase action buttons
          filter: '.phase-action-btn',
          preventOnFilter: true,

          onEnd: async () => {
            const ids = Array.from(container.querySelectorAll('.phase-card'))
              .map(x => x.dataset.id)
              .filter(Boolean);

            const items = ids.map((id, index) => ({ id, order: index }));
            patchLocalPhaseOrder(ids);

            await api.request('/wizard/phase/reorder', 'POST', { items });
            ui.showStatus('PHASE SORTIERT');
          }
        });

        // 2) Activities sortable (skip collapsed)
        const section = getSection();
        const phases = getPhases(section);

        this._activitySortables = this._activitySortables || {};

        phases.forEach(phase => {
          const phaseId = String(phase.id);

          const body = document.getElementById(`phase-body-${phaseId}`);
          if (!body) return;

          // ✅ if collapsed, destroy sortable (prevents weirdness + saves CPU)
          if (this.isPhaseCollapsed(phaseId)) {
            if (this._activitySortables[phaseId]) {
              try { this._activitySortables[phaseId].destroy(); } catch (e) {}
              delete this._activitySortables[phaseId];
            }
            return;
          }

          if (this._activitySortables[phaseId]) {
            try { this._activitySortables[phaseId].destroy(); } catch (e) {}
            delete this._activitySortables[phaseId];
          }

          this._activitySortables[phaseId] = new Sortable(body, {
            group: 'activities',
            handle: '.act-handle',
            animation: 150,
            ghostClass: 'ghost',
            onEnd: async (evt) => {
              const toPhaseId = String(evt.to.id).replace('phase-body-', '');
              const fromPhaseId = String(evt.from.id).replace('phase-body-', '');

              const movedId = evt.item?.dataset?.id;
              if (movedId) {
                patchLocalActivitiesMove(movedId, fromPhaseId, toPhaseId, evt.newIndex);
              }

              const buildItemsFor = (listEl) => {
                const pid = String(listEl.id).replace('phase-body-', '');
                const rows = Array.from(listEl.querySelectorAll('.activity-item'));
                return rows.map((row, idx) => ({
                  id: row.dataset.id,
                  phase_id: pid,
                  sort_order: idx,
                }));
              };

              const items = [
                ...buildItemsFor(evt.to),
                ...(evt.from !== evt.to ? buildItemsFor(evt.from) : []),
              ];

              await api.request('/wizard/activity/reorder', 'POST', { items });
              ui.showStatus('ACTIVITY SORTIERT');
            }
          });
        });
      },

      // --- CRUD Actions ---

      async addPhase() {
          const name = prompt("Name der neuen Phase:");
          if (!name) return;

          const res = await api.request('/wizard/phase', 'POST', {
            product_id: state.selectedGroup.id,
            section_id: this.activeSectionId,
            phase_name: name
          });

          if (!res?.data) return;

          // ✅ realtime: push into local tree
          const section = this.data.find(s => String(s.id) === String(this.activeSectionId));
          if (!section) return;

          if (!Array.isArray(section.task_phases)) section.task_phases = [];
          section.task_phases.push({ ...res.data, activities: [] });

          this.renderPhases();

          // ✅ refresh task options so editor sees it without browser reload
          api.loadTaskOptions(state.taskSearch || '');
          ui.showStatus('PHASE ADDED');
        },


      async updatePhase(id, name) {
          await api.request(`/wizard/phase/${id}`, 'PUT', { phase_name: name });
      },

      async deletePhase(id) {
          if(!confirm("Phase und alle Aktivitäten löschen?")) return;
          await api.request(`/wizard/phase/${id}`, 'DELETE');
          this.init();
      },

      async addActivity(phaseId) {
        const res = await api.request('/wizard/activity', 'POST', {
          product_id: state.selectedGroup.id,
          section_id: this.activeSectionId,
          phase_id: phaseId,
          title: 'Neue Aufgabe',
          duration: '00:00'
        });

        if (!res?.data) return;

        const section = this.data.find(s => String(s.id) === String(this.activeSectionId));
        const phasesRaw = Array.isArray(section?.task_phases) ? section.task_phases : [];
        const ph = phasesRaw.find(p => String(p.id) === String(phaseId));
        if (!ph) return;

        if (!Array.isArray(ph.activities)) ph.activities = [];
        ph.activities.push(res.data);

        this.renderPhases();
        api.loadTaskOptions(state.taskSearch || '');
        ui.showStatus('ACTIVITY ADDED');
      },


      async updateActivity(id, field, value) {
          const payload = {};
          payload[field] = value;
          await api.request(`/wizard/activity/${id}`, 'PUT', payload);
      },

      async deleteActivity(id) {
        if (!confirm("Löschen?")) return;

        // ✅ optimistic local remove
        const removed = this.removeActivityLocal(id);

        // ✅ instant UI update (no full reload)
        if (removed) {
          // remove row if exists
          const row = document.querySelector(`.activity-item[data-id="${id}"]`);
          if (row) row.remove();

          // re-render phase to keep clean + rebind sortables safely
          this.renderPhases();
        }

        const res = await api.request(`/wizard/activity/${id}`, 'DELETE');

        if (!res) {
          // ❌ rollback by reloading tree if server failed
          await this.init();
          ui.showStatus('ERROR', true);
          return;
        }

        // ✅ keep options panel in editor updated without reload
        api.loadTaskOptions(state.taskSearch || '');

        ui.showStatus('ACTIVITY GELÖSCHT');
      },

      
      async cloneActivity(id) {
        // find original first (for phase + insertion point)
        const orig = this.findActivityById(id);
        if (!orig) return ui.showStatus('NOT FOUND', true);

        const res = await api.request('/wizard/activity/clone', 'POST', { id });
        if (!res) {
          ui.showStatus('ERROR', true);
          return;
        }

        const newAct = res.data || res.activity || null;

        // If backend doesn't return the cloned record, fallback to init()
        if (!newAct) {
          await this.init();
          api.loadTaskOptions(state.taskSearch || '');
          ui.showStatus('DUPLIZIERT');
          return;
        }

        // Ensure phase_id exists
        newAct.phase_id = newAct.phase_id ?? orig.phase_id;

        // ✅ insert cloned activity right after original (realtime)
        const ok = this.insertActivityLocal(newAct, {
          phaseId: newAct.phase_id,
          afterActivityId: id,
        });

        if (!ok) {
          await this.init();
          api.loadTaskOptions(state.taskSearch || '');
          ui.showStatus('DUPLIZIERT');
          return;
        }

        // ✅ update UI (no full reload)
        this.renderPhases();

        // ✅ refresh editor options too
        api.loadTaskOptions(state.taskSearch || '');

        ui.showStatus('ACTIVITY DUPLIZIERT');
      },

  };

  // ===============================
  // Activity Modal (insert-at)
  // ===============================
  wizard._activityModalCtx = { phaseId: null, afterActivityId: null };

  wizard.openActivityModal = function({ phaseId, afterActivityId = null } = {}) {
    this._activityModalCtx = { phaseId: String(phaseId || ''), afterActivityId: afterActivityId ? String(afterActivityId) : null };

    // reset inputs
    const t = document.getElementById('am_title');
    const d = document.getElementById('am_duration');
    const n = document.getElementById('am_notes');

    if (t) t.value = '';
    if (d) d.value = '00:00';
    if (n) n.value = '';

    document.getElementById('activity-modal')?.classList.remove('hidden');

    // focus title
    setTimeout(() => t?.focus(), 50);
  };

  wizard.closeActivityModal = function() {
    document.getElementById('activity-modal')?.classList.add('hidden');
    this._activityModalCtx = { phaseId: null, afterActivityId: null };
  };

  wizard.saveActivityModal = async function() {
    const title = document.getElementById('am_title')?.value?.trim();
    if (!title) return ui.showStatus('TITLE REQUIRED', true);

    const duration = document.getElementById('am_duration')?.value?.trim() || '00:00';
    const notes = document.getElementById('am_notes')?.value?.trim() || '';

    const phaseId = this._activityModalCtx.phaseId;
    const afterActivityId = this._activityModalCtx.afterActivityId;

    if (!phaseId) return ui.showStatus('NO PHASE', true);

    const payload = {
      product_id: state.selectedGroup?.id,
      section_id: this.activeSectionId,
      phase_id: phaseId,
      after_activity_id: afterActivityId || null,

      title,
      duration,
      notes,

      // optional fields (keep if you want)
      description: null,
      link: null,
      photo_required: null,
      stage_id: null,

      // optional: auto-add to a master set (only if you want)
      // master_set_id: state.editingSet?.id ?? null,
    };

    const res = await api.requestTaskWizard('/wizard/activity/at', 'POST', payload);
    if (!res?.data) return ui.showStatus('ERROR', true);

    // ✅ realtime: insert into local wizard tree
    const newAct = res.data;
    this.insertActivityLocal(newAct, { phaseId, afterActivityId });

    // ✅ re-render wizard list + rebind sortables
    this.renderPhases();

    // ✅ refresh task options used in editor tab (no reload)
    api.loadTaskOptions(state.taskSearch || '');

    // ✅ if task picker modal is open, refresh its content too
    if (state.pickerContext?.type === 'task' && !document.getElementById('modal-container')?.classList.contains('hidden')) {
      document.getElementById('modal-content').innerHTML = ui.getTaskOptionsHTML({ mode: 'modal' });
    }

    this.closeActivityModal();
    ui.showStatus('ACTIVITY ADDED');
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
            
            <button 
                onclick="event.stopPropagation(); state.selectedGroup={id:${g.id}, name:'${escapeHtml(g.article_group)}'}; app.navigate('wizard')" 
                class="card-settings-btn" 
                title="Konfiguration Wizard">
                <i class="fas fa-magic"></i>
            </button>

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
    // ------------------------------------------------
    // NEW: Render Group Sets (Folders) Layout
    // ------------------------------------------------
     // ... existing ui methods ...

    // 1. Render the Folder List
    renderGroupSetsTab(container, headerContent) {
        let html = headerContent || ''; 
        
        // Custom Header for Group Tab
        html += `
         <div class="list-header">
            <div class="list-nav">
                <button onclick="app.navigate('dashboard')" class="btn btn-icon"><i class="fas fa-chevron-left"></i></button>
                <div>
                    <h2 style="font-size:1.5rem; font-weight:900; color:var(--text-main);">${escapeHtml(state.selectedGroup?.name || '')}</h2>
                    <p style="font-size:0.75rem; font-weight:700; color:#94a3b8; text-transform:uppercase;">Group Sets (Ordner)</p>
                </div>
            </div>
            
            <div style="display:flex; align-items:center; gap:.75rem;">
                 <div class="editor-tabs" style="margin:0;">
                    <button class="editor-tab-btn active" disabled>
                        <i class="fas fa-layer-group"></i> Gruppensets
                    </button>
                    <button class="editor-tab-btn" onclick="app.setSetsTab('all')">
                        <i class="fas fa-list"></i> Einzelset
                    </button>
                 </div>

                 <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Ordner suchen..." 
                           oninput="ui.filterGroupSets(this.value)" style="width:200px;">
                 </div>

                 <button onclick="ui.openGroupSetModal()" class="btn btn-accent">
                    <i class="fas fa-folder-plus"></i> NEUER ORDNER
                 </button>
            </div>
         </div>
        `;

        // Render Folders
        const groups = state.groupSets || [];
        
        if (groups.length === 0) {
            html += `
                <div style="padding:4rem; text-align:center; border:2px dashed var(--border-color); border-radius:var(--radius-2xl); color:#cbd5e1;">
                    <i class="fas fa-folder-open" style="font-size:3rem; margin-bottom:1rem;"></i><br>
                    <strong>Keine Gruppensets vorhanden</strong><br>
                    Erstellen Sie einen Ordner, um Sets zu gruppieren.
                </div>`;
        } else {
            html += `<div id="group-sets-container" class="folder-grid">`;
            
            groups.forEach(g => {
                const s = g.stats || {};
                html += `
                <div class="folder-card group-row" data-name="${escapeHtml(g.name).toLowerCase()}" style="border-left-color: ${g.color};">
                    <div class="folder-header" onclick="ui.toggleGroupAccordion(${g.id})">
                        <div class="folder-info">
                            <div class="folder-title-row">
                                <i class="fas fa-folder" style="color:${g.color}; font-size:1.25rem;"></i>
                                <span class="folder-name">${escapeHtml(g.name)}</span>
                                <span class="folder-count-badge">${s.sets_count || 0} Sets</span>
                            </div>
                            <div class="folder-stats-row">
                                <span class="f-stat blue" title="Components"><i class="fas fa-cubes"></i> ${s.mainCount} M / ${s.subCount} S</span>
                                <span class="f-stat green" title="Labor"><i class="fas fa-user-clock"></i> ${s.laborCount}</span>
                                <span class="f-stat purple" title="Tasks"><i class="fas fa-list-check"></i> ${s.taskCount}</span>
                                <span class="f-stat orange" title="Protocols"><i class="fas fa-clipboard-check"></i> ${s.protocolCount}</span>
                            </div>
                        </div>

                        <div class="folder-actions">
                            <button onclick="event.stopPropagation(); ui.editGroupSet(${g.id})" class="btn-icon-small" title="Bearbeiten">
                                <i class="fas fa-pen"></i>
                            </button>
                             <button onclick="event.stopPropagation(); ui.deleteGroupSet(${g.id})" class="btn-icon-small" style="color:var(--danger)" title="Löschen">
                                <i class="fas fa-trash"></i>
                            </button>
                            <i class="fas fa-chevron-down" id="chevron-${g.id}" style="color:#cbd5e1; margin-left:10px; transition:transform 0.2s;"></i>
                        </div>
                    </div>

                    <div id="folder-content-${g.id}" class="folder-content">
                        <div style="text-align:center; padding:1rem;"><span class="loader"></span></div>
                    </div>
                </div>
                `;
            });
            html += `</div>`;
        }

        container.innerHTML = html;
    },

    // 2. Filter Logic for Folder Search
    filterGroupSets(val) {
        const term = val.toLowerCase();
        document.querySelectorAll('.group-row').forEach(row => {
            const name = row.getAttribute('data-name');
            row.style.display = name.includes(term) ? 'block' : 'none';
        });
    },

    // 3. Accordion Logic (Open/Close)
    async toggleGroupAccordion(groupId) {
        const content = document.getElementById(`folder-content-${groupId}`);
        const chevron = document.getElementById(`chevron-${groupId}`);
        
        if (!content) return;

        const isOpen = content.classList.contains('open');

        if (isOpen) {
            content.classList.remove('open');
            chevron.style.transform = 'rotate(0deg)';
        } else {
            content.classList.add('open');
            chevron.style.transform = 'rotate(180deg)';
            
            // Lazy Load Sets if not already loaded
            if (!content.getAttribute('data-loaded')) {
                await this.loadSetsIntoFolder(groupId);
            }
        }
    },

    // 4. Fetch Sets for a specific Folder
      async loadSetsIntoFolder(groupSetId) {
        const content = document.getElementById(`folder-content-${groupSetId}`);
        if (!content) return;

        // ✅ group set folder endpoint (NOT article groups)
        const res = await api.request(`/group-sets/${groupSetId}/master-sets`, 'GET');

        const rows = res?.data || [];
        if (!rows.length) {
          content.innerHTML = `<div class="folder-empty-msg">Keine MasterSets in diesem Ordner.</div>`;
          content.setAttribute('data-loaded', 'true');
          return;
        }

        content.innerHTML = `
          <div class="list-grid">
            ${rows.map(s => {
              const stats = s.stats || {};
              return `
                <div onclick="app.editSet(${s.id})" class="card-set" style="padding:1rem; border:1px solid #e2e8f0; border-left:4px solid var(--primary);">
                  <div class="set-info">
                    <div>
                      <h4 style="font-weight:900; font-size:1rem; margin-bottom:4px;">${escapeHtml(s.name)}</h4>
                      <div class="pill-container">
                        <span class="pill pill-blue"><div class="dot bg-blue"></div> ${stats.mainCount || 0} Main</span>
                        <span class="pill pill-green"><div class="dot bg-green"></div> ${stats.laborCount || 0} Pers.</span>
                        <span class="pill pill-purple"><div class="dot bg-purple"></div> ${stats.taskCount || 0} Tasks</span>
                      </div>
                    </div>
                  </div>
                  <div style="text-align:right;">
                    <p style="font-weight:900; font-size:1.1rem;">${(stats.total || 0).toLocaleString('de-DE', { style:'currency', currency:'EUR' })}</p>
                  </div>
                </div>
              `;
            }).join('')}
          </div>
        `;
        content.setAttribute('data-loaded', 'true');
      },

    // 5. Edit Prep
    editGroupSet(groupId) {
        const group = state.groupSets.find(g => g.id === groupId);
        if (!group) return;

        // Populate global draft
        window.__groupSetDraft = {
            id: group.id,
            name: group.name,
            description: group.description,
            color: group.color || '#74b2d4',
            selectedIds: new Set(),
            isEdit: true
        };

        ui.openGroupSetModal(true); // Pass true for Edit Mode
    },

    
    // Filter Logic for Folder Search
    filterGroupSets(val) {
        const term = val.toLowerCase();
        document.querySelectorAll('.group-row').forEach(row => {
            const name = row.getAttribute('data-name');
            row.style.display = name.includes(term) ? 'block' : 'none';
        });
    },

    // Accordion Logic
    async toggleGroupAccordion(groupId) {
        const content = document.getElementById(`folder-content-${groupId}`);
        const chevron = document.getElementById(`chevron-${groupId}`);
        
        if (!content) return;

        // Toggle UI
        const isOpen = content.classList.contains('open');
        
        // Close others (optional, keeps UI clean)
        /* document.querySelectorAll('.folder-content.open').forEach(el => {
            if(el.id !== `folder-content-${groupId}`) {
                el.classList.remove('open');
                // reset chevrons...
            }
        });
        */

        if (isOpen) {
            content.classList.remove('open');
            chevron.style.transform = 'rotate(0deg)';
        } else {
            content.classList.add('open');
            chevron.style.transform = 'rotate(180deg)';
            
            // Fetch Sets if empty (Lazy Load)
            // We check if we already loaded real content (not just the loader)
            if (!content.getAttribute('data-loaded')) {
                await this.loadSetsIntoFolder(groupId);
            }
        }
    },

    async loadSetsIntoFolder(groupId) {
        const content = document.getElementById(`folder-content-${groupId}`);
        
        // Call API
        // GET /admin/master-sets/groups/{group}/master-sets
        const res = await api.request(`/groups/${groupId}/master-sets`);
        
        if (!res || !res.data || res.data.length === 0) {
            content.innerHTML = `<div class="folder-empty-msg">Keine MasterSets in diesem Ordner.</div>`;
            content.setAttribute('data-loaded', 'true');
            return;
        }

        // Render Sets Card (Reusing similar style to main list, but smaller)
        const html = `
            <div class="list-grid">
                ${res.data.map(s => {
                    const stats = s.stats || {};
                    return `
                    <div onclick="app.editSet(${s.id})" class="card-set" style="padding:1rem; border:1px solid #e2e8f0; border-left:4px solid var(--primary);">
                        <div class="set-info">
                             <div>
                                <h4 style="font-weight:900; font-size:1rem; margin-bottom:4px;">${escapeHtml(s.name)}</h4>
                                <div class="pill-container">
                                     <span class="pill pill-blue"><div class="dot bg-blue"></div> ${stats.mainCount} Main</span>
                                     <span class="pill pill-green"><div class="dot bg-green"></div> ${stats.laborCount} Pers.</span>
                                     <span class="pill pill-purple"><div class="dot bg-purple"></div> ${stats.taskCount} Tasks</span>
                                </div>
                             </div>
                        </div>
                        <div style="text-align:right;">
                             <p style="font-weight:900; font-size:1.1rem;">${(stats.total || 0).toLocaleString('de-DE', { style:'currency', currency:'EUR' })}</p>
                        </div>
                    </div>
                    `;
                }).join('')}
            </div>
        `;
        
        content.innerHTML = html;
        content.setAttribute('data-loaded', 'true');
    },

    // Edit Group Modal Prep
    editGroupSet(groupId) {
        const group = state.groupSets.find(g => g.id === groupId);
        if (!group) return;

        // Populate global draft
        window.__groupSetDraft = {
            id: group.id,
            name: group.name,
            description: group.description,
            color: group.color || '#74b2d4',
            selectedIds: new Set(), // We don't load IDs here for edit to keep it simple, or we fetch them
            isEdit: true
        };

        // Reuse the modal but switch mode
        ui.openGroupSetModal(true); // pass true for Edit Mode
    },

    async deleteGroupSet(groupSetId) {
        if (!confirm('Diesen Ordner wirklich löschen? Die Sets darin bleiben erhalten.')) return;

        const gid = state.selectedGroup?.id;

        // ✅ optimistic remove from state -> instant UI update
        const prev = Array.isArray(state.groupSets) ? [...state.groupSets] : [];
        state.groupSets = prev.filter(g => String(g.id) !== String(groupSetId));

        // close accordion if open (avoid “ghost open”)
        const content = document.getElementById(`folder-content-${groupSetId}`);
        if (content) content.classList.remove('open');

        ui.render(); // instant repaint

        // ✅ backend
        const res = await api.deleteGroupSet(groupSetId);
        if (!res || res.status !== 'ok') {
          // rollback on failure
          state.groupSets = prev;
          ui.render();
          return ui.showStatus(res?.message || 'ERROR', true);
        }

        // ✅ force refresh list from server (so no stale cache)
        state.groupSetsForGroupId = null;
        if (gid) await api.getGroupSets(gid);

        ui.showStatus('ORDNER GELÖSCHT');
      },


    promptText(title, placeholder = '', initial = '') {
        const val = prompt(`${title}`, initial || '');
        if (val == null) return null;
        const v = String(val).trim();
        return v.length ? v : null;
      },

      async createGroupFlow() {
        const name = this.promptText('Neue Business Unit / Artikelgruppe Name:', 'z.B. Tür', '');
        if (!name) return;

        const res = await api.createGroup({ article_group: name });
        if (!res) return ui.showStatus('ERROR', true);

        await api.getGroups(state.groupSearch || '');
        ui.showStatus('GROUP CREATED');
      },

      openGroupSetModal(isEdit = false) {
          // If it's NOT edit mode, reset draft
          if (!isEdit) {
              state.pickerContext = { type: 'groupSet' };
              window.__groupSetDraft = {
                  name: '', description: '', color: '#74b2d4', q: '', selectedIds: new Set()
              };
          }

          const d = window.__groupSetDraft;
          const modal = $('#modal-container');
          
          // ... (standard modal setup code) ...
          $('#modal-title').innerHTML = isEdit 
              ? `<i class="fas fa-edit"></i> <span>Ordner bearbeiten</span>`
              : `<i class="fas fa-folder-plus"></i> <span>Neuer Ordner</span>`;

          $('#modal-content').innerHTML = this.getGroupSetModalHTML(); 
          $('#modal-container').classList.remove('hidden');

          // Pre-fill inputs if edit
          if (isEdit) {
              setTimeout(() => {
                  if($('#gs_name')) $('#gs_name').value = d.name || '';
                  if($('#gs_desc')) $('#gs_desc').value = d.description || '';
                  if($('#gs_color')) $('#gs_color').value = d.color || '#74b2d4';
                  // Hide the "Select Sets" part for simple edit to avoid complexity, 
                  // or fetch set_ids via API if you want full editing capability.
                  // For layout purposes, hiding the set picker in edit mode is cleaner unless requested.
              }, 50);
          }
      },
        getGroupSetModalHTML() {
          return `
            <div style="display:flex; flex-direction:column; gap:1rem;">

              <div style="background:white; border:1px solid var(--border-color); border-radius:16px; padding:1rem;">
                <div class="form-group mb-2">
                  <label>Name *</label>
                  <input id="gs_name" class="form-control" placeholder="z.B. Tür Komplettpaket"
                    oninput="window.__groupSetDraft.name=this.value" />
                </div>

                <div class="form-group mb-2">
                  <label>Beschreibung</label>
                  <input id="gs_desc" class="form-control" placeholder="Optional..."
                    oninput="window.__groupSetDraft.description=this.value" />
                </div>

                <div class="form-row">
                  <div class="col">
                    <label>Farbe</label>
                    <div style="display:flex; gap:.75rem; align-items:center;">
                      <input id="gs_color" type="color" value="${escapeHtml(window.__groupSetDraft?.color || '#74b2d4')}"
                        style="width:56px; height:44px; border-radius:12px; border:1px solid var(--border-color);"
                        onchange="window.__groupSetDraft.color=this.value" />
                      <div class="text-muted">Diese Farbe kann z.B. für Cards/Badges genutzt werden.</div>
                    </div>
                  </div>
                </div>
              </div>

              <div style="background:white; border:1px solid var(--border-color); border-radius:16px; padding:1rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:.75rem;">
                  <div style="font-weight:900;">MasterSets hinzufügen</div>
                  <div class="pill pill-gray" style="margin:0;">
                    <div class="dot bg-gray"></div>
                    <span id="gs_sel_count">${(window.__groupSetDraft?.selectedIds?.size || 0)} ausgewählt</span>
                  </div>
                </div>

                <div class="search-wrapper" style="margin-bottom:.75rem;">
                  <i class="fas fa-search search-icon"></i>
                  <input id="gs_search" type="text" class="search-input" style="width:100%;"
                    placeholder="Sets suchen (Name)...">
                </div>

                <div id="gs_sets_list" style="max-height:360px; overflow:auto; padding-right:6px;">
                  ${this.getGroupSetSetsListHTML()}
                </div>
              </div>

              <div style="display:flex; justify-content:flex-end; gap:.5rem;">
                <button class="btn btn-secondary" onclick="ui.closeModal()">Abbrechen</button>
                <button class="btn btn-primary" onclick="ui.saveGroupSetFromModal()">
                  <i class="fas fa-save"></i> Gruppenset speichern
                </button>
              </div>

            </div>
          `;
        },

        getGroupSetSetsListHTML() {
          const all = Array.isArray(state.sets) ? state.sets : [];
          const d = window.__groupSetDraft || { q: '', selectedIds: new Set() };
          const q = String(d.q || '').toLowerCase().trim();

          // Only normal sets (exclude existing group sets)
          const list = all.filter(s => {
            const isGroup =
              s.is_group_set === true ||
              s.isGroupSet === true ||
              String(s.type || '').toLowerCase() === 'group' ||
              String(s.kind || '').toLowerCase() === 'group';
            if (isGroup) return false;

            if (!q) return true;
            return String(s.name || '').toLowerCase().includes(q);
          });

          if (!list.length) {
            return `<div style="padding:1.5rem; text-align:center; color:#cbd5e1; font-weight:900;">Keine Sets gefunden</div>`;
          }

          return `
            <div style="display:flex; flex-direction:column; gap:.5rem;">
              ${list.map(s => {
                const checked = d.selectedIds.has(String(s.id));
                const total = (s.stats?.total || 0);
                return `
                  <label class="catalog-item" style="margin:0; display:flex; align-items:center; gap:.75rem; cursor:pointer;">
                    <input type="checkbox" ${checked ? 'checked' : ''}
                      onchange="ui.toggleGroupSetPick(${s.id}, this.checked)" />
                    <div style="flex:1; min-width:0;">
                      <div style="font-weight:900; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        ${escapeHtml(s.name || ('Set #' + s.id))}
                      </div>
                      <div style="font-size:12px; font-weight:800; color:#94a3b8;">
                        ${(total).toLocaleString('de-DE',{style:'currency',currency:'EUR'})}
                        · ${escapeHtml((s.stats?.mainCount ?? 0))} Main
                        · ${escapeHtml((s.stats?.subCount ?? 0))} Sub
                        · ${escapeHtml((s.stats?.laborCount ?? 0))} Pers.
                      </div>
                    </div>
                  </label>
                `;
              }).join('')}
            </div>
          `;
        },

        toggleGroupSetPick(id, on) {
          const d = window.__groupSetDraft;
          if (!d) return;

          const key = String(id);
          if (on) d.selectedIds.add(key);
          else d.selectedIds.delete(key);

          const cnt = document.getElementById('gs_sel_count');
          if (cnt) cnt.textContent = `${d.selectedIds.size} ausgewählt`;
        },

        async saveGroupSetFromModal() {
          const d = window.__groupSetDraft;
          if (!d) return;

          const name = (String(d.name || '').trim() || String(document.getElementById('gs_name')?.value || '').trim());
          const description = (String(d.description || '').trim() || String(document.getElementById('gs_desc')?.value || '').trim());
          const color = (String(d.color || '').trim() || String(document.getElementById('gs_color')?.value || '').trim());

          if (!name) return ui.showStatus("NAME REQUIRED", true);

          const isEdit = !!d.isEdit && !!d.id;

          // collect set_ids only if create OR if user actually selected
          const rawIds =
            d.selectedIds instanceof Set ? Array.from(d.selectedIds) :
            Array.isArray(d.selectedIds) ? d.selectedIds :
            typeof d.selectedIds === "string" ? d.selectedIds.split(",") :
            [];

          const setIds = rawIds.map(x => parseInt(String(x).trim(), 10)).filter(Number.isFinite);

          const payload = {
            article_group_id: state?.selectedGroup?.id ?? null,
            name,
            description,
            color,
          };

          // ✅ only require & include set_ids when creating (or when explicitly provided)
          if (!isEdit) {
            if (!setIds.length) return ui.showStatus("SELECT SETS", true);
            payload.set_ids = setIds;
          } else if (setIds.length) {
            payload.set_ids = setIds; // optional update behavior if you support it
          }

          const endpoint = isEdit ? `/group-sets/${d.id}` : `/group-sets`;
          const method   = isEdit ? 'PUT' : 'POST';

          const res = await api.request(endpoint, method, payload);
          if (!res || res.status !== 'ok') return ui.showStatus(res?.message || "ERROR", true);

          ui.closeModal();

          const gid = state?.selectedGroup?.id;
          if (gid) {
            state.groupSetsForGroupId = null;
            await api.getGroupSets(gid);
            await api.getSets(gid);
          }

          ui.showStatus(isEdit ? "GROUPSET UPDATED" : "GROUPSET CREATED");
        },


      async editGroupFlow(groupId, currentName) {
        const name = this.promptText('Artikelgruppe umbenennen:', '', currentName || '');
        if (!name) return;

        const res = await api.updateGroup(groupId, { article_group: name });
        if (!res) return ui.showStatus('ERROR', true);

        // update selectedGroup name if it was edited
        if (state.selectedGroup && String(state.selectedGroup.id) === String(groupId)) {
          state.selectedGroup.name = name;
        }

        await api.getGroups(state.groupSearch || '');
        ui.showStatus('GROUP UPDATED');
      },

      async deleteGroupFlow(groupId) {
        if (!confirm('Artikelgruppe wirklich löschen? (Sets evtl. betroffen)')) return;

        const res = await api.deleteGroup(groupId);
        if (!res) return ui.showStatus('ERROR', true);

        // reset selection if deleted
        if (state.selectedGroup && String(state.selectedGroup.id) === String(groupId)) {
          state.selectedGroup = null;
          state.sets = [];
          state.view = 'dashboard';
        }

        await api.getGroups(state.groupSearch || '');
        ui.showStatus('GROUP DELETED');
      },


    async toggleChecklistItems(uid, checklistIndex) {
        const el = document.getElementById(uid);
        if (!el) return;

        const willOpen = el.classList.contains('hidden');
        el.classList.toggle('hidden');

        // Only load on open + only if empty
        if (!willOpen) return;

        const list = state.editingSet?.checklists || [];
        const c = list[checklistIndex];
        if (!c) return;

        const hasItems = Array.isArray(c.items) && c.items.length > 0;
        if (hasItems) return;

        // show small loader while fetching
        el.innerHTML = `<div class="text-muted">Lade Items...</div>`;

        const id = c.maintenance_checklist_id ?? c.id;
        if (!id) {
          el.innerHTML = `<div class="text-muted">Keine ID</div>`;
          return;
        }

        const items = await api.loadChecklistItems(id);
        c.items = items;

        // re-render to show items
        ui.renderChecklistsTab();
        // auto-open again after re-render
        setTimeout(() => document.getElementById(uid)?.classList.remove('hidden'), 0);
      },


    updateChecklistField(index, field, value) {
      const list = state.editingSet?.checklists || [];
      if (!list[index]) return;

      if (field === 'is_required') list[index][field] = !!value;
      else list[index][field] = value;

      ui.renderChecklistsTab();
    },

    removeChecklistAt(index) {
      const list = state.editingSet?.checklists || [];
      list.splice(index, 1);
      // reindex sort order
      list.forEach((x,i)=> x.sort_order = i);
      ui.renderChecklistsTab();
    },


        // ------------------------------
    // Checklists
    // ------------------------------
    reloadChecklistOptions() {
      api.loadChecklistOptions(state.checklistSearch || '');
      ui.showStatus('CHECKLISTS SYNC');
    },

    openChecklistPicker() {
      state.pickerContext = { type: 'checklists' };

      const modal = $('#modal-container');
      const titleEl = $('#modal-title');
      const searchBox = $('#modal-search-box');
      const contentEl = $('#modal-content');
      const searchInput = $('#modal-search-input');

      titleEl.innerHTML = `<i class="fas fa-square-check" style="color:var(--primary)"></i> <span>Checklists wählen</span>`;
      searchBox.classList.remove('hidden');

      searchInput.value = state.checklistSearch || '';
      searchInput.oninput = (e) => {
        state.checklistSearch = e.target.value;
        api.loadChecklistOptions(state.checklistSearch);
      };

      contentEl.innerHTML = this.getChecklistOptionsHTML({ mode: 'modal' });
      modal.classList.remove('hidden');
    },

    renderChecklistsTab() {
      const mount = $('#checklists-tab-mount');
      if (!mount) return;

      mount.innerHTML = `
        <div style="display:grid; grid-template-columns: 1fr 1.2fr; gap:1.5rem;">
          <!-- Options -->
          <div style="background:white; border:1px solid var(--border-color); border-radius:var(--radius-2xl); padding:1.25rem;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
              <div>
                <div style="font-weight:900; color:var(--text-main);">Checklist Optionen</div>
              </div>
              <button class="btn btn-icon" onclick="ui.openChecklistPicker()" title="Vollansicht">
                <i class="fas fa-up-right-from-square"></i>
              </button>
            </div>

            <div class="search-wrapper" style="margin-bottom:1rem;">
              <i class="fas fa-search search-icon"></i>
              <input id="checklists-search" type="text" class="search-input" style="width:100%;"
                placeholder="Checklist suchen..." value="${escapeHtml(state.checklistSearch || '')}">
            </div>

            <div id="checklists-options-panel" style="max-height: 520px; overflow:auto; padding-right:6px;">
              ${this.getChecklistOptionsHTML({ mode: 'inline' })}
            </div>
          </div>

          <!-- Selected -->
          <div style="background:white; border:1px solid var(--border-color); border-radius:var(--radius-2xl); padding:1.25rem;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
              <div>
                <div style="font-weight:900; color:var(--text-main);">Zugewiesene Checklists</div>
              </div>
              <div class="pill pill-gray" style="margin:0;">
                <div class="dot bg-gray"></div>
                <span>${(state.editingSet.checklists || []).length} Checklists</span>
              </div>
            </div>

            <div id="checklists-selected-panel">
              ${this.getSelectedChecklistsHTML()}
            </div>
          </div>
        </div>
      `;

      const search = $('#checklists-search');
      if (search) {
        search.oninput = (e) => {
          state.checklistSearch = e.target.value;
          api.loadChecklistOptions(state.checklistSearch);
          ui.renderChecklistsTab();
        };
      }

      this.bindChecklistDnD(); // ✅ drag/drop
    },

    getChecklistOptionsHTML({ mode }) {
      const list = Array.isArray(state.checklistOptions) ? state.checklistOptions : [];
      if (!list.length) {
        return `<div style="padding:2rem; text-align:center; color:#cbd5e1; font-weight:900;">Keine Checklists gefunden</div>`;
      }

      // render cards -> each is draggable/clonable into selected list
      return `
        <div id="checklists-options-list" style="display:flex; flex-direction:column; gap:0.75rem;">
          ${list.map((c) => {
            const payload = b64EncodeJson({
              maintenance_checklist_id: c.id ?? c.maintenance_checklist_id, 
              title: c.title ?? c.name ?? '',
              description: c.description ?? '',
              items_count: c.items_count ?? (Array.isArray(c.items) ? c.items.length : 0),
            });

            const title = c.title ?? c.name ?? ('Checklist #' + (c.id ?? c.checklist_id));
            const desc  = c.description ?? '';
            const cnt   = c.items_count ?? (Array.isArray(c.items) ? c.items.length : 0);

            return `
              <div
                class="catalog-item checklist-opt"
                data-encoded="${escapeHtml(payload)}"
                style="margin:0; cursor:grab;"
                onclick="ui.addChecklistFromEncoded('${payload}')"
                title="Klicken oder ziehen zum Hinzufügen"
              >
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem;">
                  <div style="display:flex; gap:.75rem; align-items:flex-start;">
                    <div class="handle" style="width:28px; height:28px;">
                      <i class="fas fa-grip-vertical"></i>
                    </div>
                    <div>
                      <div style="font-weight:900; color:var(--text-main);">${escapeHtml(title)}</div>
                      <div style="font-size:.75rem; font-weight:700; color:#94a3b8;">${desc ? escapeHtml(desc).slice(0,110) : '—'}</div>
                    </div>
                  </div>
                  <div style="font-size:10px; font-weight:900; color:#94a3b8; text-transform:uppercase; white-space:nowrap;">
                    ${escapeHtml(cnt)} Items
                  </div>
                </div>
              </div>
            `;
          }).join('')}
        </div>
      `;
    },

   addChecklistFromEncoded(encoded) {
      let payload;
      try { payload = b64DecodeJson(encoded); } catch (e) {
        ui.showStatus('PAYLOAD ERROR', true); return;
      }

      const cid = String(payload.maintenance_checklist_id ?? '');
      if (!cid) return ui.showStatus('NO ID', true);

      // allow duplicates only if trigger differs; default trigger=start
      const trigger = 'start';
      const exists = (state.editingSet.checklists || []).some(x =>
        String(x.maintenance_checklist_id) === cid && String(x.trigger || 'start') === trigger
      );
      if (exists) return ui.showStatus('ALREADY ADDED', true);

      state.editingSet.checklists = Array.isArray(state.editingSet.checklists) ? state.editingSet.checklists : [];

      state.editingSet.checklists.push({
        maintenance_checklist_id: parseInt(cid, 10),
        title: payload.title || '',
        description: payload.description || '',
        items_count: payload.items_count ?? 0,

        trigger: trigger,
        is_required: true,
        sort_order: state.editingSet.checklists.length,

        // items will come from /master-sets/{id} show() after save/reload, or keep empty
        items: payload.items || [],
      });

      ui.renderChecklistsTab();
      ui.showStatus('CHECKLIST ADDED');
    },

    getSelectedChecklistsHTML() {
      const list = Array.isArray(state.editingSet?.checklists) ? state.editingSet.checklists : [];
      if (!list.length) {
        return `<div style="padding:2rem; text-align:center; color:#cbd5e1; font-weight:900;">Keine Checklists zugewiesen</div>`;
      }

      const triggerOptions = (current) => CHECKLIST_TRIGGERS.map(t =>
        `<option value="${escapeHtml(t.value)}" ${String(current)===String(t.value)?'selected':''}>${escapeHtml(t.label)}</option>`
      ).join('');

      const renderItems = (items) => {
        const arr = Array.isArray(items) ? items : [];
        if (!arr.length) return `<div class="text-muted">Keine Items</div>`;

        return `
          <div style="margin-top:.75rem; display:flex; flex-direction:column; gap:.5rem;">
            ${arr.map(it => `
              <div style="border:1px solid #eef2f7; background:#fff; border-radius:12px; padding:.65rem .75rem;">
                <div style="display:flex; justify-content:space-between; gap:.75rem;">
                  <div style="font-weight:900; color:#0f172a;">${escapeHtml(it.label || it.field_name || 'Item')}</div>
                  <div style="font-size:10px; font-weight:900; color:#94a3b8; text-transform:uppercase; white-space:nowrap;">
                    ${escapeHtml(it.field_type || '')}${it.is_required ? ' · REQUIRED' : ''}
                  </div>
                </div>
                ${it.help_text ? `<div style="font-size:.75rem; font-weight:700; color:#64748b; margin-top:4px;">${escapeHtml(it.help_text)}</div>` : ''}
              </div>
            `).join('')}
          </div>
        `;
      };

      return `
        <div id="checklists-selected-list" style="display:flex; flex-direction:column; gap:.75rem;">
          ${list
            .slice()
            .sort((a,b)=> (+a.sort_order) - (+b.sort_order))
            .map((c, idx) => {
              const title = c.title || ('Checklist #' + (c.maintenance_checklist_id || c.id));
              const cnt = Number.isFinite(+c.items_count) ? +c.items_count : (Array.isArray(c.items) ? c.items.length : 0);
              const trigger = c.trigger || 'start';
              const required = (c.is_required !== undefined) ? !!c.is_required : true;

              const uid = `mcl_${String(c.maintenance_checklist_id || c.id)}_${idx}`;
              return `
                <div class="catalog-item" style="margin:0;">
                  <div style="display:flex; justify-content:space-between; gap:1rem; align-items:flex-start;">
                    <div style="min-width:0;">
                      <div style="font-weight:900; color:var(--text-main); display:flex; align-items:center; gap:.5rem;">
                        <button class="btn-icon-small" type="button"
                          onclick="ui.toggleChecklistItems('${uid}', ${idx})" 
                          title="Items anzeigen"
                          style="border-color:#e2e8f0;">
                          <i class="fas fa-list-check"></i>
                        </button>
                        <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${escapeHtml(title)}</span>
                        <span class="pill pill-gray" style="margin-left:.5rem;"><span>${cnt} Items</span></span>
                      </div>
                      <div class="text-muted" style="margin-top:4px;">${c.description ? escapeHtml(c.description).slice(0,140) : '—'}</div>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:.5rem; align-items:flex-end;">
                      <select class="form-control"
                        style="min-width:170px; padding:.55rem .75rem; border-radius:12px;"
                        onchange="ui.updateChecklistField(${idx}, 'trigger', this.value)">
                        ${triggerOptions(trigger)}
                      </select>

                      <label style="display:flex; gap:.5rem; align-items:center; font-weight:900; font-size:12px; color:#64748b;">
                        <input type="checkbox" ${required ? 'checked' : ''}
                          onchange="ui.updateChecklistField(${idx}, 'is_required', this.checked)">
                        Required
                      </label>

                      <button class="btn btn-danger" type="button"
                        onclick="ui.removeChecklistAt(${idx})" title="Entfernen">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </div>

                  <div id="${uid}" class="hidden" style="margin-top:.75rem; padding-top:.75rem; border-top:1px solid #f1f5f9;">
                    ${renderItems(c.items)}
                  </div>
                </div>
              `;
            }).join('')
          }
        </div>
      `;
    },

    removeChecklistById(id) {
      const list = state.editingSet.checklists || [];
      const idx = list.findIndex(x => String(x.checklist_id ?? x.id) === String(id));
      if (idx === -1) return;
      list.splice(idx, 1);
      list.forEach((x,i) => x.sort_order = i);
      ui.showStatus('ENTFERNT');
      ui.renderChecklistsTab();
    },

    bindChecklistDnD() {
      const optWrap = document.getElementById('checklists-options-list');
      const selWrap = document.getElementById('checklists-selected-list');

      // Selected list must exist even if empty
      if (!selWrap) {
        // when empty state, user can still drag in -> create a drop zone
        const panel = document.getElementById('checklists-selected-panel');
        if (panel) {
          panel.innerHTML = `<div id="checklists-selected-list" style="min-height:120px; padding:1rem; border:2px dashed var(--border-color); border-radius:var(--radius-2xl);"></div>`;
        }
      }

      const sel = document.getElementById('checklists-selected-list');
      if (!sel) return;

      // options list: clone items out
      if (optWrap) {
        new Sortable(optWrap, {
          group: { name: 'checklists', pull: 'clone', put: false },
          sort: false,
          handle: '.handle',
          animation: 150,
          ghostClass: 'ghost',
        });
      }

      // selected list: accept drops + reorder
      new Sortable(sel, {
        group: { name: 'checklists', pull: true, put: true },
        handle: '.checklist-handle, .handle',
        animation: 150,
        ghostClass: 'ghost',
        onAdd: (evt) => {
          // dragged from options -> read payload and add to state, then re-render
          const el = evt.item;
          const encoded = el?.getAttribute('data-encoded');

          // remove DOM clone (we re-render from state)
          try { el?.remove(); } catch (e) {}

          if (encoded) ui.addChecklistFromEncoded(encoded);
          else ui.renderChecklistsTab();
        },
        onEnd: () => {
          // reorder in state based on DOM order
          const ids = Array.from(sel.querySelectorAll('.checklist-row'))
            .map(x => x.getAttribute('data-checklist-id'))
            .filter(Boolean);

          const map = new Map((state.editingSet.checklists || []).map(x => [String(x.checklist_id ?? x.id), x]));
          const ordered = ids.map(id => map.get(String(id))).filter(Boolean);

          state.editingSet.checklists = ordered;
          state.editingSet.checklists.forEach((x,i) => x.sort_order = i);

          ui.showStatus('SORTIERT');
        }
      });
    },


   openAddTaskForStage(stageId) {
      console.log('[openAddTaskForStage] stageId=', stageId, 'typeof=', typeof stageId);

      const id = (stageId === '' || stageId == null) ? null : String(stageId).trim();
      state.selectedStageId = id || (state.taskOptions?.[0]?.id ?? null);

      ui.openTaskPicker();

      setTimeout(() => {
        if (!id) return;
        const el = document.getElementById(`taskopt-stage-${id}`);
        console.log('[openAddTaskForStage] scroll target=', `taskopt-stage-${id}`, 'found=', !!el);
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }, 80);
    },



    updateGroupGrid() {
      const grid = $('#groups-grid');
      if (grid) grid.innerHTML = this.getGroupGridHTML();
    },

    addFromCatalogEncoded(encoded) {
      let p;
      try {
        p = b64DecodeJson(encoded);
      } catch (e) {
        ui.showStatus('PAYLOAD ERROR', true);
        return;
      }

      ui.addFromCatalog(
        p.product_id,
        p.product_name,
        p.distributor_price_id,
        p.distributor_id,
        p.distributor_name,
        p.unit_price
      );
    },
 
    // ------------------------------
    // Main render
    // ------------------------------
    render() {
        // Top nav back button visibility
        const navBtn = $('#nav-back-btn');
        if (navBtn) {
          if (state.view === 'dashboard') navBtn.classList.add('hidden');
          else navBtn.classList.remove('hidden');
        }

        // Ensure container exists
        const container = $('#app-container');
        if (!container) return;
        container.innerHTML = '';

        // --- VIEW: DASHBOARD ---
        if (state.view === 'dashboard') {
          container.innerHTML = `
              <header class="dashboard-header">
                <div class="dash-left">
                  <h2 class="brand-title">Artikelgruppen und Dienste</h2>
                  <p class="brand-sub">Wählen Sie einen Artikelgruppe zur Verwaltung</p>
                </div>
                <div class="dash-right">
                  <button type="button" class="btn btn-primary" onclick="TaskWizard.open()">
                    <i class="fas fa-magic" style="margin-right:.45rem;"></i> Phasenassistent hinzufügen
                  </button>
                  <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" oninput="app.searchGroups(this.value)" class="search-input" placeholder="Bereich suchen..." value="${escapeHtml(state.groupSearch || '')}">
                  </div>
                </div>
              </header>
              <div id="groups-grid" class="grid-cards">${this.getGroupGridHTML()}</div>
            `;
          return;
        }

        // --- VIEW: GROUP LIST (The Folder/Set View) ---
        if (state.view === 'groupList') {
          // Prepare data
          const allSets = Array.isArray(state.sets) ? state.sets : [];
          const groupSets = Array.isArray(state.groupSets) ? state.groupSets : [];

          // Logic to fetch group sets if we are in 'group' tab but haven't loaded them yet
          if (state.setsTab === 'group' && state.selectedGroup?.id) {
              const gid = String(state.selectedGroup.id);
              const loadedFor = String(state.groupSetsForGroupId || '');
              if (loadedFor !== gid || !groupSets.length) {
                  // We call API but continue rendering to avoid blocking UI; API will re-render on success
                  api.getGroupSets(state.selectedGroup.id);
              }
          }

          // Get basic breadcrumbs string
          // IMPORTANT: getBreadcrumbsHTML() must NOT try to set innerHTML on 'container'
          let content = this.getBreadcrumbsHTML();

          // 1. If in "Group Sets" (Folder) Mode
          if (state.setsTab === 'group') {
              // Delegate to specific renderer passing the container
              this.renderGroupSetsTab(container, content);
              return;
          }

          // 2. If in "All Sets" Mode (Standard List)
          const listForTab = allSets; 
          
          content += `
            <div class="list-header">
              <div class="list-nav">
                <button onclick="app.navigate('dashboard')" class="btn btn-icon"><i class="fas fa-chevron-left"></i></button>
                <div>
                  <h2 style="font-size:1.5rem; font-weight:900; color:var(--text-main);">${escapeHtml(state.selectedGroup?.name || '')}</h2>
                  <p style="font-size:0.75rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.1em;">MasterSet Übersicht</p>
                </div>
              </div>
              
              <div style="display:flex; align-items:center; gap:.75rem; flex-wrap:wrap;">
                <div class="editor-tabs" style="margin:0;">
                  <button class="editor-tab-btn ${state.setsTab === 'group' ? 'active' : ''}" onclick="app.setSetsTab('group')" type="button" title="Gruppensets">
                    <i class="fas fa-layer-group"></i> Gruppensets
                    <span style="margin-left:.25rem; opacity:.9;">(${groupSets.length})</span>
                  </button>
                  <button class="editor-tab-btn ${state.setsTab === 'all' ? 'active' : ''}" onclick="app.setSetsTab('all')" type="button" title="Alle Sets">
                    <i class="fas fa-list"></i> Einzelset
                    <span style="margin-left:.25rem; opacity:.9;">(${allSets.length})</span>
                  </button>
                </div>
                
                <button onclick="app.createNewSet()" class="btn btn-primary">
                  <i class="fas fa-plus"></i> NEUES SET ERSTELLEN
                </button>
                
                <button type="button" class="btn btn-accent" onclick="ui.openGroupSetModal()" title="Neues Gruppenset erstellen">
                  <i class="fas fa-layer-group"></i> NEUES GRUPPENSET
                </button>
              </div>
            </div>

            <div class="list-grid">
          `;

          if (listForTab && listForTab.length) {
              content += listForTab.map(s => {
                  const total = (s.stats?.total || 0);
                  // percent calcs safe against /0
                  const mainPct  = total > 0 ? ((s.stats?.mainCost || 0) / total * 100).toFixed(0) : 0;
                  const subPct   = total > 0 ? ((s.stats?.subCost  || 0) / total * 100).toFixed(0) : 0;
                  const laborPct = total > 0 ? ((s.stats?.labor    || 0) / total * 100).toFixed(0) : 0;

                  const taskCount = (s.stats?.taskCount ?? 0);
                  const protocolCount = (s.stats?.protocolCount ?? 0);

                  return `
                    <div onclick="app.editSet(${s.id})" class="card-set">
                      <div class="set-info">
                        <div class="set-icon"><i class="fas fa-box-open fa-lg"></i></div>
                        <div>
                          <h4 style="font-weight:900; font-size:1.125rem; color:var(--text-main); margin-bottom:0.5rem;">${escapeHtml(s.name || 'Unbenanntes Set')}</h4>
                          <div class="pill-container">
                            <div class="pill pill-blue"><div class="dot bg-blue"></div><span>${s.stats?.mainCount || 0} Main (${mainPct}%)</span></div>
                            <div class="pill pill-gray"><div class="dot bg-gray"></div><span>${s.stats?.subCount || 0} Sub (${subPct}%)</span></div>
                            <div class="pill pill-green"><div class="dot bg-green"></div><span>${s.stats?.laborCount || 0} Pers. (${laborPct}%)</span></div>
                            <div class="pill pill-purple"><div class="dot bg-purple"></div><span>${taskCount} Aufgaben</span></div>
                            <div class="pill pill-orange"><div class="dot bg-orange"></div><span>${protocolCount} Protokolle</span></div>
                          </div>
                        </div>
                      </div>
                      <div style="text-align:right;">
                        <p style="font-size:0.625rem; font-weight:900; color:#cbd5e1; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.25rem;">Set Gesamtwert</p>
                        <p style="font-size:1.75rem; font-weight:900; color:var(--text-main);">
                          ${(s.stats?.total || 0).toLocaleString('de-DE', { style:'currency', currency:'EUR' })}
                        </p>
                      </div>
                    </div>
                  `;
              }).join('');
          } else {
              content += `
                  <div style="grid-column:1/-1; background:white; border:1px dashed var(--border-color); border-radius:var(--radius-2xl); padding:2.5rem; text-align:center;">
                    <div style="font-weight:900; color:#0f172a; margin-bottom:.35rem;">Keine Sets vorhanden</div>
                    <div style="color:#94a3b8; font-weight:800; font-size:.85rem;">Erstelle ein neues Set über den Button rechts.</div>
                  </div>
              `;
          }
          
          content += `</div>`; // Close grid
          container.innerHTML = content;
          return;
        }

        // --- VIEW: WIZARD ---
        if (state.view === 'wizard') {
            container.innerHTML = `
                <div class="dashboard-header" style="margin-bottom:1rem;">
                    <div>
                        <button onclick="app.navigate('dashboard')" class="btn btn-secondary" style="margin-bottom:0.5rem"><i class="fas fa-arrow-left"></i> Zurück</button>
                        <h2 style="font-size:1.75rem; font-weight:900;">Task Configuration Wizard</h2>
                        <p style="color:#64748b;">${escapeHtml(state.selectedGroup?.name || 'Produkt')} Konfiguration</p>
                    </div>
                </div>
                <div class="wizard-layout">
                    <div class="wizard-sidebar">
                        <div style="padding:1rem; border-bottom:1px solid #e2e8f0; font-weight:900; background:#f8fafc; color:#94a3b8;">PHASE SECTIONS</div>
                        <div id="wiz-sections-list" style="overflow-y:auto; flex:1;"></div>
                    </div>
                    <div class="wizard-main custom-scrollbar">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                            <h3 id="wiz-active-section-title" style="font-size:1.25rem; font-weight:900; color:var(--primary);">Wählen Sie eine Sektion</h3>
                            <button onclick="wizard.addPhase()" id="btn-add-phase" class="btn btn-primary hidden"><i class="fas fa-plus"></i> NEUE PHASE</button>
                        </div>
                        <div id="wiz-phases-container" style="padding-bottom:2rem;"></div>
                    </div>
                </div>
            `;
            wizard.init(); 
            return;
        }

        // --- VIEW: EDITOR ---
        if (state.view === 'editor') {
          this.renderEditor(container);
        }
      },

    // ------------------------------
    // Editor
    // ------------------------------
   // ✅ Fixed + fully rewritten (safe HTML, stable tab logic, no undefined stats crashes)
      renderEditor(container) {
        const esc = (v) => (window.escapeHtml ? window.escapeHtml(v) : String(v ?? '')
          .replace(/&/g, "&amp;")
          .replace(/</g, "&lt;")
          .replace(/>/g, "&gt;")
          .replace(/"/g, "&quot;")
          .replace(/'/g, "&#039;"));

        const fmtEUR = (n) => {
          const x = Number(n ?? 0);
          try {
            return x.toLocaleString("de-DE", { style: "currency", currency: "EUR" });
          } catch {
            return `${x.toFixed(2)} €`;
          }
        };

        // mount helper (prevents "container is null" issues)
        if (!container) return;

        // Loading state
        if (!state?.editingSet) {
          container.innerHTML = `
            ${this.getBreadcrumbsHTML?.() ?? ""}
            <div style="padding:3rem; text-align:center; color:#94a3b8; font-weight:900;">
              Lade Set...
            </div>
          `;
          return;
        }

        const s = state.editingSet;

        // ensure tab is valid
        const validTabs = new Set(["aufgabe", "material", "personal", "checklists"]);
        if (!validTabs.has(state.editorTab)) state.editorTab = "aufgabe";


        // safe stats defaults (prevents undefined errors)
        const stats = {
          mainPct:  Number(s?.stats?.mainPct  ?? 0),
          subPct:   Number(s?.stats?.subPct   ?? 0),
          laborPct: Number(s?.stats?.laborPct ?? 0),
          mainTotal:  Number(s?.stats?.mainTotal  ?? 0),
          subTotal:   Number(s?.stats?.subTotal   ?? 0),
          laborTotal: Number(s?.stats?.laborTotal ?? 0),
          total:      Number(s?.stats?.total      ?? 0),
        };

        // normalize percent widths (avoid NaN / >100 / negative)
        const clampPct = (v) => Math.max(0, Math.min(100, Number(v ?? 0) || 0));
        const wMain  = clampPct(stats.mainPct);
        const wSub   = clampPct(stats.subPct);
        const wLabor = clampPct(stats.laborPct);

        const tabLabel =
            state.editorTab === "aufgabe"    ? "Aufgabe" :
            state.editorTab === "material"   ? "Material" :
            state.editorTab === "personal"   ? "Personal" :
            "Checklists";


        // Tab content
        const materialTabHTML = `
          <!-- =======================
              TAB: MATERIAL
          ======================== -->
          <section class="tw-section">
            <header class="tw-header">
              <div class="tw-head-left">
                <h3 class="tw-title tw-title--primary">
                  <i class="fas fa-cubes"></i>
                  Komponenten
                </h3>
                <p class="tw-subtitle">Haupt- und Unterartikel des Sets (Drag & Drop)</p>
              </div>

              <div class="tw-actions">
                <button type="button" class="btn btn-icon" title="Katalog durchsuchen" onclick="ui.openCatalog('main')">
                  <i class="fas fa-search"></i>
                </button>
                <button type="button" class="btn btn-secondary" onclick="ui.openCatalog('main')">
                  <i class="fas fa-plus"></i>
                  ARTIKEL HINZUFÜGEN
                </button>
              </div>
            </header>

            <div id="comp-list"></div>
          </section> 
        `;

        const personalTabHTML = `
            <!-- =======================
                TAB: PERSONAL
            ======================== -->
            <section class="tw-section">
              <header class="tw-header">
                <div class="tw-head-left">
                  <h3 class="tw-title tw-title--accent">
                    <i class="fas fa-user-clock"></i>
                    Personalaufwand
                  </h3>
                  <p class="tw-subtitle">Zugeordnete Positionen & Arbeitsstunden</p>
                </div>

                <div class="tw-actions">
                  <button type="button" class="btn btn-accent" onclick="ui.openLaborPicker()">
                    <i class="fas fa-plus"></i>
                    PERSONAL / POSITION
                  </button>
                </div>
              </header>

              <div class="labor-table-wrap">
                <table class="labor-table">
                  <thead>
                    <tr>
                      <th>Position / Mitarbeiter</th>
                      <th class="t-center">Anteil</th>
                      <th class="t-center">Stunden</th>
                      <th class="t-right">Kosten</th>
                      <th class="t-right"></th>
                    </tr>
                  </thead>
                  <tbody id="labor-body"></tbody>
                </table>
              </div>
            </section>
          `;


        const tasksTabHTML = `
          <!-- =======================
              TAB: TASKS
          ======================== -->
          <section class="tw-section">
            <header class="tw-header">
              <div class="tw-head-left">
                <h3 class="tw-title">
                  <i class="fas fa-list-check"></i>
                  Aufgaben
                </h3>
                <p class="tw-subtitle">Aufgaben nach Stages auswählen und dem Set zuweisen</p>
              </div>

              <div class="tw-actions">
                <button type="button" class="btn btn-icon" title="Optionen aktualisieren" onclick="ui.reloadTaskOptions()">
                  <i class="fas fa-rotate"></i>
                </button>
                <button type="button" class="btn btn-secondary" onclick="ui.openTaskPicker()">
                  <i class="fas fa-plus"></i>
                  AUFGABE HINZUFÜGEN
                </button>
              </div>
            </header>

           

            <!-- Tasks UI Mount -->
            <div id="tasks-tab-mount"></div>
          </section>
        `;

        const checklistTabHTML = `
          <!-- =======================
              TAB: CHECKLISTS
          ======================== -->
          <section class="tw-section">
            <header class="tw-header">
              <div class="tw-head-left">
                <h3 class="tw-title">
                  <i class="fas fa-square-check"></i>
                  Protokols
                </h3>
                <p class="tw-subtitle">Protokolle / Checklists verwalten</p>
              </div>

              <div class="tw-actions">
                <button type="button" class="btn btn-secondary" onclick="ui.openChecklistPicker?.()">
                  <i class="fas fa-plus"></i>
                  PROTOKOLL HINZUFÜGEN
                </button>
              </div>
            </header>

            <div id="checklists-tab-mount"></div>
          </section>
        `;

       const leftHTML =
        state.editorTab === "aufgabe"    ? tasksTabHTML :
        state.editorTab === "material"   ? materialTabHTML :
        state.editorTab === "personal"   ? personalTabHTML :
        checklistTabHTML;


        const headerHTML = `
          <div class="editor-header">
            <div style="flex:1; margin-right:2rem;">
              <input
                type="text"
                class="input-title"
                value="${esc(s?.name || "")}"
                placeholder="Set Name eingeben..."
                oninput="state.editingSet.name = this.value"
              >
              <input
                type="text"
                class="input-desc"
                value="${esc(s?.description || "")}"
                placeholder="Interne Beschreibung..."
                oninput="state.editingSet.description = this.value"
              >
            </div>

            <div style="display:flex; gap:1rem;">
              <button onclick="ui.openReport()" class="btn btn-secondary">
                <i class="fas fa-file-pdf"></i> PDF EXPORT
              </button>

              ${s?.id ? `
                <button onclick="ui.deleteConfirm(${Number(s.id)})" class="btn btn-danger">
                  <i class="fas fa-trash"></i>
                </button>
              ` : ""}

              <button onclick="api.saveSet()" class="btn btn-primary">SET SPEICHERN</button>
            </div>
          </div>
        `;

        const tabsHTML = `
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <div class="editor-tabs">
              <button class="editor-tab-btn ${state.editorTab === "aufgabe" ? "active" : ""}" onclick="app.setEditorTab('aufgabe')">
                <i class="fas fa-list-check"></i> Aufgabe
              </button>

              <button class="editor-tab-btn ${state.editorTab === "material" ? "active" : ""}" onclick="app.setEditorTab('material')">
                <i class="fas fa-cubes"></i> Material
              </button>
              <button class="editor-tab-btn ${state.editorTab === "personal" ? "active" : ""}" onclick="app.setEditorTab('personal')">
                <i class="fas fa-user-clock"></i> Personal
              </button> 
              <button class="editor-tab-btn ${state.editorTab === "checklists" ? "active" : ""}" onclick="app.setEditorTab('checklists')">
                <i class="fas fa-square-check"></i> Protokols
              </button>
            </div>

            <div style="font-size:0.75rem; font-weight:800; color:#94a3b8;">
              Aktiver Tab: ${tabLabel}
            </div>
          </div>
        `;

        const summaryHTML = `
          <div>
            <div class="summary-card">
              <h4 class="summary-title">Kalkulationsübersicht</h4>

              <div class="summary-row">
                <div class="label-col">
                  <span class="label-main">Hauptartikel</span>
                  <span class="label-badge bg-blue" style="background:#f0f7ff; color:var(--primary);">${wMain}% Anteil</span>
                </div>
                <span class="val-text">${fmtEUR(stats.mainTotal)}</span>
              </div>

              <div class="summary-row">
                <div class="label-col">
                  <span class="label-main">Zubehör / Sub</span>
                  <span class="label-badge" style="background:#f8fafc; color:#94a3b8;">${wSub}% Anteil</span>
                </div>
                <span class="val-text">${fmtEUR(stats.subTotal)}</span>
              </div>

              <div class="summary-row">
                <div class="label-col">
                  <span class="label-main">Personal</span>
                  <span class="label-badge" style="background:rgba(147, 194, 28, 0.1); color:var(--accent);">${wLabor}% Anteil</span>
                </div>
                <span class="val-text">${fmtEUR(stats.laborTotal)}</span>
              </div>

              <div class="total-section">
                <div style="display:flex; justify-content:space-between; align-items:flex-end;">
                  <span class="total-label">Gesamtwert Set</span>
                  <span class="total-value">${fmtEUR(stats.total)}</span>
                </div>

                <div class="progress-bar">
                  <div class="progress-segment" style="background:var(--primary); width:${wMain}%"></div>
                  <div class="progress-segment" style="background:#94a3b8; width:${wSub}%"></div>
                  <div class="progress-segment" style="background:var(--accent); width:${wLabor}%"></div>
                </div>
              </div>
            </div>
          </div>
        `;

        const styleHTML = `
          <style>
            /* Layout */
            .tw-shell{display:flex;flex-direction:column;gap:2.5rem;}
            .tw-section{display:block;}
            .tw-header{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;}
            .tw-head-left{min-width:240px;}
            .tw-actions{display:flex;gap:.75rem;flex-wrap:wrap;}

            /* Titles */
            .tw-title{margin:0;font-size:1.05rem;font-weight:900;color:var(--text-main);display:flex;align-items:center;gap:.6rem;}
            .tw-title--primary{color:var(--primary);}
            .tw-title--accent{color:var(--accent);}
            .tw-subtitle{margin:.35rem 0 0;font-size:.9rem;opacity:.8;}

            /* Helpers */
            .t-center{text-align:center;}
            .t-right{text-align:right;}
            .muted{opacity:.75;}

            /* Checklist Shell */
            .tw-checklist{
              border:1px solid rgba(255,255,255,.08);
              border-radius:14px;
              padding:14px;
              background:rgba(255,255,255,.03);
            }
            .tw-checklist-top{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;}
            .tw-checklist-meta{min-width:240px;}
            .tw-checklist-title{
              margin:0;
              font-size:.95rem;
              font-weight:900;
              color:var(--text-main);
              display:flex;
              align-items:center;
              gap:.5rem;
            }
            .tw-checklist-title i{opacity:.85;}
            .tw-checklist-hint{margin-top:6px;font-size:.85rem;opacity:.8;}

            .tw-checklist-controls{display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;}
            .tw-search{
              display:flex;align-items:center;gap:.5rem;
              padding:.55rem .75rem;border-radius:12px;
              border:1px solid rgba(255,255,255,.08);
              background:rgba(0,0,0,.25);
            }
            .tw-search i{opacity:.75;}
            .tw-search input{
              background:transparent;border:0;outline:0;color:inherit;
              min-width:180px;
            }
            .tw-select{
              padding:.6rem .75rem;
              border-radius:12px;
              border:1px solid rgba(255,255,255,.08);
              background:rgba(0,0,0,.25);
              color:inherit;
            }

            .tw-checklist-bottom{margin-top:12px;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;}
            .tw-counts{font-size:.85rem;opacity:.85;}
            .tw-toggles{display:flex;gap:.5rem;flex-wrap:wrap;}
            .tw-pill{
              display:flex;align-items:center;gap:.5rem;
              padding:.45rem .65rem;border-radius:999px;
              border:1px solid rgba(255,255,255,.08);
              background:rgba(0,0,0,.18);
              font-size:.85rem;
            }
            .tw-mount{margin-top:12px;}
          </style>
        `;

        // Build content
        const content = `
          ${this.getBreadcrumbsHTML?.() ?? ""}
          ${headerHTML}
          ${tabsHTML}

          <div class="editor-grid">
            <div class="tw-shell">
              ${leftHTML}
            </div>
            ${styleHTML}
            ${summaryHTML}
          </div>
        `;

        container.innerHTML = content;

        // Render tab-specific mounts
        try {
         if (state.editorTab === "material") {
            this.renderComponentItems?.();
          } else if (state.editorTab === "personal") {
            this.renderLaborItems?.();
          } else if (state.editorTab === "aufgabe") {
            this.renderTasksTab?.();
          } else {
            this.renderChecklistsTab?.();
          }

        } catch (e) {
          console.error("renderEditor mount error:", e);
        }
      },

    // ------------------------------
    // Material: components
    // ------------------------------
    renderComponentItems() {
    const list = $('#comp-list');
    if (!list) return;

    const comps = state.editingSet.components || [];

    // Helper to check if a component is actually in the DB
    // (Checks if ID exists and isn't a frontend-generated temporary string)
    const isSavedInDB = (id) => id && !isNaN(id) && Number(id) > 0;

    list.innerHTML = comps.map((c, idx) => {
        const mainId = c.component_id ?? c.pivot_id ?? c.id;
        const mainComponentId = isSavedInDB(mainId) ? Number(mainId) : null;
        const mainTitle = (c.product_name || '').toString();

        return `
          <div class="comp-item" data-index="${idx}">
            <div class="comp-main">
              <div class="comp-left">
                <div class="handle"><i class="fas fa-grip-vertical"></i></div>
                <div class="comp-icon"><i class="fas fa-cube"></i></div>

                <div>
                  <div style="display:flex; align-items:center;">
                    <div style="display:flex; align-items:center;">
                      <p style="font-weight:900; color:var(--text-main); font-size:0.875rem;">
                        ${escapeHtml(c.product_name)}
                      </p>
                      <span class="comp-percent">${escapeHtml(c.percentOfTotal)}%</span>
                    </div>

                    <p style="font-size: 0.65rem; font-weight: 700; color: var(--primary); margin-top: -2px;">
                      <i class="fas fa-truck-fast"></i> ${escapeHtml(c.distributor_name || 'Standard Lieferant')}
                    </p>
                  </div>

                  <div style="display:flex; align-items:center; gap:0.5rem; margin-top:0.25rem;">
                    <span style="font-size:0.625rem; font-weight:700; color:#94a3b8; text-transform:uppercase;">
                      ${escapeHtml(c.distributor_name)} •
                    </span>

                    <div class="price-control">
                      <input
                        type="number"
                        step="0.01"
                        onchange="ui.updatePrice(${idx}, null, this.value)"
                        class="price-input"
                        value="${escapeHtml(c.unit_price)}"
                      >
                      <span>€</span>
                    </div>

                    <button onclick="ui.refreshPrice(${idx}, null)" class="btn-icon-small" title="Preis aktualisieren">
                      <i class="fas fa-sync-alt"></i>
                    </button>

                    <button
                      type="button"
                      class="btn-icon-small"
                      title="${mainComponentId ? 'Varianten bearbeiten' : 'Bitte zuerst das Set speichern'}"
                      data-comp-id="${mainComponentId ?? ''}"
                      data-comp-title="${escapeHtml(mainTitle)}"
                      onclick="event.stopPropagation(); ui.openDescriptionFromBtn(this);"
                      style="${mainComponentId ? '' : 'opacity:0.3; cursor:not-allowed; filter:grayscale(1);'}"
                      ${mainComponentId ? '' : 'disabled'}
                    >
                      <i class="fas fa-pen-nib"></i>
                    </button>

                  </div>
                </div>
              </div>

              <div style="display:flex; align-items:center; gap:1.5rem;">
                <div class="qty-control">
                  <button onclick="ui.updateQty(${idx}, null, ${toNum(c.qty) - 1})" class="btn-small">
                    <i class="fas fa-minus"></i>
                  </button>

                  <input
                    type="number"
                    onchange="ui.updateQty(${idx}, null, this.value)"
                    class="qty-input"
                    value="${escapeHtml(c.qty)}"
                  >

                  <button onclick="ui.updateQty(${idx}, null, ${toNum(c.qty) + 1})" class="btn-small">
                    <i class="fas fa-plus"></i>
                  </button>
                </div>

                <button onclick="ui.removeComp(${idx})" class="btn-danger">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>

            <div id="sub-list-${idx}" class="sub-list">
              ${(c.subComponents || []).map((sub, sIdx) => {
                const subId = sub.component_id ?? sub.pivot_id ?? sub.id;
                const subComponentId = isSavedInDB(subId) ? Number(subId) : null;
                const subTitle = (sub.product_name || '').toString();

                return `
                  <div class="sub-item" data-main-index="${idx}" data-sub-index="${sIdx}">
                    <div style="display:flex; align-items:center; gap:1rem;">
                      <div class="sub-handle"><i class="fas fa-grip-lines"></i></div>

                      <div>
                        <div style="display:flex; align-items:center; gap:0.5rem;">
                          <span style="font-size:0.75rem; font-weight:700; color:#64748b;">
                            ${escapeHtml(sub.product_name)}
                          </span>

                          <span style="font-size:0.5rem; font-weight:900; background:white; color:#94a3b8; padding:1px 4px; border:1px solid #e2e8f0; border-radius:4px;">
                            ${escapeHtml(sub.percentOfTotal)}%
                          </span>
                        </div>
                      </div>
                    </div>

                    <div style="display:flex; align-items:center; gap:1rem;">
                      <div class="price-control-sm">
                        <input
                          type="number"
                          step="0.01"
                          onchange="ui.updatePrice(${idx}, ${sIdx}, this.value)"
                          class="price-input-sm"
                          value="${escapeHtml(sub.unit_price)}"
                        >
                        <span>€</span>
                      </div>

                      <button onclick="ui.refreshPrice(${idx}, ${sIdx})" class="btn-icon-small" title="Preis aktualisieren">
                        <i class="fas fa-sync-alt"></i>
                      </button>

                      <button
                        type="button"
                        class="btn-icon-small"
                        title="${subComponentId ? 'Varianten bearbeiten' : 'Bitte zuerst das Set speichern'}"
                        data-comp-id="${subComponentId ?? ''}"
                        data-comp-title="${escapeHtml(subTitle)}"
                        onclick="event.stopPropagation(); ui.openDescriptionFromBtn(this);"
                        style="${subComponentId ? '' : 'opacity:0.3; cursor:not-allowed; filter:grayscale(1);'}"
                        ${subComponentId ? '' : 'disabled'}
                      >
                        <i class="fas fa-pen-nib"></i>
                      </button>

                      <div style="width:1px; height:20px; background:#e2e8f0;"></div>

                      <input
                        type="number"
                        onchange="ui.updateQty(${idx}, ${sIdx}, this.value)"
                        class="qty-input"
                        style="background:white; border:1px solid #e2e8f0; border-radius:6px; font-size:0.75rem;"
                        value="${escapeHtml(sub.qty)}"
                      >

                      <button onclick="ui.removeComp(${idx}, ${sIdx})" class="btn-danger" style="width:24px; height:24px; font-size:10px;">
                        <i class="fas fa-times"></i>
                      </button>
                    </div>
                  </div>
                `;
              }).join('')}
            </div>

            <button onclick="ui.openCatalog('sub', ${idx})" class="add-sub-btn">+ UNTER-ARTIKEL VERKNÜPFEN</button>
          </div>
        `;
    }).join('');

    // Handle Empty State
    if (!comps.length) {
        list.innerHTML = `
          <div style="padding:4rem; text-align:center; border:2px dashed var(--border-color); border-radius:var(--radius-2xl); color:#cbd5e1; font-weight:700; font-style:italic;">
            Noch keine Artikel hinzugefügt
          </div>`;
        return;
    }

    // Re-initialize Sortables
    this.initMaterialSortables();
},

// Moved sortable logic to a sub-function to keep render clean
initMaterialSortables() {
    const list = $('#comp-list');
    if (!list) return;

    // Main components
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

    // Sub components
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

    /**
     * ✅ ONE SAFE ENTRY POINT FOR BOTH BUTTONS (MAIN + SUB)
     * - avoids broken JSON in onclick
     * - always reads correct id + title
     */
    openDescriptionFromBtn(btn) {
      try {
        const idRaw = btn?.dataset?.compId || '';
        const compId = idRaw ? Number(idRaw) : null;
        if (!compId) return;

        // title is already escaped in HTML; here we want plain text
        const title = (btn.dataset.compTitle || '').toString();

        descUI.open(compId, title);
      } catch (e) {
        console.error('openDescriptionFromBtn failed', e);
      }
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
              </div>
              <div class="pill pill-gray" style="margin:0;">
                <div class="dot bg-gray"></div>
                <span>${(state.editingSet.tasks || []).length} Aufgaben</span>
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
          <div id="taskopt-stage-${escapeHtml(st.id)}" class="catalog-item" style="margin-bottom:1rem;">
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

  window.addEventListener('error', (e) => {
      console.error('[window.error]', e.message, e.filename + ':' + e.lineno + ':' + e.colno, e.error);
    });

    window.addEventListener('unhandledrejection', (e) => {
      console.error('[unhandledrejection]', e.reason);
    });




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

        const stageIdArg = stageKey === 'null' ? '' : stageKey; // for onchange/select consistency

        return `
          <div style="border:1px solid #f1f5f9; border-radius:var(--radius-xl); padding:1rem; margin-bottom:1rem;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
              <div style="font-weight:900; color:var(--text-main); display:flex; align-items:center; gap:0.5rem;">
                <span style="width:10px; height:10px; border-radius:999px; background:var(--accent); display:inline-block;"></span>
                ${escapeHtml(stageLabel(stageKey))}
              </div>

              <!-- ✅ RIGHT SIDE: count + button -->
              <div style="display:flex; align-items:center; gap:0.5rem;">
                <div style="font-size:10px; font-weight:900; color:#94a3b8; text-transform:uppercase;">
                  ${group.length} Aufgaben
                </div>
 >


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

      list.innerHTML = (state.catalog || []).map(p => {
        const suppliers = Array.isArray(p.suppliers) ? p.suppliers : [];

        const supplierHtml = suppliers.length
          ? suppliers.map(s => {
              const dName = s.distributor_name || s.name || '—';
              const dPrice = toNum(s.distributor_price ?? s.unit_price, 0);

              const payload = b64EncodeJson({
                product_id: p.id,
                product_name: p.name,
                distributor_price_id: s.distributor_price_id,
                distributor_id: s.distributor_id,
                distributor_name: dName,
                unit_price: dPrice,
              });

              return `
                <button
                  onclick="ui.addFromCatalogEncoded('${payload}')"
                  class="supplier-btn"
                  style="border: 1px solid var(--border-color); background: #fdfdfd;">
                  
                  <span style="font-size:0.7rem; font-weight:900; color:var(--primary); text-transform:uppercase; margin-bottom: 2px;">
                    <i class="fas fa-building"></i> ${escapeHtml(dName)}
                  </span>

                  <span style="font-size:0.9rem; font-weight:900; color:var(--text-main);">
                    ${dPrice.toFixed(2)} €
                  </span>

                  <span style="font-size:0.65rem; font-weight:800; color:#94a3b8;">
                    Distributor Price
                  </span>
                </button>
              `;
            }).join('')
          : `<div style="grid-column:1/-1; padding:0.75rem; color:#94a3b8; font-weight:800;">
              Keine Lieferantenpreise gefunden
            </div>`;

        return `
          <div class="catalog-item">
            <div style="display:flex; justify-content:space-between; margin-bottom:0.75rem;">
              <h4 style="font-weight:900; color:var(--text-main);">${escapeHtml(p.name)}</h4>
              <span style="font-size:0.625rem; font-weight:900; color:#cbd5e1; text-transform:uppercase;">Artikel</span>
            </div>

            <div class="supplier-grid">
              ${supplierHtml}
            </div>
          </div>
        `;
      }).join('');

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
        base_unit_price: toNum(price, 0),     // NEW
        is_price_overridden: false,
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
        const modal = document.getElementById('modal-container');
        
        document.getElementById('modal-title').innerHTML = '<i class="fas fa-user-tag" style="color:var(--accent)"></i> <span>Qualifikation wählen</span>';
        
        const searchBox = document.getElementById('modal-search-box');
        const searchInput = document.getElementById('modal-search-input');
        searchBox.classList.remove('hidden'); 
        searchInput.value = '';
        searchInput.oninput = (e) => ui.renderLaborList(e.target.value);
        
        ui.renderLaborList('');
        modal.classList.remove('hidden'); 
    },

    // 2. Render the Options List (Qualifications)
    renderLaborList(search) {
        const term = search.toLowerCase();
        // Filter options based on search
        const list = (state.laborOptions || []).filter(q => (q.name || '').toLowerCase().includes(term));
        
        if (list.length === 0) {
            document.getElementById('modal-content').innerHTML = '<div style="padding:2rem;text-align:center;color:#ccc">Keine Qualifikationen gefunden</div>';
            return;
        }

        const content = list.map(q => {
            // FIX: Removed "window." from b64EncodeJson
            const payload = { 
                qualification_id: q.id, 
                name: q.name, 
                hourly_rate: q.default_price 
            };
            const json = b64EncodeJson(payload); 

            // FIX: Removed "window." from escapeHtml
            return `
            <div class="catalog-item labor-opt" 
                 onclick="ui.addLaborFromJson('${json}')" 
                 style="cursor:pointer; display:flex; justify-content:space-between; align-items:center;">
                
                <div style="display:flex; align-items:center; gap:1rem;">
                    <div style="width:48px; height:48px; background:rgba(147, 194, 28, 0.1); color:var(--accent); border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.2rem;">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div>
                        <div style="font-weight:900; font-size:1rem; color:var(--text-main);">${escapeHtml(q.name)}</div>
                        <div style="font-size:0.75rem; color:#94a3b8; font-weight:700;">Standardtarif</div>
                    </div>
                </div>

                <div style="text-align:right;">
                    <div style="font-weight:900; font-size:1.1rem; color:var(--text-main);">
                        ${parseFloat(q.default_price || 0).toLocaleString('de-DE', {style:'currency', currency:'EUR'})}
                    </div>
                    <div style="font-size:0.65rem; color:#cbd5e1; font-weight:800;">PRO STUNDE</div>
                </div>
            </div>`;
        }).join('');
        
        document.getElementById('modal-content').innerHTML = `<div style="display:flex; flex-direction:column; gap:0.75rem;">${content}</div>`;
    },

    // 3. Add to Set
    addLaborFromJson(encodedOrJson) {
        let l;
        try {
            // FIX: Removed "window." from b64DecodeJson
            l = encodedOrJson.startsWith('{') ? JSON.parse(encodedOrJson) : b64DecodeJson(encodedOrJson);
        } catch (e) { console.error(e); return; }

        state.editingSet.labor.push({
            qualification_id: l.qualification_id,
            name: l.name, 
            rate: parseFloat(l.hourly_rate || 0),
            hours: 1, 
            // Clear old fields to avoid confusion
            employee_id: null,
            employee_name: null,
            position_name: null
        });

        this.closeModal();
        this.recalculateLocalStats();
        this.render();
    },

    // 4. Render Table Rows (Editable Rate)
    renderLaborItems() {
        const body = document.getElementById('labor-body');
        if (!body) return;

        body.innerHTML = (state.editingSet.labor || []).map((l, idx) => {
            // FIX: Removed "window." from escapeHtml
            const title = l.name || l.position_name || 'Unbekannt'; 
            const subTitle = l.qualification_id ? 'Qualifikation' : (l.employee_name || 'Personal');

            return `
            <tr>
              <td>
                <div class="avatar-group">
                  <div class="avatar-wrap">
                    <div style="width:40px; height:40px; background:#f0f9ff; color:var(--primary); border-radius:12px; display:flex; align-items:center; justify-content:center; border:1px solid #e0f2fe;">
                        <i class="fas fa-user-tag"></i>
                    </div>
                  </div>
                  <div>
                    <div style="font-weight:900; color:var(--text-main); font-size:0.95rem;">${escapeHtml(title)}</div>
                    <p style="font-size:0.65rem; font-weight:700; color:#94a3b8; text-transform:uppercase;">
                      ${escapeHtml(subTitle)}
                    </p>
                  </div>
                </div>
              </td>

              <td style="text-align:center;">
                <span class="labor-percent-badge">${escapeHtml(l.percentOfTotal || '0')}%</span>
              </td>

              <td style="text-align:center;">
                 <div class="price-control" style="justify-content:center;">
                  <input 
                    type="number" 
                    step="0.50" 
                    class="price-input" 
                    style="width:55px; text-align:center;"
                    value="${parseFloat(l.rate).toFixed(2)}"
                    onchange="ui.updateLaborRate(${idx}, this.value)"
                  >
                  <span style="font-size:10px;">€/h</span>
                </div>
              </td>

              <td style="text-align:center;">
                <div class="qty-control" style="display:inline-flex;">
                  <input type="number" onchange="ui.updateLaborHours(${idx}, this.value)" class="qty-input" style="width:50px;"
                    value="${escapeHtml(l.hours)}">
                  <span style="font-size:10px; font-weight:900; color:#cbd5e1; padding-right:8px;">h</span>
                </div>
              </td>

              <td style="text-align:right; font-weight:900; color:var(--text-main);">
                ${(parseFloat(l.rate || 0) * parseFloat(l.hours || 0)).toLocaleString('de-DE', {style:'currency', currency:'EUR'})}
              </td>

              <td style="text-align:right;">
                <button onclick="ui.removeLabor(${idx})" class="btn-danger"><i class="fas fa-trash-alt"></i></button>
              </td>
            </tr>
          `;
        }).join('');
    },

    // 5. Update Rate Logic
    updateLaborRate(idx, val) {
        if (state.editingSet.labor[idx]) {
            state.editingSet.labor[idx].rate = parseFloat(val) || 0;
            this.recalculateLocalStats();
            this.render();
        }
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
      const comp = (sIdx === null)
        ? state.editingSet.components[mIdx]
        : state.editingSet.components[mIdx].subComponents[sIdx];

      if (!comp) return;

      comp.unit_price = n;

      // NEW: mark as overridden, but DON'T destroy the base price
      comp.is_price_overridden = (toNum(comp.base_unit_price, comp.unit_price) !== comp.unit_price);

      this.recalculateLocalStats();
      this.render();
    },


    async refreshPrice(mIdx, sIdx) {
        const comp = (sIdx === null)
          ? state.editingSet.components[mIdx]
          : state.editingSet.components[mIdx].subComponents[sIdx];

        if (!comp) return;

        // 1) Always restore from cached original distributor price immediately ✅
        const cached = toNum(comp.base_unit_price, null);
        if (cached !== null) {
          comp.unit_price = cached;
          comp.is_price_overridden = false;
          ui.recalculateLocalStats();
          ui.render();
          ui.showStatus('PREIS ZURÜCKGESETZT');
        }

        // 2) Optional: sync latest distributor price via a dedicated endpoint ✅
        if (!comp.distributor_price_id) return;

        const res = await api.getDistributorPrice(comp.distributor_price_id);
        const newPrice = toNum(res?.data?.unit_price, null);

        if (newPrice === null) return; // keep cached fallback

        // Update both base + current price to latest distributor price
        comp.base_unit_price = newPrice;
        comp.unit_price = newPrice;
        comp.is_price_overridden = false;

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

    // --- Modal close helpers (GLOBAL MODAL + PROMPT + PDF + LINK + TASKWIZ) ---
    ui.closeModal = function () {
      const el = document.getElementById('modal-container');
      if (!el) return;
      el.classList.add('hidden');

      // cleanup search input handler to avoid stale bindings
      const searchInput = document.getElementById('modal-search-input');
      if (searchInput) searchInput.oninput = null;

      state.pickerContext = null;
    };

    ui.closePrompt = function () {
      const el = document.getElementById('prompt-modal');
      if (!el) return;
      el.classList.add('hidden');

      const btn = document.getElementById('prompt-confirm-btn');
      if (btn) btn.onclick = null;
    };

    ui.closePdf = function () {
      document.getElementById('pdf-modal-container')?.classList.add('hidden');
    };

    ui.closeLink = function () {
      wizard.closeLinkModal();
    };

    // Close on backdrop click
    function bindBackdropClose(id, closeFn) {
      const el = document.getElementById(id);
      if (!el) return;
      el.addEventListener('mousedown', (e) => {
        // only close when clicking the backdrop, not inside window
        if (e.target === el) closeFn();
      });
    }

    bindBackdropClose('modal-container', () => ui.closeModal());
    bindBackdropClose('prompt-modal', () => ui.closePrompt());
    bindBackdropClose('pdf-modal-container', () => ui.closePdf());
    bindBackdropClose('link-modal', () => ui.closeLink());

    // Close on ESC
    document.addEventListener('keydown', (e) => {
      if (e.key !== 'Escape') return;
      ui.closeModal();
      ui.closePrompt();
      ui.closePdf();
      ui.closeLink();
      if (window.TaskWizard?.close) window.TaskWizard.close();
    });

    
    window.TaskWizard = (() => {
      const modal = () => document.getElementById('taskWizardModal');
      const panes = () => Array.from(document.querySelectorAll('.twiz-pane'));
      const steps = () => Array.from(document.querySelectorAll('.twiz-step'));
      const csrf  = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

      let step = 1;

      function open(){
        modal()?.classList.remove('d-none');
        if (document.getElementById('tw_activities')?.children.length === 0) addActivityRow();
        go(1);
      }

      function close(){
        modal()?.classList.add('d-none');
      }

      function go(n){
        step = n;
        panes().forEach(p => p.classList.toggle('d-none', String(p.dataset.pane) !== String(step)));
        steps().forEach(s => s.classList.toggle('active', String(s.dataset.step) === String(step)));

        const nextBtn = document.getElementById('tw_next_btn');
        if (!nextBtn) return;
        nextBtn.textContent = (step === 5) ? 'Speichern' : 'Weiter';
      }

      function prev(){
        if(step > 1) go(step - 1);
      }

      function next(){
        if(step === 1){
          const phaseName = document.getElementById('tw_phase_name')?.value.trim();
          if(!phaseName){ alert('Phasenname ist erforderlich.'); return; }

          const acts = readActivities();
          if(acts.length === 0){ alert('Bitte mindestens eine Aktivität hinzufügen.'); return; }

          go(2); return;
        }

        if(step === 2){
          if(getCheckedValues('.tw_stage_cb').length === 0){
            alert('Bitte mindestens eine Stage auswählen.');
            return;
          }
          go(3); return;
        }

        if(step === 3){
          if(getCheckedValues('.tw_section_cb').length === 0){
            alert('Bitte mindestens eine Sektion auswählen.');
            return;
          }
          go(4); return;
        }

        if(step === 4){
          if(getCheckedValues('.tw_product_cb').length === 0){
            alert('Bitte mindestens ein Produkt auswählen.');
            return;
          }
          buildReview();
          go(5); return;
        }

        if(step === 5){
          save();
        }
      }

      function getCheckedValues(sel){
        return Array.from(document.querySelectorAll(sel + ':checked')).map(x => x.value);
      }

      function addActivityRow(){
        const wrap = document.getElementById('tw_activities');
        if (!wrap) return;

        const idx = wrap.children.length + 1;

        const el = document.createElement('div');
        el.className = 'twiz-act';
        el.innerHTML = `
          <div class="d-flex justify-content-between align-items-center mb-2">
            <strong>Aktivität #${idx}</strong>
            <button type="button" class="btn btn-sm btn-outline-danger">Entfernen</button>
          </div>

          <div class="row mb-2" style="gap:10px;">
            <div style="flex:1;">
              <label>Titel *</label>
              <input class="form-control tw_a_title" placeholder="z.B. Baustelle vermessen" />
            </div>

            <div style="width:160px;">
              <label>Zeit (HH:MM)</label>
              <input class="form-control tw_a_duration" placeholder="01:30" />
            </div>
          </div>

          <div class="row mb-2" style="gap:10px;">
            <div style="flex:1;">
              <label>Beschreibung</label>
              <input class="form-control tw_a_description" placeholder="Optional" />
            </div>
          </div>

          <div class="row mb-2" style="gap:10px;">
            <div style="flex:1;">
              <label>Link</label>
              <input class="form-control tw_a_link" placeholder="https://..." />
            </div>

            <div style="width:220px; display:flex; align-items:end;">
              <label style="display:flex; gap:10px; align-items:center; margin:0;">
                <input type="checkbox" class="tw_a_photo_required" />
                <span>Foto erforderlich</span>
              </label>
            </div>
          </div>
        `;

        el.querySelector('button')?.addEventListener('click', () => {
          el.remove();
          renumberActivities();
        });

        wrap.appendChild(el);
      }

      function renumberActivities(){
        const acts = Array.from(document.querySelectorAll('#tw_activities .twiz-act'));
        acts.forEach((a, i) => {
          const strong = a.querySelector('strong');
          if (strong) strong.textContent = `Aktivität #${i+1}`;
        });
      }

      function readActivities(){
        const acts = Array.from(document.querySelectorAll('#tw_activities .twiz-act'));
        const out = [];

        for(const a of acts){
          const title = a.querySelector('.tw_a_title')?.value.trim();
          if(!title) continue;

          const photoChecked = !!a.querySelector('.tw_a_photo_required')?.checked;

          out.push({
            title,
            duration: a.querySelector('.tw_a_duration')?.value.trim() || null,      // Zeit
            description: a.querySelector('.tw_a_description')?.value.trim() || null,
            link: a.querySelector('.tw_a_link')?.value.trim() || null,
            photo_required: photoChecked ? 'needed' : 'off',
          });
        }
        return out;
      }

      function filterProducts(q){
        q = (q || '').toLowerCase().trim();
        document.querySelectorAll('.twiz-product').forEach(el => {
          const name = el.getAttribute('data-name') || '';
          el.style.display = (!q || name.includes(q)) ? '' : 'none';
        });
      }

      function buildReview(){
        const phase_name  = document.getElementById('tw_phase_name')?.value.trim();
        const activities  = readActivities();
        const stage_ids   = getCheckedValues('.tw_stage_cb');
        const section_keys= getCheckedValues('.tw_section_cb');
        const product_ids = getCheckedValues('.tw_product_cb');

        const neededPhotos = activities.filter(a => a.photo_required === 'needed').length;

        document.getElementById('tw_review').innerHTML = `
          <div><strong>Phase:</strong> ${escapeHtml(phase_name)}</div>
          <div class="mt-2"><strong>Aktivitäten:</strong> ${activities.length} <small class="text-muted">(Foto erforderlich: ${neededPhotos})</small></div>
          <div><strong>Stages:</strong> ${stage_ids.length}</div>
          <div><strong>Sektionen:</strong> ${section_keys.join(', ')}</div>
          <div><strong>Produkte:</strong> ${product_ids.length}</div>
          <hr/>
          <div class="text-muted">
            Es werden Phasen je Kombination (Produkt × Sektion × Stage) erstellt und die Aktivitäten übernommen.
          </div>
        `;
        document.getElementById('tw_save_result').innerHTML = '';
      }

      function escapeHtml(s){
        return String(s || '')
          .replaceAll('&','&amp;').replaceAll('<','&lt;')
          .replaceAll('>','&gt;').replaceAll('"','&quot;')
          .replaceAll("'","&#039;");
      }

      async function save(){
        const btn = document.getElementById('tw_next_btn');
        if (btn) {
          btn.disabled = true;
          btn.textContent = 'Speichern...';
        }

        const body = {
          product_ids: getCheckedValues('.tw_product_cb').map(Number),
          stage_ids: getCheckedValues('.tw_stage_cb').map(Number),
          section_keys: getCheckedValues('.tw_section_cb'),

          phase_name: document.getElementById('tw_phase_name')?.value.trim(),
          activities: readActivities()
        };

        try{
          const res = await fetch(`{{ route('taskWizard.apply') }}`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrf(),
              'Accept': 'application/json'
            },
            body: JSON.stringify(body)
          });

          const data = await res.json();

          if(!res.ok){
            const msg = data?.message || 'Speichern fehlgeschlagen.';
            document.getElementById('tw_save_result').innerHTML =
              `<div class="alert alert-danger">${escapeHtml(msg)}</div>`;
            if (btn) { btn.disabled = false; btn.textContent = 'Speichern'; }
            return;
          }

          document.getElementById('tw_save_result').innerHTML =
            `<div class="alert alert-success">
              Gespeichert ✅ Erstellt: <strong>${data.created?.phases ?? 0}</strong> Phasen,
              <strong>${data.created?.activities ?? 0}</strong> Aktivitäten
            </div>`;

          // ✅ Kein Full-Reload nötig (optional: Wizard-Tree refreshen)
          setTimeout(() => {
            close();
            // optional: wenn du willst den Wizard/Tree refresh:
            // if (window.wizard?.init) wizard.init();
          }, 700);

        } catch(e){
          document.getElementById('tw_save_result').innerHTML =
            `<div class="alert alert-danger">Unerwarteter Fehler: ${escapeHtml(e.message)}</div>`;
          if (btn) { btn.disabled = false; btn.textContent = 'Speichern'; }
        }
      }

      return { open, close, next, prev, addActivityRow, filterProducts };
    })();

  // ===========================================================================
  // Expose to window for inline onclick handlers
  // ===========================================================================
  window.state = state;
  window.api = api;
  window.app = app;
  window.ui = ui;  

  window.wizard = wizard;
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