<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Sidebar with Bootstrap & jQuery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#sidebar").hover(
                function() {
                    $(this).addClass("w-260").removeClass("w-50");
                    $("#menu-items").fadeIn();
                },
                function() {
                    $(this).addClass("w-50").removeClass("w-260");
                    $("#menu-items").fadeOut();
                }
            );

            // Handle active nav selection
            $(".nav-item").click(function() {
                $(".nav-item").removeClass("active-nav-list");
                $(this).addClass("active-nav-list");
            });
        });
    </script>
    <style>
        .sidebar {
            left: 0;
            top: 0;
            background-color: #343a40;
            color: white;
            transition: all 0.3s ease;
            overflow: hidden;
            width: 50px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .w-260 { width: 260px !important; }
        .w-50 { width: 50px !important; }
        .sidebar ul {
            list-style: none;
            padding: 0;
            width: 100%;
            text-align: center;
        }
        .sidebar ul li {
            padding: 15px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .sidebar ul li:hover {
            background-color: #495057;
        }
        .active-nav-list {
            background-color: #007bff !important;
            color: white !important;
        }
        .app-content {
            transition: margin-left 0.3s ease;
            width: 100%;
            margin-left: 20px;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
        }
        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar p-3">
            <div class="user-avatar" id="avatarContainer">
                <img src="https://via.placeholder.com/40" alt="User Avatar">
            </div>
            <ul class="mt-3" id="menu-items" style="display: none;">
                <li class="bg-dark rounded nav-item">🏠 Dashboard</li>
                <li class="bg-dark rounded nav-item">⚙️ Settings</li>
                <li class="bg-dark rounded nav-item">👤 Profile</li>
            </ul>
        </div>
        <!-- Main Content -->
        <div id="main-content" class="app-content p-4">
            <h1 class="fw-bold">Main Content Area</h1>
            <p>Welcome to the Laravel Sidebar using Bootstrap & jQuery!</p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
