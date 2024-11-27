@auth
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>@yield('title')</title>
        <link rel="icon" href="{{ asset('images/logo/logo.png') }}">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    </head>

    <body class="pt-serif-regular main_class">
        <div class="sidebar" id="sidebar">
            <div class="logo">
                <img src="{{ asset('images/logo/logo.png') }}" alt="Logo">
            </div>
            <div class="logo-nombre mt-3">
                <p class="company-name">AIxion Systems</p>
            </div>
            <ul class="nav">
                <li class="nav-item active">
                    <a href="{{ url('documentos') }}">
                        <i class="fa fa-files-o"></i>
                        <span class="nav-text">Documentos</span>
                    </a>
                </li>
                @if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total') ||
                        auth()->user()->rol->privilegios->contains('nombre', 'Acceso a Gerencia'))
                    <li class="nav-item active">
                        <a href="{{ url('gerencias') }}">
                            <i class="fa fa-suitcase" aria-hidden="true"></i>
                            <span class="nav-text">Gerencias</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('usuarios') }}">
                            <i class="fa fa-address-book" aria-hidden="true"></i>
                            <span class="nav-text">Usuarios</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ url('personas') }}">
                            <i class="fa fa-address-book-o" aria-hidden="true"></i>
                            <span class="nav-text">Personas</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('documentos.historial', ['documentoId' => 0]) }}">
                            <i class="fa fa-book" aria-hidden="true"></i>
                            <span class="nav-text">Historial</span>
                        </a>
                    </li>
                @endif

                <!-- <li class="nav-item">
                                                    <a href="#">
                                                        <i class="fa fa-book"></i>
                                                        <span class="nav-text">Otros</span>
                                                    </a>
                                                </li> -->
                {{-- @if (auth()->check() && auth()->user()->rols === 'SuperAdmin') --}}
                {{-- @if (auth()->check() && auth()->user()->rol && auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total')) --}}
                @if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total') || auth()->user()->rol->nombre === 'Gerente')
                    <li class="nav-item">
                        <a href="{{ url('roles') }}">
                            <i class="fa fa-users"></i>
                            <span class="nav-text">Roles y Privilegios</span>
                        </a>
                    </li>
                @endif
                @if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total'))
                    <li class="nav-item">
                        <a href="{{ url('privilegios') }}">
                            <i class="fa fa-users"></i>
                            <span class="nav-text">Privilegios</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('tipodocumento') }}">
                            <i class="fa fa-file-text" aria-hidden="true"></i>
                            <span class="nav-text">Tipo Documento</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('dashboard') }}">
                            <i class="fa fa-file-text" aria-hidden="true"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('backup-acciones') }}">
                            <i class="fa fa-history" aria-hidden="true"></i>
                            <span class="nav-text">Acciones</span>
                        </a>
                    </li>
                @endif
                {{-- <li class="nav-item">
                        <a href="{{ url('rolprivilegios') }}">
                            <i class="fa fa-users"></i>
                            <span class="nav-text">Roles y Privilegios</span>
                        </a>
                    </li> --}}

            </ul>
            <div class="logout-btn">
                <div class="logout-btn-wrapper">
                    <button onclick="window.location.href='{{ route('logout') }}'">
                        <i class="fa fa-sign-out" aria-hidden="true"></i>
                        <a>Cerrar Sesión</a>
                    </button>
                </div>
            </div>
        </div>
        <div class="content">
            <header class="header_class">
                <div class="header-left">
                    <div class="toggle-sidebar-btn" id="toggleSidebarBtn">
                        {{-- <i class="fa fa-bars fa-2x" aria-hidden="true"></i> --}}
                    </div>
                </div>
                <div class="header-right pt-serif-bold">
                    <div class="profile" id="profile-div">
                        @php $personaLogueada = auth()->user()->persona; @endphp
                        @if ($personaLogueada && $personaLogueada->avatar)
                            <img src="{{ asset('storage/' . $personaLogueada->avatar) }}"
                                alt="{{ $personaLogueada->nombres }}" class="img-fluid">
                        @else
                            <img src="{{ asset('images/logo/avatar.png') }}" alt="Avatar" class="img-fluid">
                        @endif
                        <p>{{ auth()->user()->nombre_usuario }}<span>{{ auth()->user()->rol->nombre }}</span></p>
                    </div>
                    <div id="profile-links" class="profile-links text-center">
                        <a href="{{ route('personas.show', ['persona' => auth()->user()->persona->id]) }}"
                            class="boton_perfil">
                            <i class="fa fa-user" aria-hidden="true"></i> Perfil
                        </a>
                        <a href="{{ route('change-password') }}" class="boton_cambiar_contrasena">
                            <i class="fa fa-lock" aria-hidden="true"></i>Cambiar Contraseña
                        </a>
                    </div>
                </div>
            </header>
            <div class="border-dark border-bottom mb-2">
                <h4 class="pt-serif-bold">
                    @yield('title')
                </h4>
            </div>
            <main>
                <div>
                    @yield('content')
                </div>
            </main>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const toggleSidebarBtn = document.getElementById('toggleSidebarBtn');
                const sidebar = document.getElementById('sidebar');
                const content = document.querySelector('.content');

                // Función para minimizar el sidebar
                function minimizeSidebar() {
                    sidebar.classList.add('sidebar-closed');
                    content.classList.add('content-closed');
                    localStorage.setItem('sidebarState', 'closed');
                }

                // Función para maximizar el sidebar
                function maximizeSidebar() {
                    sidebar.classList.remove('sidebar-closed');
                    content.classList.remove('content-closed');
                    localStorage.setItem('sidebarState', 'open');
                }

                // Listener para el botón de toggle
                toggleSidebarBtn.addEventListener('click', () => {
                    if (sidebar.classList.contains('sidebar-closed')) {
                        maximizeSidebar();
                    } else {
                        minimizeSidebar();
                    }
                });

                // Listener para detectar el cambio de tamaño de pantalla
                window.addEventListener('resize', () => {
                    if (window.innerWidth <= 600) {
                        minimizeSidebar();
                    } else {
                        maximizeSidebar();
                    }
                });

                // Recuperar el estado del sidebar desde el localStorage
                const sidebarState = localStorage.getItem('sidebarState');
                if (sidebarState === 'closed') {
                    minimizeSidebar();
                } else {
                    maximizeSidebar();
                }

                // Si la pantalla es menor a 600px al cargar, minimizar el sidebar
                if (window.innerWidth <= 600) {
                    minimizeSidebar();
                }
            });
        });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const profileDiv = document.getElementById('profile-div');
                const profileLinks = document.getElementById('profile-links');

                profileDiv.addEventListener('click', function(e) {
                    e.stopPropagation(); // Evita que el clic se propague al documento
                    profileLinks.style.display = profileLinks.style.display === 'none' ? 'block' : 'none';
                });

                // Cierra el menú si se hace clic fuera de él
                document.addEventListener('click', function(e) {
                    if (!profileDiv.contains(e.target) && !profileLinks.contains(e.target)) {
                        profileLinks.style.display = 'none';
                    }
                });
            });
        </script>
        @stack('scripts')

    </body>


    </html>
@endauth
