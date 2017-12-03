@extends('layouts.front')

@section('title', 'This is a blank page')
@section('description', 'This is a blank page that needs to be implemented')

@section('content')
<link rel="stylesheet" href="{!! asset('css/select2.min.css') !!}">

<script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="{!! asset('/js/myScript.js') !!}"></script>
<script src="{!! asset('/mystyle/js/jsHoso.js') !!}"></script>
<script type="text/javascript" src="{!! asset('mystyle/js/styleLapDanhSach.js') !!}"></script>
<section class="content">
<script type="text/javascript">
$(function () {

  $("#exampleInputFile").filestyle({
    buttonText : 'Đính kèm',
    buttonName : 'btn-info'
  });
  $('select#view-tonghopdanhsach').change(function() {
    GET_INITIAL_NGHILC();
    loaddataTotal($(this).val());
  });
  loadComboxTruongHoc();
  loadComboxNamHoc();
  loaddataTotal($('#view-tonghopdanhsach').val());
});
   function myModalLapDanhSach(){
    // $('#sltDoiTuong').multiselect({
    //     nonSelectedText:'-- Chọn đối tượng --'
    // });
    $("#myModalLapDanhSach").modal("show");
   }

  function openPopupSendPheDuyet(){
    $("#drNguoinhan").val('').select2({
      placeholder: "-- Chọn người nhận --",
      allowClear: true
    });
    $("#drCC").val('').select2({
      placeholder: "-- Chọn người nhận --",
      allowClear: true
    });
    $("#myModalSendPheduyet").modal("show");
  }
</script>

<div class="modal fade" id="myModalRevertPheduyet" role="dialog">
    <div class="modal-dialog modal-md" style="width: 60%;margin: 30px auto;">
    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title-revert">Thông tin</h4>
        </div>
        <div class="box-body no-padding">              
              <div class="mailbox-read-message">
               <form class="form-horizontal" action="">
                <div class="modal-body">
                  <div class="row" id="msg_revert_phe" style="padding-left: 10%;padding-right: 10%"></div>   
                  <div class="box-body">
                    
                    <div class="form-group" style="margin: 0;">
                      <div class="col-sm-12" style="padding-left: 0">
                         <label style="padding-top: 0px;" class="col-sm-4 control-label">Ý kiến:</label>
                          <div class="col-sm-8">
                            <p id="note-content">ABC</p>
                          </div>
                      </div>  
                    </div>
                    <div class="form-group" style="margin: 0;">
                      <div class="col-sm-12" style="padding-left: 0">
                         <label style="padding-top: 0px;" class="col-sm-4 control-label">Văn bản đi kèm:</label>
                          <div class="col-sm-8">
                            <p id="file-attach">123</p>
                          </div>
                      </div>  
                    </div>
                    <div class="form-group" style="margin: 0;">
                      <div class="col-sm-12" style="padding-left: 0">
                         <label style="padding-top: 0px;" class="col-sm-4 control-label">Ý kiến:</label>
                          <div class="col-sm-8">
                            <p id="note-content-approved">ABC</p>
                          </div>
                      </div>  
                    </div>
                    <div class="form-group" style="margin: 0;">
                      <div class="col-sm-12" style="padding-left: 0">
                         <label style="padding-top: 0px;" class="col-sm-4 control-label">Văn bản đi kèm:</label>
                          <div class="col-sm-8">
                            <p id="file-attach-approved">123</p>
                          </div>
                      </div>  
                    </div>
              </div>   
                </div>
            </form>

              </div>
                
            </div>
      </div>
      
    </div>
</div>

<div class="modal fade" id="myModalSendPheduyet" role="dialog">
    <div class="modal-dialog modal-md" style="width: auto;margin: 10px;">    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Gửi danh sách đi</h4>
        </div>
        <form class="form-horizontal" action="" id="form1">  
        <input type="hidden" class="form-control" id="txtIdDS">          
                <div class="modal-body" style="font-size: 12px;padding: 5px;">
                    <div class="row" id="msg_send" style="padding-left: 10%;padding-right: 10%"></div>   
                    <div class="box-body">
                <div class="form-group">
                  <div class="col-sm-4" style="padding-left: 0">
                    <label  class="col-sm-6">Chọn người nhận <font style="color: red">*</font></label>
                    <div class="col-sm-12">
                      <select name="drNguoinhan" id="drNguoinhan" multiple="multiple" class="form-control" style="width: 100% !important">
                        
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-4" style="padding-left: 0">
                    <label  class="col-sm-6 ">Thêm cc</label>
                  <div class="col-sm-12">
                    <select name="drCC" id="drCC" class="form-control" multiple="multiple" style="width: 100% !important">
                        
                    </select>
                  </div>
                  </div>
                </div>
              </div></div>

                <div class="modal-footer">
                    <div class="row text-center">
                        <button type="button" data-loading-text="Đang thêm mới dữ liệu" class="btn btn-primary" onclick="sendDStonghop()" id="btnSendPheduyet">Gửi danh sách</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </form>   
        </div>
    </div>
</div>

<div class="modal fade" id="myModalTongDanhSach" role="dialog">
    <div class="modal-dialog modal-md" style="width: auto;margin: 10px;">
    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Lập danh sách mới</h4>
        </div>
        <form class="form-horizontal" action="">  
        <input type="hidden" class="form-control" id="txtIdDS">          
                <div class="modal-body" style="font-size: 12px;padding: 5px;">
                    <div class="row" id="group_mghp" style="padding-left: 10%;padding-right: 10%"></div>   
                    <div class="box-body">
                <div class="form-group">
                  <div class="col-sm-4" style="padding-left: 0">
                    <label  class="col-sm-6">Tên danh sách<font style="color: red">*</font></label>

                    <div class="col-sm-12">
                      <input type="text" class="form-control" id="txtNameDS" placeholder="Nhập tên danh sách">
                    </div>
                  </div>
                  <div class="col-sm-4" style="padding-left: 0">
                    <label  class="col-sm-6 ">Tên người lập<font style="color: red">*</font></label>

                  <div class="col-sm-12">
                    <input type="text" class="form-control" id="txtNguoiLap" placeholder="Nhập tên người lập">
                  </div>
                  </div>
                  <div class="col-sm-4" style="padding-left: 0">
                    <label  class="col-sm-6 ">Người ký<font style="color: red">*</font></label>

                    <div class="col-sm-12">
                      <input type="text" class="form-control" id="txtNguoiKy" placeholder="Nhập tên người ký">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-12" style="padding-left: 0">
                    <label  class="col-sm-6">Ghi chú </label>

                    <div class="col-sm-12">
                      <input type="text" class="form-control" id="txtGhiChu" placeholder="Nhập ghi chú">
                    </div>
                  </div>                  
                </div>
                <div class=" box box-body" id="tableMoreProfile"  style="font-size: 12px;overflow: auto;">
              <table   class="table table-striped table-bordered table-hover dataTable no-footer">
                <thead>
                <tr class="success">
                  <th class="text-center" style="vertical-align:middle">STT</th>
                  <th class='text-center' style="vertical-align:middle"><span><input type='checkbox' id="checkAllId"/></span></th>
                  <th class="text-center" style="vertical-align:middle">Tên</th>
                  <th class="text-center" style="vertical-align:middle">Trạng thái</th>
                  <th class="text-center" style="vertical-align:middle">Người duyệt</th>
                  <th class="text-center" style="vertical-align:middle">Trường</th>
                  <th class="text-center" style="vertical-align:middle">File đính kèm</th>
                  <!-- <th class="text-center" style="vertical-align:middle">Danh sách</th> -->
                </tr>
                </thead>
                <tbody id="dataThamDinh">
                
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
                        <select name="viewTableDT" id="viewTableDT"  class="form-control input-sm pagination-show-row">
                          <option value="5">5</option>
                          <option value="10">10</option>
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
              
</div></div>

                <div class="modal-footer">
                    <div class="row text-center">
                        <button type="button" data-loading-text="Đang thêm mới dữ liệu" class="btn btn-primary" id ="saveTotal">Tổng hợp </button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </form>   
      </div>
      
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
       <a href="list"> <b>Phê duyệt </b></a> / Tổng hợp danh sách xin phê duyệt
    </div>
</div>
    <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Thông tin chung</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form class="form-horizontal" id="tonghopdanhsach">
            <div class="row" id="warning_msg" style="padding-left: 10%;padding-right: 10%"></div>
              <div class="box-body">
                <div class="form-group">

                     <div class="col-sm-5">
                      <label  class="col-sm-4 control-label">Năm học</label>
                      <div class="col-sm-8">
                        <select name='sltYear' class="form-control" id='sltYear'>
                          
                         </select>
                      </div>
                    </div>
                

                     <div class="col-sm-5">
                      <label  class="col-sm-4 control-label">Loại danh sách</label>
                      <div class="col-sm-8">
                        <select name='sltType' class="form-control" id='sltType'>
                            <option value="">-- Chọn loại danh sách --</option>
                            <option value="MGHP">Miễn giảm học phí</option>
                            <option value="CPHT">Hỗ trợ chi phí học tập</option>
                            <option value="HTAT">Hỗ trợ ăn trưa</option>
                            <option value="HTBT">Hỗ trợ học sinh bán trú</option>
                            <option value="NGNA">Hỗ trợ người nấu ăn</option>
                            <option value="HSKT">Hỗ trợ học sinh khuyết tật</option>
                            <option value="HSDTTS">Hỗ trợ học sinh dân tộc thiểu số</option>
                            <!-- <option value="TONGHOP">Chế độ chinh sách ưu đãi trẻ em MG,HS,SV </option> -->
                         </select>
                      </div>
                    </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-5">
                      <label  class="col-sm-4 control-label">Tính chất</label>
                      <div class="col-sm-8">
                        <select name='sltStatus' class="form-control" id='sltStatus'>
                          <option value='0'>Bình thường</option>
                          <option value='1'>Cần xử lý ngay</option>
                         </select>
                      </div>
                    </div>
                     <div class="col-sm-5">
                      <label  class="col-sm-4 control-label">Đính kèm </label>
                      <div class="col-sm-8">
                      <input style="margin-top: 2px;" type="file" id="exampleInputFile"  name="file">
                      </div>
                    </div>
                </div>

                 <div class="modal-footer">
                        <div class="row text-center" id="ex-thamdinhhoso">
                            
                            
                            
                        </div>
                    </div>

            </form>
          </div>

            <div class="box box-primary">

                <div class="box-body" style="font-size:12px;overflow: auto ; max-width: 100%">
                    <table class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                  <tr class="success">
                    <th  class="text-center" style="vertical-align:middle">STT</th>
                    <th  class="text-center" style="vertical-align:middle">Tên danh sách</th>
                    <th  class="text-center" style="vertical-align:middle">Người tạo</th>
                    <th  class="text-center" style="vertical-align:middle">Trạng thái</th>
                    <th  class="text-center" style="vertical-align:middle">Hiện trạng</th>
                    <th  class="text-center" style="vertical-align:middle"> File đính kèm</th>

                    <th  class="text-center" style="vertical-align:middle">Chức năng</th>
                  </tr>
                 
                      </thead>
                        <tbody id="dataTotal">                     
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
                        <select name="view-tonghopdanhsach" id="view-tonghopdanhsach"  class="form-control input-sm pagination-show-row">
                          
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