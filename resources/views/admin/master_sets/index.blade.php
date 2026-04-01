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
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js for Beautiful Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- html2pdf for PDF Generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
      <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
      <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
      <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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

        /* Hide arrows for Chrome, Safari, Edge, Opera */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
          -webkit-appearance: none;
          margin: 0;
        }

        .material-scroll-wrap{
            position: relative;
            max-height: 72vh;
            overflow: auto;
            overscroll-behavior: contain;
            scroll-behavior: auto;
          }

          .material-scroll-zone{
            position: sticky;
            left: 0;
            right: 0;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 25;
            pointer-events: none;
            opacity: 0;
            transition: opacity .15s ease;
          }

          .material-scroll-zone.top{
            top: 0;
            margin-bottom: -42px;
            background: linear-gradient(to bottom, rgba(255,255,255,.96), rgba(255,255,255,0));
          }

          .material-scroll-zone.bottom{
            bottom: 0;
            margin-top: -42px;
            background: linear-gradient(to top, rgba(255,255,255,.96), rgba(255,255,255,0));
          }

          .material-scroll-zone.active{
            opacity: 1;
          }

          .material-scroll-zone .scroll-chip{
            width: 34px;
            height: 34px;
            border-radius: 999px;
            background: white;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-md);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
          }

        /* Hide arrows for Firefox */
        input[type="number"] {
          -moz-appearance: textfield;
          appearance: textfield;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            padding-bottom: 2rem;
        }

        .material-table-wrap{
          background:#fff;
          border:1px solid var(--border-color);
          border-radius:var(--radius-2xl);
          overflow-x:auto;
          overflow-y:hidden;
          box-shadow:var(--shadow-sm);
          -webkit-overflow-scrolling:touch;
        }

        .material-table{
          width:100%;
          min-width:1100px;
          border-collapse:collapse;
          table-layout:auto;
        }

        .material-table th:nth-child(3),
        .material-table td:nth-child(3){
          min-width:260px;
          width:auto;
        }

        .material-title-text,
        .material-sub-text{
          display:block;
          min-width:0;
          max-width:100%;
          overflow:hidden;
          text-overflow:ellipsis;
          white-space:nowrap;
        }
        .material-table thead th{
          background:#f8fafc;
          border-bottom:1px solid var(--border-color);
          padding:0.9rem 0.75rem;
          font-size:10px;
          font-weight:900;
          text-transform:uppercase;
          letter-spacing:.08em;
          color:#94a3b8;
          text-align:left;
        }

        .material-table tbody td{
          padding:0.75rem;
          border-bottom:1px solid #f1f5f9;
          vertical-align:middle;
          font-size:0.8rem;
        }

        .material-row-main{
          background:#fff;
        }

        .material-row-main:hover{
          background:#fcfdff;
        }

        .material-row-sub{
          background:#f8fafc;
        }

        .material-row-sub td{
          font-size:0.75rem;
        }

        .material-row-hidden{
          display:none;
        }


        /* --- PDF Report Styles --- */
      .pdf-report-root{
          width: 210mm;
          background: #eef2f7;
          padding: 0;
      }

      .pdf-print-page{
          width: 210mm;
          min-height: 297mm;
          background: white;
          color: #1e293b;
          font-family: 'Inter', sans-serif;
          position: relative;
          padding: 16mm 14mm 24mm 14mm; /* extra bottom space for footer */
          page-break-after: always;
          break-after: page;
          overflow: hidden;
      }

      .pdf-print-page:last-child{
          page-break-after: auto;
          break-after: auto;
      }

      .pdf-header{
          display:flex;
          justify-content:space-between;
          align-items:flex-start;
          gap:1rem;
          border-bottom:2px solid var(--primary);
          padding-bottom:1rem;
          margin-bottom:1.25rem;
      }

      .pdf-brand{
          font-size:1.8rem;
          font-weight:900;
          letter-spacing:-0.04em;
          color:#0f172a;
      }

      .pdf-meta{
          text-align:right;
      }

      .pdf-meta h2{
          font-size:.8rem;
          font-weight:800;
          color:#64748b;
          text-transform:uppercase;
          margin-bottom:.2rem;
      }

      .pdf-meta p{
          font-size:1rem;
          font-weight:900;
      }

      .pdf-cover-title{
          font-size:2rem;
          font-weight:900;
          line-height:1.2;
          margin-bottom:.4rem;
          color:#0f172a;
      }

      .pdf-cover-desc{
          color:#64748b;
          font-size:.92rem;
          line-height:1.6;
      }

      .pdf-summary-grid{
          display:grid;
          grid-template-columns: 68mm 1fr;
          gap:1rem;
          margin-top:1.25rem;
          align-items:center;
      }

      .pdf-stats-card{
          background:#f8fafc;
          padding:1rem;
          border-radius:14px;
          border:1px solid #e2e8f0;
      }

      .pdf-stat-row{
          display:flex;
          justify-content:space-between;
          gap:1rem;
          margin-bottom:.75rem;
          padding-bottom:.75rem;
          border-bottom:1px solid #e2e8f0;
      }

      .pdf-stat-row:last-child{
          border-bottom:none;
          margin-bottom:0;
          padding-bottom:0;
      }

      .pdf-stat-label{
          font-size:.75rem;
          font-weight:700;
          color:#64748b;
      }

      .pdf-stat-val{
          font-size:.92rem;
          font-weight:900;
          color:#0f172a;
          text-align:right;
      }

      .pdf-section-title{
          font-size:.9rem;
          font-weight:900;
          text-transform:uppercase;
          color:var(--primary);
          border-bottom:1px solid #e2e8f0;
          padding-bottom:.4rem;
          margin-bottom:.75rem;
          margin-top:0;
      }

      .pdf-section-subtitle{
          font-size:.72rem;
          font-weight:700;
          color:#64748b;
          margin-bottom:.75rem;
      }

      .pdf-block{
          margin-bottom:1rem;
          break-inside: avoid;
          page-break-inside: avoid;
      }

      .pdf-table{
          width:100%;
          border-collapse:collapse;
          font-size:.74rem;
          table-layout:fixed;
      }

      .pdf-table th{
          text-align:left;
          padding:.55rem .5rem;
          background:#f1f5f9;
          font-weight:900;
          color:#475569;
          border-bottom:1px solid #dbe3ec;
      }

      .pdf-table td{
          padding:.5rem;
          border-bottom:1px solid #e2e8f0;
          vertical-align:top;
          word-break:break-word;
      }

      .pdf-table tr{
          break-inside: avoid;
          page-break-inside: avoid;
      }

      .pdf-table tr:last-child td{
          border-bottom:none;
      }

      .pdf-pos-badge{
          display:inline-flex;
          align-items:center;
          justify-content:center;
          min-width:24px;
          height:24px;
          padding:0 6px;
          border-radius:999px;
          background:#eaf4fb;
          color:var(--primary);
          font-size:.68rem;
          font-weight:900;
      }

      .pdf-sub-pos-badge{
          display:inline-flex;
          align-items:center;
          justify-content:center;
          min-width:30px;
          height:20px;
          padding:0 6px;
          border-radius:999px;
          background:#fff;
          border:1px solid #dbe3ec;
          color:#64748b;
          font-size:.62rem;
          font-weight:900;
      }

      .pdf-check{
          font-size:.95rem;
          font-weight:900;
          color:var(--accent);
          line-height:1;
      }

      .pdf-main-row{
          background:#fff;
      }

      .pdf-sub-row{
          background:#f8fafc;
      }

      .pdf-text-muted{
          color:#64748b;
      }

      .pdf-task-stage{
          margin-top:1rem;
          margin-bottom:.5rem;
          font-size:.8rem;
          font-weight:900;
          color:#0f172a;
          display:flex;
          justify-content:space-between;
          gap:1rem;
      }

      .pdf-total-box{
          margin-top:1rem;
          text-align:right;
          background:#f0f7ff;
          padding:1rem 1.25rem;
          border-radius:14px;
          break-inside: avoid;
          page-break-inside: avoid;
      }

      .pdf-total-label{
          font-size:.75rem;
          font-weight:800;
          color:var(--primary);
          text-transform:uppercase;
      }

      .pdf-total-val{
          font-size:1.8rem;
          font-weight:900;
          color:#0f172a;
          line-height:1.1;
          margin-top:.35rem;
      }

      .pdf-page-break{
          page-break-before: always;
          break-before: page;
      }

      .pdf-footer-note{
          position:absolute;
          left:14mm;
          right:14mm;
          bottom:8mm;
          border-top:1px solid #dbe3ec;
          padding-top:3mm;
          display:flex;
          justify-content:space-between;
          gap:1rem;
          font-size:.62rem;
          font-weight:700;
          color:#94a3b8;
      }

        .material-cell-center{
          text-align:center;
        }

        .material-cell-right{
          text-align:right;
        }

        .material-drag{
          color:#cbd5e1;
          cursor:grab;
          width:20px;
          text-align:center;
        }

        .material-drag:active{
          cursor:grabbing;
          color:var(--primary);
        }

        .material-collapse-btn{
          width:28px;
          height:28px;
          border:none;
          background:#f8fafc;
          border:1px solid #e2e8f0;
          border-radius:8px;
          cursor:pointer;
          color:#64748b;
          display:inline-flex;
          align-items:center;
          justify-content:center;
        }

        .material-collapse-btn:hover{
          border-color:var(--primary);
          color:var(--primary);
          background:#fff;
        }

        .material-title-block{
          display:flex;
          align-items:center;
          gap:.65rem;
          min-width:0;
        }

        .material-number-badge{
          min-width:28px;
          height:28px;
          display:inline-flex;
          align-items:center;
          justify-content:center;
          border-radius:999px;
          background:var(--primary-light);
          color:var(--primary);
          font-size:0.7rem;
          font-weight:900;
          flex-shrink:0;
        }

        .material-sub-badge{
          min-width:38px;
          height:22px;
          display:inline-flex;
          align-items:center;
          justify-content:center;
          border-radius:999px;
          background:#fff;
          border:1px solid #e2e8f0;
          color:#64748b;
          font-size:0.65rem;
          font-weight:900;
          flex-shrink:0;
        }

        .material-title-text{
          min-width:0;
          overflow:hidden;
          text-overflow:ellipsis;
          white-space:nowrap;
          font-weight:900;
          color:var(--text-main);
        }

        .material-sub-text{
          min-width:0;
          overflow:hidden;
          text-overflow:ellipsis;
          white-space:nowrap;
          color:#475569;
          font-weight:800;
        }

        .material-inline-stack{
          display:flex;
          flex-wrap:wrap;
          gap:.4rem;
          align-items:center;
        }

        .material-actions{
          display:flex;
          align-items:center;
          gap:.35rem;
          flex-wrap:wrap;
          justify-content:flex-end;
        }

        .material-add-sub-row td{
          background:#fff;
          padding:0;
        }

        .material-add-sub-btn{
          width:100%;
          padding:0.7rem 1rem;
          border:none;
          background:#fff;
          color:var(--primary);
          font-size:10px;
          font-weight:900;
          text-transform:uppercase;
          letter-spacing:.08em;
          cursor:pointer;
          border-top:1px solid #f8fafc;
        }

        .material-add-sub-btn:hover{
          background:#f8fafc;
        }

       @media (max-width: 1400px){
          .material-table{
            min-width:1100px;
          }
        }

        @media (max-width: 900px){
          .material-table{
            min-width:980px;
          }
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
        .material-actions-menu{
          position:relative;
          display:flex;
          justify-content:center;
        }

        .material-menu-btn{
          width:30px;
          height:30px;
          border:1px solid #e2e8f0;
          background:#fff;
          border-radius:8px;
          display:inline-flex;
          align-items:center;
          justify-content:center;
          cursor:pointer;
          color:#94a3b8;
          transition:all .2s;
        }

        .material-menu-btn:hover{
          color:var(--primary);
          border-color:var(--primary);
        }

        .material-menu-dropdown{
          position:absolute;
          top:calc(100% + 6px);
          right:0;
          min-width:180px;
          background:#fff;
          border:1px solid var(--border-color);
          border-radius:12px;
          box-shadow:var(--shadow-xl);
          padding:6px;
          z-index:50;
          display:none;
        }

        .material-actions-menu.open .material-menu-dropdown{
          display:block;
        }

        .material-menu-item{
          width:100%;
          border:none;
          background:transparent;
          display:flex;
          align-items:center;
          gap:10px;
          padding:10px 12px;
          border-radius:10px;
          cursor:pointer;
          font-size:0.75rem;
          font-weight:800;
          color:var(--text-main);
          text-align:left;
        }

        .material-menu-item:hover{
          background:#f8fafc;
        }

        .material-menu-item.danger{
          color:var(--danger);
        }

        .material-menu-item.danger:hover{
          background:var(--danger-light);
        }

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

        /* --- View Toggle Switcher --- */
        .view-toggle-group {
            display: flex;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 2px;
            margin-right: 1rem;
        }
        .view-btn {
            border: none;
            background: transparent;
            color: var(--text-light);
            padding: 6px 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.9rem;
        }
        .view-btn:hover { color: var(--primary); }
        .view-btn.active {
            background: var(--primary-light);
            color: var(--primary);
            font-weight: bold;
        }

        /* --- List View Overrides --- */
        .list-layout {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .list-layout .card-group {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            margin-bottom: 0;
            gap: 1.5rem;
            min-height: auto;
            /* Reset transform on hover for list to keep it subtle */
            transform: none !important; 
        }

        .list-layout .group-icon {
            margin-bottom: 0;
            width: 48px; 
            height: 48px;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .list-layout .group-title {
            margin-bottom: 0;
            font-size: 1.1rem;
            flex: 1; /* Takes available space */
        }

        .list-layout .group-meta {
            margin-right: 2rem;
            font-size: 0.75rem;
        }

        /* Reposition the settings button in list view */
        .list-layout .card-settings-btn {
            position: static; /* Remove absolute positioning */
            background: transparent;
            border-color: transparent;
            width: auto;
            height: auto;
        }
        .list-layout .card-settings-btn:hover {
            background: #f1f5f9;
            color: var(--primary);
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

        .editor-grid { display: grid; grid-template-columns: 15fr 4fr; gap: 2rem; }
        .section-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.5rem; }
        .section-title { font-size: 1.25rem; font-weight: 900; display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem; }
        .section-subtitle { font-size: 0.75rem; font-weight: 700; color: var(--text-light); }

        .sidebar-sticky-wrapper {
            position: sticky;
            top: 100px; /* Adjust this to match your header height */
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            height: fit-content;
            place-items:end;
        }
        /* --- Sidebar Summary --- */
        .summary-card {
            background: white;
            padding: 2rem;
            border-radius: var(--radius-2xl);
            border: 1px solid var(--border-color);
            position: static !important;
            top: 200px;
            box-shadow: var(--shadow-xl);
            width:309px;
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
            width: 100%; max-width: 933px;
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

        #distributor-compare-modal .modal-body {
            align-items: stretch;
        }

        #distributor-compare-summary {
            min-height: 120px;
        }

        #distributor-compare-modal .compare-chart-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1rem;
            margin-top: 1rem;
            min-height: 360px;
            display: flex;
            flex-direction: column;
        }

        #distributor-compare-modal .compare-chart-head {
            font-weight: 900;
            margin-bottom: 0.75rem;
            flex: 0 0 auto;
        }

        #distributor-compare-modal .compare-chart-wrap {
            position: relative;
            flex: 1 1 auto;
            min-height: 280px;
            height: 320px;
            width: 100%;
        }

        #distributor-compare-chart {
            display: block;
            width: 100% !important;
            height: 100% !important;
            max-height: 320px;
        }

        @media (max-width: 1024px) {
            #distributor-compare-modal .modal-body {
                grid-template-columns: 1fr !important;
            }

            #distributor-compare-modal .compare-chart-card {
                min-height: 300px;
            }

            #distributor-compare-modal .compare-chart-wrap {
                min-height: 240px;
                height: 260px;
            }

            #distributor-compare-chart {
                max-height: 260px;
            }
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
      .twiz-step.active{background:#74b2d4;color:#fff}
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

      .material-scroll-wrap{
      position: relative;
      max-height: 72vh;
      overflow: auto;
      overscroll-behavior: contain;
      scroll-behavior: auto;
    }

    /* sticky header for material tab */
    .material-sticky-head{
      position: sticky;
      top: 0;
      z-index: 35;
      background: #f8fafc;
      border-bottom: 1px solid #e5e7eb;
      box-shadow: 0 2px 0 rgba(255,255,255,.9);
    }

    .material-sticky-head::after{
      content: "";
      position: absolute;
      left: 0;
      right: 0;
      bottom: -1px;
      height: 1px;
      background: #e5e7eb;
    }

    .material-sticky-head .mat-head-row{
      min-width: max-content;
    }

    /* optional so body rows never paint over header */
    #material-main-body{
      position: relative;
      z-index: 1;
    }

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

    /* =========================
      PDF / PRINT
    ========================= */

    .pdf-preview-root{
      width:100%;
      display:flex;
      flex-direction:column;
      align-items:center;
      gap:18px;
    }

    .pdf-report-root{
      width:210mm;
      max-width:210mm;
      background:transparent;
      padding:0;
    }

    .pdf-print-page{
      width:210mm;
      height:297mm;
      min-height:297mm;
      max-height:297mm;
      background:#fff;
      color:#1e293b;
      font-family:'Inter', sans-serif;
      position:relative;
      padding:12mm 12mm 18mm 12mm;
      overflow:hidden;
      box-shadow: var(--shadow-xl);
      page-break-after:always;
      break-after:page;
    }

    .pdf-print-page:last-child{
      page-break-after:auto;
      break-after:auto;
    }

    .pdf-header{
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap:10mm;
      border-bottom:1px solid #dbe3ec;
      padding-bottom:4mm;
      margin-bottom:5mm;
    }

    .mat-toggle-icon{
      height: 30px;
      border: 1px solid #e5e7eb;
      border-radius: 999px;
      background: #fff;
      color: #94a3b8;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 0 10px;
      font-size: 11px;
      font-weight: 800;
      cursor: pointer;
      transition: all .18s ease;
    }

    .mat-toggle-icon:hover{
      border-color: #74b2d4;
      color: #74b2d4;
      background: #f8fbfe;
    }

    .mat-toggle-icon.active.favorite{
      color: #f59e0b;
      border-color: rgba(245,158,11,.28);
      background: rgba(245,158,11,.08);
    }

    .mat-toggle-icon.active.stamm{
      color: #74b2d4;
      border-color: rgba(116,178,212,.28);
      background: rgba(116,178,212,.10);
    }

    .mat-toggle-icon:disabled{
      opacity: .5;
      cursor: not-allowed;
    }

    .pdf-brand{
      font-size:16pt;
      font-weight:900;
      line-height:1.1;
      color:#0f172a;
    }

    .pdf-meta{
      text-align:right;
      flex:0 0 auto;
    }

    .pdf-meta h2{
      font-size:8pt;
      font-weight:800;
      color:#64748b;
      text-transform:uppercase;
      margin:0 0 2mm 0;
    }

    .pdf-meta p{
      font-size:10pt;
      font-weight:900;
      margin:0;
    }

    .pdf-cover-title{
      font-size:18pt;
      font-weight:900;
      line-height:1.15;
      color:#0f172a;
      margin:0 0 2mm 0;
    }

    .pdf-cover-desc{
      font-size:8.5pt;
      line-height:1.45;
      color:#64748b;
      margin:0;
    }

    .pdf-cover-box{
      background:#f8fafc;
      border:1px solid #e2e8f0;
      border-radius:4mm;
      padding:4mm;
      margin-bottom:5mm;
    }

    .pdf-summary-grid{
      display:grid;
      grid-template-columns:54mm 1fr;
      gap:5mm;
      align-items:start;
      margin-bottom:5mm;
    }

    .pdf-chart-card,
    .pdf-stats-card{
      background:#f8fafc;
      border:1px solid #e2e8f0;
      border-radius:4mm;
      padding:4mm;
    }

    .pdf-chart-card{
      height:54mm;
      display:flex;
      align-items:center;
      justify-content:center;
    }

    .pdf-chart-card canvas{
      max-width:100%;
      max-height:100%;
    }

    .pdf-stat-row{
      display:flex;
      justify-content:space-between;
      gap:6mm;
      padding:2.2mm 0;
      border-bottom:1px solid #e2e8f0;
    }

    .pdf-stat-row:last-child{
      border-bottom:none;
    }

    .pdf-stat-label{
      font-size:7.5pt;
      font-weight:700;
      color:#64748b;
    }

    .pdf-stat-val{
      font-size:8pt;
      font-weight:900;
      color:#0f172a;
      text-align:right;
    }

    .pdf-section-title{
      font-size:9pt;
      font-weight:900;
      text-transform:uppercase;
      color:#0f172a;
      letter-spacing:.03em;
      margin:0 0 3mm 0;
      padding-bottom:2mm;
      border-bottom:1px solid #dbe3ec;
    }

    .pdf-section-subtitle{
      font-size:7pt;
      font-weight:700;
      color:#64748b;
      margin:0 0 3mm 0;
    }

    .pdf-block{
      margin-bottom:4mm;
      break-inside:avoid;
      page-break-inside:avoid;
    }

    .pdf-table{
      width:100%;
      border-collapse:collapse;
      table-layout:fixed;
      font-size:7pt;
    }

    .pdf-table th{
      text-align:left;
      padding:2.2mm 1.8mm;
      background:#f1f5f9;
      color:#475569;
      font-weight:900;
      border-bottom:1px solid #dbe3ec;
    }

    .pdf-table td{
      padding:2mm 1.8mm;
      border-bottom:1px solid #e2e8f0;
      vertical-align:top;
      word-break:break-word;
    }

    .pdf-table tr:last-child td{
      border-bottom:none;
    }

    .pdf-main-row{ background:#fff; }
    .pdf-sub-row{ background:#f8fafc; }

    .pdf-pos-badge,
    .pdf-sub-pos-badge{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      min-width:18px;
      height:18px;
      padding:0 4px;
      border-radius:999px;
      font-size:6.4pt;
      font-weight:900;
    }

    .pdf-pos-badge{
      background:#eaf4fb;
      color:var(--primary);
    }

    .pdf-sub-pos-badge{
      background:#fff;
      border:1px solid #dbe3ec;
      color:#64748b;
    }

    .pdf-check{
      font-size:8pt;
      font-weight:900;
      color:var(--accent);
    }

    .pdf-text-muted{
      color:#64748b;
    }

    .pdf-task-stage{
      display:flex;
      justify-content:space-between;
      gap:6mm;
      align-items:center;
      margin:4mm 0 2mm 0;
      font-size:7.5pt;
      font-weight:900;
      color:#0f172a;
    }

    .pdf-total-box{
      margin-top:4mm;
      background:#f0f7ff;
      border:1px solid #d8e8f6;
      border-radius:4mm;
      padding:4mm;
      text-align:right;
    }

    .pdf-total-label{
      font-size:7pt;
      font-weight:800;
      color:var(--primary);
      text-transform:uppercase;
    }

    .pdf-total-val{
      margin-top:2mm;
      font-size:17pt;
      font-weight:900;
      color:#0f172a;
      line-height:1;
    }

    .pdf-footer-note{
      position:absolute;
      left:12mm;
      right:12mm;
      bottom:6mm;
      border-top:1px solid #dbe3ec;
      padding-top:2mm;
      display:flex;
      justify-content:space-between;
      gap:6mm;
      font-size:6.5pt;
      font-weight:700;
      color:#94a3b8;
    }

    @media print{
      body *{
        visibility:hidden !important;
      }

      #pdf-modal-container,
      #pdf-modal-container *{
        visibility:visible !important;
      }

      #pdf-modal-container{
        position:static !important;
        inset:auto !important;
        background:#fff !important;
        padding:0 !important;
        display:block !important;
      }

      #pdf-modal-container .modal-window,
      #pdf-modal-container .modal-header{
        display:none !important;
      }

      #pdf-modal-container .modal-body{
        background:#fff !important;
        padding:0 !important;
        overflow:visible !important;
        display:block !important;
      }

      .pdf-preview-root{
        gap:0 !important;
      }

      .pdf-print-page{
        box-shadow:none !important;
        margin:0 !important;
      }
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

    <style>
      .mat-ctrl{
        height: 34px;
        min-height: 34px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #fff;
        color: #334155;
        font-size: 12px;
        font-weight: 700;
        outline: none;
        box-shadow: none;
        transition: all .18s ease;
      }

      .mat-ctrl:focus{
        border-color: #74b2d4;
        box-shadow: 0 0 0 3px rgba(116,178,212,.14);
      }

      .mat-ctrl[readonly],
      .mat-ctrl:disabled{
        background: #f8fafc;
        color: #64748b;
        cursor: not-allowed;
      }

      .mat-input{
        width: 100%;
        padding: 0 10px;
      }

      .mat-input-center{
        text-align: center;
        padding: 0 8px;
      }

      .mat-input-right{
        text-align: right;
        padding: 0 10px;
      }

      .mat-btn{
        height: 34px;
        min-width: 34px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #fff;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all .18s ease;
      }

      .material-grid-scroll{
        position: relative;
        overflow: auto;           /* one scroll container for x + y */
        max-height: 72vh;
        background: #fff;
      }

      .material-grid-inner{
        min-width: max-content;   /* header + body use same width */
        width: max-content;
      }

      .material-sticky-head{
        position: sticky;
        top: 0;
        z-index: 40;
        background: rgba(255,255,255,.96);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid #e5e7eb;
        box-shadow: 0 4px 14px rgba(15,23,42,.06);
      }

      .mat-head-row{
        min-width: max-content;
      }

      .mat-data-grid{
        min-width: max-content;
        align-items: stretch;
      }

      .mat-btn:hover{
        border-color: #74b2d4;
        color: #74b2d4;
        background: #f8fbfe;
      }

      .mat-btn:disabled{
        opacity: .45;
        cursor: not-allowed;
      }

      .mat-btn-icon{
        width: 34px;
        padding: 0;
      }

      .mat-addon-wrap{
        display: flex;
        align-items: center;
        height: 34px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #fff;
        overflow: hidden;
      }

      .mat-addon-wrap:focus-within{
        border-color: #74b2d4;
        box-shadow: 0 0 0 3px rgba(116,178,212,.14);
      }

      .mat-addon-input{
        border: 0;
        outline: 0;
        background: transparent;
        height: 100%;
        width: 100%;
        padding: 0 10px;
        font-size: 12px;
        font-weight: 700;
        color: #334155;
      }

      .mat-addon-text{
        height: 100%;
        padding: 0 10px 0 0;
        display: inline-flex;
        align-items: center;
        font-size: 11px;
        font-weight: 800;
        color: #94a3b8;
        white-space: nowrap;
      }

      .mat-stepper{
        display: flex;
        align-items: center;
        height: 34px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #fff;
        overflow: hidden;
      }

      .mat-stepper .step-btn{
        width: 32px;
        height: 32px;
        border: 0;
        background: transparent;
        color: #64748b;
        cursor: pointer;
      }

      .mat-stepper .step-btn:hover{
        background: #f8fafc;
        color: #74b2d4;
      }

      .mat-stepper .step-input{
        width: 42px;
        height: 100%;
        border: 0;
        outline: 0;
        text-align: center;
        font-size: 12px;
        font-weight: 800;
        background: transparent;
        color: #0f172a;
      }

      .mat-chip{
        height: 34px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #fff;
        display: inline-flex;
        align-items: center;
        padding: 0 10px;
        font-size: 12px;
        font-weight: 700;
        color: #334155;
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
                <div id="pdf-report-content" class="pdf-preview-root">
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
          <div class="twiz-step" data-step="1">1) Phase + Schritt</div>
          <div class="twiz-step" data-step="2">2) Stages</div>
          <div class="twiz-step" data-step="3">3) Phasen-Leistung</div>
          <div class="twiz-step" data-step="4">4) Artikelgruppen</div>
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
                'complete'    => 'Komplett',
                'montage'     => 'Montage',
                'repair'      => 'Reparatur',
                'maintenance' => 'Wartung',
                'plan'        => 'Planung',
                'product'     => 'Verkauf',
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

    <div id="material-desc-modal" class="modal-backdrop hidden" style="z-index: 370;">
      <div class="modal-window fade-in" style="max-width: 980px; height: 88vh;">
        <div class="modal-header">
          <h3 class="modal-title" style="display:flex; align-items:center; gap:.75rem;">
            <i class="fas fa-align-left" style="color:var(--primary)"></i>
            <span id="material-desc-modal-title">Beschreibung</span>
          </h3>

          <div style="display:flex; gap:.5rem;">
            <button class="btn btn-primary" onclick="materialDescUI.save()">
              <i class="fas fa-save"></i> Speichern
            </button>
            <button class="btn btn-icon" onclick="materialDescUI.close()">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>

        <div style="padding:1rem; border-bottom:1px solid var(--border-color); background:white;">
          <div id="material-desc-modal-subtitle" style="font-weight:900; color:#0f172a;"></div>
          <div style="font-size:12px; font-weight:700; color:#94a3b8; margin-top:4px;">
            HTML Beschreibung mit Quill Editor
          </div>
        </div>

        <div class="modal-body" style="padding:0; background:white; height:100%;">
          <div id="material-desc-quill" style="height:100%; min-height:520px;"></div>
        </div>
      </div>
    </div>

    <div id="set-desc-modal" class="modal-backdrop hidden" style="z-index: 365;">
    <div class="modal-window fade-in" style="max-width: 980px; height: 88vh;">
      <div class="modal-header">
        <h3 class="modal-title" style="display:flex; align-items:center; gap:.75rem;">
          <i class="fas fa-file-signature" style="color:var(--primary)"></i>
          <span>Set Beschreibung</span>
        </h3>

        <div style="display:flex; gap:.5rem;">
          <button class="btn btn-accent" onclick="setDescUI.autoWrite()">
            <i class="fas fa-wand-magic-sparkles"></i> Auto Write
          </button>

          <button class="btn btn-primary" onclick="setDescUI.save()">
            <i class="fas fa-save"></i> Speichern
          </button>

          <button class="btn btn-icon" onclick="setDescUI.close()">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>

      <div style="padding:1rem; border-bottom:1px solid var(--border-color); background:white;">
        <div style="display:flex; justify-content:space-between; gap:1rem; align-items:center; flex-wrap:wrap;">
          <div>
            <div style="font-weight:900; color:#0f172a;" id="set-desc-product-line">
              ${state?.editingSet?.name ? 'MasterSet: ' + escapeHtml(state.editingSet.name) : 'MasterSet Beschreibung'}
            </div>
            <div style="font-size:12px; font-weight:700; color:#94a3b8;" id="set-desc-meta-hint">
              Beschreibung für Angebot / Ausgabe
            </div>
          </div>

          <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
            <button class="btn btn-secondary" onclick="setDescUI.insertMaterialSummary()">
              <i class="fas fa-list"></i> Materialliste einfügen
            </button>

            <button class="btn btn-secondary" onclick="setDescUI.clear()">
              <i class="fas fa-eraser"></i> Leeren
            </button>
          </div>
        </div>
      </div>

      <div class="modal-body" style="padding:0; background:white; height:100%;">
        <div id="set-desc-quill" style="height:100%; min-height:500px;"></div>
      </div>
    </div>
  </div>


      <div id="task-qual-modal" class="modal-backdrop hidden" style="z-index: 400;">
        <div class="modal-window fade-in" style="max-width: 500px; height: auto; max-height: 85vh; display:flex; flex-direction:column;">
            <div class="modal-header">
                <h3 class="modal-title" style="display:flex; align-items:center; gap:0.75rem;">
                    <i class="fas fa-user-tag" style="color:var(--primary)"></i>
                    <span>Qualifikation hinzufügen</span>
                </h3>
                <button onclick="ui.closeTaskQualModal()" class="btn btn-icon"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="modal-search">
                 <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="tq-search" class="search-input" style="width:100%" placeholder="Qualifikation suchen..." oninput="ui.filterTaskQuals(this.value)">
                </div>
            </div>

            <div id="tq-list" class="modal-body custom-scrollbar" style="flex:1; overflow-y:auto; padding:1rem;">
                </div>

            <div id="tq-footer" class="hidden" style="padding:1.5rem; border-top:1px solid var(--border-color); background:white;">
                <div style="margin-bottom:1rem; font-weight:900; color:var(--text-main);" id="tq-selected-name"></div>
                
                <div style="display:flex; gap:1rem;">
                    <div style="flex:1;">
                        <label style="font-size:0.75rem; font-weight:800; color:#94a3b8; display:block; margin-bottom:4px;">Stunden</label>
                        <input type="number" id="tq-hours" class="form-control" value="1" step="0.25">
                    </div>
                    <div style="flex:1;">
                        <label style="font-size:0.75rem; font-weight:800; color:#94a3b8; display:block; margin-bottom:4px;">Rate (€/h)</label>
                        <input type="number" id="tq-rate" class="form-control" step="0.01">
                    </div>
                </div>

                <div style="margin-top:1rem; display:flex; justify-content:flex-end;">
                    <button onclick="ui.confirmTaskQual()" class="btn btn-primary" style="width:100%;">
                        <i class="fas fa-check"></i> Hinzufügen
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- COSTING MODAL -->
  <div id="costing-modal" class="modal-backdrop hidden" style="z-index: 410;">
    <div class="modal-window fade-in" style="max-width: 980px; height: 88vh;">
      <div class="modal-header">
        <h3 class="modal-title" style="display:flex; align-items:center; gap:.75rem;">
          <i class="fas fa-calculator" style="color:var(--primary)"></i>
          <span>Kalkulation (Costing)</span>
        </h3>
        <div style="display:flex; gap:.5rem;">
          <button class="btn btn-secondary" onclick="ui.costingRecalcPreview()">
            <i class="fas fa-rotate"></i> Neu berechnen
          </button>
          <button class="btn btn-icon" onclick="ui.closeCostingModal()">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>

      <div class="modal-body custom-scrollbar" style="background:rgba(248,250,252,.35);">
        <div id="costing-modal-body" style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
          <!-- Left: Settings -->
          <div style="background:#fff; border:1px solid var(--border-color); border-radius:16px; padding:1rem;">
            <div style="font-weight:900; margin-bottom:.75rem;">Sätze & Regeln</div>

            <div class="form-row mb-2">
              <div class="col">
                <label>AW Minuten</label>
                <input id="cm_aw_minutes" type="number" class="form-control" step="1" value="6">
              </div>
              <div class="col">
                <label>Gemeinkosten (%)</label>
                <input id="cm_gk" type="number" class="form-control" step="0.01" value="10">
              </div>
            </div>

            <div class="form-row mb-2">
              <div class="col">
                <label>Wagnis (%)</label>
                <input id="cm_wagnis" type="number" class="form-control" step="0.01" value="5">
              </div>
              <div class="col">
                <label>Gewinn Personal (%)</label>
                <input id="cm_profit_pers" type="number" class="form-control" step="0.01" value="30">
              </div>
            </div>

            <div class="form-row mb-2">
              <div class="col">
                <label>Gewinn Material (%)</label>
                <input id="cm_profit_mat" type="number" class="form-control" step="0.01" value="50">
              </div>
              <div class="col">
                <label>Provision (%)</label>
                <input id="cm_provision" type="number" class="form-control" step="0.01" value="0">
              </div>
            </div>

            <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid #f1f5f9;">
              <div style="display:flex; justify-content:space-between; font-weight:900;">
                <span>Quelle:</span>
                <span style="color:#64748b;">Aufgaben → Qualifikationen</span>
              </div>
              <div class="text-muted" style="margin-top:6px;">
                Die Stunden pro Qualifikation werden aus den Aufgaben summiert (task_labor).
              </div>
            </div>

            <div class="form-group mb-2">
              <label>Kostenset</label>
              <select id="cm_costing_set_id" class="form-control" onchange="ui.onCostingSetChange(this.value)">
                <option value="">— Manuell —</option>
              </select>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:.5rem; margin-top:1rem;">
              <button class="btn btn-primary" onclick="ui.costingApplyToGlobals()">
                <i class="fas fa-check"></i> Werte übernehmen
              </button>
            </div>
          </div>

          <!-- Right: Preview -->
          <div style="background:#fff; border:1px solid var(--border-color); border-radius:16px; padding:1rem;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.75rem;">
              <div style="font-weight:900;">Vorschau</div>
              <div class="pill pill-gray" style="margin:0;">
                <div class="dot bg-gray"></div>
                <span id="cm_preview_badge">—</span>
              </div>
            </div>

            <div id="cm_preview" style="display:flex; flex-direction:column; gap:.75rem;"></div>

            <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid #f1f5f9;">
              <div style="display:flex; justify-content:space-between; align-items:flex-end;">
                <div>
                  <div style="font-size:10px; font-weight:900; color:#94a3b8; text-transform:uppercase;">Gesamt (VK)</div>
                  <div id="cm_total_vk" style="font-size:1.6rem; font-weight:900;">—</div>
                </div>
                <div style="text-align:right;">
                  <div style="font-size:10px; font-weight:900; color:#94a3b8; text-transform:uppercase;">Stunden</div>
                  <div id="cm_total_hours" style="font-size:1rem; font-weight:900; color:#64748b;">—</div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>



    <div id="duplicate-modal" class="modal-backdrop hidden" style="z-index: 420;">
      <div class="modal-window fade-in" style="max-width: 760px; height:auto; max-height:90vh; display:flex; flex-direction:column;">
        <div class="modal-header">
          <h3 class="modal-title" style="display:flex; align-items:center; gap:.75rem;">
            <i class="fas fa-copy" style="color:var(--primary)"></i>
            <span>MasterSet duplizieren</span>
          </h3>
          <button onclick="ui.closeDuplicateModal()" class="btn btn-icon">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div class="modal-body custom-scrollbar" style="background:rgba(248,250,252,.35);">
          <div id="duplicate-modal-body">
            <div style="padding:2rem; text-align:center; color:#94a3b8; font-weight:800;">
              Lade Daten...
            </div>
          </div>
        </div>

        <div class="modal-header" style="border-top:1px solid var(--border-color); border-bottom:none; justify-content:flex-end; gap:.75rem;">
          <button onclick="ui.closeDuplicateModal()" class="btn btn-secondary">Abbrechen</button>
          <button id="duplicate-confirm-btn" onclick="ui.submitDuplicate()" class="btn btn-primary">
            <i class="fas fa-copy"></i> Duplizieren
          </button>
        </div>
      </div>
    </div>


    <div id="distributor-compare-modal" class="modal-backdrop hidden" style="z-index: 430;">
      <div class="modal-window fade-in" style="max-width: 1100px; height: 88vh; display:flex; flex-direction:column;">
        <div class="modal-header">
          <h3 class="modal-title" style="display:flex; align-items:center; gap:.75rem;">
            <i class="fas fa-chart-line" style="color:var(--primary)"></i>
            <span>Distributor Preisvergleich</span>
          </h3>
          <button onclick="ui.closeDistributorCompareModal()" class="btn btn-icon">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div class="modal-body custom-scrollbar" style="background:rgba(248,250,252,.35); display:grid; grid-template-columns: 1.1fr .9fr; gap:1rem;">
          <div>
            <div id="distributor-compare-summary"></div>
            <div class="compare-chart-card">
              <div class="compare-chart-head">Preisvergleich</div>
              <div class="compare-chart-wrap">
                <canvas id="distributor-compare-chart"></canvas>
              </div>
            </div>
          </div>

          <div>
            <div style="background:white; border:1px solid var(--border-color); border-radius:16px; padding:1rem; height:100%;">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.75rem;">
                <div style="font-weight:900;">Lieferanten</div>
                <div id="distributor-compare-best" class="text-muted"></div>
              </div>
              <div id="distributor-compare-list" style="display:flex; flex-direction:column; gap:.75rem;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <div id="locked-warning-modal" class="modal-backdrop hidden" style="z-index: 9999;">
      <div class="modal-window fade-in" style="max-width: 400px; height: auto; padding: 2rem; text-align: center;">
          <i class="fas fa-shield-alt" style="font-size: 3rem; color: var(--danger); margin-bottom: 1rem;"></i>
          <h3 style="font-size: 1.25rem; font-weight: 900; margin-bottom: 0.5rem; color: var(--text-main);">Set ist gesperrt</h3>
          <p style="color: var(--text-light); font-size: 0.875rem; margin-bottom: 1.5rem;">
              Dieses MasterSet ist schreibgeschützt. Änderungen können nicht vorgenommen und nicht gespeichert werden. Bitte entsperren Sie das Set zuerst oben rechts.
          </p>
          <button onclick="document.getElementById('locked-warning-modal').classList.add('hidden')" class="btn btn-secondary" style="width: 100%;">Verstanden</button>
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
    showMatDrop: false,
    showSummary: false,
    groups: [],
    sets: [],
    groupSets: [],   
    groupSetsForGroupId: null,  
    materialSearch: '',    
    setsTab: 'all',        
   selectedGroup: null,
    editingSet: null,
    autoSaveEnabled: false,

    laborOptions: [],
    catalog: [],
    pickerContext: null,

    groupSearch: '',
    editorTab: 'material',

    groupViewMode: 'list',

    costingSets: [],            // list for dropdown
    selectedCostingSetId: null, // currently chosen costing_set.id
    costingSetCache: {},        // id -> object (optional cache)

    // Tasks
    taskOptions: [],     // stages -> phases -> activities (from /tasks/options)
    taskSearch: '',
    selectedStageId: null, // stage filter for options panel
    taskSectionFilter: '',
    compareContext: {
      mainIdx: null,
      subIdx: null
    },

    isLocked: false,
    globalMatMargin: 0,
    minMatMargin: 0,
    globalPersMargin: 0,
    minPersMargin: 0,
    globalGemeinkosten: 0,
    globalWagnis: 0,
    minProfitTarget: 0,
   visibleColMat: { 
      articleNumber: true, 
      productTitle: true, 
      description: true, 
      supplier: false, 
      condition: false, 
      dokumente: false, 
      ek: true, 
      vk: true,
      profit: true,
      ek_total: true,
      vk_total: true,
      db_total: true,
      pe: true,           
      margin: false, 
      quantity: true, 
      vpe: true,             
      weighting: false, 
      total: true, 
      aktionen: true 
    },
 
       // Checklists
    checklistOptions: [],    
    checklistSearch: '',
    duplicateDraft: {
      loaded: false,
      articleGroups: [],
      targetSets: [],
      selectedArticleGroupId: null,

      copy_material: true,
      copy_tasks: true,
      copy_labor: true,
      copy_checklists: true,

      target_mode: 'clone', // clone | existing
      target_master_set_id: null,
      new_name: '',
    },
    
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

    const resolveMaterialPrice = (row) => {
      const ppCamel = parseFloat(row?.purchasePrice);
      const ppSnake = parseFloat(row?.purchase_price);
      const unit    = parseFloat(row?.unit_price);

      // if purchasePrice/purchase_price is missing OR zero, use unit_price
      if (Number.isFinite(ppCamel) && ppCamel > 0) return ppCamel;
      if (Number.isFinite(ppSnake) && ppSnake > 0) return ppSnake;
      if (Number.isFinite(unit)) return unit;

      return 0;
    };
    // Normalize nested arrays
   s.components.forEach(c => {
      c.subComponents = Array.isArray(c.subComponents) ? c.subComponents : [];

      c.quantity = toNum(c.qty, 1);
      c.purchasePrice = resolveMaterialPrice(c);
      c.unit_price = toNum(c.unit_price, c.purchasePrice);
      c.margin = toNum(c.margin, 0);
      c.skonto = toNum(c.skonto, 0);
      c.paymentTerms = toNum(c.paymentTerms ?? c.payment_terms, 14);
      c.availability = c.availability ?? true;
      c.type = c.type ?? 'haupt';
      c.isStammartikel = !!(c.isStammartikel ?? c.is_stammartikel);
      c.isFavorite = !!(c.isFavorite ?? c.is_favorite);
      c.unit = c.measure || 'Stk.';
      c.measure = c.measure || 'Stk.';
      c.price_unit = Math.max(1, toNum(c.price_unit, 1)); // ✅ Extract PE
      c.vpe = toNum(c.vpe, 1);;

      c.isExpanded = true;
      c.isEditingProps = false;
      c.docs = Array.isArray(c.docs) ? c.docs : [];

      c.subComponents.forEach(sub => {
        sub.quantity = toNum(sub.qty, 1);
       sub.purchasePrice = resolveMaterialPrice(sub);
        sub.unit_price = toNum(sub.unit_price, sub.purchasePrice);
        sub.margin = toNum(sub.margin, 0);
        sub.skonto = toNum(sub.skonto, 0);
        sub.paymentTerms = toNum(sub.paymentTerms ?? sub.payment_terms, 14);
        sub.availability = sub.availability ?? true;
        sub.type = sub.type ?? 'zubehoer';
        sub.isStammartikel = !!(sub.isStammartikel ?? sub.is_stammartikel);
        sub.isFavorite = !!(sub.isFavorite ?? sub.is_favorite);
        sub.unit = sub.measure || 'Stk.';
        sub.measure = sub.measure || 'Stk.';
        sub.price_unit = Math.max(1, toNum(sub.price_unit, 1)); // ✅ Extract PE
        sub.vpe = toNum(sub.vpe, 1);

        sub.isEditingProps = false;
        sub.docs = Array.isArray(sub.docs) ? sub.docs : [];
      });
    });

    s.checklists.forEach((c, i) => {
      const realId =
        c.source_checklist_id ??
        c.maintenance_checklist_id ??
        c.checklist_id ??
        null;

      c.source_checklist_id = realId ? Number(realId) : null;
      c.maintenance_checklist_id = realId ? Number(realId) : null;
      c.checklist_id = realId ? Number(realId) : null;

      c.sort_order = Number.isFinite(+c.sort_order) ? +c.sort_order : i;
      c.title = c.title ?? c.name ?? '';
      c.items_count = Number.isFinite(+c.items_count)
        ? +c.items_count
        : (Array.isArray(c.items) ? c.items.length : 0);
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

  const ROUTES = {
    adminBase: `${window.location.origin}/admin`,
    masterSetsBase: `${window.location.origin}/admin/master-sets`,
    costingSetsBase: `${window.location.origin}/admin/costing-sets`,
  };

      
   const PAGE_STATE_KEY = 'masterset_page_state_v1';

  const getPageStateScope = () => {
    const view = state?.view || 'dashboard';
    const groupId = state?.selectedGroup?.id || 'nogroup';
    const setId = state?.editingSet?.id || 'noset';
    const tab = state?.editorTab || 'notab';

    return `${view}|g:${groupId}|s:${setId}|t:${tab}`;
  };

  const getPageStateKey = () => {
    return `masterset_page_state_v2:${getPageStateScope()}`;
  };

  const pageState = {
    save() {
      try {
        const materialWrap = document.getElementById('material-scroll-wrap');
        const active = document.activeElement;

        const focused = active && (
          active.tagName === 'INPUT' ||
          active.tagName === 'TEXTAREA' ||
          active.tagName === 'SELECT'
        ) ? {
          id: active.id || null,
          name: active.name || null,
          field: active.getAttribute('data-field') || null,
          main: active.getAttribute('data-main-index') || null,
          sub: active.getAttribute('data-sub-index') || null,
          selectionStart: typeof active.selectionStart === 'number' ? active.selectionStart : null,
          selectionEnd: typeof active.selectionEnd === 'number' ? active.selectionEnd : null,
        } : null;

        const collapsedPhases = window.wizard?._collapsedPhases
          ? Array.from(window.wizard._collapsedPhases)
          : [];

        const payload = {
          scope: getPageStateScope(),

          view: state.view,
          selectedGroup: state.selectedGroup ? {
            id: state.selectedGroup.id,
            name: state.selectedGroup.name || ''
          } : null,
          editingSetId: state.editingSet?.id || null,
          editorTab: state.editorTab || 'material',
          setsTab: state.setsTab || 'all',
          groupViewMode: state.groupViewMode || 'list',
          showSummary: !!state.showSummary,
          selectedStageId: state.selectedStageId || null,
          taskSearch: state.taskSearch || '',
          taskSectionFilter: state.taskSectionFilter || '',
          materialSearch: state.materialSearch || '',
          showMatDrop: !!state.showMatDrop,

          windowScrollY: window.scrollY || 0,
          materialScrollTop: materialWrap ? materialWrap.scrollTop : 0,
          materialScrollLeft: materialWrap ? materialWrap.scrollLeft : 0,

          focused,
          collapsedPhases,
          timestamp: Date.now()
        };

        sessionStorage.setItem(getPageStateKey(), JSON.stringify(payload));
      } catch (e) {
        console.warn('pageState.save failed', e);
      }
    },

    load(scopeOverride = null) {
      try {
        const key = scopeOverride
          ? `masterset_page_state_v2:${scopeOverride}`
          : getPageStateKey();

        const raw = sessionStorage.getItem(key);
        if (!raw) return null;

        const parsed = JSON.parse(raw);

        if (!parsed || parsed.scope !== (scopeOverride || getPageStateScope())) {
          return null;
        }

        return parsed;
      } catch (e) {
        console.warn('pageState.load failed', e);
        return null;
      }
    },

    clear(scopeOverride = null) {
      try {
        const key = scopeOverride
          ? `masterset_page_state_v2:${scopeOverride}`
          : getPageStateKey();

        sessionStorage.removeItem(key);
      } catch (e) {}
    },

    clearAllLegacy() {
      try {
        sessionStorage.removeItem('masterset_page_state_v1');
      } catch (e) {}
    },

    restoreDom(saved) {
      if (!saved) return;

      if (saved.scope !== getPageStateScope()) {
        return;
      }

      requestAnimationFrame(() => {
        const materialWrap = document.getElementById('material-scroll-wrap');

        if (materialWrap) {
          materialWrap.scrollTop = saved.materialScrollTop || 0;
          materialWrap.scrollLeft = saved.materialScrollLeft || 0;
        }
 

        if (saved.focused) {
          let target = null;

          if (saved.focused.id) {
            target = document.getElementById(saved.focused.id);
          }

          if (!target && saved.focused.field !== null && saved.focused.main !== null) {
            const selector = [
              `[data-field="${saved.focused.field}"]`,
              `[data-main-index="${saved.focused.main}"]`,
              saved.focused.sub !== null && saved.focused.sub !== ''
                ? `[data-sub-index="${saved.focused.sub}"]`
                : ''
            ].join('');

            target = document.querySelector(selector);
          }

          if (target) {
            target.focus({ preventScroll: true });

            if (
              typeof saved.focused.selectionStart === 'number' &&
              typeof saved.focused.selectionEnd === 'number' &&
              typeof target.setSelectionRange === 'function'
            ) {
              target.setSelectionRange(
                saved.focused.selectionStart,
                saved.focused.selectionEnd
              );
            }
          }
        }
      });
    }
  };

  window.pageState = pageState;

 // ===========================================================================
  // API
  // ===========================================================================
  const api = {
   // ✅ Route base (adjust once here if your prefix differs)
 
   
   async hydrateSetComponents(masterSetId) {
      return await this.request(`/${masterSetId}/hydrate-components`, 'POST');
    },

    async hydrateGroupComponents(articleGroupId) {
      return await this.request(`/groups/${articleGroupId}/hydrate-components`, 'POST');
    },

    async request(endpoint, method = 'GET', data = null, base = ROUTES.masterSetsBase) {
      ui.showLoading(true);

      try {
        const isAbsolute = /^https?:\/\//i.test(endpoint);

        // endpoint can be "/costing-sets/1" or "?q=x" or full absolute URL
        const url = isAbsolute
          ? endpoint
          : `${String(base).replace(/\/+$/, '')}/${String(endpoint).replace(/^\/+/, '')}`;

        const headers = {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
          'Accept': 'application/json',
        };

        // Only set JSON content-type when we send a body
        const options = { method, headers };
        if (data !== null && data !== undefined) {
          options.headers['Content-Type'] = 'application/json';
          options.body = JSON.stringify(data);
        }

        const res = await fetch(url, options);

        // Try json first; fallback to text
        let payload = null;
        const ct = res.headers.get('content-type') || '';
        if (ct.includes('application/json')) {
          payload = await res.json().catch(() => null);
        } else {
          const text = await res.text().catch(() => '');
          payload = text ? (() => { try { return JSON.parse(text); } catch { return text; } })() : null;
        }

        if (!res.ok) {
          console.error('[API ERROR]', method, url, res.status, payload);

          const msg =
            (payload && typeof payload === 'object' && payload.message) ||
            (payload && typeof payload === 'object' && payload.errors
              ? Object.values(payload.errors).flat().join(' · ')
              : '') ||
            `HTTP ${res.status}`;

          ui.showStatus(msg || 'ERROR', true);
          return null;
        }

        ui.showStatus('SYNC OK');
        return payload;
      } catch (e) {
        console.error('API Error:', e);
        ui.showStatus('ERROR', true);
        return null;
      } finally {
        ui.showLoading(false);
      }
    },

    async loadCostingSets(articleGroupId = null) {
      const gid = articleGroupId || getGroupId();
      if (!gid) return;

      // ✅ /admin/costing-sets/options?article_group_id=...
      const endpoint = `options?article_group_id=${encodeURIComponent(gid)}`;

      const res = await this.request(endpoint, 'GET', null, ROUTES.costingSetsBase);
      state.costingSets = res?.data || [];
    },

    async showCostingSet(id) {
      if (!id) return null;
      if (state.costingSetCache[id]) return state.costingSetCache[id];

      // ✅ /admin/costing-sets/{id}
      const res = await this.request(String(id), 'GET', null, ROUTES.costingSetsBase);
      const cs = res?.data || null;

      if (cs) state.costingSetCache[id] = cs;
      return cs;
    },

    async getDuplicateOptions(masterSetId, articleGroupId = null) {
      const q = articleGroupId ? `?article_group_id=${encodeURIComponent(articleGroupId)}` : '';
      return await this.request(`/${masterSetId}/duplicate-options${q}`, 'GET');
    },

    async duplicateSet(masterSetId, payload) {
      return await this.request(`/${masterSetId}/duplicate`, 'POST', payload);
    },

    async deleteGroupSet(id) { return await this.request(`/group-sets/${id}`, 'DELETE'); },
    async getGroupSets(groupId) {
      if (!groupId) return;
      const res = await this.request(`/group-sets?article_group_id=${groupId}`);
      if (!res) return;
      const payload = res.data ?? res;
      state.groupSets = Array.isArray(payload) ? payload : Array.isArray(payload?.data) ? payload.data : Array.isArray(payload?.group_sets) ? payload.group_sets : Array.isArray(payload?.groupSets) ? payload.groupSets : [];
      state.groupSetsForGroupId = String(groupId);
      ui.render();
    },
    async createGroup(payload) { return await this.request(`/groups`, 'POST', payload); },
    async updateGroup(id, payload) { return await this.request(`/groups/${id}`, 'PUT', payload); },
    async deleteGroup(id) { return await this.request(`/groups/${id}`, 'DELETE'); },
    async getSets(groupId) {
    const res = await this.request(`/data?article_group_id=${groupId}`);
          if (!res) return;
          const payload = res.data ?? null;
          const rawSets = Array.isArray(payload)
      ? payload
      : Array.isArray(payload?.data)
        ? payload.data
        : Array.isArray(payload?.sets)
          ? payload.sets
          : Array.isArray(res?.sets)
            ? res.sets
            : [];

      state.sets = rawSets.map(set => {
        const normalized = normalizeSet(set);

        const hasComponents =
          Array.isArray(normalized.components) && normalized.components.length > 0;

        const hasLabor =
          Array.isArray(normalized.labor) && normalized.labor.length > 0;

        // fallback from backend summary fields
        let mainTotal = Number(
          normalized.stats?.mainTotal ??
          normalized.stats?.mainCost ??
          normalized.main_total ??
          0
        );

        let subTotal = Number(
          normalized.stats?.subTotal ??
          normalized.stats?.subCost ??
          normalized.sub_total ??
          0
        );

        let laborTotal = Number(
          normalized.stats?.laborTotal ??
          normalized.stats?.labor ??
          normalized.labor_total ??
          0
        );

        let mainCount = Number(
          normalized.stats?.mainCount ??
          normalized.main_count ??
          (Array.isArray(normalized.components) ? normalized.components.length : 0)
        );

        let subCount = Number(
          normalized.stats?.subCount ??
          normalized.sub_count ??
          0
        );

        let laborCount = Number(
          normalized.stats?.laborCount ??
          normalized.labor_count ??
          (Array.isArray(normalized.labor) ? normalized.labor.length : 0)
        );

        // only recalc if full detail is actually present
        if (hasComponents) {
          mainTotal = 0;
          subTotal = 0;
          mainCount = 0;
          subCount = 0;

          normalized.components.forEach(main => {
            mainCount += 1;
            mainTotal += ui.getMaterialLineTotal(main);

            const subs = Array.isArray(main.subComponents) ? main.subComponents : [];
            subs.forEach(sub => {
              subCount += 1;
              subTotal += ui.getMaterialLineTotal(sub);
            });
          });
        }

        if (hasLabor) {
          laborTotal = 0;
          laborCount = normalized.labor.length;

          normalized.labor.forEach(l => {
            const baseEK = Number(l.rate || 0) * Number(l.hours || 0);
            const gk = baseEK * (Number(state.globalGemeinkosten || 0) / 100);
            const wagnis = baseEK * (Number(state.globalWagnis || 0) / 100);
            const profit = baseEK * (Number(state.globalPersMargin || 0) / 100);

            laborTotal += baseEK + gk + wagnis + profit;
          });
        }

        const total = mainTotal + subTotal + laborTotal;

       normalized.stats = {
        ...(normalized.stats || {}),
        mainTotal,
        subTotal,
        laborTotal,
        total,
        mainCount,
        subCount,
        laborCount,
      };

        return normalized;
      });

    const gsArr = Array.isArray(res?.group_sets) ? res.group_sets : Array.isArray(res?.groupSets) ? res.groupSets : Array.isArray(res?.meta?.group_sets) ? res.meta.group_sets : Array.isArray(res?.meta?.groupSets) ? res.meta.groupSets : Array.isArray(payload?.group_sets) ? payload.group_sets : Array.isArray(payload?.groupSets) ? payload.groupSets : Array.isArray(payload?.group_sets_data) ? payload.group_sets_data : null;
      if (Array.isArray(gsArr)) {
        state.groupSets = gsArr;
        state.groupSetsForGroupId = String(groupId);
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
      if (state.view === 'editor' && state.editorTab === 'checklists') ui.renderChecklistsTab();
    },
    async validateChecklistAttach(payload) { return await this.request(`/checklists/validate`, 'POST', payload); },
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
    async getDistributorPrice(distributorPriceId) { return await this.request(`/distributor-price/${distributorPriceId}`); },
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

      // ✅ total as stored in DB (before any client recalculation/normalization)
      const raw = res.data || {};
      const dbTotal =
        Number(raw?.total ?? raw?.stats?.total ?? raw?.total_value ?? 0) || 0;

      state.editingSet = normalizeSet(raw);

      // ✅ Lock flag (0 = locked, 1 = unlocked)
      state.isLocked = (parseInt(state.editingSet.is_locked, 10) === 0);

      ensureSelectedStage();
      ui.recalculateLocalStats();
      ui.render();

      // ✅ only auto-save if DB total is 0 but client calc produced > 0
      if (!state.isLocked && dbTotal === 0 && (state.editingSet?.stats?.total || 0) > 0) {
        app.autoSave();
      }
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
        const section = state.taskSectionFilter || '';
        const stage = state.selectedStageId || '';
        const url = `/tasks/options?article_group_id=${groupId}&q=${encodeURIComponent(q)}&section=${encodeURIComponent(section)}&stage_id=${encodeURIComponent(stage)}`;
        const res = await this.request(url);
        if (!res) return;
        state.taskOptions = res.data || [];
          if (state.pickerContext?.type === 'task') {
            const modalContent = document.getElementById('modal-content');
            if (modalContent) {
              modalContent.innerHTML = ui.getTaskOptionsHTML({ mode: 'modal' });
            }
          } else if (state.view === 'editor' && state.editorTab === 'aufgabe') {
            ui.refreshTaskOptionsPanel();
          }
    }, 
    // --- PERFECTED SAVE SET (HANDLES AUTO-SAVE + UX + CREATION) ---
    async saveSet(isAutoSave = false, needsReload = false) {
      const s = state.editingSet;
      const savedPageState = pageState.load() || null;
      if (!s) return;

      if (!s.name || s.name.trim() === '') {
        if (!isAutoSave) {
          alert('Bitte geben Sie einen Namen ein.');
          return;
        } else {
          s.name = 'Unbenanntes Set';
        }
      }

      const groupId = s.article_group_id || state.selectedGroup?.id || null;
      if (!groupId) {
        if (!isAutoSave) alert('Fehler: Keine Artikelgruppe zugewiesen.');
        return;
      }

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
        main_total: s.stats.mainTotal,
        sub_total: s.stats.subTotal,
        labor_total: s.stats.laborTotal,
        total: s.stats.total,

       components: (s.components || []).map(c => ({
          id: c.id || null,
          product_id: c.product_id,
          article_no: c.article_no || c.articleNumber || null,
          distributor_article_no: c.distributor_article_no || null,
          distributor_price_id: c.distributor_price_id ? c.distributor_price_id : null,
          qty: toNum(c.qty ?? c.quantity, 0),
          unit_price: toNum(c.unit_price, 0),
          description: c.description || null,
          sort_order: toNum(c.sort_order, 0),
          purchase_price: toNum(c.purchasePrice ?? c.purchase_price ?? c.unit_price, 0),
          margin: toNum(c.margin, state.globalMatMargin),
          skonto: toNum(c.skonto, 0),
          payment_terms: toNum(c.paymentTerms ?? c.payment_terms, 14),
          availability: c.availability ?? true,
          type: c.type || 'haupt',
          is_stammartikel: !!(c.isStammartikel ?? c.is_stammartikel),
          is_favorite: !!(c.isFavorite ?? c.is_favorite),
          price_unit: Math.max(1, toNum(c.price_unit, 1)),
          measure: c.measure || c.unit || 'Stk.',
          vpe: toNum(c.vpe, 1),

          subComponents: (c.subComponents || []).map(sub => ({
            id: sub.id || null,
            product_id: sub.product_id,
            article_no: sub.article_no || sub.articleNumber || null,
            distributor_article_no: sub.distributor_article_no || null,
            distributor_price_id: sub.distributor_price_id ? sub.distributor_price_id : null,
            qty: toNum(sub.qty ?? sub.quantity, 0),
            unit_price: toNum(sub.unit_price, 0),
            price_unit: Math.max(1, toNum(sub.price_unit, 1)),
            description: sub.description || null,
            sort_order: toNum(sub.sort_order, 0),
            purchase_price: toNum(sub.purchasePrice ?? sub.purchase_price ?? sub.unit_price, 0),
            margin: toNum(sub.margin, state.globalMatMargin),
            skonto: toNum(sub.skonto, 0),
            payment_terms: toNum(sub.paymentTerms ?? sub.payment_terms, 14),
            availability: sub.availability ?? true,
            type: sub.type || 'zubehoer',
            is_stammartikel: !!(sub.isStammartikel ?? sub.is_stammartikel),
            is_favorite: !!(sub.isFavorite ?? sub.is_favorite),
            measure: sub.measure || sub.unit || 'Stk.',
            vpe: toNum(sub.vpe, 1),
          })),
        })),

        labor: (s.labor || []).map(l => ({
          qualification_id: l.qualification_id || null,
          department_position_id: null,
          hours: l.hours,
          hourly_rate: l.rate,
          sort_order: l.sort_order
        })),

        tasks: tasks.map((t, i) => ({
          stage_id: t.stage_id ?? null,
          task_phase_id: t.task_phase_id ?? null,
          phase_activity_id: t.phase_activity_id,
          hours: t.hours ?? 0,
          sort_order: (t.sort_order ?? i),
          stage_name: t.stage_name ?? null,
          phase_name: t.phase_name ?? null,
          title: t.title ?? null,
          description: t.description ?? null,
          duration: t.duration ?? null,
          duration_type: t.duration_type ?? null,
          notes: t.notes ?? null,
          priority: t.priority ?? null,
          percent: t.percent ?? null,
          task_labor: t.task_labor || []
        })),

        checklists: (s.checklists || [])
          .map((c, i) => {
            const realChecklistId = Number(
              c.source_checklist_id ??
              c.maintenance_checklist_id ??
              c.checklist_id ??
              0
            );

            return {
              maintenance_checklist_id: realChecklistId,
              trigger: c.trigger || 'start',
              is_required: c.is_required !== false,
              sort_order: Number.isFinite(+c.sort_order) ? +c.sort_order : i,
              checklist_title_snapshot: c.title ?? null,
              checklist_type_snapshot: c.type ?? null,
            };
          })
          .filter(c => Number.isInteger(c.maintenance_checklist_id) && c.maintenance_checklist_id > 0)
          .sort((a, b) => (+a.sort_order) - (+b.sort_order)),
      };

      const origShowLoading = ui.showLoading;

      if (isAutoSave) {
        ui.showLoading = () => {};
        const btn = document.getElementById('main-save-btn');
        if (btn) {
          btn.innerHTML = `<i class="fas fa-spinner fa-spin" style="margin-right:0.5rem;"></i> Speichert...`;
        }
      } else {
        ui.showLoading(true);
      }

      let res;
      try {
        if (s.id) res = await this.request(`/${s.id}`, 'PUT', payload);
        else res = await this.request('', 'POST', payload);
      } finally {
        ui.showLoading = origShowLoading;
        if (!isAutoSave) ui.showLoading(false);

        const btn = document.getElementById('main-save-btn');
        if (btn) {
          btn.innerHTML = `<i class="fas fa-save" style="margin-right:0.5rem;"></i> ${s.id ? 'GESPEICHERT' : 'SPEICHERN'}`;
        }
      }

      if (res && res.status === 'ok') {
        const wasNew = !s.id;

        if (!s.id && res.id) s.id = res.id;

        if (isAutoSave) {
          ui.showStatus('AUTOMATISCH GESPEICHERT');

          if (wasNew && res.id) {
            state.editingSet.id = res.id;
          }

          if (res.data && typeof res.data === 'object') {
            const fresh = normalizeSet(res.data);

            if (needsReload) {
              pageState.save();

              const oldComponents = state.editingSet.components || [];
              const newComponents = fresh.components || [];

              newComponents.forEach((nc, i) => {
                const oc = oldComponents[i];
                if (!oc) return;

                nc.isExpanded = oc.isExpanded ?? true;
                nc.isEditingProps = oc.isEditingProps ?? false;

                if (Array.isArray(nc.subComponents) && Array.isArray(oc.subComponents)) {
                  nc.subComponents.forEach((ns, j) => {
                    const os = oc.subComponents[j];
                    if (!os) return;
                    ns.isEditingProps = os.isEditingProps ?? false;
                  });
                }
              });

              state.editingSet = {
                ...state.editingSet,
                ...fresh,
              };

              ui.recalculateLocalStats();
              ui.render();
              pageState.restoreDom(savedPageState || pageState.load());
            } else {
              const oldComps = state.editingSet.components || [];
              const freshComps = fresh.components || [];

              oldComps.forEach((oc, i) => {
                if (!oc.id && freshComps[i]) oc.id = freshComps[i].id;

                if (oc.subComponents && freshComps[i]?.subComponents) {
                  oc.subComponents.forEach((os, j) => {
                    if (!os.id && freshComps[i].subComponents[j]) {
                      os.id = freshComps[i].subComponents[j].id;
                    }
                  });
                }
              });
            }
          }
        } else {
          ui.showStatus('GESPEICHERT');

          if (s.id) {
            const fresh = await this.request(`/${s.id}`);
            if (fresh && fresh.data) {
              state.editingSet = normalizeSet(fresh.data);
              ui.recalculateLocalStats();
              ui.render();
            }
          }
        }

        if (state.selectedGroup?.id) {
          await api.getSets(state.selectedGroup.id);
          await api.getGroupSets(state.selectedGroup.id);
        }
      }
    },

    async deleteSet(id) {
      const res = await this.request(`/${id}`, 'DELETE');
      if (res && res.status === 'ok') {
        if (state.selectedGroup?.id) await this.getSets(state.selectedGroup.id);
        app.navigate('groupList');
        ui.showStatus('GELÖSCHT');
      }
    }
  };
 
 // ==============================
  // Description Designer (Quill)
  // ==============================
  const descUI = {
    componentId: null,
    context: 'angebot', // Default context
    variants: [],
    current: null,
    fallback: { component_description: '', product_short_description: '' },
    quill: null,
    sortable: null,

    // Helper: Validates Quill Delta to prevent editor crashes
    isValidDelta(delta) {
      if (!delta || typeof delta !== 'object') return false;
      if (!Array.isArray(delta.ops)) return false;
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

      const toolbar = [
        [{ font: [] }, { size: [] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ color: [] }, { background: [] }],
        [{ script: 'sub' }, { script: 'super' }],
        [{ header: 1 }, { header: 2 }, 'blockquote', 'code-block'],
        [{ list: 'ordered' }, { list: 'bullet' }, { indent: '-1' }, { indent: '+1' }],
        [{ direction: 'rtl' }, { align: [] }],
        ['link', 'image'],
        ['clean']
      ];

      this.quill = new Quill('#desc-quill', {
        theme: 'snow',
        modules: { toolbar }
      });
    },

    open(componentId, productLabel = '') {
      this.componentId = String(componentId);

      // Always reset to 'angebot' when opening
      this.context = 'angebot';
      const ctxSelect = document.getElementById('desc-context');
      if (ctxSelect) ctxSelect.value = 'angebot';

      const titleEl = document.getElementById('desc-modal-title');
      if (titleEl) titleEl.textContent = 'Beschreibung Designer';
      
      const lineEl = document.getElementById('desc-product-line');
      if (lineEl) lineEl.textContent = productLabel || `Component #${this.componentId}`;

      this.ensureQuill();
      this.current = null;
      this.variants = [];
      
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

      const token = (this._loadToken = (this._loadToken || 0) + 1);

      try {
        const url = `/components/${this.componentId}/descriptions?context=${encodeURIComponent(this.context ?? '')}`;
        const res = await api.request(url, 'GET');

        if (token !== this._loadToken) return;

        const data = res?.data;
        if (!data) {
          ui.showStatus('ERROR', true);
          return;
        }

        this.variants = (Array.isArray(data.variants) ? data.variants : []).map(v => {
          const vv = { ...v };
          if (!this.isValidDelta(vv.delta)) vv.delta = null;
          if (!vv.html && vv.delta == null) vv.html = '<p><br></p>';
          return vv;
        });

        this.fallback = data.fallback || { component_description: '', product_short_description: '' };

        const fallbackText = String(
          (this.fallback.component_description || this.fallback.product_short_description || '')
        ).trim();

        const hintEl = document.querySelector('#desc-fallback-hint');
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
      const titleEl = document.getElementById('desc-variant-title');
      if (titleEl) titleEl.value = '';
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
          .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0))
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
                  ${window.ui ? window.ui.escapeHtml(v.title || 'Ohne Titel') : (v.title || 'Ohne Titel')}
                </div>
                <div style="font-size:11px; font-weight:800; color:#94a3b8; margin-top:4px;">
                  #${v.id} · ${window.ui ? window.ui.escapeHtml(v.context || this.context) : (v.context || this.context)}
                </div>
              </div>
              <div class="handle" style="width:28px;height:28px;"><i class="fas fa-grip-vertical"></i></div>
            </div>
          </div>
        `).join('');
      }

      if (this.sortable) { try { this.sortable.destroy(); } catch (e) { } }
      
      this.sortable = new Sortable(box, {
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
      this.setEditorFromVariant(v);
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
      this.quill.setText(fb);
    },

    async saveCurrent() {
      if (!this.current?.id) return ui.showStatus('NO VARIANT', true);

      const titleInput = document.getElementById('desc-variant-title');
      const title = (titleInput?.value || '').trim();
      
      const delta = this.quill.getContents();
      const text = this.quill.getText();
      const html = this.quill.root.innerHTML;

      // 1. Save to Backend (Specific Description Table)
      const res = await api.request(`/components/descriptions/${this.current.id}`, 'PUT', {
        title,
        delta,
        text,
        html,
      });

      if (!res?.data) return ui.showStatus('ERROR', true);

      // 2. Update local variants list
      const idx = this.variants.findIndex(x => String(x.id) === String(this.current.id));
      if (idx !== -1) this.variants[idx] = res.data;
      this.current = res.data;

      this.renderVariants();

      // ============================================================
      // ✅ THE FIX: Sync text back to main state object immediately
      // ============================================================
      if (state.editingSet && state.editingSet.components) {
          const targetId = String(this.componentId);
          const updateText = text.trim();

          const updateRecursive = (list) => {
              for (const comp of list) {
                  // Check if this is the edited component (Main or Sub)
                  if (String(comp.id) === targetId) {
                      comp.description = updateText; // Sync!
                      return true; 
                  }
                  // Check nested
                  if (comp.subComponents && comp.subComponents.length > 0) {
                      if (updateRecursive(comp.subComponents)) return true;
                  }
              }
              return false;
          };

          updateRecursive(state.editingSet.components);
      }
      // ============================================================

      ui.showStatus('GESPEICHERT');
    },

    async deleteCurrent() {
      if (!this.current?.id) return;

      if (!confirm('Variante löschen?')) return;

      const id = this.current.id;
      const res = await api.request(`/components/descriptions/${id}`, 'DELETE');
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

const setDescUI = {
  quill: null,

  ensureQuill() {
    if (this.quill) return;

    const toolbar = [
      [{ font: [] }, { size: [] }],
      ['bold', 'italic', 'underline', 'strike'],
      [{ color: [] }, { background: [] }],
      [{ header: 1 }, { header: 2 }],
      [{ list: 'ordered' }, { list: 'bullet' }],
      [{ align: [] }],
      ['link'],
      ['clean']
    ];

    this.quill = new Quill('#set-desc-quill', {
      theme: 'snow',
      modules: { toolbar }
    });
  },

  open() {
    if (!state.editingSet) return;

    this.ensureQuill();

    const modal = document.getElementById('set-desc-modal');
    const line = document.getElementById('set-desc-product-line');

    if (line) {
      line.textContent = `MasterSet: ${state.editingSet.name || 'Unbenanntes Set'}`;
    }

    const currentHtml = String(state.editingSet.description || '').trim();

    this.quill.setContents([]);
    if (currentHtml) {
      this.quill.clipboard.dangerouslyPasteHTML(currentHtml);
    } else {
      this.quill.setText('');
    }

    modal?.classList.remove('hidden');
  },

  close() {
    document.getElementById('set-desc-modal')?.classList.add('hidden');
  },

  clear() {
    this.ensureQuill();
    this.quill.setText('');
  },

  save() {
    if (!state.editingSet) return;
    this.ensureQuill();

    const html = this.quill.root.innerHTML;
    state.editingSet.description = html;

    this.syncPreview();
    this.close();
    app.autoSave();
    ui.showStatus('BESCHREIBUNG GESPEICHERT');
  },

  syncPreview() {
    const preview = document.getElementById('set-desc-preview-text');
    if (!preview) return;

    const plain = String(state.editingSet?.description || '')
      .replace(/<[^>]*>/g, ' ')
      .replace(/\s+/g, ' ')
      .trim();

    const fallback = 'Angebotsbeschreibung bearbeiten...';

    const words = (plain || fallback).split(' ').filter(Boolean);
    const shortText = words.length > 8
      ? words.slice(0, 8).join(' ') + '...'
      : words.join(' ');

    preview.textContent = shortText;
    preview.setAttribute('title', plain || fallback); // full text on hover
  },

  getMaterialData() {
    const comps = Array.isArray(state.editingSet?.components) ? state.editingSet.components : [];

    const mainItems = comps.map(c => ({
      title: c.product_name || 'Unbenannt',
      qty: toNum(c.qty, 0),
      measure: c.measure || 'Stk',
      sub: Array.isArray(c.subComponents) ? c.subComponents : []
    }));

    const flatSub = [];
    mainItems.forEach(item => {
      item.sub.forEach(s => {
        flatSub.push({
          title: s.product_name || 'Unbenannt',
          qty: toNum(s.qty, 0),
          measure: s.measure || 'Stk'
        });
      });
    });

    return {
      setName: state.editingSet?.name || 'Unbenanntes Set',
      mainItems,
      subItems: flatSub
    };
  },

  generateAutoText() {
    const data = this.getMaterialData();
    const { setName, mainItems, subItems } = data;

    const mainCount = mainItems.length;
    const subCount = subItems.length;

    const mainList = mainItems.slice(0, 6).map(item =>
      `<li><strong>${escapeHtml(item.title)}</strong>${item.qty > 0 ? ` – ${item.qty} ${escapeHtml(item.measure)}` : ''}</li>`
    ).join('');

    const subList = subItems.slice(0, 8).map(item =>
      `<li>${escapeHtml(item.title)}${item.qty > 0 ? ` – ${item.qty} ${escapeHtml(item.measure)}` : ''}</li>`
    ).join('');

    const hasMoreMain = mainItems.length > 6;
    const hasMoreSub = subItems.length > 8;

    return `
      <h2>${escapeHtml(setName)}</h2>

      <p>
        Dieses MasterSet beinhaltet eine abgestimmte Zusammenstellung der benötigten Materialien und Komponenten
        für die vorgesehene Ausführung. Alle Positionen wurden strukturiert zusammengestellt, damit eine
        übersichtliche und nachvollziehbare Angebotsdarstellung möglich ist.
      </p>

      <p>
        Enthalten sind <strong>${mainCount} Hauptartikel</strong>
        ${subCount ? `sowie <strong>${subCount} ergänzende Unterartikel</strong>` : ''},
        die funktional und wirtschaftlich passend aufeinander abgestimmt sind.
      </p>

      ${mainItems.length ? `
        <h3>Hauptbestandteile</h3>
        <ul>
          ${mainList}
          ${hasMoreMain ? `<li>Weitere Positionen gemäß Detailaufstellung</li>` : ''}
        </ul>
      ` : ''}

      ${subItems.length ? `
        <h3>Ergänzende Komponenten / Zubehör</h3>
        <ul>
          ${subList}
          ${hasMoreSub ? `<li>Weitere Zusatzpositionen gemäß Detailaufstellung</li>` : ''}
        </ul>
      ` : ''}

      <p>
        Die genaue Ausführung, Mengenverteilung sowie eventuelle Alternativen oder Ergänzungen ergeben sich
        aus der vollständigen Material- und Leistungsübersicht des Angebots.
      </p>
    `;
  },

  autoWrite() {
    this.ensureQuill();

    const html = this.generateAutoText();
    this.quill.setContents([]);
    this.quill.clipboard.dangerouslyPasteHTML(html);

    ui.showStatus('TEXT ERSTELLT');
  },

  insertMaterialSummary() {
    this.ensureQuill();

    const data = this.getMaterialData();
    const allItems = [];

    data.mainItems.forEach(item => {
      allItems.push({
        title: item.title,
        qty: item.qty,
        measure: item.measure
      });

      (item.sub || []).forEach(sub => {
        allItems.push({
          title: '– ' + (sub.product_name || 'Unbenannt'),
          qty: toNum(sub.qty, 0),
          measure: sub.measure || 'Stk'
        });
      });
    });

    if (!allItems.length) {
      ui.showStatus('KEIN MATERIAL', true);
      return;
    }

    const html = `
      <h3>Materialübersicht</h3>
      <ul>
        ${allItems.map(item => `
          <li>
            ${escapeHtml(item.title)}
            ${item.qty > 0 ? ` – ${item.qty} ${escapeHtml(item.measure)}` : ''}
          </li>
        `).join('')}
      </ul>
    `;

    const range = this.quill.getSelection(true);
    if (range) {
      this.quill.clipboard.dangerouslyPasteHTML(range.index, html);
    } else {
      this.quill.clipboard.dangerouslyPasteHTML(this.quill.getLength(), html);
    }

    ui.showStatus('MATERIALLISTE EINGEFÜGT');
  }
};

window.setDescUI = setDescUI;


const materialDescUI = {
  quill: null,
  mainIdx: null,
  subIdx: null,

  ensureQuill() {
    if (this.quill) return;

    const toolbar = [
      [{ font: [] }, { size: [] }],
      ['bold', 'italic', 'underline', 'strike'],
      [{ color: [] }, { background: [] }],
      [{ header: 1 }, { header: 2 }],
      [{ list: 'ordered' }, { list: 'bullet' }],
      [{ align: [] }],
      ['link'],
      ['clean']
    ];

    this.quill = new Quill('#material-desc-quill', {
      theme: 'snow',
      modules: { toolbar }
    });
  },

  getTarget() {
    if (!state.editingSet?.components?.length) return null;

    const main = state.editingSet.components[this.mainIdx];
    if (!main) return null;

    if (this.subIdx === null || this.subIdx === undefined) return main;

    return main.subComponents?.[this.subIdx] || null;
  },

  open(mainIdx, subIdx = null) {
    this.mainIdx = Number(mainIdx);
    this.subIdx = (subIdx === null || subIdx === undefined || subIdx === 'null') ? null : Number(subIdx);

    const target = this.getTarget();
    if (!target) {
      ui.showStatus('BESCHREIBUNG NICHT GEFUNDEN', true);
      return;
    }

    this.ensureQuill();

    const modal = document.getElementById('material-desc-modal');
    const title = document.getElementById('material-desc-modal-title');
    const subtitle = document.getElementById('material-desc-modal-subtitle');

    const label = target.productTitle || target.product_name || target.description || 'Beschreibung';
    const html = String(target.description || '').trim();

    if (title) {
      title.textContent = this.subIdx === null ? 'Hauptartikel Beschreibung' : 'Unterartikel Beschreibung';
    }

    if (subtitle) {
      subtitle.textContent = label;
    }

    this.quill.setContents([]);
    if (html) {
      this.quill.clipboard.dangerouslyPasteHTML(html);
    } else {
      this.quill.setText('');
    }

    modal?.classList.remove('hidden');
  },

  save() {
    const target = this.getTarget();
    if (!target || !this.quill) return;

    const html = this.quill.root.innerHTML;

    target.description = html;

    this.close();
    ui.renderComponentItems();
    app.autoSave();
    ui.showStatus('BESCHREIBUNG GESPEICHERT');
  },

  close() {
    document.getElementById('material-desc-modal')?.classList.add('hidden');
    this.mainIdx = null;
    this.subIdx = null;
  }
};

window.materialDescUI = materialDescUI;



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
  // ===========================================================================
  // App Controller
  // ===========================================================================
  const app = {
      autoSave() {
          if (!state.editingSet) return;
          if (!state.autoSaveEnabled) return;

          pageState.save();

          clearTimeout(this._autoSaveTimer);
          this._autoSaveTimer = setTimeout(async () => {
            const s = state.editingSet;
            if (!s || !state.autoSaveEnabled) return;

            const needsReload =
              s.components.some(c => !c.id || c.subComponents.some(sub => !sub.id)) ||
              s.checklists.some(c => !c.id) ||
              s.tasks.some(t => !t.id) ||
              s.labor.some(l => !l.id);

            await api.saveSet(true, needsReload);
          }, 1000);
        },
      
        setAutoSave(enabled) {
            state.autoSaveEnabled = !!enabled;

            if (!state.autoSaveEnabled) {
              clearTimeout(this._autoSaveTimer);
              this._autoSaveTimer = null;
              ui.showStatus('AUTO SAVE AUS');
            } else {
              ui.showStatus('AUTO SAVE AN');
            }

            ui.render();
          },

      async navigate(view) {
        pageState.save();
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
      
      async setSetsTab(tab) {
            state.setsTab = (tab === 'group') ? 'group' : 'all';
            ui.render(); 
            // The fetch logic is now handled safely inside ui.render() to prevent duplicate calls.
        },

      setEditorTab(tab) {
        state.editorTab = tab;
        ui.render();
      },

      async selectGroup(group) {
        state.selectedGroup = group;
        await this.navigate('groupList');
      },

      searchGroups(q) {
        state.groupSearch = q ?? '';
        clearTimeout(this._searchTimer);
        this._searchTimer = setTimeout(async () => {
             await api.getGroups(state.groupSearch);
        }, 300);
      },

      async editSet(id) {
        state.view = 'editor';
        state.editorTab = 'material';
        ui.render();

        await Promise.all([
          api.loadSet(id),
          api.loadLaborOptions(),
          api.loadTaskOptions(''),
          api.loadChecklistOptions(''),
          api.loadCostingSets(state.selectedGroup?.id),
        ]);

        ui.render();

        const saved = pageState.load();
        pageState.restoreDom(saved);
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
        state.editorTab = 'material';
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

  async hydrateCurrentSetComponents() {
  if (!state.editingSet?.id) {
    ui.showStatus('SET FEHLT', true);
    return;
  }

  if (state.isLocked) {
    document.getElementById('locked-warning-modal')?.classList.remove('hidden');
    return;
  }

  const ok = confirm(
    'Alle importierten Komponenten dieses Sets neu mit Produktdaten abgleichen?\n\n' +
    'Es werden fehlende Felder wie Hersteller-Nr., Lieferanten-Nr. und Beschreibung nachgeladen.'
  );
  if (!ok) return;

  pageState.save();

  const res = await api.hydrateSetComponents(state.editingSet.id);
  if (!res || res.status !== 'ok') {
    ui.showStatus(res?.message || 'SYNC FEHLER', true);
    return;
  }

  if (res.data) {
    const oldSet = state.editingSet;
    const fresh = normalizeSet(res.data);

    // UI state preserve
    const oldComponents = oldSet.components || [];
    const newComponents = fresh.components || [];

    newComponents.forEach((nc, i) => {
      const oc = oldComponents[i];
      if (!oc) return;

      nc.isExpanded = oc.isExpanded ?? true;
      nc.isEditingProps = oc.isEditingProps ?? false;

      if (Array.isArray(nc.subComponents) && Array.isArray(oc.subComponents)) {
        nc.subComponents.forEach((ns, j) => {
          const os = oc.subComponents[j];
          if (!os) return;
          ns.isEditingProps = os.isEditingProps ?? false;
        });
      }
    });

    state.editingSet = {
      ...oldSet,
      ...fresh,
    };

    ui.recalculateLocalStats();
    ui.render();
    pageState.restoreDom(pageState.load());
  } else {
    await api.loadSet(state.editingSet.id);
  }

  if (state.selectedGroup?.id) {
    await api.getSets(state.selectedGroup.id);
    await api.getGroupSets(state.selectedGroup.id);
  }

  ui.showStatus('KOMPONENTEN AKTUALISIERT');
},

async hydrateAllSetsInGroup() {
  if (!state.selectedGroup?.id) {
    ui.showStatus('GRUPPE FEHLT', true);
    return;
  }

  const ok = confirm(
    'Alle Sets dieser Artikelgruppe aktualisieren?\n\n' +
    'Fehlende Hersteller-Nr., Lieferanten-Nr. und Beschreibung werden aus den Quelldaten nachgeladen.'
  );
  if (!ok) return;

  const res = await api.hydrateGroupComponents(state.selectedGroup.id);
  if (!res || res.status !== 'ok') {
    ui.showStatus(res?.message || 'SYNC FEHLER', true);
    return;
  }

  if (state.view === 'editor' && state.editingSet?.id) {
    await api.loadSet(state.editingSet.id);
  }

  await api.getSets(state.selectedGroup.id);
  await api.getGroupSets(state.selectedGroup.id);

  ui.showStatus(`AKTUALISIERT: ${res.updated_sets || 0} SETS`);
},

 

  getMaterialScrollState() {
  const wrap = document.getElementById('material-scroll-wrap');
  return {
    top: wrap ? wrap.scrollTop : 0,
    left: wrap ? wrap.scrollLeft : 0,
  };
},

restoreMaterialScrollState(saved) {
  if (!saved) return;

  requestAnimationFrame(() => {
    const wrap = document.getElementById('material-scroll-wrap');
    if (!wrap) return;

    wrap.scrollTop = saved.top || 0;
    wrap.scrollLeft = saved.left || 0;
  });
},

 
  refreshTaskOptionsPanel() {
    const panel = document.getElementById('tasks-options-panel');
    if (panel) {
      panel.innerHTML = this.getTaskOptionsHTML({ mode: 'inline' });
    }
  },

  ensureSetHasName(actionLabel = 'fortfahren') {
  const s = state.editingSet;
  if (!s) return false;

  const name = String(s.name || '').trim();
  if (name.length) return true;

  ui.showStatus('SET NAME REQUIRED', true);

  const titleInput = document.getElementById('set-title-input');
  if (titleInput) {
    titleInput.focus();
    titleInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  alert(`Bitte zuerst den Namen des Sets eingeben, bevor Sie ${actionLabel}.`);
  return false;
},

  removeLaborWithConfirm(idx) {
    const laborItem = state.editingSet?.labor?.[idx];
    if (!laborItem) return;

    const label = laborItem.name || laborItem.position_name || laborItem.employee_name || 'diese Position';

    const ok = confirm(
      `Möchten Sie "${label}" wirklich entfernen?\n\n` +
      `Diese Qualifikation wird dann auch aus allen Aufgaben im Tab "Aufgabe" gelöscht.`
    );

    if (!ok) return;

    this.removeLabor(idx);
  },
 

restoreMaterialViewState(saved) {
  if (!saved) return;

  requestAnimationFrame(() => {
    const wrap = document.querySelector('#comp-list .overflow-x-auto');
    if (wrap) {
      wrap.scrollLeft = saved.scrollLeft || 0;
      wrap.scrollTop = saved.scrollTop || 0;
    }

    

    if (!saved.focus) return;

    let target = null;

    if (saved.focus.id) {
      target = document.getElementById(saved.focus.id);
    }

    if (!target && saved.focus.field !== null && saved.focus.main !== null) {
      const selector = [
        `[data-field="${saved.focus.field}"]`,
        `[data-main-index="${saved.focus.main}"]`,
        saved.focus.sub !== null ? `[data-sub-index="${saved.focus.sub}"]` : ':not([data-sub-index])'
      ].join('');
      target = document.querySelector(selector);
    }

    if (target) {
      target.focus({ preventScroll: true });
      if (
        typeof saved.focus.selectionStart === 'number' &&
        typeof saved.focus.selectionEnd === 'number' &&
        typeof target.setSelectionRange === 'function'
      ) {
        target.setSelectionRange(saved.focus.selectionStart, saved.focus.selectionEnd);
      }
    }
  });
},
  getEffectiveCosting() {
  // Defaults = current global knobs (manual mode)
  const fallback = {
    aw: toNum(state.awMinutes ?? 6, 6),
    gk: toNum(state.globalGemeinkosten, 0),
    wagnis: toNum(state.globalWagnis, 0),
    profitPers: toNum(state.globalPersMargin, 0),
    profitMat: toNum(state.globalMatMargin, 0),
    provision: toNum(state.provisionPercent ?? 0, 0),
    source: 'manual',
  };

  const id = state.selectedCostingSetId;
  if (!id) return fallback;

  const cs = state.costingSetCache?.[id];
  if (!cs) return { ...fallback, source: 'costing_set_pending' };

  // Map your costing_sets columns (use what exists, fallback safely)
  const aw = toNum(cs.aw_minutes, fallback.aw);

  // GK: prefer labor_overhead_percent; fallback to material_overhead_percent
  const gk = (cs.labor_overhead_percent != null)
    ? toNum(cs.labor_overhead_percent, fallback.gk)
    : toNum(cs.material_overhead_percent, fallback.gk);

  const wagnis = toNum(cs.risk_percent, fallback.wagnis);

  // Gewinn fields differ per schema/project; try common names, fallback to global
  const profitPers =
    (cs.profit_labor_percent != null) ? toNum(cs.profit_labor_percent, fallback.profitPers) :
    (cs.profit_personal_percent != null) ? toNum(cs.profit_personal_percent, fallback.profitPers) :
    (cs.profit_percent != null) ? toNum(cs.profit_percent, fallback.profitPers) :
    fallback.profitPers;

  const profitMat =
    (cs.profit_material_percent != null) ? toNum(cs.profit_material_percent, fallback.profitMat) :
    fallback.profitMat;

  const provision =
    (cs.provision_percent != null) ? toNum(cs.provision_percent, fallback.provision) :
    (cs.provision != null) ? toNum(cs.provision, fallback.provision) :
    fallback.provision;

  return { aw, gk, wagnis, profitPers, profitMat, provision, source: 'costing_set' };
},

 
  populateCostingSetSelect() {
  const sel = document.getElementById('cm_costing_set_id');
  if (!sel) return;

  const list = Array.isArray(state.costingSets) ? state.costingSets : [];
  const current = state.selectedCostingSetId;

  sel.innerHTML = `
    <option value="">— Manuell —</option>
    ${list.map(cs => `
      <option value="${cs.id}" ${String(cs.id) === String(current) ? 'selected' : ''}>
        ${ui.escapeHtml(cs.name || ('Costing Set #' + cs.id))}
      </option>
    `).join('')}
  `;
},

async onCostingSetChange(id) {
  state.selectedCostingSetId = id || null;

  if (!id) {
    ui.showStatus('MANUELL');
    ui.costingRecalcPreview();
    return;
  }

  const cs = await api.showCostingSet(id);
  if (!cs) return ui.showStatus('COSTING SET ERROR', true);

  ui.applyCostingSetToModal(cs);
  ui.costingRecalcPreview();
  ui.showStatus('COSTING SET GELADEN');
},

applyCostingSetToModal(cs) {
  // Map your costing_sets fields to your modal inputs
  // Adjust if your column names differ.
  const aw = toNum(cs.aw_minutes, 6);

  // You currently have ONE GK% in the modal.
  // Prefer labor_overhead_percent if present, else material_overhead_percent.
  const gk = (cs.labor_overhead_percent != null)
    ? toNum(cs.labor_overhead_percent, 10)
    : toNum(cs.material_overhead_percent, 10);

  const wagnis = toNum(cs.risk_percent, 5);

  // If you later add profit fields to costing_sets, map them here:
  // const profitPers = toNum(cs.profit_labor_percent, toNum($('#cm_profit_pers').value, 30));
  // const profitMat  = toNum(cs.profit_material_percent, toNum($('#cm_profit_mat').value, 50));

  // For now keep your current profit defaults:
  const profitPers = toNum(document.getElementById('cm_profit_pers')?.value, 30);
  const profitMat  = toNum(document.getElementById('cm_profit_mat')?.value, 50);

  const provision = toNum(cs.provision_percent ?? cs.provision ?? 0, 0);

  const awEl = document.getElementById('cm_aw_minutes');
  const gkEl = document.getElementById('cm_gk');
  const wEl  = document.getElementById('cm_wagnis');
  const ppEl = document.getElementById('cm_profit_pers');
  const pmEl = document.getElementById('cm_profit_mat');
  const prEl = document.getElementById('cm_provision');

  if (awEl) awEl.value = aw;
  if (gkEl) gkEl.value = gk;
  if (wEl)  wEl.value  = wagnis;
  if (ppEl) ppEl.value = profitPers;
  if (pmEl) pmEl.value = profitMat;
  if (prEl) prEl.value = provision;
},

toggleTaskLaborLock(taskIdx, laborIdx, field){
  const t = state.editingSet?.tasks?.[taskIdx];
  const tl = t?.task_labor?.[laborIdx];
  if (!tl) return;

  // normalize defaults: if undefined -> treat as locked
  if (field === 'unit_rate') {
    const locked = ui.isTaskLaborUnitRateLocked(tl);
    // toggle: locked -> unlock, unlock -> lock
    tl.unit_rate_locked = locked ? false : true;
  }

  if (field === 'ek_total') {
    const locked = ui.isTaskLaborEKTotalLocked(tl);
    tl.ek_total_locked = locked ? false : true;
  }

  ui.renderTasksTab();
  app.autoSave();
},
  // default lock rules
isTaskLaborUnitRateLocked(tl){
  // locked by default unless explicitly unlocked
  return tl?.unit_rate_locked !== false;
},
isTaskLaborEKTotalLocked(tl){
  return tl?.ek_total_locked !== false;
},

 // ✅ wrappers used by onclick handlers
toggleTaskLaborUnitRateLock(taskIdx, laborIdx){
  return ui.toggleTaskLaborLock(taskIdx, laborIdx, 'unit_rate');
},
toggleTaskLaborEKTotalLock(taskIdx, laborIdx){
  return ui.toggleTaskLaborLock(taskIdx, laborIdx, 'ek_total');
},
syncSummaryWithTaskLabor() {
  const tasks = Array.isArray(state.editingSet?.tasks) ? state.editingSet.tasks : [];
  const anyOpen = tasks.some(t => !!t.isLaborExpanded);

  // default hidden
  // only auto-hide when open, but do not auto-show again
  if (anyOpen) {
    state.showSummary = false;
  }
},


toggleTaskLabor(taskIndex) {
  const t = state.editingSet.tasks?.[taskIndex];
  if (!t) return;

  // toggle the local UI state
  t.isLaborExpanded = !t.isLaborExpanded;

  // ✅ auto hide/show summary based on open accordions
  ui.syncSummaryWithTaskLabor();

  // summary card lives in editor layout => full render
  ui.render();
},


weekHours: 40, // ✅ configurable (1 Woche = 40h)

/** factor to convert qty -> hours */
taskLaborUnitFactor(unit){
  const u = String(unit || 'h').toLowerCase();
  if (u === 'min' || u === 'minute' || u === 'minuten') return 1/60;
  if (u === 'woche' || u === 'week') return (toNum(this.weekHours, 40));
  return 1; // 'h'
},

/** ensure fields exist (backward compatible with existing data that only has hours/rate) */
ensureTaskLaborFields(tl){
  if (!tl) return tl;

  tl.unit = tl.unit || 'h';
  tl.hours = toNum(tl.hours, 0);
  tl.rate  = toNum(tl.rate, 0);

  // ✅ DEFAULT LOCKED if missing
  if (tl.unit_rate_locked === undefined) tl.unit_rate_locked = true;
  if (tl.ek_total_locked === undefined) tl.ek_total_locked = true;

  // keep qty in selected unit, derived from hours
  const f = this.taskLaborUnitFactor(tl.unit);
  tl.qty = (tl.qty != null) ? toNum(tl.qty, 0) : (f > 0 ? (tl.hours / f) : tl.hours);

  return tl;
},
/** compute row EK/VK using canonical hours + hourly rate */
calcTaskLaborRow(tl){
  this.ensureTaskLaborFields(tl);

  const hours = toNum(tl.hours, 0);
  const rateH = toNum(tl.rate, 0);

  const baseEK = hours * rateH;

  const gk = baseEK * (toNum(state.globalGemeinkosten, 0) / 100);
  const wagnis = baseEK * (toNum(state.globalWagnis, 0) / 100);
  const profit = baseEK * (toNum(state.globalPersMargin, 0) / 100);

  const vk = baseEK + gk + wagnis + profit;

  // unit price shown in UI (€/unit)
  const unit = String(tl.unit || 'h').toLowerCase();
  const f = this.taskLaborUnitFactor(unit);
  // unitRate = €/unit = €/h * hoursPerUnit
  const unitRate = rateH * (unit === 'min' || unit === 'minute' || unit === 'minuten' ? (1/60) : (unit === 'woche' || unit === 'week' ? toNum(this.weekHours, 40) : 1));

  return { hours, rateH, unit, unitRate, baseEK, vk, gk, wagnis, profit };
},

openInfoModal(title, html){
  const modal = document.getElementById('modal-container');
  const titleEl = document.getElementById('modal-title');
  const searchBox = document.getElementById('modal-search-box');
  const contentEl = document.getElementById('modal-content');

  if (!modal || !titleEl || !contentEl) return;

  titleEl.innerHTML = `<i class="fas fa-circle-info" style="color:var(--primary)"></i> <span>${ui.escapeHtml(title || 'Info')}</span>`;
  if (searchBox) searchBox.classList.add('hidden');

  contentEl.innerHTML = `
    <div style="background:#fff; border:1px solid var(--border-color); border-radius:16px; padding:1rem; line-height:1.55;">
      ${html || ''}
    </div>
  `;

  modal.classList.remove('hidden');
},

/** qty input changed (qty in selected unit) */
updateTaskLaborQty(taskIdx, laborIdx, val){
  const t = state.editingSet.tasks?.[taskIdx];
  if (!t?.task_labor?.[laborIdx]) return;

  const tl = t.task_labor[laborIdx];
  this.ensureTaskLaborFields(tl);

  const qty = Math.max(0, toNum(val, 0));
  tl.qty = qty;

  const f = this.taskLaborUnitFactor(tl.unit);
  tl.hours = (String(tl.unit).toLowerCase() === 'woche' || String(tl.unit).toLowerCase() === 'week')
    ? qty * toNum(this.weekHours, 40)
    : qty * f;

  this.syncGlobalLaborFromTasks();
  this.recalculateLocalStats();
},

/** unit changed (convert qty to keep same HOURS) */
updateTaskLaborUnit(taskIdx, laborIdx, unit){
  const t = state.editingSet.tasks?.[taskIdx];
  if (!t?.task_labor?.[laborIdx]) return;

  const tl = t.task_labor[laborIdx];
  this.ensureTaskLaborFields(tl);

  const oldHours = toNum(tl.hours, 0);

  tl.unit = unit || 'h';

  const f = this.taskLaborUnitFactor(tl.unit);
  tl.qty = f > 0 ? (oldHours / f) : oldHours;

  // keep hours unchanged
  tl.hours = oldHours;

  this.syncGlobalLaborFromTasks();
  this.recalculateLocalStats();
},

updateTaskLaborEKTotal(taskIdx, laborIdx, val){
  const t = state.editingSet.tasks?.[taskIdx];
  const tl = t?.task_labor?.[laborIdx];
  if (!tl) return;

  ui.ensureTaskLaborFields(tl);

  const ek = Math.max(0, toNum(val, 0));
  tl.ek_total_override = ek;

  // Convert override EK back into hourly rate based on hours
  const hours = Math.max(0.000001, toNum(tl.hours, 0));
  tl.rate = ek / hours;

  ui.syncGlobalLaborFromTasks();
  ui.recalculateLocalStats();
},

clearTaskLaborEKOverride(taskIdx, laborIdx){
  const t = state.editingSet.tasks?.[taskIdx];
  const tl = t?.task_labor?.[laborIdx];
  if (!tl) return;

  tl.ek_total_override = null;

  // restore rate from qualification default (if exists)
  const q = (state.laborOptions || []).find(x => String(x.id) === String(tl.qualification_id));
  if (q) tl.rate = toNum(q.default_price, tl.rate);

  ui.syncGlobalLaborFromTasks();
  ui.recalculateLocalStats();
},


/** einzelpreis (€/unit) edited -> update canonical hourly rate */
updateTaskLaborUnitRate(taskIdx, laborIdx, val){
  const t = state.editingSet.tasks?.[taskIdx];
  if (!t?.task_labor?.[laborIdx]) return;

  const tl = t.task_labor[laborIdx];
  this.ensureTaskLaborFields(tl);

  const unit = String(tl.unit || 'h').toLowerCase();
  const unitRate = Math.max(0, toNum(val, 0));

  // reverse to €/h
  if (unit === 'min' || unit === 'minute' || unit === 'minuten') {
    tl.rate = unitRate * 60;
  } else if (unit === 'woche' || unit === 'week') {
    tl.rate = toNum(this.weekHours, 40) > 0 ? (unitRate / toNum(this.weekHours, 40)) : 0;
  } else {
    tl.rate = unitRate;
  }

  this.syncGlobalLaborFromTasks();
  this.recalculateLocalStats();
},



openCostingModal() {
  if (!state.editingSet) return;

  // Prefill from your global knobs (so it matches calculations)
      // ensure dropdown is populated every time modal opens
    ui.populateCostingSetSelect();

    // if a costing set was already selected, apply it immediately
    if (state.selectedCostingSetId) {
      api.showCostingSet(state.selectedCostingSetId).then(cs => {
        if (cs) ui.applyCostingSetToModal(cs);
        ui.costingRecalcPreview();
      });
    } else {
      ui.costingRecalcPreview();
    }
  $('#cm_aw_minutes').value    = toNum(state.awMinutes ?? 6, 6);
  $('#cm_gk').value           = toNum(state.globalGemeinkosten, 10);
  $('#cm_wagnis').value       = toNum(state.globalWagnis, 5);
  $('#cm_profit_pers').value  = toNum(state.globalPersMargin, 30);
  $('#cm_profit_mat').value   = toNum(state.globalMatMargin, 50);
  $('#cm_provision').value    = toNum(state.provisionPercent ?? 0, 0);

  document.getElementById('costing-modal')?.classList.remove('hidden');
  this.costingRecalcPreview();
},

closeCostingModal() {
  document.getElementById('costing-modal')?.classList.add('hidden');
},

costingRecalcPreview() {
  const tasks = state.editingSet?.tasks || [];
  const labor = state.editingSet?.labor || [];

  // Inputs
  const gk = toNum($('#cm_gk')?.value, 0) / 100;
  const wagnis = toNum($('#cm_wagnis')?.value, 0) / 100;
  const profitPers = toNum($('#cm_profit_pers')?.value, 0) / 100;
  const provision = toNum($('#cm_provision')?.value, 0) / 100;

  // Aggregate from tasks->task_labor (preferred), fallback to labor table
  const totals = {}; // qid => {name, rate, hours}
  tasks.forEach(t => {
    (t.task_labor || []).forEach(tl => {
      const qid = String(tl.qualification_id || '');
      if (!qid) return;
      if (!totals[qid]) totals[qid] = { name: tl.name || '—', rate: toNum(tl.rate, 0), hours: 0 };
      totals[qid].hours += toNum(tl.hours, 0);
      // keep latest rate
      totals[qid].rate = toNum(tl.rate, totals[qid].rate);
    });
  });

  // if no task_labor entries exist, use labor table
  if (Object.keys(totals).length === 0) {
    labor.forEach(l => {
      const qid = String(l.qualification_id || '');
      if (!qid) return;
      totals[qid] = { name: l.name || '—', rate: toNum(l.rate, 0), hours: toNum(l.hours, 0) };
    });
  }

  let totalHours = 0;
  let totalVK = 0;

  const rows = Object.values(totals)
    .filter(x => x.hours > 0)
    .map(x => {
      const baseEK = x.rate * x.hours;

      const addGK = baseEK * gk;
      const addWagnis = baseEK * wagnis;
      const addProfit = baseEK * profitPers;

      let vk = baseEK + addGK + addWagnis + addProfit;
      const addProvision = vk * provision;
      vk += addProvision;

      totalHours += x.hours;
      totalVK += vk;

      return { ...x, baseEK, addGK, addWagnis, addProfit, addProvision, vk };
    });

  // Render preview
  const box = document.getElementById('cm_preview');
  const badge = document.getElementById('cm_preview_badge');
  const outVK = document.getElementById('cm_total_vk');
  const outH = document.getElementById('cm_total_hours');

  badge.textContent = `${rows.length} Qualifikationen`;
  outVK.textContent = ui.formatMoney(totalVK);
  outH.textContent = `${totalHours.toFixed(2)} h`;

  if (!rows.length) {
    box.innerHTML = `<div style="padding:1rem; text-align:center; color:#94a3b8; font-weight:800;">Keine Qualifikationen/Stunden gefunden.</div>`;
    return;
  }

  box.innerHTML = rows.map(r => `
    <div style="border:1px solid #e2e8f0; border-radius:14px; padding:.85rem;">
      <div style="display:flex; justify-content:space-between; gap:1rem;">
        <div style="min-width:0;">
          <div style="font-weight:900; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
            ${ui.escapeHtml(r.name)}
          </div>
          <div style="font-size:.75rem; font-weight:800; color:#94a3b8; margin-top:2px;">
            ${r.hours.toFixed(2)}h × ${r.rate.toFixed(2)} €/h
          </div>
        </div>
        <div style="text-align:right;">
          <div style="font-size:10px; font-weight:900; color:#94a3b8; text-transform:uppercase;">VK</div>
          <div style="font-weight:900;">${ui.formatMoney(r.vk)}</div>
        </div>
      </div>

      <div style="margin-top:.6rem; display:grid; grid-template-columns: repeat(5, 1fr); gap:.35rem; font-size:10px; font-weight:800; color:#64748b;">
        <div>EK: ${ui.formatMoney(r.baseEK)}</div>
        <div>GK: ${ui.formatMoney(r.addGK)}</div>
        <div>Wagnis: ${ui.formatMoney(r.addWagnis)}</div>
        <div>Gewinn: ${ui.formatMoney(r.addProfit)}</div>
        <div>Prov.: ${ui.formatMoney(r.addProvision)}</div>
      </div>
    </div>
  `).join('');
},

costingApplyToGlobals() {
  // Apply back to your existing global knobs so all calculations stay consistent
  state.awMinutes = toNum($('#cm_aw_minutes')?.value, 6);
  state.globalGemeinkosten = toNum($('#cm_gk')?.value, 10);
  state.globalWagnis = toNum($('#cm_wagnis')?.value, 5);
  state.globalPersMargin = toNum($('#cm_profit_pers')?.value, 30);
  state.globalMatMargin = toNum($('#cm_profit_mat')?.value, 50);
  state.provisionPercent = toNum($('#cm_provision')?.value, 0);

  ui.recalculateLocalStats();
  ui.render();
  app.autoSave();
  ui.showStatus('COSTING ÜBERNOMMEN');
},

  getMaterialUnitBase(item) {
      return toNum(
        item?.purchasePrice ?? item?.purchase_price ?? item?.unit_price,
        0
      );
    },

    getMaterialQty(item) {
      return Math.max(0, toNum(item?.quantity ?? item?.qty, 0));
    },

    getMaterialMargin(item) {
      return toNum(item?.margin, state.globalMatMargin);
    },

    getMaterialLineTotal(item) {
        const base = this.getMaterialUnitBase(item);
        const qty = this.getMaterialQty(item);
        const margin = this.getMaterialMargin(item);
        const pe = Math.max(1, toNum(item.price_unit, 1)); // ✅ Get PE

        const gk = base * (toNum(state.globalGemeinkosten, 0) / 100);
        const wagnis = base * (toNum(state.globalWagnis, 0) / 100);
        const profit = base * (margin / 100);

        const sellPerUnit = base + gk + wagnis + profit;

        // ✅ Divide by PE
        return (sellPerUnit / pe) * qty; 
      },



  toggleSummary() {
        state.showSummary = !state.showSummary;
        this.render(); // Re-render layout to expand/collapse
    },
  toggleMatDrop(e) {
      if (e) e.stopPropagation();
      state.showMatDrop = !state.showMatDrop;
      this.renderComponentItems();
    },

        // Inside ui object
    toggleSetLock() {
        const s = state.editingSet;
        if (!s) return;

        // Toggle: if currently 1 (unlocked), set to 0 (locked). If 0, set to 1.
        const currentStatus = parseInt(s.is_locked ?? 1);
        const newStatus = (currentStatus === 1) ? 0 : 1;

        s.is_locked = newStatus;
        
        // Update global lock state to sync UI components
        state.isLocked = (newStatus === 0);

        ui.showStatus(newStatus === 0 ? 'SET GESPERRT' : 'SET FREIGESCHALTET');
        
        // Re-render UI to apply disabled states and show warning
        ui.render();
        
        // Auto-save the change to DB
        app.autoSave();
    },

    toggleMatCol(key) {
        // 1. Save the current scroll position of the dropdown and the page
        const dropList = document.querySelector('#mat-col-dropdown-wrap .overflow-y-auto');
        const dropScroll = dropList ? dropList.scrollTop : 0;
        const pageScroll = window.scrollY;

        // 2. Update the state
        state.visibleColMat[key] = !state.visibleColMat[key];
        
        // 3. Rebuild the HTML
        this.renderComponentItems();

        // 4. Instantly restore the scroll positions after the HTML is painted
        requestAnimationFrame(() => {
          const newDropList = document.querySelector('#mat-col-dropdown-wrap .overflow-y-auto');
          if (newDropList) {
            newDropList.scrollTop = dropScroll;
          }
          window.scrollTo(0, pageScroll);
        });
      },
    // ------------------------------
    // Dashboard / Lists
    // ------------------------------
    escapeHtml(s) {
        return String(s || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    },


    toggleMaterialMenu(btn) {
        document.querySelectorAll('.material-actions-menu.open').forEach(el => {
          if (el !== btn.closest('.material-actions-menu')) {
            el.classList.remove('open');
          }
        });

        const wrap = btn.closest('.material-actions-menu');
        if (wrap) wrap.classList.toggle('open');
      },

      closeAllMaterialMenus() {
        document.querySelectorAll('.material-actions-menu.open').forEach(el => {
          el.classList.remove('open');
        });
      },

    initMaterialTableSortables() {
        const mainBody = document.getElementById('material-main-body');
        if (!mainBody || !window.Sortable) return;

        // destroy old instances
        if (this._materialMainSortable) {
          try { this._materialMainSortable.destroy(); } catch (e) {}
          this._materialMainSortable = null;
        }

        if (!this._materialSubSortables) this._materialSubSortables = {};
        Object.keys(this._materialSubSortables).forEach((k) => {
          try { this._materialSubSortables[k].destroy(); } catch (e) {}
        });
        this._materialSubSortables = {};

        // MAIN rows: drag only main rows
        this._materialMainSortable = new Sortable(mainBody, {
          draggable: '.material-row-main',
          handle: '.handle',
          animation: 150,
          ghostClass: 'ghost',
          onEnd: (evt) => {
            const oldIndex = evt.oldDraggableIndex;
            const newIndex = evt.newDraggableIndex;

            if (oldIndex == null || newIndex == null || oldIndex === newIndex) return;

            const comps = Array.isArray(state.editingSet?.components)
              ? state.editingSet.components
              : [];

            if (
              oldIndex < 0 ||
              newIndex < 0 ||
              oldIndex >= comps.length ||
              newIndex >= comps.length
            ) {
              console.warn('Main drag reorder failed: invalid indexes', {
                oldIndex,
                newIndex,
                length: comps.length,
                evt
              });
              return;
            }

            const moved = comps.splice(oldIndex, 1)[0];
            if (!moved) {
              console.warn('Main drag reorder failed: moved item is undefined', {
                oldIndex,
                newIndex,
                evt
              });
              return;
            }

            comps.splice(newIndex, 0, moved);

            // optional: refresh position numbers
            comps.forEach((item, idx) => {
              item.pos = idx + 1;
            });

            ui.recalculateLocalStats();
            ui.renderComponentItems();
            app.autoSave();
          }
        });

        // SUB rows: one sortable per parent
        (state.editingSet.components || []).forEach((comp, mainIdx) => {
          const subRows = Array.from(
            document.querySelectorAll(`.material-row-sub[data-parent-index="${mainIdx}"]`)
          );

          if (!subRows.length) return;

          const holder = document.createElement('div');
          holder.id = `material-sub-sort-${mainIdx}`;
          holder.style.display = 'contents';

          const first = subRows[0];
          first.parentNode.insertBefore(holder, first);
          subRows.forEach((row) => holder.appendChild(row));

          this._materialSubSortables[mainIdx] = new Sortable(holder, {
            draggable: '.material-row-sub',
            handle: '.sub-handle',
            animation: 150,
            ghostClass: 'ghost',
            onEnd: (evt) => {
              const oldIndex = evt.oldIndex;
              const newIndex = evt.newIndex;

              if (oldIndex == null || newIndex == null || oldIndex === newIndex) return;

              const subs = Array.isArray(state.editingSet.components[mainIdx]?.subComponents)
                ? state.editingSet.components[mainIdx].subComponents
                : [];

              if (
                oldIndex < 0 ||
                newIndex < 0 ||
                oldIndex >= subs.length ||
                newIndex >= subs.length
              ) {
                console.warn('Sub drag reorder failed: invalid indexes', {
                  mainIdx,
                  oldIndex,
                  newIndex,
                  length: subs.length,
                  evt
                });
                return;
              }

              const moved = subs.splice(oldIndex, 1)[0];
              if (!moved) {
                console.warn('Sub drag reorder failed: moved sub item is undefined', {
                  mainIdx,
                  oldIndex,
                  newIndex,
                  evt
                });
                return;
              }

              subs.splice(newIndex, 0, moved);

              ui.recalculateLocalStats();
              ui.renderComponentItems();
              app.autoSave();
            }
          });
        });
      },

    toggleMaterialCollapse(mainIdx) {
      if (!state.materialCollapsed) state.materialCollapsed = {};
      state.materialCollapsed[mainIdx] = !state.materialCollapsed[mainIdx];
      this.renderComponentItems();
    },

  filterMaterialTable(value) {
        const term = String(value || '').toLowerCase().trim();

        // Persist search so rerenders keep it active
        state.materialSearch = term;

        // Select all main group wrappers
        const groups = document.querySelectorAll('#material-main-body > .group-row');

        groups.forEach(groupEl => {
          const mainIdx = groupEl.getAttribute('data-main-index');
          const item = state.editingSet.components[mainIdx];
          if (!item) return;

          // Helper function to extract all possible searchable text from an item's data
          const buildSearchString = (c) => {
            return [
              c.articleNumber, c.article_no,
              c.productTitle, c.product_name,
              c.description,
              c.supplier, c.distributor_name,
              c.unit_price, c.purchasePrice,
              c.qty, c.quantity
            ].filter(Boolean).join(' ').toLowerCase();
          };

          // 1. Check if the Main Item matches
          const mainStr = buildSearchString(item);
          const mainMatch = !term || mainStr.includes(term);

          let anySubMatch = false;

          // 2. Check all Sub-Items independently
          const subRows = groupEl.querySelectorAll('.material-row-sub[data-sub-index]');
          subRows.forEach(subEl => {
            const sIdx = subEl.getAttribute('data-sub-index');
            const subItem = item.subComponents?.[sIdx];
            if (!subItem) return;

            const subStr = buildSearchString(subItem);
            const subMatch = !term || subStr.includes(term);

            if (subMatch) anySubMatch = true;

            // If the main item matched the term, show all its subs. 
            // If the main item didn't match, ONLY show the specific subs that matched.
            subEl.style.display = (mainMatch || subMatch) ? '' : 'none';
          });

          // 3. Show the entire group if the main item matched, OR if ANY sub item matched.
          groupEl.style.display = (mainMatch || anySubMatch) ? '' : 'none';

          // Ensure the sub-list wrapper itself is visible if we have subs to show
          const subListWrap = groupEl.querySelector(`#sub-list-${mainIdx}`);
          if (subListWrap) {
            subListWrap.style.display = (mainMatch || anySubMatch) ? '' : 'none';
          }
        });
      },

    _distributorCompareChart: null,

    setDistributorCompareContext(mainIdx, subIdx = null) {
      state.compareContext = {
        mainIdx: Number(mainIdx),
        subIdx: subIdx === null ? null : Number(subIdx)
      };
    },

    getDistributorCompareTarget() {
      const ctx = state.compareContext || {};
      const mIdx = Number.isInteger(ctx.mainIdx) ? ctx.mainIdx : null;
      const sIdx = ctx.subIdx;

      if (mIdx === null || !state.editingSet?.components?.[mIdx]) return null;

      if (sIdx === null) {
        return {
          item: state.editingSet.components[mIdx],
          mainIdx: mIdx,
          subIdx: null
        };
      }

      const sub = state.editingSet.components[mIdx]?.subComponents?.[sIdx];
      if (!sub) return null;

      return {
        item: sub,
        mainIdx: mIdx,
        subIdx: sIdx
      };
    },

   applyDistributorCompareSelection(encodedOrObject) {
      let payload;

      try {
        payload = (typeof encodedOrObject === 'string')
          ? (encodedOrObject.trim().startsWith('{')
              ? JSON.parse(encodedOrObject)
              : b64DecodeJson(encodedOrObject))
          : encodedOrObject;
      } catch (e) {
        console.error('Distributor compare payload decode failed:', e);
        ui.showStatus('PAYLOAD ERROR', true);
        return;
      }

      const target = ui.getDistributorCompareTarget();
      if (!target || !target.item) {
        ui.showStatus('NO TARGET', true);
        return;
      }

      const item = target.item;

      // Extract the new price from the payload
      const nextPrice = toNum(
        payload.purchase_price ?? payload.price ?? payload.unit_price ?? 0,
        0
      );

      item.distributor_price_id = payload.distributor_price_id ?? item.distributor_price_id ?? null;
      item.distributor_id = payload.distributor_id ?? item.distributor_id ?? null;
      item.distributor_name = payload.distributor_name ?? item.distributor_name ?? '—';

      item.distributor_availability = payload.availability ?? null;
      item.distributor_article_no = payload.article_no ?? null;
      item.distributor_price_date = payload.price_date ?? null;

      // ✅ FIX: Update ALL price fields so the calculation engine catches the change immediately
      item.purchasePrice = nextPrice;
      item.purchase_price = nextPrice;
      item.unit_price = nextPrice;
      item.base_unit_price = nextPrice;
      item.is_price_overridden = false;

      item.measure = item.measure || 'Stk';
      item.price_unit = Math.max(1, toNum(item.price_unit, 1));

      ui.recalculateLocalStats();
      ui.render();
      ui.closeDistributorCompareModal();
      ui.showStatus('LIEFERANT ÜBERNOMMEN');
      app.autoSave();
    },

    async openDistributorCompare(productId, distributorId) {
      if (!productId || !distributorId) {
        ui.showStatus('PRODUKT/LIEFERANT FEHLT', true);
        return;
      }

      const modal = document.getElementById('distributor-compare-modal');
      const summary = document.getElementById('distributor-compare-summary');
      const list = document.getElementById('distributor-compare-list');
      const best = document.getElementById('distributor-compare-best');

      if (!modal || !summary || !list) return;

      summary.innerHTML = `
        <div style="background:white; border:1px solid var(--border-color); border-radius:16px; padding:1rem;">
          <div style="padding:1rem; text-align:center; color:#94a3b8; font-weight:800;">Lade Vergleich...</div>
        </div>
      `;
      list.innerHTML = `
        <div style="padding:1rem; text-align:center; color:#94a3b8; font-weight:800;">Lade Daten...</div>
      `;
      if (best) best.innerHTML = '';

      modal.classList.remove('hidden');

      const res = await api.request(`/distributor-compare/product/${productId}/distributor/${distributorId}`, 'GET');
      if (!res?.data) {
        ui.showStatus('VERGLEICH FEHLGESCHLAGEN', true);
        return;
      }

      const data = res.data;
      const product = data.product || {};
      const items = Array.isArray(data.items) ? data.items : [];
      const cheapest = data.cheapest || null;
      const currentPrice = data.current_price;

      summary.innerHTML = `
        <div style="background:white; border:1px solid var(--border-color); border-radius:16px; padding:1rem;">
          <div style="display:flex; justify-content:space-between; gap:1rem; align-items:flex-start;">
            <div>
              <div style="font-size:1rem; font-weight:900; color:var(--text-main);">
                ${ui.escapeHtml(product.name || 'Produkt')}
              </div>
              <div style="font-size:.75rem; font-weight:700; color:#94a3b8; margin-top:4px;">
                Art.-Nr.: ${ui.escapeHtml(product.article_no || '—')}
                ${product.model ? `• Modell: ${ui.escapeHtml(product.model)}` : ''}
              </div>
            </div>

            <div style="text-align:right;">
              <div style="font-size:.7rem; font-weight:900; color:#94a3b8; text-transform:uppercase;">Aktueller EK</div>
              <div style="font-size:1.35rem; font-weight:900; color:var(--text-main);">
                ${currentPrice != null ? ui.formatMoney(currentPrice) : '—'}
              </div>
            </div>
          </div>

          ${
            cheapest && cheapest.effective_price != null
              ? `
                <div style="margin-top:1rem; display:flex; gap:.75rem; flex-wrap:wrap;">
                  <span class="pill pill-green">
                    <div class="dot bg-green"></div>
                    Günstigster: ${ui.escapeHtml(cheapest.distributor_name || '—')}
                  </span>
                  <span class="pill pill-blue">
                    <div class="dot bg-blue"></div>
                    ${ui.formatMoney(cheapest.effective_price)}
                  </span>
                </div>
              `
              : ''
          }
        </div>
      `;

      if (best) {
        best.innerHTML = cheapest?.distributor_name
          ? `Bestpreis: <strong>${ui.escapeHtml(cheapest.distributor_name)}</strong>`
          : '';
      }

      list.innerHTML = items.length
        ? items.map(item => {
            const diff = item.difference_from_current;
            const diffText = diff == null
              ? '—'
              : diff === 0
                ? 'gleich'
                : diff < 0
                  ? `${ui.formatMoney(Math.abs(diff))} günstiger`
                  : `${ui.formatMoney(diff)} teurer`;

            const currentBadge = item.is_current
              ? `<span class="pill pill-blue" style="margin:0;"><div class="dot bg-blue"></div><span>Aktuell</span></span>`
              : '';

            const availabilityScore = Number(item.availability_score || 0);

            const encoded = b64EncodeJson({
              distributor_price_id: item.id,
              distributor_id: item.distributor_id,
              distributor_name: item.distributor_name || '—',
              price: item.price,
              purchase_price: item.purchase_price,
              availability: item.availability,
              article_no: item.article_no,
              price_date: item.price_date
            });

            return `
              <div style="border:1px solid ${item.is_current ? 'var(--primary)' : 'var(--border-color)'}; background:${item.is_current ? 'rgba(116,178,212,.06)' : 'white'}; border-radius:14px; padding:.9rem;">
                <div style="display:flex; justify-content:space-between; gap:.75rem; align-items:flex-start;">
                  <div>
                    <div style="font-weight:900; color:var(--text-main); display:flex; align-items:center; gap:.5rem; flex-wrap:wrap;">
                      <span>${ui.escapeHtml(item.distributor_name || 'Lieferant')}</span>
                      ${currentBadge}
                    </div>

                    <div style="font-size:.72rem; font-weight:700; color:#94a3b8; margin-top:4px;">
                      Art.-Nr.: ${ui.escapeHtml(item.article_no || '—')}
                    </div>

                    <div style="font-size:.72rem; font-weight:700; color:#64748b; margin-top:4px;">
                      Verfügbarkeit: ${ui.escapeHtml(item.availability || '—')}
                      ${item.price_date ? `• Stand: ${ui.escapeHtml(item.price_date)}` : ''}
                    </div>
                  </div>

                  <div style="text-align:right;">
                    <div style="font-size:.95rem; font-weight:900; color:var(--text-main);">
                      ${item.effective_price != null ? ui.formatMoney(item.effective_price) : '—'}
                    </div>
                    <div style="font-size:.7rem; font-weight:800; color:${diff == null ? '#94a3b8' : (diff < 0 ? 'var(--accent)' : (diff > 0 ? 'var(--danger)' : '#64748b'))};">
                      ${ui.escapeHtml(diffText)}
                    </div>
                  </div>
                </div>

                <div style="margin-top:.65rem;">
                  <div style="height:8px; border-radius:999px; background:#f1f5f9; overflow:hidden;">
                    <div style="height:100%; width:${Math.max(0, Math.min(100, availabilityScore))}%; background:var(--accent);"></div>
                  </div>
                  <div style="font-size:.65rem; font-weight:800; color:#94a3b8; margin-top:4px;">
                    Verfügbarkeits-Score: ${availabilityScore}/100
                  </div>
                </div>

                <div style="margin-top:.75rem; display:flex; justify-content:flex-end;">
                  <button
                    type="button"
                    class="btn btn-primary"
                    style="padding:.55rem .9rem; border-radius:10px;"
                    onclick="ui.applyDistributorCompareSelection('${encoded}')"
                  >
                    <i class="fas fa-check"></i> Übernehmen
                  </button>
                </div>
              </div>
            `;
          }).join('')
        : `<div style="padding:1rem; text-align:center; color:#94a3b8; font-weight:800;">Keine Lieferantenpreise gefunden</div>`;

      ui.renderDistributorCompareChart(items);
    },

    renderDistributorCompareChart(items) {
      const canvas = document.getElementById('distributor-compare-chart');
      if (!canvas) return;

      const labels = items.map(x => x.distributor_name || '—');
      const prices = items.map(x => x.effective_price ?? null);
      const availability = items.map(x => Number(x.availability_score || 0));

      if (ui._distributorCompareChart) {
        try { ui._distributorCompareChart.destroy(); } catch (e) {}
        ui._distributorCompareChart = null;
      }

      const ctx = canvas.getContext('2d');
      ui._distributorCompareChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets: [
            {
              type: 'bar',
              label: 'Preis (€)',
              data: prices,
              backgroundColor: 'rgba(116,178,212,0.7)',
              borderColor: 'rgba(116,178,212,1)',
              borderWidth: 1,
              yAxisID: 'y',
            },
            {
              type: 'line',
              label: 'Verfügbarkeit',
              data: availability,
              borderColor: 'rgba(147,194,28,1)',
              backgroundColor: 'rgba(147,194,28,0.15)',
              borderWidth: 2,
              tension: 0.35,
              yAxisID: 'y1',
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
              position: 'top'
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: 'Preis (€)'
              }
            },
            y1: {
              beginAtZero: true,
              min: 0,
              max: 100,
              position: 'right',
              grid: {
                drawOnChartArea: false
              },
              title: {
                display: true,
                text: 'Verfügbarkeit'
              }
            }
          }
        }
      });
    },

    closeDistributorCompareModal() {
      document.getElementById('distributor-compare-modal')?.classList.add('hidden');

      if (ui._distributorCompareChart) {
        try { ui._distributorCompareChart.destroy(); } catch (e) {}
        ui._distributorCompareChart = null;
      }
    },

    getComponentLineTotal(item) {
      const price = toNum(item?.unit_price, 0);
      const qty = toNum(item?.qty, 0);
      const pe = Math.max(1, toNum(item?.price_unit, 1));
      return (price * qty) / pe;
    },

    getMaterialCounts() {
      const comps = Array.isArray(state.editingSet?.components) ? state.editingSet.components : [];
      const mainCount = comps.length;
      const subCount = comps.reduce((sum, c) => sum + ((c.subComponents || []).length), 0);
      return {
        mainCount,
        subCount,
        totalCount: mainCount + subCount,
      };
    },

    formatMoney(v) {
      return toNum(v, 0).toLocaleString('de-DE', {
        style: 'currency',
        currency: 'EUR'
      });
    },

    async duplicateSetConfirm(id) {
      if (!confirm('Dieses MasterSet duplizieren?')) return;

      const current = (state.sets || []).find(s => String(s.id) === String(id));
      const fallbackName = current?.name || 'MasterSet';

      const res = await api.duplicateSet(id, {
        copy_material: true,
        copy_tasks: true,
        copy_labor: true,
        copy_checklists: true,
        target_mode: 'clone',
        target_article_group_id: state.selectedGroup?.id ? parseInt(state.selectedGroup.id, 10) : null,
        target_master_set_id: null,
        new_name: `${fallbackName} (Kopie)`
      });

      if (!res || res.status !== 'ok') {
        ui.showStatus(res?.message || 'ERROR', true);
        return;
      }

      if (state.selectedGroup?.id) {
        await api.getSets(state.selectedGroup.id);
        await api.getGroupSets(state.selectedGroup.id);
      }

      ui.showStatus('DUPLIZIERT');
    },

    async deleteSetConfirm(id) {
      if (!confirm('Dieses MasterSet wirklich löschen?')) return;

      const res = await api.request(`/${id}`, 'DELETE');
      if (!res || res.status !== 'ok') {
        ui.showStatus(res?.message || 'ERROR', true);
        return;
      }

      if (state.selectedGroup?.id) {
        await api.getSets(state.selectedGroup.id);
        await api.getGroupSets(state.selectedGroup.id);
      }

      ui.showStatus('GELÖSCHT');
    },

    // Update the Measurement Unit (e.g., "m", "kg", "Stk")
    updateMeasure(mIdx, sIdx, val) {
      const comp = (sIdx === null)
        ? state.editingSet.components[mIdx]
        : state.editingSet.components[mIdx].subComponents[sIdx];

      if (!comp) return;
      comp.measure = val; // Save to state
      // No recalculation needed for text change, but re-render preserves focus/state
      // Alternatively, just do nothing if you don't need to re-render immediately
      // this.render(); 
      app.autoSave();
    },

    openDuplicateModal: async function () {
      const s = state.editingSet;
      if (!s?.id) {
        ui.showStatus('ERST SPEICHERN', true);
        return;
      }

        // reset draft
        state.duplicateDraft = {
          loaded: false,
          articleGroups: [],
          targetSets: [],
          selectedArticleGroupId: s.article_group_id || null,

          copy_material: true,
          copy_tasks: true,
          copy_labor: true,
          copy_checklists: true,

          target_mode: 'clone',
          target_master_set_id: null,
          new_name: `${s.name || 'MasterSet'} (Kopie)`,
      };
 

  document.getElementById('duplicate-modal')?.classList.remove('hidden');
  ui.renderDuplicateModal();

  const res = await api.getDuplicateOptions(s.id, state.duplicateDraft.selectedArticleGroupId);
  if (!res?.data) {
    ui.showStatus('ERROR', true);
    return;
  }

  state.duplicateDraft.loaded = true;
  state.duplicateDraft.articleGroups = res.data.article_groups || [];
  state.duplicateDraft.targetSets = res.data.target_sets || [];
  state.duplicateDraft.selectedArticleGroupId = res.data.selected_article_group_id || s.article_group_id;

  ui.renderDuplicateModal();
},

closeDuplicateModal: function () {
  document.getElementById('duplicate-modal')?.classList.add('hidden');
},

renderDuplicateModal: function () {
  const box = document.getElementById('duplicate-modal-body');
  if (!box) return;

  const d = state.duplicateDraft || {};
  const loaded = !!d.loaded;

  if (!loaded) {
    box.innerHTML = `
      <div style="padding:2rem; text-align:center; color:#94a3b8; font-weight:800;">
        Lade Optionen...
      </div>
    `;
    return;
  }

  const articleGroupOptions = (d.articleGroups || []).map(g => `
    <option value="${g.id}" ${String(g.id) === String(d.selectedArticleGroupId) ? 'selected' : ''}>
      ${ui.escapeHtml(g.name)}
    </option>
  `).join('');

  const targetSetOptions = (d.targetSets || []).length
    ? d.targetSets.map(s => `
        <option value="${s.id}" ${String(s.id) === String(d.target_master_set_id) ? 'selected' : ''}>
          ${ui.escapeHtml(s.name)}
        </option>
      `).join('')
    : `<option value="">Keine Ziel-MasterSets gefunden</option>`;

  box.innerHTML = `
    <div style="display:flex; flex-direction:column; gap:1rem;">

      <div style="background:white; border:1px solid var(--border-color); border-radius:16px; padding:1rem;">
        <div style="font-weight:900; color:#0f172a; margin-bottom:.75rem;">Was soll kopiert werden?</div>

        <div class="twiz-grid">
          <label class="twiz-chip">
            <input type="checkbox" ${d.copy_material ? 'checked' : ''} onchange="ui.setDuplicateFlag('copy_material', this.checked)">
            <span>Material</span>
          </label>

          <label class="twiz-chip">
            <input type="checkbox" ${d.copy_tasks ? 'checked' : ''} onchange="ui.setDuplicateFlag('copy_tasks', this.checked)">
            <span>Aufgaben</span>
          </label>

          <label class="twiz-chip">
            <input type="checkbox" ${d.copy_labor ? 'checked' : ''} onchange="ui.setDuplicateFlag('copy_labor', this.checked)">
            <span>Personal</span>
          </label>

          <label class="twiz-chip">
            <input type="checkbox" ${d.copy_checklists ? 'checked' : ''} onchange="ui.setDuplicateFlag('copy_checklists', this.checked)">
            <span>Protokolle</span>
          </label>
        </div>
      </div>

      <div style="background:white; border:1px solid var(--border-color); border-radius:16px; padding:1rem;">
        <div style="font-weight:900; color:#0f172a; margin-bottom:.75rem;">Wohin duplizieren?</div>

        <div class="twiz-grid" style="margin-bottom:1rem;">
          <label class="twiz-chip">
            <input type="radio" name="duplicate_target_mode" value="clone" ${d.target_mode === 'clone' ? 'checked' : ''} onchange="ui.setDuplicateMode('clone')">
            <span>Neues MasterSet erstellen</span>
          </label>

          <label class="twiz-chip">
            <input type="radio" name="duplicate_target_mode" value="existing" ${d.target_mode === 'existing' ? 'checked' : ''} onchange="ui.setDuplicateMode('existing')">
            <span>In bestehendes MasterSet kopieren</span>
          </label>
        </div>

        <div class="form-group mb-2">
          <label>Artikelgruppe</label>
          <select class="form-control" onchange="ui.changeDuplicateArticleGroup(this.value)">
            ${articleGroupOptions}
          </select>
        </div>

        ${d.target_mode === 'clone' ? `
          <div class="form-group">
            <label>Neuer Name</label>
            <input
              id="duplicate-new-name"
              class="form-control"
              value="${ui.escapeHtml(d.new_name || '')}"
              oninput="state.duplicateDraft.new_name = this.value"
              placeholder="Name der Kopie"
            >
          </div>
        ` : `
          <div class="form-group">
            <label>Ziel-MasterSet</label>
            <select class="form-control" onchange="state.duplicateDraft.target_master_set_id = this.value">
              <option value="">Bitte wählen</option>
              ${targetSetOptions}
            </select>
          </div>
        `}
      </div>

      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; padding:1rem; font-size:.8rem; color:#64748b; font-weight:700;">
        Quelle: <strong>${ui.escapeHtml(state.editingSet?.name || '')}</strong><br>
        ${d.target_mode === 'clone'
          ? `Es wird ein neues MasterSet erstellt.`
          : `Die gewählten Daten werden an das bestehende Ziel-MasterSet angehängt.`}
      </div>
    </div>
  `;
},

setDuplicateFlag: function (key, val) {
  state.duplicateDraft[key] = !!val;
},

setDuplicateMode: function (mode) {
  state.duplicateDraft.target_mode = mode;
  ui.renderDuplicateModal();
},

changeDuplicateArticleGroup: async function (articleGroupId) {
  state.duplicateDraft.selectedArticleGroupId = articleGroupId;
  state.duplicateDraft.target_master_set_id = null;

  ui.renderDuplicateModal();

  const s = state.editingSet;
  if (!s?.id) return;

  const res = await api.getDuplicateOptions(s.id, articleGroupId);
  if (!res?.data) return;

  state.duplicateDraft.targetSets = res.data.target_sets || [];
  ui.renderDuplicateModal();
},

submitDuplicate: async function () {
  const s = state.editingSet;
  const d = state.duplicateDraft;

  if (!s?.id) return;

  if (!d.copy_material && !d.copy_tasks && !d.copy_labor && !d.copy_checklists) {
    ui.showStatus('BITTE AUSWÄHLEN', true);
    return;
  }

  if (d.target_mode === 'existing' && !d.target_master_set_id) {
    ui.showStatus('ZIEL WÄHLEN', true);
    return;
  }

  if (d.target_mode === 'clone' && !(d.new_name || '').trim()) {
    ui.showStatus('NAME FEHLT', true);
    return;
  }

  const btn = document.getElementById('duplicate-confirm-btn');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Dupliziert...`;
  }

  const payload = {
    copy_material: !!d.copy_material,
    copy_tasks: !!d.copy_tasks,
    copy_labor: !!d.copy_labor,
    copy_checklists: !!d.copy_checklists,

    target_mode: d.target_mode,
    target_article_group_id: d.selectedArticleGroupId ? parseInt(d.selectedArticleGroupId, 10) : null,
    target_master_set_id: d.target_mode === 'existing' && d.target_master_set_id
      ? parseInt(d.target_master_set_id, 10)
      : null,
    new_name: d.target_mode === 'clone' ? (d.new_name || '').trim() : null,
  };

  const res = await api.duplicateSet(s.id, payload);

  if (btn) {
    btn.disabled = false;
    btn.innerHTML = `<i class="fas fa-copy"></i> Duplizieren`;
  }

  if (!res || res.status !== 'ok') {
    ui.showStatus(res?.message || 'ERROR', true);
    return;
  }

  ui.closeDuplicateModal();
  ui.showStatus('DUPLIZIERT');

  // refresh current group list if needed
  if (state.selectedGroup?.id) {
    await api.getSets(state.selectedGroup.id);
    await api.getGroupSets(state.selectedGroup.id);
  }
},


    // Update the Price Unit Divisor (e.g., 10, 100, 1)
    updatePriceUnit(mIdx, sIdx, val) {
      const n = Math.max(1, toNum(val, 1)); // Prevent 0 or negative
      const comp = (sIdx === null)
        ? state.editingSet.components[mIdx]
        : state.editingSet.components[mIdx].subComponents[sIdx];

      if (!comp) return;
      comp.price_unit = n; // Save to state
      
      this.recalculateLocalStats(); // Recalculate totals
      this.render(); // Re-render to update percentages/totals visually
      app.autoSave();
    },


    // Update this function inside your 'ui' object
   async openCustomTaskModal() {
        // Reset inputs
        if (!ui.ensureSetHasName('eine benutzerdefinierte Aufgabe erstellen')) return;
        const modal = document.getElementById('activity-modal');
        const titleInput = document.getElementById('am_title');
        const durationInput = document.getElementById('am_duration');
        const notesInput = document.getElementById('am_notes');
        const headerTitle = modal.querySelector('.modal-title span');
        const saveBtn = modal.querySelector('.btn-primary');
        const bodyContainer = modal.querySelector('div[style*="padding:1.25rem"]');

        // Reset text values
        if(titleInput) titleInput.value = '';
        if(durationInput) durationInput.value = '00:00';
        if(notesInput) notesInput.value = '';
        
        // Update Modal Title
        headerTitle.innerText = "Benutzerdefinierte Aufgabe";
        
        // Update save button to call the new async function
        saveBtn.setAttribute('onclick', 'ui.saveCustomTask()');

        // ---------------------------------------------------------
        // 1. SECTION SELECTOR (Prepared Container)
        // ---------------------------------------------------------
        let sectionSelect = document.getElementById('am_section_select');
        
        if (!sectionSelect) {
            const sectionGroup = document.createElement('div');
            sectionGroup.className = 'form-group mb-2';
            sectionGroup.innerHTML = `
                <label>Deinstleistung *</label>
                <select id="am_section_select" class="form-control" style="width:100%">
                    <option value="">Lade Daten...</option>
                </select>
            `;
            // Insert at the very top
            bodyContainer.prepend(sectionGroup);
            sectionSelect = document.getElementById('am_section_select');
        }

        // ---------------------------------------------------------
        // 2. PHASE SELECTOR (EXISTING)
        // ---------------------------------------------------------
        let phaseSelect = document.getElementById('am_phase_select');
        
        if (!phaseSelect) {
            const phaseGroup = document.createElement('div');
            phaseGroup.className = 'form-group mb-2';
            phaseGroup.innerHTML = `
                <label>Phase *</label>
                <select id="am_phase_select" class="form-control" style="width:100%">
                    <option value="">Lade Daten...</option>
                </select>
            `;
            // Insert after Section
            sectionSelect.closest('.form-group').after(phaseGroup);
            phaseSelect = document.getElementById('am_phase_select');
        }

        // Show modal
        modal.classList.remove('hidden');
        ui.showLoading(true);

        try {
            const groupId = state.selectedGroup?.id;
            if(!groupId) throw new Error("Keine Gruppe ausgewählt");

            // ========================================================
            // A. FETCH SECTIONS (To get IDs instead of Strings)
            // ========================================================
            // We use the wizard tree endpoint because it returns sections strictly for this product
            const treeRes = await api.request(`/wizard/tree/${groupId}`);
            const sections = treeRes?.data || [];

            let sectionHtml = '<option value="">-- Bitte wählen --</option>';
            
            // Generate Options with IDs
            sections.forEach(sec => {
                // sec.id = Integer ID (fixes 422 error)
                // sec.label = German Name (e.g. "Planung", "Montage")
                sectionHtml += `<option value="${sec.id}">${ui.escapeHtml(sec.label || sec.name)}</option>`;
            });

            sectionSelect.innerHTML = sectionHtml;

            // Initialize Select2 for Section
            if (window.jQuery && window.jQuery.fn.select2) {
                const $sSelect = window.jQuery('#am_section_select');
                if ($sSelect.data('select2')) $sSelect.select2('destroy');

                $sSelect.select2({
                    dropdownParent: window.jQuery(modal),
                    width: '100%',
                    placeholder: "Bereich wählen...",
                    minimumResultsForSearch: Infinity
                });
                $sSelect.val('').trigger('change');
            }

            // ========================================================
            // B. FETCH PHASES (Unfiltered)
            // ========================================================
            const res = await api.request(`/tasks/options?article_group_id=${groupId}`);
            const fullOptions = res?.data || [];

            const uniquePhases = new Map();
            
            fullOptions.forEach(stage => {
                 const phases = stage.phases || stage.task_phases || [];
                 phases.forEach(p => {
                     const stageName = stage.stage || stage.name || 'Ohne Stage';
                     const phaseName = p.phase_name || p.name || 'Unbenannt';
                     const key = `${stageName}__${phaseName}`;
                     
                     if (!uniquePhases.has(key)) {
                         uniquePhases.set(key, {
                             val: `${stage.id}|${p.id}|${stageName}|${phaseName}`, 
                             label: phaseName,
                             group: stageName
                         });
                     }
                 });
            });

            // Build Phase HTML
            let optionsHtml = '<option value="">-- Bitte wählen --</option>';
            const grouped = {};
            uniquePhases.forEach((item) => {
                if (!grouped[item.group]) grouped[item.group] = [];
                grouped[item.group].push(item);
            });

            Object.keys(grouped).sort().forEach(groupName => {
                optionsHtml += `<optgroup label="${ui.escapeHtml(groupName)}">`;
                grouped[groupName].forEach(item => {
                    optionsHtml += `<option value="${ui.escapeHtml(item.val)}">${ui.escapeHtml(item.label)}</option>`;
                });
                optionsHtml += `</optgroup>`;
            });

            phaseSelect.innerHTML = optionsHtml;

            // Initialize Select2 for Phase
            if (window.jQuery && window.jQuery.fn.select2) {
                const $pSelect = window.jQuery(phaseSelect);
                if ($pSelect.data('select2')) $pSelect.select2('destroy');

                $pSelect.select2({
                    dropdownParent: window.jQuery(modal), 
                    width: '100%',
                    placeholder: "Phase suchen...",
                    allowClear: true,
                    language: { noResults: () => "Keine Phase gefunden" }
                });
                $pSelect.val('').trigger('change');
            }

        } catch (e) {
            console.error("Error loading task data:", e);
            sectionSelect.innerHTML = '<option value="">Fehler</option>';
            phaseSelect.innerHTML = '<option value="">Fehler</option>';
            ui.showStatus("Fehler beim Laden der Daten", true);
        } finally {
            ui.showLoading(false);
        }
    },

   async saveCustomTask() {
        const title = document.getElementById('am_title')?.value?.trim();
        const duration = document.getElementById('am_duration')?.value?.trim() || '00:00';
        const notes = document.getElementById('am_notes')?.value?.trim() || '';
        
        // 1. Get Section Value
        let sectionVal = '';
        if (window.jQuery && window.jQuery('#am_section_select').data('select2')) {
            sectionVal = window.jQuery('#am_section_select').val();
        } else {
            sectionVal = document.getElementById('am_section_select')?.value;
        }

        // 2. Get Phase Value
        let phaseVal = '';
        if (window.jQuery && window.jQuery('#am_phase_select').data('select2')) {
            phaseVal = window.jQuery('#am_phase_select').val();
        } else {
            phaseVal = document.getElementById('am_phase_select')?.value;
        }

        // Validation
        if (!sectionVal) return ui.showStatus('SEKTION WÄHLEN', true);
        if (!phaseVal) return ui.showStatus('PHASE WÄHLEN', true);
        if (!title) return ui.showStatus('TITEL FEHLT', true);

        // Parse composite phase value
        const [stageId, phaseId, stageName, phaseName] = phaseVal.split('|');

        ui.showLoading(true);

        try {
            const payload = {
                product_id: state.selectedGroup?.id,
                
                // ✅ Pass the Section ID/Key
                // Note: If your backend expects an Integer ID for section, you might need 
                // to map 'montage' -> 2, etc. If it accepts keys/strings, this is correct.
                section_id: sectionVal, 
                
                phase_id: parseInt(phaseId),
                title: title,
                duration: duration,
                notes: notes,
            };

            // Send to Backend
            const res = await api.requestTaskWizard('/wizard/activity/at', 'POST', payload);
            
            if (!res || !res.data) {
                throw new Error(res?.message || "Fehler beim Erstellen");
            }

            const createdAct = res.data;

            // Create local Task Object
            const newTask = {
                id: null,
                stage_id: parseInt(stageId),
                stage_name: stageName,
                task_phase_id: parseInt(phaseId),
                phase_name: phaseName,
                phase_activity_id: createdAct.id, 
                title: title,
                description: null,
                duration: duration,
                duration_type: 'custom', 
                notes: notes,
                priority: 'normal',
                percent: 0,
                hours: toNum(ui.parseDurationToHours(duration), 0), 
                sort_order: state.editingSet.tasks.length,
                task_labor: [],
                is_custom: true 
            };

            state.editingSet.tasks.push(newTask);

            ui.closeTaskQualModal(); 
            document.getElementById('activity-modal').classList.add('hidden');
            
            // Clean up Select2
            if (window.jQuery) {
                window.jQuery('#am_phase_select').select2('destroy');
                window.jQuery('#am_section_select').select2('destroy');
            }
            
            ui.renderTasksTab();
            ui.showStatus('AUFGABE ERSTELLT');

        } catch (e) {
            console.error(e);
            ui.showStatus('FEHLER: ' + e.message, true);
        } finally {
            ui.showLoading(false);
        }
    },

    // Helper for HH:MM parsing (Ensure this is in your ui object)
    parseDurationToHours(durationStr) {
        if(!durationStr) return 0;
        if (!durationStr.includes(':')) return parseFloat(durationStr) || 0;
        const parts = durationStr.split(':');
        const h = parseInt(parts[0], 10) || 0;
        const m = parseInt(parts[1], 10) || 0;
        return h + (m / 60);
    },

    toggleGroupView(mode) {
        state.groupViewMode = mode;
        ui.render(); // Re-render to apply class and button states
    },

    // Add this to the ui object
    addPhaseFromEncoded(encoded) {
      let payload;
      try {
        payload = b64DecodeJson(encoded);
      } catch (e) {
        ui.showStatus('PAYLOAD ERROR', true);
        return;
      }

      if (!payload.activities || !Array.isArray(payload.activities)) {
        ui.showStatus('NO ACTIVITIES', true);
        return;
      }

      let addedCount = 0;
      const currentTasks = state.editingSet.tasks || [];

      payload.activities.forEach(act => {
        // Dedupe check: Skip if phase_activity_id already exists in the set
        const exists = currentTasks.some(
            (t) => String(t.phase_activity_id) === String(act.phase_activity_id)
        );
        
        if (!exists) {
            const nextOrder = state.editingSet.tasks.length;
            
            state.editingSet.tasks.push({
                stage_id: payload.stage_id ?? null,
                stage_name: payload.stage_name ?? null,
                task_phase_id: payload.task_phase_id ?? null,
                phase_name: payload.phase_name ?? null,
                phase_activity_id: act.phase_activity_id,
                title: act.title ?? null,
                description: act.description ?? null,
                duration: act.duration ?? null,
                duration_type: act.duration_type ?? null,
                notes: act.notes ?? null,
                priority: act.priority ?? null,
                percent: act.percent ?? null,
                hours: toNum(act.hours, 0),
                sort_order: nextOrder,
                task_labor: [] // Initialize empty labor for new tasks
            });
            addedCount++;
        }
      });

      if (addedCount > 0) {
        ui.showStatus(`${addedCount} TASKS IMPORTIERT`);
        ui.renderTasksTab();
      } else {
        ui.showStatus('BEREITS VORHANDEN', true);
      }
    },




   // Inside UI object
    promptTaskQualification(taskIdx) {
        state.tqContext = { taskIdx: taskIdx, selected: null }; // New context
        
        // Reset inputs
        const modal = document.getElementById('task-qual-modal');
        const list = document.getElementById('tq-list');
        const footer = document.getElementById('tq-footer');
        const search = document.getElementById('tq-search');
        
        if(search) search.value = '';
        if(footer) footer.classList.add('hidden');
        
        // Render List
        this.renderTaskQualList();

        modal.classList.remove('hidden');
        setTimeout(() => search?.focus(), 50);
    },

    renderTaskQualList(filter = '') {
        const list = document.getElementById('tq-list');
        const quals = state.laborOptions || [];
        const term = filter.toLowerCase();

        const filtered = quals.filter(q => q.name.toLowerCase().includes(term));

        if(filtered.length === 0) {
            list.innerHTML = `<div style="text-align:center; padding:2rem; color:#cbd5e1; font-weight:800;">Nichts gefunden</div>`;
            return;
        }

        list.innerHTML = filtered.map(q => {
            const isSel = (state.tqContext?.selected?.id === q.id);
            const bg = isSel ? 'var(--primary-light)' : 'white';
            const border = isSel ? 'var(--primary)' : 'var(--border-color)';

            return `
                <div onclick="ui.selectTaskQual(${q.id})" 
                     style="background:${bg}; border:1px solid ${border}; border-radius:12px; padding:0.75rem; margin-bottom:0.5rem; cursor:pointer; display:flex; justify-content:space-between; align-items:center; transition:all 0.1s;">
                    <div style="font-weight:900; color:var(--text-main);">${ui.escapeHtml(q.name)}</div>
                    <div style="font-size:0.85rem; font-weight:800; color:#64748b;">${parseFloat(q.default_price).toFixed(2)} €</div>
                </div>
            `;
        }).join('');
    },

    filterTaskQuals(val) {
        this.renderTaskQualList(val);
    },

    selectTaskQual(id) {
        const q = state.laborOptions.find(x => x.id === id);
        if(!q) return;

        state.tqContext.selected = q;
        this.renderTaskQualList(document.getElementById('tq-search').value); // Re-render to highlight

        // Show Footer & Fill defaults
        const footer = document.getElementById('tq-footer');
        footer.classList.remove('hidden');
        
        document.getElementById('tq-selected-name').innerText = q.name;
        document.getElementById('tq-rate').value = parseFloat(q.default_price).toFixed(2);
        document.getElementById('tq-hours').value = "1";
        document.getElementById('tq-hours').focus();
    },

    confirmTaskQual() {
        const ctx = state.tqContext;
        if(!ctx || !ctx.selected) return;

        const hours = parseFloat(document.getElementById('tq-hours').value) || 0;
        const rate = parseFloat(document.getElementById('tq-rate').value) || 0;

        if(hours <= 0) return alert("Stunden müssen > 0 sein");

        this.addTaskLabor(ctx.taskIdx, ctx.selected.id, ctx.selected.name, rate, hours);
        this.closeTaskQualModal();
    },

    closeTaskQualModal() {
        document.getElementById('task-qual-modal').classList.add('hidden');
        state.tqContext = null;
    },

    // 2. Add entry to task and Sync
    addTaskLabor(taskIdx, qualId, name, rate, hours) {
        const t = state.editingSet.tasks[taskIdx];
        if(!t.task_labor) t.task_labor = [];

        const existing = t.task_labor.find(x => String(x.qualification_id) === String(qualId));

        if(existing) {
          // add hours to existing, keep unit/rate
          existing.hours += (parseFloat(hours) || 0);

          // keep qty synced to unit
          ui.ensureTaskLaborFields(existing);
          const f = ui.taskLaborUnitFactor(existing.unit);
          existing.qty = f > 0 ? (existing.hours / f) : existing.hours;

        } else {
          const row = {
            qualification_id: qualId,
            name: name,

            // canonical hourly rate (€/h) comes from qualification default_price
            rate: parseFloat(rate) || 0,

            // unit system
            unit: 'h',           // h|min|woche
            hours: parseFloat(hours) || 0,
            qty: parseFloat(hours) || 0, // qty in selected unit (for 'h' it matches)

            // allow EK Gesamt override (optional)
            ek_total_override: null
          };

          ui.ensureTaskLaborFields(row);
          t.task_labor.push(row);
        }

        ui.syncGlobalLaborFromTasks();
        ui.renderTasksTab();
      },

    // 3. Update hours of a specific qualification inside a task
    updateTaskLaborHours(taskIdx, laborIdx, val) {
        const t = state.editingSet.tasks[taskIdx];
        if(t && t.task_labor && t.task_labor[laborIdx]) {
            t.task_labor[laborIdx].hours = parseFloat(val) || 0;
            this.syncGlobalLaborFromTasks();
            this.recalculateLocalStats(); 
        }
    },

    // 4. Remove qualification from a task
    removeTaskLabor(taskIdx, laborIdx) {
        const t = state.editingSet.tasks[taskIdx];
        if(t && t.task_labor) {
            t.task_labor.splice(laborIdx, 1);
            this.syncGlobalLaborFromTasks();
            this.renderTasksTab();
        }
    },
 
   // 5. THE AGGREGATOR: Calculates total hours per Qualification across ALL tasks and updates Main Labor
    syncGlobalLaborFromTasks() {
        const tasks = Array.isArray(state.editingSet?.tasks) ? state.editingSet.tasks : [];
        const totals = {};

        tasks.forEach(t => {
          if (!Array.isArray(t.task_labor)) t.task_labor = [];

          let taskHours = 0;

          t.task_labor.forEach(tl => {
            const qid = String(tl.qualification_id || '');
            if (!qid) return;

            const hours = parseFloat(tl.hours) || 0;
            const rate = parseFloat(tl.rate) || 0;
            const name = tl.name || '—';

            taskHours += hours;

            if (!totals[qid]) {
              totals[qid] = {
                qualification_id: parseInt(qid, 10),
                name,
                rate,
                hours: 0,
                department_id: null,
                position_id: null,
                employee_id: null,
                percentOfTotal: 0
              };
            }

            totals[qid].hours += hours;
            totals[qid].rate = rate; // latest task rate wins
            totals[qid].name = name;
          });

          // task hours always follow task_labor
          t.hours = taskHours;
        });

        // rebuild labor list only from tasks
        state.editingSet.labor = Object.values(totals);

        this.recalculateLocalStats();

        if (state.editorTab === 'personal') {
          this.renderLaborItems();
        }
        if (state.editorTab === 'aufgabe') {
          this.renderTasksTab();
        }
      },

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
            } 
            
            else {
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
                
                // ✅ FIX: Aggressive fallback checking for the folder stats
                const taskCount = s.taskCount ?? g.tasks_count ?? 0;
                const protocolCount = s.protocolCount ?? g.checklists_count ?? g.protocols_count ?? 0;

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
                                <span class="f-stat blue" title="Components"><i class="fas fa-cubes"></i> ${s.mainCount || 0} M / ${s.subCount || 0} S</span>
                                <span class="f-stat green" title="Labor"><i class="fas fa-user-clock"></i> ${s.laborCount || 0}</span>
                                <span class="f-stat purple" title="Tasks"><i class="fas fa-list-check"></i> ${taskCount}</span>
                                <span class="f-stat orange" title="Protocols"><i class="fas fa-clipboard-check"></i> ${protocolCount}</span>
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
              
              // ✅ FIX: Aggressive fallback checking for nested folder sets
              const taskCount = stats.taskCount ?? s.tasks_count ?? (Array.isArray(s.tasks) ? s.tasks.length : 0);
              const protocolCount =
                  stats.protocolCount ??
                  stats.checklists_count ??
                  stats.protocols_count ??
                  s.checklists_count ??
                  s.protocols_count ??
                  (Array.isArray(s.checklists) ? s.checklists.length : 0) ??
                  (Array.isArray(s.protocols) ? s.protocols.length : 0) ??
                  0;

              return `
                <div onclick="app.editSet(${s.id})" class="card-set" style="padding:1rem; border:1px solid #e2e8f0; border-left:4px solid var(--primary);">
                  <div class="set-info">
                     <div>
                        <h4 style="font-weight:900; font-size:1rem; margin-bottom:4px;">${escapeHtml(s.name)}</h4>
                        <div class="pill-container">
                             <span class="pill pill-blue"><div class="dot bg-blue"></div> ${stats.mainCount || 0} Main</span>
                             <span class="pill pill-green"><div class="dot bg-green"></div> ${stats.laborCount || 0} Pers.</span>
                             <span class="pill pill-purple"><div class="dot bg-purple"></div> ${taskCount} Tasks</span>
                             <span class="pill pill-orange"><div class="dot bg-orange"></div> ${protocolCount} Protokolle</span>
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
      if (!ui.ensureSetHasName('ein Protokoll hinzufügen')) return;
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
      return `
        <div style="padding:2rem; text-align:center; color:#cbd5e1; font-weight:900;">
          Keine Checklists gefunden
        </div>
      `;
    }

    const shortenWords = (text, maxWords = 2) => {
      const clean = String(text || '').trim().replace(/\s+/g, ' ');
      if (!clean) return '—';

      const words = clean.split(' ');
      if (words.length <= maxWords) return clean;

      return words.slice(0, maxWords).join(' ') + ' ...';
    };

    return `
      <div id="checklists-options-list" style="display:flex; flex-direction:column; gap:0.75rem; width:100%; min-width:0;">
        ${list.map((c) => {
          const checklistId =
            c.maintenance_checklist_id ??
            c.checklist_id ??
            c.id ??
            null;

          const fullTitle =
            c.title ??
            c.name ??
            (checklistId ? `Checklist #${checklistId}` : 'Checklist');

          const fullDesc = c.description ?? '';

          const shortTitle = shortenWords(fullTitle, 2);
          const shortDesc  = shortenWords(fullDesc, 2);

          const cnt = Number.isFinite(+c.items_count)
            ? +c.items_count
            : (Array.isArray(c.items) ? c.items.length : 0);

          const payload = b64EncodeJson({
            source_checklist_id: checklistId,
            maintenance_checklist_id: checklistId,
            checklist_id: checklistId,
            title: c.title ?? c.name ?? '',
            description: c.description ?? '',
            items_count: cnt,
            items: Array.isArray(c.items) ? c.items : [],
            type: c.type ?? null
          });

          return `
            <div
              class="catalog-item checklist-opt"
              data-encoded="${escapeHtml(payload)}"
              style="
                margin:0;
                cursor:grab;
                width:100%;
                min-width:0;
                padding:0.9rem 1rem;
                overflow:hidden;
              "
              onclick="ui.addChecklistFromEncoded('${payload}')"
              title="${escapeHtml(fullTitle)}"
            >
              <div
                style="
                  display:grid;
                  grid-template-columns: 28px minmax(0, 1fr) auto;
                  align-items:start;
                  gap:.75rem;
                  width:100%;
                  min-width:0;
                "
              >
                <div class="handle" style="width:28px; height:28px; flex:0 0 auto;">
                  <i class="fas fa-grip-vertical"></i>
                </div>

                <div style="min-width:0; max-width:100%; overflow:hidden;">
                  <div
                    title="${escapeHtml(fullTitle)}"
                    style="
                      font-weight:900;
                      color:var(--text-main);
                      white-space:nowrap;
                      overflow:hidden;
                      text-overflow:ellipsis;
                      max-width:100%;
                    "
                  >
                    ${escapeHtml(shortTitle)}
                  </div>

                  <div
                    title="${escapeHtml(fullDesc)}"
                    style="
                      font-size:.75rem;
                      font-weight:700;
                      color:#94a3b8;
                      margin-top:2px;
                      white-space:nowrap;
                      overflow:hidden;
                      text-overflow:ellipsis;
                      max-width:100%;
                    "
                  >
                    ${escapeHtml(shortDesc)}
                  </div>

                  ${
                    c.type
                      ? `
                        <div
                          title="${escapeHtml(c.type)}"
                          style="
                            margin-top:6px;
                            font-size:10px;
                            font-weight:900;
                            color:var(--primary);
                            text-transform:uppercase;
                            letter-spacing:.04em;
                            white-space:nowrap;
                            overflow:hidden;
                            text-overflow:ellipsis;
                            max-width:100%;
                          "
                        >
                          ${escapeHtml(shortenWords(c.type, 2))}
                        </div>
                      `
                      : ''
                  }
                </div>

                <div
                  title="${cnt} Items"
                  style="
                    font-size:10px;
                    font-weight:900;
                    color:#94a3b8;
                    text-transform:uppercase;
                    white-space:nowrap;
                    flex:0 0 auto;
                    padding-left:.25rem;
                  "
                >
                  ${cnt} Items
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

      try {
        payload = b64DecodeJson(encoded);
      } catch (e) {
        ui.showStatus('PAYLOAD ERROR', true);
        return;
      }

      const checklistId = String(
        payload.source_checklist_id ??
        payload.maintenance_checklist_id ??
        payload.checklist_id ??
        ''
      );

      if (!checklistId) {
        ui.showStatus('NO ID', true);
        return;
      }

      const trigger = 'start';

      const exists = (state.editingSet.checklists || []).some(x =>
        String(
          x.source_checklist_id ??
          x.maintenance_checklist_id ??
          x.checklist_id ??
          x.id
        ) === checklistId &&
        String(x.trigger || 'start') === trigger
      );

      if (exists) {
        ui.showStatus('ALREADY ADDED', true);
        return;
      }

      if (!Array.isArray(state.editingSet.checklists)) {
        state.editingSet.checklists = [];
      }

      state.editingSet.checklists.push({
        source_checklist_id: parseInt(checklistId, 10),
        maintenance_checklist_id: parseInt(checklistId, 10),
        checklist_id: parseInt(checklistId, 10),

        title: payload.title || '',
        description: payload.description || '',
        items_count: payload.items_count ?? 0,
        items: Array.isArray(payload.items) ? payload.items : [],
        type: payload.type ?? null,

        trigger,
        is_required: true,
        sort_order: state.editingSet.checklists.length
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
      try { p = b64DecodeJson(encoded); } catch (e) { 
        ui.showStatus('PAYLOAD ERROR', true); 
        return; 
      }

      ui.addFromCatalog(
        p.product_id,
        p.product_name,
        p.distributor_price_id,
        p.distributor_id,
        p.distributor_name,
        p.unit_price,
        p.measure,
        p.price_unit,
        p.article_no,
        p.distributor_article_no
      );
    },

  addFromCatalog(pId, pName, dpId, dId, dName, price, measure = 'Stk', pe = 1, articleNo = '', distributorArticleNo = '') {
      const scrollWrap = document.getElementById('material-scroll-wrap');
      const savedTop = scrollWrap ? scrollWrap.scrollTop : 0;
      const savedLeft = scrollWrap ? scrollWrap.scrollLeft : 0;
      const savedPageY = window.scrollY;

      const basePrice = toNum(price, 0);

      const item = {
        id: 'tmp_' + Date.now() + '_' + Math.floor(Math.random() * 10000),
        product_id: pId,
        product_name: pName,

        article_no: articleNo || '',
        articleNumber: articleNo || '',

        distributor_price_id: dpId,
        distributor_id: dId,
        distributor_name: dName,
        distributor_article_no: distributorArticleNo || '',

        unit_price: basePrice,
        base_unit_price: basePrice,
        is_price_overridden: false,

        purchasePrice: basePrice,
        purchase_price: basePrice,

        qty: 1,
        quantity: 1,

        margin: toNum(state.globalMatMargin, 50),
        skonto: 0,
        paymentTerms: 14,
        payment_terms: 14,
        availability: true,

        type: 'haupt',
        isStammartikel: false,
        is_stammartikel: false,
        isFavorite: false,
        is_favorite: false,

        measure: measure || 'Stk.',
        unit: measure || 'Stk.',
        price_unit: Math.max(1, toNum(pe, 1)),
        vpe: 1,

        description: '',
        subComponents: [],
        isExpanded: true,
        isEditingProps: false,
        docs: [],
      };
      if (state.pickerContext?.type === 'main') {
        state.editingSet.components.push(item);
      } else if (state.pickerContext?.type === 'sub') {
        const main = state.editingSet.components[state.pickerContext.mainIdx];
        if (!main.subComponents) main.subComponents = [];
        item.type = 'zubehoer';
        main.subComponents.push(item);
        main.isExpanded = true;
      }

      this.closeModal();
      this.recalculateLocalStats();
      this.renderComponentItems();

      const newWrap = document.getElementById('material-scroll-wrap');
      if (newWrap) {
        newWrap.scrollTop = savedTop;
        newWrap.scrollLeft = savedLeft;
      }
      window.scrollTo(0, savedPageY);

      app.autoSave();
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
            // Determine class based on state
            const containerClass = state.groupViewMode === 'list' ? 'list-layout' : 'grid-cards';
            
            container.innerHTML = `
                <header class="dashboard-header">
                  <div class="dash-left">
                    <h2 class="brand-title">Artikelgruppen und Dienste</h2>
                    <p class="brand-sub">Wählen Sie einen Artikelgruppe zur Verwaltung</p>
                  </div>
                  <div class="dash-right">
                    <div class="view-toggle-group">
                      <button class="view-btn ${state.groupViewMode === 'grid' ? 'active' : ''}" 
                              onclick="ui.toggleGroupView('grid')" title="Kachelansicht">
                          <i class="fas fa-th-large"></i>
                      </button>
                      <button class="view-btn ${state.groupViewMode === 'list' ? 'active' : ''}" 
                              onclick="ui.toggleGroupView('list')" title="Listenansicht">
                          <i class="fas fa-list"></i>
                      </button>
                    </div>

                    <button type="button" class="btn btn-primary" onclick="TaskWizard.open()">
                      <i class="fas fa-magic" style="margin-right:.45rem;"></i> Aufgabe Wizard
                    </button>
                    <div class="search-wrapper">
                      <i class="fas fa-search search-icon"></i>
                      <input type="text" oninput="app.searchGroups(this.value)" class="search-input" placeholder="Bereich suchen..." value="${escapeHtml(state.groupSearch || '')}">
                    </div>
                  </div>
                </header>
                <div id="groups-grid" class="${containerClass}">${this.getGroupGridHTML()}</div>
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
              
              // CRITICAL BUG FIX: We MUST NOT check `!groupSets.length`. 
              // If a group has 0 folders, `groupSets.length` is 0, which triggers an infinite 
              // loop of fetching and re-rendering! We ONLY check if the ID matches.
              if (loadedFor !== gid) {
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
                 const mainVal = Number(
                    s.stats?.mainTotal ??
                    s.stats?.mainCost ??
                    s.main_total ??
                    0
                  );

                  const subVal = Number(
                    s.stats?.subTotal ??
                    s.stats?.subCost ??
                    s.sub_total ??
                    0
                  );

                  const laborVal = Number(
                    s.stats?.laborTotal ??
                    s.stats?.labor ??
                    s.labor_total ??
                    0
                  );
 
                  const total = mainVal + subVal + laborVal;

                  const mainPct  = total > 0 ? ((mainVal / total) * 100).toFixed(0) : 0;
                  const subPct   = total > 0 ? ((subVal / total) * 100).toFixed(0) : 0;
                  const laborPct = total > 0 ? ((laborVal / total) * 100).toFixed(0) : 0;

                  const taskCount = s.stats?.taskCount ?? s.tasks_count ?? (Array.isArray(s.tasks) ? s.tasks.length : 0);
                  const protocolCount =
                      s.stats?.protocolCount ??
                      s.stats?.checklists_count ??
                      s.stats?.protocols_count ??
                      s.checklists_count ??
                      s.protocols_count ??
                      (Array.isArray(s.checklists) ? s.checklists.length : 0) ??
                      (Array.isArray(s.protocols) ? s.protocols.length : 0) ??
                      0;

                  return `
                    <div onclick="app.editSet(${s.id})" class="card-set">
                      <div class="set-info">
                        <div class="set-icon"><i class="fas fa-box-open fa-lg"></i></div>
                        <div>
                          <h4 style="font-weight:900; font-size:1.125rem; color:var(--text-main); margin-bottom:0.5rem;">
                            ${escapeHtml(s.name || 'Unbenanntes Set')}
                          </h4>
                          <div class="pill-container">
                            <div class="pill pill-blue"><div class="dot bg-blue"></div><span>${s.stats?.mainCount || 0} Main (${mainPct}%)</span></div>
                            <div class="pill pill-gray"><div class="dot bg-gray"></div><span>${s.stats?.subCount || 0} Sub (${subPct}%)</span></div>
                            <div class="pill pill-green"><div class="dot bg-green"></div><span>${s.stats?.laborCount || 0} Pers. (${laborPct}%)</span></div>
                            <div class="pill pill-purple"><div class="dot bg-purple"></div><span>${taskCount} Aufgaben</span></div>
                            <div class="pill pill-orange"><div class="dot bg-orange"></div><span>${protocolCount} Protokolle</span></div>
                          </div>
                        </div>
                      </div>

                      <div style="display:flex; align-items:center; gap:1rem;">
                        <div style="text-align:right;">
                          <p style="font-size:0.625rem; font-weight:900; color:#cbd5e1; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.25rem;">
                            Set Gesamtwert
                          </p>
                          <p style="font-size:1.75rem; font-weight:900; color:var(--text-main);">
                          ${total.toLocaleString('de-DE', { style:'currency', currency:'EUR' })}
                        </p>
                        </div>

                        <div style="display:flex; gap:0.5rem;" onclick="event.stopPropagation()">
                          <button
                            class="btn btn-icon"
                            title="Duplizieren"
                            onclick="ui.duplicateSetConfirm(${s.id})"
                          >
                            <i class="fas fa-copy"></i>
                          </button>

                          <button
                            class="btn btn-icon"
                            title="Löschen"
                            style="color:var(--danger); border-color:#fecaca;"
                            onclick="ui.deleteSetConfirm(${s.id})"
                          >
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>
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

        if (!container) return;

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
        const validTabs = new Set(["material", "aufgabe", "personal", "checklists"]);
        if (!validTabs.has(state.editorTab)) state.editorTab = "material";

        const stats = {
          mainPct:  Number(s?.stats?.mainPct  ?? 0),
          subPct:   Number(s?.stats?.subPct   ?? 0),
          laborPct: Number(s?.stats?.laborPct ?? 0),
          mainTotal:  Number(s?.stats?.mainTotal  ?? 0),
          subTotal:   Number(s?.stats?.subTotal   ?? 0),
          laborTotal: Number(s?.stats?.laborTotal ?? 0),
          total:      Number(s?.stats?.total      ?? 0),
        };

        const clampPct = (v) => Math.max(0, Math.min(100, Number(v ?? 0) || 0));
        const wMain  = clampPct(stats.mainPct);
        const wSub   = clampPct(stats.subPct);
        const wLabor = clampPct(stats.laborPct);

        const tabLabel =
            state.editorTab === "aufgabe"    ? "Aufgabe" :
            state.editorTab === "material"   ? "Material" :
            state.editorTab === "personal"   ? "Personal" :
            "Checklists";
      const isLocked = parseInt(s?.is_locked) === 0;

      const headerHTML = `
            <div class="editor-header">
              
              <div style="display: flex; align-items: flex-start; gap: 1rem; flex: 1; min-width: 0; margin-right: 2rem;">
                
                <button 
                  onclick="app.navigate('groupList')" 
                  class="btn btn-icon" 
                  style="margin-top: 2px; flex-shrink: 0;" 
                  title="Zurück zur Übersicht"
                >
                  <i class="fas fa-arrow-left"></i>
                </button>

                <div style="flex: 1; min-width: 0;">
                  <input
                    id="set-title-input"
                    type="text"
                    class="input-title"
                    value="${esc(s?.name || "")}"
                    placeholder="Set Name eingeben..."
                    ${isLocked ? 'disabled' : ''}
                    style="width: 100%;"
                    oninput="state.editingSet.name = this.value; app.autoSave();"
                  >
                  
                  <div 
                    class="input-desc" 
                    style="cursor: pointer; display: flex; align-items: center; gap: 0.5rem; margin-top: 0.25rem; transition: color 0.2s; color: var(--text-light);"
                    onclick="${isLocked ? '' : 'setDescUI.open()'}"
                    onmouseover="${isLocked ? '' : "this.style.color='var(--primary)'"}"
                    onmouseout="this.style.color='var(--text-light)'"
                    title="${isLocked ? 'Gesperrt' : 'Klicken, um Beschreibung zu bearbeiten'}"
                  >
                    <i class="fas fa-pen-nib" style="flex-shrink: 0;"></i>
                    <span id="set-desc-preview-text" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">Lade Beschreibung...</span>
                  </div>
                </div>
              </div> 

              <div style="display: flex; gap: 0.75rem; align-items: center; flex-shrink: 0; flex-wrap: wrap; justify-content: flex-end;">
                <button 
                  onclick="ui.toggleSetLock()" 
                  class="btn ${isLocked ? 'btn-danger' : 'btn-secondary'}" 
                  style="padding: 0.5rem 1.25rem; border-radius: var(--radius-full); border: 2px solid ${isLocked ? 'var(--danger)' : 'var(--primary)'};"
                >
                  <i class="fas ${isLocked ? 'fa-lock' : 'fa-unlock-alt'}"></i> 
                  ${isLocked ? 'GESPERRT' : 'FREIGESCHALTET'}
                </button>

                <button onclick="ui.openReport()" class="btn btn-secondary" title="Als PDF exportieren">
                  <i class="fas fa-file-pdf"></i> PDF EXPORT
                </button>
                
               ${!isLocked ? `
                  <label
                    style="
                      display:flex;
                      align-items:center;
                      gap:.55rem;
                      padding:.55rem .9rem;
                      background:white;
                      border:1px solid var(--border-color);
                      border-radius:999px;
                      font-size:.75rem;
                      font-weight:900;
                      color:var(--text-main);
                      cursor:pointer;
                      user-select:none;
                    "
                    title="Automatisches Speichern umschalten"
                  >
                    <input
                      type="checkbox"
                      ${state.autoSaveEnabled ? 'checked' : ''}
                      onchange="app.setAutoSave(this.checked)"
                      style="width:16px; height:16px; accent-color: var(--primary); cursor:pointer;"
                    >
                    <i class="fas fa-check-circle" style="color:${state.autoSaveEnabled ? 'var(--accent)' : '#cbd5e1'};"></i>
                    <span>AUTO SAVE</span>
                  </label>

                  <button onclick="ui.openDuplicateModal()" class="btn btn-accent" title="Set duplizieren">
                    <i class="fas fa-copy"></i>
                  </button>
                  <button onclick="ui.deleteConfirm(${Number(s.id)})" class="btn btn-danger" title="Set löschen">
                    <i class="fas fa-trash"></i>
                  </button>
                ` : ''}

                <button id="main-save-btn" onclick="api.saveSet()" class="btn btn-primary" ...

                <button id="main-save-btn" onclick="api.saveSet()" class="btn btn-primary" ${isLocked ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : ''}>
                  <i class="fas fa-save"></i> ${s?.id ? 'SPEICHERN' : 'SET ERSTELLEN'}
                </button>
              </div>
              
            </div>
          `;

        const tabsHTML = `
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <div class="editor-tabs">
              <button class="editor-tab-btn ${state.editorTab === "material" ? "active" : ""}" onclick="app.setEditorTab('material')">
                <i class="fas fa-cubes"></i> Material
              </button>
              <button class="editor-tab-btn ${state.editorTab === "aufgabe" ? "active" : ""}" onclick="app.setEditorTab('aufgabe')">
                <i class="fas fa-list-check"></i> Aufgabe
              </button>
              <button class="editor-tab-btn ${state.editorTab === "personal" ? "active" : ""}" onclick="app.setEditorTab('personal')">
                <i class="fas fa-user-clock"></i> Personal
              </button>
              <button class="editor-tab-btn ${state.editorTab === "checklists" ? "active" : ""}" onclick="app.setEditorTab('checklists')">
                <i class="fas fa-square-check"></i> Protokols
              </button>
            </div>
            <div style="display:flex; align-items:center; gap: 1rem;">
                <div style="font-size:0.75rem; font-weight:800; color:#94a3b8;">
                  Aktiver Tab: ${tabLabel}
                </div>
                <button onclick="ui.toggleSummary()" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.7rem;" title="Übersicht umschalten">
                  <i class="fas ${state.showSummary ? 'fa-eye-slash' : 'fa-eye'}"></i> 
                  ${state.showSummary ? 'Übersicht ausblenden' : 'Übersicht einblenden'}
                </button>
              </div>
            </div>
          `;


        const summaryHTML = `
          <div class="sidebar-sticky-wrapper">
          ${isLocked ? `
            <div style="background: var(--danger-light); border: 1px solid var(--danger); padding: 1rem; border-radius: var(--radius-xl); margin-bottom: 1rem; display: flex; gap: 1rem; align-items: center;" class="fade-in">
              <div style="width: 40px; height: 40px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-shield-alt" style="color: var(--danger);"></i>
              </div>
              <div>
                <div style="font-weight: 900; color: var(--danger); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Schreibschutz Aktiv</div>
                <p style="font-size: 11px; font-weight: 700; color: #b91c1c; margin: 0; line-height: 1.3;">Dieses Set ist archiviert. Änderungen sind erst nach der Freischaltung oben rechts möglich.</p>
              </div>
            </div>
          ` : ''}
            <div class="summary-card">
              <h4 class="summary-title">Kalkulationsübersicht</h4>
              
              <div class="summary-row">
                <div class="label-col">
                  <span class="label-main">Hauptartikel</span>
                  <span class="label-badge bg-blue" style="background:#f0f7ff; color:var(--primary);">${stats.mainPct}% Anteil</span>
                </div>
                <span class="val-text">${fmtEUR(stats.mainTotal)}</span>
              </div>

              <div class="summary-row">
                <div class="label-col">
                  <span class="label-main">Zubehör / Sub</span>
                  <span class="label-badge" style="background:#f8fafc; color:#94a3b8;">${stats.subPct}% Anteil</span>
                </div>
                <span class="val-text">${fmtEUR(stats.subTotal)}</span>
              </div>

              <div class="summary-row">
                <div class="label-col">
                  <span class="label-main">Personal</span>
                  <span class="label-badge" style="background:rgba(147, 194, 28, 0.1); color:var(--accent);">${stats.laborPct}% Anteil</span>
                </div>
                <span class="val-text">${fmtEUR(stats.laborTotal)}</span>
              </div>
              
              <div style="height:1px; background:#f1f5f9; margin: 1.5rem 0;"></div>

              <div class="summary-row">
                <div class="label-col">
                  <span class="label-main">Aufgaben</span>
                  <span class="label-badge" style="background:rgba(168, 85, 247, 0.1); color:#a855f7;">${s.stats?.taskCount || 0} Tasks</span>
                </div>
                <span class="val-text" style="font-size:1rem;">${(s.stats?.taskTotalHours || 0).toFixed(2)} h</span>
              </div>

              <div class="summary-row">
                <div class="label-col">
                  <span class="label-main">Protokolle</span>
                </div>
                <span class="val-text" style="font-size:1rem;">${s.stats?.protocolCount || 0}</span>
              </div>

              ${(s.stats?.personalBadges && s.stats.personalBadges.length > 0) ? `
                  <div style="margin-bottom: 1.5rem;">
                    <span class="label-main" style="display:block; margin-bottom:0.5rem; font-size:0.75rem;">Beteiligtes Personal</span>
                    <div style="display:flex; flex-wrap:wrap; gap:0.5rem;">
                        ${s.stats.personalBadges.map(p => `
                            <div style="display:flex; align-items:center; gap:4px; background:#f8fafc; border:1px solid #e2e8f0; padding:2px 6px; border-radius:6px; font-size:0.7rem; font-weight:700; color:#64748b;">
                                <i class="fas fa-user-tag" style="color:var(--accent)"></i>
                                <span>${escapeHtml(p.name)}</span>
                                <span style="background:#e2e8f0; padding:0 4px; border-radius:4px; font-size:0.65rem;">${p.hours.toFixed(1)}h</span>
                            </div>
                        `).join('')}
                    </div>
                  </div>
              ` : ''}

              <div class="total-section">
                <div style="display:flex; justify-content:space-between; align-items:flex-end;">
                  <span class="total-label">Gesamtwert Set</span>
                  <span class="total-value">${fmtEUR(stats.total)}</span>
                </div>
                <div class="progress-bar">
                  <div class="progress-segment" style="background:var(--primary); width:${stats.mainPct}%"></div>
                  <div class="progress-segment" style="background:#94a3b8; width:${stats.subPct}%"></div>
                  <div class="progress-segment" style="background:var(--accent); width:${stats.laborPct}%"></div>
                </div>
              </div>
            </div>

            <div class="summary-card" style="background: linear-gradient(to bottom right, #ffffff, #f8fafc); margin-top:0;">
              <div style="display:flex; align-items:center; gap:0.5rem; border-bottom:1px solid #f1f5f9; padding-bottom:0.75rem; margin-bottom:1rem;">
                <i class="fas fa-book-open" style="color:var(--primary);"></i>
                <h4 style="font-size:10px; font-weight:900; color:#475569; text-transform:uppercase; letter-spacing:0.1em; margin:0;">Set-Biografie</h4>
              </div>
              
              <div style="display:flex; flex-direction:column; gap:0.75rem;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                  <span style="font-size:10px; font-weight:800; color:#94a3b8; text-transform:uppercase;">Verfasser</span>
                  <div style="display:flex; align-items:center; gap:6px;">
                      <i class="fas fa-user-circle" style="color:#cbd5e1; font-size:14px;"></i>
                      <span style="font-weight:900; color:#334155; font-size:12px;">${escapeHtml(s.creator_name || 'System')}</span>
                  </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center;">
                  <span style="font-size:10px; font-weight:800; color:#94a3b8; text-transform:uppercase;">In Angeboten</span>
                  <span style="font-weight:900; color:#334155; font-size:12px;">${s.count_offer || 0} mal genutzt</span>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center;">
                  <span style="font-size:10px; font-weight:800; color:#94a3b8; text-transform:uppercase;">Dupliziert</span>
                  <span style="font-weight:900; color:#334155; font-size:12px;">${s.count_copy || 0} mal kopiert</span>
                </div>

                <div style="margin-top:0.25rem; padding-top:0.75rem; border-top:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center;">
                  <span style="font-size:10px; font-weight:900; color:var(--primary); text-transform:uppercase;">Soll/Ist Präzision</span>
                  <div style="display:flex; align-items:center; gap:4px;">
                      <i class="fas fa-bullseye" style="color:var(--primary); font-size:10px;"></i>
                      <span style="font-weight:900; color:var(--primary); font-size:13px;">98.5%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        `;

        const styleHTML = `
          <style>
            .tw-shell{display:flex;flex-direction:column;gap:2.5rem;}
            .tw-section{display:block;}
            .tw-header{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;}
            .tw-head-left{min-width:240px;}
            .tw-actions{display:flex;gap:.75rem;flex-wrap:wrap;}
            .tw-title{margin:0;font-size:1.05rem;font-weight:900;color:var(--text-main);display:flex;align-items:center;gap:.6rem;}
            .tw-title--primary{color:var(--primary);}
            .tw-title--accent{color:var(--accent);}
            .tw-subtitle{margin:.35rem 0 0;font-size:.9rem;opacity:.8;}
          </style>
        `;

        const content = `
          ${this.getBreadcrumbsHTML?.() ?? ""}
          ${headerHTML}
          ${tabsHTML}
         <div class="${state.showSummary ? 'editor-grid' : ''}" style="${!state.showSummary ? 'display: block;' : ''}">
            <div class="tw-shell min-w-0" id="dynamic-tab-content">
              </div>
            ${styleHTML}
            ${state.showSummary ? summaryHTML : ''}
          </div>
        `;

        container.innerHTML = content;

        // Render tab-specific mounts
        const tabContainer = document.getElementById('dynamic-tab-content');
        if(!tabContainer) return;

        try {
          if (state.editorTab === "material") {
              const materialCounts = this.getMaterialCounts();
              const mainTotal = toNum(s?.stats?.mainTotal, 0);
              const subTotal = toNum(s?.stats?.subTotal, 0);
              const materialGrandTotal = mainTotal + subTotal;

              tabContainer.innerHTML = `
                <section class="tw-section">
                  <header class="tw-header mb-4">
                    <div class="tw-head-left">
                      <h3 class="tw-title tw-title--primary"><i class="fas fa-cubes"></i> Komponenten</h3>

                      <div style="display:flex; flex-wrap:wrap; gap:.5rem; margin-top:.75rem;">
                        <span class="pill pill-blue"><div class="dot bg-blue"></div> ${materialCounts.mainCount} Hauptartikel</span>
                        <span class="pill pill-gray"><div class="dot bg-gray"></div> ${materialCounts.subCount} Unterartikel</span>
                        <span class="pill pill-purple"><div class="dot bg-purple"></div> ${materialCounts.totalCount} Gesamtpositionen</span>
                        <span class="pill pill-green"><div class="dot bg-green"></div> Material: ${this.formatMoney(materialGrandTotal)}</span>
                      </div>
                    </div>
                  </header>

                  <div id="comp-list"></div>
                </section>
              `;
              this.renderComponentItems?.();
                  } else if (state.editorTab === "personal") {
                    tabContainer.innerHTML = `
                        <section class="tw-section">
                          <header class="tw-header">
                            <div class="tw-head-left">
                              <h3 class="tw-title tw-title--accent"><i class="fas fa-user-clock"></i> Personalaufwand</h3>
                              <p class="tw-subtitle">Zugeordnete Positionen & Arbeitsstunden</p>
                            </div>
                            <div class="tw-actions">
                            <button type="button" class="btn btn-accent" disabled style="opacity:.55; cursor:not-allowed;" title="Personal wird automatisch aus Aufgabe übernommen">
                              <i class="fas fa-link"></i> AUTO AUS AUFGABE
                            </button>               
                            </div>
                          </header>
                          <div class="labor-table-wrap">
                            <table class="labor-table">
                                <thead>
                                <tr>
                                  <th>Position / Mitarbeiter</th>
                                  <th class="t-center">Anteil</th>
                                  <th class="t-center">Stundensatz</th>
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
                    this.renderLaborItems?.();
                  } else if (state.editorTab === "aufgabe") {
                    tabContainer.innerHTML = `
                        <section class="tw-section">
                          <header class="tw-header">
                            <div class="tw-head-left">
                              <h3 class="tw-title"><i class="fas fa-list-check"></i> Aufgaben</h3> 
                            </div>
                            <div class="tw-actions">
                              <button type="button" class="btn btn-icon" title="Optionen aktualisieren" onclick="ui.reloadTaskOptions()"><i class="fas fa-rotate"></i></button>
                              <button type="button" class="btn btn-secondary" onclick="ui.openCustomTaskModal()"><i class="fas fa-plus"></i> EIGENE AUFGABE ERSTELLEN</button>
                              <button type="button" class="btn btn-accent" onclick="ui.openCostingModal()">
                                <i class="fas fa-calculator"></i> Kosten
                              </button>
                            </div>
                          </header>
                          <div id="tasks-tab-mount"></div>
                        </section>
                    `;
                    this.renderTasksTab?.();
                  } else {
                    tabContainer.innerHTML = `
                        <section class="tw-section">
                          <header class="tw-header">
                            <div class="tw-head-left">
                              <h3 class="tw-title"><i class="fas fa-square-check"></i> Protokolle</h3>
                              <p class="tw-subtitle">Protokolle / Checklists verwalten</p>
                            </div>
                            <div class="tw-actions">
                              ${!isLocked ? `<button type="button" class="btn btn-secondary" onclick="ui.openChecklistPicker?.()"><i class="fas fa-plus"></i> PROTOKOLL HINZUFÜGEN</button>` : ''}
                            </div>
                          </header>
                          <div id="checklists-tab-mount"></div>
                        </section>
                    `;
                    this.renderChecklistsTab?.();
              }

       } catch (e) {
          console.error("renderEditor mount error:", e);
        }
        
        // ADDED: Sync the text snippet when the editor tab loads
        setTimeout(() => {
          if (window.setDescUI) window.setDescUI.syncPreview();
        }, 50);
        
      },
 

renderComponentItems() {
  const list = document.getElementById('comp-list');
  if (!list) return;

  const esc = (v) => ui.escapeHtml(v ?? '');
  const num = (v, d = 0) => {
    const n = Number(v);
    return Number.isFinite(n) ? n : d;
  };

  const comps = Array.isArray(state.editingSet?.components) ? state.editingSet.components : [];
  const isLocked = !!state.isLocked;
  const vCols = state.visibleColMat || {};

  const flattenComponents = (items = []) => {
    const out = [];
    items.forEach(item => {
      out.push(item);
      if (Array.isArray(item?.subComponents) && item.subComponents.length) {
        out.push(...flattenComponents(item.subComponents));
      }
    });
    return out;
  };

  const allComponents = flattenComponents(comps);

  const totals = allComponents.reduce((acc, data) => {
    const pPrice = ui.getMaterialUnitBase(data);
    const pQty = ui.getMaterialQty(data);
    const pMargin = ui.getMaterialMargin(data);
    const pPe = Math.max(1, num(data.price_unit, 1));

    const gkPerPiece = pPrice * (num(state.globalGemeinkosten, 0) / 100);
    const wagnisPerPiece = pPrice * (num(state.globalWagnis, 0) / 100);
    const profitPerPiece = pPrice * (pMargin / 100);
    const salesPrice = pPrice + gkPerPiece + wagnisPerPiece + profitPerPiece;

    const ekTotal = (pPrice / pPe) * pQty;
    const vkTotal = (salesPrice / pPe) * pQty;
    const dbTotal = vkTotal - ekTotal;

    acc.ek += ekTotal;
    acc.vk += vkTotal;
    acc.db += dbTotal;

    return acc;
  }, { ek: 0, vk: 0, db: 0 });

  const grandTotalEK = totals.ek;
  const grandTotalVK = totals.vk;
  const grandTotalDB = totals.db;
  const grandTotalMarginEuro = grandTotalDB;

  const colLabels = {
    articleNumber: 'Hersteller-Nr.',
    productTitle: 'Produkttitel',
    description: 'Beschreibung',
    supplier: 'Lieferant & Kondition',
    dokumente: 'Dokumente',
    quantity: 'Menge',
    vpe: 'Einheit',
    pe: 'VPE',
    ek: 'EK / Einheit',
    ek_total: 'EK gesamt',
    margin: 'Marge',
    vk: 'VK / Einheit',
    profit: 'DB / Einheit',
    vk_total: 'VK gesamt',
    db_total: 'DB gesamt',
    weighting: 'Gewichtung',
    aktionen: 'Aktionen',
  };

  const colDefs = [
    { key: '__pos', label: 'Pos.', width: '88px', always: true, align: 'center' },
    { key: 'articleNumber', label: colLabels.articleNumber, width: '170px' },
    { key: 'productTitle', label: colLabels.productTitle, width: '280px' },
    { key: 'description', label: colLabels.description, width: '240px' },

    { key: 'supplier', label: colLabels.supplier, width: '240px' },
    { key: 'dokumente', label: colLabels.dokumente, width: '180px' },
    { key: 'quantity', label: colLabels.quantity, width: '130px', align: 'center' },

    { key: 'vpe', label: colLabels.vpe, width: '110px', align: 'center' },   // Einheit
    { key: 'pe', label: colLabels.pe, width: '90px', align: 'center' },       // VPE

    { key: 'ek', label: colLabels.ek, width: '130px', align: 'right' },
    { key: 'ek_total', label: colLabels.ek_total, width: '145px', align: 'right' },

    { key: 'margin', label: colLabels.margin, width: '110px', align: 'right' },

    { key: 'vk', label: colLabels.vk, width: '130px', align: 'right' },
    { key: 'profit', label: colLabels.profit, width: '130px', align: 'right' },

    { key: 'vk_total', label: colLabels.vk_total, width: '145px', align: 'right' },
    { key: 'db_total', label: colLabels.db_total, width: '145px', align: 'right' },

    { key: 'weighting', label: colLabels.weighting, width: '180px' },
    { key: 'aktionen', label: colLabels.aktionen, width: '120px', align: 'right' },
  ];

  const visibleCols = colDefs.filter(col => col.always || !!vCols[col.key]);
  const gridTemplate = visibleCols.map(col => col.width).join(' ');

  const buildColumnsDropdown = () => {
    if (!state.showMatDrop) return '';

    return `
      <div class="absolute right-0 top-full w-64 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 p-2 flex flex-col gap-1 mt-2 max-h-96 overflow-y-auto">
        ${Object.keys(vCols)
          .filter((key) => key !== 'condition')
          .map((key) => `
            <label class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 cursor-pointer text-sm font-medium text-gray-700">
              <input
                type="checkbox"
                ${vCols[key] ? 'checked' : ''}
                onchange="ui.toggleMatCol('${key}')"
                class="rounded text-[#78b2ce] w-4 h-4"
              >
              ${colLabels[key] ?? key}
            </label>
          `)
          .join('')}
      </div>
    `;
  };

  const toolbarHtml = `
    <div class="flex justify-between items-center gap-4 flex-wrap p-4 border-b border-gray-200 bg-white rounded-t-[2rem]">
      <div class="search-wrapper" style="max-width:350px; width:100%;">
        <i class="fas fa-search search-icon"></i>
        <input
          type="text"
          class="search-input"
          style="width:100%;"
          placeholder="Komponenten suchen..."
          value="${esc(state.materialSearch || '')}"
          oninput="ui.filterMaterialTable(this.value)"
        >
      </div>

      <div class="flex items-center gap-3 relative flex-wrap" id="mat-col-dropdown-wrap">
        <button
          onclick="ui.toggleMatDrop(event)"
          class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-full shadow-sm text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors"
        >
          <i class="fas fa-sliders-h"></i> Spalten
        </button>

        <button
          type="button"
          onclick="ui.hydrateCurrentSetComponents()"
          class="flex items-center gap-2 px-4 py-2 bg-white border border-[#74b2d4]/30 text-[#74b2d4] rounded-full shadow-sm text-sm font-bold hover:bg-[#f0f7ff] transition-colors"
          ${isLocked ? 'disabled style="opacity:.5;cursor:not-allowed;"' : ''}
          title="Fehlende Daten für dieses Set nachladen"
        >
          <i class="fas fa-rotate"></i> SET DATEN NACHLADEN
        </button>

        <button
          type="button"
          onclick="ui.hydrateAllSetsInGroup()"
          class="flex items-center gap-2 px-4 py-2 bg-white border border-[#93c21c]/30 text-[#93c21c] rounded-full shadow-sm text-sm font-bold hover:bg-[rgba(147,194,28,.08)] transition-colors"
          title="Alle Sets der aktuellen Gruppe nachladen"
        >
          <i class="fas fa-database"></i> ALLE SETS SYNC
        </button>

        ${buildColumnsDropdown()}

        ${!isLocked ? `
          <button
            onclick="ui.openCatalog('main')"
            class="flex items-center gap-2 px-5 py-2 bg-[#78b2ce] text-white text-sm font-black rounded-full hover:bg-[#639ab5] shadow-md tracking-wide transition-all"
          >
            <i class="fas fa-plus"></i> ARTIKEL
          </button>
        ` : ''}
      </div>
    </div>
  `;

  const cellWrap = (content, extra = '') => `
    <div class="mat-grid-cell ${extra}">
      ${content}
    </div>
  `;

  const renderTotalsRow = () => {
    let cells = '';

    visibleCols.forEach(col => {
      switch (col.key) {
        case '__pos':
          cells += cellWrap(`
            <div class="font-black text-xs uppercase tracking-[0.14em] text-slate-500">
              Gesamt
            </div>
          `, 'mat-grid-cell-center mat-total-cell');
          break;

        case 'ek_total':
          cells += cellWrap(`
            <div class="w-full flex flex-col items-end">
              <div class="text-[10px] uppercase tracking-[0.12em] font-black text-slate-500">EK gesamt</div>
              <div class="text-sm font-black text-slate-800">${ui.formatMoney(grandTotalEK)}</div>
            </div>
          `, 'mat-grid-cell-right mat-total-cell');
          break;

        case 'vk_total':
          cells += cellWrap(`
            <div class="w-full flex flex-col items-end">
              <div class="text-[10px] uppercase tracking-[0.12em] font-black text-[#78b2ce]">VK gesamt</div>
              <div class="text-sm font-black text-[#4f8aa7]">${ui.formatMoney(grandTotalVK)}</div>
            </div>
          `, 'mat-grid-cell-right mat-total-cell');
          break;

        case 'db_total':
          cells += cellWrap(`
            <div class="w-full flex flex-col items-end">
              <div class="text-[10px] uppercase tracking-[0.12em] font-black text-emerald-600">DB gesamt</div>
              <div class="text-sm font-black text-emerald-700">${ui.formatMoney(grandTotalDB)}</div>
            </div>
          `, 'mat-grid-cell-right mat-total-cell');
          break;

        default:
          cells += cellWrap(``, 'mat-total-cell');
          break;
      }
    });

    return `
      <div class="mat-data-row mat-total-row">
        <div
          class="mat-data-grid"
          style="display:grid; grid-template-columns:${gridTemplate};"
        >
          ${cells}
        </div>
      </div>
    `;
  };

  const renderRow = (data, { isSubItem = false, parentId = null, mIdx = null, sIdx = null } = {}) => {
    const pPrice = ui.getMaterialUnitBase(data);
    const pQty = ui.getMaterialQty(data);
    const pMargin = ui.getMaterialMargin(data);
    const pPe = Math.max(1, num(data.price_unit, 1));

    const gkPerPiece = pPrice * (num(state.globalGemeinkosten, 0) / 100);
    const wagnisPerPiece = pPrice * (num(state.globalWagnis, 0) / 100);
    const profitPerPiece = pPrice * (pMargin / 100);
    const salesPrice = pPrice + gkPerPiece + wagnisPerPiece + profitPerPiece;

    const totalSalesPrice = (salesPrice / pPe) * pQty;
    const totalItemEK = (pPrice / pPe) * pQty;
    const totalItemDB = totalSalesPrice - totalItemEK;

    const weightEK = grandTotalEK > 0 ? (totalItemEK / grandTotalEK) * 100 : 0;
    const weightProfit = grandTotalMarginEuro > 0 ? (totalItemDB / grandTotalMarginEuro) * 100 : 0;

    const weightEKText = Number.isFinite(weightEK) ? weightEK.toFixed(1) : '0.0';
    const weightProfitText = Number.isFinite(weightProfit) ? weightProfit.toFixed(1) : '0.0';

    const safeWeightEK = Math.max(0, Math.min(100, Number(weightEKText)));
    const safeWeightProfit = Math.max(0, Math.min(100, Number(weightProfitText)));

    const globalMatMargin = num(state.globalMatMargin, 0);
    const minMatMargin = num(state.minMatMargin, 0);
    const isCustomMargin = Math.abs(pMargin - globalMatMargin) > 0.01;

    const marginStatus =
      pMargin < minMatMargin
        ? 'critical'
        : pMargin < globalMatMargin
          ? 'warning'
          : 'good';

    const heatmapColor =
      marginStatus === 'critical'
        ? 'bg-red-500'
        : marginStatus === 'warning'
          ? 'bg-amber-400'
          : 'bg-emerald-400';

    const heatmapBg = marginStatus === 'critical' ? 'bg-red-50/30' : '';

    const mainIdStr = parentId || data.id;
    const subIdStr = isSubItem ? `'${data.id}'` : 'null';
    const compIdForText = data.component_id ?? data.pivot_id ?? data.id ?? '';

    const fieldAttr = (field) =>
      `data-field="${field}" data-main-index="${mIdx}" data-sub-index="${isSubItem ? sIdx : ''}"`;

    const ctrl = 'mat-ctrl';
    const input = 'mat-ctrl mat-input';
    const inputCenter = 'mat-ctrl mat-input-center';
    const btnIcon = 'mat-btn mat-btn-icon';
    const menuBtn = 'mat-btn mat-btn-icon material-menu-btn';

    const productTypeBadge =
      data.type === 'zubehoer'
        ? 'bg-slate-200 text-slate-600'
        : 'bg-[#78b2ce]/10 text-[#78b2ce]';

    const productTypeLabel = data.type === 'zubehoer' ? 'Zubehör' : 'Hauptartikel';

    let cells = '';

    visibleCols.forEach(col => {
      switch (col.key) {
        case '__pos':
          cells += cellWrap(`
            <div class="flex items-start justify-center pt-2 gap-2">
              ${
                !isSubItem && !isLocked
                  ? `<i class="fas fa-grip-vertical w-4 h-4 text-gray-300 cursor-grab mt-2 handle"></i>`
                  : !isSubItem
                    ? `<div class="w-4 h-4 mt-2"></div>`
                    : ''
              }
              ${isSubItem && !isLocked ? `<i class="fas fa-grip-lines w-3 h-3 text-gray-300 cursor-grab mt-2 sub-handle"></i>` : ''}
              <div class="flex items-center justify-center font-bold text-sm ${isSubItem ? 'text-gray-400 mt-1' : 'w-8 h-8 rounded-full bg-slate-100 text-slate-700 mt-1'}">
                ${data.pos}
              </div>
            </div>
          `, 'mat-grid-cell-center');
          break;

        case 'articleNumber':
          cells += cellWrap(`
            <div class="flex items-start gap-2">
              ${
                !isSubItem
                  ? `
                    <button type="button" onclick="ui.toggleExpandMat('${data.id}')" class="${btnIcon} shrink-0 mt-1">
                      <i class="fas ${data.isExpanded ? 'fa-chevron-down' : 'fa-chevron-right'} w-3 h-3 text-center"></i>
                    </button>
                  `
                  : ''
              }
              <input
                ${fieldAttr('articleNumber')}
                value="${esc(data.articleNumber || data.article_no || '—')}"
                onchange="ui.handleUpdateMaterial('${mainIdStr}', ${subIdStr}, 'articleNumber', this.value)"
                ${isLocked ? 'readonly' : ''}
                class="${input}"
              />
            </div>
          `);
          break;

        case 'productTitle':
          cells += cellWrap(`
            <div class="flex flex-col justify-center">
              <div class="flex gap-1 mb-1 items-center flex-wrap">
                <span class="text-[9px] uppercase font-bold px-1.5 py-0.5 rounded-sm ${productTypeBadge}">
                  ${productTypeLabel}
                </span>
              </div>

              <input
                ${fieldAttr('productTitle')}
                value="${esc(data.productTitle || data.product_name || '')}"
                onchange="ui.handleUpdateMaterial('${mainIdStr}', ${subIdStr}, 'productTitle', this.value)"
                ${isLocked ? 'readonly' : ''}
                class="${input}"
              />

              <div class="flex items-center gap-2 mt-2 flex-wrap">
                <button
                  type="button"
                  class="mat-toggle-icon ${data.isFavorite ? 'active favorite' : ''}"
                  title="Favorit ${data.isFavorite ? 'deaktivieren' : 'aktivieren'}"
                  onclick="ui.handleUpdateMaterial('${mainIdStr}', ${subIdStr}, 'isFavorite', ${!data.isFavorite})"
                  ${isLocked ? 'disabled' : ''}
                >
                  <i class="${data.isFavorite ? 'fas' : 'far'} fa-star"></i>
                  <span>Favorit</span>
                </button>

                <button
                  type="button"
                  class="mat-toggle-icon ${data.isStammartikel ? 'active stamm' : ''}"
                  title="Stammartikel ${data.isStammartikel ? 'deaktivieren' : 'aktivieren'}"
                  onclick="ui.handleUpdateMaterial('${mainIdStr}', ${subIdStr}, 'isStammartikel', ${!data.isStammartikel})"
                  ${isLocked ? 'disabled' : ''}
                >
                  <i class="fas fa-database"></i>
                  <span>Stamm</span>
                </button>
              </div>
            </div>
          `);
          break;

        case 'description':
          cells += cellWrap(`
            <button
              type="button"
              onclick="materialDescUI.open(${mIdx}, ${isSubItem ? sIdx : 'null'})"
              class="w-full text-left border border-gray-200 rounded-xl px-3 py-2 bg-white hover:bg-slate-50 transition"
              ${isLocked ? 'disabled' : ''}
            >
              <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                  <div class="text-[10px] uppercase font-black tracking-wider text-slate-400 mb-1">
                    Beschreibung
                  </div>
                  <div class="text-sm font-semibold text-slate-700 truncate">
                    ${
                      data.description
                        ? esc(String(data.description).replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim()).slice(0, 80)
                        : 'Beschreibung öffnen...'
                    }
                  </div>
                </div>

                <div class="mat-btn mat-btn-icon shrink-0" style="width:30px; height:30px;">
                  <i class="fas fa-expand"></i>
                </div>
              </div>
            </button>
          `);
          break;

        case 'supplier':
          cells += cellWrap(`
            <div class="flex flex-col gap-2">
              <input
                ${fieldAttr('supplier')}
                value="${esc(data.supplier || data.distributor_name || '')}"
                onchange="ui.handleUpdateMaterial('${mainIdStr}', ${subIdStr}, 'supplier', this.value)"
                ${isLocked ? 'readonly' : ''}
                class="${input}"
              />

              <input
                ${fieldAttr('distributor_article_no')}
                value="${esc(data.distributor_article_no || '')}"
                onchange="ui.handleUpdateMaterial('${mainIdStr}', ${subIdStr}, 'distributor_article_no', this.value)"
                ${isLocked ? 'readonly' : ''}
                placeholder="Lief.-Nr."
                class="${input}"
              />

              <div class="flex flex-col gap-2">
                <div class="mat-addon-wrap">
                  <input
                    ${fieldAttr('skonto')}
                    type="number"
                    value="${num(data.skonto, 0)}"
                    onchange="ui.handleUpdateMaterial('${mainIdStr}', ${subIdStr}, 'skonto', parseFloat(this.value))"
                    ${isLocked ? 'readonly' : ''}
                    class="mat-addon-input text-right"
                  />
                  <span class="mat-addon-text">%</span>
                </div>

                <div class="mat-addon-wrap">
                  <input
                    ${fieldAttr('paymentTerms')}
                    type="number"
                    value="${num(data.paymentTerms, 0)}"
                    onchange="ui.handleUpdateMaterial('${mainIdStr}', ${subIdStr}, 'paymentTerms', parseFloat(this.value))"
                    ${isLocked ? 'readonly' : ''}
                    class="mat-addon-input text-right"
                  />
                  <span class="mat-addon-text">T</span>
                </div>
              </div>
            </div>
          `);
          break;

        case 'dokumente':
          cells += cellWrap(`
            <div class="flex flex-col items-start gap-2">
              ${
                Array.isArray(data.docs) && data.docs.length
                  ? data.docs.map((doc) => `
                      <button type="button" class="mat-chip w-full justify-start gap-2 text-[#78b2ce] border-[#78b2ce]/20 bg-[#78b2ce]/10">
                        <i class="fas fa-file-alt w-3 h-3 shrink-0"></i>
                        <span class="truncate">${esc(doc?.name)}</span>
                      </button>
                    `).join('')
                  : `
                    <span class="text-[10px] text-gray-400 italic flex items-center gap-1">
                      <i class="fas fa-minus w-3 h-3"></i> Keine Docs
                    </span>
                  `
              }
            </div>
          `);
          break;

        case 'ek':
          cells += cellWrap(`
            <div class="mat-addon-wrap">
              <input
                ${fieldAttr('purchasePrice')}
                type="number"
                value="${num(data.purchasePrice, 0)}"
                onchange="ui.handleUpdateMaterial('${mainIdStr}', ${subIdStr}, 'purchasePrice', parseFloat(this.value))"
                ${isLocked ? 'readonly' : ''}
                class="mat-addon-input text-right"
              />
              <span class="mat-addon-text">€</span>
            </div>
          `, 'mat-grid-cell-right');
          break;

        case 'vk':
          cells += cellWrap(`
            <div class="mat-addon-wrap">
              <input
                ${fieldAttr('vk')}
                type="number"
                value="${Number(salesPrice).toFixed(2)}"
                onchange="ui.handleMaterialVKChange('${mainIdStr}', ${subIdStr}, this.value, ${num(data.purchasePrice, 0)})"
                ${isLocked ? 'readonly' : ''}
                class="mat-addon-input text-right"
              />
              <span class="mat-addon-text">€</span>
            </div>
          `, 'mat-grid-cell-right');
          break;

        case 'profit':
          cells += cellWrap(`
            <div class="mat-chip justify-end w-full text-[#78b2ce] font-bold">
              +${ui.formatMoney(profitPerPiece)}
            </div>
          `, 'mat-grid-cell-right');
          break;

        case 'ek_total':
          cells += cellWrap(`
            <div class="w-full text-right">
              <span class="font-black text-slate-800">${ui.formatMoney(totalItemEK)}</span>
            </div>
          `, 'mat-grid-cell-right');
          break;

        case 'vk_total':
          cells += cellWrap(`
            <div class="w-full text-right">
              <span class="font-black text-[#4f8aa7]">${ui.formatMoney(totalSalesPrice)}</span>
            </div>
          `, 'mat-grid-cell-right');
          break;

        case 'db_total':
          cells += cellWrap(`
            <div class="w-full text-right">
              <span class="font-black text-emerald-700">${ui.formatMoney(totalItemDB)}</span>
            </div>
          `, 'mat-grid-cell-right');
          break;

        case 'margin':
          cells += cellWrap(`
            <div class="mat-addon-wrap ${isCustomMargin ? 'ring-1 ring-emerald-200 bg-emerald-50/30' : ''}">
              <input
                ${fieldAttr('margin')}
                type="number"
                value="${num(data.margin, 0)}"
                onchange="ui.handleUpdateMaterial('${mainIdStr}', ${subIdStr}, 'margin', parseFloat(this.value))"
                ${isLocked ? 'readonly' : ''}
                class="mat-addon-input text-right"
              />
              <span class="mat-addon-text">%</span>
            </div>
          `, 'mat-grid-cell-right');
          break;

        case 'pe':
          cells += cellWrap(`
            <input
              ${fieldAttr('price_unit')}
              type="number"
              value="${pPe}"
              min="1"
              onchange="ui.handleUpdateMaterial('${mainIdStr}', ${subIdStr}, 'price_unit', Math.max(1, parseFloat(this.value)))"
              ${isLocked ? 'readonly' : ''}
              style="width:65px"
              class="mat-ctrl mat-input-center"
            />
          `, 'mat-grid-cell-center');
          break;

        case 'quantity':
          cells += cellWrap(`
            <div class="mat-stepper">
              <button
                type="button"
                ${isLocked ? 'disabled' : ''}
                onclick="ui.handleUpdateMaterial('${mainIdStr}', ${subIdStr}, 'quantity', ${Math.max(1, num(data.quantity, 1) - 1)})"
                class="step-btn"
              >
                <i class="fas fa-minus text-[10px]"></i>
              </button>

              <input
                ${fieldAttr('quantity')}
                type="number"
                value="${num(data.quantity, 1)}"
                onchange="ui.handleUpdateMaterial('${mainIdStr}', ${subIdStr}, 'quantity', parseFloat(this.value))"
                ${isLocked ? 'readonly' : ''}
                class="step-input"
              />

              <button
                type="button"
                ${isLocked ? 'disabled' : ''}
                onclick="ui.handleUpdateMaterial('${mainIdStr}', ${subIdStr}, 'quantity', ${num(data.quantity, 1) + 1})"
                class="step-btn"
              >
                <i class="fas fa-plus text-[10px]"></i>
              </button>
            </div>
          `, 'mat-grid-cell-center');
          break;

        case 'vpe':
          cells += cellWrap(`
            <select
              ${fieldAttr('measure')}
              onchange="ui.handleUpdateMaterial('${mainIdStr}', ${subIdStr}, 'measure', this.value)"
              ${isLocked ? 'disabled' : ''}
              class="${ctrl} mat-input-center w-full"
            >
              <option value="Stk" ${data.measure === 'Stk' || data.measure === 'Stk.' ? 'selected' : ''}>Stk</option>
              <option value="m" ${data.measure === 'm' ? 'selected' : ''}>m</option>
              <option value="m²" ${data.measure === 'm²' ? 'selected' : ''}>m²</option>
              <option value="m³" ${data.measure === 'm³' ? 'selected' : ''}>m³</option>
              <option value="kg" ${data.measure === 'kg' ? 'selected' : ''}>kg</option>
              <option value="l" ${data.measure === 'l' ? 'selected' : ''}>l</option>
              <option value="Pausch" ${data.measure === 'Pausch' ? 'selected' : ''}>Pausch</option>
            </select>
          `, 'mat-grid-cell-center');
          break;

        case 'weighting':
          cells += cellWrap(`
            <div class="flex flex-col gap-2">
              <div class="flex flex-col gap-0.5">
                <div class="flex justify-between items-center text-[10px]">
                  <span class="text-gray-400">Kosten</span>
                  <span class="font-bold text-slate-700">${weightEKText}%</span>
                </div>
                <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                  <div class="h-full bg-slate-400 rounded-full" style="width:${safeWeightEK}%"></div>
                </div>
              </div>

              <div class="flex flex-col gap-0.5">
                <div class="flex justify-between items-center text-[10px]">
                  <span class="text-gray-400">DB</span>
                  <span class="font-bold text-[#78b2ce]">${weightProfitText}%</span>
                </div>
                <div class="w-full h-1.5 bg-[#78b2ce]/10 rounded-full overflow-hidden">
                  <div class="h-full bg-[#78b2ce] rounded-full" style="width:${safeWeightProfit}%"></div>
                </div>
              </div>
            </div>
          `);
          break;

        case 'total':
          cells += cellWrap(`
            <div class="flex flex-col items-end w-full">
              <span class="font-black text-gray-900 text-base">${ui.formatMoney(totalSalesPrice)}</span>
              <span class="text-[10px] font-semibold text-slate-400">EK ${ui.formatMoney(totalItemEK)}</span>
            </div>
          `, 'mat-grid-cell-right');
          break;

        case 'aktionen':
          cells += cellWrap(`
            ${
              !isLocked
                ? `
                  <div class="material-actions-menu" style="display:flex; justify-content:flex-end;">
                    <button
                      type="button"
                      class="${menuBtn}"
                      onclick="event.stopPropagation(); ui.toggleMaterialMenu(this)"
                      title="Aktionen"
                    >
                      <i class="fas fa-ellipsis-v"></i>
                    </button>

                    <div class="material-menu-dropdown" onclick="event.stopPropagation()">
                      <button
                        type="button"
                        class="material-menu-item"
                        onclick="ui.refreshPrice(${mIdx}, ${sIdx}); ui.closeAllMaterialMenus();"
                      >
                        <i class="fas fa-sync-alt"></i><span>Preis aktualisieren</span>
                      </button>

                      <button
                        type="button"
                        class="material-menu-item"
                        onclick="ui.handleUpdateMaterial('${mainIdStr}', ${subIdStr}, 'isEditingProps', ${!data.isEditingProps}); ui.closeAllMaterialMenus();"
                      >
                        <i class="fas fa-sliders-h"></i><span>Typ & Favorit</span>
                      </button>

                      <button
                        type="button"
                        class="material-menu-item"
                        data-comp-id="${compIdForText}"
                        data-comp-title="${esc(data.productTitle || data.product_name || '')}"
                        onclick="ui.openDescriptionFromBtn(this); ui.closeAllMaterialMenus();"
                      >
                        <i class="fas fa-pen-nib"></i><span>Texte & Varianten</span>
                      </button>

                      <button
                        type="button"
                        class="material-menu-item"
                        onclick="ui.setDistributorCompareContext(${mIdx}, ${sIdx}); ui.openDistributorCompare(${data.product_id || 'null'}, ${data.distributor_id || 'null'}); ui.closeAllMaterialMenus();"
                        ${!data.product_id ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : ''}
                      >
                        <i class="fas fa-chart-line"></i><span>Lieferanten vergleichen</span>
                      </button>

                      <button
                        type="button"
                        class="material-menu-item danger"
                        onclick="ui.removeComp(${mIdx}, ${sIdx}); ui.closeAllMaterialMenus();"
                      >
                        <i class="fas fa-trash"></i><span>Löschen</span>
                      </button>
                    </div>
                  </div>
                `
                : ''
            }
          `, 'mat-grid-cell-right');
          break;
      }
    });

    const rowClass = isSubItem
      ? 'mat-data-row is-sub-row'
      : `mat-data-row is-main-row ${heatmapBg}`;

    return `
      <div class="${rowClass}" ${isSubItem ? `data-sub-index="${data.pos}"` : ''}>
        ${!isSubItem ? `<div class="absolute left-0 top-0 bottom-0 w-1.5 ${heatmapColor}" title="Marge Status"></div>` : ''}

        <div
          class="mat-data-grid"
          style="display:grid; grid-template-columns:${gridTemplate};"
        >
          ${cells}
        </div>

        ${
          data.isEditingProps && !isLocked
            ? `
              <div class="flex flex-wrap gap-6 p-4 border-t border-gray-200 bg-slate-100/80 shadow-inner ${isSubItem ? 'ml-12' : 'ml-0'}">
                <div class="flex flex-col gap-1.5">
                  <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-1">
                    <i class="fas fa-tag w-3 h-3"></i> Typ
                  </span>
                  <select
                    onchange="ui.handleUpdateMaterial('${mainIdStr}', ${subIdStr}, 'type', this.value)"
                    class="${input}"
                  >
                    <option value="haupt" ${data.type === 'haupt' ? 'selected' : ''}>Hauptartikel</option>
                    <option value="zubehoer" ${data.type === 'zubehoer' ? 'selected' : ''}>Zubehör</option>
                  </select>
                </div>

                <div class="flex flex-col gap-1.5">
                  <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-1">
                    <i class="fas fa-circle-info w-3 h-3"></i> Status
                  </span>

                  <div class="flex items-center gap-2 flex-wrap">
                    <span class="mat-chip gap-2 ${data.isStammartikel ? 'text-[#78b2ce] border-[#78b2ce]/20 bg-[#78b2ce]/10' : 'text-slate-400'}">
                      <i class="fas fa-database"></i>
                      <span>${data.isStammartikel ? 'Stammartikel aktiv' : 'Stammartikel aus'}</span>
                    </span>

                    <span class="mat-chip gap-2 ${data.isFavorite ? 'text-amber-500 border-amber-200 bg-amber-50' : 'text-slate-400'}">
                      <i class="${data.isFavorite ? 'fas' : 'far'} fa-star"></i>
                      <span>${data.isFavorite ? 'Favorit aktiv' : 'Favorit aus'}</span>
                    </span>
                  </div>
                </div>

                <div class="ml-auto flex items-end">
                  <button
                    type="button"
                    onclick="ui.handleUpdateMaterial('${mainIdStr}', ${subIdStr}, 'isEditingProps', false)"
                    class="mat-btn px-4 !min-w-[110px] !bg-[#78b2ce] !text-white !border-[#78b2ce] hover:!bg-[#639ab5]"
                  >
                    Fertig
                  </button>
                </div>
              </div>
            `
            : ''
        }
      </div>
    `;
  };

  const renderHeader = () => {
    const cols = visibleCols.map(col => {
      const alignClass =
        col.align === 'right'
          ? 'text-right justify-end'
          : col.align === 'center'
            ? 'text-center justify-center'
            : 'text-left justify-start';

      return `
        <div class="mat-head-cell ${alignClass}">
          <span>${esc(col.label)}</span>
        </div>
      `;
    }).join('');

    return `
      <div class="material-sticky-head">
        <div
          class="mat-head-row"
          style="display:grid; grid-template-columns:${gridTemplate};"
        >
          ${cols}
        </div>
      </div>
    `;
  };

  let bodyHtml = '';

  if (!comps.length) {
    bodyHtml = `
      <div style="padding:4rem; text-align:center; color:#cbd5e1; font-weight:700; font-style:italic;">
        Noch keine Artikel hinzugefügt
      </div>
    `;
  } else {
    comps.forEach((c, idx) => {
      c.pos = idx + 1;

      bodyHtml += `<div class="group-row material-main-row-wrap" data-main-index="${idx}">`;
      bodyHtml += renderRow(c, { isSubItem: false, parentId: null, mIdx: idx, sIdx: null });

      if (c.isExpanded && Array.isArray(c.subComponents) && c.subComponents.length > 0) {
        bodyHtml += `<div class="flex flex-col border-t border-gray-100 bg-slate-50/30" data-parent-index="${idx}" id="sub-list-${idx}">`;

        c.subComponents.forEach((sub, sIdx) => {
          sub.pos = `${c.pos}.${sIdx + 1}`;
          bodyHtml += renderRow(sub, { isSubItem: true, parentId: c.id, mIdx: idx, sIdx });
        });

        bodyHtml += `</div>`;
      }

      if (!isLocked) {
        bodyHtml += `
          <div class="py-2.5 bg-white flex justify-center border-t border-gray-100">
            <button onclick="ui.openCatalog('sub', ${idx})" class="text-[11px] font-bold text-slate-400 hover:text-[#78b2ce] uppercase flex items-center gap-1 transition-colors">
              <i class="fas fa-plus w-3 h-3"></i> Unter-Artikel verknüpfen
            </button>
          </div>
        `;
      }

      bodyHtml += `</div>`;
    });
  }

  const bottomAddButton = !isLocked ? `
    <div style="padding:1rem 1rem 1.25rem; border-top:1px solid #eef2f7; background:#fff; display:flex; justify-content:center;">
      <button
        type="button"
        onclick="ui.openCatalog('main')"
        class="flex items-center gap-2 px-6 py-3 bg-[#78b2ce] text-white text-sm font-black rounded-full hover:bg-[#639ab5] shadow-md tracking-wide transition-all"
      >
        <i class="fas fa-plus"></i> ARTIKEL HINZUFÜGEN
      </button>
    </div>
  ` : '';

  list.innerHTML = `
    <style>
      .material-table-shell{
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:2rem;
        overflow:hidden;
        box-shadow:var(--shadow-sm);
      }

      .material-grid-scroll{
        position:relative;
        overflow:auto;
        max-height:72vh;
        background:#fff;
      }

      .material-sticky-head{
        position:sticky;
        top:0;
        z-index:40;
        background:rgba(255,255,255,.96);
        backdrop-filter:blur(10px);
        border-bottom:1px solid #e5e7eb;
        box-shadow:0 4px 14px rgba(15,23,42,.06);
      }

      .mat-head-row{
        min-width:max-content;
      }

      .mat-head-cell{
        display:flex;
        align-items:center;
        min-height:58px;
        padding:0 14px;
        font-size:11px;
        font-weight:900;
        text-transform:uppercase;
        letter-spacing:.08em;
        color:#94a3b8;
        background:rgba(248,250,252,.95);
        border-right:1px solid #eef2f7;
        text-align:center;
      }

      .mat-head-cell:last-child{
        border-right:none;
      }

      .mat-data-row{
        position:relative;
        background:#fff;
        border-bottom:1px solid #eef2f7;
      }

      .mat-data-row.is-sub-row{
        background:#f8fafc;
        border-left:6px solid #78b2ce;
      }

      .mat-data-grid{
        min-width:max-content;
        align-items:stretch;
      }

      .mat-grid-cell{
        padding:12px 14px;
        border-right:1px solid #f1f5f9;
        display:flex;
        align-items:center;
        min-height:84px;
      }

      .mat-grid-cell:last-child{
        border-right:none;
      }

      .mat-grid-cell-center{
        justify-content:center;
        text-align:center;
      }

      .mat-grid-cell-right{
        justify-content:flex-end;
        text-align:right;
      }

      .material-main-row-wrap:hover > .mat-data-row.is-main-row{
        background:#fcfdff;
      }

      .mat-total-row{
        position:sticky;
        bottom:0;
        z-index:35;
        background:rgba(255,255,255,.98);
        backdrop-filter:blur(10px);
        border-top:2px solid #dbe5ef;
        box-shadow:0 -8px 20px rgba(15,23,42,.06);
      }

      .mat-total-row .mat-data-grid{
        background:linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
      }

      .mat-total-cell{
        min-height:72px;
        background:transparent;
      }

      .mat-toggle-icon{
        display:inline-flex;
        align-items:center;
        gap:6px;
        border:1px solid #e2e8f0;
        background:#fff;
        color:#64748b;
        font-size:11px;
        font-weight:800;
        border-radius:999px;
        padding:6px 10px;
        transition:.2s ease;
      }

      .mat-toggle-icon:hover{
        border-color:#78b2ce;
        color:#78b2ce;
        background:#f8fbfe;
      }

      .mat-toggle-icon.active.favorite{
        color:#f59e0b;
        border-color:#fcd34d;
        background:#fffbeb;
      }

      .mat-toggle-icon.active.stamm{
        color:#78b2ce;
        border-color:rgba(120,178,206,.3);
        background:rgba(120,178,206,.08);
      }
    </style>

    <div class="material-table-shell">
      ${toolbarHtml}

      <div class="material-grid-scroll material-scroll-wrap" id="material-scroll-wrap">
        <div class="material-scroll-zone top" id="material-scroll-zone-top">
          <div class="scroll-chip"><i class="fas fa-chevron-up"></i></div>
        </div>

        <div class="material-grid-inner" id="material-grid-inner">
          ${renderHeader()}

          <div class="flex flex-col min-w-max" id="material-main-body">
            ${bodyHtml}
            ${renderTotalsRow()}
          </div>
        </div>

        <div class="material-scroll-zone bottom" id="material-scroll-zone-bottom">
          <div class="scroll-chip"><i class="fas fa-chevron-down"></i></div>
        </div>
      </div>

      ${bottomAddButton}
    </div>
  `;

  if (state.materialSearch) {
    this.filterMaterialTable(state.materialSearch);
  }

  this.initMaterialSortables();
},
  // Moved sortable logic to a sub-function to keep render clean
  
  
  initMaterialSortables() {
    if (typeof Sortable === 'undefined') {
      console.warn('SortableJS not loaded');
      return;
    }

    if (!this._matSortables) this._matSortables = { main: null, subs: {} };

    try {
      if (this._matSortables.main) this._matSortables.main.destroy();
    } catch (e) {}

    Object.keys(this._matSortables.subs || {}).forEach(k => {
      try { this._matSortables.subs[k]?.destroy(); } catch (e) {}
    });
    this._matSortables.subs = {};

    const mainBody = document.getElementById('material-main-body');
    const scrollWrap = document.getElementById('material-scroll-wrap');
    const zoneTop = document.getElementById('material-scroll-zone-top');
    const zoneBottom = document.getElementById('material-scroll-zone-bottom');

    if (!mainBody || !scrollWrap) return;

    let autoScrollRAF = null;
    let autoScrollDir = 0;

    const stopAutoScroll = () => {
      autoScrollDir = 0;
      if (autoScrollRAF) {
        cancelAnimationFrame(autoScrollRAF);
        autoScrollRAF = null;
      }
      zoneTop?.classList.remove('active');
      zoneBottom?.classList.remove('active');
    };

    const runAutoScroll = () => {
      if (!autoScrollDir) {
        autoScrollRAF = null;
        return;
      }

      scrollWrap.scrollTop += autoScrollDir * 14;
      autoScrollRAF = requestAnimationFrame(runAutoScroll);
    };

    const startAutoScroll = (dir) => {
      if (autoScrollDir === dir && autoScrollRAF) return;
      autoScrollDir = dir;

      zoneTop?.classList.toggle('active', dir < 0);
      zoneBottom?.classList.toggle('active', dir > 0);

      if (!autoScrollRAF) {
        autoScrollRAF = requestAnimationFrame(runAutoScroll);
      }
    };

    const handlePointerNearEdge = (evt) => {
      if (!evt || !scrollWrap) return;

      const rect = scrollWrap.getBoundingClientRect();
      const y = evt.clientY;
      const edgeSize = 70;

      if (y <= rect.top + edgeSize) {
        startAutoScroll(-1);
      } else if (y >= rect.bottom - edgeSize) {
        startAutoScroll(1);
      } else {
        stopAutoScroll();
      }
    };

    this._matSortables.main = new Sortable(mainBody, {
        animation: 150,
        handle: '.handle',
        draggable: '.group-row',
        ghostClass: 'drag-ghost',
        chosenClass: 'drag-chosen',
        dragClass: 'drag-dragging',
        forceFallback: true,
        fallbackOnBody: true,
        swapThreshold: 0.65,
        scroll: true,
        scrollSensitivity: 80,
        scrollSpeed: 18,
        bubbleScroll: true,
        scrollFn: function(offsetX, offsetY, originalEvent) {
          handlePointerNearEdge(originalEvent);
        },
        filter: 'input, textarea, select, button, a',
        preventOnFilter: false,

        onStart: () => {
          zoneTop?.classList.add('active');
          zoneBottom?.classList.add('active');
        },

        onMove: (evt, originalEvent) => {
          handlePointerNearEdge(originalEvent);
          return true;
        },

        onEnd: (evt) => {
          stopAutoScroll();

          const oldIndex = evt.oldDraggableIndex;
          const newIndex = evt.newDraggableIndex;

          if (oldIndex == null || newIndex == null || oldIndex === newIndex) return;

          const comps = Array.isArray(state.editingSet?.components)
            ? state.editingSet.components
            : [];

          if (oldIndex < 0 || newIndex < 0 || oldIndex >= comps.length || newIndex >= comps.length) {
            console.warn('Invalid drag indexes', {
              oldIndex,
              newIndex,
              length: comps.length,
              evt
            });
            return;
          }

          const moved = comps.splice(oldIndex, 1)[0];
          if (!moved) {
            console.warn('Moved component is undefined', { oldIndex, newIndex, evt });
            return;
        }

          comps.splice(newIndex, 0, moved);

          comps.forEach((c, i) => {
            if (c) c.pos = i + 1;
          });

          ui.renderComponentItems();
          app.autoSave();
        }
      });
    const comps = Array.isArray(state.editingSet?.components) ? state.editingSet.components : [];
    comps.forEach((c, mIdx) => {
      const subWrap = document.getElementById(`sub-list-${mIdx}`);
      if (!subWrap) return;

      this._matSortables.subs[mIdx] = new Sortable(subWrap, {
        animation: 150,
        handle: '.sub-handle',
        draggable: ':scope > .material-row-sub',
        ghostClass: 'drag-ghost',
        chosenClass: 'drag-chosen',
        dragClass: 'drag-dragging',
        forceFallback: true,
        fallbackOnBody: true,
        swapThreshold: 0.65,
        scroll: true,
        scrollSensitivity: 80,
        scrollSpeed: 18,
        bubbleScroll: true,
        scrollFn: function(offsetX, offsetY, originalEvent) {
          handlePointerNearEdge(originalEvent);
        },
        filter: 'input, textarea, select, button, a',
        preventOnFilter: false,

        onStart: () => {
          zoneTop?.classList.add('active');
          zoneBottom?.classList.add('active');
        },

        onMove: (evt, originalEvent) => {
          handlePointerNearEdge(originalEvent);
          return true;
        },

        onEnd: (evt) => {
          stopAutoScroll();

          const oldIndex = evt.oldIndex;
          const newIndex = evt.newIndex;
          if (oldIndex == null || newIndex == null || oldIndex === newIndex) return;

          if (!Array.isArray(c.subComponents)) c.subComponents = [];
          const moved = c.subComponents.splice(oldIndex, 1)[0];
          c.subComponents.splice(newIndex, 0, moved);

          ui.renderComponentItems();
          app.autoSave();
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

    openTaskPicker: async function() { 
      // 1. Set Context
      if (!ui.ensureSetHasName('Aufgaben hinzufügen')) return;
      state.pickerContext = { type: 'task' };

      const modal = document.getElementById('modal-container');
      const titleEl = document.getElementById('modal-title');
      const searchBox = document.getElementById('modal-search-box');
      const contentEl = document.getElementById('modal-content');

      
      // 2. Set Modal Title
      titleEl.innerHTML = `<i class="fas fa-list-check" style="color:var(--primary)"></i> <span>Aufgaben wählen:</span>`;
      
      // 3. Define Section Options
      const sections = [
          {v:'', l:'Alle Sektionen'},
          {v:'plan', l:'Planung'},
          {v:'montage', l:'Montage'},
          {v:'product', l:'Verkauf'},
          {v:'repair', l:'Reparatur'},
          {v:'maintenance', l:'Wartung'},
          {v:'complete', l:'Komplett'}
      ];
      
      const sectionOptions = sections.map(s => 
          `<option value="${s.v}" ${state.taskSectionFilter === s.v ? 'selected' : ''}>${s.l}</option>`
      ).join('');

      // 4. Inject Search Bar with LABELS (Fixed Layout)
      searchBox.innerHTML = `
        <div style="padding: 1rem; display: flex; flex-direction: column; gap: 0.75rem;">
            
            <div style="display:flex; gap:1rem; align-items: flex-end;">
                <div style="display:flex; flex-direction:column; gap:4px;">
                    <label style="font-size:0.7rem; font-weight:800; color:#94a3b8; text-transform:uppercase; margin:0;">Deinstleistung</label>
                    <select id="modal-task-section" class="search-input" style="width:160px; padding:0.5rem; font-size:0.85rem;">
                        ${sectionOptions}
                    </select>
                </div>

                <div style="display:flex; flex-direction:column; gap:4px;">
                    <label style="font-size:0.7rem; font-weight:800; color:#94a3b8; text-transform:uppercase; margin:0;">Phase</label>
                    <select id="modal-task-stage" class="search-input" style="width:160px; padding:0.5rem; font-size:0.85rem;">
                        <option value="">Alle Stages</option>
                        <option disabled>Lade Daten...</option>
                    </select>
                </div>

                <div style="flex:1; display:flex; flex-direction:column; gap:4px;">
                    <label style="font-size:0.7rem; font-weight:800; color:#94a3b8; text-transform:uppercase; margin:0;">Suche</label>
                    <div class="search-wrapper" style="width:100%;">
                        <i class="fas fa-search search-icon"></i>
                        <input id="modal-task-search" type="text" class="search-input" style="width:100%" 
                               placeholder="Phase oder Aufgabe suchen..." 
                               value="${this.escapeHtml(state.taskSearch || '')}">
                    </div>
                </div>
            </div>
        </div>
      `;
      searchBox.classList.remove('hidden');

      // 5. Logic to fetch ALL Stages for the dropdown (Independent of filtering)
      const populateStages = async () => {
          const sel = document.getElementById('modal-task-stage');
          if(!sel) return;
          
          const groupId = state.selectedGroup?.id;
          if(!groupId) return;

          try {
             // Request options with NO filters to get the full list of stages
             // Note: using /tasks/options endpoint but omitting 'stage_id' and 'section'
             const url = `/tasks/options?article_group_id=${groupId}`; 
             
             // Use api.request if available to handle auth headers automatically
             const res = await api.request(url); 
             
             if(res && res.data) {
                 let html = '<option value="">Alle Stages</option>';
                 res.data.forEach(st => {
                     const isSel = String(st.id) === String(state.selectedStageId) ? 'selected' : '';
                     html += `<option value="${st.id}" ${isSel}>${this.escapeHtml(st.stage || st.name)}</option>`;
                 });
                 sel.innerHTML = html;
             }
          } catch(e) {
              console.error("Failed to load stages for dropdown", e);
              sel.innerHTML = '<option value="">Fehler beim Laden</option>';
          }
      };

      // 6. Bind Events
      const sectionInput = document.getElementById('modal-task-section');
      const stageInput = document.getElementById('modal-task-stage');
      const textInput = document.getElementById('modal-task-search');

      const triggerLoad = () => {
          state.taskSectionFilter = sectionInput.value;
          state.selectedStageId = stageInput.value;
          state.taskSearch = textInput.value;
          
          // Load specific results based on filter (This updates the list below)
          api.loadTaskOptions(state.taskSearch); 
      };

      sectionInput.onchange = triggerLoad;
      stageInput.onchange = triggerLoad;
      textInput.oninput = triggerLoad;

      // 7. Execute
      populateStages(); // Load dropdown data
      contentEl.innerHTML = this.getTaskOptionsHTML({ mode: 'modal' }); // Render current list

      modal.classList.remove('hidden');
      
      setTimeout(() => textInput.focus(), 50);
    },
    renderTasksTab() {
      const mount = $('#tasks-tab-mount');

      ui.syncSummaryWithTaskLabor();
      if (!mount) return;

      ensureSelectedStage();

      // Two-column layout: options (left) + selected (right)
      mount.innerHTML = `
        <div style="display:grid; grid-template-columns: 1fr 1.9fr; gap:2px;">
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

          clearTimeout(ui._taskSearchTimer);
          ui._taskSearchTimer = setTimeout(() => {
            api.loadTaskOptions(state.taskSearch);
          }, 250);
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

                    // --- NEW: Prepare Bulk Import Payload ---
                    const phasePayload = {
                        stage_id: st.id,
                        stage_name: stName,
                        task_phase_id: ph.id,
                        phase_name: phName,
                        // We map activities to a minimal structure to save space
                        activities: acts.map(a => ({
                            phase_activity_id: a.id ?? a.phase_activity_id,
                            title: a.title || a.name,
                            description: a.description,
                            duration: a.duration,
                            duration_type: a.duration_type,
                            notes: a.notes,
                            priority: a.priority,
                            percent: a.percent,
                            hours: a.hours ?? a.default_hours ?? 0
                        }))
                    };
                    const phaseEncoded = b64EncodeJson(phasePayload);
                    // ----------------------------------------

                    return `
                    <div style="border-top:1px solid #f1f5f9; padding-top:0.75rem; margin-top:0.75rem;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                        <div style="font-size:0.75rem; font-weight:900; color:#64748b;">
                            ${escapeHtml(phName)}
                        </div>
                        
                        <div style="display:flex; align-items:center; gap: 8px;">
                            <div style="font-size:10px; font-weight:900; color:#94a3b8; text-transform:uppercase;">
                                ${acts.length} Activities
                            </div>
                            <button class="btn-icon-small" 
                                    style="width:auto; padding: 2px 8px; gap:4px; border-color:var(--primary); color:var(--primary);"
                                    title="Alle ${acts.length} Activities importieren"
                                    onclick="ui.addPhaseFromEncoded('${phaseEncoded}')">
                                <i class="fas fa-plus-circle"></i> Alle
                            </button>
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

  // Empty state
  if (!tasks.length) {
    return `
      <div style="padding:3rem; text-align:center; border:2px dashed var(--border-color); border-radius:var(--radius-2xl); color:#cbd5e1; font-weight:900;">
        Noch keine Aufgaben zugewiesen
      </div>
    `;
  }

  // Helpers
  const stageLabel = (stageKey) => {
    if (stageKey === 'null') return 'Ohne Stage';
    const stage = findStageById(stageKey);
    const fallback = tasks.find(x => String(x.stage_id) === String(stageKey))?.stage_name;
    return stage?.name || stage?.title || fallback || ('Stage #' + stageKey);
  };

  const clampPercent = (v) => {
    const n = parseFloat(v);
    if (!Number.isFinite(n)) return 0;
    return Math.max(0, Math.min(100, n));
  };

  const num = (v, d = 0) => toNum(v, d);

  const calcLaborRow = (tl) => {
    const rate = parseFloat(tl?.rate) || 0;
    const hours = parseFloat(tl?.hours) || 0;
    const baseEK = rate * hours;

    const gk = baseEK * (num(state.globalGemeinkosten, 0) / 100);
    const wagnis = baseEK * (num(state.globalWagnis, 0) / 100);
    const profit = baseEK * (num(state.globalPersMargin, 0) / 100);

    return { rate, hours, baseEK, gk, wagnis, profit, totalVK: baseEK + gk + wagnis + profit };
  };

  const calcLaborSummary = (taskLabor) => {
    let sumHours = 0;
    let sumVK = 0;

    taskLabor.forEach(tl => {
      const r = calcLaborRow(tl);
      sumHours += r.hours;
      sumVK += r.totalVK;
    });

    return { sumHours, sumVK };
  };

const renderLaborAccordion = (t, taskIdx) => {
  const taskLabor = Array.isArray(t?.task_labor) ? t.task_labor : [];
  if (!taskLabor.length) return '';
  const locked = !!state.isLocked;

  // totals from rows
  let sumEK = 0;
  let sumVK = 0;
  let sumHours = 0;

  // ✅ inline SVG lock icon
  const lockSvg = `
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
      width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"
      stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <rect x="5" y="11" width="14" height="10" rx="2"></rect>
      <path d="M8 11V8a4 4 0 0 1 8 0v3"></path>
    </svg>
  `;

  taskLabor.forEach((tl) => {
    ui.ensureTaskLaborFields(tl);

    const r = ui.calcTaskLaborRow(tl);

    // EK total is always computed unless override is provided
    const ekTotal = (tl.ek_total_override != null)
      ? Math.max(0, toNum(tl.ek_total_override, 0))
      : r.baseEK;

    const c = ui.getEffectiveCosting();
    const gkPct = toNum(c.gk, 0) / 100;
    const wagnisPct = toNum(c.wagnis, 0) / 100;
    const profitPct = toNum(c.profitPers, 0) / 100;
    const provPct = toNum(c.provision, 0) / 100;

    const gk = ekTotal * gkPct;
    const wagnis = ekTotal * wagnisPct;
    const profit = ekTotal * profitPct;

    let vk = ekTotal + gk + wagnis + profit;
    vk += (vk * provPct);

    sumEK += ekTotal;
    sumVK += vk;
    sumHours += r.hours;
  });

  const expanded = !!t.isLaborExpanded;

  const infoHtml = `
    <div style="font-weight:900; margin-bottom:.5rem;">Wie wird gerechnet?</div>
    <div style="font-size:.85rem; font-weight:700; color:#475569;">
      <div style="margin-bottom:.5rem;">
        <strong>EK Gesamt</strong> = Menge × Einzelpreis (Einzelpreis aus Qualifikation / editierbar)
      </div>
      <div style="margin-bottom:.5rem;">
        <strong>VK Gesamt</strong> = EK + GK(${toNum(state.globalGemeinkosten,0)}%) + Wagnis(${toNum(state.globalWagnis,0)}%) + Gewinn(${toNum(state.globalPersMargin,0)}%)
        ${toNum(state.provisionPercent,0) ? ` + Provision(${toNum(state.provisionPercent,0)}%)` : ''}
      </div>
    </div>
  `;

  return `
    <div style="border:1px solid #e2e8f0; border-radius:10px; overflow:hidden;">

      <!-- header -->
      <div style="padding:8px 12px; background:#f8fafc; display:flex; justify-content:space-between; align-items:center; gap:12px;">
        <div style="display:flex; align-items:center; gap:10px; min-width:0;">
          <div style="width:24px; height:24px; background:rgba(116,178,212,0.15); color:var(--primary); border-radius:6px; display:flex; align-items:center; justify-content:center;">
            <i class="fas fa-users" style="font-size:10px;"></i>
          </div>

          <div style="min-width:0;">
            <div style="display:flex; align-items:center; gap:8px;">
              <span style="font-weight:900; font-size:0.85rem;">${taskLabor.length} Personal</span>
              <span style="font-size:0.75rem; font-weight:800; color:#94a3b8;">(${sumHours.toFixed(2)}h)</span>

              <button
                class="btn-icon-small"
                style="width:22px;height:22px;border-radius:999px;"
                title="Info"
                data-title="Personal Kalkulation"
                data-html='${ui.escapeHtml(infoHtml).replaceAll("&#x27;", "&apos;")}'
                onclick="event.stopPropagation(); ui.openInfoModal(this.dataset.title, this.dataset.html);"
              >
                <i class="fas fa-info" style="font-size:10px;"></i>
              </button>
            </div>
          </div>
        </div>

        <div style="display:flex; align-items:center; gap:12px;">
          <div style="text-align:right;">
            <div style="font-size:9px; font-weight:900; color:#94a3b8; text-transform:uppercase;">VK</div>
            <div style="font-weight:900; color:var(--text-main);">${ui.formatMoney(sumVK)}</div>
          </div>

          <button class="btn-icon-small" onclick="event.stopPropagation(); ui.toggleTaskLabor(${taskIdx})" title="${expanded ? 'Einklappen' : 'Aufklappen'}" ${locked ? 'disabled style="opacity:.5;cursor:not-allowed;"' : ''}>
            <i class="fas ${expanded ? 'fa-chevron-up' : 'fa-chevron-down'}"></i>
          </button>
        </div>
      </div>

      <!-- body -->
      <div style="display:${expanded ? 'block' : 'none'}; border-top:1px solid #e2e8f0; background:#fff; overflow-x:auto;">
        <div style="min-width:760px;">

          <!-- header row -->
          <div style="display:flex; padding:6px 12px; background:#f1f5f9; font-size:9px; font-weight:900; color:#94a3b8; text-transform:uppercase;">
            <div style="flex:1;">Qualifikation</div>
            <div style="width:90px; text-align:left;">Einheit</div>
            <div style="width:90px; text-align:left;">Menge</div>
            <div style="width:110px; text-align:left;">Einzelpreis</div>
            <div style="width:110px; text-align:left;">EK Gesamt</div>
            <div style="width:110px; text-align:left;">VK Gesamt</div>
            <div style="width:60px;"></div>
          </div>

          ${taskLabor.map((tl, laborIdx) => {
            ui.ensureTaskLaborFields(tl);

            const r = ui.calcTaskLaborRow(tl);

            // ✅ locking applies only to unit rate (Einzelpreis)
            const isRateLocked = !!tl.unit_rate_locked; // rename if you have another field

            // ✅ EK total is calculated (unless override) and MUST NOT be typed directly
            const ekTotal = (tl.ek_total_override != null)
              ? Math.max(0, toNum(tl.ek_total_override, 0))
              : r.baseEK;

            const gkPct = toNum(state.globalGemeinkosten, 0) / 100;
            const wagnisPct = toNum(state.globalWagnis, 0) / 100;
            const profitPct = toNum(state.globalPersMargin, 0) / 100;
            const provPct = toNum(state.provisionPercent, 0) / 100;

            const gk = ekTotal * gkPct;
            const wagnis = ekTotal * wagnisPct;
            const profit = ekTotal * profitPct;

            let vk = ekTotal + gk + wagnis + profit;
            vk += (vk * provPct);

            // selected unit price
            const unitRate = r.unitRate;

            const lockBtn = `
              <button
                class="btn-icon-small"
                style="
                  width:22px;height:22px;border-radius:999px;
                  display:inline-flex;align-items:center;justify-content:center;
                  ${isRateLocked ? 'background:rgba(148,163,184,.18);' : 'background:transparent;'}
                "
                title="${isRateLocked ? 'Einzelpreis gesperrt' : 'Einzelpreis freigeben'}"
                onclick="event.stopPropagation(); ui.toggleTaskLaborUnitRateLock(${taskIdx}, ${laborIdx})"
                      ${locked ? 'disabled style="opacity:.5;cursor:not-allowed;"' : ''}
              >
                <span style="display:inline-flex; line-height:0; color:${isRateLocked ? '#64748b' : '#cbd5e1'};">
                  ${lockSvg}
                </span>
              </button>
            `;

            return `
              <div style="display:flex; align-items:center; padding:8px 12px; border-bottom:1px solid #f1f5f9; gap:12px;">
                <div style="flex:1; min-width:180px; font-weight:900; color:var(--text-main);">
                  ${ui.escapeHtml(tl.name || '—')}
                  <div style="font-size:10px; font-weight:800; color:#94a3b8; margin-top:2px;">
                    ${(() => {
                      const c = ui.getEffectiveCosting();
                      return `GK ${toNum(c.gk,0)}% · Wagnis ${toNum(c.wagnis,0)}% · Gewinn ${toNum(c.profitPers,0)}%` +
                            (toNum(c.provision,0) ? ` · Prov ${toNum(c.provision,0)}%` : '');
                    })()}
                  </div>
                </div>

                <!-- Einheit -->
                <div style="width:90px; text-align:center;" onclick="event.stopPropagation()">
                  <select
                      class="form-control"
                      style="padding:.35rem .5rem; border-radius:10px; font-weight:900; font-size:12px;"
                      onchange="ui.updateTaskLaborUnit(${taskIdx}, ${laborIdx}, this.value)"
                      ${locked ? 'disabled' : ''}
                    >
                    <option value="h" ${String(tl.unit).toLowerCase()==='h'?'selected':''}>h</option>
                    <option value="min" ${String(tl.unit).toLowerCase()==='min'?'selected':''}>min</option>
                    <option value="woche" ${String(tl.unit).toLowerCase()==='woche'?'selected':''}>Woche</option>
                  </select>
                </div>

                <!-- Menge -->
                <div style="width:90px; text-align:right;" onclick="event.stopPropagation()">
                  <input
                    type="number"
                    step="0.25"
                    class="form-control"
                    style="padding:.35rem .5rem; border-radius:10px; font-weight:900; font-size:12px; text-align:right;"
                    value="${toNum(tl.qty,0)}"
                    onchange="ui.updateTaskLaborQty(${taskIdx}, ${laborIdx}, this.value)"
                    ${locked ? 'readonly' : ''}
                  />
                </div>

                <!-- Einzelpreis (editable unless locked) -->
                <div style="width:110px; text-align:right; display:flex; gap:6px; align-items:center;" onclick="event.stopPropagation()">
                  <input
                    type="number"
                    step="0.01"
                    class="form-control"
                    style="padding:.35rem .5rem; border-radius:10px; font-weight:900; font-size:12px; text-align:right;"
                    value="${toNum(unitRate,0).toFixed(2)}"
                    onchange="ui.updateTaskLaborUnitRate(${taskIdx}, ${laborIdx}, this.value)"
                    ${locked || isRateLocked ? 'disabled' : ''}
                  />
                  ${lockBtn}
                </div>

                <!-- EK Gesamt (calculated => disabled) -->
                <div style="width:110px; text-align:right;" onclick="event.stopPropagation()">
                  <input
                    type="number"
                    step="0.01"
                    class="form-control"
                    style="padding:.35rem .5rem; border-radius:10px; font-weight:900; font-size:12px; text-align:right; background:#f8fafc;"
                    value="${ekTotal.toFixed(2)}"
                    disabled
                  />
                </div>

                <!-- VK Gesamt (calculated => disabled) -->
                <div style="width:110px; text-align:right;" onclick="event.stopPropagation()">
                  <input
                    type="text"
                    class="form-control"
                    style="padding:.35rem .5rem; border-radius:10px; font-weight:900; font-size:12px; text-align:right; background:#f8fafc;"
                    value="${ui.formatMoney(vk)}"
                    disabled
                  />
                </div>

                <!-- trash -->
                <div style="width:36px; text-align:right;" onclick="event.stopPropagation()">
                  <button
                      class="btn-icon-small"
                      style="color:var(--danger); border-color:transparent;"
                      onclick="ui.removeTaskLabor(${taskIdx}, ${laborIdx})"
                      title="Entfernen"
                      ${locked ? 'disabled style="opacity:.5;cursor:not-allowed;"' : ''}
                    >
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>
            `;
          }).join('')}

        </div>
      </div>
    </div>
  `;
};
  const renderTaskRow = (t, idx) => {
    const laborHtml = renderLaborAccordion(t, idx);

    return `
      <div class="catalog-item task-row" data-task-index="${idx}" style="margin:0; border-radius:var(--radius-xl); display:flex; flex-direction:column; gap:1rem;">
        <div style="display:flex; justify-content:space-between; gap:1rem; align-items:flex-start;">

          <div style="display:flex; gap:0.75rem; align-items:flex-start; flex:1; min-width:0;">
            <div class="handle task-handle" title="Sortieren" style="margin-top:2px; flex-shrink:0;">
              <i class="fas fa-grip-vertical"></i>
            </div>

            <div style="flex:1; min-width:0;">
              <div style="font-weight:900; color:var(--text-main); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                ${escapeHtml(t?.title || 'Untitled')}
              </div>

              <div style="font-size:0.75rem; font-weight:700; color:#94a3b8; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                ${escapeHtml(t?.phase_name || '—')}
              </div>

              ${t?.description
                ? `<div style="font-size:0.75rem; font-weight:700; color:#64748b; margin-top:0.25rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${escapeHtml(t.description)}</div>`
                : ''
              }

              <div style="margin-top:8px;">
                  <button
                      class="btn-icon-small"
                      style="width:auto; padding:2px 8px; height:24px; gap:4px; border:1px solid #cbd5e1; background:#f8fafc;"
                      onclick="ui.promptTaskQualification(${idx})"
                      ${state.isLocked ? 'disabled style="width:auto; padding:2px 8px; height:24px; gap:4px; border:1px solid #cbd5e1; background:#f8fafc; opacity:.5; cursor:not-allowed;"' : ''}
                    >
                  <i class="fas fa-plus" style="font-size:0.6rem;"></i>
                  Personal
                </button>
              </div>
            </div>
          </div>

          <div style="display:flex; gap:0.75rem; align-items:flex-start; flex-shrink:0;">
            <select
              onchange="ui.changeTaskStage(${idx}, this.value)"
              class="search-input" ${state.isLocked ? 'disabled' : ''}
              style="width:130px; padding-left:0.75rem; padding-right:1.5rem;"
            >
              <option value="">Ohne Stage</option>
              ${(state.taskOptions || []).map(st => `
                <option value="${escapeHtml(st.id)}" ${String(st.id) === String(t?.stage_id) ? 'selected' : ''}>
                  ${escapeHtml(st.stage || st.name)}
                </option>
              `).join('')}
            </select>

            <div class="qty-control" style="display:inline-flex;">
              <input
                type="number"
                min="0"
                step="0.25"
                onchange="ui.updateTaskHours(${idx}, this.value)"
                class="qty-input ${state.isLocked ? 'disabled' : ''}"
                style="width:45px;"
                value="${escapeHtml(t?.hours ?? 0)}"
              />
              <span style="font-size:10px; font-weight:900; color:#cbd5e1; padding-right:8px;">h</span>
            </div>

            <button onclick="ui.removeTask(${idx})" class="btn-danger ${state.isLocked ? 'disabled' : ''}" title="Entfernen">
              <i class="fas fa-trash-alt"></i>
            </button>
          </div>
        </div>

        ${laborHtml ? `<div>${laborHtml}</div>` : ''}

      </div>
    `;
  };

  // Group by stage_id
  const byStage = new Map();
  tasks.forEach((t, idx) => {
    const key = String(t?.stage_id ?? 'null');
    if (!byStage.has(key)) byStage.set(key, []);
    byStage.get(key).push({ t, idx });
  });

  // Stage order
  const stageOrder = Array.from(byStage.keys()).sort((a, b) => a.localeCompare(b));

  // Render groups
  return stageOrder.map(stageKey => {
    const group = (byStage.get(stageKey) || [])
      .slice()
      .sort((a, b) => (num(a?.t?.sort_order, 0) - num(b?.t?.sort_order, 0)));

    return `
      <div style="border:1px solid #f1f5f9; border-radius:var(--radius-xl); padding:1rem; margin-bottom:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
          <div style="font-weight:900; color:var(--text-main); display:flex; align-items:center; gap:0.5rem;">
            <span style="width:10px; height:10px; border-radius:999px; background:var(--accent); display:inline-block;"></span>
            ${escapeHtml(stageLabel(stageKey))}
          </div>

          <div style="font-size:10px; font-weight:900; color:#94a3b8; text-transform:uppercase;">
            ${group.length} Aufgaben
          </div>
        </div>

        <div class="tasks-stage-list" data-stage="${escapeHtml(stageKey)}" style="display:flex; flex-direction:column; gap:0.5rem;">
          ${group.map(({ t, idx }) => renderTaskRow(t, idx)).join('')}
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
      
      // ✅ Trigger labor sync to drop labor from deleted tasks
      this.syncGlobalLaborFromTasks(); 
      
      ui.showStatus('ENTFERNT');
      ui.renderTasksTab();
    },
 
    // ------------------------------
    // Catalog + Labor modal actions
    // ------------------------------
    openCatalog(type, mainIdx = null) {
  if (!ui.ensureSetHasName(type === 'main' ? 'einen Artikel hinzufügen' : 'einen Unter-Artikel verknüpfen')) {
    return;
  }

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
          
          // ✅ Get Measure from backend (defaults to Stk)
          const measure = p.measure || 'Stk'; 
          const pe = p.price_unit || 1;

          const supplierHtml = suppliers.length
            ? suppliers.map(s => {
                const dName = s.distributor_name || s.name || '—';
                const dPrice = toNum(s.distributor_price ?? s.unit_price, 0);

                // ✅ Pass measure/pe to the add function payload
               const payload = b64EncodeJson({
                  product_id: p.id,
                  product_name: p.product_name || p.name || '',
                  article_no: p.article_no || p.articleNumber || p.manufacturer_no || p.hersteller_nr || '',
                  distributor_price_id: s.distributor_price_id,
                  distributor_id: s.distributor_id,
                  distributor_name: dName,
                  distributor_article_no: s.article_no || s.distributor_article_no || s.distributor_nr || '',
                  unit_price: dPrice,
                  measure: measure,
                  price_unit: pe
                });

                // Label logic: "per 100 m" or "per m"
                const unitLabel = pe > 1 ? `${pe} ${measure}` : measure;

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
                      per ${escapeHtml(unitLabel)}
                    </span>
                  </button>
                `;
              }).join('')
            : `<div style="grid-column:1/-1; padding:0.75rem; color:#94a3b8; font-weight:800;">Keine Lieferantenpreise gefunden</div>`;

          return `
            <div class="catalog-item">
              <div style="display:flex; justify-content:space-between; margin-bottom:0.75rem;">
                 <h4 style="font-weight:900; color:var(--text-main);">${escapeHtml(
                    p.product_name || p.name || p.product || p.article_group || 'Unbenannt'
                )}</h4>
                
                <span style="font-size:0.625rem; font-weight:900; color:#cbd5e1; text-transform:uppercase;">
                  ${escapeHtml(measure)}
                </span>
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

  
  openLaborPicker() { 
    if (!ui.ensureSetHasName('Personal hinzufügen')) return;
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
  
     // 4. Render Table Rows (Readonly Hours)
    renderLaborItems() {
      const body = document.getElementById('labor-body');
      if (!body) return;

      body.innerHTML = (state.editingSet.labor || []).map((l, idx) => {
        const title = l.name || l.position_name || 'Unbekannt';
        const subTitle = l.qualification_id ? 'Qualifikation' : (l.employee_name || 'Personal');

        const rate = parseFloat(l.rate || 0);
        const hours = parseFloat(l.hours || 0);
        const total = rate * hours;

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
              <span class="labor-percent-badge">${escapeHtml(l.percentOfTotal || '0.0')}%</span>
            </td>

            <td style="text-align:center;">
              <div
                class="price-control"
                style="justify-content:center; opacity:.6; cursor:not-allowed;"
                title="Stundensatz wird aus Aufgabe → Personal übernommen."
              >
                <input
                  type="text"
                  class="price-input"
                  style="width:70px; text-align:center; cursor:not-allowed;"
                  value="${rate.toFixed(2)}"
                  readonly
                >
                <span style="font-size:10px;">€/h</span>
              </div>
              <div style="font-size:0.6rem; color:#94a3b8; margin-top:4px; font-weight:700;">
                (Auto aus Aufgaben)
              </div>
            </td>

            <td style="text-align:center;">
              <div
                class="qty-control"
                style="display:inline-flex; background:#f8fafc; border-color:#e2e8f0; cursor:not-allowed;"
                title="Stunden werden automatisch aus den Aufgaben summiert."
              >
                <input
                  type="text"
                  readonly
                  class="qty-input"
                  style="width:50px; cursor:not-allowed; color:#94a3b8;"
                  value="${hours.toFixed(2)}"
                >
                <span style="font-size:10px; font-weight:900; color:#cbd5e1; padding-right:8px;">h</span>
              </div>
              <div style="font-size:0.6rem; color:#94a3b8; margin-top:4px; font-weight:700;">
                (Auto aus Aufgaben)
              </div>
            </td>

            <td style="text-align:right; font-weight:900; color:var(--text-main);">
              ${total.toLocaleString('de-DE', { style:'currency', currency:'EUR' })}
            </td>

            <td style="text-align:right;">
              <button
                onclick="ui.removeLaborWithConfirm(${idx})"
                class="btn-danger"
                title="Qualifikation aus allen Aufgaben entfernen"
              >
                <i class="fas fa-trash-alt"></i>
              </button>
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

  handleUpdateMaterial(parentId, subId, field, value) {
    const savedScroll = this.getMaterialScrollState();

    const list = Array.isArray(state.editingSet?.components) ? state.editingSet.components : [];
    const item = list.find(i => String(i.id) === String(parentId));
    if (!item) return;

    const target = subId
      ? (Array.isArray(item.subComponents) ? item.subComponents.find(s => String(s.id) === String(subId)) : null)
      : item;

    if (!target) return;

    let safeVal = value;

    if (field === 'margin') {
      safeVal = Math.max(toNum(state.minMatMargin, 0), toNum(value, 0));
    }

    if (field === 'quantity') {
      safeVal = Math.max(1, toNum(value, 1));
      target.qty = safeVal;
      target.quantity = safeVal;
    } else if (field === 'purchasePrice') {
      safeVal = Math.max(0, toNum(value, 0));
      target.purchasePrice = safeVal;
      target.purchase_price = safeVal;
      target.unit_price = safeVal;
      target.base_unit_price = safeVal;
      target.is_price_overridden = false;
    } else if (field === 'price_unit') {
      safeVal = Math.max(1, toNum(value, 1));
      target.price_unit = safeVal;
    } else {
      target[field] = safeVal;
    }

    this.recalculateLocalStats();

    // Rebuild only the material area, but DO NOT restore focus / page scroll
    this.renderComponentItems();
    this.restoreMaterialScrollState(savedScroll);

    app.autoSave();
  },
   handleMaterialVKChange(parentId, subId, newVK, purchasePrice) {
    const savedScroll = this.getMaterialScrollState();

    const list = Array.isArray(state.editingSet?.components) ? state.editingSet.components : [];
    const item = list.find(i => String(i.id) === String(parentId));
    if (!item) return;

    const target = subId
      ? (Array.isArray(item.subComponents) ? item.subComponents.find(s => String(s.id) === String(subId)) : null)
      : item;

    if (!target) return;

    const vkValue = Math.max(0, toNum(newVK, 0));
    const ek = Math.max(0, toNum(purchasePrice, 0));

    let newMargin = 0;
    if (ek > 0) {
      newMargin = ((vkValue - ek) / ek) * 100;
      newMargin -= toNum(state.globalGemeinkosten, 0);
      newMargin -= toNum(state.globalWagnis, 0);
    }

    target.margin = Math.max(toNum(state.minMatMargin, 0), newMargin);

    this.recalculateLocalStats();
    this.renderComponentItems();
    this.restoreMaterialScrollState(savedScroll);

    app.autoSave();
  },
    toggleExpandMat(itemId) {
      const item = state.editingSet.components.find(i => String(i.id) === String(itemId));
      if (item) {
        item.isExpanded = !item.isExpanded;
        this.renderComponentItems();
      }
    },
    // ------------------------------
    // Updates: qty/price/labor
    // ------------------------------
    updateQty(mIdx, sIdx, val) {
      const savedScroll = this.getMaterialScrollState();

      const n = Math.max(0, toNum(val, 0));
      if (sIdx === null) {
        state.editingSet.components[mIdx].qty = n;
        state.editingSet.components[mIdx].quantity = n;
      } else {
        state.editingSet.components[mIdx].subComponents[sIdx].qty = n;
        state.editingSet.components[mIdx].subComponents[sIdx].quantity = n;
      }

      this.recalculateLocalStats();
      this.renderComponentItems();
      this.restoreMaterialScrollState(savedScroll);
      app.autoSave();
    },

    updatePrice(mIdx, sIdx, val) {
      const savedScroll = this.getMaterialScrollState();

      const n = Math.max(0, toNum(val, 0));
      const comp = (sIdx === null)
        ? state.editingSet.components[mIdx]
        : state.editingSet.components[mIdx].subComponents[sIdx];

      if (!comp) return;

      comp.unit_price = n;
      comp.purchasePrice = n;
      comp.purchase_price = n;
      comp.base_unit_price = n;
      comp.is_price_overridden = false;

      this.recalculateLocalStats();
      this.renderComponentItems();
      this.restoreMaterialScrollState(savedScroll);
      app.autoSave();
    },

    updateMeasure(mIdx, sIdx, val) {
      const comp = (sIdx === null)
        ? state.editingSet.components[mIdx]
        : state.editingSet.components[mIdx].subComponents[sIdx];

      if (!comp) return;

      comp.measure = val;
      app.autoSave();
    },

    updatePriceUnit(mIdx, sIdx, val) {
      const savedScroll = this.getMaterialScrollState();

      const n = Math.max(1, toNum(val, 1));
      const comp = (sIdx === null)
        ? state.editingSet.components[mIdx]
        : state.editingSet.components[mIdx].subComponents[sIdx];

      if (!comp) return;

      comp.price_unit = n;

      this.recalculateLocalStats();
      this.renderComponentItems();
      this.restoreMaterialScrollState(savedScroll);
      app.autoSave();
    },

    removeComp(mIdx, sIdx = null) {
      if (sIdx === null) state.editingSet.components.splice(mIdx, 1);
      else state.editingSet.components[mIdx].subComponents.splice(sIdx, 1);
      this.recalculateLocalStats();
      this.renderComponentItems(); // FIX: Changed from this.render()
      app.autoSave();
    },

    removeLabor(idx) {
      const laborItem = state.editingSet.labor[idx];
      if (!laborItem) return;

      const qid = String(laborItem.qualification_id);

      // remove from personal table source
      state.editingSet.labor.splice(idx, 1);

      // remove same qualification from all tasks
      if (Array.isArray(state.editingSet.tasks)) {
        state.editingSet.tasks.forEach(t => {
          if (Array.isArray(t.task_labor)) {
            t.task_labor = t.task_labor.filter(tl => String(tl.qualification_id) !== qid);

            // re-sum task hours from remaining task_labor
            t.hours = t.task_labor.reduce((sum, tl) => sum + (parseFloat(tl.hours) || 0), 0);
          }
        });
      }

      // rebuild personal from task_labor
      this.syncGlobalLaborFromTasks();
      this.recalculateLocalStats();

      if (state.editorTab === 'personal') {
        this.renderLaborItems();
      } else if (state.editorTab === 'aufgabe') {
        this.renderTasksTab();
      } else {
        this.render();
      }

      this.showStatus('QUALIFIKATION ENTFERNT');
    },

  // ------------------------------
    // Stats
    // ------------------------------
      recalculateLocalStats() {
        const s = state.editingSet;
        if (!s) return;

        const comps = Array.isArray(s.components) ? s.components : [];
        const labor = Array.isArray(s.labor) ? s.labor : [];
        const tasks = Array.isArray(s.tasks) ? s.tasks : [];
        const checklists = Array.isArray(s.checklists) ? s.checklists : [];

        let mainTotal = 0;
        let subTotal = 0;
        let mainCount = 0;
        let subCount = 0;

        comps.forEach((main) => {
          mainCount += 1;
          mainTotal += ui.getMaterialLineTotal(main);

          const subs = Array.isArray(main.subComponents) ? main.subComponents : [];
          subs.forEach((sub) => {
            subCount += 1;
            subTotal += ui.getMaterialLineTotal(sub);
          });
        });

        let laborTotal = 0;
        let laborHoursTotal = 0;

        labor.forEach((l) => {
          const hours = toNum(l.hours, 0);
          const rate = toNum(l.rate, 0);

          const c = ui.getEffectiveCosting();
          const baseEK = rate * hours;
          const gk = baseEK * (toNum(c.gk, 0) / 100);
          const wagnis = baseEK * (toNum(c.wagnis, 0) / 100);
          const profit = baseEK * (toNum(c.profitPers, 0) / 100);

          const lineTotal = baseEK + gk + wagnis + profit;

          laborTotal += lineTotal;
          laborHoursTotal += hours;
        });

        labor.forEach((l) => {
          const h = toNum(l.hours, 0);
          l.percentOfTotal = laborHoursTotal > 0
            ? ((h / laborHoursTotal) * 100).toFixed(1)
            : '0.0';
        });

        const total = mainTotal + subTotal + laborTotal;

        const mainPct = total > 0 ? Math.round((mainTotal / total) * 100) : 0;
        const subPct = total > 0 ? Math.round((subTotal / total) * 100) : 0;
        const laborPct = total > 0 ? Math.max(0, 100 - mainPct - subPct) : 0;

        const taskTotalHours = tasks.reduce((sum, t) => sum + toNum(t.hours, 0), 0);

        const personalBadges = labor
          .filter(l => toNum(l.hours, 0) > 0)
          .map(l => ({
            name: l.name || l.position_name || l.employee_name || '—',
            hours: toNum(l.hours, 0)
          }));

        s.stats = {
          mainTotal,
          subTotal,
          laborTotal,
          total,

          mainPct,
          subPct,
          laborPct,

          mainCount,
          subCount,
          laborCount: labor.length,

          taskCount: tasks.length,
          taskTotalHours,

          protocolCount: checklists.length,
          personalBadges
        };
      }, // ✅ important
    // ------------------------------
    // Report + PDF
    // ------------------------------
      openReport() {
        const s = state.editingSet;
        if (!s) return;

        const container = document.getElementById('pdf-report-content');
        if (!container) return;

        const today = new Date().toLocaleDateString('de-DE');
        const components = Array.isArray(s.components) ? s.components : [];
        const laborRows = Array.isArray(s.labor) ? s.labor : [];
        const tasks = Array.isArray(s.tasks) ? s.tasks.slice() : [];

        const stageLabel = (stageId, fallbackName) => {
          if (!stageId) return 'Ohne Stage';
          const st = findStageById(stageId);
          return st?.stage || st?.name || st?.title || fallbackName || ('Stage #' + stageId);
        };

        const calcLine = (item) => {
          const price = toNum(item?.unit_price, 0);
          const qty = toNum(item?.qty, 0);
          const pe = Math.max(1, toNum(item?.price_unit, 1));
          return (price * qty) / pe;
        };

        const fmtMoney = (v) => this.formatMoney(v);
        const fmtQtyUnit = (item) => `${toNum(item?.qty, 0)} ${escapeHtml(item?.measure || 'Stk')}`;

        const plainDesc = String(s.description || '')
          .replace(/<[^>]*>/g, ' ')
          .replace(/\s+/g, ' ')
          .trim();

        const shortDesc = plainDesc
          ? (plainDesc.length > 220 ? plainDesc.slice(0, 220).trim() + '…' : plainDesc)
          : 'Keine Beschreibung';

        tasks.sort((a, b) => {
          const sa = String(a.stage_id ?? '');
          const sb = String(b.stage_id ?? '');
          if (sa !== sb) return sa.localeCompare(sb);
          return toNum(a.sort_order, 0) - toNum(b.sort_order, 0);
        });

        const totalTaskHours = tasks.reduce((sum, t) => sum + toNum(t.hours, 0), 0);

        const taskGroups = new Map();
        tasks.forEach((t) => {
          const label = stageLabel(t.stage_id, t.stage_name);
          if (!taskGroups.has(label)) taskGroups.set(label, []);
          taskGroups.get(label).push(t);
        });

        let materialRowsHtml = '';
        components.forEach((c, idx) => {
          const mainPos = idx + 1;
          materialRowsHtml += `
            <tr class="pdf-main-row">
              <td style="width:24px; text-align:center;"><span class="pdf-pos-badge">${mainPos}</span></td>
              <td style="width:18px; text-align:center;"><span class="pdf-check">✓</span></td>
              <td style="font-weight:900;">${escapeHtml(getProductLabel(c))}</td>
              <td style="text-align:center;">${fmtQtyUnit(c)}</td>
              <td style="text-align:right;">${fmtMoney(toNum(c.unit_price, 0))}</td>
              <td style="text-align:center;">${Math.max(1, toNum(c.price_unit, 1))}</td>
              <td style="text-align:right; font-weight:900;">${fmtMoney(calcLine(c))}</td>
            </tr>
          `;

          (c.subComponents || []).forEach((sub, sIdx) => {
            const subPos = `${mainPos}.${sIdx + 1}`;
            materialRowsHtml += `
              <tr class="pdf-sub-row">
                <td style="width:24px; text-align:center;"><span class="pdf-sub-pos-badge">${subPos}</span></td>
                <td style="width:18px; text-align:center;"><span class="pdf-check">✓</span></td>
                <td class="pdf-text-muted">↳ ${escapeHtml(getProductLabel(sub))}</td>
                <td style="text-align:center;" class="pdf-text-muted">${fmtQtyUnit(sub)}</td>
                <td style="text-align:right;" class="pdf-text-muted">${fmtMoney(toNum(sub.unit_price, 0))}</td>
                <td style="text-align:center;" class="pdf-text-muted">${Math.max(1, toNum(sub.price_unit, 1))}</td>
                <td style="text-align:right; font-weight:900;">${fmtMoney(calcLine(sub))}</td>
              </tr>
            `;
          });
        });

        if (!materialRowsHtml) {
          materialRowsHtml = `
            <tr>
              <td colspan="7" style="text-align:center; color:#94a3b8; font-weight:800; padding:6mm 0;">
                Keine Materialpositionen vorhanden
              </td>
            </tr>
          `;
        }

        let laborRowsHtml = laborRows.map((l, idx) => {
          const name = l.name || l.position_name || l.employee_name || 'Personal';
          const cost = toNum(l.rate, 0) * toNum(l.hours, 0);

          return `
            <tr>
              <td style="width:24px; text-align:center;"><span class="pdf-pos-badge">${idx + 1}</span></td>
              <td style="width:18px; text-align:center;"><span class="pdf-check">✓</span></td>
              <td style="font-weight:900;">${escapeHtml(name)}</td>
              <td style="text-align:center;">${toNum(l.hours, 0).toFixed(2)} h</td>
              <td style="text-align:right;">${fmtMoney(toNum(l.rate, 0))}</td>
              <td style="text-align:right; font-weight:900;">${fmtMoney(cost)}</td>
            </tr>
          `;
        }).join('');

        if (!laborRowsHtml) {
          laborRowsHtml = `
            <tr>
              <td colspan="6" style="text-align:center; color:#94a3b8; font-weight:800; padding:6mm 0;">
                Kein Personal zugewiesen
              </td>
            </tr>
          `;
        }

        let taskPagesHtml = '';
        if (!tasks.length) {
          taskPagesHtml = `
            <div class="pdf-block">
              <div class="pdf-section-title">Aufgaben</div>
              <div style="padding:4mm; background:#f8fafc; border:1px solid #e2e8f0; border-radius:4mm; color:#64748b; font-weight:700;">
                Keine Aufgaben zugewiesen
              </div>
            </div>
          `;
        } else {
          let sections = '';
          for (const [label, group] of taskGroups.entries()) {
            const groupHours = group.reduce((sum, t) => sum + toNum(t.hours, 0), 0);

            sections += `
              <div class="pdf-block">
                <div class="pdf-task-stage">
                  <span>${escapeHtml(label)}</span>
                  <span class="pdf-text-muted">${group.length} Aufgaben • ${groupHours.toFixed(2)} h</span>
                </div>

                <table class="pdf-table">
                  <thead>
                    <tr>
                      <th style="width:24px;">Pos.</th>
                      <th style="width:18px;">✓</th>
                      <th>Aufgabe</th>
                      <th style="width:70px;">Phase</th>
                      <th style="width:55px; text-align:center;">Stunden</th>
                      <th style="width:95px;">Notiz</th>
                    </tr>
                  </thead>
                  <tbody>
                    ${group.map((t, idx) => `
                      <tr>
                        <td style="text-align:center;"><span class="pdf-pos-badge">${idx + 1}</span></td>
                        <td style="text-align:center;"><span class="pdf-check">✓</span></td>
                        <td style="font-weight:900;">${escapeHtml(t.title || 'Untitled')}</td>
                        <td>${escapeHtml(t.phase_name || '—')}</td>
                        <td style="text-align:center;">${toNum(t.hours, 0).toFixed(2)} h</td>
                        <td class="pdf-text-muted">${escapeHtml((t.notes || t.description || '')).slice(0, 70) || '—'}</td>
                      </tr>
                    `).join('')}
                  </tbody>
                </table>
              </div>
            `;
          }

          taskPagesHtml = `
            <div class="pdf-block">
              <div class="pdf-section-title">Aufgabenübersicht</div>
              <div class="pdf-section-subtitle">Nach Stage gruppiert.</div>
              ${sections}
            </div>
          `;
        }

        container.innerHTML = `
          <div class="pdf-report-root">
            <div class="pdf-print-page">
              <div class="pdf-header">
                <div class="pdf-brand">Nuri <span style="color:var(--primary)">Head</span></div>
                <div class="pdf-meta">
                  <h2>Master Set Report</h2>
                  <p>${today}</p>
                </div>
              </div>

              <div class="pdf-cover-box">
                <div class="pdf-cover-title">${escapeHtml(s.name || 'Unbenanntes Set')}</div>
                <div class="pdf-cover-desc">${escapeHtml(shortDesc)}</div>
              </div>

              <div class="pdf-summary-grid">
                <div class="pdf-chart-card">
                  <canvas id="pdfChart"></canvas>
                </div>

                <div class="pdf-stats-card">
                  <div class="pdf-stat-row"><span class="pdf-stat-label">Hauptartikel</span><span class="pdf-stat-val">${fmtMoney(s.stats.mainTotal)}</span></div>
                  <div class="pdf-stat-row"><span class="pdf-stat-label">Zubehör / Sub</span><span class="pdf-stat-val">${fmtMoney(s.stats.subTotal)}</span></div>
                  <div class="pdf-stat-row"><span class="pdf-stat-label">Personal</span><span class="pdf-stat-val">${fmtMoney(s.stats.laborTotal)}</span></div>
                  <div class="pdf-stat-row"><span class="pdf-stat-label">Aufgaben</span><span class="pdf-stat-val">${tasks.length}</span></div>
                  <div class="pdf-stat-row"><span class="pdf-stat-label">Aufgabenstunden</span><span class="pdf-stat-val">${totalTaskHours.toFixed(2)} h</span></div>
                  <div class="pdf-stat-row"><span class="pdf-stat-label">Protokolle</span><span class="pdf-stat-val">${toNum(s.stats.protocolCount, 0)}</span></div>
                </div>
              </div>

              <div class="pdf-total-box">
                <div class="pdf-total-label">Gesamtwert Master Set</div>
                <div class="pdf-total-val">${fmtMoney(s.stats.total)}</div>
              </div>

              <div class="pdf-footer-note">
                <span>${escapeHtml(s.name || 'MasterSet')}</span>
                <span>Erstellt am ${today}</span>
              </div>
            </div>

            <div class="pdf-print-page">
              <div class="pdf-header">
                <div class="pdf-brand" style="font-size:12pt;">Materialübersicht</div>
                <div class="pdf-meta">
                  <h2>Positionen</h2>
                  <p>${components.length} Hauptpositionen</p>
                </div>
              </div>

              <div class="pdf-block">
                <div class="pdf-section-title">Material / Komponenten</div>
                <div class="pdf-section-subtitle">Menge mit Einheit, Positionsnummern und Summen.</div>

                <table class="pdf-table">
                  <thead>
                    <tr>
                      <th style="width:24px;">Pos.</th>
                      <th style="width:18px;">✓</th>
                      <th>Artikel / Position</th>
                      <th style="width:70px; text-align:center;">Menge</th>
                      <th style="width:60px; text-align:right;">Einzel</th>
                      <th style="width:34px; text-align:center;">PE</th>
                      <th style="width:65px; text-align:right;">Summe</th>
                    </tr>
                  </thead>
                  <tbody>${materialRowsHtml}</tbody>
                </table>
              </div>

              <div class="pdf-footer-note">
                <span>Materialliste</span>
                <span>Seite 2</span>
              </div>
            </div>

            <div class="pdf-print-page">
              <div class="pdf-header">
                <div class="pdf-brand" style="font-size:12pt;">Personalübersicht</div>
                <div class="pdf-meta">
                  <h2>Personal</h2>
                  <p>${laborRows.length} Positionen</p>
                </div>
              </div>

              <div class="pdf-block">
                <div class="pdf-section-title">Personal / Arbeitszeit</div>

                <table class="pdf-table">
                  <thead>
                    <tr>
                      <th style="width:24px;">Pos.</th>
                      <th style="width:18px;">✓</th>
                      <th>Position / Mitarbeiter</th>
                      <th style="width:55px; text-align:center;">Stunden</th>
                      <th style="width:60px; text-align:right;">Rate</th>
                      <th style="width:65px; text-align:right;">Kosten</th>
                    </tr>
                  </thead>
                  <tbody>${laborRowsHtml}</tbody>
                </table>
              </div>

              <div class="pdf-footer-note">
                <span>Personalübersicht</span>
                <span>Seite 3</span>
              </div>
            </div>

            <div class="pdf-print-page">
              <div class="pdf-header">
                <div class="pdf-brand" style="font-size:12pt;">Aufgabenübersicht</div>
                <div class="pdf-meta">
                  <h2>Tasks</h2>
                  <p>${tasks.length} Aufgaben • ${totalTaskHours.toFixed(2)} h</p>
                </div>
              </div>

              ${taskPagesHtml}

              <div class="pdf-footer-note">
                <span>Aufgabenübersicht</span>
                <span>Seite 4</span>
              </div>
            </div>
          </div>
        `;

        document.getElementById('pdf-modal-container')?.classList.remove('hidden');

        setTimeout(() => {
          const el = document.getElementById('pdfChart');
          if (!el) return;

          if (window.__pdfChartInstance) {
            try { window.__pdfChartInstance.destroy(); } catch (e) {}
          }

          window.__pdfChartInstance = new Chart(el.getContext('2d'), {
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
              maintainAspectRatio: false,
              plugins: { legend: { display: false } },
              cutout: '68%'
            }
          });
        }, 100);
      },

       downloadPDF() {
        const element = document.querySelector('#pdf-report-content .pdf-report-root');
        if (!element) return;

        const fileName = `MasterSet_${(state.editingSet?.name || 'export').replace(/[^\w\-]+/g, '_')}.pdf`;

        const opt = {
          margin: 0,
          filename: fileName,
          image: { type: 'jpeg', quality: 0.98 },
          html2canvas: {
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff',
            scrollX: 0,
            scrollY: 0
          },
          jsPDF: {
            unit: 'mm',
            format: 'a4',
            orientation: 'portrait'
          },
          pagebreak: {
            mode: ['css']
          }
        };

        html2pdf()
          .set(opt)
          .from(element)
          .save();
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

    const getProductLabel = (item) => {
      if (!item) return 'Unbenannt';

      return (
        item.product_name ||
        item.name ||
        item.title ||
        item.product?.product ||
        item.product?.name ||
        item.product?.article_group ||
        item.product?.title ||
        'Unbenannt'
      );
    };
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
    bindBackdropClose('distributor-compare-modal', () => ui.closeDistributorCompareModal());
    bindBackdropClose('set-desc-modal', () => setDescUI.close());
    bindBackdropClose('costing-modal', () => ui.closeCostingModal());
    bindBackdropClose('material-desc-modal', () => materialDescUI.close());
    // Close on ESC
    document.addEventListener('keydown', (e) => {
      if (e.key !== 'Escape') return;
      ui.closeModal();
      ui.closePrompt();
      ui.closePdf();
      ui.closeLink();
      ui.closeCostingModal();
      materialDescUI.close();
      setDescUI.close();
      ui.closeDistributorCompareModal();
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

      function escapeHtml(s) {
        return String(s || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
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
  // MAGIC INTERCEPTOR: Automatically trigger Auto-Save on all data mutations!
  // ===========================================================================
    const mutateMethods = [
        'updateMeasure', 'updatePriceUnit', 'saveCustomTask', 'addPhaseFromEncoded',
        'addTaskLabor', 'updateTaskLaborHours', 'removeTaskLabor', 'addLaborFromJson',
        'updateLaborRate', 'updateLaborHours', 'removeLabor', 'updateQty', 'updatePrice',
        'refreshPrice', 'removeComp', 'addTaskFromEncoded', 'updateTaskHours',
        'changeTaskStage', 'removeTask', 'addFromCatalog', 'updateChecklistField',
        'removeChecklistAt', 'removeChecklistById', 'addChecklistFromEncoded',

        // ✅ missing task-labor mutations
        'updateTaskLaborQty',
        'updateTaskLaborUnit',
        'updateTaskLaborUnitRate',
        'updateTaskLaborEKTotal',
        'clearTaskLaborEKOverride',
        'toggleTaskLaborUnitRateLock',
        'toggleTaskLaborEKTotalLock'
    ];

  mutateMethods.forEach(method => {
        if (typeof ui[method] === 'function') {
            const original = ui[method].bind(ui);
            ui[method] = async function(...args) {
                // 1. Check if locked BEFORE allowing the change
                if (state.isLocked) {
                    document.getElementById('locked-warning-modal').classList.remove('hidden');
                    // Re-render to revert any visual input changes the user just typed
                    ui.render(); 
                    return; // Stop execution!
                }
                
                // 2. If unlocked, proceed normally
                const result = await original(...args);
                app.autoSave(); // SILENTLY TRIGGERS SAVE AFTER THE ACTION!
                return result;
            };
        }
    });
 

  const originalBindTaskSort = ui.bindSelectedTasksSortables;
  ui.bindSelectedTasksSortables = function() {
      originalBindTaskSort.call(ui);
      if (window.Sortable) {
          document.querySelectorAll('.tasks-stage-list').forEach(el => {
              const sortableInstance = Sortable.get(el);
              if (sortableInstance) sortableInstance.option('disabled', state.isLocked);
              if(sortableInstance) {
                  const origOnEnd = sortableInstance.options.onEnd;
                  sortableInstance.option('onEnd', function(evt) {
                      if(origOnEnd) origOnEnd(evt);
                      app.autoSave();
                  });
              }
          });
      }
  };
  
  const originalBindChecklistSort = ui.bindChecklistDnD;
  ui.bindChecklistDnD = function() {
      originalBindChecklistSort.call(ui);
      if (window.Sortable) {
          const sel = document.getElementById('checklists-selected-list');
          if (sel) {
              const sortableInstance = Sortable.get(sel);
              if (sortableInstance) sortableInstance.option('disabled', state.isLocked);
              if(sortableInstance) {
                  const origOnEnd = sortableInstance.options.onEnd;
                  sortableInstance.option('onEnd', function(evt) {
                      if(origOnEnd) origOnEnd(evt);
                      app.autoSave();
                  });
              }
          }
      }
  };
 
  window.state = state;
  window.api = api;
  window.app = app;
  window.ui = ui;  

  window.wizard = wizard;
  // ===========================================================================
  // Boot
  // ===========================================================================
  window.addEventListener('DOMContentLoaded', async () => { 
   await api.getGroups();

    const saved = pageState.load();

    if (!saved) {
      ui.render();
      return;
    }

    // restore simple UI flags
    state.editorTab = saved.editorTab || 'material';
    state.setsTab = saved.setsTab || 'all';
    state.groupViewMode = saved.groupViewMode || 'list';
    state.showSummary = !!saved.showSummary;
    state.selectedStageId = saved.selectedStageId || null;
    state.taskSearch = saved.taskSearch || '';
    state.taskSectionFilter = saved.taskSectionFilter || '';
    state.materialSearch = saved.materialSearch || '';
    state.showMatDrop = !!saved.showMatDrop;

    // restore selected group
    if (saved.selectedGroup?.id) {
      const foundGroup = (state.groups || []).find(g => String(g.id) === String(saved.selectedGroup.id));

      if (foundGroup) {
        state.selectedGroup = {
          id: foundGroup.id,
          name: foundGroup.article_group || foundGroup.name || saved.selectedGroup.name || ''
        };

        await api.getSets(foundGroup.id);
        await api.getGroupSets(foundGroup.id);

        state.view = saved.view || 'groupList';

        // restore open editor
        if (saved.editingSetId) {
          state.view = 'editor';

          await Promise.all([
            api.loadSet(saved.editingSetId),
            api.loadLaborOptions(),
            api.loadTaskOptions(saved.taskSearch || ''),
            api.loadChecklistOptions(''),
            api.loadCostingSets(foundGroup.id),
          ]);

          ui.render();

          // restore collapsed wizard phases if needed
          if (window.wizard && Array.isArray(saved.collapsedPhases)) {
            window.wizard._collapsedPhases = new Set(saved.collapsedPhases.map(String));
          }

          pageState.restoreDom(saved);
          return;
        }

        ui.render();
        pageState.restoreDom(saved);
        return;
      }
    }

    ui.render();
    pageState.restoreDom(saved);
  });
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.material-actions-menu')) {
      document.querySelectorAll('.material-actions-menu.open').forEach(el => {
        el.classList.remove('open');
      });
    }

    if (!e.target.closest('#mat-col-dropdown-wrap')) {
      if (window.state && window.state.showMatDrop) {
        window.state.showMatDrop = false;
        const dd = document.querySelector('#mat-col-dropdown-wrap .absolute');
        if (dd) dd.remove();
        if (window.ui) window.ui.renderComponentItems();
      }
    }
  });

 

})();
</script> 
 
</body>
</html>