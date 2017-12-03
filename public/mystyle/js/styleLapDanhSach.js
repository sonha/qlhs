$(function () {
    //alert(1);
    var insert = true;
    
    $(document).on('click', 'button#btnDeleteDecided', function () {
        $(this).closest('tr').remove();
    });
    
    var counter = 1;
        $('#btnAddNewRow').click(function(event){
            event.preventDefault();
            counter++;
            var newRow = $('<tr id="trContent"><td>' +
                counter + '</td><td><button id="btnDeleteDecided"  class="btn btn-danger btn-xs editor_remove"><i class="glyphicon glyphicon-remove"></i> </button></td><td class="code"><input type="text" name="" id="txtDecidedCode' +
                counter + '"/></td><td class="name"><input type="text" name="" id="txtDecidedName' +
                counter + '"/></td><td class="number"><input type="text" name="" id="txtDecidedNumber' +
                counter + '"/></td><td class="confirmation"><input type="text" name="" id="txtDecidedConfirmation' +
                counter + '"/></td><td class="confirmdate"><input type="date" name="" id="txtDecidedConfirmDate' +
                counter + '"/></td><td class="uploadfile"><input type="file" name="" id="txtDecidedFileUpload' +
                counter + '"/></td><td class="oldfile"><label id="lblOldfile' +
                counter + '"></label></td></tr>');
            $('#tbDecided').append(newRow);
        });
    reset = function(){
        $('#formmiengiamhocphi')[0].reset();
    }
    del_report_mghp = function (id) {
        utility.confirm("Xóa bản ghi?", "Bạn có chắc chắn muốn xóa?", function () {
            insertUpdate(1);
            $.ajax({
                    type: "GET",
                    url: '/ho-so/lap-danh-sach/mien-giam-hoc-phi/delete/'+id,
                    success: function(dataget) {
                        if(dataget.success != null || dataget.success != undefined){
                        $("#myModal").modal("hide");
                        utility.message("Thông báo",dataget.success,null,3000)
                        GET_INITIAL_NGHILC();
                        loaddata($('#view-miengiamhocphi').val());
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
    download_attach = function (id) {
        window.open('mien-giam-hoc-phi/download/'+id, '_blank');
            $.ajax({
                    type: "GET",
                    url: 'mien-giam-hoc-phi/download/'+id,
                    success: function(dataget) {
                        $('#group_mghp').html(dataget);
                        
                    }, error: function(dataget) {
                }
            });
    }

    // $('select#viewTableProfile').change(function() {
    //    GET_INITIAL_NGHILC();
    //    loaddataProfile($(this).val(),$('select#sltTruongGrid').val(),$('select#sltLopGrid').val());
    // });
    // $('select#sltTruong').change(function() {
    // 	if(parseInt($(this).val()) != 0){
    // 		loadComboxLop($(this).val(),'sltLop');
    // 		$('select#sltLop').removeAttr('disabled');
    // 	}else{
    // 		$('select#sltLop').html('<option value="">--Chọn lớp--</option>');
    // 		$('select#sltLop').attr('disabled','disabled');
    // 	}
    // });
    // $('select#sltNhomDoiTuong').change(function() {
    // 	loadComboxDoiTuong($(this).val());
    // });
    // $('select#sltLopGrid').change(function() {
    // 	GET_INITIAL_NGHILC();
    // 	loaddataProfile($('select#viewTableProfile').val(),$('select#sltTruongGrid').val(),$(this).val());
    // });
    //loadComboxTinhThanh(0,'sltTinh');
   // alert(check);
    //	if(check){
    //		loadComboxTinhThanh($(this).val(),'sltQuan');
    //		$('select#sltQuan').removeAttr('disabled');
    //		$('select#sltPhuong').attr('disabled','disabled');
    //	}else{
    //		$('select#sltQuan').attr('disabled','disabled');
    //		$('select#sltPhuong').attr('disabled','disabled');
    //	}
    // $('select#sltTinh').change(function() {

    // 		loadComboxTinhThanh($(this).val(),'sltQuan');
    // 		$('select#sltQuan').removeAttr('disabled');
    // 		$('select#sltPhuong').attr('disabled','disabled');
    // 		$('select#sltPhuong').html('<option value="">--Chọn danh mục--</option>');

    // });
    // $('select#sltQuan').change(function() {
    // 	loadComboxTinhThanh($(this).val(),'sltPhuong');
    // 	$('select#sltPhuong').removeAttr('disabled');
    // 	//$('select#sltPhuong').attr('disabled','disabled');
    // });
    // $('select#sltPhuong').change(function() {
    // 	//if(parseInt($(this).val()) != 0){
    // 	//	loadComboxLop($(this).val());
    // 	//}
    // });

  //   $('input#ckbNghihoc').click(function() {
		// if (!$(this).is(':checked')) {
  //           $('div#divNgayNghi').attr('hidden','hidden');
  //           $('div#divNgayNghi').attr('disabled','disabled');
			
  //       }else{
  //       	$('div#divNgayNghi').removeAttr('hidden');
  //       	$('div#divNgayNghi').removeAttr('disabled');
  //       }
		// $('input#txtDateNghi').val('');
  //   });
   // autocompleteSearch("txtSearchDT");

    $('#btnViewReport').click(function(){
        GET_INITIAL_NGHILC();
        loadTonghophoso($('#view-tonghopdanhsach').val());
    });

    $('a#btnInsertKinhPhiDoiTuong').click(function(){
        insertUpdate(0);
        insert=true;
        $("#btnResetKinhPhiDoiTuong").show();
        $("#btnSaveKinhPhiDoiTuong").html('<i class="glyphicon glyphicon-plus-sign"></i> Lưu');
    });
    $('button#btnRefresh').click(function(){
        
        loaddata($('#view-miengiamhocphi').val());
    });

    //loadComboxNhomDoiTuong();
    //loadKinhPhiDoiTuong($('select#viewTableDT').val());
    $('button#save1').click(function(){
        if($('#txtNameDS').val()!=""){
            if($('#txtNguoiLap').val()!=""){
                if($('#txtNguoiKy').val()!=""){
                    var file_data = $('input#exampleInputFile').prop('files')[0];   
                    var form_datas = new FormData();   
                    form_datas.append('file', file_data);
                    form_datas.append('id_truong', $('#sltSchool').val());
                    form_datas.append('nam_hoc', $('#sltYear').val());
                    form_datas.append('name', $('#txtNameDS').val());
                    form_datas.append('create_name', $('#txtNguoiLap').val());
                    form_datas.append('create_sign', $('#txtNguoiKy').val());
                    form_datas.append('status', $('#sltStatus').val());
                    form_datas.append('note', $('#txtGhiChu').val());
                    insertMienGiamHocPhi(form_datas);   
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
          

        // var temp = {
        //         "id_truong": $('#sltSchool').val(),
        //         "nam_hoc": $('#sltYear').val(),
        //         "name": $('#txtNameDS').val(),
        //         "create_name": $('#txtNguoiLap').val(),
        //         "create_sign": $('#txtNguoiKy').val(),
        //         "status": $('#sltStatus').val(),
        //         "data" : file_data
        //     };
           
        //if(insert){
            //insertKinhPhiDoiTuong(temp);
        //}else{
        //    updateKinhPhiDoiTuong(temp);
        //}
    });
    $('button#save2').click(function(){
        if($('#txtNameDS').val()!=""){
            if($('#txtNguoiLap').val()!=""){
                if($('#txtNguoiKy').val()!=""){
                    var file_data = $('input#exampleInputFile').prop('files')[0];   
                    var form_datas = new FormData();   

                    form_datas.append('file', file_data);
                    form_datas.append('id_truong', $('#sltSchool').val());
                    form_datas.append('nam_hoc', $('#sltYear').val());
                    form_datas.append('name', $('#txtNameDS').val());
                    form_datas.append('create_name', $('#txtNguoiLap').val());
                    form_datas.append('create_sign', $('#txtNguoiKy').val());
                    form_datas.append('status', $('#sltStatus').val());
                    form_datas.append('note', $('#txtGhiChuCPHT').val());

                    insertDanhSach(form_datas,'CPHT','dataChiPhiHocTap','view-chiphihoctap');
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
    $('button#save3').click(function(){
        if($('#txtNameDS').val()!=""){
            if($('#txtNguoiLap').val()!=""){
                if($('#txtNguoiKy').val()!=""){
                    var file_data = $('input#exampleInputFile').prop('files')[0];   
                    var form_datas = new FormData();   

                    form_datas.append('file', file_data);
                    form_datas.append('id_truong', $('#sltSchool').val());
                    form_datas.append('nam_hoc', $('#sltYear').val());
                    form_datas.append('name', $('#txtNameDS').val());
                    form_datas.append('create_name', $('#txtNguoiLap').val());
                    form_datas.append('create_sign', $('#txtNguoiKy').val());
                    form_datas.append('status', $('#sltStatus').val());
                    form_datas.append('note', $('#txtGhiChuHTAT').val());

                    insertDanhSach(form_datas,'HTAT','dataHoTroAnTrua','view-hotroantrua');
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

    $('#btnInsertHSDTTS').click(function(){
        var file_data = $('input#exampleInputFileHSDTTS').prop('files')[0]; 
        var v_year = $('#sltYear').val();
        var v_year_new = v_year.substring(0, 4);  
        var form_datas = new FormData();   

        form_datas.append('FILE', file_data);
        form_datas.append('SCHOOLID', $('#sltSchool').val());
        form_datas.append('YEAR', v_year_new);
        form_datas.append('REPORTNAME', $('#txtNameDSHSDTTS').val());
        form_datas.append('CREATENAME', $('#txtNguoiLapHSDTTS').val());
        form_datas.append('CREATESIGN', $('#txtNguoiKyHSDTTS').val());
        form_datas.append('STATUS', $('#sltStatusHSDTTS').val());
        form_datas.append('NOTE', $('#txtGhiChuHSDTTS').val());

        var message = "";
        message = validatePopupHSDTTS();
        if (message !== "") {
          utility.messagehide("group_message", message, 1, 5000);
          return;
        }
        var objData = {
            FILE: file_data,
            SCHOOLID: $('#sltSchool').val(),
            YEAR: v_year_new,
            REPORTNAME: $('#txtNameDSHSDTTS').val(),
            CREATESIGN: $('#txtNguoiKyHSDTTS').val(),
            CREATENAME: $('#txtNguoiLapHSDTTS').val(),
            STATUS: $('#sltStatusHSDTTS').val()
        };
        insertHocsinhdantocthieuso(form_datas);
    });

    $('#btnInsertHTBT').click(function(){
        var file_data = $('input#exampleInputFileHTBT').prop('files')[0]; 
        var v_year = $('#sltYear').val();
        var v_year_new = v_year.substring(0, 4);  
        
        var message = "";
        message = validatePopupHTBT();
        if (message !== "") {
          utility.messagehide("group_message", message, 1, 5000);
          return;
        }

        var form_datas = new FormData();   

        form_datas.append('FILE', file_data);
        form_datas.append('SCHOOLID', $('#sltSchool').val());
        form_datas.append('YEAR', v_year_new);
        form_datas.append('REPORTNAME', $('#txtNameDSHTBT').val());
        form_datas.append('CREATENAME', $('#txtNguoiLapHTBT').val());
        form_datas.append('CREATESIGN', $('#txtNguoiKyHTBT').val());
        form_datas.append('STATUS', $('#drStatusHTBT').val());
        form_datas.append('NOTE', $('#txtGhiChuHTBT').val());

        var objData = {
            FILE: file_data,
            SCHOOLID: $('#sltSchool').val(),
            YEAR: v_year_new,
            REPORTNAME: $('#txtNameDSHTBT').val(),
            CREATENAME: $('#txtNguoiLapHTBT').val(),
            CREATESIGN: $('#txtNguoiKyHTBT').val(),
            STATUS: $('#drStatusHTBT').val()
        };
        insertHotrobantru(form_datas);
    });

    $('#btnInsertNGNA').click(function(){
        var file_data = $('input#exampleInputFileNGNA').prop('files')[0]; 
        var v_year = $('#sltYear').val();
        var v_year_new = v_year.substring(0, 4);  
        
        var message = "";
        message = validatePopupNGNA();
        if (message !== "") {
          utility.messagehide("group_message", message, 1, 5000);
          return;
        }

        var form_datas = new FormData();   

        form_datas.append('FILE', file_data);
        form_datas.append('SCHOOLID', $('#sltSchool').val());
        form_datas.append('YEAR', v_year_new);
        form_datas.append('REPORTNAME', $('#txtNameDSNGNA').val());
        form_datas.append('CREATENAME', $('#txtNguoiLapNGNA').val());
        form_datas.append('CREATESIGN', $('#txtNguoiKyNGNA').val());
        form_datas.append('STATUS', $('#drStatusNGNA').val());
        form_datas.append('NOTE', $('#txtGhiChuNGNA').val());

        var objData = {
            FILE: file_data,
            SCHOOLID: $('#sltSchool').val(),
            YEAR: v_year_new,
            REPORTNAME: $('#txtNameDSNGNA').val(),
            CREATENAME: $('#txtNguoiLapNGNA').val(),
            CREATESIGN: $('#txtNguoiKyNGNA').val(),
            STATUS: $('#drStatusNGNA').val()
        };
        insertHotroNGNA(form_datas);
    });

    $('#btnInsertHSKT').click(function(){
        var file_data = $('input#exampleInputFileHSKT').prop('files')[0]; 
        var v_year = $('#sltYear').val();
        var v_year_new = v_year.substring(0, 4);  
        
        var message = "";
        message = validatePopupHSKT();
        if (message !== "") {
          utility.messagehide("group_message", message, 1, 5000);
          return;
        }

        var form_datas = new FormData();   

        form_datas.append('FILE', file_data);
        form_datas.append('SCHOOLID', $('#sltSchool').val());
        form_datas.append('YEAR', v_year_new);
        form_datas.append('REPORTNAME', $('#txtNameDSHSKT').val());
        form_datas.append('CREATENAME', $('#txtNguoiLapHSKT').val());
        form_datas.append('CREATESIGN', $('#txtNguoiKyHSKT').val());
        form_datas.append('STATUS', $('#drStatusHSKT').val());
        form_datas.append('NOTE', $('#txtGhiChuHSKT').val());

        var objData = {
            FILE: file_data,
            SCHOOLID: $('#sltSchool').val(),
            YEAR: v_year_new,
            REPORTNAME: $('#txtNameDSHSKT').val(),
            CREATENAME: $('#txtNguoiLapHSKT').val(),
            CREATESIGN: $('#txtNguoiKyHSKT').val(),
            STATUS: $('#drStatusHSKT').val()
        };
        insertHotroHSKT(form_datas);
    });

    $('#btnInsertTongHop').click(function(){
        var file_data = $('input#exampleInputFileTongHop').prop('files')[0]; 
        var v_year = $('#sltYear').val();
        var v_year_new = v_year.substring(0, 4);  
        
        var message = "";
        message = validatePopupTongHop();
        if (message !== "") {
          utility.messagehide("group_message", message, 1, 5000);
          return;
        }

        var form_datas = new FormData();   

        form_datas.append('FILE', file_data);
        form_datas.append('SCHOOLID', $('#sltSchool').val());
        form_datas.append('YEAR', v_year_new);
        form_datas.append('REPORTNAME', $('#txtNameDSTongHop').val());
        form_datas.append('CREATENAME', $('#txtNguoiLapTongHop').val());
        form_datas.append('CREATESIGN', $('#txtNguoiKyTongHop').val());
        form_datas.append('STATUS', $('#drStatusTongHop').val());
        form_datas.append('NOTE', $('#txtGhiChuTONGHOP').val());

        var objData = {
            FILE: file_data,
            SCHOOLID: $('#sltSchool').val(),
            YEAR: v_year_new,
            REPORTNAME: $('#txtNameDSTongHop').val(),
            CREATENAME: $('#txtNguoiLapTongHop').val(),
            CREATESIGN: $('#txtNguoiKyTongHop').val(),
            STATUS: $('#drStatusTongHop').val()
        };
        insertTongHop(form_datas);
    });

        

    $('#btnInsertTHCD').click(function(){        
        var message = "";
        message = validatePopupTongHopCheDo();
        if (message !== "") {
          utility.messagehide("group_message_THCD", message, 1, 5000);
          return;
        }

        var file_data = $('input#fileAttack').prop('files')[0];   
        var form_datas = new FormData();   
        form_datas.append('FILE', file_data);
        form_datas.append('SCHOOLID', $('#sltSchool').val());
        form_datas.append('YEAR', $('#sltYear').val());
        form_datas.append('REPORTNAME', $('#txtNameDSTHCD').val());
        form_datas.append('CREATENAME', $('#txtNguoiLapTHCD').val());
        form_datas.append('SIGNNAME', $('#txtNguoiKyTHCD').val());
        form_datas.append('STATUS', $('#sltStatus').val());
        form_datas.append('NOTE', $('#txtGhiChuTHCD').val());

        lapdanhsachDanhSachTongHop(form_datas);
    });

    $('#btnApprovedTHCD').click(function(){        
        var strData = "";
        $('input[type=checkbox]').each(function () {
            
            if (this.checked) {
                var sThisVal = $(this).val();
                strData += (strData=="" ? sThisVal : "-" + sThisVal);
            }
        });
        // console.log (strData);
        approvedChedo(strData);
    });

    // $('button#btnOpenPopupSendDSTH').click(function(){        
    //     var strData = $(this).attr("data");
    //     console.log("strData");
    // });
  });
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
        function loadComboxNhomDoiTuong() {
            $.ajax({
                type: "GET",
                url: '/danh-muc/load/nhom-doi-tuong',
                success: function(dataget) {
                    $('#sltNhomDoiTuong').html("");
                    var html_show = "";
                    if(dataget.length >0){
                      //  $.fn.dataTable.render.number( '.', ',', 0, '' ) 
                        html_show += "<option value=''>-- Chọn nhóm đối tượng --</option>";
                        for (var i = dataget.length - 1; i >= 0; i--) {
                            html_show += "<option value='"+dataget[i].group_id+"'>"+dataget[i].group_name+"</option>";
                        }
                        $('#sltNhomDoiTuong').html(html_show);
                    }else{
                        $('#sltNhomDoiTuong').html("<option value=''>-- Chưa có nhóm đối tượng --</option>");
                    }
                }, error: function(dataget) {
                }
            });
        };
function loadComboxDantoc(){
            $.ajax({
                type: "GET",
                url: '/danh-muc/load/dan-toc',
                success: function(dataget) {
                    $('#sltDantoc').html("");
                    var html_show = "";
                    if(dataget.length >0){
                     
                        html_show += "<option value=''>-- Chọn dân tộc --</option>";
                        for (var i = 0; i < dataget.length; i++) {
                            html_show += "<option value='"+dataget[i].nationals_id+"'>"+dataget[i].nationals_name+"</option>";
                        }
                        $('#sltDantoc').html(html_show);
                    }else{
                        $('#sltDantoc').html("<option value=''>-- Chưa có dân tộc --</option>");
                    }
                }, error: function(dataget) {
                }
            });
        };
function loadComboxTruongHoc(idchoise = null) {
            $.ajax({
                type: "GET",
                url: '/danh-muc/load/truong-hoc',
                success: function(data) {
                    var dataget = data.truong;
                    var datakhoi = data.khoi;

                    // console.log(dataget.length);
                    // <optgroup label="Cats">
                    $('#sltSchool').html("");
                    //$('#sltTruongGrid').html("");
                    var html_show = "";
                    if(datakhoi.length > 0){
                        if (dataget.length > 1) {
                            html_show += "<option value='0'>-- Chọn trường học --</option>";
                            for (var j = 0; j < datakhoi.length; j++) {
                                html_show +="<optgroup label='"+datakhoi[j].unit_name+"'>";
                                    if(dataget.length > 0){
                                        // for (var i = 0; i < dataget.length; i++) {
                                        //     if(datakhoi[j].unit_id === dataget[i].schools_unit_id){
                                        //         // html_show += "<option value='"+dataget[i].schools_id+"'>"+dataget[i].schools_name+"</option>";
                                        //         if(idchoise===parseInt(dataget[i].schools_id)){
                                        //             html_show += "<option value='"+dataget[i].schools_id+"' selected>"+dataget[i].schools_name+"</option>";
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
                        }
                        else {
                            for (var i = 0; i < dataget.length; i++) {
                                    
                                html_show += "<option value='"+dataget[i].schools_id+"'>"+dataget[i].schools_name+"</option>";
                            }
                        }
                        //$('#sltTruong').html(html_show);
                        $('#sltSchool').html(html_show);
                    }else{
                    	//$('#sltTruongGrid').html("<option value=''>-- Chưa có trường --</option>");
                        $('#sltSchool').html("<option value=''>-- Chưa có trường --</option>");
                    }
                }, error: function(dataget) {
                }
            });
        };
        function loadComboxLop(id,idselect) {
            $.ajax({
                type: "GET",
                url: '/danh-muc/load/lop/'+id,
                success: function(dataget) {
                   $('#'+idselect).html("");
                    var html_show = "";
                    if(dataget.length >0){
                        html_show += "<option value='0'>-- Chọn lớp --</option>";
                        for (var i = 0; i < dataget.length; i++) {
                            html_show += "<option value='"+dataget[i].class_id+"'>"+dataget[i].class_name+"</option>";
                        }
                        $('#'+idselect).html(html_show);
                    }else{
                        $('#'+idselect).html("<option value=''>-- Chưa có lớp --</option>");
                    }
                }, error: function(dataget) {
                }
            });
        };
    function loadComboxDoiTuong(id) {
            $.ajax({
                type: "GET",
                url: '/danh-muc/load/doi-tuong/'+id,
                success: function(dataget) {
                    $('#sltDoiTuong').html("");
                    var html_show = "";
                    if(dataget.length >0){
                      // $('.multiselect-selected-text').html('-- Chọn dân tộc --');
                        html_show += "<option value=''>-- Chọn đối tượng --</option>";
                        for (var i = dataget.length - 1; i >= 0; i--) {
                            html_show += "<option value='"+dataget[i].subject_id+"'>"+dataget[i].subject_name+"</option>";
                        }
                        $('#sltDoiTuong').html(html_show);
                    }else{
                        $('#sltDoiTuong').html("<option value=''>-- Chưa có đối tượng --</option>");
                    }
                }, error: function(dataget) {
                }
            });
        };
function loadComboxTinhThanh(id,idselect) {
	//var size = false;
            $.ajax({
                type: "GET",
                url: '/danh-muc/load/city/'+id,
                success: function(dataget) {
                    $('#'+idselect).html("");
                    var html_show = "";
                    if(dataget.length >0){
                      //if(id===0){
                       		html_show += "<option value=''>-- Chọn danh mục --</option>";
                       //}
                        for (var i = 0; i < dataget.length; i++) {
                            html_show += "<option value='"+dataget[i].site_id+"'>"+dataget[i].site_name+"</option>";
                        }
                        $('#'+idselect).html(html_show);
                     //   size = true;
                    }else{
                        $('#'+idselect).html("<option value=''>-- Chưa có danh mục --</option>");
                    	//size = false;
                    }
                }, error: function(dataget) {
                }
            });
           // return size;
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
        return false;
    } catch (e) {
        console.log(e);
    }
    return true;
}
function loaddata(row) {
    //if(lop===null) lop=0;
    var html_show = "";
    var o = {
        start: (GET_START_RECORD_NGHILC()),
        limit : row,
      //  id_truong: 1,
      //  nam_hoc:2
    };
            $.ajax({
                type: "POST",
                url: '/ho-so/lap-danh-sach/mien-giam-hoc-phi/load',
                data: JSON.stringify(o),
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(datas) {
                    //console.log(datas);
                    SETUP_PAGING_NGHILC(datas, function () {
                        loaddata(row);
                    });
                    $('#dataMienGiam').html("");
                    var dataget = datas.data;
                    // b = dataget.b;
                    // c = dataget.c;
                    // html_show += "<tr><td colspan='50' class='text-center'>Học sinh có mặt tại trường tháng 5/2016</td></tr>";
                    if(dataget.length>0){
                        for (var i = 0; i < dataget.length; i++) {
                                
                            html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            html_show += "<td><a href='javascript:;' onclick='export_file("+dataget[i].report_id+", 1);'>"+dataget[i].report_name+"</a></td>";
                            html_show += "<td>"+dataget[i].report_user+"</td>";
                            if(parseInt(dataget[i].report_nature) === 0){
                               html_show += "<td class='text-center'>Bình thường</td>";
                            }else{
                                html_show += "<td class='text-center'>Khẩn cấp</td>";
                            }
                            //alert(dataget[i].status);
                            if(parseInt(dataget[i].report_status) === 0){
                               html_show += "<td class='text-center'>Chưa gửi</td>";
                            }else if(parseInt(dataget[i].report_status) === 1){
                               html_show += "<td class='text-center'>Đã gửi</td>";
                            }
                            else if(parseInt(dataget[i].report_status) === 2){
                                html_show += "<td class='text-center'>Chuyển lại</td>";
                            }
                            else if(parseInt(dataget[i].report_status) === 3){
                                html_show += "<td class='text-center'>Đã duyệt</td>";
                            }
                            html_show += "<td class='text-center'><a href='javascript:;' onclick='download_attach_file("+dataget[i].report_id+", 1);'>"+dataget[i].report_attach_name+"</a></td>";
                            html_show += "<td class='text-center'>";
                            if(check_Permission_Feature('5')){
                                if(parseInt(dataget[i].report_status) == 0){
                                    html_show += "<button  class='btn btn-info btn-xs' onclick='openPopupSend("+dataget[i].report_id+",1)'><i class='glyphicon glyphicon-send'></i> Gửi</button> ";
                                    if(check_Permission_Feature('3')===true){
                                        html_show += "<button  onclick='del_report("+dataget[i].report_id+", 1);'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                                    }
                                }else if(parseInt(dataget[i].report_status) == 1){
                                    html_show += "<button  class='btn btn-primary btn-xs' > Chờ phê duyệt</button> ";
                                }else if(parseInt(dataget[i].report_status) == 2){
                                    html_show += "<button onclick='loadPopupRevert("+dataget[i].report_id+", 1);' class='btn btn-warning btn-xs' > Trả lại</button> ";
                                    // if(check_Permission_Feature('3')===true){
                                    //     html_show += "<button  onclick='del_report("+dataget[i].report_id+", 1);'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                                    // }
                                }else {
                                    html_show += "<button onclick='loadPopupRevert("+dataget[i].report_id+", 1);' class='btn btn-success btn-xs' > Đã phê duyệt </button> ";
                                }

                            }
                            html_show += "</td></tr>";
                        }                    
                        
                    }
                      else {
                         html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                     }
                    $('#dataMienGiam').html(html_show);
                }, error: function(dataget) {

                }
            });
        };

        function insertMienGiamHocPhi(temp) {
            //console.log(temp);
            loading();
            $.ajax({
                type: "POST",
                url: 'mien-giam-hoc-phi/insert',
                data: temp,//JSON.stringify(temp),
               // dataType: 'json',
                contentType: false,//'application/json; charset=utf-8',
                cache: false,             // To unable request pages to be cached
                processData: false,
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {
                    //console.log(dataget);
                    if(dataget.success != null || dataget.success != undefined){
                        $("#myModalLapDanhSach").modal("hide");
                        utility.message("Thông báo",dataget.success,null,3000);
                        insertUpdate(1);
                        $('#formmiengiamhocphi')[0].reset();
                        GET_INITIAL_NGHILC();
                        loaddata($('#view-miengiamhocphi').val());
                        $("#myModalLapDanhSach").modal("hide");
                        closeLoading();
                    }else if(dataget.error != null || dataget.error != undefined){
                        $("#myModalLapDanhSach").modal("hide");
                        utility.message("Thông báo",dataget.error,null,5000,1);
                        closeLoading();
                    }
                }, error: function(dataget) {
                    closeLoading();
                }
            });
        };
function insertDanhSach(temp,type,idbody,idView) {
    loading();
    var url = '';
    if(type === 'CPHT'){
        url = 'chi-phi-hoc-tap';
    }else if(type === 'HTAT'){
        url = 'ho-tro-an-trua';
    }
            $.ajax({
                type: "POST",
                url: url+'/insert',
                data: temp,//JSON.stringify(temp),
               // dataType: 'json',
                contentType: false,//'application/json; charset=utf-8',
                cache: false,    
                processData: false,
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {
                    if(dataget.success != null || dataget.success != undefined){
                        $("#myModalLapDanhSach"+type).modal("hide");
                        utility.message("Thông báo",dataget.success,null,3000)
                        insertUpdate(1);
                        if(type === 'CPHT'){$('#frmCPHT')[0].reset(); $("#myModalLapDanhSachCPHT").modal("hide");}
                        if(type === 'HTAT'){$('#frmHTAT')[0].reset(); $("#myModalLapDanhSachHTAT").modal("hide");}
                        GET_INITIAL_NGHILC();
                        loaddataAll($('#'+idView).val(),type,idbody);
                        closeLoading();
                   // loadKinhPhiDoiTuong($('select#viewTableDT').val());
                    }else if(dataget.error != null || dataget.error != undefined){
                        $("#myModalLapDanhSach").modal("hide");
                        utility.message("Thông báo",dataget.error,null,5000,1)
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
    function loaddataAll(row,type,idbody) {
        var url = '';
        var number = 0;
        if(type === 'CPHT'){
            url = '/ho-so/lap-danh-sach/chi-phi-hoc-tap';
            number = 2;
        }else if(type === 'HTAT'){
            url = '/ho-so/lap-danh-sach/ho-tro-an-trua';
            number = 3;
        }
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit : row,
            type: type
        };
            $.ajax({
                type: "POST",
                url: url+'/load',
                data: JSON.stringify(o),
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(datas) {
                    SETUP_PAGING_NGHILC(datas, function () {
                        loaddataAll(row,type,idbody);
                    });
                    $('#'+idbody).html("");
                    var dataget = datas.data;
                    // b = dataget.b;
                    // c = dataget.c;
                    // html_show += "<tr><td colspan='50' class='text-center'>Học sinh có mặt tại trường tháng 5/2016</td></tr>";
                    if(dataget.length>0){
                        for (var i = 0; i < dataget.length; i++) {
                                
                            html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            html_show += "<td><a href='javascript:;' onclick='export_file("+dataget[i].report_id+","+number+");'>"+dataget[i].report_name+"</a></td>";
                            html_show += "<td>"+dataget[i].report_user+"</td>";
                            if(parseInt(dataget[i].report_nature) === 0){
                               html_show += "<td class='text-center'>Bình thường</td>";
                            }else{
                                html_show += "<td class='text-center'>Khẩn cấp</td>";
                            }
                            //alert(dataget[i].status);
                            if(parseInt(dataget[i].report_status) === 0){
                               html_show += "<td class='text-center'>Chưa gửi</td>";
                            }else if(parseInt(dataget[i].report_status) === 1){
                               html_show += "<td class='text-center'>Đã gửi</td>";
                            }
                            else if(parseInt(dataget[i].report_status) === 2){
                                html_show += "<td class='text-center'>Chuyển lại</td>";
                            }
                            else if(parseInt(dataget[i].report_status) === 3){
                                html_show += "<td class='text-center'>Đã duyệt</td>";
                            }
                            html_show += "<td class='text-center'><a href='javascript:;' onclick='download_attach_file("+dataget[i].report_id+", "+number+");'>"+dataget[i].report_attach_name+"</a></td>";
                            html_show += "<td class='text-center'>";
                            if(check_Permission_Feature('5')){
                                if(parseInt(dataget[i].report_status) == 0){
                                    html_show += "<button  class='btn btn-info btn-xs' onclick='openPopupSend("+dataget[i].report_id+","+number+")'><i class='glyphicon glyphicon-send'></i> Gửi</button> ";
                                    if(check_Permission_Feature('3')){
                                        html_show += "<button  onclick='del_report("+dataget[i].report_id+","+number+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                                    }
                                }else if(parseInt(dataget[i].report_status) == 1){
                                    html_show += "<button  class='btn btn-primary btn-xs' > Chờ phê duyệt</button> ";
                                }else if(parseInt(dataget[i].report_status) == 2){
                                    html_show += "<button onclick='loadPopupRevert("+dataget[i].report_id+", "+number+");' class='btn btn-warning btn-xs' > Trả lại</button> ";
                                }else {
                                    html_show += "<button onclick='loadPopupRevert("+dataget[i].report_id+", "+number+");' class='btn btn-success btn-xs' > Đã phê duyệt </button> ";
                                }
                            }else if(check_Permission_Feature('3')){
                                html_show += "<button  onclick='del_report("+dataget[i].report_id+","+number+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                            }
                            html_show += "</td></tr>";
                        }                    
                        
                    }
                      else {
                         html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                     }
                    $('#'+idbody).html(html_show);
                }, error: function(dataget) {

                }
            });
        };
        function insertUpdate(type){
            //$('#formmiengiamhocphi')[0].reset();
            //$('#form1')[0].reset();
                $("#sltSchool").val('0');
                $("#sltYear").val('');
                $("#sltStatus").val('0');
                $("#txtNameDS").val('');
                $("#txtNguoiLap").val('');
                $("#txtNguoiKy").val('');
                $("#txtGhiChu").val('');
                
            // if(type===1){
            //     $("#txtCodeKinhPhi1").attr('disabled','disabled');
            //     $("#sltSubject").attr('disabled','disabled');
            //     $("#sltTruongDt").attr('disabled','disabled');
            //     $("#txtMoney1").attr('disabled','disabled');
            //     $("#datepicker1").attr('disabled','disabled');
            //     $("#datepicker2").attr('disabled','disabled');
            //     $("#btnSaveKinhPhiDoiTuong").hide();
            //     $("#btnCancelKinhPhiDoiTuong").hide();
            //     $("#btnResetKinhPhiDoiTuong").hide();
            // }else{
            //     $("#sltTruongDt").removeAttr('disabled');
            //     $("#txtCodeKinhPhi1").removeAttr('disabled');
            //     $("#sltSubject").removeAttr('disabled');
            //     $("#txtMoney1").removeAttr('disabled');
            //     $("#datepicker1").removeAttr('disabled');
            //     $("#datepicker2").removeAttr('disabled');
            //     $("#btnSaveKinhPhiDoiTuong").removeAttr('disabled');
            //     $("#btnSaveKinhPhiDoiTuong").show();
            //     $("#btnResetKinhPhiDoiTuong").hide();
            //     $("#btnCancelKinhPhiDoiTuong").show();
            // }
           
        };

autocompleteSearch = function (idSearch) {
        var lstCustomerForCombobox;
        $('#' + idSearch).autocomplete({
            source: function (request, response) {
                var cusNameSearch = $.ui.autocomplete.escapeRegex(request.term).replace(/[%\\\-]/g, '');
                if (cusNameSearch.length >= 4) {
                    $.get("/kinh-phi/muc-ho-tro-doi-tuong/search/" + cusNameSearch, function (data) {
                        lstCustomerForCombobox = [];
                        var item;
                        if (data.length > 0) {
                            for (var i = 0; i < data.length; i++) {
                                var dl = data[i];
                                if (dl.Name != null)
                                    item = dl.CustomerId + '-' + dl.Name;
                                else
                                    item = dl.CustomerId;
                                lstCustomerForCombobox.push(item);
                            }
                        } else {
                           // $('#' + idCusName).val('');
                            //$('#' + idCusId).val('');
                        }
                        var matcher = new RegExp(cusNameSearch, "i");
                        response($.grep(lstCustomerForCombobox, function (item) {
                            return matcher.test(item);
                        }));
                    });
                }
            },
            minLength: 1,
            delay: 222,
            autofocus: true,
            select: function (event, ui) {
                var value = ui.item.value;
                var customerCode = value.split('-')[0];
                var customerName = value.split('-')[1];
                //$('#' + idCusCode).val(customerCode);
                //$('#' + idCusName).val(customerName);
               // $('#' + idCusId).val('');
                return false;
            }
        });
    };

    function exportExcelAllSchool()
    {
        window.open('mien-giam-hoc-phi/exportExcelAllSchool', '_blank');
        $.ajax({
            type: "GET",
            url: 'mien-giam-hoc-phi/exportExcelAllSchool',
            success: function(dataget) {
                //$('#group_mghp').html(dataget);                        
            }, error: function(dataget) {
            }
        });
    }

//Học sinh dân tộc thiểu số---------------------------------------------------------------------------
    
    function loaddataHSDTTS(row) {
        
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit : row
        };
        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/hoc-sinh-dan-toc-thieu-so/load',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(datas) {
                SETUP_PAGING_NGHILC(datas, function () {
                    loaddataHSDTTS(row);
                });
                //console.log(datas);
                $('#dataHSDTTS').html("");
                var dataget = datas.data;
                
                if(dataget.length>0){
                    for (var i = 0; i < dataget.length; i++) {
                                    
                        html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                        html_show += "<td><a href='javascript:;' onclick='export_file("+dataget[i].report_id+", 5);'>"+dataget[i].report_name+"</a></td>";
                        html_show += "<td>"+dataget[i].report_user+"</td>";
                        if(parseInt(dataget[i].report_nature) === 0){
                            html_show += "<td class='text-center'>Bình thường</td>";
                        }else{
                            html_show += "<td class='text-center'>Khẩn cấp</td>";
                        }
                        //alert(dataget[i].status);
                        if(parseInt(dataget[i].report_status) === 0){
                            html_show += "<td class='text-center'>Chưa gửi</td>";
                        }else if(parseInt(dataget[i].report_status) === 1){
                            html_show += "<td class='text-center'>Đã gửi</td>";
                        }
                        else if(parseInt(dataget[i].report_status) === 2){
                            html_show += "<td class='text-center'>Chuyển lại</td>";
                        }
                        else if(parseInt(dataget[i].report_status) === 3){
                            html_show += "<td class='text-center'>Đã duyệt</td>";
                        }
                        html_show += "<td class='text-center'><a href='javascript:;' onclick='download_attach_file("+dataget[i].report_id+", 5);'>"+dataget[i].report_attach_name+"</a></td>";
                        html_show += "<td class='text-center'>";
                        if(check_Permission_Feature('5')){
                            if(parseInt(dataget[i].report_status) == 0){
                                html_show += "<button  class='btn btn-info btn-xs' onclick='openPopupSend("+dataget[i].report_id+",4)'><i class='glyphicon glyphicon-send'></i> Gửi</button> ";
                                if(check_Permission_Feature('3')){
                                    html_show += "<button  onclick='del_report("+dataget[i].report_id+",5);'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                                }
                            }else if(parseInt(dataget[i].report_status) == 1){
                                html_show += "<button  class='btn btn-primary btn-xs' > Chờ phê duyệt</button> ";
                            }else if(parseInt(dataget[i].report_status) == 2){
                                html_show += "<button onclick='loadPopupRevert("+dataget[i].report_id+", 5);' class='btn btn-warning btn-xs' > Trả lại</button> ";
                            }else {
                                html_show += "<button onclick='loadPopupRevert("+dataget[i].report_id+", 5);' class='btn btn-success btn-xs' > Đã phê duyệt </button> ";
                            }
                        }else if(check_Permission_Feature('3')){
                            html_show += "<button  onclick='del_report("+dataget[i].report_id+",5);'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                        }
                        html_show += "</td></tr>";
                    }                    
                            
                }
                else {
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }
                $('#dataHSDTTS').html(html_show);
            }, error: function(dataget) {

            }
        });
    };

    function insertHocsinhdantocthieuso(objData){
        loading();
        //var objJson = JSON.stringify(objData);
        //console.log(objJson);
        //window.open('hoc-sinh-dan-toc-thieu-so/getData', '_blank');
        
        $.ajax({
            type: "POST",
            url:'/ho-so/lap-danh-sach/hoc-sinh-dan-toc-thieu-so/getData',
            data: objData,
            contentType: false,
            cache: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //console.log(data);
                if (data['success'] != "" && data['success'] != undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    resetHSDTTS();
                    $('#txtNameDSHSDTTS').val('');
                    $('#txtNguoiLapHSDTTS').val('');
                    $('#txtNguoiKyHSDTTS').val('');
                    loaddataHSDTTS($('#drPagingHSDTTS').val());
                    $("#myModalLapDanhSach").modal("hide");
                    closeLoading();
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000,1);
                    closeLoading();
                }
            }, error: function(data) {
                closeLoading();
            }
        });
    }

    function validateHSDTTS(){
        var messageValidate = "";
        var schools_id = $('#sltSchool').val();
        var year = $('#sltYear').val();

        if (schools_id == "" || schools_id == 0) {
            messageValidate = "Vui lòng chọn trường!";
            return messageValidate;
        }
        if (year == "" || year == 0) {
            messageValidate = "Vui lòng chọn năm học!";
            return messageValidate;
        }

        return messageValidate;
    }

    function validatePopupHSDTTS(){
        var messageValidate = "";
        var reportName = $('#txtNameDSHSDTTS').val();
        var tennguoilap = $('#txtNguoiLapHSDTTS').val();
        var tennguoiky = $('#txtNguoiKyHSDTTS').val();

        if (reportName.trim() == "") {
            messageValidate = "Vui lòng nhập tên danh sách!";
            $('#txtNameDSHSDTTS').focus(); 
            return messageValidate;
        }else if (reportName.length > 200) {
            messageValidate = "Tên danh sách không được vượt quá 200 ký tự!";
            $('#txtNameDSHSDTTS').focus();
            $('#txtNameDSHSDTTS').val("");
            return messageValidate;
        }
        else{
            $('#txtNameDSHSDTTS').focusout();
            var specialChars = "#/|\\";

            for (var i = 0; i < reportName.length; i++) {
                if (specialChars.indexOf(reportName.charAt(i)) != -1) {
                    messageValidate = "Tên danh sách không được chứa ký tự #, /, |, \\!";
                    $('#txtNameDSHSDTTS').focus();
                    $('#txtNameDSHSDTTS').val("");
                    return messageValidate;
                }
            }
        }

        if (tennguoilap.trim() == "") {
            messageValidate = "Vui lòng nhập tên người lập!";
            $('#txtNguoiLapHSDTTS').focus(); 
            return messageValidate;
        }else if (tennguoilap.length > 200) {
            messageValidate = "Tên người lập không được vượt quá 200 ký tự!";
            $('#txtNguoiLapHSDTTS').focus();
            $('#txtNguoiLapHSDTTS').val("");
            return messageValidate;
        }
        else{
            $('#txtNguoiLapHSDTTS').focusout();
            var specialChars = "#/|\\";

            for (var i = 0; i < tennguoilap.length; i++) {
                if (specialChars.indexOf(tennguoilap.charAt(i)) != -1) {
                    messageValidate = "Tên người lập không được chứa ký tự #, /, |, \\!";
                    $('#txtNguoiLapHSDTTS').focus();
                    $('#txtNguoiLapHSDTTS').val("");
                    return messageValidate;
                }
            }
        }

        if (tennguoiky.trim() == "") {
            messageValidate = "Vui lòng nhập tên người ký!";
            $('#txtNguoiKyHSDTTS').focus(); 
            return messageValidate;
        }else if (tennguoiky.length > 200) {
            messageValidate = "Tên người ký không được vượt quá 200 ký tự!";
            $('#txtNguoiKyHSDTTS').focus();
            $('#txtNguoiKyHSDTTS').val("");
            return messageValidate;
        }
        else{
            $('#txtNguoiKyHSDTTS').focusout();
            var specialChars = "#/|\\";

            for (var i = 0; i < tennguoiky.length; i++) {
                if (specialChars.indexOf(tennguoiky.charAt(i)) != -1) {
                    messageValidate = "Tên người ký không được chứa ký tự #, /, |, \\!";
                    $('#txtNguoiKyHSDTTS').focus();
                    $('#txtNguoiKyHSDTTS').val("");
                    return messageValidate;
                }
            }
        }

        return messageValidate;
    }

    function resetHSDTTS(){
        $('#formhsdtts')[0].reset();
    }

//Hỗ trợ học sinh bán trú---------------------------------------------------------------------------
    
    function loaddataHTBT(row) {
        
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit : row
        };
        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/hoc-sinh-ban-tru/load',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(datas) {
                SETUP_PAGING_NGHILC(datas, function () {
                    loaddataHTBT(row);
                });
                //console.log(datas);
                $('#dataBantru').html("");
                var dataget = datas.data;
                
                if(dataget.length>0){
                    for (var i = 0; i < dataget.length; i++) {
                                    
                        html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                        html_show += "<td><a href='javascript:;' onclick='export_file("+dataget[i].report_id+", 4);'>"+dataget[i].report_name+"</a></td>";
                        html_show += "<td>"+dataget[i].report_user+"</td>";
                        if(parseInt(dataget[i].report_nature) === 0){
                            html_show += "<td class='text-center'>Bình thường</td>";
                        }else{
                            html_show += "<td class='text-center'>Khẩn cấp</td>";
                        }
                        //alert(dataget[i].status);
                        if(parseInt(dataget[i].report_status) === 0){
                            html_show += "<td class='text-center'>Chưa gửi</td>";
                        }else if(parseInt(dataget[i].report_status) === 1){
                            html_show += "<td class='text-center'>Đã gửi</td>";
                        }
                        else if(parseInt(dataget[i].report_status) === 2){
                            html_show += "<td class='text-center'>Chuyển lại</td>";
                        }
                        else if(parseInt(dataget[i].report_status) === 3){
                            html_show += "<td class='text-center'>Đã duyệt</td>";
                        }
                        html_show += "<td class='text-center'><a href='javascript:;' onclick='download_attach_file("+dataget[i].report_id+", 4);'>"+dataget[i].report_attach_name+"</a></td>";
                        html_show += "<td class='text-center'>";
                        if(check_Permission_Feature('5')){
                            if(parseInt(dataget[i].report_status) == 0){
                                html_show += "<button  class='btn btn-info btn-xs' onclick='openPopupSend("+dataget[i].report_id+",5)'><i class='glyphicon glyphicon-send'></i> Gửi</button> ";
                                if(check_Permission_Feature('3')){
                                    html_show += "<button  onclick='del_report("+dataget[i].report_id+",4);'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                                }
                            }else if(parseInt(dataget[i].report_status) == 1){
                                html_show += "<button  class='btn btn-primary btn-xs' > Chờ phê duyệt</button> ";
                            }else if(parseInt(dataget[i].report_status) == 2){
                                html_show += "<button onclick='loadPopupRevert("+dataget[i].report_id+", 4);' class='btn btn-warning btn-xs' > Trả lại</button> ";
                            }else {
                                html_show += "<button onclick='loadPopupRevert("+dataget[i].report_id+", 4);' class='btn btn-success btn-xs' > Đã phê duyệt </button> ";
                            }
                        }else if(check_Permission_Feature('3')){
                            html_show += "<button  onclick='del_report("+dataget[i].report_id+",4);'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                        }
                        html_show += "</td></tr>";
                    }                    
                            
                }
                else {
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }
                $('#dataBantru').html(html_show);
            }, error: function(dataget) {

            }
        });
    };

    function insertHotrobantru(objData){
        loading();
        //var objJson = JSON.stringify(objData);
        //console.log(objData);
        //window.open('hoc-sinh-dan-toc-thieu-so/getData', '_blank');
        
        $.ajax({
            type: "POST",
            url:'/ho-so/lap-danh-sach/hoc-sinh-ban-tru/getData',
            data: objData,
            contentType: false,
            cache: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //console.log(data);
                if (data['success'] != "" && data['success'] != undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    resetHTBT();
                    $('#txtNameDSHTBT').val('');
                    $('#txtNguoiLapHTBT').val('');
                    $('#txtNguoiKyHTBT').val('');
                    loaddataHTBT($('#drPagingHTBT').val());
                    $("#myModalLapDanhSach").modal("hide");
                    closeLoading();
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000,1);
                    closeLoading();
                }
            }, error: function(data) {
                closeLoading();
            }
        });
    }

    function validateHTBT(){
        var messageValidate = "";
        var schools_id = $('#sltSchool').val();
        var year = $('#sltYear').val();

        if (schools_id == "" || schools_id == 0) {
            messageValidate = "Vui lòng chọn trường!";
            return messageValidate;
        }
        if (year == "" || year == 0) {
            messageValidate = "Vui lòng chọn năm học!";
            return messageValidate;
        }

        return messageValidate;
    }

    function validatePopupHTBT(){
        var messageValidate = "";
        var reportName = $('#txtNameDSHTBT').val();
        var tennguoilap = $('#txtNguoiLapHTBT').val();
        var tennguoiky = $('#txtNguoiKyHTBT').val();

        if (reportName.trim() == "") {
            messageValidate = "Vui lòng nhập tên danh sách!";
            $('#txtNameDSHTBT').focus(); 
            return messageValidate;
        }else if (reportName.length > 200) {
            messageValidate = "Tên danh sách không được vượt quá 200 ký tự!";
            $('#txtNameDSHTBT').focus();
            $('#txtNameDSHTBT').val("");
            return messageValidate;
        }
        else{
            $('#txtNameDSHTBT').focusout();
            var specialChars = "#/|\\";

            for (var i = 0; i < reportName.length; i++) {
                if (specialChars.indexOf(reportName.charAt(i)) != -1) {
                    messageValidate = "Tên danh sách không được chứa ký tự #, /, |, \\!";
                    $('#txtNameDSHTBT').focus();
                    $('#txtNameDSHTBT').val("");
                    return messageValidate;
                }
            }
        }

        if (tennguoilap.trim() == "") {
            messageValidate = "Vui lòng nhập tên người lập!";
            $('#txtNguoiLapHTBT').focus(); 
            return messageValidate;
        }else if (tennguoilap.length > 200) {
            messageValidate = "Tên người lập không được vượt quá 200 ký tự!";
            $('#txtNguoiLapHTBT').focus();
            $('#txtNguoiLapHTBT').val("");
            return messageValidate;
        }
        else{
            $('#txtNguoiLapHTBT').focusout();
            var specialChars = "#/|\\";

            for (var i = 0; i < tennguoilap.length; i++) {
                if (specialChars.indexOf(tennguoilap.charAt(i)) != -1) {
                    messageValidate = "Tên người lập không được chứa ký tự #, /, |, \\!";
                    $('#txtNguoiLapHTBT').focus();
                    $('#txtNguoiLapHTBT').val("");
                    return messageValidate;
                }
            }
        }

        if (tennguoiky.trim() == "") {
            messageValidate = "Vui lòng nhập tên người ký!";
            $('#txtNguoiKyHTBT').focus(); 
            return messageValidate;
        }else if (tennguoiky.length > 200) {
            messageValidate = "Tên người ký không được vượt quá 200 ký tự!";
            $('#txtNguoiKyHTBT').focus();
            $('#txtNguoiKyHTBT').val("");
            return messageValidate;
        }
        else{
            $('#txtNguoiKyHTBT').focusout();
            var specialChars = "#/|\\";

            for (var i = 0; i < tennguoiky.length; i++) {
                if (specialChars.indexOf(tennguoiky.charAt(i)) != -1) {
                    messageValidate = "Tên người ký không được chứa ký tự #, /, |, \\!";
                    $('#txtNguoiKyHTBT').focus();
                    $('#txtNguoiKyHTBT').val("");
                    return messageValidate;
                }
            }
        }

        return messageValidate;
    }

    function resetHTBT(){
        $('#formhtbt')[0].reset();
    }

//Hỗ trợ người nấu ăn---------------------------------------------------------------------------
    
    function loaddataNGNA(row) {
        
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit : row
        };
        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/nguoi-nau-an/load',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(datas) {
                SETUP_PAGING_NGHILC(datas, function () {
                    loaddataNGNA(row);
                });
                //console.log(datas);
                $('#dataNGNA').html("");
                var dataget = datas.data;
                
                if(dataget.length>0){
                    for (var i = 0; i < dataget.length; i++) {
                                    
                        html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                        html_show += "<td><a href='javascript:;' onclick='export_file("+dataget[i].report_id+", 8);'>"+dataget[i].report_name+"</a></td>";
                        html_show += "<td>"+dataget[i].report_user+"</td>";
                        if(parseInt(dataget[i].report_nature) === 0){
                            html_show += "<td class='text-center'>Bình thường</td>";
                        }else{
                            html_show += "<td class='text-center'>Khẩn cấp</td>";
                        }
                        //alert(dataget[i].status);
                        if(parseInt(dataget[i].report_status) === 0){
                            html_show += "<td class='text-center'>Chưa gửi</td>";
                        }else if(parseInt(dataget[i].report_status) === 1){
                            html_show += "<td class='text-center'>Đã gửi</td>";
                        }
                        else if(parseInt(dataget[i].report_status) === 2){
                            html_show += "<td class='text-center'>Chuyển lại</td>";
                        }
                        else if(parseInt(dataget[i].report_status) === 3){
                            html_show += "<td class='text-center'>Đã duyệt</td>";
                        }
                        html_show += "<td class='text-center'><a href='javascript:;' onclick='download_attach_file("+dataget[i].report_id+" , 8);'>"+dataget[i].report_attach_name+"</a></td>";
                        html_show += "<td class='text-center'>";
                        if(check_Permission_Feature('5')){
                            if(parseInt(dataget[i].report_status) == 0){
                                html_show += "<button  class='btn btn-info btn-xs' onclick='openPopupSend("+dataget[i].report_id+",6)'><i class='glyphicon glyphicon-send'></i> Gửi</button> ";
                                if(check_Permission_Feature('3')){
                                    html_show += "<button  onclick='del_report("+dataget[i].report_id+",8);'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                                }
                            }else if(parseInt(dataget[i].report_status) == 1){
                                html_show += "<button  class='btn btn-primary btn-xs' > Chờ phê duyệt</button> ";
                            }else if(parseInt(dataget[i].report_status) == 2){
                                html_show += "<button onclick='loadPopupRevert("+dataget[i].report_id+", 8);' class='btn btn-warning btn-xs' > Trả lại</button> ";
                            }else {
                                html_show += "<button onclick='loadPopupRevert("+dataget[i].report_id+", 8);' class='btn btn-success btn-xs' > Đã phê duyệt </button> ";
                            }
                        }else if(check_Permission_Feature('3')){
                            html_show += "<button  onclick='del_report("+dataget[i].report_id+",8);'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                        }
                        html_show += "</td></tr>";
                    }                    
                            
                }
                else {
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }
                $('#dataNGNA').html(html_show);
            }, error: function(dataget) {

            }
        });
    };

    function insertHotroNGNA(objData){
        loading();
        //var objJson = JSON.stringify(objData);
        //console.log(objJson);
        //window.open('hoc-sinh-dan-toc-thieu-so/getData', '_blank');
        
        $.ajax({
            type: "POST",
            url:'/ho-so/lap-danh-sach/nguoi-nau-an/getData',
            data: objData,
            contentType: false,
            cache: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                // console.log(data);
                if (data['success'] != "" && data['success'] != undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    resetNGNA();
                    $('#txtNameDSNGNA').val('');
                    $('#txtNguoiLapNGNA').val('');
                    $('#txtNguoiKyNGNA').val('');
                    loaddataNGNA($('#drPagingNGNA').val());
                    $("#myModalLapDanhSach").modal("hide");
                    closeLoading();
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000,1);
                    closeLoading();
                }
            }, error: function(data) {
                closeLoading();
            }
        });
    }

    function validateNGNA(){
        var messageValidate = "";
        var schools_id = $('#sltSchool').val();
        var year = $('#sltYear').val();

        if (schools_id == "" || schools_id == 0) {
            messageValidate = "Vui lòng chọn trường!";
            return messageValidate;
        }
        if (year == "" || year == 0) {
            messageValidate = "Vui lòng chọn năm học!";
            return messageValidate;
        }

        return messageValidate;
    }

    function validatePopupNGNA(){
        var messageValidate = "";
        var reportName = $('#txtNameDSNGNA').val();
        var tennguoilap = $('#txtNguoiLapNGNA').val();
        var tennguoiky = $('#txtNguoiKyNGNA').val();

        if (reportName.trim() == "") {
            messageValidate = "Vui lòng nhập tên danh sách!";
            $('#txtNameDSNGNA').focus(); 
            return messageValidate;
        }else if (reportName.length > 200) {
            messageValidate = "Tên danh sách không được vượt quá 200 ký tự!";
            $('#txtNameDSNGNA').focus();
            $('#txtNameDSNGNA').val("");
            return messageValidate;
        }
        else{
            $('#txtNameDSNGNA').focusout();
            var specialChars = "#/|\\";

            for (var i = 0; i < reportName.length; i++) {
                if (specialChars.indexOf(reportName.charAt(i)) != -1) {
                    messageValidate = "Tên danh sách không được chứa ký tự #, /, |, \\!";
                    $('#txtNameDSNGNA').focus();
                    $('#txtNameDSNGNA').val("");
                    return messageValidate;
                }
            }
        }

        if (tennguoilap.trim() == "") {
            messageValidate = "Vui lòng nhập tên người lập!";
            $('#txtNguoiLapNGNA').focus(); 
            return messageValidate;
        }else if (tennguoilap.length > 200) {
            messageValidate = "Tên người lập không được vượt quá 200 ký tự!";
            $('#txtNguoiLapNGNA').focus();
            $('#txtNguoiLapNGNA').val("");
            return messageValidate;
        }
        else{
            $('#txtNguoiLapNGNA').focusout();
            var specialChars = "#/|\\";

            for (var i = 0; i < tennguoilap.length; i++) {
                if (specialChars.indexOf(tennguoilap.charAt(i)) != -1) {
                    messageValidate = "Tên người lập không được chứa ký tự #, /, |, \\!";
                    $('#txtNguoiLapNGNA').focus();
                    $('#txtNguoiLapNGNA').val("");
                    return messageValidate;
                }
            }
        }

        if (tennguoiky.trim() == "") {
            messageValidate = "Vui lòng nhập tên người ký!";
            $('#txtNguoiKyNGNA').focus(); 
            return messageValidate;
        }else if (tennguoiky.length > 200) {
            messageValidate = "Tên người ký không được vượt quá 200 ký tự!";
            $('#txtNguoiKyNGNA').focus();
            $('#txtNguoiKyNGNA').val("");
            return messageValidate;
        }
        else{
            $('#txtNguoiKyNGNA').focusout();
            var specialChars = "#/|\\";

            for (var i = 0; i < tennguoiky.length; i++) {
                if (specialChars.indexOf(tennguoiky.charAt(i)) != -1) {
                    messageValidate = "Tên người ký không được chứa ký tự #, /, |, \\!";
                    $('#txtNguoiKyNGNA').focus();
                    $('#txtNguoiKyNGNA').val("");
                    return messageValidate;
                }
            }
        }

        return messageValidate;
    }

    function resetNGNA(){
        $('#formngna')[0].reset();
    }

//Hỗ trợ học sinh khuyết tật---------------------------------------------------------------------------
    
    function loaddataHSKT(row) {
        
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit : row
        };
        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/hoc-sinh-khuyet-tat/load',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(datas) {
                SETUP_PAGING_NGHILC(datas, function () {
                    loaddataHSKT(row);
                });
                //console.log(datas);
                $('#dataHSKT').html("");
                var dataget = datas.data;
                
                if(dataget.length>0){
                    for (var i = 0; i < dataget.length; i++) {
                                    
                        html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                        html_show += "<td><a href='javascript:;' onclick='export_file("+dataget[i].report_id+", 6);'>"+dataget[i].report_name+"</a></td>";
                        html_show += "<td>"+dataget[i].report_user+"</td>";
                        if(parseInt(dataget[i].report_nature) === 0){
                            html_show += "<td class='text-center'>Bình thường</td>";
                        }else{
                            html_show += "<td class='text-center'>Khẩn cấp</td>";
                        }
                        //alert(dataget[i].status);
                        if(parseInt(dataget[i].report_status) === 0){
                            html_show += "<td class='text-center'>Chưa gửi</td>";
                        }else if(parseInt(dataget[i].report_status) === 1){
                            html_show += "<td class='text-center'>Đã gửi</td>";
                        }
                        else if(parseInt(dataget[i].report_status) === 2){
                            html_show += "<td class='text-center'>Chuyển lại</td>";
                        }
                        else if(parseInt(dataget[i].report_status) === 3){
                            html_show += "<td class='text-center'>Đã duyệt</td>";
                        }
                        html_show += "<td class='text-center'><a href='javascript:;' onclick='download_attach_file("+dataget[i].report_id+", 6);'>"+dataget[i].report_attach_name+"</a></td>";
                        html_show += "<td class='text-center'>";
                        if(check_Permission_Feature('5')){
                            if(parseInt(dataget[i].report_status) == 0){
                                html_show += "<button  class='btn btn-info btn-xs' onclick='openPopupSend("+dataget[i].report_id+",7)'><i class='glyphicon glyphicon-send'></i> Gửi</button> ";
                                if(check_Permission_Feature('3')){
                                    html_show += "<button  onclick='del_report("+dataget[i].report_id+",6);'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                                }
                            }else if(parseInt(dataget[i].report_status) == 1){
                                html_show += "<button  class='btn btn-primary btn-xs' > Chờ phê duyệt</button> ";
                            }else if(parseInt(dataget[i].report_status) == 2){
                                html_show += "<button onclick='loadPopupRevert("+dataget[i].report_id+", 6);' class='btn btn-warning btn-xs' > Trả lại</button> ";
                            }else {
                                html_show += "<button onclick='loadPopupRevert("+dataget[i].report_id+", 6);' class='btn btn-success btn-xs' > Đã phê duyệt </button> ";
                            }
                        }else if(check_Permission_Feature('3')){
                            html_show += "<button  onclick='del_report("+dataget[i].report_id+",6);'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                        }
                        html_show += "</td></tr>";
                    }                    
                            
                }
                else {
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }
                $('#dataHSKT').html(html_show);
            }, error: function(dataget) {

            }
        });
    };

    function insertHotroHSKT(objData){
        loading();
        //var objJson = JSON.stringify(objData);
        //console.log(objJson);
        //window.open('hoc-sinh-dan-toc-thieu-so/getData', '_blank');
        // console.log(objData);
        // for (var pair of objData.entries()) {
        //     console.log(pair[0]+ ', ' + pair[1]); 
        // }
        $.ajax({
            type: "POST",
            url:'/ho-so/lap-danh-sach/hoc-sinh-khuyet-tat/getData',
            data: objData,
            contentType: false,
            cache: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //console.log(data);
                if (data['success'] != "" && data['success'] != undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    resetHSKT();
                    $('#txtNameDSHSKT').val('');
                    $('#txtNguoiLapHSKT').val('');
                    $('#txtNguoiKyHSKT').val('');
                    loaddataHSKT($('#drPagingHSKT').val());
                    $("#myModalLapDanhSach").modal("hide");
                    closeLoading();
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000,1);
                    closeLoading();
                }
            }, error: function(data) {
                closeLoading();
            }
        });
    }

    function validateHSKT(){
        var messageValidate = "";
        var schools_id = $('#sltSchool').val();
        var year = $('#sltYear').val();

        if (schools_id == "" || schools_id == 0) {
            messageValidate = "Vui lòng chọn trường!";
            return messageValidate;
        }
        if (year == "" || year == 0) {
            messageValidate = "Vui lòng chọn năm học!";
            return messageValidate;
        }

        return messageValidate;
    }

    function validatePopupHSKT(){
        var messageValidate = "";
        var reportName = $('#txtNameDSHSKT').val();
        var tennguoilap = $('#txtNguoiLapHSKT').val();
        var tennguoiky = $('#txtNguoiKyHSKT').val();

        if (reportName.trim() == "") {
            messageValidate = "Vui lòng nhập tên danh sách!";
            $('#txtNameDSHSKT').focus(); 
            return messageValidate;
        }else if (reportName.length > 200) {
            messageValidate = "Tên danh sách không được vượt quá 200 ký tự!";
            $('#txtNameDSHSKT').focus();
            $('#txtNameDSHSKT').val("");
            return messageValidate;
        }
        else{
            $('#txtNameDSHSKT').focusout();
            var specialChars = "#/|\\";

            for (var i = 0; i < reportName.length; i++) {
                if (specialChars.indexOf(reportName.charAt(i)) != -1) {
                    messageValidate = "Tên danh sách không được chứa ký tự #, /, |, \\!";
                    $('#txtNameDSHSKT').focus();
                    $('#txtNameDSHSKT').val("");
                    return messageValidate;
                }
            }
        }

        if (tennguoilap.trim() == "") {
            messageValidate = "Vui lòng nhập tên người lập!";
            $('#txtNguoiLapHSKT').focus(); 
            return messageValidate;
        }else if (tennguoilap.length > 200) {
            messageValidate = "Tên người lập không được vượt quá 200 ký tự!";
            $('#txtNguoiLapHSKT').focus();
            $('#txtNguoiLapHSKT').val("");
            return messageValidate;
        }
        else{
            $('#txtNguoiLapHSKT').focusout();
            var specialChars = "#/|\\";

            for (var i = 0; i < tennguoilap.length; i++) {
                if (specialChars.indexOf(tennguoilap.charAt(i)) != -1) {
                    messageValidate = "Tên người lập không được chứa ký tự #, /, |, \\!";
                    $('#txtNguoiLapHSKT').focus();
                    $('#txtNguoiLapHSKT').val("");
                    return messageValidate;
                }
            }
        }

        if (tennguoiky.trim() == "") {
            messageValidate = "Vui lòng nhập tên người ký!";
            $('#txtNguoiKyHSKT').focus(); 
            return messageValidate;
        }else if (tennguoiky.length > 200) {
            messageValidate = "Tên người ký không được vượt quá 200 ký tự!";
            $('#txtNguoiKyHSKT').focus();
            $('#txtNguoiKyHSKT').val("");
            return messageValidate;
        }
        else{
            $('#txtNguoiKyHSKT').focusout();
            var specialChars = "#/|\\";

            for (var i = 0; i < tennguoiky.length; i++) {
                if (specialChars.indexOf(tennguoiky.charAt(i)) != -1) {
                    messageValidate = "Tên người ký không được chứa ký tự #, /, |, \\!";
                    $('#txtNguoiKyHSKT').focus();
                    $('#txtNguoiKyHSKT').val("");
                    return messageValidate;
                }
            }
        }

        return messageValidate;
    }

    function resetHSKT(){
        $('#formhskt')[0].reset();
    }

//Tổng hợp---------------------------------------------------------------------------
    
    function loaddataTongHop(row) {
        
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit : row
        };
        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/chinh-sach-uu-dai/load',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(datas) {
                SETUP_PAGING_NGHILC(datas, function () {
                    loaddataTongHop(row);
                });
                //console.log(datas);
                $('#dataTongHop').html("");
                var dataget = datas.data;
                
                if(dataget.length>0){
                    for (var i = 0; i < dataget.length; i++) {
                                    
                        html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                        html_show += "<td><a href='javascript:;' onclick='export_file("+dataget[i].report_id+", 7);'>"+dataget[i].report_name+"</a></td>";
                        html_show += "<td>"+dataget[i].report_user+"</td>";
                        if(parseInt(dataget[i].report_nature) === 0){
                            html_show += "<td class='text-center'>Bình thường</td>";
                        }else{
                            html_show += "<td class='text-center'>Khẩn cấp</td>";
                        }
                        //alert(dataget[i].status);
                        if(parseInt(dataget[i].report_status) === 0){
                            html_show += "<td class='text-center'>Chưa gửi</td>";
                        }else if(parseInt(dataget[i].report_status) === 1){
                            html_show += "<td class='text-center'>Đã gửi</td>";
                        }
                        else if(parseInt(dataget[i].report_status) === 2){
                            html_show += "<td class='text-center'>Chuyển lại</td>";
                        }
                        else if(parseInt(dataget[i].report_status) === 3){
                            html_show += "<td class='text-center'>Đã duyệt</td>";
                        }
                        html_show += "<td class='text-center'><a href='javascript:;' onclick='download_attach_file("+dataget[i].report_id+", 7);'>"+dataget[i].report_attach_name+"</a></td>";
                        html_show += "<td class='text-center'>";
                        if(check_Permission_Feature('5')){
                            if(parseInt(dataget[i].report_status) == 0){
                                html_show += "<button  class='btn btn-info btn-xs' onclick='openPopupSend("+dataget[i].report_id+",8)'><i class='glyphicon glyphicon-send'></i> Gửi</button> ";
                                if(check_Permission_Feature('3')){
                                    html_show += "<button  onclick='del_report("+dataget[i].report_id+",7);'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                                }
                            }else if(parseInt(dataget[i].report_status) == 1){
                                html_show += "<button  class='btn btn-primary btn-xs' > Chờ phê duyệt</button> ";
                            }else if(parseInt(dataget[i].report_status) == 2){
                                html_show += "<button onclick='loadPopupRevert("+dataget[i].report_id+", 7);' class='btn btn-warning btn-xs' > Trả lại</button> ";
                            }else {
                                html_show += "<button onclick='loadPopupRevert("+dataget[i].report_id+", 7);' class='btn btn-success btn-xs' > Đã phê duyệt </button> ";
                            }
                        }else if(check_Permission_Feature('3')){
                            html_show += "<button  onclick='del_report("+dataget[i].report_id+",7);'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                        }
                        html_show += "</td></tr>";
                    }                    
                            
                }
                else {
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }
                $('#dataTongHop').html(html_show);
            }, error: function(dataget) {

            }
        });
    };

    function insertTongHop(objData){
        loading();
        //var objJson = JSON.stringify(objData);
        //console.log(objJson);
        //window.open('hoc-sinh-dan-toc-thieu-so/getData', '_blank');
        
        $.ajax({
            type: "POST",
            url:'/ho-so/lap-danh-sach/chinh-sach-uu-dai/getData',
            data: objData,
            contentType: false,
            cache: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //console.log(data);
                if (data['success'] != "" && data['success'] != undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    resetTongHop();
                    $('#txtNameDSTongHop').val('');
                    $('#txtNguoiLapTongHop').val('');
                    $('#txtNguoiKyTongHop').val('');
                    loaddataTongHop($('#drPagingTongHop').val());
                    $("#myModalLapDanhSach").modal("hide");
                    closeLoading();
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000,1);
                    closeLoading();
                }
            }, error: function(data) {
                closeLoading();
            }
        });
    }

    function validateTongHop(){
        var messageValidate = "";
        var schools_id = $('#sltSchool').val();
        var year = $('#sltYear').val();

        if (schools_id == "" || schools_id == 0) {
            messageValidate = "Vui lòng chọn trường!";
            return messageValidate;
        }
        if (year == "" || year == 0) {
            messageValidate = "Vui lòng chọn năm học!";
            return messageValidate;
        }

        return messageValidate;
    }

    function validatePopupTongHop(){
        var messageValidate = "";
        var reportName = $('#txtNameDSTongHop').val();
        var tennguoilap = $('#txtNguoiLapTongHop').val();
        var tennguoiky = $('#txtNguoiKyTongHop').val();

        if (reportName.trim() == "") {
            messageValidate = "Vui lòng nhập tên danh sách!";
            $('#txtNameDSTongHop').focus(); 
            return messageValidate;
        }else if (reportName.length > 200) {
            messageValidate = "Tên danh sách không được vượt quá 200 ký tự!";
            $('#txtNameDSTongHop').focus();
            $('#txtNameDSTongHop').val("");
            return messageValidate;
        }
        else{
            $('#txtNameDSTongHop').focusout();
            var specialChars = "#/|\\";

            for (var i = 0; i < reportName.length; i++) {
                if (specialChars.indexOf(reportName.charAt(i)) != -1) {
                    messageValidate = "Tên danh sách không được chứa ký tự #, /, |, \\!";
                    $('#txtNameDSTongHop').focus();
                    $('#txtNameDSTongHop').val("");
                    return messageValidate;
                }
            }
        }

        if (tennguoilap.trim() == "") {
            messageValidate = "Vui lòng nhập tên người lập!";
            $('#txtNguoiLapTongHop').focus(); 
            return messageValidate;
        }else if (tennguoilap.length > 200) {
            messageValidate = "Tên người lập không được vượt quá 200 ký tự!";
            $('#txtNguoiLapTongHop').focus();
            $('#txtNguoiLapTongHop').val("");
            return messageValidate;
        }
        else{
            $('#txtNguoiLapTongHop').focusout();
            var specialChars = "#/|\\";

            for (var i = 0; i < tennguoilap.length; i++) {
                if (specialChars.indexOf(tennguoilap.charAt(i)) != -1) {
                    messageValidate = "Tên người lập không được chứa ký tự #, /, |, \\!";
                    $('#txtNguoiLapTongHop').focus();
                    $('#txtNguoiLapTongHop').val("");
                    return messageValidate;
                }
            }
        }

        if (tennguoiky.trim() == "") {
            messageValidate = "Vui lòng nhập tên người ký!";
            $('#txtNguoiKyTongHop').focus(); 
            return messageValidate;
        }else if (tennguoiky.length > 200) {
            messageValidate = "Tên người ký không được vượt quá 200 ký tự!";
            $('#txtNguoiKyTongHop').focus();
            $('#txtNguoiKyTongHop').val("");
            return messageValidate;
        }
        else{
            $('#txtNguoiKyTongHop').focusout();
            var specialChars = "#/|\\";

            for (var i = 0; i < tennguoiky.length; i++) {
                if (specialChars.indexOf(tennguoiky.charAt(i)) != -1) {
                    messageValidate = "Tên người ký không được chứa ký tự #, /, |, \\!";
                    $('#txtNguoiKyTongHop').focus();
                    $('#txtNguoiKyTongHop').val("");
                    return messageValidate;
                }
            }
        }

        return messageValidate;
    }

    function resetTongHop(){
        $('#formtonghop')[0].reset();
    }


//----------------------------------------------------------Danh sách hỗ trợ tổng hợp-----------------------------------------------------------
    var _year = '';
    function loaddataDanhSachTongHop(row) {

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
            YEAR: year
        };
        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/tong-hop-che-do-ho-tro/load',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(datas) {
                // console.log(year);

                SETUP_PAGING_NGHILC(datas, function () {
                    loaddataDanhSachTongHop(row);
                });
                
                $('#dataDanhsachTonghop').html("");
                var dataget = datas.data;
                
                if(dataget.length > 0){
                    for (var i = 0; i < dataget.length; i++) {
                                    
                        html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
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
                        if(parseInt(dataget[i].TRANGTHAI) == 0){
                            html_show += "<td><button class='btn btn-primary btn-xs' onclick='openPopupDuyetChedo("+dataget[i].profile_id+", "+dataget[i].qlhs_thcd_id+", "+number+")'> Chờ duyệt</button> ";
                        }else if(parseInt(dataget[i].TRANGTHAI) == 1){
                            html_show += "<td><button class='btn btn-success btn-xs' onclick='openPopupDuyetChedo("+dataget[i].profile_id+", "+dataget[i].qlhs_thcd_id+", "+number+")'> Đã duyệt </button> ";
                        }
                        html_show += "</td></tr>";
                    }                            
                }
                else {
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }
                $('#dataDanhsachTonghop').html(html_show);
            }, error: function(dataget) {

            }
        });
    };

    function loadComboboxHocky() {

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
                }else{
                    //$('#sltTruongGrid').html("<option value=''>-- Chưa có trường --</option>");
                    $('#sltYear').html("<option value=''>-- Chưa có học kỳ --</option>");
                }
            }, error: function(dataget) {

            }
        });
    };

    var _id = 0;
    var _idProfile = 0;

    function openPopupDuyetChedo(id, idTHCD, number){
        _id = idTHCD;
        _idProfile = id;
        id = id + '-' + number;

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
                    for (var i = 0; i < data['GROUP'].length; i++) {

                        for (var j = 0; j < data['SUBJECT'].length; j++) {
                            if (parseInt(data['GROUP'][i].group_id) == parseInt(data['SUBJECT'][j].subject_history_group_id)) {
                                html_show += "<tr>";
                                html_show += "<td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * 10))+"</td>";
                                html_show += "<td class='text-center'>";

                                // console.log(data['CHEDO'][0]['TRANGTHAIHK1']);
                                // console.log(data['CHEDO'][0]['TRANGTHAIHK2']);

                                if (data['CHEDO'][0]['TRANGTHAI'] == 1) {

                                    if ((parseInt(data['SUBJECT'][j].subject_id) === 19 
                                            || parseInt(data['SUBJECT'][j].subject_id) === 34
                                            || parseInt(data['SUBJECT'][j].subject_id) === 35
                                            || parseInt(data['SUBJECT'][j].subject_id) === 36
                                            || parseInt(data['SUBJECT'][j].subject_id) === 38
                                            || parseInt(data['SUBJECT'][j].subject_id) === 39
                                            || parseInt(data['SUBJECT'][j].subject_id) === 40
                                            || parseInt(data['SUBJECT'][j].subject_id) === 41) && parseInt(data['CHEDO'][0]['MGHP']) == 1) {
                                        html_show += "<input type='checkbox' name='choose' value='"+data['SUBJECT'][j].subject_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_id) === 24 
                                            || parseInt(data['SUBJECT'][j].subject_id) === 25) && parseInt(data['CHEDO'][0]['CPHT']) == 1) {
                                        html_show += "<input type='checkbox' name='choose' value='"+data['SUBJECT'][j].subject_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_id) === 19 
                                            || parseInt(data['SUBJECT'][j].subject_id) === 23
                                            || parseInt(data['SUBJECT'][j].subject_id) === 26
                                            || parseInt(data['SUBJECT'][j].subject_id) === 27
                                            || parseInt(data['SUBJECT'][j].subject_id) === 28) && parseInt(data['CHEDO'][0]['HTAT']) == 1) {
                                        html_show += "<input type='checkbox' name='choose' value='"+data['SUBJECT'][j].subject_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_id) === 46) && parseInt(data['CHEDO'][0]['HTBT_TA']) == 1) {
                                        html_show += "<input type='checkbox' name='choose' value='"+data['SUBJECT'][j].subject_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_id) === 48) && parseInt(data['CHEDO'][0]['HTBT_TO']) == 1) {
                                        html_show += "<input type='checkbox' name='choose' value='"+data['SUBJECT'][j].subject_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_id) === 66) && parseInt(data['CHEDO'][0]['HTBT_VHTT']) == 1) {
                                        html_show += "<input type='checkbox' name='choose' value='"+data['SUBJECT'][j].subject_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_id) === 47) && parseInt(data['CHEDO'][0]['HSKT_HB']) == 1) {
                                        html_show += "<input type='checkbox' name='choose' value='"+data['SUBJECT'][j].subject_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_id) === 50) && parseInt(data['CHEDO'][0]['HSKT_DDHT']) == 1) {
                                        html_show += "<input type='checkbox' name='choose' value='"+data['SUBJECT'][j].subject_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_id) === 49) && parseInt(data['CHEDO'][0]['HSDTTS']) == 1) {
                                        html_show += "<input type='checkbox' name='choose' value='"+data['SUBJECT'][j].subject_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_id) === 69) && parseInt(data['CHEDO'][0]['HTATHS']) == 1) {
                                        html_show += "<input type='checkbox' name='choose' value='"+data['SUBJECT'][j].subject_id+"' checked='checked'>";
                                    }
                                    else if ((parseInt(data['SUBJECT'][j].subject_id) === 70) && parseInt(data['CHEDO'][0]['HBHSDTNT']) == 1) {
                                        html_show += "<input type='checkbox' name='choose' value='"+data['SUBJECT'][j].subject_id+"' checked='checked'>";
                                    }
                                    else{
                                        html_show += "<input type='checkbox' name='choose' value='"+data['SUBJECT'][j].subject_id+"'>";
                                    }
                                }
                                else{
                                    html_show += "<input type='checkbox' name='choose' value='"+data['SUBJECT'][j].subject_id+"'>";
                                }
                                
                                html_show += "</td>";
                                
                                html_show += "<td>"+data['GROUP'][i].group_name+"</td>";
                                html_show += "<td>"+data['SUBJECT'][j].subject_name+"</td>";
                                html_show += "</tr>";
                            }
                        }
                    }
                }

                $('#dataDanhsachCheDo').html(html_show);
                $("#myModalApproved").modal("show");
            }, error: function(data) {
                closeLoading();
            }
        });
    }

    function approvedChedo(objData){
        var strData = 'ID' + _id + '-' + _year + '-' + 'IDPROFILE' + _idProfile + '-' + objData;
        // console.log(strData);
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
                        loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val());
                        $("#myModalLapDanhSachTHCD").modal("hide");
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

    function getProfileSubById(id, number){
        // console.log(id);
        id = id + '-' + number;

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

                var html_show = "";

                var groupId = 0;

                if (data !== null && data !== "") {
                    for (var i = 0; i < data['GROUP'].length; i++) {

                        html_show += '<div class="form-group" style="margin: 0;">';
                        html_show += '<div class="col-sm-12" style="padding-left: 0">';
                        html_show += '<label style="padding-top: 0px;" class="col-sm-4 control-label">'+data['GROUP'][i]['group_name']+'  :</label>';

                        for (var j = 0; j < data['SUBJECT'].length; j++) {
                            if (data['GROUP'][i]['group_id'] == data['SUBJECT'][j]['subject_history_group_id']) {
                                // if (data['GROUP'][i]['group_id'] == groupId) {
                                //     html_show += '<div class="form-group" style="margin: 0;">';
                                //     html_show += '<div class="col-sm-12" style="padding-left: 0">';
                                //     html_show += '<label style="padding-top: 0px;" class="col-sm-4 control-label">---</label>';
                                //     html_show += '<div class="col-sm-8">';
                                //     html_show += '<p>'+data['SUBJECT'][j]['subject_name']+'</p>';
                                //     html_show += '</div>';
                                // }
                                // else {
                                //     html_show += '<div class="form-group" style="margin: 0;">';
                                //     html_show += '<div class="col-sm-12" style="padding-left: 0">';
                                //     html_show += '<label style="padding-top: 0px;" class="col-sm-4 control-label">'+data['GROUP'][i]['group_name']+'  :</label>';
                                    
                                // }

                                html_show += '<div class="col-sm-8">';
                                html_show += '<p>'+data['SUBJECT'][j]['subject_name']+'</p>';
                                html_show += '</div>';
                            }
                        }
                        html_show += '</div></div>';

                        groupId = data['GROUP'][i]['group_id'];
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

        msg_warning = validateTHCD();

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

        $("#myModalLapDanhSachTHCD").modal("show");
    }

    function lapdanhsachDanhSachTongHop(objData) {

        var schools_id = $('#sltSchool').val();
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
                    loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val());
                    $("#myModalLapDanhSachTHCD").modal("hide");
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    // resetFormTHCD();
                    loaddataDanhSachTongHop($('#drPagingDanhsachtonghop').val());
                    utility.message("Thông báo",data['error'],null,3000,1);
                }
            }, error: function(data) {

            }
        });
    };

    function validateTHCD(){
        var messageValidate = "";
        var schools_id = $('#sltSchool').val();
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

    function validatePopupTongHopCheDo(){
        var messageValidate = "";
        var reportName = $('#txtNameDSTHCD').val();
        var tennguoilap = $('#txtNguoiLapTHCD').val();
        var tennguoiky = $('#txtNguoiKyTHCD').val();

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

        if (tennguoilap.trim() == "") {
            messageValidate = "Vui lòng nhập tên người lập!";
            $('#txtNguoiLapTHCD').focus(); 
            return messageValidate;
        }else if (tennguoilap.length > 200) {
            messageValidate = "Tên người lập không được vượt quá 200 ký tự!";
            $('#txtNguoiLapTHCD').focus();
            $('#txtNguoiLapTHCD').val("");
            return messageValidate;
        }
        else{
            $('#txtNguoiLapTHCD').focusout();
            var specialChars = "#/|\\";

            for (var i = 0; i < tennguoilap.length; i++) {
                if (specialChars.indexOf(tennguoilap.charAt(i)) != -1) {
                    messageValidate = "Tên người lập không được chứa ký tự #, /, |, \\!";
                    $('#txtNguoiLapTHCD').focus();
                    $('#txtNguoiLapTHCD').val("");
                    return messageValidate;
                }
            }
        }

        if (tennguoiky.trim() == "") {
            messageValidate = "Vui lòng nhập tên người ký!";
            $('#txtNguoiKyTHCD').focus(); 
            return messageValidate;
        }else if (tennguoiky.length > 200) {
            messageValidate = "Tên người ký không được vượt quá 200 ký tự!";
            $('#txtNguoiKyTHCD').focus();
            $('#txtNguoiKyTHCD').val("");
            return messageValidate;
        }
        else{
            $('#txtNguoiKyTHCD').focusout();
            var specialChars = "#/|\\";

            for (var i = 0; i < tennguoiky.length; i++) {
                if (specialChars.indexOf(tennguoiky.charAt(i)) != -1) {
                    messageValidate = "Tên người ký không được chứa ký tự #, /, |, \\!";
                    $('#txtNguoiKyTHCD').focus();
                    $('#txtNguoiKyTHCD').val("");
                    return messageValidate;
                }
            }
        }

        return messageValidate;
    }

    function resetFormTHCD(){
        $('#formtonghopchedo')[0].reset();
    }


//Export-----------------------------------------------------------------------------------------------------------------
    
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
        // alert(number + "-------------------");
        // alert(url_export + "-------------------");

        window.open(url_export + id, '_blank');
        // $.ajax({
        //     type: "GET",
        //     url: url_export + id,
        //     contentType: 'application/json; charset=utf-8',
        //     headers: {
        //         'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
        //     },
        //     success: function(dataget) {
        //         //$('#group_mghp').html(dataget);
        //         //console.log(dataget);
        //     }, error: function(dataget) {
        //     }
        // });
    }

    function del_report (id, number) {
        var url_del = '';
        if (number == 1) {
            url_del = '/ho-so/lap-danh-sach/mien-giam-hoc-phi/delete/';
        }
        if (number == 2) {
            url_del = '/ho-so/lap-danh-sach/chi-phi-hoc-tap/delete/';
        }
        if (number == 3) {
            url_del = '/ho-so/lap-danh-sach/ho-tro-an-trua-tre-em/delete/';
        }
        if (number == 4) {
            url_del = '/ho-so/lap-danh-sach/hoc-sinh-ban-tru/delete/';
        }
        if (number == 5) {
            url_del = '/ho-so/lap-danh-sach/hoc-sinh-dan-toc-thieu-so/delete/';
        }
        if (number == 6) {
            url_del = '/ho-so/lap-danh-sach/hoc-sinh-khuyet-tat/delete/';
        }
        if (number == 7) {
            url_del = '/ho-so/lap-danh-sach/chinh-sach-uu-dai/delete/';
        }
        if (number == 8) {
            url_del = '/ho-so/lap-danh-sach/nguoi-nau-an/delete/';
        }
        // alert(number + "-------------------");
        // alert(url_export + "-------------------");
        utility.confirm("Xóa bản ghi?", "Bạn có chắc chắn muốn xóa?", function () {
            insertUpdate(1);
            $.ajax({
                    type: "GET",
                    url: url_del + id,
                    success: function(dataget) {
                        //alert(dataget);
                        if(dataget.success != null || dataget.success != undefined){
                            $("#myModal").modal("hide");
                            utility.message("Thông báo",dataget.success,null,3000);
                            GET_INITIAL_NGHILC();
                            if (number == 1) {
                                loaddata($('#view-miengiamhocphi').val());
                            }
                            if (number == 2) {                        
                                loaddataAll($('#view-chiphihoctap').val(),'CPHT','dataChiPhiHocTap');
                            }
                            if (number == 3) {
                                loaddataAll($('#view-hotroantrua').val(),'HTAT','dataHoTroAnTrua');
                            }
                            if (number == 4) {
                                loaddataHTBT($('#drPagingHTBT').val());
                            }
                            if (number == 5) {
                                loaddataHSDTTS($('#drPagingHSDTTS').val());
                            }
                            if (number == 6) {
                                loaddataHSKT($('#drPagingHSKT').val());
                            }
                            if (number == 7) {
                                loaddataTongHop($('#drPagingTongHop').val());
                            }
                            if (number == 8) {
                                loaddataNGNA($('#drPagingNGNA').val());
                            }
                            
                        }else if(dataget.error != null || dataget.error != undefined){
                            utility.message("Thông báo",dataget.error,null,3000);
                        }                        
                    }, error: function(dataget) {
                }
            });
        });        
    }

    function download_attach_file (id, number) {
        var url_download = '';
        if (number == 1) {
            url_download = '/ho-so/lap-danh-sach/mien-giam-hoc-phi/download/';
        }
        if (number == 2) {
            url_download = '/ho-so/lap-danh-sach/chi-phi-hoc-tap/download/';
        }
        if (number == 3) {
            url_download = '/ho-so/lap-danh-sach/ho-tro-an-trua-tre-em/download/';
        }
        if (number == 4) {
            url_download = '/ho-so/lap-danh-sach/hoc-sinh-ban-tru/download/';
        }
        if (number == 5) {
            url_download = '/ho-so/lap-danh-sach/hoc-sinh-dan-toc-thieu-so/download/';
        }
        if (number == 6) {
            url_download = '/ho-so/lap-danh-sach/hoc-sinh-khuyet-tat/download/';
        }
        if (number == 7) {
            url_download = '/ho-so/lap-danh-sach/chinh-sach-uu-dai/download/';
        }
        if (number == 8) {
            url_download = '/ho-so/lap-danh-sach/nguoi-nau-an/download/';
        }
        // alert(number + "-------------------");
        // alert(url_export + "-------------------");
        window.open(url_download + id, '_blank');
        $.ajax({
            type: "GET",
            url: url_download + id,
            success: function(dataget) {
                // $('#group_mghp').html(dataget);
                // $('#group_mghp').html(dataget);
            }, error: function(dataget) {
            }
        });
    }

//----------------------------------------------------------Send Thẩm định--------------------------------------------------------------
    var report_id_send = 0;
    var report_type_send = "";

    function openPopupSend(id, number){

        report_id_send = id;

        if(parseInt(number)===1){
            report_type_send = 'MGHP';
            openPopupSendMGHP();
        }
        if(parseInt(number)===2){
            report_type_send = 'CPHT';
            openPopupSendCPHT();
        }
        if(parseInt(number)===3){
            report_type_send = 'HTAT';
            openPopupSendHTAT();
        }
        if(parseInt(number)===4){
            report_type_send = 'HSDTTS';
            openPopupSendHSDTTS();
        }
        if(parseInt(number)===5){
            report_type_send = 'HTBT';
            openPopupSendHTBT();
        }
        if(parseInt(number)===6){
            report_type_send = 'NGNA';
            openPopupSendNGNA();
        }
        if(parseInt(number)===7){
            report_type_send = 'HSKT';
            openPopupSendHSKT();
        }
        if(parseInt(number)===8){
            report_type_send = 'TONGHOP';
            openPopupSendTONGHOP();
        }

        if(parseInt(number)===20){
            report_type_send = 'TONGHOP';
            openPopupSendTHDSHT();
        }

        loadComboboxNguoi();
    }

    function loadComboboxNguoi(){
        $.ajax({
            type: "GET",
            url: '/ho-so/lap-danh-sach/nguoi-nau-an/getAllUser',
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

    function send() {
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
                    type: report_type_send,
                    id : report_id_send,
                    list_id_nguoinhan: list_id_nhan.toString(),
                    list_id_cc : lst_id_cc
                };
                // alert(JSON.stringify(o));
                $.ajax({
                    type: "POST",
                    url: '/ho-so/lap-danh-sach/send',
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
                            if(report_type_send == "MGHP"){
                                $("#myModalSendMGHP").modal("hide");
                                loaddata($('#view-miengiamhocphi').val());
                            }
                            if(report_type_send == "CPHT"){
                                $("#myModalSendCPHT").modal("hide");
                                loaddataAll($('#view-chiphihoctap').val(),'CPHT','dataChiPhiHocTap');
                            }
                            if(report_type_send == "HTAT"){
                                $("#myModalSendHTAT").modal("hide");
                                loaddataAll($('#view-hotroantrua').val(),'HTAT','dataHoTroAnTrua');
                            }
                            if(report_type_send == "HSDTTS"){
                                $("#myModalSendHSDTTS").modal("hide");
                                loaddataHSDTTS($('#drPagingHSDTTS').val());
                            }
                            if(report_type_send == "HTBT"){
                                $("#myModalSendHTBT").modal("hide");
                                loaddataHTBT($('#drPagingHTBT').val());
                            }
                            if(report_type_send == "NGNA"){
                                $("#myModalSendNGNA").modal("hide");
                                loaddataNGNA($('#drPagingNGNA').val());
                            }
                            if(report_type_send == "HSKT"){
                                $("#myModalSendHSKT").modal("hide");
                                loaddataHSKT($('#drPagingHSKT').val());
                            }
                            if(report_type_send == "TONGHOP"){
                                $("#myModalSendTONGHOP").modal("hide");
                                loaddataTongHop($('#drPagingTongHop').val());
                            }

                            report_id_send = 0;
                            report_type_send = "";
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


    var listIDSend = "";
    function sendTonghop() {
        var list_id_nhan = $('#drNguoinhan').val();
        // alert(list_id_nhan);
        var list_id_cc = $('#drCC').val();
        // console.log(listIDSend + "...ABC");
        var lst_id_cc = "";
        if (list_id_nhan !== null && list_id_nhan !== "") {
            utility.confirm("Gửi danh sách?", "Bạn có chắc chắn muốn gửi?", function () {
                if (list_id_cc !== null && list_id_cc !== "") {
                    lst_id_cc = list_id_cc.toString();
                }
                var o = {
                    id : listIDSend,
                    list_id_nguoinhan: list_id_nhan.toString(),
                    list_id_cc : lst_id_cc
                };
                // alert(JSON.stringify(o));
                $.ajax({
                    type: "POST",
                    url: '/ho-so/lap-danh-sach/sendTonghop',
                    data: JSON.stringify(o),
                    // dataType: 'json',
                    contentType: 'application/json; charset=utf-8',
                     headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                    },
                        success: function(dataget) {
                            if(dataget.success != null || dataget.success != undefined){
                                utility.message("Thông báo",dataget.success,null,3000)

                                GET_INITIAL_NGHILC();
                                
                                loadTonghophoso($('#view-tonghopdanhsach').val());

                                listIDSend = "";
                                report_type_send = "";
                                $("#myModalSendTHDSHT").modal("hide");
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

    function deleteTonghop(listId) {
        console.log(listId + "...ABC");
        
            utility.confirm("Xóa danh sách?", "Bạn có chắc chắn muốn xóa danh sách?", function () {
                
                var o = {
                    id : listId
                };
                
                $.ajax({
                    type: "POST",
                    url: '/ho-so/lap-danh-sach/deleteTonghop',
                    data: JSON.stringify(o),
                    // dataType: 'json',
                    contentType: 'application/json; charset=utf-8',
                     headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                    },
                        success: function(dataget) {
                            console.log(dataget);
                            if(dataget.success != null || dataget.success != undefined){
                                utility.message("Thông báo",dataget.success,null,3000);

                                GET_INITIAL_NGHILC();
                                
                                loadTonghophoso($('#view-tonghopdanhsach').val());
                            }else if(dataget.error != null || dataget.error != undefined){
                                utility.message("Thông báo",dataget.error,null,3000);
                            }
                        }, error: function(dataget) {
                    }
                });
            });       
    }

//--------------------------------------------------Load, download dữ liệu danh sách trả về hoặc thẩm định-----------------------------------------------------------
    function loadPopupRevert(report_id_revert, number){
        
        $.ajax({
            type: "GET",
            url: '/ho-so/lap-danh-sach/nguoi-nau-an/getContent/' + report_id_revert,
            success: function(data) {
                // console.log(data);
                
                if (data[0].report_note !== null && data[0].report_note !== "") {
                    $('#note-content').html(data[0].report_note);
                }
                else {
                    $('#note-content').html('Không có ý kiến');
                }

                if (data[0].report_file_revert !== null && data[0].report_file_revert !== "") {
                    $('#file-attach').html('&nbsp;&nbsp;<i class="fa fa-file-excel-o"></i>  <a style="font-style: italic;"> '+data[0].report_file_revert+'</a> ( <a href="#" onclick="download_file_revert('+report_id_revert+')" ><i class="fa fa-download"></i> Tải về </a>)');
                }
                else {
                    $('#file-attach').html('Không có file đính kèm');
                }

                $('html, body').animate({ scrollTop: 0 }, 'slow');

                if(parseInt(number)===1){
                    $("#myModalRevertMGHP").modal("show");
                }
                if(parseInt(number)===2){
                    $("#myModalRevertCPHT").modal("show");
                }
                if(parseInt(number)===3){
                    $("#myModalRevertHTAT").modal("show");
                }
                if(parseInt(number)===5){
                    $("#myModalRevertHSDTTS").modal("show");
                }
                if(parseInt(number)===4){
                    $("#myModalRevertHTBT").modal("show");
                }
                if(parseInt(number)===8){
                    $("#myModalRevertNGNA").modal("show");
                }
                if(parseInt(number)===6){
                    $("#myModalRevertHSKT").modal("show");
                }
                if(parseInt(number)===7){
                    $("#myModalRevertTONGHOP").modal("show");
                }
                
            }, error: function(data) {
            }
        });
    }

    function download_file_revert(id_report){
        window.open('/ho-so/lap-danh-sach/nguoi-nau-an/download_file_revert/' + id_report, '_blank');
    }

//--------------------------------------------------Load, download dữ liệu danh sách trả về hoặc thẩm định-----------------------------------------------------------
    function loadTonghophoso(row) {

        var msg_warning = "";

        msg_warning = validateTHCD();

        // alert(msg_warning);

        if (msg_warning !== null && msg_warning !== "") {
            utility.messagehide("messageValidate", msg_warning, 1, 5000);
            return;
        }

        var schools_id = $('#sltSchool').val();
        var year = $('#sltYear').val();

        
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit: row,
            SCHOOLID: schools_id,
            YEAR: year
        };
        $.ajax({
            type: "POST",
            url: '/ho-so/lap-danh-sach/tong-hop-ho-so/load',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(datas) {
                console.log(datas);

                SETUP_PAGING_NGHILC(datas, function () {
                    loadTonghophoso(row);
                });
                
                $('#dataTongHopHoSo').html("");
                // var dataget = datas.result;
                var dataget = datas.data;
                var status = 0;
                var strID = "";
                // console.log(dataget);

                if (dataget.length > 0) {
                    for (var i = 0; i < dataget.length; i++) {

                        // var MGHPName = "-";
                        // var CPHTName = "-";
                        // var HTATName = "-";
                        // var HTBTName = "-";
                        // var HSKTName = "-";
                        // var NGNAName = "-";
                        // var HSDTTSName = "-";
                        // var HTATHSName = "-";
                        // var HBHSDTNTName = "-";


                        // var MGHPID = 0;
                        // var CPHTID = 0;
                        // var HTATID = 0;
                        // var HTBTID = 0;
                        // var HSKTID = 0;
                        // var NGNAID = 0;
                        // var HSDTTSID = 0;
                        // var HTATHSID = 0;
                        // var HBHSDTNTID = 0;

                        // var statusMGHP = 0;
                        // var statusCPHT = 0;
                        // var statusHTAT = 0;
                        // var statusHTBT = 0;
                        // var statusHSKT = 0;
                        // var statusNGNA = 0;
                        // var statusHSDTTS = 0;
                        // var statusHTATHS = 0;
                        // var statusHBHSDTNT = 0;

                        html_show += "<tr>";

                        html_show += "<td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";

                        // for (var j = 0; j < 8; j++) {

                        //     if (dataget[i].length == j) { break; }

                        //     if (dataget[i][j].report == "MGHP") {
                        //         MGHPName = dataget[i][j].report_name;
                        //         MGHPID = dataget[i][j].report_id;
                        //         statusMGHP = dataget[i][j].report_status;
                        //     }

                        //     if (dataget[i][j].report == "CPHT") {
                        //         CPHTName = dataget[i][j].report_name;
                        //         CPHTID = dataget[i][j].report_id;
                        //         statusCPHT = dataget[i][j].report_status;
                        //     }                           
                            
                        //     if (dataget[i][j].report == "HTAT") {
                        //         HTATName = dataget[i][j].report_name;
                        //         HTATID = dataget[i][j].report_id;
                        //         statusHTAT = dataget[i][j].report_status;
                        //     }
                            
                        //     if (dataget[i][j].report == "HTBT") {
                        //         HTBTName = dataget[i][j].report_name;
                        //         HTBTID = dataget[i][j].report_id;
                        //         statusHTBT = dataget[i][j].report_status;
                        //     }
                            
                        //     if (dataget[i][j].report == "HSKT") {
                        //         HSKTName = dataget[i][j].report_name;
                        //         HSKTID = dataget[i][j].report_id;
                        //         statusHSKT = dataget[i][j].report_status;
                        //     }
                            
                        //     if (dataget[i][j].report == "NGNA") {
                        //         NGNAName = dataget[i][j].report_name;
                        //         NGNAID = dataget[i][j].report_id;
                        //         statusNGNA = dataget[i][j].report_status;
                        //     }
                            
                        //     if (dataget[i][j].report == "HTATHS") {
                        //         HTATHSName = dataget[i][j].report_name;
                        //         HTATHSID = dataget[i][j].report_id;
                        //         statusHTATHS = dataget[i][j].report_status;
                        //     }
                            
                        //     if (dataget[i][j].report == "HSDTTS") {
                        //         HSDTTSName = dataget[i][j].report_name;
                        //         HSDTTSID = dataget[i][j].report_id;
                        //         statusHSDTTS = dataget[i][j].report_status;
                        //     }
                            
                        //     if (dataget[i][j].report == "HBHSDTNT") {
                        //         HBHSDTNTName = dataget[i][j].report_name;
                        //         HBHSDTNTID = dataget[i][j].report_id;
                        //         statusHBHSDTNT = dataget[i][j].report_status;
                        //     }
                        // }

                        html_show += "<td><a href='javascript:;' onclick='export_file("+dataget[i].MGHP_id+", 1);'>"+ConvertString(dataget[i].MGHP_name)+"</a></td>"; 
                        html_show += "<td><a href='javascript:;' onclick='export_file("+dataget[i].CPHT_id+", 2);'>"+ConvertString(dataget[i].CPHT_name)+"</a></td>"; 
                        html_show += "<td><a href='javascript:;' onclick='export_file("+dataget[i].HTAT_id+", 3);'>"+ConvertString(dataget[i].HTAT_name)+"</a></td>"; 
                        html_show += "<td><a href='javascript:;' onclick='export_file("+dataget[i].HTBT_id+", 4);'>"+ConvertString(dataget[i].HTBT_name)+"</a></td>"; 
                        html_show += "<td><a href='javascript:;' onclick='export_file("+dataget[i].HSKT_id+", 6);'>"+ConvertString(dataget[i].HSKT_name)+"</a></td>"; 
                        html_show += "<td><a href='javascript:;' onclick='export_file("+dataget[i].NGNA_id+", 8);'>"+ConvertString(dataget[i].NGNA_name)+"</a></td>";
                        html_show += "<td><a href='javascript:;' onclick='export_file("+dataget[i].HTATHS_id+", 9);'>"+ConvertString(dataget[i].HTATHS_name)+"</a></td>"; 
                        html_show += "<td><a href='javascript:;' onclick='export_file("+dataget[i].HSDTTS_id+", 5);'>"+ConvertString(dataget[i].HSDTTS_name)+"</a></td>"; 
                        html_show += "<td><a href='javascript:;' onclick='export_file("+dataget[i].HBHSDTNT_id+", 10);'>"+ConvertString(dataget[i].HBHSDTNT_name)+"</a></td>"; 

                        html_show += "<td class='text-center'>";
                            if(check_Permission_Feature('5')){
                                // if(parseInt(dataget[i].MGHP_status) === 0 && parseInt(dataget[i].CPHT_status) === 0 && parseInt(dataget[i].HTAT_status) === 0 
                                //     && parseInt(dataget[i].HTBT_status) === 0 && parseInt(dataget[i].HSKT_status) === 0 && parseInt(dataget[i].NGNA_status) === 0 
                                //     && parseInt(dataget[i].HTATHS_status) === 0 && parseInt(dataget[i].HSDTTS_status) === 0 && parseInt(dataget[i].HBHSDTNT_status) === 0){
                                    // html_show += "<button data-id='' class='btn btn-info btn-xs btnOpenPopupSendDSTH' onclick='openPopupSendTHDSHT(\""+MGHPID+','+'MGHP'+"-"+CPHTID+','+'CPHT'+"-"+HTATID+','+'HTAT'+"-"+HTBTID+','+'HTBT'+"-"+HSKTID+','+'HSKT'+"-"+NGNAID+','+'NGNA'+"-"+HTATHSID+','+'HTATHS'+"-"+HSDTTSID+','+'HSDTTS'+"-"+HBHSDTNTID+','+'HBHSDTNT'+"\");' ><i class='glyphicon glyphicon-send'></i> Gửi</button> ";
                                    if(check_Permission_Feature('3')){
                                        html_show += "<button data='' onclick='deleteTonghop(\""+dataget[i].MGHP_id+'-'+dataget[i].CPHT_id+'-'+dataget[i].HTAT_id+'-'+dataget[i].HTBT_id+'-'+dataget[i].HSKT_id+'-'+dataget[i].NGNA_id+'-'+dataget[i].HTATHS_id+'-'+dataget[i].HSDTTS_id+'-'+dataget[i].HBHSDTNT_id+"\");' class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                                    }
                                // }else if(parseInt(dataget[i].MGHP_status) === 1 && parseInt(dataget[i].CPHT_status) === 1 && parseInt(dataget[i].HTAT_status) === 1 
                                //     && parseInt(dataget[i].HTBT_status) === 1 && parseInt(dataget[i].HSKT_status) === 1 && parseInt(dataget[i].NGNA_status) === 1 
                                //     && parseInt(dataget[i].HTATHS_status) === 1 && parseInt(dataget[i].HSDTTS_status) === 1 && parseInt(dataget[i].HBHSDTNT_status) === 1){
                                //     html_show += "<button  class='btn btn-primary btn-xs' > Chờ phê duyệt</button> ";
                                // }else if(parseInt(dataget[i].MGHP_status) === 2 && parseInt(dataget[i].CPHT_status) === 2 && parseInt(dataget[i].HTAT_status) === 2 
                                //     && parseInt(dataget[i].HTBT_status) === 2 && parseInt(dataget[i].HSKT_status) === 2 && parseInt(dataget[i].NGNA_status) === 2 
                                //     && parseInt(dataget[i].HTATHS_status) === 2 && parseInt(dataget[i].HSDTTS_status) === 2 && parseInt(dataget[i].HBHSDTNT_status) === 2){
                                //     html_show += "<button onclick='loadPopupRevert();' class='btn btn-warning btn-xs' > Trả lại</button> ";
                                // }else {
                                //     html_show += "<button onclick='loadPopupRevert();' class='btn btn-success btn-xs' > Đã phê duyệt </button> ";
                                // }
                            }else if(check_Permission_Feature('3')){
                                html_show += "<button  onclick='del_report();'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                            }
                        html_show += "</td>";
                        html_show += "</tr>";
                    }
                }
                else {
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }

                $('#dataTongHopHoSo').html(html_show);
            }, error: function(dataget) {

            }
        });
    };