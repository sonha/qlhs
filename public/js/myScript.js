function focusSelect2(){
    var select2_open;
// open select2 dropdown on focus
    $(document).on('focus', '.select2-selection--single', function(e) {
        select2_open = $(this).parent().parent().siblings('select');
        select2_open.select2('open');
    });

    // fix for ie11
    if (/rv:11.0/i.test(navigator.userAgent)) {
        $(document).on('blur', '.select2-search__field', function (e) {
            select2_open.select2('close');
        });
    }
}
function timeAgo (dateString) {
        var rightNow = new Date();
        var then = new Date(dateString);
     //   var  then = Date.parse(dateString.replace(/( \+)/, ' UTC$1'));

        var diff = rightNow - then;

        var second = 1000,
        minute = second * 60,
        hour = minute * 60,
        day = hour * 24,
        week = day * 7;

        if (isNaN(diff) || diff < 0) {
            return ""; // return blank string if unknown
        }

        if (diff < second * 2) {
            // within 2 seconds
            return "bây giờ";
        }

        if (diff < minute) {
            return Math.floor(diff / second) + " giây trước";
        }

        if (diff < minute * 2) {
            return "1 phút trước";
        }

        if (diff < hour) {
            return Math.floor(diff / minute) + " phút trước";
        }

        if (diff < hour * 2) {
            return "1 tiếng trước";
        }

        if (diff < day) {             return  Math.floor(diff / hour) + " tiếng trước";         }           if (diff > day && diff < day * 2) {
            return "hôm qua";
        }

        if (diff < day * 365) {
            return Math.floor(diff / day) + " ngày trước";
        }

        else {
            return "hơn 1 năm";
        }
    }
function resultMessage_show(id, mess, type, time) {
    var texthmlt;
    if (type == 0)
        texthmlt = '<div class="row text-center">' +
                '<div id="infoconfigsuccess" class="alert-info margin"><strong> ' +
                mess +
                '</strong></div>' +
                '</div>';
    else
        texthmlt = '<div class="row text-center">' +
                '<div id="infoconfigfail" class="alert-danger margin"><strong>' +
                mess +
                '</strong></div>' +
                '</div>';

    $('#' + id).html(texthmlt);
    setTimeout(function () {
        $('#' + id).html('');
    }, time);
}
function setSinglePicker(idClass, toDate, leftOrRight) {
    if (!leftOrRight)
        leftOrRight = 'left';

    if (toDate === null)
        toDate = moment().format("YYYY-MM-DD");
    var status = true;
    multiLanguageForDatePickerCollate(idClass, toDate, toDate, 'YYYY-MM-DD', leftOrRight, status, null);

    $('#' + idClass).val(toDate);

    $('.calendar.single.left').parent().find('.right').remove();

};

function setSinglePickerFormat(idClass, toDate, leftOrRight, formats) {
    if (!leftOrRight)
        leftOrRight = 'left';

    if (toDate === null)
        toDate = moment().format(formats);
    var status = true;
    multiLanguageForDatePickerCollate(idClass, toDate, toDate, formats, leftOrRight, status, null);

    $('#' + idClass).val(toDate);

    $('.calendar.single.left').parent().find('.right').remove();

};
function setSinglePickerThang(idClass, toDate, leftOrRight) {
    if (!leftOrRight)
        leftOrRight = 'left';

    if (toDate === null)
        toDate = moment().format("YYYY-MM");
    var status = true;
    multiLanguageForDatePickerCollate(idClass, toDate, toDate, 'YYYY-MM', leftOrRight, status, null);

    $('#' + idClass).val(toDate);

    $('.calendar.single.left').parent().find('.right').remove();

};
function formatNumbers( thousands, decimal, precision, prefix ) {
            return {
                display: function ( d ) {
                    if ( typeof d !== 'number' && typeof d !== 'string' ) {
                        return d;
                    }
    
                    var negative = d < 0 ? '-' : '';
                    d = Math.abs( parseFloat( d ) );
    
                    var intPart = parseInt( d, 10 );
                    var floatPart = precision ?
                        decimal+(d - intPart).toFixed( precision ).substring( 2 ):
                        '';
    
                    return negative + (prefix||'') +
                        intPart.toString().replace(
                            /\B(?=(\d{3})+(?!\d))/g, thousands
                        ) +
                        floatPart;
                }
            };
        }
function formatDate(jsonDateString) {
    if (jsonDateString === null || jsonDateString === "-") {
        return "-";
    }
    //var currentTime = new Date(jsonDateString);
    return jsonDateString;
};
function formatDates(data){
    if (data === null || data === "-") {
                        return "-";
    }
    var currentTime = new Date(data);
    return moment(data).format('DD-MM-YYYY'); 
}
function formatMonth(data){
    if (data === null || data === "-") {
                        return "-";
    }
    var currentTime = new Date(data);
    return moment(data).format('MM-YYYY'); 
}
function formatDateTimes(data){
    if (data === null || data === "-") {
                        return "-";
    }
    var currentTime = new Date(data);
    return moment(data).format('DD-MM-YYYY HH:MM:SS'); 
}
function formatDateTime(jsonDateString) {
    if (jsonDateString === null || jsonDateString === "-") {
        return "-";
    }
    var currentTime = new Date(jsonDateString);
    return currentTime.format("yyyy-mm-dd");
};
function ConvertString(jsonDateString) {
    if (jsonDateString === null || jsonDateString === "-") {
        return "-";
    }

    return jsonDateString;
};
function ConvertTimestamp(timeStamp_value){

    if (timeStamp_value === null || timeStamp_value === "null") {
        return "-";
    }
    var theDate = new Date(timeStamp_value * 1000);
    return moment(timeStamp_value * 1000).format('DD-MM-YYYY'); //theDate.format("yyyy-mm-dd");
}
function ConvertDate(jsonDateString) {
    if (jsonDateString === null || jsonDateString === "-") {
        return "-";
    }

    return new Date(jsonDateString);
};
function setDateTimepicker(idClass, toDate, isNoTime, callback) {
    var format1;
    var format2;
    if (!isNoTime) {
        format1 = 'yyyy-mm-dd hh:ii';
        format2 = 'YYYY-MM-DD HH:mm';
    } else {
        format1 = 'yyyy-mm-dd';
        format2 = 'YYYY-MM-DD';
    }
    $('#' + idClass).datetimepicker({
        format: format1,
        isRTL: false,
        autoclose: true,
        language: 'vi'
    });
    $('#' + idClass).val(moment().format(format2));
}
function formatFloatNumber_nghilc(number, numberFloat) {

    if (number === undefined || number === null || number === "") {
        return "-";
    } if (parseFloat(number) === NaN || parseFloat(number) === "NaN") { return "-"; } else {
        var tmp = (parseFloat(number)).toLocaleString('en-US', { minimumFractionDigits: numberFloat });
        return replaceAll(tmp, ",", "");
    }
    // return tmp.replace(","," ");
};
function formatDate2(jsonDateString) {
    if (jsonDateString === null || jsonDateString === "-") {
        return "-";
    }
    var currentTime = new Date(parseInt(jsonDateString.replace('/Date(', '')));
    return currentTime.format("yyyy-mm-dd HH:MM:ss");
};
function formatFloatNumber_NGHILC(datax, numberFloat) {
    if (datax === undefined)
        return "-";
    else
        return parseFloat(datax.numberFloat).toFixed(3);

};

function formatString(datax, key) {
    if (datax === undefined || datax.hasOwnProperty[key] === null)
        return "-";
    if (datax.hasOwnProperty(key)) {
        return datax[key];
    }
    return "-";
};

function formatFloatNumber(datax, numberFloat) {
    //alert(datax.hasOwnProperty(numberFloat));
    if (datax === undefined || datax[numberFloat] === null || datax[numberFloat] === "null") {

        return "-";
    } else {

        if (datax.hasOwnProperty(numberFloat)) {
            var number = (parseFloat(datax[numberFloat]) + '');
            if (parseFloat(number) === NaN || parseFloat(number) === "NaN") { return "-"; } else {
                return parseFloat(number);
            }
        }
    }
    return "-";
};


function formatNumber_2(number, numberFloat, pattern) {
    if (number === undefined || number === null) {
        return "0";
    }
    //var tmp = (number + '').replace(",", "");
    var tmp = replaceAll(number + '', ",", "");

    return tmp;
};

function replaceAll(str, find, replace) {
    return str.replace(new RegExp(find, 'g'), replace);
}
function multiLanguageForDatePickerCollate(idClass, fromDate, toDate, format, leftOrRight, satus, callback) {
    var lang = "vi";
    switch (lang) {
        case "vi":
            $('#' + idClass).daterangepicker({
                format: format,
                opens: leftOrRight,
                singleDatePicker: satus,
                showDropdowns: true,
                separator: ' - ',
                startDate: fromDate,
                endDate: toDate,
                changeMonth: false,
                checkDateRangePickerActive: true,
                maxDate: moment().endOf('year'),
                locale: {
                    applyLabel: 'Đồng ý',
                    cancelLabel: 'Đóng',
                    fromLabel: 'Từ ngày',
                    toLabel: 'Đến ngày',
                    weekLabel: 'W',
                    firstDay: 0,
                    currentText: 'Hôm nay',
                    monthNames: ['Tháng' + ' 1', 'Tháng' + ' 2', 'Tháng' + ' 3', 'Tháng' + ' 4', 'Tháng' + ' 5', 'Tháng' + ' 6',
                        'Tháng' + ' 7', 'Tháng' + ' 8', 'Tháng' + ' 9', 'Tháng' + ' 10', 'Tháng' + ' 11', 'Tháng' + ' 12'],
                    daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7']
                },
                //ranges: {
                //    'Hôm nay': [moment().startOf('hour'), moment()],
                //    'Tuần này': [moment().startOf('isoweek'), moment()],
                //    'Tuần trước': [moment().subtract('week', 1).startOf('isoweek'), moment().subtract('week', 1).endOf('isoweek')],
                //    'Tháng này': [moment().startOf('month'), moment()],
                //    'Tháng trước': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                //}
            },
            function (start, end) {
                if (callback !== null) {
                    //console.log("multiLanguageForDatePicker: " + start + '-' + end)
                    callback(start.format('DD/MM/YYYY'), end.format('DD/MM/YYYY'));
                }
            });
            break;
        default:
            $('#' + idClass).daterangepicker({
                format: format,
                opens: leftOrRight,
                singleDatePicker: satus,
                showDropdowns: true,
                separator: ' - ',
                startDate: fromDate,
                endDate: toDate,
                maxDate: moment().endOf('month'),
                checkDateRangePickerActive: true,
                ranges: {
                    'Current date': [moment().startOf('hour'), moment()],
                    'Current Week': [moment().startOf('isoweek'), moment()],
                    'Before Week': [moment().subtract('week', 1).startOf('isoweek'), moment().subtract('week', 1).endOf('isoweek')],
                    'Current Month': [moment().startOf('month'), moment()],
                    'Before Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                }
            });
            break;
            ///

    }


};


var startRecord = 0;
var totalRows = 0;
var numShowRows = 5;
var idCountRowPaging = null;
var idCbbPaging = null;
var callback = null;
var isRun = true;
var isRun1 = true;
var isRun2 = true;
var paging = 0;

function GET_INITIAL_NGHILC() {
    startRecord = 0;
    totalRows = 0;
    numShowRows = 2;
    idCountRowPaging = null;
    idCbbPaging = null;
    callback = null;
    isRun = true;
    isRun1 = true;
    isRun2 = true;
    paging = 0;
    $("label.g_countRowsPaging").html(0);
    $("select.g_selectPaging").html('');
    $("ul.g_clickedPaging").html(' <li><a>&laquo;</a></li><li><a>0</a></li><li><a>&raquo;</a></li>');
};

function GET_START_RECORD_NGHILC() {
    return parseInt(startRecord);
};

function SETUP_PAGING_NGHILC(data, callback) {
    //    var startRecord = startRecord;
    //    var totalRows = totalRows;
    //    var numRows = numRows;

    var StartRecord = parseInt(data.startRecord);
    var TotalRows = parseInt(data.totalRows);
    var numRows = parseInt(data.numRows);
    //alert(StartRecord);
    if (StartRecord !== 0 || isRun === null) {
        $("select.g_selectPaging").val(StartRecord);
        return;
    }
    isRun = null;
    paging = 0;
    startRecord = StartRecord;
    totalRows = TotalRows;
    numShowRows = numRows;

    //PHAN TRANG
    var page_selector = "";

    var n = ~~(TotalRows / numRows);
    var du = TotalRows % numRows;
    if (du > 0)
        n = n + 1;
    paging = n;

    for (var i = 0; i < n; i++) {
        page_selector += "<option value=" + (i) + ">" + (i + 1) + "/" + n + "</option>";
    }

    $("label.g_countRowsPaging").html(TotalRows);
    $("select.g_selectPaging").html(page_selector);
    $("select.g_selectPaging").val(StartRecord);
    //PHAN TRANG

    //HUNG
    page_selector = "";
    page_selector += "<li id='paging_left' style='cursor:pointer'><a>&laquo;</a></li>";

    var maxShowLi = n;
    if (maxShowLi > 5) {
        maxShowLi = 5;
    }
    for (var i = 0; i < maxShowLi; i++) {
        var x = '';
        if (i === 0)
            x = 'paging_item_first';
        if (i === maxShowLi - 1)
            x = 'paging_item_end';
        page_selector += "<li class='paging_item " + x + "' style='cursor:pointer' value='" + i + "'><a>" + (parseInt(i) + 1) + "</a></li>";
    }
    page_selector += "<li id='paging_right' style='cursor:pointer'><a>&raquo;</a></li>";

    $("ul.g_clickedPaging").html(page_selector);
    $("li.paging_item").removeClass('active');
    $("li.paging_item_first").addClass('active');

    //HUNG ---------------------------------------------------------------------   
    $("select.g_selectPaging").unbind('change');
    $("select.g_selectPaging").change(function () {
        startRecord = $(this).val();
        setLeftPagingHunglm(parseInt($(this).val()));
        callback();
    });
    
    $("ul.g_clickedPaging").unbind('click');
    $("ul.g_clickedPaging").on('click', 'li.paging_item', (function () {

        var rowCurrent = $(this).attr('value');
        startRecord = rowCurrent;
        $("select.g_selectPaging").val(rowCurrent);

        $("li.paging_item").removeClass('active');
        $(this).addClass('active');
        callback();
    }));

    setLeftRightPaging();
};

function SETUP_PAGING_NGHILC1(data, callback) {
    //    var startRecord = startRecord;
    //    var totalRows = totalRows;
    //    var numRows = numRows;

    var StartRecord = parseInt(data.startRecord);
    var TotalRows = parseInt(data.totalRows);
    var numRows = parseInt(data.numRows);
    //alert(StartRecord);
    if (StartRecord !== 0 || isRun1 === null) {
        $("select.g_selectPaging").val(StartRecord);
        return;
    }
    isRun1 = null;
    paging = 0;
    startRecord = StartRecord;
    totalRows = TotalRows;
    numShowRows = numRows;

    //PHAN TRANG
    var page_selector = "";

    var n = ~~(TotalRows / numRows);
    var du = TotalRows % numRows;
    if (du > 0)
        n = n + 1;
    paging = n;

    for (var i = 0; i < n; i++) {
        page_selector += "<option value=" + (i) + ">" + (i + 1) + "/" + n + "</option>";
    }

    $("label.g_countRowsPaging").html(TotalRows);
    $("select.g_selectPaging").html(page_selector);
    $("select.g_selectPaging").val(StartRecord);
    //PHAN TRANG

    //HUNG
    page_selector = "";
    page_selector += "<li id='paging_left' style='cursor:pointer'><a>&laquo;</a></li>";

    var maxShowLi = n;
    if (maxShowLi > 5) {
        maxShowLi = 5;
    }
    for (var i = 0; i < maxShowLi; i++) {
        var x = '';
        if (i === 0)
            x = 'paging_item_first';
        if (i === maxShowLi - 1)
            x = 'paging_item_end';
        page_selector += "<li class='paging_item " + x + "' style='cursor:pointer' value='" + i + "'><a>" + (parseInt(i) + 1) + "</a></li>";
    }
    page_selector += "<li id='paging_right' style='cursor:pointer'><a>&raquo;</a></li>";

    $("ul.g_clickedPaging").html(page_selector);
    $("li.paging_item").removeClass('active');
    $("li.paging_item_first").addClass('active');

    //HUNG ---------------------------------------------------------------------   
    $("select.g_selectPaging").unbind('change');
    $("select.g_selectPaging").change(function () {
        startRecord = $(this).val();
        setLeftPagingHunglm(parseInt($(this).val()));
        callback();
    });
    
    $("ul.g_clickedPaging").unbind('click');
    $("ul.g_clickedPaging").on('click', 'li.paging_item', (function () {

        var rowCurrent = $(this).attr('value');
        startRecord = rowCurrent;
        $("select.g_selectPaging").val(rowCurrent);

        $("li.paging_item").removeClass('active');
        $(this).addClass('active');
        callback();
    }));

    setLeftRightPaging();
};

function SETUP_PAGING_NGHILC2(data, callback) {
    //    var startRecord = startRecord;
    //    var totalRows = totalRows;
    //    var numRows = numRows;

    var StartRecord = parseInt(data.startRecord);
    var TotalRows = parseInt(data.totalRows);
    var numRows = parseInt(data.numRows);
    //alert(StartRecord);
    if (StartRecord !== 0 || isRun2 === null) {
        $("select.g_selectPaging").val(StartRecord);
        return;
    }
    isRun2 = null;
    paging = 0;
    startRecord = StartRecord;
    totalRows = TotalRows;
    numShowRows = numRows;

    //PHAN TRANG
    var page_selector = "";

    var n = ~~(TotalRows / numRows);
    var du = TotalRows % numRows;
    if (du > 0)
        n = n + 1;
    paging = n;

    for (var i = 0; i < n; i++) {
        page_selector += "<option value=" + (i) + ">" + (i + 1) + "/" + n + "</option>";
    }

    $("label.g_countRowsPaging").html(TotalRows);
    $("select.g_selectPaging").html(page_selector);
    $("select.g_selectPaging").val(StartRecord);
    //PHAN TRANG

    //HUNG
    page_selector = "";
    page_selector += "<li id='paging_left' style='cursor:pointer'><a>&laquo;</a></li>";

    var maxShowLi = n;
    if (maxShowLi > 5) {
        maxShowLi = 5;
    }
    for (var i = 0; i < maxShowLi; i++) {
        var x = '';
        if (i === 0)
            x = 'paging_item_first';
        if (i === maxShowLi - 1)
            x = 'paging_item_end';
        page_selector += "<li class='paging_item " + x + "' style='cursor:pointer' value='" + i + "'><a>" + (parseInt(i) + 1) + "</a></li>";
    }
    page_selector += "<li id='paging_right' style='cursor:pointer'><a>&raquo;</a></li>";

    $("ul.g_clickedPaging").html(page_selector);
    $("li.paging_item").removeClass('active');
    $("li.paging_item_first").addClass('active');

    //HUNG ---------------------------------------------------------------------   
    $("select.g_selectPaging").unbind('change');
    $("select.g_selectPaging").change(function () {
        startRecord = $(this).val();
        setLeftPagingHunglm(parseInt($(this).val()));
        callback();
    });
    
    $("ul.g_clickedPaging").unbind('click');
    $("ul.g_clickedPaging").on('click', 'li.paging_item', (function () {

        var rowCurrent = $(this).attr('value');
        startRecord = rowCurrent;
        $("select.g_selectPaging").val(rowCurrent);

        $("li.paging_item").removeClass('active');
        $(this).addClass('active');
        callback();
    }));

    setLeftRightPaging();
};

function setLeftPagingHunglm(startIndx) {

    var page_selector = "";
    page_selector += "<li id='paging_left' style='cursor:pointer'><a>&laquo;</a></li>";
    var first = $("li.paging_item_first").val();
    var end = $("li.paging_item_end").val();
    if (first === undefined || end === undefined || (parseInt(first) === 0 && startIndx === undefined))
        return;

    var startShowLi = parseInt(first) - 1;
    if (startIndx !== undefined) {
        startShowLi = startIndx - 4;
        if (startShowLi < 0)
            startShowLi = 0;
    }
    var n = parseInt(paging);
    var maxShowLi = startShowLi + 4;
    if (maxShowLi >= n - 1)
        maxShowLi = n - 1;
    for (var i = startShowLi; i <= maxShowLi; i++) {
        var x = '';
        if (i === startShowLi)
            x = 'paging_item_first';
        if (i === maxShowLi - 1)
            x = 'paging_item_end';
        page_selector += "<li class='paging_item " + x + "' style='cursor:pointer' value='" + i + "'><a>" + (parseInt(i) + 1) + "</a></li>";
    }
    page_selector += "<li id='paging_right' style='cursor:pointer'><a>&raquo;</a></li>";
    $("ul.g_clickedPaging").html(page_selector);
    $("li.paging_item").each(function () {
        if (parseInt($(this).attr('value')) === parseInt(startRecord)) {
            $(this).addClass('active');
        }
    });

    setLeftRightPaging();
};
function setLeftRightPaging() {
    $("li#paging_left").unbind('click');
    $('li#paging_left').click(function () {
        setLeftPagingHunglm();
    });
    $("li#paging_right").unbind('click');
    $('li#paging_right').click(function () {
        setRightPagingHunglm();
    });
};

function setRightPagingHunglm() {
    var n = parseInt(paging);
    var page_selector = "";
    page_selector += "<li id='paging_left' style='cursor:pointer'><a>&laquo;</a></li>";
    var first = $("li.paging_item_first").val();
    var end = $("li.paging_item_end").val();
    if (first === undefined || end === undefined || (parseInt(first) + 4) >= n - 1)
        return;
    var startShowLi = parseInt(first) + 1;
    var maxShowLi = startShowLi + 4;
    if (maxShowLi > n - 1)
        maxShowLi = n - 1;

    for (var i = startShowLi; i <= maxShowLi; i++) {
        var x = '';
        if (i === startShowLi)
            x = 'paging_item_first';
        if (i === maxShowLi - 1)
            x = 'paging_item_end';
        page_selector += "<li class='paging_item " + x + "' style='cursor:pointer' value='" + i + "'><a>" + (parseInt(i) + 1) + "</a></li>";
    }
    page_selector += "<li id='paging_right' style='cursor:pointer'><a>&raquo;</a></li>";
    $("ul.g_clickedPaging").html(page_selector);
    $("li.paging_item").each(function () {

        if (parseInt($(this).attr('value')) === parseInt(startRecord)) {
            $(this).addClass('active');
        }
    });

    setLeftRightPaging();
};

PostToServer = function (url, dataJson, callbackSuc, callbackError, buttonLoading, nameFuction, pageName) {
    $('div.pace').removeClass().addClass('pace  pace-active');
    if (!dataJson || dataJson === null)
        dataJson = {};
    $('#' + buttonLoading).button('loading');
    $.ajax({
        type: "POST",
        url: url,
        data: JSON.stringify(dataJson),
        dataType: 'json',
        contentType: 'application/json; charset=utf-8',
        headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
        },
        success: function (val) {
            //           / alert("Val: "+val);
           
            $('#' + buttonLoading).button('reset');
            $('div.pace').removeClass().addClass('pace  pace-inactive');
            callbackSuc(val);
            // return;
        },
        error: function (val) {
            $('#' + buttonLoading).button('reset');
            $('div.pace').removeClass().addClass('pace  pace-inactive');
            // callbackError(val);
        }
    });
};
PostToServerFormData = function (url, dataJson, callbackSuc, callbackError, buttonLoading, nameFuction, pageName) {
    $('div.pace').removeClass().addClass('pace  pace-active');
    if (!dataJson || dataJson === null)
        dataJson = {};
    $('#' + buttonLoading).button('loading');
    $.ajax({
        type: "POST",
        url: url,
                data: dataJson,
                contentType: false,
                cache: false,    
                processData: false,
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
        success: function (val) {
            //           / alert("Val: "+val);
           
            $('#' + buttonLoading).button('reset');
            $('div.pace').removeClass().addClass('pace  pace-inactive');
            callbackSuc(val);
            // return;
        },
        error: function (val) {
            $('#' + buttonLoading).button('reset');
            $('div.pace').removeClass().addClass('pace  pace-inactive');
            callbackError(val);
        }
    });
};

GetFromServer = function (url, callbackSuc, callbackError, buttonLoading, nameFuction, pageName) {
    $('#' + buttonLoading).button('loading');

    $.ajax({
        type: "GET",
        url: url,
        dataType: 'json',
        contentType: 'application/json; charset=utf-8',
        success: function (val) {
            $('#' + buttonLoading).button('reset');
            callbackSuc(val);
            
        },
        error: function (val) {
            $('#' + buttonLoading).button('reset');
            callbackError(val);
            
        }
    });
};
info_View = function (recData) {
    var TuTValue = "";
    var TiTValue = "";
    var TuMValue = "";
    var TiMValue = "";
    if (recData.TuTValue === null) { TuTValue = recData.Tu; } else { TuTValue = recData.TuTValue; }
    if (recData.TiTValue === null) { TiTValue = recData.Ti; } else { TiTValue = recData.TiTValue; }
    if (recData.TuMValue === null) { TuMValue = 1; } else { TuMValue = recData.TuMValue; }
    if (recData.TiMValue === null) { TiMValue = 1; } else { TiMValue = recData.TiMValue; }
    var tumStr = TuMValue == 1 ? "" : " / " + TuMValue;
    var timStr = TiMValue == 1 ? "" : " / " + TiMValue;
    var info = "";
    info += "<p> " + window.Ami.amisys.Serial + ": <span>" + recData.serial + "</span> ; " + window.Ami.amisys.MeterType + ": <span> " + ConvertString(recData.MeterName) + "</span> - Tu: <span>" + TuTValue + tumStr + "</span> ; Ti: <span>" + TiTValue + timStr + "</span>  </p>";
    info += "<p> " + window.Ami.amisys.MeasurementPoint + ": <span>" + recData.MeasurementPointId + "</span> - <span>" + ConvertString(recData.CusName) + "</span> ;  " + window.Ami.amisys.MultiplierOut + " = <span>1</span></p>";
    info += "<p> " + window.Ami.amisys.CustomerCode + ": <span>" + recData.CusCode + "</span> - <span> " + ConvertString(recData.Name) + "</span> ;" + window.Ami.amisys.Address + ": <span>" + ConvertString(recData.Address) + "</span></p>";
    info += "<p> " + window.Ami.amisys.IMEI + ": <span>" + recData.DcuId + "</span></p>";
    return info;
};
var nodeTree;
var idjsTree;
loadTree = function (idTree) {

    $('#' + idTree).jstree({

        "core": {
            "animation": 300,
            "check_callback": false,
            "data": {
                "url": function (node) {
                    return '/Treeview/GetRoot'
                    //   return '@Url.Action("GetRoot", "Treeview")';
                },
                "data": function (node) {
                    nodeTree = node;
                    idjsTree = idTree;
                    loadTreeChildren(idTree, node);
                    return { "id": node.id };
                }
            },
            "strings": {
                "Loading ...": window.Ami.amisys.Loading
            }
        },
        "types": {
            "#": {
                "max_children": 1,
                "max_depth": 4,

            },
            "root": {
                // "icon": "/Content/images/icons/folder.png",
                "valid_children": ["default"]
            },
            "good": {
                "icon": "/Content/images/icons/circle_green.png",
                // "valid_children": ["default"]
            },
            "notgood": {
                "icon": "/Content/images/icons/circle_blue.png",
                // "valid_children": ["default"]
            },
            "bad": {
                "icon": "/Content/images/icons/circle_yellow.png",
                // "valid_children": ["default"]
            },
            "notbad": {
                "icon": "/Content/images/icons/circle_red.png",
                // "valid_children": ["default"]
            },
            "noMeter": {
                "icon": "/Content/images/icons/circle_xanh.png",
                // "valid_children": ["default"]
            },
            "init": {
                "icon": "/Content/images/icons/circle_grey.png",
                // "valid_children": ["default"]
            },
            "default": {
                "valid_children": ["default", "file"]
            },
            "file": {
                "icon": "glyphicon glyphicon-file",
                "valid_children": []
            }
        },
        "plugins": [
          "themes", "types", "json_data",
          "ui", "cookies", "crrm"
        ]
    }).on('hover_node.jstree', function (e, data) {
        //console.log(data.node.text);
        if (data.node.parent !== '#') {
            var $node = $("#" + data.node.id);
            var url = $node.find('a').attr('href');
            if (data.node.id.split('-')[0] !== '4') {
                var arr = data.node.text.split('[');
                var arr1 = arr[1].split("/");//"'" + arr[0] + "' có " + arr1[0] + " điểm đo đang online trên tổng " + arr2 + " điểm đo."
                var arr2 = arr1[1].split("]")[0];//arr1[0] + ' and ' + arr2 tooltipTree
                $("#" + data.node.parent).prop({ 'title': String.format(window.Ami.amisys.tooltipTree, arr1[0], arr2), 'content': '<img src="' + url + '" alt=""/>' });
            } else {
                $("#" + data.node.id).prop({ 'title': window.Ami.amisys.MeasurementPoint + " " + data.node.text.split('-')[0], 'content': '<img src="' + url + '" alt=""/>' });
            }
        }
    });
};
String.format = function () {
    var s = arguments[0];


    for (var i = 0; i < arguments.length - 1; i++) {
        var reg = new RegExp("\\{" + i + "\\}", "gm");
        s = s.replace(reg, arguments[i + 1]);
    }
    return s;
}
loadTreeChildren = function (idTree, node) {
    //alert(idTree + "-" + node);
    $.ajax({
        type: "GET",
        dataType: "json",
        contentType: "application/json; charset=utf-8",
        url: '/Treeview/GetChildrens?id=' + node.id,
        success: function (data) {
            if (data === null || data === undefined || data.length <= 0) {
                document.location.replace('/Account/Login');
            } else {
                console.log('load node tree text: ' + node.id);
                //   deptid = (node.id).split("-")[1];
                // alert("_+++++" + (node.id).split("-")[1])
                if (data[0].text !== '0') {
                    var newNum = data[0].text.split("/");
                    var parent = $('#' + idTree).jstree(true).get_node(node.id);
                    var text = $('#' + idTree).jstree(true).get_text(parent);

                    var arr = text.split("[");

                    var arr1 = arr[1].split("/");
                    var num = parseInt(newNum) - parseInt(arr1[0]);

                    if (num !== 0) {
                        var flag = true;
                        while (flag) {
                            text = $('#' + idTree).jstree(true).get_text(parent);
                            if (text !== false) {
                                arr = text.split("[");
                                arr1 = arr[1].split("/");
                                var sum = parseInt(arr1[0]) + num;
                                $('#' + idTree).jstree(true).set_text(parent, arr[0] + "[" + sum + "/" + arr1[1]);
                                var tempParent = $('#' + idTree).jstree(true).get_parent(parent);
                                parent = tempParent;
                            }
                            else
                                flag = false;
                        }
                    }
                }
            }
        },
        complete: function (data) {

        }
    });
};
refresh = function (idTree, node) {//deptOrgId
    if (node === '') {
        node = $('#deptOrgId').val();
    }
    $('#button-refresh').button('loading');
    //$("#object-children-tree").jstree("refresh", $("#" + node));

    $.ajax({
        type: "GET",
        dataType: "json",
        contentType: "application/json; charset=utf-8",
        url: '/Treeview/GetChildrens?id=' + node,
        success: function (data) {
            if (data === null || data === undefined || data.length <= 0) {
                document.location.replace('/Account/Login');
            } else {
                console.log('load node tree text refresh: ' + node);
                //   deptid = (node.id).split("-")[1];
                // alert("_+++++" + (node.id).split("-")[1])

                if (data[0].text !== '0') {
                    $('#' + idTree).jstree(true).refresh_node($('#' + idTree).jstree(true).get_node(node).id);
                    var newNum = data[0].text.split("/");
                    var parent = $('#' + idTree).jstree(true).get_node(node);
                    var text = $('#' + idTree).jstree(true).get_text(parent);

                    var arr = text.split("[");

                    var arr1 = arr[1].split("/");
                    var num = parseInt(newNum) - parseInt(arr1[0]);

                    if (num !== 0) {
                        var flag = true;
                        while (flag) {
                            text = $('#' + idTree).jstree(true).get_text(parent);
                            if (text !== false) {
                                arr = text.split("[");
                                arr1 = arr[1].split("/");
                                var sum = parseInt(arr1[0]) + num;
                                $('#' + idTree).jstree(true).set_text(parent, arr[0] + "[" + sum + "/" + arr1[1]);
                                var tempParent = $('#' + idTree).jstree(true).get_parent(parent);
                                parent = tempParent;
                            }
                            else
                                flag = false;
                        }
                    }
                    $('#' + idTree).jstree('close_node', $('#' + node));
                } else {
                    var nodes = $('#' + idTree).jstree(true).get_node(node);
                    $('#' + idTree).jstree(true).refresh_node(nodes);
                    $('#' + idTree).jstree('close_node', $('#' + nodes.parents[0]));
                }
            }
            $('#button-refresh').button('reset');
        },
        complete: function (data) {
            $('#button-refresh').button('reset');
        }
    });


};

//var Resources = {
//    Entry1: 'Entry 1 text', Entry2: 'Entry 2 text',
//    Entry3: 'Entry 3 text'
//};

searchOperation = function () {
    var stringplace = window.Resources.resources.SearchSuggestion;
    $("#searchText").select2({
        placeholder: stringplace,
        minimumInputLength: 3,
        language: {
            inputTooShort: function () {
                return window.Resources.resources.inputTooShort;
            }
        },
        ajax: {
            url: '/api/search-meter',
            dataType: 'json',
            type: "POST",
            quietMillis: 1000,
            data: function (params) {
                return {
                    searchField: $('#searchField').val(),
                    searchText: params.term
                };
            },
            processResults: function (data) {
                if (data) {
                    return {
                        results: $.map(data, function (item) {

                            return {
                                text: item.MeterCode + '-' + item.CustomerName,
                                id: item.MeasurementPointMeterId,
                                data: item
                            };
                        })
                    };
                } else {
                    var url = "/Account/Login";
                    document.location.replace(url);
                }
            }
        },
        templateResult: format,
        //templateSelection: format,
    });

    function format(state) {
        if (!state.id) { return state.text; }
        var $state = $("<div><span>" + window.Ami.amisys.IMEI + ":</span> <span>" + state.data.DcuCode + "</span> <br/> <span>" + window.Ami.amisys.Serial + ":</span> <span>" + state.data.MeterCode + "</span> <br/>"
                          + "<span>" + window.Ami.amisys.CustomerCode + ":</span> <span>" + state.data.CustomerCode + "</span><br/><span>" + state.data.CustomerName + "</span>"
                    + "<div>");
        return $state;
    }
}
function searchPointLinkToTree(oldPointId, newPointId, GroupId, orgId, treeId, deptid) {
    var callback = undefined;

    if (parseInt($('#deptTypeId').val()) === 1 || $('#deptTypeId').val() === '1') {
        $('#' + treeId).jstree('open_node', $('#1-' + deptid), function (e, data) {
            $("li.jstree-node[id^='2-']").each(function () {
                var id = $(this).attr('id');
                //   alert('-' + id);
                $('#' + treeId).jstree('close_node', $('#' + id));
            });
            setTimeout(function () {
                // alert(parseInt($('#deptCount').val()));
                //alert(oldPointId + '/' + newPointId + '/' + GroupId + '/' + orgId + '/' + treeId + '/' + deptid + $('#deptTypeId').val())
                if (parseInt($('#deptCount').val()) === 2 || $('#deptCount').val() === '2') {

                    $('#' + treeId).jstree('open_node', $('#2-' + GroupId), function (e, data) {
                        // $('#' + treeId).jstree('open_node', $('#3-' + GroupId), function (e, data) {
                        var newNode;
                        var newNodeId;
                        //var firstChildId = $("#" + '3-' + GroupId).find(" li:first-child").attr("id");
                        var firstChildId = $("#" + '2-' + GroupId).find(" li:first-child").attr("id");
                        var firstChild = $('#' + treeId).jstree(true).get_node(firstChildId);
                        $("li.jstree-node[id*='" + newPointId + "']").each(function () {
                            newNodeId = $(this).attr('id');
                            // alert(newNodeId);
                            newNode = $('#' + treeId).jstree(true).get_node(newNodeId);
                        });
                        var tempId = newNodeId;

                        var newNodeText = $('#' + treeId).jstree(true).get_text(newNode);
                        var firstChildText = $('#' + treeId).jstree(true).get_text(firstChild);


                        var newNodeStyle = $("li[id='" + newNodeId + "'] > a > i").attr('style');
                        var firstChildStyle = $("li[id='" + firstChildId + "'] > a > i").attr('style');

                        $("li[id='" + newNodeId + "'] > a > i").attr('style', firstChildStyle);
                        $("li[id='" + firstChildId + "'] > a > i").attr('style', newNodeStyle);

                        $('#' + treeId).jstree(true).set_id(newNode, "firstChildId");
                        $('#' + treeId).jstree(true).set_text(newNode, firstChildText);

                        $('#' + treeId).jstree(true).set_id(firstChild, tempId);
                        $('#' + treeId).jstree(true).set_text(firstChild, newNodeText);

                        $('#' + treeId).jstree(true).set_id(newNode, firstChildId);

                        $('li[id*="' + oldPointId + '"] > a').removeClass().addClass('jstree-anchor');
                        $('li[id*="' + newPointId + '"] > a').removeClass().addClass('jstree-anchor  jstree-clicked');

                        if (callback !== undefined) {
                            callback();
                        }

                        //   }, true);
                    }, true);
                } else if (parseInt($('#deptCount').val()) === 3 || $('#deptCount').val() === '3') {
                    $('#' + treeId).jstree('open_node', $('#2-' + orgId), function (e, data) {
                        $('#' + treeId).jstree('open_node', $('#3-' + GroupId), function (e, data) {
                            var newNode;
                            var newNodeId;
                            //var firstChildId = $("#" + '3-' + GroupId).find(" li:first-child").attr("id");
                            var firstChildId = $("#" + '3-' + GroupId).find(" li:first-child").attr("id");
                            var firstChild = $('#' + treeId).jstree(true).get_node(firstChildId);
                            $("li.jstree-node[id*='" + newPointId + "']").each(function () {
                                newNodeId = $(this).attr('id');
                                // alert(newNodeId);
                                newNode = $('#' + treeId).jstree(true).get_node(newNodeId);
                            });
                            var tempId = newNodeId;

                            var newNodeText = $('#' + treeId).jstree(true).get_text(newNode);
                            var firstChildText = $('#' + treeId).jstree(true).get_text(firstChild);


                            var newNodeStyle = $("li[id='" + newNodeId + "'] > a > i").attr('style');
                            var firstChildStyle = $("li[id='" + firstChildId + "'] > a > i").attr('style');

                            $("li[id='" + newNodeId + "'] > a > i").attr('style', firstChildStyle);
                            $("li[id='" + firstChildId + "'] > a > i").attr('style', newNodeStyle);

                            $('#' + treeId).jstree(true).set_id(newNode, "firstChildId");
                            $('#' + treeId).jstree(true).set_text(newNode, firstChildText);

                            $('#' + treeId).jstree(true).set_id(firstChild, tempId);
                            $('#' + treeId).jstree(true).set_text(firstChild, newNodeText);

                            $('#' + treeId).jstree(true).set_id(newNode, firstChildId);

                            $('li[id*="' + oldPointId + '"] > a').removeClass().addClass('jstree-anchor');
                            $('li[id*="' + newPointId + '"] > a').removeClass().addClass('jstree-anchor  jstree-clicked');

                            if (callback !== undefined) {
                                callback();
                            }

                        }, true);
                    }, true);
                }
            }, 500);
        }, true);
    } else {
        setTimeout(function () {
            $('#' + treeId).jstree('open_node', $('#2-' + GroupId), function (e, data) {
                var newNode;
                var newNodeId;
                var firstChildId = $("#" + '2-' + GroupId).find(" li:first-child").attr("id");
                var firstChild = $('#' + treeId).jstree(true).get_node(firstChildId);
                $("li.jstree-node[id*='" + newPointId + "']").each(function () {
                    newNodeId = $(this).attr('id');
                    // alert(newNodeId);
                    newNode = $('#' + treeId).jstree(true).get_node(newNodeId);
                });
                var tempId = newNodeId;

                var newNodeText = $('#' + treeId).jstree(true).get_text(newNode);
                var firstChildText = $('#' + treeId).jstree(true).get_text(firstChild);


                var newNodeStyle = $("li[id='" + newNodeId + "'] > a > i").attr('style');
                var firstChildStyle = $("li[id='" + firstChildId + "'] > a > i").attr('style');

                $("li[id='" + newNodeId + "'] > a > i").attr('style', firstChildStyle);
                $("li[id='" + firstChildId + "'] > a > i").attr('style', newNodeStyle);

                $('#' + treeId).jstree(true).set_id(newNode, "firstChildId");
                $('#' + treeId).jstree(true).set_text(newNode, firstChildText);

                $('#' + treeId).jstree(true).set_id(firstChild, tempId);
                $('#' + treeId).jstree(true).set_text(firstChild, newNodeText);

                $('#' + treeId).jstree(true).set_id(newNode, firstChildId);

                $('li[id*="' + oldPointId + '"] > a').removeClass().addClass('jstree-anchor');
                $('li[id*="' + newPointId + '"] > a').removeClass().addClass('jstree-anchor  jstree-clicked');

                if (callback !== undefined) {
                    callback();
                }
            }, true);
        }, 500);
    }
};

compareDate = function (idDateFrom, idDateTo, idValid) {
    var startDate = new Date($('#' + idDateFrom).val()).getTime();
    var endDate = new Date($('#' + idDateTo).val()).getTime();
    if (startDate > endDate) {
        $('#' + idValid).html('Ngày bắt đầu phải nhỏ hơn ngày kết thúc!');
        setTimeout(function () {
            $('#' + idDateTo).removeClass('form-control-warning');
            $('#' + idValid).html('');
        }, 5000);
        $('#' + idDateTo).addClass('form-control-warning');
        // $("#validateOperation").delay(3200).fadeOut(500);           
        $('#' + idDateTo).focus();
        return false
    }
    return true;
};
function changeCallBack(id){
        $("#"+id).change(function(e, callback) {
                    if (typeof callback === "function")
                            callback();
        });

};
function callbackTrigger(id){
        $("#"+id).trigger();

}
function loading(){
    
  $('body').append("<div class='modal'></div>");
  $('body').addClass("loading");
  //  $("body").addClass("loading");
}
function formatter(number){
 var formatter = new Intl.NumberFormat('de-DE');
 return  formatter.format(number);
}
function closeLoading(){
   
  $('body').removeClass("loading");
    // $("body").removeClass("loading"); 
}
Notify = function(text, callback, close_callback, style) {

    var time = '10000';
    var $container = $('#notifications');
    var icon = '<i class="fa fa-shopping-cart "></i>';

    if (typeof style == 'undefined' ) style = 'warning'

    var html = $('<div class="alert alert-' + style + '  hide">' + icon +  " " + text + '</div>');

    $('<a>',{
        text: '×',
        class: 'button close',
        style: 'padding-left: 10px;',
        href: '#',
        click: function(e){
            e.preventDefault()
            close_callback && close_callback()
            remove_notice()
        }
    }).prependTo(html)

    $container.prepend(html)
    html.removeClass('hide').hide().fadeIn('slow')

    function remove_notice() {
        html.stop().fadeOut('slow').remove()
    }

    var timer =  setInterval(remove_notice, time);

    $(html).hover(function(){
        clearInterval(timer);
    }, function(){
        timer = setInterval(remove_notice, time);
    });

    html.on('click', function () {
        clearInterval(timer)
        callback && callback()
        remove_notice()
    });


}