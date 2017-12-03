<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Quản lý hồ sơ</title>
  <meta name="csrf_token" content="{{ csrf_token() }}">
  <meta name="description" content="@yield('description')">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="{!! asset('/bootstrap/css/bootstrap.min.css') !!}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{!! asset('/dist/css/AdminLTE.css') !!}">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{!! asset('/dist/css/skins/_all-skins.css') !!}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{!! asset('/plugins/iCheck/flat/blue.css') !!}">
  <!-- Morris chart -->
  <link rel="stylesheet" href="{!! asset('/plugins/morris/morris.css') !!}">
  <!-- jvectormap -->
  <link rel="stylesheet" href="{!! asset('/plugins/jvectormap/jquery-jvectormap-1.2.2.css') !!}">
  <!-- Date Picker -->
  <link rel="stylesheet" href="{!! asset('/plugins/datepicker/datepicker3.css') !!}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{!! asset('/plugins/daterangepicker/daterangepicker.css') !!}">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="{!! asset('/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') !!}">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

  <link rel="stylesheet" href="{!! asset('css/styletree.css') !!}">
  <link rel="stylesheet" href="{!! asset('css/toastr.css') !!}">

  <link rel="stylesheet" type="text/css" href="{!! asset('/themes/default/easyui.css') !!}">
  <link rel="stylesheet" type="text/css" href="{!! asset('/themes/icon.css') !!}">
  <link rel="stylesheet" type="text/css" href="{!! asset('/themes/demo.css') !!}">
<!--   <link rel="stylesheet" type="text/css" href="/css/build.css"> -->
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.0.min.js"></script>
  <script type="text/javascript" src="http://www.jeasyui.com/easyui/jquery.easyui.min.js"></script>

  <script>
    $(function () {
        focusSelect2();
        getListWarning(10);
        setInterval(function () {
            getListWarning(10);
            }, 60000);
        
          $('body').on('keydown', 'input, select, textarea', function(e) {
              var self = $(this)
                , form = self.parents('form:eq(0)')
                , focusable
                , next
                , prev
                ;

              if (e.shiftKey) {
               if (e.keyCode == 13) {
                   focusable =   form.find('input,select,button,textarea').filter(':visible');
                   prev = focusable.eq(focusable.index(this)-1); 

                   if (prev.length) {
                      prev.focus();
                   } else {
                     // form.submit();
                  }
                }
              }
                else
              if (e.keyCode == 13) {
                  focusable = form.find('input,select,button,textarea').filter(':visible');
                  next = focusable.eq(focusable.index(this)+1);
                  if (next.length) {
                      next.focus();
                  } else {
                      //form.submit();
                  }
                  return false;
              }
    });
          // setInterval(function () {
          //       message();      
          //   }, 20000);

        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
     });
    function getListWarning(count) {
            // $('#lable_see_detail_header').show();
            if (count == null || count === undefined)
                count = 20;
            $.ajax({
                type: "GET",
                dataType: "json",
                contentType: "application/json; charset=utf-8",
                url: "/ho-so/message/" + count,
                success: function (dataGet) {
                    if (!dataGet) {
                        document.getElementById('logout-form').submit();
                    } else {
                      $('#header-message').html('Có '+dataGet.total+' thông báo mới');
                        if (dataGet.data.length > 0) {
                            $('.label-warning').html(dataGet.total);
                            var show_data = "";
                            for (var i = 0; i < dataGet.data.length; i++) {
                                var dl = dataGet.data[i];
                                if(parseInt(dl.type) > 0){
                                  show_data += '<li><a href="#"><div class="pull-left">'
                                            + ' <i class="fa fa-reply-all"></i></div><h4>'
                                            + dl.message_text 
                                            + '<small><i class="fa fa-clock-o"></i> '+timeAgo(dl.updated_at)+'</small></h4>'
                                            + '<p>'+dl.schools_name+'</p> </a> </li>';
   
                                }else{//
                                  show_data += '<li><a href="#"><div class="pull-left">'
                                            + ' <i class="fa fa-envelope-o"></i></div><h4>'
                                            + dl.message_text 
                                            + '<small><i class="fa fa-clock-o"></i> '+timeAgo(dl.updated_at)+'</small></h4>'
                                            + '<p>'+dl.schools_name+'</p> </a> </li>';
                                    
                                }
                                
                            }
                            $('ul#alert-message').html(show_data);
                            // if (!$('#liShowLstWarning').hasClass('open') && count == 20) {
                            //     $('ul.warning').html(show_data);
                            //     $('#lable_see_detail_header').show();
                            // }
                            // if ($('#liShowLstWarning').hasClass('open') && count != 20) {
                            //     $('ul.warning').empty();
                            //     $('#lable_see_detail_header').show();
                            //     $('ul.warning').append(show_data);
                            // }
                        } else {
                            $('.label-warning').html(0);
                            $('ul#alert-message').html("");
                        }
                    }
                },
                error: function (data) {
                  console.log(data);
                }
            });
        };
 
    </script>
     <style type="text/css">
      .dataTables_wrapper .dataTables_filter {
    float: right;
    text-align: right;
}

  .modals {
    display:    none;
    position:   fixed;
    z-index:   1000;
    top:        0;
    left:       0;
    height:     100%;
    width:      100%;
    background: rgba( 255, 255, 255, .8 ) 
                url('http://i.stack.imgur.com/FhHRx.gif') 
                50% 50% 
                no-repeat;
}
.panel{
  margin-bottom: 0 !important;
}

.input-sm + .form-control-feedback {
    line-height: 34px !important;
}
.input-sm {
    height: 34px !important;
}

/* When the body has the loading class, we turn
   the scrollbar off with overflow:hidden */
body.loading {
    overflow: hidden;   
}

/* Anytime the body has the loading class, our
   modal element will be visible */
body.loading .modals {
    display: block ;
}

    </style>
</head>
<body class="hold-transition skin-blue layout-top-nav">

<div class="wrapper">
  <header class="main-header">
    <nav class="navbar navbar-static-top">
      <div class="container" style="width: 100%;">
        <div class="navbar-header">
          <a href="/" class="navbar-brand"><b>QL </b>hồ sơ</a>
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
            <i class="fa fa-bars"></i>
          </button>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
          <ul class="nav navbar-nav">
                <?php
                            $data = \Illuminate\Support\Facades\DB::select('select qlhs_modules.* from qlhs_modules LEFT JOIN (SELECT module_id,role_user_id from permission_users GROUP BY module_id,role_user_id ) as permission_user
 on qlhs_modules.module_id = permission_user.module_id  where module_view = :view and role_user_id = :id and  module_nav is not null order by module_order,module_id', ['id' => Auth::user()->id,'view' => 1]);
                           // var_dump($data) or die;
                            echo \helperClass\helperClass::menuIndex($data,'',0,'', "",Auth::user()->id);
                        ?>
          </ul>

        </div>
        <!-- /.navbar-collapse -->
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">

            <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span class="label label-warning">0</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header" id="header-message"></li>
              <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu" id="alert-message">
                  
                  
                </ul>
              </li>
              <!-- <li class="footer"><a href="#">View all</a></li> -->
            </ul>
          </li>
            <!-- User Account Menu -->
            <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <span class="hidden-xs" style="font-weight: 700">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
            </a>
            <input type="hidden" name="school-per" id="school-per" value="{{Auth::user()->truong_id}}">
            <ul class="dropdown-menu">            
              <li>
                            <a  href="{{ url('change-pass') }}/{{ Auth::user()->username }}"><i class="fa fa-fw fa-user"></i> Thông tin cá nhân</a>
                        </li>
                      
                        <li>
                            <a href="{{ url('/logout') }}"
                                onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                                <i class="fa fa-fw fa-power-off"></i> Đăng xuất
                            </a>

                            <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
            </ul>
          </li>
          </ul>
        </div>
        <!-- /.navbar-custom-menu -->
      </div>
      <!-- /.container-fluid -->
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->

  <div class="content-wrapper">
  @yield('content')
    <!-- Content Header (Page header) -->
    
        <!-- /.col -->
</div>
      <!-- /.row (main row) -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<!--   <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; 2017 <a href="#">Nghị Lê</a>.</strong> 
  </footer> -->

  
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 2.2.3 -->
<script src="{!! asset('/plugins/jQuery/jquery-2.2.3.min.js') !!}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- DataTables -->
<script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../plugins/datatables/dataTables.bootstrap.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="{!! asset('/bootstrap/js/bootstrap.min.js') !!}"></script>
<!-- Morris.js charts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<!-- <script src="plugins/morris/morris.min.js"></script> -->
<!-- Sparkline -->
<script src="{!! asset('/plugins/sparkline/jquery.sparkline.min.js') !!}"></script>
<!-- jvectormap -->
<script src="{!! asset('/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') !!}"></script>
<script src="{!! asset('/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') !!}"></script>
<!-- jQuery Knob Chart -->
<script src="{!! asset('/plugins/knob/jquery.knob.js') !!}"></script>
<!-- daterangepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="{!! asset('/plugins/daterangepicker/daterangepicker.js') !!}"></script>
<!-- datepicker -->
<script src="{!! asset('/plugins/datepicker/bootstrap-datepicker.js') !!}"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="{!! asset('/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') !!}"></script>
<!-- Slimscroll -->
<script src="{!! asset('/plugins/slimScroll/jquery.slimscroll.min.js') !!}"></script>
<!-- FastClick -->
<script src="{!! asset('/plugins/fastclick/fastclick.js') !!}"></script>
<script type="text/javascript" src="../../js/jstree.min.js"></script>
<script src="{!! asset('plugins/bootstrap-filestyle/bootstrap-filestyle.js') !!}"></script>
<script src="{!! asset('js/utility.js') !!}"></script>
<script src="{!! asset('js/toastr.js') !!}"></script>
<!-- AdminLTE App -->
<script src="{!! asset('/js/select2.min.js') !!}"></script>
<script src="{!! asset('/dist/js/app.min.js') !!}"></script>
<script src="{!! asset('/js/myScript.js') !!}"></script>
<script src="{!! asset('/dist/js/demo.js') !!}"></script>
<script src="{!! asset('dist/js/bootstrap-multiselect.js') !!}"></script>
<!--
<script src="{!! asset('/mystyle/js/jsHoso.js') !!}"></script>
<script type="text/javascript" src="{!! asset('mystyle/js/jsDanhMuc.js') !!}"></script>
<script type="text/javascript" src="{!! asset('mystyle/js/styleRole.js') !!}"></script>
<script type="text/javascript" src="{!! asset('mystyle/js/styleLapDanhSach.js') !!}"></script> -->

</body>
</html>
