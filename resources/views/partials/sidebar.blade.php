<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    @if (auth()->user()->hasRole(['super-admin', 'lya']))
                        <a class="nav-link" href="{{ route('admin.home') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Administración</div>
                    @else
                        <a class="nav-link" href="{{ route('user.home') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                    @endif

                    <!-- Layouts -->
                    @can('maestros-access')
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLayouts"
                            aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            Maestros
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>

                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne"
                            data-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">

                                @can('users-access')
                                    <a class="nav-link" href="{{ route('admin.users.index') }}">
                                        <i class="fas fa-user-alt mr-1"></i>
                                        Usuarios
                                    </a>
                                @endcan
                                @can('roles-access')
                                    <a class="nav-link" href="{{ route('admin.roles.index') }}">
                                        <i class="fas fa-user-secret mr-1"></i>
                                        Roles
                                    </a>
                                @endcan
                                @can('roles-access')
                                    <a class="nav-link" href="{{ route('admin.permissions.index') }}">
                                        <i class="fas fa-user-secret mr-1"></i>
                                        Permisos
                                    </a>
                                @endcan
                                @can('suministros-access')
                                    <a class="nav-link" href="{{ route('admin.suministros.index') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas fa-pills"></i>
                                        </div>
                                        Suministros
                                    </a>
                                @endcan
                                @can('terceros-access')
                                    <a class="nav-link" href="{{ route('admin.terceros.index') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas fa-flask"></i>
                                        </div>
                                        Proveedores
                                    </a>
                                @endcan

                            </nav>
                        </div>
                    @endcan

                    @can('rotacion-module-access')
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBusiness"
                            aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-building"></i></div>
                            Subgerencia
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseBusiness" aria-labelledby="headingOne"
                            data-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                @can('rotaciones-access')
                                    <a class="nav-link" href="{{ route('rotaciones.index') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas fa-business-time"></i>
                                        </div>
                                        Rotación HTT Max & Min
                                    </a>
                                @endcan
                            </nav>
                        </div>
                    @endcan


                    @can('reportes-access')
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReports"
                            aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-building"></i></div>
                            Reportes
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseReports" aria-labelledby="headingOne"
                            data-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                @can('reportes-nomina')
                                    <a class="nav-link" href="{{ route('reportes.nomina1') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas fa-business-time"></i>
                                        </div>
                                        Reporte de Nomina
                                    </a>
                                    <a class="nav-link" href="{{ route('reportes.nomina2') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas fa-business-time"></i>
                                        </div>
                                        Conceptos duplicados
                                    </a>
                                    <a class="nav-link" href="{{ route('reportes.nomina3') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas fa-business-time"></i>
                                        </div>
                                        Huellero
                                    </a>
                                @endcan

                                @can('reportes-rrhh')
                                    <a class="nav-link" href="{{ route('reportes.rrhh1') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas fa-business-time"></i>
                                        </div>
                                        Cumpleaños
                                    </a>
                                    <a class="nav-link" href="{{ route('reportes.rrhh2') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas fa-business-time"></i>
                                        </div>
                                        Permisos Aprobados
                                    </a>
                                @endcan

                                @can('reportes-prealta')
                                    <a class="nav-link" href="{{ route('reportes.prealta') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas fa-business-time"></i>
                                        </div>
                                        Pacientes Prealta
                                    </a>
                                @endcan

                                @can('reportes-auditoria-med-amb')
                                    <a class="nav-link" href="{{ route('reportes.audmedamb') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas fa-business-time"></i>
                                        </div>
                                        Aud. Medicamentos
                                    </a>
                                @endcan

                                @can('reportes-auditoria-med-amb')
                                    <a class="nav-link" href="{{ route('reportes.glucometrias') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas fa-business-time"></i>
                                        </div>
                                        Glucometrias
                                    </a>
                                @endcan

                                @can('reporte-devolucion-oxigeno')
                                    <a class="nav-link" href="{{ route('reportes.devoluciones') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas fa-business-time"></i>
                                        </div>
                                        Devolución Oxígeno
                                    </a>
                                @endcan

                                @can('reporte-devolucion-total')
                                    <a class="nav-link" href="{{ route('reportes.devolucionestotal') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas fa-business-time"></i>
                                        </div>
                                        Devoluciones Pendientes
                                    </a>
                                @endcan

                                @can('reportes-marcaciones-egresos')
                                    <a class="nav-link" href="{{ route('reportes.marcacionesegresos') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas fa-business-time"></i>
                                        </div>
                                        Marcaciones Egresos
                                    </a>
                                @endcan

                                @can('seg-evoluciones-fact')
                                    <a class="nav-link" href="{{ route('reportes.segEvolucionesFact') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas fa-business-time"></i>
                                        </div>
                                        Seg. Evoluciones Fact
                                    </a>
                                @endcan

                            </nav>
                        </div>
                    @endcan



                    @can('farmacia-access')
                        <a class="nav-link collapsed" href="#" data-toggle="collapse"
                            data-target="#collapseCasino" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-building"></i></div>
                            Farmacia
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>


                        <div class="collapse" id="collapseCasino" aria-labelledby="headingOne"
                            data-parent="#sidenavAccordion">
                            @can('recepcion-medicamentos')
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('farmacia.recepcion') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas fa-business-time"></i>
                                        </div>
                                        Recepción Medicamentos
                                    </a>
                                </nav>
                            @endcan

                            @can('envio-ordenes-compras')
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('farmacia.envio.ordenes') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas fa-business-time"></i>
                                        </div>
                                        Envio Ordenes de Compras
                                    </a>
                                </nav>
                            @endcan

                            @can('provider-evaluation')
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('farmacia.providersEvaluation') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas fa-business-time"></i>
                                        </div>
                                        Evaluación de Proveedores
                                    </a>
                                </nav>
                            @endcan

                        </div>
                    @endcan


                    @can('infraestructura-access')
                        <a class="nav-link collapsed" href="#" data-toggle="collapse"
                            data-target="#collapseSecurity" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-building"></i></div>
                            Orientación al usuario
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>

                        @can('ubicacion-equipos')
                            <div class="collapse" id="collapseSecurity" aria-labelledby="headingOne"
                                data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('ousuario.family-income') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas fa-business-time"></i>
                                        </div>
                                        Registro de familiares
                                    </a>
                                </nav>
                            </div>
                        @endcan
                    @endcan

                    @can('compras-access')
                        <a class="nav-link collapsed" href="#" data-toggle="collapse"
                            data-target="#collapseCompras" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-building"></i></div>
                            Compras
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>

                        @can('seguimiento-compras')
                            <div class="collapse" id="collapseCompras" aria-labelledby="headingOne"
                                data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('compras.seguimiento-compras') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas fa-business-time"></i>
                                        </div>
                                        Seguimiento Compras
                                    </a>
                                </nav>
                            </div>
                        @endcan
                    @endcan

                </div>
            </div>
            <div class="sb-sidenav-footer text-center">
                <div class="small">Ingresaste como:</div>
                {{ auth()->user()->name }} {{ auth()->user()->lastName }}
                <a class="btn btn-primary btn-sm btn-block" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                                                     document.getElementById('logout-form-sidebar').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    {{ __('Cerrar Sesión') }}
                </a>
                <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST"
                    style="display: none;">
                    @csrf
                </form>
            </div>
        </nav>
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <main class="py-4">
                    @yield('content')
                </main>
            </div>
        </main>

        @include('partials.footer')

    </div>
</div>
