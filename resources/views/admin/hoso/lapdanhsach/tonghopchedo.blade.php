@extends('layouts.front')

@section('title', 'This is a blank page')
@section('description', 'This is a blank page that needs to be implemented')

@section('content')
<link rel="stylesheet" href="{!! asset('css/select2.min.css') !!}">

<script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="{!! asset('/js/myScript.js') !!}"></script>
<script type="text/javascript" src="{!! asset('mystyle/js/styleLapDanhSach.js') !!}"></script>
<section class="content">
<script type="text/javascript">
$(function () {

  $("#fileAttack").filestyle({
    buttonText : 'Đính kèm',
    buttonName : 'btn-info'
  });
  loadComboxTruongHoc(47);
  loadComboboxHocky()

  $('#drPagingDanhsachtonghop').change(function() {
       GET_INITIAL_NGHILC();
       loaddataDanhSachTongHop($(this).val());
  });

  var pageing = $('#drPagingDanhsachtonghop').val();
    
  permission(function(){
        var html_view  = '';
        if(check_Permission_Feature('5')){
            html_view += '<button type="button" onclick="loaddataDanhSachTongHop('+$('#drPagingDanhsachtonghop').val()+')" class="btn btn-success" id =""><i class="glyphicon glyphicon-search"></i> Xem </button>';
        }
        if(check_Permission_Feature('1')){
            
            html_view += '<button type="button" onclick="openPopupLapTHCD()" class="btn btn-success" id =""><i class="glyphicon glyphicon-pushpin"></i> Lập danh sách </button>';
        }
        html_view += '<button type="button" onclick="resetFormTHCD()" class="btn btn-primary" id =""><i class="glyphicon glyphicon-refresh"></i> Làm mới</button>';
        
        $('#event-thcd').html(html_view);
    });
  //loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val());
});
   function myModalLapDanhSach(){
    var message = "";
    message = validatePopupTongHopCheDo();
    if (message !== "") {
      utility.messagehide("messageValidate", message, 1, 5000);
      return;
    }
    
    $('#txtNameDSTHCD').val('');
    $('#txtNguoiLapTHCD').val('');
    $('#txtNguoiKyTHCD').val('');
    $('#txtGhiChuTHCD').val('');
    $("#myModalLapDanhSach").modal("show");
   }

  function openPopupSendNGNA(){
    $("#drNguoinhan").val('').select2({
      placeholder: "-- Chọn người nhận --",
      allowClear: true
    });
    $("#drCC").val('').select2({
      placeholder: "-- Chọn người nhận --",
      allowClear: true
    });
    $("#myModalSendNGNA").modal("show");
  }
  
</script>

<div class="modal fade" id="myModalApproved" role="dialog">
    <div class="modal-dialog modal-md" style="width: 100%;margin: 30px auto;">
    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Duyệt chế độ được hưởng</h4>
        </div>
        <div class="box-body no-padding">
              
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message" style="margin-top: 10px;">
              <form class="form-horizontal" action="">         
                <div class="modal-body">
                    <div class="row" id="group_message_approved" style="padding-left: 10%;padding-right: 10%"></div>   
                    <div class="box-body">

                      <div class="box box-primary">

                <div class="box-body" style="font-size:12px;overflow: auto ; max-width: 100%">
                    <table class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                        <tr class="success" id="cmisGridHeader">
                          <th  class="text-center" style="vertical-align:middle">STT</th>
                          <th  class="text-center" style="vertical-align:middle">Chọn</th>
                          <th  class="text-center" style="vertical-align:middle">Tên chế độ</th>
                          <th  class="text-center" style="vertical-align:middle">Nhóm đối tượng</th>

                        </tr>
                 
                      </thead>
                        <tbody id="dataDanhsachCheDo">                     
                        </tbody>
                    </table>
                    <!-- <div class="box-footer clearfix">
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
                                          <select name="drPagingDanhsachtonghop" id="drPagingDanhsachtonghop"  class="form-control input-sm pagination-show-row">
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
                  </div> -->
                </div>       
            </div>

                  </div>   
                </div>
                <div class="modal-footer">
                    <div class="row text-center">
                        <button type="button" data-loading-text="Đang thêm mới dữ liệu" class="btn btn-primary" id ="btnApprovedTHCD">Duyệt chế độ</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </form>

              </div>
                
            </div>
      </div>
      
    </div>
</div>

<div class="modal fade" id="myModalTHCD" role="dialog">
    <div class="modal-dialog modal-md" style="width: 80%;margin: 30px auto;">
    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Thông tin chế độ được hưởng</h4>
        </div>
        <div class="box-body no-padding">
              
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message" style="margin-top: 10px;">
              <form class="form-horizontal" action="">         
                <div class="modal-body">
                    <div class="row" id="group_message" style="padding-left: 10%;padding-right: 10%"></div>   
                    <div class="box-body" id="contentBox">

                      <!-- <div class="form-group" style="margin: 0;">
                        <div class="col-sm-12" style="padding-left: 0">
                           <label style="padding-top: 0px;" class="col-sm-4 control-label">Chế độ</label>

                            <div class="col-sm-8">
                              <p>Nhóm đối tượng</p>
                            </div>
                        </div>  
                      </div> -->

                      <div class="form-group" style="margin: 0;">
                        <div class="col-sm-12" style="padding-left: 0">
                           <label style="padding-top: 0px;" class="col-sm-4 control-label">Chế độ:</label>

                            <div class="col-sm-8">
                              <p>Nhóm đối tượng</p>
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

<div class="modal fade" id="myModalLapDanhSachTHCD" role="dialog">
    <div class="modal-dialog modal-md" style="width: auto;margin: 10px;">
    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Lập danh sách mới</h4>
        </div>
        <form class="form-horizontal" action="" id="frmPopupTHCD">  
        <input type="hidden" class="form-control" id="txtIdDS">          
                <div class="modal-body" style="font-size: 12px;padding: 5px;">
                    <div class="row" id="group_message_THCD" style="padding-left: 10%;padding-right: 10%"></div>   
                    <div class="box-body">
                <div class="form-group">
                  <div class="col-sm-4" style="padding-left: 0">
                    <label  class="col-sm-6">Tên danh sách <font style="color: red">*</font></label>

                    <div class="col-sm-12">
                      <input type="text" class="form-control" id="txtNameDSTHCD" placeholder="Nhập tên danh sách">
                    </div>
                  </div>
                  <div class="col-sm-4" style="padding-left: 0">
                    <label  class="col-sm-6 ">Tên người lập<font style="color: red">*</font></label>

                  <div class="col-sm-12">
                    <input type="text" class="form-control" id="txtNguoiLapTHCD" placeholder="Nhập tên người lập">
                  </div>
                  </div>
                  <div class="col-sm-4" style="padding-left: 0">
                    <label  class="col-sm-6 ">Người ký<font style="color: red">*</font></label>

                    <div class="col-sm-12">
                      <input type="text" class="form-control" id="txtNguoiKyTHCD" placeholder="Nhập tên người ký">
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-4" style="padding-left: 0">
                    <label  class="col-sm-6 ">Tính chất</label>

                    <div class="col-sm-12">
                      <select name='sltStatus' class="form-control" id='sltStatus'>
                          <option value='0'>Bình thường</option>
                          <option value='1'>Cần xử lý ngay</option>
                         </select>
                    </div>
                  </div>
                  <div class="col-sm-4" style="padding-left: 0">
                    <label  class="col-sm-6 ">Đính kèm </label>

                  <div class="col-sm-12">
                    <input style="margin-top: 2px;" type="file" id="fileAttack" name="file">
                  </div>
                  </div>
                  <div class="col-sm-4" style="padding-left: 0">
                    <!-- <label  class="col-sm-6 ">Người ký<font style="color: red">*</font></label> -->

                    <div class="col-sm-12">
                      <!-- <input type="text" class="form-control" id="txtNguoiKyTHCD" placeholder="Nhập tên người ký"> -->
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <div class="col-sm-12" style="padding-left: 0">
                    <label  class="col-sm-6">Ghi chú </label>

                    <div class="col-sm-12">
                      <input type="text" class="form-control" id="txtGhiChuTHCD" placeholder="Nhập ghi chú">
                    </div>
                  </div>
                </div>
              
          </div></div>

                <div class="modal-footer">
                    <div class="row text-center">
                        <button type="button" data-loading-text="Đang thêm mới dữ liệu" class="btn btn-primary" id ="btnInsertTHCD">Lập danh sách</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </form>   
      </div>
      
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
       <a href="/ho-so/lap-danh-sach/list"><b> Hồ sơ </b></a> / Danh sách hỗ trợ
    </div>
</div>
    <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Thông tin chung</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form class="form-horizontal" id="formtonghopchedo">
            <div class="row" id="messageValidate" style="padding-left: 10%;padding-right: 10%"></div> 
              <div class="box-body">
                <div class="form-group">
                  <div class="col-sm-5">
                      <label  class="col-sm-4 control-label">Chọn trường <font style="color: red">*</font></label>
                      <div class="col-sm-8">
                        <select name='sltSchool' class="form-control" id='sltSchool'>
                         </select>
                      </div>
                    </div>
                     <div class="col-sm-5">
                      <label  class="col-sm-4 control-label">Năm học <font style="color: red">*</font></label>
                      <div class="col-sm-8">
                        <select name='sltYear' class="form-control" id='sltYear'>
                          
                         </select>
                      </div>
                    </div>
                </div>

                 <div class="modal-footer">
                        <div class="row text-center" id="event-thcd">
                            
                        </div>
                    </div>
                  </div>
            </form>
          </div>

            <div class="box box-primary">

                <div class="box-body" style="font-size:12px;overflow: auto ; max-width: 100%">
                    <table class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                        <tr class="success" id="cmisGridHeader">
                          <th  class="text-center" style="vertical-align:middle">STT</th>
                          <th  class="text-center" style="vertical-align:middle">Tên học sinh</th>
                          <th  class="text-center" style="vertical-align:middle">Ngày sinh</th>
                          <th  class="text-center" style="vertical-align:middle">Trường học</th>
                          <th  class="text-center" style="vertical-align:middle">Lớp học</th>
                          <th  class="text-center" style="vertical-align:middle">Hỗ trợ MGHP</th>
                          <th  class="text-center" style="vertical-align:middle">Hỗ trợ CPHT</th>
                          <th  class="text-center" style="vertical-align:middle">Hỗ trợ AT TEMG</th>
                          <th  class="text-center" style="vertical-align:middle">Hỗ trợ BT TA</th>
                          <th  class="text-center" style="vertical-align:middle">Hỗ trợ BT TO</th>
                          <th  class="text-center" style="vertical-align:middle">Hỗ trợ BT VHTT</th>
                          <th  class="text-center" style="vertical-align:middle">Hỗ trợ TA HS</th>
                          <th  class="text-center" style="vertical-align:middle">Hỗ trợ HSKT HB</th>
                          <th  class="text-center" style="vertical-align:middle">Hỗ trợ HSKT DDHT</th>
                          <th  class="text-center" style="vertical-align:middle">Hỗ trợ HB HSDTNT</th>
                          <th  class="text-center" style="vertical-align:middle">Hỗ trợ HSDTTS</th>
                          <th  class="text-center" style="vertical-align:middle">Tổng tiền</th>

                          <th  class="text-center" style="vertical-align:middle">Chức năng</th>
                        </tr>
                 
                      </thead>
                        <tbody id="dataDanhsachTonghop">                     
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
                                          <select name="drPagingDanhsachtonghop" id="drPagingDanhsachtonghop"  class="form-control input-sm pagination-show-row">
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