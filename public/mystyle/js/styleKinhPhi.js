$(function () {
    //alert(1);
    var insert = true;
    $('#ex-insert-kpdoituong').html('');
    insertUpdate(1);
    permission(function(){
        var html_view  = '<b> Quản lý kinh phí </b> / Cập nhật mức hỗ trợ theo đối tượng';
        
        if(check_Permission_Feature('1')){
            html_view += '<a style="margin-left: 10px" id="btnInsertKinhPhiDoiTuong" onclick="btnInsertKinhPhiDoiTuong();"  class=" btn btn-success btn-xs pull-right"  ><i class="glyphicon glyphicon-plus"></i> Tạo mới </a > ';
        }
        // if(check_Permission_Feature('4')){
        //     html_view += '<a class="btn btn-success btn-xs pull-right"  href="#"><i class="glyphicon glyphicon-print"></i> Xuất excel</a>';
        // }
        $('#ex-insert-kpdoituong').html(html_view);
    });
    $('#datepicker1').datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true
    });
    // $('#datepicker2').datepicker({
    //   format: 'dd-mm-yyyy',
    //   autoclose: true
    // });
    delBanGhi = function (id) {
        utility.confirm("Xóa bản ghi?", "Bạn có chắc chắn muốn xóa?", function () {
            insertUpdate(1);
            $.ajax({
                    type: "GET",
                    url: '/kinh-phi/muc-ho-tro-doi-tuong/delId/'+id,
                    success: function(dataget) {
                        if(dataget.success != null || dataget.success != undefined){
                        $("#myModal").modal("hide");
                        utility.message("Thông báo",dataget.success,null,3000)
                        GET_INITIAL_NGHILC();
                        loadKinhPhiDoiTuong($('select#viewTableDT').val());
                    }else if(dataget.error != null || dataget.error != undefined){
                        //$("#myModal").modal("hide");
                        utility.message("Thông báo",dataget.error,null,5000,1)
                        //insertUpdate(1);
                        //loadKinhPhiDoiTuong($('select#viewTableDT').val()); 
                    }
                        
                    }, error: function(dataget) {
                }
            });
        });
        
    }
    updateBanGhi = function (id) {
        //alert(check_Permission_Feature('3'));
        $.ajax({
                type: "GET",
                url: '/kinh-phi/muc-ho-tro-doi-tuong/getId/'+id,
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
                        if(dataget[0].start_date != null){
                            //$('#datepicker1').val('');
                            $('#datepicker1').datepicker('setDate', new Date(dataget[0].start_date));    
                        }else{
                            $('#datepicker1').val('');
                        }
                        if(dataget[0].end_date != null){
                            //$('#datepicker2').val('');
                            // $('#datepicker2').datepicker('setDate', new Date(dataget[0].end_date));
                        }else{
                            // $('#datepicker2').val('');   
                        }

                        $('#datepicker1').attr('disabled', 'disabled');
                        //$('html,body').scrollTop(0);
                        $('html, body').animate({ scrollTop: 0 }, "slow");
                    }
                }, error: function(dataget) {
                }
            });
    }
    $('select#viewTableDT').change(function() {
       GET_INITIAL_NGHILC();
       loadKinhPhiDoiTuong($(this).val(),$('#txtSearchDT').val());
    });
    autocompleteSearch("txtSearchDT");
    //$('a#btnInsertKinhPhiDoiTuong').click(function(){
    btnInsertKinhPhiDoiTuong = function(){

        $('#btnSaveKinhPhiDoiTuong').button('reset');
        $('#datepicker1').removeAttr('disabled');
        insertUpdate(0);
        insert=true;
        $("#btnResetKinhPhiDoiTuong").show();
        $("#btnSaveKinhPhiDoiTuong").html('<i class="glyphicon glyphicon-plus-sign"></i> Lưu');
    };
    $('button#btnCancelKinhPhiDoiTuong').click(function(){
        
        insertUpdate(1);
    });
    loadComboxTruongHoc();
    loadComboxDoiTuong();
    loadKinhPhiDoiTuong($('select#viewTableDT').val(),$('#txtSearchDT').val());
    $('button#btnSaveKinhPhiDoiTuong').click(function(){
        // alert($('#sltTruongDt').val());
        if($('#sltTruongDt').val() !== '' && $('#sltTruongDt').val() > 0){
            if($('#sltSubject').val() !== '' && $('#sltSubject').val() > 0){
                if($('#txtMoney1').val() !== ''){
                    if($('#datepicker1').val() !== ''){
                        $('#btnSaveKinhPhiDoiTuong').button('loading');
                        // if($('#datepicker2').val() !== ''){
                            var v_start = $('#datepicker1').val();
                            // var v_end = $('#datepicker2').val();

                            var v_startDate = v_start.substring(0, 2);
                            var v_startMonth = v_start.substring(3, 5);
                            var v_startYear = v_start.substring(6, v_start.length);

                            // var v_endDate = v_end.substring(0, 2);
                            // var v_endMonth = v_end.substring(3, 5);
                            // var v_endYear = v_end.substring(6, v_end.length);

                            // if ((v_startYear > v_endYear)
                            //     || (v_startYear == v_endYear && v_startMonth > v_endMonth)
                            //     || (v_startYear == v_endYear && v_startMonth == v_endMonth && v_startDate > v_endDate)) {
                            //     utility.message("Thông báo","Ngày hiệu lực không được lớn hơn ngày hết hiệu lực!", null, 5000);
                            // }
                            // else {
                                var temp = {
                                    "id": $('#txtIdKinhPhi').val(),
                                    "idTruong": $('#sltTruongDt').val(),
                                    "code": $('#txtCodeKinhPhi1').val(),
                                    "idDoiTuong": $('#sltSubject').val(),
                                    "money": $('#txtMoney1').val(),
                                    "startDate": $('#datepicker1').val()
                                };
                                    // "endDate": $('#datepicker2').val()

                                if(insert){
                                    insertKinhPhiDoiTuong(temp);
                                }else{
                                    updateKinhPhiDoiTuong(temp);
                                }
                            // }
                        // }else{
                        //     utility.message("Thông báo","Xin mời nhập ngày hết hiệu lực",null,3000)
                        //     $('#datepicker2').focus();
                        // }
                    }else{
                        utility.message("Thông báo","Xin mời nhập ngày hiệu lực",null,3000)
                        $('#datepicker1').focus();
                    }
                }else{
                    utility.message("Thông báo","Xin mời nhập số tiền",null,3000)
                    $('#txtMoney1').focus();
                }
            }else{
                utility.message("Thông báo","Xin mời chọn đối tượng",null,3000)
                $('#sltSubject').focus();
            }
        }else{
            utility.message("Thông báo","Xin mời chọn trường",null,3000)
            $('#sltTruongDt').focus();
        }
        
    });
    
 //    var t = $('#example1').DataTable({
 //      "paging": true,
 //      "language": {
 //         "lengthMenu":  "Hiển thị _MENU_ bản ghi",
 //         "info": "Hiển thị _START_ đến _END_ của _TOTAL_ bản ghi" ,
 //          "paginate": {
 //                       "first": "First",
 //                       "last": "Last",
 //                       "next": "Trang sau",
 //                       "previous": "Trang trước"
 //           },"emptyTable": "Không có dữ liệu"
 //      },
 //      "processing": true,
 //      "lengthMenu": [[1, 2, 3, -1], [1, 2, 3, "All"]],
 //      "lengthChange": true,
 //      "searching": false,
 //      "ordering": true,
 //      "info": true,
 //      //"ajax":'load',
 //      "ajax": {
 //            // "url": "load",
 //            // "type": 'POST'
 //            "type": "POST",
 //            "url": 'load',
 //            "contentType": 'application/json; charset=utf-8',
 //            "headers": {
 //                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
 //            }
 //        },
 //        "columns": [
 //            { "data": "id" },
 //            { "data": "code" },
 //            { "data": "subject_name" },
 //            { "data": "money", render: $.fn.dataTable.render.number( '.', ',', 0, '' ) },
 //            { "data": "start_date",render:function(data){
 //                    if (data === null || data === "-") {
 //                        return "-";
 //                    }
 //                    var currentTime = new Date(data);
 //                    return moment(data).format('DD-MM-YYYY'); 
 //                } },
 //            { "data": "end_date",render:function(data){
 //                    if (data === null || data === "-") {
 //                        return "-";
 //                    }
 //                    var currentTime = new Date(data);
 //                    return moment(data).format('DD-MM-YYYY'); 
 //                }},
 //            { "data": "updated_at",render:function(data){
 //                    if (data === null || data === "-") {
 //                        return "-";
 //                    }
 //                    var currentTime = new Date(data);
 //                    return moment(data).format('DD-MM-YYYY HH:MM:SS'); 
 //                }},
 //            { "data": "username" },
 //            {
 //                "data": null,
 //                "className": "center",
 //                "defaultContent": '<a href="" class="btn btn-info btn-xs editor_edit"><i class="glyphicon glyphicon-pencil"></i> Sửa</a> <a href="" class="btn btn-danger btn-xs editor_remove"><i class="glyphicon glyphicon-remove"></i> Xóa</a>'
 //            }
 //        ],
 //        // "columns": [
 //        //     { "data": "data.code" },
 //        //     { "data": "data.subject_name" },
 //        //     { "data": "data.money" },
 //        //     { "data": "data.start_date" },
 //        //     { "data": "data.end_date" },
 //        //     { "data": "data.updated_at" }
 //        //     { "data": "data.username" }
 //        // ],
 //        // "select": true,
       
 //      "autoWidth": false
 //    });
 // t.on( 'order.dt search.dt', function () {
 //        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
 //            cell.innerHTML = i+1;
 //        } );
 //    } ).draw();
  });
function loadComboxTruongHoc(idchoise = null) {
            $.ajax({
                type: "GET",
                url: '/danh-muc/load/truong-hoc',
                success: function(data) {
                    var dataget = data.truong;
                    var datakhoi = data.khoi;
                    // <optgroup label="Cats">
                    $('#sltTruongDt').html("");
                    var html_show = "";
                    if(datakhoi.length >0){
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
                        $('#sltTruongDt').html(html_show);
                    }else{
                        $('#sltTruongDt').html("<option value=''>-- Chưa có trường --</option>");
                    }
                }, error: function(dataget) {
                }
            });
        };
    function loadComboxDoiTuong() {
            $.ajax({
                type: "GET",
                url: '/kinh-phi/muc-ho-tro-doi-tuong/load-nhom-doi-tuong',
                success: function(dataget) {
                    $('#sltSubject').html("");
                    var html_show = "";
                    if(dataget.length >0){
                      //  $.fn.dataTable.render.number( '.', ',', 0, '' ) 
                        html_show += "<option value=''>-- Chọn nhóm đối tượng --</option>";
                        for (var i = dataget.length - 1; i >= 0; i--) {
                            html_show += "<option value='"+dataget[i].group_id+"'>"+dataget[i].group_name+"</option>";
                        }
                        $('#sltSubject').html(html_show);
                    }else{
                        $('#sltSubject').html("<option value=''>-- Chưa có nhóm đối tượng --</option>");
                    }
                }, error: function(dataget) {
                }
            });
        };
var CODE_FEATURES ;
function permission(callback) {
            $.ajax({
                type: "GET",
                url: '/kinh-phi/muc-ho-tro-doi-tuong/permission/info',
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
function loadKinhPhiDoiTuong(row,key='') {

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
                url: '/kinh-phi/muc-ho-tro-doi-tuong/load',
                data: JSON.stringify(o),
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {

                    SETUP_PAGING_NGHILC(dataget, function () {
                        loadKinhPhiDoiTuong(row,key);
                    });
                    $('#dataKinhPhiDoiTuong').html("");
                    data = dataget.data;
                    //permission = dataget.permission;
                    if(data.length>0){
                        for (var i = 0; i < data.length; i++) {

                            html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            // html_show += "<td>"+data[i].code+"</td>";
                            html_show += "<td>"+data[i].schools_name+"</td>";
                            html_show += "<td>"+data[i].group_name+"</td>";
                            html_show += "<td>"+(data[i].money).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")+"</td>";
                            html_show += "<td>"+formatDates(data[i].start_date)+"</td>";
                            html_show += "<td>"+formatDates(data[i].end_date)+"</td>";
                            html_show += "<td>"+formatDateTimes(data[i].updated_at)+"</td>";
                            html_show += "<td>"+data[i].username+"</td>";
                            html_show += "<td class='text-center'>";
                            if(check_Permission_Feature('2')){
                                html_show += "<button data='"+data[i].id+"' onclick='updateBanGhi("+data[i].id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Sửa</button>";
                            }
                            if(check_Permission_Feature('3')){
                                html_show += "<button  onclick='delBanGhi("+data[i].id+");' data='"+data[i].id+"' class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                            }
                            html_show += "</td></tr>";
                        }
                        
                    } else {
                        html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                    }
                    $('#dataKinhPhiDoiTuong').html(html_show);
                }, error: function(dataget) {

                }
            });
        };

        function insertKinhPhiDoiTuong(temp) {
            //console.log(temp);
            $.ajax({
                type: "POST",
                url:'/kinh-phi/muc-ho-tro-doi-tuong/insert',
                data: JSON.stringify(temp),
                // dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                // contentType: false,
                cache: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {
                    // console.log(dataget);
                    if(dataget.success != null || dataget.success != undefined){
                        $("#myModal").modal("hide");
                        utility.message("Thông báo",dataget.success,null,3000)
                        insertUpdate(1);
                        GET_INITIAL_NGHILC();
                        loadKinhPhiDoiTuong($('select#viewTableDT').val());
                        $('#btnSaveKinhPhiDoiTuong').button('reset');
                        $('#datepicker1').removeAttr('disabled');
                    }else if(dataget.error != null || dataget.error != undefined){
                        //$("#myModal").modal("hide");
                        utility.message("Thông báo",dataget.error,null,5000,1)
                        $('#btnSaveKinhPhiDoiTuong').button('reset');
                        $('#datepicker1').removeAttr('disabled');
                        //insertUpdate(1);
                        //loadKinhPhiDoiTuong($('select#viewTableDT').val()); 
                    }
                   // utility.message("Thông báo","Lưu bản ghi thành công",null,5000)
                    
                           
                }, error: function(dataget) {
                }
            });
        };
        function updateKinhPhiDoiTuong(temp) {
            //console.log(temp);
            $.ajax({
                type: "POST",
                url:'/kinh-phi/muc-ho-tro-doi-tuong/update',
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
                        insertUpdate(1);
                        GET_INITIAL_NGHILC();
                        loadKinhPhiDoiTuong($('select#viewTableDT').val());
                        $('#btnSaveKinhPhiDoiTuong').button('reset');
                        $('#datepicker1').removeAttr('disabled');
                    }else if(dataget.error != null || dataget.error != undefined){
                        //$("#myModal").modal("hide");
                        utility.message("Thông báo",dataget.error,null,5000,1)
                        $('#btnSaveKinhPhiDoiTuong').button('reset');
                        //insertUpdate(1);
                        //loadKinhPhiDoiTuong($('select#viewTableDT').val()); 
                    }
                    
                         
                }, error: function(dataget) {
                }
            });
        };

        var $dateDDMMYYYY = $('#datepicker1').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true
        });

        function insertUpdate(type){
                $("#txtIdKinhPhi").val('');      
                $("#txtCodeKinhPhi1").val('');
                $("#sltSubject").val('');
                $("#txtMoney1").val('');
                $("#datepicker1").val('');
                // $("#datepicker2").val('');

                //-------------------------Clear date-----------------------------------------------------
                $dateDDMMYYYY.datepicker('setDate', null);
                
                
                $("#sltTruongDt").val('');
                $("#sltSubject option").removeAttr('selected');
                $("#sltTruongDt option").removeAttr('selected');
            if(type===1){
                $("#txtCodeKinhPhi1").attr('disabled','disabled');
                $("#sltSubject").attr('disabled','disabled');
                $("#sltTruongDt").attr('disabled','disabled');
                $("#txtMoney1").attr('disabled','disabled');
                $("#datepicker1").attr('disabled','disabled');
                // $("#datepicker2").attr('disabled','disabled');
                $("#btnSaveKinhPhiDoiTuong").hide();
                $("#btnCancelKinhPhiDoiTuong").hide();
                $("#btnResetKinhPhiDoiTuong").hide();
            }else{
                $("#sltTruongDt").removeAttr('disabled');
                $("#txtCodeKinhPhi1").removeAttr('disabled');
                $("#sltSubject").removeAttr('disabled');
                $("#txtMoney1").removeAttr('disabled');
                $("#datepicker1").removeAttr('disabled');
                // $("#datepicker2").removeAttr('disabled');
                $("#btnSaveKinhPhiDoiTuong").removeAttr('disabled');
                $("#btnSaveKinhPhiDoiTuong").show();
                $("#btnResetKinhPhiDoiTuong").hide();
                $("#btnCancelKinhPhiDoiTuong").show();
            }
           
        };

autocompleteSearch = function (idSearch) {
        var lstCustomerForCombobox;
        $('#' + idSearch).autocomplete({
            source: function (request, response) {
                var cusNameSearch = $.ui.autocomplete.escapeRegex(request.term).replace(/[%\\\-]/g, '');
                //alert(cusNameSearch.length);
                if (cusNameSearch.length >= 4) {
                    GET_INITIAL_NGHILC();
                    loadKinhPhiDoiTuong($('select#viewTableDT').val(),cusNameSearch);
                    
                }else if(cusNameSearch.length == 0){
                    GET_INITIAL_NGHILC();
                    loadKinhPhiDoiTuong($('select#viewTableDT').val());
                }
            },
            minLength: 0,
            delay: 222,
            autofocus: true
            // select: function (event, ui) {
            //     var value = ui.item.value;
            //     var customerCode = value.split('-')[0];
            //     var customerName = value.split('-')[1];
            //     //$('#' + idCusCode).val(customerCode);
            //     //$('#' + idCusName).val(customerName);
            //    // $('#' + idCusId).val('');
            //     return false;
            // }
        });
    };