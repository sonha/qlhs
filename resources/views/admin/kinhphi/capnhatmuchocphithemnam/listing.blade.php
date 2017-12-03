@extends('layouts.front')

@section('title', 'This is a blank page')
@section('description', 'This is a blank page that needs to be implemented')

@section('content')
<script src="{!! asset('/js/myScript.js') !!}"></script>
<script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="{!! asset('mystyle/js/styleKinhPhi2.js') !!}"></script>
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
       <b> Quản lý kinh phí </b> / Cập nhật mức thu phí theo năm học
       <a style="margin-left: 10px" id="btnInsertKinhPhiDoiTuong"  class=" btn btn-success btn-xs pull-right"  >
            <i class="glyphicon glyphicon-plus"></i> Tạo mới
        </a > 
        <!-- <a class="btn btn-success btn-xs pull-right"  href="#">
            <i class="glyphicon glyphicon-print"></i> Xuất excel
        </a> -->
    </div>
</div>
    <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Thông tin chung</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form class="form-horizontal">
              <div class="box-body">
                <div class="form-group">
                <input type="hidden" name="txtIdKinhPhi2" id="txtIdKinhPhi2" disabled="disabled">
                    <!-- <div class="col-sm-6">
                        <label  class="col-sm-4 control-label">Mã <font style="color: red">*</font></label>
                        <div class="col-sm-6">
                          <input type="text" name="txtCodeKinhPhi2" id="txtCodeKinhPhi2" class="form-control" placeholder="KPNH- ..." readonly="readonly" disabled="disabled" title="Mã tự sinh">
                        </div>
                      </div> -->
                    <div class="col-sm-4">
                      <label  class="col-sm-4 control-label">Chọn trường <font style="color: red">*</font></label>
                      <div class="col-sm-8">
                        <select name='sltTruong' class="form-control" id='sltTruong'>
                         </select>
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <label  class="col-sm-4 control-label">Chọn năm học <font style="color: red">*</font></label>
                      <div class="col-sm-8">
                        <select name='sltSchool1' class="form-control" id='sltSchool1'>
                         </select>
                      </div>
                    </div>
                     <div class="col-sm-4">
                      <label  class="col-sm-4 control-label">Mức thu <font style="color: red">*</font></label>
                      <div class="col-sm-4">
                        <input type="text" name="txtMoney2" id="txtMoney2" class="form-control" placeholder="Nhập số tiền ...">
                      </div>
                      <label  class="col-sm-4 control-label" style="text-align: left">/ tháng</label>
                    </div>
                </div>
                <div class="form-group">
                    
              </div>
              <!-- <div class="form-group">
                     <div class="col-sm-6">
                      <label  class="col-sm-4 control-label">Ngày hiệu lực <font style="color: red">*</font></label>
                      <div class="col-sm-8">
                        <input type="text" id="datepicker3" name="txtDateBegin3" class="form-control" placeholder="ngày-tháng-năm">
                      </div>
                    </div>
                     <div class="col-sm-6">
                      <label  class="col-sm-4 control-label">Ngày hết hiệu lực <font style="color: red">*</font></label>
                      <div class="col-sm-8">
                        <input type="text" id="datepicker4" name="txtDateBegin4" class="form-control" placeholder="ngày-tháng-năm">
                      </div>
                    </div>
                </div> -->
                 <div class="modal-footer">
                        <div class="row text-center">
                            <button type="button" class="btn btn-primary" id ="btnSaveKinhPhiNamHoc"><i class="glyphicon glyphicon-plus-sign"></i> Thêm bản ghi</button>
                            <!-- <button type="reset" class="btn btn-primary" id ="btnResetKinhPhiNamHoc"><i class="glyphicon glyphicon-refresh"></i> Làm lại</button> -->
                            <button type="button" class="btn btn-primary" id ="btnCancelKinhPhiNamHoc"><i class="glyphicon glyphicon-remove-sign"></i> Hủy</button>
                        </div>
                    </div>

            </form>
          </div>

            <div class="box box-primary form-horizontal" style="font-size: 12px;">
            <div class="form-group " style="margin-top: 10px;margin-bottom: 0px">
              <div class="box-header with-border">
              <h3 class="box-title"></h3>

              <div class="box-tools pull-right">
                <div class="has-feedback">
                  <input id="txtSearchNH" type="text" class="form-control input-sm" placeholder="Tìm kiếm ">
                  <span class="glyphicon glyphicon-search form-control-feedback"></span>
                </div>
              </div>
              <!-- /.box-tools -->
            </div>
                    <!--  <div class="col-sm-6">
                      <label  class="col-sm-3 control-label">Hiển thị: </label>
                      <div class="col-sm-3">
                        <select name="viewTableNH" id="viewTableNH"  class="form-control input-sm">
                          <option value="5">5</option>
                          <option value="10">10</option>
                          <option value="15">15</option>
                          <option value="20">20</option>
                    </select>
                      </div>
                    </div> -->
                     <!-- <div class="col-sm-6">
                      <label  class="col-sm-4 control-label">Tìm kiếm</label>
                      <div class="col-sm-8">
                        <input type="text" id="txtSearchNH" name="txtSearchNH" class="form-control" >
                      </div>
                    </div> -->
                </div>

                <div class="box-body" style="overflow: auto ; max-width: 100%">
                    <table id="" class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                          <tr class="success">
                            <th  class="text-center" style="vertical-align:middle">STT</th>
                             <!-- <th  class="text-center" style="vertical-align:middle">Mã</th> -->
                             <th  class="text-center" style="vertical-align:middle">Trường học</th>
                              <th  class="text-center" style="vertical-align:middle">Năm học</th>
                               <th  class="text-center" style="vertical-align:middle">Số tiền / tháng</th>
                                <!-- <th  class="text-center" style="vertical-align:middle">Ngày hiệu lực</th>
                                <th  class="text-center" style="vertical-align:middle">Ngày hết hiệu lực</th> -->
                                 <th  class="text-center" style="vertical-align:middle">Ngày cập nhật</th>
                                 <th  class="text-center" style="vertical-align:middle">Người cập nhật</th>
                                  <th  class="text-center" style="vertical-align:middle">Chức năng</th>
                          </tr>
                        </thead>
                        <tbody id="dataKinhPhiNamHoc">                     
                        </tbody>
                    </table>
                     <div class="box-footer clearfix">
    <div class="row">
        <div class="col-md-2">
            <label class="text-right col-md-9 control-label">Tổng </label>
            <label class="col-md-3 control-label g_countRowsPaging">0</label>
        </div>
        <div class="col-md-3">
            <label class="col-md-6 control-label text-right">Trang </label>
            <div class="col-md-6">
                <select class="form-control input-sm g_selectPaging">
                    <option value="0">0 / 20 </option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
                      <label  class="col-md-6 control-label">Hiển thị: </label>
                      <div class="col-md-6">
                        <select name="viewTableNH" id="viewTableNH"  class="form-control input-sm pagination-show-row">
                          <option value="10">10</option>
                          <option value="15">15</option>
                          <option value="20">20</option>
                    </select>
                      </div>
                    </div>
        <div class="col-md-4">
        <label  class="col-md-2 control-label"></label>
        <div class="col-md-10">
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
            </div>
          
</div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>

@endsection