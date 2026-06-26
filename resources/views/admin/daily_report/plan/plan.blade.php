<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Plan Timeline</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/katex.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/monokai-sublime.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css') }}">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/colors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/dark-layout.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/semi-dark-layout.css') }}">
    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-gradient.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/authentication.css') }}">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f1f1;
            margin: 0;
        }
        nav {
            background-color: #f1f1f1;
            color: #74b2d4;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
        }
        nav .nav-title {
            font-size: 20px;
            font-weight: bold;
        }
        nav .nav-actions button {
            margin-left: 10px;
            padding: 8px 14px;
            border: none;
            background-color: #8fc73e;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }
        nav .nav-actions button:hover {
            background-color: #8fc73e;
        }
        #map {
            width: 100%;
            height: 400px;
            margin: 20px auto;  
        }
        h2 {
            text-align: center;
            color: #3b3f5c;
            margin:0;
        }
        .timeline {
            position: relative;
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px 0;
        }
        .timeline::after {
            content: '';
            position: absolute;
            width: 4px;
            background-color: #c5cbca;
            top: 0;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }
        .timeline-item {
            padding: 20px;
            position: relative;
            width: 50%;
        }
        .timeline-item.left {
            left: -33px;
        }
        .timeline-item.right {
            left: 54%;
        }
        .timeline-item::before {
          content: '';
            position: absolute;
            top: 20px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #8fc73e;
            border: 3px solid #ffffff;
            z-index: 1;
        }
        .timeline-item.left::before {
            right: -8px;
        }
        .timeline-item.right::before {
            left: -8px;
        }
        .timeline-content {
              background-color: #ffffff;
            border-radius: 8px;
            padding: 4px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .timeline-content h3 {
            margin-top: 0;
            background-color: #8fc73e;
            color: white;
            padding: 10px;
            border-radius: 6px 6px 0 0;
        }

        .task_design {
            background-color: #74b2d4 !important;
        }

         .appointment_design {
            background-color: #8fc73e !important;
        }
        .timeline-content p {
            margin-bottom: 10px;
            font-size: 15px;
        }
        .btn-group {
            margin-top: 10px;
        }
        .btn-group button {
            margin-right: 10px;
            padding: 6px 14px;
            border: none;
            background-color: #3b3f5c;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-group button:hover {
            background-color: #2c2f45;
        }
        @media screen and (max-width: 768px) {
            .timeline::after {
                left: 8px;
                transform: none;
            }
            .timeline-item.left, .timeline-item.right {
                width: 100%;
                left: 0 !important;
                padding-left: 40px;
                padding-right: 20px;
                margin-bottom: 30px;
            }
            .timeline-item.left::before, .timeline-item.right::before {
                left: 10px;
            }
        }

        .last {
                justify-self: anchor-center;
        }

        .report-group {
            display: flex;
            justify-content: space-around;
        }

        .start-time {
                color: #8fc73e;
            font-weight: bold;
        }

        .end-time i {
                color:rgb(199, 103, 62);
        
        }
    </style>

<style>
.badge {
    display: inline-block;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: bold;
    border-radius: 10px;
    vertical-align: middle;
}
.badge-success { background-color: #28a745; color: white; }
.badge-secondary { background-color: #6c757d; color: white; }
.badge-warning { background-color: #ffc107; color: black; }
</style>

</head>
<body>

<nav>
    <div class="nav-title">Mitarbeiter-Tracker</div>
    <div class="nav-actions">
        <button onclick="alert('Logging in...')">Login</button>
        <button onclick="alert('Checked out successfully.')">Checkout</button>
        <button onclick="renderAllMarkers()">Clear</button>
    </div>
</nav>

<h2 id="plan-header">Der heutige Plan</h2>  
<p class="last">Last check in was in 
   
            {{ $plan->start_date }}
            <input type="hidden" id="daily_report_id" value="{{$plan->daily_report_id}}">
            <input type="hidden" id="work_place_id" value="{{$plan->work_place_id}}">
            <input type="hidden" id="daily_times_id" value="{{$plan->daily_times_id}}"> 
    
</p>
<div class="container">
    <div id="map"></div>
    <div class="timeline" id="timeline"></div>
</div>
<script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>
<script src="{{ asset('app-assets/js/core/app-menu.js') }}"></script>
<script src="{{ asset('app-assets/js/core/app.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/components.js') }}"></script>
 <script>
    const employeeId = "{{ $employee->id }}";
    const employeeName = "{{ $employee->name }}";
    const employeeLastName = "{{ $employee->lastname }}";
    const employeeImage = "{{ $employee->image }}";
    const employeeImageBaseUrl = "{{ asset('images/employee') }}";   
    const dailyReportId = document.getElementById('daily_report_id').value;  
    const workPlaceId = document.getElementById('work_place_id').value;  
    const dailyTimesId = document.getElementById('daily_times_id').value;  
</script>

<script>
    let events = [];
    let map, directionsService, directionsRenderer, markers = [];
    let userLocation = null;

    function initMap() {
        navigator.geolocation.getCurrentPosition(position => {
            userLocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };

            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: userLocation
            });

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer();
            directionsRenderer.setMap(map);

            new google.maps.Marker({
                position: userLocation,
                map: map,
                label: "You"
            });

            renderEmployeeHeader();
            fetchAndRenderPlan();
        });
    }

    function renderEmployeeHeader() {
        const profileHeader = document.createElement('div');
        profileHeader.style.display = 'flex';
        profileHeader.style.alignItems = 'center';
        profileHeader.style.justifyContent = 'center'; 
        profileHeader.style.margin = '0';
        profileHeader.style.flexDirection = 'column';

        const img = document.createElement('img');
        img.src = `${employeeImageBaseUrl}/${employeeImage}`; 
        img.alt = `${employeeName} ${employeeLastName}`;
        img.style.width = '100px';
        img.style.height = '100px';
        img.style.borderRadius = '50%';
        img.style.border = '3px solid #00b894';
        img.style.objectFit = 'cover';

        const text = document.createElement('h2');
        text.style.color = '#3b3f5c';
        text.textContent = `Today's Plan for ${employeeName} ${employeeLastName}`;

        const planHeader = document.getElementById('plan-header');
        planHeader.innerText = '';
        planHeader.appendChild(profileHeader);
        profileHeader.appendChild(img);
        profileHeader.appendChild(text);
    }

    function fetchAndRenderPlan() {
        fetch(`/employee/qr/code/plan/${employeeId}`)
                .then(res => res.json())
                .then(data => {
                    console.log('API Data:', data);
                    events = data.events || [];
                    console.log('Parsed Events:', events);
                    renderAllMarkers();
                    renderTimeline();
                }) 
            .catch(err => {
                console.error('Failed to load plan data:', err);
            });
    }

    function renderAllMarkers() {
        clearMap();
        events.forEach((event, index) => {
            if (event.latitude && event.longitude) {
                const pos = { lat: parseFloat(event.latitude), lng: parseFloat(event.longitude) };
                const marker = new google.maps.Marker({
                    position: pos,
                    map: map,
                    title: `${event.title} (${event.type})`
                });
                markers.push(marker);
            }
        });
    }

   function renderTimeline() {
        const timeline = document.getElementById('timeline');
        timeline.innerHTML = '';

        events.forEach((event, index) => {
            const side = index % 2 === 0 ? 'left' : 'right';
            const hasCoords = event.latitude && event.longitude;
            const safeTitle = event.title ? event.title.replace(/"/g, '&quot;') : 'Untitled';
            const safeDesc = event.description ? event.description.replace(/"/g, '&quot;') : 'No description';
            const safeLat = hasCoords ? parseFloat(event.latitude) : null;
            const safeLon = hasCoords ? parseFloat(event.longitude) : null;
            const detailsUrl = event.type === 'task' ? `/personal_task_details/${event.id}` : `/appointment_details/${event.id}`;
            const appointment = event.type === 'task' ? `task_design` : `appintment_design`;

            const titleId = `title-status-${index}`;
            const reportGroupId = `report-group-${index}`;

            let actionButton = `
                <button type="button" 
                    onclick="startWork(this)"
                   data-daily-report-id="${dailyReportId}"
                    data-daily-times-id="${dailyTimesId}"
                    data-work-place-id="${workPlaceId}"
                    data-type="${event.type}"
                    data-id="${event.id}"
                    data-work-status="${event.status}"
                    class="btn btn-icon btn-icon rounded-circle btn-success mr-1 mb-1 waves-effect waves-light">
                    <i class="feather icon-play"></i>
                </button>
            `;

            const html = `
                <div class="timeline-item ${side}">
                    <div class="timeline-content">
                        <h3 id="${titleId}" class="${appointment}">${safeTitle} (${event.start_time})</h3>
                        <p>${safeDesc}</p>
                       <div class="btn-group" id="action-button-${index}">
                            ${hasCoords ? `<button class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1 waves-effect waves-light" 
                                onclick="showDirectionPopup(${safeLat}, ${safeLon}, '${safeTitle}')"><i class="feather icon-map-pin"></i></button>` : ''}

                            <button type="button" onclick="window.location.href='${detailsUrl}'" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light"><i class="feather icon-eye"></i></button>

                            ${actionButton}
                        </div>


                        <div class="report-group" id="${reportGroupId}">
                            <div class="start-time"><i class="feather icon-clock"></i> Startzeit: ...</div>
                            <div class="end-time"><i class="feather icon-clock"></i> Endzeit: ...</div>
                            <div class="total">Gesamtzeit: ...</div>
                            <div class="status">Status: ...</div>
                        </div>
                    </div>
                </div>
            `;

            timeline.insertAdjacentHTML('beforeend', html);
            loadReportGroup(event.daily_report_id, event.daily_times_id, reportGroupId, titleId);
        });
    }


    function startWork(button) {
        const dailyReportId = button.dataset.dailyReportId;
        const workPlaceId = button.dataset.workPlaceId;
        const type = button.dataset.type;
        const eventId = button.dataset.id;

        navigator.geolocation.getCurrentPosition(position => {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            const payload = {
                daily_report_id: dailyReportId,
                work_place_id: workPlaceId,
                id: eventId,
                type: type,
                lat: lat,
                lon: lon,
                status: "started",
                work_status: "started"
            };

            fetch(`{{ route('employee.qr.start.work') }}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    Swal.fire("✅ Work Started", response.success, "success");
                } else {
                    Swal.fire("❌ Error", response.error || "Could not start work", "error");
                }
            })
            .catch(error => {
                console.error("Work start failed", error);
                Swal.fire("❌ Error", "Request failed", "error");
            });

        }, () => {
            Swal.fire("⚠️ Location Error", "Cannot access your location", "warning");
        });
    }





    function loadReportGroup(dailyReportId, dailyTimesId, targetId, titleId = null, index = null) {
    fetch(`/employee/qr/get/daily/report/${dailyReportId}/${dailyTimesId}`)
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) return;

            const report = data[0];

            const start = report.start_time || '---';
            const end = report.end_time || '---';
            const status = report.times_status || '---';

            let total = '---';
            if (start !== '---' && end !== '---') {
                const startTime = new Date(`1970-01-01T${start}`);
                const endTime = new Date(`1970-01-01T${end}`);
                const diffMs = endTime - startTime;
                const hours = Math.floor(diffMs / 1000 / 60 / 60);
                const minutes = Math.floor((diffMs / 1000 / 60) % 60);
                total = `${hours}h ${minutes}min`;
            }

            const container = document.getElementById(targetId);
            if (!container) return;

            container.innerHTML = `
                <div class="start-time"><i class="feather icon-clock"></i> Startzeit: ${start}</div>
                <div class="end-time"><i class="feather icon-clock"></i> Endzeit: ${end}</div>
                <div class="total">Gesamtzeit: ${total}</div>
                <div class="status">Status: ${status}</div>
            `;

            if (titleId) {
                const titleEl = document.getElementById(titleId);
                if (titleEl) {
                    const badgeColor = status === 'started' ? 'badge-success' :
                        status === 'ended' ? 'badge-secondary' : 'badge-warning';
                    const badge = `<span class="badge ${badgeColor}" style="margin-left: 10px;">${status}</span>`;
                    titleEl.innerHTML += ` ${badge}`;
                }
            }

            // 🔁 Switch play to pause if started
            if (index !== null && status === 'started') {
                const buttonContainer = document.getElementById(`action-button-${index}`);
                if (buttonContainer) {
                    const playBtn = buttonContainer.querySelector('.btn-success');
                    if (playBtn) {
                        playBtn.outerHTML = `
                            <button disabled type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1 waves-effect waves-light">
                                <i class="feather icon-pause"></i>
                            </button>
                        `;
                    }
                }
            }
        })
        .catch(err => {
            console.error('❌ Failed to fetch report data:', err);
        });
}




    function clearMap() {
        directionsRenderer.set('directions', null);
        markers.forEach(m => m.setMap(null));
        markers = [];
    }

    function showDirectionPopup(lat, lon, title) {
        const mapUrl = `https://maps.google.com/maps?saddr=${userLocation.lat},${userLocation.lng}&daddr=${lat},${lon}`;

        Swal.fire({
            title: `Route to ${title}`,
            html: `<iframe width="100%" height="400" frameborder="0" style="border:0" src="${mapUrl}&output=embed" allowfullscreen></iframe>`,
            width: 700
        });
    }

    window.onload = initMap;
</script>


<script>
function autoCheckout(lat, lon) {
    fetch(`{{ route('employee.qr.check.out') }}?lat=${lat}&lon=${lon}`)
        .then(res => res.json())
        .then(data => {
            Swal.fire("✅ Checked Out", data.success, "success");
        })
        .catch(() => {
            Swal.fire("❌ Error", "Could not auto checkout", "error");
        });
}

// Check every 1 minute
setInterval(() => {
    const now = new Date();
    const currentHour = now.getHours();
    const currentMinute = now.getMinutes();

    if (currentHour === 16 && currentMinute === 0) {
        navigator.geolocation.getCurrentPosition((position) => {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            Swal.fire({
                title: "⏳ Are you still working?",
                text: "If not, you’ll be checked out automatically.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, still working",
                cancelButtonText: "No, check me out",
                timer: 15000,
                timerProgressBar: true,
                reverseButtons: true
            }).then((result) => {
                if (!result.isConfirmed) {
                    autoCheckout(lat, lon);
                }
            });

        }, () => {
            Swal.fire("⚠️ Location Error", "Cannot access your location. Auto-checkout failed.", "warning");
        });
    }
}, 60000); // 60000 ms = 1 minute
</script>


</body>
</html>