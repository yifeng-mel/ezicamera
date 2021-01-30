@extends('layouts.app-v1')

@section('meta')
<meta http-equiv="refresh" content="1800" >
@endsection

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark"> Camera </h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Camera</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container">
        <div class="row justify-content-center">
            <div id="progress-bar-div" class="card-body" style="position:absolute;width:400px;top:170px;z-index:1;display:none" >
              <div id="progress-bar-text" style="position:absolute;color:white;top:0"></div>
              <div class="progress progress-xxs mt-1">
                <div id="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" class="progress-bar progress-bar-danger progress-bar-striped" style="width:0%; transition:width 3s ease">
                </div>
              </div>
            </div>
            <video id="stream" muted controls autoplay playsinline width="640" height="480" style="max-width:100%;background-color:black">
            Your browser doesn't support video
            </video>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <h5>Title</h5>
      <p>Sidebar content</p>
    </div>
  </aside>
  <!-- /.control-sidebar -->
@endsection

@section('js')
  @include('camera.javascript')
@endsection