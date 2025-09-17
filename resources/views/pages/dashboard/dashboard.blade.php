@extends('pages.index')

@section('admin_content')
<div class="container-fluid">
  <!-- ========== title-wrapper start ========== -->
  <div class="title-wrapper pt-30">
    <div class="row align-items-start">
      <div class="col-md-6">
        <div class="title">
          <h2>Dashboard</h2>
        </div>
      </div>
        <div class="col-md-6">
            <div class="breadcrumb-wrapper">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item">
                        <a href="{{ route('dashboard.index') }}">Dashboard</a>
                      </li>
                      <li class="breadcrumb-item active" aria-current="page">
                        Dashboard
                      </li>
                    </ol>
                </nav>
            </div>
        </div>
              <!-- end col -->
    </div>
  </div>
  <!-- ========== title-wrapper end ========== -->
  <div class="row">
    <div class="col-xl-3 col-lg-4 col-sm-6">
        <div class="icon-card mb-30 ">
            <div class="icon purple">
                 <i class="lni lni-dashboard"></i>
            </div>
                <div class="content">
                    <h6 class="mb-10">Total Siswa</h6>
                    <h3 class="text-bold mb-10">34,567</h3>
                </div>
        </div>
    </div>
    <!-- End Col -->
    <div class="col-xl-3 col-lg-4 col-sm-6">
        <div class="icon-card mb-30">
            <div class="icon success">
            <i class="lni lni-users"></i>  <!-- GANTI ICON JADI USERS -->
            </div>
            <div class="content">
            <h6 class="mb-10">Siswa Kelas 10</h6>
            <h3 class="text-bold mb-10">1,234</h3> <!-- GANTI JADI ANGKA SISWA -->
            </div>
        </div>
        <!-- End Icon Cart -->
    </div>

    <!-- End Col -->
    <div class="col-xl-3 col-lg-4 col-sm-6">
      <div class="icon-card mb-30">
        <div class="icon primary">
          <i class="lni lni-user"></i>
        </div>
        <div class="content">
          <h6 class="mb-10">Siswa Kelas 11</h6>
          <h3 class="text-bold mb-10">24,567</h3>
        </div>
      </div>
      <!-- End Icon Cart -->
    </div>
    <!-- End Col -->
    <div class="col-xl-3 col-lg-4 col-sm-6">
      <div class="icon-card mb-30">
        <div class="icon orange">
          <i class="lni lni-graduation"></i>
        </div>
        <div class="content">
          <h6 class="mb-10">Siswa Kelas 12</h6>
          <h3 class="text-bold mb-10">34,567</h3>
        </div>
      </div>
      <!-- End Icon Cart -->
    </div>
    <!-- End Col -->
  </div>
  <!-- End Row -->
  <div class="row">
    <div class="col-lg-7">
      <div class="card-style mb-30">
        <div class="title d-flex flex-wrap justify-content-between">
          <div class="left">
            <h6 class="text-medium mb-10">Yearly Stats</h6>
          </div>
          <div class="right">
            <div class="select-style-1">
              <div class="select-position select-sm">
                <select class="light-bg">
                  <option value="">Yearly</option>
                  <option value="">Monthly</option>
                  <option value="">Weekly</option>
                </select>
              </div>
            </div>
            <!-- end select -->
          </div>
        </div>
        <!-- End Title -->
        <div class="chart">
          <canvas id="Chart1" style="width: 100%; height: 400px; margin-left: -35px;"></canvas>
        </div>
        <!-- End Chart -->
      </div>
    </div>
    <!-- End Col -->
    <div class="col-lg-5">
      <div class="card-style mb-30">
        <!-- End Title -->
        <div class="chart">
          <canvas id="Chart2" style="width: 100%; height: 400px; margin-left: -45px;"></canvas>
        </div>
        <!-- End Chart -->
      </div>
    </div>
    <!-- End Col -->
  </div>

  <!-- End Row -->
    <div class="row">
        <div class="col-lg-5">
        <div class="card-style calendar-card mb-30">
            <div id="calendar-mini"></div>
        </div>
        </div>
    </div>
  <!-- End Row -->
</div>
@endsection

