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

    $("#fileAttack").filestyle({
      buttonText : 'Đính kèm',
      buttonName : 'btn-info'
    });
    // loadComboxTruongHoc(function(){
    //                     $("#sltSchool").val(dataget[0].truong_id).change();
    //                 });

    loadComboxTruongHoc("drSchoolTHCD",function(){
      closeLoading();
       loadComboboxHocky(1, function(){
      
      });
    },$('#school-per').val());


    $('#drPagingDanhsachtonghop').change(function() {
         GET_INITIAL_NGHILC();
         loaddataDanhSachTongHop($(this).val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
    });

    $('#sltTrangthai').change(function() {
         GET_INITIAL_NGHILC();
         loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $(this).val());
    });

    var pageing = $('#drPagingDanhsachtonghop').val();
      
    permission(function(){
          var html_view  = '';
          var html_view_header  = '<b> Hồ sơ </b> / Lập đề nghị';
          
          if(check_Permission_Feature('5')){
            html_view += '<button type="button" data-loading-text="Đang tải dữ liệu" class="btn btn-success" id ="btnLoadDataTruong"><i class="glyphicon glyphicon-search"></i> Xem </button>';
              // html_view += '<button type="submit" class="btn btn-info" id ="btnSendNGNA"><i class="glyphicon glyphicon-send"></i> Gửi danh sách</button>';
          }
          
          if(check_Permission_Feature('1')){
            html_view_header += '<a onclick="openModalAdd()" style="margin-left: 10px" class="btn btn-success btn-xs pull-right"> <i class="glyphicon glyphicon-plus"></i> Tạo mới </a >';
            
            html_view += '<button type="button" onclick="openPopupLapTHCD()" class="btn btn-success" id =""><i class="glyphicon glyphicon-pushpin"></i> Lập danh sách </button>';

            // html_view += '<button type="button" onclick="loaddataBaocaoTongHop(10)" class="btn btn-success" id =""><i class="glyphicon glyphicon-search"></i> Xem danh sách</button>';

            html_view += '<button type="button" onclick="approvedAll(1)" class="btn btn-success" id =""><i class="glyphicon glyphicon-pencil"></i> Duyệt toàn bộ</button>';

            html_view += '<button type="button" onclick="approvedUnAll(1)" class="btn btn-success" id =""><i class="glyphicon glyphicon-pencil"></i> Hủy duyệt toàn bộ</button>';
          }
          $('#addnew-export-profile').html(html_view_header);
          // $('#event-thcd').html(html_view);
      }, 91);
    
    // permission(function(){
    //   var html_view  = '<b> Hồ sơ </b> / Thêm mới hồ sơ học sinh';

    //   if(check_Permission_Feature('1')){
    //     html_view += '<a onclick="openModalAdd()" style="margin-left: 10px" class="btn btn-success btn-xs pull-right"> <i class="glyphicon glyphicon-plus"></i> Tạo mới </a >';
    //     // html_view += '<a onclick="openModalUpto()" style="margin-left: 10px" class="btn btn-success btn-xs pull-right"> <i class="glyphicon glyphicon-plus"></i> Chức năng </a >';
    //   }
    //   if(check_Permission_Feature('4')){
    //       // html_view += '<a onclick="exportExcelProfile()" class="btn btn-success btn-xs pull-right" href="#"> <i class="glyphicon glyphicon-print"></i> Xuất excel </a>';
    //   }
    //   $('#addnew-export-profile').html(html_view);
    // }, 91);

    // GET_INITIAL_NGHILC();
    // loaddataProfile($('select#viewTableProfile').val(),$('select#sltTruongGrid').val(),$('select#sltLopGrid').val(), $('#txtSearchProfile').val());


    $("#sltDantoc").select2({
      placeholder: "-- Chọn dân tộc --",
      allowClear: true,
      focus: open
    });

    $("#sltTruong").select2({
      placeholder: "-- Chọn trường --",
      allowClear: true
    });

    $("#sltLop").select2({
      placeholder: "-- Chọn lớp --",
      allowClear: true
    });

    $("#sltTinh").select2({
      placeholder: "-- Chọn tỉnh/ thành phố --",
      allowClear: true
    });

    $("#sltQuan").select2({
      placeholder: "-- Chọn huyện/ quận --",
      allowClear: true
    });

    $("#sltPhuong").select2({
      placeholder: "-- Chọn xã/ phường --",
      allowClear: true
    });

    autocompleteSearch("txtSearchProfile", 1);


    


    $("#sltHocky").change(function () {
      var namhoc = $('#txtYearProfile').val();
      namhoc = namhoc.substr(3, namhoc.length);
      // console.log(namhoc);
      var hocky = $(this).val();
      var truongId = $('#sltTruong').val();
      var arrSubID = []; 
      var str = "";
      var $el = $(".multiselect-container");
      $el.find('li.active input').each(function(){
        str += $(this).val() + ",";
      });

      str = hocky + '-' + namhoc + '-' + truongId + '-' + str;
      // console.log(str.substr(0,str.length-1));
      // console.log("ABC");
      loadMoneybySubject(str.substr(0,str.length-1));
    });


    // $('#checkedAllChedo').click(function() {
    //     var checkboxes = $(this).closest('table').find('td').find(':checkbox');
    //     if($(this).is(':checked')) {
    //         checkboxes.attr('checked', 'checked');
    //     } else {
    //         checkboxes.removeAttr('checked');
    //     }
    // });

    $('#checkedAllChedo').change(function() {
        if ($('#checkedAllChedo').prop('checked'))
            $('[id*="chilCheck"]').prop('checked', true);
        else
            $('[id*="chilCheck"]').prop('checked', false);
    });
  });
  
    // window.onclick = function(event) {
    //     resetControl();
    // }

  var close = true;
  function viewMoreProfile(){
    if(close){
      $('#tableMoreProfile').removeAttr('hidden');
      close = false;
    }else{
      $('#tableMoreProfile').attr('hidden','hidden');
      close = true;
    }
  }
  function openModalAdd(){
   // insertUpdate(1);
   $('#divKyHoc').attr('hidden','hidden');
   loading();
    $('#saveProfile').html("Thêm mới");
    $('#sltDoituong').multiselect({
      nonSelectedText:'-- Chọn đối tượng --'
    });
   resetControl();
   loadComboxTruongHoc("sltTruong",function(){
      closeLoading();
   },$('#school-per').val());
    //$("#sltDoituong").multiselect('clearSelection');
    $("#sltDoituong").val("").multiselect("clearSelection");
    loadComboxDantoc(null, 24);
    $('#sltDoituong').removeAttr('disabled');
    $("#myModalProfile").modal("show");
  };
  function openModalUpdate(){
    $('#divKyHoc').removeAttr('hidden');
    $('#saveProfile').html("Cập nhật");
    $('.modal-title').html('Sửa hồ sơ học sinh');
   // insertUpdate(1);

   //$("#sltDoituong").multiselect('clearSelection');
    //loadComboxDantoc();
    $('#sltDoituong').multiselect({
      nonSelectedText:'-- Chọn đối tượng --'
    });
     
  //    $("#sltDoituong option:selected").prop("selected", false);
    //   $("#sltDoituong").multiselect('refresh');
    $("#myModalProfile").modal("show");

   }
   function openModalUpto(){
    var t =  $('#uptoClass').DataTable().clear().draw().destroy();
    resetControl();
    $('#upClass-select-all').prop('checked', false);
    loading();
    loadComboxTruongHoc("drSchoolUpto",function(){
      closeLoading();
    },$('#school-per').val());
    $("#myModalUpto").modal("show");
   }

    function openModalHistory(){    
    $("#myHistory").modal("show");
   }
</script>

<!-- ////////////////////////////////////////////////////////////////Phần hồ sơ///////////////////////////////////////////////////////////// -->
<div class="modal fade" id="myModalApproved" role="dialog">
    <div class="modal-dialog modal-md" style="margin: 30px auto; width: 80%;">
    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="">Chọn chế độ được hưởng</h4>
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
                          <th  class="text-center" style="vertical-align:middle"><input type="checkbox" name="checkedAllChedo" id="checkedAllChedo"></th>
                          <th  class="text-center" style="vertical-align:middle">Tên chế độ</th>
                          <th  class="text-center" style="vertical-align:middle">Nhóm đối tượng</th>

                        </tr>
                 
                      </thead>
                        <tbody id="dataDanhsachCheDo">                     
                        </tbody>
                    </table>
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


                  </div>   
                </div>
                <div class="modal-footer">
                    <div class="row text-center">
                        <button type="button" data-loading-text="Đang thêm mới dữ liệu" class="btn btn-primary" id ="btnApprovedTHCD">Lưu</button>
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
          <h4>Thông tin chế độ được hưởng : <label id="lblProfileName"></label>></h4>
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
    <div class="modal-dialog modal-md" style="width: 70%;margin: 10px auto;">
    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="">Lập danh sách đề nghị</h4>
        </div>
        <form class="form-horizontal" action="" id="frmPopupTHCD">  
        <input type="hidden" class="form-control" id="txtIdDS">          
                <div class="modal-body" style="font-size: 12px;padding: 5px;">
                    <div class="row" id="group_message_THCD" style="padding-left: 10%;padding-right: 10%"></div>   
                    <div class="box-body">
                <div class="form-group">
                  <div class="col-sm-4" style="padding-left: 0">
                    <label  class="col-sm-6">Số công văn <font style="color: red">*</font></label>
                    <div class="col-sm-12" style="padding-right: 5px !important;">
                      <input type="text" class="form-control" id="txtNameDSTHCD" placeholder="Nhập số công văn">
                    </div>
                  </div>

                  <div class="col-sm-4" style="padding-left: 0">
                    <label  class="col-sm-6 ">Chọn chế độ </label>
                    <div class="col-sm-12" style="padding-left: 5px !important; padding-right: 5px !important;">
                      <select name="sltChedo" id="sltChedo" multiple="multiple" style="width: 500px;" class="form-control">
                        <option value="1">Miễn giảm học phí</option>
                        <option value="2">Chi phí học tập</option>
                        <option value="3">Hỗ trợ ăn trưa trẻ em mẫu giáo</option>
                        <option value="4">Hỗ trợ học sinh bán trú</option>
                        <option value="5">Hỗ trợ học sinh khuyết tật</option>
                        <option value="6">Hỗ trợ ăn trưa học sinh theo NQ57</option>
                        <option value="7">Hỗ trợ học sinh dân tộc thiểu số</option>
                        <option value="8">Hỗ trợ học bổng cho học sinh dân tộc nội trú</option>
                        <option value="9">Hỗ trợ người nấu ăn</option>
                      </select>
                    </div>
                  </div>


                  <!-- <div class="col-sm-6" style="padding-left: 0">
                    <label  class="col-sm-6">Ghi chú </label>

                    <div class="col-sm-12">
                      <input type="text" class="form-control" id="txtGhiChuTHCD" placeholder="Nhập ghi chú">
                    </div>
                  </div> -->

                  <!-- <div class="col-sm-4" style="padding-left: 0">
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
                  </div> -->
                </div>

                <!-- <div class="form-group">
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

                    <div class="col-sm-12">
                      
                    </div>
                  </div>
                </div> -->
                
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

<!-- ////////////////////////////////////////////////////////////////Phần hồ sơ///////////////////////////////////////////////////////////// -->
<div class="modal fade" id="modalDanhsachBaocao" role="dialog">
    <div class="modal-dialog modal-md" style="width: auto;margin: 10px;">
    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="">Lập danh sách đề nghị</h4>
        </div>
        <form class="form-horizontal" action="" id="frmPopupTHCD">  
        <input type="hidden" class="form-control" id="txtIdDS">          
                <div class="modal-body" style="font-size: 12px;padding: 5px;">
                    <div class="row" id="group_message_THCD" style="padding-left: 10%;padding-right: 10%"></div>   
                    <div class="box-body">

                <div class="form-group">
                  <div class="col-sm-4" style="padding-left: 0">
                    <label  class="col-sm-6">Số công văn <font style="color: red">*</font></label>
                    <div class="col-sm-12">
                      <select name="sltCongvan" id="sltCongvan" style="width: 100%;" class="form-control">
                      </select>
                    </div>
                  </div>

                  <div class="col-sm-4" style="padding-left: 0">
                    <label  class="col-sm-6 ">Chọn chế độ </label>
                    <div class="col-sm-12">
                      <select name="sltLoaiChedo" id="sltLoaiChedo" class="form-control">
                        <option value="">--- Chọn chế độ ---</option>
                        <option value="MGHP">Miễn giảm học phí</option>
                        <option value="CPHT">Chi phí học tập</option>
                        <option value="HTAT">Hỗ trợ ăn trưa trẻ em mẫu giáo</option>
                        <option value="HTBT">Hỗ trợ học sinh bán trú</option>
                        <option value="HSKT">Hỗ trợ học sinh khuyết tật</option>
                        <option value="HTATHS">Hỗ trợ ăn trưa học sinh theo NQ57</option>
                        <option value="HSDTTS">Hỗ trợ học sinh dân tộc thiểu số</option>
                        <option value="HBHSDTNT">Hỗ trợ học bổng cho học sinh dân tộc nội trú</option>
                      </select>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <div id="">
                    <div class="box-body" style="font-size: 12px">
                      <table id="HistoryTable" class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                        <tr class="success">
                          <th class="text-center" style="vertical-align:middle">STT</th>
                          <th class="text-center" style="vertical-align:middle">Tên học sinh</th>
                          <th class="text-center" style="vertical-align:middle">Ngày sinh</th>
                          <th class="text-center" style="vertical-align:middle">Dân tộc</th>
                          <th class="text-center" style="vertical-align:middle">Bố mẹ</th>
                          <th class="text-center" style="vertical-align:middle">Hộ khẩu</th>
                          <th class="text-center" style="vertical-align:middle">Trường</th>
                          <th class="text-center" style="vertical-align:middle">Lớp</th>
                          <th class="text-center" style="vertical-align:middle">Năm học</th>
                          <th class="text-center" style="vertical-align:middle">Tiền ăn</th>
                          <th class="text-center" style="vertical-align:middle">Tiền ở</th>
                          <th class="text-center" style="vertical-align:middle">Tiền văn hóa, tủ thuốc</th>
                          <th class="text-center" style="vertical-align:middle">Chức năng</th>
                        </tr>
                        </thead>
                        <tbody id="contentPopupModalDanhsach">
                        
                        </tbody>
                      </table>
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

<div class="modal fade" id="" role="dialog">
    <div class="modal-dialog modal-md" style="width: auto;margin: 10px;">
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title-up-to">Công văn đã lập<p id="currentDate"></p> </h4>
        </div>
         
          <br>
                <div class="form-group">
                  <div class="col-sm-4" style="padding-left: 0">
                    <label  class="col-sm-6">Số công văn </label>

                    <div class="col-sm-12">
                      <select name="sltCongvan" id="sltCongvan" style="width: 100%;" class="form-control">
                    </div>
                  </div>

                  <div class="col-sm-4" style="padding-left: 0">
                    <label  class="col-sm-6">Loại chế độ </label>

                    <div class="col-sm-12">
                      <select name="sltLoaichedo" id="sltLoaichedo" style="width: 100%;" class="form-control">
                    </div>
                  </div>
                </div>
        <!-- <div id="">
            <div class="box-body" style="font-size: 12px">
              <table id="HistoryTable" class="table table-striped table-bordered table-hover dataTable no-footer">
                <thead>
                <tr class="success">
                  <th class="text-center" style="vertical-align:middle">STT</th>
                  <th class="text-center" style="vertical-align:middle">Tên danh sách</th>
                  <th class="text-center" style="vertical-align:middle">Loại danh sách</th>
                  <th class="text-center" style="vertical-align:middle">Trạng thái</th>
                  <th class="text-center" style="vertical-align:middle">Ngày tạo</th>
                  <th class="text-center" style="vertical-align:middle">Người tạo</th>
                </tr>
                </thead>
                <tbody id="contentPopupModalDanhsach">
                
                </tbody>
              </table>
            </div>
           
        </div> -->
         <div class="modal-footer">
                    <div class="row text-center">
                        <button type="button" class="btn btn-primary" id="btnClosePopupUpto" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
      </div>
      
    </div>
</div>


<div class="modal fade" id="myModalUpto" role="dialog">
    <div class="modal-dialog modal-md" style="width: auto;margin: 10px;">
    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title-up-to">Chức năng lên lớp - học lại - nghỉ học</h4>
        </div>
          <form class="form-horizontal" action="">                 
            <div class="modal-body" style="font-size: 12px;padding: 5px;">
                    <div class="row" id="message_Upto" style="padding-left: 10%;padding-right: 10%">
                      <div id="messageDanger"></div>
                    </div>
                    <div class="box-body">
                
                      <div class="form-group">
                        
                        <div class="col-sm-4" style="padding-left: 0">
                          <label  class="col-sm-12 ">Trường học <font style="color: red">*</font></label>

                          <div class="col-sm-12">
                            <select name="drSchoolUpto" id="drSchoolUpto" class="form-control" style="width: 100% !important">
                              <option value="">--Chọn trường--</option>
                          </select>
                          </div>
                        </div>
                        <div class="col-sm-4" style="padding-left: 0">
                          <label  class="col-sm-6 ">Lớp học hiện tại <font style="color: red">*</font></label>

                          <div class="col-sm-12" >
                            <select name="drClassUpto" disabled="disabled" id="drClassUpto" class="form-control" style="width: 100% !important">
                                <option value="">--Chọn lớp--</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-4" style="padding-left: 0">
                          <label  class="col-sm-6 ">Năm học <font style="color: red">*</font></label>

                          <div class="col-sm-12" >
                            <select name="drYearUpto" disabled="disabled" id="drYearUpto"  class="form-control" style="width: 100% !important">
                                <option value="">--Chọn năm học--</option>
                            </select>
                          </div>
                        </div>
                       
                      </div>
                <div class="form-group">
                   <div class="col-sm-4" style="padding-left: 0">
                          <label  class="col-sm-12 ">Chức năng <font style="color: red">*</font></label>

                          <div class="col-sm-12" >
                            <select name="drClassNext" disabled="disabled" id="drClassNext" class="form-control" style="width: 100% !important">
                                <option value="">-- Chức năng --</option>
                                <option value="1">-- Lên lớp  --</option>
                                <option value="2">-- Nghỉ học --</option>
                                <option value="3">-- Học lại - chuyển lớp --</option>

                            </select>
                          </div>
                        </div>
                        <div class="col-sm-4" style="padding-left: 0">
                         <label id="labelClassBack" hidden="hidden" class="col-sm-12 ">Lớp học lại - chuyển lớp<font style="color: red">*</font></label>
                          <div class="col-sm-12" >
                            <select name="drClassBack" hidden="hidden" disabled="disabled" id="drClassBack" class="form-control" style="width: 100% !important">
                    
                            </select>
                          </div>

                          <label id="labelClassNext" hidden="hidden" class="col-sm-12 ">Lên lớp <font style="color: red">*</font></label>
                          <div class="col-sm-12" >
                            <select name="StlClassNext"  disabled="disabled" id="StlClassNext" class="hidden form-control" style="width: 100% !important">
                    
                            </select>
                          </div>

                          <label id="labelOutProfile" hidden="hidden" class="col-sm-12 ">Ngày nghỉ học </label>

                          <div class="col-sm-12" >
                            <input type="text"  name="dateOutProfile" disabled="disabled" placeholder="ngày-tháng-năm"  id="dateOutProfile" class="form-control" hidden="hidden">
                          </div>
                        </div>
                        <div class="col-sm-4" style="padding-left: 0">
                         
                        </div>
                </div>
                    </div>
                    </div>
                <div class="modal-footer">
                    <div class="row text-center">
                        <button type="button" data-loading-text="Đang thêm mới dữ liệu" class="btn btn-primary" id ="btnUpto">Lên lớp</button>
                        <button type="button" data-loading-text="Đang thêm mới dữ liệu" class="btn btn-primary" id ="btnRevert">Hoàn tác</button>
                        <button type="button" class="btn btn-primary" id="btnClosePopupUpto" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
          </form>
          <br>
        <div id="">
            <div class="box-body" style="font-size: 12px">
              <table id="uptoClass" class="table table-striped table-bordered table-hover dataTable no-footer">
                <thead>
                <tr class="success">
                  <th class="text-center" style="vertical-align:middle">STT</th>
                  <th class="text-center" style="vertical-align:middle"><input name="select_all" value="1" id="upClass-select-all" type="checkbox"></th>
                  <th class="text-center" style="vertical-align:middle">Mã học sinh</th>
                  <th class="text-center" style="vertical-align:middle">Họ và tên</th>
                  <th class="text-center" style="vertical-align:middle">Năm sinh</th>
                  <th class="text-center" style="vertical-align:middle">Dân tộc</th>
                 
                  <th class="text-center" style="vertical-align:middle">Hộ khẩu thường trú</th>
                  <th class="text-center" style="vertical-align:middle">Cha mẹ - người giám hộ</th>
                  <th class="text-center" style="vertical-align:middle">Trường học</th>
  <!--                 <th class="text-center" style="vertical-align:middle">Trạng thái lớp học</th>
                  <th class="text-center" style="vertical-align:middle">Năm học</th> -->
                </tr>
                </thead>
                <tbody id="contentPopupUpto">
                
                </tbody>
              </table>
            </div>
           
        </div>
      </div>
      
    </div>
</div>


<div class="modal fade" id="myModalProfile" role="dialog">
    <div class="modal-dialog modal-md" style="width: 100%;margin: 10px auto;">
    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Thêm mới học sinh</h4>
        </div>
        <form class="form-horizontal" action="" id="formtest">  
        <input type="hidden" class="form-control" id="txtIdProfile">          
                <div class="modal-body" style="font-size: 12px;padding: 5px;">
                    <div class="row" id="group_message" style="padding-left: 10%;padding-right: 10%">
                      <div id="messageDangers"></div>
                    </div>
                    <div class="box-body">

                <div class="form-group">
                  <!-- <div class="col-sm-3" style="padding-left: 0">
                    <label  class="col-sm-6">Mã học sinh <font style="color: red">*</font></label>

                    <div class="col-sm-12">
                      <input type="text" class="form-control" id="txtCodeProfile" placeholder="Nhập mã học sinh" autofocus="true">
                    </div>
                  </div> -->
                  <div class="col-sm-3" style="padding-left: 0">
                    <label  class="col-sm-6 ">Họ và tên <font style="color: red">*</font></label>

                  <div class="col-sm-12">
                    <input type="text" class="form-control" id="txtNameProfile" placeholder="Nhập tên học sinh">
                  </div>
                  </div>
                  <div class="col-sm-3" style="padding-left: 0">
                    <label  class="col-sm-6 ">Ngày sinh <font style="color: red">*</font></label>

                    <div class="col-sm-12">
                      <input type="text" class="form-control" id="txtBirthday" placeholder="ngày-tháng-năm">
                    </div>
                  </div>
                  <div class="col-sm-3" style="padding-left: 0">
                    <label  class="col-sm-6 ">Dân tộc <font style="color: red">*</font></label>

                    <div class="col-sm-12">
                      <select name="sltDantoc" id="sltDantoc"  class="form-control" style="width: 100% !important">
                      </select>
                    </div>
                  </div>
                 <div class="col-sm-3" style="padding-left: 0">
                    <label  class="col-sm-12 ">Chủ hộ <font style="color: red">*</font></label>
    
                    <div class="col-sm-12">
                      <input type="text" class="form-control" id="txtParent" placeholder="Nhập cha mẹ hoặc người giám hộ">
                    </div>
                  </div>
                </div>

                <div class="form-group">
                   <div class="col-sm-3" style="padding-left: 0" >
                    <label  class="col-sm-12 ">Thuộc đối tượng </label>

                    <div class="col-sm-12">
                      <select name="sltDoituong" id="sltDoituong" multiple="multiple" class="form-control">

                      </select>
                    </div>
                  </div>
                  
                  <div class="col-sm-3" style="padding-left: 0">
                    <label  class="col-sm-12 ">Trường <font style="color: red">*</font></label>

                    <div class="col-sm-12">
                      <select name="sltTruong" id="sltTruong"  class="form-control" style="width: 100% !important">
                        <option value="">--Chọn trường--</option>
                    </select>
                    </div>
                  </div>
                  <div class="col-sm-3" style="padding-left: 0">
                    <label  class="col-sm-6 ">Lớp học <font style="color: red">*</font></label>

                    <div class="col-sm-12">
                      <select name="sltLop" disabled="disabled" id="sltLop"  class="form-control" style="width: 100% !important">
                          <option value="">--Chọn lớp--</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3" style="padding-left: 0">
                    <label  class="col-sm-6 ">Năm nhập học <font style="color: red">*</font></label>
                    <div class="col-sm-12">
                      <input type="text" class="form-control" id="txtYearProfile" name="txtYearProfile" placeholder="tháng-năm">
                    </div>
                  </div>
                  
                </div>

                <div class="form-group">
                  <div class="col-sm-3" style="padding-left: 0">
                    <label  class="col-sm-6 ">Tỉnh/thành <font style="color: red">*</font></label>

                    <div class="col-sm-12">
                      <select name="sltTinh" id="sltTinh" class="form-control" style="width: 100% !important">
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3" style="padding-left: 0">
                    <label  class="col-sm-6 ">Quận/huyện <font style="color: red">*</font></label>

                    <div class="col-sm-12">
                      <select name="sltQuan" disabled="disabled" id="sltQuan"  class="form-control" style="width: 100% !important">
                          
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3" style="padding-left: 0">
                    <label  class="col-sm-6 ">Phường/xã</label>

                    <div class="col-sm-12">
                      <select name="sltPhuong" disabled="disabled" id="sltPhuong"  class="form-control" style="width: 100% !important">
                       
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3" style="padding-left: 0">
                    <label  class="col-sm-6 ">Thôn xóm <font style="color: red">*</font></label>

                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="txtThonxom" placeholder="Nhập thôn xóm">
                    </div>
                  </div>
                </div>

                
                <div class="modal-header">
          <h4>Học sinh được hưởng chế độ 116</h4>
        </div>

                <div class="form-group">
                  <div class="col-sm-3" style="padding-left: 0">
                    <label  class="col-sm-6 " style="width: 80% !important">Trường hợp nhà ở xa trường</label>

                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="txtKhoangcach" placeholder="Nhập số km">
                    </div>
                  </div>
                  <div class="col-sm-3" style="padding-left: 0">
                    <label  class="col-sm-12 ">Giao thông cách trở, đi lại khó khăn</label>

                    <div class="col-sm-12">
                      <input type="text" class="form-control" id="drGiaoThong" placeholder="Nhập số km">
                       <!--  <select name="sltBantru" id="drGiaoThong"  class="form-control ">
                          <option value="0">Không</option>
                          <option value="1">Có</option>
                    </select> -->
                    </div>
                  </div>
                  <div class="col-sm-3" style="padding-left: 0">
                    <label  class="col-sm-12 ">Ở trong trường/ ngoài trường <font style="color: red">*</font></label>

                    <div class="col-sm-12">
                        <select name="sltBantru" id="sltBantru"  class="form-control ">
                          <option value="">-- Chọn --</option>
                          <option value="0">Ở ngoài trường</option>
                          <option value="1">Ở trong trường</option>
                        </select>
                    </div>
                  </div> 
                   <div class="col-sm-3" style="padding-left: 0">
                     <label  class="col-sm-12 ">Ở trong trường </label>

                    <div class="col-sm-12" style="padding-left: 5px !important; padding-right: 5px !important;">
                        <label for="ckbNQ57" class="btn btn-default">Nghị Quyết 57 <input type="checkbox" id="ckbNQ57" class="badgebox"><span class="badge">&check;</span></label>
                    </div>
   
                  </div>                         
                </div>
                <div class="form-group" id="divKyHoc">
                  <div class="col-sm-3" style="padding-left: 0">
                    <label  class="col-sm-6 ">Kỳ học</label>

                    <div class="col-sm-12">
                        <select name="sltHocky" id="sltHocky"  class="form-control ">
                          <option value="">-- Chọn kỳ học --</option>
                          <option value="HK1">Học kỳ 1</option>
                          <option value="HK2">Học kỳ 2</option>
                          <option value="CA">Cả năm</option>
                        </select>
                    </div>
                  </div>
                 
                </div>
                 <div class="row" id="group_message" style="padding-left: 10%;padding-right: 10%">
                      <div id="messageDangersQD"></div>
        
                    </div>
       <div class=" box box-body" id="tableMoreProfile" hidden="hidden" style="font-size: 12px;overflow: auto;">
              <table   class="table table-striped table-bordered table-hover dataTable no-footer">
                <thead>
                <tr class="success">
                  <th class="text-center" style="vertical-align:middle;width: 1%">STT</th>
                  <th class="text-center" style="vertical-align:middle;width: 1%">X</th>
                  <th class="text-center" style="vertical-align:middle;width: 15%">Loại quyết định <font style="color: red">*</font></th>
                  <th class="text-center" style="vertical-align:middle;width: 15%">Mã quyết định <font style="color: red">*</font></th>
                  <th class="text-center" style="vertical-align:middle;width: 15%">Số/kí hiệu <font style="color: red">*</font></th>
                  <th class="text-center" style="vertical-align:middle;width: 15%">Cơ quan xác nhận <font style="color: red">*</font></th>
                  <th class="text-center" style="vertical-align:middle;width: 15%">Ngày xác nhận <font style="color: red">*</font></th>
                  <th class="text-center" style="vertical-align:middle;width: 15%">Đính kèm<font style="color: red">*</font></th>
                  <th class="text-center" style="vertical-align:middle;width: 10%">File </th>
                  
                </tr>
                </thead>
                <tbody id="tbDecided">
                <tr id="trContent">
                        <!-- <td>
                            <label id="lblSTT">1</label>
                        </td>
                        <td>
                            <button id="btnDeleteDecided" onclick='' class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i></button>
                        </td>
                        <td class="type">
                          <select name="drDecidedType" id="drDecidedType" class="form-control">
                            <option value="MGHP">Miễn giảm học phí</option>
                            <option value="CPHT">Chi phí học tập</option>
                            <option value="HTAT">Hỗ trợ ăn trưa</option>
                            <option value="HTBT">Hỗ trợ bán trú</option>
                            <option value="NGNA">Hỗ trọ người nấu ăn</option>
                            <option value="HSKT">Hỗ trợ học sinh khuyết tật</option>
                            <option value="HSDTTS">Hỗ trợ học sinh dân tộc thiểu số tại huyện Mù Cang Chải và Trạm tấu</option>
                            <option value="TONGHOP">Chế độ chính sách ưu đãi</option>
                          </select>
                        </td>
                        <td class="code">
                            <input type="text" name="" id="txtDecidedCode">
                        </td>
                        <td class="name">
                            <input type="text" name="" id="txtDecidedName">
                        </td>
                        <td class="number">
                            <input type="text" name="" id="txtDecidedNumber">
                        </td>
                        <td class="confirmation">
                            <input type="text" name="" id="txtDecidedConfirmation">
                        </td>
                        <td class="confirmdate">
                            <input type="date" name="" id="txtDecidedConfirmDate">
                        </td>
                        <td class="uploadfile">
                            <input type="file" name="" id="txtDecidedFileUpload" title="Chọn file Upload">
                        </td>
                        <td class="oldfile">
                            <label id="lblOldfile"></label>
                        </td> -->
                        
                    </tr>
                </tbody>
              </table>
              <div class="col-sm-2"> 
            <button style="margin-top: 5px" type="button" class="btn btn-block btn-default" id="btnAddNewRow">Thêm tài liêu</button></div> 
            <div class="col-sm-2"> 
              <button style="margin-top: 5px" type="button" class="btn btn-block btn-default" id="clearFile">Xóa tệp file chọn</button></div>
            </div>


            <!--//////////////////////////////////////// Bảng tính tiền ///////////////////////////////////////////////////-->
             <div class=" box box-body" id="tbMoney" hidden="hidden" style="font-size: 12px;overflow: auto; width: 60%;">
              <table   class="table table-striped table-bordered table-hover dataTable no-footer">
                <thead>
                <tr class="success">
                  <th class="text-center" style="vertical-align:middle;width: 1%">STT</th>
                  <th class="text-left" style="vertical-align:middle;width: 20%">Chế độ/ chính sách được hưởng </th>
                  <th class="text-center" style="vertical-align:middle;width: 5%">Số tiền </th>
                  
                </tr>
                </thead>
                <tbody id="tbMoneyContent">
                
                </tbody>
              </table>
              
            </div>
          </div>
        </div>

                <div class="modal-footer">
                    <div class="row text-center">
                        <button type="button" data-loading-text="Đang lưu" class="btn btn-primary" id ="saveProfile">Lưu</button>
                        <button type="button" class="btn btn-primary" id="btnClosePopupProfile" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </form>   
      </div>
      
    </div>
</div>

<div class="panel panel-default" style="margin-bottom: 0">
    <div class="panel-heading" id="addnew-export-profile">
       <!-- <a href="/ho-so/lap-danh-sach/list"><b> Hồ sơ </b></a> / Danh sách hỗ trợ -->
    </div>
</div>
    <div class="box box-info">
            <!-- <div class="box-header with-border">
              <h3 class="box-title">Thông tin chung</h3>
            </div> -->
            <!-- /.box-header -->
            <!-- form start -->
            <form class="form-horizontal" id="formtonghopchedo">
            <div class="row" id="messageValidate" style="padding-left: 10%;padding-right: 10%"></div> 
              <div class="box-body">
                <div class="form-group">
                  <div class="col-sm-4">
                      <label  class="col-sm-4 control-label">Chọn trường <font style="color: red">*</font></label>
                      <div class="col-sm-8">
                        <select name='drSchoolTHCD' class="form-control" id='drSchoolTHCD'>
                         </select>
                      </div>
                    </div>
                     <div class="col-sm-4">
                      <label  class="col-sm-4 control-label">Năm học <font style="color: red">*</font></label>
                      <div class="col-sm-8">
                        <select name='sltYear' class="form-control" id='sltYear'>
                          
                         </select>
                      </div>
                    </div>
                     <div class="col-sm-4">
                      <label  class="col-sm-4 control-label">Tìm kiếm </label>
                      <div class="box-tools col-sm-8">
                          <div class="has-feedback">
                            <input id="txtSearchProfile" type="text" class="form-control input-sm" placeholder="Tìm kiếm ">
                            <span class="glyphicon glyphicon-search form-control-feedback"></span>
                          </div>
                        </div>
                    </div>
                </div>

                 <div class="modal-footer">
                        <div class="row text-center" id="event-thcd">
                            <button type="button" onclick="" class="btn btn-success" id ="btnLoadDataTruong"><i class="glyphicon glyphicon-search"></i> Xem danh sách </button>
                            <button type="button" onclick="openPopupLapTHCD()" class="btn btn-success" id =""><i class="glyphicon glyphicon-pushpin"></i> Lập danh sách </button>
                            <button type="button" onclick="approvedAll(1)" class="btn btn-success" id =""><i class="glyphicon glyphicon-pencil"></i> Chọn toàn bộ</button>
                            <button type="button" onclick="approvedUnAll(1)" class="btn btn-success" id =""><i class="glyphicon glyphicon-pencil"></i> Hủy chọn toàn bộ</button>
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
                          <th  class="text-center" style="vertical-align:middle;width: 7%">Tên học sinh</th>
                          <th  class="text-center" style="vertical-align:middle;width: 5%">Ngày sinh</th>
                          <th  class="text-center" style="vertical-align:middle;width: 8%">Trường học</th>
                          <th  class="text-center" style="vertical-align:middle;width: 3%">Lớp học</th>
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
                          <th  class="text-center" style="vertical-align:middle">Ghi chú</th>
                          <th  class="text-center" style="vertical-align:middle"> 
                            <select name="sltTrangthai" id="sltTrangthai">
                              <option value="">---Tất cả---</option>
                              <option value="CHO">Chưa chọn</option>
                              <option value="DA">Đã chọn</option>
                            </select>
                          </th>
                          <th  class="text-center" colspan="2" style="vertical-align:middle">Chức năng</th>
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
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>

@endsection