@extends('layouts.front')

@section('title', 'This is a blank page')
@section('description', 'This is a blank page that needs to be implemented')

@section('content')
<link rel="stylesheet" href="{!! asset('css/select2.min.css') !!}">
<script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
<section class="content">
<script src="{!! asset('js/select2.min.js') !!}"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
<script src="{!! asset('/js/myScript.js') !!}"></script>
<script type="text/javascript" src="{!! asset('mystyle/js/jsDanhMuc.js') !!}"></script>
<style type="text/css">
    #using_json_2 a {
    white-space: normal !important;
    height: auto;
    padding: 1px 2px;
    font-size: 12px;
    width: 90%;
}
</style>
<script type="text/javascript">    

$(function () {
  
  $('#btnPhanLoai').click(function(){
    $('#typeWards').html('');
    GetFromServer('/danh-muc/phanloaixa/loadcomboPLXa',function(data){
      var html_show = "";
      var stt =0;
        for (var i = 0; i < data.length; i++) {
            if (data[i].wards_parent_id == 0) {
                stt++;
                html_show += "<tr><td class='text-center' style='vertical-align:middle'>"+stt+"</td>";
                html_show += "<td style='vertical-align:middle'>"+data[i].wards_name+"</td>";
                var k = 0;
                for (var j = 0; j < data.length; j++) {
                    
                    if(data[i].wards_id == data[j].wards_parent_id){
                      k++;
                      html_show += "<td class='text-center' style='vertical-align:middle'><input type='checkbox' name='radio_wards' id='check_"+data[j].wards_id+"' value='"+data[j].wards_id+"'/>&nbsp;<label for='check_"+data[j].wards_id+"'> "+data[j].wards_name+"</label></td>";
                    }
                    
                    
                }
                if(k==0){
                      html_show += "<td class='text-center'  style='vertical-align:middle'><input type='checkbox' name='radio_wards' id='check_"+data[i].wards_id+"' value='"+data[i].wards_id+"'/></td>";
                }
                html_show += "</tr>";
                }
        }
        $('#typeWards').html(html_show);
        $('#mdPhanLoai').modal('show');
    },function(data){
      console.log(data);
    },"","","");
      
  });

    $("#drSiteParents").attr("disabled", true);
    loadComboXaPhuong();
    $('#txtSiteCode').focus();
    $("#drSiteLevel").change(function(){
      getSitebyLevel($(this).val(), null);
    });

    $("#drSiteParents").select2({
      placeholder: "-- Chọn cấp trực thuộc --",
      allowClear: true,
      focus: open
    });

    $('#using_json_2').jstree({
        'core' : {
            'data' : 
            {
                'url' : function (node) {
                    //console.log(node);
                  return node.id === '#' ?
                    '/danh-muc/xaphuong/deptsites' :
                    '/danh-muc/xaphuong/childsites';
                },
                'data' : function (node) {
                    //alert(node.id);
                    //console.log(node);
                    //data: [{id: node.id, text: '<button>hello</button>'}]
                  return { 'id' : node.id };
                }
            }
        }
    });

    $('#using_json_2').on("changed.jstree", function (e, data) {
        var currentIdSelected = data.selected[0];
            
        var v_jsonData = JSON.stringify({ SITEID: currentIdSelected });
        var show_html = "";
        var site_parent_id = 0;
        $.ajax({
            type: "POST",
            url:'/danh-muc/xaphuong/getXaPhuongbyID',
            data: v_jsonData,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success : function (results){
              var level = 0;
              var item = '';
              $('#txtSiteID').val(results[0]['site_id']);
              $('#txtSiteCode').val(results[0]['site_code']);
              $('#txtSiteName').val(results[0]['site_name']);
              $('#drSiteLevel').val(results[0]['site_level']);
              $('#drSiteActive').val(results[0]['site_active']);

              getSitebyLevel(parseInt(results[0]['site_level']), parseInt(results[0]['site_parent_id']));
              $("#btnInsertSite").html("Lưu");
              $("#btnDeleteSite").attr("disabled", false);
              $("#txtSiteCode").attr("disabled", true);
            },
            error : function (results){
              console.log(results);
            }
        });
    });

  });
</script>
<div class="modal fade" id="mdPhanLoai" role="dialog">
    <div class="modal-dialog modal-md" style="width: auto;margin: 10px;">
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title-up-to">Phân loại phường xã</h4>
        </div>
         
          <br>
        <div id="">
            <div class="box-body" style="font-size: 12px">
              <table id="HistoryTable" class="table table-striped table-bordered table-hover dataTable no-footer">
                <thead>
                <tr class="success">
                  <th class="text-center" style="vertical-align:middle">STT</th>
                  <th class="text-center" style="vertical-align:middle">Phân loại</th>
                  <th class="text-center" colspan="5" style="vertical-align:middle">Lựa chọn</th>
                </tr>
                </thead>
                <tbody id="typeWards">
                
                </tbody>
              </table>
            </div>
           
        </div>
         <div class="modal-footer">
                    <div class="row text-center">
                      <button type="button" class="btn btn-primary">Cập nhật</button>
                        <button type="button" class="btn btn-primary" id="btnClosePopupUpto" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
      </div>
      
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
       <b> Danh mục </b> / Tỉnh/ Thành
        <!-- <a data-toggle="modal" data-target="#myModal" style="margin-left: 10px" class="btn btn-success btn-xs pull-right"  >
            <i class="glyphicon glyphicon-plus"></i> Tạo mới
        </a >  -->
        <!-- <a class="btn btn-success btn-xs pull-right" href="#" onclick="exportExcel('SITE')">
            <i class="glyphicon glyphicon-print"></i> Xuất excel
        </a> -->
    </div>
    </div>
          <section class="content">
      <div class="row">
        <!-- left column -->
        <div class="col-md-5">
          <!-- general form elements -->
          <div id="changeTree" class="right-side strech row box box-primary" style="overflow: auto ; max-height: 600px">
            <div class="box-header with-border">
              <h3 class="box-title">Tỉnh/ Thành</h3>
            </div>
            <!-- /.box-header -->
            
  <div id="using_json_2" class="jstree jstree-1 jstree-default">
      <!-- <ul>
        <li>
            <span class="content">Folder</span>
            <ul>
                <li>
                    <span class="content">Subfolder</span>
                    <ul>
                        <li>
                            <span class="content">File</span>
                            <span class="actions">
                                <a href="/open">open</a>
                                <a href="/delete">delete</a>
                            </span>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
      </ul> -->
  </div>

          </div>
          <!-- /.box -->
        </div>
        <!--/.col (left) -->
        <!-- right column -->
        <div class="col-md-7">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Thông tin</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form class="form-horizontal">
            <input type="hidden" class="form-control" id="txtSiteID">    
              <div class="box-body">
                <div class="form-group">
                  <label class="col-sm-3 control-label">Mã địa phương<font style="color: red">*</font></label>

                  <div class="col-sm-9">
                    <input type="text" name="" id="txtSiteCode" class="form-control" placeholder="Mã địa phương" accept="charset" autofocus="true">
                    <!-- <img src="../../images/Image_valid.png" id="imgValidDepartment"><label id="lblValidDepartment" class="valid"></label> -->
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label">Tên địa phương<font style="color: red">*</font></label>
                  <div class="col-sm-9">
                    <input type="text" name="" id="txtSiteName" class="form-control" placeholder="Tên địa phương" accept="charset">
                    <!-- <img src="../../images/Image_valid.png" id="imgValidCode"><label id="lblValidCode" class="valid"></label> -->
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label">Cấp hành chính<font style="color: red">*</font></label>
                  <div class="col-sm-9">
                    <select id="drSiteLevel" class="form-control">
                      <option value="0">-- Chọn cấp hành chính --</option>
                      <option value="1">Tỉnh/ Thành phố</option>
                      <option value="2">Huyện/ Quận</option>
                      <option value="3">Xã/ Phường</option>
                      <option value="4">Thôn/ Xóm</option>
                    </select>
                    <!-- <img src="../../images/Image_valid.png" id="imgValidName"><label id="lblValidName" class="valid"></label> -->
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label">Trực thuộc</label>
                  <div class="col-sm-9">
                    <select id="drSiteParents" class="form-control">
                      <option value='0'>-- Chọn cấp trực thuộc --</option>
                    </select>
                  </div>
                </div>
                 <div class="form-group">
                  <label class="col-sm-3 control-label">Trạng thái</label>

                  <div class="col-sm-9">
                    <select id="drSiteActive" class="form-control">
                        <option value="1">Kích hoạt</option>
                        <option value="0">Chưa kích hoạt</option>
                    </select>
                  </div>
                </div>
              </div>

                 <div class="modal-footer">
                        <div class="row text-center">
                            <button type="button" class="btn btn-primary" id="btnInsertSite">Thêm mới</button>
                            <button type="button" class="btn btn-primary" id="btnDeleteSite" disabled="true">Xóa</button>
                            <button type="button" class="btn btn-primary" id="btnResetSite" data-dismiss="modal">Làm mới</button>
                             <button type="button" class="btn btn-primary" id="btnPhanLoai" data-dismiss="modal">Phân loại</button>
                        </div>
                    </div>
            </form>
          </div>
          <!-- /.box -->
          
          <!-- /.box -->
        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
    </section>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>

@endsection