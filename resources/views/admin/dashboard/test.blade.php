<!-- Enhanced Analytical Dashboard Layout -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Overview</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js'></script>
    <style>
  .fc .fc-toolbar.fc-header-toolbar {
    flex-wrap: wrap;
    gap: 0.5rem;
    padding-bottom: 0.5rem;
  }

  .fc .fc-toolbar-title {
    font-size: 1.1rem;
    text-align: center;
  }

  .fc-daygrid-event, .fc-timegrid-event {
    border: none !important;
  }

  .fc-event {
    padding: 2px 6px !important;
    font-size: 12px !important;
  }

  @media (max-width: 640px) {
    .fc .fc-toolbar.fc-header-toolbar {
      flex-direction: column;
      align-items: stretch;
    }

    .fc .fc-daygrid-day-frame {
      padding: 6px;
    }

    .fc .fc-scroller-harness {
      overflow-x: auto;
    }

    .fc .fc-timegrid-slot-label {
      font-size: 10px;
    }

    .fc .fc-event-title {
      font-size: 11px !important;
    }

    .fc-event {
      padding: 2px 4px !important;
    }
  }
</style>



</head>

<body class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- KPI Cards Row -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8 text-center">
            <div class="bg-white shadow rounded-xl py-6 px-4">
                <h2 class="text-4xl font-bold text-red-600">38</h2>
                <p class="text-gray-600">Ongoing</p>
            </div>
            <div class="bg-white shadow rounded-xl py-6 px-4">
                <h2 class="text-4xl font-bold text-yellow-600">41</h2>
                <p class="text-gray-600">Process</p>
            </div>
            <div class="bg-white shadow rounded-xl py-6 px-4">
                <h2 class="text-4xl font-bold text-green-600">40.8%</h2>
                <p class="text-gray-600">Completed</p>
            </div>
        </div>

        <!-- Circular Progress Chart + Goal -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white shadow rounded-xl p-6 flex flex-col items-center">
                <canvas id="progressDonut" width="120" height="120"></canvas>
                <p class="mt-4 font-semibold text-gray-700">Tasks Summary</p>
                <button class="mt-2 text-sm bg-blue-500 text-white px-3 py-1 rounded">View Tasks</button>
            </div>
            <div class="col-span-2 bg-white shadow rounded-xl p-6">
                <p class="text-green-600 font-semibold mb-2">✅ You've almost reached your goal</p>
                <p class="text-gray-600 text-sm">75% of your goals are completed, just complete 25% of remaining goals
                </p>
                <div id="goalList" class="space-y-3">
                    <div
                        class="border-l-4 border-red-500 bg-white rounded-xl p-4 flex items-center justify-between shadow">
                        <div class="flex items-center gap-2">
                            <i data-feather="alert-circle" class="text-red-500"></i>
                            <input type="checkbox">
                            <span class="text-sm font-medium">Urgent Task: Submit project report</span>
                        </div>
                        <span class="text-xs px-2 py-1 bg-red-100 text-red-600 rounded-full">Task</span>
                    </div>
                    <div
                        class="border-l-4 border-yellow-500 bg-white rounded-xl p-4 flex items-center justify-between shadow">
                        <div class="flex items-center gap-2">
                            <i data-feather="tool" class="text-yellow-500"></i>
                            <input type="checkbox">
                            <span class="text-sm font-medium">Critical Project: Solar Panel Installation</span>
                        </div>
                        <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-600 rounded-full">Project</span>
                    </div>
                    <div
                        class="border-l-4 border-blue-500 bg-white rounded-xl p-4 flex items-center justify-between shadow">
                        <div class="flex items-center gap-2">
                            <i data-feather="edit" class="text-blue-500"></i>
                            <input type="checkbox">
                            <span class="text-sm font-medium">Note Reminder: Client feedback meeting prep</span>
                        </div>
                        <span class="text-xs px-2 py-1 bg-blue-100 text-blue-600 rounded-full">Note</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inject Chart.js donut -->
        <script>
        const ctx = document.getElementById('progressDonut').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Completed', 'Assigned'],
                datasets: [{
                    data: [30, 40, 30],
                    backgroundColor: ['#10b981', '#3b82f6', '#facc15'],
                    borderWidth: 1
                }]
            },
            options: {
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        </script>

        <!-- Task, Appointment, Project, Offer, Note Tabs -->
        <div class="mt-10">
            <div class="flex items-center justify-between mb-4">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="tabs">
                    <li class="me-2"><button class="tab-button border-b-2 border-blue-500 text-blue-600 px-4 py-2"
                            data-tab="all">All</button></li>
                    <li class="me-2"><button class="tab-button px-4 py-2" data-tab="tasks">Tasks</button></li>
                    <li class="me-2"><button class="tab-button px-4 py-2" data-tab="appointments">Appointments</button>
                    </li>
                    <li class="me-2"><button class="tab-button px-4 py-2" data-tab="projects">Projects</button></li>
                    <li class="me-2"><button class="tab-button px-4 py-2" data-tab="offers">Offers</button></li>
                    <li class="me-2"><button class="tab-button px-4 py-2" data-tab="notes">Notes</button></li>
                    <li class="me-2"><button class="tab-button px-4 py-2" data-tab="calendar">Calendar</button></li>

                </ul>
                <div>
                    <input type="text" id="searchBar" placeholder="Search..."
                        class="border rounded px-3 py-1 text-sm w-64">
                </div>
            </div>
            <div id="tab-content">
                <div class="flex flex-col items-center justify-center mb-4">
                    <!-- Tabs -->
                    <ul class="flex flex-wrap justify-center text-sm font-medium text-center space-x-2 mb-2" id="tabs">
                        <li>
                            <button class="tab-button border-b-2 border-blue-500 text-blue-600 px-4 py-2" data-tab="all">
                                All <span class="ml-1 bg-gray-200 text-gray-700 rounded-full px-2 text-xs" id="allCount">0</span>
                            </button>
                        </li>
                        <li>
                            <button class="tab-button px-4 py-2" data-tab="tasks">
                                Tasks <span class="ml-1 bg-gray-200 text-gray-700 rounded-full px-2 text-xs" id="taskCount">0</span>
                            </button>
                        </li>
                        <li>
                            <button class="tab-button px-4 py-2" data-tab="appointments">
                                Appointments <span class="ml-1 bg-gray-200 text-gray-700 rounded-full px-2 text-xs" id="appointmentCount">0</span>
                            </button>
                        </li>
                        <li>
                            <button class="tab-button px-4 py-2" data-tab="projects">
                                Projects <span class="ml-1 bg-gray-200 text-gray-700 rounded-full px-2 text-xs" id="projectCount">0</span>
                            </button>
                        </li>
                        <li>
                            <button class="tab-button px-4 py-2" data-tab="offers">
                                Offers <span class="ml-1 bg-gray-200 text-gray-700 rounded-full px-2 text-xs" id="offerCount">0</span>
                            </button>
                        </li>
                        <li>
                            <button class="tab-button px-4 py-2" data-tab="notes">
                                Notes <span class="ml-1 bg-gray-200 text-gray-700 rounded-full px-2 text-xs" id="noteCount">0</span>
                            </button>
                        </li>
                        <li>
                            <button class="tab-button px-4 py-2" data-tab="calendar">
                                Calendar <span class="ml-1 bg-gray-200 text-gray-700 rounded-full px-2 text-xs" id="calendarCount">0</span>
                            </button>
                        </li>
                    </ul>

                    <!-- Search Bar -->
                    <input type="text" id="searchBar" placeholder="Search..."
                        class="border rounded px-3 py-1 text-sm w-64 text-center">
                </div>


                <div class="tab-panel hidden" id="tab-calendar">
                    <!-- Calendar Controls -->
                    <div class="mb-4 flex flex-wrap items-center gap-4">
                        
                        <!-- Date Filter -->
                        <div class="flex items-center gap-2">
                        <label for="calendarDatePicker" class="text-sm font-semibold text-gray-700">📅 Jump to date:</label>
                        <input type="date" id="calendarDatePicker" class="border rounded px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <button id="jumpToDateBtn" class="bg-blue-500 text-white px-3 py-1 text-sm rounded shadow hover:bg-blue-600">Go</button>
                        </div>

                        <!-- Employee Dropdown -->
                        <div class="relative">
                        <button id="toggleDropdown" class="bg-gray-100 border px-3 py-1 rounded text-sm shadow hover:bg-gray-200">
                            👥 Select Employees
                        </button>
                        <div id="employeeDropdown" class="absolute mt-2 bg-white border rounded shadow w-64 p-2 hidden z-50 max-h-60 overflow-auto">
                            <!-- Employee checkboxes will be inserted by JS -->
                        </div>
                        </div>
                    </div>

                    <!-- Selected Employee Avatars -->
                    <div id="selectedEmployees" class="flex flex-wrap gap-2 mb-4"></div>

                    <!-- Calendar itself -->
                    <div id="calendar" class="bg-white rounded shadow p-4"></div>
                    </div>



            </div>
        </div>

            <!-- Event Modal -->
            <div id="eventModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 items-center justify-center" onclick="outsideModalClick(event)">
            <div id="eventModalContent" class="bg-white rounded-xl shadow-xl w-full max-w-md mx-auto p-6 relative">
            <button onclick="closeEventModal()" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">
                    <i class="ri-close-line text-2xl"></i>
                </button>
                <div class="flex items-center gap-4 mb-4">
                    <img id="modalAvatar" src="" alt="Avatar" class="w-12 h-12 rounded-full object-cover border" />
                    
                    <div>
                        <h2 id="modalTitle" class="text-lg font-bold text-gray-800"></h2>
                        <p id="modalTime" class="text-sm text-gray-500"></p>
                    </div>
                    <div id="modalPeople" class="flex flex-wrap gap-1 mt-3"></div>


                </div>
                <div>
                    <p id="modalDescription" class="text-gray-700 text-sm"></p>
                </div>
            </div>
        </div>
        <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Initialize feather icons
            feather.replace();

            // Initialize SortableJS
            new Sortable(document.getElementById('goalList'), {
                animation: 150,
                ghostClass: 'bg-yellow-100'
            });

            // Tab Switching
            const tabs = document.querySelectorAll('.tab-button');
            const panels = document.querySelectorAll('.tab-panel');

            tabs.forEach(button => {
                button.addEventListener('click', () => {
                    tabs.forEach(btn => btn.classList.remove('border-b-2', 'border-blue-500',
                        'text-blue-600'));
                    panels.forEach(panel => panel.classList.add('hidden'));

                    button.classList.add('border-b-2', 'border-blue-500', 'text-blue-600');
                    document.getElementById(`tab-${button.dataset.tab}`).classList.remove(
                        'hidden');
                });
            });

            // Search Filter
            const searchBar = document.getElementById('searchBar');
            searchBar.addEventListener('input', () => {
                const keyword = searchBar.value.toLowerCase();
                document.querySelectorAll('.tab-panel:not(.hidden) .bg-white').forEach(card => {
                    const text = card.textContent.toLowerCase();
                    card.style.display = text.includes(keyword) ? 'block' : 'none';
                });
            });
        });

        </script>
        <script>
        document.addEventListener('DOMContentLoaded', () => {
        const calendarEl = document.getElementById('calendar');
        if (!calendarEl) return;

        let selectedEmployeeIds = [];

        const employees = [
            { id: 1, name: 'Alice', avatar: 'https://i.pravatar.cc/100?img=1' },
            { id: 2, name: 'Bob', avatar: 'https://i.pravatar.cc/100?img=2' },
            { id: 3, name: 'Charlie', avatar: 'https://i.pravatar.cc/100?img=3' },
            { id: 4, name: 'Diana', avatar: 'https://i.pravatar.cc/100?img=4' },
            { id: 5, name: 'Ethan', avatar: 'https://i.pravatar.cc/100?img=5' }
        ];

        const allCalendarEvents = [
            {
            title: 'Quarterly Strategy Call',
            start: '2025-06-17T15:00:00',
            backgroundColor: '#f472b6',
            extendedProps: {
                description: 'Important high-level planning and review',
                employeeIds: [1, 2, 3, 4, 5],
                people: employees.map(e => e.avatar)
            }
            },
            {
            title: 'Check-in Call',
            start: '2025-06-11T15:00:00',
            backgroundColor: '#34d399',
            extendedProps: {
                description: 'Short sync with team',
                employeeIds: [2],
                people: [employees[1].avatar]
            }
            }
        ];

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: window.innerWidth < 640 ? 'timeGridDay' : 'dayGridMonth',
            height: 'auto',
            headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: (info, successCallback) => {
            const filtered = selectedEmployeeIds.length
                ? allCalendarEvents.filter(e =>
                    (e.extendedProps.employeeIds || []).some(id => selectedEmployeeIds.includes(id))
                )
                : allCalendarEvents;
            successCallback(filtered);
            },
            eventClick: function(info) {
            info.jsEvent.preventDefault();
            openEventModal(info.event);
            },
            eventContent: function(arg) {
            const people = arg.event.extendedProps.people || [];
            const visible = people.slice(0, 4);
            const hiddenCount = people.length - visible.length;

            const avatarHTML = visible.map(src =>
                `<img src="${src}" class="avatar w-5 h-5 rounded-full border border-white -ml-2 first:ml-0" />`
            ).join('');

            const plusHTML = hiddenCount > 0
                ? `<div class="w-5 h-5 text-xs bg-gray-200 text-gray-600 rounded-full flex items-center justify-center -ml-2 border border-white">+${hiddenCount}</div>`
                : '';

            return {
                html: `
                <div style="
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    background-color: ${arg.event.backgroundColor || '#3b82f6'};
                    color: white;
                    border-radius: 6px;
                    padding: 2px 6px;
                    font-size: 12px;
                    max-width: 100%;
                    overflow: hidden;">
                    <div class="fc-event-title" style="flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    ${arg.event.title}
                    </div>
                    <div class="flex ml-2">${avatarHTML}${plusHTML}</div>
                </div>`
            };
            }
        });

        calendar.render();

        // UI Setup
        document.getElementById('calendarDatePicker').valueAsDate = new Date();
        document.getElementById('jumpToDateBtn').addEventListener('click', () => {
            const date = document.getElementById('calendarDatePicker').value;
            if (date) calendar.gotoDate(date);
        });

        document.getElementById('toggleDropdown').addEventListener('click', () => {
            document.getElementById('employeeDropdown').classList.toggle('hidden');
        });

        function renderEmployeeDropdown() {
            const container = document.getElementById('employeeDropdown');
            container.innerHTML = '';
            employees.forEach(emp => {
            const checked = selectedEmployeeIds.includes(emp.id) ? 'checked' : '';
            container.innerHTML += `
                <label class="flex items-center gap-2 py-1 cursor-pointer">
                <input type="checkbox" ${checked} onchange="handleEmployeeSelection(${emp.id})">
                <img src="${emp.avatar}" class="w-6 h-6 rounded-full border">
                <span class="text-sm">${emp.name}</span>
                </label>`;
            });
        }

        function renderSelectedAvatars() {
            const container = document.getElementById('selectedEmployees');
            container.innerHTML = '';
            employees.filter(emp => selectedEmployeeIds.includes(emp.id)).forEach(emp => {
            container.innerHTML += `
                <div class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded shadow">
                <img src="${emp.avatar}" class="w-5 h-5 rounded-full border">
                <span class="text-xs">${emp.name}</span>
                </div>`;
            });
        }

        window.handleEmployeeSelection = function(empId) {
            const index = selectedEmployeeIds.indexOf(empId);
            if (index > -1) {
            selectedEmployeeIds.splice(index, 1);
            } else {
            selectedEmployeeIds.push(empId);
            }
            renderSelectedAvatars();
            renderEmployeeDropdown();
            calendar.refetchEvents();
        };

        function openEventModal(event) {
            document.getElementById('modalTitle').textContent = event.title;
            document.getElementById('modalTime').textContent = new Date(event.start).toLocaleString();
            document.getElementById('modalDescription').textContent = event.extendedProps.description || 'No description provided.';
            document.getElementById('modalAvatar').src = event.extendedProps.people?.[0] || 'https://i.pravatar.cc/100?u=default';

            const peopleHTML = (event.extendedProps.people || []).map(src =>
            `<img src="${src}" class="w-7 h-7 rounded-full border border-white shadow" />`
            ).join('');
            document.getElementById('modalPeople').innerHTML = peopleHTML;

            document.getElementById('eventModal').classList.remove('hidden');
            document.getElementById('eventModal').classList.add('flex');
        }

        function closeEventModal() {
            document.getElementById('eventModal').classList.remove('flex');
            document.getElementById('eventModal').classList.add('hidden');
        }

        window.outsideModalClick = function(event) {
            const modalContent = document.getElementById('eventModalContent');
            if (!modalContent.contains(event.target)) closeEventModal();
        };

        // Render on load
        renderEmployeeDropdown();
        renderSelectedAvatars();

        // Responsive change view
        window.addEventListener('resize', () => {
            calendar.changeView(window.innerWidth < 640 ? 'timeGridDay' : 'dayGridMonth');
        });

        // Tab re-render fix
        document.querySelector('[data-tab="calendar"]')?.addEventListener('click', () => {
            setTimeout(() => calendar.render(), 100);
        });
        });
        </script>


    </div>



</body>

</html>