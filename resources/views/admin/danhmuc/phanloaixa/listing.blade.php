@extends('layouts.front')

@section('title', 'This is a blank page')
@section('description', 'This is a blank page that needs to be implemented')

@section('content')
<script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
<section class="content">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
<script src="{!! asset('/js/myScript.js') !!}"></script>
<script type="text/javascript" src="{!! asset('mystyle/js/jsDanhMuc.js') !!}"></script>
<style type="text/css">
    #using_json_2 a {
    white-space: normal !important;
    height: auto;
    padding: 1px 2px;
    font-size: 12px;
    width: 90%;
}
</style>
<script type="text/javascript">
$(function () {
    loadComboWard(null);
    $('#txtWardCode').focus();
    $('#using_json_2').jstree({
        'core' : {
            'data' : 
            {
                'url' : function (node) {
                    //console.log(node);
                return node.id === '#' ?
                    '/danh-muc/phanloaixa/deptwards' :
                    '/danh-muc/phanloaixa/childwards';
                },
                'data' : function (node) {
                    //alert(node.id);
                    //console.log(node);
                  return { 'id' : node.id };
                }
            }
        }
    });

    $('#using_json_2').on("changed.jstree", function (e, data) {
            var currentIdSelected = data.selected[0];
            
            var v_objJson = JSON.stringify({ WARDID: currentIdSelected });

            //alert(v_objJson);
            $.ajax({
              type: "POST",
              url:'/danh-muc/phanloaixa/getWardbyID',
              data: v_objJson,
              dataType: 'json',
              cache: false,
              contentType: 'application/json; charset=utf-8',
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
              },
              success : function (results){
                var level = 0;
                var item = '';
                $('#txtWardID').val(results[0]['wards_id']);
                $('#txtWardCode').val(results[0]['wards_code']);
                $('#txtWardName').val(results[0]['wards_name']);
                $('#drWardActive').val(results[0]['wards_active']);

                loadComboWard( function(data){
                    $('#drWardParent').val(results[0]['wards_parent_id']);
                });

                $('#btnInsertWard').html('Lưu');
                $("#btnDeleteWard").attr("disabled", false);
                $("#txtWardCode").attr("disabled", true);
              },
              error : function (){
                console.log('Có lỗi xảy ra trong quá trình xử lý');
              }
            });
    });
  
});
</script>
<div class="modal fade" id="myModalPhanQuyen" role="dialog">
    <div class="modal-dialog modal-md" style="width: auto;margin: 10px;">
    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Phân quyền</h4>
        </div>
          <form id="permission_group_form" action="">            
                <div class="modal-body">
                    <div class="row" id="permission_group_message" style="padding-left: 10%;padding-right: 10%"></div>   
                    <div class="row" id="group_message_applyRole">
                        <div class="col-sm-12" id="TitleRole">

                        </div>
                        <input type ="text" class="hidden" id="RoleId" name="roleId">
                    </div>   
                      
                    <div class="row" style="overflow: auto; height: 400px">
                        <div class="col-sm-12">                                                 
                            <div class="panel-body">
                                <div class="row">
                                    <table class="table table-striped table-bordered table-hover" style="width: 100%">                                   
                                        <thead>
                                            <tr class="success">
                                                <th class="text-center">Chức năng</th>
                                                <th class='text-center'>Xem<br/><span><input type='checkbox' id="CheckAllGroupGet"/></span></th>
                                                <th class='text-center'>Thêm<br/><span><input type='checkbox' id="CheckAllGroupAdd"/></span></th>
                                                <th class='text-center'>Sửa<br/><span><input type='checkbox' id="CheckAllGroupUpdate"/></span></th>
                                                <th class='text-center'>Xóa<br/><span><input type='checkbox' id="CheckAllGroupDelete"/></span></th>
                                                <th class='text-center'>Quản lý người dùng<br/><span><input type='checkbox' id="CheckAllGroupBusiness"/></span></th>
                                            </tr>                           
                                        </thead>
                                         <?php
                                        $data = \Illuminate\Support\Facades\DB::select('select * from qlhs_modules where module_view = :view and module_parentid = 0  order by module_order,module_id', ['view' => 1]);
                                        ?>
                                        <tbody id="permission_group_data">
                                         @foreach($data as $key=>$value)
                                         <tr>
                                    <td id="code"><a href="#">-- {{$value->module_name}}</a></td>
                                    <td class="text-center" id="name"><input class="apiCheck" value="{{$value->module_id}}" name="get" id="getCheck{{$value->module_id}}" onclick="checkEvent(this)" type="checkbox"></td>
                                    <td class="text-center" id="name"><input class="apiCheck" value="{{$value->module_id}}" name="add" id="addCheck{{$value->module_id}}" onclick="checkEvent(this)" type="checkbox"></td>
                                    <td class="text-center" id="name"><input class="apiCheck" value="{{$value->module_id}}" name="update" id="updateCheck{{$value->module_id}}" onclick="checkEvent(this)" type="checkbox"></td>
                                    <td class="text-center" id="name"><input class="apiCheck" value="{{$value->module_id}}" name="delete" id="deleteCheck{{$value->module_id}}" onclick="checkEvent(this)" type="checkbox"></td>
                                    <td class="text-center" id="name"><input class="apiCheck" value="{{$value->module_id}}" name="business" id="businessCheck{{$value->module_id}}" onclick="checkEvent(this)" type="checkbox"></td>
                                </tr>
                                          <?php
                                         $datachild = \Illuminate\Support\Facades\DB::select('select * from qlhs_modules where module_view = :view and module_parentid = :parent  order by module_order,module_id',  ['view' => 1,'parent' =>$value->module_id]);
                                         ?>
                                          @foreach($datachild as $key=>$value1)
                                            <tr>
                                                <td id="code"><a href="#">----- {{$value1->module_name}}</a></td>
                                                <td class="text-center" id="name"><input class="apiCheck" value="{{$value1->module_id}}" name="get" id="getCheck{{$value1->module_id}}" onclick="checkEvent(this)" type="checkbox"></td>
                                    <td class="text-center" id="name"><input class="apiCheck" value="{{$value1->module_id}}" name="add" id="addCheck{{$value1->module_id}}" onclick="checkEvent(this)" type="checkbox"></td>
                                    <td class="text-center" id="name"><input class="apiCheck" value="{{$value1->module_id}}" name="update" id="updateCheck{{$value1->module_id}}" onclick="checkEvent(this)" type="checkbox"></td>
                                    <td class="text-center" id="name"><input class="apiCheck" value="{{$value1->module_id}}" name="delete" id="deleteCheck{{$value1->module_id}}" onclick="checkEvent(this)" type="checkbox"></td>
                                    <td class="text-center" id="name"><input class="apiCheck" value="{{$value1->module_id}}" name="business" id="businessCheck{{$value1->module_id}}" onclick="checkEvent(this)" type="checkbox"></td>
                                            </tr>
                                          @endforeach           
                                @endforeach                                       
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>   
                    </div>
                    <div class="modal-footer">
                        <div class="row text-center">
                            <button type="submit" class="btn btn-primary" id ="saveApplyRole">Cập nhật phân quyền</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>   
            </form>  
      </div>
      
    </div>
</div>

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-md" style="width: auto;margin: 10px;">
    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Thêm mới nhóm quyền</h4>
        </div>
        <form class="form-horizontal" action="">  
        <input type="hidden" class="form-control" id="txtIdRoleGroup">          
                <div class="modal-body">
                    <div class="row" id="group_message" style="padding-left: 10%;padding-right: 10%"></div>   
                    <div class="box-body">
                                <div class="form-group">
                                  <label for="inputEmail3" class="col-sm-2 control-label">Mã địa phương</label>

                                  <div class="col-sm-10">
                                    <input type="text" class="form-control" id="txtWardCodePopup" placeholder="Nhập mã địa phương" autofocus="true">
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="inputEmail3" class="col-sm-2 control-label">Tên nhóm</label>

                                  <div class="col-sm-10">
                                    <input type="text" class="form-control" id="txtWardNamePopup" placeholder="Nhập tên địa phương">
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="inputEmail3" class="col-sm-2 control-label">Cấp hành chính</label>

                                  <div class="col-sm-10">
                                    <select id="drLevelWardPopup" class="form-control">
                                        <option value="0">---Chọn cấp---</option>
                                        <option value="1">Tỉnh/ Thành phố</option>
                                        <option value="2">Huyện/ Quận</option>
                                        <option value="3">Xã/ Phường</option>
                                    </select>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="inputEmail3" class="col-sm-2 control-label">Trực thuộc</label>

                                  <div class="col-sm-10">
                                    <select id="drWardParentPopup" class="form-control">
                                        <option value="0">---Chọn cấp---</option>
                                    </select>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="inputEmail3" class="col-sm-2 control-label">Trạng thái</label>

                                  <div class="col-sm-10">
                                    <select id="drWardActivePopup" class="form-control">
                                        <option value="1">Kích hoạt</option>
                                        <option value="0">Chưa kích hoạt</option>
                                    </select>
                                  </div>
                                </div>
              </div>   
                </div>
                <div class="modal-footer">
                    <div class="row text-center">
                        <button type="button" data-loading-text="Đang thêm mới dữ liệu" class="btn btn-primary" id ="updateRole">Thêm mới</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </form>   
      </div>
      
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
       <b> Danh mục </b> / Phân loại xã
        <!-- <a data-toggle="modal" data-target="#myModal" style="margin-left: 10px" class="btn btn-success btn-xs pull-right"  >
            <i class="glyphicon glyphicon-plus"></i> Tạo mới
        </a >  -->
        <!-- <a class="btn btn-success btn-xs pull-right" href="#" onclick="exportExcel('WARD')">
            <i class="glyphicon glyphicon-print"></i> Xuất excel
        </a> -->
    </div>
    </div>
          <section class="content">
      <div class="row">
        <!-- left column -->
        <div class="col-md-5">
          <!-- general form elements -->
          <div id="changeTree" class="right-side strech row box box-primary" style="overflow: auto ; max-height: 600px">
            <div class="box-header with-border">
              <h3 class="box-title">Phân loại xã</h3>
            </div>
            <!-- /.box-header -->
            
   <div id="using_json_2" class="jstree jstree-1 jstree-default">
        
    </div>

          </div>
          <!-- /.box -->
        </div>
        <!--/.col (left) -->
        <!-- right column -->
        <div class="col-md-7">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Thông tin</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form class="form-horizontal">
            <input type="hidden" id="txtWardID" name="">
            <div class="row" id="group_message" style="padding-left: 10%;padding-right: 10%"></div>
              <div class="box-body">
                <div class="form-group">
                  <label class="col-sm-3 control-label">Mã loại xã<font style="color: red">*</font></label>

                  <div class="col-sm-9">
                      <input type="text" name="" id="txtWardCode" class="form-control" placeholder="Mã loại xã" accept="charset" autofocus="true">
                        <!-- <img src="../../images/Image_valid.png" id="imgValidDepartment"><label id="lblValidDepartment" class="valid"></label> -->
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label">Tên loại xã<font style="color: red">*</font></label>
                  <div class="col-sm-9">
                      <input type="text" name="" id="txtWardName" class="form-control" placeholder="Tên loại xã" accept="charset">
                    <!-- <img src="../../images/Image_valid.png" id="imgValidCode"><label id="lblValidCode" class="valid"></label> -->
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label">Trực thuộc</label>
                  <div class="col-sm-9">
                    <select id="drWardParent" class="form-control">
                    </select>
                    <!-- <img src="../../images/Image_valid.png" id="imgValidName"><label id="lblValidName" class="valid"></label> -->
                  </div>
                </div>
                 <div class="form-group">
                  <label class="col-sm-3 control-label">Trạng thái</label>

                  <div class="col-sm-9">
                    <select id="drWardActive" class="form-control">
                        <option value="1">Kích hoạt</option>
                        <option value="0">Chưa kích hoạt</option>
                    </select>
                  </div>
                </div>
              </div>

                 <div class="modal-footer">
                        <div class="row text-center">
                            <button type="button" class="btn btn-primary" id="btnInsertWard">Thêm mới</button>
                            <button type="button" class="btn btn-primary" id="btnDeleteWard" disabled="true">Xóa</button>
                            <button type="button" class="btn btn-primary" id="btnResetWard" >Làm mới</button>
                        </div>
                    </div>
            </form>
          </div>
          <!-- /.box -->
          
          <!-- /.box -->
        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
    </section>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>

@endsection