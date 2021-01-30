<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title>SmartVision</title>

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css">

  <!-- Theme style -->
  <link rel="stylesheet" href="/adminlte/dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="/css/fonts.css">
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        Smart Vision
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
          @if(!is_null($error))
            <p class="login-box-msg text-danger">{{$error}}</p>
          @else
            <p class="login-box-msg">Your password has been reset successfully!</p>
          @endif
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
  $('#dateOfBirth').datepicker({
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0"
  });
</script>

</body>

</html>
