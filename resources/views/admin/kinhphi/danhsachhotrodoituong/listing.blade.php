@extends('layouts.front')
@section('title', 'This is a blank page')
@section('description', 'This is a blank page that needs to be implemented')

@section('content')
<section class="content">
<!-- <script src="../../../mystyle/js/.js"></script> -->
<script type="text/javascript" src="{!! asset('mystyle/js/styleDanhSachHoTroKinhPhi.js') !!}"></script>
<script src="{!! asset('/js/myScript.js') !!}"></script>

<script type="text/javascript">

</script>

<div class="panel panel-default">
    <div class="panel-heading" id="ex-insert-danhsachhotrodoituong">
      <b> Quản lý kinh phí </b> / Danh sách hỗ trợ đối tượng
       <div id="">
          <!-- <a style="margin-left: 10px" id="btnInsertKinhPhiDoiTuong"  class=" btn btn-success btn-xs pull-right"  >
              <i class="glyphicon glyphicon-plus"></i> Tạo mới
          </a > 
          <a class="btn btn-success btn-xs pull-right"  href="#">
              <i class="glyphicon glyphicon-print"></i> Xuất excel
          </a> -->
        </div>
    </div>
</div>
            <div class="box box-primary form-horizontal" style="font-size: 12px;">
            <div class="box-header with-border">
              <h3 class="box-title"></h3>

              <div class="box-tools pull-right">
                <div class="has-feedback">
                  <input id="txtSearchDSHTDT" type="text" class="form-control input-sm" placeholder="Tìm kiếm danh sách">
                  <span class="glyphicon glyphicon-search form-control-feedback"></span>
                </div>
              </div>
              <!-- /.box-tools -->
            </div>
            
                <div class="box-body" >
                    <table id="" class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                          <tr class="success">
                            <th  class="text-center" style="width: 10px;vertical-align:middle">STT</th>
                             <th  class="text-center" style="width: 200px; vertical-align:middle">Tên danh sách</th>
                             <th  class="text-center" style="vertical-align:middle">Người duyệt</th>
                              <th  class="text-center" style="vertical-align:middle">Ngày duyệt</th>
                               <th  class="text-center" style="vertical-align:middle">Người gửi</th>
                                <th  class="text-center" style="vertical-align:middle">Ngày gửi</th>
                                <th  class="text-center" style="vertical-align:middle">Người thẩm định</th>
                                 <th  class="text-center" style="vertical-align:middle">Tệp đính kèm</th>
                                 <th  class="text-center" style="vertical-align:middle">Tệp đi kèm</th>
                                  <th  class="text-center" style="width: 120px; vertical-align:middle">Trạng thái</th>
                          </tr>
                        </thead>
                        <tbody id="dataDanhsachhotrodoituong">                     
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
                        <select name="viewTableDSHTDT" id="viewTableDSHTDT"  class="form-control input-sm pagination-show-row">
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