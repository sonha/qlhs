@extends('layouts.admin')

@section('title', 'This is a blank page')
@section('description', 'This is a blank page that needs to be implemented')

@section('content')
<script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
<section class="content">
<script type="text/javascript">

  function test(){
   $("#myModal").modal("show");
   }
   function testPhanquyen(){

    $("#myModalPhanQuyen").modal("show");
   }
</script>

<div class="panel panel-default">
    <div class="panel-heading">
       <b> {{$category}} </b> 
    </div>
</div>
    <div class="box">
            <!-- /.box-header -->
        <section class="content" style="min-height: 0;">

        
          <?php 
          $datachild = \Illuminate\Support\Facades\DB::select('select qlhs_modules.* from qlhs_modules LEFT JOIN (SELECT module_id,role_user_id from permission_users GROUP BY module_id,role_user_id ) as permission_user
 on qlhs_modules.module_id = permission_user.module_id  where module_view = :view and role_user_id = :id and module_parentid = :parent order by module_order,module_id', ['id' => Auth::user()->id,'view' => 1,'parent' =>7]);
          if(count($datachild)>0){ ?>
           <div class="row">
            <?php
            foreach ($datachild as $value){
            ?>
              <div class="col-md-3 col-sm-6 col-xs-12">
              <a href="/{{$value->module_path}}">
                <div class="info-box" >
                  <span class="info-box-icon">
                  <img src="../../../../images/{{$value->module_icon}}">
                 <!--  <i class="glyphicon glyphicon-leaf"></i> -->
                  </span>

                  <div class="info-box-content" style="margin-left: 10px">
                    <span class="info-text" style="font-weight: bold;">{{$value->module_listfile}}</span>
                    <span class="info-number">{{$value->module_name}}</span>
                  </div>
                  <!-- /.info-box-content -->
                </div>
                </a>
              </div>
              <?php } ?>
                </div>   

               <?php }else{ ?>
                <div class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Thông báo!</h4>
                Bạn không có quyền truy cập vào nội dung này
                  </div>
                <?php } ?>      
        </section>
            
    </div>
          <!-- /.box -->
</div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>

@endsection