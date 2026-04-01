<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuri Head: Mobile & Web Architecture Mesh</title>
    
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
        
        /* Packet Animation */
        @keyframes flowPulse {
            0% { transform: scale(1); opacity: 1; box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1.5); opacity: 0; box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { transform: scale(1); opacity: 0; box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        .packet-pulse { animation: flowPulse 1s infinite; }

        /* Line Dash Animation for Active State */
        @keyframes dash {
            to { stroke-dashoffset: -20; }
        }
        .line-active {
            stroke-dasharray: 5;
            animation: dash 0.5s linear infinite;
        }
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
            Wifi: (p) => <IconWrapper {...p}><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></IconWrapper>
        };

        // --- DOMAINS ---
        const domains = {
            MOBILE: { title: "Mobile Ecosystem (Offline First)", bg: "bg-pink-50/50", border: "border-pink-200" },
            WEB:    { title: "Website / Backend Ecosystem", bg: "bg-slate-50/50", border: "border-slate-200" }
        };

        const categories = {
            MOBILE_VIEW: { color: '#db2777', label: 'Mobile View', icon: Icons.Mobile, domain: 'MOBILE' },
            MOBILE_DATA: { color: '#be185d', label: 'Local Cache', icon: Icons.HardDrive, domain: 'MOBILE' },
            WEB_VIEW:    { color: '#f59e0b', label: 'Blade View', icon: Icons.Code, domain: 'WEB' },
            ROUTE:       { color: '#10b981', label: 'API Route', icon: Icons.Globe, domain: 'WEB' },
            CONTROLLER:  { color: '#8b5cf6', label: 'Controller', icon: Icons.Server, domain: 'WEB' },
            MODEL:       { color: '#3b82f6', label: 'Model', icon: Icons.FileCode, domain: 'WEB' },
            MIGRATION:   { color: '#0ea5e9', label: 'DB Table', icon: Icons.Table, domain: 'WEB' },
            SERVICE:     { color: '#6366f1', label: 'Service', icon: Icons.Zap, domain: 'WEB' },
        };

        const architecture = [
            // --- MOBILE ZONE (Top) ---
            { id: 'mob_login', label: 'Mobile Login', category: 'MOBILE_VIEW', details: 'PIN Authentication.', x: 100, y: 100 },
            { id: 'mob_dash', label: 'Mobile Dashboard', category: 'MOBILE_VIEW', details: 'Task List & Progress.', x: 350, y: 100 },
            { id: 'mob_active', label: 'Active Mode', category: 'MOBILE_VIEW', details: 'GPS & Time Tracking.', x: 600, y: 100 },
            { id: 'mob_store', label: 'Local Storage', category: 'MOBILE_DATA', details: 'wf_tasks, wf_logs.', x: 350, y: 250 },

            // --- API BRIDGE (Middle) ---
            { id: 'api_sync', label: 'POST /api/sync', category: 'ROUTE', details: 'Sync Offline Data.', x: 350, y: 450 },
            { id: 'api_dnd', label: 'POST /dnd/move', category: 'ROUTE', details: 'Drag & Drop.', x: 650, y: 450 },
            { id: 'svc_pusher', label: 'Pusher / WebSocket', category: 'SERVICE', details: 'Real-time Updates.', x: 900, y: 350 },

            // --- WEB ZONE (Bottom) ---
            
            // Views
            { id: 'web_planner', label: 'Planner View', category: 'WEB_VIEW', details: 'Admin Planner UI.', x: 650, y: 600 },

            // Controllers
            { id: 'ctl_planner', label: 'PlannerPlanController', category: 'CONTROLLER', x: 650, y: 750 },
            { id: 'ctl_state', label: 'PlannerItemStateController', category: 'CONTROLLER', x: 900, y: 750 },

            // Models (Grouped by Context)
            // CRM
            { id: 'mod_lead', label: 'NewLead', category: 'MODEL', x: 100, y: 900 },
            { id: 'tab_lead', label: 'new_leads', category: 'MIGRATION', x: 100, y: 1050, columns: ['id','firma','name'] },
            
            { id: 'mod_alt', label: 'LeadAlternativeAdd', category: 'MODEL', x: 100, y: 1200 },
            { id: 'tab_alt', label: 'lead_alternative_adds', category: 'MIGRATION', x: 100, y: 1350 },

            { id: 'mod_lpl', label: 'LeadProductList', category: 'MODEL', x: 300, y: 900 },
            { id: 'tab_lpl', label: 'lead_product_lists', category: 'MIGRATION', x: 300, y: 1050 },

            // Catalog
            { id: 'mod_art', label: 'ArticleGroup', category: 'MODEL', x: 300, y: 1200 },
            { id: 'tab_art', label: 'article_groups', category: 'MIGRATION', x: 300, y: 1350 },

            // Operations / Tasks
            { id: 'mod_appt', label: 'MainAppointment', category: 'MODEL', x: 500, y: 900 },
            { id: 'tab_appt', label: 'main_appointments', category: 'MIGRATION', x: 500, y: 1050 },

            { id: 'mod_ptask', label: 'PersonalTask', category: 'MODEL', x: 500, y: 1200 },
            { id: 'tab_ptask', label: 'personal_tasks', category: 'MIGRATION', x: 500, y: 1350 },

            { id: 'mod_prob', label: 'Problem (Ticket)', category: 'MODEL', x: 700, y: 900 },
            { id: 'tab_prob', label: 'problems', category: 'MIGRATION', x: 700, y: 1050 },

            { id: 'mod_ttask', label: 'TicketTask', category: 'MODEL', x: 700, y: 1200 },
            { id: 'tab_ttask', label: 'ticket_tasks', category: 'MIGRATION', x: 700, y: 1350 },

            // Planner Core
            { id: 'mod_plan', label: 'PlannerPlan', category: 'MODEL', x: 900, y: 900 },
            { id: 'tab_plan', label: 'planner_plans', category: 'MIGRATION', x: 900, y: 1050 },

            { id: 'mod_item', label: 'PlannerItem', category: 'MODEL', x: 900, y: 1200 },
            { id: 'tab_item', label: 'planner_items', category: 'MIGRATION', x: 900, y: 1350 },

            // Planner Pivots
            { id: 'tab_pie', label: 'planner_item_employees', category: 'MIGRATION', x: 1100, y: 1200 },
            { id: 'tab_pia', label: 'planner_item_assets', category: 'MIGRATION', x: 1100, y: 1350 },
            { id: 'tab_pid', label: 'planner_item_dependencies', category: 'MIGRATION', x: 1100, y: 1500 },

            // HR / Org
            { id: 'mod_emp', label: 'Employee', category: 'MODEL', x: 1300, y: 900 },
            { id: 'tab_emp', label: 'employees', category: 'MIGRATION', x: 1300, y: 1050 },

            { id: 'mod_branch', label: 'Branch', category: 'MODEL', x: 1500, y: 900 },
            { id: 'tab_branch', label: 'branches', category: 'MIGRATION', x: 1500, y: 1050 },
            
            { id: 'mod_dept', label: 'Department', category: 'MODEL', x: 1500, y: 1200 },
            { id: 'tab_dept', label: 'departments', category: 'MIGRATION', x: 1500, y: 1350 },

            // Assets
            { id: 'mod_asset', label: 'Asset', category: 'MODEL', x: 1700, y: 900 },
            { id: 'tab_asset', label: 'assets', category: 'MIGRATION', x: 1700, y: 1050 },
        ];

        // Define initial layout positions
        const layout = {
            'mob_view_login': { x: 50, y: 100 },
            'mob_view_dash': { x: 50, y: 300 },
            'mob_view_active': { x: 50, y: 500 },
            'mob_storage': { x: 300, y: 300 }, 

            'api_sync': { x: 600, y: 300 },
            'svc_pusher': { x: 600, y: 100 },

            'web_planner': { x: 900, y: 100 },
            'api_dnd': { x: 900, y: 300 },
            'api_wizard': { x: 900, y: 500 },
            'api_play': { x: 900, y: 700 },

            'ctl_planner': { x: 1200, y: 300 },
            'ctl_state': { x: 1200, y: 700 },

            'mod_plan': { x: 1500, y: 200 },
            'mod_item': { x: 1500, y: 400 },
            'mod_emp': { x: 1500, y: 600 },

            'db_plans': { x: 1800, y: 200 },
            'db_items': { x: 1800, y: 400 },
            'db_pivot': { x: 1800, y: 550 },
            'db_logs': { x: 1800, y: 700 },
        };

        // Create initial nodes with coordinates
        const initialNodes = architecture.map(n => ({
            ...n,
            x: layout[n.id]?.x || n.x || 0,
            y: layout[n.id]?.y || n.y || 0
        }));

        const relationships = [
            // Mobile Internal
            { source: 'mob_login', target: 'mob_storage', label: 'auth' },
            { source: 'mob_dash', target: 'mob_storage', label: 'read/write' },
            { source: 'mob_active', target: 'mob_storage', label: 'logs' },

            // Mobile <-> Web
            { source: 'mob_storage', target: 'api_sync', label: 'sync packet', animated: true },
            { source: 'svc_pusher', target: 'mob_dash', label: 'realtime push', animated: true },

            // Web Controller Logic
            { source: 'api_sync', target: 'ctl_planner', label: 'syncFromMobile()' },
            { source: 'web_planner', target: 'api_dnd', label: 'drag drop' },
            { source: 'api_dnd', target: 'ctl_planner', label: 'move()' },
            { source: 'ctl_planner', target: 'svc_pusher', label: 'broadcast' },

            // Controllers -> Models
            { source: 'ctl_planner', target: 'mod_plan', label: 'manage' },
            { source: 'ctl_planner', target: 'mod_item', label: 'manage' },
            { source: 'ctl_state', target: 'mod_item', label: 'status' },

            // Models -> Tables (ORM)
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

            // Model Relationships
            { source: 'mod_lpl', target: 'mod_lead', label: 'customer' },
            { source: 'mod_plan', target: 'mod_lpl', label: 'project' },
            { source: 'mod_item', target: 'mod_plan', label: 'plan' },
            { source: 'mod_item', target: 'tab_pie', label: 'assignees' },
            { source: 'mod_emp', target: 'tab_pie', label: 'assigned' },
        ];

        // --- SIMULATION SCENARIOS ---
        const simulations = [
            {
                id: 'sim_sync',
                label: 'Simulate: Offline Sync',
                steps: [
                    { from: 'mob_dash', to: 'mob_storage', msg: 'Task Completed (Offline)' },
                    { from: 'mob_storage', to: 'api_sync', msg: 'Internet! POST /api/sync' },
                    { from: 'api_sync', to: 'ctl_planner', msg: 'Controller Processing' },
                    { from: 'ctl_planner', to: 'mod_item', msg: 'Update Status' },
                    { from: 'mod_item', to: 'tab_item', msg: 'DB UPDATE' },
                    { from: 'ctl_planner', to: 'svc_pusher', msg: 'Broadcast Event' },
                    { from: 'svc_pusher', to: 'web_planner', msg: 'UI Refresh' },
                ]
            },
            {
                id: 'sim_move',
                label: 'Simulate: Desktop Move',
                steps: [
                    { from: 'web_planner', to: 'api_dnd', msg: 'Drag & Drop Event' },
                    { from: 'api_dnd', to: 'ctl_planner', msg: 'Controller: move()' },
                    { from: 'ctl_planner', to: 'mod_item', msg: 'Find Item' },
                    { from: 'mod_item', to: 'tab_item', msg: 'UPDATE sort_order' },
                    { from: 'ctl_planner', to: 'tab_pie', msg: 'UPDATE Lead' },
                    { from: 'ctl_planner', to: 'web_planner', msg: 'Success Response' },
                ]
            }
        ];

        function SchemaVisualizer() {
            // Use state for nodes to allow dragging updates
            const [nodes, setNodes] = useState(initialNodes);
            const [viewState, setViewState] = useState({ x: 50, y: 50, scale: 0.5 });
            const [searchTerm, setSearchTerm] = useState('');
            const [selectedNode, setSelectedNode] = useState(null);
            
            // Dragging State
            const [draggingNode, setDraggingNode] = useState(null);
            const [isPanDragging, setIsPanDragging] = useState(false);
            const [lastMousePos, setLastMousePos] = useState({ x: 0, y: 0 });
            
            // Simulation State
            const [activePacket, setActivePacket] = useState(null); 
            const [simulating, setSimulating] = useState(false);
            const [activeEdge, setActiveEdge] = useState(null);

            // Filtering
            const filteredNodes = useMemo(() => {
                if (!searchTerm) return nodes;
                const lower = searchTerm.toLowerCase();
                return nodes.map(n => ({
                    ...n,
                    dimmed: !n.label.toLowerCase().includes(lower) && !n.category.toLowerCase().includes(lower)
                }));
            }, [searchTerm, nodes]);

            // --- Handlers ---
            const handleMouseDown = (e) => {
                if(e.button !== 0) return;
                setIsPanDragging(true);
                setLastMousePos({ x: e.clientX, y: e.clientY });
            };

            const handleNodeMouseDown = (e, nodeId) => {
                e.stopPropagation();
                const node = nodes.find(n => n.id === nodeId);
                if (node) {
                    setDraggingNode({ id: nodeId, startX: e.clientX, startY: e.clientY, initialNodeX: node.x, initialNodeY: node.y });
                    setSelectedNode(node);
                }
            };

            const handleMouseMove = (e) => {
                if (draggingNode) {
                    const dx = (e.clientX - draggingNode.startX) / viewState.scale;
                    const dy = (e.clientY - draggingNode.startY) / viewState.scale;
                    
                    setNodes(prev => prev.map(n => {
                        if (n.id === draggingNode.id) {
                            return { 
                                ...n, 
                                x: draggingNode.initialNodeX + dx, 
                                y: draggingNode.initialNodeY + dy 
                            };
                        }
                        return n;
                    }));
                } else if (isPanDragging) {
                    const dx = e.clientX - lastMousePos.x;
                    const dy = e.clientY - lastMousePos.y;
                    setViewState(prev => ({ ...prev, x: prev.x + dx, y: prev.y + dy }));
                    setLastMousePos({ x: e.clientX, y: e.clientY });
                }
            };

            const handleMouseUp = () => {
                setIsPanDragging(false);
                setDraggingNode(null);
            };

            const handleWheel = (e) => {
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    const scaleAmt = -e.deltaY * 0.001;
                    setViewState(prev => ({ ...prev, scale: Math.min(Math.max(0.1, prev.scale + scaleAmt), 2) }));
                }
            };

            // --- Simulation Logic ---
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
                    const startNode = nodes.find(n => n.id === step.from);
                    const endNode = nodes.find(n => n.id === step.to);

                    if (!startNode || !endNode) { stepIndex++; animateStep(); return; }

                    setActiveEdge({ source: step.from, target: step.to });

                    const startX = startNode.x + 220; 
                    const startY = startNode.y + 40; 
                    const endX = endNode.x;
                    const endY = endNode.y + 40;

                    let progress = 0;
                    const duration = 60; 

                    const tick = () => {
                        progress += 1 / duration;
                        if (progress >= 1) {
                            stepIndex++;
                            setTimeout(animateStep, 300);
                        } else {
                            const curX = startX + (endX - startX) * progress;
                            const curY = startY + (endY - startY) * progress;
                            setActivePacket({ x: curX, y: curY, msg: step.msg });
                            requestAnimationFrame(tick);
                        }
                    };
                    tick();
                };
                animateStep();
            }, [simulating, nodes]);

            // --- Render Edges ---
            const edgesSvg = useMemo(() => {
                return relationships.map((rel, idx) => {
                    const sourceNode = nodes.find(n => n.id === rel.source);
                    const targetNode = nodes.find(n => n.id === rel.target);
                    if (!sourceNode || !targetNode) return null;

                    const startX = sourceNode.x + 220;
                    const startY = sourceNode.y + 40;
                    const endX = targetNode.x;
                    const endY = targetNode.y + 40;

                    const cp1X = startX + (endX - startX) / 2;
                    const cp2X = endX - (endX - startX) / 2;
                    
                    const isVertical = Math.abs(startY - endY) > 200;
                    const path = isVertical
                        ? `M ${startX} ${startY} C ${startX + 100} ${startY}, ${endX - 100} ${endY}, ${endX} ${endY}`
                        : `M ${startX} ${startY} C ${cp1X} ${startY}, ${cp2X} ${endY}, ${endX} ${endY}`;

                    const isActive = activeEdge && activeEdge.source === rel.source && activeEdge.target === rel.target;

                    return (
                        <g key={idx}>
                            <path 
                                d={path} 
                                fill="none" 
                                stroke={isActive ? "#10b981" : "#94a3b8"} 
                                strokeWidth={isActive ? 4 : 2} 
                                className={isActive ? "line-active" : ""}
                                markerEnd={isActive ? "url(#arrowhead-active)" : "url(#arrowhead)"} 
                                opacity={isActive ? 1 : 0.4} 
                            />
                            {!isActive && <text x={(startX+endX)/2} y={(startY+endY)/2 - 5} textAnchor="middle" fill="#64748b" fontSize="10" fontWeight="bold">{rel.label}</text>}
                        </g>
                    );
                });
            }, [nodes, activeEdge]);

            const NODE_WIDTH = 220;

            // Styles
            const gridStyle = { 
                backgroundImage: 'radial-gradient(#cbd5e1 1px, transparent 1px)', 
                backgroundSize: `${20 * viewState.scale}px ${20 * viewState.scale}px`,
                backgroundPosition: `${viewState.x}px ${viewState.y}px`,
                opacity: 0.5
            };

            const canvasStyle = { 
                transform: `translate(${viewState.x}px, ${viewState.y}px) scale(${viewState.scale})`,
                width: '5000px', height: '5000px'
            };

            const svgStyle = { overflow: 'visible' };

            return (
                <div className="w-full h-screen bg-slate-100 flex flex-col font-sans overflow-hidden">
                    {/* Header */}
                    <div className="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between shadow-sm z-20">
                        <div className="flex items-center gap-3">
                            <div className="bg-indigo-600 p-2 rounded-lg"><Icons.Zap className="text-white" /></div>
                            <div>
                                <h1 className="text-xl font-bold text-slate-800">Nuri Head Architecture</h1>
                                <p className="text-xs text-slate-500">Mobile & Web Mesh</p>
                            </div>
                        </div>
                        
                        <div className="flex items-center gap-4">
                            {/* Search */}
                            <div className="relative">
                                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"><Icons.Search size={14} /></span>
                                <input 
                                    type="text" 
                                    placeholder="Search models..." 
                                    className="pl-9 pr-4 py-1.5 bg-slate-100 rounded-lg text-sm outline-none focus:ring-2 focus:ring-indigo-500"
                                    value={searchTerm}
                                    onChange={(e) => setSearchTerm(e.target.value)}
                                />
                            </div>

                            {/* Zoom */}
                            <div className="flex items-center gap-1 bg-slate-100 rounded-lg p-1">
                                <button onClick={() => setViewState(p => ({...p, scale: Math.max(0.1, p.scale - 0.1)}))} className="p-1.5 hover:bg-white rounded shadow-sm text-slate-600"><Icons.ZoomOut size={16} /></button>
                                <span className="text-xs w-12 text-center text-slate-500">{Math.round(viewState.scale * 100)}%</span>
                                <button onClick={() => setViewState(p => ({...p, scale: Math.min(2, p.scale + 0.1)}))} className="p-1.5 hover:bg-white rounded shadow-sm text-slate-600"><Icons.ZoomIn size={16} /></button>
                            </div>

                            <div className="h-6 w-px bg-slate-200"></div>

                            {/* Sims */}
                            {simulations.slice(0, 2).map(sim => (
                                <button 
                                    key={sim.id}
                                    onClick={() => runSimulation(sim)}
                                    disabled={simulating}
                                    className={`px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-2 transition-all ${simulating ? 'bg-slate-100 text-slate-400' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 shadow-sm'}`}
                                >
                                    <Icons.Play size={12} /> {sim.label}
                                </button>
                            ))}
                        </div>
                    </div>

                    <div className="flex-1 relative flex overflow-hidden">
                        <div 
                            className="flex-1 h-full cursor-grab active:cursor-grabbing bg-slate-100 relative overflow-hidden"
                            onMouseDown={handleMouseDown}
                            onMouseMove={handleMouseMove}
                            onMouseUp={handleMouseUp}
                            onMouseLeave={handleMouseUp}
                            onWheel={handleWheel}
                        >
                            <div className="absolute inset-0 pointer-events-none" style={gridStyle} />

                            <div className="absolute origin-top-left transition-transform duration-75" style={canvasStyle}>
                                
                                {/* ZONES */}
                                <div className="absolute top-[50px] left-[0px] w-[1100px] h-[500px] border-2 border-dashed border-pink-300 bg-pink-50/20 rounded-3xl flex flex-col items-center pt-4 pointer-events-none">
                                    <span className="text-pink-400 font-black uppercase tracking-widest text-2xl opacity-50">MOBILE APP (OFFLINE FIRST)</span>
                                </div>
                                <div className="absolute top-[850px] left-[0px] w-[2000px] h-[750px] border-2 border-dashed border-slate-300 bg-white/40 rounded-3xl flex flex-col items-center pt-4 pointer-events-none">
                                    <span className="text-slate-300 font-black uppercase tracking-widest text-2xl opacity-50">WEBSITE / BACKEND</span>
                                </div>
                                <div className="absolute top-[550px] left-[550px] w-[2px] h-[300px] border-l-4 border-dashed border-indigo-200 pointer-events-none"></div>
                                <div className="absolute top-[700px] left-[580px] text-indigo-300 font-bold uppercase tracking-widest -rotate-90 pointer-events-none">API BRIDGE</div>

                                <svg className="absolute top-0 left-0 w-full h-full pointer-events-none" style={svgStyle}>
                                    <defs>
                                        <marker id="arrowhead" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto">
                                            <polygon points="0 0, 10 3.5, 0 7" fill="#94a3b8" />
                                        </marker>
                                        <marker id="arrowhead-active" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto">
                                            <polygon points="0 0, 10 3.5, 0 7" fill="#10b981" />
                                        </marker>
                                    </defs>
                                    {edgesSvg}
                                </svg>

                                {filteredNodes.map(node => {
                                    const cat = categories[node.category];
                                    const Icon = cat.icon;
                                    const isSelected = selectedNode?.id === node.id;
                                    const isDimmed = node.dimmed;

                                    const nodeStyle = { left: node.x, top: node.y, width: NODE_WIDTH, opacity: isDimmed ? 0.3 : 1 };
                                    const headerStyle = { backgroundColor: cat.color };

                                    return (
                                        <div
                                            key={node.id}
                                            onMouseDown={(e) => handleNodeMouseDown(e, node.id)}
                                            className={`absolute rounded-lg bg-white shadow-lg border-2 transition-transform ${isSelected ? 'border-indigo-500 scale-105 z-10' : 'border-white hover:border-slate-300'} flex flex-col group no-select cursor-pointer`}
                                            style={nodeStyle}
                                        >
                                            <div className="px-3 py-2 text-white font-bold rounded-t-[5px] flex items-center gap-2 text-sm" style={headerStyle}>
                                                <Icon size={14} />
                                                <span className="truncate">{node.label}</span>
                                            </div>
                                            <div className="p-2 bg-slate-50 text-[10px] text-slate-500 rounded-b-lg">
                                                {cat.label}
                                            </div>
                                        </div>
                                    );
                                })}

                                {activePacket && (() => {
                                    const packetStyle = { 
                                        left: activePacket.x, 
                                        top: activePacket.y,
                                        transform: 'translate(-50%, -50%)' 
                                    };
                                    return (
                                        <div 
                                            className="absolute z-50 flex items-center justify-center pointer-events-none transition-all duration-75"
                                            style={packetStyle}
                                        >
                                            <div className="w-6 h-6 bg-emerald-500 rounded-full shadow-lg shadow-emerald-500/50 packet-pulse border-2 border-white"></div>
                                            <div className="absolute top-8 bg-slate-900 text-white text-[12px] font-bold px-3 py-1.5 rounded-full whitespace-nowrap opacity-90 shadow-xl z-50">
                                                {activePacket.msg}
                                            </div>
                                        </div>
                                    );
                                })()}
                            </div>
                        </div>

                        {/* Sidebar Details */}
                        <div className={`fixed top-16 bottom-0 right-0 w-96 bg-white border-l border-slate-200 shadow-2xl transform transition-transform duration-300 ease-in-out z-40 overflow-y-auto ${selectedNode ? 'translate-x-0' : 'translate-x-full'}`}>
                            {selectedNode && (
                                <div className="p-6">
                                    <div className="flex items-start justify-between mb-6">
                                        <div>
                                            {(() => {
                                                const pillStyle = { backgroundColor: categories[selectedNode.category].color };
                                                return (
                                                    <div className="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-bold text-white mb-2" style={pillStyle}>
                                                        {categories[selectedNode.category].label}
                                                    </div>
                                                );
                                            })()}
                                            <h2 className="text-xl font-bold text-slate-800 break-all">{selectedNode.label}</h2>
                                        </div>
                                        <button onClick={() => setSelectedNode(null)} className="p-1 hover:bg-slate-100 rounded-full transition-colors"><Icons.X /></button>
                                    </div>

                                    <div className="space-y-6">
                                        <div className="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                            <h3 className="text-sm font-bold text-slate-700 mb-2 flex items-center gap-2"><Icons.Info size={14} /> Description</h3>
                                            <p className="text-sm text-slate-600 leading-relaxed">{selectedNode.details || "No details provided."}</p>
                                        </div>
                                        
                                        {selectedNode.tech && (
                                            <div>
                                                <h3 className="text-sm font-bold text-slate-700 mb-2">Technology</h3>
                                                <div className="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-2 rounded-lg border border-slate-200">
                                                    {selectedNode.tech}
                                                </div>
                                            </div>
                                        )}

                                        {selectedNode.input && (
                                            <div>
                                                <h3 className="text-sm font-bold text-slate-700 mb-2">Input Parameters</h3>
                                                <div className="font-mono text-xs bg-slate-800 text-slate-200 p-3 rounded-lg overflow-x-auto">
                                                    {selectedNode.input}
                                                </div>
                                            </div>
                                        )}

                                        <div>
                                            <h3 className="text-sm font-bold text-slate-700 mb-3 flex items-center justify-between">
                                                <span className="flex items-center gap-2"><Icons.Maximize2 size={14} /> Content / Methods</span>
                                            </h3>
                                            <div className="space-y-1">
                                                {selectedNode.columns.map((col, idx) => (
                                                    <div key={idx} className="px-3 py-2 text-sm rounded border flex justify-between items-center bg-white border-slate-100 text-slate-600 font-mono">
                                                        <span>{col}</span>
                                                    </div>
                                                ))}
                                            </div>
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