$(function () {
    delSubject = function(id,time){
        utility.confirm('Thông báo','Bản muốn xóa bản ghi này',function(){
                GetFromServer('/ho-so/hoc-sinh/subject/delByProfile/'+time+'/'+id,function(data){
                    if(data.success != null && data.success != undefined && data.success != ''){
                       utility.message("Thông báo", data.success, null,5000,0);
                          
                      }else if(data.fall != null && data.fall != undefined && data.fall != ''){
                          utility.message("Thông báo", data.fall, null,5000,1);
                      }else{
                          utility.message("Thông báo", data.error, null,5000,1);
                      }
                      GET_INITIAL_NGHILC();
                    loadDataSubject();
                },function(result){
                    console.log(result);
                },"","","");
        });
    }
    getSubject = function(id,time,type=0){
        GetFromServer('/ho-so/hoc-sinh/subject/getByProfile/'+time+'/'+id,function(result){
            $('#dataSubjectProfile').html("");
                var dataget = result;
                var html_show = ""; 
                $('#txtStart_time').val(dataget[0].profile_start_time);
                $('#txtEnd_time').val(dataget[0].profile_end_time);
                $('#txtProfileID').val(dataget[0].profile_subject_profile_id);
                $('#start_year').val(dataget[0].start_year);
                $('#end_year').val(dataget[0].end_year);
                if(type==0){
                    $('#txtstart_year').val(dataget[0].end_year);
                    $('#txtend_year').val(dataget[0].end_year);
                }else{
                    $('#txtstart_year').val('');
                    $('#txtend_year').val('');
                }
                
                if(dataget.length > 0){
                    for (var i = 0; i < dataget.length; i++) {

                    // if(dataget[i].profile_subject_id != null && dataget[i].profile_subject_id != undefined){
                    //     html_show += "<tr id='"+dataget[i].profile_subject_id+"' style='background: #e1e1e1'>";    
                    // }else{
                    //     html_show += "<tr id='"+dataget[i].profile_subject_id+"'>";
                    // }
                     html_show += "<tr id='"+dataget[i].profile_subject_id+"'>";
                        html_show += "<td class='text-center' style='vertical-align:middle'>"+(i + 1 + (GET_START_RECORD_NGHILC() * 10))+"</td>";
                        if(dataget[i].profile_subject_id != null && dataget[i].profile_subject_id != undefined){
                        html_show += "<td class='text-center' style='vertical-align:middle'><input type='checkbox' value='"+dataget[i].subject_id+"' checked class='checkboxactive'/></td>";    
                    }else{
                        html_show += "<td class='text-center' style='vertical-align:middle'><input type='checkbox' value='"+dataget[i].subject_id+"' class='checkboxactive'/></td>";
                    }
                        
                        html_show += "<td style='vertical-align:middle'><a href='javascript:;' onclick='getProfileSubById("+parseInt(dataget[i].profile_subject_id)+");'>"+dataget[i].subject_name+"</a></td>";
                        html_show += "<td class='text-center' style='vertical-align:middle'>"+formatDates(dataget[i].profile_subject_createdate)+"</td>";
                        html_show += "<td class='text-center' style='vertical-align:middle'>"+formatDates(dataget[i].profile_subject_updatedate)+"</td>";
                        html_show += "<td class='text-center' style='vertical-align:middle'>"+ConvertString(dataget[i].last_name)+" "+ConvertString(dataget[i].first_name)+"</td>";
                        if(dataget[i].profile_subject_id != null && dataget[i].profile_subject_id != undefined){
                            html_show += "<td class='text-center' style='vertical-align:middle'><img src='/images/Icon/check.png' height='15' width='15'/></td>";
                        }else{
                            html_show += "<td class='text-center' style='vertical-align:middle'>-</td>";
                        }



                        html_show += "</tr>";
                    }                            
                }
                else {
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }
                $('#dataSubjectProfile').html(html_show);
               
                if(type===0){
                    $('#btnChangeSubject').addClass('hidden');
                }else{
                    $('#btnChangeSubject').removeClass('hidden');
                }
            $('#ModalSubjectProfile').modal('show');
        },function(result){
            console.log(result);
        },"","","");
    };
    var insert = true;
    resetControl(1);

    
    $('#cbxChooseAll').change(function() {
            if ($('#cbxChooseAll').prop('checked'))
                $('[id*="someCheckbox"]').prop('checked', true);
            else
                $('[id*="someCheckbox"]').prop('checked', false);
    });

    $('#txtBirthday').datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true
    });
    $('#dateOutProfile').datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true
    });
    $('#txtYearProfile').datepicker({
      format: 'mm-yyyy',
      autoclose: true
    });
    $('#txtDateNghi').datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true
    });
    var counter = 0;
    $('#clearFile').click(function() {
        for (var i = 1; i <= counter; i++) {
            $("input[name*='txtDecidedFileUpload_"+i+"']").filestyle('clear');
        }
        
   });   
    viewHistory = function(id){
        $('#HistoryTable').DataTable().clear().draw().destroy();
        var o = {PROFILEID: id};
        //GetFromServer('/ho-so/hoc-sinh/viewhistory/'+id,function());
        $('#HistoryTable').DataTable({
            "language": {
                   "lengthMenu": "Hiển thị _MENU_ bản ghi" ,
                   "info": String.format("Hiển thị {0} đến {1} trên tổng {2} bản ghi", "_START_", "_END_", "_TOTAL_"),// "Showing page _PAGE_ of _PAGES_",
                   "infoEmpty": "",
                   "sSearch": "Tìm kiếm: ",
                   "paginate": {
                       "first": "First",
                       "last": "Last",
                       "next":"Trang sau",
                       "previous": "Trang trước"
                   },
                   "emptyTable": "Không tìm thấy dữ liệu"
               }, 
            "bDestroy": true,
            "ajax": {
                'type': 'POST',
                'url': '/ho-so/hoc-sinh/viewhistory',
                'data': o,
                'headers': {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                }
            },
             
        'columnDefs': [
        {
            "searchable": false,
            "orderable": false,
            "targets": [0],
            'className': 'text-center',
            "width" : "5%",
            "render": function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
            }
        },
        {
             'targets': [1],
             "width" : "20%",
             'data': "class_name"
        },
        {
             'targets': [2],
             "width" : "20%",
             'data': "history_year"
        },{
             'targets': [3],
             "width" : "20%",
             'data': "history_upto_level",
             "render": function (data) {
               if(parseInt(data) === 1){
                    return "Ra trường";
               }else if(parseInt(data) === 2){
                    return "Học lại";
               }else if(parseInt(data) === 3){
                    return "Lên lớp";
               }else if(parseInt(data) === 4){
                    return "Nghỉ học";
               }else{
                    return "-";
               }
                
            }
        },
        {
             'targets': [4],
             "width" : "20%",
             'data': "username"
        },
        {
             'targets': [5],
             "width" : "20%",
             'data': "history_update_date",
            "render": function (data) {
                var date = new Date(data);
                var month = (date.getMonth()+1)+'';
                return ((date.getDate()+'').length > 1 ? date.getDate():"0"+date.getDate()) + "-" + (month.length > 1 ? month : "0" + month) + "-" + date.getFullYear();
            }
        }
      ],
  });
        openModalHistory();
    }
    $(document).on('click', 'button#btnDeleteDecided', function () {
        counter--;
        $(this).closest('tr').remove();
    });
    
        $('#btnAddNewRow').click(function(event){
            event.preventDefault();
            counter++;
            //loadComboDecidedType();
    //         <label class="btn btn-default btn-file">
    //     Browse <input type="file" style="display: none;">
    // </label>
            var newRow = $('<tr id="trContent"><td style="vertical-align:middle"><label id="idnum">' +
                counter + '</label></td> '
                + ' <td style="vertical-align:middle"><button id="btnDeleteDecided" class="btn btn-danger btn-xs editor_remove"><i class="glyphicon glyphicon-remove"></i> </button></td>'
                + ' <td class="type"><select name="drDecidedType_'+counter+'" id="drDecidedType" class="form-control"><option value="">--- Chọn loại hồ sơ ---</option><option value="MGHP">Miễn giảm học phí</option><option value="CPHT">Chi phí học tập</option><option value="HTAT">Hỗ trợ ăn trưa</option><option value="HTBT">Hỗ trợ bán trú</option><option value="NGNA">Hỗ trợ người nấu ăn</option><option value="HSKT">Hỗ trợ học sinh khuyết tật</option><option value="HSDTTS">Hỗ trợ học sinh dân tộc thiểu số tại huyện Mù Cang Chải và Trạm tấu</option><option value="TONGHOP">Chế độ chính sách ưu đãi</option></select></td><td class="code"><input class="form-control" type="text" name="txtDecidedCode_'+counter+'" id="txtDecidedCode"/></td><td class="number"><input type="text" class="form-control" name="txtDecidedNumber_'+counter+'" id="txtDecidedNumber"/></td><td class="confirmation"><input type="text" class="form-control" name="txtDecidedConfirmation_'+counter+'" id="txtDecidedConfirmation"/></td><td class="confirmdate"><input type="text" placeholder="ngày-tháng-năm" class="form-control" name="txtDecidedConfirmDate_'+counter+'" id="txtDecidedConfirmDate"/></td><td colspan="2" class="uploadfile" > <input type="file" name="txtDecidedFileUpload_'+counter+'" id="txtDecidedFileUpload" ></td></tr>');
            
            $('#tbDecided').append(newRow);
            $("input[name*='txtDecidedFileUpload_"+counter+"']").filestyle({
                buttonText : ' ',
                buttonName : 'btn-info'
               });
            $("input[name*='txtDecidedConfirmDate_"+counter+"']").datepicker({
           // $('#txtDecidedConfirmDate').datepicker({
              format: 'dd-mm-yyyy',
              autoclose: true
            });
        });
    loadDataSubject = function(keyword=null){
        var row = $('#drPagingDanhsachtonghop').val();
        var o = {};
        keyword = $('#txtSearchProfile').val();
        if(keyword != null || keyword != ''){
            o = {
                schools_id : $('#drSchoolTHCD').val(),
                class_id : $('#sltLopGrid').val(),
                start: (GET_START_RECORD_NGHILC()),
                limit : row,
                key : keyword
            };    
        }else{
            o = {
                schools_id : $('#drSchoolTHCD').val(),
                class_id : $('#sltLopGrid').val(),
                start: (GET_START_RECORD_NGHILC()),
                limit : row,
            };
        }
        
        PostToServer('/ho-so/hoc-sinh/subject/load',o,function(result){
              SETUP_PAGING_NGHILC(result, function () {
                        loadDataSubject();
                    }); 
              $('#dataSubject').html("");
                var dataget = result.data;
                var html_show = ""; 
                if(dataget.length > 0){
                    for (var i = 0; i < dataget.length; i++) {
                                    
                        html_show += "<tr><td class='text-center' style='vertical-align:middle'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                        html_show += "<td style='vertical-align:middle'><a href='javascript:;' onclick='getProfileSubById("+parseInt(dataget[i].profile_id)+");'>"+dataget[i].profile_name+"</a></td>";
                        html_show += "<td class='text-center' style='vertical-align:middle'>"+formatDates(dataget[i].profile_birthday)+"</td>";
                        html_show += "<td class='text-center' style='vertical-align:middle'>"+dataget[i].class_name+"</td>";
                        html_show += "<td style='vertical-align:middle'>"+dataget[i].subject_name+"</td>";
                        html_show += "<td class='text-center' style='vertical-align:middle'>"+ConvertString(dataget[i].start_year)+"</td>";
                        html_show += "<td class='text-center' style='vertical-align:middle'>"+ConvertString(dataget[i].end_year)+"</td>";
                        html_show += "<td class='text-center' style='vertical-align:middle'>"+ConvertTimestamp(dataget[i].profile_end_time)+"</td>";
                        
                        if(check_Permission_Feature("2")){
                            if(dataget[i].profile_end_time != null && dataget[i].profile_end_time != undefined){
                                html_show += "<td class='text-center' style='vertical-align:middle'><button data='"+dataget[i].profile_id+"' onclick='getSubject("+dataget[i].profile_id+","+dataget[i].profile_start_time+",0);' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Cập nhật </button></td>";    
                            }else{
                                if(parseInt(dataget[i].profile_status) != 1){
                                    html_show += "<td class='text-center' style='vertical-align:middle'><button data='"+dataget[i].profile_id+"' onclick='getSubject("+dataget[i].profile_id+","+dataget[i].profile_start_time+",1);' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Cập nhật </button></td>";    
                                }else{
                                    html_show += "<td class='text-center' style='vertical-align:middle'><button data='"+dataget[i].profile_id+"' onclick='getSubject("+dataget[i].profile_id+","+dataget[i].profile_start_time+",0);' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Cập nhật </button></td>";      
                                }
                            }
                            
                        }
                        if(check_Permission_Feature("3")){
                            if(dataget[i].profile_end_time == null || dataget[i].profile_end_time == undefined){
                                html_show += "<td class='text-center' style='vertical-align:middle'><button data='"+dataget[i].profile_id+"' onclick='delSubject("+dataget[i].profile_id+","+dataget[i].profile_start_time+");' class='btn btn-danger btn-xs' ><i class='glyphicon glyphicon-remove'></i> Xóa</button></td>";    
                            }
                        }
                        html_show += "</tr>";
                    }                            
                }
                else {
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }
                $('#dataSubject').html(html_show);
        },function(result){
            console.log(result);
        },"btnLoadDataSubject","","");
    };
    loadUpdateDataSubject = function(){
        var row = $('#drPagingDanhsachtonghop').val();
        var o = {
            schools_id : $('#drSchoolTHCD').val(),
            class_id : $('#sltLopGrid').val(),
            start: (GET_START_RECORD_NGHILC()),
            limit : row,
        };
        PostToServer('/ho-so/hoc-sinh/subject/loadnew',o,function(result){
              SETUP_PAGING_NGHILC(result, function () {
                        loadUpdateDataSubject();
                    }); 
              $('#dataSubject').html("");
                var dataget = result.data;
                var html_show = ""; 
                if(dataget.length > 0){
                    for (var i = 0; i < dataget.length; i++) {
                                    
                        html_show += "<tr><td class='text-center' style='vertical-align:middle'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                        html_show += "<td style='vertical-align:middle'><a href='javascript:;' onclick='getProfileSubById("+parseInt(dataget[i].profile_id)+");'>"+dataget[i].profile_name+"</a></td>";
                        html_show += "<td class='text-center' style='vertical-align:middle'>"+formatDates(dataget[i].profile_birthday)+"</td>";
                        html_show += "<td class='text-center' style='vertical-align:middle'>"+dataget[i].class_name+"</td>";
                        html_show += "<td style='vertical-align:middle'>"+dataget[i].subject_name+"</td>";
                        html_show += "<td class='text-center' style='vertical-align:middle'>"+ConvertTimestamp(dataget[i].profile_start_time)+"</td>";
                       // html_show += "<td class='text-center' style='vertical-align:middle'>"+ConvertString(dataget[i].profile_end_time)+"</td>";
                        
                        if(check_Permission_Feature("2")){
                            html_show += "<td class='text-center' style='vertical-align:middle'><button data='"+dataget[i].profile_id+"' onclick='getSubject("+dataget[i].profile_id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Cập nhật </button></td>";
                        }
                        
                        html_show += "</tr>";
                    }                            
                }
                else {
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }
                $('#dataSubject').html(html_show);
        },function(result){
            console.log(result);
        },"btnLoadDataSubject","","");
    };
    delHoSoHocSinh = function (id) {
        utility.confirm("Xóa bản ghi?", "Bạn có chắc chắn muốn xóa?", function () {
            resetControl(1);
            var o = {PROFILEID: id};
            PostToServer('/ho-so/hoc-sinh/delete',o,function(data){
                if (data['success'] != "" && data['success'] != undefined) {
                        utility.message("Xóa hồ sơ học sinh",data['success'],null,3000);
                        resetControl(1);
                        $('#saveProfile').html("Thêm mới");
                        GET_INITIAL_NGHILC();
                        loaddataProfile($('select#viewTableProfile').val(),$('select#sltTruongGrid').val(),$('select#sltLopGrid').val(), $('#txtSearchProfile').val());
                        loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    }
                    
                    if (data['error'] != "" && data['error'] != undefined) {
                        utility.message("Thông báo",data['error'],null,3000,1);
                    }
                },null,null,"","");
        });
        
    }
    download_quyetdinh = function(id){
        var url = 'download_quyetdinh/'+id;
        window.open(url, '_blank');
    }
    getHoSoHocSinh = function (id) {
        resetControl();
        counter = 0;
        $('#saveProfile').html("Cập nhật");
        profile_id = id;
       // $('#saveProfile').button('loading');
        loading();
        var objJson = JSON.stringify({PROFILEID: id});
        //alert(objJson);
        var test;
        $.ajax({
            type: "POST",
            url:'/ho-so/hoc-sinh/getprofilebyid',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
           // animation: "spinner",
            success: function(data) {
                $('#sltDoituong').attr('disabled','disabled');
                var objProfile = data['objProfile'];
                //console.log(objProfile);
                // $('#txtCodeProfile').attr('disabled', 'disabled');
                // $('#txtCodeProfile').val(objProfile[0]['profile_code']);
                $('#txtNameProfile').val(objProfile[0]['profile_name']);

                var v_birthday = formatDates(objProfile[0]['profile_birthday']);
                $('#txtBirthday').val(v_birthday);

                loadComboxDantoc(function(){
                    $('#sltDantoc').val(objProfile[0]['profile_nationals_id']).trigger('change');
                });

                $('#txtParent').val(objProfile[0]['profile_parentname']);

                var v_year = formatMonth(objProfile[0]['profile_year']);
                
                if(parseInt(objProfile[0]['history']) > 1 ){
                    $('#txtYearProfile').val(v_year);
                    $('#txtYearProfile').datepicker('setDate', new Date(objProfile[0]['profile_year']));  
                    
                }else{
                    $('#txtYearProfile').val(v_year);
                    $('#txtYearProfile').datepicker('setDate', new Date(objProfile[0]['profile_year']));  
                    $('#txtYearProfile').removeAttr('disabled');
                }
                
                //  $('#txtYearProfile').datepicker({
                //   format: 'mm-yyyy',
                //   autoclose: true
                // });
                loadComboxTruongHoc('sltTruong',function(){
                    if(parseInt(objProfile[0]['history']) > 1 ){
                            $('select#sltTruong').attr('disabled','disabled');
                        }else{
                            $('select#sltTruong').removeAttr('disabled');
                          
                        }
                    loadComboxLop(objProfile[0]['profile_school_id'],'sltLop',function(){
                        if(parseInt(objProfile[0]['history']) > 1 ){
                            $('select#sltLop').attr('disabled','disabled');
                        }else{
                            $('select#sltLop').removeAttr('disabled');
                          
                        }
                    },parseInt(objProfile[0]['profile_class_id']));
                },parseInt(objProfile[0]['profile_school_id']));
                loadComboxTinhThanh(0,'sltTinh',function(){
                    loadComboxTinhThanh(parseInt(objProfile[0]['profile_site_id1']),'sltQuan',function(){
                        $('select#sltQuan').removeAttr('disabled');
                        if(objProfile[0]['profile_site_id3']!=null){
                            loadComboxTinhThanh(parseInt(objProfile[0]['profile_site_id2']),'sltPhuong',function(){
                                $('select#sltPhuong').removeAttr('disabled');
                            },parseInt(objProfile[0]['profile_site_id3']));
                        }
                        //$('#saveProfile').button('reset');
                        closeLoading();
                       // $('#saveProfile').button('reset').html("Cập nhật");
                        openModalUpdate();

                        //$('#saveProfile').removeAttr('disabled');
                    },parseInt(objProfile[0]['profile_site_id2']));
                },parseInt(objProfile[0]['profile_site_id1']));
               


                $('#txtThonxom').val(objProfile[0]['profile_household']);


                $('#sltBantru').val(objProfile[0]['profile_bantru']);

                var v_status = objProfile[0]['profile_status'];
                //alert(v_status);
                if (v_status == 1) {
                    $('#ckbNghihoc').prop('checked', true);
                    $('#txtDateNghi').val(formatDates(objProfile[0]['profile_leaveschool_date']));
                    $('div#divNgayNghi').removeAttr('hidden');
                    $('div#divNgayNghi').removeAttr('disabled');
                }
                else {
                    $('#ckbNghihoc').prop('checked', false);
                    $('div#divNgayNghi').attr('hidden','hidden');
                    $('div#divNgayNghi').attr('disabled','disabled');
                }

                var v_statusNQ57 = objProfile[0]['profile_statusNQ57'];
                //alert(v_status);
                if (v_statusNQ57 == 1) {
                    $('#ckbNQ57').prop('checked', true);
                }
                else {
                    $('#ckbNQ57').prop('checked', false);
                }

                $('#txtKhoangcach').val(objProfile[0]['profile_km']);
                $('#drGiaoThong').val(objProfile[0]['profile_giaothong']);

                loadComboxDoiTuong();
                var arrData = "";

                var arrProfileSub = data['arrProfileSub'];
                for (var i = 0; i < arrProfileSub.length; i++) {
                    arrData += (arrProfileSub[i]['profile_subject_subject_id']) + ",";    
                }
                var item = arrData.split(",");
                $("#sltDoituong").multiselect({
                     buttonWidth: '100%'
                });
                $("#sltDoituong").val(item);
                $("#sltDoituong").multiselect("refresh");
                //console.log(arrData);

                var arrProfileDec = data['arrProfileDec'];
                if(arrProfileDec.length>0){
                    $('#tableMoreProfile').removeAttr('hidden');
                    close = false;
                }else{
                    $('#tableMoreProfile').attr('hidden','hidden');
                    close = true;
                }
                
                for (var i = 0; i < arrProfileDec.length; i++) {    
                var dated = arrProfileDec[i]['decided_confirmdate']; 
                // $('#datepicker3').val('');
                //            $('#datepicker3').datepicker('setDate', new Date(data[0].start_date));                   
                    counter++;
                    var newRow = $('<tr id="trContent"><td style="vertical-align:middle"><label id="idnum">' +
                        counter + '</label></td><td style="vertical-align:middle"><button id="btnDeleteDecided" class="btn btn-danger btn-xs editor_remove"><i class="glyphicon glyphicon-remove"></i> </button></td><td class="type"><select name="drDecidedType_'+counter+'" id="drDecidedType" class="form-control"><option value="">--- Chọn loại hồ sơ ---</option><option value="MGHP">Miễn giảm học phí</option><option value="CPHT">Chi phí học tập</option><option value="HTAT">Hỗ trợ ăn trưa</option><option value="HTBT">Hỗ trợ bán trú</option><option value="NGNA">Hỗ trợ người nấu ăn</option><option value="HSKT">Hỗ trợ học sinh khuyết tật</option><option value="HSDTTS">Hỗ trợ học sinh dân tộc thiểu số tại huyện Mù Cang Chải và Trạm tấu</option><option value="TONGHOP">Chế độ chính sách ưu đãi</option></select></td><td class="code"><input class="form-control" type="text" name="txtDecidedCode_'+counter+'" id="txtDecidedCode" value="' + arrProfileDec[i]['decided_code'] + '"/></td><td class="number"><input type="text" class="form-control" name="txtDecidedNumber_'+counter+'" id="txtDecidedNumber" value="' + arrProfileDec[i]['decided_number'] + '"/></td><td class="form-control" class="confirmation"><input type="text" class="form-control" name="txtDecidedConfirmation_'+counter+'" id="txtDecidedConfirmation" value="' + arrProfileDec[i]['decided_confirmation'] + '"/></td><td class="confirmdate"><input type="text" placeholder="ngày-tháng-năm" name="txtDecidedConfirmDate_'+counter+'" class="form-control" id="txtDecidedConfirmDate" value="' + dated + '"/></td><td class="uploadfile"><input type="file" name="txtDecidedFileUpload_'+counter+'" id="txtDecidedFileUpload"></td><td class="oldfile" style="vertical-align:middle"><a style="cursor:pointer" onclick="download_quyetdinh('+arrProfileDec[i]['decided_id']+')"><label style="cursor:pointer" id="lblOldfile" >' + arrProfileDec[i]['decided_filename'] + '</label></a></td></tr>');

                    $('#tbDecided').append(newRow);
                    $("select[name*='drDecidedType_"+counter+"']").val(arrProfileDec[i]['decided_type']).trigger('change');
                  //  $('#drDecidedType').val(arrProfileDec[i]['decided_type']).trigger('change');
                  $("input[name*='txtDecidedFileUpload_"+counter+"']").filestyle({
                        buttonText : ' ',
                        buttonName : 'btn-info'
                    });
                    $("input[name*='txtDecidedConfirmDate_"+counter+"']").datepicker({
                   // $('#txtDecidedConfirmDate').datepicker({
                      format: 'dd-mm-yyyy',
                      autoclose: true
                    });
                    $("input[name*='txtDecidedConfirmDate_"+counter+"']").datepicker('setDate', new Date(arrProfileDec[i]['decided_confirmdate']));
                }

            }, error: function(data) {
            }
            
        });
        
    }
    $('select#viewTableProfile').change(function() {
       GET_INITIAL_NGHILC();
       loaddataProfile($(this).val(),$('select#sltTruongGrid').val(),$('select#sltLopGrid').val(), $('#txtSearchProfile').val());
    });
    $('select#sltTruong').change(function() {
    	if(parseInt($(this).val()) != 0){
            loading();
    		loadComboxLop($(this).val(),'sltLop',function(){
                closeLoading();
            });
            $('select#sltLop').removeAttr('disabled');
    		$('select#txtYearProfile').removeAttr('disabled');
    	}else{
    		$('select#sltLop').html('<option value="">--Chọn lớp--</option>');
    		$('select#sltLop').attr('disabled','disabled');
    	}
    });
    $('select#sltTruongGrid').change(function() {
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
    	loaddataProfile($('select#viewTableProfile').val(),$(this).val(),$('select#sltLopGrid').val(), $('#txtSearchProfile').val());
    });

    $('#drSchoolUpto').change(function() {
        if($(this).val() != ""){
            if(parseInt($(this).val()) != 0){
                loading();
                loadComboxLop($(this).val(),'drClassUpto',function(){
                    closeLoading();
                });
                $('#drClassUpto').removeAttr('disabled');
            }else{
                $('#drClassUpto').html('<option value="">--Chọn lớp--</option>');
                $('#drClassUpto').attr('disabled','disabled');
            }
        }else{
            resetControl();
            $('#drClassUpto').html('<option value="">--Chọn lớp--</option>');
            $('#drClassUpto').attr('disabled','disabled');
            $('#drYearUpto').html('<option value="">--Chọn năm học--</option>');
            $('#drYearUpto').attr('disabled','disabled');
            $('#drClassNext').html('<option value="">--Chọn lớp--</option>');
            $('#drClassNext').attr('disabled','disabled');
            
           
        }
    });

    $('#drClassUpto').change(function() {
        if($(this).val() != ""){
            if($('#drYearUpto').val()!=""){
                $('#upClass-select-all').prop('checked', false);
                loadTableUpto($('#drPagingUpto').val());
            }else{
                var class_idUpto = $(this).val();
                loading();
                var v_jsonData = { PROFILECLASS: class_idUpto };
                var show_html = "";
                PostToServer('/ho-so/hoc-sinh/getYearHistory',v_jsonData,function(dataget){
                        closeLoading();
                        var data = dataget.year_his;
                        var lop = dataget.levelClass;
                        $("#drYearUpto").removeAttr('disabled');
                        var $strClass = 0;
                        show_html += "<option value=''>--Chọn năm học--</option>";
                        for (var i = 0; i < data.length; i++) {
                            var text = data[i]['history_year'].toString();
                            show_html += "<option value='" + text + "'>" + text + "</option>";
                        }
                        $("#drYearUpto").html(show_html);
                        loadComboxLop(lop[0].class_schools_id, 'StlClassNext',null,null,lop[0].level_level);
                        loadComboxLop(lop[0].class_schools_id, 'drClassBack',null,null,lop[0].level_level,false);
                        $('#drClassBack').hide();
                        $('#labelClassBack').hide();
                        $('#drClassBack').attr('disabled','disabled');
                        $('#labelOutProfile').hide();
                        $('#dateOutProfile').hide();
                        $('#dateOutProfile').attr('disabled','disabled');
                },null,"","","");
            }
        }else{
            resetControl();
            $('#drYearUpto').html('<option value="">--Chọn năm học--</option>');
            $('#drYearUpto').attr('disabled','disabled');
            //$('#drClassNext').html('<option value="">--Chọn lớp--</option>');
            $('#drClassNext').attr('disabled','disabled');    
        }
    });

    $('select#drClassNext').change(function() {

           if(parseInt($(this).val()) === 2){
            // Nghỉ học
                $('#btnUpto').html("Thực hiện");
                $('#dateOutProfile').show();
                $('#labelOutProfile').show();
                $('#dateOutProfile').removeAttr('disabled');
                $('#dateOutProfile').datepicker('setDate', new Date());  
                // đóng form học lại
                $('#drClassBack').hide();
                $('#labelClassBack').hide();
                $('#drClassBack').addClass('disabled','disabled');
                $('#drClassBack').val('').trigger('change');

                // đóng form lên lớp
                $('#labelClassNext').hide();
                $('#StlClassNext').addClass('hidden');
                $('#StlClassNext').addClass('disabled','disabled');
                $('#StlClassNext').val('').trigger('change');

           }else if(parseInt($(this).val()) === 3){
            // Học lại
                $('#btnUpto').html("Thực hiện");
                $('#drClassBack').show();
                $('#labelClassBack').show();
                $('#drClassBack').removeAttr('disabled');
                // đóng form nghỉ học
                $('#dateOutProfile').hide();
                $('#labelOutProfile').hide();
                $('#dateOutProfile').addClass('disabled','disabled');
                // đóng form lên lớp
                $('#labelClassNext').hide();
                $('#StlClassNext').addClass('hidden');
                $('#StlClassNext').addClass('disabled','disabled');
                $('#StlClassNext').val('').trigger('change');

           }else if(parseInt($(this).val()) === 1){
            // Lên lớp
                $('#btnUpto').html("Lên lớp");
                $('#labelClassNext').show();
                $('#StlClassNext').removeClass('hidden');
                $('#StlClassNext').show();
                $('#StlClassNext').removeAttr('disabled');
                // đóng form học lại
                $('#drClassBack').hide();
                $('#labelClassBack').hide();
                $('#drClassBack').addClass('disabled','disabled');
                $('#drClassBack').val('').trigger('change');

                // đóng form nghỉ học
                $('#dateOutProfile').hide();
                $('#labelOutProfile').hide();
                $('#dateOutProfile').addClass('disabled','disabled');
                //$('#dateOutProfile').datepicker('setDate', new Date());  
           }else{
                // Chưa chọn
                $('#btnUpto').html("Thực hiện");
                $('#labelClassNext').hide();
                $('#StlClassNext').addClass('hidden');
                $('#StlClassNext').addClass('disabled','disabled');
                $('#StlClassNext').val('').trigger('change');
                // đóng form học lại
                $('#drClassBack').hide();
                $('#labelClassBack').hide();
                $('#drClassBack').addClass('disabled','disabled');
                $('#drClassBack').val('').trigger('change');
                // đóng form nghỉ học
                $('#dateOutProfile').hide();
                $('#labelOutProfile').hide();
                $('#dateOutProfile').addClass('disabled','disabled');
           }
    });
    $('select#sltLopGrid').change(function() {
    	GET_INITIAL_NGHILC();
    	loaddataProfile($('select#viewTableProfile').val(),$('select#sltTruongGrid').val(),$(this).val(), $('#txtSearchProfile').val());
    });
    

    $('select#sltTinh').change(function() {
        loading();
    		loadComboxTinhThanh($(this).val(),'sltQuan', function(){closeLoading();});
    		$('select#sltQuan').removeAttr('disabled');
    		$('select#sltPhuong').attr('disabled','disabled');
    		$('select#sltPhuong').html('<option value="">--Chọn danh mục--</option>');

    });
    $('select#sltQuan').change(function() {
        loading();
    	loadComboxTinhThanh($(this).val(),'sltPhuong', function(){closeLoading();});
    	$('select#sltPhuong').removeAttr('disabled');
    	//$('select#sltPhuong').attr('disabled','disabled');
    });


    $('input#ckbNghihoc').click(function() {
		if (!$(this).is(':checked')) {
            $('div#divNgayNghi').attr('hidden','hidden');
            $('div#divNgayNghi').attr('disabled','disabled');
			
        }else{
        	$('div#divNgayNghi').removeAttr('hidden');
        	$('div#divNgayNghi').removeAttr('disabled');
        }
		$('input#txtDateNghi').val('');
    });
    
    $('a#btnInsertKinhPhiDoiTuong').click(function(){
        resetControl(0);
        insert=true;
        $("#btnResetKinhPhiDoiTuong").show();
        $("#btnSaveKinhPhiDoiTuong").html('<i class="glyphicon glyphicon-plus-sign"></i> Lưu');
    });
    $('button#btnCancelKinhPhiDoiTuong').click(function(){
        
        resetControl(1);
    });

    loadComboxDoiTuong();
    $('button#saveProfile').click(function(){
           var form_datas = new FormData();
           var decided = true;
        //var arrObjDecided = [];
        var num = 0;
        if(counter > 0){
        $('#tbDecided tr').each(function(i) {
            num = i;
            var idnum = $(this).find("#idnum").text();
            var v_type = $(this).find("#drDecidedType").val();

            var v_code = $(this).find("#txtDecidedCode").val();

            //$(this).find("#txtDecidedName").val();                

            var v_number = $(this).find("#txtDecidedNumber").val();                

            var v_confirmation = $(this).find("#txtDecidedConfirmation").val();                

            var v_confirmdate = $(this).find("#txtDecidedConfirmDate").val();        

            var v_fildOld = $(this).find("#lblOldfile").text();
            var v_name = change_alias(v_type+'_'+v_code+'_'+v_number+'_'+v_confirmdate); 
            if(v_type == "" || v_type == undefined){
                utility.messagehide("messageDangersQD", "Xin mời chọn loại quyết định", 1, 5000);
                $("input[name*='drDecidedType_"+(idnum)+"']").focus();
                decided = false;
                return;    
            }else{
                if(v_code == "" || v_code == undefined){
                    utility.messagehide("messageDangersQD", "Xin mời nhập mã quyết định", 1, 5000);
                    $("input[name*='txtDecidedCode_"+(idnum)+"']").focus();
                    decided = false;
                    return;  
                }else{
                    if(v_name == "" || v_name == undefined){
                        utility.messagehide("messageDangersQD", "Xin mời nhập tên quyết định", 1, 5000);
                        $("input[name*='txtDecidedName_"+(idnum)+"']").focus();
                        decided = false;
                        return;  
                    }else{
                        if(v_number == "" || v_number == undefined){
                            utility.messagehide("messageDangersQD", "Xin mời nhập số quyết định", 1, 5000);
                            $("input[name*='txtDecidedNumber_"+(idnum)+"']").focus();
                            decided = false;
                            return;  
                        }else{
                            if(v_confirmation == "" || v_confirmation == undefined){
                                utility.messagehide("messageDangersQD", "Xin mời nhập cơ quan xác nhận", 1, 5000);
                                $("input[name*='txtDecidedConfirmation_"+(idnum)+"']").focus();
                                decided = false;
                                return;  
                            }else{
                                if(v_confirmdate == "" || v_confirmdate == undefined){
                                    utility.messagehide("messageDangersQD", "Xin mời nhập ngày xác nhận", 1, 5000);
                                    $("input[name*='txtDecidedConfirmDate_"+(idnum)+"']").focus();
                                    decided = false;
                                    return;  
                                }else{
                                    if($(this).find("#txtDecidedFileUpload").prop('files')[0] == undefined && v_fildOld == ""){
                                        utility.messagehide("messageDangersQD", "Xin mời chọn đính kèm", 1, 5000);
                                        $("label[name*='labelAttach_"+(idnum)+"']").removeClass('btn-success').addClass('btn-warning');
                                        decided = false;
                                        return;  //btn-warning // btn-success
                                    }else{
                                        $("label[name*='labelAttach_"+(idnum)+"']").addClass('btn-success').removeClass('btn-warning');
                                        form_datas.append("decided_type_"+i, v_type); 
                                        form_datas.append("code_"+i, v_code); 
                                        form_datas.append("name_"+i, v_name); 
                                        form_datas.append("confirmation_"+i, v_confirmation); 
                                        form_datas.append("confirmdate_"+i, v_confirmdate); 
                                        form_datas.append("number_"+i, v_number);
                                        form_datas.append("file_"+i, $(this).find("#txtDecidedFileUpload").prop('files')[0]);
                                        form_datas.append("fileold_"+i, v_fildOld);
                                        decided = true;
                                    }
                                }
                            }
                        }
                    }
                }
            }                
            

        });
        form_datas.append("decided_number", num+1);
}

        var v_status;
        var status = $("#ckbNghihoc").is(":checked");
        if (status == true) { v_status = 1; }
        else { v_status = 0; }
        var v_statusNQ57;
        var status_NQ57 = $("#ckbNQ57").is(":checked");
        if (status_NQ57 == true) { v_statusNQ57 = 1; }
        else { v_statusNQ57 = 0; }

        var arrSubID = [];var str = "";
        var $el = $(".multiselect-container");
        $el.find('li.active input').each(function(){
            str += $(this).val() + "-";
        });

        var year = $('#sltYear').val();
        var numberYear = 0;
        // console.log(year);
        if (year !== null && year !== "" && year !== undefined) {
            var ky = year.split("-");
            numberYear = ky[1];
        }

        form_datas.append('PROFILEID', profile_id);
        // form_datas.append('PROFILECODE', $('#txtCodeProfile').val());
        form_datas.append('PROFILENAME', $('#txtNameProfile').val());
        form_datas.append('PROFILEBIRTHDAY', $('#txtBirthday').val());
        form_datas.append('PROFILENATIONALID', $('#sltDantoc').val());
        form_datas.append('PROFILESITE1', $('#sltTinh').val());
        form_datas.append('PROFILESITE2', $('#sltQuan').val());
        form_datas.append('PROFILESITE3', $('#sltPhuong').val());
        form_datas.append('PROFILEHOUSEHOLD', $('#txtThonxom').val());
        form_datas.append('PROFILEPARENTNAME', $('#txtParent').val());
        form_datas.append('PROFILEYEAR', $('#txtYearProfile').val());
        form_datas.append('PROFILESCHOOLID', $('#sltTruong').val());
        form_datas.append('PROFILECLASSID', $('#sltLop').val());
        form_datas.append('PROFILESTATUS', v_status);
        form_datas.append('PROFILESTATUSNQ57', v_statusNQ57);
        form_datas.append('PROFILELEAVESCHOOLDATE', $('#txtDateNghi').val());
        form_datas.append('ARRSUBJECTID', str.substr(0,str.length-1));
        form_datas.append('ARRDECIDED', "");
        form_datas.append('PROFILEBANTRU', $('#sltBantru').val());
        form_datas.append('PROFILEKM', $('#txtKhoangcach').val());
        form_datas.append('PROFILEGIAOTHONG', $('#drGiaoThong').val());

        form_datas.append('CURRENTYEAR', numberYear);

        messageValidate = "";
        validateInput();
        //alert(messageValidate);
        if (messageValidate !== "") {

            utility.messagehide("messageDangers", messageValidate, 1, 5000);

            return;
        }
        else{
            if(decided){
                if (profile_id == 0) { 

                    insertProfile(form_datas); 
                }
                else { 
                    updateProfile(form_datas); 
                }
            }
        }
    });

    $('button#btnClosePopupProfile').click(function(){
        var truong = $('#drSchoolTHCD').val();
        var hocky = $('#sltYear').val();
        if (truong !== null && truong !== "" && hocky !== null && hocky !== "") {
            GET_INITIAL_NGHILC();
            loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
        }
        
        resetControl();
    });

    $('button.close').click(function(){
        var truong = $('#drSchoolTHCD').val();
        var hocky = $('#sltYear').val();
        if (truong !== null && truong !== "" && hocky !== null && hocky !== "") {
            GET_INITIAL_NGHILC();
            loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
        }
        
        resetControl();
    });

    //Get Table for Year Popup--------------------------------------------------------------------------------------
    $("#drYearUpto").change(function(){
        $('#upClass-select-all').prop('checked', false);
        GET_INITIAL_NGHILC();
        loadTableUpto($('#drPagingUpto').val());
    });

    $("#cbxChooseAll").click(function(){
        $('input#cbxChooseItem').not(this).prop('checked', this.checked);
    });

    $("#btnUpto").click(function(){
            // var $row = $('table#tablePopup').closest("tr");
            // var $strCode = $row.find("#tdChoosePopup").text();
            var arrProfileID = new Array();
            var classID = $('#drClassUpto').val();
            var year = "";
            var classID_next = $('#drClassNext').val();
            //var ext = $('#drClassNext').val();
            $("input#cbxChooseItem").each(function () {
                if ($(this).is(':checked')) {
                    var profileId = $(this).attr('data');
                    //var classId = $(this).attr('data-class');
                    arrProfileID.push(profileId);
                    //classID = classId;
                    year = $(this).attr('data-year');
                }
            });
            // console.log(arrProfileID);
            // console.log(classID);
            if (arrProfileID.length <= 0) { // Chưa chọn học sinh
                var message = "Vui lòng chọn học sinh !";
                utility.message("Thông báo", message, null, 3000,1);
            }else{// đã chọn học sinh
                if(parseInt(classID_next) === 1){
                    if($('#StlClassNext').val() === null || $('#StlClassNext').val() === ''){
                        utility.message("Thông báo", "Vui lòng lớp tiếp theo.", null, 3000,1);
                        return;
                    }else{
                        controlClass("",$('#StlClassNext').val(),"",arrProfileID,classID,year,classID_next);
                    }
                }else if(parseInt(classID_next) === 2){
                    if($('#dateOutProfile').val() === null || $('#dateOutProfile').val() === ''){
                        utility.message("Thông báo", "Vui lòng nhập ngày nghỉ học đúng định dạng.", null, 3000,1);
                        return;
                    }else{
                        controlClass("","",$('#dateOutProfile').val(),arrProfileID,classID,year,classID_next);
                    }
                }else if(parseInt(classID_next) === 3){
                    if($('#drClassBack').val() === null || $('#drClassBack').val() === ''){
                        utility.message("Thông báo", "Vui lòng chọn lớp.", null, 3000,1);
                        return;
                    }else{
                        controlClass($('#drClassBack').val(),"","",arrProfileID,classID,year,classID_next);
                    }
                }
            }

            //     if(classID_next == 0){
            //         var message = "Vui lòng chọn lớp tiếp theo!";
            //         utility.message("Thông báo", message, null, 3000,1);
            //     }else{
            //         if(parseInt(classID_next) === 2){// nghỉ học
            //             if($('#dateOutProfile').val() == null || $('#dateOutProfile').val() == "" || $('#dateOutProfile').val() == 0){
            //                 var message = "Vui lòng chọn ngày nghỉ học!";
            //                 utility.message("Thông báo", message, null, 3000,1);
            //             }else{

                                
            //                 }
            //             }else if(parseInt(classID_next) === 3){// học lại
            //                 if($('#drClassBack').val() == null || $('#drClassBack').val() == "" || $('#drClassBack').val() == 0){
            //                     var message = "Vui lòng chọn lớp học lại!";
            //                     utility.message("Thông báo", message, null, 3000,1);
            //                 }else{

            //                     var v_jsonClass = JSON.stringify({CLASSBACK:$('#drClassBack').val(), DATEOUTPROFIEL:$('#dateOutProfile').val(), ARRPROFILEID: arrProfileID, CLASSID: classID, YEAR: year, CLASSIDNEXT: classID_next });
            //                     //console.log(v_jsonClass);
            //                     $.ajax({
            //                         type: "POST",
            //                         url:'/ho-so/hoc-sinh/uptoprofile',
            //                         data: v_jsonClass,
            //                         dataType: 'json',
            //                         contentType: 'application/json; charset=utf-8',
            //                         headers: {
            //                             'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            //                         },
            //                         success: function(data) {
            //                             //console.log(data);
            //                             if (data['success'] != "" && data['success'] != undefined) {
            //                                 utility.message("Thông báo",data['success'],null,3000);
            //                                 $('#uptoClass').DataTable().clear().draw().destroy();
            //                                 resetControl();
            //                                 GET_INITIAL_NGHILC();
            //                                 $('#upClass-select-all').prop('checked', false);
            //                                 loaddataProfile($('select#viewTableProfile').val(),$('select#sltTruongGrid').val(),$('select#sltLopGrid').val(), $('#txtSearchProfile').val());
            //                             }
            //                             if (data['error'] != "" && data['error'] != undefined) {
            //                                 utility.message("Thông báo",data['error'],null,3000,1);
            //                             }
            //                         }, error: function(data) {
            //                         }
            //                     });
            //                 }
            //         }else if(parseInt(classID_next) === 1){ // lên lớp

            //                     var v_jsonClass = JSON.stringify({CLASSBACK:$('#drClassBack').val(), DATEOUTPROFIEL:$('#dateOutProfile').val(), ARRPROFILEID: arrProfileID, CLASSID: classID, YEAR: year, CLASSIDNEXT: classID_next });
            //                     //console.log(v_jsonClass);
            //                     $.ajax({
            //                         type: "POST",
            //                         url:'/ho-so/hoc-sinh/uptoprofile',
            //                         data: v_jsonClass,
            //                         dataType: 'json',
            //                         contentType: 'application/json; charset=utf-8',
            //                         headers: {
            //                             'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            //                         },
            //                         success: function(data) {
            //                             //console.log(data);
            //                             if (data['success'] != "" && data['success'] != undefined) {
            //                                 utility.message("Thông báo",data['success'],null,3000);
            //                                 $('#uptoClass').DataTable().clear().draw().destroy();
            //                                 resetControl();
            //                                 GET_INITIAL_NGHILC();
            //                                 $('#upClass-select-all').prop('checked', false);
            //                                 loaddataProfile($('select#viewTableProfile').val(),$('select#sltTruongGrid').val(),$('select#sltLopGrid').val(), $('#txtSearchProfile').val());
            //                             }
            //                             if (data['error'] != "" && data['error'] != undefined) {
            //                                 utility.message("Thông báo",data['error'],null,3000,1);
            //                             }
            //                         }, error: function(data) {
            //                         }
            //                     });
            //                 }
            //     }
            // }
        });


    $("#btnRevert").click(function(){
        //utility.confirmAlert("Thông báo", "Hoàn tác về thao tác mới nhất.Chắn chắn?", function () {
            // var $row = $('table#tablePopup').closest("tr");
            // var $strCode = $row.find("#tdChoosePopup").text();
            var arrProfileID = new Array();
            var classID = "";
            var year = "";
            var classID_next = $('#drClassNext').val();
            $("input#cbxChooseItem").each(function () {
                if ($(this).is(':checked')) {
                    var profileId = $(this).attr('data');
                    var classId = $(this).attr('data-class');
                    arrProfileID.push(profileId);
                    classID = classId;
                    year = $(this).attr('data-year');
                }
            });
            // console.log(arrProfileID);
            // console.log(classID);
            if (arrProfileID.length <= 0) {
                var message = "Vui lòng chọn học sinh lên lớp!";
                utility.message("Thông báo", message, null, 3000);
            }else{
               //  if(classID_next == null || classID_next == "" || classID_next == 0){
               //     var message = "Vui lòng chọn lớp tiếp theo!";
               //     utility.message("Thông báo", message, null, 3000,1);
                //} else {
                    var v_jsonClass = JSON.stringify({ ARRPROFILEID: arrProfileID, CLASSID: classID, YEAR: year, CLASSIDNEXT: classID_next });
                   // console.log(v_jsonClass);
                    $.ajax({
                        type: "POST",
                        url:'/ho-so/hoc-sinh/revertprofile',
                        data: v_jsonClass,
                        dataType: 'json',
                        contentType: 'application/json; charset=utf-8',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                        },
                        success: function(data) {
                            //console.log(data);
                            if (data['success'] != "" && data['success'] != undefined) {
                                utility.message("Thông báo",data['success'],null,3000);
                                $('#uptoClass').DataTable().clear().draw().destroy();
                                resetControl();
                                GET_INITIAL_NGHILC();
                                $('#upClass-select-all').prop('checked', false);
                                loaddataProfile($('select#viewTableProfile').val(),$('select#sltTruongGrid').val(),$('select#sltLopGrid').val(), $('#txtSearchProfile').val());
                            }
                            if (data['error'] != "" && data['error'] != undefined) {
                                utility.message("Thông báo",data['error'],null,3000,1);
                            }
                        }, error: function(data) {
                        }
                    });
               // }
            }
    });

//--------------------------------------------------------------Phần danh sách----------------------------------------------------
    $('#btnInsertTHCD').click(function(){
        $('#btnInsertTHCD').button('loading');
        var message = "";
        message = validatePopupTongHopCheDo();
        if (message !== "") {
          utility.messagehide("group_message_THCD", message, 1, 5000);
          $('#btnInsertTHCD').button('reset');
          return;
        }

        // console.log($('#sltChedo').val());

        // var file_data = $('input#fileAttack').prop('files')[0];   
        var form_datas = new FormData();   
        // form_datas.append('FILE', file_data);
        form_datas.append('SCHOOLID', $('#drSchoolTHCD').val());
        form_datas.append('YEAR', $('#sltYear').val());
        form_datas.append('REPORTNAME', $('#txtNameDSTHCD').val());
        // form_datas.append('CREATENAME', $('#txtNguoiLapTHCD').val());
        // form_datas.append('SIGNNAME', $('#txtNguoiKyTHCD').val());
        form_datas.append('ARRCHEDO', $('#sltChedo').val());
        form_datas.append('NOTE', $('#txtGhiChuTHCD').val());

        lapdanhsachDanhSachTongHop(form_datas);
    });

    $('#btnInsertTHCD_PheDuyet').click(function(){
        $('#btnInsertTHCD_PheDuyet').button('loading');
        var message = "";
        message = validatePopupTongHopCheDo();
        if (message !== "") {
          utility.messagehide("group_message_THCD", message, 1, 5000);
          $('#btnInsertTHCD_PheDuyet').button('reset');
          return;
        }

        // console.log($('#sltChedo').val());

        // var file_data = $('input#fileAttack').prop('files')[0];   
        var form_datas = new FormData();   
        // form_datas.append('FILE', file_data);
        form_datas.append('SCHOOLID', $('#drSchoolTHCD').val());
        form_datas.append('YEAR', $('#sltYear').val());
        form_datas.append('REPORTNAME', $('#txtNameDSTHCD').val());
        // form_datas.append('CREATENAME', $('#txtNguoiLapTHCD').val());
        // form_datas.append('SIGNNAME', $('#txtNguoiKyTHCD').val());
        form_datas.append('ARRCHEDO', $('#sltChedo').val());
        form_datas.append('NOTE', $('#txtGhiChuTHCD').val());

        lapdanhsachDanhSachTongHop_PheDuyet(form_datas);
    });

    $('#btnInsertTHCD_ThamDinh').click(function(){
        $('#btnInsertTHCD_ThamDinh').button('loading');
        var message = "";
        message = validatePopupTongHopCheDo();
        if (message !== "") {
          utility.messagehide("group_message_THCD", message, 1, 5000);
          $('#btnInsertTHCD_ThamDinh').button('reset');
          return;
        }

        // console.log($('#sltChedo').val());

        // var file_data = $('input#fileAttack').prop('files')[0];   
        var form_datas = new FormData();   
        // form_datas.append('FILE', file_data);
        form_datas.append('SCHOOLID', $('#drSchoolTHCD').val());
        form_datas.append('YEAR', $('#sltYear').val());
        form_datas.append('REPORTNAME', $('#txtNameDSTHCD').val());
        // form_datas.append('CREATENAME', $('#txtNguoiLapTHCD').val());
        // form_datas.append('SIGNNAME', $('#txtNguoiKyTHCD').val());
        form_datas.append('ARRCHEDO', $('#sltChedo').val());
        form_datas.append('NOTE', $('#txtGhiChuTHCD').val());

        lapdanhsachDanhSachTongHop_ThamDinh(form_datas);
    });

    $('#btnApprovedTHCD').click(function(){        
        var strData = "";
        $('input#chilCheck').each(function () {
            
            if (this.checked) {
                var sThisVal = $(this).val();
                strData += (strData=="" ? sThisVal : "-" + sThisVal);
            }
        });

        var note = $('#txtGhiChuTHCD').val();
        // console.log (strData);
        approvedChedo(strData, note);
    });

    $('#btnApprovedTHCDPheDuyet').click(function(){        
        var strData = "";
        // input[type=checkbox]
        $('input#chilCheckPD').each(function () {
            
            if (this.checked) {
                var sThisVal = $(this).val();
                strData += (strData=="" ? sThisVal : "-" + sThisVal);
            }
        });

        var truong = $('#drSchoolTHCD').val();
        var socongvan = $('#sltCongvan').val();
        var note = $('#txtGhiChuTHCD').val();
        // console.log (strData);
        approvedChedoPheDuyet(strData, truong, socongvan, note);
    });

    $('#btnApprovedTHCDThamDinh').click(function(){        
        var strData = "";
        $('input#chilCheckTD').each(function () {
            
            if (this.checked) {
                var sThisVal = $(this).val();
                strData += (strData=="" ? sThisVal : "-" + sThisVal);
            }
        });

        var truong = $('#drSchoolTHCD').val();
        var socongvan = $('#sltCongvan').val();
        var note = $('#txtGhiChuTHCD').val();
        // console.log (strData);
        approvedChedoThamDinh(strData, truong, socongvan, note);
    });


    $("#btnViewDanhSachTongHopGroup").click(function(){
        // console.log("Click");
        GET_INITIAL_NGHILC();
        loaddataDanhSachGroupA($('#drPagingDanhsach').val(), $('#txtSearchProfileLapdanhsach').val());
    });

    $("#btnViewDanhSachTruongLap").click(function(){
        // console.log("Click");
        GET_INITIAL_NGHILC();
        loaddataDanhSachGroupB($('#drPagingDanhsach').val(), $('#txtSearchProfileLapdanhsach').val());
    });

    $("#btnLoadDataTruong").click(function(){
        // console.log("Click");
        GET_INITIAL_NGHILC();
        loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
    });

    $("#btnLoadDataPhong").click(function(){
        // console.log("Click");
        GET_INITIAL_NGHILC();
        loadlistApproved($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
    });

    $("#btnLoadDataSo").click(function(){
        // console.log("Click");
        GET_INITIAL_NGHILC();
        loadlistApprovedPheDuyet($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
    });

    $("#btnTralai").click(function(){
        var strData = "";
        $('input#Phongchoose').each(function () {
            
            if (this.checked) {
                var sThisVal = $(this).val();
                strData += (strData=="" ? sThisVal : "-" + sThisVal);
            }
        });

        var message = "";
        message = validateDanhsachtralai(strData);
        if (message !== "") {
          utility.messagehide("messageValidate", message, 1, 5000);
          return;
        }

        $("#txtNote").val("");
        $("#myModalRevertPhong").modal("show");
        
        // var form_datas = new FormData();
        // form_datas.append('ARRPROFILEID', strData);
        // form_datas.append('NOTE', $('#txtNote').val());
        // // console.log(strData);
        // danhsachPhongtralai(form_datas);
    });

    $("#btnPhongRevert").click(function(){

        var strData = "";
        $('input#Phongchoose').each(function () {
            
            if (this.checked) {
                var sThisVal = $(this).val();
                strData += (strData=="" ? sThisVal : "-" + sThisVal);
            }
        });

        var reportName = $('#sltCongvan').val();
        var reportType = $('#sltLoaiChedo').val();

        var form_datas = new FormData();
        form_datas.append('ARRPROFILEID', strData);
        form_datas.append('NOTE', $('#txtNote').val());
        form_datas.append('REPORTNAME', reportName);
        form_datas.append('REPORTTYPE', reportType);
        // console.log(strData);
        danhsachPhongtralai(form_datas);
    });


    // $("#PhongChooseAll").click(function(){
    //     $('input#Phongchoose').not(this).prop('checked', this.checked);
    //     alert("Choose1");
    // });

    // $('#PhongChooseAll').change(function() {
    //     alert("Choose2");
    //         if ($('#PhongChooseAll').prop('checked'))
    //             $('[id*="Phongchoose"]').prop('checked', true);
    //         else
    //             $('[id*="Phongchoose"]').prop('checked', false);
    // });
});

var profile_id = 0;

//Check Permisstion
    var CODE_FEATURES ;
    function permission(callback, moduleId) {
        // console.log(moduleId);
        $.ajax({
            type: "GET",
            url: '/ho-so/hoc-sinh/permission/' + moduleId,
            success: function(data) {
                CODE_FEATURES = data.permission;
                if(callback!=null){
                    callback();
                }
            }, error: function(data) {
            }
        });
    };

    function check_Permission_Feature(featureCode) {
        // console.log(Object.values(CODE_FEATURES));
        // console.log(Object.values(CODE_FEATURES).indexOf("2"));        
        try {
            if (Object.values(CODE_FEATURES).indexOf(featureCode) >= 0) {
                //console.log(Object.values(CODE_FEATURES).indexOf(featureCode));
                return true;
            }
                
            return false;
        } catch (e) {
            console.log(e);
        }
        return true;
    }

    function loadComboxDantoc(callback, idchoise = null){
    //loading();
            $.ajax({
                type: "GET",
                url: '/danh-muc/load/dan-toc',
                success: function(dataget) {
                   // closeLoading();
                    $('#sltDantoc').html("");
                    var html_show = "";
                    if(dataget.length >0){
                     
                        html_show += "<option value=''>-- Chọn dân tộc --</option>";
                        for (var i = 0; i < dataget.length; i++) {
                            if (dataget[i].nationals_id === idchoise) {
                                html_show += "<option value='"+dataget[i].nationals_id+"' selected>"+dataget[i].nationals_name+"</option>";
                            }
                            else {
                                html_show += "<option value='"+dataget[i].nationals_id+"'>"+dataget[i].nationals_name+"</option>";
                            }
                        }
                        $('#sltDantoc').html(html_show);
                        // $("#sltDantoc").select2('24', "Mông");
                    }else{
                        $('#sltDantoc').html("<option value=''>-- Chưa có dân tộc --</option>");
                    }
                    if(callback != null){
                        callback();
                    }

                }, error: function(dataget) {
                }
            });
        };

        function loadComboxTruongHoc(id,callback,idchoise=null) {
            //loading();
            $.ajax({
                type: "GET",
                url: '/danh-muc/load/truong-hoc',
                success: function(data) {
                   // closeLoading();
                    var dataget = data.truong;
                    var datakhoi = data.khoi;
                    // <optgroup label="Cats">
                    // console.log(dataget);
                    // console.log(datakhoi);

                    $('#'+id).html("");
                   // $('#sltTruongGrid').html("");
                   // $('#drSchoolUpto').html("");
                    var html_show = "";
                    if(datakhoi.length > 0){
                        if(dataget.length > 1){
                            html_show += "<option value=''>-- Chọn trường học --</option>";
                                for (var j = 0; j < datakhoi.length; j++) {
                                html_show +="<optgroup label='"+datakhoi[j].unit_name+"'>";
                                    
                                    for (var i = 0; i < dataget.length; i++) {
                                        if(datakhoi[j].unit_id === dataget[i].schools_unit_id){
                                            if(idchoise != null){
                                                if(idchoise.split('-').length == 1 && parseInt(idchoise) != 0){
                                                   // if(idchoise===parseInt(dataget[i].schools_id)){
                                                    html_show += "<option value='"+dataget[i].schools_id+"' selected>"+dataget[i].schools_name+"</option>";
                                                   // }else{
                                                   //     html_show += "<option value='"+dataget[i].schools_id+"'>"+dataget[i].schools_name+"</option>";
                                                   // } 
                                                }else{
                                                    html_show += "<option value='"+dataget[i].schools_id+"'>"+dataget[i].schools_name+"</option>";
                                                }
                                            }
                                                    
                                        }
                                    }    
                                html_show +="</optgroup>"                           
                            }
                        }
                       // $('#sltTruongGrid').html(html_show);
                       // $('#drSchoolUpto').html(html_show);
                        else {
                            for (var i = 0; i < dataget.length; i++) {
                                    
                                html_show += "<option value='"+dataget[i].schools_id+"'>"+dataget[i].schools_name+"</option>";
                                var school_id = dataget[i].schools_id;
                                loadComboxLop(school_id,'sltLop',function(){
                                    // if(school_id > 0){
                                    //     $('select#sltLop').attr('disabled','disabled');
                                    // }else{
                                        $('select#sltLop').removeAttr('disabled');
                                      
                                    // }
                                });
                                // console.log(school_id);
                            }
                        }
                        $('#'+id).html(html_show);
                    }
                    // else if (dataget.length == 1){
                    // 	for (var i = 0; i < Things.length; i++) {
                    //         Things[i]
                    //     }
                    //   //  $('#sltTruong').html("<option value=''>-- Chưa có trường --</option>");
                    //   //  $('#drSchoolUpto').html("<option value=''>-- Chưa có trường --</option>");
                    // }
                    else {
                        $('#'+id).html("<option value=''>-- Chưa có trường --</option>");
                    }

                    if(callback != null){
                        callback();
                    }
                }, error: function(dataget) {
                }
            });
        };

        function loadComboxTruongHocSingle(id,callback,idchoise = null) {
        //loading();
            $.ajax({
                type: "GET",
                url: '/danh-muc/load/truong-hoc',
                success: function(data) {
                    var dataget = data.truong;
                    var datakhoi = data.khoi;

                    // console.log(dataget);
                    $('#'+id).html("");

                    var html_show = "";
                    if(datakhoi.length > 0){
                        html_show += "<option value=''>-- Chọn trường học --</option>";
                        for (var j = 0; j < datakhoi.length; j++) {
                            html_show +="<optgroup label='"+datakhoi[j].unit_name+"'>";
                                if(dataget.length > 0){
                                    // for (var i = 0; i < dataget.length; i++) {
                                    //     if(datakhoi[j].unit_id === dataget[i].schools_unit_id){
                                    //         if(parseInt(idchoise) === parseInt(dataget[i].schools_id)){
                                    //             html_show += "<option selected value='"+dataget[i].schools_id+"'>"+dataget[i].schools_name+"</option>";
                                    //         }else{
                                    //             html_show += "<option value='"+dataget[i].schools_id+"'>"+dataget[i].schools_name+"</option>";
                                    //         }
                                    //     }
                                    // }
                                    for (var i = 0; i < dataget.length; i++) {
                                        if(datakhoi[j].unit_id === dataget[i].schools_unit_id){
                                            if(idchoise != null){
                                                if(idchoise.split('-').length == 1 && parseInt(idchoise) != 0){
                                                   // if(idchoise===parseInt(dataget[i].schools_id)){
                                                    html_show += "<option value='"+dataget[i].schools_id+"' selected>"+dataget[i].schools_name+"</option>";
                                                   // }else{
                                                   //     html_show += "<option value='"+dataget[i].schools_id+"'>"+dataget[i].schools_name+"</option>";
                                                   // } 
                                                }else{
                                                    html_show += "<option value='"+dataget[i].schools_id+"'>"+dataget[i].schools_name+"</option>";
                                                }
                                            }
                                        }
                                    }
                                }    
                            html_show +="</optgroup>"
                        }
                        $('#'+id).html(html_show);
                       // $('#sltTruongGrid').html(html_show);
                       // $('#drSchoolUpto').html(html_show);
                    }else{
                        $('#'+id).html("<option value=''>-- Chưa có trường --</option>");
                      //  $('#sltTruong').html("<option value=''>-- Chưa có trường --</option>");
                      //  $('#drSchoolUpto').html("<option value=''>-- Chưa có trường --</option>");
                    }
                    if(callback!=null){
                        callback();
                    }
                }, error: function(dataget) {
                }
            });
        };



        function loadComboxTruongHocUpto(idchoise = null) {
        //loading();
            $.ajax({
                type: "GET",
                url: '/danh-muc/load/truong-hoc',
                success: function(data) {

                    var dataget = data.truong;
                    var datakhoi = data.khoi;

                    console.log(dataget);
                    $('#drSchoolUpto').html("");

                    var html_show = "";
                    if(datakhoi.length > 0){
                        html_show += "<option value=''>-- Chọn trường học --</option>";
                        for (var j = 0; j < datakhoi.length; j++) {
                            html_show +="<optgroup label='"+datakhoi[j].unit_name+"'>";
                                if(dataget.length > 0){
                                    // for (var i = 0; i < dataget.length; i++) {
                                    //     if(datakhoi[j].unit_id === dataget[i].schools_unit_id){
                                            
                                    //         html_show += "<option value='"+dataget[i].schools_id+"'>"+dataget[i].schools_name+"</option>";
                                    //     }
                                    // }
                                    for (var i = 0; i < dataget.length; i++) {
                                        if(datakhoi[j].unit_id === dataget[i].schools_unit_id){
                                            if(idchoise != null){
                                                if(idchoise.split('-').length == 1 && parseInt(idchoise) != 0){
                                                   // if(idchoise===parseInt(dataget[i].schools_id)){
                                                    html_show += "<option value='"+dataget[i].schools_id+"' selected>"+dataget[i].schools_name+"</option>";
                                                   // }else{
                                                   //     html_show += "<option value='"+dataget[i].schools_id+"'>"+dataget[i].schools_name+"</option>";
                                                   // } 
                                                }else{
                                                    html_show += "<option value='"+dataget[i].schools_id+"'>"+dataget[i].schools_name+"</option>";
                                                }
                                            }
                                                    
                                        }
                                    }
                                }    
                            html_show +="</optgroup>"
                        }
                        $('#drSchoolUpto').html(html_show);
                    }else{
                        $('#drSchoolUpto').html("<option value=''>-- Chưa có trường --</option>");
                    }
                    if(callback!=null){
                        callback();
                    }
                }, error: function(dataget) {
                }
            });
        };

function loadComboxLop(id,idselect,callback,idchoise=null,level=0,type=true) {
    if(id != null && id != ""){
       // loading();
        GetFromServer('/danh-muc/load/lop/'+id, function(dataget){
          //  closeLoading();
                $('#'+idselect).html("");
                    var html_show = "";
                    if(dataget.length >0){
                        html_show += "<option value=''>-- Chọn lớp --</option>";
                        // if(type){
                        //     if(level!=0){
                        //         html_show += "<option value='-1'>-- Nghỉ học --</option>"; 
                        //         html_show += "<option value='-2'>-- Học lại --</option>";    
                        //     }
                        // }
                        for (var i = 0; i < dataget.length; i++) {
                            if(level==0){
                                if(idchoise!=null){
                                    if(parseInt(dataget[i].class_id)===parseInt(idchoise)){
                                        html_show += "<option value='"+dataget[i].class_id+"' selected>"+dataget[i].class_name+"</option>";
                                    }else{
                                        html_show += "<option value='"+dataget[i].class_id+"'>"+dataget[i].class_name+"</option>";
                                    }
                                }else{
                                    html_show += "<option value='"+dataget[i].class_id+"'>"+dataget[i].class_name+"</option>";
                                }
                            }else{
                                if(idchoise!=null){
                                    if(parseInt(dataget[i].class_id)===parseInt(idchoise)){
                                        html_show += "<option value='"+dataget[i].class_id+"' selected>"+dataget[i].class_name+"</option>";
                                    }else{
                                        html_show += "<option value='"+dataget[i].class_id+"'>"+dataget[i].class_name+"</option>";
                                    }
                                }else{
                                    if(type){
                                        if(level < dataget[i].level_level){
                                            html_show += "<option value='"+dataget[i].class_id+"'>"+dataget[i].class_name+"</option>";
                                        }
                                    }else{
                                        if(parseInt(level) === parseInt(dataget[i].level_level)){
                                            html_show += "<option value='"+dataget[i].class_id+"'>"+dataget[i].class_name+"</option>";
                                        }
                                    }
                                }
                            }
                            
                        }
                        $('#'+idselect).html(html_show);
                    }else{
                        $('#'+idselect).html("<option value=''>-- Chưa có lớp --</option>");
                    }
                    if(callback != null){
                        callback(dataget);
                    }

                }
        , null, null, null, null);        
    }else{
        $('#'+idselect).html('<option value="">--Chọn lớp--</option>');
        $('#'+idselect).attr('disabled','disabled');
    }    
         };
function loadComboxDoiTuong() {
    //loading();
            $.ajax({
                type: "GET",
                url: '/danh-muc/load/doi-tuong',
                success: function(dataget) {
                 //   closeLoading();
                    $('#sltDoituong').html("");
                    var html_show = "";
                    if(dataget.length >0){
                      // $('.multiselect-selected-text').html('-- Chọn dân tộc --');
                       // html_show += "<option value=''>-- Chọn đối tượng --</option>";
                        for (var i = dataget.length - 1; i >= 0; i--) {
                            html_show += "<option value='"+dataget[i].subject_id+"'>"+dataget[i].subject_name+"</option>";
                        }
                        $('#sltDoituong').html(html_show);
                    }else{
                        $('#sltDoituong').html("<option value=''>-- Chưa có đối tượng --</option>");
                    }
                }, error: function(dataget) {
                }
            });
        };
function loadComboxTinhThanh(id,idselect,callback,idchoise=null) {
	//loading();
        return $.ajax({
                type: "GET",
                url: '/danh-muc/load/city/'+id,
                success: function(dataget) {
                   // closeLoading();
                    $('#'+idselect).html("");
                    var html_show = "";
                    if(dataget.length > 0){
                      //if(id===0){
                       		html_show += "<option value=''>-- Chọn danh mục --</option>";
                       //}
                        for (var i = 0; i < dataget.length; i++) {
                            if(parseInt(idchoise)===parseInt(dataget[i].site_id)){
                                html_show += "<option value='"+dataget[i].site_id+"' selected>"+dataget[i].site_name+"</option>";    
                            }else{
                                html_show += "<option value='"+dataget[i].site_id+"'>"+dataget[i].site_name+"</option>";
                            }
                        }
                        $('#'+idselect).html(html_show);
                    //   size = true;
                    }
                    else{
                        $('#'+idselect).html("<option value=''>-- Chưa có danh mục --</option>");
                    	//size = false;
                    }

                    if(callback != null){
                        callback();
                    }
                }, error: function(dataget) {
                }
            });
           // return size;
        };

function loadTableUpto(row){
    var school_id = $('#drSchoolUpto').val();
    var class_id = $('#drClassUpto').val();
    var year = $('#drYearUpto').val();
    var d = { start: (GET_START_RECORD_NGHILC()), limit : row, PROFILESCHOOL: school_id, PROFILECLASS: class_id, PROFILEYEAR: year };
    var v_jsonData = JSON.stringify(d);
    var show_html = "";
    if(year != "" && class_id != "" && school_id != ""){
    var t =  $('#uptoClass').DataTable({
            "language": {
                   "lengthMenu": "Hiển thị _MENU_ bản ghi" ,
                   "info": String.format("Hiển thị {0} đến {1} trên tổng {2} bản ghi", "_START_", "_END_", "_TOTAL_"),// "Showing page _PAGE_ of _PAGES_",
                   "infoEmpty": "",
                   "sSearch": "Tìm kiếm: ",
                   "paginate": {
                       "first": "First",
                       "last": "Last",
                       "next":"Trang sau",
                       "previous": "Trang trước"
                   },
                   "emptyTable": "Không tìm thấy dữ liệu"
               }, 
            "bDestroy": true,
            "ajax": {
                'type': 'POST',
                'url': '/ho-so/hoc-sinh/getProfilePopupUpto',
                'data': d,
                'headers': {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                }
            },
             "initComplete": function(settings, json){ 
                var info = this.api().page.info();
                if(parseInt(info.recordsTotal) > 0 ){
                    $('#drClassNext').removeAttr('disabled');
                    $('#btnUpto').removeAttr('disabled');
                    $('#btnRevert').removeAttr('disabled');
                }else{
                    $('#drClassNext').attr('disabled','disabled');
                    $('#btnUpto').attr('disabled','disabled');
                    $('#btnRevert').attr('disabled','disabled');
                }
        },
        'columnDefs': [
        {
            "searchable": false,
            "orderable": false,
            "targets": [0],
            'className': 'text-center',
            "width" : "5%",
            "render": function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
            }
        },
        {
             'targets': [1],
             "width" : "5%",
             'className': 'text-center',
             'render': function (mData, type, full, meta){
                return "<input type='checkbox' data='" + full.profile_id + "' data-class='" + full.class_id + "' data-year='" + full.history_year + "' id='cbxChooseItem'>";
      //           return '<input type="checkbox" name="id[]" value="'+full.profile_id+'">';

             }
        },
        // {
        //      'targets': [2],
        //      "width" : "20%",
        //      'data': "profile_code"
        // },
        {
             'targets': [2],
             "width" : "20%",
             'data': "profile_name"
        },
        {
             'targets': [3],
             "width" : "10%",
             'data': "profile_birthday",
            "render": function (data) {
                var date = new Date(data);
                var month = (date.getMonth()+1)+'';
                return ((date.getDate()+'').length > 1 ? date.getDate():"0"+date.getDate()) + "-" + (month.length > 1 ? month : "0" + month) + "-" + date.getFullYear();
            }
        },
        {
             'targets': [4],
             "width" : "10%",
             'data': "nationals_name"
        },
        {
             'targets': [5],
             "width" : "10%",
             'data': "profile_household"
        },
        {
             'targets': [6],
             "width" : "10%",
             'data': "profile_parentname"
        },
        {
             'targets': [7],
             "width" : "10%",
             'data': "schools_name"
        }
      ],
  });

   $('#upClass-select-all').on('click', function(){
      var rows = t.rows({ 'search': 'applied' }).nodes();      
      $('input[type="checkbox"]', rows).prop('checked', this.checked);
   });

   $('#uptoClass tbody').on('change', 'input[type="checkbox"]', function(){
      if(!this.checked){
         var el = $('#upClass-select-all').get(0);
         if(el && el.checked && ('indeterminate' in el)){
            el.indeterminate = true;
         }
      }
   });
}else{
    $('#drClassNext').attr('disabled','disabled');
    $('#drClassNext').val('').trigger('change');
    $('#drClassBack').hide();
    $('#labelClassBack').hide();
    $('#drClassBack').attr('disabled','disabled');

    $('#dateOutProfile').hide();
    $('#dateOutProfile').val('');
    $('#labelOutProfile').hide();
    $('#dateOutProfile').attr('disabled','disabled');
    $('#uptoClass').DataTable().clear().draw().destroy();
}
    
   // PostToServer('/ho-so/hoc-sinh/getProfilePopupUpto',d,function(dataget){
   //      $('#contentPopupUpto').html("");
   //          data = dataget.data;            
   //          if(data.length > 0){
   //              for (var i = 0; i < data.length; i++) {
   //                  show_html += "<tr>";
   //                  show_html += "<td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
   //                  show_html += "<td class='text-center' style='vertical-align:middle' id='tdChoosePopup' data='" + data[i]['profile_id'] + "' data-class='" + data[i]['class_name'] + "'>";
   //                  show_html += "<input type='checkbox' data='" + data[i]['profile_id'] + "' data-class='" + data[i]['class_id'] + "' data-year='" + data[i]['history_year'] + "' id='cbxChooseItem'>";
   //                  show_html += "</td>";
   //                  show_html += "<td>" + ConvertString(data[i]['profile_code']) + "</td>";
   //                  show_html += "<td>" + ConvertString(data[i]['profile_name']) + "</td>";
   //                  show_html += "<td>" + formatDates(data[i]['profile_birthday']) + "</td>";
   //                  show_html += "<td>" + ConvertString(data[i]['nationals_name']) + "</td>";
   //                  show_html += "<td>" + ConvertString(data[i]['profile_household']) + "</td>";
   //                  show_html += "<td>" + ConvertString(data[i]['profile_parentname']) + "</td>";
   //                  show_html += "<td>" + ConvertString(data[i]['schools_name']) + "</td>";
   //                  show_html += "<td>" + ConvertString(data[i]['level_name'] + "-" + data[i]['class_name']) + "</td>";
   //                  show_html += "<td>" + ConvertString(data[i]['history_year']) + "</td>";
   //                  show_html += "</tr>";
   //              }
                
   //              $('#drClassNext').removeAttr('disabled');
   //          } else {
   //              html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
   //          }

   //          $('#contentPopupUpto').html(show_html);
   //          $('#btnUpto').removeAttr('disabled');
   //          $('#btnRevert').removeAttr('disabled');
   // },function(dataget){
   //      console.log('Có lỗi xảy ra trong quá trình xử lý ' + dataget);
   // },"btnUpto","","");
         
};

function loaddataProfile(row,truong,lop,keysearch) {
        if(lop===null) lop=0;
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit : row,
            id_truong: truong,
            id_lop: lop,
            key: keysearch
        };
            $.ajax({
                type: "POST",
                url: 'load',
                data: JSON.stringify(o),
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {

                    SETUP_PAGING_NGHILC(dataget, function () {
                        loaddataProfile(row,truong,lop,keysearch);
                    });
                    var leaveDate = "";
                    $('#dataProfile').html("");
                    data = dataget.data;
                    if(data.length>0){
                        for (var i = 0; i < data.length; i++) {
                            if (data[i].profile_status == 1) {
                                leaveDate = data[i].profile_leaveschool_date;
                            }
                            else {leaveDate = "";}
                            html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            // html_show += "<td><a style='cursor:pointer' onclick='viewHistory("+data[i].profile_id+")'>"+ConvertString(data[i].profile_code)+"</a></td>";
                            html_show += "<td style='vertical-align:middle'><a style='cursor:pointer' onclick='viewHistory("+data[i].profile_id+")'>"+ConvertString(data[i].profile_name)+"</a></td>";
                            html_show += "<td class='text-center' style='vertical-align:middle'>"+formatDates(data[i].profile_birthday)+"</td>";
                            html_show += "<td style='vertical-align:middle'>"+ConvertString(data[i].nationals_name)+"</td>";
                            html_show += "<td style='vertical-align:middle'>"+ConvertString(data[i].profile_household+"-"+data[i].site_name)+"</td>";
                            html_show += "<td style='vertical-align:middle'>"+ConvertString(data[i].profile_parentname)+"</td>";
                            html_show += "<td class='text-center' style='vertical-align:middle'>"+ConvertString(data[i].class_name)+"</td>";
                            html_show += "<td class='text-center' style='vertical-align:middle'>"+ConvertString(data[i].history_year)+"</td>";
                            html_show += "<td class='text-center' style='vertical-align:middle'>"+formatMonth(data[i].profile_year)+"</td>";
                            html_show += "<td class='text-center' style='vertical-align:middle'>"+formatDates(data[i].profile_leaveschool_date)+"</td>";

                            if(check_Permission_Feature("2")){
                                html_show += "<td class='text-center' style='vertical-align:middle'><button data='"+data[i].profile_id+"' onclick='getHoSoHocSinh("+data[i].profile_id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Cập nhật </button> </td>";
                            }
                            if(check_Permission_Feature("3")){
                                html_show += "<td class='text-center' style='vertical-align:middle'><button  onclick='delHoSoHocSinh("+data[i].profile_id+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button></td>";
                            }
                            html_show += "</tr>";
                        }
                        
                    } else {
                        html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                    }
                    $('#dataProfile').html(html_show);
                }, error: function(dataget) {

                }
            });
        };

function insertKinhPhiDoiTuong(temp) {
            //console.log(temp);
            $.ajax({
                type: "POST",
                url:'insert',
                data: JSON.stringify(temp),
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {
                    if(dataget.success != null || dataget.success != undefined){
                        $("#myModal").modal("hide");
                        utility.message("Thông báo",dataget.success,null,3000)
                       resetControl(1);
                       GET_INITIAL_NGHILC();
                    loadKinhPhiDoiTuong($('select#viewTableDT').val());
                    }else if(dataget.error != null || dataget.error != undefined){
                        //$("#myModal").modal("hide");
                        utility.message("Thông báo",dataget.error,null,5000)
                        //resetControl(1);
                        //loadKinhPhiDoiTuong($('select#viewTableDT').val()); 
                    }
                   // utility.message("Thông báo","Lưu bản ghi thành công",null,5000)
                    
                           
                }, error: function(dataget) {
                }
            });
        };
function updateKinhPhiDoiTuong(temp) {
    //PostToServer('update',temp,);
            $.ajax({
                type: "POST",
                url:'update',
                data: JSON.stringify(temp),
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {
                    if(dataget.success != null || dataget.success != undefined){
                        $("#myModal").modal("hide");
                        utility.message("Thông báo",dataget.success,null,3000)
                        resetControl(1);
                        GET_INITIAL_NGHILC();
                        loadKinhPhiDoiTuong($('select#viewTableDT').val());    
                    }else if(dataget.error != null || dataget.error != undefined){
                        //$("#myModal").modal("hide");
                        utility.message("Thông báo",dataget.error,null,5000)
                        //resetControl(1);
                        //loadKinhPhiDoiTuong($('select#viewTableDT').val()); 
                    }
                    
                         
                }, error: function(dataget) {
                }
            });
        };

function insertProfile(objData){
        PostToServerFormData('/ho-so/hoc-sinh/insert',objData,function(data){
            if (data['success'] != "" && data['success'] != undefined) {

                var valueTruong = $('#sltTruong').val();
                var valueNamhoc = $('#txtYearProfile').val();
                var valueHocky = $('#sltHocky').val();
                valueNamhoc = valueNamhoc.substr(3);

                if (valueHocky === null || valueHocky === "") {
                    valueHocky = "CA";
                }
                
                valueHocky = valueHocky + '-' + valueNamhoc;

                $('#drSchoolTHCD').val(valueTruong);
                $('#sltYear').val(valueHocky);

                resetControl();
                $("#sltDoituong").val("").multiselect("clearSelection");
                loaddataProfile($('select#viewTableProfile').val(),$('select#sltTruongGrid').val(),$('select#sltLopGrid').val(), $('#txtSearchProfile').val());
                utility.message("Thông báo",data['success'],null);
            }
            if (data['error'] != "" && data['error'] != undefined) {
                utility.message("Thông báo",data['error'],null);
            }
        },null,"saveProfile","","");
    // var objJson = JSON.stringify(objData);
    // console.log(objData);
    // $.ajax({
    //     type: "POST",
    //     url:'/ho-so/hoc-sinh/insert',
    //     // data: objData,
    //     // contentType: false,
    //     // cache: false,
    //     // processData: false,
    //     data: objJson,
    //     dataType: 'json',
    //     contentType: 'application/json; charset=utf-8',
    //     headers: {
    //         'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
    //     },
    //     success: function(data) {
    //         //console.log(data + '-----------------------');
    //         if (data['success'] != "" && data['success'] != undefined) {
    //             utility.message("Thông báo",data['success'],null,3000);
    //             resetControl();
    //             loaddataProfile($('select#viewTableProfile').val(),$('select#sltTruongGrid').val(),$('select#sltLopGrid').val(), $('#txtSearchProfile').val());
    //         }
    //         if (data['error'] != "" && data['error'] != undefined) {
    //             utility.message("Thông báo",data['error'],null,3000);
    //         }
    //     }, error: function(data) {
    //     }
    // });
};
function updateProfile(objData){
    PostToServerFormData('/ho-so/hoc-sinh/update',objData,function(data){
         if (data["success"] != "" && data["success"] != undefined) {
                utility.message("Thông báo",data["success"],null,5000,0);
                $("#myModalProfile").modal("hide");
                loaddataProfile($('select#viewTableProfile').val(),$('select#sltTruongGrid').val(),$('select#sltLopGrid').val(), $('#txtSearchProfile').val());
                loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
            }
            if (data['error'] != "" && data['error'] != undefined) {
                utility.message("Thông báo", data['error'], null, 3000);
            }
        },null,"saveProfile","","");
           
}

//Export Excel--------------------------------------------------------------------------------------------------
function exportExcelProfile(){
    var schools_id = $('#sltTruongGrid').val();
    var class_id = $('#sltLopGrid').val();
    var keysearch = $('#txtSearchProfile').val();
    var objJson = JSON.stringify({ SCHOOLID: schools_id, CLASSID: class_id, KEY: keysearch });
    //alert(objJson);
    window.open('/ho-so/hoc-sinh/exportExcel/' + objJson, '_blank');
    // $.ajax({
    //     type: "get",
    //     url:'/ho-so/hoc-sinh/exportExcel/' + objJson,
    //     //data: objJson,
    //     //dataType: 'json',
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

var $dateDDMMYYYY = $('#txtBirthday, #txtDateNghi, #dateOutProfile').datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true
    });

var $dateMMYYYY = $('#txtYearProfile').datepicker({
      format: 'mm-yyyy',
      autoclose: true
    });

function resetControl(){
    profile_id = "";
     $("#txtNameProfile").focus();
   // / $('#saveProfile').html("Thêm mới");
    $('.modal-title').html('Thêm mới hồ sơ học sinh');
    $("#txtIdProfile").val('');      
    // $("#txtCodeProfile").val('');
    // $('#txtCodeProfile').removeAttr('disabled');
    $("#txtNameProfile").val('');
    $("#txtBirthday").val('');
    $("#sltDantoc").val('').select2();//.trigger('change');
    $("#sltDantoc option").removeAttr('selected');
    $("#txtParent").val('');
    $("#sltTruong").val('').select2();
    $("#sltTruong").removeAttr('disabled');;
    $("#sltTruong option").removeAttr('selected');
    $("#sltLop").html('<option value="">-- Chọn trường --</option>');
    $("#sltLop").val('').select2();//.trigger("change");//.select2('val', '');
    $("#sltLop").attr('disabled','disabled');
    $("#sltLop").html('<option value="">-- Chọn lớp --</option>');
    $("#txtYearProfile").val('');
    $("#txtYearProfile").removeAttr('disabled');
    $("#txtThonxom").val('');
    $("#sltTinh").val('').select2()//.trigger("change");//.select2('val', '');
    $("#sltTinh option").removeAttr('selected');
    $("#sltQuan").val('').select2();//.trigger("change");//.select2('val', '');
    $("#sltQuan").attr('disabled','disabled');
    $("#sltQuan").html('<option value="">-- Chọn huyện/ quận --</option>');
    $("#sltPhuong").val('').select2();//.trigger("change");//.select2('val', '');
    $("#sltPhuong").attr('disabled','disabled');
    $("#sltPhuong").html('<option value="">-- Chọn xã/ phường --</option>');
    //$("#sltDoituong").val("").multiselect("clearSelection");

    $('input#ckbNghihoc').removeAttr('checked');
    $('input#ckbNQ57').removeAttr('checked');
    $('div#divNgayNghi').attr('hidden','hidden');
    $('div#divNgayNghi').attr('disabled','disabled');
    $('#txtKhoangcach').val('');
    $('#drGiaoThong').val('')
    $('#sltHocky').val('')

    //-------------------------Clear date-----------------------------------------------------
   // $dateDDMMYYYY.datepicker('setDate', new Date());
   // $dateMMYYYY.datepicker('setDate', new Date());

    //-------------------------Quyết định-----------------------------------------------------
    $("#tbDecided tr").remove();
    counter = 0;

    //Lên lớp---------------------------------------------------------------------------------    
    $("#drSchoolUpto").val('');
    $("#drClassUpto").val('');
    $("#drClassUpto").attr('disabled','disabled');
    $("#drYearUpto").val('');
    $("#drYearUpto").attr('disabled','disabled');
    $('#drClassNext').val('');
    $('#drClassNext').attr('disabled','disabled');
    $('#StlClassNext').html('');
    $('#StlClassNext').addClass('hidden');
    $('#labelClassNext').hide();

    $('#btnUpto').attr('disabled', 'disabled');
    $('#btnRevert').attr('disabled', 'disabled');
    $('#contentPopupUpto').html('');
    $('#dateOutProfile').hide();
    $('#dateOutProfile').html('');
    $('#labelOutProfile').hide();
    $('#dateOutProfile').addClass('disabled','disabled');
    $('#drClassBack').hide();
    $('#labelClassBack').hide();
    $('#drClassBack').addClass('disabled','disabled');
    $('#drClassBack').html('<option value="">--Chọn lớp--</option>');

    //Form mới------------------------------------------------------------------------------------
    $('#tbMoney').attr('hidden', 'hidden');
};

var messageValidate = "";

function validateInput(){
    // var v_profileCode = $("#txtCodeProfile").val();
    var v_profileName = $("#txtNameProfile").val();
    var v_profileBirthday = $("#txtBirthday").val();
    var v_profileNation = $("#sltDantoc").val();
    var v_profileParent = $("#txtParent").val();
    var v_profileSchool = $("#sltTruong").val();
    var v_profileClass = $("#sltLop").val();
    var v_profileYear = $("#txtYearProfile").val();
    var v_profileSite1 = $("#sltTinh").val();
    var v_profileSite2 = $("#sltQuan").val();

    var v_status = $("#ckbNghihoc").is(":checked");
    var v_leaveDate = $("#txtDateNghi").val();
            
    v_profileName = v_profileName.replace(/[\n\t\r]/g,"");
    v_profileParent = v_profileParent.replace(/[\n\t\r]/g,"");

    //console.log(v_profileCode + "-----------------------------------------------------------------");

    // if (v_profileCode == "") {   
    //     // messageValidate = "Vui lòng nhập mã học sinh!";
    //     // $('#txtCodeProfile').focus();
    //     // return messageValidate;
    // }else if (v_profileCode !== "" && v_profileCode.length > 200) {
    //     v_profileCode = v_profileCode.replace(/[\n\t\r]/g,""); 
    //     messageValidate = "Mã học sinh không được vượt quá 200 ký tự!";
    //     $('#txtCodeProfile').focus();
    //     $('#txtCodeProfile').val("");
    //     return messageValidate;
    // }
    // else{
    //     var specialChars = "!@#$%^&*()+=[]\\\';,./{}|\":<>?";
    //     var unicodeChars = "àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđÁÀẠẢÃÂẤẦẬẨẪĂẮẰẶẲẴÉÈẸẺẼÊẾỀỆỂỄÍÌỊỈĨÓÒỌỎÕÔỐỒỘỔỖƠỚỜỢỞỠÚÙỤỦŨƯỨỪỰỬỮÝỲỴỶỸĐ";

    //     for (var i = 0; i < v_profileCode.length; i++) {
    //         if (specialChars.indexOf(v_profileCode.charAt(i)) != -1) {
    //             messageValidate = "Mã nhập không được chứa ký tự đặc biệt!";
    //             $('#txtCodeProfile').focus();
    //             $('#txtCodeProfile').val("");
    //             return messageValidate;
    //         }

    //         if (unicodeChars.indexOf(v_profileCode.charAt(i)) != -1) {
    //             messageValidate = "Mã nhập không được chứa ký tự có dấu!";
    //             $('#txtCodeProfile').focus();
    //             $('#txtCodeProfile').val("");
    //             return messageValidate;
    //         }
    //     }
    //     $('#txtCodeProfile').focusout();
    // }

    //Validate Name----------------------------------------------------------------------------------------
    if (v_profileName.trim() == "") {
        messageValidate = "Vui lòng nhập tên học sinh!";
        $('#txtNameProfile').focus();
        return messageValidate;
    }else if (v_profileName.length > 200) {
        messageValidate = "Tên học sinh không được vượt quá 200 ký tự!";
        $('#txtNameProfile').focus();
        $('#txtNameProfile').val("");
        return messageValidate;
    }
    else{
        var specialChars = "#/|\\";

        for (var i = 0; i < v_profileName.length; i++) {
            if (specialChars.indexOf(v_profileName.charAt(i)) != -1) {
                messageValidate = "Tên học sinh không được chứa ký tự #, /, |, \\";
                $('#txtNameProfile').focus();
                return messageValidate;
            }
        }

        $('#txtNameProfile').focusout();
    }

    //Validate Birthday----------------------------------------------------------------------------------------
    if (v_profileBirthday == "") {
        messageValidate = "Vui lòng nhập ngày sinh!";
        $('#txtBirthday').focus(); 
        return messageValidate;
    }
    else{
        $('#txtBirthday').focusout();
    }

    //Validate National----------------------------------------------------------------------------------------
    if (v_profileNation == "") {
        messageValidate = "Vui lòng chọn dân tộc!";
        return messageValidate;
    }

    //Validate ParentName----------------------------------------------------------------------------------------
    if (v_profileParent.trim() == "") {
        messageValidate = "Vui lòng nhập họ tên cha/ mẹ hoặc người giám hộ!";
        $('#txtParent').focus(); 
        return messageValidate;
    }else if (v_profileParent.length > 200) {
        messageValidate = "Họ tên cha/ mẹ không được vượt quá 200 ký tự!";
        $('#txtParent').focus();
        $('#txtParent').val("");
        return messageValidate;
    }
    else{
        $('#txtParent').focusout();
        var specialChars = "#/|\\";

        for (var i = 0; i < v_profileParent.length; i++) {
            if (specialChars.indexOf(v_profileParent.charAt(i)) != -1) {
                messageValidate = "Họ tên cha/ mẹ không được chứa ký tự #, /, |, \\!";
                $('#txtParent').focus();
                $('#txtParent').val("");
                return messageValidate;
            }
        }
    }

    //Validate School----------------------------------------------------------------------------------------
    if (v_profileSchool == "") {
        messageValidate = "Vui lòng chọn trường học!";
        return messageValidate;
    }

    //Validate Class----------------------------------------------------------------------------------------
    if (v_profileClass == "") {
        messageValidate = "Vui lòng chọn lớp học!";
        return messageValidate;
    }

    //Validate Year----------------------------------------------------------------------------------------
    if (v_profileYear == "") {
        messageValidate = "Vui lòng nhập năm học!";
        $('#txtYearProfile').focus(); 
        return messageValidate;
    }

    //Validate Tỉnh-Thành phố----------------------------------------------------------------------------------------
    if (v_profileSite1 == "") {
        messageValidate = "Vui lòng chọn Tỉnh/ Thành phố!";
        return messageValidate;
    }
    else{
        //Validate Quận-Huyện----------------------------------------------------------------------------------------
        if (v_profileSite2 == "") {
            messageValidate = "Vui lòng chọn Huyện/ Quận!";
            return messageValidate;
        }
    }

    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();

    if(dd < 10) {
        dd = '0' + dd;
    } 

    if(mm < 10) {
        mm = '0' + mm;
    } 

    today = dd + '-' + mm + '-' + yyyy;

    // var v_newBirthday = v_profileBirthday.substring(3, v_profileBirthday.length);
    // var v_newYear = '28' + '-' + v_profileYear;

    //console.log(v_newBirthday);console.log(v_profileYear);

    var v_birthdayDate = v_profileBirthday.substring(0, 2);
    var v_birthdayMonth = v_profileBirthday.substring(3, 5);
    var v_birthdayYear = v_profileBirthday.substring(6, v_profileBirthday.length);
    //console.log(v_birthdayDate);console.log(v_birthdayMonth);console.log(v_birthdayYear);

    var v_yearMonth = v_profileYear.substring(0, 2);
    var v_yearYear = v_profileYear.substring(3, v_profileYear.length);
    //console.log(v_yearMonth);console.log(v_yearYear);

    // if (v_birthdayYear > yyyy || (v_birthdayYear == yyyy && v_birthdayMonth > mm) || (v_birthdayYear == yyyy && v_birthdayMonth == mm && v_birthdayDate > dd)) {
    //     messageValidate = "Ngày sinh không được lớn hơn ngày hiện tại!";
    //     $('#txtBirthday').focus();
    //     return messageValidate;
    // }

    // if (v_birthdayYear > v_yearYear || (v_birthdayYear == v_yearYear && v_birthdayMonth > v_yearMonth)) {
    //     messageValidate = "Ngày nhập học không được nhỏ hơn ngày sinh!";
    //     $('#txtYearProfile').focus();
    //     return messageValidate;
    // }

    // if (v_status == true) {
    //     var v_newLeaveDate = v_leaveDate.substring(0, 2);
    //     var v_newLeaveMonth = v_leaveDate.substring(3, 5);
    //     var v_newLeaveYear = v_leaveDate.substring(6, v_leaveDate.length);
    //     //console.log(v_newLeaveDate);console.log(v_newLeaveMonth);console.log(v_newLeaveYear);
    //     if (v_yearYear > v_newLeaveYear || (v_yearYear == v_newLeaveYear && v_yearMonth > v_newLeaveMonth)) {
    //         messageValidate = "Ngày nghỉ không được nhỏ hơn ngày vào học!";
    //         $('#txtDateNghi').focus(); 
    //         return messageValidate;
    //     }
    // }
};

//Search-------------------------------------------------------------------------------
    function autocompleteSearch(idControl, number = null) {
        var keySearch = "";
        $('#' + idControl).autocomplete({
            source: function (request, response) {
                keySearch = $.ui.autocomplete.escapeRegex(request.term).replace(/[%\\\-]/g, '');
                //console.log(keySearch.length);
                if (keySearch.length >= 2) {
                    GET_INITIAL_NGHILC();
                    if (number == null || number == "") {
                        loaddataProfile($('select#viewTableProfile').val(),$('select#sltTruongGrid').val(),$('select#sltLopGrid').val(), keySearch);
                    }
                    
                    if (number == 1) {
                        loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val(), keySearch, $('#sltTrangthai').val());
                    }
                    if (number == 2) {
                        loadlistApproved($('#drPagingDanhsachtonghop').val(), keySearch, $('#sltTrangthai').val());
                    }
                    if (number == 3) {
                        loadlistApprovedPheDuyet($('#drPagingDanhsachtonghop').val(), keySearch, $('#sltTrangthai').val());
                    }
                    if (number == 4) {
                        loadlistUnApproved($('#drPagingDanhsachtonghop').val(), keySearch);
                    }
                    if (number == 5) {
                        loadlistUnApprovedThamdinh($('#drPagingDanhsachtonghop').val(), keySearch);
                    }
                        
                }else if(keySearch.length < 2){
                    GET_INITIAL_NGHILC();
                    if (number == null || number == "") {
                        loaddataProfile($('select#viewTableProfile').val(),$('select#sltTruongGrid').val(),$('select#sltLopGrid').val(), "");
                    }
                    
                    if (number == 1) {
                        loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val(), "", $('#sltTrangthai').val());
                    }
                    if (number == 2) {
                        loadlistApproved($('#drPagingDanhsachtonghop').val(), "", $('#sltTrangthai').val());
                    }
                    if (number == 3) {
                        loadlistApprovedPheDuyet($('#drPagingDanhsachtonghop').val(), "", $('#sltTrangthai').val());
                    }
                    if (number == 4) {
                        loadlistUnApproved($('#drPagingDanhsachtonghop').val(), "");
                    }
                    if (number == 5) {
                        loadlistUnApprovedThamdinh($('#drPagingDanhsachtonghop').val(), "");
                    }
                }
            },
            minLength: 0,
            delay: 222,
            autofocus: true
        });
    };

function loadComboDecidedType(){
    var html_show = "";
    html_show += "<option value='MGHP'>Miễn giảm học phí</option>";
    html_show += "<option value='CPHT'>Chi phí học tập</option>";
    html_show += "<option value='HTAT'>Hỗ trợ ăn trưa</option>";
    html_show += "<option value='HTBT'>Hỗ trợ bán trú</option>";
    html_show += "<option value='NGNA'>Hỗ trợ người nấu ăn</option>";
    html_show += "<option value='HSKT'>Hỗ trợ học sinh khuyết tật</option>";
    html_show += "<option value='HSDTTS'>Hỗ trợ học sinh dân tộc thiểu số tại huyện Mù Cang Chải và Trạm tấu</option>";
    html_show += "<option value='TONGHOP'>Chế độ chính sách ưu đãi</option>";
    
    $('#drDecidedType').html(html_show);
}
function change_alias(alias)
{
    var str = alias;
    str= str.toLowerCase(); 
    str= str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ  |ặ|ẳ|ẵ/g,"a"); 
    str= str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e"); 
    str= str.replace(/ì|í|ị|ỉ|ĩ/g,"i"); 
    str= str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ  |ợ|ở|ỡ/g,"o"); 
    str= str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u"); 
    str= str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y"); 
    str= str.replace(/đ/g,"d"); 
    str= str.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_/g,"-");
    /* tìm và thay thế các kí tự đặc biệt trong chuỗi sang kí tự - */
    str= str.replace(/-+-/g,"-"); //thay thế 2- thành 1-
    str= str.replace(/^\-+|\-+$/g,""); 
    //cắt bỏ ký tự - ở đầu và cuối chuỗi 
    return str;
}

function controlClass(valback,valnext,dateout,arrProfileID,classID,year,classID_next){
    var v_jsonClass = {
        CLASSBACK:valback, 
        CLASSNEXT:valnext, 
        DATEOUTPROFIEL:dateout, 
        ARRPROFILEID: arrProfileID, 
        CLASSID: classID, 
        YEAR: year, 
        CLASSIDNEXT: classID_next 
    };
    PostToServer('/ho-so/hoc-sinh/uptoprofile',v_jsonClass,function(data){
        if (data['success'] != "" && data['success'] != undefined) {
            utility.message("Thông báo",data['success'],null,3000);
            $('#uptoClass').DataTable().clear().draw().destroy();
            resetControl();
            GET_INITIAL_NGHILC();
            $('#upClass-select-all').prop('checked', false);
            loaddataProfile($('select#viewTableProfile').val(),$('select#sltTruongGrid').val(),$('select#sltLopGrid').val(), $('#txtSearchProfile').val());
        }
        if (data['error'] != "" && data['error'] != undefined) {
            utility.message("Thông báo",data['error'],null,3000,1);
        }
    },null,"btnUpto","","");
                            // $.ajax({
                            //         type: "POST",
                            //         url:'/ho-so/hoc-sinh/uptoprofile',
                            //         data: v_jsonClass,
                            //         dataType: 'json',
                            //         contentType: 'application/json; charset=utf-8',
                            //         headers: {
                            //             'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                            //         },
                            //         success: function(data) {
                            //             //console.log(data);
                                       
                            //         }, error: function(data) {
                            //         }
                            //     });
}
// autocompleteSearch = function (idSearch) {
//         var lstCustomerForCombobox;
//         $('#' + idSearch).autocomplete({
//             source: function (request, response) {
//                 var cusNameSearch = $.ui.autocomplete.escapeRegex(request.term).replace(/[%\\\-]/g, '');
//                 if (cusNameSearch.length >= 4) {
//                     $.get("/kinh-phi/muc-ho-tro-doi-tuong/search/" + cusNameSearch, function (data) {
//                         lstCustomerForCombobox = [];
//                         var item;
//                         if (data.length > 0) {
//                             for (var i = 0; i < data.length; i++) {
//                                 var dl = data[i];
//                                 if (dl.Name != null)
//                                     item = dl.CustomerId + '-' + dl.Name;
//                                 else
//                                     item = dl.CustomerId;
//                                 lstCustomerForCombobox.push(item);
//                             }
//                         } else {
//                            // $('#' + idCusName).val('');
//                             //$('#' + idCusId).val('');
//                         }
//                         var matcher = new RegExp(cusNameSearch, "i");
//                         response($.grep(lstCustomerForCombobox, function (item) {
//                             return matcher.test(item);
//                         }));
//                     });
//                 }
//             },
//             minLength: 1,
//             delay: 222,
//             autofocus: true,
//             select: function (event, ui) {
//                 var value = ui.item.value;
//                 var customerCode = value.split('-')[0];
//                 var customerName = value.split('-')[1];
//                 //$('#' + idCusCode).val(customerCode);
//                 //$('#' + idCusName).val(customerName);
//                // $('#' + idCusId).val('');
//                 return false;
//             }
//         });
//     };


// function SETUP_PAGING(data, callback) {
//     //    var startRecord = startRecord;
//     //    var totalRows = totalRows;
//     //    var numRows = numRows;

//     var StartRecord = (data.startRecord);
//     var TotalRows = data.totalRows;
//     var numRows = data.numRows;
//     alert(TotalRows);
//     if (StartRecord !== 0 || isRun === null) {
//         $("select.g_selectPagingUpto").val(StartRecord);
//         return;
//     }
//     isRun = null;
//     paging = 0;
//     // startRecord = StartRecord;
//     // totalRows = TotalRows;
//     // numShowRows = numRows;

//     //PHAN TRANG
//     var page_selector = "";

//     var n = ~~(TotalRows / numRows);
//     var du = TotalRows % numRows;
//     if (du > 0)
//         n = n + 1;
//     paging = n;

//     for (var i = 0; i < n; i++) {
//         page_selector += "<option value=" + (i) + ">" + (i + 1) + "/" + n + "</option>";
//     }

//     $("#g_countRowsPagingUpto").html(TotalRows);
//     $("select.g_selectPagingUpto").html(page_selector);
//     $("select.g_selectPagingUpto").val(StartRecord);
//     //PHAN TRANG

//     //HUNG
//     page_selector = "";
//     page_selector += "<li id='paging_left' style='cursor:pointer'><a>&laquo;</a></li>";

//     var maxShowLi = n;
//     if (maxShowLi > 5) {
//         maxShowLi = 5;
//     }
//     for (var i = 0; i < maxShowLi; i++) {
//         var x = '';
//         if (i === 0)
//             x = 'paging_item_first';
//         if (i === maxShowLi - 1)
//             x = 'paging_item_end';
//         page_selector += "<li class='paging_item " + x + "' style='cursor:pointer' value='" + i + "'><a>" + (parseInt(i) + 1) + "</a></li>";
//     }
//     page_selector += "<li id='paging_right' style='cursor:pointer'><a>&raquo;</a></li>";

//     $("ul.g_clickedPagingUpto").html(page_selector);
//     $("li.paging_item").removeClass('active');
//     $("li.paging_item_first").addClass('active');

//     //HUNG ---------------------------------------------------------------------   
//     $("select.g_selectPagingUpto").unbind('change');
//     $("select.g_selectPagingUpto").change(function () {
//         startRecord = $(this).val();
//         setLeftPaging_Hunglm(parseInt($(this).val()));
//         callback();
//     });
    
//     $("ul.g_clickedPagingUpto").unbind('click');
//     $("ul.g_clickedPagingUpto").on('click', 'li.paging_item', (function () {

//         var rowCurrent = $(this).attr('value');
//         startRecord = rowCurrent;
//         $("select.g_selectPagingUpto").val(rowCurrent);

//         $("li.paging_item").removeClass('active');
//         $(this).addClass('active');
//         callback();
//     }));

//     setLeftRightPaging_();
// };

// function setLeftPaging_Hunglm(startIndx) {

//     var page_selector = "";
//     page_selector += "<li id='paging_left' style='cursor:pointer'><a>&laquo;</a></li>";
//     var first = $("li.paging_item_first").val();
//     var end = $("li.paging_item_end").val();
//     if (first === undefined || end === undefined || (parseInt(first) === 0 && startIndx === undefined))
//         return;

//     var startShowLi = parseInt(first) - 1;
//     if (startIndx !== undefined) {
//         startShowLi = startIndx - 4;
//         if (startShowLi < 0)
//             startShowLi = 0;
//     }
//     var n = parseInt(paging);
//     var maxShowLi = startShowLi + 4;
//     if (maxShowLi >= n - 1)
//         maxShowLi = n - 1;
//     for (var i = startShowLi; i <= maxShowLi; i++) {
//         var x = '';
//         if (i === startShowLi)
//             x = 'paging_item_first';
//         if (i === maxShowLi - 1)
//             x = 'paging_item_end';
//         page_selector += "<li class='paging_item " + x + "' style='cursor:pointer' value='" + i + "'><a>" + (parseInt(i) + 1) + "</a></li>";
//     }
//     page_selector += "<li id='paging_right' style='cursor:pointer'><a>&raquo;</a></li>";
//     $("ul.g_clickedPagingUpto").html(page_selector);
//     $("li.paging_item").each(function () {
//         if (parseInt($(this).attr('value')) === parseInt(startRecord)) {
//             $(this).addClass('active');
//         }
//     });

//     setLeftRightPaging_();
// };

// function setLeftRightPaging_() {
//     $("li#paging_left").unbind('click');
//     $('li#paging_left').click(function () {
//         setLeftPaging_Hunglm();
//     });
//     $("li#paging_right").unbind('click');
//     $('li#paging_right').click(function () {
//         setRightPaging_Hunglm();
//     });
// };

// function setRightPaging_Hunglm() {
//     var n = parseInt(paging);
//     var page_selector = "";
//     page_selector += "<li id='paging_left' style='cursor:pointer'><a>&laquo;</a></li>";
//     var first = $("li.paging_item_first").val();
//     var end = $("li.paging_item_end").val();
//     if (first === undefined || end === undefined || (parseInt(first) + 4) >= n - 1)
//         return;
//     var startShowLi = parseInt(first) + 1;
//     var maxShowLi = startShowLi + 4;
//     if (maxShowLi > n - 1)
//         maxShowLi = n - 1;

//     for (var i = startShowLi; i <= maxShowLi; i++) {
//         var x = '';
//         if (i === startShowLi)
//             x = 'paging_item_first';
//         if (i === maxShowLi - 1)
//             x = 'paging_item_end';
//         page_selector += "<li class='paging_item " + x + "' style='cursor:pointer' value='" + i + "'><a>" + (parseInt(i) + 1) + "</a></li>";
//     }
//     page_selector += "<li id='paging_right' style='cursor:pointer'><a>&raquo;</a></li>";
//     $("ul.g_clickedPagingUpto").html(page_selector);
//     $("li.paging_item").each(function () {

//         if (parseInt($(this).attr('value')) === parseInt(startRecord)) {
//             $(this).addClass('active');
//         }
//     });

//     setLeftRightPaging_();
// };



//----------------------------------------------------------Danh sách hỗ trợ tổng hợp-----------------------------------------------------------
    var _year = '';
    function loaddataDanhSachTongHop(row, keySearch = "", status = "") {

        // var msg_warning = "";

        // msg_warning = validateTHCD();

        // // alert(msg_warning);

        // if (msg_warning !== null && msg_warning !== "") {
        //     utility.messagehide("messageValidate", msg_warning, 1, 5000);
        //     return;
        // }
        // GET_INITIAL_NGHILC();
        var schools_id = $('#drSchoolTHCD').val();
        var year = $('#sltYear').val();

        _year = year;
        var number = 0;
        var nam = 0;

        if (year !== null && year !== "" && year !== undefined) {
            var ky = year.split("-");
            nam = ky[1];
            if (ky[0] == 'HK1') {
                number = 1;
            }
            else if (ky[0] == 'HK2') {
                number = 2;
            }
            else if (ky[0] == 'CA') {
                number = 3;
            }
        }
        
        
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit: row,
            SCHOOLID: schools_id,
            YEAR: year,
            KEY: keySearch,
            STATUS: status
        };
        PostToServer('/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/load',o,function(datas){
            SETUP_PAGING_NGHILC(datas, function () {
                    loaddataDanhSachTongHop(row, keySearch, status);
                });
                
                $('#dataDanhsachTonghop').html("");
                var dataget = datas.data;
                // console.log(dataget);
                
                if(dataget.length > 0){
                    for (var i = 0; i < dataget.length; i++) {
                                    
                        html_show += "<tr><td class='text-center' style='vertical-align:middle'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                        html_show += "<td><a href='javascript:;' onclick='getProfileSubById("+parseInt(dataget[i].profile_id)+", "+number+", "+nam+");'>"+dataget[i].profile_name+"</a></td>";
                        html_show += "<td class='text-center' style='vertical-align:middle'>"+formatDates(dataget[i].profile_birthday)+"</td>";
                        html_show += "<td>"+dataget[i].schools_name+"</td>";
                        html_show += "<td class='text-center' style='vertical-align:middle'>"+dataget[i].class_name+"</td>";
                        html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].MGHP)+"</td>";
                        html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].CPHT)+"</td>";
                        html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].HTAT)+"</td>";
                        html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].HTBT_TA)+"</td>";
                        html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].HTBT_TO)+"</td>";
                        html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].HTBT_VHTT)+"</td>";
                        html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].HTATHS)+"</td>";
                        html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].HSKT_HB)+"</td>";
                        html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].HSKT_DDHT)+"</td>";
                        html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].HBHSDTNT)+"</td>";
                        html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].HSDTTS)+"</td>";
                        html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].TONGTIEN)+"</td>";
                        html_show += "<td  style='vertical-align:middle'>"+ConvertString(dataget[i].GHICHU)+"</td>";
                        // html_show += "<td>"+ConvertString(dataget[i].GHICHU)+"</td>";
                        // if (parseInt(dataget[i].TRANGTHAIPHEDUYET) == 0) {
                            if(parseInt(dataget[i].TRANGTHAI) == 0){
                                html_show += "<td class='text-center' style='vertical-align:middle'><button class='btn btn-primary btn-xs' onclick='openPopupDuyetChedo("+dataget[i].profile_id+", "+dataget[i].qlhs_thcd_id+", "+number+", "+nam+")'> Chưa chọn </button></td>";
                            }else if(parseInt(dataget[i].TRANGTHAI) == 1){
                                html_show += "<td class='text-center' style='vertical-align:middle'><button class='btn btn-success btn-xs' onclick='openPopupDuyetChedo("+dataget[i].profile_id+", "+dataget[i].qlhs_thcd_id+", "+number+", "+nam+")'> Đã chọn </button></td>";
                            }
                        // }
                        // else {
                        //     html_show += "<td class='text-center' style='vertical-align:middle'><button class='btn btn-primary btn-xs'> Đã phê duyệt</button></td>";
                        // }
                        
                        if(check_Permission_Feature("2")){
                            html_show += "<td class='text-center' style='vertical-align:middle'><button data='"+dataget[i].profile_id+"' onclick='getHoSoHocSinh("+dataget[i].profile_id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Sửa </button></td>";
                        }
                        if(check_Permission_Feature("3")){
                            html_show += "<td class='text-center' style='vertical-align:middle'><button  onclick='delHoSoHocSinh("+dataget[i].profile_id+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button></td>";
                        }
                        
                        html_show += "</tr>";
                    }                            
                }
                else {
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }
                $('#dataDanhsachTonghop').html(html_show);
            },function(result){
                console.log(result);
            },"btnLoadDataTruong","","");

    };

    function loadComboboxHocky(level, callback) {

        $.ajax({
            type: "get",
            url: '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/loadhocky',
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(dataget) {
                // console.log(dataget['HOCKY']);

                // $('#sltYear').html("");
                //     var html_show = "";
                //     if(dataget.length >0){
                //         html_show += "<option selected='selected' value=''>-- Chọn năm học --</option>";
                //         for (var i = dataget.length - 1; i >= 0; i--) {
                //             html_show += "<option value='"+dataget[i].qlhs_hocky_value+"'>"+dataget[i].qlhs_hocky_name+"</option>";
                //         }
                //         $('#sltYear').html(html_show);
                //     }else{
                //         $('#sltYear').html("<option value=''>-- Chưa có năm học --</option>");
                //     }
                var hocky = dataget['HOCKY'];
                var namhoc = dataget['NAMHOC'];
                // <optgroup label="Cats">
                $('#sltYear').html("");
                //$('#sltTruongGrid').html("");
                var html_show = "";
                if(namhoc.length > 0){
                    html_show += "<option value='0'>-- Chọn học kỳ --</option>";
                    for (var j = 0; j < namhoc.length; j++) {
                    html_show +="<optgroup label='Năm học "+namhoc[j].name+"'>";
                        if(hocky.length > 0){
                            for (var i = 0; i < hocky.length; i++) {
                                if(namhoc[j].code === hocky[i].qlhs_hocky_code){
                                    html_show += "<option value='"+hocky[i].qlhs_hocky_value+"'>"+hocky[i].qlhs_hocky_name+"</option>";
                                }
                            }
                        }    
                    html_show +="</optgroup>"
                    }
                    //$('#sltTruong').html(html_show);
                    $('#sltYear').html(html_show);
                    $('#sltYear').val('HK1-2017');
                    //Danh sách Trường
                    if (level == 1) {
                        GET_INITIAL_NGHILC();
                        loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    }
                    //Danh sách Phòng
                    if (level == 2) {
                        GET_INITIAL_NGHILC();
                        // loadlistApproved($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    }
                    //Danh sách Sở
                    if (level == 3) {
                        GET_INITIAL_NGHILC();
                        // loadlistApprovedPheDuyet($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    }
                    //Danh sách Phòng trả lại
                    if (level == 4) {
                        GET_INITIAL_NGHILC();
                        // loadlistUnApproved($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val());
                    }
                    //Danh sách Sở trả lại
                    if (level == 5) {
                        GET_INITIAL_NGHILC();
                        // loadlistUnApprovedThamdinh($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val());
                    }
                }else{
                    //$('#sltTruongGrid').html("<option value=''>-- Chưa có trường --</option>");
                    $('#sltYear').html("<option value=''>-- Chưa có học kỳ --</option>");
                }

                if (callback != null) { callback(dataget); }
            }, error: function(dataget) {
                console.log("loadComboboxHocky styleProfile: "+dataget);
            }
        });
    };

    var _id = 0;
    var _idProfile = 0;

    function openPopupDuyetChedo(id, idTHCD, number, nam){
        _id = idTHCD;
        _idProfile = id;
        id = id + '-' + number + '-' + _year;

        $('#txtGhiChuTHCD').val('');
        $('#checkedAllChedo').prop('checked', false);
        // console.log(id);
        $.ajax({
            type: "get",
            url:'/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/getProfileSubById/' + id,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                // console.log(data);
                
                // console.log(data['GROUP']);
                // console.log(data['SUBJECT']);
                // console.log(data['CHEDO']);

                var html_show = "";

                var groupId = 0;

                if (data !== null && data !== "") {

                    // for (var i = 0; i < data['GROUP'].length; i++) {
                        for (var j = 0; j < data['SUBJECT'].length; j++) {

                            // if (data['GROUP'][i].group_id == data['SUBJECT'][j].subject_history_group_id) {
                                html_show += "<tr>";
                                html_show += "<td class='text-center'>"+(j + 1 + (GET_START_RECORD_NGHILC() * 10))+"</td>";
                                html_show += "<td class='text-center'>";

                                // console.log(data['CHEDO'][0]['TRANGTHAIHK1']);
                                // console.log(data['CHEDO'][0]['TRANGTHAIHK2']);

                                if (parseInt(data['CHEDO'][0]['TRANGTHAI']) == 1) {

                                    if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 89 
                                            || parseInt(data['SUBJECT'][j].subject_history_group_id) === 90
                                            || parseInt(data['SUBJECT'][j].subject_history_group_id) === 91) && parseInt(data['CHEDO'][0]['MGHP']) === 1) {
                                        html_show += "<input type='checkbox' id='chilCheck' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 92) && parseInt(data['CHEDO'][0]['CPHT']) === 1) {
                                        html_show += "<input type='checkbox' id='chilCheck' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 93) && parseInt(data['CHEDO'][0]['HTAT']) === 1) {
                                        html_show += "<input type='checkbox' id='chilCheck' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 94) && parseInt(data['CHEDO'][0]['HTBT_TA']) === 1) {
                                        html_show += "<input type='checkbox' id='chilCheck' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 98) && parseInt(data['CHEDO'][0]['HTBT_TO']) === 1) {
                                        html_show += "<input type='checkbox' id='chilCheck' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 115) && parseInt(data['CHEDO'][0]['HTBT_VHTT']) === 1) {
                                        html_show += "<input type='checkbox' id='chilCheck' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 95) && parseInt(data['CHEDO'][0]['HSKT_HB']) === 1) {
                                        html_show += "<input type='checkbox' id='chilCheck' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 100) && parseInt(data['CHEDO'][0]['HSKT_DDHT']) === 1) {
                                        html_show += "<input type='checkbox' id='chilCheck' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 99) && parseInt(data['CHEDO'][0]['HSDTTS']) === 1) {
                                        html_show += "<input type='checkbox' id='chilCheck' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 118) && parseInt(data['CHEDO'][0]['HTATHS']) === 1) {
                                        html_show += "<input type='checkbox' id='chilCheck' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 119) && parseInt(data['CHEDO'][0]['HBHSDTNT']) === 1) {
                                        html_show += "<input type='checkbox' id='chilCheck' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else{
                                        html_show += "<input type='checkbox' id='chilCheck' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"'>";
                                    }
                                }
                                else{
                                    html_show += "<input type='checkbox' id='chilCheck' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"'>";
                                }
                                
                                html_show += "</td>";
                                
                                html_show += "<td>"+data['SUBJECT'][j].group_name+"</td>";
                                html_show += "<td>"+data['SUBJECT'][j].subject_name+"</td>";
                                html_show += "</tr>";
                            // }

                            // if (i === data['SUBJECT'].length) { break; }
                        }
                    // }
                }

                $('#txtGhiChuTHCD').val(data['CHEDO'][0]['GHICHU']);
                $('#dataDanhsachCheDo').html(html_show);
                $("#myModalApproved").modal("show");
            }, error: function(data) {
                console.log(data);
                closeLoading();
            }
        });
    }

    function approvedChedo(objData, note){
        var strData = 'ID' + _id + '-' + _year + '-' + 'IDPROFILE' + _idProfile + '-' + note + '-' + objData;
        console.log(strData);
        // utility.confirm("Duyệt cấp kinh phí?", "Bạn có chắc chắn muốn Duyệt?", function () {
            $.ajax({
                type: "get",
                url:'/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/approved/' + strData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(data) {
                    console.log(data);
                    if (data['success'] != "" && data['success'] != undefined) {
                        utility.message("Thông báo",data['success'],null,3000);
                        // resetFormTHCD();
                        GET_INITIAL_NGHILC();
                        loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                        $("#myModalApproved").modal("hide");
                        closeLoading();
                    }
                    if (data['error'] != "" && data['error'] != undefined) {
                        // resetFormTHCD();
                        GET_INITIAL_NGHILC();
                        loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                        utility.message("Thông báo",data['error'],null,3000,1);
                        closeLoading();
                    }
                }, error: function(data) {
                    closeLoading();
                }
            });
        // });
    }

    function revertapprovedChedo(id){
        var strData = id + '-' + _year;
        // console.log(id);
        utility.confirm("Hủy duyệt cấp kinh phí?", "Bạn có chắc chắn muốn hủy duyệt?", function () {
            $.ajax({
                type: "get",
                url:'/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/revertApproved/' + strData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(data) {
                    // console.log(data);
                    if (data['success'] != "" && data['success'] != undefined) {
                        utility.message("Thông báo",data['success'],null,3000);
                        // resetFormTHCD();
                        GET_INITIAL_NGHILC();
                        loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                        $("#myModalLapDanhSachTHCD").modal("hide");
                        closeLoading();
                    }
                    if (data['error'] != "" && data['error'] != undefined) {
                        // resetFormTHCD();
                        GET_INITIAL_NGHILC();
                        loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                        utility.message("Thông báo",data['error'],null,3000,1);
                        closeLoading();
                    }
                }, error: function(data) {
                    closeLoading();
                }
            });
        });
    }

    function getProfileSubById(id, number, nam = 0){
        
        id = id + '-' + number + '-' + _year;
        console.log(id);
        $.ajax({
            type: "get",
            url:'/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/getProfileSubById/' + id,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                // console.log(data['GROUP']);
                // console.log(data['SUBJECT']);

                var html_show = "";

                var groupId = 0;

                if (data['SUBJECT'] !== null && data['SUBJECT'] !== "") {
                    for (var i = 0; i < data['SUBJECT'].length; i++) {

                        html_show += '<div class="form-group" style="margin: 0;">';
                        html_show += '<div class="col-sm-12" style="padding-left: 0">';
                        html_show += '<label style="padding-top: 0px;" class="col-sm-4 control-label">'+data['SUBJECT'][i]['group_name']+'  :</label>';

                        html_show += '<div class="col-sm-8">';
                        html_show += '<p>'+data['SUBJECT'][i]['subject_name']+'</p>';
                        html_show += '</div>';

                        // for (var j = 0; j < data['SUBJECT'].length; j++) {
                        //     if (data['GROUP'][i]['group_id'] == data['SUBJECT'][j]['subject_history_group_id']) {
                        //         // if (data['GROUP'][i]['group_id'] == groupId) {
                        //         //     html_show += '<div class="form-group" style="margin: 0;">';
                        //         //     html_show += '<div class="col-sm-12" style="padding-left: 0">';
                        //         //     html_show += '<label style="padding-top: 0px;" class="col-sm-4 control-label">---</label>';
                        //         //     html_show += '<div class="col-sm-8">';
                        //         //     html_show += '<p>'+data['SUBJECT'][j]['subject_name']+'</p>';
                        //         //     html_show += '</div>';
                        //         // }
                        //         // else {
                        //         //     html_show += '<div class="form-group" style="margin: 0;">';
                        //         //     html_show += '<div class="col-sm-12" style="padding-left: 0">';
                        //         //     html_show += '<label style="padding-top: 0px;" class="col-sm-4 control-label">'+data['GROUP'][i]['group_name']+'  :</label>';
                                    
                        //         // }

                        //         html_show += '<div class="col-sm-8">';
                        //         html_show += '<p>'+data['SUBJECT'][j]['subject_name']+'</p>';
                        //         html_show += '</div>';
                        //     }
                        // }
                        html_show += '</div></div>';

                        // groupId = data['GROUP'][i]['group_id'];
                    }
                }

                $('#contentBox').html(html_show);
                $("#myModalTHCD").modal("show");
            }, error: function(data) {
                closeLoading();
            }
        });
    }

    function openPopupLapTHCD(){
        var msg_warning = "";

        msg_warning = validateTHCDTruong();

        // alert(msg_warning);

        if (msg_warning !== null && msg_warning !== "") {
            utility.messagehide("messageValidate", msg_warning, 1, 5000);
            return;
        }

        $('#txtNameDSTHCD').val("");
        $('#txtNguoiLapTHCD').val("");
        $('#txtNguoiKyTHCD').val("");
        $('#txtGhiChuTHCD').val("");

        $('#frmPopupTHCD')[0].reset();

        // $("#sltChedo").val('').select2();//.trigger('change');
        // $("#sltChedo option").removeAttr('selected');

        $("#sltChedo").select2({
            placeholder: "--- Chọn chế độ ---",
            allowClear: true,
            closeOnSelect : false,
            templateResult: function (data) {
                var $res = $('<span style="width: 500px;"></span>');
                var $check = $('<input type="checkbox" /> ');
            
                $res.text(data.text);
            
                if (data.element) {
                    $res.prepend($check);
                    $check.prop('checked', data.element.selected);
                }

                return $res;
            }
        });

        // $('#sltChedo').multiselect({
        //   nonSelectedText:'--- Chọn loại chế độ ---'
        // });
        // $("#sltChedo").val("").multiselect("clearSelection");

        $("#myModalLapDanhSachTHCD").modal("show");
    }

    function lapdanhsachDanhSachTongHop(objData) {

        var schools_id = $('#drSchoolTHCD').val();
        var year = $('#sltYear').val();

        // console.log(objData);


        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/lapdanhsach',
            data: objData,
            contentType: false,
            cache: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                console.log(data);
                if (data['success'] != "" && data['success'] != undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    // resetFormTHCD();
                    GET_INITIAL_NGHILC();
                    loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    $("#myModalLapDanhSachTHCD").modal("hide");
                    $('#btnInsertTHCD').button('reset');
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    // resetFormTHCD();
                    GET_INITIAL_NGHILC();
                    loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    utility.message("Thông báo",data['error'],null,3000,1);
                    $('#btnInsertTHCD').button('reset');
                }
            }, error: function(data) {
                $('#btnInsertTHCD').button('reset');
            }
        });
    };

    function lapdanhsachDanhSachTongHop_PheDuyet(objData) {

        var schools_id = $('#drSchoolTHCD').val();
        var year = $('#sltYear').val();

        // console.log(objData);


        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/lapdanhsach_PD',
            data: objData,
            contentType: false,
            cache: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                console.log(data);
                if (data['success'] != "" && data['success'] != undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    // resetFormTHCD();
                    GET_INITIAL_NGHILC();
                    loadlistApproved($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    $("#myModalLapDanhSachTHCD").modal("hide");
                    $('#btnInsertTHCD_PheDuyet').button('reset');
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    // resetFormTHCD();
                    GET_INITIAL_NGHILC();
                    loadlistApproved($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    utility.message("Thông báo",data['error'],null,3000,1);
                    $('#btnInsertTHCD_PheDuyet').button('reset');
                }
            }, error: function(data) {
                $('#btnInsertTHCD_PheDuyet').button('reset');
            }
        });
    };

    function lapdanhsachDanhSachTongHop_ThamDinh(objData) {

        var schools_id = $('#drSchoolTHCD').val();
        var year = $('#sltYear').val();

        // console.log(objData);


        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/lapdanhsach_TD',
            data: objData,
            contentType: false,
            cache: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                console.log(data);
                if (data['success'] != "" && data['success'] != undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    // resetFormTHCD();
                    GET_INITIAL_NGHILC();
                    loadlistApprovedPheDuyet($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    $("#myModalLapDanhSachTHCD").modal("hide");
                    $('#btnInsertTHCD_ThamDinh').button('reset');
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    // resetFormTHCD();
                    GET_INITIAL_NGHILC();
                    loadlistApprovedPheDuyet($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    utility.message("Thông báo",data['error'],null,3000,1);
                    $('#btnInsertTHCD_ThamDinh').button('reset');
                }
            }, error: function(data) {
                $('#btnInsertTHCD_ThamDinh').button('reset');
            }
        });
    };

    function validateTHCDTruong(){
        var messageValidate = "";
        var schools_id = $('#drSchoolTHCD').val();
        var year = $('#sltYear').val();

        if (schools_id == null || schools_id == "" || schools_id == 0) {
            messageValidate = "Vui lòng chọn trường!";
            return messageValidate;
        }
        if (year == null || year == "" || year == 0) {
            messageValidate = "Vui lòng chọn năm học!";
            return messageValidate;
        }

        return messageValidate;
    }

    function validateTHCD(){
        var messageValidate = "";
        var schools_id = $('#drSchoolTHCD').val();
        var socongvan = $('#sltCongvan').val();

        if (schools_id == null || schools_id == "" || schools_id == 0) {
            messageValidate = "Vui lòng chọn trường!";
            return messageValidate;
        }
        if (socongvan == null || socongvan == "") {
            messageValidate = "Vui lòng chọn số công văn!";
            return messageValidate;
        }

        return messageValidate;
    }

    

    function validatePopupTongHopCheDo(){
        var messageValidate = "";
        var reportName = $('#txtNameDSTHCD').val();
        var tennguoilap = $('#txtNguoiLapTHCD').val();
        var tennguoiky = $('#txtNguoiKyTHCD').val();

        var chedo = $('#sltChedo').val();

        if (reportName.trim() == "") {
            messageValidate = "Vui lòng nhập tên danh sách!";
            $('#txtNameDSTHCD').focus(); 
            return messageValidate;
        }else if (reportName.length > 200) {
            messageValidate = "Tên danh sách không được vượt quá 200 ký tự!";
            $('#txtNameDSTHCD').focus();
            $('#txtNameDSTHCD').val("");
            return messageValidate;
        }
        else{
            $('#txtNameDSTHCD').focusout();
            var specialChars = "#/|\\";

            for (var i = 0; i < reportName.length; i++) {
                if (specialChars.indexOf(reportName.charAt(i)) != -1) {
                    messageValidate = "Tên danh sách không được chứa ký tự #, /, |, \\!";
                    $('#txtNameDSTHCD').focus();
                    $('#txtNameDSTHCD').val("");
                    return messageValidate;
                }
            }
        }

        if (chedo == null || chedo.trim == "") {
            messageValidate = "Vui lòng chọn loại chế độ!";
            return messageValidate;
        }

        // if (tennguoilap.trim() == "") {
        //     messageValidate = "Vui lòng nhập tên người lập!";
        //     $('#txtNguoiLapTHCD').focus(); 
        //     return messageValidate;
        // }else if (tennguoilap.length > 200) {
        //     messageValidate = "Tên người lập không được vượt quá 200 ký tự!";
        //     $('#txtNguoiLapTHCD').focus();
        //     $('#txtNguoiLapTHCD').val("");
        //     return messageValidate;
        // }
        // else{
        //     $('#txtNguoiLapTHCD').focusout();
        //     var specialChars = "#/|\\";

        //     for (var i = 0; i < tennguoilap.length; i++) {
        //         if (specialChars.indexOf(tennguoilap.charAt(i)) != -1) {
        //             messageValidate = "Tên người lập không được chứa ký tự #, /, |, \\!";
        //             $('#txtNguoiLapTHCD').focus();
        //             $('#txtNguoiLapTHCD').val("");
        //             return messageValidate;
        //         }
        //     }
        // }

        // if (tennguoiky.trim() == "") {
        //     messageValidate = "Vui lòng nhập tên người ký!";
        //     $('#txtNguoiKyTHCD').focus(); 
        //     return messageValidate;
        // }else if (tennguoiky.length > 200) {
        //     messageValidate = "Tên người ký không được vượt quá 200 ký tự!";
        //     $('#txtNguoiKyTHCD').focus();
        //     $('#txtNguoiKyTHCD').val("");
        //     return messageValidate;
        // }
        // else{
        //     $('#txtNguoiKyTHCD').focusout();
        //     var specialChars = "#/|\\";

        //     for (var i = 0; i < tennguoiky.length; i++) {
        //         if (specialChars.indexOf(tennguoiky.charAt(i)) != -1) {
        //             messageValidate = "Tên người ký không được chứa ký tự #, /, |, \\!";
        //             $('#txtNguoiKyTHCD').focus();
        //             $('#txtNguoiKyTHCD').val("");
        //             return messageValidate;
        //         }
        //     }
        // }

        return messageValidate;
    }

    function resetFormTHCD(){
        $('#formtonghopchedo')[0].reset();
        $('#dataProfile').html("");
    }

    function loadMoneybySubject(objData){
        //console.log(objData);
        console.log(JSON.stringify({ LISTID: objData }));

        $.ajax({
            type: "get",
            url: '/ho-so/hoc-sinh/loadMoneybySub/' + objData,
            // data: JSON.stringify({ LISTID: objData }),
            // contentType: false,
            // cache: false,
            // processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                console.log(data);
                var html_show = "";
                var total = 0;

                if (data.length > 0) {
                    $('#tbMoney').removeAttr('hidden');
                    for (var i = 0; i < data.length; i++) {
                        
                        html_show += "<tr>";
                        html_show += "<td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * 10))+"</td>";
                        html_show += "<td>"+ConvertString(data[i]['group_name'])+"</td>";
                        html_show += "<td class='text-center'>"+ConvertString(data[i]['money'])+"</td>";
                        html_show += "</tr>";

                        total = total + parseInt(data[i]['money']);
                    }
                    html_show += "<tr>";
                    html_show += "<td class='text-center'></td>";
                    html_show += "<td class='text-center'>Tổng</td>";
                    html_show += "<td class='text-center'>"+ConvertString(total)+"</td>";
                    html_show += "</tr>";
                }

                $('#tbMoneyContent').html(html_show);
            }, error: function(data) {

            }
        });
    }

    function loaddataBaocaoTongHop(row) {

        var msg_warning = "";

        msg_warning = validateTHCD();

        // alert(msg_warning);

        if (msg_warning !== null && msg_warning !== "") {
            utility.messagehide("messageValidate", msg_warning, 1, 5000);
            return;
        }
        GET_INITIAL_NGHILC();
        var schools_id = $('#drSchoolTHCD').val();
        var year = $('#sltYear').val();

        
        var html_show = "";
        var o = {
            // start: (GET_START_RECORD_NGHILC()),
            // limit: row,
            SCHOOLID: schools_id,
            YEAR: year
        };
        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/loadDanhsachbaocao',
            data: JSON.stringify(o),
            // dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(datas) {
                // console.log(datas);

                // SETUP_PAGING_NGHILC(datas, function () {
                //     loaddataBaocaoTongHop(row);
                // });
                
                $('#contentPopupModalDanhsach').html("");
                
                if(datas.length > 0){
                    for (var i = 0; i < datas.length; i++) {
                                    
                        html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                        if (datas[i].report === "MGHP") {
                            html_show += "<td><a href='javascript:;' onclick='export_file("+datas[i].report_id+", 1);'>"+ConvertString(datas[i].report_name)+"</a></td>";
                            html_show += "<td class=''>Hỗ trợ miễn giảm học phí</td>";
                        }
                        else if (datas[i].report === "CPHT") {
                            html_show += "<td><a href='javascript:;' onclick='export_file("+datas[i].report_id+", 2);'>"+ConvertString(datas[i].report_name)+"</a></td>";
                            html_show += "<td class=''>Hỗ trợ chi phí học tập</td>";
                        }
                        else if (datas[i].report === "HTAT") {
                            html_show += "<td><a href='javascript:;' onclick='export_file("+datas[i].report_id+", 3);'>"+ConvertString(datas[i].report_name)+"</a></td>";
                            html_show += "<td class=''>Hỗ trợ ăn trưa cho trẻ em mẫu giáo</td>";
                        }
                        else if (datas[i].report === "HTBT") {
                            html_show += "<td><a href='javascript:;' onclick='export_file("+datas[i].report_id+", 4);'>"+ConvertString(datas[i].report_name)+"</a></td>";
                            html_show += "<td class=''>Hỗ trợ học sinh bán trú</td>";
                        }
                        else if (datas[i].report === "HSDTTS") {
                            html_show += "<td><a href='javascript:;' onclick='export_file("+datas[i].report_id+", 5);'>"+ConvertString(datas[i].report_name)+"</a></td>";
                            html_show += "<td class=''>Hỗ trợ học sinh dân tộc thiểu số tại huyện Mù Cang Chải và Trạm Tấu</td>";
                        }
                        else if (datas[i].report === "HSKT") {
                            html_show += "<td><a href='javascript:;' onclick='export_file("+datas[i].report_id+", 6);'>"+ConvertString(datas[i].report_name)+"</a></td>";
                            html_show += "<td class=''>Hỗ trợ học sinh khuyết tật</td>";
                        }
                        else if (datas[i].report === "NGNA") {
                            html_show += "<td><a href='javascript:;' onclick='export_file("+datas[i].report_id+", 8);'>"+ConvertString(datas[i].report_name)+"</a></td>";
                            html_show += "<td class=''>Hỗ trợ người nấu ăn</td>";
                        }
                        else if (datas[i].report === "HTATHS") {
                            html_show += "<td><a href='javascript:;' onclick='export_file("+datas[i].report_id+", 9);'>"+ConvertString(datas[i].report_name)+"</a></td>";
                            html_show += "<td class=''>Hỗ trợ ăn trưa cho học sinh</td>";
                        }
                        else if (datas[i].report === "HBHSDTNT") {
                            html_show += "<td><a href='javascript:;' onclick='export_file("+datas[i].report_id+", 10);'>"+ConvertString(datas[i].report_name)+"</a></td>";
                            html_show += "<td class=''>Hỗ trợ học bổng cho học sinh dân tộc nội trú</td>";
                        }
                        
                        // html_show += "<td>"+ConvertString(datas[i].report + '-' + datas[i].report_name)+"</td>";
                        if (parseInt(datas[i].report_status) === 0) {
                            html_show += "<td class='text-center'>Chưa gửi</td>";
                        }
                        else if (parseInt(datas[i].report_status) === 1) {
                            html_show += "<td class='text-center'>Đã gửi</td>";
                        }
                        else if (parseInt(datas[i].report_status) === 2) {
                            html_show += "<td class='text-center'>Trả lại</td>";
                        }
                        else if (parseInt(datas[i].report_status) === 3) {
                            html_show += "<td class='text-center'>Đã duyệt</td>";
                        }
                        else {
                            html_show += "<td class='text-center'>---</td>";
                        }
                        html_show += "<td class='text-center'>"+formatDates(datas[i].report_date)+"</td>";
                        html_show += "<td class=''>"+ConvertString(datas[i].first_name + ' ' + datas[i].last_name)+"</td>";
                        html_show += "</tr>";
                    }                            
                }
                else {
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }
                $('#contentPopupModalDanhsach').html(html_show);

                $("#modalDanhsachBaocao").modal("show");
            }, error: function(datas) {

            }
        });
    };

    function approvedAll(level = 0) {

        var msg_warning = "";

        msg_warning = validateTHCDTruong();

        // alert(msg_warning);

        if (msg_warning !== null && msg_warning !== "") {
            utility.messagehide("messageValidate", msg_warning, 1, 5000);
            return;
        }

        var schools_id = $('#drSchoolTHCD').val();
        var year = $('#sltYear').val();

        var ky = year.split("-");
        
        var o = {
            LEVEL: level,
            SCHOOLID: schools_id,
            YEAR: ky[1]
        };
        console.log(o);
        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/approvedAll',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                // console.log(data);
                if (data['success'] != "" && data['success'] != undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    GET_INITIAL_NGHILC();
                    if (level == 1) {
                        loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    }
                    if (level == 2) {
                        loadlistApproved($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    }
                    if (level == 3) {
                        loadlistApprovedPheDuyet($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    }
                    
                    closeLoading();
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    GET_INITIAL_NGHILC();
                    if (level == 1) {
                        loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    }
                    if (level == 2) {
                        loadlistApproved($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    }
                    if (level == 3) {
                        loadlistApprovedPheDuyet($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    }
                    
                    utility.message("Thông báo",data['error'],null,3000,1);
                    closeLoading();
                }
            }, error: function(data) {

            }
        });
    };

    function approvedUnAll(level = 0) {

        var msg_warning = "";

        msg_warning = validateTHCDTruong();

        // alert(msg_warning);

        if (msg_warning !== null && msg_warning !== "") {
            utility.messagehide("messageValidate", msg_warning, 1, 5000);
            return;
        }

        var schools_id = $('#drSchoolTHCD').val();
        var year = $('#sltYear').val();

        var ky = year.split("-");
        
        var o = {
            LEVEL: level,
            SCHOOLID: schools_id,
            YEAR: ky[1]
        };
        console.log(o);
        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/unApprovedAll',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                // console.log(data);
                if (data['success'] != "" && data['success'] != undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    GET_INITIAL_NGHILC();
                    if (level == 1) {
                        loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    }
                    if (level == 2) {
                        loadlistApproved($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    }
                    if (level == 3) {
                        loadlistApprovedPheDuyet($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    }
                    
                    closeLoading();
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    GET_INITIAL_NGHILC();
                    if (level == 1) {
                        loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    }
                    if (level == 2) {
                        loadlistApproved($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    }
                    if (level == 3) {
                        loadlistApprovedPheDuyet($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    }
                    
                    utility.message("Thông báo",data['error'],null,3000,1);
                    closeLoading();
                }
            }, error: function(data) {

            }
        });
    };


    function export_file(id, number) {
        var url_export = '';
        if (number == 1) {
            url_export = '/ho-so/lap-danh-sach/mien-giam-hoc-phi/downloadfileExport/';
        }
        if (number == 2) {
            url_export = '/ho-so/lap-danh-sach/chi-phi-hoc-tap/downloadfileExport/';
        }
        if (number == 3) {
            url_export = '/ho-so/lap-danh-sach/ho-tro-an-trua-tre-em/downloadfileExport/';
        }
        if (number == 4) {
            url_export = '/ho-so/lap-danh-sach/hoc-sinh-ban-tru/downloadfileExport/';
        }
        if (number == 5) {
            url_export = '/ho-so/lap-danh-sach/hoc-sinh-dan-toc-thieu-so/downloadfileExport/';
        }
        if (number == 6) {
            url_export = '/ho-so/lap-danh-sach/hoc-sinh-khuyet-tat/downloadfileExport/';
        }
        if (number == 7) {
            url_export = '/ho-so/lap-danh-sach/chinh-sach-uu-dai/downloadfileExport/';
        }
        if (number == 8) {
            url_export = '/ho-so/lap-danh-sach/nguoi-nau-an/downloadfileExport/';
        }
        if (number == 9) {
            url_export = '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/downloadfileExportHTATHS/';
        }
        if (number == 10) {
            url_export = '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/downloadfileExportHBHSDTNT/';
        }
        // console.log(number + "-------------------");
        // console.log(url_export + "-------------------");

        window.open(url_export + id, '_blank');
    }

//-----------------------------------------------------------------Phê duyệt---------------------------------------------------------------
    function loadlistApproved(row, keySearch = "", status = "") {

        var msg_warning = "";

        msg_warning = validateTHCD();

        // alert(msg_warning);

        if (msg_warning !== null && msg_warning !== "") {
            utility.messagehide("messageValidate", msg_warning, 1, 5000);
            return;
        }
        
        var schools_id = $('#drSchoolTHCD').val();
        // var year = $('#sltYear').val();
        var socongvan = $('#sltCongvan').val();

        // _year = year;

        // var ky = year.split("-");
        var number = 3;

        // if (ky[0] == 'HK1') {
        //     number = 1;
        // }
        // else if (ky[0] == 'HK2') {
        //     number = 2;
        // }
        // else if (ky[0] == 'CA') {
        //     number = 3;
        // }
        
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit: row,
            SCHOOLID: schools_id,
          //  YEAR: year,
            SOCONGVAN: socongvan,
            KEY: keySearch,
            STATUS: status
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
                    loadlistApproved(row, keySearch, status);
                });
                
                $('#dataListApproved').html("");
                var dataget = datas.data;
                console.log(dataget);
                
                if(dataget.length > 0){
                    for (var i = 0; i < dataget.length; i++) {
                        var totalMoney = 0;
                        totalMoney = parseInt(dataget[i].MGHP) + parseInt(dataget[i].CPHT) + parseInt(dataget[i].HTAT) + parseInt(dataget[i].HTBT_TA) + parseInt(dataget[i].HTBT_TO) + parseInt(dataget[i].HTBT_VHTT) + parseInt(dataget[i].HTATHS) + parseInt(dataget[i].HSKT_HB) + parseInt(dataget[i].HSKT_DDHT) + parseInt(dataget[i].HBHSDTNT) + parseInt(dataget[i].HSDTTS);

                        html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                        html_show += "<td><a href='javascript:;' onclick='getProfileSubById("+parseInt(dataget[i].profile_id)+", "+number+");'>"+dataget[i].profile_name+"</a></td>";
                        html_show += "<td>"+formatDates(dataget[i].profile_birthday)+"</td>";
                        html_show += "<td>"+dataget[i].schools_name+"</td>";
                        html_show += "<td>"+dataget[i].class_name+"</td>";
                        html_show += "<td>"+formatter(dataget[i].MGHP)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].CPHT)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HTAT)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HTBT_TA)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HTBT_TO)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HTBT_VHTT)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HTATHS)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HSKT_HB)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HSKT_DDHT)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HBHSDTNT)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HSDTTS)+"</td>";
                        html_show += "<td>"+formatter(totalMoney)+"</td>";
                        // html_show += "<td>"+ConvertString(dataget[i].GHICHU)+"</td>";
                        
                        
                        // if(check_Permission_Feature("2")){
                        //     html_show += "<button data='"+dataget[i].profile_id+"' onclick='getHoSoHocSinh("+dataget[i].profile_id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Sửa </button> &nbsp;";
                        // }
                        // if(check_Permission_Feature("3")){
                        //     html_show += " &nbsp;<button  onclick='delHoSoHocSinh("+dataget[i].profile_id+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                        // }
                        // if (parseInt(dataget[i].TRANGTHAITHAMDINH) == 0) {
                            if(parseInt(dataget[i].TRANGTHAIPHEDUYET) == 0){
                                html_show += "<td class='text-center' style='vertical-align:middle'><button class='btn btn-primary btn-xs' onclick='openPopupPheDuyetChedoNew("+dataget[i].profile_id+", \""+socongvan+"\")'> Chờ phê duyệt</button> </td>";
                            }else if(parseInt(dataget[i].TRANGTHAIPHEDUYET) == 1){
                                html_show += "<td class='text-center' style='vertical-align:middle'><button class='btn btn-success btn-xs' onclick='openPopupPheDuyetChedoNew("+dataget[i].profile_id+", \""+socongvan+"\")'> Đã phê duyệt </button> </td>";
                            }
                        // }
                        // else {
                        //     html_show += "<td class='text-center' style='vertical-align:middle'><button class='btn btn-primary btn-xs'> Đã thẩm định</button></td>";
                        // }
                        
                        html_show += "</tr>";
                    }
                }
                else {
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }
                $('#dataListApproved').html(html_show);
            }, error: function(dataget) {

            }
        });
    };

    function openPopupPheDuyetChedoNew(id, socongvan){
        
        _idProfile = id;
        // id = id + '-' + socongvan + '-' + _year;

        var objJson = JSON.stringify({ PROFILEID: id, SOCONGVAN: socongvan });
        // console.log(objJson);
        $('#txtGhiChuTHCD').val('');
        $('#checkedAllChedo').prop('checked', false);

        $.ajax({
            type: "get",
            url:'/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/getProfileSubjectByIdPhongSo/' + objJson,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                // console.log(data);
                
                console.log(data['GROUP']);
                // console.log(data['SUBJECT']);
                // console.log(data['CHEDO']);

                var html_show = "";

                var groupId = 0;

                if (data !== null && data !== "") {
                    // for (var i = 0; i < data['GROUP'].length; i++) {

                        for (var j = 0; j < data['SUBJECT'].length; j++) {
                            // if (data['GROUP'][i].group_id == data['SUBJECT'][j].subject_history_group_id) {
                                html_show += "<tr>";
                                html_show += "<td class='text-center'>"+(j + 1 + (GET_START_RECORD_NGHILC() * 10))+"</td>";
                                html_show += "<td class='text-center'>";

                                // console.log(data['CHEDO'][0]['TRANGTHAIHK1']);
                                // console.log(data['CHEDO'][0]['TRANGTHAIHK2']);

                                // if (data['CHEDO'][0]['TRANGTHAI_PHEDUYET'] == 1) {

                                    if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 89 
                                            || parseInt(data['SUBJECT'][j].subject_history_group_id) === 90
                                            || parseInt(data['SUBJECT'][j].subject_history_group_id) === 91) && parseInt(data['GROUP'][0]['trangthai_pheduyet_MGHP']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 92) && parseInt(data['GROUP'][0]['trangthai_pheduyet_CPHT']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 93) && parseInt(data['GROUP'][0]['trangthai_pheduyet_HTAT']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 94) && parseInt(data['GROUP'][0]['trangthai_pheduyet_HTBT']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 98) && parseInt(data['GROUP'][0]['trangthai_pheduyet_HTBT']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 115) && parseInt(data['GROUP'][0]['trangthai_pheduyet_HTBT']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 95) && parseInt(data['GROUP'][0]['trangthai_pheduyet_HSKT']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 100) && parseInt(data['GROUP'][0]['trangthai_pheduyet_HSKT']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 99) && parseInt(data['GROUP'][0]['trangthai_pheduyet_HSDTTS']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 118) && parseInt(data['GROUP'][0]['trangthai_pheduyet_HTATHS']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 119) && parseInt(data['GROUP'][0]['trangthai_pheduyet_HBHSDTNT']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else{
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"'>";
                                    }
                                // }
                                // else{
                                //     html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"'>";
                                // }
                                
                                html_show += "</td>";
                                
                                html_show += "<td>"+data['SUBJECT'][j].group_name+"</td>";
                                html_show += "<td>"+data['SUBJECT'][j].subject_name+"</td>";
                                html_show += "</tr>";
                            // }
                        }
                    // }
                }

                // $('#txtGhiChuTHCD').val(data['CHEDO'][0]['GHICHU_PHEDUYET']);
                $('#dataDanhsachCheDo').html(html_show);
                $("#myModalApproved").modal("show");
            }, error: function(data) {
                closeLoading();
            }
        });
    }

    function openPopupPheDuyetChedo(id, idTHCD, number){
        _id = idTHCD;
        _idProfile = id;
        id = id + '-' + number + '-' + _year;

        $('#txtGhiChuTHCD').val('');
        $('#checkedAllChedo').prop('checked', false);

        $.ajax({
            type: "get",
            url:'/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/getProfileSubById/' + id,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                // console.log(data);
                
                // console.log(data['GROUP']);
                // console.log(data['SUBJECT']);
                // console.log(data['CHEDO']);

                var html_show = "";

                var groupId = 0;

                if (data !== null && data !== "") {
                    // for (var i = 0; i < data['GROUP'].length; i++) {

                        for (var j = 0; j < data['SUBJECT'].length; j++) {
                            // if (data['GROUP'][i].group_id == data['SUBJECT'][j].subject_history_group_id) {
                                html_show += "<tr>";
                                html_show += "<td class='text-center'>"+(j + 1 + (GET_START_RECORD_NGHILC() * 10))+"</td>";
                                html_show += "<td class='text-center'>";

                                // console.log(data['CHEDO'][0]['TRANGTHAIHK1']);
                                // console.log(data['CHEDO'][0]['TRANGTHAIHK2']);

                                if (data['CHEDO'][0]['TRANGTHAI_PHEDUYET'] == 1) {

                                    if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 89 
                                            || parseInt(data['SUBJECT'][j].subject_history_group_id) === 90
                                            || parseInt(data['SUBJECT'][j].subject_history_group_id) === 91) && parseInt(data['CHEDO'][0]['MGHP_PHEDUYET']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 92) && parseInt(data['CHEDO'][0]['CPHT_PHEDUYET']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 93) && parseInt(data['CHEDO'][0]['HTAT_PHEDUYET']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 94) && parseInt(data['CHEDO'][0]['HTBT_TA_PHEDUYET']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 98) && parseInt(data['CHEDO'][0]['HTBT_TO_PHEDUYET']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 115) && parseInt(data['CHEDO'][0]['HTBT_VHTT_PHEDUYET']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 95) && parseInt(data['CHEDO'][0]['HSKT_HB_PHEDUYET']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 100) && parseInt(data['CHEDO'][0]['HSKT_DDHT_PHEDUYET']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 99) && parseInt(data['CHEDO'][0]['HSDTTS_PHEDUYET']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 118) && parseInt(data['CHEDO'][0]['HTATHS_PHEDUYET']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 119) && parseInt(data['CHEDO'][0]['HBHSDTNT_PHEDUYET']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else{
                                        html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"'>";
                                    }
                                }
                                else{
                                    html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"'>";
                                }
                                
                                html_show += "</td>";
                                
                                html_show += "<td>"+data['SUBJECT'][j].group_name+"</td>";
                                html_show += "<td>"+data['SUBJECT'][j].subject_name+"</td>";
                                html_show += "</tr>";
                            // }
                        }
                    // }
                }

                $('#txtGhiChuTHCD').val(data['CHEDO'][0]['GHICHU_PHEDUYET']);
                $('#dataDanhsachCheDo').html(html_show);
                $("#myModalApproved").modal("show");
            }, error: function(data) {
                closeLoading();
            }
        });
    }

    function approvedChedoPheDuyet(objData, truong, socongvan, note){
        // var strData = 'ID' + _id + '-' + _year + '-' + 'IDPROFILE' + _idProfile + '-' + note + '.' + '-' + objData;
        // console.log(strData);
        // utility.confirm("Duyệt cấp kinh phí?", "Bạn có chắc chắn muốn Duyệt?", function () {
            var objJson = JSON.stringify({ PROFILEID: _idProfile, SCHOOLID: truong, SOCONGVAN: socongvan, ARRSUBJECTID: objData, NOTE: note });
            $.ajax({
                type: "get",
                url:'/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/approvedchedoPD/' + objJson,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(data) {
                    console.log(data);
                    if (data['success'] != "" && data['success'] != undefined) {
                        utility.message("Thông báo",data['success'],null,3000);
                        // resetFormTHCD();
                        loadlistApproved($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                        $("#myModalApproved").modal("hide");
                        closeLoading();
                    }
                    if (data['error'] != "" && data['error'] != undefined) {
                        // resetFormTHCD();
                        loadlistApproved($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                        utility.message("Thông báo",data['error'],null,3000,1);
                        closeLoading();
                    }
                }, error: function(data) {
                    closeLoading();
                }
            });
        // });
    }

    function loadlistUnApproved(row, keySearch = "") {

        var msg_warning = "";

        msg_warning = validateTHCD();

        // alert(msg_warning);

        if (msg_warning !== null && msg_warning !== "") {
            utility.messagehide("messageValidate", msg_warning, 1, 5000);
            return;
        }
        
        var schools_id = $('#drSchoolTHCD').val();
        // var year = $('#sltYear').val();
        var socongvan = $('#sltCongvan').val();

        // _year = year;

        // var ky = year.split("-");
        var number = 3;

        // if (ky[0] == 'HK1') {
        //     number = 1;
        // }
        // else if (ky[0] == 'HK2') {
        //     number = 2;
        // }
        // else if (ky[0] == 'CA') {
        //     number = 3;
        // }
        
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit: row,
            SCHOOLID: schools_id,
          //  YEAR: year,
            SOCONGVAN: socongvan,
            KEY: keySearch
        };
        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/loadListUnApproved',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(datas) {

                SETUP_PAGING_NGHILC(datas, function () {
                    loadlistApproved(row, keySearch, status);
                });
                
                $('#dataListPhongRevert').html("");
                var dataget = datas.data;
                console.log(dataget);
                
                if(dataget.length > 0){
                    for (var i = 0; i < dataget.length; i++) {
                        var totalMoney = 0;
                        totalMoney = parseInt(dataget[i].MGHP) + parseInt(dataget[i].CPHT) + parseInt(dataget[i].HTAT) + parseInt(dataget[i].HTBT_TA) + parseInt(dataget[i].HTBT_TO) + parseInt(dataget[i].HTBT_VHTT) + parseInt(dataget[i].HTATHS) + parseInt(dataget[i].HSKT_HB) + parseInt(dataget[i].HSKT_DDHT) + parseInt(dataget[i].HBHSDTNT) + parseInt(dataget[i].HSDTTS);

                        html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                        html_show += "<td><a href='javascript:;' onclick='getProfileSubById("+parseInt(dataget[i].profile_id)+", "+number+");'>"+dataget[i].profile_name+"</a></td>";
                        html_show += "<td>"+formatDates(dataget[i].profile_birthday)+"</td>";
                        html_show += "<td>"+dataget[i].schools_name+"</td>";
                        html_show += "<td>"+dataget[i].class_name+"</td>";
                        html_show += "<td>"+formatter(dataget[i].MGHP)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].CPHT)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HTAT)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HTBT_TA)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HTBT_TO)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HTBT_VHTT)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HTATHS)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HSKT_HB)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HSKT_DDHT)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HBHSDTNT)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HSDTTS)+"</td>";
                        html_show += "<td>"+formatter(totalMoney)+"</td>";
                        html_show += "<td>"+ConvertString(dataget[i].Note)+"</td>";
                        // html_show += "<td>"+ConvertString(dataget[i].GHICHU)+"</td>";
                        
                        
                        // if(check_Permission_Feature("2")){
                        //     html_show += "<button data='"+dataget[i].profile_id+"' onclick='getHoSoHocSinh("+dataget[i].profile_id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Sửa </button> &nbsp;";
                        // }
                        // if(check_Permission_Feature("3")){
                        //     html_show += " &nbsp;<button  onclick='delHoSoHocSinh("+dataget[i].profile_id+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                        // }
                        // if (parseInt(dataget[i].TRANGTHAITHAMDINH) == 0) {
                        //     if(parseInt(dataget[i].TRANGTHAIPHEDUYET) == 0){
                        //         html_show += "<td class='text-center' style='vertical-align:middle'><button class='btn btn-primary btn-xs' onclick='openPopupPheDuyetChedoNew("+dataget[i].profile_id+", \""+socongvan+"\")'> Chờ phê duyệt</button> </td>";
                        //     }else if(parseInt(dataget[i].TRANGTHAIPHEDUYET) == 1){
                        //         html_show += "<td class='text-center' style='vertical-align:middle'><button class='btn btn-success btn-xs' onclick='openPopupPheDuyetChedoNew("+dataget[i].profile_id+", \""+socongvan+"\")'> Đã phê duyệt </button> </td>";
                        //     }
                        // }
                        // else {
                        //     html_show += "<td class='text-center' style='vertical-align:middle'><button class='btn btn-primary btn-xs'> Đã thẩm định</button></td>";
                        // }
                        
                        html_show += "</tr>";
                    }                            
                }
                else {
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }
                $('#dataListPhongRevert').html(html_show);
            }, error: function(dataget) {

            }
        });
    };

    function approvedAllPheDuyet() {

        var msg_warning = "";

        msg_warning = validateTHCD();

        // alert(msg_warning);

        if (msg_warning !== null && msg_warning !== "") {
            utility.messagehide("messageValidate", msg_warning, 1, 5000);
            return;
        }

        var schools_id = $('#drSchoolTHCD').val();
        var socongvan = $('#sltCongvan').val();
        
        var o = {
            SCHOOLID: schools_id,
            SOCONGVAN: socongvan
        };
        // console.log(o);
        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/approvedAllPheDuyet',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                // console.log(data);
                if (data['success'] != "" && data['success'] != undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    GET_INITIAL_NGHILC();
                    
                    loadlistApproved($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    
                    closeLoading();
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    GET_INITIAL_NGHILC();
                    
                    loadlistApproved($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    
                    utility.message("Thông báo",data['error'],null,3000,1);
                    closeLoading();
                }
            }, error: function(data) {

            }
        });
    };

    function unApprovedAllPheDuyet() {

        var msg_warning = "";

        msg_warning = validateTHCD();

        // alert(msg_warning);

        if (msg_warning !== null && msg_warning !== "") {
            utility.messagehide("messageValidate", msg_warning, 1, 5000);
            return;
        }

        var schools_id = $('#drSchoolTHCD').val();
        var socongvan = $('#sltCongvan').val();
        
        var o = {
            SCHOOLID: schools_id,
            SOCONGVAN: socongvan
        };
        // console.log(o);
        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/unApprovedAllPheDuyet',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                // console.log(data);
                if (data['success'] != "" && data['success'] != undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    GET_INITIAL_NGHILC();
                    loadlistApproved($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    
                    closeLoading();
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    GET_INITIAL_NGHILC();
                    loadlistApproved($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    
                    utility.message("Thông báo",data['error'],null,3000,1);
                    closeLoading();
                }
            }, error: function(data) {

            }
        });
    };

//-----------------------------------------------------------------Thẩm định---------------------------------------------------------------
    function loadlistApprovedPheDuyet(row, keySearch = "", status = "") {

        var msg_warning = "";

        msg_warning = validateTHCD();

        // alert(msg_warning);

        if (msg_warning !== null && msg_warning !== "") {
            utility.messagehide("messageValidate", msg_warning, 1, 5000);
            return;
        }
        
        var schools_id = $('#drSchoolTHCD').val();
        var socongvan = $('#sltCongvan').val();

        // _year = year;

        // var ky = year.split("-");
        var number = 3;

        // if (ky[0] == 'HK1') {
        //     number = 1;
        // }
        // else if (ky[0] == 'HK2') {
        //     number = 2;
        // }
        // else if (ky[0] == 'CA') {
        //     number = 3;
        // }
        
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit: row,
            SCHOOLID: schools_id,
            // YEAR: year,
            SOCONGVAN: socongvan,
            KEY: keySearch,
            STATUS: status
        };
        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/loadListApprovedPheduyet',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(datas) {

                SETUP_PAGING_NGHILC(datas, function () {
                    loadlistApprovedPheDuyet(row, keySearch, status);
                });
                
                $('#dataListApproved').html("");
                var dataget = datas.data;
                console.log(dataget);
                
                if(dataget.length > 0){
                    for (var i = 0; i < dataget.length; i++) {
                        var totalMoney = 0;
                        totalMoney = parseInt(dataget[i].MGHP) + parseInt(dataget[i].CPHT) + parseInt(dataget[i].HTAT) + parseInt(dataget[i].HTBT_TA) + parseInt(dataget[i].HTBT_TO) + parseInt(dataget[i].HTBT_VHTT) + parseInt(dataget[i].HTATHS) + parseInt(dataget[i].HSKT_HB) + parseInt(dataget[i].HSKT_DDHT) + parseInt(dataget[i].HBHSDTNT) + parseInt(dataget[i].HSDTTS);
                                    
                        html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                        html_show += "<td><a href='javascript:;' onclick='getProfileSubById("+parseInt(dataget[i].profile_id)+", "+number+");'>"+dataget[i].profile_name+"</a></td>";
                        html_show += "<td>"+formatDates(dataget[i].profile_birthday)+"</td>";
                        html_show += "<td>"+dataget[i].schools_name+"</td>";
                        html_show += "<td>"+dataget[i].class_name+"</td>";
                        html_show += "<td>"+formatter(dataget[i].MGHP)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].CPHT)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HTAT)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HTBT_TA)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HTBT_TO)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HTBT_VHTT)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HTATHS)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HSKT_HB)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HSKT_DDHT)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HBHSDTNT)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HSDTTS)+"</td>";
                        html_show += "<td>"+formatter(totalMoney)+"</td>";
                        // html_show += "<td>"+ConvertString(dataget[i].GHICHU)+"</td>";
                        html_show += "<td class='text-center' style='vertical-align:middle'>"
                        // if(check_Permission_Feature("2")){
                        //     html_show += "<button data='"+dataget[i].profile_id+"' onclick='getHoSoHocSinh("+dataget[i].profile_id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Sửa </button> &nbsp;";
                        // }
                        // if(check_Permission_Feature("3")){
                        //     html_show += " &nbsp;<button  onclick='delHoSoHocSinh("+dataget[i].profile_id+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                        // }
                        if(parseInt(dataget[i].TRANGTHAITHAMDINH) == 0){
                            html_show += "<button class='btn btn-primary btn-xs' onclick='openPopupThamDinhChedoNew("+dataget[i].profile_id+", \""+socongvan+"\")'> Chờ thẩm định</button> ";
                        }else if(parseInt(dataget[i].TRANGTHAITHAMDINH) == 1){
                            html_show += "<button class='btn btn-success btn-xs' onclick='openPopupThamDinhChedoNew("+dataget[i].profile_id+", \""+socongvan+"\")'> Đã thẩm định</button> ";
                        }
                        html_show += "</td></tr>";
                    }                            
                }
                else {
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }
                $('#dataListApproved').html(html_show);
            }, error: function(dataget) {

            }
        });
    };

    function openPopupThamDinhChedoNew(id, socongvan){
        
        _idProfile = id;
        // id = id + '-' + socongvan + '-' + _year;

        var objJson = JSON.stringify({ PROFILEID: id, SOCONGVAN: socongvan });
        // console.log(objJson);
        $('#txtGhiChuTHCD').val('');
        $('#checkedAllChedo').prop('checked', false);

        $.ajax({
            type: "get",
            url:'/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/getProfileSubjectByIdPhongSo/' + objJson,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                // console.log(data);
                
                console.log(data['GROUP']);
                // console.log(data['SUBJECT']);
                // console.log(data['CHEDO']);

                var html_show = "";

                var groupId = 0;

                if (data !== null && data !== "") {
                    // for (var i = 0; i < data['GROUP'].length; i++) {

                        for (var j = 0; j < data['SUBJECT'].length; j++) {
                            // if (data['GROUP'][i].group_id == data['SUBJECT'][j].subject_history_group_id) {
                                html_show += "<tr>";
                                html_show += "<td class='text-center'>"+(j + 1 + (GET_START_RECORD_NGHILC() * 10))+"</td>";
                                html_show += "<td class='text-center'>";

                                // console.log(data['CHEDO'][0]['TRANGTHAIHK1']);
                                // console.log(data['CHEDO'][0]['TRANGTHAIHK2']);

                                // if (data['CHEDO'][0]['TRANGTHAI_PHEDUYET'] == 1) {

                                    if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 89 
                                            || parseInt(data['SUBJECT'][j].subject_history_group_id) === 90
                                            || parseInt(data['SUBJECT'][j].subject_history_group_id) === 91) && parseInt(data['GROUP'][0]['trangthai_thamdinh_MGHP']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 92) && parseInt(data['GROUP'][0]['trangthai_thamdinh_CPHT']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 93) && parseInt(data['GROUP'][0]['trangthai_thamdinh_HTAT']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 94) && parseInt(data['GROUP'][0]['trangthai_thamdinh_HTBT']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 98) && parseInt(data['GROUP'][0]['trangthai_thamdinh_HTBT']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 115) && parseInt(data['GROUP'][0]['trangthai_thamdinh_HTBT']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 95) && parseInt(data['GROUP'][0]['trangthai_thamdinh_HSKT']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 100) && parseInt(data['GROUP'][0]['trangthai_thamdinh_HSKT']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 99) && parseInt(data['GROUP'][0]['trangthai_thamdinh_HSDTTS']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 118) && parseInt(data['GROUP'][0]['trangthai_thamdinh_HTATHS']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 119) && parseInt(data['GROUP'][0]['trangthai_thamdinh_HBHSDTNT']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else{
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"'>";
                                    }
                                // }
                                // else{
                                //     html_show += "<input type='checkbox' id='chilCheckPD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"'>";
                                // }
                                
                                html_show += "</td>";
                                
                                html_show += "<td>"+data['SUBJECT'][j].group_name+"</td>";
                                html_show += "<td>"+data['SUBJECT'][j].subject_name+"</td>";
                                html_show += "</tr>";
                            // }
                        }
                    // }
                }

                // $('#txtGhiChuTHCD').val(data['CHEDO'][0]['GHICHU_PHEDUYET']);
                $('#dataDanhsachCheDo').html(html_show);
                $("#myModalApproved").modal("show");
            }, error: function(data) {
                closeLoading();
            }
        });
    }

    function openPopupThamDinhChedo(id, idTHCD, number){
        _id = idTHCD;
        _idProfile = id;
        id = id + '-' + number + '-' + _year;

        $('#txtGhiChuTHCD').val('');
        $('#checkedAllChedo').prop('checked', false);

        $.ajax({
            type: "get",
            url:'/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/getProfileSubById/' + id,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                // console.log(data);
                
                // console.log(data['GROUP']);
                // console.log(data['SUBJECT']);
                // console.log(data['CHEDO']);

                var html_show = "";

                var groupId = 0;

                if (data !== null && data !== "") {
                    // for (var i = 0; i < data['GROUP'].length; i++) {

                        for (var j = 0; j < data['SUBJECT'].length; j++) {
                            // if (data['GROUP'][i].group_id == data['SUBJECT'][j].subject_history_group_id) {
                                html_show += "<tr>";
                                html_show += "<td class='text-center'>"+(j + 1 + (GET_START_RECORD_NGHILC() * 10))+"</td>";
                                html_show += "<td class='text-center'>";

                                // console.log(data['CHEDO'][0]['TRANGTHAIHK1']);
                                // console.log(data['CHEDO'][0]['TRANGTHAIHK2']);

                                if (data['CHEDO'][0]['TRANGTHAI_THAMDINH'] == 1) {

                                    if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 89 
                                            || parseInt(data['SUBJECT'][j].subject_history_group_id) === 90
                                            || parseInt(data['SUBJECT'][j].subject_history_group_id) === 91) && parseInt(data['CHEDO'][0]['MGHP_THAMDINH']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 92) && parseInt(data['CHEDO'][0]['CPHT_THAMDINH']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 93) && parseInt(data['CHEDO'][0]['HTAT_THAMDINH']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 94) && parseInt(data['CHEDO'][0]['HTBT_TA_THAMDINH']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 98) && parseInt(data['CHEDO'][0]['HTBT_TO_THAMDINH']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 115) && parseInt(data['CHEDO'][0]['HTBT_VHTT_THAMDINH']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 95) && parseInt(data['CHEDO'][0]['HSKT_HB_THAMDINH']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 100) && parseInt(data['CHEDO'][0]['HSKT_DDHT_THAMDINH']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 99) && parseInt(data['CHEDO'][0]['HSDTTS_THAMDINH']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 118) && parseInt(data['CHEDO'][0]['HTATHS_THAMDINH']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_history_group_id) === 119) && parseInt(data['CHEDO'][0]['HBHSDTNT_THAMDINH']) == 1) {
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"' checked='checked'>";
                                    }
                                    else{
                                        html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"'>";
                                    }
                                }
                                else{
                                    html_show += "<input type='checkbox' id='chilCheckTD' name='choose' value='"+data['SUBJECT'][j].subject_history_group_id+"'>";
                                }
                                
                                html_show += "</td>";
                                
                                html_show += "<td>"+data['SUBJECT'][j].group_name+"</td>";
                                html_show += "<td>"+data['SUBJECT'][j].subject_name+"</td>";
                                html_show += "</tr>";
                            // }
                        }
                    // }
                }

                $('#txtGhiChuTHCD').val(data['CHEDO'][0]['GHICHU_THAMDINH']);
                $('#dataDanhsachCheDo').html(html_show);
                $("#myModalApproved").modal("show");
            }, error: function(data) {
                closeLoading();
            }
        });
    }

    function approvedChedoThamDinh(objData, truong, socongvan, note){
        // var strData = 'ID' + _id + '-' + _year + '-' + 'IDPROFILE' + _idProfile + '-' + note + '.' + '-' + objData;
        // console.log(strData);
        // utility.confirm("Duyệt cấp kinh phí?", "Bạn có chắc chắn muốn Duyệt?", function () {
            var objJson = JSON.stringify({ PROFILEID: _idProfile, SCHOOLID: truong, SOCONGVAN: socongvan, ARRSUBJECTID: objData, NOTE: note });
            $.ajax({
                type: "get",
                url:'/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/approvedchedoTD/' + objJson,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(data) {
                    console.log(data);
                    if (data['success'] != "" && data['success'] != undefined) {
                        utility.message("Thông báo",data['success'],null,3000);
                        // resetFormTHCD();
                        loadlistApprovedPheDuyet($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                        $("#myModalApproved").modal("hide");
                        closeLoading();
                    }
                    if (data['error'] != "" && data['error'] != undefined) {
                        // resetFormTHCD();
                        loadlistApprovedPheDuyet($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                        utility.message("Thông báo",data['error'],null,3000,1);
                        closeLoading();
                    }
                }, error: function(data) {
                    closeLoading();
                }
            });
        // });
    }


    function loadlistUnApprovedThamdinh(row, keySearch = "") {

        var msg_warning = "";

        msg_warning = validateTHCD();

        // alert(msg_warning);

        if (msg_warning !== null && msg_warning !== "") {
            utility.messagehide("messageValidate", msg_warning, 1, 5000);
            return;
        }
        
        var schools_id = $('#drSchoolTHCD').val();
        // var year = $('#sltYear').val();
        var socongvan = $('#sltCongvan').val();

        // _year = year;

        // var ky = year.split("-");
        var number = 3;

        // if (ky[0] == 'HK1') {
        //     number = 1;
        // }
        // else if (ky[0] == 'HK2') {
        //     number = 2;
        // }
        // else if (ky[0] == 'CA') {
        //     number = 3;
        // }
        
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit: row,
            SCHOOLID: schools_id,
          //  YEAR: year,
            SOCONGVAN: socongvan,
            KEY: keySearch
        };
        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/loadListUnApprovedThamDinh',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(datas) {

                SETUP_PAGING_NGHILC(datas, function () {
                    loadlistApproved(row, keySearch, status);
                });
                
                $('#dataListUnApprovedSo').html("");
                var dataget = datas.data;
                console.log(dataget);
                
                if(dataget.length > 0){
                    for (var i = 0; i < dataget.length; i++) {
                        var totalMoney = 0;
                        totalMoney = parseInt(dataget[i].MGHP) + parseInt(dataget[i].CPHT) + parseInt(dataget[i].HTAT) + parseInt(dataget[i].HTBT_TA) + parseInt(dataget[i].HTBT_TO) + parseInt(dataget[i].HTBT_VHTT) + parseInt(dataget[i].HTATHS) + parseInt(dataget[i].HSKT_HB) + parseInt(dataget[i].HSKT_DDHT) + parseInt(dataget[i].HBHSDTNT) + parseInt(dataget[i].HSDTTS);

                        html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                        html_show += "<td><a href='javascript:;' onclick='getProfileSubById("+parseInt(dataget[i].profile_id)+", "+number+");'>"+dataget[i].profile_name+"</a></td>";
                        html_show += "<td>"+formatDates(dataget[i].profile_birthday)+"</td>";
                        html_show += "<td>"+dataget[i].schools_name+"</td>";
                        html_show += "<td>"+dataget[i].class_name+"</td>";
                        html_show += "<td>"+formatter(dataget[i].MGHP)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].CPHT)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HTAT)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HTBT_TA)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HTBT_TO)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HTBT_VHTT)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HTATHS)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HSKT_HB)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HSKT_DDHT)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HBHSDTNT)+"</td>";
                        html_show += "<td>"+formatter(dataget[i].HSDTTS)+"</td>";
                        html_show += "<td>"+formatter(totalMoney)+"</td>";
                        html_show += "<td>"+ConvertString(dataget[i].Note)+"</td>";
                        // html_show += "<td>"+ConvertString(dataget[i].GHICHU)+"</td>";
                        
                        
                        // if(check_Permission_Feature("2")){
                        //     html_show += "<button data='"+dataget[i].profile_id+"' onclick='getHoSoHocSinh("+dataget[i].profile_id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Sửa </button> &nbsp;";
                        // }
                        // if(check_Permission_Feature("3")){
                        //     html_show += " &nbsp;<button  onclick='delHoSoHocSinh("+dataget[i].profile_id+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                        // }
                        // if (parseInt(dataget[i].TRANGTHAITHAMDINH) == 0) {
                        //     if(parseInt(dataget[i].TRANGTHAIPHEDUYET) == 0){
                        //         html_show += "<td class='text-center' style='vertical-align:middle'><button class='btn btn-primary btn-xs' onclick='openPopupPheDuyetChedoNew("+dataget[i].profile_id+", \""+socongvan+"\")'> Chờ phê duyệt</button> </td>";
                        //     }else if(parseInt(dataget[i].TRANGTHAIPHEDUYET) == 1){
                        //         html_show += "<td class='text-center' style='vertical-align:middle'><button class='btn btn-success btn-xs' onclick='openPopupPheDuyetChedoNew("+dataget[i].profile_id+", \""+socongvan+"\")'> Đã phê duyệt </button> </td>";
                        //     }
                        // }
                        // else {
                        //     html_show += "<td class='text-center' style='vertical-align:middle'><button class='btn btn-primary btn-xs'> Đã thẩm định</button></td>";
                        // }
                        
                        html_show += "</tr>";
                    }                            
                }
                else {
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }
                $('#dataListUnApprovedSo').html(html_show);
            }, error: function(dataget) {

            }
        });
    };

    function approvedAllThamDinh() {

        var msg_warning = "";

        msg_warning = validateTHCD();

        // alert(msg_warning);

        if (msg_warning !== null && msg_warning !== "") {
            utility.messagehide("messageValidate", msg_warning, 1, 5000);
            return;
        }

        var schools_id = $('#drSchoolTHCD').val();
        var socongvan = $('#sltCongvan').val();
        
        var o = {
            SCHOOLID: schools_id,
            SOCONGVAN: socongvan
        };
        // console.log(o);
        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/approvedAllThamDinh',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                // console.log(data);
                if (data['success'] != "" && data['success'] != undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    GET_INITIAL_NGHILC();
                    
                    loadlistApprovedPheDuyet($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    
                    closeLoading();
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    GET_INITIAL_NGHILC();
                    
                    loadlistApprovedPheDuyet($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    
                    utility.message("Thông báo",data['error'],null,3000,1);
                    closeLoading();
                }
            }, error: function(data) {

            }
        });
    };

    function unApprovedAllThamDinh() {

        var msg_warning = "";

        msg_warning = validateTHCD();

        // alert(msg_warning);

        if (msg_warning !== null && msg_warning !== "") {
            utility.messagehide("messageValidate", msg_warning, 1, 5000);
            return;
        }

        var schools_id = $('#drSchoolTHCD').val();
        var socongvan = $('#sltCongvan').val();
        
        var o = {
            SCHOOLID: schools_id,
            SOCONGVAN: socongvan
        };
        // console.log(o);
        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/unApprovedAllThamDinh',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                // console.log(data);
                if (data['success'] != "" && data['success'] != undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    GET_INITIAL_NGHILC();
                    loadlistApprovedPheDuyet($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    
                    closeLoading();
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    GET_INITIAL_NGHILC();
                    loadlistApprovedPheDuyet($('#drPagingDanhsachtonghop').val(), $('#txtSearchProfile').val(), $('#sltTrangthai').val());
                    
                    utility.message("Thông báo",data['error'],null,3000,1);
                    closeLoading();
                }
            }, error: function(data) {

            }
        });
    };

//--------------------------------------------------------------------Danh sách đề nghị đã lập--------------------------------------------------
    function loadReport() {
        $.ajax({
            type: "GET",
            url: '/ho-so/hoc-sinh/loadComboReport',
            success: function(dataget) {
                var dataschool = dataget['SCHOOL'];
                var datareport = dataget.REPORT;

                // console.log(dataget);
                $('#sltCongvan').html("");
                var html_show = "";

                if(dataschool.length > 0){
                    html_show += "<option value=''>--- Chọn số công văn ---</option>";
                    for (var j = 0; j < dataschool.length; j++) {
                        html_show +="<optgroup label='"+dataschool[j].schools_name+"'>";
                            if(datareport.length > 0){
                                for (var i = 0; i < datareport.length; i++) {
                                    if(dataschool[j].schools_id === datareport[i].report_id_truong){
                                            
                                        html_show += "<option value='"+datareport[i].report_name+"'>"+datareport[i].report_name+"</option>";
                                    }                           
                                }
                            }    
                        html_show +="</optgroup>"
                    }
                    $('#sltCongvan').html(html_show);
                }else{
                    $('#sltCongvan').html("<option value=''>-- Chưa có công văn nào --</option>");
                }



                // console.log(dataget);
                // $('#sltCongvan').html("");
                // var html_show = "";

                // if(dataget.length > 0){
                //     html_show += "<option value=''>--- Chọn số công văn ---</option>";
                //     for (var i = dataget.length - 1; i >= 0; i--) {
                //         html_show += "<option value='"+dataget[i].report_name+"'>"+dataget[i].report_name+"</option>";
                //     }
                //     $('#sltCongvan').html(html_show);
                // }else{
                //     $('#sltCongvan').html("<option value=''>-- Chưa có công văn nào --</option>");
                // }
            }, error: function(dataget) {
            }
        });
    };

    function loadReportType(reportName) {
        $.ajax({
            type: "GET",
            url: '/ho-so/hoc-sinh/loadComboReportType/' + reportName,
            success: function(dataget) {
                console.log(dataget);
                $('#sltLoaiChedo').html("");
                var html_show = "";
                if(dataget.length > 0){
                    html_show += "<option value=''>--- Chọn chế độ ---</option>";
                    for (var i = dataget.length - 1; i >= 0; i--) {
                        if (dataget[i].report == "MGHP") {
                            html_show += "<option value='"+dataget[i].report+"'>Hỗ trợ miễn giảm học phí</option>";
                        }
                        else if (dataget[i].report == "CPHT") {
                            html_show += "<option value='"+dataget[i].report+"'>Hỗ trợ chi phí học tập</option>";
                        }
                        else if (dataget[i].report == "HTAT") {
                            html_show += "<option value='"+dataget[i].report+"'>Hỗ trợ ăn trưa trẻ em mẫu giáo</option>";
                        }
                        else if (dataget[i].report == "HTBT") {
                            html_show += "<option value='"+dataget[i].report+"'>Hỗ trợ học sinh bán trú</option>";
                        }
                        else if (dataget[i].report == "HSKT") {
                            html_show += "<option value='"+dataget[i].report+"'>Hỗ trợ học sinh khuyết tật, tàn tật</option>";
                        }
                        else if (dataget[i].report == "HTATHS") {
                            html_show += "<option value='"+dataget[i].report+"'>Hỗ trợ ăn trưa cho học sinh theo NQ57</option>";
                        }
                        else if (dataget[i].report == "HSDTTS") {
                            html_show += "<option value='"+dataget[i].report+"'>Hỗ trợ học sinh dân tộc thiểu số</option>";
                        }
                        else if (dataget[i].report == "HBHSDTNT") {
                            html_show += "<option value='"+dataget[i].report+"'>Hỗ trợ học bổng cho học sinh dân tộc nội trú</option>";
                        }
                        else if (dataget[i].report == "NGNA") {
                            html_show += "<option value='"+dataget[i].report+"'>Hỗ trợ người nấu ăn</option>";
                        }
                    }
                    $('#sltLoaiChedo').html(html_show);
                }else{
                    $('#sltLoaiChedo').html("<option value=''>-- Chưa có chế độ --</option>");
                }

                // if(callback != null){
                //     callback();
                // }
            }, error: function(dataget) {
            }
        });
    };


    function loadReportBySchool(school_id, callback) {
        $.ajax({
            type: "GET",
            url: '/ho-so/hoc-sinh/loadComboReportBySchool/' + school_id,
            success: function(dataget) {
                console.log(dataget);
                $('#sltCongvan').html("");
                var html_show = "";

                if(dataget.length > 0){
                    html_show += "<option value=''>--- Chọn số công văn ---</option>";
                    for (var i = dataget.length - 1; i >= 0; i--) {
                        html_show += "<option value='"+dataget[i].report_name+"'>"+dataget[i].report_name+"</option>";
                    }
                    $('#sltCongvan').html(html_show);
                }else{
                    $('#sltCongvan').html("<option value=''>-- Chưa có công văn nào --</option>");
                }

                if (callback != null) { callback(dataget); }
            }, error: function(dataget) {
            }
        });
    };
    
    function loaddataDanhSachGroupA(row, keySearch = "", group = "") {

        // var msg_warning = "";

        // msg_warning = validateDanhsachdenghi();

        // // alert(msg_warning);

        // if (msg_warning !== null && msg_warning !== "") {
        //     utility.messagehide("messageValidate", msg_warning, 1, 5000);
        //     return;
        // }

        var reportName = $('#sltCongvan').val();
        var reportType = $('#sltLoaiChedo').val();

        
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit: row,
            REPORTNAME: reportName,
            REPORTTYPE: reportType,
            KEY: keySearch,
            GROUP: group
        };
        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/loadDataGroupA',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(datas) {

                SETUP_PAGING_NGHILC(datas, function () {
                    loaddataDanhSachGroupA(row, keySearch, group);
                });

                // $('#divSearch').html("");
                $('#headerTable').html("");
                var html_search = "";
                var html_header = '';
                var html_show = "";
                var html_paging = "";
                $('#dataLapDanhsachHS').html("");
                // $('#divPaging').html("");
                var dataget = datas.data;
                console.log(dataget);
                if(dataget.length > 0){

                    html_search += '<input id="txtSearchProfile" type="text" class="form-control input-sm" placeholder="Tìm kiếm ">';
                    html_search += '<span class="glyphicon glyphicon-search form-control-feedback"></span>';

                    if (reportType == "MGHP" || reportType == "CPHT" || reportType == "HTAT") {
                        html_header += '<th class="text-center" style="vertical-align:middle">STT</th>';
                        // html_header += '<th class="text-center" style="vertical-align:middle">Số công văn</th>';
                        // html_header += '<th class="text-center" style="vertical-align:middle">Tên chế độ</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhóm học sinh</th>';
                        // html_header += '<th class="text-center" style="vertical-align:middle">';
                        // html_header += '<select name="sltGroupHS" id="sltGroupHS">';
                        // html_header += '<option value="">-Tìm kiếm theo nhóm-</option>';
                        // html_header += '<option value="GROUPA">Đang có mặt tại trường</option>';
                        // html_header += '<option value="GROUPB">Chuẩn bị nhập học</option>';
                        // html_header += '<option value="GROUPC">Dự kiến tuyển mới</option>';
                        // html_header += '</select></th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Tên học sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Ngày sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dân tộc</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Cha mẹ</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Thôn/ xóm</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Xã/ phường</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Huyện/ quận</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Trường</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Lớp học</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Ngày nhập học</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhu cầu</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dự toán</th>';

                        
                        for (var i = 0; i < dataget.length; i++) {
                                            
                            html_show += "<tr><td class='text-center' style='vertical-align:middle'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            // html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(reportName)+"</td>";
                            // if (reportType == "MGHP") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ miễn giảm học phí</td>";
                            // }
                            // else if (reportType == "CPHT") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ chi phí học tập</td>";
                            // }
                            // else if (reportType == "HTAT") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ ăn trưa trẻ em mẫu giáo</td>";
                            // }
                            // else if (reportType == "HTBT") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ học sinh bán trú</td>";
                            // }
                            // else if (reportType == "HSKT") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ học sinh khuyết tật</td>";
                            // }
                            // else if (reportType == "HTATHS") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ ăn trưa cho học sinh theo NQ57</td>";
                            // }
                            // else if (reportType == "HSDTTS") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ học sinh dân tộc thiểu số</td>";
                            // }
                            // else if (reportType == "HBHSDTNT") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ học bổng học sinh dân tộc nội trú</td>";
                            // }

                            if (parseInt(dataget[i].type) === 1) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh đang học tại trường</td>";
                            }
                            else if (parseInt(dataget[i].type) === 2) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh chuẩn bị nhập học</td>";
                            }
                            else if (parseInt(dataget[i].type) === 3) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh dự kiến tuyển mới</td>";
                            }
                            html_show += "<td><a href='javascript:;' onclick='getProfileSubById("+dataget[i].profile_id+", 3);'>"+dataget[i].profile_name+"</a></td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatDates(dataget[i].profile_birthday)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].nationals_name)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].profile_parentname)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].profile_household)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].tenxa)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].tenhuyen)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].schools_name)+"</td>";
                            if (parseInt(dataget[i].type) === 1 || parseInt(dataget[i].type) === 2) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].old_level_cur)+"</td>";
                            }
                            else if (parseInt(dataget[i].type) === 3) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].new_level_cur)+"</td>";
                            }
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatDates(dataget[i].profile_year)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatNumber_2(dataget[i].nhu_cau)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatNumber_2(dataget[i].du_toan)+"</td>";
                            // html_show += "<td>"+ConvertString(dataget[i].GHICHU)+"</td>";
                            // if(parseInt(dataget[i].TRANGTHAI) == 0){
                            //     html_show += "<td class='text-center' style='vertical-align:middle'><button class='btn btn-primary btn-xs' onclick='openPopupDuyetChedo("+dataget[i].profile_id+", "+dataget[i].qlhs_thcd_id+", "+number+")'> Chờ duyệt</button></td>";
                            // }else if(parseInt(dataget[i].TRANGTHAI) == 1){
                            //     html_show += "<td class='text-center' style='vertical-align:middle'><button class='btn btn-success btn-xs' onclick='openPopupDuyetChedo("+dataget[i].profile_id+", "+dataget[i].qlhs_thcd_id+", "+number+")'> Đã duyệt </button></td>";
                            // }
                            // if(check_Permission_Feature("2")){
                            //     html_show += "<td class='text-center' style='vertical-align:middle'><button data='"+dataget[i].profile_id+"' onclick='getHoSoHocSinh("+dataget[i].profile_id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Sửa </button></td>";
                            // }
                            // if(check_Permission_Feature("3")){
                            //     html_show += "<td class='text-center' style='vertical-align:middle'><button  onclick='delHoSoHocSinh("+dataget[i].profile_id+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button></td>";
                            // }
                                
                            html_show += "</tr>";
                        } 
                    }
                    if (reportType == "HTBT") {
                        html_header += '<th class="text-center" style="vertical-align:middle">STT</th>';
                        // html_header += '<th class="text-center" style="vertical-align:middle">Số công văn</th>';
                        // html_header += '<th class="text-center" style="vertical-align:middle">Tên chế độ</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhóm học sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Tên học sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Ngày sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dân tộc</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Cha mẹ</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Thôn/ xóm</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Xã/ phường</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Huyện/ quận</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Trường</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Lớp học</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Ngày nhập học</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhu cầu tiền ăn</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhu cầu tiền ở</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhu cầu VHTT</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dự toán tiền ăn</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dự toán tiền ở</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dự toán VHTT</th>';

                        for (var i = 0; i < dataget.length; i++) {
                            // console.log(dataget[i].type);
                            html_show += "<tr><td class='text-center' style='vertical-align:middle'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            // html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(reportName)+"</td>";
                            // if (reportType == "MGHP") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ miễn giảm học phí</td>";
                            // }
                            // else if (reportType == "CPHT") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ chi phí học tập</td>";
                            // }
                            // else if (reportType == "HTAT") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ ăn trưa trẻ em mẫu giáo</td>";
                            // }
                            // else if (reportType == "HTBT") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ học sinh bán trú</td>";
                            // }
                            // else if (reportType == "HSKT") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ học sinh khuyết tật</td>";
                            // }
                            // else if (reportType == "HTATHS") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ ăn trưa cho học sinh theo NQ57</td>";
                            // }
                            // else if (reportType == "HSDTTS") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ học sinh dân tộc thiểu số</td>";
                            // }
                            // else if (reportType == "HBHSDTNT") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ học bổng học sinh dân tộc nội trú</td>";
                            // }

                            if (parseInt(dataget[i].type) === 1) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh đang học tại trường</td>";
                            }
                            else if (parseInt(dataget[i].type) === 2) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh chuẩn bị nhập học</td>";
                            }
                            else if (parseInt(dataget[i].type) === 3) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh dự kiến tuyển mới</td>";
                            }
                            html_show += "<td><a href='javascript:;' onclick='getProfileSubById("+dataget[i].profile_id+", 3);'>"+dataget[i].profile_name+"</a></td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatDates(dataget[i].profile_birthday)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].nationals_name)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].profile_parentname)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].profile_household)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].tenxa)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].tenhuyen)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].schools_name)+"</td>";
                            if (parseInt(dataget[i].type) === 1 || parseInt(dataget[i].type) === 2) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].old_level_cur)+"</td>";
                            }
                            else if (parseInt(dataget[i].type) === 3) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].new_level_cur)+"</td>";
                            }
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatDates(dataget[i].profile_year)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatNumber_2(dataget[i].nhucau_hotrotienan)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatNumber_2(dataget[i].nhucau_hotrotieno)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatNumber_2(dataget[i].nhucau_VHTT)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatNumber_2(dataget[i].dutoan_hotrotienan)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatNumber_2(dataget[i].dutoan_hotrotieno)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatNumber_2(dataget[i].dutoan_VHTT)+"</td>";
                            // html_show += "<td>"+ConvertString(dataget[i].GHICHU)+"</td>";
                            // if(parseInt(dataget[i].TRANGTHAI) == 0){
                            //     html_show += "<td class='text-center' style='vertical-align:middle'><button class='btn btn-primary btn-xs' onclick='openPopupDuyetChedo("+dataget[i].profile_id+", "+dataget[i].qlhs_thcd_id+", "+number+")'> Chờ duyệt</button></td>";
                            // }else if(parseInt(dataget[i].TRANGTHAI) == 1){
                            //     html_show += "<td class='text-center' style='vertical-align:middle'><button class='btn btn-success btn-xs' onclick='openPopupDuyetChedo("+dataget[i].profile_id+", "+dataget[i].qlhs_thcd_id+", "+number+")'> Đã duyệt </button></td>";
                            // }
                            // if(check_Permission_Feature("2")){
                            //     html_show += "<td class='text-center' style='vertical-align:middle'><button data='"+dataget[i].profile_id+"' onclick='getHoSoHocSinh("+dataget[i].profile_id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Sửa </button></td>";
                            // }
                            // if(check_Permission_Feature("3")){
                            //     html_show += "<td class='text-center' style='vertical-align:middle'><button  onclick='delHoSoHocSinh("+dataget[i].profile_id+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button></td>";
                            // }
                                
                            html_show += "</tr>";
                        }
                    }
                    if (reportType == "HSKT") {
                        html_header += '<th class="text-center" style="vertical-align:middle">STT</th>';
                        // html_header += '<th class="text-center" style="vertical-align:middle">Số công văn</th>';
                        // html_header += '<th class="text-center" style="vertical-align:middle">Tên chế độ</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhóm học sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Tên học sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Ngày sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dân tộc</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Cha mẹ</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Thôn/ xóm</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Xã/ phường</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Huyện/ quận</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Trường</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Lớp học</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Ngày nhập học</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhu cầu học bổng</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhu cầu mua đồ dùng</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dự toán học bổng</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dự toán mua đồ dùng</th>';

                        for (var i = 0; i < dataget.length; i++) {
                                            
                            html_show += "<tr><td class='text-center' style='vertical-align:middle'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            // html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(reportName)+"</td>";
                            // if (reportType == "MGHP") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ miễn giảm học phí</td>";
                            // }
                            // else if (reportType == "CPHT") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ chi phí học tập</td>";
                            // }
                            // else if (reportType == "HTAT") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ ăn trưa trẻ em mẫu giáo</td>";
                            // }
                            // else if (reportType == "HTBT") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ học sinh bán trú</td>";
                            // }
                            // else if (reportType == "HSKT") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ học sinh khuyết tật</td>";
                            // }
                            // else if (reportType == "HTATHS") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ ăn trưa cho học sinh theo NQ57</td>";
                            // }
                            // else if (reportType == "HSDTTS") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ học sinh dân tộc thiểu số</td>";
                            // }
                            // else if (reportType == "HBHSDTNT") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ học bổng học sinh dân tộc nội trú</td>";
                            // }

                            if (parseInt(dataget[i].type) === 1) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh đang học tại trường</td>";
                            }
                            else if (parseInt(dataget[i].type) === 2) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh chuẩn bị nhập học</td>";
                            }
                            else if (parseInt(dataget[i].type) === 3) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh dự kiến tuyển mới</td>";
                            }
                            html_show += "<td><a href='javascript:;' onclick='getProfileSubById("+dataget[i].profile_id+", 3);'>"+dataget[i].profile_name+"</a></td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatDates(dataget[i].profile_birthday)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].nationals_name)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].profile_parentname)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].profile_household)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].tenxa)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].tenhuyen)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].schools_name)+"</td>";
                            if (parseInt(dataget[i].type) === 1 || parseInt(dataget[i].type) === 2) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].old_level_cur)+"</td>";
                            }
                            else if (parseInt(dataget[i].type) === 3) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].new_level_cur)+"</td>";
                            }
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatDates(dataget[i].profile_year)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatNumber_2(dataget[i].nhucau_hocbong)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatNumber_2(dataget[i].nhucau_muadodung)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatNumber_2(dataget[i].dutoan_hocbong)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatNumber_2(dataget[i].dutoan_muadodung)+"</td>";
                            // html_show += "<td>"+ConvertString(dataget[i].GHICHU)+"</td>";
                            // if(parseInt(dataget[i].TRANGTHAI) == 0){
                            //     html_show += "<td class='text-center' style='vertical-align:middle'><button class='btn btn-primary btn-xs' onclick='openPopupDuyetChedo("+dataget[i].profile_id+", "+dataget[i].qlhs_thcd_id+", "+number+")'> Chờ duyệt</button></td>";
                            // }else if(parseInt(dataget[i].TRANGTHAI) == 1){
                            //     html_show += "<td class='text-center' style='vertical-align:middle'><button class='btn btn-success btn-xs' onclick='openPopupDuyetChedo("+dataget[i].profile_id+", "+dataget[i].qlhs_thcd_id+", "+number+")'> Đã duyệt </button></td>";
                            // }
                            // if(check_Permission_Feature("2")){
                            //     html_show += "<td class='text-center' style='vertical-align:middle'><button data='"+dataget[i].profile_id+"' onclick='getHoSoHocSinh("+dataget[i].profile_id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Sửa </button></td>";
                            // }
                            // if(check_Permission_Feature("3")){
                            //     html_show += "<td class='text-center' style='vertical-align:middle'><button  onclick='delHoSoHocSinh("+dataget[i].profile_id+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button></td>";
                            // }
                                
                            html_show += "</tr>";
                        }
                    }
                    if (reportType == "HTATHS" || reportType == "HSDTTS" || reportType == "HBHSDTNT") {
                        html_header += '<th class="text-center" style="vertical-align:middle">STT</th>';
                        // html_header += '<th class="text-center" style="vertical-align:middle">Số công văn</th>';
                        // html_header += '<th class="text-center" style="vertical-align:middle">Tên chế độ</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhóm học sinh</th>';
                        // html_header += '<th class="text-center" style="vertical-align:middle">';
                        // html_header += '<select name="sltGroupHS" id="sltGroupHS">';
                        // html_header += '<option value="">-Tìm kiếm theo nhóm-</option>';
                        // html_header += '<option value="GROUPA">Đang có mặt tại trường</option>';
                        // html_header += '<option value="GROUPB">Chuẩn bị nhập học</option>';
                        // html_header += '<option value="GROUPC">Dự kiến tuyển mới</option>';
                        // html_header += '</select></th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Tên học sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Ngày sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dân tộc</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Cha mẹ</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Thôn/ xóm</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Xã/ phường</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Huyện/ quận</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Trường</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Lớp học</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Ngày nhập học</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhu cầu</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dự toán</th>';

                        
                        for (var i = 0; i < dataget.length; i++) {
                                            
                            html_show += "<tr><td class='text-center' style='vertical-align:middle'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            // html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(reportName)+"</td>";
                            // if (reportType == "MGHP") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ miễn giảm học phí</td>";
                            // }
                            // else if (reportType == "CPHT") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ chi phí học tập</td>";
                            // }
                            // else if (reportType == "HTAT") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ ăn trưa trẻ em mẫu giáo</td>";
                            // }
                            // else if (reportType == "HTBT") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ học sinh bán trú</td>";
                            // }
                            // else if (reportType == "HSKT") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ học sinh khuyết tật</td>";
                            // }
                            // else if (reportType == "HTATHS") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ ăn trưa cho học sinh theo NQ57</td>";
                            // }
                            // else if (reportType == "HSDTTS") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ học sinh dân tộc thiểu số</td>";
                            // }
                            // else if (reportType == "HBHSDTNT") {
                            //     html_show += "<td class='text-left' style='vertical-align:middle'>Hỗ trợ học bổng học sinh dân tộc nội trú</td>";
                            // }

                            if (parseInt(dataget[i].type) === 1) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh đang học tại trường</td>";
                            }
                            else if (parseInt(dataget[i].type) === 2) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh chuẩn bị nhập học</td>";
                            }
                            else if (parseInt(dataget[i].type) === 3) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh dự kiến tuyển mới</td>";
                            }
                            html_show += "<td><a href='javascript:;' onclick='getProfileSubById("+dataget[i].profile_id+", 3);'>"+dataget[i].profile_name+"</a></td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatDates(dataget[i].profile_birthday)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].nationals_name)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].profile_parentname)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].profile_household)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].tenxa)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].tenhuyen)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].schools_name)+"</td>";
                            if (parseInt(dataget[i].type) === 1 || parseInt(dataget[i].type) === 2) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].old_level_cur)+"</td>";
                            }
                            else if (parseInt(dataget[i].type) === 3) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].new_level_cur)+"</td>";
                            }
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatDates(dataget[i].profile_year)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatNumber_2(dataget[i].nhucau)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatNumber_2(dataget[i].dutoan)+"</td>";
                            // html_show += "<td>"+ConvertString(dataget[i].GHICHU)+"</td>";
                            // if(parseInt(dataget[i].TRANGTHAI) == 0){
                            //     html_show += "<td class='text-center' style='vertical-align:middle'><button class='btn btn-primary btn-xs' onclick='openPopupDuyetChedo("+dataget[i].profile_id+", "+dataget[i].qlhs_thcd_id+", "+number+")'> Chờ duyệt</button></td>";
                            // }else if(parseInt(dataget[i].TRANGTHAI) == 1){
                            //     html_show += "<td class='text-center' style='vertical-align:middle'><button class='btn btn-success btn-xs' onclick='openPopupDuyetChedo("+dataget[i].profile_id+", "+dataget[i].qlhs_thcd_id+", "+number+")'> Đã duyệt </button></td>";
                            // }
                            // if(check_Permission_Feature("2")){
                            //     html_show += "<td class='text-center' style='vertical-align:middle'><button data='"+dataget[i].profile_id+"' onclick='getHoSoHocSinh("+dataget[i].profile_id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Sửa </button></td>";
                            // }
                            // if(check_Permission_Feature("3")){
                            //     html_show += "<td class='text-center' style='vertical-align:middle'><button  onclick='delHoSoHocSinh("+dataget[i].profile_id+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button></td>";
                            // }
                                
                            html_show += "</tr>";
                        } 
                    }
                    if (reportType == "NGNA") {
                        html_header += '<th class="text-center" style="vertical-align:middle">STT</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhóm học sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Tên học sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Ngày sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dân tộc</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Cha mẹ</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Hộ khẩu</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Trường</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Lớp học</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Ngày nhập học</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhu cầu</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dự toán</th>';
                        html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                    }

                    html_paging += '<div class="row">';
                    html_paging += '<div class="col-md-2">';
                    html_paging += '<label class="text-right col-md-9 control-label">Tổng </label>';
                    html_paging += '<label class="col-md-3 control-label g_countRowsPaging">0</label>';
                    html_paging += '</div>';
                    html_paging += '<div class="col-md-3">';
                    html_paging += '<label class="col-md-6 control-label text-right">Trang </label>';
                    html_paging += '<div class="col-md-6">';
                    html_paging += '<select class="form-control input-sm g_selectPaging">';
                    html_paging += '<option value="0">0 / 20 </option>';
                    html_paging += '</select>';
                    html_paging += '</div>';
                    html_paging += '</div>';
                    html_paging += '<div class="col-md-3">';
                    html_paging += '<label  class="col-md-6 control-label">Hiển thị: </label>';
                    html_paging += '<div class="col-md-6">';
                    html_paging += '<select name="drPagingDanhsach" id="drPagingDanhsach"  class="form-control input-sm pagination-show-row">';
                    html_paging += '<option value="10">10</option>';
                    html_paging += '<option value="15">15</option>';
                    html_paging += '<option value="20">20</option>';
                    html_paging += '</select>';
                    html_paging += '</div>';
                    html_paging += '</div>';
                    html_paging += '<div class="col-md-4">';
                    html_paging += '<label  class="col-md-2 control-label"></label>';
                    html_paging += '<div class="col-md-10">';
                    html_paging += '<ul class="pagination pagination-sm no-margin pull-right g_clickedPaging">';
                    html_paging += '<li><a>&laquo;</a></li>';
                    html_paging += '<li><a>0</a></li>';
                    html_paging += '<li><a>&raquo;</a></li>';
                    html_paging += '</ul>';
                    html_paging += '</div>';
                    html_paging += '</div>';
                    html_paging += '</div>';
                }
                else {
                    html_header += '<th class="text-center" style="vertical-align:middle">STT</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Nhóm học sinh</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Tên học sinh</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Ngày sinh</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Dân tộc</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Cha mẹ</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Hộ khẩu</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Trường</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Lớp học</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Ngày nhập học</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Nhu cầu</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Dự toán</th>';
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }

                if (reportName === null || reportName === "" || reportType === null || reportType === "") {
                    html_header += '<th class="text-center" style="vertical-align:middle">STT</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Nhóm học sinh</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Tên học sinh</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Ngày sinh</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Dân tộc</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Cha mẹ</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Hộ khẩu</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Trường</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Lớp học</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Ngày nhập học</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Nhu cầu</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Dự toán</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle"><button class="btn btn-info btn-xs">Chọn tất cả</button></th>';
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }

                // $('#divSearch').html(html_search);
                $('#headerTable').html(html_header);
                $('#dataLapDanhsachHS').html(html_show);
                // $('#divPaging').html(html_paging);
            }, error: function(dataget) {

            }
        });
    };

    // $("#PhongChooseAll").click(function(){
    //     $('input#Phongchoose').not(this).prop('checked', this.checked);
    //     alert("Choose1");
    // });

    // $('#PhongChooseAll').change(function() {
    //     alert("Choose2");
    //         if ($('#PhongChooseAll').prop('checked'))
    //             $('[id*="Phongchoose"]').prop('checked', true);
    //         else
    //             $('[id*="Phongchoose"]').prop('checked', false);
    // });

    function clickChooseAll(){
        // $('input#PhongChooseAll').change(function() {
        //         if ($('input#PhongChooseAll').prop('checked'))
        //             $('[id*="Phongchoose"]').prop('checked', true);
        //         else
        //             $('[id*="Phongchoose"]').prop('checked', false);
        // });
        $('input#Phongchoose').each(function () {
            
            if (this.checked) {
                $(this).prop('checked', false);
            }
            else{
                $(this).prop('checked', true);
            }
        });
    };

    function loaddataDanhSachGroupB(row, keySearch = "", group = "") {

        var msg_warning = "";

        msg_warning = validateDanhsachdenghi();

        // alert(msg_warning);

        if (msg_warning !== null && msg_warning !== "") {
            utility.messagehide("messageValidate", msg_warning, 1, 5000);
            return;
        }

        var reportName = $('#sltCongvan').val();
        var reportType = $('#sltLoaiChedo').val();

        
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit: row,
            REPORTNAME: reportName,
            REPORTTYPE: reportType,
            KEY: keySearch,
            GROUP: group
        };
        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/loadDataGroupA',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(datas) {

                SETUP_PAGING_NGHILC(datas, function () {
                    loaddataDanhSachGroupB(row, keySearch, group);
                });

                // $('#divSearch').html("");
                $('#headerTable').html("");
                var html_search = "";
                var html_header = '';
                var html_show = "";
                var html_paging = "";
                $('#dataLapDanhsachHS').html("");
                // $('#divPaging').html("");
                var dataget = datas.data;
                console.log(dataget);
                if(dataget.length > 0){

                    html_search += '<input id="txtSearchProfile" type="text" class="form-control input-sm" placeholder="Tìm kiếm ">';
                    html_search += '<span class="glyphicon glyphicon-search form-control-feedback"></span>';

                    if (reportType == "MGHP" || reportType == "CPHT" || reportType == "HTAT") {
                        html_header += '<th class="text-center" style="vertical-align:middle">STT</th>';
                        // html_header += '<th class="text-center" style="vertical-align:middle"><input type="checkbox"  onclick="clickChooseAll()" /></th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 12%;">Nhóm học sinh</th>';
                        
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 8%;">Tên học sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 7%;">Ngày sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dân tộc</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 8%;">Cha mẹ</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Thôn/ xóm</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Xã/ phường</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Huyện/ quận</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 12%;">Trường</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 5%;">Lớp học</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 7%;">Ngày nhập học</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhu cầu</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dự toán</th>';
                        // html_header += '<th class="text-center" style="vertical-align:middle"><button class="btn btn-info btn-xs" onclick="clickB()">Chọn tất cả</button></th>';

                        
                        for (var i = 0; i < dataget.length; i++) {
                                            
                            html_show += "<tr><td class='text-center' style='vertical-align:middle'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            // html_show += "<td class='text-center' style='vertical-align:middle'><input type='checkbox' id='Phongchoose' value='"+dataget[i].profile_id+"' data-choose='"+dataget[i].profile_id+"' /></td>";

                            if (parseInt(dataget[i].type) === 1) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh đang học tại trường</td>";
                            }
                            else if (parseInt(dataget[i].type) === 2) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh chuẩn bị nhập học</td>";
                            }
                            else if (parseInt(dataget[i].type) === 3) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh dự kiến tuyển mới</td>";
                            }
                            html_show += "<td><a href='javascript:;' onclick='getProfileSubById("+dataget[i].profile_id+", 3);'>"+dataget[i].profile_name+"</a></td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatDates(dataget[i].profile_birthday)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].nationals_name)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].profile_parentname)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].profile_household)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].tenxa)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].tenhuyen)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].schools_name)+"</td>";
                            if (parseInt(dataget[i].type) === 1 || parseInt(dataget[i].type) === 2) {
                                if (dataget[i].old_level_cur !== null && dataget[i].old_level_cur !== "") {
                                    html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].old_level_cur)+"</td>";
                                }
                                else {
                                    html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].old_level_old)+"</td>";
                                }
                            }
                            else if (parseInt(dataget[i].type) === 3) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].new_level_cur)+"</td>";
                            }
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatDates(dataget[i].profile_year)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].nhu_cau)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].du_toan)+"</td>";
                                
                            html_show += "</tr>";
                        } 
                    }
                    if (reportType == "HTBT") {
                        html_header += '<th class="text-center" style="vertical-align:middle">STT</th>';
                        // html_header += '<th class="text-center" style="vertical-align:middle"><input type="checkbox"  onclick="clickChooseAll()" /></th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 10%;">Nhóm học sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 8%;">Tên học sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 7%;">Ngày sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dân tộc</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 8%;">Cha mẹ</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Thôn/ xóm</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Xã/ phường</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Huyện/ quận</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 10%;">Trường</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 5%;">Lớp học</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 7%;">Ngày nhập học</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhu cầu tiền ăn</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhu cầu tiền ở</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhu cầu VHTT</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dự toán tiền ăn</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dự toán tiền ở</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dự toán VHTT</th>';
                        // html_header += '<th class="text-center" style="vertical-align:middle"><button class="btn btn-info btn-xs">Chọn tất cả</button></th>';

                        for (var i = 0; i < dataget.length; i++) {
                            // console.log(dataget[i].type);
                            html_show += "<tr><td class='text-center' style='vertical-align:middle'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            // html_show += "<td class='text-center' style='vertical-align:middle'><input type='checkbox' id='Phongchoose' value='"+dataget[i].profile_id+"' data-choose='"+dataget[i].profile_id+"' /></td>";

                            if (parseInt(dataget[i].type) === 1) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh đang học tại trường</td>";
                            }
                            else if (parseInt(dataget[i].type) === 2) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh chuẩn bị nhập học</td>";
                            }
                            else if (parseInt(dataget[i].type) === 3) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh dự kiến tuyển mới</td>";
                            }
                            html_show += "<td><a href='javascript:;' onclick='getProfileSubById("+dataget[i].profile_id+", 3);'>"+dataget[i].profile_name+"</a></td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatDates(dataget[i].profile_birthday)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].nationals_name)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].profile_parentname)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].profile_household)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].tenxa)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].tenhuyen)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].schools_name)+"</td>";
                            if (parseInt(dataget[i].type) === 1 || parseInt(dataget[i].type) === 2) {
                                if (dataget[i].old_level_cur !== null && dataget[i].old_level_cur !== "") {
                                    html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].old_level_cur)+"</td>";
                                }
                                else {
                                    html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].old_level_old)+"</td>";
                                }
                            }
                            else if (parseInt(dataget[i].type) === 3) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].new_level_cur)+"</td>";
                            }
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatDates(dataget[i].profile_year)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].nhucau_hotrotienan)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].nhucau_hotrotieno)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].nhucau_VHTT)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].dutoan_hotrotienan)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].dutoan_hotrotieno)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].dutoan_VHTT)+"</td>";
                                
                            html_show += "</tr>";
                        }
                    }
                    if (reportType == "HSKT") {
                        html_header += '<th class="text-center" style="vertical-align:middle">STT</th>';
                        // html_header += '<th class="text-center" style="vertical-align:middle"><input type="checkbox"  onclick="clickChooseAll()" /></th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 10%;">Nhóm học sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 8%;">Tên học sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 7%;">Ngày sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dân tộc</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 8%;">Cha mẹ</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Thôn/ xóm</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Xã/ phường</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Huyện/ quận</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 10%;">Trường</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 5%;">Lớp học</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 7%;">Ngày nhập học</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhu cầu học bổng</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhu cầu mua đồ dùng</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dự toán học bổng</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dự toán mua đồ dùng</th>';
                        // html_header += '<th class="text-center" style="vertical-align:middle"><button class="btn btn-info btn-xs">Chọn tất cả</button></th>';

                        for (var i = 0; i < dataget.length; i++) {
                                            
                            html_show += "<tr><td class='text-center' style='vertical-align:middle'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            // html_show += "<td class='text-center' style='vertical-align:middle'><input type='checkbox' id='Phongchoose' value='"+dataget[i].profile_id+"' data-choose='"+dataget[i].profile_id+"' /></td>";

                            if (parseInt(dataget[i].type) === 1) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh đang học tại trường</td>";
                            }
                            else if (parseInt(dataget[i].type) === 2) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh chuẩn bị nhập học</td>";
                            }
                            else if (parseInt(dataget[i].type) === 3) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh dự kiến tuyển mới</td>";
                            }
                            html_show += "<td><a href='javascript:;' onclick='getProfileSubById("+dataget[i].profile_id+", 3);'>"+dataget[i].profile_name+"</a></td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatDates(dataget[i].profile_birthday)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].nationals_name)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].profile_parentname)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].profile_household)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].tenxa)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].tenhuyen)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].schools_name)+"</td>";
                            if (parseInt(dataget[i].type) === 1 || parseInt(dataget[i].type) === 2) {
                                if (dataget[i].old_level_cur !== null && dataget[i].old_level_cur !== "") {
                                    html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].old_level_cur)+"</td>";
                                }
                                else {
                                    html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].old_level_old)+"</td>";
                                }
                            }
                            else if (parseInt(dataget[i].type) === 3) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].new_level_cur)+"</td>";
                            }
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatDates(dataget[i].profile_year)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].nhucau_hocbong)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].nhucau_muadodung)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].dutoan_hocbong)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].dutoan_muadodung)+"</td>";
                                
                            html_show += "</tr>";
                        }
                    }
                    if (reportType == "HTATHS" || reportType == "HSDTTS" || reportType == "HBHSDTNT") {
                        html_header += '<th class="text-center" style="vertical-align:middle">STT</th>';
                        // html_header += '<th class="text-center" style="vertical-align:middle"><input type="checkbox"  onclick="clickChooseAll()" /></th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 12%;">Nhóm học sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 8%;">Tên học sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 7%;">Ngày sinh</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dân tộc</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 8%;">Cha mẹ</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Thôn/ xóm</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Xã/ phường</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Huyện/ quận</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 12%;">Trường</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 5%;">Lớp học</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 7%;">Ngày nhập học</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhu cầu</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dự toán</th>';
                        // html_header += '<th class="text-center" style="vertical-align:middle"><button class="btn btn-info btn-xs">Chọn tất cả</button></th>';

                        
                        for (var i = 0; i < dataget.length; i++) {
                                            
                            html_show += "<tr><td class='text-center' style='vertical-align:middle'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            // html_show += "<td class='text-center' style='vertical-align:middle'><input type='checkbox' id='Phongchoose' value='"+dataget[i].profile_id+"' data-choose='"+dataget[i].profile_id+"' /></td>";
                            if (parseInt(dataget[i].type) === 1) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh đang học tại trường</td>";
                            }
                            else if (parseInt(dataget[i].type) === 2) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh chuẩn bị nhập học</td>";
                            }
                            else if (parseInt(dataget[i].type) === 3) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>Học sinh dự kiến tuyển mới</td>";
                            }
                            html_show += "<td><a href='javascript:;' onclick='getProfileSubById("+dataget[i].profile_id+", 3);'>"+dataget[i].profile_name+"</a></td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatDates(dataget[i].profile_birthday)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].nationals_name)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].profile_parentname)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].profile_household)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].tenxa)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].tenhuyen)+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].schools_name)+"</td>";
                            if (parseInt(dataget[i].type) === 1 || parseInt(dataget[i].type) === 2) {
                                if (dataget[i].old_level_cur !== null && dataget[i].old_level_cur !== "") {
                                    html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].old_level_cur)+"</td>";
                                }
                                else {
                                    html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].old_level_old)+"</td>";
                                }
                            }
                            else if (parseInt(dataget[i].type) === 3) {
                                html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].new_level_cur)+"</td>";
                            }
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatDates(dataget[i].profile_year)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].nhucau)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].dutoan)+"</td>";
                                
                            html_show += "</tr>";
                        } 
                    }
                    if (reportType == "NGNA") {
                        var year_NGNA = datas.year;
                        html_header += '<th class="text-center" style="vertical-align:middle">STT</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 15%;">Tên trường</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 10%;">Số hs học kỳ 2 năm '+(parseInt(year_NGNA) - 1)+'-'+year_NGNA+'</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 10%;">Số hs học kỳ 1 năm '+year_NGNA+'-'+(parseInt(year_NGNA) + 1)+'</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 10%;">Số hs học kỳ 2 năm '+year_NGNA+'-'+(parseInt(year_NGNA) + 1)+'</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 10%;">Số hs học kỳ 1 năm '+(parseInt(year_NGNA) + 1)+'-'+(parseInt(year_NGNA) + 2)+'</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 10%;">Số người nấu ăn học kỳ 2 năm '+(parseInt(year_NGNA) - 1)+'-'+year_NGNA+'</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 10%;">Số người nấu ăn học kỳ 1 năm '+year_NGNA+'-'+(parseInt(year_NGNA) + 1)+'</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 10%;">Số người nấu ăn học kỳ 2 năm '+year_NGNA+'-'+(parseInt(year_NGNA) + 1)+'</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle; width: 10%;">Số người nấu ăn học kỳ 1 năm '+(parseInt(year_NGNA) + 1)+'-'+(parseInt(year_NGNA) + 2)+'</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Nhu cầu</th>';
                        html_header += '<th class="text-center" style="vertical-align:middle">Dự toán</th>';
                        
                        for (var i = 0; i < dataget.length; i++) {
                            html_show += "<tr><td class='text-center' style='vertical-align:middle'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            html_show += "<td class='text-left' style='vertical-align:middle'>"+ConvertString(dataget[i].schools_name)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+(dataget[i].sohocsinhhocky2_old)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+(dataget[i].sohocsinhhocky1_cur)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+(dataget[i].sohocsinhhocky2_cur)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+(dataget[i].sohocsinhhocky1_new)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+(dataget[i].nguoinauanhocky2_old)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+(dataget[i].nguoinauanhocky1_cur)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+(dataget[i].nguoinauanhocky2_cur)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+(dataget[i].nguoinauanhocky1_new)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].nhucau)+"</td>";
                            html_show += "<td class='text-right' style='vertical-align:middle'>"+formatter(dataget[i].dutoan)+"</td>";
                        }
                    }

                    html_paging += '<div class="row">';
                    html_paging += '<div class="col-md-2">';
                    html_paging += '<label class="text-right col-md-9 control-label">Tổng </label>';
                    html_paging += '<label class="col-md-3 control-label g_countRowsPaging">0</label>';
                    html_paging += '</div>';
                    html_paging += '<div class="col-md-3">';
                    html_paging += '<label class="col-md-6 control-label text-right">Trang </label>';
                    html_paging += '<div class="col-md-6">';
                    html_paging += '<select class="form-control input-sm g_selectPaging">';
                    html_paging += '<option value="0">0 / 20 </option>';
                    html_paging += '</select>';
                    html_paging += '</div>';
                    html_paging += '</div>';
                    html_paging += '<div class="col-md-3">';
                    html_paging += '<label  class="col-md-6 control-label">Hiển thị: </label>';
                    html_paging += '<div class="col-md-6">';
                    html_paging += '<select name="drPagingDanhsach" id="drPagingDanhsach"  class="form-control input-sm pagination-show-row">';
                    html_paging += '<option value="10">10</option>';
                    html_paging += '<option value="15">15</option>';
                    html_paging += '<option value="20">20</option>';
                    html_paging += '</select>';
                    html_paging += '</div>';
                    html_paging += '</div>';
                    html_paging += '<div class="col-md-4">';
                    html_paging += '<label  class="col-md-2 control-label"></label>';
                    html_paging += '<div class="col-md-10">';
                    html_paging += '<ul class="pagination pagination-sm no-margin pull-right g_clickedPaging">';
                    html_paging += '<li><a>&laquo;</a></li>';
                    html_paging += '<li><a>0</a></li>';
                    html_paging += '<li><a>&raquo;</a></li>';
                    html_paging += '</ul>';
                    html_paging += '</div>';
                    html_paging += '</div>';
                    html_paging += '</div>';
                }
                else {
                    html_header += '<th class="text-center" style="vertical-align:middle">STT</th>';
                    // html_header += '<th class="text-center" style="vertical-align:middle"><input type="checkbox"  onclick="clickChooseAll()" /></th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Nhóm học sinh</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Tên học sinh</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Ngày sinh</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Dân tộc</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Cha mẹ</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Hộ khẩu</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Trường</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Lớp học</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Ngày nhập học</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Nhu cầu</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Dự toán</th>';
                    // html_header += '<th class="text-center" style="vertical-align:middle"><button class="btn btn-info btn-xs">Chọn tất cả</button></th>';
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }

                if (reportName === null || reportName === "" || reportType === null || reportType === "") {
                    html_header += '<th class="text-center" style="vertical-align:middle">STT</th>';
                    // html_header += '<th class="text-center" style="vertical-align:middle"><input type="checkbox"  onclick="clickChooseAll()" /></th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Nhóm học sinh</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Tên học sinh</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Ngày sinh</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Dân tộc</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Cha mẹ</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Hộ khẩu</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Trường</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Lớp học</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Ngày nhập học</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Nhu cầu</th>';
                    html_header += '<th class="text-center" style="vertical-align:middle">Dự toán</th>';
                    // html_header += '<th class="text-center" style="vertical-align:middle"><button class="btn btn-info btn-xs">Chọn tất cả</button></th>';
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }

                // $('#divSearch').html(html_search);
                $('#headerTable').html(html_header);
                $('#dataLapDanhsachHS').html(html_show);
                // $('#divPaging').html(html_paging);
            }, error: function(dataget) {

            }
        });
    };

    function danhsachPhongtralai(objData) {

        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/insertPhongtralai',
            data: objData,
            // dataType: 'json',
            contentType: false,//'application/json; charset=utf-8',
            cache: false,             // To unable request pages to be cached
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(datas) {
                if(datas.success != null || datas.success != undefined){
                    utility.message("Thông báo",datas.success,null,3000);
                    $("#myModalRevertPhong").modal("hide");
                    loaddataDanhSachGroupB($('#drPagingDanhsach').val(), $('#txtSearchProfileLapdanhsach').val());
                }else if(datas.error != null || datas.error != undefined){
                    utility.message("Thông báo",datas.error,null,5000,1);
                }
            }, error: function(datas) {

            }
        });
    };

    function exportExcelTruongDeNghi(){
        var message = "";
        message = validateDanhsachdenghi();
        if (message !== "") {
          utility.messagehide("messageValidate", message, 1, 5000);
          return;
        }

        var reportType = $('#sltLoaiChedo').val();
        var reportName = $('#sltCongvan').val();
        var objJson = JSON.stringify({ REPORTNAME: reportName, REPORTTYPE: reportType });
        //alert(objJson);
        window.open('/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/exportExcel/' + objJson, '_blank');
        // $.ajax({
        //     type: "get",
        //     url:'/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/exportExcel/' + objJson,
        //     //data: objJson,
        //     //dataType: 'json',
        //     contentType: 'application/json; charset=utf-8',
        //     headers: {
        //         'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
        //     },
        //     success: function(data) {
        //         console.log(data);
        //     }, error: function(data) {
        //     }
        // });
    }

    function validateDanhsachdenghi(){
        var messageValidate = "";
        var reportType = $('#sltLoaiChedo').val();
        var reportName = $('#sltCongvan').val();

        if (reportName == null || reportName == "") {
            messageValidate = "Vui lòng chọn số công văn!";
            return messageValidate;
        }
        if (reportType == null || reportType == "") {
            messageValidate = "Vui lòng chọn chế độ!";
            return messageValidate;
        }

        return messageValidate;
    }

    function validateDanhsachtralai(arrData){
        var messageValidate = "";
        var reportType = $('#sltLoaiChedo').val();
        var reportName = $('#sltCongvan').val();

        if (reportName == null || reportName == "") {
            messageValidate = "Vui lòng chọn số công văn!";
            return messageValidate;
        }
        if (reportType == null || reportType == "") {
            messageValidate = "Vui lòng chọn chế độ!";
            return messageValidate;
        }
        if (arrData == null || arrData == "") {
            messageValidate = "Vui lòng chọn học sinh!";
            return messageValidate;
        }

        return messageValidate;
    }

//----------------------------------------------------------------Search Danh sách đề nghị đã lập------------------------------------------------------------------
    function autocompleteSearchDenghidalap() {
        var keySearch = "";
        $('#txtSearchProfileLapdanhsach').autocomplete({
            source: function (request, response) {
                keySearch = $.ui.autocomplete.escapeRegex(request.term).replace(/[%\\\-]/g, '');
                //console.log(keySearch.length);
                if (keySearch.length >= 2) {
                    GET_INITIAL_NGHILC();
                    loaddataDanhSachGroupA($('#drPagingDanhsach').val(), keySearch, $('#sltGroupHS').val());
                        
                }else if(keySearch.length < 2){
                    GET_INITIAL_NGHILC();
                    loaddataDanhSachGroupA($('#drPagingDanhsach').val(), "", $('#sltGroupHS').val());
                }
            },
            minLength: 0,
            delay: 222,
            autofocus: true
        });
    };

function autocomChangeSubProfile(idControl, number = null) {
        var keySearch = "";
        $('#' + idControl).autocomplete({
            source: function (request, response) {
                keySearch = $.ui.autocomplete.escapeRegex(request.term).replace(/[%\\\-]/g, '');
                //console.log(keySearch.length);
                if (keySearch.length >= 2) {
                    GET_INITIAL_NGHILC();
                    
                        
                }else if(keySearch.length < 2){
                    GET_INITIAL_NGHILC();
                    loadDataSubject();
                    
                }
            },
            minLength: 0,
            delay: 222,
            autofocus: true
        });
    };

function autocomUpdateSubProfile(idControl, number = null) {
        var keySearch = "";
        $('#' + idControl).autocomplete({
            source: function (request, response) {
                keySearch = $.ui.autocomplete.escapeRegex(request.term).replace(/[%\\\-]/g, '');
                //console.log(keySearch.length);
                if (keySearch.length >= 2) {
                    GET_INITIAL_NGHILC();
                    loadDataSubject(keySearch);
                        
                }else if(keySearch.length < 2){
                    GET_INITIAL_NGHILC();
                    loadDataSubject();
                    
                }
            },
            minLength: 0,
            delay: 222,
            autofocus: true
        });
    };