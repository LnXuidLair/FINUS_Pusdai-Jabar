<div class="header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="float-left">
                    <div class="hamburger sidebar-toggle">
                        <span class="line"></span>
                        <span class="line"></span>
                        <span class="line"></span>
                    </div>
                </div>
                <div class="float-right">
                    <div class="dropdown dib">
                        <div class="header-icon dropdown-toggle d-flex align-items-center"
                            data-toggle="dropdown" style="cursor:pointer">
                            <i class="fa fa-circle-user mr-2"></i>
                            <span>{{ auth()->user()->first_name }}</span>
                        </div>
                        <div class="drop-down dropdown-menu dropdown-menu-right">
                            <div class="dropdown-content-heading px-3 py-2">
                                <strong>{{ auth()->user()->name }}</strong>
                                <small class="text-muted d-block">{{ auth()->user()->email }}</small>
                            </div>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit">
                                    <i class="ti-power-off mr-2"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>