@extends('layouts.front')

@section('title', 'This is a blank page')
@section('description', 'This is a blank page that needs to be implemented')

@section('content')
<script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="{!! asset('/js/myScript.js') !!}"></script>
<script type="text/javascript" src="{!! asset('mystyle/js/styleRole.js') !!}"></script>
<section class="content">
<script type="text/javascript">
$(function () {
    loadTableRole();
});

	// function test(){
	//  $("#myModal").modal("show");
	//  }
 //   function testPhanquyen(){

 //    $("#myModalPhanQuyen").modal("show");
 //   }
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
<div class="panel panel-default">
    <div class="panel-heading">
       <b> Danh mục </b> / Danh sách nhóm người dùng
        <a data-toggle="modal" data-target="#myModal" style="margin-left: 10px" class="btn btn-success btn-xs pull-right" id="btnInsertGroups" >
            <i class="glyphicon glyphicon-plus"></i> Tạo mới
        </a > 
        <a class="btn btn-success btn-xs pull-right"  href="#">
            <i class="glyphicon glyphicon-print"></i> Xuất excel
        </a>
    </div>
    </div>
          <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example" class="table table-striped table-bordered table-hover dataTable no-footer">
                <thead>
                <tr>
                  <th class="text-center" style="vertical-align:middle">STT</th>
                  <th class="text-center" style="vertical-align:middle">Mã nhóm</th>
                  <th class="text-center" style="vertical-align:middle">Tên nhóm</th>
                  <th class="text-center" style="vertical-align:middle">Mô tả</th>
                  <th class="text-center" style="vertical-align:middle">Ngày sửa</th>
                  <th class="text-center" style="vertical-align:middle">Người sửa</th>
                  <th class="text-center" style="vertical-align:middle">Chức năng</th>
                </tr>
                </thead>
                <tbody id="dataRoles">
                <!--  @foreach($list as $key=>$value)
                                <tr>
                                    <td class="text-center">{{$loop->index+1}}</td>
                                    <td id="code"><a href="#">{{$value->name}}</a></td>
                                    <td id="name">{{$value->display_name}}</td>
                                    <td id="description">{{$value->description}}</td>
                                    <td class="text-center">{{$value->updated_at}}</td>
                                    <td class="action">
                                <a id='btnUpdateRole' class="btn btn-info btn-xs"
                                data="{{$value->id}}"  href="#">
                                    <i class="glyphicon glyphicon-pencil"></i>Sửa
                                </a>
                              <a class="btn-delete btn btn-danger btn-xs" data="1" onclick="test();"
                                   href="#"><i class="glyphicon glyphicon-remove"></i>Xóa&nbsp;</a>
                                    <a id='btnRole' class="btn btn-info btn-xs" data="{{$value->id}}" 
                                   href="#"><i class="glyphicon glyphicon-pushpin"></i>Phân quyền&nbsp;</a>
                            </td>
                                </tr>
                                @endforeach -->
                </tbody>
              </table>
              <div class="box-footer clearfix">
    <div class="row">
        <div class="col-md-3">
            <label class="text-right col-md-6 control-label">Tổng </label>
            <label class="col-md-6 control-label g_countRowsPaging">0</label>
        </div>
        <div class="col-md-4">
            <label class="col-md-3 control-label text-right">Trang </label>
            <div class="col-md-4">
                <select class="form-control g_selectPaging">
                    <option value="0">0 / 20 </option>
                </select>
            </div>
        </div>
        <div class="col-md-5">
            <ul class="pagination pagination-sm no-margin pull-right g_clickedPaging">
                <li><a>&laquo;</a></li>
                <li><a>0</a></li>
                <li><a>&raquo;</a></li>
            </ul>
        </div>
    </div>
</div>
            </div>
            
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>

@endsection