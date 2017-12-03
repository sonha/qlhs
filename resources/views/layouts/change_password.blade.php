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

  loaduserinfo($('#txtIdUser').val());
});
  function test(){
    $('#txtPassOld').val('');
    $('#txtPassNew').val('');
    $('#txtRePassNew').val('');
   $("#myModal").modal("show");
   }
   function testPhanquyen(){

    $("#myModalPhanQuyen").modal("show");
   }
</script>
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-md" style="width: auto;margin: 10px;">
    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Thay đổi mật khẩu người dùng <font style="font-weight: 700">{{ Auth::user()->username }}</font></h4>
        </div>
        <form class="form-horizontal" action="">  
        <input type="hidden" class="form-control" id="txtIdRoleGroup">          
                <div class="modal-body">
                    <div class="row" id="changepass_message" style="padding-left: 10%;padding-right: 10%"></div>   
                    <div class="box-body">
                <div class="form-group">
                  <div class="col-sm-10">
                    <label  class="col-sm-4 control-label">Mật khẩu hiện tại </label>

                    <div class="col-sm-6">
                      <input type="password" class="form-control" id="txtPassOld" placeholder="Nhập mật khẩu hiện tại">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-10">
                    <label  class="col-sm-4 control-label">Mật khẩu mới</label>

                  <div class="col-sm-6">
                    <input type="password" class="form-control" id="txtPassNew" placeholder="Nhập mật khẩu mới">
                  </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-10">
                    <label  class="col-sm-4 control-label">Xác nhận lại mật khẩu mới</label>

                    <div class="col-sm-6">
                      <input type="password" class="form-control" id="txtRePassNew" placeholder="Xác nhận mật khẩu mới">
                    </div>
                  </div>
                </div>
              </div>   
                </div>
                <div class="modal-footer">
                    <div class="row text-center">
                        <button type="button" data-loading-text="Đang thêm mới dữ liệu" class="btn btn-primary" id ="btnChangePass">Thay đổi</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </form>   
      </div>
      
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
       <b> Thông tin người dùng </b> / Thông tin
    </div>
    </div>
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Tài khoản người dùng <font style="color: red;font-weight: 700">{{ Auth::user()->username }}</font></h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form class="form-horizontal" action="">  
        <input type="hidden" class="form-control" id="txtIdUser" value="{{ Auth::user()->id }}">          
                <div class="modal-body">
                    <div class="row" id="group_message" style="padding-left: 10%;padding-right: 10%"></div>   
                    <div class="box-body">
                
                <div class="form-group">
                  <div class="col-sm-6">
                    <label class="col-sm-4 control-label">Họ</label>

                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="txtLastName" placeholder="Nhập họ người dùng">
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <label  class="col-sm-4 control-label">Tên</label>

                  <div class="col-sm-8">
                    <input type="text" class="form-control" id="txtFirstName" placeholder="Nhập tên người dùng">
                  </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-6">
                    <label  class="col-sm-4 control-label">Email</label>

                    <div class="col-sm-8">
                      <input type="email" class="form-control" id="txtEmail" placeholder="Nhập địa chỉ email">
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <label class="col-sm-4 control-label">Trạng thái</label>

                  <div class="col-sm-8">
                        <label id="lbStatus"  class="control-label"></label>
                  </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-6">
                    <label  class="col-sm-4 control-label">Mật khẩu</label>

                  <div class="col-sm-5">
                     <button type="button" data-loading-text="Đang thêm mới dữ liệu" class="btn btn-primary" onclick="test()" id ="btnChange"><i class="glyphicon glyphicon-lock"></i> Đổi mật khẩu</button>
                  </div>
                  </div>
                </div>
              </div>   
                </div>
                <div class="modal-footer">
                    <div class="row text-center">
                        <button type="button"  data-loading-text="Đang thêm mới dữ liệu" class="btn btn-primary" id ="updateProfile">Cập nhật thông tin</button>
                    </div>
                </div>
            </form>   
          </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>

@endsection