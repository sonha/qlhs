@extends('layouts.front')

@section('title', 'This is a blank page')
@section('description', 'This is a blank page that needs to be implemented')

@section('content')

<script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="{!! asset('/mystyle/js/jsHoso.js') !!}"></script>
<script src="{!! asset('/js/myScript.js') !!}"></script>
<script type="text/javascript" src="{!! asset('mystyle/js/jsDanhMuc.js') !!}"></script>
<section class="content">
<script type="text/javascript">
$(function () {
  $("#txtFile_attack").filestyle({
    buttonText : 'Đính kèm',
    buttonName : 'btn-info'
  });
  
  $("#txtFile_attack_approved").filestyle({
    buttonText : 'Đính kèm',
    buttonName : 'btn-info'
  });
  
  GET_INITIAL_NGHILC();
  loadInboxPheDuyet($('#viewpheduyet').val());
  $('#viewpheduyet').change(function() {
    GET_INITIAL_NGHILC();
    loadInboxPheDuyet($(this).val());
  });

  autocompleteSearchDanhsachs("txtSearchDuyetDanhSach", 2);
});
  function test(){
   $("#myModal").modal("show");
   }
   // function testPhanquyen(){

   //  $("#myModalPhanQuyen").modal("show");
   // }
</script>
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-md" style="width: 60%;margin: 30px auto;">
    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Thông tin chung</h4>
        </div>
        <div class="box-body no-padding">
              <div class="mailbox-read-info">
                <h4><strong style="color: blue;font-style: italic;" id="title-vanban"></strong></h4>
                <h5>
                  <span class="mailbox-read-time pull-right"></span></h5>
              </div>
              <!-- /.mailbox-read-info -->
              <div class="mailbox-controls with-border pull-right" >
              <div class="btn-group">
                  <label><i class="fa fa-paperclip"></i>  Văn bản đính kèm:  </label>
                 
                </div>
            <!-- <label style="font-weight: 500">&nbsp;&nbsp;<i class="fa fa-file-excel-o"></i>  <a style="font-style: italic;"> DS_HOTRO.xlsx</a> ( <a href="#"><i class="fa fa-download"></i> Tải về</a>)</label> -->
                  <label id="no-file-load"></label>
              </div>
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message" style="margin-top: 40px;">
               <form class="form-horizontal" action="">  
        <input type="hidden" class="form-control" id="txtPheDuyet_ID">          
                <div class="modal-body">
                    <div class="row" id="group_message" style="padding-left: 10%;padding-right: 10%"></div>   
                    <div class="box-body">
                    <div class="form-group" style="margin: 0;">
                      <div class="col-sm-12" style="padding-left: 0">
                         <label style="padding-top: 0px;" class="col-sm-4 control-label">Số văn bản:</label>

                          <div class="col-sm-8">
                            <p id="no-vanban"></p>
                          </div>
                      </div>  
                    </div>
                    <div class="form-group" style="margin: 0;">
                      <div class="col-sm-12" style="padding-left: 0">
                         <label style="padding-top: 0px;" class="col-sm-4 control-label">Người gửi:</label>

                          <div class="col-sm-8">
                            <p id="address-send"></p>
                          </div>
                      </div>  
                    </div>
                    <!-- <div class="form-group" style="margin: 0;"> 
                      <div class="col-sm-12" style="padding-left: 0">
                         <label style="padding-top: 0px;" class="col-sm-4 control-label">Trích yếu:</label>
                          <div class="col-sm-8">
                            <p id="content-short"> </p>
                          </div>
                      </div>  
                    </div> -->
                    <div class="form-group" style="margin: 0;">
                      <div class="col-sm-12" style="padding-left: 0">
                         <label style="padding-top: 0px;" class="col-sm-4 control-label">Độ khẩn cấp:</label>
                          <div class="col-sm-8">
                            <p id="status"></p>
                          </div>
                      </div>  
                    </div>
                    <div class="form-group" style="margin: 0;">
                      <div class="col-sm-12" style="padding-left: 0">
                         <label style="padding-top: 0px;" class="col-sm-4 control-label">Văn bản đi kèm:</label>
                          <div class="col-sm-8">
                            <p id="file-attach"> </p>
                          </div>
                      </div>  
                    </div>
                    <div class="form-group" style="margin: 0;">
                      <div class="col-sm-12" style="padding-left: 0">
                         <label style="padding-top: 0px;" class="col-sm-4 control-label">Người thẩm định:</label>
                          <div class="col-sm-8">
                            <p id="user-thamdinh"> </p>
                          </div>
                      </div>  
                    </div>
                    <div class="form-group" style="margin: 0;">
                      <div class="col-sm-12" style="padding-left: 0">
                          <label style="padding-top: 0px;" class="col-sm-4 control-label">Ngày thẩm định:</label>
                          <div class="col-sm-8">
                            <p id="date-thamdinh"> </p>
                          </div>
                      </div>  
                    </div>
                    <div class="form-group" style="margin: 0;">
                      <div class="col-sm-12" style="padding-left: 0">
                          <label style="padding-top: 0px;" class="col-sm-4 control-label">Trạng thái:</label>
                          <div class="col-sm-8">
                            <p id="thamdinh-set"> </p>
                          </div>
                      </div>  
                    </div>
                    <div class="form-group" style="margin: 0;">
                      <div class="col-sm-12" style="padding-left: 0">
                          <label style="padding-top: 0px;" class="col-sm-4 control-label">Văn bản quyết định:</label>
                          <div class="col-sm-8">
                            <p id="file-attach-approved"> </p>
                          </div>
                      </div>  
                    </div>
                    <div class="form-group">
                      <div class="col-sm-8" style="padding-left: 0">
                        <label  class="col-sm-6">Ý kiến </label>
                      </div>
                      <div class="col-sm-4" style="padding-left: 0">
                        <label  class="col-sm-6">Đính kèm </label>
                      </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-8">
                          <input type="text" class="form-control" id="txtNoteSend" placeholder="Nhập ý kiến">
                        </div>
                        <div class="col-sm-4">
                          <input type="file" class="form-control" id="txtFile_attack">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-8">
                          <input type="text" class="form-control" id="txtNoteApproved" placeholder="Nhập quyết định duyệt cấp kinh phí">
                        </div>
                        <div class="col-sm-4">
                          <input type="file" class="form-control" id="txtFile_attack_approved">
                        </div>
                    </div>
                 
              </div>   
                </div>
                <div class="modal-footer">
                    <div class="row text-center">
                        <button type="button" data-loading-text="Đang thêm mới dữ liệu" class="btn btn-primary" id ="btnPheDuyet"><i class="glyphicon glyphicon-ok"></i> Thẩm định</button>
                         <button type="button" data-loading-text="Đang thêm mới dữ liệu" class="btn btn-primary" id ="btnRevertPheDuyet"><i class="glyphicon glyphicon-ok"></i> Chuyển lại</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </form>

              </div>
                
            </div>
      </div>
      
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
       <b> Thẩm định </b> / Danh sách hồ sơ
    </div>
    </div>
          <div class="box ">
           <section class="content">
      <div class="row">
        
        <!-- /.col -->
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"></h3>

              <div class="box-tools pull-right">
                <div class="has-feedback">
                  <input type="text" class="form-control input-sm" id="txtSearchDuyetDanhSach" placeholder="Tìm kiếm văn bản">
                  <span class="glyphicon glyphicon-search form-control-feedback"></span>
                </div>
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
              
              <div class="table-responsive mailbox-messages">
                <table  id="example2" class="table table-hover table-striped dataTable">
                  <tbody id="inbox-thamdinh-pheduyet">
                  
                  </tbody>
                </table>
                <!-- /.table -->
              </div>
              <!-- /.mail-box-messages -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer no-padding">
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
                                      <select name="viewpheduyet" id="viewpheduyet"  class="form-control input-sm pagination-show-row">
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
          <!-- /. box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
            
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>

@endsection