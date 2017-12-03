@extends('layouts.front')

@section('title', 'This is a blank page')
@section('description', 'This is a blank page that needs to be implemented')

@section('content')
<script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="{!! asset('/js/myScript.js') !!}"></script>
<script type="text/javascript" src="{!! asset('mystyle/js/jsDanhMuc.js') !!}"></script>
<section class="content">
<script type="text/javascript">
 $(function () {
    $('#startDate').datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true
    });
    $('#endDate').datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true
    });
      // module = 57;
      permission(function(){
        var html_view  = '<b> Danh mục </b> / Quản lý số lượng người nấu ăn';
        
        if(check_Permission_Feature('1')){
            html_view += '<a data-toggle="modal" data-target="#myModal" style="margin-left: 10px" class="btn btn-success btn-xs pull-right" onclick="popupAddnewNgna()" ><i class="glyphicon glyphicon-plus"></i> Tạo mới </a >';
        }

        // if(check_Permission_Feature('4')){
        //     html_view += '<a class="btn btn-success btn-xs pull-right" href="#" onclick="exportExcelNation()"> <i class="glyphicon glyphicon-print"></i> Xuất excel </a>';
        // }
        $('#addnew-ngna').html(html_view);
      });

      GET_INITIAL_NGHILC();
      loaddataNguoinauan($('#drPagingNguoinauan').val(), $('#txtSearchNgna').val());

      $('#drPagingNguoinauan').change(function() {
        GET_INITIAL_NGHILC();
        loaddataNguoinauan($(this).val(), $('#txtSearchNgna').val());
      });

      loadComboxTruongHocSingle('sltTruongDt', function(){}, $('#school-per').val());

      autocompleteSearch("txtSearchNgna", "NGNA");
  });

    function popupAddnewNgna(){
      $('.modal-title').html('Thêm mới số lượng người nấu ăn');
      $('#sltTruongDt').removeAttr('disabled');
      $('#startDate').removeAttr('disabled');
      $('#btnInsertNgna').html('Thêm mới');
      $("#modalAddNew").modal("show");
    }

    function popupUpdateNgna(){
      $('.modal-title').html('Sửa số lượng người nấu ăn');
      $('#sltTruongDt').attr('disabled', 'disabled');
      $('#startDate').attr('disabled', 'disabled');
      $('#btnInsertNgna').html('Lưu');
      $("#modalAddNew").modal("show");
    }

    function popupConfirmDelete(){
      $("#modalDelete").modal("show");
    }

   function testPhanquyen(){

    $("#myModalPhanQuyen").modal("show");
   }
</script>
  <div class="modal fade" id="modalDelete" role="dialog">
    <div class="modal-dialog modal-md" style="width: auto;margin: 10px;">
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Xóa số người nấu ăn</h4>
        </div>
        <div class="modal-footer">
          <div class="row text-center">
            <h2>Bạn có thực sự muốn xóa không?</h2>
            <input type="button" value="Xác nhận" id="btnConfirmDeleteNgna" class="btn btn-primary">
            <input type="button" value="Hủy" id="btnCancelDelete" class="btn btn-primary" data-dismiss="modal">
          </div>
        </div>
      </div>
    </div>
  </div>

<div class="modal fade" id="modalAddNew" role="dialog">
    <div class="modal-dialog modal-md" style="width: 80%;margin: 10px auto;">
    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Thêm mới số người nấu ăn</h4>
        </div>
        <form class="form-horizontal" action="">  
        <input type="hidden" class="form-control" id="txtIdRoleGroup">          
                <div class="modal-body">
                    <div class="row" id="group_message" style="padding-left: 10%;padding-right: 10%"></div>   
                    <div class="box-body">
                <div class="form-group">
                    <div class="col-sm-6">
                      <label  class="col-sm-4 control-label">Chọn trường <font style="color: red">*</font></label>
                      <div class="col-sm-8">
                        <select name='sltTruongDt' class="form-control" id='sltTruongDt'>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <label  class="col-sm-4 control-label">Số người nấu ăn <font style="color: red">*</font></label>
                      <div class="col-sm-8">
                        <input type="text" name="txtAmount" id="txtAmount" class="form-control" placeholder="Nhập số người">                       
                      </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-6">
                      <label  class="col-sm-4 control-label">Ngày bắt đầu hiệu lực <font style="color: red">*</font></label>
                      <div class="col-sm-8">
                        <input type="text" id="startDate" name="startDate" class="form-control" placeholder="ngày-tháng-năm">
                      </div>
                    </div>
                    <!-- <div class="col-sm-6">
                      <label  class="col-sm-4 control-label">Ngày hết hiệu lực <font style="color: red">*</font></label>
                      <div class="col-sm-8">
                        <input type="text" id="endDate" name="endDate" class="form-control" placeholder="ngày-tháng-năm">
                      </div>
                    </div> -->
                </div>
              </div>   
                </div>
                <div class="modal-footer">
                    <div class="row text-center">
                        <button type="button" data-loading-text="Đang thêm mới dữ liệu" class="btn btn-primary" id ="btnInsertNgna">Thêm mới</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" id="btncloseNGNA">Đóng</button>
                    </div>
                </div>
            </form>   
      </div>
      
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading" id="addnew-ngna">
       
        
        
    </div>
    </div>
          <div class="box">
                <div class="form-group " style="margin-top: 10px;margin-bottom: 0px;">
                <div class="box-header with-border">
              <h3 class="box-title"></h3>

              <div class="box-tools pull-right">
                <div class="has-feedback">
                  <input id="txtSearchNgna" type="text" class="form-control input-sm" placeholder="Tìm kiếm ">
                  <span class="glyphicon glyphicon-search form-control-feedback"></span>
                </div>
              </div>
              <!-- /.box-tools -->
            </div>
                     
                </div>

            <!-- /.box-header -->
            <div class="box-body">
              <table class="table table-striped table-bordered table-hover dataTable no-footer">
                <thead>
                <tr>
                  <th class="text-center" style="vertical-align:middle">STT</th>
                  <th class="text-center" style="vertical-align:middle; width: 20%;">Tên trường</th>
                  <th class="text-center" style="vertical-align:middle">Số người nấu ăn</th>
                  <th class="text-center" style="vertical-align:middle">Ngày bắt đầu hiệu lực</th>
                  <th class="text-center" style="vertical-align:middle">Ngày kết thúc hiệu lực</th>
                  <th class="text-center" style="vertical-align:middle">Ngày sửa</th>
                  <th class="text-center" style="vertical-align:middle; width: 15%;">Người sửa</th>
                  <th class="text-center" style="vertical-align:middle">Chức năng</th>
                </tr>
                </thead>
                <tbody id="dataTableNGNA">
                
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
                        <select name="drPagingNguoinauan" id="drPagingNguoinauan"  class="form-control input-sm pagination-show-row">
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
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>

@endsection