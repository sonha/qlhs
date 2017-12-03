<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>@yield('title')</title>
  <meta name="description" content="@yield('description')">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{!! asset('dist/css/AdminLTE.css') !!}">
    <link rel="stylesheet" href="{!! asset('css/styletree.css') !!}">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{!! asset('dist/css/skins/_all-skins.css') !!}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{!! asset('plugins/iCheck/flat/blue.css') !!}">
  <!-- Morris chart -->
  <link rel="stylesheet" href="{!! asset('plugins/morris/morris.css') !!}">
  <!-- jvectormap -->
  <link rel="stylesheet" href="{!! asset('plugins/jvectormap/jquery-jvectormap-1.2.2.css') !!}">
  <!-- Date Picker -->
  <link rel="stylesheet" href="{!! asset('plugins/datepicker/datepicker3.css') !!}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{!! asset('plugins/daterangepicker/daterangepicker.css') !!}">
  <!-- bootstrap wysihtml5 - text editor -->
<!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="{!! asset('bootstrap/css/bootstrap.min.css') !!}">
  <link rel="stylesheet" href="{!! asset('css/toastr.css') !!}">
<!--   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> -->
<!-- <script src="../../js/utility.js"></script> -->
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

<!-- 
  <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->
  <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
    
</head>
<input type="hidden" id="token" value="{{ csrf_token() }}">
<body class="hold-transition skin-blue sidebar-mini" style="background-color: #ecf0f5;">
@yield('content')
</body>
<!-- ./wrapper -->

<!-- jQuery 2.2.3 -->
<script src="{!! asset('plugins/jQuery/jquery-2.2.3.min.js') !!}"></script>
<!-- Bootstrap 3.3.6 -->
<script src="{!! asset('bootstrap/js/bootstrap.min.js') !!}"></script>
<!-- DataTables -->
<script src="{!! asset('js/jquery-ui.min.js') !!}"></script>

<script src="{!! asset('plugins/datatables/jquery.dataTables.min.js') !!}"></script>
<script src="{!! asset('plugins/datatables/dataTables.bootstrap.min.js') !!}"></script>
<!-- SlimScroll -->
<script src="{!! asset('plugins/slimScroll/jquery.slimscroll.min.js') !!}"></script>
<!-- FastClick -->
<script src="{!! asset('plugins/fastclick/fastclick.js') !!}"></script>
<!-- AdminLTE App -->
<script src="{!! asset('dist/js/app.min.js') !!}"></script>
<!-- AdminLTE for demo purposes -->

<script src="{!! asset('dist/js/demo.js') !!}"></script>
<!-- page script -->
<script src="{!! asset('js/myScript.js') !!}"></script>
<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>
<script src="{!! asset('js/moment.min.js') !!}"></script>
<script src="{!! asset('js/jstree.min.js') !!}"></script>
<!-- date-range-picker -->
<script src="{!! asset('plugins/daterangepicker/daterangepicker.js') !!}"></script>
<!-- bootstrap datepicker -->
<script src="{!! asset('plugins/datepicker/bootstrap-datepicker.js') !!}"></script>

<script src="{!! asset('js/utility.js') !!}"></script>
<script src="{!! asset('mystyle/js/styleDanhSachHoTroKinhPhi.js') !!}"></script>
<script src="{!! asset('js/toastr.js') !!}"></script>
</html>