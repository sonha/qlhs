@extends('layouts.front')

@section('title', 'This is a blank page')
@section('description', 'This is a blank page that needs to be implemented')

@section('content')
<script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
<section class="content">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
<script src="{!! asset('/js/myScript.js') !!}"></script>
<script type="text/javascript" src="{!! asset('mystyle/js/jsDanhMuc.js') !!}"></script>

<script type="text/javascript">

    function message()
    {
      $(".delete_message").slideDown();
    }

    function hide()
    {
      $(".delete_message").slideUp();
    }

  $(document).ready(function () {

    $('#txtDepartCode').focus();

        $("#cancel").click(function(){
            hide();
          });
          
          $("#btnDeleteConfirm").click(function(){
            var v_id = $('#txtname').attr( "data" );
            //alert(v_id);            
            var v_jsonData = JSON.stringify({ DEPARTMENTID: v_id });
            console.log(v_jsonData);
            $.ajax({
                url: "/deleteDepartment/" + v_jsonData,
                type: "get",
                //data: v_jsonData,
                contentType: "application/json, charset=utf-8",
                success: function (data) {
                    alert(data);
                            window.location.reload();
                },
                error : function (){
                            alert('Có lỗi xảy ra trong quá trình xử lý');
                        }
            });
          });

        $('#txtcode').focus();
        $('#imgValidDepartment').attr("hidden", "hidden");
        $('#imgValidCode').attr("hidden", "hidden");
        $('#imgValidName').attr("hidden", "hidden");
        $('#btnUpdate').attr("hidden", "hidden");
        $('#btnDelete').attr("hidden", "hidden");

        $('#btnInsert').click(function() {
            var v_code = $('#txtcode').val();
            if (v_code.trim() == "") {
                $('#imgValidCode').removeAttr("hidden");
                $('#lblValidCode').html("Vui lòng nhập mã!");
                $('#txtcode').focus();
                return;
            }
            else{
                var specialChars = "!@#$%^&*()+=[]\\\';,./{}|\":<>?";
                var unicodeChars = "àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđÁÀẠẢÃÂẤẦẬẨẪĂẮẰẶẲẴÉÈẸẺẼÊẾỀỆỂỄÍÌỊỈĨÓÒỌỎÕÔỐỒỘỔỖƠỚỜỢỞỠÚÙỤỦŨƯỨỪỰỬỮÝỲỴỶỸĐ";

                for (var i = 0; i < v_code.length; i++) {
                    if (specialChars.indexOf(v_code.charAt(i)) != -1) {
                        $('#imgValidCode').removeAttr("hidden");
                        $('#lblValidCode').html("Mã nhập không được chứa ký tự đặc biệt!");
                        $('#txtcode').focus();
                        $('#txtcode').val("");
                        return;
                    }

                    if (unicodeChars.indexOf(v_code.charAt(i)) != -1) {
                        $('#imgValidCode').removeAttr("hidden");
                        $('#lblValidCode').html("Mã nhập không được chứa ký tự có dấu!");
                        $('#txtcode').focus();
                        $('#txtcode').val("");
                        return;
                    }
                }

                $('#imgValidCode').remove();
                $('#lblValidCode').remove();
                $('#txtcode').focusout();
            }
          
            var v_objData = getData();
            var v_jsonData = JSON.stringify(v_objData);
                alert(v_jsonData);
                         
                    $.ajax({
                        url : "/insertDepartment/" + v_jsonData,
                        type : "get",
                        //data : v_jsonData,
                        contentType: "application/json, charset=utf-8",
                        success : function (data){
                            var my_array = JSON.parse(data);

                                if (my_array['code'] == '1') {
                                    alert(my_array['message']);
                                    window.location.reload();
                                }
                                if (my_array['code'] == '0') {
                                    alert(my_array['message']);
                                    $('#txtcode').focus();
                                }
                        },
                        error : function (){
                            alert('Có lỗi xảy ra trong quá trình xử lý');
                        }
                    });
            });
        
        $('#btnSave').click(function () {
            
            var v_objData = getData();
            var v_jsonData = JSON.stringify(v_objData);
            //alert(v_jsonData);
            $.ajax({
                url: "/updateDepartment/" + v_jsonData,
                type: "get",
                //data: v_jsonData,
                contentType: "application/json, charset=utf-8",
                success: function (data) {
                    alert(data);
                            window.location.reload();
                },
                        error : function (){
                            alert('Có lỗi xảy ra trong quá trình xử lý');
                        }
            });
        });

        $("#btnDelete").click(function(){
            message();
        });
    });
 

    function getData(){
        //Tao doi tuong de gui len Controler
        var v_id = $('#txtname').attr( "data" );
        var v_code = $('#txtcode').val();
        var v_name = $('#txtname').val();
        var v_parent_id = $('#drpDepartment').val();
        var v_active = $('#drActive').val();

        if (v_name.trim() == "") {
            $('#imgValidName').removeAttr("hidden");
            $('#lblValidName').html("Vui lòng nhập tên!");
            $('#txtname').focus(); 
            return null;
        }
        else{
            var specialChars = "#";
            //"!@#$%^&*()+=[]\\\';,./{}|\":<>?";

            for (var i = 0; i < v_name.length; i++) {
                if (specialChars.indexOf(v_name.charAt(i)) != -1) {
                    $('#imgValidName').removeAttr("hidden");
                    $('#lblValidName').html("Tên không được chứa ký tự #!");
                    $('#txtname').focus();
                    //$('#txtname').val("");
                    return;
                }
            }

            $('#imgValidName').remove();
            $('#lblValidName').remove();
            $('#txtname').focusout();
        }

        if (v_parent_id == 0) {
            $('#imgValidDepartment').removeAttr("hidden");
            $('#lblValidDepartment').html("Vui lòng chọn!");
            return null;
        }
        else{
            $('#imgValidDepartment').remove();
            $('#lblValidDepartment').remove();
        }

        if (v_name == "") {return null;}
            
        return ({ DEPARTMENTID: v_id, CODE: v_code, NAME: v_name, DEPARTMENT_PARENT_ID: v_parent_id, ACTIVE: v_active });

    }
</script>
<script type="text/javascript">
$(function () {

    loadComboDepartment(null);
    $('#using_json_2').jstree({
        'core' : {
            'data' : 
            {
                'url' : function (node) {
                //console.log(node);
                return node.id === '#' ?
                '/danh-muc/deptdepartment' :
                '/danh-muc/childdepartment';
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
            var v_objJson = JSON.stringify({ DEPARTMENTID: currentIdSelected });

            //alert(v_objJson);
            $.ajax({
              type: "POST",
              url:'/danh-muc/getDepartmentbyID',
              data: v_objJson,
              dataType: 'json',
              contentType: 'application/json; charset=utf-8',
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
              },
              success : function (results){
                var level = 0;
                var item = '';
                $('#txtDepartID').val(results[0]['department_id']);
                $('#txtDepartCode').val(results[0]['department_code']);
                $('#txtDepartName').val(results[0]['department_name']);
                $('#drDepartActive').val(results[0]['department_active']);

                loadComboDepartment( function(data){
                    $('#drpDepartment').val(results[0]['department_parent_id']);
                });

                $('#btnInsertDepartment').html('Lưu');
                $("#btnDeleteDepartment").attr("disabled", false);
                $("#txtDepartCode").attr("disabled", true);
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
                  <label for="inputEmail3" class="col-sm-2 control-label">Mã nhóm</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="txtRoleCode" placeholder="Nhập mã nhóm">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Tên nhóm</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="txtRoleName" placeholder="Nhập tên nhóm">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Mô tả chi tiết</label>

                  <div class="col-sm-10">
                    <textarea class="textarea" id="txtRoleMota" name="txtRoleMota"  style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
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

<!-- Phòng ban -->
<div class="panel panel-default">
    <div class="panel-heading">
       <b> Danh mục </b> / Phòng ban
        <!-- <a data-toggle="modal" data-target="#myModal" style="margin-left: 10px" class="btn btn-success btn-xs pull-right"  >
            <i class="glyphicon glyphicon-plus"></i> Tạo mới
        </a >  -->
        <!-- <a class="btn btn-success btn-xs pull-right" href="#" onclick="exportExcel('DEPARTMENT')">
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
              <h3 class="box-title">Phòng ban</h3>
            </div>
            <!-- /.box-header -->
            
   <div id="using_json_2">
        
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
            <input type="hidden" name="" id="txtDepartID">
              <div class="box-body">
                <div class="form-group">
                  <label for="inputPassword3" class="col-sm-3 control-label">Mã phòng ban<font style="color: red">*</font></label>

                  <div class="col-sm-9">

                     <input type="text" name="" id="txtDepartCode" maxlength="100" placeholder="Mã phòng ban" class="form-control" accept="charset">
    <img src="../images/Image_valid.png" id="imgValidCode"><label id="lblValidCode" class="valid"></label>
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputPassword3" class="col-sm-3 control-label">Tên phòng ban<font style="color: red">*</font></label>

                  <div class="col-sm-9">

                    <input type="text" name="" class="form-control" id="txtDepartName" maxlength="100" placeholder="Tên phòng ban">
    <img src="../images/Image_valid.png" id="imgValidName"><label id="lblValidName" class="valid"></label>
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-3 control-label">Trực thuộc</label>

                  <div class="col-sm-9">
                    <select name='department' class="form-control" id='drpDepartment'>
                    </select>
        <img src="../images/Image_valid.png" id="imgValidDepartment"><label id="lblValidDepartment" class="valid"></label>
                  </div>
                </div>
                 <div class="form-group">
                  <label for="inputPassword3" class="col-sm-3 control-label">Trạng thái</label>

                  <div class="col-sm-9">
        <select id="drDepartActive" class="form-control">
            <option value="1">Kích hoạt</option>
            <option value="0">Chưa kích hoạt</option>
        </select>
                  </div>
                </div>
              </div>

                 <div class="modal-footer">
                        <div class="row text-center">
                            <button type="button" class="btn btn-primary" id="btnInsertDepartment">Thêm mới</button>
                            <button type="button" class="btn btn-primary" id="btnDeleteDepartment" disabled="true">Xóa</button>
                            <button type="button" class="btn btn-primary" id="btnResetDepartment" data-dismiss="modal">Làm mới</button>
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