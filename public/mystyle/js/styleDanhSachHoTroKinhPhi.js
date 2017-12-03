$(function () {
    loadDataDanhsachhotrodoituong($('#viewTableDSHTDT').val(), "");

    $('#viewTableDSHTDT').change(function() {
      GET_INITIAL_NGHILC();
      loadDataDanhsachhotrodoituong($(this).val(), "");
    });

    autocompleteSearchDSHTDT("txtSearchDSHTDT");
});

//----------------------------------------------------------------Load Data---------------------------------------------------------------------
    function loadDataDanhsachhotrodoituong(row, keysearch) {
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit : row,
            key: keysearch
        };
        
        $.ajax({
            type: "POST",
            url: '/kinh-phi/danh-sach-ho-tro/loadDSHTDT',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(dataResult) {
                SETUP_PAGING_NGHILC(dataResult, function () {
                    loadDataDanhsachhotrodoituong(row, keysearch);
                });

                var data = dataResult['data'];

                if(data.length > 0){
                    for (var i = 0; i < data.length; i++) {
                        
                        html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                        html_show += "<td>"+ConvertString(data[i].pheduyet_name)+"</td>";
                        html_show += "<td>"+ConvertString(data[i].nguoiduyet_first_name) + " " + ConvertString(data[i].nguoiduyet_last_name)+"</td>";
                        html_show += "<td>"+formatDates(data[i].pheduyet_ngayduyet)+"</td>";

                        html_show += "<td>"+ConvertString(data[i].nguoigui_first_name) + " " + ConvertString(data[i].nguoigui_last_name)+"</td>";
                        html_show += "<td>"+formatDates(data[i].pheduyet_ngaygui)+"</td>";

                        html_show += "<td>"+ConvertString(data[i].nguoithamdinh_first_name) + " " + ConvertString(data[i].nguoithamdinh_last_name)+"</td>";

                        html_show += "<td><a href='#' onclick='downloads_DSHTDT_dinhkem("+data[i].pheduyet_id+")' style='font-style: italic;'>"+ConvertString(data[i].pheduyet_file_dinhkem)+"</a></td>";
                        html_show += "<td><a href='#' onclick='downloads_DSHTDT_dikem("+data[i].pheduyet_id+")' style='font-style: italic;'>"+ConvertString(data[i].pheduyet_file_dikem)+"</a></td>";
                        // html_show += "<td>"+ConvertString(data[i].pheduyet_trangthai)+"</td>";

                        if(parseInt(data[i].pheduyet_trangthai) === 0){
                            html_show += '<td class="mailbox-star" style="width:7%"><i class="text-yellow">Chưa duyệt</i></td>';
                        }else if(parseInt(data[i].pheduyet_trangthai) === 1){
                            html_show += '<td class="mailbox-star" style="width:7%"><i class="text-green">Đã duyệt</i></td>';
                        }else{
                            html_show += '<td class="mailbox-star" style="width:7%"><i class="text-yellow">Chuyển lại</i></td>';
                        }
                    }
                } else {
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }

                $('#dataDanhsachhotrodoituong').html(html_show);
            }, 
            error: function(dataResult) {
            }
        });
    };

//---------------------------------------------------------------------------Download file----------------------------------------------------------------
    function downloads_DSHTDT_dinhkem(id){
        window.open('/kinh-phi/danh-sach-ho-tro/downloadDSHTDT-dinhkem/' + id, '_blank');
    }

    function downloads_DSHTDT_dikem(id){
        window.open('/kinh-phi/danh-sach-ho-tro/downloadDSHTDT-dikem/' + id, '_blank');
    }

//---------------------------------------------------------------------------Search-----------------------------------------------------------------------
    function autocompleteSearchDSHTDT(idControl) {
        var keySearch = "";
        $('#' + idControl).autocomplete({
            source: function (request, response) {
                keySearch = $.ui.autocomplete.escapeRegex(request.term).replace(/[%\\\-]/g, '');
                //console.log(keySearch.length);
                if (keySearch.length >= 4) {
                    GET_INITIAL_NGHILC();
                    loadDataDanhsachhotrodoituong($('#viewTableDSHTDT').val(), keySearch);
                }else if(keySearch.length < 4){
                    GET_INITIAL_NGHILC();
                    loadDataDanhsachhotrodoituong($('#viewTableDSHTDT').val(), "");
                }
            },
            minLength: 0,
            delay: 222,
            autofocus: true
        });
    };