@extends('layouts.front')

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
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-md" style="width: auto;margin: 10px;">
    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Thêm mới khối</h4>
        </div>
        <form class="form-horizontal" action="">  
        <input type="hidden" class="form-control" id="txtIdRoleGroup">          
                <div class="modal-body">
                    <div class="row" id="group_message" style="padding-left: 10%;padding-right: 10%"></div>   
                    <div class="box-body">
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Mã khối</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="txtRoleCode" placeholder="Nhập mã nhóm">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Tên khối</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="txtRoleName" placeholder="Nhập tên nhóm">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Trạng thái</label>

                  <div class="col-sm-10">
                   
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
       <b> Quản lý hồ sơ </b> /  Lập danh sách đề nghị cấp kinh phí
    </div>
</div>
    <div class="box">
            <!-- /.box-header -->
        <section class="content">

          <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
            <a href="mien-giam-hoc-phi">
              <div class="info-box" >
                <span class="info-box-icon">
                <img src="../../../../images/Icon/add_Dispatch_Icon.png">
               <!--  <i class="glyphicon glyphicon-leaf"></i> -->
                </span>

                <div class="info-box-content" style="margin-left: 10px">
                  <span class="info-text" style="font-weight: bold;">Lập danh sách</span>
                  <span class="info-number">Chính sách đối tượng miễn, giảm học phí</span>
                </div>
                <!-- /.info-box-content -->
              </div>
              </a>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
            <a href="chi-phi-hoc-tap">
              <div class="info-box" >
                <span class="info-box-icon">
                <img src="../../../../images/Icon/add_Dispatch_Icon.png">
               <!--  <i class="glyphicon glyphicon-leaf"></i> -->
                </span>

                <div class="info-box-content" style="margin-left: 10px">
                  <span class="info-text" style="font-weight: bold;">Lập danh sách</span ><span class="info-number">Chính sách hỗ trợ chi phí học tập</span>
                </div>
                <!-- /.info-box-content -->
              </div>
             </a>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
            <a href="ho-tro-an-trua-tre-em">
              <div class="info-box" >
                <span class="info-box-icon">
                <img src="../../../../images/Icon/add_Dispatch_Icon.png">
               <!--  <i class="glyphicon glyphicon-leaf"></i> -->
                </span>

                <div class="info-box-content" style="margin-left: 10px">
                  <span class="info-text" style="font-weight: bold;">Lập danh sách</span><span class="info-number">Chính sách hỗ trợ ăn trưa cho trẻ em mẫu giáo</span>
                </div>
                <!-- /.info-box-content -->
              </div>
             </a>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
            <a href="hoc-sinh-ban-tru">
              <div class="info-box" >
                <span class="info-box-icon">
                <img src="../../../../images/Icon/add_Dispatch_Icon.png">
               <!--  <i class="glyphicon glyphicon-leaf"></i> -->
                </span>

                <div class="info-box-content" style="margin-left: 10px">
                  <span class="info-text" style="font-weight: bold;">Lập danh sách</span><span class="info-number">Chính sách hỗ trợ học sinh bán trú</span>
                </div>
                <!-- /.info-box-content -->
              </div>
              </a>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
            <a href="nguoi-nau-an">
              <div class="info-box">
                <span class="info-box-icon">
                <img src="../../../../images/Icon/add_Dispatch_Icon.png">
               <!--  <i class="glyphicon glyphicon-leaf"></i> -->
                </span>

                <div class="info-box-content" style="margin-left: 10px">
                  <span class="info-text" style="font-weight: bold;">Lập danh sách</span>
                  <span class="info-number">Chính sách hỗ trợ người nấu ăn</span>
                </div>
                <!-- /.info-box-content -->
              </div>
              </a>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
            <a href="hoc-sinh-khuyet-tat">
              <div class="info-box" >
                <span class="info-box-icon">
                <img src="../../../../images/Icon/add_Dispatch_Icon.png">
               <!--  <i class="glyphicon glyphicon-leaf"></i> -->
                </span>

                <div class="info-box-content" style="margin-left: 10px">
                  <span class="info-text" style="font-weight: bold;">Lập danh sách</span ><span class="info-number">Chính sách hỗ trợ học sinh khuyết tật</span>
                </div>
                <!-- /.info-box-content -->
              </div>
              </a>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
            <a href="hoc-sinh-dan-toc-thieu-so">
              <div class="info-box" >
                <span class="info-box-icon">
                <img src="../../../../images/Icon/add_Dispatch_Icon.png">
               <!--  <i class="glyphicon glyphicon-leaf"></i> -->
                </span>

                <div class="info-box-content" style="margin-left: 10px">
                  <span class="info-text" style="font-weight: bold;">Lập danh sách</span><span class="info-number">Chính sách hỗ trợ học sinh dân tộc thiểu số tại huyện Mù Căng Chải và huyện Trạm Tấu</span>
                </div>
                <!-- /.info-box-content -->
              </div>
              </a>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
            <a href="chinh-sach-uu-dai">
              <div class="info-box">
                <span class="info-box-icon">
                <img src="../../../../images/Icon/add_Dispatch_Icon.png">
               <!--  <i class="glyphicon glyphicon-leaf"></i> -->
                </span>

                <div class="info-box-content" style="margin-left: 10px">
                  <span class="info-text" style="font-weight: bold;">Lập danh sách</span><span class="info-number">Chế độ chính sách ưu đãi cho trẻ em mẫu giáo, học sinh</span>
                </div>
                <!-- /.info-box-content -->
              </div>
              </a>
            </div>
          </div>
        </section>
            
    </div>
          <!-- /.box -->
</div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>

@endsection