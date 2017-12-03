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

    loadReport();

    $('#sltCongvan').change(function() {
      loadReportType($(this).val());
    });
    
    autocompleteSearchDenghidalap();

    loadComboxTruongHoc("drSchoolTHCD", function(){
      closeLoading();
    },$('#school-per').val());
    
    $('#drPagingDanhsach').change(function() {
      GET_INITIAL_NGHILC();
      loaddataDanhSachGroupB($(this).val(), $('#txtSearchProfileLapdanhsach').val(), $('#sltGroupHS').val());
    });

    $('select#sltGroupHS').change(function() {
      console.log($(this).val());
      GET_INITIAL_NGHILC();
      loaddataDanhSachGroupB($('#drPagingDanhsach').val(), $('#txtSearchProfileLapdanhsach').val(), $(this).val());
    });

    permission(function(){
      var html_view  = '';
      var html_view_header  = '<b> Hồ sơ </b> / Danh sách đề nghị đã lập';
        html_view += '<button type="button" onclick="" class="btn btn-success" id ="btnViewDanhSachTruongLap"><i class="glyphicon glyphicon-search"></i> Xem danh sách </button>';
      if(check_Permission_Feature('5')){
        
        // html_view += '<button type="button" onclick="loaddataDanhSachGroupB('+$('#drPagingDanhsach').val()+')" class="btn btn-success" id =""><i class="glyphicon glyphicon-search"></i> Xem danh sách học sinh mới nhập học </button>';
        // html_view += '<button type="button" onclick="loaddataDanhSachGroupC('+$('#drPagingDanhsach').val()+')" class="btn btn-success" id =""><i class="glyphicon glyphicon-search"></i> Xem danh sách học sinh dự kiến tuyển </button>';
      }
          
      if(check_Permission_Feature('1')){
        // html_view_header += '<a onclick="openModalAdd()" style="margin-left: 10px" class="btn btn-success btn-xs pull-right"> <i class="glyphicon glyphicon-plus"></i> Tạo mới </a >';
            
        // html_view += '<button type="button" onclick="openPopupLapTHCD()" class="btn btn-success" id =""><i class="glyphicon glyphicon-pushpin"></i> Lập danh sách </button>';

        // html_view += '<button type="button" onclick="loaddataBaocaoTongHop(10)" class="btn btn-success" id =""><i class="glyphicon glyphicon-search"></i> Xem danh sách</button>';
      }
      if(check_Permission_Feature('4')){
          html_view_header += '<a onclick="exportExcelTruongDeNghi()" class="btn btn-success btn-xs pull-right" href="#"> <i class="glyphicon glyphicon-print"></i> Xuất excel </a>';
      }
      $('#addnew-export-profile').html(html_view_header);
      // $('#event-thcd').html(html_view);
    }, 91);
  }); 
</script>

<div class="modal fade" id="myModalRevertPhong" role="dialog">
    <div class="modal-dialog modal-md" style="width: 80%;margin: 30px auto;">
    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="">Danh sách học sinh trả về</h4>
        </div>
        <div class="box-body no-padding">
              
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message">
              <form class="form-horizontal" action="">         
                <div class="modal-body">
                    <div class="box-body">

                <div class="form-group">
                  <div class="col-sm-12" style="padding-left: 0">
                    <label  class="col-sm-6">Ghi chú </label>

                    <div class="col-sm-12">
                      <input type="text" class="form-control" id="txtNote" placeholder="Nhập ghi chú">
                    </div>
                  </div>
                </div>


                  </div>   
                </div>
                <div class="modal-footer">
                    <div class="row text-center">
                        <button type="button" data-loading-text="Đang thêm mới dữ liệu" class="btn btn-primary" id="btnPhongRevert">Trả lại danh sách</button>
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

<div class="panel panel-default">
    <div class="panel-heading" id="addnew-export-profile">
       <!-- <a href="/ho-so/lap-danh-sach/list"><b> Hồ sơ </b></a> / Danh sách hỗ trợ -->
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
                    <label  class="col-sm-4 control-label">Số công văn <font style="color: red">*</font></label>
                    <div class="col-sm-8">
                      <select name='sltCongvan' class="form-control" id='sltCongvan'>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-5">
                    <label  class="col-sm-4 control-label">Chế độ <font style="color: red">*</font></label>
                    <div class="col-sm-8">
                      <select name="sltLoaiChedo" id="sltLoaiChedo" class="form-control">
                        <option value="">--- Chọn chế độ ---</option>
                        <!-- <option value="MGHP">Miễn giảm học phí</option>
                        <option value="CPHT">Chi phí học tập</option>
                        <option value="HTAT">Hỗ trợ ăn trưa trẻ em mẫu giáo</option>
                        <option value="HTBT">Hỗ trợ học sinh bán trú</option>
                        <option value="HSKT">Hỗ trợ học sinh khuyết tật</option>
                        <option value="HTATHS">Hỗ trợ ăn trưa học sinh theo NQ57</option>
                        <option value="HSDTTS">Hỗ trợ học sinh dân tộc thiểu số</option>
                        <option value="HBHSDTNT">Hỗ trợ học bổng cho học sinh dân tộc nội trú</option> -->
                      </select>
                    </div>
                  </div>
                </div>
                <!-- <div class="form-group">
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
                </div> -->

                 <div class="modal-footer">
                        <div class="row text-center" id="event-thcd">
                            <button type="button" onclick="" class="btn btn-success" id ="btnViewDanhSachTruongLap"><i class="glyphicon glyphicon-search"></i> Xem danh sách </button>
                            <button type="button" onclick="" class="btn btn-success" id ="btnTralai"><i class="glyphicon glyphicon-pencil"></i> Trả lại </button>
                        </div>
                    </div>
                  </div>
            </form>
          </div>

            <div class="box box-primary">

                <div class="box-body" style="font-size:12px;overflow: auto ; max-width: 100%">
                  <div class="form-group " style="margin-top: 10px;margin-bottom: 10px">
                    <!--  <div class="col-sm-3">
                      <label  class="col-sm-4 control-label">Hiển thị: </label>
                      <div class="col-sm-6">
                        <select name="viewTableProfile" id="viewTableProfile"  class="form-control">
                          <option value="5">5</option>
                          <option value="10">10</option>
                          <option value="15">15</option>
                          <option value="20">20</option>
                    </select>
                    </div>
                    </div> -->
                    <div class="col-sm-4">
                      <!-- <label  class="col-sm-4 control-label">Trường</label>
                      <div class="col-sm-8">
                       <select name="sltTruongGrid" id="sltTruongGrid"  class="form-control "></select>
                      </div> -->
                    </div>
                    <div class="col-sm-4">
                      <!-- <label  class="col-sm-4 control-label">Lớp</label>
                      <div class="col-sm-8">
                        <select name="sltLopGrid" disabled="disabled" id="sltLopGrid"  class="form-control "></select>
                      </div> -->
                    </div>
                    <div class="col-sm-4">
                      <div class="box-header with-border">
                        <h3 class="box-title"></h3>

                        <div class="box-tools pull-right">
                          <div class="has-feedback" id="divSearch">
                            <input id="txtSearchProfileLapdanhsach" type="text" class="form-control input-sm" placeholder="Tìm kiếm ">
                            <span class="glyphicon glyphicon-search form-control-feedback"></span>
                          </div>
                        </div>
                        <!-- /.box-tools -->
                      </div>
                    </div>
                  </div>
                  
                    <table class="table table-striped table-bordered table-hover dataTable no-footer">
                      <thead>
                          <tr class="success" id="headerTable">
                            <th class="text-center" style="vertical-align:middle">STT</th>
                            <th class="text-center" style="vertical-align:middle"><input type="checkbox" name="" /></th>
                            <!-- <th class="text-center" style="vertical-align:middle">Số công văn</th>
                            <th class="text-center" style="vertical-align:middle">Tên chế độ</th> -->
                            <th class="text-center" style="vertical-align:middle">Nhóm học sinh</th>
                            <!-- <th class="text-center" style="vertical-align:middle">
                              <select name="sltGroupHS" id="sltGroupHS">
                                <option value="">-Tìm kiếm theo nhóm-</option>
                                <option value="GROUPA">Đang có mặt tại trường</option>
                                <option value="GROUPB">Chuẩn bị nhập học</option>
                                <option value="GROUPC">Dự kiến tuyển mới</option>
                              </select>
                            </th> -->
                            <th class="text-center" style="vertical-align:middle">Tên học sinh</th>
                            <!-- <th class="text-center" style="vertical-align:middle"><button class="btn btn-info btn-xs">Chọn tất cả</button></th> -->
                            <th class="text-center" style="vertical-align:middle">Ngày sinh</th>
                            <th class="text-center" style="vertical-align:middle">Dân tộc</th>
                            <th class="text-center" style="vertical-align:middle">Bố mẹ</th>
                            <th class="text-center" style="vertical-align:middle">Thôn/ xóm</th>
                            <th class="text-center" style="vertical-align:middle">Xã/ phường</th>
                            <th class="text-center" style="vertical-align:middle">Huyện/ Quận</th>
                            <th class="text-center" style="vertical-align:middle">Trường</th>
                            <th class="text-center" style="vertical-align:middle">Lớp</th>
                            <th class="text-center" style="vertical-align:middle">Ngày nhập học</th>
                            <th class="text-center" style="vertical-align:middle">Nhu cầu</th>
                            <th class="text-center" style="vertical-align:middle">Dự toán</th>
                          </tr>
                 
                      </thead>
                        <tbody id="dataLapDanhsachHS">                     
                        </tbody>
                    </table>
                    <div class="box-footer clearfix" id="divPaging">
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
                                          <select name="drPagingDanhsach" id="drPagingDanhsach"  class="form-control input-sm pagination-show-row">
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