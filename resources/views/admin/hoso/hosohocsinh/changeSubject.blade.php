@extends('layouts.front')

@section('content')
<link rel="stylesheet" href="{!! asset('dist/css/bootstrap-multiselect.css') !!}">
<link rel="stylesheet" href="{!! asset('css/select2.min.css') !!}">
<!-- Bootstrap 3.3.6 -->
<link rel="stylesheet" href="{!! asset('bootstrap/css/bootstrap.min.css') !!}"> 

<link rel="stylesheet" href="{!! asset('css/toastr.css') !!}">

<script src="{!! asset('/plugins/jQuery/jquery-2.2.3.min.js') !!}"></script>
<script src="{!! asset('/js/myScript.js') !!}"></script>
<!-- datepicker -->
<script src="{!! asset('/plugins/datepicker/bootstrap-datepicker.js') !!}"></script>
<script src="{!! asset('js/select2.min.js') !!}"></script>
<script src="{!! asset('js/toastr.js') !!}"></script>
<script src="{!! asset('js/utility.js') !!}"></script>
<script src="{!! asset('/mystyle/js/styleProfile.js') !!}"></script>

<section class="content">
<script type="text/javascript">
  $(function () {
autocomUpdateSubProfile('txtSearchProfile');
    $('#btnUpdateSubject').click(function(){
        var subject_id = '';
        var un_subject_id = '';
        $("input.checkboxactive").each(function() {
            if($(this).is(':checked')){
              subject_id += $(this).val() + "-";
            }else{
              un_subject_id += $(this).val() + "-";
            }
        });
        subject_id = subject_id.substring(0, subject_id.length - 1);
        un_subject_id = un_subject_id.substring(0, un_subject_id.length - 1);
        var o = {
          start_time : $('#txtStart_time').val(),
          end_time : $('#txtEnd_time').val(),
          profile_id : $('#txtProfileID').val(),
          start_year : $('#start_year').val(),
          end_year : $('#end_year').val(),
          subject : subject_id,
          un_subject : un_subject_id,
        }
        PostToServer('/ho-so/hoc-sinh/subject/updateByProfile/',o,function(dataget){
            if(dataget.success != null || dataget.success != undefined){
                $("#ModalSubjectProfile").modal("hide");
                    utility.message("Thông báo",dataget.success,null,3000)
                    GET_INITIAL_NGHILC();
                    loadDataSubject($('#txtSearchProfile').val());
                }else if(dataget.error != null || dataget.error != undefined){
                    utility.message("Thông báo",dataget.error,null,3000)
            }
        },function(result){
          console.log('updateByProfile changeSubject: '+ result);
        },"btnUpdateSubject","","");
    });
    $('#btnChangeSubject').click(function(){
        var subject_id = '';
      //  var un_subject_id = '';
        $("input.checkboxactive").each(function() {
            if($(this).is(':checked')){
              subject_id += $(this).val() + "-";
            }
        });
        subject_id = subject_id.substring(0, subject_id.length - 1);
        var o = {
          profile_id : $('#txtProfileID').val(),
          subject : subject_id,
          start_year : $('#txtstart_year').val(),
          start_year_cur : $('#start_year').val()
        }
        PostToServer('/ho-so/hoc-sinh/subject/insertByProfile/',o,function(dataget){
            if(dataget.success != null || dataget.success != undefined){
                $("#ModalSubjectProfile").modal("hide");
                    utility.message("Thông báo",dataget.success,null,3000)
                    GET_INITIAL_NGHILC();
                    loadDataSubject($('#txtSearchProfile').val());
                }else if(dataget.error != null || dataget.error != undefined){
                    utility.message("Thông báo",dataget.error,null,3000)
            }
        },function(result){
          console.log('updateByProfile changeSubject: '+ result);
        },"btnUpdateSubject","","");
    });
    $('select#drSchoolTHCD').change(function() {
      if($(this).val() != null && $(this).val() != "" && parseInt($(this).val()) != 0){
            loading();
        loadComboxLop($(this).val(),'sltLopGrid',function(){
                closeLoading();
            });
        $('select#sltLopGrid').removeAttr('disabled');

      }else{
        $('select#sltLopGrid').html('<option value="">--Chọn lớp--</option>');
        $('select#sltLopGrid').attr('disabled','disabled');
      }
      GET_INITIAL_NGHILC();
      loadDataSubject();
    });
  loadComboxTruongHoc("drSchoolTHCD", function(){
      loadComboxLop($('#school-per').val(),'sltLopGrid',function(){
          closeLoading();
          $('select#sltLopGrid').removeAttr('disabled');
          GET_INITIAL_NGHILC();
          loadDataSubject($('#txtSearchProfile').val());
      });
       
  }, $('#school-per').val());
  $("#drSchoolTHCD").select2({
      placeholder: "-- Chọn trường học --",
      allowClear: true,
    });

    $("#sltLopGrid").select2({
      placeholder: "-- Chọn lớp học --",
      allowClear: true
    });
  $('select#sltLopGrid').change(function() {
      GET_INITIAL_NGHILC();
      loadDataSubject($('#txtSearchProfile').val());
  });
  $('#btnLoadDataSubject').click(function(){
      GET_INITIAL_NGHILC();
      loadDataSubject($('#txtSearchProfile').val());
  });
  $('#drPagingDanhsachtonghop').click(function(){
      GET_INITIAL_NGHILC();
      loadDataSubject($('#txtSearchProfile').val());
  });




  //  autocompleteSearch("txtSearchProfile", 1);


   
  });


 
   
 
</script>

<div class="modal fade" id="ModalSubjectProfile" role="dialog">
    <div class="modal-dialog modal-md" style="width: 80%;margin: 30px auto;">
    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header" style="padding: 0px 10px">
          <button type="button" class="close" style="margin-top: 10px;" data-dismiss="modal">&times;</button>
          <h4 class="">Đối tượng</h4>
        </div><div style="margin-top: 5px;margin-bottom: 10px">
              <div class="col-sm-5" style="padding-left: 0">
                    <label  class="col-sm-5" style="margin-top: 5px;">Năm áp dụng mới<font style="color: red">*</font></label>

                  <div class="col-sm-6">
                    <input type="text"  class="form-control" name="txtstart_year" id="txtstart_year" >
                  </div>
                  </div>
                  <div class="col-sm-5" style="padding-left: 0">
                    <label  class="col-sm-4 " style="margin-top: 5px;">Năm kết thúc </label>

                  <div class="col-sm-6">
                    <input type="text" class="form-control" disabled="disabled" name="txtend_year" id="txtend_year" >
                  </div>
                  </div> </div>
        <div class="box-body no-padding">
              
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message" style="margin-top: 10px;">
              <form class="form-horizontal" action=""> 
                      
                <div class="modal-body">
                    <div class="row" id="group_message_approved" style="padding-left: 10%;padding-right: 10%"></div>   
                    <div class="box-body">
                      <input type="hidden" name="txtStart_time" id="txtStart_time">
                      <input type="hidden" name="txtEnd_time" id="txtEnd_time">
                      <input type="hidden" name="txtProfileID" id="txtProfileID">
                      <input type="hidden" name="start_year" id="start_year">
                      <input type="hidden" name="end_year" id="end_year">
                 
                      <div class="box box-primary">

                <div class="box-body" style="font-size:12px;overflow: auto ; max-width: 100%">
                    <table class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                        <tr class="success" id="cmisGridHeader">
                          <th  class="text-center" style="vertical-align:middle;width: 5%">STT</th>
                          <th  class="text-center" style="vertical-align:middle;width: 5%">X</th>
                          <th  class="text-center" style="vertical-align:middle;width: 50%">Tên đối tượng</th>
                          <th  class="text-center" style="vertical-align:middle;width: 10%">Ngày tạo</th>
                          <th  class="text-center" style="vertical-align:middle;width: 10%">Ngày cập nhật</th>
                          <th  class="text-center" style="vertical-align:middle;width: 10%">Người cập nhật</th>
                          <th  class="text-center" style="vertical-align:middle;width: 10%">Trạng thái</th>

                        </tr>
                 
                      </thead>
                        <tbody id="dataSubjectProfile">                     
                        </tbody>
                    </table>
                </div>       
            </div>

                


                  </div>   
                </div>
                <div class="modal-footer">
                    <div class="row text-center" >
                        <button type="button"  data-loading-text="Đang cập nhật dữ liệu" class="btn btn-primary" id ="btnUpdateSubject">Cập nhật</button>
                        <button type="button" data-loading-text="Đang thay đổi dữ liệu" class="btn btn-primary" id ="btnChangeSubject">Thay đổi</button>
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
    <div class="panel-heading" id="addnew-export-profile">
       <a href="/"><b> Hồ sơ </b></a> / Cập nhật thay đổi đối tượng
    </div>
</div>
    

            <div class="box box-primary">

               <div class="box box-primary form-horizontal" style="font-size: 12px;">
                  <div class="form-group " style="margin-top: 10px;margin-bottom: 10px">
                    <div class="col-sm-4">
                      <label  class="col-sm-2 control-label">Trường</label>
                      <div class="col-sm-10">
                       <select name="drSchoolTHCD" id="drSchoolTHCD"  class="form-control "></select>
                      </div>
                    </div>
                     <div class="col-sm-3">
                      <label  class="col-sm-2 control-label">Lớp</label>
                      <div class="col-sm-10">
                        <select name="sltLopGrid" disabled="disabled" id="sltLopGrid"  class="form-control "></select>
                      </div>
                    </div>
                     <div class="col-sm-3">
                       <label  class="col-sm-4 control-label">Tìm kiếm</label>
                      <div class="box-tools col-sm-8">
                          <div class="has-feedback">
                            <input id="txtSearchProfile" type="text" class="form-control input-sm" placeholder="Tìm kiếm ">
                            <span class="glyphicon glyphicon-search form-control-feedback"></span>
                          </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                     <div class="row text-center" id="event-thcd">
                            <button type="button" data-loading-text="Đang tải dữ liệu" class="btn btn-success" id ="btnLoadDataSubject"><i class="glyphicon glyphicon-search"></i> Xem </button>
                          
                        </div>
                    </div>
                     
                </div>  
                <div class="box-body" style="font-size: 12px">     
                    <table class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                        <tr class="success" id="cmisGridHeader">
                          <th  class="text-center" style="vertical-align:middle;width: 5%">STT</th>
                          <th  class="text-center" style="vertical-align:middle;width: 10%">Tên học sinh</th>
                          <th  class="text-center" style="vertical-align:middle;width: 10%">Ngày sinh</th>
                          
                          <th  class="text-center" style="vertical-align:middle;width: 5%">Lớp học</th>
                          <th  class="text-center" style="vertical-align:middle;width: 40%">Thuộc đối tượng</th>
                          <th  class="text-center" style="vertical-align:middle;width: 10%">Năm áp dụng</th>
                          <th  class="text-center" style="vertical-align:middle;width: 10%">Kết thúc áp dụng</th>
                          <th  class="text-center" style="vertical-align:middle;width: 10%">Thời gian</th>
                          <th  class="text-center" colspan="2" style="vertical-align:middle;width: 10%">Chức năng</th>
                        </tr>
                 
                      </thead>
                        <tbody id="dataSubject">                     
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
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>

@endsection