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

  $("#exampleInputFile").filestyle({
    buttonText : 'Đính kèm',
    buttonName : 'btn-info'
  });
  loadComboxTruongHoc();
  loadComboxNamHoc();
  permission(function(){
        var html_view  = '';
        
        if(check_Permission_Feature('1')){
            html_view += '<button type="button" onclick="myModalLapDanhSach()" class="btn btn-success" id ="btnSave"><i class="glyphicon glyphicon-pushpin"></i> Tạo danh sách</button> ';
        }
        html_view += '<button type="button" class="btn btn-primary" onclick="reset()"><i class="glyphicon glyphicon-refresh"></i> Làm mới</button>';
        // if(check_Permission_Feature('5')){
        //     html_view += '<button type="submit" class="btn btn-info" id ="btnSave"><i class="glyphicon glyphicon-send"></i> Gửi danh sách</button>';
        // }
        $('#ex-hotroantrua').html(html_view);
    });
  loaddataAll($('select#view-hotroantrua').val(),'HTAT','dataHoTroAnTrua');
});
   function myModalLapDanhSach(){
    if(parseInt($('#sltSchool').val()) != 0){
        if($('#sltYear').val() != ""){
          $('#txtNameDS').val('');
          $('#txtNguoiLap').val('');
          $('#txtNguoiKy').val('');
          $('#txtGhiChuHTAT').val('');
           $("#myModalLapDanhSachHTAT").modal("show");
        }else{
            //utility.message("Thông báo","Xin mời chọn năm học",null,3000)
            utility.messagehide('warning_msg','Xin mời chọn năm học',1,3000);
            $('#sltYear').focus();
        }
        
      }else{
          //utility.message("Thông báo","Xin mời chọn trường",null,2000)
          utility.messagehide('warning_msg','Xin mời chọn trường',1,3000);
          $('#sltSchool').focus();
      }
    
   }

  function openPopupSendHTAT(){
    $("#drNguoinhan").val('').select2({
      placeholder: "-- Chọn người nhận --",
      allowClear: true
    });
    $("#drCC").val('').select2({
      placeholder: "-- Chọn người nhận --",
      allowClear: true
    });
    $("#myModalSendHTAT").modal("show");
  }
  
</script>

<div class="modal fade" id="myModalRevertHTAT" role="dialog">
    <div class="modal-dialog modal-md" style="width: 60%;margin: 30px auto;">
    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Thông tin chung</h4>
        </div>
        <div class="box-body no-padding">              
              <div class="mailbox-read-message">
               <form class="form-horizontal" action="">  
        <input type="hidden" class="form-control" id="txtIdthamdinh">          
                <div class="modal-body">
                    <div class="row" id="group_message" style="padding-left: 10%;padding-right: 10%"></div>   
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
              </div>   
                </div>
            </form>

              </div>
                
            </div>
      </div>
      
    </div>
</div>

<div class="modal fade" id="myModalSendHTAT" role="dialog">
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
                        <button type="button" data-loading-text="Đang thêm mới dữ liệu" class="btn btn-primary" onclick="send()" id="btnSendMGHP">Gửi danh sách</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </form>   
        </div>
    </div>
</div>

<div class="modal fade" id="myModalLapDanhSachHTAT" role="dialog">
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
                    <label  class="col-sm-6">Tên danh sách <font style="color: red">*</font></label>

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
                      <input type="text" class="form-control" id="txtGhiChuHTAT" placeholder="Nhập ghi chú">
                    </div>
                  </div>
                </div>
                
              
</div></div>

                <div class="modal-footer">
                    <div class="row text-center">
                        <button type="button" data-loading-text="Đang thêm mới dữ liệu" class="btn btn-primary" id ="save3">Thêm mới</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </form>   
      </div>
      
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
       <a href="/ho-so/lap-danh-sach/list"><b> Hồ sơ </b></a> / Hỗ trợ ăn trưa cho trẻ em mẫu giáo
    </div>
</div>
    <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Thông tin chung</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form class="form-horizontal" id="frmHTAT">
            <div class="row" id="warning_msg" style="padding-left: 10%;padding-right: 10%"></div>
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
                      <label  class="col-sm-4 control-label">Năm học<font style="color: red">*</font></label>
                      <div class="col-sm-8">
                        <select name='sltYear' class="form-control" id='sltYear'>
                          
                         </select>
                      </div>
                    </div>
                </div>
              
                <div class="form-group">
                  <div class="col-sm-5">
                      <label  class="col-sm-4 control-label">Tính chất văn bản</label>
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
                      <input style="margin-top: 2px;" type="file" id="exampleInputFile">
                      </div>
                    </div>
                </div>

                 <div class="modal-footer">
                        <div class="row text-center" id="ex-hotroantrua">
                           <!--  <button type="button" onclick="myModalLapDanhSach()" class="btn btn-success" id ="btnSave"><i class="glyphicon glyphicon-pushpin"></i> Tạo danh sách</button>
                            <button type="submit" class="btn btn-primary" id ="btnSave"><i class="glyphicon glyphicon-refresh"></i> Hủy danh sách</button>
                            <button type="submit" class="btn btn-info" id ="btnSave"><i class="glyphicon glyphicon-send"></i> Gửi danh sách</button> -->
                        </div>
                    </div>
                  </div>
            </form>
          </div>

            <div class="box box-primary">

                <div class="box-body" style="font-size:12px;overflow: auto ; max-width: 100%">
                    <table id="example" class="table table-striped table-bordered table-hover dataTable no-footer">
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
                        <tbody id="dataHoTroAnTrua">                     
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
                        <select name="view-hotroantrua" id="view-hotroantrua"  class="form-control input-sm pagination-show-row">
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