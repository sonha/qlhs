$(function () {
    $('#datepicker1').datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true
    });
    $('#datepicker2').datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true
    });

//---------------------------------------------------Danh sách hộ nghèo---------------------------------------------------------
    $('#btnInsertDSHN').click(function(){
        messageValidate = "";
        ValidateDSHN();
        if (messageValidate !== "") {
            utility.messagehide("group_message", messageValidate, 1, 5000);
            return;
        }
        var objData = {
            DSHNID: dshn_id,
            NAME: $('#txtName').val(),
            BIRTHDAY: $('#txtBirthday').val(),
            SEX: $('#sltSex').val(),
            NATION: $('#sltNations').val(),
            RELATIONSHIP: $('#sltRelationShip').val(),
            SITE1: $('#sltSite1').val(),
            SITE2: $('#sltSite2').val(),
            TYPE: $('#sltTypeName').val()
            // ENDDATE: $('#endDate').val()
        };
        if (dshn_id == "") {
            insertDSHN(objData);
        }
        else {updateDSHN(objData);}
    });

    getDSHN = function (id) {
        dshn_id = id;
        var objJson = JSON.stringify({DSHNID: dshn_id});
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/danhsachhongheo/getbydshnid',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //console.log(data);
                dshn_id = data[0]['DShongheo_id'];
                $('#txtName').val(data[0]['DShongheo_name']);
                $('#txtBirthday').val(data[0]['DShongheo_birthday']);
                $('#sltSex').val(data[0]['DShongheo_sex']).trigger('change');;
                $('#sltRelationShip').val(data[0]['DShongheo_relationship']);
                // $('#sltNations').val(data[0]['DShongheo_nation_id']);
                $('#sltTypeName').val(data[0]['DShongheo_type']).trigger('change');;
                // $('#sltSite2').val(data[0]['DShongheo_site_idthon']);
                // $('#sltSite1').val(data[0]['DShongheo_site_idxa']);

                loadDataDantoc(parseInt(data[0]['DShongheo_nation_id']));
                loadDataSite(parseInt(data[0]['DShongheo_site_idxa']));
                loadDataSiteByID(parseInt(data[0]['DShongheo_site_idxa']), parseInt(data[0]['DShongheo_site_idthon']));

                $('#btnInsertDSHN').html('Lưu');
                popupUpdateDSHN();
            }, error: function(data) {
            }
        });
    };

    deleteDSHN = function (id) {
        utility.confirm("Xóa bản ghi?", "Bạn có chắc chắn muốn xóa?", function () {
            dshn_id = id;
            var objJson = JSON.stringify({DSHNID: dshn_id});
            //alert(objJson);
            $.ajax({
                type: "POST",
                url:'/danh-muc/danhsachhongheo/delete',
                data: objJson,
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(data) {
                    //alert(data);
                    if (data['success'] !== "" || data['success'] !== undefined) {
                        utility.message("Thông báo",data['success'],null,3000);
                        dshn_id = "";
                        $('#btnInsertDSHN').html('Thêm mới');
                        GET_INITIAL_NGHILC();
                        loaddataDSHN($('#drPagingDSHN').val(), $('#txtSearchDSHN').val());
                    }
                    if (data['error'] != "" && data['error'] != undefined) {
                        utility.message("Thông báo",data['error'],null,3000,1);
                    }
                }, error: function(data) {
                }
            });
        });
    };
    
    $('#btncloseDSHN').click(function(){
        $("#sltSex").val('').select2();
        $("#sltSex option").removeAttr('selected');
        // $("#sltSex").html('<option value="">-- Chọn giới tính --</option>');

        $("#sltNations").val('').select2();
        $("#sltNations option").removeAttr('selected');
        // $("#sltNations").html('<option value="">-- Chọn dân tộc --</option>');

        $("#sltTypeName").val('').select2();
        $("#sltTypeName option").removeAttr('selected');
        // $("#sltTypeName").html('<option value="">-- Chọn diện --</option>');

        $("#sltSite2").attr('disabled', 'disabled');
        $("#sltSite2").val('').select2();
        $("#sltSite2 option").removeAttr('selected');
        // $("#sltSite2").html('<option value="">-- Chọn thôn --</option>');

        $("#sltSite1").val('').select2();
        $("#sltSite1 option").removeAttr('selected');
        // $("#sltSite1").html('<option value="">-- Chọn xã --</option>');

        $("#sltRelationShip").val('1').select2();

        $('#txtName').val('');
        $('#txtBirthday').val('');
        // $('#sltSex').val('');
        // $('#sltRelationShip').val('');
        // $('#sltNations').val('');
        // $('#sltTypeName').val('');
        // $('#sltSite2').val('');
        // $('#sltSite1').val('');
        dshn_id = "";
    });

//---------------------------------------------------quản lý người nấu ăn---------------------------------------------------------
    $('#btnInsertNgna').click(function(){
        messageValidate = "";
        ValidateNGNA("NGNA");
        if (messageValidate !== "") {
            utility.messagehide("group_message", messageValidate, 1, 5000);
            return;
        }
        var objData = {
            NGNAID: ngna_id,
            SCHOOLID: $('#sltTruongDt').val(),
            AMOUNT: $('#txtAmount').val(),
            STARTDATE: $('#startDate').val()
            // ENDDATE: $('#endDate').val()
        };
        if (ngna_id == "") {
            insertNGNA(objData);
        }
        else {updateNGNA(objData);}
    });

    getNGNA = function (id) {
        ngna_id = id;
        var objJson = JSON.stringify({NGNAID: ngna_id});
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/nguoinauan/getbyngnaid',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //console.log(data);
                ngna_id = data[0]['NGNA_id'];
                $('#sltTruongDt').val(data[0]['NGNA_school_id']);
                $('#txtAmount').val(data[0]['NGNA_amount']);
                // $('#startDate').val(data[0]['NGNA_startdate']);
                $('#startDate').datepicker('setDate', new Date(data[0]['NGNA_startdate']));
                // $('#endDate').val(data[0]['NGNA_enddate']);
                // $('#endDate').datepicker('setDate', new Date(data[0]['NGNA_enddate']));
                $('#btnInsertNgna').html('Lưu');
                popupUpdateNgna();
            }, error: function(data) {
            }
        });
    };

    deleteNGNA = function (id) {
        utility.confirm("Xóa bản ghi?", "Bạn có chắc chắn muốn xóa?", function () {
            ngna_id = id;
            var objJson = JSON.stringify({NGNAID: ngna_id});
            //alert(objJson);
            $.ajax({
                type: "POST",
                url:'/danh-muc/nguoinauan/delete',
                data: objJson,
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(data) {
                    //alert(data);
                    if (data['success'] !== "" || data['success'] !== undefined) {
                        utility.message("Thông báo",data['success'],null,3000);
                        ngna_id = "";
                        $('#btnInsertNgna').html('Thêm mới');
                        GET_INITIAL_NGHILC();
                        loaddataNguoinauan($('#drPagingNguoinauan').val(), $('#txtSearchNgna').val());
                    }
                    if (data['error'] != "" && data['error'] != undefined) {
                        utility.message("Thông báo",data['error'],null,3000,1);
                    }
                }, error: function(data) {
                }
            });
        });
    };
    
    $('#btncloseNGNA').click(function(){
        $('#sltTruongDt').val("");
        $('#txtAmount').val("");
        $('#startDate').val("");
        // $('#endDate').val("");
        ngna_id = "";
    });

//Nhóm đối tượng----------------------------------------------------------------------------------------------------
    $('#btnInsertGroup').click(function(){
        messageValidate = "";
        validateInput("GROUP");
        if (messageValidate !== "") {
            utility.messagehide("group_message", messageValidate, 1, 5000);
            return;
        }
        var objData = {
            GROUPID: group_id,
            GROUPCODE: $('#txtGroupCode').val(),
            GROUPNAME: $('#txtGroupName').val(),
            GROUPACTIVE: $('#drGroupActive').val(),
        };
        if (group_id == "") {
            insertNhomDoiTuong(objData);
        }
        else { updateNhomDoiTuong(objData); }
    });
    
    $('a#btnUpdateGroup').click(function(){
        group_id = $(this).attr('data');
        var objData = {
            GROUPID: group_id,
        };

        getNhomDoiTuongbyID(objData);
        $('#btnInsertGroup').html('Lưu');
    });
    
    $('a#btnDeleteGroup').click(function(){
        group_id = $(this).attr('data'),
        $('#btnInsertGroup').html('Thêm mới');
    });

    $('#btnConfirmDeleteGroup').click(function(){
        var objData = {
            GROUPID: group_id,
        };

        deleteNhomDoiTuong(objData);
    });

    getGroup = function (id) {
        group_id = id;
        var objJson = JSON.stringify({GROUPID: group_id});
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/nhomdoituong/getbygroupid',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                group_id = data[0]['group_id'];
                $('#txtGroupCode').val(data[0]['group_code']);
                $('#txtGroupName').val(data[0]['group_name']);
                $('#drGroupActive').val(data[0]['group_active']);
                $('#btnInsertGroup').html('Lưu');
                popupUpdateGroup();
            }, error: function(data) {
            }
        });
    };

    deleteGroup = function (id) {
        utility.confirm("Xóa bản ghi?", "Bạn có chắc chắn muốn xóa?", function () {
            group_id = id;
            var objJson = JSON.stringify({GROUPID: group_id});
            //alert(objJson);
            $.ajax({
                type: "POST",
                url:'/danh-muc/nhomdoituong/delete',
                data: objJson,
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(data) {
                    //alert(data);
                    if (data['success'] !== "" || data['success'] !== undefined) {
                        utility.message("Thông báo",data['success'],null,3000);
                        group_id = "";
                        $('#btnInsertGroup').html('Thêm mới');
                        loaddataGroup($('#drPagingGroup').val(), $('#txtSearchGroup').val());
                    }
                    if (data['error'] != "" && data['error'] != undefined) {
                        utility.message("Thông báo",data['error'],null,3000);
                    }
                }, error: function(data) {
                }
            });
        });
    };

    $('#btnCloseGroup').click(function(){
        $('#txtGroupCode').val("");
        $('#txtGroupName').val("");
        $('#drGroupActive').val(1);
        group_id = "";
    });

//Khối----------------------------------------------------------------------------------------------------
    $('#btnInsertUnit').click(function(){
        messageValidate = "";
        validateInput("UNIT");
        if (messageValidate !== "") {
            utility.messagehide("group_message", messageValidate, 1, 5000);
            return;
        }
        var objData = {
            UNITID: unit_id,
            UNITCODE: $('#txtUnitCode').val(),
            UNITNAME: $('#txtUnitName').val(),
            UNITACTIVE: $('#drUnitActive').val(),
        };
        if (unit_id == "") {
            insertKhoi(objData);
        }
        else {updateKhoi(objData);}
    });
    
    $('a#btnUpdateUnit').click(function(){
        unit_id = $(this).attr('data');
        var objData = {
            UNITID: unit_id,
        };

        $('#btnInsertUnit').html('Lưu');
        getKhoibyID(objData);
    });
    
    $('a#btnDeleteUnit').click(function(){
        unit_id = $(this).attr('data');
        $('#btnInsertUnit').html('Thêm mới');
    });
    
    $('#btnConfirmDeleteUnit').click(function(){
        var objData = {
            UNITID: unit_id,
        };

        deleteKhoi(objData);
    });

    getUnit = function (id) {
        unit_id = id;
        var objJson = JSON.stringify({UNITID: unit_id});
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/khoi/getbyunitid',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                unit_id = data[0]['unit_id'];
                $('#txtUnitCode').val(data[0]['unit_code']);
                $('#txtUnitName').val(data[0]['unit_name']);
                $('#drUnitActive').val(data[0]['unit_active']);
                $('#btnInsertUnit').html('Lưu');
                popupUpdateUnit();
            }, error: function(data) {
            }
        });
    };

    deleteUnit = function (id) {
        utility.confirm("Xóa bản ghi?", "Bạn có chắc chắn muốn xóa?", function () {
            unit_id = id;
            var objJson = JSON.stringify({UNITID: unit_id});
            //alert(objJson);
            $.ajax({
                type: "POST",
                url:'/danh-muc/khoi/delete',
                data: objJson,
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(data) {
                    //alert(data);
                    if (data['success'] !== "" || data['success'] !== undefined) {
                        utility.message("Thông báo",data['success'],null,3000);
                        nation_id = "";
                        $('#btnInsertUnit').html('Thêm mới');
                        loaddataUnit($('#drPagingUnit').val(), $('#txtSearchUnit').val());
                    }
                    if (data['error'] != "" && data['error'] != undefined) {
                        utility.message("Thông báo",data['error'],null,3000);
                    }
                }, error: function(data) {
                }
            });
        });
    };

    $('#btnCloseUnit').click(function(){
        $('#txtUnitCode').val("");
        $('#txtUnitName').val("");
        $('#drUnitActive').val(1);
        unit_id = "";
    });

//Dân tộc----------------------------------------------------------------------------------------------------
    $('#btnInsertNation').click(function(){
        messageValidate = "";
        validateInput("NATION");
        if (messageValidate !== "") {
            utility.messagehide("group_message", messageValidate, 1, 5000);
            return;
        }
        var objData = {
            NATIONID: nation_id,
            NATIONCODE: $('#txtNationCode').val(),
            NATIONNAME: $('#txtNationName').val(),
            NATIONACTIVE: $('#drNationActive').val(),
        };
        if (nation_id == "") {
            insertDantoc(objData);
        }
        else {updateDantoc(objData);}
    });
    
    $('a#btnUpdateNation').click(function(){
        nation_id = $(this).attr('data');
        var objData = {
            NATIONID: nation_id,
        };

        $('#btnInsertNation').html('Lưu');
        getDantocbyID(objData);
    });

    getNation = function (id) {
        nation_id = id;
        var objJson = JSON.stringify({NATIONID: nation_id});
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/dantoc/getbynationid',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //console.log(data);
                nation_id = data[0]['nationals_id'];
                $('#txtNationCode').val(data[0]['nationals_code']);
                $('#txtNationName').val(data[0]['nationals_name']);
                $('#drNationActive').val(data[0]['nationals_active']);
                $('#btnInsertNation').html('Lưu');
                $('#txtNationCode').attr('readonly', true);
                popupUpdateNation();
            }, error: function(data) {
            }
        });
    };
    
    $('a#btnDeleteNation').click(function(){
        nation_id = $(this).attr('data');
        $('#btnInsertNation').html('Thêm mới');
    });

    deleteNation = function (id) {
        utility.confirm("Xóa bản ghi?", "Bạn có chắc chắn muốn xóa?", function () {
            nation_id = id;
            var objJson = JSON.stringify({NATIONID: nation_id});
            //alert(objJson);
            $.ajax({
                type: "POST",
                url:'/danh-muc/dantoc/delete',
                data: objJson,
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(data) {
                    //alert(data);
                    if (data['success'] !== "" || data['success'] !== undefined) {
                        utility.message("Thông báo",data['success'],null,3000);
                        nation_id = "";
                        $('#btnInsertNation').html('Thêm mới');
                        $('#txtNationCode').attr('readonly', true);
                        loaddataNation($('#drPagingNation').val(), $('#txtSearchNational').val());
                    }
                    if (data['error'] != "" && data['error'] != undefined) {
                        utility.message("Thông báo",data['error'],null,3000);
                    }
                }, error: function(data) {
                }
            });
        });
    };
    
    $('#btnCloseNation').click(function(){
        $('#txtNationCode').val("");
        $('#txtNationName').val("");
        $('#drNationActive').val(1);
        nation_id = "";
    });

//Đối tượng----------------------------------------------------------------------------------------------------
    
    $('a#btnUpdateSubject').click(function(){
        subject_id = $(this).attr('data');
        $('#btnInsertSubject').html('Lưu');
        getDoituongbyID();
        getNhombyDoituongID();
        popupAddnewSubject();
    });
    
    $('a#btnDeleteSubject').click(function(){
        subject_id = $(this).attr('data');
        $('#btnInsertSubject').html('Thêm mới');
        popupConfirmDeleteSubject();
    });

    getSubject = function (id) {
        subject_id = id;
        var objJson = JSON.stringify({SUBJECTID: subject_id});
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/doituong/getbysubjectid',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                subject_id = data['objSubject'][0]['subject_id'];
                $('#txtSubjectCode').val(data['objSubject'][0]['subject_code']);
                $('#txtSubjectName').val(data['objSubject'][0]['subject_name']);
                $('#drSubjectActive').val(data['objSubject'][0]['subject_active']);
                $('#btnInsertSubject').html('Lưu');
                // getNhombyDoituongID(function(data){
                // });

                loadComboNhomDoiTuong();

                // var arrGroupID = data['arrGroupID'];
                // for (var i = 0; i < arrGroupID.length; i++) {
                //     $("#drGroupSubject option[value='" + arrGroupID[i]['subject_history_group_id'] + "']").attr("selected", true);
                //     $("#drGroupSubject").multiselect("refresh");
                // }
                var arrData = "";

                var arrGroupID = data['arrGroupID'];
                for (var i = 0; i < arrGroupID.length; i++) {
                    arrData += (arrGroupID[i]['subject_history_group_id']) + ",";    
                }
                var item = arrData.split(",");
                $("#drGroupSubject").val(item);
                $("#drGroupSubject").multiselect("refresh");
                popupUpdateSubject();
            }, error: function(data) {
            }
        });
    };

    deleteSubject = function (id) {
        utility.confirm("Xóa bản ghi?", "Bạn có chắc chắn muốn xóa?", function () {
            subject_id = id;
            var objJson = JSON.stringify({SUBJECTID: subject_id});
            //alert(objJson);
            $.ajax({
                type: "POST",
                url:'/danh-muc/doituong/delete',
                data: objJson,
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(data) {
                    //alert(data);
                    if (data['success'] !== "" || data['success'] !== undefined) {
                        utility.message("Thông báo",data['success'],null,3000);
                        subject_id = "";
                        $('#btnInsertSubject').html('Thêm mới');
                        loaddataSubject($('#drPagingSubject').val(), $('#txtSearchSubject').val());
                    }
                    if (data['error'] != "" && data['error'] != undefined) {
                        utility.message("Thông báo",data['error'],null,3000);
                    }
                }, error: function(data) {
                }
            });
        });
    };

    $('#btnCloseSubject').click(function(){
        $('#txtSubjectCode').val("");
        $('#txtSubjectName').val("");
        $('#drSubjectActive').val(1);

        subject_id = "";
    });

//Trường----------------------------------------------------------------------------------------------------
    $('#btnInsertSchool').click(function(){
        messageValidate = "";
        validateInput("SCHOOL");
        if (messageValidate !== "") {
            utility.messagehide("group_message", messageValidate, 1, 5000);
            return;
        }
        var objData = {
            SCHOOLID: school_id,
            SCHOOLCODE: $('#txtSchoolCode').val(),
            SCHOOLNAME: $('#txtSchoolName').val(),
            UNITID: $('#drUnit').val(),
            SCHOOLACTIVE: $('#drSchoolActive').val(),
        };
        if (school_id == "") {
            insertTruong(objData);
        }
        else {updateTruong(objData);}
    });
    
    $('a#btnUpdateSchool').click(function(){
        school_id = $(this).attr('data');
        var objData = {
            SCHOOLID: school_id,
        };

        $('#btnInsertSchool').html('Lưu');
        getTruongbyID(objData);
    });
    
    $('a#btnDeleteSchool').click(function(){
        school_id = $(this).attr('data');
        $('#btnInsertSchool').html('Thêm mới');
    });
    
    $('#btnConfirmDeleteSchool').click(function(){
        var objData = {
            SCHOOLID: school_id,
        };

        deleteTruong(objData);
    });

    getSchool = function (id) {
        school_id = id;
        var objJson = JSON.stringify({SCHOOLID: school_id});
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/truong/getbyschoolid',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //console.log(data);
                school_id = data[0]['schools_id'];
                $('#txtSchoolCode').val(data[0]['schools_code']);
                $('#txtSchoolName').val(data[0]['schools_name']);                
                $('#drUnit').val(data[0]['schools_unit_id']);
                $('#drSchoolActive').val(data[0]['schools_active']);
                $('#btnInsertSchool').html('Lưu');
                popupUpdateSchool();
            }, error: function(data) {
            }
        });
    };

    deleteSchool = function (id) {
        utility.confirm("Xóa bản ghi?", "Bạn có chắc chắn muốn xóa?", function () {
            school_id = id;
            var objJson = JSON.stringify({SCHOOLID: school_id});
            //alert(objJson);
            $.ajax({
                type: "POST",
                url:'/danh-muc/truong/delete',
                data: objJson,
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(data) {
                    //alert(data);
                    if (data['success'] !== "" || data['success'] !== undefined) {
                        utility.message("Thông báo",data['success'],null,3000);
                        school_id = "";
                        $('#btnInsertSchool').html('Thêm mới');
                        loaddataSchool($('#drPagingSchool').val(), $('#txtSearchSchool').val());
                    }
                    if (data['error'] != "" && data['error'] != undefined) {
                        utility.message("Thông báo",data['error'],null,3000);
                    }
                }, error: function(data) {
                }
            });
        });
    };

    $('#btnCloseSchool').click(function(){
        $('#txtSchoolCode').val("");
        $('#txtSchoolName').val("");
        $('#drUnit').val("");
        $('#drSchoolActive').val(1);
        school_id = "";
    });

//Lớp----------------------------------------------------------------------------------------------------
    $('#btnInsertClass').click(function(){
        messageValidate = "";
        validateInput("CLASS");
        if (messageValidate !== "") {
            utility.messagehide("group_message", messageValidate, 1, 5000);
            return;
        }
        var objData = {
            CLASSID: class_id,
            CLASSCODE: $('#txtClassCode').val(),
            CLASSNAME: $('#txtClassName').val(),
            SCHOOLID: $('#drSchool').val(),
            LEVELID: $('#drLevel').val(),
            CLASSACTIVE: $('#drClassActive').val(),
        };
        if (class_id == "") {
            insertLop(objData);
        }
        else {updateLop(objData);}
    });
    
    $('a#btnUpdateClass').click(function(){
        class_id = $(this).attr('data');
        var objData = {
            CLASSID: class_id,
        };

        $('#btnInsertClass').html('Lưu');
        getLopbyID(objData);
    });
    
    $('a#btnDeleteClass').click(function(){
        class_id = $(this).attr('data');
        $('#btnInsertClass').html('Thêm mới');
    });
    
    $('#btnConfirmDeleteClass').click(function(){
        var objData = {
            CLASSID: class_id,
        };

        deleteLop(objData);
    });

    getClass = function (id) {
        class_id = id;
        var objJson = JSON.stringify({CLASSID: class_id});
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/lop/getbyclassid',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //console.log(data);
                school_id = data[0]['class_id'];
                $('#txtClassCode').val(data[0]['class_code']);
                $('#txtClassName').val(data[0]['class_name']);                
                $('#drSchool').val(data[0]['class_schools_id']);
                getLevelbySchool(data[0]['class_schools_id'],function(data){
                    $('#drLevel').removeAttr('disabled');                    

                    var objData = {
                        CLASSID: class_id
                    };

                    getLopbyID(objData, function(data){
                        //$('#drLevel').val(data[0]['class_level_id']);
                    });
                });
                                
                $('#drClassActive').val(data[0]['class_active']);
                $('#btnInsertClass').html('Lưu');
                popupUpdateClass();
            }, error: function(data) {
            }
        });
    };

    deleteClass = function (id) {
        utility.confirm("Xóa bản ghi?", "Bạn có chắc chắn muốn xóa?", function () {
            class_id = id;
            var objJson = JSON.stringify({CLASSID: class_id});
            //alert(objJson);
            $.ajax({
                type: "POST",
                url:'/danh-muc/lop/delete',
                data: objJson,
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(data) {
                    //alert(data);
                    if (data['success'] !== "" || data['success'] !== undefined) {
                        utility.message("Thông báo",data['success'],null,3000);
                        class_id = "";
                        $('#btnInsertClass').html('Thêm mới');
                        loaddataClass($('#drPagingClass').val(), $('#txtSearchClass').val());
                    }
                    if (data['error'] != "" && data['error'] != undefined) {
                        utility.message("Thông báo",data['error'],null,3000);
                    }
                }, error: function(data) {
                }
            });
        });
    };

    $('#btnCloseClass').click(function(){
        $('#txtClassCode').val("");
        $('#txtClassName').val("");
        $('#drSchool').val("");
        $('#drLevel').val("");
        $('#drClassActive').val(1);
        class_id = "";
    });

//Phân loại xã----------------------------------------------------------------------------------------------------
    $('#btnInsertWard').click(function(){
        messageValidate = "";
        validateInput("WARD");
        if (messageValidate !== "") {
            //utility.messagehide("group_message", messageValidate, 1, 5000);
            alert(messageValidate);
            return;
        }
        ward_id = $('#txtWardID').val();
        var ward_level = $('#drWardParent').find(":selected").attr('data');
        var objData = {
            WARDID: ward_id,
            WARDCODE: $('#txtWardCode').val(),
            WARDNAME: $('#txtWardName').val(),
            WARDPARENTID: $('#drWardParent').val(),
            WARDLEVEL: ward_level,
            WARDACTIVE: $('#drWardActive').val(),
        };
        if (ward_id == "") {
            insertWard(objData);
        }
        else {updateWard(objData);}
    });
    
    $('#btnDeleteWard').click(function(){
        utility.confirm("Xóa bản ghi?", "Bạn có chắc chắn muốn xóa?", function () {
            var objData = {
                WARDID: $('#txtWardID').val()
            };

            deleteWard(objData);
        });
    });

    $('#btnResetWard').click(function(){
        $('#txtWardCode').focus();
        $('#txtWardCode').val("");
        $('#txtWardName').val("");
        loadComboWard( function(data){
        });
        $('#drWardActive').val(1);
        $('#txtWardID').val("");
        $("#btnInsertWard").html("Thêm mới");
        ward_id = "";
        $("#btnDeleteWard").attr("disabled", true);
        $("#txtWardCode").attr("disabled", false);
        //$("#using_json_2").jstree().deselect_node(true);
        //$('#using_json_2').jstree("destroy").empty();
        //$("#using_json_2").jstree('destroy');
    });

//Xã Phường----------------------------------------------------------------------------------------------------
    $('#btnInsertSite').click(function(){
        messageValidate = "";
        validateInput("SITE");
        if (messageValidate !== "") {
            //utility.messagehide("group_message", messageValidate, 1, 5000);
            alert(messageValidate);
            return;
        }
        site_id = $('#txtSiteID').val();
        var site_level = $('#drSiteLevel').val();
        var objData = {
            SITEID: site_id,
            SITECODE: $('#txtSiteCode').val(),
            SITENAME: $('#txtSiteName').val(),
            SITEPARENTID: $('#drSiteParents').val(),
            SITELEVEL: site_level++,
            SITEACTIVE: $('#drSiteActive').val(),
        };
        if (site_id == "") {
            insertSite(objData);
        }
        else {updateSite(objData);}
    });
    
    $('#btnDeleteSite').click(function(){
        utility.confirm("Xóa bản ghi?", "Bạn có chắc chắn muốn xóa?", function () {
            var objData = {
                SITEID: $('#txtSiteID').val()
            };

            deleteSite(objData);
        });
    });

    $('#btnResetSite').click(function(){
        resetXaPhuong();
    });

//Phòng ban----------------------------------------------------------------------------------------------------
    $('#btnInsertDepartment').click(function(){
        messageValidate = "";
        validateInput("DEPARTMENT");
        if (messageValidate !== "") {
            //utility.messagehide("group_message", messageValidate, 1, 5000);
            alert(messageValidate);
            return;
        }
        depart_id = $('#txtDepartID').val();
        var depart_level = $('#drpDepartment').find(":selected").attr('data');
        var objData = {
            DEPARTMENTID: depart_id,
            DEPARTMENTCODE: $('#txtDepartCode').val(),
            DEPARTMENTNAME: $('#txtDepartName').val(),
            DEPARTMENTPARENTID: $('#drpDepartment').val(),
            DEPARTMENTLEVEL: depart_level,
            DEPARTMENTACTIVE: $('#drDepartActive').val(),
        };
        if (depart_id == "") {
            insertDepartment(objData);
        }
        else {updateDepartment(objData);}
    });
    
    $('#btnDeleteDepartment').click(function(){
        utility.confirm("Xóa bản ghi?", "Bạn có chắc chắn muốn xóa?", function () {
            var objData = {
                DEPARTMENTID: $('#txtDepartID').val()
            };

            deleteDepartment(objData);
        });
    });    

    $('#btnResetDepartment').click(function(){
        $('#txtDepartCode').focus();
        $('#txtDepartCode').val("");
        $('#txtDepartName').val("");
        loadComboDepartment( function(data){
        });
        $('#drDepartActive').val(1);
        $("#btnInsertDepartment").html("Thêm mới");
        $('#txtDepartID').val("");
        depart_id = "";
        $("#btnDeleteDepartment").attr("disabled", true);        
        $("#txtDepartCode").attr("disabled", false);
    });

    $('button.close').click(function(){
        $('#txtUnitCode').val("");
        $('#txtUnitName').val("");
        $('#drUnitActive').val(1);
        unit_id = "";
        $('#txtSubjectCode').val("");
        $('#txtSubjectName').val("");
        $('#drSubjectActive').val(1);
        $("#drGroupSubject").val('');
        $("#drGroupSubject option").removeAttr('selected');
        subject_id = "";
        $('#txtGroupCode').val("");
        $('#txtGroupName').val("");
        $('#drGroupActive').val(1);
        group_id = "";
        $('#txtNationCode').val("");
        $('#txtNationName').val("");
        $('#drNationActive').val(1);
        nation_id = "";
        $('#txtSchoolCode').val("");
        $('#txtSchoolName').val("");
        $('#drUnit').val("");
        $('#drSchoolActive').val(1);
        school_id = "";
        $('#txtClassCode').val("");
        $('#txtClassName').val("");
        $('#drSchool').val("");
        $('#drLevel').val("");
        $('#drClassActive').val(1);
        class_id = "";
    });

//End Action Form-------------------------------------------------------------------------------------------------------

    // var t = $('#example1').DataTable({
    //     "paging": true,
    //     "language": {
    //         "lengthMenu":  "b _MENU_ a",
    //         "info": "Hiển thị _START_ đến _END_ của _TOTAL_ bản ghi" ,
    //         "paginate": {
    //                     "first": "First",
    //                     "last": "Last",
    //                     "next": "Trang sau",
    //                     "previous": "Trang trước"
    //         },"emptyTable": "Không có dữ liệu"
    //     },
    //     "lengthChange": false,
    //     "searching": false,
    //     "ordering": true,
    //     "info": true,
    //     //"ajax":'load',
    //     "ajax": {
    //         // "url": "load",
    //         // "type": 'POST'
    //         "type": "POST",
    //         "url": 'load',
    //         "contentType": 'application/json; charset=utf-8',
    //         "headers": {
    //             'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
    //         }
    //     },
    //     "columns": [
    //         { "data": "id" },
    //         { "data": "code" },
    //         { "data": "subject_name" },
    //         { "data": "money" },
    //         { "data": "start_date" },
    //         { "data": "end_date" },
    //         { "data": "updated_at" },
    //         { "data": "username" },
    //         {
    //             "data": null,
    //             "className": "center",
    //             "defaultContent": '<a href="" class="btn btn-info btn-xs editor_edit"><i class="glyphicon glyphicon-pencil"></i> Sửa</a> <a href="" class="btn btn-danger btn-xs editor_remove"><i class="glyphicon glyphicon-remove"></i> Xóa</a>'
    //         }
    //     ],
    //     // "columns": [
    //     //     { "data": "data.code" },
    //     //     { "data": "data.subject_name" },
    //     //     { "data": "data.money" },
    //     //     { "data": "data.start_date" },
    //     //     { "data": "data.end_date" },
    //     //     { "data": "data.updated_at" }
    //     //     { "data": "data.username" }
    //     // ],
    //     // "select": true,
       
    //   "autoWidth": false
    // });
    // t.on( 'order.dt search.dt', function () {
    //     t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
    //         cell.innerHTML = i+1;
    //     } );
    // }).draw();
});

var dshn_id = "";
var ngna_id = "";
var group_id = "";
var unit_id = "";
var nation_id = "";
var subject_id = "";
var school_id = "";
var class_id = "";
var site_id = "";
var ward_id = "";
var depart_id = "";

//Check Permisstion
    var module = 0;
    var CODE_FEATURES;
    function permission(callback) {
                // console.log(module);
        $.ajax({
            type: "GET",
            url: '/danh-muc/permission',
            success: function(data) {
                // console.log(data);
                CODE_FEATURES = data.permission;
                if(callback!=null){
                    callback();
                }
            }, error: function(data) {
            }
        });
    };

    function check_Permission_Feature(featureCode) {
        // console.log(featureCode);
        // console.log(CODE_FEATURES);
        try {
            if (CODE_FEATURES.indexOf(featureCode) >= 0) {
                // console.log(Object.values(CODE_FEATURES).indexOf(featureCode));
                return true;
            }
            // if(callback!=null){
            //     callback();
            // }

            // for (var i = 0; i < CODE_FEATURES.length; i++) {
            //     if(CODE_FEATURES[i] == featureCode) { 
            //         console.log(featureCode); 
            //         return true; }
            // }
                
            return false;


        } catch (e) {
            console.log(e);
        }
        return true;
    }

//------------------------------------------------Danh sách hộ nghèo----------------------------------------------------------------------
    function loaddataDSHN(row, keySearch) {
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit : row,
            key: keySearch
        };
        $.ajax({
            type: "POST",
            url: '/danh-muc/danhsachhongheo/loadDanhsachhongheo',
            data: JSON.stringify(o),
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(dataget) {

                SETUP_PAGING_NGHILC(dataget, function () {
                    loaddataDSHN(row, keySearch);
                });
                var v_status = "";
                $('#dataTableDSHN').html("");
                data = dataget.data;
                if(data.length>0){
                    for (var i = 0; i < data.length; i++) {
                        html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                        html_show += "<td>"+ConvertString(data[i].DShongheo_name)+"</td>";
                        html_show += "<td class='text-right'>"+ConvertString(data[i].DShongheo_birthday)+"</td>";
                        html_show += "<td>"+ConvertString(data[i].DShongheo_sex)+"</td>";
                        html_show += "<td>"+ConvertString(data[i].nationals_name)+"</td>";
                        if (parseInt(data[i].DShongheo_relationship) == 1) {
                            html_show += "<td>Chủ hộ</td>";
                        }
                        else {
                            html_show += "<td>Chưa rõ</td>";
                        }
                        html_show += "<td>"+ConvertString(data[i].tenthon)+"</td>";
                        html_show += "<td>"+ConvertString(data[i].tenxa)+"</td>";
                        html_show += "<td>"+ConvertString(data[i].DShongheo_typename)+"</td>";

                        // if (parseInt(data[i].DShongheo_parent_id) == 1) {
                        //     html_show += "<td>Diện hộ nghèo</td>";
                        // }
                        // else {
                        //     html_show += "<td>Diện khác</td>";
                        // }

                        html_show += "<td class='text-center'>"
                        if(check_Permission_Feature('2')){
                            html_show += "<button onclick='getDSHN("+data[i].DShongheo_id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Sửa</button> ";
                        }
                        if(check_Permission_Feature('3')){
                            html_show += "<button onclick='deleteDSHN("+data[i].DShongheo_id+");' class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                        }
                        html_show += "</td></tr>";
                    }
                        
                } else {
                    html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }
                $('#dataTableDSHN').html(html_show);
            }, error: function(dataget) {

            }
        });
    };

    function loadDataDantoc(idchoise = 0){
    
        $.ajax({
            type: "GET",
            url: '/danh-muc/load/dan-toc',
            success: function(dataget) {
                // console.log(dataget);
                $('#sltNations').html("");
                var html_show = "";
                if(dataget.length >0){
                     
                    html_show += "<option value=''>-- Chọn dân tộc --</option>";
                    for (var i = 0; i < dataget.length; i++) {
                        // if (parseInt(dataget[i].nationals_id) === idchoise) {
                        //     html_show += "<option value='"+dataget[i].nationals_id+"' selected>"+dataget[i].nationals_name+"</option>";
                        // }
                        // else {
                        html_show += "<option value='"+dataget[i].nationals_id+"'>"+dataget[i].nationals_name+"</option>";
                        // }
                    }
                    $('#sltNations').html(html_show);
                }
                else{
                    $('#sltNations').html("<option value=''>-- Chưa có dân tộc --</option>");
                }

                if (idchoise > 0){
                    $('#sltNations').val(idchoise);
                }
            }, error: function(dataget) {
            }
        });
    };

    function loadDataSite(idchoise = 0){
    
        $.ajax({
            type: "GET",
            url: '/danh-muc/danhsachhongheo/getSite',
            success: function(data) {
                // console.log(data);
                var dataSite1 = data.SITE1;
                var dataSite2 = data.SITE2;
                $('#sltSite1').html("");
                var html_show = "";

                if (dataSite1.length > 0 && dataSite2.length > 0) {
                    html_show += "<option value=''>-- Chọn xã --</option>";
                    for (var i = 0; i < dataSite1.length; i++) {
                        html_show +="<optgroup label='"+dataSite1[i].site_name+"'>";
                        
                        for (var j = 0; j < dataSite2.length; j++) {
                            if (parseInt(dataSite1[i].site_id) == parseInt(dataSite2[j].site_parent_id)) {
                                html_show += "<option value='"+dataSite2[j].site_id+"'>"+dataSite2[j].site_name+"</option>";
                            }
                        }

                        html_show +="</optgroup>";
                    }

                    $('#sltSite1').html(html_show);

                    if (idchoise > 0){
                        $('#sltSite1').val(idchoise);
                    }
                }
                else {
                    $('#sltSite1').html("<option value=''>-- Không có xã nào --</option>");
                }
            }, error: function(data) {
            }
        });
    };



    function loadDataSiteByID(siteID, idchoise = 0){
    
        $.ajax({
            type: "GET",
            url: '/danh-muc/danhsachhongheo/getSiteByID/' + siteID,
            success: function(data) {
                // console.log(data);
                
                $('#sltSite2').html("");
                var html_show = "";

                if (data.length > 0) {
                    html_show += "<option value=''>-- Chọn thôn --</option>";
                    for (var i = 0; i < data.length; i++) {
                        html_show += "<option value='"+data[i].site_id+"'>"+data[i].site_name+"</option>";
                    }

                    $('#sltSite2').html(html_show);

                    if (idchoise > 0){
                        $('#sltSite2').val(idchoise);
                    }

                    $('#sltSite2').removeAttr('disabled');
                }
                else {
                    $('#sltSite2').html("<option value=''>-- Không có thôn nào --</option>");
                }
            }, error: function(data) {
            }
        });
    };

    function insertDSHN(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/danhsachhongheo/insert',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                // console.log(data);
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    $('#txtName').val('');
                    $('#txtBirthday').val('');
                    $("#sltSex").val('').select2();
                    $("#sltSex option").removeAttr('selected');
                    // $("#sltSex").html('<option value="">-- Chọn giới tính --</option>');

                    $("#sltNations").val('').select2();
                    $("#sltNations option").removeAttr('selected');
                    // $("#sltNations").html('<option value="">-- Chọn dân tộc --</option>');

                    $("#sltTypeName").val('').select2();
                    $("#sltTypeName option").removeAttr('selected');
                    // $("#sltTypeName").html('<option value="">-- Chọn diện --</option>');

                    $("#sltSite2").attr('disabled', 'disabled');
                    $("#sltSite2").val('').select2();
                    $("#sltSite2 option").removeAttr('selected');
                    // $("#sltSite2").html('<option value="">-- Chọn thôn --</option>');

                    $("#sltSite1").val('').select2();
                    $("#sltSite1 option").removeAttr('selected');
                    // $("#sltSite1").html('<option value="">-- Chọn xã --</option>');

                    $("#sltRelationShip").val('1').select2();
                    dshn_id = "";
                    GET_INITIAL_NGHILC();
                    loaddataDSHN($('#drPagingDSHN').val(), $('#txtSearchDSHN').val());
                }
                if (data['error'] !== "" && data['error'] !== undefined) {
                    utility.message("Thông báo",data['error'],null,3000,1);
                }
            }, error: function(data) {
            }
        });
    };

    function updateDSHN(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/danhsachhongheo/update',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    
                    dshn_id = "";
                    $('#btnInsertDSHN').html('Thêm mới');
                    $("#modalAddNewDSHN").modal("hide");
                    GET_INITIAL_NGHILC();
                    loaddataDSHN($('#drPagingDSHN').val(), $('#txtSearchDSHN').val());
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000,1);
                }
            }, error: function(data) {
            }
        });
    };

//------------------------------------------------Quản lý người nấu ăn--------------------------------------------------------------------
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

    function loaddataNguoinauan(row, keySearch) {
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit : row,
            key: keySearch
        };
            $.ajax({
                type: "POST",
                url: '/danh-muc/nguoinauan/loadNguoinauan',
                data: JSON.stringify(o),
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {

                    SETUP_PAGING_NGHILC(dataget, function () {
                        loaddataNguoinauan(row, keySearch);
                    });
                    var v_status = "";
                    $('#dataTableNGNA').html("");
                    data = dataget.data;
                    if(data.length>0){
                        for (var i = 0; i < data.length; i++) {
                            html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            html_show += "<td>"+ConvertString(data[i].schools_name)+"</td>";
                            html_show += "<td class='text-right'>"+data[i].NGNA_amount+"</td>";
                            html_show += "<td class='text-right'>"+formatDates(data[i].NGNA_startdate)+"</td>";
                            html_show += "<td class='text-right'>"+formatDates(data[i].NGNA_enddate)+"</td>";
                            html_show += "<td class='text-right'>"+formatDates(data[i].NGNA_create_date)+"</td>";
                            html_show += "<td>"+ConvertString(data[i].first_name + " " + data[i].first_name)+"</td>";
                            html_show += "<td class='text-center'>"
                            if(check_Permission_Feature('2')){
                                html_show += "<button onclick='getNGNA("+data[i].NGNA_id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Sửa</button>";
                            }
                            if(check_Permission_Feature('3')){
                                html_show += "<button onclick='deleteNGNA("+data[i].NGNA_id+");' class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                            }
                            html_show += "</td></tr>";
                        }
                        
                    } else {
                        html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                    }
                    $('#dataTableNGNA').html(html_show);
                }, error: function(dataget) {

                }
            });
        };

    function insertNGNA(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/nguoinauan/insert',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                // console.log(data);
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    $('#sltTruongDt').val("");
                    $('#txtAmount').val("");
                    $('#startDate').val("");
                    // $('#endDate').val("");
                    ngna_id = "";
                    GET_INITIAL_NGHILC();
                    loaddataNguoinauan($('#drPagingNguoinauan').val(), $('#txtSearchNgna').val());
                }
                if (data['error'] !== "" && data['error'] !== undefined) {
                    utility.message("Thông báo",data['error'],null,3000,1);
                }
            }, error: function(data) {
            }
        });
    };

    function updateNGNA(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/nguoinauan/update',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    $('#sltTruongDt').val("");
                    $('#txtAmount').val("");
                    $('#startDate').val("");
                    // $('#endDate').val("");
                    ngna_id = "";
                    $('#btnInsertNgna').html('Thêm mới');
                    $("#modalAddNew").modal("hide");
                    GET_INITIAL_NGHILC();
                    loaddataNguoinauan($('#drPagingNguoinauan').val(), $('#txtSearchNgna').val());
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000,1);
                }
            }, error: function(data) {
            }
        });
    };

//Nhóm đối tượng----------------------------------------------------------------------------------------
    function loaddataGroup(row, keySearch) {
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit: row,
            key: keySearch
        };
            $.ajax({
                type: "POST",
                url: '/danh-muc/nhomdoituong/loadNhomDoiTuong',
                data: JSON.stringify(o),
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {

                    SETUP_PAGING_NGHILC(dataget, function () {
                        loaddataGroup(row, keySearch);
                    });
                    var v_status = "";
                    $('#dataGroup').html("");
                    data = dataget.data;
                    //console.log(data);
                    if(data.length>0){
                        for (var i = 0; i < data.length; i++) {
                            //console.log(data[i].group_active);
                            if (data[i].group_active == 1) {
                                v_status = "Đang hoạt động";
                            }
                            else {v_status = "Không hoạt động";}

                            html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            html_show += "<td>"+data[i].group_code+"</td>";
                            html_show += "<td>"+data[i].group_name+"</td>";
                            html_show += "<td>"+v_status+"</td>";
                            html_show += "<td>"+formatDateTimes(data[i].updated_at)+"</td>";
                            
                            if(check_Permission_Feature('2')){
                                html_show += "<td class='text-center'><button data='"+data[i].group_id+"' onclick='getGroup("+data[i].group_id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Sửa</button></td>";
                            }
                            if(check_Permission_Feature('3')){
                                html_show += "<td class='text-center'><button  onclick='deleteGroup("+data[i].group_id+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button></td>";
                            }
                            html_show += "</tr>";
                        }
                        
                    } else {
                        html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                    }
                    $('#dataGroup').html(html_show);
                }, error: function(dataget) {

                }
            });
        };

    function insertNhomDoiTuong(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/nhomdoituong/insert',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    $('#txtGroupCode').val("");
                    $('#txtGroupName').val("");
                    $('#drGroupActive').val(1);
                    loaddataGroup($('#drPagingGroup').val(), $('#txtSearchGroup').val());
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000,1);
                }
            }, error: function(data) {
            }
        });
    };

    function getNhomDoiTuongbyID(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/nhomdoituong/getbygroupid',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                group_id = data[0]['group_id'];
                $('#txtGroupCode').val(data[0]['group_code']);
                $('#txtGroupName').val(data[0]['group_name']);
                $('#drGroupActive').val(data[0]['group_active']);
            }, error: function(data) {
            }
        });
    };

    function updateNhomDoiTuong(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/nhomdoituong/update',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    $('#txtGroupCode').val("");
                    $('#txtGroupName').val("");
                    $('#drGroupActive').val(1);
                    $('#txtGroupCode').attr('readonly', false);
                    $('#btnInsertGroup').html('Thêm mới');
                    $("#modalAddNew").modal("hide");
                    loaddataGroup($('#drPagingGroup').val(), $('#txtSearchGroup').val());
                    group_id = "";
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000,1);
                }
            }, error: function(data) {
            }
        });
    };

    function deleteNhomDoiTuong(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/nhomdoituong/delete',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    group_id = "";
                    $('#txtGroupCode').attr('readonly', false);
                    $('#btnInsertGroup').html('Thêm mới');
                    loaddataGroup($('#drPagingGroup').val(), $('#txtSearchGroup').val());
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000,1);
                }
            }, error: function(data) {
            }
        });
    };

//Khối----------------------------------------------------------------------------------------
    function loaddataUnit(row, keySearch) {
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit : row,
            key: keySearch
        };
            $.ajax({
                type: "POST",
                url: '/danh-muc/khoi/loadKhoi',
                data: JSON.stringify(o),
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {

                    SETUP_PAGING_NGHILC(dataget, function () {
                        loaddataUnit(row, keySearch);
                    });
                    var v_status = "";
                    $('#dataUnit').html("");
                    data = dataget.data;
                    if(data.length>0){
                        for (var i = 0; i < data.length; i++) {
                            if (data[i].unit_active == 1) {
                                v_status = "Đang hoạt động";
                            }
                            else {v_status = "Không hoạt động";}

                            html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            html_show += "<td>"+data[i].unit_code+"</td>";
                            html_show += "<td>"+data[i].unit_name+"</td>";
                            html_show += "<td>"+v_status+"</td>";
                            html_show += "<td>"+formatDateTimes(data[i].updated_at)+"</td>";
                            html_show += "<td>"
                            if(check_Permission_Feature('2')){
                                html_show += "<button data='"+data[i].unit_id+"' onclick='getUnit("+data[i].unit_id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Sửa</button>";
                            }
                            if(check_Permission_Feature('3')){
                                html_show += "<button  onclick='deleteUnit("+data[i].unit_id+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                            }
                            html_show += "</td></tr>";
                        }
                        
                    } else {
                        html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                    }
                    $('#dataUnit').html(html_show);
                }, error: function(dataget) {

                }
            });
        };

    function insertKhoi(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/khoi/insert',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    $('#txtUnitCode').val("");
                    $('#txtUnitName').val("");
                    $('#drUnitActive').val(1);
                    loaddataUnit($('#drPagingUnit').val(), $('#txtSearchUnit').val());
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

    function getKhoibyID(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/khoi/getbyunitid',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                unit_id = data[0]['unit_id'];
                $('#txtUnitCode').val(data[0]['unit_code']);
                $('#txtUnitName').val(data[0]['unit_name']);
                $('#drUnitActive').val(data[0]['unit_active']);
            }, error: function(data) {
            }
        });
    };

    function updateKhoi(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/khoi/update',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    unit_id = "";
                    $('#txtUnitCode').val("");
                    $('#txtUnitName').val("");
                    $('#drUnitActive').val(1);
                    $('#btnInsertUnit').html('Thêm mới');
                    $("#modalAddNew").modal("hide");
                    loaddataUnit($('#drPagingUnit').val(), $('#txtSearchUnit').val());
                }
                
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

    function deleteKhoi(temp) {
        var objJson = JSON.stringify(temp);
        alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/khoi/delete',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                alert(data);
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    unit_id = "";
                    $('#btnInsertUnit').html('Thêm mới');
                }
                
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

//Dân tộc----------------------------------------------------------------------------------------

    function loaddataNation(row, keySearch) {
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit : row,
            key: keySearch
        };
            $.ajax({
                type: "POST",
                url: '/danh-muc/dantoc/loadDantoc',
                data: JSON.stringify(o),
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {

                    SETUP_PAGING_NGHILC(dataget, function () {
                        loaddataNation(row, keySearch);
                    });
                    var v_status = "";
                    $('#dataNation').html("");
                    data = dataget.data;
                    if(data.length>0){
                        for (var i = 0; i < data.length; i++) {
                            if (data[i].nationals_active == 1) {
                                v_status = "Đang hoạt động";
                            }
                            else {v_status = "Không hoạt động";}

                            html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            html_show += "<td>"+data[i].nationals_code+"</td>";
                            html_show += "<td>"+data[i].nationals_name+"</td>";
                            html_show += "<td>"+v_status+"</td>";
                            html_show += "<td>"+formatDateTimes(data[i].updated_at)+"</td>";
                            html_show += "<td>"
                            if(check_Permission_Feature('2')){
                                html_show += "<button data='"+data[i].nationals_id+"' onclick='getNation("+data[i].nationals_id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Sửa</button>";
                            }
                            if(check_Permission_Feature('3')){
                                html_show += "<button  onclick='deleteNation("+data[i].nationals_id+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                            }
                            html_show += "</td></tr>";
                        }
                        
                    } else {
                        html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                    }
                    $('#dataNation').html(html_show);
                }, error: function(dataget) {

                }
            });
        };

    function insertDantoc(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/dantoc/insert',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    nation_id = "";
                    $('#txtNationCode').val("");
                    $('#txtNationName').val("");
                    $('#drNationActive').val(1);
                    loaddataNation($('#drPagingNation').val(), $('#txtSearchNational').val());
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

    function getDantocbyID(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/dantoc/getbynationid',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //console.log(data);
                nation_id = data[0]['nationals_id'];
                $('#txtNationCode').val(data[0]['nationals_code']);
                $('#txtNationName').val(data[0]['nationals_name']);
                $('#drNationActive').val(data[0]['nationals_active']);
            }, error: function(data) {
            }
        });
    };

    function updateDantoc(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/dantoc/update',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    $('#txtNationCode').val("");
                    $('#txtNationName').val("");
                    $('#drNationActive').val(1);
                    nation_id = "";
                    $('#btnInsertNation').html('Thêm mới');
                    $('#txtNationCode').attr('readonly', false);
                    $("#modalAddNew").modal("hide");
                    loaddataNation($('#drPagingNation').val(), $('#txtSearchNational').val());
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

    function deleteDantoc(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/dantoc/delete',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //alert(data);
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    nation_id = "";
                    $('#btnInsertNation').html('Thêm mới');
                    $('#txtNationCode').attr('readonly', false);
                }
                
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

//Đối tượng----------------------------------------------------------------------------------------------------------------------
    function loaddataSubject(row, keySearch) {
        
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit : row,
            key: keySearch
        };
            $.ajax({
                type: "POST",
                url: '/danh-muc/doituong/loadDoiTuong',
                data: JSON.stringify(o),
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {

                    SETUP_PAGING_NGHILC(dataget, function () {
                        loaddataSubject(row, keySearch);
                    });
                    
                    $('#dataSubject').html("");
                    data = dataget.data;
                    console.log(data);
                    if(data.length > 0){
                        var v_status = "";
                        var v_active = -1;
                        var subject_code = "";
                        var group_name = "";
                        for (var i = 0; i < data.length; i++) {
                            // if (subject_code == data[i].subject_code) {
                            //     group_name =  group_name + ", " + data[i].group_name;
                            // }
                           // else { group_name =  data[i].group_name; }
                           group_name =  data[i].group_name;
                            subject_code = data[i].subject_code;

                            v_active = data[i].subject_active;
                            if (v_active == 1) {
                                v_status = "Đang hoạt động";
                            }
                            else {v_status = "Không hoạt động";}

                            html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            html_show += "<td>"+subject_code+"</td>";
                            html_show += "<td>"+data[i].subject_name+"</td>";
                            html_show += "<td>"+group_name+"</td>";
                            html_show += "<td>"+v_status+"</td>";
                            html_show += "<td>"+formatDateTimes(data[i].updated_at)+"</td>";
                            if(check_Permission_Feature('2')){
                                html_show += "<td><button data='"+data[i].subject_id+"' onclick='getSubject("+data[i].subject_id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Sửa</button></td>";
                            }
                            if(check_Permission_Feature('3')){
                                html_show += "<td><button  onclick='deleteSubject("+data[i].subject_id+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                            }
                            html_show += "</td></tr>";
                        }
                        
                    } else {
                        html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                    }
                    $('#dataSubject').html(html_show);
                }, error: function(dataget) {

                }
            });
        };

    function loadComboNhomDoiTuong() {
        $.ajax({
            type: "get",
            url: '/danh-muc/load/nhom-doi-tuong',
            success: function(data) {
                //console.log(data);
                $('#drGroupSubject').html("");
                var html_show = "";
                if(data.length >0){
                    //html_show += "<option value=''>---Chọn nhóm đối tượng---</option>";
                    for (var i = data.length - 1; i >= 0; i--) {
                        html_show += "<option value='"+data[i].group_id+"'>"+data[i].group_name+"</option>";
                    }
                    $('#drGroupSubject').html(html_show);
                }else {
                    $('#drGroupSubject').html("<option value=''>---Chưa có chế độ---</option>");
                }
            }, error: function(data) {
            }
        });
    };

    var expanded = false;

    function showCheckboxes() {
        var checkboxes = document.getElementById("checkboxes");
        if (!expanded) {
            checkboxes.style.display = "block";
            expanded = true;
        } else {
            checkboxes.style.display = "none";
            expanded = false;
        }
    }

    function popupConfirmDeleteSubject(){
        $("#modalDeleteSubject").modal("show");
    }

    function insertupdateDoiTuong() {
        messageValidate = "";
        validateInput("SUBJECT");
        if (messageValidate !== "") {
            utility.messagehide("group_message", messageValidate, 1, 5000);
            return;
        }

        var arrSubID = [];
        var $el = $(".multiselect-container");
        $el.find('li.active input').each(function(){
            arrSubID.push({value:$(this).val()});
        });

        // var arrData = "";
        // var arrGroupID = data['arrGroupID'];
        // for (var i = 0; i < arrGroupID.length; i++) {
        //     arrData += (arrGroupID[i]['subject_history_group_id']) + ",";    
        // }
        // var item = arrData.split(",");

        var objData = {
            SUBJECTID: subject_id,
            SUBJECTCODE: $('#txtSubjectCode').val(),
            SUBJECTNAME: $('#txtSubjectName').val(),
            SUBJECTACTIVE: $('#drSubjectActive').val(),
            ARRGROUPID: arrSubID
        };
        var objJson = JSON.stringify(objData);
        //alert(objJson);
        var url_part = '';
        if (subject_id !== "" && subject_id > 0) {url_part = '/danh-muc/doituong/update'}
        else { url_part = '/danh-muc/doituong/insert'; }

        $.ajax({
            type: "POST",
            url: url_part,
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //console.log(data);
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    $('#txtSubjectCode').attr('readonly', false);
                    $('#txtSubjectCode').val("");
                    $('#txtSubjectName').val("");
                    $('#drSubjectActive').val(1);
                    subject_id = "";
                    $('#btnInsertSubject').html('Thêm mới');

                    loaddataSubject($('#drPagingSubject').val(), $('#txtSearchSubject').val());
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

    function getDoituongbyID() {
        var objData = {
            SUBJECTID: subject_id
        };
        var objJson = JSON.stringify(objData);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/doituong/getbysubjectid',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                subject_id = data[0]['subject_id'];
                $('#txtSubjectCode').val(data[0]['subject_code']);
                $('#txtSubjectName').val(data[0]['subject_name']);
                $('#drSubjectActive').val(data[0]['subject_active']);
            }, error: function(data) {
            }
        });
    };

    function getNhombyDoituongID(callback) {
        var objData = {
            SUBJECTID: subject_id
        };
        var objJson = JSON.stringify(objData);
        //alert(objJson);
        return $.ajax({
            type: "POST",
            url:'/danh-muc/doituong/getlistgroupbysubjectid',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //Set checked Checkbox Subject-------------------------------------------------------------------------------------
                //console.log(data);
                for (var i = 0; i < data.length; i++) {
                    $('input[type=checkbox]').each(function () {
                        if (data[i]['subject_history_group_id'] == $(this).val()) {
                            $(this).attr('checked', 'checked');
                            //console.log($(this).val());
                        }
                    });
                }

                if(callback != null){
                    callback(data);
                }
            }, error: function(data) {
            }
        });
    };

    function updateDoiTuong() {
        popupAddnewSubject();
        var arrGroupID = [];
        $(':checkbox:checked').each(function(i){
            arrGroupID[i] = $(this).val();
        });

        var objData = {
            SUBJECTID: subject_id,
            SUBJECTNAME: $('#txtSubjectName').val(),
            SUBJECTACTIVE: $('#drSubjectActive').val(),
            ARRGROUPID: arrGroupID
        };
        var objJson = JSON.stringify(objData);
        alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/doituong/update',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    $('#txtSubjectCode').val("");
                    $('#txtSubjectName').val("");
                    $('#drSubjectActive').val(1);
                    subject_id = "";
                    loaddataSubject($('#drPagingSubject').val(), $('#txtSearchSubject').val());
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

    function deleteDoiTuong() {
        var objData = {
            SUBJECTID: subject_id
        };
        var objJson = JSON.stringify(objData);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/doituong/delete',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    subject_id = "";
                    $('#btnInsertSubject').html('Thêm mới');
                }
                
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

//Trường----------------------------------------------------------------------------------------
    function loaddataSchool(row, keySearch) {
        
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit : row,
            key: keySearch
        };
            $.ajax({
                type: "POST",
                url: '/danh-muc/truong/loadTruong',
                data: JSON.stringify(o),
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {

                    SETUP_PAGING_NGHILC(dataget, function () {
                        loaddataSchool(row, keySearch);
                    });
                    var v_status = "";
                    $('#dataSchool').html("");
                    data = dataget.data;
                    if(data.length>0){
                        for (var i = 0; i < data.length; i++) {
                            if (data[i].schools_active == 1) {
                                v_status = "Đang hoạt động";
                            }
                            else {v_status = "Không hoạt động";}

                            html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            html_show += "<td>"+data[i].schools_code+"</td>";
                            html_show += "<td>"+data[i].schools_name+"</td>";
                            html_show += "<td>"+data[i].unit_name+"</td>";
                            html_show += "<td>"+v_status+"</td>";
                            html_show += "<td>"+formatDateTimes(data[i].updated_at)+"</td>";
                            html_show += "<td>"
                            if(check_Permission_Feature('2')){
                                html_show += "<button data='"+data[i].schools_id+"' onclick='getSchool("+data[i].schools_id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Sửa</button>";
                            }
                            if(check_Permission_Feature('3')){
                                html_show += "<button  onclick='deleteSchool("+data[i].schools_id+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                            }
                            html_show += "</td></tr>";
                        }
                        
                    } else {
                        html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                    }
                    $('#dataSchool').html(html_show);
                }, error: function(dataget) {

                }
            });
        };

    function insertTruong(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/truong/insert',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    $('#txtSchoolCode').val("");
                    $('#txtSchoolName').val("");
                    $('#drUnit').val("");
                    $('#drSchoolActive').val(1);
                    loaddataSchool($('#drPagingSchool').val(), $('#txtSearchSchool').val());
                }
                if (data['error'] !== "" && data['error'] !== undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

    function getTruongbyID(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/truong/getbyschoolid',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                console.log(data);
                school_id = data[0]['schools_id'];
                $('#txtSchoolCode').val(data[0]['schools_code']);
                $('#txtSchoolName').val(data[0]['schools_name']);                
                $('#drUnit').val(data[0]['schools_unit_id']);
                $('#drSchoolActive').val(data[0]['schools_active']);
            }, error: function(data) {
            }
        });
    };

    function updateTruong(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/truong/update',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    $('#txtSchoolCode').val("");
                    $('#txtSchoolName').val("");
                    $('#drUnit').val("");
                    $('#drSchoolActive').val(1);
                    school_id = "";
                    $('#btnInsertSchool').html('Thêm mới');
                    $("#modalAddNew").modal("hide");
                    loaddataSchool($('#drPagingSchool').val(), $('#txtSearchSchool').val());
                }
                if (data['error'] !== "" && data['error'] !== undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

    function deleteTruong(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/truong/delete',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    nation_id = "";
                    $('#btnInsertSchool').html('Thêm mới');
                }
                
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

//Lớp----------------------------------------------------------------------------------------
    function loaddataClass(row, keySearch) {
        
        var html_show = "";
        var o = {
            start: (GET_START_RECORD_NGHILC()),
            limit : row,
            key: keySearch
        };
            $.ajax({
                type: "POST",
                url: '/danh-muc/lop/loadLop',
                data: JSON.stringify(o),
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(dataget) {

                    SETUP_PAGING_NGHILC(dataget, function () {
                        loaddataClass(row, keySearch);
                    });
                    var v_status = "";
                    $('#dataClass').html("");
                    data = dataget.data;
                    if(data.length>0){
                        for (var i = 0; i < data.length; i++) {
                            if (data[i].class_active == 1) {
                                v_status = "Đang hoạt động";
                            }
                            else {v_status = "Không hoạt động";}

                            html_show += "<tr><td class='text-center'>"+(i + 1 + (GET_START_RECORD_NGHILC() * row))+"</td>";
                            html_show += "<td>"+data[i].class_code+"</td>";
                            html_show += "<td>"+data[i].class_name+"</td>";
                            html_show += "<td>"+data[i].schools_name+"</td>";
                            html_show += "<td>"+data[i].level_name+"</td>";
                            html_show += "<td>"+v_status+"</td>";
                            html_show += "<td>"+formatDateTimes(data[i].updated_at)+"</td>";
                            html_show += "<td>"
                            if(check_Permission_Feature('2')){
                                html_show += "<button data='"+data[i].class_id+"' onclick='getClass("+data[i].class_id+");' class='btn btn-info btn-xs' id='editor_editss'><i class='glyphicon glyphicon-pencil'></i> Sửa</button>";
                            }
                            if(check_Permission_Feature('3')){
                                html_show += "<button  onclick='deleteClass("+data[i].class_id+");'  class='btn btn-danger btn-xs editor_remove'><i class='glyphicon glyphicon-remove'></i> Xóa</button>";
                            }
                            html_show += "</td></tr>";
                        }
                        
                    } else {
                        html_show += "<tr><td colspan='50' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                    }
                    $('#dataClass').html(html_show);
                }, error: function(dataget) {

                }
            });
        };

    function getLevelbySchool(objData, callback) {
        var objJson = JSON.stringify({SCHOOLID: objData});
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/lop/getLevelbySchoolID',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                $('#drLevel').html("");
                var html_show = "";
                     
                html_show += "<option value=''>-- Chọn khối lớp --</option>";
                for (var i = 0; i < data.length; i++) {
                    html_show += "<option value='"+data[i].level_id+"'>"+data[i].level_name+"</option>";
                }
                $('#drLevel').html(html_show);

                if(callback != null){
                    callback(data);
                }
            }, error: function(data) {
            }
        });
    };

    function insertLop(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/lop/insert',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    $('#txtClassCode').val("");
                    $('#txtClassName').val("");
                    $('#drSchool').val("");
                    $('#drLevel').val("");
                    $('#drClassActive').val(1);
                    $("#drLevel").attr("disabled", true);
                    loaddataClass($('#drPagingClass').val(), $('#txtSearchClass').val());
                }
                if (data['error'] !== "" && data['error'] !== undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

    function getLopbyID(temp, callback) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/lop/getbyclassid',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //console.log(data);
                school_id = data[0]['class_id'];
                $('#drLevel').val(data[0]['class_level_id']);

                if(callback != null){
                    callback(data);
                }
            }, error: function(data) {
            }
        });
    };

    function updateLop(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/lop/update',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    class_id = "";
                    $('#txtClassCode').attr('readonly', false);
                    $('#txtClassCode').val("");
                    $('#txtClassName').val("");
                    $('#drSchool').val("");
                    $('#drLevel').val("");
                    $('#drClassActive').val(1);
                    $('#btnInsertClass').html('Thêm mới');
                    $("#drLevel").attr("disabled", true);
                    $("#modalAddNew").modal("hide");
                    loaddataClass($('#drPagingClass').val(), $('#txtSearchClass').val());
                }
                if (data['error'] !== "" && data['error'] !== undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

    function deleteLop(temp) {
        var objJson = JSON.stringify(temp);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/lop/delete',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    $('#btnInsertClass').html('Thêm mới');
                    nation_id = "";
                }
                
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

//Xã Phường---------------------------------------------------------------------------------------
    function getSitebyLevel(objData, idchoise = 0) {
        var objJson = JSON.stringify({SITELEVEL: objData});
        // alert(objJson);
        return $.ajax({
            type: "POST",
            url:'/danh-muc/xaphuong/loadXaPhuongbyLevel',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                
                if (objData > 1) {
                    var datalv1 = data.LEVEL1;
                    var datalv2 = data.LEVEL2;
                    $('#drSiteParents').html("");
                    if (datalv2.length > 0) {
                        $("#drSiteParents").removeAttr("disabled");
                        var html_show = "";
                        html_show += "<option value='0'>-- Chọn cấp trực thuộc --</option>";
                        for (var j = 0; j < datalv2.length; j++) {
                            html_show +="<optgroup label='"+datalv2[j].site_name+"'>";
                            if (datalv1.length > 0) {
                                for (var i = 0; i < datalv1.length; i++) {
                                    if (parseInt(datalv2[j].site_id) == parseInt(datalv1[i].site_parent_id)) {
                                        html_show += "<option value='"+datalv1[i].site_id+"'>"+datalv1[i].site_name+"</option>";
                                    }
                                }
                            }
                            else {
                                $('#drSiteParents').html("<option value=''>-- Chưa có cấp trực thuộc --</option>");
                                return;
                            }
                            html_show +="</optgroup>";
                        }
                        $('#drSiteParents').html(html_show);
                        if (idchoise !== null && idchoise !== "" && idchoise > 0) {
                            $('#drSiteParents').val(idchoise);
                        }
                    }
                    else if (datalv2.length == 0 && datalv1.length > 0) {
                        if (datalv1.length > 0) {
                            $("#drSiteParents").removeAttr("disabled");
                            
                            var html_show = "";
                                 
                            html_show += "<option value='0'>-- Chọn cấp trực thuộc --</option>";
                            for (var i = 0; i < datalv1.length; i++) {
                                html_show += "<option value='"+datalv1[i].site_id+"'>"+datalv1[i].site_name+"</option>";
                            }
                            $('#drSiteParents').html(html_show);

                            if (idchoise !== null && idchoise !== "" && idchoise > 0) {
                                $('#drSiteParents').val(idchoise);
                            }
                        }
                    }
                    else {
                        $('#drSiteParents').html("<option value=''>-- Không có cấp trực thuộc --</option>");
                    }
                }
                else {
                    $("#drSiteParents").attr("disabled", true);
                    $('#drSiteParents').html("<option value=''>-- Không có cấp trực thuộc --</option>");
                }
            }, error: function(data) {
            }
        });
    };

    function loadComboXaPhuong() {
        $.ajax({
            type: "get",
            url:'/danh-muc/xaphuong/loadcomboXaPhuong',
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                $('#drSiteParent').html("");
                var html_show = "";
                     
                html_show += "<option value=''>-- Chọn cấp trực thuộc --</option>";
                for (var i = 0; i < data.length; i++) {
                    html_show += "<option value='"+data[i].site_id+"'>"+data[i].site_name+"</option>";
                }
                $('#drSiteParent').html(html_show);
            }, error: function(data) {
            }
        });
    };

    function insertSite(objData) {
        var objJson = JSON.stringify(objData);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/xaphuong/insert',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //alert(data);
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    resetXaPhuong();
                    $("#using_json_2").jstree("refresh");
                }
                
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

    function updateSite(objData) {
        var objJson = JSON.stringify(objData);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/xaphuong/update',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //alert(data);
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    resetXaPhuong();
                    $("#using_json_2").jstree("refresh");
                }
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

    function deleteSite(objData) {
        var objJson = JSON.stringify(objData);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/xaphuong/delete',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //alert(data);
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    resetXaPhuong();
                    $("#using_json_2").jstree("refresh");
                }
                
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

    function resetXaPhuong(){
        $('#txtSiteCode').focus();
        $('#txtSiteID').val("");
        $('#txtSiteCode').val("");
        $('#txtSiteName').val("");
        $('#drSiteLevel').val(0);
        $("#drSiteParents").attr("disabled", true);
        $("#drSiteParents").val('').select2();
        $("#drSiteParents").html('<option value="0">-- Chọn cấp trực thuộc --</option>');
        $('#drSiteActive').val(1);
        $("#btnInsertSite").html("Thêm mới");
        site_id = "";
        $("#btnDeleteSite").attr("disabled", true);        
        $("#txtSiteCode").attr("disabled", false);
    }

//Phân loại xã---------------------------------------------------------------------------------------

    function loadComboWard(callback) {
        $.ajax({
            type: "get",
            url:'/danh-muc/phanloaixa/loadcomboPLXa',
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                $('#drWardParent').html("");
                var html_show = "";
                //console.log(data);
                html_show += "<option value='0'>-- Chọn cấp trực thuộc --</option>";
                for (var i = 0; i < data.length; i++) {
                    // for (var j = 0; j < data.length; j++) {
                    //     if (data[i].wards_id == data[j].wards_parent_id) {
                    //         html_show += "<option data='"+data[i].wards_level+"' value='"+data[i].wards_id+"'>"+data[i].wards_name+"</option>";
                    //     }
                    // }
                    html_show += "<option data='"+data[i].wards_level+"' value='"+data[i].wards_id+"'>"+data[i].wards_name+"</option>";
                }
                $('#drWardParent').html(html_show);

                if(callback != null){
                    callback(data);
                }
            }, error: function(data) {
            }
        });
    };

    function insertWard(objData) {
        var objJson = JSON.stringify(objData);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/phanloaixa/insert',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //alert(data);
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    $("#using_json_2").jstree(true).refresh();
                    $('#txtWardID').val('');
                    $('#txtWardCode').val('');
                    $('#txtWardName').val('');
                    loadComboWard();
                    $('#drWardActive').val(1);
                    ward_id = "";
                }
                
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

    function updateWard(objData) {
        var objJson = JSON.stringify(objData);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/phanloaixa/update',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //alert(data);
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    $('#txtWardCode').val("");
                    $('#txtWardName').val("");
                    $('#drWardActive').val(1);
                    $('#txtWardID').val('');
                    $("#btnInsertWard").html("Thêm mới");
                    ward_id = "";
                    loadComboWard();
                    $("#using_json_2").jstree("refresh");
                }
                
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

    function deleteWard(objData) {
        var objJson = JSON.stringify(objData);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/phanloaixa/delete',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //console.log(data);
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    $('#txtWardCode').val("");
                    $('#txtWardName').val("");                    
                    loadComboWard();
                    $('#drWardActive').val(1);
                    $('#txtWardID').val("");
                    $("#btnInsertWard").html("Thêm mới");
                    ward_id = "";
                    $("#using_json_2").jstree("refresh");
                }
                
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

//Phòng ban---------------------------------------------------------------------------------------

    function loadComboDepartment(callback) {
        return $.ajax({
            type: "get",
            url:'/danh-muc/loadcomboDepartment',
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                $('#drpDepartment').html("");
                var html_show = "";
                     
                html_show += "<option value='0'>-- Chọn cấp trực thuộc --</option>";
                for (var i = 0; i < data.length; i++) {
                    html_show += "<option data='"+data[i].department_level+"' value='"+data[i].department_id+"'>"+data[i].department_name+"</option>";
                }
                $('#drpDepartment').html(html_show);

                if(callback != null){
                    callback(data);
                }
            }, error: function(data) {
            }
        });
    };

    function insertDepartment(objData) {
        var objJson = JSON.stringify(objData);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/insert',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //alert(data);
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    $('#txtDepartCode').val("");
                    $('#txtDepartName').val("");                    
                    loadComboDepartment( function(data){
                    });
                    $('#drDepartActive').val(1);
                    depart_id = "";

                    //$("#using_json_2").jstree("loaded");
                    $("#using_json_2").jstree("refresh");
                }
                
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

    function updateDepartment(objData) {
        var objJson = JSON.stringify(objData);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/update',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //alert(data);
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    $('#txtDepartCode').val("");
                    $('#txtDepartName').val("");
                    loadComboDepartment( function(data){
                    });
                    $('#drDepartActive').val(1);
                    $("#btnInsertDepartment").html("Thêm mới");
                    $('#txtDepartID').val("");
                    depart_id = "";
                    $("#using_json_2").jstree("refresh");
                }
                
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

    function deleteDepartment(objData) {
        var objJson = JSON.stringify(objData);
        //alert(objJson);
        $.ajax({
            type: "POST",
            url:'/danh-muc/delete',
            data: objJson,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                //alert(data);
                if (data['success'] !== "" && data['success'] !== undefined) {
                    utility.message("Thông báo",data['success'],null,3000);
                    $('#txtDepartCode').val("");
                    $('#txtDepartName').val("");
                    loadComboDepartment( function(data){
                    });
                    $('#drDepartActive').val(1);
                    $("#btnInsertDepartment").html("Thêm mới");
                    $('#txtDepartID').val("");
                    depart_id = "";
                    $("#using_json_2").jstree("refresh");
                }
                
                if (data['error'] != "" && data['error'] != undefined) {
                    utility.message("Thông báo",data['error'],null,3000);
                }
            }, error: function(data) {
            }
        });
    };

//Validate input all form

    var messageValidate = "";

    function validateInput(formname)
    {
        if (formname == "GROUP") {
            var v_groupcode = $('#txtGroupCode').val();
            var v_groupname = $('#txtGroupName').val();

            v_groupcode = v_groupcode.replace(/[\n\t\r]/g,"");
            v_groupname = v_groupname.replace(/[\n\t\r]/g,"");

            if (v_groupcode.trim() == "") {        
                messageValidate = "Vui lòng nhập mã chế độ!";
                $('#txtGroupCode').focus();
                return messageValidate;
            }else if (v_groupcode.length > 200) {
                messageValidate = "Mã chế độ không được vượt quá 200 ký tự!";
                $('#txtGroupCode').focus();
                $('#txtGroupCode').val("");
                return messageValidate;
            }
            else{
                var specialChars = "!@#$%^&*()+=[]\\\';,./{}|\":<>?";
                var unicodeChars = "àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđÁÀẠẢÃÂẤẦẬẨẪĂẮẰẶẲẴÉÈẸẺẼÊẾỀỆỂỄÍÌỊỈĨÓÒỌỎÕÔỐỒỘỔỖƠỚỜỢỞỠÚÙỤỦŨƯỨỪỰỬỮÝỲỴỶỸĐ";

                for (var i = 0; i < v_groupcode.length; i++) {
                    if (specialChars.indexOf(v_groupcode.charAt(i)) != -1) {
                        messageValidate = "Mã nhập không được chứa ký tự đặc biệt!";
                        $('#txtGroupCode').focus();
                        $('#txtGroupCode').val("");
                        return messageValidate;
                    }

                    if (unicodeChars.indexOf(v_groupcode.charAt(i)) != -1) {
                        messageValidate = "Mã nhập không được chứa ký tự có dấu!";
                        $('#txtGroupCode').focus();
                        $('#txtGroupCode').val("");
                        return messageValidate;
                    }
                }
                $('#txtGroupCode').focusout();
            }        

            //Validate Name----------------------------------------------------------------------------------------
            if (v_groupname.trim() == "") {
                messageValidate = "Vui lòng nhập tên chế độ!";
                $('#txtGroupName').focus();
                return messageValidate;
            }else if (v_groupname.length > 200) {
                messageValidate = "Tên chế độ không được vượt quá 200 ký tự!";
                $('#txtGroupName').focus();
                $('#txtGroupName').val("");
                return messageValidate;
            }
            else{
                var specialChars = "#/|\\";

                for (var i = 0; i < v_groupname.length; i++) {
                    if (specialChars.indexOf(v_groupname.charAt(i)) != -1) {
                        messageValidate = "Tên chế độ không được chứa ký tự #, /, |, \\";
                        $('#txtGroupName').focus();
                        return messageValidate;
                    }
                }

                $('#txtGroupName').focusout();
            }
        }
        if (formname == "UNIT") {
            var v_unitcode = $('#txtUnitCode').val();
            var v_unitname = $('#txtUnitName').val();
            
            v_unitcode = v_unitcode.replace(/[\n\t\r]/g,"");
            v_unitname = v_unitname.replace(/[\n\t\r]/g,"");

            if (v_unitcode.trim() == "") {        
                messageValidate = "Vui lòng nhập mã khối!";
                $('#txtUnitCode').focus();
                return messageValidate;
            }else if (v_unitcode.length > 200) {
                messageValidate = "Mã khối không được vượt quá 200 ký tự!";
                $('#txtUnitCode').focus();
                $('#txtUnitCode').val("");
                return messageValidate;
            }
            else{
                var specialChars = "!@#$%^&*()+=[]\\\';,./{}|\":<>?";
                var unicodeChars = "àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđÁÀẠẢÃÂẤẦẬẨẪĂẮẰẶẲẴÉÈẸẺẼÊẾỀỆỂỄÍÌỊỈĨÓÒỌỎÕÔỐỒỘỔỖƠỚỜỢỞỠÚÙỤỦŨƯỨỪỰỬỮÝỲỴỶỸĐ";

                for (var i = 0; i < v_unitcode.length; i++) {
                    if (specialChars.indexOf(v_unitcode.charAt(i)) != -1) {
                        messageValidate = "Mã nhập không được chứa ký tự đặc biệt!";
                        $('#txtUnitCode').focus();
                        $('#txtUnitCode').val("");
                        return messageValidate;
                    }

                    if (unicodeChars.indexOf(v_unitcode.charAt(i)) != -1) {
                        messageValidate = "Mã nhập không được chứa ký tự có dấu!";
                        $('#txtUnitCode').focus();
                        $('#txtUnitCode').val("");
                        return messageValidate;
                    }
                }
                $('#txtUnitCode').focusout();
            }        

            //Validate Name----------------------------------------------------------------------------------------
            if (v_unitname.trim() == "") {
                messageValidate = "Vui lòng nhập tên khối!";
                $('#txtUnitName').focus();
                return messageValidate;
            }else if (v_unitname.length > 200) {
                messageValidate = "Tên khối không được vượt quá 200 ký tự!";
                $('#txtUnitName').focus();
                $('#txtUnitName').val("");
                return messageValidate;
            }
            else{
                var specialChars = "#/|\\";

                for (var i = 0; i < v_unitname.length; i++) {
                    if (specialChars.indexOf(v_unitname.charAt(i)) != -1) {
                        messageValidate = "Tên khối không được chứa ký tự #, /, |, \\";
                        $('#txtUnitName').focus();
                        return messageValidate;
                    }
                }

                $('#txtUnitName').focusout();
            }
        }
        if (formname == "NATION") {
            var v_nationcode = $('#txtNationCode').val();
            var v_nationname = $('#txtNationName').val();
            
            v_nationcode = v_nationcode.replace(/[\n\t\r]/g,"");
            v_nationname = v_nationname.replace(/[\n\t\r]/g,"");

            if (v_nationcode.trim() == "") {        
                messageValidate = "Vui lòng nhập mã dân tộc!";
                $('#txtNationCode').focus();
                return messageValidate;
            }else if (v_nationcode.length > 200) {
                messageValidate = "Mã dân tộc không được vượt quá 200 ký tự!";
                $('#txtNationCode').focus();
                $('#txtNationCode').val("");
                return messageValidate;
            }
            else{
                var specialChars = "!@#$%^&*()+=[]\\\';,./{}|\":<>?";
                var unicodeChars = "àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđÁÀẠẢÃÂẤẦẬẨẪĂẮẰẶẲẴÉÈẸẺẼÊẾỀỆỂỄÍÌỊỈĨÓÒỌỎÕÔỐỒỘỔỖƠỚỜỢỞỠÚÙỤỦŨƯỨỪỰỬỮÝỲỴỶỸĐ";

                for (var i = 0; i < v_nationcode.length; i++) {
                    if (specialChars.indexOf(v_nationcode.charAt(i)) != -1) {
                        messageValidate = "Mã nhập không được chứa ký tự đặc biệt!";
                        $('#txtNationCode').focus();
                        $('#txtNationCode').val("");
                        return messageValidate;
                    }

                    if (unicodeChars.indexOf(v_nationcode.charAt(i)) != -1) {
                        messageValidate = "Mã nhập không được chứa ký tự có dấu!";
                        $('#txtNationCode').focus();
                        $('#txtNationCode').val("");
                        return messageValidate;
                    }
                }
                $('#txtNationCode').focusout();
            }        

            //Validate Name----------------------------------------------------------------------------------------
            if (v_nationname.trim() == "") {
                messageValidate = "Vui lòng nhập tên dân tộc!";
                $('#txtNationName').focus();
                return messageValidate;
            }else if (v_nationname.length > 200) {
                messageValidate = "Tên dân tộc không được vượt quá 200 ký tự!";
                $('#txtNationName').focus();
                $('#txtNationName').val("");
                return messageValidate;
            }
            else{
                var specialChars = "#/|\\";

                for (var i = 0; i < v_nationname.length; i++) {
                    if (specialChars.indexOf(v_nationname.charAt(i)) != -1) {
                        messageValidate = "Tên dân tộc không được chứa ký tự #, /, |, \\";
                        $('#txtNationName').focus();
                        return messageValidate;
                    }
                }

                $('#txtNationName').focusout();
            }
        }
        if (formname == "SUBJECT") {
            var v_subjectcode = $('#txtSubjectCode').val();
            var v_subjectname = $('#txtSubjectName').val();
            
            v_subjectcode = v_subjectcode.replace(/[\n\t\r]/g,"");
            v_subjectname = v_subjectname.replace(/[\n\t\r]/g,"");

            var arrSubID = [];
            var $el = $(".multiselect-container");
            $el.find('li.active input').each(function(){
                arrSubID.push({value:$(this).val()});
            });

            if (v_subjectcode.trim() == "") {        
                messageValidate = "Vui lòng nhập mã nhóm đối tượng!";
                $('#txtSubjectCode').focus();
                return messageValidate;
            }else if (v_subjectcode.length > 200) {
                messageValidate = "Mã nhóm đối tượng không được vượt quá 200 ký tự!";
                $('#txtSubjectCode').focus();
                $('#txtSubjectCode').val("");
                return messageValidate;
            }
            else{
                var specialChars = "!@#$%^&*()+=[]\\\';,./{}|\":<>?";
                var unicodeChars = "àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđÁÀẠẢÃÂẤẦẬẨẪĂẮẰẶẲẴÉÈẸẺẼÊẾỀỆỂỄÍÌỊỈĨÓÒỌỎÕÔỐỒỘỔỖƠỚỜỢỞỠÚÙỤỦŨƯỨỪỰỬỮÝỲỴỶỸĐ";

                for (var i = 0; i < v_subjectcode.length; i++) {
                    if (specialChars.indexOf(v_subjectcode.charAt(i)) != -1) {
                        messageValidate = "Mã nhập không được chứa ký tự đặc biệt!";
                        $('#txtSubjectCode').focus();
                        $('#txtSubjectCode').val("");
                        return messageValidate;
                    }

                    if (unicodeChars.indexOf(v_subjectcode.charAt(i)) != -1) {
                        messageValidate = "Mã nhập không được chứa ký tự có dấu!";
                        $('#txtSubjectCode').focus();
                        $('#txtSubjectCode').val("");
                        return messageValidate;
                    }
                }
                $('#txtSubjectCode').focusout();
            }        

            //Validate Name----------------------------------------------------------------------------------------
            if (v_subjectname.trim() == "") {
                messageValidate = "Vui lòng nhập tên nhóm đối tượng!";
                $('#txtSubjectName').focus();
                return messageValidate;
            }else if (v_subjectname.length > 200) {
                messageValidate = "Tên nhóm đối tượng không được vượt quá 200 ký tự!";
                $('#txtSubjectName').focus();
                $('#txtSubjectName').val("");
                return messageValidate;
            }
            else{
                var specialChars = "#/|\\";

                for (var i = 0; i < v_subjectname.length; i++) {
                    if (specialChars.indexOf(v_subjectname.charAt(i)) != -1) {
                        messageValidate = "Tên nhóm đối tượng không được chứa ký tự #, /, |, \\";
                        $('#txtSubjectName').focus();
                        return messageValidate;
                    }
                }

                $('#txtSubjectName').focusout();
            }

            //Validate Group----------------------------------------------------------------------------------------
            // if (arrSubID.length <= 0) {
            //     messageValidate = "Vui lòng chọn nhóm đối tượng!";
            //     return messageValidate;
            // }
        }
        if (formname == "SCHOOL") {
            var v_schoolcode = $('#txtSchoolCode').val();
            var v_schoolname = $('#txtSchoolName').val();
            var v_unitid = $('#drUnit').val();
            
            v_schoolcode = v_schoolcode.replace(/[\n\t\r]/g,"");
            v_schoolname = v_schoolname.replace(/[\n\t\r]/g,"");

            if (v_schoolcode.trim() == "") {        
                messageValidate = "Vui lòng nhập mã trường!";
                $('#txtSchoolCode').focus();
                return messageValidate;
            }else if (v_schoolcode.length > 200) {
                messageValidate = "Mã trường không được vượt quá 200 ký tự!";
                $('#txtSchoolCode').focus();
                $('#txtSchoolCode').val("");
                return messageValidate;
            }
            else{
                var specialChars = "!@#$%^&*()+=[]\\\';,./{}|\":<>?";
                var unicodeChars = "àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđÁÀẠẢÃÂẤẦẬẨẪĂẮẰẶẲẴÉÈẸẺẼÊẾỀỆỂỄÍÌỊỈĨÓÒỌỎÕÔỐỒỘỔỖƠỚỜỢỞỠÚÙỤỦŨƯỨỪỰỬỮÝỲỴỶỸĐ";

                for (var i = 0; i < v_schoolcode.length; i++) {
                    if (specialChars.indexOf(v_schoolcode.charAt(i)) != -1) {
                        messageValidate = "Mã nhập không được chứa ký tự đặc biệt!";
                        $('#txtSchoolCode').focus();
                        $('#txtSchoolCode').val("");
                        return messageValidate;
                    }

                    if (unicodeChars.indexOf(v_schoolcode.charAt(i)) != -1) {
                        messageValidate = "Mã nhập không được chứa ký tự có dấu!";
                        $('#txtSchoolCode').focus();
                        $('#txtSchoolCode').val("");
                        return messageValidate;
                    }
                }
                $('#txtSchoolCode').focusout();
            }        

            //Validate Name----------------------------------------------------------------------------------------
            if (v_schoolname.trim() == "") {
                messageValidate = "Vui lòng nhập tên trường!";
                $('#txtSchoolName').focus();
                return messageValidate;
            }else if (v_schoolname.length > 200) {
                messageValidate = "Tên trường không được vượt quá 200 ký tự!";
                $('#txtSchoolName').focus();
                $('#txtSchoolName').val("");
                return messageValidate;
            }
            else{
                var specialChars = "#/|\\";

                for (var i = 0; i < v_schoolname.length; i++) {
                    if (specialChars.indexOf(v_schoolname.charAt(i)) != -1) {
                        messageValidate = "Tên trường không được chứa ký tự #, /, |, \\";
                        $('#txtSchoolName').focus();
                        return messageValidate;
                    }
                }

                $('#txtSchoolName').focusout();
            }

            //Validate Unit
            if (v_unitid == "" || v_unitid == 0) {
                messageValidate = "Vui lòng chọn khối!";
                return messageValidate;
            }
        }
        if (formname == "CLASS") {
            var v_classcode = $('#txtClassCode').val();
            var v_classname = $('#txtClassName').val();
            var v_schoolid = $('#drSchool').val();
            var v_levelid = $('#drLevel').val();
            
            v_classcode = v_classcode.replace(/[\n\t\r]/g,"");
            v_classname = v_classname.replace(/[\n\t\r]/g,"");

            if (v_classcode.trim() == "") {        
                messageValidate = "Vui lòng nhập mã lớp!";
                $('#txtClassCode').focus();
                return messageValidate;
            }else if (v_classcode.length > 200) {
                messageValidate = "Mã lớp không được vượt quá 200 ký tự!";
                $('#txtClassCode').focus();
                $('#txtClassCode').val("");
                return messageValidate;
            }
            else{
                var specialChars = "!@#$%^&*()+=[]\\\';,./{}|\":<>?";
                var unicodeChars = "àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđÁÀẠẢÃÂẤẦẬẨẪĂẮẰẶẲẴÉÈẸẺẼÊẾỀỆỂỄÍÌỊỈĨÓÒỌỎÕÔỐỒỘỔỖƠỚỜỢỞỠÚÙỤỦŨƯỨỪỰỬỮÝỲỴỶỸĐ";

                for (var i = 0; i < v_classcode.length; i++) {
                    if (specialChars.indexOf(v_classcode.charAt(i)) != -1) {
                        messageValidate = "Mã nhập không được chứa ký tự đặc biệt!";
                        $('#txtClassCode').focus();
                        $('#txtClassCode').val("");
                        return messageValidate;
                    }

                    if (unicodeChars.indexOf(v_classcode.charAt(i)) != -1) {
                        messageValidate = "Mã nhập không được chứa ký tự có dấu!";
                        $('#txtClassCode').focus();
                        $('#txtClassCode').val("");
                        return messageValidate;
                    }
                }
                $('#txtClassCode').focusout();
            }        

            //Validate Name----------------------------------------------------------------------------------------
            if (v_classname.trim() == "") {
                messageValidate = "Vui lòng nhập tên lớp!";
                $('#txtClassName').focus();
                return messageValidate;
            }else if (v_classname.length > 200) {
                messageValidate = "Tên lớp không được vượt quá 200 ký tự!";
                $('#txtClassName').focus();
                $('#txtClassName').val("");
                return messageValidate;
            }
            else{
                var specialChars = "#/|\\";

                for (var i = 0; i < v_classname.length; i++) {
                    if (specialChars.indexOf(v_classname.charAt(i)) != -1) {
                        messageValidate = "Tên lớp không được chứa ký tự #, /, |, \\";
                        $('#txtClassName').focus();
                        return messageValidate;
                    }
                }

                $('#txtClassName').focusout();
            }

            //Validate School
            if (v_schoolid == "" || v_schoolid == 0) {
                messageValidate = "Vui lòng chọn trường!";
                return messageValidate;
            }

            //Validate Level
            if (v_levelid == "" || v_levelid == 0) {
                messageValidate = "Vui lòng chọn khối lớp!";
                return messageValidate;
            }
        }
        if (formname == "SITE") {
            var v_sitecode = $('#txtSiteCode').val();
            var v_sitename = $('#txtSiteName').val();
            var v_sitelevel = $('#drSiteLevel').val();
            var v_siteparentid = $('#drSiteParents').val();
            
            v_sitecode = v_sitecode.replace(/[\n\t\r]/g,"");
            v_sitename = v_sitename.replace(/[\n\t\r]/g,"");

            if (v_sitecode.trim() == "") {        
                messageValidate = "Vui lòng nhập mã địa phương!";
                $('#txtSiteCode').focus();
                return messageValidate;
            }else if (v_sitecode.length > 200) {
                messageValidate = "Mã địa phương không được vượt quá 200 ký tự!";
                $('#txtSiteCode').focus();
                $('#txtSiteCode').val("");
                return messageValidate;
            }
            else{
                var specialChars = "!@#$%^&*()+=[]\\\';,./{}|\":<>?";
                var unicodeChars = "àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđÁÀẠẢÃÂẤẦẬẨẪĂẮẰẶẲẴÉÈẸẺẼÊẾỀỆỂỄÍÌỊỈĨÓÒỌỎÕÔỐỒỘỔỖƠỚỜỢỞỠÚÙỤỦŨƯỨỪỰỬỮÝỲỴỶỸĐ";

                for (var i = 0; i < v_sitecode.length; i++) {
                    if (specialChars.indexOf(v_sitecode.charAt(i)) != -1) {
                        messageValidate = "Mã nhập không được chứa ký tự đặc biệt!";
                        $('#txtSiteCode').focus();
                        $('#txtSiteCode').val("");
                        return messageValidate;
                    }

                    if (unicodeChars.indexOf(v_sitecode.charAt(i)) != -1) {
                        messageValidate = "Mã nhập không được chứa ký tự có dấu!";
                        $('#txtSiteCode').focus();
                        $('#txtSiteCode').val("");
                        return messageValidate;
                    }
                }
                $('#txtSiteCode').focusout();
            }        

            //Validate Name----------------------------------------------------------------------------------------
            if (v_sitename.trim() == "") {
                messageValidate = "Vui lòng nhập tên địa phương!";
                $('#txtSiteName').focus();
                return messageValidate;
            }else if (v_sitename.length > 200) {
                messageValidate = "Tên địa phương không được vượt quá 200 ký tự!";
                $('#txtSiteName').focus();
                $('#txtSiteName').val("");
                return messageValidate;
            }
            else{
                var specialChars = "#/|\\";

                for (var i = 0; i < v_sitename.length; i++) {
                    if (specialChars.indexOf(v_sitename.charAt(i)) != -1) {
                        messageValidate = "Tên địa phương không được chứa ký tự #, /, |, \\";
                        $('#txtSiteName').focus();
                        return messageValidate;
                    }
                }

                $('#txtSiteName').focusout();
            }

            //Validate Level and Parent Site
            if (v_sitelevel == "" || v_sitelevel == 0) {
                messageValidate = "Vui lòng chọn cấp hành chính!";
                return messageValidate;
            }
            else if (v_sitelevel == 2 || v_sitelevel == 3) {
                if (v_siteparentid == "" || v_siteparentid == 0) {
                    messageValidate = "Vui lòng chọn địa phương trực thuộc!";
                    return messageValidate;
                }            
            }
        }
        if (formname == "WARD") {
            var v_wardcode = $('#txtWardCode').val();
            var v_wardname = $('#txtWardName').val();
            
            v_wardcode = v_wardcode.replace(/[\n\t\r]/g,"");
            v_wardname = v_wardname.replace(/[\n\t\r]/g,"");

            if (v_wardcode.trim() == "") {        
                messageValidate = "Vui lòng nhập mã phân loại xã!";
                $('#txtWardCode').focus();
                return messageValidate;
            }else if (v_wardcode.length > 200) {
                messageValidate = "Mã phân loại xã không được vượt quá 200 ký tự!";
                $('#txtWardCode').focus();
                $('#txtWardCode').val("");
                return messageValidate;
            }
            else{
                var specialChars = "!@#$%^&*()+=[]\\\';,./{}|\":<>?";
                var unicodeChars = "àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđÁÀẠẢÃÂẤẦẬẨẪĂẮẰẶẲẴÉÈẸẺẼÊẾỀỆỂỄÍÌỊỈĨÓÒỌỎÕÔỐỒỘỔỖƠỚỜỢỞỠÚÙỤỦŨƯỨỪỰỬỮÝỲỴỶỸĐ";

                for (var i = 0; i < v_wardcode.length; i++) {
                    if (specialChars.indexOf(v_wardcode.charAt(i)) != -1) {
                        messageValidate = "Mã nhập không được chứa ký tự đặc biệt!";
                        $('#txtWardCode').focus();
                        $('#txtWardCode').val("");
                        return messageValidate;
                    }

                    if (unicodeChars.indexOf(v_wardcode.charAt(i)) != -1) {
                        messageValidate = "Mã nhập không được chứa ký tự có dấu!";
                        $('#txtWardCode').focus();
                        $('#txtWardCode').val("");
                        return messageValidate;
                    }
                }
                $('#txtWardCode').focusout();
            }        

            //Validate Name----------------------------------------------------------------------------------------
            if (v_wardname.trim() == "") {
                messageValidate = "Vui lòng nhập tên phân loại xã!";
                $('#txtWardName').focus();
                return messageValidate;
            }else if (v_wardname.length > 200) {
                messageValidate = "Tên phân loại xã không được vượt quá 200 ký tự!";
                $('#txtWardName').focus();
                $('#txtWardName').val("");
                return messageValidate;
            }
            else{
                var specialChars = "#/|\\";

                for (var i = 0; i < v_wardname.length; i++) {
                    if (specialChars.indexOf(v_wardname.charAt(i)) != -1) {
                        messageValidate = "Tên phân loại xã không được chứa ký tự #, /, |, \\";
                        $('#txtWardName').focus();
                        return messageValidate;
                    }
                }

                $('#txtWardName').focusout();
            }
        }
        if (formname == "DEPARTMENT") {
            var v_departmentcode = $('#txtDepartCode').val();
            var v_departmentname = $('#txtDepartName').val();
            
            v_departmentcode = v_departmentcode.replace(/[\n\t\r]/g,"");
            v_departmentname = v_departmentname.replace(/[\n\t\r]/g,"");

            if (v_departmentcode.trim() == "") {        
                messageValidate = "Vui lòng nhập mã phòng ban!";
                $('#txtDepartCode').focus();
                return messageValidate;
            }else if (v_departmentcode.length > 200) {
                messageValidate = "Mã phòng ban không được vượt quá 200 ký tự!";
                $('#txtDepartCode').focus();
                $('#txtDepartCode').val("");
                return messageValidate;
            }
            else{
                var specialChars = "!@#$%^&*()+=[]\\\';,./{}|\":<>?";
                var unicodeChars = "àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđÁÀẠẢÃÂẤẦẬẨẪĂẮẰẶẲẴÉÈẸẺẼÊẾỀỆỂỄÍÌỊỈĨÓÒỌỎÕÔỐỒỘỔỖƠỚỜỢỞỠÚÙỤỦŨƯỨỪỰỬỮÝỲỴỶỸĐ";

                for (var i = 0; i < v_departmentcode.length; i++) {
                    if (specialChars.indexOf(v_departmentcode.charAt(i)) != -1) {
                        messageValidate = "Mã nhập không được chứa ký tự đặc biệt!";
                        $('#txtDepartCode').focus();
                        $('#txtDepartCode').val("");
                        return messageValidate;
                    }

                    if (unicodeChars.indexOf(v_departmentcode.charAt(i)) != -1) {
                        messageValidate = "Mã nhập không được chứa ký tự có dấu!";
                        $('#txtDepartCode').focus();
                        $('#txtDepartCode').val("");
                        return messageValidate;
                    }
                }
                $('#txtDepartCode').focusout();
            }        

            //Validate Name----------------------------------------------------------------------------------------
            if (v_departmentname.trim() == "") {
                messageValidate = "Vui lòng nhập tên phòng ban!";
                $('#txtDepartName').focus();
                return messageValidate;
            }else if (v_departmentname.length > 200) {
                messageValidate = "Tên phòng ban không được vượt quá 200 ký tự!";
                $('#txtDepartName').focus();
                $('#txtDepartName').val("");
                return messageValidate;
            }
            else{
                var specialChars = "#/|\\";

                for (var i = 0; i < v_departmentname.length; i++) {
                    if (specialChars.indexOf(v_departmentname.charAt(i)) != -1) {
                        messageValidate = "Tên phòng ban không được chứa ký tự #, /, |, \\";
                        $('#txtDepartName').focus();
                        return messageValidate;
                    }
                }

                $('#txtDepartName').focusout();
            }
        }
    };

    function ValidateNGNA(){
        var schools_id = $('#sltTruongDt').val();
        var amount = $('#txtAmount').val();
        var startDate = $('#startDate').val();
        // var endDate = $('#endDate').val();

        if (schools_id.trim() == null || schools_id.trim() == "" || schools_id.trim() == 0) {
            messageValidate = "Vui lòng chọn trường!";
            return messageValidate;
        }

        if (amount.trim() == null || amount.trim() == "" || amount.trim() == 0) {
            messageValidate = "Vui lòng nhập số người nấu ăn!";
            return messageValidate;
        }

        if (startDate.trim() == null || startDate.trim() == "") {
            messageValidate = "Vui lòng nhập số ngày bắt đầu!";
            return messageValidate;
        }

        // if (endDate.trim() == null || endDate.trim() == "") {
        //     messageValidate = "Vui lòng nhập ngày kết thúc!";
        //     return messageValidate;
        // }
    }

    function ValidateDSHN(){
        var name = $('#txtName').val();
        var birthday = $('#txtBirthday').val();
        var sex = $('#sltSex').val();
        var nation = $('#sltNations').val();
        var type = $('#sltTypeName').val();
        var site2 = $('#sltSite2').val();
        var site1 = $('#sltSite1').val();
        var relation = $('#sltRelationShip').val();
        //Validate Name----------------------------------------------------------------------------------------
        if (name.trim() == "") {
            messageValidate = "Vui lòng nhập họ tên!";
            $('#txtName').focus();
            return messageValidate;
        }else if (name.length > 200) {
            messageValidate = "Họ tên không được vượt quá 200 ký tự!";
            $('#txtName').focus();
            $('#txtName').val("");
            return messageValidate;
        }
        else{
            var specialChars = "#/|\\";

            for (var i = 0; i < name.length; i++) {
                if (specialChars.indexOf(name.charAt(i)) != -1) {
                    messageValidate = "Họ tên không được chứa ký tự #, /, |, \\";
                    $('#txtName').focus();
                    return messageValidate;
                }
            }

            $('#txtName').focusout();
        }
        //Validate Birhtday----------------------------------------------------------------------------------------
        if (birthday.trim() == null || birthday.trim() == "") {
            messageValidate = "Vui lòng nhập ngày sinh!";
            return messageValidate;
        }
        //Validate Sex----------------------------------------------------------------------------------------
        if (sex.trim() == null || sex.trim() == "") {
            messageValidate = "Vui lòng chọn giới tính!";
            return messageValidate;
        }
        //Validate Nation----------------------------------------------------------------------------------------
        if (nation.trim() == null || nation.trim() == "") {
            messageValidate = "Vui lòng chọn dân tộc!";
            return messageValidate;
        }
        //Validate RelationShip----------------------------------------------------------------------------------------
        if (relation.trim() == null || relation.trim() == "") {
            messageValidate = "Vui lòng chọn quan hệ!";
            return messageValidate;
        }
        //Validate Site1----------------------------------------------------------------------------------------
        if (site1.trim() == null || site1.trim() == "") {
            messageValidate = "Vui lòng chọn xã!";
            return messageValidate;
        }
        //Validate Site2----------------------------------------------------------------------------------------
        if (site2.trim() == null || site2.trim() == "") {
            messageValidate = "Vui lòng chọn thôn!";
            return messageValidate;
        }
        //Validate Type----------------------------------------------------------------------------------------
        if (type.trim() == null || type.trim() == "") {
            messageValidate = "Vui lòng chọn diện chính sách!";
            return messageValidate;
        }
    }

//Search AutoComplete-----------------------------------------------------------------------------------------------------------------------
    function autocompleteSearch(idControl, formSearch) {
        var keySearch = "";
        if (formSearch == "GROUP") {
            $('#' + idControl).autocomplete({
                source: function (request, response) {
                    keySearch = $.ui.autocomplete.escapeRegex(request.term).replace(/[%\\\-]/g, '');
                    //console.log(keySearch.length);
                    if (keySearch.length >= 4) {
                        GET_INITIAL_NGHILC();
                        loaddataGroup($('#drPagingGroup').val(),keySearch);
                        
                    }else if(keySearch.length < 4){
                        GET_INITIAL_NGHILC();
                        loaddataGroup($('#drPagingGroup').val(), "");
                    }
                },
                minLength: 0,
                delay: 222,
                autofocus: true
            });
        }

        if (formSearch == "UNIT") {
            $('#' + idControl).autocomplete({
                source: function (request, response) {
                    keySearch = $.ui.autocomplete.escapeRegex(request.term).replace(/[%\\\-]/g, '');
                    //console.log(keySearch.length);
                    if (keySearch.length >= 4) {
                        GET_INITIAL_NGHILC();
                        loaddataUnit($('#drPagingUnit').val(),keySearch);
                        
                    }else if(keySearch.length < 4){
                        GET_INITIAL_NGHILC();
                        loaddataUnit($('#drPagingUnit').val(), "");
                    }
                },
                minLength: 0,
                delay: 222,
                autofocus: true
            });
        }

        if (formSearch == "NATION") {
            $('#' + idControl).autocomplete({
                source: function (request, response) {
                    keySearch = $.ui.autocomplete.escapeRegex(request.term).replace(/[%\\\-]/g, '');
                    //console.log(keySearch.length);
                    if (keySearch.length >= 4) {
                        GET_INITIAL_NGHILC();
                        loaddataNation($('#drPagingNation').val(),keySearch);
                        
                    }else if(keySearch.length < 4){
                        GET_INITIAL_NGHILC();
                        loaddataNation($('#drPagingNation').val(), "");
                    }
                },
                minLength: 0,
                delay: 222,
                autofocus: true
            });
        }

        if (formSearch == "SUBJECT") {
            $('#' + idControl).autocomplete({
                source: function (request, response) {
                    keySearch = $.ui.autocomplete.escapeRegex(request.term).replace(/[%\\\-]/g, '');
                    //console.log(keySearch.length);
                    if (keySearch.length >= 4) {
                        GET_INITIAL_NGHILC();
                        loaddataSubject($('#drPagingSubject').val(),keySearch);
                        
                    }else if(keySearch.length < 4){
                        GET_INITIAL_NGHILC();
                        loaddataSubject($('#drPagingSubject').val(), "");
                    }
                },
                minLength: 0,
                delay: 222,
                autofocus: true
            });
        }

        if (formSearch == "SCHOOL") {
            $('#' + idControl).autocomplete({
                source: function (request, response) {
                    keySearch = $.ui.autocomplete.escapeRegex(request.term).replace(/[%\\\-]/g, '');
                    //console.log(keySearch.length);
                    if (keySearch.length >= 4) {
                        GET_INITIAL_NGHILC();
                        loaddataSchool($('#drPagingSchool').val(),keySearch);
                        
                    }else if(keySearch.length < 4){
                        GET_INITIAL_NGHILC();
                        loaddataSchool($('#drPagingSchool').val(), "");
                    }
                },
                minLength: 0,
                delay: 222,
                autofocus: true
            });
        }

        if (formSearch == "CLASS") {
            $('#' + idControl).autocomplete({
                source: function (request, response) {
                    keySearch = $.ui.autocomplete.escapeRegex(request.term).replace(/[%\\\-]/g, '');
                    //console.log(keySearch.length);
                    if (keySearch.length >= 4) {
                        GET_INITIAL_NGHILC();
                        loaddataClass($('#drPagingClass').val(),keySearch);
                        
                    }else if(keySearch.length < 4){
                        GET_INITIAL_NGHILC();
                        loaddataClass($('#drPagingClass').val(), "");
                    }
                },
                minLength: 0,
                delay: 222,
                autofocus: true
            });
        }

        if (formSearch == "DSHN") {
            $('#' + idControl).autocomplete({
                source: function (request, response) {
                    keySearch = $.ui.autocomplete.escapeRegex(request.term).replace(/[%\\\-]/g, '');
                    //console.log(keySearch.length);
                    if (keySearch.length >= 4) {
                        GET_INITIAL_NGHILC();
                        loaddataDSHN($('#drPagingDSHN').val(), keySearch);
                    }else if(keySearch.length < 4){
                        GET_INITIAL_NGHILC();
                        loaddataDSHN($('#drPagingDSHN').val(), "");
                    }
                },
                minLength: 0,
                delay: 222,
                autofocus: true
            });
        }
    };

//Export Excel----------------------------------------------------------------------------------------------------
    
    function exportExcel(formName) {
        alert(formName);
        window.open('/danh-muc/exportExcel/' + formName,'_blank');
        $.ajax({
            type: "get",
            url:'/danh-muc/exportExcel/' + formName,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                console.log(data);
            }, error: function(data) {
            }
        });
    };

    function exportExcelNation(formName) {
        window.open('/danh-muc/dantoc/exportExcelNation','_blank');
        $.ajax({
            type: "get",
            url:'/danh-muc/dantoc/exportExcelNation',
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                console.log(data);
            }, error: function(data) {
            }
        });
    };

    function exportExcelUnit(formName) {
        window.open('/danh-muc/khoi/exportExcelUnit','_blank');
        $.ajax({
            type: "get",
            url:'/danh-muc/khoi/exportExcelUnit',
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                console.log(data);
            }, error: function(data) {
            }
        });
    };

    function exportExcelGroup(formName) {
        window.open('/danh-muc/nhomdoituong/exportExcelGroup','_blank');
        $.ajax({
            type: "get",
            url:'/danh-muc/nhomdoituong/exportExcelGroup',
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                console.log(data);
            }, error: function(data) {
            }
        });
    };

    function exportExcelSubject(formName) {
        window.open('/danh-muc/doituong/exportExcelSubject','_blank');
        $.ajax({
            type: "get",
            url:'/danh-muc/doituong/exportExcelSubject',
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                console.log(data);
            }, error: function(data) {
            }
        });
    };

    function exportExcelSchool(formName) {
        window.open('/danh-muc/truong/exportExcelSchool','_blank');
        $.ajax({
            type: "get",
            url:'/danh-muc/truong/exportExcelSchool',
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                console.log(data);
            }, error: function(data) {
            }
        });
    };

    function exportExcelClass(formName) {
        window.open('/danh-muc/lop/exportExcelClass','_blank');
        $.ajax({
            type: "get",
            url:'/danh-muc/lop/exportExcelClass',
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            success: function(data) {
                console.log(data);
            }, error: function(data) {
            }
        });
    };