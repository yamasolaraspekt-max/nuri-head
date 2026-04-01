@extends('admin.layouts.app')

@section('title') Employee Dashboard @endsection
@section('style')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="{{ asset('css/icon.min.css')}}" rel="stylesheet" type="text/css" />
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://unpkg.com/feather-icons"></script>
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

.fc-daygrid-event,
.fc-timegrid-event {
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

@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">

            </div>
        </div>
        <div class="content-body">
            <div class="max-w-8xl mx-auto px-4 py-8">
                <!-- KPI Cards Row -->
                @php
                $user = DB::table('employees')
                ->select('name', 'lastname', 'image')
                ->where('id', auth()->user()->name) // Double-check this!
                ->first();

                $full_name = $user ? $user->name . ' ' . $user->lastname : 'Benutzer';
                $image_path = $user ? asset('images/employee/' . $user->image) : asset('images/default-user.png');
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 mb-8 text-center">
                    <!-- User Welcome Card -->
                    <div class="bg-white shadow rounded-xl py-6 px-4 flex flex-col items-center justify-center">
                        <img src="{{ $image_path }}" alt="Profilbild" class="w-16 h-16 rounded-full mb-2 shadow">
                        <h2 class="text-xl font-semibold text-gray-800">Willkommen, {{ $full_name }}!</h2>
                        <p class="text-gray-500">Schön, dass Sie wieder da sind.</p>
                    </div>

                    <!-- Stat Cards -->
                    <div class="bg-white shadow rounded-xl py-6 px-4 flex flex-col items-center justify-center">
                        <h2 class="text-4xl font-bold text-red-600">38</h2>
                        <p class="text-gray-600 mt-2">Laufend</p>
                    </div>
                    <div class="bg-white shadow rounded-xl py-6 px-4 flex flex-col items-center justify-center">
                        <h2 class="text-4xl font-bold text-yellow-600">41</h2>
                        <p class="text-gray-600 mt-2">In Bearbeitung</p>
                    </div>
                    <div class="bg-white shadow rounded-xl py-6 px-4 flex flex-col items-center justify-center">
                        <h2 class="text-4xl font-bold text-green-600">40.8%</h2>
                        <p class="text-gray-600 mt-2">Abgeschlossen</p>
                    </div>
                </div>


                <!-- Circular Progress Chart + Goal -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white shadow rounded-xl p-4 flex flex-col items-center justify-center">
                        <canvas id="progressDonut" width="100" height="100"></canvas>
                        <p class="mt-2 text-sm font-semibold text-gray-700">Tasks Summary</p>
                        <button class="mt-1 text-xs bg-blue-500 text-white px-2 py-1 rounded">View Tasks</button>
                    </div>
                    <div class="col-span-2 bg-white shadow rounded-xl p-4">
                        <p class="text-green-600 text-sm font-semibold mb-1">✅ Fast geschafft!</p>
                        <p class="text-xs text-gray-600 mb-3">75% der Ziele sind erreicht – nur noch 25% fehlen!</p>
                        <div id="goalList" class="space-y-2 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300"
                            style="max-height: 150px;">
                            <div
                                class="border-l-4 border-red-500 bg-white rounded p-1 flex items-center justify-between shadow">
                                <div class="flex items-center gap-1">
                                    <i data-feather="alert-circle" class="text-red-500 w-4 h-4"></i>
                                    <input type="checkbox">
                                    <span class="text-xs font-medium">Projektbericht abgeben</span>
                                </div>
                                <span class="text-xs px-2 py-0.5 bg-red-100 text-red-600 rounded-full">Task</span>
                            </div>
                            <div
                                class="border-l-4 border-yellow-500 bg-white rounded p-1 flex items-center justify-between shadow">
                                <div class="flex items-center gap-1">
                                    <i data-feather="tool" class="text-yellow-500 w-4 h-4"></i>
                                    <input type="checkbox">
                                    <span class="text-xs font-medium">Solaranlage installieren</span>
                                </div>
                                <span
                                    class="text-xs px-2 py-0.5 bg-yellow-100 text-yellow-600 rounded-full">Project</span>
                            </div>
                            <div
                                class="border-l-4 border-blue-500 bg-white rounded p-1 flex items-center justify-between shadow">
                                <div class="flex items-center gap-1">
                                    <i data-feather="edit" class="text-blue-500 w-4 h-4"></i>
                                    <input type="checkbox">
                                    <span class="text-xs font-medium">Kundenvorbereitung Feedback</span>
                                </div>
                                <span class="text-xs px-2 py-0.5 bg-blue-100 text-blue-600 rounded-full">Note</span>
                            </div>

                            <div
                                class="border-l-4 border-yellow-500 bg-white rounded p-1 flex items-center justify-between shadow">
                                <div class="flex items-center gap-1">
                                    <i data-feather="tool" class="text-yellow-500 w-4 h-4"></i>
                                    <input type="checkbox">
                                    <span class="text-xs font-medium">Solaranlage installieren</span>
                                </div>
                                <span
                                    class="text-xs px-2 py-0.5 bg-yellow-100 text-yellow-600 rounded-full">Project</span>
                            </div>
                            <div
                                class="border-l-4 border-blue-500 bg-white rounded p-1 flex items-center justify-between shadow">
                                <div class="flex items-center gap-1">
                                    <i data-feather="edit" class="text-blue-500 w-4 h-4"></i>
                                    <input type="checkbox">
                                    <span class="text-xs font-medium">Kundenvorbereitung Feedback</span>
                                </div>
                                <span class="text-xs px-2 py-0.5 bg-blue-100 text-blue-600 rounded-full">Note</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Inject Chart.js donut -->

                <script>
                const ctx = document.getElementById('progressDonut').getContext('2d');
                const donutData = [30, 40, 30]; // active, completed, assigned

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Active', 'Completed', 'Assigned'],
                        datasets: [{
                            data: donutData,
                            backgroundColor: ['#10b981', '#3b82f6', '#facc15'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        cutout: '85%',
                        responsive: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            centerText: {
                                display: true
                            }
                        }
                    },
                    plugins: [{
                        id: 'centerText',
                        beforeDraw(chart) {
                            const {
                                width,
                                height,
                                ctx
                            } = chart;
                            const total = donutData.reduce((a, b) => a + b, 0);
                            const completed = donutData[1]; // 40
                            const percent = Math.round((completed / total) * 100);

                            ctx.save();
                            ctx.font = 'bold 10px sans-serif';
                            ctx.fillStyle = '#1f2937'; // dark-gray
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillText(`${percent}%`, width / 2, height / 2);
                            ctx.restore();
                        }
                    }]
                });
                </script>

                <!-- Task, Appointment, Project, Offer, Note Tabs -->
                    <div class="mt-10">
                        <div id="tab-content">
                            <div class="flex flex-col items-center justify-center mb-4">
                                <!-- Tabs -->
                                <ul class="flex flex-wrap justify-center text-sm font-medium text-center space-x-2 mb-2"
                                    id="tabs">
                                    <li>
                                        <button class="tab-button border-b-2 border-blue-500 text-blue-600 px-4 py-2"
                                            data-tab="all">
                                            All <span class="ml-1 bg-gray-200 text-gray-700 rounded-full px-2 text-xs"
                                                id="allCount">0</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button class="tab-button px-4 py-2" data-tab="tasks">
                                            Tasks <span class="ml-1 bg-gray-200 text-gray-700 rounded-full px-2 text-xs"
                                                id="taskCount">0</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button class="tab-button px-4 py-2" data-tab="appointments">
                                            Appointments <span
                                                class="ml-1 bg-gray-200 text-gray-700 rounded-full px-2 text-xs"
                                                id="appointmentCount">0</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button class="tab-button px-4 py-2" data-tab="projects">
                                            Projects <span class="ml-1 bg-gray-200 text-gray-700 rounded-full px-2 text-xs"
                                                id="projectCount">0</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button class="tab-button px-4 py-2" data-tab="offers">
                                            Offers <span class="ml-1 bg-gray-200 text-gray-700 rounded-full px-2 text-xs"
                                                id="offerCount">0</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button class="tab-button px-4 py-2" data-tab="notes">
                                            Notes <span class="ml-1 bg-gray-200 text-gray-700 rounded-full px-2 text-xs"
                                                id="noteCount">0</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button class="tab-button px-4 py-2" data-tab="calendar">
                                            Calendar <span class="ml-1 bg-gray-200 text-gray-700 rounded-full px-2 text-xs"
                                                id="calendarCount">0</span>
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
                                        <label for="calendarDatePicker" class="text-sm font-semibold text-gray-700">📅 Jump
                                            to date:</label>
                                        <input type="date" id="calendarDatePicker"
                                            class="border rounded px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                        <button id="jumpToDateBtn"
                                            class="bg-blue-500 text-white px-3 py-1 text-sm rounded shadow hover:bg-blue-600">Go</button>
                                    </div>

                                    <!-- Employee Dropdown -->
                                    <div class="relative">
                                        <button id="toggleDropdown"
                                            class="bg-gray-100 border px-3 py-1 rounded text-sm shadow hover:bg-gray-200">
                                            👥 Select Employees
                                        </button>
                                        <div id="employeeDropdown"
                                            class="absolute mt-2 bg-white border rounded shadow w-64 p-2 hidden z-50 max-h-60 overflow-auto">
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

                        <div id="tab-content">
                            <div class="tab-panel" id="tab-all">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <!-- Cards for each tab -->
                                    <!-- All previous cards moved here -->
                                    <div class="bg-white rounded-xl shadow-md p-2">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="flex items-center gap-2 text-blue-600 font-semibold">
                                                <i class="ri-checkbox-circle-line text-xl"></i> Task
                                            </div>
                                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded">High</span>
                                        </div>
                                        <p class="text-gray-600">Fix API integration issues and update error handling.</p>
                                        <div class="mt-3 text-sm text-gray-500 flex justify-between">
                                            <span>🗓️ 20 Mar – 30 Nov 2024</span>
                                            <span class="text-blue-600 font-medium">In Progress</span>
                                        </div>
                                    </div>
                                    <div class="bg-white rounded-xl shadow-md p-2">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="flex items-center gap-2 text-green-600 font-semibold">
                                                <i class="ri-calendar-line text-xl"></i> Appointment
                                            </div>
                                            <span
                                                class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded">Confirmed</span>
                                        </div>
                                        <p class="text-gray-600">Senior team leader interview.</p>
                                        <div class="mt-3 text-sm text-gray-500 flex justify-between">
                                            <span>📅 15 Aug 2024</span>
                                            <span>🕒 AM 10:15</span>
                                        </div>
                                    </div>
                                    <div class="bg-white rounded-xl shadow-md p-2">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="flex items-center gap-2 text-yellow-600 font-semibold">
                                                <i class="ri-folder-2-line text-xl"></i> Project
                                            </div>
                                            <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded">In
                                                Progress</span>
                                        </div>
                                        <p class="text-gray-600">Bank Data Management – API and reporting implementation.
                                        </p>
                                        <div class="w-full bg-gray-100 h-2 rounded-full mt-3">
                                            <div class="bg-yellow-400 h-2 rounded-full" style="width: 70%"></div>
                                        </div>
                                        <p class="text-xs text-right text-gray-400 mt-1">Progress: 70%</p>
                                    </div>
                                    <div class="bg-white rounded-xl shadow-md p-2">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="flex items-center gap-2 text-pink-600 font-semibold">
                                                <i class="ri-price-tag-3-line text-xl"></i> Offer
                                            </div>
                                            <span
                                                class="bg-pink-100 text-pink-700 text-xs px-2 py-0.5 rounded">Active</span>
                                        </div>
                                        <p class="text-gray-600">Exclusive: 25% off premium features for summer campaign.
                                        </p>
                                        <div class="mt-3 text-sm text-gray-500 flex justify-between">
                                            <span>📆 Valid until: 30 June</span>
                                            <span>💶 €2.500</span>
                                        </div>
                                    </div>
                                    <div class="bg-white rounded-xl shadow-md p-2">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="flex items-center gap-2 text-purple-600 font-semibold">
                                                <i class="ri-sticky-note-line text-xl"></i> Note
                                            </div>
                                            <span
                                                class="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded">Reminder</span>
                                        </div>
                                        <p class="text-gray-600">Review July analytics, send team feedback summary.</p>
                                        <div class="mt-3 text-sm text-right text-gray-500">🗓️ Last updated: 02 Jun 2025
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-panel hidden" id="tab-tasks">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div class="bg-white rounded-xl shadow-md p-2">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="flex items-center gap-2 text-blue-600 font-semibold">
                                                <i class="ri-checkbox-circle-line text-xl"></i> Task
                                            </div>
                                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded">High</span>
                                        </div>
                                        <p class="text-gray-600">Fix API integration issues and update error handling.</p>
                                        <div class="mt-3 text-sm text-gray-500 flex justify-between">
                                            <span>🗓️ 20 Mar – 30 Nov 2024</span>
                                            <span class="text-blue-600 font-medium">In Progress</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-panel hidden" id="tab-appointments">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div class="bg-white rounded-xl shadow-md p-2">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="flex items-center gap-2 text-green-600 font-semibold">
                                                <i class="ri-calendar-line text-xl"></i> Appointment
                                            </div>
                                            <span
                                                class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded">Confirmed</span>
                                        </div>
                                        <p class="text-gray-600">Senior team leader interview.</p>
                                        <div class="mt-3 text-sm text-gray-500 flex justify-between">
                                            <span>📅 15 Aug 2024</span>
                                            <span>🕒 AM 10:15</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-panel hidden" id="tab-projects">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div class="bg-white rounded-xl shadow-md p-2">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="flex items-center gap-2 text-yellow-600 font-semibold">
                                                <i class="ri-folder-2-line text-xl"></i> Project
                                            </div>
                                            <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded">In
                                                Progress</span>
                                        </div>
                                        <p class="text-gray-600">Bank Data Management – API and reporting implementation.
                                        </p>
                                        <div class="w-full bg-gray-100 h-2 rounded-full mt-3">
                                            <div class="bg-yellow-400 h-2 rounded-full" style="width: 70%"></div>
                                        </div>
                                        <p class="text-xs text-right text-gray-400 mt-1">Progress: 70%</p>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-panel hidden" id="tab-offers">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div class="bg-white rounded-xl shadow-md p-2">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="flex items-center gap-2 text-pink-600 font-semibold">
                                                <i class="ri-price-tag-3-line text-xl"></i> Offer
                                            </div>
                                            <span
                                                class="bg-pink-100 text-pink-700 text-xs px-2 py-0.5 rounded">Active</span>
                                        </div>
                                        <p class="text-gray-600">Exclusive: 25% off premium features for summer campaign.
                                        </p>
                                        <div class="mt-3 text-sm text-gray-500 flex justify-between">
                                            <span>📆 Valid until: 30 June</span>
                                            <span>💶 €2.500</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-panel hidden" id="tab-notes">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div class="bg-white rounded-xl shadow-md p-2">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="flex items-center gap-2 text-purple-600 font-semibold">
                                                <i class="ri-sticky-note-line text-xl"></i> Note
                                            </div>
                                            <span
                                                class="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded">Reminder</span>
                                        </div>
                                        <p class="text-gray-600">Review July analytics, send team feedback summary.</p>
                                        <div class="mt-3 text-sm text-right text-gray-500">🗓️ Last updated: 02 Jun 2025
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-panel hidden" id="tab-calendar">
                                <!-- Calendar Controls -->
                                <div class="mb-4 flex flex-wrap items-center gap-1">

                                    <!-- Date Filter -->
                                    <div class="flex items-center gap-2">
                                        <label for="calendarDatePicker" class="text-sm font-semibold text-gray-700">📅 Jump
                                            to date:</label>
                                        <input type="date" id="calendarDatePicker"
                                            class="border rounded px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                        <button id="jumpToDateBtn"
                                            class="bg-blue-500 text-white px-3 py-1 text-sm rounded shadow hover:bg-blue-600">Go</button>
                                    </div>

                                    <!-- Employee Dropdown -->
                                    <div class="relative">
                                        <button id="toggleDropdown"
                                            class="bg-gray-100 border px-3 py-1 rounded text-sm shadow hover:bg-gray-200">
                                            👥 Select Employees
                                        </button>
                                        <div id="employeeDropdown"
                                            class="absolute mt-2 bg-white border rounded shadow w-64 p-2 hidden z-50 max-h-60 overflow-auto">
                                            <!-- Employee checkboxes will be inserted by JS -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Selected Employee Avatars -->
                                <div id="selectedEmployees" class="flex flex-wrap gap-2 mb-4"></div>

                                <!-- Calendar itself -->
                                <div id="calendar" class="bg-white rounded shadow p-1"></div>
                            </div>



                        </div>
                    </div>

                <!-- Event Modal -->
                <div id="eventModal"
                    class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 items-center justify-center"
                    onclick="outsideModalClick(event)">
                    <div id="eventModalContent"
                        class="bg-white rounded-xl shadow-xl w-full max-w-md mx-auto p-6 relative">
                        <button onclick="closeEventModal()"
                            class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">
                            <i class="ri-close-line text-2xl"></i>
                        </button>
                        <div class="flex items-center gap-1 mb-4">
                            <img id="modalAvatar" src="" alt="Avatar"
                                class="w-12 h-12 rounded-full object-cover border" />

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


            </div>


        </div>
    </div>
</div>

@endsection

@section('script')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://unpkg.com/feather-icons"></script>

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

    const employees = [{
            id: 1,
            name: 'Alice',
            avatar: 'https://i.pravatar.cc/100?img=1'
        },
        {
            id: 2,
            name: 'Bob',
            avatar: 'https://i.pravatar.cc/100?img=2'
        },
        {
            id: 3,
            name: 'Charlie',
            avatar: 'https://i.pravatar.cc/100?img=3'
        },
        {
            id: 4,
            name: 'Diana',
            avatar: 'https://i.pravatar.cc/100?img=4'
        },
        {
            id: 5,
            name: 'Ethan',
            avatar: 'https://i.pravatar.cc/100?img=5'
        }
    ];

    const allCalendarEvents = [{
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
            const filtered = selectedEmployeeIds.length ?
                allCalendarEvents.filter(e =>
                    (e.extendedProps.employeeIds || []).some(id => selectedEmployeeIds.includes(id))
                ) :
                allCalendarEvents;
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

            const plusHTML = hiddenCount > 0 ?
                `<div class="w-5 h-5 text-xs bg-gray-200 text-gray-600 rounded-full flex items-center justify-center -ml-2 border border-white">+${hiddenCount}</div>` :
                '';

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
        document.getElementById('modalDescription').textContent = event.extendedProps.description ||
            'No description provided.';
        document.getElementById('modalAvatar').src = event.extendedProps.people?. [0] ||
            'https://i.pravatar.cc/100?u=default';

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




<script>
    document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.addEventListener('click', function () {
            const tab = this.dataset.tab;
            loadTabData(tab);
        });
    });

    function loadTabData(tab) {
        fetch("/dashboard/load-tab", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ tab: tab })
        })
        .then(res => res.text())
        .then(html => {
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
            document.querySelector(`#tab-${tab}`).classList.remove('hidden');
            document.querySelector(`#tab-${tab}`).innerHTML = html;
        });
    }

    function updateTabCounts() {
        fetch('/dashboard/tab-counts')
            .then(res => res.json())
            .then(data => {
                document.getElementById('taskCount').textContent = data.tasks;
                document.getElementById('appointmentCount').textContent = data.appointments;
                document.getElementById('projectCount').textContent = data.projects;
                document.getElementById('offerCount').textContent = data.offers;
                document.getElementById('noteCount').textContent = data.notes;
                document.getElementById('allCount').textContent =
                    data.tasks + data.appointments + data.projects + data.offers + data.notes;
            });
    }

    updateTabCounts();
});

</script>
@endsection