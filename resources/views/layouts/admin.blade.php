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
<!--   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css"> -->
  <!-- Ionicons -->
<!--   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css"> -->
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
<!--   <link rel="stylesheet" href="{!! asset('plugins/jvectormap/jquery-jvectormap-1.2.2.css') !!}"> -->
  <!-- Date Picker -->
  <link rel="stylesheet" href="{!! asset('plugins/datepicker/datepicker3.css') !!}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{!! asset('plugins/daterangepicker/daterangepicker.css') !!}">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="{!! asset('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') !!}">
<!--   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> -->
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="{!! asset('bootstrap/css/bootstrap.min.css') !!}">
<!--   <link rel="stylesheet" href="../../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css"> -->
      <link rel="stylesheet" href="{!! asset('dist/css/bootstrap-multiselect.css') !!}">
       <link rel="stylesheet" href="{!! asset('css/toastr.css') !!}">
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
    <style type="text/css">
      
  .modals {
    display:    none;
    position:   fixed;
    z-index:    1000;
    top:        0;
    left:       0;
    height:     100%;
    width:      100%;
    background: rgba( 255, 255, 255, .8 ) 
                url('http://i.stack.imgur.com/FhHRx.gif') 
                50% 50% 
                no-repeat;
}

/* When the body has the loading class, we turn
   the scrollbar off with overflow:hidden */
body.loading {
    overflow: hidden;   
}

/* Anytime the body has the loading class, our
   modal element will be visible */
body.loading .modals {
    display: block;
}

    </style>
</head>
<input type="hidden" id="token" value="{{ csrf_token() }}">
<body class="hold-transition skin-blue sidebar-mini" style="background-color: #ecf0f5;">
@yield('content')
</body>
<!-- ./wrapper -->

<!-- jQuery 2.2.3 -->
<script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../../bootstrap/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../plugins/datatables/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="../../plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>
<script src="../../js/myScript.js"></script>
<!-- page script -->
<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>
<script src="../../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<script src="../../mystyle/js/styleRole.js"></script>
<script type="text/javascript" src="../../js/jstree.min.js"></script>
<!-- date-range-picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="../../plugins/daterangepicker/daterangepicker.js"></script>
<!-- bootstrap datepicker -->
<script type="text/javascript" src="{!! asset('js/utility.js') !!}"></script>
<script src="../../plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="../../plugins/datepicker/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="{!! asset('mystyle/js/jsDanhMuc.js') !!}"></script>
<script src="{!! asset('dist/js/bootstrap-multiselect.js') !!}"></script>
<!-- AutoComplete -->
<script src="{!! asset('js/jquery-ui.min.js') !!}"></script>
<script src="{!! asset('js/toastr.js') !!}"></script>
</html>