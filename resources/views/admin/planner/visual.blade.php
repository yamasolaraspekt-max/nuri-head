<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuri Head: Full Architecture Mesh</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- React & ReactDOM -->
    <script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
    <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
    
    <!-- Babel for JSX -->
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        .no-select { user-select: none; -webkit-user-select: none; }
        
        /* Animations */
        @keyframes flowPulse {
            0% { transform: scale(1); opacity: 1; box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1.5); opacity: 0; box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { transform: scale(1); opacity: 0; box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        .packet-pulse { animation: flowPulse 1s infinite; }

        @keyframes dash { to { stroke-dashoffset: -20; } }
        .line-active-green { stroke: #10b981; stroke-width: 3; stroke-dasharray: 6; animation: dash 0.5s linear infinite; opacity: 1 !important; }
        .line-active-red   { stroke: #ef4444; stroke-width: 3; stroke-dasharray: 6; animation: dash 0.5s linear infinite; opacity: 1 !important; }
        .line-active-blue  { stroke: #3b82f6; stroke-width: 3; stroke-dasharray: 6; animation: dash 0.5s linear infinite; opacity: 1 !important; }
        .line-active-violet { stroke: #8b5cf6; stroke-width: 3; stroke-dasharray: 6; animation: dash 0.5s linear infinite; opacity: 1 !important; }
        .line-selected     { stroke: #3b82f6; stroke-width: 3; opacity: 1 !important; }
        
        /* Node Highlight Animations */
        .animate-pulse-green { border-color: #10b981; box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.2); transition: all 0.2s; }
        .animate-pulse-red   { border-color: #ef4444; box-shadow: 0 0 0 6px rgba(239, 68, 68, 0.2); transition: all 0.2s; }
        .animate-pulse-blue  { border-color: #3b82f6; box-shadow: 0 0 0 6px rgba(59, 130, 246, 0.2); transition: all 0.2s; }
        .animate-pulse-violet { border-color: #8b5cf6; box-shadow: 0 0 0 6px rgba(139, 92, 246, 0.2); transition: all 0.2s; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 overflow-hidden">
    <div id="root"></div>

    <script type="text/babel">
        const { useState, useRef, useEffect, useMemo, useCallback } = React;

        // --- ICONS ---
        const IconWrapper = ({ children, size = 18, className = "" }) => (
            <svg xmlns="http://www.w3.org/2000/svg" width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={className}>
                {children}
            </svg>
        );

        const Icons = {
            Database: (p) => <IconWrapper {...p}><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></IconWrapper>,
            Globe: (p) => <IconWrapper {...p}><circle cx="12" cy="12" r="10"/><line x1="2" x2="22" y1="12" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></IconWrapper>,
            Server: (p) => <IconWrapper {...p}><rect width="20" height="8" x="2" y="2" rx="2" ry="2"/><rect width="20" height="8" x="2" y="14" rx="2" ry="2"/><line x1="6" x2="6.01" y1="6" y2="6"/><line x1="6" x2="6.01" y1="18" y2="18"/></IconWrapper>,
            Code: (p) => <IconWrapper {...p}><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></IconWrapper>,
            Table: (p) => <IconWrapper {...p}><path d="M12 3v18"/><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/></IconWrapper>,
            Zap: (p) => <IconWrapper {...p}><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></IconWrapper>,
            Play: (p) => <IconWrapper {...p}><polygon points="5 3 19 12 5 21 5 3"/></IconWrapper>,
            Search: (p) => <IconWrapper {...p}><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></IconWrapper>,
            X: (p) => <IconWrapper {...p}><path d="M18 6 6 18"/><path d="m6 6 12 12"/></IconWrapper>,
            Maximize2: (p) => <IconWrapper {...p}><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" x2="14" y1="3" y2="10"/><line x1="3" x2="10" y1="21" y2="14"/></IconWrapper>,
            Info: (p) => <IconWrapper {...p}><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></IconWrapper>,
            Move: (p) => <IconWrapper {...p}><polyline points="5 9 2 12 5 15"/><polyline points="9 5 12 2 15 5"/><polyline points="15 19 12 22 9 19"/><polyline points="19 9 22 12 19 15"/><line x1="2" x2="22" y1="12" y2="12"/><line x1="12" x2="12" y1="2" y2="22"/></IconWrapper>,
            ZoomIn: (p) => <IconWrapper {...p}><circle cx="11" cy="11" r="8"/><line x1="21" x2="16.65" y1="21" y2="16.65"/><line x1="11" x2="11" y1="8" y2="14"/><line x1="8" x2="14" y1="11" y2="11"/></IconWrapper>,
            ZoomOut: (p) => <IconWrapper {...p}><circle cx="11" cy="11" r="8"/><line x1="21" x2="16.65" y1="21" y2="16.65"/><line x1="8" x2="14" y1="11" y2="11"/></IconWrapper>,
            FileCode: (p) => <IconWrapper {...p}><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="m9 13 3 3 3-3"/></IconWrapper>,
            Mobile: (p) => <IconWrapper {...p}><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/></IconWrapper>,
            HardDrive: (p) => <IconWrapper {...p}><line x1="22" x2="2" y1="12" y2="12"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/><line x1="6" x2="6.01" y1="16" y2="16"/><line x1="10" x2="10.01" y1="16" y2="16"/></IconWrapper>,
            Lock: (p) => <IconWrapper {...p}><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></IconWrapper>,
            Shield: (p) => <IconWrapper {...p}><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></IconWrapper>,
            Skull: (p) => <IconWrapper {...p}><circle cx="9" cy="12" r="1"/><circle cx="15" cy="12" r="1"/><path d="M8 20v2h8v-2"/><path d="m12.5 17-.5-1-.5 1h1z"/><path d="M16 20a2 2 0 0 0 1.56-3.25 8 8 0 1 0-11.12 0A2 2 0 0 0 8 20"/></IconWrapper>,
            Upload: (p) => <IconWrapper {...p}><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></IconWrapper>,
            Download: (p) => <IconWrapper {...p}><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></IconWrapper>,
            Trash: (p) => <IconWrapper {...p}><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></IconWrapper>,
            Link: (p) => <IconWrapper {...p}><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></IconWrapper>,
            Copy: (p) => <IconWrapper {...p}><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></IconWrapper>,
            Check: (p) => <IconWrapper {...p}><polyline points="20 6 9 17 4 12"/></IconWrapper>,
        };

        // --- DOMAINS & ZONES ---
        const domains = {
            MOBILE: { title: "Mobile Ecosystem", bg: "bg-pink-50/50", border: "border-pink-200" },
            WEB:    { title: "Website Ecosystem", bg: "bg-slate-50/50", border: "border-slate-200" },
            SEC:    { title: "Security Layer", bg: "bg-red-50/50", border: "border-red-200" }
        };

        const categories = {
            MOBILE_VIEW: { color: '#db2777', label: 'Mobile View', icon: Icons.Mobile, domain: 'MOBILE' },
            MOBILE_DATA: { color: '#be185d', label: 'Local Cache', icon: Icons.HardDrive, domain: 'MOBILE' },
            WEB_VIEW:    { color: '#f59e0b', label: 'Blade View', icon: Icons.Code, domain: 'WEB' },
            ROUTE:       { color: '#10b981', label: 'API Route', icon: Icons.Globe, domain: 'WEB' },
            SECURITY:    { color: '#ef4444', label: 'Security', icon: Icons.Lock, domain: 'SEC' },
            CONTROLLER:  { color: '#8b5cf6', label: 'Controller', icon: Icons.Server, domain: 'WEB' },
            MODEL:       { color: '#3b82f6', label: 'Model', icon: Icons.FileCode, domain: 'WEB' },
            MIGRATION:   { color: '#0ea5e9', label: 'DB Table', icon: Icons.Table, domain: 'WEB' },
            SERVICE:     { color: '#6366f1', label: 'Service', icon: Icons.Zap, domain: 'WEB' },
            ATTACKER:    { color: '#000000', label: 'Attacker', icon: Icons.Skull, domain: 'EXTERNAL' },
        };

        // --- FULL ARCHITECTURE DEFINITION ---
        const architecture = [
            // --- TOP: MOBILE ECOSYSTEM ---
            { id: 'mob_login', label: 'Login (PIN)', category: 'MOBILE_VIEW', x: 100, y: 100 },
            { id: 'mob_dash', label: 'Dashboard', category: 'MOBILE_VIEW', x: 350, y: 100 },
            { id: 'mob_active', label: 'Active Mode', category: 'MOBILE_VIEW', x: 600, y: 100 },
            { id: 'mob_store', label: 'Local Storage', category: 'MOBILE_DATA', details: 'wf_tasks, wf_attendance_log', x: 300, y: 250 },
            
            // --- MIDDLE: SECURITY & API ---
            { id: 'api_sync', label: 'POST /api/sync', category: 'ROUTE', x: 300, y: 450 },
            { id: 'api_dnd', label: 'POST /planner/dnd', category: 'ROUTE', x: 800, y: 450 },
            { id: 'api_wizard', label: 'POST /store-wizard', category: 'ROUTE', x: 1050, y: 450 },
            { id: 'api_play', label: 'POST /item/play', category: 'ROUTE', x: 1300, y: 450 },
            { id: 'svc_pusher', label: 'Pusher (Realtime)', category: 'SERVICE', x: 1200, y: 350 },

            // Security
            { id: 'sec_sanctum', label: 'Sanctum Auth', category: 'SECURITY', details: 'Validates Bearer Token', x: 550, y: 580 },
            { id: 'sec_csrf', label: 'CSRF Protection', category: 'SECURITY', details: 'Validates _token', x: 900, y: 580 },
            { id: 'sec_policy', label: 'Policy Gate', category: 'SECURITY', details: 'PlannerPolicy', x: 1200, y: 580 },

            // --- BOTTOM: BACKEND ---
            { id: 'web_planner', label: 'Planner UI', category: 'WEB_VIEW', x: 1050, y: 100 },

            // Controllers
            { id: 'ctl_planner', label: 'PlannerPlanController', category: 'CONTROLLER', x: 700, y: 750 },
            { id: 'ctl_state', label: 'ItemStateController', category: 'CONTROLLER', x: 950, y: 750 },

            // --- DATABASE: CRM ---
            { id: 'mod_lead', label: 'NewLead', category: 'MODEL', x: 50, y: 900 },
            { id: 'tab_lead', label: 'new_leads', category: 'MIGRATION', columns: ['id', 'customer_type', 'customer_no', 'title', 'academic_title', 'firma', 'lastname', 'name', 'full_address', 'street', 'latitude', 'longitude', 'polygon_height', 'polygon_width', 'polygon_area', 'elevation', 'postcode', 'city', 'phone', 'telephone', 'email', 'source', 'contact_person', 'branch', 'interest_rating', 'seriousness_rating', 'price_information', 'status', 'status_msg', 'info', 'purchase_status', 'total_purchase', 'default_project_minutes', 'purchase_date'], x: 50, y: 1050 },
            
            { id: 'mod_alt', label: 'LeadAlternative', category: 'MODEL', x: 250, y: 900 },
            { id: 'tab_alt', label: 'lead_alternative_adds', category: 'MIGRATION', columns: ['id', 'lead_id', 'full_address', 'street', 'postcode', 'city', 'lat', 'lon', 'elevation', 'main', 'address_no', 'object_name', 'request_date', 'periority', 'document', 'note', 'appointment', 'appointment_by', 'objective', 'living_space', 'unusable_space', 'number_people', 'number_we', 'number_stories', 'installation_location', 'annual_consumption', 'roof_type', 'heating_system_type', 'electric_car', 'status', 'stage', 'project_date', 'object_type', 'building_condition', 'owner_count', 'income_taxed', 'investment_costs', 'calculated_subsidy', 'solar_module_kwp', 'battery_kwh'], x: 250, y: 1050 },

            { id: 'mod_lpl', label: 'LeadProductList', category: 'MODEL', x: 450, y: 900 },
            { id: 'tab_lpl', label: 'lead_product_lists', category: 'MIGRATION', columns: ['id', 'customer_id', 'alternative_id', 'product_id', 'service_id', 'department_id', 'employee_id', 'field_employee', 'teams', 'service', 'status', 'work_status', 'interest', 'realization_time', 'stage_history', 'stage', 'price', 'project_minutes'], x: 450, y: 1050 },

            { id: 'mod_art', label: 'ArticleGroup', category: 'MODEL', x: 650, y: 900 },
            { id: 'tab_art', label: 'article_groups', category: 'MIGRATION', columns: ['id', 'article_group', 'initial', 'min_value', 'max_value', 'image'], x: 650, y: 1050 },

            // --- DATABASE: CONFIG ---
            { id: 'tab_stages', label: 'stages', category: 'MIGRATION', columns: ['id', 'stage', 'product_id', 'version', 'status', 'sort_order', 'default'], x: 50, y: 1250 },
            { id: 'tab_psect', label: 'phase_sections', category: 'MIGRATION', columns: ['id', 'product_id', 'phase_section', 'status'], x: 250, y: 1250 },
            { id: 'tab_tphase', label: 'task_phases', category: 'MIGRATION', columns: ['id', 'product_id', 'section_id', 'section_name', 'phase_name', 'stage', 'stage_id', 'version', 'status', 'order'], x: 450, y: 1250 },
            { id: 'tab_pacts', label: 'phase_activities', category: 'MIGRATION', columns: ['id', 'phase_id', 'product_id', 'section_id', 'parent_id', 'copy_from', 'stage_id', 'title', 'duration', 'description', 'notes', 'status', 'priority', 'percent', 'usage_count'], x: 650, y: 1250 },

            // --- DATABASE: OPS ---
            { id: 'mod_appt', label: 'MainAppointment', category: 'MODEL', x: 850, y: 900 },
            { id: 'tab_appt', label: 'main_appointments', category: 'MIGRATION', columns: ['id', 'created_by', 'name', 'execution_type', 'appointment_type', 'start_date', 'end_date', 'start_time', 'end_time', 'full_address', 'lat/long', 'customer_id', 'products', 'task_id', 'problem_id', 'contact_id', 'is_report', 'report_by'], x: 850, y: 1050 },

            { id: 'mod_ptask', label: 'PersonalTask', category: 'MODEL', x: 1050, y: 900 },
            { id: 'tab_ptask', label: 'personal_tasks', category: 'MIGRATION', columns: ['id', 'customer_id', 'alternative_id', 'product_id', 'is_customer', 'task_title', 'description', 'assigned_by', 'task_status', 'priority', 'due_date', 'due_time', 'controller_id'], x: 1050, y: 1050 },

            { id: 'mod_prob', label: 'Problem', category: 'MODEL', x: 1250, y: 900 },
            { id: 'tab_prob', label: 'problems', category: 'MIGRATION', columns: ['id', 'ticket_no', 'error_code', 'customer_id', 'product_id', 'responsible', 'problem', 'solution', 'progress', 'status', 'priority'], x: 1250, y: 1050 },

            { id: 'mod_ttask', label: 'TicketTask', category: 'MODEL', x: 1450, y: 900 },
            { id: 'tab_ttask', label: 'ticket_tasks', category: 'MIGRATION', columns: ['id', 'ticket_id', 'employee_id', 'title', 'status', 'solution', 'is_done'], x: 1450, y: 1050 },

            // --- DATABASE: PLANNER CORE ---
            { id: 'mod_plan', label: 'PlannerPlan', category: 'MODEL', x: 1650, y: 900 },
            { id: 'tab_plan', label: 'planner_plans', category: 'MIGRATION', columns: ['id', 'account_id', 'customer_id', 'project_id', 'stage', 'title', 'status', 'created_by', 'published_at', 'meta'], x: 1650, y: 1050 },

            { id: 'mod_item', label: 'PlannerItem', category: 'MODEL', x: 1850, y: 900 },
            { id: 'tab_item', label: 'planner_items', category: 'MIGRATION', columns: ['id', 'plan_id', 'client_uid', 'source_type', 'source_id', 'title', 'category', 'description', 'duration_minutes', 'status', 'planned_start_at', 'planned_end_at', 'sort_order'], x: 1850, y: 1050 },

            { id: 'tab_pie', label: 'planner_item_employees', category: 'MIGRATION', columns: ['id', 'planner_item_id', 'employee_id', 'role'], x: 2050, y: 1050 },
            { id: 'tab_pid', label: 'planner_item_dependencies', category: 'MIGRATION', columns: ['id', 'planner_item_id', 'depends_on_item_id'], x: 2250, y: 1050 },
            { id: 'tab_pia', label: 'planner_item_assets', category: 'MIGRATION', columns: ['id', 'planner_item_id', 'asset_id', 'qty', 'notes'], x: 2450, y: 1050 },

            // --- DATABASE: HR & ORG ---
            { id: 'mod_emp', label: 'Employee', category: 'MODEL', x: 1650, y: 1250 },
            { id: 'tab_emp', label: 'employees', category: 'MIGRATION', columns: ['id', 'title', 'name', 'lastname', 'branch', 'salary_per_hour', 'qualification_id', 'skill_id', 'supervisor', 'working_type', 'daily_start_time', 'daily_end_time', 'sick_leave', 'leave', 'email', 'phone', 'status'], x: 1650, y: 1350 },

            { id: 'mod_branch', label: 'Branch', category: 'MODEL', x: 1850, y: 1250 },
            { id: 'tab_branch', label: 'branches', category: 'MIGRATION', columns: ['id', 'branch', 'color', 'chairman', 'street', 'city', 'country', 'status'], x: 1850, y: 1350 },

            { id: 'mod_dept', label: 'Department', category: 'MODEL', x: 2050, y: 1250 },
            { id: 'tab_dept', label: 'departments', category: 'MIGRATION', columns: ['id', 'department_name', 'parent_id', 'branch_id', 'department_head'], x: 2050, y: 1350 },

            { id: 'mod_asset', label: 'Asset', category: 'MODEL', x: 2250, y: 1250 },
            { id: 'tab_asset', label: 'assets', category: 'MIGRATION', columns: ['id', 'serial_no', 'item', 'model', 'category', 'parent_id', 'purchase_price', 'leasing_from', 'location', 'status', 'handover_id', 'branch_id', 'used_for'], x: 2250, y: 1350 },

            // Attacker
            { id: 'attacker', label: 'Attacker', category: 'ATTACKER', x: 50, y: 450 },
        ];

        // Define initial layout mapping
        const initialNodes = architecture.map(n => ({ ...n, x: n.x, y: n.y }));

        const relationships = [
            // Mobile Flow
            { source: 'mob_dash', target: 'mob_store', label: 'save' },
            { source: 'mob_store', target: 'api_sync', label: 'sync' },

            // API & Security
            { source: 'api_sync', target: 'sec_sanctum', label: 'Token' },
            { source: 'sec_sanctum', target: 'ctl_planner', label: 'Auth OK' },
            { source: 'attacker', target: 'sec_sanctum', label: 'Bad Token' },
            
            { source: 'web_planner', target: 'api_dnd', label: 'AJAX' },
            { source: 'api_dnd', target: 'sec_csrf', label: 'CSRF' },
            { source: 'sec_csrf', target: 'ctl_planner', label: 'Valid' },

            // Controller Logic
            { source: 'ctl_planner', target: 'mod_plan', label: 'manage' },
            { source: 'ctl_planner', target: 'mod_item', label: 'manage' },
            { source: 'ctl_planner', target: 'mod_lead', label: 'read' },
            { source: 'ctl_planner', target: 'svc_pusher', label: 'event' },
            
            { source: 'svc_pusher', target: 'mob_dash', label: 'push', animated: true },

            // ORM Mapping
            { source: 'mod_lead', target: 'tab_lead', label: 'orm' },
            { source: 'mod_alt', target: 'tab_alt', label: 'orm' },
            { source: 'mod_lpl', target: 'tab_lpl', label: 'orm' },
            { source: 'mod_art', target: 'tab_art', label: 'orm' },
            { source: 'mod_appt', target: 'tab_appt', label: 'orm' },
            { source: 'mod_ptask', target: 'tab_ptask', label: 'orm' },
            { source: 'mod_prob', target: 'tab_prob', label: 'orm' },
            { source: 'mod_ttask', target: 'tab_ttask', label: 'orm' },
            { source: 'mod_plan', target: 'tab_plan', label: 'orm' },
            { source: 'mod_item', target: 'tab_item', label: 'orm' },
            { source: 'mod_emp', target: 'tab_emp', label: 'orm' },
            { source: 'mod_branch', target: 'tab_branch', label: 'orm' },
            { source: 'mod_dept', target: 'tab_dept', label: 'orm' },
            { source: 'mod_asset', target: 'tab_asset', label: 'orm' },
            
            // Relationships
            { source: 'tab_lpl', target: 'tab_leads', label: 'belongsTo' },
            { source: 'tab_plans', target: 'tab_leads', label: 'belongsTo' },
            { source: 'tab_items', target: 'tab_plans', label: 'belongsTo' },
            { source: 'tab_pie', target: 'tab_items', label: 'belongsTo' },
            { source: 'tab_pie', target: 'tab_emp', label: 'belongsTo' },
            { source: 'tab_appt', target: 'tab_leads', label: 'belongsTo' },
            { source: 'tab_prob', target: 'tab_leads', label: 'belongsTo' },
            { source: 'tab_ttask', target: 'tab_prob', label: 'belongsTo' },
        ];

        // --- ZONES ---
        const initialZones = [
            { id: 'zone_mobile', label: 'MOBILE APP (OFFLINE)', domain: 'MOBILE', x: 0, y: 50, width: 1000, height: 320, styleClass: 'border-pink-300 bg-pink-50/30 text-pink-400' },
            { id: 'zone_sec', label: 'SECURITY / API LAYER', domain: 'SEC', x: 250, y: 390, width: 1100, height: 220, styleClass: 'border-red-300 bg-red-50/20 text-red-300' },
            { id: 'zone_web', label: 'WEBSITE / BACKEND', domain: 'WEB', x: 0, y: 630, width: 2800, height: 1000, styleClass: 'border-slate-300 bg-white/40 text-slate-300' },
        ];

        // --- SIMULATIONS ---
        const simulations = [
            {
                id: 'sim_sync_up',
                label: 'Sync: Upload Offline Data',
                color: 'emerald',
                type: 'green',
                steps: [
                    { from: 'mob_store', to: 'api_sync', msg: 'POST /sync [Payload]' },
                    { from: 'api_sync', to: 'sec_sanctum', msg: 'Auth Check' },
                    { from: 'sec_sanctum', to: 'ctl_planner', msg: 'Controller: sync()' },
                    { from: 'ctl_planner', to: 'tab_items', msg: 'Batch Insert/Update' },
                    { from: 'ctl_planner', to: 'svc_pusher', msg: 'Broadcast Update' },
                ]
            },
            {
                id: 'sim_sync_down',
                label: 'Sync: Download Fresh Data',
                color: 'emerald',
                type: 'blue',
                steps: [
                    { from: 'mob_dash', to: 'api_sync', msg: 'GET /sync [Timestamp]' },
                    { from: 'api_sync', to: 'sec_sanctum', msg: 'Auth Check' },
                    { from: 'sec_sanctum', to: 'ctl_planner', msg: 'Controller: fetch()' },
                    { from: 'ctl_planner', to: 'tab_items', msg: 'SELECT * WHERE updated > ?' },
                    { from: 'tab_items', to: 'ctl_planner', msg: 'Results' },
                    { from: 'ctl_planner', to: 'mob_store', msg: 'JSON Response' },
                ]
            },

            // --- CRUD ---
            {
                id: 'sim_crud_create',
                label: 'CRUD: Create Plan (Wizard)',
                color: 'indigo',
                type: 'violet',
                steps: [
                    { from: 'web_planner', to: 'api_wizard', msg: 'POST /wizard' },
                    { from: 'api_wizard', to: 'sec_csrf', msg: 'Verify CSRF' },
                    { from: 'sec_csrf', to: 'ctl_planner', msg: 'storeWizard()' },
                    { from: 'ctl_planner', to: 'sec_policy', msg: 'Can Create?' },
                    { from: 'sec_policy', to: 'mod_plan', msg: 'New PlannerPlan' },
                    { from: 'mod_plan', to: 'tab_plans', msg: 'INSERT' },
                    { from: 'ctl_planner', to: 'web_planner', msg: '201 Created' },
                ]
            },
            {
                id: 'sim_crud_update',
                label: 'CRUD: Update (Drag & Drop)',
                color: 'violet',
                type: 'violet',
                steps: [
                    { from: 'web_planner', to: 'api_dnd', msg: 'POST /move' },
                    { from: 'api_dnd', to: 'sec_csrf', msg: 'Verify CSRF' },
                    { from: 'sec_csrf', to: 'ctl_planner', msg: 'move()' },
                    { from: 'ctl_planner', to: 'mod_item', msg: 'Update Sort Order' },
                    { from: 'mod_item', to: 'tab_items', msg: 'UPDATE' },
                    { from: 'ctl_planner', to: 'svc_pusher', msg: 'Broadcast' },
                ]
            },
            {
                id: 'sim_crud_delete',
                label: 'CRUD: Soft Delete Item',
                color: 'orange',
                type: 'orange',
                steps: [
                    { from: 'web_planner', to: 'ctl_planner', msg: 'DELETE /items/1' },
                    { from: 'ctl_planner', to: 'sec_policy', msg: 'Can Delete?' },
                    { from: 'sec_policy', to: 'mod_item', msg: 'delete()' },
                    { from: 'mod_item', to: 'tab_items', msg: 'UPDATE deleted_at=NOW()' },
                    { from: 'tab_items', to: 'web_planner', msg: '200 OK' },
                ]
            },

            // --- SECURITY ---
            {
                id: 'sim_attack_token',
                label: 'Sec: Token Hijack',
                color: 'red',
                type: 'red',
                steps: [
                    { from: 'attacker', to: 'api_sync', msg: 'POST /sync [Bad Token]' },
                    { from: 'api_sync', to: 'sec_sanctum', msg: 'Validate...' },
                    { from: 'sec_sanctum', to: 'attacker', msg: '401 UNAUTHORIZED' },
                ]
            },
            {
                id: 'sim_attack_csrf',
                label: 'Sec: CSRF Exploit',
                color: 'red',
                type: 'red',
                steps: [
                    { from: 'attacker', to: 'api_wizard', msg: 'POST /wizard [No Token]' },
                    { from: 'api_wizard', to: 'sec_csrf', msg: 'Check _token' },
                    { from: 'sec_csrf', to: 'attacker', msg: '419 PAGE EXPIRED' },
                ]
            },
            {
                id: 'sim_attack_sql',
                label: 'Sec: SQL Injection',
                color: 'red',
                type: 'red',
                steps: [
                    { from: 'attacker', to: 'web_planner', msg: 'Search: " OR 1=1; --' },
                    { from: 'web_planner', to: 'ctl_planner', msg: 'GET /search' },
                    { from: 'ctl_planner', to: 'mod_item', msg: 'Eloquent Binding' },
                    { from: 'mod_item', to: 'tab_items', msg: 'SELECT ... WHERE col = ?' },
                    { from: 'tab_items', to: 'attacker', msg: 'Safe Result (0 found)' },
                ]
            }
        ];

        function SchemaVisualizer() {
            // Safe initialization
            const [nodes, setNodes] = useState(initialNodes || []);
            const [zones, setZones] = useState(initialZones || []);
            const [viewState, setViewState] = useState({ x: 50, y: 50, scale: 0.45 });
            const [searchTerm, setSearchTerm] = useState('');
            const [selectedNode, setSelectedNode] = useState(null);
            const [connectedNodes, setConnectedNodes] = useState([]);
            
            const [activePacket, setActivePacket] = useState(null); 
            const [simulating, setSimulating] = useState(false);
            const [activeEdge, setActiveEdge] = useState(null); 
            const [copySuccess, setCopySuccess] = useState(false);

            const [draggingType, setDraggingType] = useState(null);
            const [draggingId, setDraggingId] = useState(null);
            const [dragStart, setDragStart] = useState({ x: 0, y: 0 });
            const [initialPos, setInitialPos] = useState({ x: 0, y: 0 });

            // Filtering with Guard
            const filteredNodes = useMemo(() => {
                const current = nodes || [];
                if (!searchTerm) return current;
                const lower = searchTerm.toLowerCase();
                return current.map(n => ({
                    ...n,
                    dimmed: !n.label.toLowerCase().includes(lower) && !n.category.toLowerCase().includes(lower)
                }));
            }, [searchTerm, nodes]);

            // Update connected nodes when selectedNode changes
            useEffect(() => {
                if (!selectedNode) {
                    setConnectedNodes([]);
                    return;
                }
                const related = relationships.filter(r => r.source === selectedNode.id || r.target === selectedNode.id);
                setConnectedNodes(related);
            }, [selectedNode]);


            // Handlers
            const handleMouseDown = (e) => {
                if(e.button !== 0) return;
                setDraggingType('pan');
                setDragStart({ x: e.clientX, y: e.clientY });
                setInitialPos({ x: viewState.x, y: viewState.y });
            };

            const handleNodeMouseDown = (e, nodeId) => {
                e.stopPropagation();
                const node = nodes.find(n => n.id === nodeId);
                if (node) {
                    setDraggingType('node');
                    setDraggingId(nodeId);
                    setDragStart({ x: e.clientX, y: e.clientY });
                    setInitialPos({ x: node.x, y: node.y });
                    setSelectedNode(node);
                }
            };

            const handleZoneMouseDown = (e, zoneId) => {
                e.stopPropagation();
                const zone = zones.find(z => z.id === zoneId);
                if (zone) {
                    setDraggingType('zone');
                    setDraggingId(zoneId);
                    setDragStart({ x: e.clientX, y: e.clientY });
                    setInitialPos({ x: zone.x, y: zone.y });
                }
            };

            const handleMouseMove = (e) => {
                if (!draggingType) return;

                const dx = (e.clientX - dragStart.x) / viewState.scale;
                const dy = (e.clientY - dragStart.y) / viewState.scale;
                const panDx = e.clientX - dragStart.x;
                const panDy = e.clientY - dragStart.y;

                if (draggingType === 'node') {
                    setNodes(prev => prev.map(n => n.id === draggingId ? { ...n, x: initialPos.x + dx, y: initialPos.y + dy } : n));
                } else if (draggingType === 'zone') {
                    const newZoneX = initialPos.x + dx;
                    const newZoneY = initialPos.y + dy;
                    const zone = zones.find(z => z.id === draggingId);
                    const deltaX = newZoneX - zone.x;
                    const deltaY = newZoneY - zone.y;

                    setZones(prev => prev.map(z => z.id === draggingId ? { ...z, x: newZoneX, y: newZoneY } : z));
                    const domain = zone.domain;
                    setNodes(prev => prev.map(n => {
                        const cat = categories[n.category];
                        if (cat && cat.domain === domain) {
                             return { ...n, x: n.x + deltaX, y: n.y + deltaY };
                        }
                        return n;
                    }));
                } else if (draggingType === 'pan') {
                    setViewState(prev => ({ ...prev, x: initialPos.x + panDx, y: initialPos.y + panDy }));
                }
            };

            const handleMouseUp = () => { setDraggingType(null); setDraggingId(null); };

            const handleWheel = (e) => {
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    setViewState(prev => ({ ...prev, scale: Math.min(Math.max(0.1, prev.scale - e.deltaY * 0.001), 2) }));
                }
            };

            // Migration Generator
            const generateMigrationCode = () => {
                const migrations = nodes
                    .filter(n => n.category === 'MIGRATION')
                    .map(node => {
                        let schema = `Schema::create('${node.label}', function (Blueprint $table) {\n`;
                        if (node.columns) {
                            node.columns.forEach(col => {
                                let type = 'string';
                                let extras = '->nullable()';

                                if (col === 'id') {
                                    schema += `    $table->id();\n`;
                                    return;
                                }
                                
                                if (col.endsWith('_id') || col === 'created_by' || col === 'updated_by' || col === 'contact_person' || col === 'branch' || col === 'responsible') type = 'unsignedBigInteger';
                                else if (col === 'id') type = 'id';
                                else if (col.includes('json') || col === 'meta' || col === 'teams' || col === 'products') type = 'json';
                                else if (col.includes('date') || col.endsWith('_at')) type = 'timestamp';
                                else if (col.startsWith('is_') || col.startsWith('has_')) type = 'boolean';
                                else if (col === 'description' || col === 'note' || col === 'problem' || col === 'solution') type = 'longText';
                                else if (col.includes('price') || col.includes('cost') || col === 'total_purchase' || col === 'investment_costs') { type = 'decimal'; extras = ', 10, 2)->nullable()'; }
                                else if (col.includes('count') || col === 'usage_count' || col === 'number_people' || col === 'number_we' || col === 'number_stories') type = 'integer';
                                
                                schema += `    $table->${type}('${col}')${extras};\n`;
                            });
                        }
                        schema += `    $table->timestamps();\n`;
                        schema += `    $table->softDeletes();\n`;
                        schema += `});\n`;
                        return schema;
                    });
                return migrations.join('\n');
            };

            const handleCopyMigrations = () => {
                const code = generateMigrationCode();
                const textarea = document.createElement('textarea');
                textarea.value = code;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                
                setCopySuccess(true);
                setTimeout(() => setCopySuccess(false), 2000);
            };

            // Simulation Logic
            const runSimulation = useCallback((sim) => {
                if(simulating) return;
                setSimulating(true);
                setActiveEdge(null);
                let stepIndex = 0;

                const animateStep = () => {
                    if (stepIndex >= sim.steps.length) {
                        setSimulating(false);
                        setActivePacket(null);
                        setActiveEdge(null);
                        return;
                    }
                    const step = sim.steps[stepIndex];
                    const safeNodes = nodes || [];
                    const startNode = safeNodes.find(n => n.id === step.from);
                    const endNode = safeNodes.find(n => n.id === step.to);

                    if (!startNode || !endNode) { stepIndex++; animateStep(); return; }

                    setActiveEdge({ source: step.from, target: step.to, type: sim.type });

                    const startX = startNode.x + 220; 
                    const startY = startNode.y + 40; 
                    const endX = endNode.x;
                    const endY = endNode.y + 40;

                    let progress = 0;
                    const duration = 70; 

                    const tick = () => {
                        progress += 1 / duration;
                        if (progress >= 1) {
                            stepIndex++;
                            // Pause for "Processing" visuals
                            setTimeout(animateStep, 400);
                        } else {
                            const curX = startX + (endX - startX) * progress;
                            const curY = startY + (endY - startY) * progress;
                            setActivePacket({ x: curX, y: curY, msg: step.msg, type: sim.type });
                            requestAnimationFrame(tick);
                        }
                    };
                    tick();
                };
                animateStep();
            }, [simulating, nodes]);

            // Render Edges
            const edgesSvg = useMemo(() => {
                const currentNodes = nodes || [];
                return (relationships || []).map((rel, idx) => {
                    const sourceNode = currentNodes.find(n => n.id === rel.source);
                    const targetNode = currentNodes.find(n => n.id === rel.target);
                    if (!sourceNode || !targetNode) return null;

                    const startX = sourceNode.x + 220;
                    const startY = sourceNode.y + 40;
                    const endX = targetNode.x;
                    const endY = targetNode.y + 40;

                    const isVertical = Math.abs(startY - endY) > 150;
                    const controlOffset = isVertical ? 0 : 100;
                    const path = `M ${startX} ${startY} C ${startX + controlOffset} ${startY}, ${endX - controlOffset} ${endY}, ${endX} ${endY}`;

                    const isActive = activeEdge && activeEdge.source === rel.source && activeEdge.target === rel.target;
                    const isSelected = selectedNode && (rel.source === selectedNode.id || rel.target === selectedNode.id);
                    
                    let strokeColor = '#94a3b8';
                    let strokeClass = '';
                    
                    if (isActive) {
                         if (activeEdge.type === 'red') { strokeColor = '#ef4444'; strokeClass = 'line-active-red'; }
                         else if (activeEdge.type === 'blue') { strokeColor = '#3b82f6'; strokeClass = 'line-active-blue'; }
                         else if (activeEdge.type === 'violet') { strokeColor = '#8b5cf6'; strokeClass = 'line-active-violet'; }
                         else if (activeEdge.type === 'orange') { strokeColor = '#f97316'; strokeClass = 'line-active-orange'; }
                         else { strokeColor = '#10b981'; strokeClass = 'line-active-green'; }
                    } else if (isSelected) {
                        strokeColor = '#3b82f6';
                        strokeClass = 'line-selected';
                    }

                    return (
                        <g key={idx}>
                            <path d={path} fill="none" stroke={strokeColor} strokeWidth={isActive?4:2} className={strokeClass} markerEnd="url(#arrowhead)" opacity={isActive ? 1 : 0.3} />
                            {!isActive && <text x={(startX+endX)/2} y={(startY+endY)/2 - 5} textAnchor="middle" fill="#64748b" fontSize="10" fontWeight="bold">{rel.label}</text>}
                        </g>
                    );
                });
            }, [nodes, activeEdge, selectedNode]);

            const NODE_WIDTH = 220;
            const gridStyle = { backgroundImage: 'radial-gradient(#cbd5e1 1px, transparent 1px)', backgroundSize: `${20 * viewState.scale}px ${20 * viewState.scale}px`, backgroundPosition: `${viewState.x}px ${viewState.y}px`, opacity: 0.5 };
            const canvasStyle = { transform: `translate(${viewState.x}px, ${viewState.y}px) scale(${viewState.scale})`, width: '8000px', height: '8000px' };
            const svgStyle = { overflow: 'visible' };

            return (
                <div className="w-full h-screen bg-slate-100 flex flex-col font-sans overflow-hidden">
                    {/* Header */}
                    <div className="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between shadow-sm z-20">
                        <div className="flex items-center gap-3">
                            <div className="bg-indigo-600 p-2 rounded-lg"><Icons.Zap className="text-white" /></div>
                            <div>
                                <h1 className="text-xl font-bold text-slate-800">Secure Architecture Mesh</h1>
                                <p className="text-xs text-slate-500">Mobile Sync • Security • Database</p>
                            </div>
                        </div>
                        <div className="flex items-center gap-4">
                            <div className="relative">
                                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"><Icons.Search size={14} /></span>
                                <input type="text" placeholder="Search..." className="pl-9 pr-4 py-1.5 bg-slate-100 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500" value={searchTerm} onChange={(e) => setSearchTerm(e.target.value)} />
                            </div>
                            <div className="flex items-center gap-1 bg-slate-100 rounded-lg p-1">
                                <button onClick={() => setViewState(p => ({...p, scale: Math.max(0.1, p.scale - 0.1)}))} className="p-1.5 hover:bg-white rounded shadow-sm text-slate-600"><Icons.ZoomOut size={16} /></button>
                                <span className="text-xs w-12 text-center text-slate-500">{Math.round(viewState.scale * 100)}%</span>
                                <button onClick={() => setViewState(p => ({...p, scale: Math.min(2, p.scale + 0.1)}))} className="p-1.5 hover:bg-white rounded shadow-sm text-slate-600"><Icons.ZoomIn size={16} /></button>
                            </div>
                        </div>
                    </div>

                    <div className="flex-1 relative flex overflow-hidden">
                        <div 
                            className="flex-1 h-full cursor-grab active:cursor-grabbing bg-slate-100 relative overflow-hidden"
                            onMouseDown={handleMouseDown} onMouseMove={handleMouseMove} onMouseUp={handleMouseUp} onMouseLeave={handleMouseUp} onWheel={handleWheel}
                        >
                            <div className="absolute inset-0 pointer-events-none" style={gridStyle} />

                            <div className="absolute origin-top-left transition-transform duration-75" style={canvasStyle}>
                                
                                {/* DRAGGABLE ZONES */}
                                {zones.map(z => {
                                    const zStyle = { left: z.x, top: z.y, width: z.width, height: z.height };
                                    return (
                                        <div key={z.id} className={`absolute border-2 border-dashed rounded-3xl flex flex-col items-center pt-4 group transition-colors hover:bg-opacity-30 ${z.styleClass}`} style={zStyle}>
                                            <div className="absolute top-0 w-full h-12 cursor-grab active:cursor-grabbing bg-transparent hover:bg-black/5 rounded-t-3xl" onMouseDown={(e) => handleZoneMouseDown(e, z.id)} title="Drag Zone"></div>
                                            <span className="font-black text-2xl opacity-50 pointer-events-none select-none uppercase tracking-widest">{z.label}</span>
                                        </div>
                                    );
                                })}

                                <svg className="absolute top-0 left-0 w-[8000px] h-[8000px] pointer-events-none" style={svgStyle}>
                                    <defs>
                                        <marker id="arrowhead" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto"><polygon points="0 0, 10 3.5, 0 7" fill="#94a3b8" /></marker>
                                        <marker id="arrowhead-active" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto"><polygon points="0 0, 10 3.5, 0 7" fill="#10b981" /></marker>
                                    </defs>
                                    {edgesSvg}
                                </svg>

                                {filteredNodes.map(node => {
                                    const cat = categories[node.category];
                                    const Icon = cat.icon;
                                    const isSelected = selectedNode?.id === node.id;
                                    const isDimmed = node.dimmed;
                                    const isActive = activeEdge && (activeEdge.source === node.id || activeEdge.target === node.id);
                                    
                                    let pulseClass = '';
                                    if(isActive) {
                                        if (activeEdge.type === 'red') pulseClass = 'animate-pulse-red';
                                        else if (activeEdge.type === 'blue') pulseClass = 'animate-pulse-blue';
                                        else if (activeEdge.type === 'violet') pulseClass = 'animate-pulse-violet';
                                        else if (activeEdge.type === 'orange') pulseClass = 'animate-pulse-orange';
                                        else pulseClass = 'animate-pulse-green';
                                    }

                                    const nodeStyle = { left: node.x, top: node.y, width: NODE_WIDTH, opacity: isDimmed ? 0.3 : 1 };
                                    const headerStyle = { backgroundColor: cat.color };

                                    return (
                                        <div
                                            key={node.id}
                                            onMouseDown={(e) => handleNodeMouseDown(e, node.id)}
                                            className={`absolute rounded-lg bg-white shadow-lg border-2 flex flex-col group cursor-pointer w-[220px] ${isActive ? pulseClass : (isSelected ? 'border-blue-500 scale-105 z-10' : 'border-white hover:border-slate-300')}`}
                                            style={nodeStyle}
                                        >
                                            <div className="px-3 py-2 text-white font-bold rounded-t-[5px] flex items-center gap-2 text-sm" style={headerStyle}>
                                                <Icon size={14} />
                                                <span className="truncate">{node.label}</span>
                                            </div>
                                            <div className="p-2 bg-slate-50 text-[10px] text-slate-500 rounded-b-lg font-bold">{cat.label}</div>
                                        </div>
                                    );
                                })}

                                {activePacket && (() => {
                                    const packetStyle = { 
                                        left: activePacket.x, 
                                        top: activePacket.y,
                                        transform: 'translate(-50%, -50%)' 
                                    };
                                    let colorBg = 'bg-emerald-500 shadow-emerald-500/50';
                                    if(activePacket.type === 'red') colorBg = 'bg-red-500 shadow-red-500/50';
                                    if(activePacket.type === 'blue') colorBg = 'bg-blue-500 shadow-blue-500/50';
                                    if(activePacket.type === 'violet') colorBg = 'bg-violet-500 shadow-violet-500/50';
                                    if(activePacket.type === 'orange') colorBg = 'bg-orange-500 shadow-orange-500/50';

                                    return (
                                        <div 
                                            className="absolute z-50 flex items-center justify-center pointer-events-none transition-all duration-75"
                                            style={packetStyle}
                                        >
                                            <div className={`w-6 h-6 rounded-full shadow-lg border-2 border-white ${colorBg} packet-pulse`}></div>
                                            <div className="absolute top-8 bg-slate-900 text-white text-[10px] font-bold px-3 py-1.5 rounded-full whitespace-nowrap opacity-90 shadow-xl z-50">
                                                {activePacket.msg}
                                            </div>
                                        </div>
                                    );
                                })()}
                            </div>
                        </div>

                        {/* Sidebar Details / Simulation Dashboard */}
                        <div className={`fixed top-16 bottom-0 right-0 w-96 bg-white border-l border-slate-200 shadow-2xl z-40 overflow-y-auto transform transition-transform duration-300 ${ 'translate-x-0' }`}>
                            {selectedNode ? (
                                <div className="p-6">
                                    <div className="flex items-start justify-between mb-6">
                                        <div>
                                            {(() => {
                                                const pillStyle = { backgroundColor: categories[selectedNode.category].color };
                                                return <div className="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-bold text-white mb-2" style={pillStyle}>{categories[selectedNode.category].label}</div>;
                                            })()}
                                            <h2 className="text-xl font-bold text-slate-800 break-all">{selectedNode.label}</h2>
                                        </div>
                                        <button onClick={() => setSelectedNode(null)} className="p-1 hover:bg-slate-100 rounded-full transition-colors"><Icons.X /></button>
                                    </div>
                                    
                                    {/* Relationships Section (NEW) */}
                                    {connectedNodes.length > 0 && (
                                        <div className="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-100">
                                            <h3 className="text-sm font-bold text-blue-900 mb-3 flex items-center gap-2"><Icons.Link size={14} /> Connected Tables</h3>
                                            <div className="space-y-2">
                                                {connectedNodes.map((rel, idx) => {
                                                    const isSource = rel.source === selectedNode.id;
                                                    const otherId = isSource ? rel.target : rel.source;
                                                    const otherNode = nodes.find(n => n.id === otherId);
                                                    const direction = isSource ? '→' : '←';
                                                    return (
                                                        <div key={idx} className="flex items-center justify-between bg-white px-3 py-2 rounded border border-blue-200 text-xs shadow-sm">
                                                            <span className="font-bold text-slate-600 truncate max-w-[120px]">{otherNode?.label || otherId}</span>
                                                            <div className="flex items-center gap-2">
                                                                <span className="text-slate-400 font-mono">{rel.label}</span>
                                                                <span className="text-blue-500 font-bold">{direction}</span>
                                                            </div>
                                                        </div>
                                                    );
                                                })}
                                            </div>
                                        </div>
                                    )}

                                    <div className="space-y-6">
                                        <div className="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                            <h3 className="text-sm font-bold text-slate-700 mb-2 flex items-center gap-2"><Icons.Info size={14} /> Description</h3>
                                            <p className="text-sm text-slate-600 leading-relaxed">{selectedNode.details || "No details provided."}</p>
                                        </div>
                                        {selectedNode.input && <div><h3 className="text-sm font-bold text-slate-700 mb-2">Input Parameters</h3><div className="font-mono text-xs bg-slate-800 text-slate-200 p-3 rounded-lg overflow-x-auto shadow-inner">{selectedNode.input}</div></div>}
                                        
                                        {/* Structure / Columns */}
                                        <div>
                                            <h3 className="text-sm font-bold text-slate-700 mb-3 flex items-center justify-between"><span className="flex items-center gap-2"><Icons.Maximize2 size={14} /> Structure</span></h3>
                                            <div className="space-y-1">
                                                {selectedNode.columns && selectedNode.columns.map((col, idx) => (
                                                    <div key={idx} className="px-3 py-2 text-sm rounded border flex justify-between items-center bg-white border-slate-100 text-slate-600 font-mono"><span>{col}</span></div>
                                                ))}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <div className="p-6">
                                    <h2 className="text-lg font-black text-slate-800 mb-1">SIMULATION LAB</h2>
                                    <p className="text-xs text-slate-500 mb-6">Run architecture scenarios to test flow.</p>
                                    <div className="space-y-8">
                                        <div>
                                            <h3 className="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Sync & Data</h3>
                                            <div className="space-y-2">
                                                {simulations.slice(0, 3).map(sim => (
                                                    <button key={sim.id} onClick={() => runSimulation(sim)} disabled={simulating} className={`w-full text-left px-4 py-3 rounded-xl border-2 font-bold text-xs flex items-center justify-between transition-all ${simulating ? 'opacity-50 cursor-not-allowed' : 'hover:scale-[1.02]'} ${sim.color === 'emerald' ? 'bg-emerald-50 border-emerald-100 text-emerald-700' : 'bg-blue-50 border-blue-100 text-blue-700'}`}>
                                                        <span>{sim.label}</span><Icons.Play size={12} />
                                                    </button>
                                                ))}
                                            </div>
                                        </div>
                                        <div>
                                            <h3 className="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">CRUD Operations</h3>
                                            <div className="space-y-2">
                                                {simulations.slice(3, 6).map(sim => (
                                                    <button key={sim.id} onClick={() => runSimulation(sim)} disabled={simulating} className={`w-full text-left px-4 py-3 rounded-xl border-2 font-bold text-xs flex items-center justify-between transition-all ${simulating ? 'opacity-50 cursor-not-allowed' : 'hover:scale-[1.02]'} bg-white border-slate-200 text-slate-600 hover:border-indigo-300 hover:text-indigo-600`}>
                                                        <span>{sim.label}</span><Icons.Zap size={12} />
                                                    </button>
                                                ))}
                                            </div>
                                        </div>
                                        <div>
                                            <h3 className="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Security Tests</h3>
                                            <div className="space-y-2">
                                                {simulations.slice(6).map(sim => (
                                                    <button key={sim.id} onClick={() => runSimulation(sim)} disabled={simulating} className={`w-full text-left px-4 py-3 rounded-xl border-2 font-bold text-xs flex items-center justify-between transition-all ${simulating ? 'opacity-50 cursor-not-allowed' : 'hover:scale-[1.02]'} bg-red-50 border-red-100 text-red-600 hover:bg-red-100`}>
                                                        <span>{sim.label}</span><Icons.Shield size={12} />
                                                    </button>
                                                ))}
                                            </div>
                                        </div>

                                        {/* EXPORT SECTION */}
                                        <div>
                                            <h3 className="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Tools</h3>
                                            <button 
                                                onClick={handleCopyMigrations}
                                                className="w-full text-left px-4 py-3 rounded-xl border-2 font-bold text-xs flex items-center justify-between transition-all bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100 hover:border-slate-300"
                                            >
                                                <span>{copySuccess ? 'Copied to Clipboard!' : 'Copy All Migrations'}</span>
                                                {copySuccess ? <Icons.Check size={12} /> : <Icons.Copy size={12} />}
                                            </button>
                                        </div>

                                        <div className="p-4 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-500">
                                            <p className="font-bold mb-1">Instructions:</p>
                                            <p>1. Drag zones (dashed borders) to reorganize.</p>
                                            <p>2. Click nodes to see columns & connections.</p>
                                            <p>3. Run simulations to see data flow.</p>
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            );
        }

        const root = ReactDOM.createRoot(document.getElementById('root'));
        root.render(<SchemaVisualizer />);
    </script>
</body>
</html>