<!-- 🔔 Notification Sidebar -->
<nav id="notificationSidebar"
     class="bg-white shadow"
     style="width: 350px; position: fixed; top: 0; right: 0; height: 100vh; z-index: 1050; overflow-y: auto; display: none;">

    <!-- Header -->
    <div class="bg-primary text-white d-flex justify-content-between align-items-center px-3 py-2">
        <h5 class="mb-0">Benachrichtigungen</h5>
        <button class="btn btn-sm btn-danger text-dark" onclick="toggleSidebar()">
            <i data-feather="x"></i>
        </button>
    </div>

    <!-- Section Content -->
    <div class="px-3 py-2" id="sidebarMenu">
        @php
            $sections = [
                'appointment' => ['label' => 'Termine',    'icon' => 'calendar',    'badge' => 'primary'],
                'task'        => ['label' => 'Aufgaben',   'icon' => 'check-square','badge' => 'warning'],
                'customer'    => ['label' => 'Kunden',     'icon' => 'users',       'badge' => 'success'],
                'lead'        => ['label' => 'Leads',      'icon' => 'trending-up', 'badge' => 'info'],
                'project'     => ['label' => 'Projekte',   'icon' => 'trello',      'badge' => 'dark'],
                'offer'       => ['label' => 'Angebote',   'icon' => 'file-text',   'badge' => 'danger'],
                'rest'        => ['label' => 'Sonstiges',  'icon' => 'info',        'badge' => 'light'],
            ];
        @endphp

        @foreach ($sections as $key => $meta)
            <div class="card border-0 mb-1">
                <div class="card-header bg-primary text-white py-1 px-2 d-flex justify-content-between align-items-center"
                     onclick="toggleCollapse('{{ $key }}')" style="cursor: pointer;">
                    <div class="d-flex align-items-center">
                        <span class="badge badge-{{ $meta['badge'] }} mr-2" id="badge-{{ $key }}">0</span>
                        <i data-feather="{{ $meta['icon'] }}" class="mr-1"></i> {{ $meta['label'] }}
                    </div>
                    <i data-feather="chevron-down" id="icon-{{ $key }}"></i>
                </div>
                <div id="collapse-{{ $key }}" class="collapse-section" style="display: {{ $loop->first ? 'block' : 'none' }};">
                    <ul class="list-group px-2 py-1" id="list-{{ $key }}">
                        <li class="list-group-item list-group-item-action d-flex align-items-start"
                            title="Klicken um als gelesen zu markieren" style="cursor: pointer;">
                            <i data-feather="info" class="text-primary mr-2 mt-1"></i>
                            <span class="font-weight-normal">
                                <strong>Aufgabenstatusänderung:</strong><br>
                                Der Status wurde von <strong>Ramin Sadid</strong> in <span class="text-success">[Starten]</span> geändert
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
</nav>

<!-- 🔘 Backdrop -->
<div id="notificationBackdrop"
     onclick="closeSidebar()"
     style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.4); z-index: 1040;">
</div>

<script>
function toggleCollapse(key) {
    const section = document.getElementById(`collapse-${key}`);
    const icon = document.getElementById(`icon-${key}`);
    const isVisible = section.style.display === "block";

    section.style.display = isVisible ? "none" : "block";
    icon.setAttribute("data-feather", isVisible ? "chevron-down" : "chevron-up");
    feather.replace();
}
</script>
