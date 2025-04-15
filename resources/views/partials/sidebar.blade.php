<nav class="sidebar">
    <div class="sidebar-header">
        <a href="javascript:void(0)" class="sidebar-brand">
            <img src="{{ asset('images/sidebar_logo.png') }}" alt="logo" />
        </a>
        <div class="sidebar-toggler not-active">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar-body">
        <ul class="nav">
            @if(Auth::user()->can('viewMenu:Dashboard') || Auth::user()->can('viewMenu:All') || Auth::user()->type ==
            'admin')
            <li class="nav-item nav-category">Main</li>
            <li class="nav-item">
                <a href="{{ URL::to('home') }}" class="nav-link">
                    <i class="link-icon" data-feather="box"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>
            @endif
            @if(Auth::user()->can('viewMenu:Permission') || Auth::user()->can('viewMenu:Roles') ||
            Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
            <!-- <li class="nav-item nav-category">Settings</li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#emails" role="button" aria-expanded="false"
                    aria-controls="emails">
                    <i class="link-icon menu-icon fa fa-lg fa-cog"></i>
                    <span class="link-title">Setting</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="emails">
                    <ul class="nav sub-menu">
                        @if(Auth::user()->can('viewMenu:Permission') || Auth::user()->can('viewMenu:All') ||
                        Auth::user()->type == 'admin')
                        <li class="nav-item">
                            <a href="{{ URL::to('permissions') }}" class="nav-link">Permissions</a>
                        </li>
                        @endif
                        @if(Auth::user()->can('viewMenu:Roles') || Auth::user()->can('viewMenu:All') ||
                        Auth::user()->type == 'admin')
                        <li class="nav-item">
                            <a href="{{ URL::to('roles') }}" class="nav-link">Roles</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li> -->
            @endif
            <li class="nav-item nav-category">Pages</li>
            @if(Auth::user()->can('viewMenu:Vehicle') || Auth::user()->can('viewMenu:VehicleService') ||
            Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#vehicle_nav" role="button" aria-expanded="false"
                    aria-controls="vehicle_nav">
                    <i class="link-icon menu-icon fa fa-lg fa-truck"></i>
                    <span class="link-title">Vehicles</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="vehicle_nav">
                    <ul class="nav sub-menu">
						<li class="nav-item">
                            <a href="{{ URL::to('vehicles') }}" class="nav-link">Vehicles/Units</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ URL::to('meter_histories') }}" class="nav-link">Meter History</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ URL::to('fuel_histories') }}" class="nav-link">Fuel History</a>
                        </li>
                        <!-- <li class="nav-item">
                            <a href="{{ URL::to('vehicle_assignment_histories') }}" class="nav-link">Assignment
                                History</a>
                        </li> -->
                    </ul>
                </div>
            </li>
            @endif
            @if(Auth::user()->can('viewMenu:IssueCategory') || Auth::user()->can('viewMenu:UserIssue') ||
            Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#issue_nav" role="button" aria-expanded="false"
                    aria-controls="issue_nav">
                    <i class="link-icon menu-icon fa fa-lg fa-list-alt"></i>
                    <span class="link-title">Issue</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="issue_nav">
                    <ul class="nav sub-menu">
                        @if(Auth::user()->can('viewMenu:IssueCategory') || Auth::user()->can('viewMenu:All') ||
                        Auth::user()->type == 'admin')
                        <li class="nav-item">
                            <a href="{{ URL::to('issue_categories') }}" class="nav-link">Issue Category</a>
                        </li>
                        @endif
                        @if( Auth::user()->can('viewMenu:UserIssue') || Auth::user()->can('viewMenu:All') ||
                        Auth::user()->type == 'admin')
                        <li class="nav-item">
                            <a href="{{ URL::to('user_issues') }}" class="nav-link">User Issue</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
            @if(Auth::user()->can('viewMenu:Employee') || Auth::user()->can('viewMenu:Customer') ||
            Auth::user()->can('viewMenu:Broker') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#users_nav" role="button" aria-expanded="false"
                    aria-controls="users_nav">
                    <i class="link-icon menu-icon fa fa-lg fa-user"></i>
                    <span class="link-title">Users</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="users_nav">
                    <ul class="nav sub-menu">
                        @if(Auth::user()->can('viewMenu:Employee') || Auth::user()->can('viewMenu:All') ||
                        Auth::user()->type == 'admin')
                        <li class="nav-item">
                            <a href="{{ URL::to('employees') }}" class="nav-link">Employee</a>
                        </li>
                        @endif
                        @if(Auth::user()->can('viewMenu:Broker') || Auth::user()->can('viewMenu:All') ||
                        Auth::user()->type == 'admin')
                        <li class="nav-item">
                            <a href="{{ URL::to('brokers') }}" class="nav-link">Broker</a>
                        </li>
                        @endif
                        @if(Auth::user()->can('viewMenu:Customer') || Auth::user()->can('viewMenu:All') ||
                        Auth::user()->type == 'admin')
                        <li class="nav-item">
                            <a href="{{ URL::to('customers') }}" class="nav-link">Customers</a>
                        </li>
                        @endif
                        @if(Auth::user()->can('viewMenu:User') || Auth::user()->can('viewMenu:All') ||
                        Auth::user()->type == 'admin')
                        <li class="nav-item">
                            <a href="{{ URL::to('users') }}" class="nav-link">Admin Users</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
            @if(Auth::user()->can('viewMenu:Transaction') || Auth::user()->can('viewMenu:All') ||
            Auth::user()->type == 'admin')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#finan_nav" role="button" aria-expanded="false"
                    aria-controls="finan_nav">
                    <i class="link-icon menu-icon fa fa-lg fa-exchange"></i>
                    <span class="link-title">Financials</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="finan_nav">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ URL::to('transaction/employee/1') }}" class="nav-link">Company Transactions</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endif
            @if(Auth::user()->can('viewMenu:Dispatch') ||Auth::user()->can('viewMenu:DispatchTicket') ||
            Auth::user()->can('viewMenu:All') ||
            Auth::user()->type == 'admin')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#dispatch_nav" role="button" aria-expanded="false"
                    aria-controls="dispatch_nav">
                    <i class="link-icon menu-icon fa fa-lg fa-first-order"></i>
                    <span class="link-title">Dispatches</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="dispatch_nav">
                    <ul class="nav sub-menu">
                        @if(Auth::user()->can('viewMenu:Dispatch') || Auth::user()->can('viewMenu:All') ||
                        Auth::user()->type == 'admin')
                        <li class="nav-item">
                            <a href="{{ URL::to('dispatches') }}" class="nav-link">Dispatches</a>
                        </li>
                        @endif
                        @if(Auth::user()->can('viewMenu:DispatchTicket') || Auth::user()->can('viewMenu:All') ||
                        Auth::user()->type == 'admin')
                        <li class="nav-item">
                            <a href="{{ URL::to('dispatch_tickets') }}" class="nav-link">Dispatch Tickets</a>
                        </li>
                        @endif
                        @if(Auth::user()->can('viewMenu:Dispatch') || Auth::user()->can('viewMenu:All') ||
                        Auth::user()->type == 'admin')
                        <li class="nav-item">
                            <a href="{{ URL::to('dispatch/vehicle_assignment') }}" class="nav-link">Vehicle
                                Assignment</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
            @if(Auth::user()->can('viewMenu:Invoice') ||Auth::user()->can('viewMenu:Report') ||
            Auth::user()->can('viewMenu:All') || Auth::user()->type ==
            'admin')
            <li class="nav-item nav-category">Reports</li>
            @if(Auth::user()->can('viewMenu:Invoice') || Auth::user()->can('viewMenu:All') || Auth::user()->type ==
            'admin')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#general-pages" role="button" aria-expanded="false"
                    aria-controls="general-pages">
                    <i class="link-icon menu-icon fa fa-lg fa-file"></i>
                    <span class="link-title">Invoice</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="general-pages">
                    <ul class="nav sub-menu">
                        @if(Auth::user()->can('viewMenu:ActionInvoice') || Auth::user()->can('viewMenu:All') ||
                        Auth::user()->type == 'admin')
                        <li class="nav-item">
                            <a href="{{ URL::to('reports/invoice') }}" class="nav-link">Generate Invoice</a>
                        </li>
                        @endif
                        @if(Auth::user()->can('viewMenu:Invoice') || Auth::user()->can('viewMenu:All') ||
                        Auth::user()->type == 'admin')
                        <li class="nav-item">
                            <a href="{{ URL::to('reports/all_invoices') }}" class="nav-link">Invoices</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
			<li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#general-bill-pages" role="button" aria-expanded="false"
                    aria-controls="general-bill-pages">
                    <i class="link-icon menu-icon fa fa-lg fa-file"></i>
                    <span class="link-title">Bills</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="general-bill-pages">
                    <ul class="nav sub-menu">
                        @if(Auth::user()->can('viewMenu:ActionInvoice') || Auth::user()->can('viewMenu:All') ||
                        Auth::user()->type == 'admin')
                        <li class="nav-item">
                            <a href="{{ URL::to('reports/employee_bill') }}" class="nav-link">Generate Bill</a>
                        </li>
                        @endif
                        @if(Auth::user()->can('viewMenu:Invoice') || Auth::user()->can('viewMenu:All') ||
                        Auth::user()->type == 'admin')
                        <li class="nav-item">
                            <a href="{{ URL::to('reports/employee_all_bills') }}" class="nav-link">Bills</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
            @if(Auth::user()->can('viewMenu:Report') || Auth::user()->can('viewMenu:All') || Auth::user()->type ==
            'admin')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#authPages" role="button" aria-expanded="false"
                    aria-controls="authPages">
                    <i class="link-icon menu-icon mdi mdi-floor-plan"></i>
                    <span class="link-title">Reports</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="authPages">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ URL::to('reports/dispatch_report') }}" class="nav-link">Dispatches</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ URL::to('reports/customer_revenue') }}" class="nav-link">Customers</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ URL::to('reports/employee_payment_earning') }}" class="nav-link">Employees</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endif
            @endif
            @if(Auth::user()->can('viewMenu:Roles') || Auth::user()->can('viewMenu:Fuel') || Auth::user()->can('viewMenu:ServiceCategory') ||
            Auth::user()->can('viewMenu:All') || Auth::user()->type ==
            'admin')
            <li class="nav-item nav-category">Setting</li>
            <li class="nav-item">
                <a href="{{ URL::to('roles') }}" class="nav-link">
                    <i class="link-icon menu-icon fa fa-lg fa-cog"></i>
                    <span class="link-title">Roles</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ URL::to('fuels') }}" class="nav-link">
                    <i class="link-icon menu-icon fa fa-thermometer-full"></i>
                    <span class="link-title">Fuel Prices</span>
                </a>
            </li>
			<li class="nav-item">
                <a href="{{ URL::to('service_categories') }}" class="nav-link">
                    <i class="link-icon menu-icon fa fa-lg fa-cog"></i>
                    <span class="link-title">Expense Categories</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
</nav>