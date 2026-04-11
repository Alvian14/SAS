<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="{{ asset('image/smk-taruna-bakti.png') }}" type="image/x-icon" />
    <title>Dashboard - SAS SMK Taruna Bakti Kertosono</title>

    <!-- ========== All CSS files linkup ========= -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lineicons.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/css/materialdesignicons.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/css/fullcalendar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
      .profile-box .profile-info .info .image {
        width: 32px !important;
        height: 32px !important;
        min-width: 32px !important;
        border-radius: 50% !important;
        overflow: hidden !important;
      }

      .author-info .image {
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        border-radius: 50% !important;
        overflow: hidden !important;
      }

      .profile-avatar-circle {
        width: 100% !important;
        height: 100% !important;
        border-radius: 50% !important;
        object-fit: cover !important;
        display: block !important;
      }

      .dropdown-avatar-circle {
        width: 100% !important;
        height: 100% !important;
        border-radius: 50% !important;
        object-fit: cover !important;
        display: block !important;
      }
    </style>
  </head>
  <body>
    <!-- ======== sidebar-nav start =========== -->
    <aside class="sidebar-nav-wrapper">
      <div class="navbar-logo">
        <div class="text-center my-3">
          <a href="{{ route('dashboard.index') }}">
            <img src="{{ asset('image/smk-taruna-bakti.png') }}"
                 style="width:80px;height:80px;object-fit:contain;"
                 alt="logo" />
          </a>
        </div>
        <hr class="my-2">
      </div>

      <nav class="sidebar-nav">
        <ul>
          <!-- Dashboard -->
          <li class="nav-item {{ Route::is('dashboard.index') ? 'active' : '' }}">
            <a href="{{ route('dashboard.index') }}" class="{{ Route::is('dashboard.index') ? 'active' : '' }}">
              <span class="icon">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M8.74999 18.3333C12.2376 18.3333 15.1364 15.8128 15.7244 12.4941C15.8448 11.8143 15.2737 11.25 14.5833 11.25H9.99999C9.30966 11.25 8.74999 10.6903 8.74999 10V5.41666C8.74999 4.7263 8.18563 4.15512 7.50586 4.27556C4.18711 4.86357 1.66666 7.76243 1.66666 11.25C1.66666 15.162 4.83797 18.3333 8.74999 18.3333Z" />
                  <path d="M17.0833 10C17.7737 10 18.3432 9.43708 18.2408 8.75433C17.7005 5.14918 14.8508 2.29947 11.2457 1.75912C10.5629 1.6568 10 2.2263 10 2.91665V9.16666C10 9.62691 10.3731 10 10.8333 10H17.0833Z" />
                </svg>
              </span>
              <span class="text">Dashboard</span>
            </a>
          </li>

          <!-- Akun -->
          <li class="nav-item nav-item-has-children
              {{ Route::is('akun.indentitas_guru') || Route::is('akun.indentitas_siswa') ? 'active' : '' }}">
            <a href="#0" class="collapsed {{ Route::is('akun.indentitas_guru') || Route::is('akun.indentitas_siswa') ? 'active' : '' }}"
               data-bs-toggle="collapse" data-bs-target="#ddmenu_2"
               aria-controls="ddmenu_2" aria-expanded="false" aria-label="Toggle navigation">
              <span class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M4 22C4 18.6863 6.68629 16 10 16H14C17.3137 16 20 18.6863 20 22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              <span class="text">Akun</span>
            </a>
            <ul id="ddmenu_2" class="collapse dropdown-nav">
              <li>
                <a href="{{ route('akun.indentitas_guru') }}"
                   class="{{ Route::is('akun.indentitas_guru') ? 'active' : '' }}">
                   Identitas Guru
                </a>
              </li>
              <li>
                <a href="{{ route('akun.indentitas_siswa') }}"
                   class="{{ Route::is('akun.indentitas_siswa') ? 'active' : '' }}">
                   Identitas Siswa
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item {{ Route::is('mapel.index') ? 'active' : '' }}">
            <a href="{{ route('mapel.index') }}" class="{{ Route::is('mapel.index') ? 'active' : '' }}">
              <span class="icon">
                <i class="fas fa-book-open"></i>
              </span>
              <span class="text">Mata pelajaran</span>
            </a>
        </li>

          <!-- Periode -->
        <li class="nav-item {{ Route::is('periode.index') ? 'active' : '' }}">
            <a href="{{ route('periode.index') }}" class="{{ Route::is('periode.index') ? 'active' : '' }}">
              <span class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M8 2V5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M16 2V5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M3.5 9.09H20.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M21 8.5V17C21 20 19.5 22 16 22H8C4.5 22 3 20 3 17V8.5C3 5.5 4.5 3.5 8 3.5H16C19.5 3.5 21 5.5 21 8.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M15.6947 13.7H15.7037" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M15.6947 16.7H15.7037" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M11.9955 13.7H12.0045" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M11.9955 16.7H12.0045" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M8.29431 13.7H8.30329" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M8.29431 16.7H8.30329" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              <span class="text">Periode</span>
            </a>
        </li>

        <li class="nav-item ">
            <a href="{{ route('jadwal.index') }}">
              <span class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M8 2V5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M16 2V5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M3.5 9.09H20.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M21 8.5V17C21 20 19.5 22 16 22H8C4.5 22 3 20 3 17V8.5C3 5.5 4.5 3.5 8 3.5H16C19.5 3.5 21 5.5 21 8.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M15.6947 13.7H15.7037" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M15.6947 16.7H15.7037" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M11.9955 13.7H12.0045" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M11.9955 16.7H12.0045" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M8.29431 13.7H8.30329" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M8.29431 16.7H8.30329" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              <span class="text">Jadwal</span>
            </a>
        </li>

          <span class="divider"><hr /></span>


          <!-- Kelas -->
          <li class="nav-item ">
            <a href="{{ route('kelas.absensi') }}" class="{{ Route::is('kelas.absensi') ? 'active' : '' }}">
              <span class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M3 4C3 2.89543 3.89543 2 5 2H19C20.1046 2 21 2.89543 21 4V14C21 15.1046 20.1046 16 19 16H5C3.89543 16 3 15.1046 3 14V4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M7 20L12 16L17 20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              <span class="text">Absensi Harian</span>
            </a>
          </li>

          <!-- Mapel -->
          <li class="nav-item">
            <a href="{{ route('kelas.mapel') }}" class="{{ Route::is('kelas.mapel') ? 'active' : '' }}">
              <span class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M3 4H21V16H3V4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M8 20H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M12 16V20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              <span class="text">Absensi Mapel</span>
            </a>
          </li>

          <span class="divider"><hr /></span>

          <!-- Notifications -->
          <li class="nav-item {{ Route::is('notifikasi.index') ? 'active' : '' }}">
          <a href="{{ route('notifikasi.index') }}" class="{{ Route::is('notifikasi.index') ? 'active' : '' }}">
              <span class="icon">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M10.8333 2.50008C10.8333 2.03984 10.4602 1.66675 9.99999 1.66675C9.53975 1.66675 9.16666 2.03984 9.16666 2.50008C9.16666 2.96032 9.53975 3.33341 9.99999 3.33341C10.4602 3.33341 10.8333 2.96032 10.8333 2.50008Z" />
                  <path d="M17.5 5.41673C17.5 7.02756 16.1942 8.33339 14.5833 8.33339C12.9725 8.33339 11.6667 7.02756 11.6667 5.41673C11.6667 3.80589 12.9725 2.50006 14.5833 2.50006C16.1942 2.50006 17.5 3.80589 17.5 5.41673Z" />
                  <path d="M11.4272 2.69637C10.9734 2.56848 10.4947 2.50006 10 2.50006C7.10054 2.50006 4.75003 4.85057 4.75003 7.75006V9.20873C4.75003 9.72814 4.62082 10.2393 4.37404 10.6963L3.36705 12.5611C2.89938 13.4272 3.26806 14.5081 4.16749 14.9078C7.88074 16.5581 12.1193 16.5581 15.8326 14.9078C16.732 14.5081 17.1007 13.4272 16.633 12.5611L15.626 10.6963C15.43 10.3333 15.3081 9.93606 15.2663 9.52773C15.0441 9.56431 14.8159 9.58339 14.5833 9.58339C12.2822 9.58339 10.4167 7.71791 10.4167 5.41673C10.4167 4.37705 10.7975 3.42631 11.4272 2.69637Z" />
                  <path d="M7.48901 17.1925C8.10004 17.8918 8.99841 18.3335 10 18.3335C11.0016 18.3335 11.9 17.8918 12.511 17.1925C10.8482 17.4634 9.15183 17.4634 7.48901 17.1925Z" />
                </svg>
              </span>
              <span class="text">Notifikasi</span>
            </a>
          </li>

          <li class="nav-item {{ Route::is('laporkan.index') ? 'active' : '' }}">
          <a href="{{ route('laporkan.index') }}" class="{{ Route::is('laporkan.index') ? 'active' : '' }}">
              <span class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M14 2H7C5.89543 2 5 2.89543 5 4V20C5 21.1046 5.89543 22 7 22H17C18.1046 22 19 21.1046 19 20V7L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M14 2V7H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M9 12H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M9 16H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              <span class="text">Laporkan</span>
            </a>
          </li>

          <li class="nav-item {{ Route::is('perizinan.index') ? 'active' : '' }}">
          <a href="{{ route('perizinan.index') }}" class="{{ Route::is('perizinan.index') ? 'active' : '' }}">
              <span class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M14 2H7C5.89543 2 5 2.89543 5 4V20C5 21.1046 5.89543 22 7 22H17C18.1046 22 19 21.1046 19 20V7L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M14 2V7H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M9 12H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M9 16H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              <span class="text">Perizinan</span>
            </a>
          </li>
        </ul>
      </nav>
    </aside>
    <div class="overlay"></div>
    <!-- ======== sidebar-nav end =========== -->

    <!-- ======== main-wrapper start =========== -->
    <main class="main-wrapper">
      <!-- ========== header start ========== -->
      <header class="header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-5 col-md-5 col-6">
              <div class="header-left d-flex align-items-center">
                <div class="menu-toggle-btn mr-15">
                  <button id="menu-toggle" class="main-btn primary-btn btn-hover">
                    <i class="lni lni-chevron-left me-2"></i> Menu
                  </button>
                </div>
                <div class="header-search d-none d-md-flex">
                  <form action="#"></form>
                </div>
              </div>
            </div>
            <div class="col-lg-7 col-md-7 col-6">
              <div class="header-right">
                <span id="clock" class="me-3 fw-bold d-none d-md-inline" style="margin-top: 11px;">
                  {{ isset($serverTime) ? $serverTime : '' }}
                </span>
                @php
                  $currentUser = auth()->user();
                  $displayName = 'Admin';
                  if ($currentUser && !empty($currentUser->name)) {
                    $displayName = $currentUser->name;
                  }

                  $letters = strtoupper(substr(preg_replace('/\s+/', '', $displayName), 0, 2));
                  $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($letters) . '&background=0086c1&color=ffffff&size=128';
                  if ($currentUser && !empty($currentUser->profile_picture)) {
                    $avatarUrl = asset('storage/profile/' . $currentUser->profile_picture);
                  }
                @endphp
                <div class="profile-box ml-15 dropdown">
                  <button class="dropdown-toggle bg-transparent border-0" type="button" id="profile"
                          data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="profile-info">
                      <div class="info">
                        <div class="image">
                          <img src="{{ $avatarUrl }}"
                             alt="User Avatar" class="profile-avatar-circle" />
                        </div>
                        <div>
                          <h6 class="fw-500">{{ $displayName }}</h6>
                        </div>
                      </div>
                    </div>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profile" style="z-index: 1055;">
                    <li>
                      <div class="author-info flex items-center !p-1">
                        <div class="image">
                          <img src="{{ $avatarUrl }}" alt="image" class="dropdown-avatar-circle">
                        </div>
                        <div class="content">
                          <h4 class="text-sm">{{ $displayName }}</h4>
                          <a class="text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white text-xs">{{ $currentUser?->email ?? '-' }}</a>
                        </div>
                      </div>
                    </li>
                    <li>
                      <a href="{{ route('setting.index') }}" class="dropdown-item">
                        <i class="lni lni-cog"></i> Setting Profil
                      </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a class="dropdown-item" href="#" id="logout-btn">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                      </a>
                      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                      </form>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </header>
      <!-- ========== header end ========== -->

      <!-- ========== section start ========== -->
      <div class="content">
        @yield('admin_content')
      </div>
      <!-- ========== section end ========== -->

      <!-- ========== footer start =========== -->
      <footer class="footer">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-6 order-last order-md-first">
              <div class="copyright text-center text-md-start">
                <p class="text-sm">&copy; 2026, Team Polije Nganjuk</p>
              </div>
            </div>
          </div>
        </div>
      </footer>
      <!-- ========== footer end =========== -->
    </main>
    <!-- ======== main-wrapper end =========== -->

    <!-- ========= All Javascript files linkup ======== -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/js/dynamic-pie-chart.js') }}"></script>
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/fullcalendar.js') }}"></script>
    <script src="{{ asset('assets/js/jvectormap.min.js') }}"></script>
    <script src="{{ asset('assets/js/world-merc.js') }}"></script>
    <script src="{{ asset('assets/js/polyfill.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/sweet.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      function updateClock() {
        const now = new Date();
        const time = now.toLocaleTimeString('id-ID', {
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit',
          hour12: false,
          timeZone: 'Asia/Jakarta'
        });
        document.getElementById('clock').textContent = time + ' WIB';
      }

      setInterval(updateClock, 1000);
      updateClock();

      // ======== jvectormap activation
      var markers = [
        { name: "Egypt", coords: [26.8206, 30.8025] },
        { name: "Russia", coords: [61.524, 105.3188] },
        { name: "Canada", coords: [56.1304, -106.3468] },
        { name: "Greenland", coords: [71.7069, -42.6043] },
        { name: "Brazil", coords: [-14.235, -51.9253] },
      ];

      var jvm = new jsVectorMap({
        map: "world_merc",
        selector: "#map",
        zoomButtons: true,

        regionStyle: {
          initial: {
            fill: "#d1d5db",
          },
        },

        labels: {
          markers: {
            render: (marker) => marker.name,
          },
        },

        markersSelectable: true,
        selectedMarkers: markers.map((marker, index) => {
          var name = marker.name;

          if (name === "Russia" || name === "Brazil") {
            return index;
          }
        }),
        markers: markers,
        markerStyle: {
          initial: { fill: "#4A6CF7" },
          selected: { fill: "#ff5050" },
        },
        markerLabelStyle: {
          initial: {
            fontWeight: 400,
            fontSize: 14,
          },
        },
      });
      // ====== calendar activation
      document.addEventListener("DOMContentLoaded", function () {
        var calendarMiniEl = document.getElementById("calendar-mini");
        var calendarMini = new FullCalendar.Calendar(calendarMiniEl, {
          initialView: "dayGridMonth",
          headerToolbar: {
            end: "today prev,next",
          },
        });
        calendarMini.render();
      });

      // =========== chart two start
      const ctx2 = document.getElementById("Chart2").getContext("2d");
      const chart2 = new Chart(ctx2, {
        type: "bar",
        data: {
          labels: [
            "Jan",
            "Fab",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
          ],
          datasets: [
            {
              label: "",
              backgroundColor: "#365CF5",
              borderRadius: 30,
              barThickness: 6,
              maxBarThickness: 8,
              data: [
                600, 700, 1000, 700, 650, 800, 690, 740, 720, 1120, 876, 900,
              ],
            },
          ],
        },
        options: {
          plugins: {
            tooltip: {
              callbacks: {
                titleColor: function (context) {
                  return "#8F92A1";
                },
                label: function (context) {
                  let label = context.dataset.label || "";

                  if (label) {
                    label += ": ";
                  }
                  label += context.parsed.y;
                  return label;
                },
              },
              backgroundColor: "#F3F6F8",
              titleAlign: "center",
              bodyAlign: "center",
              titleFont: {
                size: 12,
                weight: "bold",
                color: "#8F92A1",
              },
              bodyFont: {
                size: 16,
                weight: "bold",
                color: "#171717",
              },
              displayColors: false,
              padding: {
                x: 30,
                y: 10,
              },
          },
          },
          legend: {
            display: false,
            },
          legend: {
            display: false,
          },
          layout: {
            padding: {
              top: 15,
              right: 15,
              bottom: 15,
              left: 15,
            },
          },
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              grid: {
                display: false,
                drawTicks: false,
                drawBorder: false,
              },
              ticks: {
                padding: 35,
                max: 1200,
                min: 0,
              },
            },
            x: {
              grid: {
                display: false,
                drawBorder: false,
                color: "rgba(143, 146, 161, .1)",
                drawTicks: false,
                zeroLineColor: "rgba(143, 146, 161, .1)",
              },
              ticks: {
                padding: 20,
              },
            },
          },
        },
      });
      // =========== chart two end

      // =========== chart three start
      const ctx3 = document.getElementById("Chart3").getContext("2d");
      const chart3 = new Chart(ctx3, {
        type: "line",
        data: {
          labels: [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
          ],
          datasets: [
            {
              label: "Revenue",
              backgroundColor: "transparent",
              borderColor: "#365CF5",
              data: [80, 120, 110, 100, 130, 150, 115, 145, 140, 130, 160, 210],
              pointBackgroundColor: "transparent",
              pointHoverBackgroundColor: "#365CF5",
              pointBorderColor: "transparent",
              pointHoverBorderColor: "#365CF5",
              pointHoverBorderWidth: 3,
              pointBorderWidth: 5,
              pointRadius: 5,
              pointHoverRadius: 8,
              fill: false,
              tension: 0.4,
            },
            {
              label: "Profit",
              backgroundColor: "transparent",
              borderColor: "#9b51e0",
              data: [
                120, 160, 150, 140, 165, 210, 135, 155, 170, 140, 130, 200,
              ],
              pointBackgroundColor: "transparent",
              pointHoverBackgroundColor: "#9b51e0",
              pointBorderColor: "transparent",
              pointHoverBorderColor: "#9b51e0",
              pointHoverBorderWidth: 3,
              pointBorderWidth: 5,
              pointRadius: 5,
              pointHoverRadius: 8,
              fill: false,
              tension: 0.4,
            },
            {
              label: "Order",
              backgroundColor: "transparent",
              borderColor: "#f2994a",
              data: [180, 110, 140, 135, 100, 90, 145, 115, 100, 110, 115, 150],
              pointBackgroundColor: "transparent",
              pointHoverBackgroundColor: "#f2994a",
              pointBorderColor: "transparent",
              pointHoverBorderColor: "#f2994a",
              pointHoverBorderWidth: 3,
              pointBorderWidth: 5,
              pointRadius: 5,
              pointHoverRadius: 8,
              fill: false,
              tension: 0.4,
            },
          ],
        },
        options: {
          plugins: {
            tooltip: {
              intersect: false,
              backgroundColor: "#fbfbfb",
              titleColor: "#8F92A1",
              bodyColor: "#272727",
              titleFont: {
                size: 16,
                family: "Plus Jakarta Sans",
                weight: "400",
              },
              bodyFont: {
                family: "Plus Jakarta Sans",
                size: 16,
              },
              multiKeyBackground: "transparent",
              displayColors: false,
              padding: {
                x: 30,
                y: 15,
              },
              borderColor: "rgba(143, 146, 161, .1)",
              borderWidth: 1,
              enabled: true,
            },
            title: {
              display: false,
            },
            legend: {
              display: false,
            },
          },
          layout: {
            padding: {
              top: 0,
            },
          },
          responsive: true,
          // maintainAspectRatio: false,
          legend: {
            display: false,
          },
          scales: {
            y: {
              grid: {
                display: false,
                drawTicks: false,
                drawBorder: false,
              },
              ticks: {
                padding: 35,
              },
              max: 350,
              min: 50,
            },
            x: {
              grid: {
                drawBorder: false,
                color: "rgba(143, 146, 161, .1)",
                drawTicks: false,
                zeroLineColor: "rgba(143, 146, 161, .1)",
              },
              ticks: {
                padding: 20,
              },
            },
          },
        },
      });
      // =========== chart three end

      // ================== chart four start
      const ctx4 = document.getElementById("Chart4").getContext("2d");
      const chart4 = new Chart(ctx4, {
        type: "bar",
        data: {
          labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
          datasets: [
            {
              label: "",
              backgroundColor: "#365CF5",
              borderColor: "transparent",
              borderRadius: 20,
              borderWidth: 5,
              barThickness: 20,
              maxBarThickness: 20,
              data: [600, 700, 1000, 700, 650, 800],
            },
            {
              label: "",
              backgroundColor: "#d50100",
              borderColor: "transparent",
              borderRadius: 20,
              borderWidth: 5,
              barThickness: 20,
              maxBarThickness: 20,
              data: [690, 740, 720, 1120, 876, 900],
            },
          ],
        },
        options: {
          plugins: {
            tooltip: {
              backgroundColor: "#F3F6F8",
              titleColor: "#8F92A1",
              titleFontSize: 12,
              bodyColor: "#171717",
              bodyFont: {
                weight: "bold",
                size: 16,
              },
              multiKeyBackground: "transparent",
              displayColors: false,
              padding: {
                x: 30,
                y: 10,
              },
              bodyAlign: "center",
              titleAlign: "center",
              enabled: true,
            },
            legend: {
              display: false,
            },
          },
          layout: {
            padding: {
              top: 0,
            },
          },
          responsive: true,
          // maintainAspectRatio: false,
          title: {
            display: false,
          },
          scales: {
            y: {
              grid: {
                display: false,
                drawTicks: false,
                drawBorder: false,
              },
              ticks: {
                padding: 35,
                max: 1200,
                min: 0,
              },
            },
            x: {
              grid: {
                display: false,
                drawBorder: false,
                color: "rgba(143, 146, 161, .1)",
                zeroLineColor: "rgba(143, 146, 161, .1)",
              },
              ticks: {
                padding: 20,
              },
            },
          },
        },
      });
        // =========== chart four end

      // Jam digital otomatis WIB (Asia/Jakarta) - pastikan browser tidak cache response
      document.addEventListener('DOMContentLoaded', function () {
        var dropdowns = document.querySelectorAll('[data-bs-toggle="dropdown"]');
        dropdowns.forEach(function (dropdownToggleEl) {
          new bootstrap.Dropdown(dropdownToggleEl);
        });
      });
    </script>
    @stack('scripts')
  </body>
</html>
