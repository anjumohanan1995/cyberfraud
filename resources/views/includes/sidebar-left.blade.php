<aside class="main-sidebar app-sidebar sidebar-scroll ps ps--active-y">
    <div class="main-sidebar-header active">
        <a class="desktop-logo logo-light active" href="index.html">
            <img src="/img/logo.png" class="main-logo" />
        </a>
        <a class="desktop-logo icon-logo active" href="index.html">
            <img src="img/favicon.png" class="logo-icon" />
        </a>
        <a class="desktop-logo logo-dark active" href="index.html">
            <img src="/img/logo-white.png" class="main-logo dark-theme" alt="logo" />
        </a>
        <a class="logo-icon mobile-logo icon-dark active" href="index.html">
            <img src="img/favicon-white.png" class="logo-icon dark-theme" alt="logo" />
        </a>
    </div>
    <!-- /logo -->
    <div class="main-sidebar-loggedin">
        <div class="app-sidebar__user">
            <div class="dropdown user-pro-body text-center">
                <div class="user-info"></div>
            </div>
        </div>
    </div>
    <!-- /user -->
    {{-- <div class="sidebar-navs">
        <ul class="nav nav-pills-circle">
            <li class="nav-item" data-toggle="tooltip" data-placement="top" title=""
                data-original-title="Settings">
                <a class="nav-link text-center m-2">
                    <i class="fe fe-settings"> </i>
                </a>
            </li>
            <li class="nav-item" data-toggle="tooltip" data-placement="top" title="" data-original-title="Chat">
                <a class="nav-link text-center m-2">
                    <i class="fe fe-mail"> </i>
                </a>
            </li>
            <li class="nav-item" data-toggle="tooltip" data-placement="top" title=""
                data-original-title="Followers">
                <a class="nav-link text-center m-2">
                    <i class="fe fe-user"> </i>
                </a>
            </li>
            <li class="nav-item" data-toggle="tooltip" data-placement="top" title="" data-original-title="Logout">
                <a class="nav-link text-center m-2" href="{{url('logout')}}">
                    <i class="fe fe-power"> </i>
                </a>
            </li>
        </ul>
    </div> --}}
    <div class="main-sidebar-body">
        <ul class="side-menu">
            <li class="slide active">
                <a class="side-menu__item active" href="/dashboard">
                    <i class="side-menu__icon fe fe-airplay"> </i>
                    <span class="side-menu__label">Dashboard</span>
                </a>
            </li>
            @php

                $permission = \App\Models\RolePermission::where('role', \Auth::user()->role)->first();

                @$sub_permission = $permission->sub_permissions
                    ? json_decode($permission->sub_permissions, true)
                    : null;

            @endphp
            {{-- @if (!empty($permission))

                @foreach (@$permission->permission as $permissions)

                    @if (@$permissions == 'user-management')


                    <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <i class="side-menu__icon fe fe-box"> </i>
                            <span class="side-menu__label">Users Management</span>
                            <i class="angle fe fe-chevron-down"> </i>
                        </a>
                        <ul class="slide-menu">
                            @if (!empty($sub_permission) && in_array('users-list', $sub_permission))
                                <li>
                                    <a class="slide-item" href="{{url('users')}}">Users</a>
                                </li>
                            @endif
                            @if (!empty($sub_permission) && in_array('role-list', $sub_permission))
                                <li>
                                    <a class="slide-item" href="{{url('roles')}}">Roles</a>
                                </li>
                            @endif
                            @if (!empty($sub_permission) && in_array('permission-list', $sub_permission))
                                <li>
                                    <a class="slide-item" href="{{url('permissions')}}">Permissions</a>
                                </li>
                            @endif

                        </ul>
                    </li>
                    @endif

                @endforeach
            @endif --}}
            {{-- <li class="slide">
                        <a class="side-menu__item" href="{{url('modus')}}">
                            <i class="side-menu__icon fe fe-database"> </i>
                            <span class="side-menu__label">Modus</span>
                        </a>
                    </li>

                    <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <i class="side-menu__icon fe fe-mail menu-icons">
                            </i>
                            <span class="side-menu__label">Mail</span>
                            <span class="badge badge-warning side-badge">5</span>
                        </a>
                        <ul class="slide-menu">
                            <li>
                                <a class="slide-item" href="mail.html">Mail</a>
                            </li>
                            <li>
                                <a class="slide-item" href="mail-compose.html">Mail Compose</a>
                            </li>
                            <li>
                                <a class="slide-item" href="mail-read.html">Read-mail</a>
                            </li>
                            <li>
                                <a class="slide-item" href="mail-settings.html">mail-settings</a>
                            </li>
                            <li>
                                <a class="slide-item" href="chat.html">Chat</a>
                            </li>
                        </ul>
                    </li>

                    <li class="slide">
                        <a class="side-menu__item" href="{{url('police_stations')}}">
                            <i class="side-menu__icon fe fe-database"> </i>
                            <span class="side-menu__label">Police Stations</span>
                        </a>
                    </li> --}}
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon fe fe-user"> </i>
                    <span class="side-menu__label">Users Management</span>
                    <i class="angle fe fe-chevron-down"> </i>
                </a>
                <ul class="slide-menu">
                    <li>
                        <a class="slide-item" href="{{ url('users') }}">Users</a>
                    </li>
                    <li>
                        <a class="slide-item" href="{{ url('roles') }}">Roles</a>
                    </li>
                    <li>
                        <a class="slide-item" href="{{ url('permissions') }}">Permissions</a>
                    </li>

                </ul>
            </li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon fe fe-box"> </i>
                    <span class="side-menu__label">Upload NCRP CaseData</span>
                    <i class="angle fe fe-chevron-down"> </i>
                </a>
                <ul class="slide-menu">
                    <li>
                        <a class="slide-item" href="{{ url('import-complaints') }}">Primary Data</a>
                    </li>
                    <li>
                        <a class="slide-item" href="{{ route('bank-case-data.index') }}">Bank Action</a>
                    </li>

                </ul>
            </li>
             <li class="slide">
                <a class="side-menu__item"  href="{{ route('upload-others-caseData') }}">
                    <i class="side-menu__icon fe fe-box"> </i>
                    <span class="side-menu__label">Upload Others CaseData</span>

                </a>
            </li>
            <li class="slide">
                <a class="side-menu__item" href="{{ url('case-data') }}">
                    <i class="side-menu__icon fe fe-database"> </i>
                    <span class="side-menu__label">NCRP Case Data</span>
                </a>
            </li>
            <li class="slide">
                <a class="side-menu__item" href="{{ url('case-data-others') }}">
                    <i class="side-menu__icon fe fe-database"> </i>
                    <span class="side-menu__label">Others Case Data </span>
                </a>
            </li>
            <li class="slide">
                <a class="side-menu__item" href="{{ url('sourcetype') }}">
                    <i class="side-menu__icon fe fe-database"> </i>
                    <span class="side-menu__label">Source Type</span>
                </a>
            </li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon fe fe-file"> </i>
                    <span class="side-menu__label">Notice Management</span>
                    <i class="angle fe fe-chevron-down"> </i>
                </a>
                <ul class="slide-menu">
                    <li>
                        <a class="slide-item" href="{{ route('notice.evidence') }}">Against Evidence</a>
                    </li>
                    <li>
                        <a class="slide-item" href="{{ route('bank-case-data.index') }}">Against Bank</a>
                    </li>
                    <li>
                        <a class="slide-item" href="{{ route('bank-case-data.index') }}">Against Mule Account</a>
                    </li>

                </ul>
                
            </li>
             <li class="slide">
                <a class="side-menu__item" href="{{ url('evidence.management') }}">
                    <i class="side-menu__icon fe fe-database"> </i>
                    <span class="side-menu__label">Evidence Management</span>
                </a>
            </li>
           <li class="slide">
                <a class="side-menu__item" href="{{ url('reports') }}">
                    <i class="side-menu__icon fe fe-database"> </i>
                    <span class="side-menu__label">Reports</span>
                </a>
            </li>
            <li class="slide">
                <a class="side-menu__item" href="{{ url('evidencetype') }}">
                    <i class="side-menu__icon fe fe-database"> </i>
                    <span class="side-menu__label">Evidence Type</span>
                </a>
            </li>
            {{-- <li class="slide">
                <a class="side-menu__item" href="{{ url('modus') }}">
                    <i class="side-menu__icon fe fe-database"> </i>
                    <span class="side-menu__label">Modus</span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon fe fe-mail menu-icons">
                    </i>
                    <span class="side-menu__label">Mail</span>
                    <span class="badge badge-warning side-badge">5</span>
                </a>
                <ul class="slide-menu">
                    <li>
                        <a class="slide-item" href="mail.html">Mail</a>
                    </li>
                    <li>
                        <a class="slide-item" href="mail-compose.html">Mail Compose</a>
                    </li>
                    <li>
                        <a class="slide-item" href="mail-read.html">Read-mail</a>
                    </li>
                    <li>
                        <a class="slide-item" href="mail-settings.html">mail-settings</a>
                    </li>
                    <li>
                        <a class="slide-item" href="chat.html">Chat</a>
                    </li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ url('police_stations') }}">
                    <i class="side-menu__icon fe fe-database"> </i>
                    <span class="side-menu__label">Police Stations</span>
                </a>
            </li> --}}

        </ul>
    </div>
    <div class="ps__rail-x" style="left: 0px; top: 0px">
        <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px"></div>
    </div>
    <div class="ps__rail-y" style="top: 0px; height: 657px; right: 0px">
        <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 426px"></div>
    </div>
</aside>
