@extends('layouts.app-v1')
@section('css')
<style>
.overlay {
    position:fixed;
    top:0;
    left:0;
    right:0;
    bottom:0;
    //background-color:rgba(0, 0, 0, 0);
    background-color:rgba(0, 0, 0, 0.5);
    z-index:9999 !important;
    color:white;
}

.overlay-center {
    display: inline-block;
    vertical-align: middle;
    padding: 10px 15px;
    position:relative;
    //font-weight:bold;
}

.overlay {
    text-align: center;
}
 
.overlay:before {
    content: '';
    display: inline-block;
    height: 100%;
    vertical-align: middle;
    margin-right: -0.25em;
}

.overlay-inside-div-center {
  position: absolute;
  top: 50%;
  left: 50%;
  height: 30%;
  width: 50%;
  margin: -10% 0 0 -25%;
}

.div-overlay {
    position:absolute;
    top:0;
    left:0;
    right:0;
    bottom:0;
    background-color:rgba(0, 0, 0, 0);
    z-index:9999;
    color:white;
}

.div-overlay-center {
    display: inline-block;
    vertical-align: middle;
    padding: 10px 15px;
    position:relative;
    font-weight:bold;
}

.div-overlay {
    text-align: center;
}
 
.div-overlay:before {
    content: '';
    display: inline-block;
    height: 100%;
    vertical-align: middle;
    margin-right: -0.25em;
}
</style>
@endsection
@section('content')
  <div class="overlay d-none">
      <div class="overlay-center bg-white" style="box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19); width: 400px; height: 260px; border-radius:15px">
          <div class="overlay-inside-div-center downloading d-none" style="left:0;width:100%;margin: -10% 0 0 0;position:absolute;">
            <div id="progress-bar-div" class="card-body" style="position:absolute;width:400px;z-index:1;" >
              <div id="progress-bar-text" style="position:absolute;color:black;top:0"></div>
              <div class="progress progress-xxs mt-1">
                <div id="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" class="progress-bar progress-bar-danger progress-bar-striped" style="width:0%; transition:width 3s ease">
                </div>
              </div>
            </div>

            {{-- <div class="text-black pb-4" style="font-size:18px">Downloading file ...</div>
            <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
              <span class="sr-only">Loading...</span>
            </div> --}}
          </div>
          <div class="overlay-inside-div-center downloaded d-none">
            <div class="text-black pb-4 text-success" style="font-size:18px">File downloaded!</div>
            <a id="save-file-btn" class="btn btn-primary text-white" style="font-size:18px">Save file</a>
          </div>          
      </div>
  </div>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark"> Videos </h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Videos</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container">
      <a id="download"></a>
        <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card">
                <div class="d-none div-overlay">
                  <div class="div-overlay-center">
                    <div class="spinner-grow text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-secondary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>                
                    <div class="spinner-grow text-success" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>                                
                  </div>
                </div>
                <div class="card-header">
                  <h3 class="card-title">Recorded Videos</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table class="table table-bordered">
                    <thead>                  
                      <tr>
                        <th>Date</th>
                        <th>Start</th>
                        <th>End</th>
                        {{-- <th>Duration</th> --}}
                        <th style="width: 130px"></th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($files_arr as $file)
                        <tr>
                          <td>{{$file['date']}}</td>
                          <td>{{$file['start_time'] }}</td>
                          <td>{{ substr($file['time'], 0, 8) }}</td>
                          <td>
                            <button style="width:120px" class="btn btn-primary btn-sm" role="link" data-filename-value="{{$file['filename']}}" data-byte-size-value="{{$file['byte_size']}}">
                              download 
                              <span style="margin-left:5px" class="badge badge-light">{{$file['size']}}</span>
                            </button></td>
                        </tr>
                      @endforeach                                                                                                                               
                    </tbody>
                  </table>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                  <span>Showing <span id="start_entry">{{ $start_entry }}</span> to <span id="end_entry">{{ $end_entry }}</span> of <span id="total_entry">{{$no_of_files}}</span> entries</span>
                  <ul class="pagination pagination-sm m-0 float-right">
                    @if(is_null($previous_page))
                    <li class="page-item mr-1" ><button id="previous-btn" disabled style="width:69px;padding: 1px !important;margin-right: 2px;margin-top: 2px;" class="btn btn-block btn-outline-primary btn-sm">Previous</button></li>
                    @else 
                    <li class="page-item mr-1" ><button id="previous-btn" style="width:69px;padding: 1px !important;margin-right: 2px;margin-top: 2px;" class="btn btn-block btn-outline-primary btn-sm">Previous</button></li>
                    @endif
                    <li class="page-item" style="margin:auto">Page 
                    <select id="page-select">
                      @for ($i = 1; $i <= $total_pages; $i++)
                        @if ($i == $current_page)
                        <option value="{{$i}}" selected="selected">{{$i}}</option>
                        @else
                        <option value="{{$i}}">{{$i}}</option>
                        @endif
                      @endfor                    
                    </select>
                    of <span id="total_pages">{{$total_pages}}</span>
                    </li>
                    @if(is_null($next_page))
                    <li class="page-item ml-1" ><button id="next-btn" style="width:69px;padding: 1px !important;margin-left: 2px;margin-top: 2px;" disabled class="btn btn-block btn-outline-primary btn-sm">Next</button></li>
                    @else 
                    <li class="page-item ml-1" ><button id="next-btn" style="width:69px;padding: 1px !important;margin-left: 2px;margin-top: 2px;" class="btn btn-block btn-outline-primary btn-sm">Next</button></li>
                    @endif
                  </ul>
                </div>
              </div>
          </div>
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
  @include('video.javascript')
@endsection
