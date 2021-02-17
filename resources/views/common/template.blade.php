<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>
      @php 
      $pageSegment = empty(Request::segment(1)) ? 'Dashboard' : Request::segment(1);
      @endphp
      {!!ucwords( str_replace("-"," ",$pageSegment) )!!} | Baratha Accounting
    </title>
    {{-- favicon --}}
    <link href="{{ asset('img/logobaratha.png') }}" rel="icon">
    <link href="{{ asset('img/logobaratha.png') }}" rel="apple-touch-icon">
    <!-- Custom fonts for this template-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/select2-develop/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/bootstrap-datepicker/dist/css/bootstrap-datepicker3.standalone.min.css') }}" rel="stylesheet" />
    <link href="{{asset('vendor/sweetalert-master/dist/sweetalert.css')}}" rel="stylesheet" />

    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
    <script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
  </head>

  <body id="page-top">
    <div class="loading">
      <div class="info">
        <img src="{{asset('img/loading.gif')}}" alt="">
        <p>Loading...</p>
      </div>
  </div>

    <!-- Page Wrapper -->
    <div id="wrapper">
      <!-- Sidebar -->
      <div class="left-sidebar">
        <ul
          class="navbar-nav sidebar sidebar-dark accordion"
          id="accordionSidebar"
        >
          <!-- Sidebar - Brand -->
          <a
            class="sidebar-brand d-flex align-items-center justify-content-center"
            href="{{ url('/dashboard') }}"
          >
            <div class="sidebar-brand-icon">
              <i class="fa fa-calculator"></i>
            </div>
            <div class="sidebar-brand-text">Baratha Accounting </div>
          </a>

          <!-- Nav Item - Dashboard -->
          <li class="nav-item {{Request::segment(1) == 'dashboard' ? 'active' : ''}}">
            <a class="nav-link" href="{{url('dashboard')}}">
              <i class="fas fa-fw fa-tachometer-alt"></i>
              <span>Dashboard</span></a
            >
          </li>


          <!-- Nav Item - Pages Collapse Menu -->
          <li class="nav-item {{Request::segment(1) == 'data-master' ? 'active' : ''}}">
            <a
              class="nav-link collapsed"
              href="#"
              data-toggle="collapse"
              data-target="#data-master"
              aria-expanded="true"
              aria-controls="data-master"
            >
            <i class="fas fa-fw fa-cog"></i>
              <span>Data Master</span>
            </a>
            <div
              id="data-master"
              class="collapse"
              aria-labelledby="headingTwo"
              data-parent="#accordionSidebar"
            >
              <div class="py-2 collapse-inner rounded">
                <a class="nav-link" href="{{url('data-master/user')}}">
                  <span>Manage User</span>
                </a>
                <a class="nav-link" href="{{url('data-master/perusahaan')}}">
                  <span>Setup Perusahaan</span>
                </a>
              </div>
            </div>
          </li>

          <li class="nav-item {{Request::segment(1) == 'master-akuntansi' ? 'active' : ''}}">
            <a
              class="nav-link collapsed"
              href="#"
              data-toggle="collapse"
              data-target="#master-akuntansi"
              aria-expanded="true"
              aria-controls="master-akuntansi"
            >
            <i class="fas fa-fw fa-money-bill-wave "></i>
              <span>Master Akuntansi</span>
            </a>
            <div
              id="master-akuntansi"
              class="collapse"
              aria-labelledby="headingTwo"
              data-parent="#accordionSidebar"
            >
              <div class="py-2 collapse-inner rounded">
                <a class="nav-link" href="{{url('master-akuntansi/kode-induk')}}">
                  <span>Kode Induk</span>
                </a>
                <a class="nav-link" href="{{url('master-akuntansi/kode-rekening')}}">
                  <span>Kode Rekening</span>
                </a>
                <a class="nav-link" href="{{url('master-akuntansi/kode-biaya')}}">
                  <span>Kode Biaya</span>
                </a>
                <a class="nav-link" href="{{url('master-akuntansi/kunci-transaksi')}}">
                  <span>Kunci Transaksi</span>
                </a>
              </div>
            </div>
          </li>
          
          <li class="nav-item {{Request::segment(1) == 'persediaan' ? 'active' : ''}}">
            <a
              class="nav-link collapsed"
              href="#"
              data-toggle="collapse"
              data-target="#persediaan"
              aria-expanded="true"
              aria-controls="persediaan"
            >
            <i class="fas fa-fw fa-boxes"></i>
              <span>Persediaan</span>
            </a>
            <div
              id="persediaan"
              class="collapse"
              aria-labelledby="headingTwo"
              data-parent="#accordionSidebar"
            >
              <div class="py-2 collapse-inner rounded">
                <a class="nav-link" href="{{url('persediaan/kategori-barang')}}">
                  <span>Kategori Barang</span>
                </a>
                <a class="nav-link" href="{{url('persediaan/barang')}}">
                  <span>Barang</span>
                </a>
                <a class="nav-link" href="{{url('persediaan/pemakaian-barang')}}">
                  <span>Pemakaian Barang</span>
                </a>
                <div class="dropdown-divider"></div>
                <a class="nav-link" href="{{url('persediaan/laporan-pemakaian-barang')}}">
                  <span>Laporan Pemakaian Barang</span>
                </a>
                <a class="nav-link" href="{{url('persediaan/kartu-stock')}}">
                  <span>Kartu Stock</span>
                </a>
                <a class="nav-link" href="{{url('persediaan/posisi-stock')}}">
                  <span>Posisi Stock</span>
                </a>
              </div>
            </div>
          </li>

          <li class="nav-item {{Request::segment(1) == 'pembelian' ? 'active' : ''}}">
            <a
              class="nav-link collapsed"
              href="#"
              data-toggle="collapse"
              data-target="#pembelian"
              aria-expanded="true"
              aria-controls="pembelian"
            >
            <i class="fas fa-fw fa-shopping-cart"></i>
              <span>Pembelian</span>
            </a>
            <div
              id="pembelian"
              class="collapse"
              aria-labelledby="headingTwo"
              data-parent="#accordionSidebar"
            >
              <div class="py-2 collapse-inner rounded">
                <a class="nav-link" href="{{url('pembelian/supplier')}}">
                  <span>Supplier</span>
                </a>
                <a class="nav-link" href="{{url('pembelian/pembelian-barang')}}">
                  <span>Pembelian Barang</span>
                </a>
                <div class="dropdown-divider"></div>
                <a class="nav-link" href="{{url('pembelian/laporan-pembelian-barang')}}">
                  <span>Laporan Pembelian Barang</span>
                </a>
              </div>
            </div>
          </li>

          <li class="nav-item {{Request::segment(1) == 'penjualan' ? 'active' : ''}}">
            <a
              class="nav-link collapsed"
              href="#"
              data-toggle="collapse"
              data-target="#penjualan"
              aria-expanded="true"
              aria-controls="penjualan"
            >
            <i class="fas fa-fw fa-cash-register "></i>
              <span>Penjualan</span>
            </a>
            <div
              id="penjualan"
              class="collapse"
              aria-labelledby="headingTwo"
              data-parent="#accordionSidebar"
            >
              <div class="py-2 collapse-inner rounded">
                <a class="nav-link" href="{{url('penjualan/customer')}}">
                  <span>Customer</span>
                </a>
                <a class="nav-link" href="{{url('penjualan/rekap-hotel')}}">
                  <span>Rekap Hotel</span>
                </a>
                <a class="nav-link" href="{{url('penjualan/rekap-resto')}}">
                  <span>Rekap Resto</span>
                </a>
                <a class="nav-link" href="{{url('penjualan/penjualan-catering')}}">
                  <span>Penjualan Catering</span>
                </a>
              </div>
            </div>
          </li>
          
          <li class="nav-item {{Request::segment(1) == 'kas' ? 'active' : ''}}">
            <a
              class="nav-link collapsed"
              href="#"
              data-toggle="collapse"
              data-target="#kas"
              aria-expanded="true"
              aria-controls="kas"
            >
            <i class="fas fa-fw fa-wallet"></i>
              <span>Kas</span>
            </a>
            <div
              id="kas"
              class="collapse"
              aria-labelledby="headingTwo"
              data-parent="#accordionSidebar"
            >
              <div class="py-2 collapse-inner rounded">
                <a class="nav-link" href="{{url('kas/transaksi-kas')}}">
                  <span>Transaksi Kas</span>
                </a>
                <div class="dropdown-divider"></div>
                <a class="nav-link" href="{{url('kas/laporan-kas')}}">
                  <span>Laporan Kas</span>
                </a>
              </div>
            </div>
          </li>
          
          <li class="nav-item {{Request::segment(1) == 'bank' ? 'active' : ''}}">
            <a
              class="nav-link collapsed"
              href="#"
              data-toggle="collapse"
              data-target="#bank"
              aria-expanded="true"
              aria-controls="bank"
            >
            <i class="fas fa-fw fa-credit-card"></i>
              <span>Bank</span>
            </a>
            <div
              id="bank"
              class="collapse"
              aria-labelledby="headingTwo"
              data-parent="#accordionSidebar"
            >
              <div class="py-2 collapse-inner rounded">
                <a class="nav-link" href="{{url('bank/transaksi-bank')}}">
                  <span>Transaksi Bank</span>
                </a>
                <div class="dropdown-divider"></div>
                <a class="nav-link" href="{{url('bank/laporan-bank')}}">
                  <span>Laporan bank</span>
                </a>
              </div>
            </div>
          </li>
          
          <li class="nav-item {{Request::segment(1) == 'memorial' ? 'active' : ''}}">
            <a
              class="nav-link collapsed"
              href="#"
              data-toggle="collapse"
              data-target="#memorial"
              aria-expanded="true"
              aria-controls="memorial"
            >
            <i class="fas fa-fw fa-file-invoice-dollar"></i>
              <span>Memorial</span>
            </a>
            <div
              id="memorial"
              class="collapse"
              aria-labelledby="headingTwo"
              data-parent="#accordionSidebar"
            >
              <div class="py-2 collapse-inner rounded">
                <a class="nav-link" href="{{url('memorial/memorial')}}">
                  <span>Memorial</span>
                </a>
                <div class="dropdown-divider"></div>
                <a class="nav-link" href="{{url('memorial/laporan-memorial')}}">
                  <span>Laporan Memorial</span>
                </a>
              </div>
            </div>
          </li>

          <li class="nav-item {{Request::segment(1) == 'general-ledger' ? 'active' : ''}}">
            <a
              class="nav-link collapsed"
              href="#"
              data-toggle="collapse"
              data-target="#general-ledger"
              aria-expanded="true"
              aria-controls="general-ledger"
            >
            <i class="fas fa-fw fa-balance-scale"></i>
              <span>General Ledger</span>
            </a>
            <div
              id="general-ledger"
              class="collapse"
              aria-labelledby="headingTwo"
              data-parent="#accordionSidebar"
            >
              <div class="py-2 collapse-inner rounded">
                <a class="nav-link" href="{{url('general-ledger/buku-besar')}}">
                  <span>Buku Besar</span>
                </a>
              </div>
            </div>
          </li>
          
          <!-- Sidebar Toggler (Sidebar) -->
          <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
          </div>
        </ul>
      </div>
      <!-- End of Sidebar -->
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">
            
            <div class="topbar-divider d-none d-sm-block"></div>
            <!-- Nav Item - User Information -->
            <span class="my-auto">{{ Auth::user()->name }}</span>
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{\Auth::user()->nama}}</span>
                <i class="fa fa-user"></i>
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                {{-- <a class="dropdown-item" href="{{ route('user.ganti-password')}}">
                  <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                  Ganti Password
                </a> --}}
                {{-- <div class="dropdown-divider"></div> --}}
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Logout
                </a>
              </div>
            </li>

          </ul>

        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid common-container">

          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div class="h4 mb-0 solid-color font-weight-bold infopage">
                  <?php 
                    $pageSegment = !empty(Request::segment(2)) ? Request::segment(2) : 'Dashboard';
                  ?>
                  {{ ucwords( str_replace("-"," ",$pageSegment) ) }}
            </div>
            <div class="float-right info-text-page">
              <a href="#"> 
                {{ucwords( str_replace("-"," ",$pageSegment) )}}
              </a>
              /
              @if (!empty($pageInfo))
                <a href="#"> {{$pageInfo}}</a>
              @else
                <a href="#"> Dashboard</a>
              @endif
            </div>
          </div>
          <div class="row pb-5">
            <div class="col-md-12">
            @yield('container')
              </div>
          </div>
      </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright 2020 - <a href="https://limadigital.id" target="_blank">LIMA Digital</a></span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
      <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div
      class="modal fade"
      id="logoutModal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
            <button
              class="close"
              type="button"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            Select "Logout" below if you are ready to end your current session.
          </div>
          <div class="modal-footer">
            <button
              class="btn btn-secondary"
              type="button"
              data-dismiss="modal"
            >
              Cancel
            </button>
            <a class="btn btn-primary" href="{{ route('logout') }}" onclick="event.preventDefault();
            document.getElementById('logout-form').submit();">Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
          </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/select2-develop/dist/js/select2.min.js') }}"></script>
    {{-- <script src="{{ asset('vendor/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') }}"></script> --}}
    <script src="{{ asset('vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{asset('vendor/sweetalert-master/dist/sweetalert-dev.js')}}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
  </body>
</html>
