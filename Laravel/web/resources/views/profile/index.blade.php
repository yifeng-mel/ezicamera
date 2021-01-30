@extends('layouts.app-v1')

@section('css')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark"> Profile </h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Profile</li>
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
          <div class="col col-6">
            @if(session()->has('profile_updated'))
              <div class="alert alert-success" role="alert">
                {{session()->get('profile_updated')}}
              </div>
            @endif
            @if(session()->has('please_complete_profile'))
              <div class="alert alert-info" role="alert">
                {{session()->get('please_complete_profile')}}
              </div>
            @endif
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Profile</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" method="post" action="/profile">
                @csrf
                <div class="card-body">
                  <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" id="email" placeholder="Enter email" autocomplete="new-password" value="{{auth()->user()->email}}" disabled style="border:none;background-color:white;padding:0">
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="firstName">First Name *</label>
                      <input name="first_name" value="{{auth()->user()->first_name}}" type="text" class="form-control {{ $errors->has('first_name') ? ' is-invalid' : '' }}" id="firstName" placeholder="First Name" autocomplete="new-password">
                        @if ($errors->has('first_name'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('first_name') }}</strong>
                          </span>
                        @endif
                    </div>
                    <div class="form-group col-md-6">
                      <label for="lastName">Last Name *</label>
                      <input name="last_name" value="{{auth()->user()->last_name}}" type="text" class="form-control {{ $errors->has('last_name') ? ' is-invalid' : '' }}" id="lastName" placeholder="Last Name" autocomplete="new-password">
                      @if ($errors->has('last_name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('last_name') }}</strong>
                        </span>
                      @endif
                    </div>                    
                  </div>
                  <div class="form-group">
                    <label for="dateOfBirth">Date of Birth *</label>
                    <input name="date_of_birth" value="{{auth()->user()->date_of_birth}}" type="text" class="form-control {{ $errors->has('date_of_birth') ? ' is-invalid' : '' }}" id="dateOfBirth" placeholder="Date of Birth" autocomplete="new-password" readonly="readonly" style="background-color:white">
                    @if ($errors->has('date_of_birth'))
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $errors->first('date_of_birth') }}</strong>
                      </span>
                    @endif
                  </div>
                  <div class="form-group">
                    <label for="password">New Password</label>
                    <input name="password" type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" id="password" placeholder="Password" autocomplete="new-password">
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                  </div>
                  <div class="form-group">
                    <label for="passwordConfirmation">Confirm New Password</label>
                    <input name="password_confirmation" type="password" class="form-control" id="passwordConfirmation" placeholder="Password" autocomplete="new-password">
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                    </span>
                  </div>                                    
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
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
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
  $('#dateOfBirth').datepicker({
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0"
  });
</script>
@endsection
