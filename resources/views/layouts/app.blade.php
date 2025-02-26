<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPULO TTH</title>
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    
    <!-- Icon -->
    <link rel="icon" type="image/png" href="{{ asset('storage/avatar/ftth.png') }}" sizes="128x128">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    
    <!-- FullCalendar CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css" rel="stylesheet" />
    
    <!-- FontAwesome -->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand custom-navbar">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="/admin/dashboard">
            <img src="{{ asset('storage/avatar/ftth.png') }}" alt="Logo" style="width: 55px; height: 40px; margin-right: 10px;">
            SIPULO
        </a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item">
                <a href="#" class="nav-link" id="notificationBell" data-bs-toggle="modal" data-bs-target="#notificationModal">
                    <i class="fas fa-bell"></i>
                    <span id="notificationCount" class="badge bg-danger">0</span> <!-- Example count -->
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i>
                    <?php
                        echo auth()->user()->name;
                    ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li>
                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Notifikasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="notificationContent">
                    <!-- Notifications will be loaded here -->
                </div>
                <div class="modal-footer">
                    <!-- Delete all notifications button -->
                    <button id="deleteAllNotifications" class="btn btn-danger w-100">Hapus Notifikasi</button>
                </div>
            </div>
        </div>
    </div>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Core</div>
                        <a class="nav-link" href="/admin/dashboard">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Interface</div>

                        @if (auth()->check() && auth()->user()->role === 'admin')
                            <a class="nav-link" href="/user">
                                <div class="sb-nav-link-icon"><i class="fas fa-clipboard-list"></i></div>
                                Akun Engineer
                            </a>
                        @endif
                        <a class="nav-link" href="/test-schedules">
                            <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                            Schedule
                        </a>
                        <a class="nav-link" href="/schedule_archive">
                            <div class="sb-nav-link-icon"><i class="fas fa-clipboard-list"></i></div>
                            Archive
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    SIPULO
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            @yield('content')
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; SIPULO 2024</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <!-- Custom Scripts -->
    <script src="{{ asset('js/scripts.js') }}"></script>

    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    
    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to fetch notifications from the file and display them in the modal
        $('#notificationBell').on('click', function() {
            // Fetch the notifications content using AJAX
            $.ajax({
                url: '{{ route('fetch.notifications') }}', // Define this route to get the notifications content
                type: 'GET',
                success: function(response) {
                    $('#notificationContent').text(response); // Display the content in the modal
                },
                error: function() {
                    $('#notificationContent').text('Failed to load notifications.');
                }
            });
        });
    
        // Fetch the notification count on page load
        $(document).ready(function() {
            $.ajax({
                url: '{{ route('count.notifications') }}',  // URL to fetch the notification count
                type: 'GET',
                success: function(response) {
                    // Update the notification badge with the count
                    $('#notificationCount').text(response.count);
                },
                error: function() {
                    // In case of error, set the count to 0
                    $('#notificationCount').text('0');
                }
            });
        });

        // Handle the click event for deleting all notifications
        $(document).on('click', '#deleteAllNotifications', function() {
            // Confirm the action with the user
            if (confirm('Anda yakin ingin menghapus semua notifikasi?')) {
                // Make an AJAX request to delete all notifications
                $.ajax({
                    url: '{{ route('delete.notifications') }}',  // The route to delete notifications
                    type: 'GET',
                    success: function(response) {
                        // If successful, show a success message and clear notifications content
                        if (response.success) {
                            alert(response.message);
                            $('#notificationContent').html(''); // Clear the notifications content
                            $('#notificationCount').text('0'); // Update notification count to 0
                            
                            // Fetch the updated notification count
                            $.ajax({
                                url: '{{ route('count.notifications') }}',  // The route to get the updated count
                                type: 'GET',
                                success: function(countResponse) {
                                    // Update the notification count with the latest count
                                    $('#notificationCount').text(countResponse.count);
                                },
                                error: function() {
                                    // In case of error, set the count to 0
                                    $('#notificationCount').text('0');
                                }
                            });
                        } else {
                            // If there's an issue, show an error message
                            alert(response.message);
                        }
                    },
                    error: function() {
                        // If an error occurs, show an error message
                        alert('Failed to delete notifications.');
                    }
                });
            }
        });

    </script>
    
</body>
</html>
