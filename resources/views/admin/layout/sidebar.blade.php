<nav class="main-header navbar navbar-expand navbar-white navbar-light">
        
</nav>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
<!-- Brand Logo -->
<a href="" class="brand-link">
  <img src="{{ asset('vendor/keyur/prikedcd/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
  <span class="brand-text font-weight-light">Dead Code Detector</span>
</a>

<!-- Sidebar -->
<div class="sidebar">
  <!-- Sidebar user panel (optional) -->
  <div class="mt-3">
    <div class="">
    </div>
    <div class="">
    </div>
  </div>

  <!-- SidebarSearch Form -->
  <div class="form-inline">
   
  </div>

  <!-- Sidebar Menu -->
  <nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
      <li class="nav-item">
            <a href="{{ route('controller_scan') }}" class="nav-link {{ request()->routeIs('controller_scan') ? 'active' : '' }}">
              <i class="far fa-circle nav-icon"></i>
              <p>Controller</p>
            </a>
      </li>
      <li class="nav-item">
            <a href="{{ route('model_scan') }}" class="nav-link {{ request()->routeIs('model_scan') ? 'active' : '' }}">
              <i class="far fa-circle nav-icon"></i>
              <p>Models</p>
            </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('app_scan') }}" class="nav-link {{ request()->routeIs('app_scan') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
          <p>
            Controller and Model
          </p>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->
</aside>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        @if (request()->routeIs('controller_scan'))
        <h1 class="m-0">Controllers</h1>
        @elseif (request()->routeIs('model_scan'))
        <h1 class="m-0">Models</h1>
        @elseif (request()->routeIs('app_scan'))
        <h1 class="m-0">Controller and Model</h1>
        @endif
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('controller_scan') }}">Home</a></li>
          <li class="breadcrumb-item active">{{ request()->routeIs('controller_scan') ? 'Controllers' : (request()->routeIs('model_scan') ? 'Models' : 'Controller and Model') }}</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->