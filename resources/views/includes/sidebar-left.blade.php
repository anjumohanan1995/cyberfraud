@php
    use App\Models\RolePermission;
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();
    $role = $user->role;

    // Fetch the permissions for the current user's role
    $permission = RolePermission::where('role', $role)->first();

    // Ensure the permissions are decoded only if they are not already arrays
    $permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
     //dd($permissions);
    $sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);
//dd($sub_permissions);
    // Check for main permissions
    $hasUserManagementPermission = in_array('User Management', $permissions) || $user->role == 'Super Admin';
    $hasRoleManagementPermission = in_array('Role Management', $permissions) || $user->role == 'Super Admin';
    $hasPermissionManagementPermission = in_array('Permission Management', $permissions) || $user->role == 'Super Admin';
    $hasUploadNCRPPermission = in_array('Upload NCRP Case Data Management', $permissions);
    $hasUploadOtherPermission = in_array('Upload Other Case Data Management', $permissions);
    $hasNCRPCasePermission = in_array('NCRP Case Data Management', $permissions);
    $hasOtherCasePermission = in_array('Other Case Data Management', $permissions);
    $hasSelfAssignedCasedataManagementPermission = in_array('Self Assigned Casedata Management', $permissions);
    $hasSourceTypeManagementPermission = in_array('Source Type Management', $permissions);
    $hasNoticeManagementPermission = in_array('Notice Management', $permissions);
    $hasEvidenceManagementPermission = in_array('Evidence Management', $permissions);
    $hasMuleAccountPermission = in_array('Mule Account Management', $permissions);
    $hasReportsPermission = in_array('Reports Management', $permissions);
    $hasEvidenceTypePermission = in_array('Evidence Type Management', $permissions);

    if ($sub_permissions) {
        $hasUploadPrimaryDataPermission = in_array('Upload Primary Data', $sub_permissions);
        $hasUploadBankActionPermission = in_array('Upload Bank Action', $sub_permissions);
        $hasViewSelfAssignedNCRPCasedataPermission = in_array('View Self Assigned NCRP Casedata', $sub_permissions);
        $hasViewSelfAssignedOthersCasedataPermission = in_array('View Self Assigned Others Casedata', $sub_permissions);
        $hasAgainstEvidencePermission = in_array('Against Evidence Permission', $sub_permissions);
        $hasAgainstBankManagement = in_array('Against Bank Management', $sub_permissions);
        $hasAgainstMuleAccountManagement = in_array('Against Mule Account Management', $sub_permissions);
        $hasNoticeViewPermission = in_array('Notice View', $sub_permissions);
        $hasViewEvidenceBasedCasedataPermission = in_array('View Evidence Based Casedata', $sub_permissions);
        $hasViewBankActionBasedCasedataReports = in_array('View Bank Action Based Casedata', $sub_permissions);
        $hasViewDailyBankPermission = in_array('View Daily Bank Reports', $sub_permissions);
        $hasViewAmountwiseReportsPermission = in_array('View Amount wise Report', $sub_permissions);

    } else{
        $hasUploadPrimaryDataPermission = false;
        $hasUploadBankActionPermission = false;
        $hasViewSelfAssignedNCRPCasedataPermission = false;
        $hasViewSelfAssignedOthersCasedataPermission = false;
        $hasAgainstEvidencePermission = false;
        $hasAgainstBankManagement = false;
        $hasAgainstMuleAccountManagement = false;
        $hasNoticeViewPermission = false;
        $hasViewEvidenceBasedCasedataPermission = false;
        $hasViewBankActionBasedCasedataReports = false;
        $hasViewDailyBankPermission = false;
        $hasViewAmountwiseReportsPermission = false;
    }

    @endphp

<aside class="main-sidebar app-sidebar sidebar-scroll ps ps--active-y">
    <div class="main-sidebar-header active">
        <a class="desktop-logo logo-light active">
            <img src="{{ asset('/img/logo.png') }}" class="main-logo" />
        </a>
        <a class="desktop-logo icon-logo active">
            <img src="{{ asset('/img/favicon.png') }}" class="logo-icon" />
        </a>
        <a class="desktop-logo logo-dark active">
            <img src="{{ asset('/img/logo-white.png') }}" class="main-logo dark-theme" alt="logo" />
        </a>
        <a class="logo-icon mobile-logo icon-dark active">
            <img src="{{ asset('img/favicon-white.png') }}" class="logo-icon dark-theme" alt="logo" />
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
            {{-- @php
                $permission = \App\Models\RolePermission::where('role', \Auth::user()->role)->first();
                @$sub_permission = $permission->sub_permissions
                    ? json_decode($permission->sub_permissions, true)
                    : null;
            @endphp --}}
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
                    @if ($hasUserManagementPermission)
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

                            @if ($hasRoleManagementPermission)
                                <li>
                                    <a class="slide-item" href="{{ url('roles') }}">Roles</a>
                                </li>
                            @endif
                            @if ($hasPermissionManagementPermission)
                                <li>
                                    <a class="slide-item" href="{{ url('permissions') }}">Permissions</a>
                                </li>
                            @endif


                        </ul>
                    </li>
                    @endif
        @if ($hasUploadNCRPPermission)
         <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <i class="side-menu__icon fe fe-box"> </i>
                            <span class="side-menu__label">Upload NCRP CaseData</span>
                            <i class="angle fe fe-chevron-down"> </i>
                        </a>
                        <ul class="slide-menu">
                            @if ($hasUploadPrimaryDataPermission)
                                <li>
                                    <a class="slide-item" href="{{ url('import-complaints') }}">Primary Data</a>
                                </li>
                            @endif
                            @if ($hasUploadBankActionPermission)
                                <li>
                                    <a class="slide-item" href="{{ route('bank-case-data.index') }}">Bank Action</a>
                                </li>
                            @endif
                        </ul>
                    </li>
        @endif
        @if ($hasUploadOtherPermission)
            <li class="slide">
                <a class="side-menu__item"  href="{{ route('upload-others-caseData') }}">
                    <i class="side-menu__icon fe fe-box"> </i>
                    <span class="side-menu__label">Upload Others CaseData</span>
                </a>
            </li>
        @endif
        @if ($hasNCRPCasePermission)
            <li class="slide">
                <a class="side-menu__item" href="{{ url('case-data') }}">
                    <i class="side-menu__icon fe fe-database"> </i>
                    <span class="side-menu__label">NCRP Case Data</span>
                </a>
            </li>
        @endif
        @if ($hasOtherCasePermission)
            <li class="slide">
                <a class="side-menu__item" href="{{ url('case-data-others') }}">
                    <i class="side-menu__icon fe fe-database"> </i>
                    <span class="side-menu__label">Others Case Data </span>
                </a>
            </li>
        @endif
        @if($hasSelfAssignedCasedataManagementPermission)
        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <i class="side-menu__icon fe fe-file"> </i>
                <span class="side-menu__label">Self Assigned Case Data</span>
            </a>
            <ul class="slide-menu">
                @if($hasViewSelfAssignedNCRPCasedataPermission) <li>
                    <a class="slide-item" href="{{ url('self-assigned-ncrp-data') }}">Self Assigned NCRP Case Data</a>
                </li>@endif
                @if($hasViewSelfAssignedOthersCasedataPermission)<li>
                    <a class="slide-item" href="{{ route('self.assigned.others') }}">Self Assigned Others Case Data</a>
                </li>@endif
            </ul>

        </li>
        @endif
        @if ($hasSourceTypeManagementPermission)
            <li class="slide">
                <a class="side-menu__item" href="{{ url('sourcetype') }}">
                    <i class="side-menu__icon fe fe-database"> </i>
                    <span class="side-menu__label">Masters</span>
                </a>
            </li>
        @endif
        @if ($hasNoticeManagementPermission)
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon fe fe-file"> </i>
                    <span class="side-menu__label">Notice Management</span>
                    <i class="angle fe fe-chevron-down"> </i>
                </a>
                <ul class="slide-menu">
                    @if($hasAgainstEvidencePermission)
                    <li>
                        {{-- <a class="slide-item" href="{{ route('notice.evidence') }}">Against Evidence</a> --}}
                        <a class="slide-item" href="{{ route('notice.evidence') }}">Against Evidence</a>
                    </li>
                    @endif
                    @if($hasAgainstBankManagement)
                    <li>
                        <a class="slide-item" href="{{ route('notice.bank') }}">Against Bank</a>
                    </li>
                    @endif
                    @if($hasAgainstMuleAccountManagement)
                    <li>
                        {{-- <a class="slide-item" href="{{ route('bank-case-data.index') }}">Against Mule Account</a> --}}
                        <a class="slide-item" href="{{ route('notice.mule.account') }}">Against Mule Account</a>
                    </li>
                    @endif
                    @if ($hasNoticeViewPermission)
                    <li>
                        <a class="slide-item" href="{{ url('notices') }}">View Notices</a>
                    </li>
                @endif
                </ul>

            </li>
        @endif
        @if ($hasEvidenceManagementPermission)
            <li class="slide">
                <a class="side-menu__item" href="{{ url('evidence.management') }}">
                    <i class="side-menu__icon fe fe-database"> </i>
                    <span class="side-menu__label">Evidence Management</span>
                </a>
            </li>
        @endif
        @if ($hasMuleAccountPermission)
            <li class="slide">
                <a class="side-menu__item" href="{{ url('muleaccount') }}">
                    <i class="side-menu__icon fe fe-database"> </i>
                    <span class="side-menu__label">Mule Account</span>
                </a>
            </li>
        @endif
        @if ($hasReportsPermission)
        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <i class="side-menu__icon fe fe-database"> </i>
                <span class="side-menu__label">Reports</span>
                <i class="angle fe fe-chevron-down"> </i>
            </a>
            <ul class="slide-menu">
                @if ($hasViewEvidenceBasedCasedataPermission)
                <li>
                    <a class="slide-item" href="{{ route('evidence.reports.index') }}">Evidence based Case Data</a>
                </li>@endif
                @if($hasViewBankActionBasedCasedataReports)<li>
                    <a class="slide-item" href="{{ route('bank.reports.index') }}">Bank Action based Case Data</a>
                </li>
                @endif
                @if($hasViewDailyBankPermission)
                    <li>
                        <a class="slide-item" href="{{ url('/bank-daily-reports') }}">Daily Bank Reports</a>
                    </li>
                @endif
                @if ($hasViewAmountwiseReportsPermission)

                    <li>
                        <a class="slide-item" href="{{ route('above-one-lakh') }}">Amount wise Report</a>
                    </li>
             @endif
            </ul>
        </li>
    @endif
        {{-- @if ($hasEvidenceTypePermission)
            <li class="slide">
                <a class="side-menu__item" href="{{ url('evidencetype') }}">
                    <i class="side-menu__icon fe fe-database"> </i>
                    <span class="side-menu__label">Evidence Type</span>
                </a>
            </li>
        @endif --}}


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

