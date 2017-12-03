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
      module = 55;
      permission(function(){
        var html_view  = '<b> Danh mục </b> / Chế độ';
        
        if(check_Permission_Feature('1')){
            html_view += '<a data-toggle="modal" data-target="#myModal" style="margin-left: 10px" class="btn btn-success btn-xs pull-right" onclick="popupAddnewGroup()" ><i class="glyphicon glyphicon-plus"></i> Tạo mới </a > ';
        }
        // if(check_Permission_Feature('4')){
        //     html_view += '<a class="btn btn-success btn-xs pull-right" href="#" onclick="exportExcelGroup()"><i class="glyphicon glyphicon-print"></i> Xuất excel </a>';
        // }
        $('#addnew-export-group').html(html_view);
      });

      GET_INITIAL_NGHILC();
      loaddataGroup($('#drPagingGroup').val(), $('#txtSearchGroup').val());

      $('#drPagingGroup').change(function() {
        GET_INITIAL_NGHILC();
        loaddataGroup($(this).val(), $('#txtSearchGroup').val());
      });

      $('#txtGroupCode').focus();
      autocompleteSearch("txtSearchGroup", "GROUP");
    });

  	function popupAddnewGroup(){
      $('.modal-title').html('Thêm mới chế độ');
      $('#txtGroupCode').attr('readonly', false);
      group_id = "";
      $('#txtGroupCode').val("");
      $('#txtGroupName').val("");
      $('#drGroupActive').val(1);
      $("#modalAddNew").modal("show");
  	}
    function popupUpdateGroup(){
      $('.modal-title').html('Sửa chế độ');
      $('#txtGroupCode').attr('readonly', true);
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
          <h4 class="modal-title">Xóa chế độ</h4>
        </div>
        <div class="modal-footer">
          <div class="row text-center">
            <h2>Bạn có thực sự muốn xóa không?</h2>
            <input type="button" value="Xác nhận" id="btnConfirmDeleteGroup" class="btn btn-primary">
            <input type="button" value="Hủy" id="btnCancelDelete" class="btn btn-primary" data-dismiss="modal">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalAddNew" role="dialog">
    <div class="modal-dialog modal-md" style="width: auto;margin: 10px;">    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Thêm mới chế độ</h4>
        </div>
        <form class="form-horizontal" action="">  
          <input type="hidden" class="form-control" id="txtGroupID">          
          <div class="modal-body">
            <div class="row" id="group_message" style="padding-left: 10%;padding-right: 10%"></div>   
            <div class="box-body">
              <div class="form-group">
                <label class="col-sm-2 control-label">Mã chế độ<font style="color: red">*</font></label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="txtGroupCode" placeholder="Nhập mã chế độ" autofocus="true">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">Tên chế độ<font style="color: red">*</font></label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="txtGroupName" placeholder="Nhập tên chế độ">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">Trạng thái</label>
                <div class="col-sm-10">
                  <select id="drGroupActive" class="form-control">
                    <option value="1">Kích hoạt</option>
                    <option value="0">Chưa kích hoạt</option>
                  </select>
                </div>
              </div>
            </div>   
          </div>
          <div class="modal-footer">
            <div class="row text-center">
              <button type="button" data-loading-text="Đang thêm mới dữ liệu" class="btn btn-primary" id ="btnInsertGroup">Thêm mới</button>
              <button type="button" class="btn btn-primary" data-dismiss="modal" id="btnCloseGroup">Đóng</button>
            </div>
          </div>
        </form>   
      </div>      
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading" id="addnew-export-group">
      
    </div>
    </div>
      <div class="box">
                <div class="form-group " style="margin-top: 10px;margin-bottom: 0px">
                 <div class="box-header with-border">
              <h3 class="box-title"></h3>

              <div class="box-tools pull-right">
                <div class="has-feedback">
                  <input id="txtSearchGroup" type="text" class="form-control input-sm" placeholder="Tìm kiếm ">
                  <span class="glyphicon glyphicon-search form-control-feedback"></span>
                </div>
              </div>
              <!-- /.box-tools -->
            </div>
                     <!-- <div class="col-sm-6">
                      <label  class="col-sm-3 control-label">Hiển thị: </label>
                      <div class="col-sm-3">
                        <select id="drPagingGroup"  class="form-control input-sm">
                          <option value="5">5</option>
                          <option value="10">10</option>
                          <option value="15">15</option>
                          <option value="20">20</option>
                    </select>
                      </div>
                    </div> -->
                    <!--  <div class="col-sm-6">
                      <label  class="col-sm-4 control-label">Tìm kiếm</label>
                      <div class="col-sm-8">
                        <input type="text" id="txtSearchGroup" class="form-control" style=" width: 70%; height: 30px; margin-bottom: 10px;">
                      </div>
                    </div> -->
                </div>
        <!-- /.box-header -->
        <div class="box-body">
          <table class="table table-striped table-bordered table-hover dataTable no-footer">
            <thead>
              <tr class="success">
                <th class="text-center" style="vertical-align:middle">STT</th>
                <th class="text-center" style="vertical-align:middle">Mã chế độ</th>
                <th class="text-center" style="vertical-align:middle">Tên chế độ</th>
                <th class="text-center" style="vertical-align:middle">Trạng thái</th>
                <th class="text-center" style="vertical-align:middle">Ngày sửa</th>
                <th class="text-center" colspan="2" style="vertical-align:middle">Chức năng</th>
              </tr>
            </thead>
            <tbody id="dataGroup">
            
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
                        <select name="drPagingGroup" id="drPagingGroup"  class="form-control input-sm pagination-show-row">
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
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
</section>

@endsection