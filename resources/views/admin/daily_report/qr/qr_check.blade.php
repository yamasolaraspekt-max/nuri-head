<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Solar Aspekt">
    <meta name="keywords" content="Photovoltaik, WP, PV, Solar, Warmpumpe, Heizung, Heiztechnique,">
    <meta name="author" content="Solar Aspekt">
    <title>SA - DESK </title>
    <link rel="apple-touch-icon" href="{{ asset('logo/logo_half.png')  }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('logo/logo_half.png')  }}">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
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

    <!-- END: Custom CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern dark-layout 1-column  navbar-floating footer-static bg-full-screen-image  blank-page blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="1-column" data-layout="dark-layout">
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <section class="row flexbox-container">
                    <div class="col-xl-7 col-10 d-flex justify-content-center">
                        <div class="card bg-authentication rounded-0 mb-0 w-100">
                            <div class="row m-0">
                                <div class="col-lg-6 d-lg-block d-none text-center align-self-center px-1 py-0">
                                    <img src="{{ asset('app-assets/images/pages/lock-screen.png')}}" alt="branding logo">
                                </div>
                                <div class="col-lg-6 col-12 p-0">
                                    <div class="card rounded-0 mb-0 px-2 pb-2">
                                        <div class="card-header pb-1">
                                            <div class="card-title">
                                                <h4 class="mb-0"></h4>
                                            </div>
                                        </div>
                                        <div class="card-content">
                                            <div class="card-body pt-1">
                                                <form id="check_form">
                                                    <input type="hidden" name="type" value="{{$type}}">
                                                    <fieldset class="form-label-group position-relative has-icon-left">
                                                        <input type="text" class="form-control" id="lastname" placeholder="Nachname" required>
                                                        <div class="form-control-position">
                                                            <i class="feather icon-user"></i>
                                                        </div>
                                                        <label for="user-name">Nachname</label>
                                                    </fieldset>

                                                    <fieldset class="form-label-group position-relative has-icon-left">
                                                        <input type="password" class="form-control" id="code" placeholder="code" required>
                                                        <div class="form-control-position">
                                                            <i class="feather icon-lock"></i>
                                                        </div>
                                                        <label for="user-password">Code</label>
                                                    </fieldset>
                                                    
                                                    <button type="submit" class="btn btn-primary float-right">Einchecken</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>
    <!-- END: Content-->
 
<script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- BEGIN: Theme JS-->
<script src="{{ asset('app-assets/js/core/app-menu.js') }}"></script>
<script src="{{ asset('app-assets/js/core/app.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/components.js') }}"></script>
<script>
    // Global function for checking plan
    function checkPlan(employeeId, type) {
        window.location.href = `/employee/qr/code/form/${employeeId}/${type}`;
    }

    // Manual checkout function
    function manualCheckout(code, lat, lon) {
        fetch(`{{ route('employee.qr.check.out.emp') }}?code=${code}&lat=${lat}&lon=${lon}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire("✅ Erfolgreich", data.success, "success");
                } else {
                    Swal.fire("⚠️ Fehler", data.error, "warning");
                }
            })
            .catch(() => {
                Swal.fire("❌ Fehler", "Ein Fehler ist aufgetreten beim Auschecken.", "error");
            });
    }

    // Main form submission
    document.getElementById('check_form').addEventListener('submit', function (e) {
        e.preventDefault();

        Swal.fire({
            title: '📍 Standort wird abgerufen...',
            text: 'Bitte warten...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        navigator.geolocation.getCurrentPosition(async function (position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            const lastname = document.getElementById('lastname').value.trim();
            const code = document.getElementById('code').value.trim();
            const type = document.querySelector('input[name="type"]').value;

            let car_number = null;

            // Ask for car number if type is Car
            if (type === 'Car') {
                Swal.close();
                const { isConfirmed, value } = await Swal.fire({
                    title: '🚗 Car Number Required',
                    input: 'text',
                    inputPlaceholder: 'Enter your car number',
                    showCancelButton: true,
                    inputValidator: (value) => {
                        if (!value) return 'You must enter a car number';
                    }
                });

                if (!isConfirmed || !value) return;
                car_number = value;

                Swal.fire({
                    title: '⏳ Wird überprüft...',
                    text: 'Bitte warten...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            }

            const formData = {
                lastname,
                code,
                lat,
                lon,
                type,
                car_number
            };

            const response = await fetch("{{ route('employee.qr.check') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();
            Swal.close();

            if (result.success) {
                Swal.fire('✅ Erfolgreich', result.success, 'success');
            } else if (result.error) {
                // Handle already checked-in
                if (result.map && result.lat && result.lon) {
                    const employeeId = result.employee_id ?? null;
                    const safeType = result.type ?? 'task';

                    Swal.fire({
                        icon: 'info',
                        title: 'Bereits eingecheckt',
                        html: `
                            <p>${result.error}</p>
                            <div id="map" style="width: 100%; height: 300px; margin-top: 10px;"></div>
                            <div style="margin-top: 20px; text-align:center;">
                                ${employeeId ? `<button onclick="checkPlan(${employeeId}, &quot;${safeType}&quot;)" style="padding: 6px 14px; margin-right: 10px; background: #00b894; color: white; border: none; border-radius: 5px;">📋 Check Plan</button>` : ''}
                                <button onclick="manualCheckout('${code}', ${lat}, ${lon})" style="padding: 6px 14px; background: #ff5252; color: white; border: none; border-radius: 5px;">🚪 Checkout</button>
                            </div>
                        `,
                        didOpen: () => {
                            const map = new google.maps.Map(document.getElementById("map"), {
                                center: { lat: parseFloat(result.lat), lng: parseFloat(result.lon) },
                                zoom: 17
                            });

                            new google.maps.Marker({
                                position: { lat: parseFloat(result.lat), lng: parseFloat(result.lon) },
                                map: map,
                                title: "Check-in Location"
                            });
                        },
                        width: 650
                    });
                } else {
                    Swal.fire('❌ Fehler', result.error, 'error');
                }
            }

        }, function () {
            Swal.close();
            Swal.fire('⚠️ Fehler', 'Standortzugriff ist erforderlich!', 'warning');
        });
    });
</script>



<!-- ✅ Load Google Maps JS API with your API key -->
<script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&callback=Function.prototype">
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
<!-- END: Body-->

</html>