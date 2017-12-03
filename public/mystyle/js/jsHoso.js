$(function () {

    //alert(1);
    //var insert = true;
    $(".checkbox-toggle").click(function () {
      var clicks = $(this).data('clicks');
      if (clicks) {
        //Uncheck all checkboxes
        $(".mailbox-messages input[type='checkbox']").iCheck("uncheck");
        $(".fa", this).removeClass("fa-check-square-o").addClass('fa-square-o');
      } else {
        //Check all checkboxes
        $(".mailbox-messages input[type='checkbox']").iCheck("check");
        $(".fa", this).removeClass("fa-square-o").addClass('fa-check-square-o');
      }
      $(this).data("clicks", !clicks);
    });
    $('#ex-thamdinhhoso').html('');
    //loadComboxNamHoc();
   // permission(function(){
        var html_view  = '';
        
        // if(check_Permission_Feature('1')){
        html_view += '<button type="button" onclick="myModalTongDanhSach()" class="btn btn-success" id ="btnSaves"><i class="glyphicon glyphicon-pushpin"></i> Tổng hợp danh sách</button> ';
        // }
        html_view += '<button type="button" class="btn btn-primary" onclick="reset()"><i class="glyphicon glyphicon-refresh"></i> Làm mới</button>';
      //  if(check_Permission_Feature('5')){
            // html_view += '<button type="submit" class="btn btn-info" id ="btnSave"><i class="glyphicon glyphicon-send"></i> Gửi danh sách</button>';
      // }
        $('#ex-thamdinhhoso').html(html_view);
    //});
    $('#checkAllId').change(function() {
            if ($('#checkAllId').prop('checked'))
                $('[id*="idCheck"]').prop('checked', true);
            else
                $('[id*="idCheck"]').prop('checked', false);
    });
    delBanGhi = function (id) {
        utility.confirm("Xóa bản ghi?", "Bạn có chắc chắn muốn xóa?", function () {
            insertUpdate(1);
            $.ajax({
                    type: "GET",
                    url: 'delId/'+id,
                    success: function(dataget) {
                        if(dataget.success != null || dataget.success != undefined){
                        $("#myModal").modal("hide");
                        utility.message("Thông báo",dataget.success,null,3000)
                        GET_INITIAL_NGHILC();
                        loadKinhPhiDoiTuong($('select#viewTableDT').val());
                    }else if(dataget.error != null || dataget.error != undefined){
                        //$("#myModal").modal("hide");
                        utility.message("Thông báo",dataget.error,null,3000, 1)
                        //insertUpdate(1);
                        //loadKinhPhiDoiTuong($('select#viewTableDT').val()); 
                    }
                        
                    }, error: function(dataget) {
                }
            });
        });
        
    }

//--------------------------------------------------Download file danh sách cần phê duyệt------------------------------------------------
    down_file_pheduyet = function (id) {
        window.open('/ho-so/tham-dinh/downfileExcelpheduyet/' + id, '_blank');
    }

    taive = function (id) {
            window.open('/ho-so/tham-dinh/taive/'+id, '_blank');
            $.ajax({
                    type: "GET",
                    url: '/ho-so/tham-dinh/taive/'+id,
                    success: function(dataget) {
                        $('#group_mghp').html(dataget);
                    }, error: function(dataget) {
                }
            });
    }

    downloads = function (id) {
            window.open('/ho-so/tham-dinh/download/'+id, '_blank');
            $.ajax({
                    type: "GET",
                    url: '/ho-so/tham-dinh/download/'+id,
                    success: function(dataget) {
                        $('#group_mghp').html(dataget);
                    }, error: function(dataget) {
                }
            });
    }
    updateBanGhi = function (id) {
        //alert(check_Permission_Feature('3'));
        $.ajax({
                type: "GET",
                url: 'getId/'+id,
                success: function(dataget) {
                   if(dataget.length >0){
                        insertUpdate(0);
                        insert=false;
                        $("#btnSaveKinhPhiDoiTuong").html('<i class="glyphicon glyphicon-edit"></i> Cập nhật');
                        $("#txtCodeKinhPhi1").attr('disabled','disabled');
                        $('#txtIdKinhPhi').val(dataget[0].id);
                        $('#txtCodeKinhPhi1').val(dataget[0].code);
                        $("#sltSubject option[value='" + dataget[0].doituong_id + "']").attr('selected', 'selected');
                        $("#sltTruongDt option[value='" + dataget[0].idTruong + "']").attr('selected', 'selected');
                        $("#sltSubject").val(dataget[0].doituong_id).change();
                        $("#sltTruongDt").val(dataget[0].idTruong).change();
                        //$("#sltSubject option[value='" + dataget[0].doituong_id + "']").attr('selected', 'selected');
                        $('#txtMoney1').val(dataget[0].money);
                        $('#datepicker1').datepicker('setDate', new Date(data[0].start_date));
                        $('#datepicker2').datepicker('setDate', new Date(data[0].end_date));
                   }
                }, error: function(dataget) {
                }
            });
    }
    $('select#viewTableDT').change(function() {
       GET_INITIAL_NGHILC();
       loadKinhPhiDoiTuong($(this).val());
    });
    //autocompleteSearch("txtSearchDT");
    //$('a#btnInsertKinhPhiDoiTuong').click(function(){
    btnInsertKinhPhiDoiTuong = function(){    
        insertUpdate(0);
        insert=true;
        $("#btnResetKinhPhiDoiTuong").show();
        $("#btnSaveKinhPhiDoiTuong").html('<i class="glyphicon glyphicon-plus-sign"></i> Lưu');
    };
    $('button#thamdinhClick').click(function(){
        sendthamdinh($('#txtIdthamdinh').val());
    });
    $('button#saveTotal').click(function(){;
        if($('#txtNameDS').val()!=""){
            if($('#txtNguoiLap').val()!=""){
                if($('#txtNguoiKy').val()!=""){
                    var lstId = null;
                    var check = false;
                    $("input#idCheck").each(function () {
                        if ($(this).is(':checked')) {
                            if (!check) {
                                lstId = "";
                            }
                            check = true;
                            var customerId = $(this).val();
                            lstId += customerId + "-";
                        }
                    });
        
                    if (lstId !== null && lstId !== "") {
                        lstId = lstId.substring(0, lstId.length - 1);

                        var file_data = $('input#exampleInputFile').prop('files')[0];   
                        var form_datas = new FormData();   
                        form_datas.append('file', file_data);
                        form_datas.append('type', $('#sltType').val());
                        form_datas.append('nam_hoc', $('#sltYear').val());
                        form_datas.append('name', $('#txtNameDS').val());
                        form_datas.append('create_name', $('#txtNguoiLap').val());
                        form_datas.append('create_sign', $('#txtNguoiKy').val());
                        form_datas.append('status', $('#sltStatus').val());
                        form_datas.append('note', $('#txtGhiChu').val());
                        form_datas.append('list', lstId);
                        insertDataTotal(form_datas,$('#sltType').val());
                    }else{
                        utility.messagehide('group_mghp','Xin mời chọn danh sách để tổng hợp',1,3000);
                    }
                }else{
                    utility.messagehide('group_mghp','Xin mời nhập tên người ký',1,3000);
                    $('#txtNguoiKy').focus();
                }
            }else{
                utility.messagehide('group_mghp','Xin mời nhập tên người lập danh sách',1,3000);
                $('#txtNguoiLap').focus();
            }
        }else{
            utility.messagehide('group_mghp','Xin mời nhập tên danh sách',1,3000);
            $('#txtNameDS').focus();
        }   
    });
    $('button#chuyenlaiClick').click(function(){
        resendthamdinh($('#txtIdthamdinh').val());
    });

    $('#btnPheDuyet').click(function(){
        var pheduyet_id = $('#txtPheDuyet_ID').val();

        var form_datas = new FormData();   
        form_datas.append('id', pheduyet_id);
        form_datas.append('note', $('#txtNoteSend').val());
        form_datas.append('file', $('#txtFile_attack').prop('files')[0]);
        form_datas.append('noteapproved', $('#txtNoteApproved').val());
        form_datas.append('fileapproved', $('#txtFile_attack_approved').prop('files')[0]);

        updatePheDuyet(form_datas);
    });
    
    $('#btnRevertPheDuyet').click(function(){
        var pheduyet_id = $('#txtPheDuyet_ID').val();

        var form_datas = new FormData();   
        form_datas.append('id', pheduyet_id);
        form_datas.append('note', $('#txtNoteSend').val());
        form_datas.append('file', $('#txtFile_attack').prop('files')[0]);

        revertPheDuyet(form_datas);
    });
  
  });
function insertDataTotal(temp,type) {
    loading();
    var url = "";
            $.ajax({
                type: "POST",
                url: '/ho-so/tham-dinh/ho-so-duyet/insert',
                data: temp,//JSON.stringify(temp),
               // dataType: 'json',
                contentType: false,//'application/json; charset=utf-8',
                cache: false,             // To unable request pages to be cached
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {
                    if(dataget.success != null || dataget.success != undefined){
                        $("#myModalTongDanhSach").modal("hide");
                        utility.message("Thông báo",dataget.success,null,3000)
                        insertUpdateTotal(1);
                        GET_INITIAL_NGHILC();
                        loaddataTotal($('#view-tonghopdanhsach').val());
                        closeLoading();
                        // loadKinhPhiDoiTuong($('select#viewTableDT').val());
                    }else if(dataget.error != null || dataget.error != undefined){
                        $("#myModalTongDanhSach").modal("hide");
                        utility.message("Thông báo",dataget.error,null,5000, 1)
                        closeLoading();
                        //insertUpdate(1);
                        //loadKinhPhiDoiTuong($('select#viewTableDT').val()); 
                    }
                    // utility.message("Thông báo","Lưu bản ghi thành công",null,5000)
                }, error: function(dataget) {
                    closeLoading();
                }
            });
        };
function loadDaThamDinh(row,key,callback) {
    
    var html_show = "";
    var o = {};
    if(key!=null){
        o = {
                key : key,
                start: (GET_START_RECORD_NGHILC()),
                limit : row,
                nam_hoc : $('#sltYear').val(),
                ho_so : $('#sltType').val()
            };
    }else{
        o = {
                start: (GET_START_RECORD_NGHILC()),
                limit : row,
                nam_hoc : $('#sltYear').val(),
                ho_so : $('#sltType').val()
            };
    }
    //console.log(JSON.stringify(o));
            $.ajax({
                type: "POST",
                url: 'load/verify',
                data: JSON.stringify(o),
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {

                    SETUP_PAGING_NGHILC(dataget, function () {
                        loadDaThamDinh(row,key);
                    });
                    $('#dataThamDinh').html("");
                    data = dataget.data;
                    //permission = dataget.permission;
                    console.log(data);
                    if(data.length > 0){
                        for (var i = 0; i < data.length; i++) {
                            html_show += "<tr><td class='text-center' style='width:3%'><label>"+(i + 1)+"</label></td>";
                            // html_show += "<tr><td style='width:3%' >"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            html_show += "<td class='text-center'><input value='"+data[i].thamdinh_id+"' id='idCheck' type='checkbox' /></td>"
                            html_show += '<td class="mailbox-name" style="width:25%">'+ConvertString(data[i].report_name)+'</td>';
                            if(parseInt(data[i].report_nature) === 0){

                                    html_show += '<td class="mailbox-star" style="width:3%"><a title="Đã duyệt"  href="#"><i class="fa fa-star text-green"></i> Bình thường</a></td>';
                                
                            }else{
                                
                                    html_show += '<td class="mailbox-star" style="width:3%"><a title="Đã duyệt"  href="#"><i class="fa fa-star-o text-green"></i> Khẩn cấp</a></td>';
                                
                            }
                            
                            html_show += '<td class="mailbox-name" style="width:25%">'+ConvertString(data[i].thamdinh_nguoiduyet)+'</td>';
                            html_show += '<td class="mailbox-name" style="width:25%">'+ConvertString(data[i].schools_name)+'</td>';
                            html_show += '<td class="mailbox-name" style="width:25%">'+ConvertString(data[i].thamdinh_file_dikem)+'</td>';
                            html_show += "</tr>";
                        }
                        
                    } else {
                        html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                    }
                    $('#dataThamDinh').html(html_show);
                    if(callback!= null){
                        callback();
                    }
                }, error: function(dataget) {

                }
            });
        };
function myModalTongDanhSach(){
    if($('#sltYear').val() === ''){
        utility.messagehide("warning_msg","Xin mời chọn năm học", 1, 5000);
    }else{
        if($('#sltType').val() === ''){
            utility.messagehide("warning_msg","Xin mời chọn loại danh sách", 1, 5000);
        }else{
            loadDaThamDinh($('#viewTableDT').val(),null,function(){
                $("#txtNameDS").val('');
                $("#txtNguoiLap").val('');
                $("#txtNguoiKy").val('');
                $("#txtGhiChu").val('');
                $('#checkAllId').prop('checked', false);
                $("#myModalTongDanhSach").modal("show");
            });
            
        }
    }
    
   }
function loadComboxNamHoc() {
            $.ajax({
                type: "GET",
                url: '/danh-muc/load/nam-hoc',
                success: function(dataget) {
                    $('#sltYear').html("");
                    var html_show = "";
                    if(dataget.length >0){
                      //  $.fn.dataTable.render.number( '.', ',', 0, '' ) 
                        html_show += "<option selected='selected' value=''>-- Chọn năm học --</option>";
                        for (var i = dataget.length - 1; i >= 0; i--) {
                            html_show += "<option value='"+dataget[i].code+"'>"+dataget[i].name+"</option>";
                        }
                        $('#sltYear').html(html_show);
                    }else{
                        $('#sltYear').html("<option value=''>-- Chưa có năm học --</option>");
                    }
                }, error: function(dataget) {
                }
            });
        };

var CODE_FEATURES ;
function permission(callback) {
            $.ajax({
                type: "GET",
                url: 'permission/info',
                success: function(dataget) {
                    CODE_FEATURES = dataget.permission;
                    if(callback!=null){
                        callback();
                    }
                }, error: function(dataget) {
                }
            });
        };
function check_Permission_Feature(featureCode) {
    try {
        if (Object.values(CODE_FEATURES).indexOf(featureCode) >=0) {
            return true;
        }
        //if (MyApp.userInformation.get('CODE_FEATURES').indexOf(featureCode) >= 0)
            
        return false;
    } catch (e) {
        console.log(e);
    }
    return true;
}

function view_inbox(id){
    $('#txtNoteSend').val('');
    $('#txtFile_attack').filestyle('clear');
    $.ajax({
                type: "GET",
                url: '/ho-so/tham-dinh/view/'+id,
                success: function(dataget) {
                   if(dataget.length >0){
                    $('#txtIdthamdinh').val(dataget[0].thamdinh_id);
                    if(dataget[0].thamdinh_type === 'MGHP'){
                        $('.modal-title').html('Danh sách miễn giảm học phí');
                    }
                    if(dataget[0].thamdinh_type === 'CPHT'){
                        $('.modal-title').html('Danh sách hỗ trợ chi phí học tập');
                    }
                    if(dataget[0].thamdinh_type === 'HTAT'){
                        $('.modal-title').html('Danh sách hỗ trợ ăn trưa');
                    }
                    if(dataget[0].thamdinh_type === 'HTBT'){
                        $('.modal-title').html('Danh sách hỗ trợ bán trú');
                    }
                    if(dataget[0].thamdinh_type === 'NGNA'){
                        $('.modal-title').html('Danh sách hỗ trợ người nấu ăn');
                    }
                    if(dataget[0].thamdinh_type === 'HSDTTS'){
                        $('.modal-title').html('Danh sách hỗ trợ học sinh dân tộc thiểu số tại huyện Mù Cang Chải và Trạm Tấu');
                    }
                    if(dataget[0].thamdinh_type === 'HSKT'){
                        $('.modal-title').html('Danh sách hỗ trợ học sinh khuyết tật');
                    }
                    if(dataget[0].thamdinh_type === 'TONGHOP'){
                        $('.modal-title').html('Danh sách hỗ trợ ưu đãi học sinh mẫu giáo, tiểu học và phổ thông trung học');
                    }
                    $('#title-vanban').html(dataget[0].report_name);
                    $('.mailbox-read-time').html(dataget[0].thamdinh_ngaygui);
                    $('#no-vanban').html('<a>'+dataget[0].thamdinh_name+'</a>');
                    $('#no-file-load').html('&nbsp;&nbsp;<i class="fa fa-file-excel-o"></i>  <a style="font-style: italic;"> '+dataget[0].thamdinh_file_dinhkem+'</a> ( <a href="#" onclick="taive('+dataget[0].thamdinh_id+')" ><i class="fa fa-download"></i> Tải về</a>)');
                    $('#address-send').html('<a>'+dataget[0].schools_name+'</a>');
                    //if(dataget[0].thamdinh_type === 'MGHP'){
                        // $('#content-short').html('<a>'+dataget[0].thamdinh_content+'</a>');
                    //}
                    if(parseInt(dataget[0].report_nature) === 0){
                        $('#status').html('<a>Bình thường</a>');   
                    }else{
                        $('#status').html('<a>Khẩn cấp</a>');   
                    }
                    $('#file-attach').html('<label style="font-weight: 500"><i class="fa fa-download"></i>  <a href="#" onclick="downloads('+dataget[0].thamdinh_id+')" style="font-style: italic;"> '+dataget[0].thamdinh_file_dikem+'</a> </label> ');   
                    $('#user-thamdinh').html('<a>'+dataget[0].thamdinh_nguoiduyet+'</a>');   
                    $('#date-thamdinh').html('<a>'+dataget[0].thamdinh_ngayduyet+'</a>');   
                    if(parseInt(dataget[0].thamdinh_trangthai) === 0){
                        $('#thamdinh-set').html('<a>Chưa phê duyệt</a>');   
                    }else if(parseInt(dataget[0].thamdinh_trangthai) === 1){
                        $('#thamdinh-set').html('<a>Đã phê duyệt</a>');
                    }else{
                        $('#thamdinh-set').html('<a>Đã chuyển lại</a>');  
                    }

                    var status = dataget[0].thamdinh_trangthai;
                    if (parseInt(status) == 1 || parseInt(status) == 2) {
                        $('#thamdinhClick').attr('disabled', 'disabled');
                        $('#chuyenlaiClick').attr('disabled', 'disabled');
                    }
                    else{
                        $('#thamdinhClick').removeAttr('disabled');
                        $('#chuyenlaiClick').removeAttr('disabled');
                    }

                    $("#myModal").modal("show");
                    loadInbox($('#viewthamdinh').val());
                   }
                }, error: function(dataget) {
                }
            });
   }

//View Inbox Thẩm định---------------------------------------------------------------------------------------------------------------------------------
function view_inbox_PheDuyet(id){
    $('#txtNoteSend').val('');
    $('#txtNoteApproved').val('');
    $('#txtFile_attack').filestyle('clear');
    $('#txtFile_attack_approved').filestyle('clear');
    
    $.ajax({
                type: "GET",
                url: 'viewPheDuyet/'+id,
                success: function(dataget) {
                   if(dataget.length >0){
                    $('#txtPheDuyet_ID').val(dataget[0].pheduyet_id);
                    if(dataget[0].type === 'MGHP'){
                        $('.modal-title').html('Danh sách hỗ trợ miễn giảm học phí');
                    }
                    if(dataget[0].type === 'CPHT'){
                        $('.modal-title').html('Danh sách hỗ trợ chi phí học tập');
                    }
                    if(dataget[0].type === 'HTAT'){
                        $('.modal-title').html('Danh sách hỗ trợ ăn trưa');
                    }
                    if(dataget[0].type === 'HTBT'){
                        $('.modal-title').html('Danh sách hỗ trợ bán trú');
                    }
                    if(dataget[0].type === 'NGNA'){
                        $('.modal-title').html('Danh sách hỗ trợ người nấu ăn');
                    }
                    if(dataget[0].type === 'HSDTTS'){
                        $('.modal-title').html('Danh sách hỗ trợ học sinh dân tộc thiểu số tại huyện Mù Cang Chải và Trạm Tấu');
                    }
                    if(dataget[0].type === 'HSKT'){
                        $('.modal-title').html('Danh sách hỗ trợ học sinh khuyết tật');
                    }
                    if(dataget[0].type === 'TONGHOP'){
                        $('.modal-title').html('Danh sách hỗ trợ ưu đãi học sinh mẫu giáo, tiểu học và phổ thông trung học');
                    }
                    $('#title-vanban').html(dataget[0].pheduyettonghop_name);
                    $('.mailbox-read-time').html(dataget[0].pheduyet_ngaygui);
                    $('#no-vanban').html('<a>'+dataget[0].pheduyet_name+'</a>');
                    $('#no-file-load').html('&nbsp;&nbsp;<i class="fa fa-file-excel-o"></i>  <a style="font-style: italic;"> '+dataget[0].pheduyet_type+'</a> ( <a href="#" onclick="down_file_pheduyet('+dataget[0].pheduyet_id+')" ><i class="fa fa-download"></i> Tải về </a>)');
                    $('#address-send').html('<a>'+dataget[0].pheduyet_nguoigui+'</a>');
                    //if(dataget[0].thamdinh_type === 'MGHP'){
                        // $('#content-short').html('<a>'+dataget[0].pheduyet_ghichu+'</a>');
                    //}
                    if(parseInt(dataget[0].pheduyettonghop_dinhkem) === 0){
                        $('#status').html('<a>Bình thường</a>');   
                    }else{
                        $('#status').html('<a>Khẩn cấp</a>');   
                    }
                    $('#file-attach').html('<label style="font-weight: 500"><i class="fa fa-download"></i>  <a href="#" onclick="downloads('+dataget[0].pheduyet_id+')" style="font-style: italic;"> '+dataget[0].pheduyet_file_dikem+'</a> </label> ');   
                    $('#user-thamdinh').html('<a>'+dataget[0].pheduyet_nguoiduyet+'</a>');   
                    $('#date-thamdinh').html('<a>'+dataget[0].pheduyet_ngayduyet+'</a>');   
                    if(parseInt(dataget[0].pheduyet_trangthai) === 0){
                        $('#thamdinh-set').html('<a>Chưa thẩm định</a>');   
                    }else if(parseInt(dataget[0].pheduyet_trangthai) === 1){
                        $('#thamdinh-set').html('<a>Đã thẩm định</a>');   
                    }else{
                        $('#thamdinh-set').html('<a>Đã chuyển lại</a>');  
                    }

                    $('#file-attach-approved').html('<label style="font-weight: 500"><i class="fa fa-download"></i>  <a href="#" onclick="download_file_approved_pheduyet('+dataget[0].pheduyettonghop_id+')" style="font-style: italic;"> '+dataget[0].pheduyettonghop_file_approved+'</a> </label> ');

                    $('#txtNoteSend').val(dataget[0].pheduyet_note);
                    $('#txtNoteApproved').val(dataget[0].pheduyet_note_approved);

                    var status = dataget[0].pheduyet_trangthai;
                    if (parseInt(status) == 1 || parseInt(status) == 3) {
                        $('#btnPheDuyet').attr('disabled', 'disabled');
                        $('#btnRevertPheDuyet').attr('disabled', 'disabled');
                    }
                    else{
                        $('#btnPheDuyet').removeAttr('disabled');
                        $('#btnRevertPheDuyet').removeAttr('disabled');
                    }

                    $("#myModal").modal("show");
                    loadInboxPheDuyet($('#viewpheduyet').val());
                   }
                }, error: function(dataget) {
                }
            });
   }

function updatePheDuyet(objData){
    $.ajax({
        type: "POST",
        url:'updatetpheduyet',
        data: objData,
        contentType: false,//'application/json; charset=utf-8',
        cache: false,             // To unable request pages to be cached
        processData: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
        },
        success: function(dataget) {
            if(dataget.success != null || dataget.success != undefined){
                $("#myModal").modal("hide");
                utility.message("Thông báo",dataget.success,null,3000)
                GET_INITIAL_NGHILC();
                loadInboxPheDuyet($('#viewpheduyet').val());
            }else if(dataget.error != null || dataget.error != undefined){
                utility.message("Thông báo",dataget.error,null,5000, 1)
            }
        }, error: function(dataget) {
        }
    });
}

function revertPheDuyet(objData){
    $.ajax({
        type: "POST",
        url:'revertpheduyet',
        data: objData,
        contentType: false,//'application/json; charset=utf-8',
        cache: false,             // To unable request pages to be cached
        processData: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
        },
        success: function(dataget) {
            if(dataget.success != null || dataget.success != undefined){
                $("#myModal").modal("hide");
                utility.message("Thông báo",dataget.success,null,3000)
                GET_INITIAL_NGHILC();
                loadInboxPheDuyet($('#viewpheduyet').val());
            }else if(dataget.error != null || dataget.error != undefined){
                utility.message("Thông báo",dataget.error,null,5000, 1)
            }
        }, error: function(dataget) {
        }
    });
}

//------------------------------------------------------------Load data tổng hợp-------------------------------------------------------------
function loaddataTotal(row) {

    var html_show = "";
    var o = {
        start: (GET_START_RECORD_NGHILC()),
        limit : row
    };
            $.ajax({
                type: "POST",
                url: '/ho-so/tham-dinh/loadtotal',
                data: JSON.stringify(o),
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(datas) {
                    SETUP_PAGING_NGHILC(datas, function () {
                        loaddataTotal(row);
                    });
                    $('#dataTotal').html("");
                    var dataget = datas.data;
                    if(dataget.length>0){
                        for (var i = 0; i < dataget.length; i++) {
                            html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            html_show += "<td><a href='javascript:;' onclick='export_files("+dataget[i].pheduyettonghop_id+");'>"+dataget[i].pheduyettonghop_name+"</a></td>";
                            html_show += "<td>"+dataget[i].pheduyettonghop_nguoilap+"</td>";
                            if(parseInt(dataget[i].pheduyettonghop_type) === 0){
                               html_show += "<td class='text-center'>Bình thường</td>";
                            }else{
                                html_show += "<td class='text-center'>Khẩn cấp</td>";
                            }
                            //alert(dataget[i].status);
                            if(parseInt(dataget[i].pheduyettonghop_status) === 0){
                               html_show += "<td class='text-center'>Chưa gửi</td>";
                            }else if(parseInt(dataget[i].pheduyettonghop_status) === 1){
                               html_show += "<td class='text-center'>Đã gửi</td>";
                            }
                            else if(parseInt(dataget[i].pheduyettonghop_status) === 2){
                                html_show += "<td class='text-center'>Chuyển lại</td>";
                            }
                            else if(parseInt(dataget[i].pheduyettonghop_status) === 3){
                                html_show += "<td class='text-center'>Đã thẩm định</td>";
                            }
                            html_show += "<td class='text-center'><a href='javascript:;' onclick='download_attach_pheduyettonghop("+dataget[i].pheduyettonghop_id+");'>"+dataget[i].pheduyettonghop_dinhkem+"</a></td>";
                            html_show += "<td class='text-center'>";
                            if(check_Permission_Feature('5')){
                                if(parseInt(dataget[i].pheduyettonghop_status) == 0){
                                    html_show += "<button  class='btn btn-info btn-xs' onclick='openPopupSendPH("+dataget[i].pheduyettonghop_id+")'><i class='glyphicon glyphicon-send'></i> Gửi</button> ";
                                    if(check_Permission_Feature('3')===true){
                                        html_show += "<button  onclick='del_report_pheduyettonghop("+dataget[i].pheduyettonghop_id+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                                    }
                                }else if(parseInt(dataget[i].pheduyettonghop_status) == 1){
                                    html_show += "<button  class='btn btn-primary btn-xs' > Chờ thầm định</button> ";
                                }else if(parseInt(dataget[i].pheduyettonghop_status) == 2){
                                    html_show += "<button onclick='loadPopupReverts("+dataget[i].pheduyettonghop_id+");' class='btn btn-warning btn-xs' > Trả lại</button> ";
                                    // if(check_Permission_Feature('3')===true){
                                    //     html_show += "<button  onclick='del_report_mghp("+dataget[i].pheduyettonghop_id+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                                    // }
                                }else {
                                    html_show += "<button onclick='loadPopupReverts("+dataget[i].pheduyettonghop_id+");' class='btn btn-success btn-xs' > Đã thẩm định </button> ";
                                }
                            }
                        html_show += "</td></tr>";
                    }                                          
                }
                else {
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }
            $('#dataTotal').html(html_show);
        }, error: function(dataget) {

        }
    });
};

//------------------------------------------------------------Load Inbox thẩm định-------------------------------------------------------------
function loadInboxPheDuyet(row, key) {
    
    var html_show = "";
    var o = {};
    if(key!=null){
        o = {
                key : key,
                start: (GET_START_RECORD_NGHILC()),
                limit : row
            };
    }else{
        o = {
                start: (GET_START_RECORD_NGHILC()),
                limit : row
            };
    }
            $.ajax({
                type: "POST",
                url: '/ho-so/duyet-danh-sach/load/verify',
                data: JSON.stringify(o),
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {

                    //console.log();

                    SETUP_PAGING_NGHILC(dataget, function () {
                        loadInboxPheDuyet(row,key);
                    });
                    $('#inbox-thamdinh-pheduyet').html("");
                    data = dataget.data;
                    //console.log(data);
                    //permission = dataget.permission;
                    if(data.length>0){
                        for (var i = 0; i < data.length; i++) {
                            // html_show += "<tr><td style='width:3%' ><input type='checkbox' value='"+data[i].pheduyet_id+"'></td>";
                            html_show += "<tr><td style='width:3%' class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            if(parseInt(data[i].pheduyettonghop_nature) === 0){
                                if(parseInt(data[i].pheduyet_trangthai) === 0){
                                    html_show += '<td class="mailbox-star" style="width:3%"><a title="Chưa thẩm định" onclick="view_inbox_PheDuyet('+data[i].pheduyet_id+')" href="#"><i class="fa fa-star text-yellow"></i></a></td>';
                                }else if(parseInt(data[i].pheduyet_trangthai) === 1){
                                    html_show += '<td class="mailbox-star" style="width:3%"><a title="Đã thẩm định" onclick="view_inbox_PheDuyet('+data[i].pheduyet_id+')" href="#"><i class="fa fa-star text-green"></i></a></td>';
                                }else{
                                    html_show += '<td class="mailbox-star" style="width:3%"><a title="Chuyển lại" onclick="view_inbox_PheDuyet('+data[i].pheduyet_id+')" href="#"><i class="fa fa-reply text-yellow"></i></a></td>';
                                }
                            }else{
                                if(parseInt(data[i].pheduyet_trangthai) === 0){
                                    html_show += '<td class="mailbox-star" style="width:3%"><a title="Chưa thẩm định" onclick="view_inbox_PheDuyet('+data[i].pheduyet_id+')" href="#"><i class="fa fa-star-o text-yellow"></i></a></td>';
                                }else if(parseInt(data[i].pheduyet_trangthai) === 1){
                                    html_show += '<td class="mailbox-star" style="width:3%"><a title="Đã thẩm định" onclick="view_inbox_PheDuyet('+data[i].pheduyet_id+')" href="#"><i class="fa fa-star-o text-green"></i></a></td>';
                                }else{
                                    html_show += '<td class="mailbox-star" style="width:3%"><a title="Chuyển lại" onclick="view_inbox_PheDuyet('+data[i].pheduyet_id+')" href="#"><i class="fa fa-reply text-yellow"></i></a></td>';
                                }
                            }
                            if(parseInt(data[i].pheduyet_trangthai) === 0){
                                html_show += '<td class="mailbox-star" style="width:7%"><i class="text-yellow">Chưa thẩm định</i></td>';
                            }else if(parseInt(data[i].pheduyet_trangthai) === 1){
                                html_show += '<td class="mailbox-star" style="width:7%"><i class="text-green">Đã thẩm định</i></td>';
                            }else{
                                html_show += '<td class="mailbox-star" style="width:7%"><i class="text-yellow">Chuyển lại</i></td>';
                            }
                            html_show += '<td class="mailbox-name" style="width:25%"><a onclick="view_inbox_PheDuyet('+data[i].pheduyet_id+')" href="#">'+data[i].pheduyettonghop_name+'</a></td>';
                            
                            if(data[i].type === 'MGHP' ){
                                if(parseInt(data[i].pheduyet_view) === 0){
                                    html_show += '<td class="mailbox-subject" style="width:25%"><b>Miễn giảm học phí</b></td>';
                                }else{
                                    html_show += '<td class="mailbox-subject" style="width:25%">Miễn giảm học phí</td>';
                                }
                            }
                            if(data[i].type === 'CPHT' ){
                                if(parseInt(data[i].pheduyet_view) === 0){
                                    html_show += '<td class="mailbox-subject" style="width:25%"><b>Hỗ trợ chi phí học tập</b></td>';
                                }else{
                                    html_show += '<td class="mailbox-subject" style="width:25%">Hỗ trợ chi phí học tập</td>';
                                }
                            }
                            if(data[i].type === 'HTAT' ){
                                if(parseInt(data[i].pheduyet_view) === 0){
                                    html_show += '<td class="mailbox-subject" style="width:25%"><b>Hỗ trợ ăn trưa trẻ em mẫu giáo</b></td>';
                                }else{
                                    html_show += '<td class="mailbox-subject" style="width:25%">Hỗ trợ ăn trưa trẻ em mẫu giáo</td>';
                                }
                            }
                            if(data[i].type === 'HTBT' ){
                                if(parseInt(data[i].pheduyet_view) === 0){
                                    html_show += '<td class="mailbox-subject" style="width:25%"><b>Hỗ trợ học sinh bán trú</b></td>';
                                }else{
                                    html_show += '<td class="mailbox-subject" style="width:25%">Hỗ trợ học sinh bán trú</td>';
                                }
                            }
                            if(data[i].type === 'NGNA' ){
                                if(parseInt(data[i].pheduyet_view) === 0){
                                    html_show += '<td class="mailbox-subject" style="width:25%"><b>Hỗ trợ người nấu ăn</b></td>';
                                }else{
                                    html_show += '<td class="mailbox-subject" style="width:25%">Hỗ trợ người nấu ăn</td>';
                                }
                            }
                            if(data[i].type === 'HSKT' ){
                                if(parseInt(data[i].pheduyet_view) === 0){
                                    html_show += '<td class="mailbox-subject" style="width:25%"><b>Hỗ trợ học sinh khuyết tật</b></td>';
                                }else{
                                    html_show += '<td class="mailbox-subject" style="width:25%">Hỗ trợ học sinh khuyết tật</td>';
                                }
                            }
                            if(data[i].type === 'HSDTTS' ){
                                if(parseInt(data[i].pheduyet_view) === 0){
                                    html_show += '<td class="mailbox-subject" style="width:25%"><b>Hỗ trợ học sinh dân tộc thiểu số tại huyện Mù Cang Chải và trạm Tấu</b></td>';
                                }else{
                                    html_show += '<td class="mailbox-subject" style="width:25%">Hỗ trợ học sinh dân tộc thiểu số tại huyện Mù Cang Chải và trạm Tấu</td>';
                                }
                            }
                            if(data[i].type === 'TONGHOP' ){
                                if(parseInt(data[i].pheduyet_view) === 0){
                                    html_show += '<td class="mailbox-subject" style="width:25%"><b>Chính sách ưu đãi dành cho trẻ em mẫu giáo, học sinh tiểu học và phổ thông trung học</b></td>';
                                }else{
                                    html_show += '<td class="mailbox-subject" style="width:25%">Chính sách ưu đãi dành cho trẻ em mẫu giáo, học sinh tiểu học và phổ thông trung học</td>';
                                }
                            }

                            if((data[i].pheduyet_user_view) !== '' && (data[i].pheduyet_user_view) !== undefined){
                                html_show += "<td class='text-right' style='font-style: italic;width:25%'>"+data[i].pheduyet_user_view+" xem cuối</td>";
                            }else{
                                html_show += "<td class='text-right' style='font-style: italic;width:25%'>---</td>";
                            }
                            html_show += "<td class='text-right' style='width:25%'>"+data[i].pheduyet_ngaygui+"</td>";
                            html_show += "</tr>";
                        }
                        
                    } else {
                        html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                    }
                    $('#inbox-thamdinh-pheduyet').html(html_show);                    
                }, error: function(dataget) {

                }
            });
        };

//---------------------------------------------------------Load Inbox phê duyệt---------------------------------------------------------
function loadInbox(row,key) {
    
    var html_show = "";
    var o = {};
    if(key!=null){
        o = {
                key : key,
                start: (GET_START_RECORD_NGHILC()),
                limit : row
            };
    }else{
        o = {
                start: (GET_START_RECORD_NGHILC()),
                limit : row
            };
    }
            $.ajax({
                type: "POST",
                url: '/ho-so/tham-dinh/load',
                data: JSON.stringify(o),
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {

                    SETUP_PAGING_NGHILC(dataget, function () {
                        loadInbox(row,key);
                    });
                    $('#inbox-thamdinh').html("");
                    data = dataget.data;
                    //permission = dataget.permission;
                    if(data.length>0){
                        for (var i = 0; i < data.length; i++) {

                            html_show += "<tr><td style='width:3%' class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            if(parseInt(data[i].report_nature) === 0){
                                if(parseInt(data[i].thamdinh_trangthai) === 0){
                                    html_show += '<td class="mailbox-star" style="width:3%"><a title="Chưa duyệt" onclick="view_inbox('+data[i].thamdinh_id+')" href="#"><i class="fa fa-star text-yellow"></i></a></td>';
                                }else if(parseInt(data[i].thamdinh_trangthai) === 1){
                                    html_show += '<td class="mailbox-star" style="width:3%"><a title="Đã duyệt" onclick="view_inbox('+data[i].thamdinh_id+')" href="#"><i class="fa fa-star text-green"></i></a></td>';
                                }else{
                                    html_show += '<td class="mailbox-star" style="width:3%"><a title="Chuyển lại" onclick="view_inbox('+data[i].thamdinh_id+')" href="#"><i class="fa fa-reply text-yellow"></i></a></td>';
                                }
                            }else{
                                if(parseInt(data[i].thamdinh_trangthai) === 0){
                                    html_show += '<td class="mailbox-star" style="width:3%"><a title="Chưa duyệt" onclick="view_inbox('+data[i].thamdinh_id+')" href="#"><i class="fa fa-star-o text-yellow"></i></a></td>';
                                }else if(parseInt(data[i].thamdinh_trangthai) === 1){
                                    html_show += '<td class="mailbox-star" style="width:3%"><a title="Đã duyệt" onclick="view_inbox('+data[i].thamdinh_id+')" href="#"><i class="fa fa-star-o text-green"></i></a></td>';
                                }else{
                                    html_show += '<td class="mailbox-star" style="width:3%"><a title="Chuyển lại" onclick="view_inbox('+data[i].thamdinh_id+')" href="#"><i class="fa fa-reply text-yellow"></i></a></td>';
                                }
                            }
                            if(parseInt(data[i].thamdinh_trangthai) === 0){
                                html_show += '<td class="mailbox-star" style="width:7%"><i class="text-yellow">Chưa duyệt</i></td>';
                            }else if(parseInt(data[i].thamdinh_trangthai) === 1){
                                html_show += '<td class="mailbox-star" style="width:7%"><i class="text-green">Đã duyệt</i></td>';
                            }else{
                                html_show += '<td class="mailbox-star" style="width:7%"><i class="text-yellow">Chuyển lại</i></td>';
                            }
                            html_show += '<td class="mailbox-name" style="width:25%"><a onclick="view_inbox('+data[i].thamdinh_id+')" href="#">'+data[i].report_name+'</a></td>';
                            
                            if(data[i].thamdinh_type === 'MGHP' ){
                                if(parseInt(data[i].thamdinh_view) === 0){
                                    html_show += '<td class="mailbox-subject" style="width:25%"><b>Hỗ trợ miễn giảm học phí</b></td>';
                                }else{
                                    html_show += '<td class="mailbox-subject" style="width:25%"> Hỗ trợ miễn giảm học phí</td>';
                                }
                            }else if(data[i].thamdinh_type === 'CPHT' ){
                                if(parseInt(data[i].thamdinh_view) === 0){
                                    html_show += '<td class="mailbox-subject" style="width:25%"><b>Hỗ trợ chi phí học tập</b></td>';
                                }else{
                                    html_show += '<td class="mailbox-subject" style="width:25%">Hỗ trợ chi phí học tập</td>';
                                }
                            }else if(data[i].thamdinh_type === 'HTAT' ){
                                if(parseInt(data[i].thamdinh_view) === 0){
                                    html_show += '<td class="mailbox-subject" style="width:25%"><b>Hỗ trợ ăn trưa</b></td>';
                                }else{
                                    html_show += '<td class="mailbox-subject" style="width:25%">Hỗ trợ ăn trưa</td>';
                                }
                            }else if(data[i].thamdinh_type === 'HSDTTS' ){
                                if(parseInt(data[i].thamdinh_view) === 0){
                                    html_show += '<td class="mailbox-subject" style="width:25%"><b>Hỗ trợ học sinh dân tộc thiểu số</b></td>';
                                }else{
                                    html_show += '<td class="mailbox-subject" style="width:25%">Hỗ trợ học sinh dân tộc thiểu số</td>';
                                }
                            }else if(data[i].thamdinh_type === 'HTBT' ){
                                if(parseInt(data[i].thamdinh_view) === 0){
                                    html_show += '<td class="mailbox-subject" style="width:25%"><b>Hỗ trợ học sinh bán trú</b></td>';
                                }else{
                                    html_show += '<td class="mailbox-subject" style="width:25%">Hỗ trợ học sinh bán trú</td>';
                                }
                            }else if(data[i].thamdinh_type === 'NGNA' ){
                                if(parseInt(data[i].thamdinh_view) === 0){
                                    html_show += '<td class="mailbox-subject" style="width:25%"><b>Hỗ trợ người nấu ăn</b></td>';
                                }else{
                                    html_show += '<td class="mailbox-subject" style="width:25%">Hỗ trợ người nấu ăn</td>';
                                }
                            }else if(data[i].thamdinh_type === 'HSKT' ){
                                if(parseInt(data[i].thamdinh_view) === 0){
                                    html_show += '<td class="mailbox-subject" style="width:25%"><b>Hỗ trợ học sinh khuyết tật</b></td>';
                                }else{
                                    html_show += '<td class="mailbox-subject" style="width:25%">Hỗ trợ học sinh khuyết tật</td>';
                                }
                            }else if(data[i].thamdinh_type === 'TONGHOP' ){
                                if(parseInt(data[i].thamdinh_view) === 0){
                                    html_show += '<td class="mailbox-subject" style="width:25%"><b>Chế độ chính sách ưu đãi cho trẻ em, HS-SV</b></td>';
                                }else{
                                    html_show += '<td class="mailbox-subject" style="width:25%">Chế độ chính sách ưu đãi cho trẻ em, HS-SV</td>';
                                }
                            }
                            if((data[i].schools_name).trim() !== ''){
                                html_show += "<td class='text-left' style='font-style: italic;width:25%'>"+data[i].schools_name+"</td>";
                            }else{
                                html_show += "<td class='text-left' style='font-style: italic;width:25%'>-</td>";
                            }
                            html_show += "<td class='text-right' style='width:25%'>"+formatDateTimes(data[i].thamdinh_ngaygui)+"</td>";
                            html_show += "</tr>";
                        }
                        
                    } else {
                        html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                    }
                    $('#inbox-thamdinh').html(html_show);
                }, error: function(dataget) {

                }
            });
        };

    function sendthamdinh(id){
        var form_datas = new FormData();
        var v_note = $('#txtNoteSend').val();

        form_datas.append("id", id); 
        form_datas.append("NOTE", v_note); 
        form_datas.append("file", $('#txtFile_attack').prop('files')[0]);

        $.ajax({
                type: "POST",
                url:'/ho-so/tham-dinh/send',
                data: form_datas,
                contentType: false,//'application/json; charset=utf-8',
                cache: false,             // To unable request pages to be cached
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {
                    if(dataget.success != null || dataget.success != undefined){
                        $("#myModal").modal("hide");
                        utility.message("Thông báo",dataget.success,null,3000)
                       GET_INITIAL_NGHILC();
                    loadInbox($('#viewthamdinh').val());
                    }else if(dataget.error != null || dataget.error != undefined){
                        utility.message("Thông báo",dataget.error,null,5000, 1)
                    }
                }, error: function(dataget) {
                }
            });
    }

    function resendthamdinh(id){
        var form_datas = new FormData();
        var v_note = $('#txtNoteSend').val();

        form_datas.append("id", id); 
        form_datas.append("NOTE", v_note); 
        form_datas.append("file", $('#txtFile_attack').prop('files')[0]);
        //alert(v_file_attack);
        $.ajax({
                type: "POST",
                url:'/ho-so/tham-dinh/resend',
                data: form_datas,
                contentType: false,//'application/json; charset=utf-8',
                cache: false,             // To unable request pages to be cached
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {
                    if(dataget.success != null || dataget.success != undefined){
                        $("#myModal").modal("hide");
                        utility.message("Thông báo",dataget.success,null,3000)
                        GET_INITIAL_NGHILC();
                        loadInbox($('#viewthamdinh').val());
                    }else if(dataget.error != null || dataget.error != undefined){
                        utility.message("Thông báo",dataget.error,null,5000, 1)
                    }
                }, error: function(dataget) {
                }
            });
   }
function loadInboxThamDinh(row,key,callback) {
    
    var html_show = "";
    var o = {};
    if(key!=null){
        o = {
                key : key,
                start: (GET_START_RECORD_NGHILC()),
                limit : row
            };
    }else{
        o = {
                start: (GET_START_RECORD_NGHILC()),
                limit : row
            };
    }
            $.ajax({
                type: "POST",
                url: '/ho-so/tham-dinh/load',
                data: JSON.stringify(o),
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {

                    SETUP_PAGING_NGHILC(dataget, function () {
                        loadInboxThamDinh(row,key);
                    });
                    $('#inbox-thamdinh').html("");
                    data = dataget.data;
                    //permission = dataget.permission;
                    if(data.length>0){
                        for (var i = 0; i < data.length; i++) {

                            html_show += "<tr><td style='width:3%' class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            if(parseInt(data[i].report_nature) === 0){
                                if(parseInt(data[i].thamdinh_trangthai) === 0){
                                    html_show += '<td class="mailbox-star" style="width:3%"><a title="Chưa duyệt" onclick="view_inbox('+data[i].thamdinh_id+')" href="#"><i class="fa fa-star text-yellow"></i></a></td>';
                                }else if(parseInt(data[i].thamdinh_trangthai) === 1){
                                    html_show += '<td class="mailbox-star" style="width:3%"><a title="Đã duyệt" onclick="view_inbox('+data[i].thamdinh_id+')" href="#"><i class="fa fa-star text-green"></i></a></td>';
                                }else{
                                    html_show += '<td class="mailbox-star" style="width:3%"><a title="Chuyển lại" onclick="view_inbox('+data[i].thamdinh_id+')" href="#"><i class="fa fa-reply text-yellow"></i></a></td>';
                                }
                            }else{
                                if(parseInt(data[i].thamdinh_trangthai) === 0){
                                    html_show += '<td class="mailbox-star" style="width:3%"><a title="Chưa duyệt" onclick="view_inbox('+data[i].thamdinh_id+')" href="#"><i class="fa fa-star-o text-yellow"></i></a></td>';
                                }else if(parseInt(data[i].thamdinh_trangthai) === 1){
                                    html_show += '<td class="mailbox-star" style="width:3%"><a title="Đã duyệt" onclick="view_inbox('+data[i].thamdinh_id+')" href="#"><i class="fa fa-star-o text-green"></i></a></td>';
                                }else{
                                    html_show += '<td class="mailbox-star" style="width:3%"><a title="Chuyển lại" onclick="view_inbox('+data[i].thamdinh_id+')" href="#"><i class="fa fa-reply text-yellow"></i></a></td>';
                                }
                            }
                            if(data[i].report_attach_name !== ''){
                                html_show += '<td class="mailbox-attachment" style="width:3%"><i class="fa fa-paperclip"></i></td>';
                            }else{
                                html_show += '<td class="mailbox-attachment" style="width:3%"></td>';
                            }
                            html_show += '<td class="mailbox-name" style="width:25%"><a onclick="view_inbox('+data[i].thamdinh_id+')" href="#">'+data[i].report_name+'</a></td>';
                            
                            if(data[i].thamdinh_type === 'MGHP' ){
                                if(parseInt(data[i].thamdinh_view) === 0){
                                    html_show += '<td class="mailbox-subject" style="width:25%"><b>Miễn giảm học phí</b></td>';
                                }else{
                                    html_show += '<td class="mailbox-subject" style="width:25%">Miễn giảm học phí</td>';
                                }
                            }
                            if((data[i].thamdinh_user_view).trim() !== ''){
                                html_show += "<td class='text-right' style='font-style: italic;width:25%'>"+data[i].thamdinh_user_view+" xem cuối</td>";
                            }else{
                                html_show += "<td class='text-right' style='font-style: italic;width:25%'>-</td>";
                            }
                            html_show += "<td class='text-right' style='width:25%'>"+data[i].thamdinh_ngaygui+"</td>";
                            html_show += "</tr>";
                        }
                        
                    } else {
                        html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                    }
                    $('#inbox-thamdinh').html(html_show);
                    if(callback!= null){
                        callback();
                    }
                }, error: function(dataget) {

                }
            });
        };
    function insertUpdateTotal(type){
        $('#tonghopdanhsach')[0].reset();
        $("#txtNameDS").val('');
        $("#txtNguoiLap").val('');
        $("#txtNguoiKy").val('');
        $("#txtGhiChu").val('');
        $('#checkAllId').prop('checked', false);
    }

    function autocompleteSearchDanhsachs(idControl, form) {
        
        $('#' + idControl).autocomplete({
            source: function (request, response) {
                var keySearch = $.ui.autocomplete.escapeRegex(request.term).replace(/[%\\\-]/g, '');
                
                if (keySearch.length >= 3) {
                    console.log(keySearch);
                    GET_INITIAL_NGHILC();
                    if (form == 2) {loadInboxPheDuyet($('#viewpheduyet').val(), keySearch);}
                    if (form == 1) {loadInbox($('#viewthamdinh').val(), keySearch);}
                }else if(keySearch.length == 0){
                    GET_INITIAL_NGHILC();
                    if (form == 2) {loadInboxPheDuyet($('#viewpheduyet').val());}
                    if (form == 1) {loadInbox($('#viewthamdinh').val());}
                }
            },
            minLength: 0,
            delay: 222,
            autofocus: true
        });
    };

//-------------------------------------------------Download file Excel phê duyệt tổng hợp------------------------------------------
    function export_files(id) {
        //alert(id);
        window.open('/ho-so/tham-dinh/downloadFileExcel/' + id, '_blank');
        // $.ajax({
        //     type: "GET",
        //     url: '/ho-so/tham-dinh/downloadFileExcel/' + id,
        //     contentType: 'application/json; charset=utf-8',
        //     headers: {
        //         'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
        //     },
        //     success: function(data) {
        //         //console.log(data);
        //     }, error: function(data) {
        //     }
        // });
    }

//----------------------------------------------------Xóa phê duyệt tổng hợp------------------------------------------------------
    function del_report_pheduyettonghop(id){
        utility.confirm("Xóa bản ghi?", "Bạn có chắc chắn muốn xóa?", function () {
            insertUpdateTotal(1);
            $.ajax({
                    type: "GET",
                    url: '/ho-so/tham-dinh/deletePheduyettonghop/' + id,
                    success: function(data) {
                        if(data.success != null || data.success != undefined){
                            $("#myModalTongDanhSach").modal("hide");
                            utility.message("Thông báo",data.success,null,3000);
                            insertUpdateTotal(1);
                            GET_INITIAL_NGHILC();
                            loaddataTotal($('#view-tonghopdanhsach').val());
                        }else if(data.error != null || data.error != undefined){
                            $("#myModalTongDanhSach").modal("hide");
                            utility.message("Thông báo",data.error,null,5000, 1);
                        }                     
                    }, error: function(data) {
                }
            });
        });
    }


//----------------------------------------------------------Send Thẩm định--------------------------------------------------------------
    var report_id_send = 0;

    function openPopupSendPH(id){
        report_id_send = id;

        openPopupSendPheDuyet();

        loadComboboxNguoi();
    }

    function loadComboboxNguoi(){
        $.ajax({
            type: "GET",
            url: '/ho-so/tham-dinh/loadUsertotal',
            success: function(data) {
                //console.log(data);
                var html_show = "";
                if(data.length > 0){
                    html_show += "<option value=''>-- Chọn người nhận --</option>";
                    for (var i = 0; i < data.length; i++) {
                        html_show += "<option value='"+data[i].id+"'>"+data[i].last_name+" "+data[i].first_name+"---phòng: "+data[i].department_name+"</option>";
                    }
                    $('#drNguoinhan').html(html_show);
                    $('#drCC').html(html_show);
                }else{
                    $('#drNguoinhan').html("<option value=''>-- Chưa có người nhận --</option>");
                    $('#drCC').html("<option value=''>-- Chưa có người nhận --</option>");
                }
                if(callback!=null){
                        callback();
                }
            }, error: function(dataget) {
            }
        });
    }
    //--------------------------------------------------Gửi danh sách cần phê duyệt------------------------------------------------
    sendtotal = function (id) {
        utility.confirm("Gửi danh sách?", "Bạn có chắc chắn muốn gửi thẩm định?", function () {
            $.ajax({
                    type: "GET",
                    url: '/ho-so/tham-dinh/sendtotal/'+id,
                    success: function(dataget) {
                    if(dataget.success != null || dataget.success != undefined){
                        utility.message("Thông báo",dataget.success,null,3000)
                        GET_INITIAL_NGHILC();
                        loaddataTotal($('#view-tonghopdanhsach').val());
                    }else if(dataget.error != null || dataget.error != undefined){
                        //$("#myModal").modal("hide");
                        utility.message("Thông báo",dataget.error,null,3000, 1)
                        //insertUpdate(1);
                        //loadKinhPhiDoiTuong($('select#viewTableDT').val()); 
                    }
                    }, error: function(dataget) {
                }
            });
        });
    }

    function sendDStonghop() {
        var list_id_nhan = $('#drNguoinhan').val();
        // alert(list_id_nhan);
        var list_id_cc = $('#drCC').val();
        // alert(list_id_cc);
        var lst_id_cc = "";
        if (list_id_nhan !== null && list_id_nhan !== "") {
            utility.confirm("Gửi danh sách?", "Bạn có chắc chắn muốn gửi?", function () {
                if (list_id_cc !== null && list_id_cc !== "") {
                    lst_id_cc = list_id_cc.toString();
                }
                var o = {
                    id: report_id_send,
                    list_id_nguoinhan: list_id_nhan.toString(),
                    list_id_cc: lst_id_cc
                };
                // alert(JSON.stringify(o));
                $.ajax({
                    type: "POST",
                    url: '/ho-so/tham-dinh/sendtotal',
                    data: JSON.stringify(o),
                    dataType: 'json',
                    contentType: 'application/json; charset=utf-8',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                    },
                        success: function(dataget) {
                        if(dataget.success != null || dataget.success != undefined){
                            utility.message("Thông báo",dataget.success,null,3000)
                            GET_INITIAL_NGHILC();
                            loaddataTotal($('#view-tonghopdanhsach').val());

                            report_id_send = 0;
                            $("#myModalSendPheduyet").modal("hide");
                        }else if(dataget.error != null || dataget.error != undefined){
                            //$("#myModal").modal("hide");
                            utility.message("Thông báo",dataget.error,null,3000)
                            //insertUpdate(1);
                            //loadKinhPhiDoiTuong($('select#viewTableDT').val()); 
                        }
                        }, error: function(dataget) {
                    }
                });
            });
        }
        else {
            utility.messagehide('msg_send','Xin mời nhập tên người nhận',1,3000);
            $('#drNguoinhan').focus();
        }        
    }

//---------------------------------------------------------Load information revert Phê duyệt----------------------------------------------------------
    function loadPopupReverts(id_revert){
        
        $.ajax({
            type: "GET",
            url: '/ho-so/tham-dinh/loadInforRevert/' + id_revert,
            success: function(data) {
                // console.log(data);
                
                if (data[0].pheduyettonghop_note !== null && data[0].pheduyettonghop_note !== "") {
                    $('#note-content').html(data[0].pheduyettonghop_note);
                }
                else {
                    $('#note-content').html('Không có ý kiến');
                }

                if (data[0].pheduyettonghop_file_revert !== null && data[0].pheduyettonghop_file_revert !== "") {
                    $('#file-attach').html('&nbsp;&nbsp;<i class="fa fa-file-excel-o"></i>  <a style="font-style: italic;"> '+data[0].pheduyettonghop_file_revert+'</a> ( <a href="#" onclick="download_file_revert_pheduyet('+id_revert+')" ><i class="fa fa-download"></i> Tải về </a>)');
                }
                else {
                    $('#file-attach').html('Không có file đính kèm');
                }

                //Quyết định duyệt-----------------------------------------------------------
                if (data[0].pheduyettonghop_note_approved !== null && data[0].pheduyettonghop_note_approved !== "") {
                    $('#note-content-approved').html(data[0].pheduyettonghop_note_approved);
                }
                else {
                    $('#note-content-approved').html('Không có quyết định');
                }

                if (data[0].pheduyettonghop_file_approved !== null && data[0].pheduyettonghop_file_approved !== "") {
                    $('#file-attach-approved').html('&nbsp;&nbsp;<i class="fa fa-file-excel-o"></i>  <a style="font-style: italic;"> '+data[0].pheduyettonghop_file_approved+'</a> ( <a href="#" onclick="download_file_approved_pheduyet('+id_revert+')" ><i class="fa fa-download"></i> Tải về </a>)');
                }
                else {
                    $('#file-attach-approved').html('Không có file đính kèm');
                }

                $('html, body').animate({ scrollTop: 0 }, 'slow');
                $("#myModalRevertPheduyet").modal("show");
            }, error: function(data) {
            }
        });
    }

    function download_file_revert_pheduyet(id_report){
        window.open('/ho-so/tham-dinh/download_file_revert_pheduyet/' + id_report, '_blank');
    }

    function download_file_approved_pheduyet(id_report){
        window.open('/ho-so/tham-dinh/download_file_approved_pheduyet/' + id_report, '_blank');
    }

    function download_attach_pheduyettonghop(id_file){
        window.open('/ho-so/phe-duyet/download_file_pheduyettonghop/' + id_file, '_blank');
    }

//------------------------------------------------Form Phê Duyệt mới----------------------------------------------------------
    var _year = '';
    function loaddataDanhSachPheDuyet(row, keySearch = "") {

        var msg_warning = "";

        msg_warning = validateTHCD();

        // alert(msg_warning);

        if (msg_warning !== null && msg_warning !== "") {
            utility.messagehide("messageValidate", msg_warning, 1, 5000);
            return;
        }

        var schools_id = $('#sltSchool').val();
        var year = $('#sltYear').val();

        _year = year;

        var ky = year.split("-");
        var number = 0;

        if (ky[0] == 'HK1') {
            number = 1;
        }
        else if (ky[0] == 'HK2') {
            number = 2;
        }
        else if (ky[0] == 'CA') {
            number = 3;
        }
        
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit: row,
            SCHOOLID: schools_id,
            YEAR: year,
            KEY: keySearch
        };
        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/loadListApproved',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(datas) {

                SETUP_PAGING_NGHILC(datas, function () {
                    loaddataDanhSachTongHop(row, keySearch);
                });
                
                $('#dataListApprovedPheduyet').html("");
                var dataget = datas.data;
                // console.log(dataget);
                
                if(dataget.length > 0){
                    for (var i = 0; i < dataget.length; i++) {
                                    
                        html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * 20))+"</td>";
                        html_show += "<td><a href='javascript:;' onclick='getProfileSubById("+dataget[i].profile_id+", "+number+");'>"+dataget[i].profile_name+"</a></td>";
                        html_show += "<td>"+formatDates(dataget[i].profile_birthday)+"</td>";
                        html_show += "<td>"+dataget[i].schools_name+"</td>";
                        html_show += "<td>"+dataget[i].class_name+"</td>";
                        html_show += "<td>"+formatNumber_2(dataget[i].MGHP)+"</td>";
                        html_show += "<td>"+formatNumber_2(dataget[i].CPHT)+"</td>";
                        html_show += "<td>"+formatNumber_2(dataget[i].HTAT)+"</td>";
                        html_show += "<td>"+formatNumber_2(dataget[i].HTBT_TA)+"</td>";
                        html_show += "<td>"+formatNumber_2(dataget[i].HTBT_TO)+"</td>";
                        html_show += "<td>"+formatNumber_2(dataget[i].HTBT_VHTT)+"</td>";
                        html_show += "<td>"+formatNumber_2(dataget[i].HTATHS)+"</td>";
                        html_show += "<td>"+formatNumber_2(dataget[i].HSKT_HB)+"</td>";
                        html_show += "<td>"+formatNumber_2(dataget[i].HSKT_DDHT)+"</td>";
                        html_show += "<td>"+formatNumber_2(dataget[i].HBHSDTNT)+"</td>";
                        html_show += "<td>"+formatNumber_2(dataget[i].HSDTTS)+"</td>";
                        html_show += "<td>"+formatNumber_2(dataget[i].TONGTIEN)+"</td>";
                        html_show += "<td>"+ConvertString(dataget[i].GHICHU)+"</td>";
                        html_show += "<td>"
                        // if(check_Permission_Feature("2")){
                        //     html_show += "<button data='"+dataget[i].profile_id+"' onclick='getHoSoHocSinh("+dataget[i].profile_id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Sửa </button> &nbsp;";
                        // }
                        // if(check_Permission_Feature("3")){
                        //     html_show += " &nbsp;<button  onclick='delHoSoHocSinh("+dataget[i].profile_id+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                        // }
                        if(parseInt(dataget[i].TRANGTHAI) == 1 && parseInt(dataget[i].TRANGTHAIPHEDUYET) == 0){
                            html_show += "<button class='btn btn-primary btn-xs' onclick='updatePheduyet("+dataget[i].qlhs_thcd_id+")'> Chờ phê duyệt</button> ";
                        }else if(parseInt(dataget[i].TRANGTHAI) == 1 && parseInt(dataget[i].TRANGTHAIPHEDUYET) == 1){
                            html_show += "<button class='btn btn-success btn-xs' onclick='updatePheduyet("+dataget[i].qlhs_thcd_id+")'> Đã phê duyệt </button> ";
                        }
                        html_show += "</td></tr>";
                    }                            
                }
                else {
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }
                $('#dataListApprovedPheduyet').html(html_show);
            }, error: function(dataget) {

            }
        });
    };

    function updatePheduyet(thcdId){
        var note = $('#txtGhiChuTHCD').val();

        var strData = 'ID' + thcdId + '-' + _year + '-' + note;
        // console.log(strData);
        utility.confirm("Duyệt cấp kinh phí?", "Bạn có chắc chắn muốn phê duyệt?", function () {
            $.ajax({
                type: "get",
                url:'/ho-so/phe-duyet/pheduyethocsinh/' + strData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(data) {
                    console.log(data);
                    if (data['success'] != "" && data['success'] != undefined) {
                        utility.message("Thông báo",data['success'],null,3000);
                        // resetFormTHCD();
                        loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val());
                        $("#myModalApproved").modal("hide");
                        closeLoading();
                    }
                    if (data['error'] != "" && data['error'] != undefined) {
                        // resetFormTHCD();
                        loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val());
                        utility.message("Thông báo",data['error'],null,3000,1);
                        closeLoading();
                    }
                }, error: function(data) {
                    closeLoading();
                }
            });
        });
    }