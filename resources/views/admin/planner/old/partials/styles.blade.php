<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<script src="https://cdn.tailwindcss.com"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.css"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                colors: {
                    brandDark: '#164191',
                    sky: '#74b2d4',
                    actionGreen: '#93c21c',
                    lightGreen: '#cfe09b',
                    background: '#f8fafc',
                },
                borderRadius: { '3xl': '1.5rem', '4xl': '2rem' }
            }
        }
    }
</script>

 <style>
        body {
            background-color: #f8fafc;
            background-image: radial-gradient(#74b2d4 0.5px, transparent 0.5px), radial-gradient(#74b2d4 0.5px, #f8fafc 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
        }

        /* Glassmorphism Utilities */
        .glass-panel {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .glass-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .sortable-ghost {
            opacity: 0.4;
            background: #cfe09b;
            border: 2px dashed #93c21c;
        }
        
        .drag-handle { cursor: grab; }
        .drag-handle:active { cursor: grabbing; }

        /* Gantt Specifics */
        .gantt-grid-line {
            border-right: 1px dashed #e2e8f0;
            height: 100%;
            position: absolute;
            top: 0;
        }
        .gantt-bar {
            position: absolute;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            padding: 0 10px;
            font-size: 12px;
            font-weight: 600;
            color: #1e293b;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            cursor: pointer;
            transition: transform 0.2s;
            z-index: 10;
        }
        .gantt-bar:hover {
            transform: scale(1.02);
            z-index: 20;
        }
         

        /* Modal Tabs */
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .tab-btn.active { border-bottom-color: #164191; color: #164191; font-weight: 700; border-bottom-width: 2px; }
        .tab-btn { border-bottom-width: 2px; border-color: transparent; }
        
        /* Avatar Stack */
        .avatar-stack { display: flex; -space-x: 0.5rem; }
        .avatar-stack img { border: 2px solid white; border-radius: 9999px; }
        .avatar-stack { display:flex; }
        .avatar-stack > * { margin-left: -0.5rem; }
        .avatar-stack > *:first-child { margin-left: 0; }


        /* Calendar Grid */
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background-color: #e2e8f0;
        }
        .calendar-day {
            background-color: white;
            min-height: 120px;
            position: relative;
        }
        .calendar-day.today {
            background-color: #f0f9ff;
        }
    </style>