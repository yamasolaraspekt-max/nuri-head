@extends('admin.layouts.app')

@section('title', 'Smart Angebots-Wizard')

@php
use App\Models\ArticleGroup;

$articleGroups = ArticleGroup::query()
  ->select('id', 'article_group', 'initial', 'image')
  ->orderBy('article_group')
  ->get();

$routes = [
  'home' => url('/employee_dashboard'),
  'oldWizard' => route('offers.wizard'),
  'smartWizard' => route('offers.wizard.smart'),
  'adminOffers' => route('admin.offers.index'),

  'customers' => route('offers.wizard.customers'),
  'customerShowBase' => url('/offers/wizard/customers'),
  'customerObjectsBase' => url('/offers/wizard/customers'),

  'createOffer' => route('offers.wizard.create'),

  'templates' => route('offers.wizard.templates'),
  'templateShowBase' => url('/offers/wizard/templates'),

  'products' => route('offers.wizard.products'),
  'productsList' => route('offers.wizard.products-list'),

  'groupSets' => route('offers.wizard.group-sets'),
  'groupSetShowBase' => url('/offers/master-set-groups'),
  'masterSetShowBase' => url('/offers/master-sets'),
];
@endphp

@once
    @push('style')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
        <style>
          :root{
            --app-bg:#f4f7f9;
            --card-bg:#ffffff;
            --text-main:#1f2937;
            --text-muted:#6b7280;
            --border:#e5e7eb;
            --primary:var(--sa-accent);
            --primary-hover:var(--sa-accent-hover);
            --primary-light:var(--sa-accent-light);
            --blue:#74b2d4;
            --blue-hover:#5d9fc5;
            --blue-light:#eff6ff;
            --success:#10b981;
            --success-light:#ecfdf5;
            --warning:#f59e0b;
            --warning-light:#fffbeb;
            --danger:#ef4444;
            --danger-light:#fef2f2;
            --purple:#7c3aed;
            --purple-light:#f5f3ff;
            --orange:#f97316;
            --orange-light:#fff7ed;
            --shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
            --shadow:0 10px 25px -10px rgb(0 0 0 / .25),0 4px 8px -4px rgb(0 0 0 / .12);
            --radius:16px;
            --transition:all .2s ease-in-out;
          }

          @keyframes ocFadeIn{from{opacity:0}to{opacity:1}}
          @keyframes ocSlideUp{from{transform:translateY(1rem);opacity:0}to{transform:translateY(0);opacity:1}}
          @keyframes ocSlideRight{from{transform:translateX(2rem);opacity:0}to{transform:translateX(0);opacity:1}}
          @keyframes ocSpin{to{transform:rotate(360deg)}}

          .oc-wrap{
            font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
            color:var(--text-main); 
          }
          @media(max-width:900px){.oc-wrap{padding:18px;margin:10px auto;}}

          .oc-animate{animation:ocFadeIn .25s ease both,ocSlideUp .35s ease both;}
          .oc-animate-right{animation:ocFadeIn .25s ease both,ocSlideRight .35s ease both;}

          .oc-header{margin-bottom:18px;}
          .oc-titlebar{display:flex;align-items:flex-end;justify-content:space-between;gap:12px;margin-bottom:16px;flex-wrap:wrap;}
          .oc-title{font-size:28px;font-weight:950;letter-spacing:-.04em;color:#111827;}
          .oc-sub{font-size:14px;color:var(--text-muted);margin-top:4px;max-width:850px;line-height:1.55;}
          .oc-breadcrumb{display:flex;align-items:center;flex-wrap:wrap;gap:8px;margin-top:10px;font-size:13px;color:var(--text-muted);}
          .oc-breadcrumb a{color:var(--text-muted);text-decoration:none;font-weight:800;}
          .oc-breadcrumb a:hover{color:#111827;}
          .oc-breadcrumb span.current{color:#111827;font-weight:900;}

          .oc-inline-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center;}

          .oc-btn,.oc-btn-soft,.oc-btn-ic{transition:var(--transition);text-decoration:none;cursor:pointer;}
          .oc-btn{
            background:var(--primary);
            color:#fff;
            border:none;
            padding:10px 16px;
            border-radius:12px;
            font-weight:950;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            white-space:nowrap;
          }
          .oc-btn:hover{background:var(--primary-hover);color:#fff;text-decoration:none;}
          .oc-btn:disabled{opacity:.55;cursor:not-allowed;}
          .oc-btn.dark{background:#111827;}
          .oc-btn.dark:hover{background:#1f2937;}
          .oc-btn.blue{background:var(--blue);}
          .oc-btn.blue:hover{background:var(--blue-hover);}
          .oc-btn.orange{background:var(--orange);}
          .oc-btn.orange:hover{background:#ea580c;}
          .oc-btn.purple{background:var(--purple);}
          .oc-btn.purple:hover{background:#6d28d9;}
          .oc-btn.green{background:var(--success);}
          .oc-btn.green:hover{background:#059669;}

          .oc-btn-soft{
            background:#fff;
            color:#111827;
            border:1px solid var(--border);
            padding:10px 14px;
            border-radius:12px;
            font-weight:900;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            white-space:nowrap;
          }
          .oc-btn-soft:hover{background:#f9fafb;color:#111827;text-decoration:none;}
          .oc-btn-soft.blue{background:var(--blue-light);border-color:#d8edf7;color:#2f7fa4;}
          .oc-btn-soft.green{background:var(--success-light);border-color:#c7f2df;color:#047857;}
          .oc-btn-soft.orange{background:var(--orange-light);border-color:#fed7aa;color:#c2410c;}
          .oc-btn-soft.purple{background:var(--purple-light);border-color:#ddd6fe;color:#6d28d9;}

          .oc-btn-ic{
            width:38px;
            height:38px;
            border-radius:10px;
            border:1px solid var(--border);
            background:#fff;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            color:var(--text-muted);
            flex:0 0 auto;
          }
          .oc-btn-ic:hover{background:#f9fafb;color:#111827;border-color:#d1d5db;text-decoration:none;}
          .oc-btn-ic.blue{color:var(--blue);border-color:#d8edf7;background:var(--blue-light);}
          .oc-btn-ic.success{color:var(--success);border-color:#c7f2df;background:var(--success-light);}

          .oc-analytics{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px;}
          @media(max-width:1200px){.oc-analytics{grid-template-columns:repeat(2,minmax(0,1fr));}}
          @media(max-width:700px){.oc-analytics{grid-template-columns:1fr;}}

          .oc-stat{
            background:#fff;
            border:1px solid var(--border);
            border-radius:18px;
            padding:16px;
            box-shadow:var(--shadow-sm);
            display:flex;
            align-items:center;
            gap:12px;
            min-height:92px;
          }
          .oc-stat-icon{width:50px;height:50px;border-radius:16px;display:flex;align-items:center;justify-content:center;flex:0 0 auto;}
          .oc-stat-icon.total{background:var(--blue-light);color:var(--blue);}
          .oc-stat-icon.published{background:var(--success-light);color:var(--success);}
          .oc-stat-icon.unpublished{background:var(--warning-light);color:#d97706;}
          .oc-stat-icon.type{background:var(--purple-light);color:var(--purple);}
          .oc-stat-label{font-size:11px;font-weight:900;color:var(--text-muted);text-transform:uppercase;letter-spacing:.07em;}
          .oc-stat-value{font-size:24px;font-weight:950;color:#111827;line-height:1.1;margin-top:4px;}
          .oc-stat-sub{font-size:12px;color:var(--text-muted);margin-top:4px;}

          .oc-card,.oc-panel{
            background:#fff;
            border:1px solid var(--border);
            border-radius:18px;
            box-shadow:var(--shadow-sm);
          }
          .oc-panel{padding:22px;}
          .oc-panel-head{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:14px;
            border-bottom:1px solid var(--border);
            padding-bottom:16px;
            margin-bottom:18px;
            flex-wrap:wrap;
          }
          .oc-panel-kicker{
            display:inline-flex;
            align-items:center;
            gap:7px;
            padding:6px 10px;
            border-radius:999px;
            background:var(--blue-light);
            color:#2f7fa4;
            font-size:11px;
            font-weight:950;
            text-transform:uppercase;
            letter-spacing:.08em;
            margin-bottom:8px;
          }
          .oc-panel-title{font-size:21px;font-weight:950;color:#111827;margin:0;}
          .oc-panel-sub{font-size:13px;color:var(--text-muted);margin-top:5px;line-height:1.5;}

          .oc-step-tabs{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:16px;}
          .oc-step-tab{
            border:1px solid var(--border);
            background:#fff;
            color:var(--text-muted);
            padding:10px 14px;
            border-radius:999px;
            font-weight:950;
            cursor:pointer;
            transition:var(--transition);
            display:flex;
            align-items:center;
            gap:8px;
          }
          .oc-step-tab.active{border-color:var(--primary);background:var(--primary-light);color:#49660d;}
          .oc-step{display:none;}
          .oc-step.active{display:block;}

          .oc-form-grid{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:16px;}
          .oc-col-12{grid-column:span 12;}
          .oc-col-8{grid-column:span 8;}
          .oc-col-6{grid-column:span 6;}
          .oc-col-4{grid-column:span 4;}
          .oc-col-3{grid-column:span 3;}
          @media(max-width:1000px){.oc-col-8,.oc-col-6,.oc-col-4,.oc-col-3{grid-column:span 12;}}

          .oc-label{display:block;font-size:13px;font-weight:900;color:#111827;margin-bottom:6px;}
          .oc-help{font-size:12px;color:var(--text-muted);margin-top:6px;}

          .oc-input,.oc-input-form,.oc-select,.oc-textarea{
            width:100%;
            border-radius:10px;
            border:1px solid var(--border);
            background:#fff;
            font-size:14px;
            outline:none;
            transition:var(--transition);
          }
          .oc-input{
            background:#f9fafb;
            padding:11px 12px 11px 38px;
            min-width:240px;
            background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
            background-repeat:no-repeat;
            background-position:12px center;
            background-size:16px;
          }
          .oc-input-form,.oc-select{padding:11px 12px;}
          .oc-textarea{padding:12px;min-height:96px;resize:vertical;}
          .oc-input:focus,.oc-input-form:focus,.oc-select:focus,.oc-textarea:focus{
            background:#fff;
            border-color:var(--primary);
            box-shadow:0 0 0 3px var(--primary-light);
          }

          .select2-container--default .select2-selection--single{
            height:44px;
            border:1px solid var(--border);
            border-radius:10px;
            display:flex;
            align-items:center;
          }
          .select2-container--default .select2-selection--single .select2-selection__rendered{
            color:#111827;
            font-size:14px;
            font-weight:800;
            padding-left:12px;
            line-height:44px;
          }
          .select2-container--default .select2-selection--single .select2-selection__arrow{height:42px;}
          .select2-dropdown{border-color:var(--border);border-radius:12px;overflow:hidden;}
          .select2-search--dropdown .select2-search__field{border:1px solid var(--border);border-radius:8px;padding:8px;}

          .oc-suggest-wrap{position:relative;}
          .oc-suggest{
            position:absolute;
            left:0;
            right:0;
            top:calc(100% + 8px);
            z-index:80;
            max-height:360px;
            overflow:auto;
            background:#fff;
            border:1px solid var(--border);
            border-radius:14px;
            box-shadow:var(--shadow);
            display:none;
          }
          .oc-suggest.open{display:block;}
          .oc-suggest-item{padding:12px;border-bottom:1px solid #f3f4f6;cursor:pointer;transition:var(--transition);}
          .oc-suggest-item:hover{background:var(--blue-light);}
          .oc-suggest-title{font-size:14px;font-weight:950;color:#111827;}
          .oc-suggest-sub{font-size:12px;color:var(--text-muted);margin-top:3px;}

          .oc-selected-card{
            display:none;
            margin-top:16px;
            background:var(--blue-light);
            border:1px solid #d8edf7;
            border-radius:16px;
            padding:14px;
          }
          .oc-selected-card.open{display:block;}
          .oc-selected-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;}
          @media(max-width:1000px){.oc-selected-grid{grid-template-columns:repeat(2,minmax(0,1fr));}}
          @media(max-width:600px){.oc-selected-grid{grid-template-columns:1fr;}}
          .oc-mini-label{font-size:10px;color:#487a94;text-transform:uppercase;letter-spacing:.07em;font-weight:950;}
          .oc-mini-value{font-size:13px;color:#111827;font-weight:950;margin-top:3px;}

          .oc-method-title{
            font-size:18px;
            font-weight:950;
            color:#111827;
            margin:26px 0 14px;
            border-bottom:1px solid var(--border);
            padding-bottom:10px;
          }
          .oc-method-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:10px;}
          @media(max-width:1200px){.oc-method-grid{grid-template-columns:repeat(2,minmax(0,1fr));}}
          @media(max-width:700px){.oc-method-grid{grid-template-columns:1fr;}}

          .oc-method-card{
            background:#fff;
            border:2px solid var(--border);
            border-radius:22px;
            padding:22px;
            cursor:pointer;
            min-height:230px;
            display:flex;
            flex-direction:column;
            position:relative;
            overflow:hidden;
            transition:var(--transition);
          }
          .oc-method-card:hover{transform:translateY(-2px);box-shadow:var(--shadow);}
          .oc-method-card.selected{box-shadow:0 0 0 4px rgba(116,178,212,.18),var(--shadow-sm);}
          .oc-method-card.blue:hover,.oc-method-card.blue.selected{border-color:#3b82f6;}
          .oc-method-card.green:hover,.oc-method-card.green.selected{border-color:#10b981;}
          .oc-method-card.orange:hover,.oc-method-card.orange.selected{border-color:#f97316;}
          .oc-method-card.purple:hover,.oc-method-card.purple.selected{border-color:#7c3aed;}
          .oc-method-icon{
            width:52px;
            height:52px;
            border-radius:999px;
            display:flex;
            align-items:center;
            justify-content:center;
            margin-bottom:14px;
          }
          .oc-method-card.blue .oc-method-icon{background:#eff6ff;color:#2563eb;}
          .oc-method-card.green .oc-method-icon{background:#ecfdf5;color:#059669;}
          .oc-method-card.orange .oc-method-icon{background:#fff7ed;color:#ea580c;}
          .oc-method-card.purple .oc-method-icon{background:#f5f3ff;color:#7c3aed;}
          .oc-method-card h3{font-size:18px;font-weight:950;color:#111827;margin:0 0 8px;}
          .oc-method-card p{font-size:12px;color:var(--text-muted);line-height:1.6;margin:0 0 18px;flex:1;font-weight:700;}
          .oc-method-card .oc-method-btn{
            width:100%;
            border:none;
            border-radius:11px;
            padding:10px 12px;
            font-size:12px;
            font-weight:950;
            background:#f3f4f6;
            color:#374151;
            transition:var(--transition);
          }
          .oc-method-card.blue.selected .oc-method-btn,.oc-method-card.blue:hover .oc-method-btn{background:#2563eb;color:#fff;}
          .oc-method-card.green.selected .oc-method-btn,.oc-method-card.green:hover .oc-method-btn{background:#059669;color:#fff;}
          .oc-method-card.orange.selected .oc-method-btn,.oc-method-card.orange:hover .oc-method-btn{background:#ea580c;color:#fff;}
          .oc-method-card.purple.selected .oc-method-btn,.oc-method-card.purple:hover .oc-method-btn{background:#7c3aed;color:#fff;}
          .oc-recommended{
            position:absolute;
            top:14px;
            right:14px;
            background:#111827;
            color:#fff;
            border-radius:999px;
            padding:5px 9px;
            font-size:10px;
            font-weight:950;
            text-transform:uppercase;
            letter-spacing:.08em;
            display:none;
          }
          .oc-method-card.recommended .oc-recommended{display:block;}

          .oc-recommendation{
            display:none;
            margin-top:18px;
            border:2px solid #111827;
            background:linear-gradient(135deg,#fff,#f8fafc);
            border-radius:22px;
            padding:24px;
            box-shadow:var(--shadow);
            position:relative;
            overflow:hidden;
          }
          .oc-recommendation.open{display:block;}
          .oc-reco-grid{display:grid;grid-template-columns:1fr auto;gap:20px;align-items:center;}
          @media(max-width:900px){.oc-reco-grid{grid-template-columns:1fr;}}
          .oc-reco-title{font-size:26px;font-weight:950;color:#111827;letter-spacing:-.03em;}
          .oc-reco-title span{border-bottom:2px solid var(--purple);color:#1f2937;}
          .oc-reco-text{
            font-size:15px;
            color:#374151;
            line-height:1.65;
            margin-top:12px;
            background:#f9fafb;
            border:1px solid #f1f5f9;
            border-radius:14px;
            padding:14px;
            font-weight:700;
          }
          .oc-reco-next{display:flex;align-items:center;gap:8px;margin-top:14px;font-size:13px;font-weight:950;color:#111827;flex-wrap:wrap;}
          .oc-reco-next span:last-child{background:var(--purple-light);color:#6d28d9;padding:6px 10px;border-radius:10px;}

          .oc-main-grid{display:grid;grid-template-columns:minmax(0,1fr)420px;gap:18px;align-items:start;}
          @media(max-width:1240px){.oc-main-grid{grid-template-columns:1fr;}}

          .oc-toolbar{
            background:#fff;
            border:1px solid var(--border);
            border-radius:16px;
            padding:14px 16px;
            display:flex;
            flex-wrap:wrap;
            gap:14px;
            align-items:flex-end;
            justify-content:space-between;
            margin-bottom:16px;
            box-shadow:var(--shadow-sm);
          }
          .oc-toolbar-left,.oc-toolbar-right{display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;}
          .oc-toolbar-left{flex:1;}
          .oc-filter-block{display:flex;flex-direction:column;gap:6px;min-width:180px;}
          .oc-filter-block.search{flex:1;min-width:280px;}
          .oc-filter-label{font-size:11px;font-weight:950;color:var(--text-muted);text-transform:uppercase;letter-spacing:.07em;}

          .oc-list-head{
            display:grid;
            grid-template-columns:92px minmax(240px,1.5fr) 140px 150px 120px 110px 180px;
            gap:14px;
            align-items:center;
            padding:16px 16px 10px;
            color:var(--text-muted);
            font-size:11px;
            font-weight:950;
            text-transform:uppercase;
            letter-spacing:.07em;
          }
          @media(max-width:1280px){.oc-list-head{display:none;}}
          .oc-list{display:flex;flex-direction:column;gap:12px;padding:0 0 16px;}
          .oc-item{
            background:#fff;
            border:1px solid var(--border);
            border-radius:16px;
            transition:var(--transition);
            overflow:hidden;
            margin:0 16px;
          }
          .oc-item:hover{border-color:var(--primary);box-shadow:var(--shadow);}
          .oc-item.active{border-color:var(--blue);box-shadow:0 0 0 3px rgba(116,178,212,.18);}
          .oc-item-row{
            padding:16px;
            display:grid;
            gap:16px;
            align-items:center;
            grid-template-columns:92px minmax(240px,1.5fr) 140px 150px 120px 110px 180px;
          }
          @media(max-width:1280px){.oc-item-row{grid-template-columns:1fr;}}
          .oc-cell{min-width:0;}
          .oc-cell-title{font-size:11px;font-weight:900;color:var(--text-muted);text-transform:uppercase;margin-bottom:4px;display:none;}
          @media(max-width:1280px){.oc-cell-title{display:block;}}
          .oc-main{display:flex;flex-direction:column;min-width:0;}
          .oc-ttl{font-weight:950;font-size:15px;margin-bottom:4px;color:#111827;}
          .oc-subt{font-size:13px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
          .oc-tags{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;}
          .oc-tag{
            display:inline-flex;
            align-items:center;
            gap:5px;
            border-radius:999px;
            padding:5px 8px;
            font-size:10px;
            line-height:1;
            font-weight:950;
            text-transform:uppercase;
            letter-spacing:.04em;
            background:#f3f4f6;
            color:#4b5563;
          }
          .oc-tag.green{background:var(--success-light);color:#047857;}
          .oc-tag.blue{background:var(--blue-light);color:#2f7fa4;}
          .oc-tag.orange{background:var(--orange-light);color:#c2410c;}
          .oc-tag.purple{background:var(--purple-light);color:#6d28d9;}

          .oc-score-badge{
            display:inline-flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            min-width:70px;
            min-height:54px;
            padding:7px 10px;
            border-radius:14px;
            border:1px solid var(--border);
            background:#f9fafb;
          }
          .oc-score-badge.good{background:var(--success-light);border-color:#c7f2df;color:#047857;}
          .oc-score-badge.mid{background:var(--blue-light);border-color:#d8edf7;color:#2f7fa4;}
          .oc-score-badge.low{background:var(--warning-light);border-color:#fde7b0;color:#b45309;}
          .oc-score-label{font-size:9px;font-weight:950;text-transform:uppercase;letter-spacing:.07em;}
          .oc-score-value{font-size:18px;font-weight:950;line-height:1.05;margin-top:2px;}

          .oc-status-pill{display:inline-flex;align-items:center;justify-content:center;padding:7px 10px;border-radius:999px;font-size:12px;font-weight:950;white-space:nowrap;}
          .oc-status-pill.blue{background:var(--blue-light);color:#2f7fa4;}

          .oc-actions{display:flex;align-items:center;justify-content:flex-end;gap:8px;flex-wrap:wrap;}
          @media(max-width:1280px){.oc-actions{justify-content:flex-start;}}

          .oc-preview-sticky{position:sticky;top:92px;}
          @media(max-width:1240px){.oc-preview-sticky{position:static;}}

          .oc-preview-empty,.oc-empty{
            text-align:center;
            padding:48px;
            color:var(--text-muted);
            background:#fff;
            border:1px dashed var(--border);
            border-radius:16px;
            margin:16px;
            font-weight:700;
          }
          .oc-preview-empty{margin:0;background:#fafafa;}
          .oc-preview-title{font-size:17px;font-weight:950;color:#111827;margin-bottom:5px;}

          .oc-summary-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:14px;}
          .oc-summary-box{background:#f9fafb;border:1px solid var(--border);border-radius:12px;padding:12px;}
          .oc-summary-label{font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.07em;font-weight:950;}
          .oc-summary-value{margin-top:3px;font-size:16px;color:#111827;font-weight:950;}

          .oc-section-card{border:1px solid var(--border);border-radius:14px;overflow:hidden;background:#fff;margin-bottom:12px;}
          .oc-section-head{background:#f9fafb;padding:12px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:10px;}
          .oc-section-title{font-size:14px;font-weight:950;color:#111827;}
          .oc-section-count{font-size:11px;color:var(--text-muted);font-weight:950;text-transform:uppercase;}
          .oc-node{padding:12px 14px;border-bottom:1px solid #f3f4f6;}
          .oc-node:last-child{border-bottom:none;}
          .oc-node.child{background:#fafafa;margin-left:18px;border-left:3px solid var(--blue-light);}
          .oc-node-title{display:flex;justify-content:space-between;gap:12px;font-size:13px;font-weight:950;color:#111827;}
          .oc-node-meta{margin-top:4px;font-size:12px;color:var(--text-muted);}

          .oc-add-box{
            margin-top:16px;
            padding:22px;
            border:2px dashed #d1d5db;
            background:#f9fafb;
            border-radius:22px;
            text-align:center;
          }
          .oc-add-box-title{font-weight:950;color:#374151;margin-bottom:14px;text-transform:uppercase;letter-spacing:.07em;font-size:12px;}
          .oc-add-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:10px;}
          @media(max-width:1000px){.oc-add-grid{grid-template-columns:repeat(2,minmax(0,1fr));}}
          @media(max-width:600px){.oc-add-grid{grid-template-columns:1fr;}}
          .oc-add-btn{
            border:1px solid var(--border);
            background:#fff;
            border-radius:14px;
            padding:14px;
            display:flex;
            flex-direction:column;
            align-items:center;
            gap:8px;
            font-weight:950;
            color:#111827;
            transition:var(--transition);
          }
          .oc-add-btn:hover{transform:translateY(-2px);box-shadow:var(--shadow-sm);border-color:var(--blue);}

          .oc-coming{border:1px dashed #c7f2df;background:var(--success-light);border-radius:16px;padding:22px;text-align:center;}

          .oc-modal-backdrop{
            position:fixed;
            inset:0;
            z-index:1200;
            background:rgba(17,24,39,.55);
            backdrop-filter:blur(3px);
            opacity:0;
            pointer-events:none;
            transition:opacity .22s ease;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:18px;
          }
          .oc-modal-backdrop.open{opacity:1;pointer-events:auto;}
          .oc-modal{
            width:100%;
            max-width:720px;
            background:#fff;
            border:1px solid rgba(229,231,235,.9);
            border-radius:16px;
            box-shadow:var(--shadow);
            transform:translateY(12px) scale(.985);
            transition:transform .22s ease;
            overflow:hidden;
          }
          .oc-modal-backdrop.open .oc-modal{transform:translateY(0) scale(1);}
          .oc-modal-h{display:flex;gap:12px;align-items:center;justify-content:space-between;padding:16px 18px;border-bottom:1px solid var(--border);background:#fafafa;}
          .oc-modal-ttl{font-weight:950;font-size:16px;line-height:1.2;margin:0;color:#111827;}
          .oc-modal-b{padding:20px 18px;max-height:72vh;overflow-y:auto;}
          .oc-modal-f{padding:14px 18px;border-top:1px solid var(--border);background:#fafafa;display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;}

          .oc-toast-wrap{position:fixed;right:20px;bottom:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none;}
          .oc-toast{
            pointer-events:auto;
            min-width:280px;
            max-width:380px;
            background:#fff;
            border:1px solid var(--border);
            border-radius:14px;
            box-shadow:var(--shadow);
            padding:12px;
            display:flex;
            gap:10px;
            align-items:flex-start;
            animation:ocFadeIn .2s ease both;
          }
          .oc-toast-ic{width:34px;height:34px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex:0 0 auto;}
          .oc-toast-ic.ok{background:var(--success-light);color:var(--success);}
          .oc-toast-ic.bad{background:var(--danger-light);color:var(--danger);}
          .oc-toast-ic.info{background:var(--blue-light);color:var(--blue);}
          .oc-toast-ttl{font-weight:950;font-size:13px;margin:0;color:#111827;}
          .oc-toast-msg{font-size:12px;color:#374151;margin:4px 0 0;line-height:1.4;}
          .oc-toast-x{margin-left:auto;background:transparent;border:none;cursor:pointer;color:var(--text-muted);}
          .oc-spinner{width:18px;height:18px;border:3px solid rgba(255,255,255,.45);border-top-color:#fff;border-radius:50%;animation:ocSpin .8s linear infinite;}
        </style>
    @endpush
@endonce

@section('content')
    <div class="oc-wrap" id="smartWizardApp">
      <div class="oc-header">
        <div class="oc-titlebar">
          <div>
            <div class="oc-title">SMART ANGEBOTS-WIZARD</div>
            <div class="oc-sub">
              Anfrage analysieren, Kundendaten zuordnen, Gewerk auswählen und den passenden Weg zur Angebotserstellung starten.
            </div>

            <div class="oc-breadcrumb">
              <a href="{{ $routes['home'] }}">Home</a>
              <span>›</span>
              <a href="{{ $routes['adminOffers'] }}">Angebote</a>
              <span>›</span>
              <span class="current">Smart Wizard</span>
            </div>
          </div>

          <div class="oc-inline-actions">
            <a href="{{ $routes['oldWizard'] }}" class="oc-btn-soft">Alter Wizard</a>
            <a href="{{ $routes['adminOffers'] }}" class="oc-btn-soft blue">Angebotsübersicht</a>
            <button type="button" class="oc-btn dark" data-reset-wizard>
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14"></path>
              </svg>
              Neue Anfrage
            </button>
          </div>
        </div>
      </div>

      <div class="oc-analytics">
        <div class="oc-stat">
          <div class="oc-stat-icon total">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 21l-6-6"/><circle cx="10" cy="10" r="7"/>
            </svg>
          </div>
          <div>
            <div class="oc-stat-label">Templates</div>
            <div class="oc-stat-value" id="stat-template-count">0</div>
            <div class="oc-stat-sub">Gefundene Treffer</div>
          </div>
        </div>

        <div class="oc-stat">
          <div class="oc-stat-icon published">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 6L9 17l-5-5"/>
            </svg>
          </div>
          <div>
            <div class="oc-stat-label">Beste Übereinstimmung</div>
            <div class="oc-stat-value" id="stat-best-score">0%</div>
            <div class="oc-stat-sub">Match Score</div>
          </div>
        </div>

        <div class="oc-stat">
          <div class="oc-stat-icon unpublished">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 8v4l3 3"/><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <div class="oc-stat-label">Ausgewählte Vorlage</div>
            <div class="oc-stat-value" id="stat-selected-template">—</div>
            <div class="oc-stat-sub">Bereit zur Übernahme</div>
          </div>
        </div>

        <div class="oc-stat">
          <div class="oc-stat-icon type">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 7h16M7 12h10M10 17h4"/>
            </svg>
          </div>
          <div>
            <div class="oc-stat-label">Aktueller Weg</div>
            <div class="oc-stat-value" id="stat-current-path">Analyse</div>
            <div class="oc-stat-sub">Wizard-Modus</div>
          </div>
        </div>
      </div>

      <div class="oc-step-tabs">
        <button type="button" class="oc-step-tab active" data-step="analysis"><span>1</span> Anfrage</button>
        <button type="button" class="oc-step-tab" data-step="templates"><span>2</span> Vorlagen</button>
        <button type="button" class="oc-step-tab" data-step="editor"><span>3</span> Editor-Vorschau</button>
      </div>

      <section class="oc-step active oc-animate" id="step-analysis">
        <div class="oc-panel">
          <div class="oc-panel-head">
            <div>
              <div class="oc-panel-kicker">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 21l-6-6"/><circle cx="10" cy="10" r="7"/>
                </svg>
                Anfrage & Kundenzuordnung
              </div>
              <h2 class="oc-panel-title">1. Anfrage & Kundendaten</h2>
              <div class="oc-panel-sub">
                Kunden über deine vorhandene Wizard-Route suchen. Das Gewerk kommt direkt aus `article_groups` per Select2.
              </div>
            </div>

            <div class="oc-inline-actions">
              <button type="button" class="oc-btn-soft blue" id="btn-create-basic-offer">Basis-Angebot erstellen</button>
              <button type="button" class="oc-btn dark" id="btn-analyze-request">Auswerten & Weg empfehlen</button>
            </div>
          </div>

          <div class="oc-form-grid">
            <div class="oc-col-6">
              <label class="oc-label">Kunde suchen</label>
              <div class="oc-suggest-wrap">
                <input type="search" id="customerSearch" class="oc-input" placeholder="Name, Firma, Kundennummer, E-Mail, Ort..." autocomplete="off">
                <div class="oc-suggest" id="customerSuggest"></div>
              </div>
              <input type="hidden" id="customer_id">
              <div class="oc-help">Tippe mindestens 2 Zeichen, um Kunden vorzuschlagen.</div>
            </div>

            <div class="oc-col-3">
              <label class="oc-label">Objekt / Alternative</label>
              <select id="alternative_id" class="oc-select">
                <option value="">Bitte zuerst Kunde wählen</option>
              </select>
            </div>

            <div class="oc-col-3">
              <label class="oc-label">Dokumenttyp</label>
              <select id="doc_type" class="oc-select">
                <option value="Angebot">Angebot</option>
                <option value="Kostenvoranschlag">Kostenvoranschlag</option>
              </select>
            </div>

            <div class="oc-col-4">
              <label class="oc-label">Gewerk / Article Group</label>
              <select id="article_group_id" class="oc-select js-select2-gewerk">
                <option value="">Gewerk auswählen</option>
                @foreach($articleGroups as $group)
                      <option value="{{ $group->id }}" data-initial="{{ $group->initial }}">
                        {{ $group->article_group }}{{ $group->initial ? ' • ' . $group->initial : '' }}
                      </option>
                @endforeach
              </select>
              <div class="oc-help">Select2 Dropdown aus `article_groups`.</div>
            </div>

            <div class="oc-col-4">
              <label class="oc-label">Produkt aus Kundenobjekt</label>
              <select id="product_id" class="oc-select">
                <option value="">Bitte Objekt wählen</option>
              </select>
              <div class="oc-help">Wird bei Kundenauswahl aus `lead_product_lists` geladen.</div>
            </div>

            <div class="oc-col-4">
              <label class="oc-label">Gewünschte Leistung</label>
              <input type="text" id="leistung" class="oc-input-form" placeholder="z.B. PV-Anlage mit Speicher, Wärmepumpe, Montage">
            </div>

            <div class="oc-col-6">
              <label class="oc-label">Zusätzliche Suchbegriffe</label>
              <input type="text" id="extra_query" class="oc-input-form" placeholder="z.B. Schrägdach, Ziegel, Speicher, Arbeitsleistung">
            </div>

            <div class="oc-col-6">
              <label class="oc-label">Datenqualität / Risiko</label>
              <select id="risk_level" class="oc-select">
                <option value="mittel">Mittel - Standardprüfung</option>
                <option value="hoch">Hoch - gute Datenlage</option>
                <option value="niedrig">Niedrig - viele Daten fehlen</option>
              </select>
            </div>

            <div class="oc-col-12">
              <label class="oc-label">Interne Notiz / Risiko</label>
              <textarea id="analysis_note" class="oc-textarea" placeholder="z.B. Zählerschrank unbekannt, Dachform fehlt, Kunde möchte Speicher..."></textarea>
            </div>
          </div>

          <div class="oc-selected-card" id="selectedCustomerCard"></div>

          <div class="oc-recommendation" id="recommendationBox">
            <div class="oc-reco-grid">
              <div>
                <div class="oc-panel-kicker" style="background:var(--purple-light);color:var(--purple);">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M13 2L3 14h8l-1 8 10-12h-8l1-8z"/>
                  </svg>
                  System-Empfehlung
                </div>
                <div class="oc-reco-title">Weg: <span id="recommendationTitle">Aus Vorlage starten</span></div>
                <div class="oc-reco-text" id="recommendationText">
                  Die Anfrage enthält verwertbare Daten. Die Suche prüft Vorlagenname, Beschreibung und gespeicherte Sections.
                </div>
                <div class="oc-reco-next">
                  <span>Nächster Schritt:</span>
                  <span id="recommendationNext">Passende Vorlage auswählen und im Editor übernehmen.</span>
                </div>
              </div>

              <div class="oc-inline-actions">
                <button type="button" class="oc-btn blue" id="btn-reco-open">Empfohlenen Weg öffnen</button>
              </div>
            </div>
          </div>

          <div class="oc-method-title">Alle 4 Wege zur Angebotserstellung</div>

          <div class="oc-method-grid">
            <div class="oc-method-card blue" data-method="vorlage">
              <div class="oc-recommended">Empfohlen</div>
              <div class="oc-method-icon">
                <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 4h16v16H4z"/><path d="M8 8h8M8 12h8M8 16h4"/>
                </svg>
              </div>
              <h3>Aus Vorlage</h3>
              <p>Nutze intelligente Suche für gespeicherte Angebotsvorlagen. Ideal für Standardangebote mit wiederkehrenden Sets.</p>
              <button type="button" class="oc-method-btn">Vorlagen suchen</button>
            </div>

            <div class="oc-method-card green" data-method="planung">
              <div class="oc-recommended">Empfohlen</div>
              <div class="oc-method-icon">
                <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 4h16v16H4z"/><path d="M9 9h6v6H9z"/><path d="M9 1v3M15 1v3M9 20v3M15 20v3M1 9h3M1 15h3M20 9h3M20 15h3"/>
                </svg>
              </div>
              <h3>Aus Planung</h3>
              <p>Fachliche Planung wie PV, Dach oder Wärmepumpe erzeugt später Material, Sets und Arbeitszeiten.</p>
              <button type="button" class="oc-method-btn">Werkzeuge öffnen</button>
            </div>

            <div class="oc-method-card orange" data-method="projekt">
              <div class="oc-recommended">Empfohlen</div>
              <div class="oc-method-icon">
                <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M18 8a3 3 0 10-3-3"/><path d="M6 16a3 3 0 103 3"/><path d="M15 5H9a4 4 0 000 8h6a4 4 0 010 8H9"/>
                </svg>
              </div>
              <h3>Ähnliches Projekt</h3>
              <p>Finde ein ähnliches abgewickeltes Projekt und übernimm die Kalkulationsstruktur als Basis.</p>
              <button type="button" class="oc-method-btn">Projekte abgleichen</button>
            </div>

            <div class="oc-method-card purple" data-method="frei">
              <div class="oc-recommended">Empfohlen</div>
              <div class="oc-method-icon">
                <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4 12.5-12.5z"/>
                </svg>
              </div>
              <h3>Frei erstellen</h3>
              <p>Starte ohne Vorlage mit leerem Editor. Gut für Sonderfälle oder wenn die Datenlage noch unklar ist.</p>
              <button type="button" class="oc-method-btn">Editor leer öffnen</button>
            </div>
          </div>
        </div>
      </section>

      <section class="oc-step oc-animate-right" id="step-templates">
        <div class="oc-main-grid">
          <div>
            <form class="oc-toolbar" id="templateSearchForm">
              <div class="oc-toolbar-left">
                <div class="oc-filter-block search">
                  <label class="oc-filter-label">Template-Suche</label>
                  <input type="text" class="oc-input" id="templateSearch" placeholder="Suche in Vorlagen, Sections, Positionen, Komponenten..." autocomplete="off">
                </div>

                <div class="oc-filter-block">
                  <label class="oc-filter-label">Gewerk</label>
                  <select class="oc-select js-select2-template-gewerk" id="templateArticleGroup">
                    <option value="">Alle Gewerke</option>
                    @foreach($articleGroups as $group)
                          <option value="{{ $group->id }}">{{ $group->article_group }}{{ $group->initial ? ' • ' . $group->initial : '' }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="oc-filter-block">
                  <label class="oc-filter-label">Sortierung</label>
                  <select class="oc-select" id="templateSort">
                    <option value="score">Bester Treffer</option>
                    <option value="usage">Häufig genutzt</option>
                    <option value="items">Mehr Positionen</option>
                    <option value="total">Höchste Summe</option>
                  </select>
                </div>
              </div>

              <div class="oc-toolbar-right">
                <button type="submit" class="oc-btn-soft">Suchen</button>
                <button type="button" class="oc-btn-soft" id="btn-clear-template-search">Zurücksetzen</button>
              </div>
            </form>

            <div class="oc-card">
              <div class="oc-list-head">
                <div>Treffer</div>
                <div>Vorlage</div>
                <div>Gewerk</div>
                <div>Hersteller</div>
                <div>Positionen</div>
                <div>Netto</div>
                <div style="text-align:right;">Aktionen</div>
              </div>

              <div class="oc-list" id="templateResults">
                <div class="oc-empty">Noch keine Suche gestartet. Klicke auf „Auswerten & Weg empfehlen“.</div>
              </div>
            </div>
          </div>

          <aside class="oc-preview-sticky">
            <div class="oc-panel">
              <div class="oc-panel-head">
                <div>
                  <div class="oc-panel-kicker">Vorlage</div>
                  <h3 class="oc-panel-title">Vorschau</h3>
                  <div class="oc-panel-sub">Wähle eine Vorlage aus der Liste.</div>
                </div>
              </div>

              <div id="templatePreview">
                <div class="oc-preview-empty">
                  <div style="font-size:34px;margin-bottom:8px;">🧩</div>
                  Noch keine Vorlage ausgewählt.
                </div>
              </div>

              <div class="oc-inline-actions" style="margin-top:16px;">
                <button type="button" class="oc-btn dark" id="btn-use-template" disabled>Vorlage übernehmen</button>
                <button type="button" class="oc-btn-soft purple" id="btn-free-editor">Frei erstellen</button>
              </div>
            </div>
          </aside>
        </div>
      </section>

      <section class="oc-step oc-animate-right" id="step-editor">
        <div class="oc-main-grid">
          <div>
            <div class="oc-panel">
              <div class="oc-panel-head">
                <div>
                  <div class="oc-panel-kicker" id="editorModeKicker">Gemeinsamer Angebotseditor</div>
                  <h2 class="oc-panel-title" id="editorTitle">ANG-NEU</h2>
                  <div class="oc-panel-sub" id="editorSubtitle">Noch keine Vorlage übernommen.</div>
                </div>

                <div class="oc-inline-actions">
                  <button type="button" class="oc-btn-soft" data-step-jump="templates">Zurück zu Vorlagen</button>
                  <button type="button" class="oc-btn blue" id="btn-create-from-editor">Angebot erstellen</button>
                </div>
              </div>

              <div id="editorSections">
                <div class="oc-empty">Das Angebot ist leer. Füge Sets, Artikel, Lohnleistungen oder Texte hinzu.</div>
              </div>

              <div class="oc-add-box">
                <div class="oc-add-box-title">Weiteres Element hinzufügen</div>
                <div class="oc-add-grid">
                  <button type="button" class="oc-add-btn">
                    <svg viewBox="0 0 24 24" width="25" height="25" fill="none" stroke="#7c3aed" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="M8 8h8M8 12h8M8 16h4"/></svg>
                    Set
                  </button>
                  <button type="button" class="oc-add-btn">
                    <svg viewBox="0 0 24 24" width="25" height="25" fill="none" stroke="#2563eb" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                    Artikel
                  </button>
                  <button type="button" class="oc-add-btn">
                    <svg viewBox="0 0 24 24" width="25" height="25" fill="none" stroke="#f97316" stroke-width="2"><path d="M2 20h20M6 20V8l6-4 6 4v12"/></svg>
                    Lohnleistung
                  </button>
                  <button type="button" class="oc-add-btn">
                    <svg viewBox="0 0 24 24" width="25" height="25" fill="none" stroke="#10b981" stroke-width="2"><path d="M3 7h11v10H3z"/><path d="M14 10h4l3 3v4h-7z"/><circle cx="7" cy="19" r="2"/><circle cx="17" cy="19" r="2"/></svg>
                    Fremdleistung
                  </button>
                  <button type="button" class="oc-add-btn">
                    <svg viewBox="0 0 24 24" width="25" height="25" fill="none" stroke="#6b7280" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h6"/></svg>
                    Textposition
                  </button>
                </div>
              </div>
            </div>
          </div>

          <aside class="oc-preview-sticky">
            <div class="oc-panel">
              <div class="oc-panel-head">
                <div>
                  <div class="oc-panel-kicker">Kalkulation</div>
                  <h3 class="oc-panel-title">Zusammenfassung</h3>
                </div>
              </div>

              <div class="oc-summary-grid">
                <div class="oc-summary-box">
                  <div class="oc-summary-label">Sections</div>
                  <div class="oc-summary-value" id="calcSections">0</div>
                </div>
                <div class="oc-summary-box">
                  <div class="oc-summary-label">Positionen</div>
                  <div class="oc-summary-value" id="calcItems">0</div>
                </div>
                <div class="oc-summary-box">
                  <div class="oc-summary-label">Material</div>
                  <div class="oc-summary-value" id="calcMaterial">0,00 €</div>
                </div>
                <div class="oc-summary-box">
                  <div class="oc-summary-label">Lohn</div>
                  <div class="oc-summary-value" id="calcLabor">0,00 €</div>
                </div>
                <div class="oc-summary-box" style="grid-column:1 / -1;background:var(--blue-light);">
                  <div class="oc-summary-label">Gesamt Netto</div>
                  <div class="oc-summary-value" id="calcTotal">0,00 €</div>
                </div>
              </div>

              <div style="margin-top:16px;">
                <button type="button" class="oc-btn dark" style="width:100%;" id="btn-create-side">Angebot erstellen und öffnen</button>
                <button type="button" class="oc-btn-soft" style="width:100%;margin-top:10px;" id="btn-preview-pdf">Vorschau generieren</button>
              </div>
            </div>
          </aside>
        </div>
      </section>
    </div>

    <div class="oc-modal-backdrop" id="planningModal">
      <div class="oc-modal">
        <div class="oc-modal-h">
          <h3 class="oc-modal-ttl">Planungswerkzeuge</h3>
          <button class="oc-btn-ic" type="button" onclick="closeModal('planningModal')">×</button>
        </div>
        <div class="oc-modal-b">
          <div class="oc-coming">
            <div style="font-size:40px;margin-bottom:10px;">🛠️</div>
            <h3 style="font-weight:950;color:#064e3b;margin-bottom:6px;">Coming soon</h3>
            <p style="color:#047857;font-weight:800;margin:0;">
              Hier können später PV-Planung, Dachplanung, Wärmepumpe und automatische Materialgenerierung verbunden werden.
            </p>
          </div>
        </div>
        <div class="oc-modal-f">
          <button type="button" class="oc-btn-soft" onclick="closeModal('planningModal')">Schließen</button>
        </div>
      </div>
    </div>

    <div class="oc-modal-backdrop" id="confirmCreateModal">
      <div class="oc-modal">
        <div class="oc-modal-h">
          <h3 class="oc-modal-ttl">Angebot erstellen</h3>
          <button class="oc-btn-ic" type="button" onclick="closeModal('confirmCreateModal')">×</button>
        </div>

        <div class="oc-modal-b">
          <p style="font-size:14px;color:#4b5563;font-weight:800;line-height:1.6;margin:0;">
            Es wird ein Basis-Angebot über die vorhandene Route erstellt und danach geöffnet.
          </p>

          <div class="oc-summary-grid">
            <div class="oc-summary-box">
              <div class="oc-summary-label">Kunde</div>
              <div class="oc-summary-value" style="font-size:13px;" id="confirmCustomer">—</div>
            </div>
            <div class="oc-summary-box">
              <div class="oc-summary-label">Objekt</div>
              <div class="oc-summary-value" style="font-size:13px;" id="confirmObject">—</div>
            </div>
            <div class="oc-summary-box">
              <div class="oc-summary-label">Gewerk</div>
              <div class="oc-summary-value" style="font-size:13px;" id="confirmProduct">—</div>
            </div>
            <div class="oc-summary-box">
              <div class="oc-summary-label">Vorlage</div>
              <div class="oc-summary-value" style="font-size:13px;" id="confirmTemplate">—</div>
            </div>
          </div>
        </div>

        <div class="oc-modal-f">
          <button type="button" class="oc-btn-soft" onclick="closeModal('confirmCreateModal')">Abbrechen</button>
          <button type="button" class="oc-btn dark" id="btn-confirm-create">Erstellen</button>
        </div>
      </div>
    </div>

    <div class="oc-toast-wrap" id="toast-wrap"></div>
@endsection

@once
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
        (function () {
          const ROUTES = @json($routes);
          const CSRF = @json(csrf_token());

          const state = {
            customer: null,
            objects: [],
            selectedObject: null,
            selectedProduct: null,
            templates: [],
            selectedTemplate: null,
            selectedTemplateFull: null,
            searchTimers: {},
            recommendedPath: 'vorlage',
            activeMethod: 'vorlage',
          };

          const euro = new Intl.NumberFormat('de-DE', { style:'currency', currency:'EUR' });
          const $id = (id) => document.getElementById(id);

          function escapeHtml(value) {
            return String(value ?? '')
              .replaceAll('&','&amp;')
              .replaceAll('<','&lt;')
              .replaceAll('>','&gt;')
              .replaceAll('"','&quot;')
              .replaceAll("'","&#039;");
          }

          function stripHtml(value) {
            const div = document.createElement('div');
            div.innerHTML = String(value ?? '');
            return div.textContent || div.innerText || '';
          }

          function openModal(id) {
            const el = $id(id);
            if (el) el.classList.add('open');
          }

          function closeModal(id) {
            const el = $id(id);
            if (el) el.classList.remove('open');
          }

          window.closeModal = closeModal;

          function toast(kind, title, msg) {
            const wrap = $id('toast-wrap');
            if (!wrap) return;

            const icons = {
              ok: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path d="M20 6L9 17l-5-5"/></svg>`,
              bad: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path d="M6 18L18 6M6 6l12 12"/></svg>`,
              info: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path d="M12 16v-4M12 8h.01"/><circle cx="12" cy="12" r="10"/></svg>`
            };

            const el = document.createElement('div');
            el.className = 'oc-toast';
            el.innerHTML = `
              <div class="oc-toast-ic ${kind}">${icons[kind] || icons.info}</div>
              <div style="flex:1;">
                <p class="oc-toast-ttl">${escapeHtml(title)}</p>
                <p class="oc-toast-msg">${escapeHtml(msg)}</p>
              </div>
              <button class="oc-toast-x" type="button">×</button>
            `;

            el.querySelector('.oc-toast-x').addEventListener('click', () => el.remove());
            wrap.appendChild(el);
            setTimeout(() => { try { el.remove(); } catch(e) {} }, 4500);
          }

          async function fetchJson(url, options = {}) {
            const response = await fetch(url, {
              headers: {
                Accept:'application/json',
                'X-Requested-With':'XMLHttpRequest',
                ...(options.headers || {}),
              },
              ...options,
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
              throw new Error(data.message || 'Anfrage fehlgeschlagen.');
            }

            return data;
          }

          function setLoadingButton(button, loading, text) {
            if (!button) return;

            if (loading) {
              button.dataset.oldHtml = button.innerHTML;
              button.disabled = true;
              button.innerHTML = `<span class="oc-spinner"></span> ${escapeHtml(text || 'Lädt...')}`;
              return;
            }

            button.disabled = false;
            if (button.dataset.oldHtml) {
              button.innerHTML = button.dataset.oldHtml;
              delete button.dataset.oldHtml;
            }
          }

          function initSelect2() {
            if (!window.jQuery || !jQuery.fn.select2) return;

            jQuery('.js-select2-gewerk').select2({
              width: '100%',
              placeholder: 'Gewerk auswählen',
              allowClear: true,
            });

            jQuery('.js-select2-template-gewerk').select2({
              width: '100%',
              placeholder: 'Alle Gewerke',
              allowClear: true,
            });

            jQuery('#article_group_id').on('change', function () {
              const id = this.value || '';
              if (id) {
                $id('templateArticleGroup').value = id;
                jQuery('#templateArticleGroup').trigger('change.select2');
              }

              const text = this.options[this.selectedIndex]?.textContent?.trim() || '';
              if (text && !$id('leistung').value.trim()) {
                $id('leistung').value = text.split('•')[0].trim();
              }

              renderSelectedCustomerCard();
            });

            jQuery('#templateArticleGroup').on('change', function () {
              clearTimeout(state.searchTimers.template);
              state.searchTimers.template = setTimeout(searchTemplates, 250);
            });
          }

          function setStep(step) {
            document.querySelectorAll('.oc-step').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.oc-step-tab').forEach(el => el.classList.remove('active'));

            const panel = $id('step-' + step);
            if (panel) panel.classList.add('active');

            const tab = document.querySelector(`.oc-step-tab[data-step="${step}"]`);
            if (tab) tab.classList.add('active');

            const label = { analysis:'Analyse', templates:'Vorlage', editor:'Editor' }[step] || step;
            $id('stat-current-path').textContent = label;

            window.scrollTo({ top:0, behavior:'smooth' });
          }

          function selectedArticleGroupId() {
            return parseInt($id('article_group_id').value || $id('product_id').value || 0, 10) || null;
          }

          function selectedAlternativeId() {
            return parseInt($id('alternative_id').value || 0, 10) || null;
          }

          function getSelectedArticleGroupText() {
            const select = $id('article_group_id');
            return select.value ? (select.options[select.selectedIndex]?.textContent || '').trim() : '';
          }

          function buildSmartQuery() {
            const parts = [];

            if (state.customer?.name || state.customer?.display_name) parts.push(state.customer.display_name || state.customer.name);
            if (getSelectedArticleGroupText()) parts.push(getSelectedArticleGroupText());
            if (state.selectedProduct?.label) parts.push(state.selectedProduct.label);
            if ($id('leistung').value.trim()) parts.push($id('leistung').value.trim());
            if ($id('extra_query').value.trim()) parts.push($id('extra_query').value.trim());
            if ($id('analysis_note').value.trim()) parts.push($id('analysis_note').value.trim());

            return parts.join(' ').replace(/\s+/g, ' ').trim();
          }

          function selectMethod(method, jump = true) {
            state.activeMethod = method;

            document.querySelectorAll('.oc-method-card').forEach(card => {
              card.classList.toggle('selected', card.dataset.method === method);
            });

            if (!jump) return;

            if (method === 'vorlage') {
              setStep('templates');
              if (!$id('templateSearch').value.trim()) $id('templateSearch').value = buildSmartQuery();
              searchTemplates();
              return;
            }

            if (method === 'planung') {
              openModal('planningModal');
              return;
            }

            if (method === 'projekt') {
              window.location.href = ROUTES.adminOffers;
              return;
            }

            if (method === 'frei') {
              openFreeEditor();
            }
          }

          function markRecommended(method) {
            state.recommendedPath = method;

            document.querySelectorAll('.oc-method-card').forEach(card => {
              card.classList.toggle('recommended', card.dataset.method === method);
            });

            selectMethod(method, false);
          }

          function renderCustomerSuggest(items) {
            const box = $id('customerSuggest');

            if (!items.length) {
              box.innerHTML = `
                <div class="oc-suggest-item">
                  <div class="oc-suggest-title">Keine Kunden gefunden</div>
                  <div class="oc-suggest-sub">Bitte Suchbegriff ändern.</div>
                </div>
              `;
              box.classList.add('open');
              return;
            }

            box.innerHTML = items.map(item => `
              <div class="oc-suggest-item" data-customer-id="${item.id}">
                <div class="oc-suggest-title">${escapeHtml(item.display_name || item.name || ('#' + item.id))}</div>
                <div class="oc-suggest-sub">
                  ${escapeHtml(item.customer_no || 'Keine Kundennummer')}
                  ${item.street ? ' · ' + escapeHtml(item.street) : ''}
                  ${item.postcode || item.city ? ' · ' + escapeHtml([item.postcode, item.city].filter(Boolean).join(' ')) : ''}
                  ${item.email ? ' · ' + escapeHtml(item.email) : ''}
                </div>
              </div>
            `).join('');

            box.classList.add('open');
          }

          async function searchCustomers(query) {
            if (query.trim().length < 2) {
              $id('customerSuggest').classList.remove('open');
              return;
            }

            const url = new URL(ROUTES.customers, window.location.origin);
            url.searchParams.set('q', query.trim());

            try {
              const data = await fetchJson(url.toString());
              renderCustomerSuggest(data.items || []);
            } catch (error) {
              toast('bad', 'Kundensuche', error.message);
            }
          }

          async function selectCustomer(customerId) {
            try {
              const data = await fetchJson(`${ROUTES.customerShowBase}/${customerId}`);
              state.customer = data.customer || null;

              $id('customer_id').value = state.customer?.id || '';
              $id('customerSearch').value = state.customer?.display_name || state.customer?.name || '';
              $id('customerSuggest').classList.remove('open');

              await loadCustomerObjects(customerId);
              renderSelectedCustomerCard();

              toast('ok', 'Kunde ausgewählt', state.customer?.display_name || 'Kunde wurde geladen.');
            } catch (error) {
              toast('bad', 'Kunde laden', error.message);
            }
          }

          async function loadCustomerObjects(customerId) {
            const data = await fetchJson(`${ROUTES.customerObjectsBase}/${customerId}/objects`);
            state.objects = data.products || [];

            const alternativeSelect = $id('alternative_id');
            const productSelect = $id('product_id');

            const alternatives = [];
            const seen = new Set();

            state.objects.forEach(row => {
              if (!row.alternative_id || seen.has(row.alternative_id)) return;
              seen.add(row.alternative_id);
              alternatives.push({ id: row.alternative_id, label: `Alternative #${row.alternative_id}` });
            });

            if (!alternatives.length) {
              alternativeSelect.innerHTML = `<option value="">Kein Objekt gefunden</option>`;
              productSelect.innerHTML = `<option value="">Kein Produkt/Gewerk gefunden</option>`;
              return;
            }

            alternativeSelect.innerHTML = alternatives.map(alt => `
              <option value="${alt.id}">${escapeHtml(alt.label)}</option>
            `).join('');

            renderProductsForAlternative(alternatives[0].id);
          }

          function renderProductsForAlternative(alternativeId) {
            const productSelect = $id('product_id');

            const products = state.objects.filter(row => String(row.alternative_id || '') === String(alternativeId || ''));

            if (!products.length) {
              productSelect.innerHTML = `<option value="">Kein Produkt/Gewerk gefunden</option>`;
              state.selectedProduct = null;
              renderSelectedCustomerCard();
              return;
            }

            productSelect.innerHTML = products.map(row => `
              <option value="${row.product_id}">
                ${escapeHtml(row.label || row.article_group || ('Produkt #' + row.product_id))}
              </option>
            `).join('');

            state.selectedProduct = products[0] || null;
            state.selectedObject = { id: alternativeId, label: `Alternative #${alternativeId}` };

            if (state.selectedProduct?.product_id) {
              $id('article_group_id').value = String(state.selectedProduct.product_id);
              $id('templateArticleGroup').value = String(state.selectedProduct.product_id);

              if (window.jQuery && jQuery.fn.select2) {
                jQuery('#article_group_id').trigger('change.select2');
                jQuery('#templateArticleGroup').trigger('change.select2');
              }
            }

            if (!$id('leistung').value.trim() && state.selectedProduct?.article_group) {
              $id('leistung').value = state.selectedProduct.article_group;
            }

            renderSelectedCustomerCard();
          }

          function renderSelectedCustomerCard() {
            const card = $id('selectedCustomerCard');

            if (!state.customer) {
              card.classList.remove('open');
              card.innerHTML = '';
              return;
            }

            card.innerHTML = `
              <div class="oc-selected-grid">
                <div>
                  <div class="oc-mini-label">Kunde</div>
                  <div class="oc-mini-value">${escapeHtml(state.customer.display_name || state.customer.name || ('#' + state.customer.id))}</div>
                </div>
                <div>
                  <div class="oc-mini-label">Adresse</div>
                  <div class="oc-mini-value">${escapeHtml([state.customer.street, state.customer.postcode, state.customer.city].filter(Boolean).join(', ') || '—')}</div>
                </div>
                <div>
                  <div class="oc-mini-label">Objekt</div>
                  <div class="oc-mini-value">${escapeHtml(state.selectedObject?.label || ($id('alternative_id').value ? 'Alternative #' + $id('alternative_id').value : '—'))}</div>
                </div>
                <div>
                  <div class="oc-mini-label">Gewerk</div>
                  <div class="oc-mini-value">${escapeHtml(getSelectedArticleGroupText() || state.selectedProduct?.article_group || state.selectedProduct?.label || '—')}</div>
                </div>
              </div>
            `;

            card.classList.add('open');
          }

          async function analyzeRequest() {
            if (!state.customer) {
              toast('bad', 'Kunde fehlt', 'Bitte zuerst einen Kunden auswählen.');
              return;
            }

            if (!selectedAlternativeId()) {
              toast('bad', 'Objekt fehlt', 'Bitte ein Objekt / eine Alternative auswählen.');
              return;
            }

            if (!selectedArticleGroupId()) {
              toast('bad', 'Gewerk fehlt', 'Bitte ein Gewerk auswählen.');
              return;
            }

            const query = buildSmartQuery();
            const risk = $id('risk_level').value;

            let method = 'vorlage';
            let title = 'Aus Vorlage starten';
            let text = 'Die Anfrage enthält verwertbare Daten. Die Suche prüft Vorlagenname, Beschreibung, Sections, Positionen, Komponenten und Lohnleistungen.';
            let next = 'Passende Vorlage auswählen und im Angebotseditor individualisieren.';

            if (risk === 'niedrig' || query.length < 8) {
              method = 'frei';
              title = 'Frei erstellen';
              text = 'Die Datenlage ist zu unklar für eine automatische Vorlage. Starte leer oder ergänze weitere Informationen.';
              next = 'Editor leer öffnen und fehlende Kundendaten manuell ergänzen.';
            } else if ($id('analysis_note').value.toLowerCase().includes('planung') || $id('analysis_note').value.toLowerCase().includes('zähler')) {
              method = 'planung';
              title = 'Aus Planung starten';
              text = 'Die Anfrage enthält technische Risiken. Eine Planung ist sicherer als eine direkte Vorlage.';
              next = 'Planungswerkzeug öffnen und technische Parameter prüfen.';
            }

            markRecommended(method);

            $id('recommendationTitle').textContent = title;
            $id('recommendationText').textContent = text;
            $id('recommendationNext').textContent = next;
            $id('recommendationBox').classList.add('open');

            $id('templateSearch').value = query;
            $id('templateArticleGroup').value = selectedArticleGroupId() || '';

            if (window.jQuery && jQuery.fn.select2) {
              jQuery('#templateArticleGroup').trigger('change.select2');
            }

            if (method === 'vorlage') {
              setStep('templates');
              await searchTemplates();
            }
          }

          async function searchTemplates() {
            const query = $id('templateSearch').value.trim();
            const button = document.querySelector('#templateSearchForm button[type="submit"]');

            setLoadingButton(button, true, 'Sucht...');

            const url = new URL(ROUTES.templates, window.location.origin);
            url.searchParams.set('q', query);

            const articleGroupId = parseInt($id('templateArticleGroup').value || selectedArticleGroupId() || 0, 10);
            if (articleGroupId) url.searchParams.set('article_group_id', articleGroupId);

            try {
              const data = await fetchJson(url.toString());

              state.templates = data.items || [];
              sortTemplates();
              renderTemplateResults();

              const best = state.templates[0]?.match_score || 0;
              $id('stat-template-count').textContent = String(state.templates.length);
              $id('stat-best-score').textContent = `${best}%`;

              if (!state.templates.length) {
                toast('info', 'Keine Vorlage', 'Keine passende Vorlage gefunden. Du kannst frei erstellen.');
              }
            } catch (error) {
              toast('bad', 'Template-Suche', error.message);
              renderTemplateError(error.message);
            } finally {
              setLoadingButton(button, false);
            }
          }

          function sortTemplates() {
            const mode = $id('templateSort').value;

            state.templates.sort((a,b) => {
              if (mode === 'usage') return (b.usage_count || 0) - (a.usage_count || 0);
              if (mode === 'items') return (b.summary?.item_count || 0) - (a.summary?.item_count || 0);
              if (mode === 'total') return (b.summary?.total_net || 0) - (a.summary?.total_net || 0);
              return (b.match_score || 0) - (a.match_score || 0);
            });
          }

          function renderTemplateError(message) {
            $id('templateResults').innerHTML = `<div class="oc-empty" style="color:#b91c1c;">${escapeHtml(message || 'Fehler beim Laden der Vorlagen.')}</div>`;
          }

          function scoreClass(score) {
            if (score >= 85) return 'good';
            if (score >= 55) return 'mid';
            return 'low';
          }

          function renderTemplateResults() {
            const box = $id('templateResults');

            if (!state.templates.length) {
              box.innerHTML = `
                <div class="oc-empty">
                  <div style="font-size:34px;margin-bottom:8px;">🔍</div>
                  Keine passende Vorlage gefunden.
                  <div style="margin-top:14px;">
                    <button type="button" class="oc-btn dark" id="emptyFreeEditor">Frei erstellen</button>
                  </div>
                </div>
              `;

              const btn = $id('emptyFreeEditor');
              if (btn) btn.addEventListener('click', openFreeEditor);
              return;
            }

            box.innerHTML = state.templates.map(template => {
              const summary = template.summary || {};
              const active = state.selectedTemplate?.id === template.id ? 'active' : '';

              const reasons = (template.match_reasons || []).slice(0,2).map(reason => `
                <span class="oc-tag green">${escapeHtml(reason)}</span>
              `).join('');

              return `
                <div class="oc-item ${active}" data-template-id="${template.id}">
                  <div class="oc-item-row">
                    <div class="oc-cell">
                      <div class="oc-cell-title">Treffer</div>
                      <span class="oc-score-badge ${scoreClass(template.match_score || 0)}">
                        <span class="oc-score-label">Match</span>
                        <span class="oc-score-value">${template.match_score || 0}%</span>
                      </span>
                    </div>

                    <div class="oc-cell">
                      <div class="oc-cell-title">Vorlage</div>
                      <div class="oc-main">
                        <div class="oc-ttl">${escapeHtml(template.name || 'Ohne Titel')}</div>
                        <div class="oc-subt">${escapeHtml(template.description || template.company_name || 'Keine Beschreibung')}</div>
                        <div class="oc-tags">
                          ${template.is_favorite ? '<span class="oc-tag orange">Favorit</span>' : ''}
                          ${template.has_stamp ? '<span class="oc-tag purple">Gestempelt</span>' : ''}
                          ${reasons}
                        </div>
                      </div>
                    </div>

                    <div class="oc-cell">
                      <div class="oc-cell-title">Gewerk</div>
                      <div class="oc-ttl" style="font-size:14px;">${escapeHtml(template.article_group_name || '—')}</div>
                    </div>

                    <div class="oc-cell">
                      <div class="oc-cell-title">Hersteller</div>
                      <div class="oc-ttl" style="font-size:14px;">${escapeHtml(template.brand_name || '—')}</div>
                      <div class="oc-subt">${escapeHtml(template.distributor_name || '')}</div>
                    </div>

                    <div class="oc-cell">
                      <div class="oc-cell-title">Positionen</div>
                      <span class="oc-status-pill blue">${summary.item_count || 0} Pos.</span>
                    </div>

                    <div class="oc-cell">
                      <div class="oc-cell-title">Netto</div>
                      <div class="oc-ttl" style="font-size:14px;">${euro.format(summary.total_net || 0)}</div>
                    </div>

                    <div class="oc-cell">
                      <div class="oc-cell-title">Aktionen</div>
                      <div class="oc-actions">
                        <button type="button" class="oc-btn-ic blue js-load-template" title="Vorschau" data-template-id="${template.id}">
                          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>
                          </svg>
                        </button>

                        <button type="button" class="oc-btn-ic success js-use-template-list" title="Übernehmen" data-template-id="${template.id}">
                          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 6L9 17l-5-5"/>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              `;
            }).join('');
          }

          async function loadTemplate(templateId) {
            try {
              const data = await fetchJson(`${ROUTES.templateShowBase}/${templateId}`);

              state.selectedTemplateFull = data.template || null;
              state.selectedTemplate = state.templates.find(item => String(item.id) === String(templateId)) || state.selectedTemplateFull;

              $id('stat-selected-template').textContent = state.selectedTemplateFull?.id ? `#${state.selectedTemplateFull.id}` : '—';

              renderTemplateResults();
              renderTemplatePreview();

              $id('btn-use-template').disabled = false;
              toast('ok', 'Vorlage geladen', state.selectedTemplateFull?.name || 'Vorlage wurde geladen.');
            } catch (error) {
              toast('bad', 'Vorlage laden', error.message);
            }
          }

          function renderTemplatePreview() {
            const template = state.selectedTemplateFull;
            const box = $id('templatePreview');

            if (!template) {
              box.innerHTML = `<div class="oc-preview-empty">Noch keine Vorlage ausgewählt.</div>`;
              return;
            }

            const summary = template.summary || {};
            const sections = template.sections || [];

            box.innerHTML = `
              <div>
                <div class="oc-preview-title">${escapeHtml(template.name || 'Vorlage')}</div>
                <div class="oc-subt">${escapeHtml(template.description || template.company_name || '')}</div>

                <div class="oc-tags">
                  <span class="oc-tag blue">${escapeHtml(template.article_group_name || 'Keine Gruppe')}</span>
                  <span class="oc-tag">${escapeHtml(template.brand_name || 'Keine Marke')}</span>
                  <span class="oc-tag purple">${escapeHtml(template.distributor_name || 'Kein Lieferant')}</span>
                </div>

                <div class="oc-summary-grid">
                  <div class="oc-summary-box"><div class="oc-summary-label">Sections</div><div class="oc-summary-value">${summary.section_count || 0}</div></div>
                  <div class="oc-summary-box"><div class="oc-summary-label">Positionen</div><div class="oc-summary-value">${summary.item_count || 0}</div></div>
                  <div class="oc-summary-box"><div class="oc-summary-label">Material</div><div class="oc-summary-value">${euro.format(summary.material_net || 0)}</div></div>
                  <div class="oc-summary-box"><div class="oc-summary-label">Lohn</div><div class="oc-summary-value">${euro.format(summary.labor_net || 0)}</div></div>
                  <div class="oc-summary-box" style="grid-column:1 / -1;background:var(--blue-light);">
                    <div class="oc-summary-label">Gesamt Netto</div>
                    <div class="oc-summary-value">${euro.format(summary.total_net || 0)}</div>
                  </div>
                </div>

                <div style="margin-top:16px;">
                  ${sections.slice(0,4).map(section => `
                    <div class="oc-section-card">
                      <div class="oc-section-head">
                        <div class="oc-section-title">${escapeHtml(section.title || 'Sektion')}</div>
                        <div class="oc-section-count">${(section.items || []).length} Pos.</div>
                      </div>
                    </div>
                  `).join('')}
                </div>
              </div>
            `;
          }

          async function useTemplate(templateId = null) {
            if (templateId && (!state.selectedTemplateFull || String(state.selectedTemplateFull.id) !== String(templateId))) {
              await loadTemplate(templateId);
            }

            if (!state.selectedTemplateFull) {
              toast('bad', 'Keine Vorlage', 'Bitte zuerst eine Vorlage auswählen.');
              return;
            }

            renderEditorFromTemplate(state.selectedTemplateFull);
            setStep('editor');
          }

          function renderEditorFromTemplate(template) {
            const summary = template.summary || {};

            $id('editorModeKicker').textContent = 'Vorlage übernommen';
            $id('editorTitle').textContent = 'ANG-NEU';
            $id('editorSubtitle').textContent = `Aus Vorlage: ${template.name || 'Vorlage'}`;

            $id('calcSections').textContent = summary.section_count || 0;
            $id('calcItems').textContent = summary.item_count || 0;
            $id('calcMaterial').textContent = euro.format(summary.material_net || 0);
            $id('calcLabor').textContent = euro.format(summary.labor_net || 0);
            $id('calcTotal').textContent = euro.format(summary.total_net || 0);

            const sections = template.sections || [];

            if (!sections.length) {
              $id('editorSections').innerHTML = `<div class="oc-empty">Diese Vorlage enthält keine Sections.</div>`;
              return;
            }

            $id('editorSections').innerHTML = sections.map((section,index) => `
              <div class="oc-section-card">
                <div class="oc-section-head">
                  <div>
                    <div class="oc-section-count">Sektion ${index + 1}</div>
                    <div class="oc-section-title">${escapeHtml(section.title || 'Ohne Titel')}</div>
                  </div>
                  <div class="oc-status-pill blue">${(section.items || []).length} Positionen</div>
                </div>
                <div>
                  ${(section.items || []).map(item => renderEditorNode(item,0)).join('')}
                </div>
              </div>
            `).join('');
          }

          function renderEditorNode(node, depth) {
            const qty = Number(node.qty || 1);
            const price = Number(node.price ?? node.rate ?? 0);
            const total = Number(node.total ?? (qty * price));
            const isLabor = node.kind === 'labor' || String(node.item_type || '').includes('labor');
            const children = (node.subItems || []).map(child => renderEditorNode(child, depth + 1)).join('');

            return `
              <div class="oc-node ${depth > 0 ? 'child' : ''}">
                <div class="oc-node-title">
                  <span>${isLabor ? '👷' : '📦'} ${escapeHtml(node.name || 'Position')}</span>
                  <span>${euro.format(total)}</span>
                </div>
                <div class="oc-node-meta">
                  ${qty} ${escapeHtml(node.unit || node.measure || '')}
                  ${price ? ' · EP ' + euro.format(price) : ''}
                  ${node.article_no ? ' · Art.-Nr. ' + escapeHtml(node.article_no) : ''}
                  ${node.distributor_article_no ? ' · Lief.-Nr. ' + escapeHtml(node.distributor_article_no) : ''}
                </div>
                ${node.desc || node.desc_html ? `<div class="oc-node-meta">${escapeHtml(stripHtml(node.desc || node.desc_html)).slice(0,220)}</div>` : ''}
                ${children ? `<div style="margin-top:8px;">${children}</div>` : ''}
              </div>
            `;
          }

          function openFreeEditor() {
            state.selectedTemplate = null;
            state.selectedTemplateFull = null;

            $id('stat-selected-template').textContent = '—';
            $id('editorModeKicker').textContent = 'Frei erstellt';
            $id('editorTitle').textContent = 'ANG-NEU';
            $id('editorSubtitle').textContent = 'Freies Angebot ohne Vorlage.';

            $id('calcSections').textContent = '0';
            $id('calcItems').textContent = '0';
            $id('calcMaterial').textContent = euro.format(0);
            $id('calcLabor').textContent = euro.format(0);
            $id('calcTotal').textContent = euro.format(0);

            $id('editorSections').innerHTML = `<div class="oc-empty">Das Angebot ist leer. Füge Sets, Artikel, Lohnleistungen oder Texte hinzu.</div>`;

            markRecommended('frei');
            setStep('editor');
          }

          function fillConfirmCreateModal() {
            $id('confirmCustomer').textContent = state.customer?.display_name || state.customer?.name || '—';
            $id('confirmObject').textContent = state.selectedObject?.label || ($id('alternative_id').value ? `Alternative #${$id('alternative_id').value}` : '—');
            $id('confirmProduct').textContent = getSelectedArticleGroupText() || state.selectedProduct?.article_group || state.selectedProduct?.label || '—';
            $id('confirmTemplate').textContent = state.selectedTemplateFull?.name || 'Keine Vorlage / Frei';
          }

          function validateCreateOfferData() {
            if (!state.customer?.id) {
              toast('bad', 'Kunde fehlt', 'Bitte zuerst einen Kunden auswählen.');
              setStep('analysis');
              return false;
            }

            if (!selectedAlternativeId()) {
              toast('bad', 'Objekt fehlt', 'Bitte ein Objekt / eine Alternative auswählen.');
              setStep('analysis');
              return false;
            }

            if (!selectedArticleGroupId()) {
              toast('bad', 'Gewerk fehlt', 'Bitte ein Gewerk auswählen.');
              setStep('analysis');
              return false;
            }

            return true;
          }

          function askCreateOffer() {
            if (!validateCreateOfferData()) return;
            fillConfirmCreateModal();
            openModal('confirmCreateModal');
          }

          async function createBasicOffer() {
            if (!validateCreateOfferData()) return;

            const button = $id('btn-confirm-create');
            setLoadingButton(button, true, 'Erstellt...');

            const payload = {
              customer_id: state.customer.id,
              alternative_id: selectedAlternativeId(),
              doc_type: $id('doc_type').value || 'Angebot',
              project_date: new Date().toISOString().slice(0,10),
              product_ids: [selectedArticleGroupId()],
            };

            try {
              const data = await fetchJson(ROUTES.createOffer, {
                method:'POST',
                headers:{
                  'Content-Type':'application/json',
                  'X-CSRF-TOKEN':CSRF,
                },
                body:JSON.stringify(payload),
              });

              toast('ok', 'Angebot erstellt', 'Das Angebot wurde erstellt und wird geöffnet.');

              if (data.redirect) {
                window.location.href = data.redirect;
                return;
              }

              if (data.offer_id) {
                window.location.href = `/offers/${data.offer_id}`;
                return;
              }

              closeModal('confirmCreateModal');
            } catch (error) {
              toast('bad', 'Angebot erstellen', error.message);
            } finally {
              setLoadingButton(button, false);
            }
          }

          function resetWizard() {
            state.customer = null;
            state.objects = [];
            state.selectedObject = null;
            state.selectedProduct = null;
            state.templates = [];
            state.selectedTemplate = null;
            state.selectedTemplateFull = null;

            $id('customerSearch').value = '';
            $id('customer_id').value = '';
            $id('alternative_id').innerHTML = `<option value="">Bitte zuerst Kunde wählen</option>`;
            $id('product_id').innerHTML = `<option value="">Bitte Objekt wählen</option>`;
            $id('article_group_id').value = '';
            $id('templateArticleGroup').value = '';
            $id('leistung').value = '';
            $id('extra_query').value = '';
            $id('analysis_note').value = '';
            $id('risk_level').value = 'mittel';
            $id('templateSearch').value = '';

            if (window.jQuery && jQuery.fn.select2) {
              jQuery('#article_group_id').trigger('change.select2');
              jQuery('#templateArticleGroup').trigger('change.select2');
            }

            $id('selectedCustomerCard').classList.remove('open');
            $id('selectedCustomerCard').innerHTML = '';
            $id('recommendationBox').classList.remove('open');

            $id('templateResults').innerHTML = `<div class="oc-empty">Noch keine Suche gestartet. Klicke auf „Auswerten & Weg empfehlen“.</div>`;
            $id('templatePreview').innerHTML = `<div class="oc-preview-empty"><div style="font-size:34px;margin-bottom:8px;">🧩</div>Noch keine Vorlage ausgewählt.</div>`;

            $id('btn-use-template').disabled = true;
            $id('stat-template-count').textContent = '0';
            $id('stat-best-score').textContent = '0%';
            $id('stat-selected-template').textContent = '—';

            markRecommended('vorlage');
            setStep('analysis');
          }

          document.addEventListener('click', function (event) {
            if (event.target.classList.contains('oc-modal-backdrop')) {
              event.target.classList.remove('open');
            }

            const reset = event.target.closest('[data-reset-wizard]');
            if (reset) {
              resetWizard();
              return;
            }

            const stepTab = event.target.closest('.oc-step-tab[data-step]');
            if (stepTab) {
              setStep(stepTab.dataset.step);
              return;
            }

            const jump = event.target.closest('[data-step-jump]');
            if (jump) {
              setStep(jump.dataset.stepJump);
              return;
            }

            const methodCard = event.target.closest('.oc-method-card[data-method]');
            if (methodCard) {
              selectMethod(methodCard.dataset.method, true);
              return;
            }

            const customerItem = event.target.closest('.oc-suggest-item[data-customer-id]');
            if (customerItem) {
              selectCustomer(customerItem.dataset.customerId);
              return;
            }

            const loadTemplateBtn = event.target.closest('.js-load-template');
            if (loadTemplateBtn) {
              loadTemplate(loadTemplateBtn.dataset.templateId);
              return;
            }

            const useTemplateBtn = event.target.closest('.js-use-template-list');
            if (useTemplateBtn) {
              useTemplate(useTemplateBtn.dataset.templateId);
              return;
            }
          });

          document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
              document.querySelectorAll('.oc-modal-backdrop.open').forEach(el => el.classList.remove('open'));
              $id('customerSuggest').classList.remove('open');
            }
          });

          $id('customerSearch').addEventListener('input', function () {
            clearTimeout(state.searchTimers.customer);
            state.searchTimers.customer = setTimeout(() => searchCustomers(this.value), 280);
          });

          $id('alternative_id').addEventListener('change', function () {
            renderProductsForAlternative(this.value);
          });

          $id('product_id').addEventListener('change', function () {
            const selected = state.objects.find(row => String(row.product_id) === String(this.value));
            state.selectedProduct = selected || null;

            if (state.selectedProduct?.product_id) {
              $id('article_group_id').value = String(state.selectedProduct.product_id);
              $id('templateArticleGroup').value = String(state.selectedProduct.product_id);

              if (window.jQuery && jQuery.fn.select2) {
                jQuery('#article_group_id').trigger('change.select2');
                jQuery('#templateArticleGroup').trigger('change.select2');
              }
            }

            if (state.selectedProduct?.article_group && !$id('leistung').value.trim()) {
              $id('leistung').value = state.selectedProduct.article_group;
            }

            renderSelectedCustomerCard();
          });

          $id('btn-analyze-request').addEventListener('click', analyzeRequest);
          $id('btn-reco-open').addEventListener('click', () => selectMethod(state.recommendedPath, true));

          $id('templateSearchForm').addEventListener('submit', function (event) {
            event.preventDefault();
            searchTemplates();
          });

          $id('templateSearch').addEventListener('input', function () {
            clearTimeout(state.searchTimers.template);
            state.searchTimers.template = setTimeout(searchTemplates, 420);
          });

          $id('templateSort').addEventListener('change', function () {
            sortTemplates();
            renderTemplateResults();
          });

          $id('btn-clear-template-search').addEventListener('click', function () {
            $id('templateSearch').value = '';
            searchTemplates();
          });

          $id('btn-use-template').addEventListener('click', function () {
            useTemplate();
          });

          $id('btn-free-editor').addEventListener('click', openFreeEditor);
          $id('btn-create-basic-offer').addEventListener('click', askCreateOffer);
          $id('btn-create-from-editor').addEventListener('click', askCreateOffer);
          $id('btn-create-side').addEventListener('click', askCreateOffer);
          $id('btn-confirm-create').addEventListener('click', createBasicOffer);

          $id('btn-preview-pdf').addEventListener('click', function () {
            toast('info', 'Vorschau', 'PDF-Vorschau kann später mit deinem bestehenden Print-System verbunden werden.');
          });

          initSelect2();
          resetWizard();
        })();
        </script>
    @endpush
@endonce

@push('scripts')
  <script>
    window.GlobalBreadcrumbs = [
      {
        label: 'Dashboard',
        url: "{{ url('/') }}"
      },
      {
        label: 'Kundenliste',
        url: "{{ url('new_lead_view') }}",
      },
      {
        label: 'Angebotliste',
        url: "{{ url()->current() }}",
        clickable: false
      }
    ];

    if (window.setGlobalBreadcrumbs) {
      window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
    }
  </script>
@endpush