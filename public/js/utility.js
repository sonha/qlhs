(function (utility, $, undefined) {
 utility.text = {
     yes:'Đồng ý',
     no: 'Không đồng ý',
     cancel: 'Hủy bỏ',
     Ok: 'OK',
     deleteConfirmTitle: 'Bạn có muốn xóa bản ghi này?',
     deleteConfirmContent: 'Xóa',
     deleteSuccess: 'Xóa thành công'
	};
    
    utility.confirm = function (title, msg, callback) {
        //alert(2);
        var modelTemplate =
      '<div class="utility-modal modal fade" id="#id#" tabindex="-1" aria-hidden="true">' +
        '<div class="modal-dialog">' +
            '<div class="modal-content">' +
                '<div class="modal-header">' +
                    '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>' +
                    '<h4 class="modal-title">#title#</h4>' +
                '</div>' +
                '<div class="modal-body">' +
                    '#msg#' +
                '</div>' +
                '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-primary btn-ok">' + utility.text.yes + '</button>' +
                    '<button type="button" class="btn btn-default btn-cancel">' + utility.text.no + '</button>' +
                '</div>' +
            '</div>' +
        '</div>' +
    '</div>';
        var modalId = "confirmModal" + parseInt(Date.now());
        modelTemplate = modelTemplate.replace('#id#', modalId);
        modelTemplate = modelTemplate.replace('#title#', title);
        modelTemplate = modelTemplate.replace('#msg#', msg);
        $('body').append(modelTemplate);
        var modal = $('#' + modalId);
        modal.modal('show');
        $('.btn-ok', modal).on('click', function (event) {
            modal.modal('hide');
            callback();
        });
        $('.btn-cancel', modal).on('click', function (event) {
            modal.modal('hide');

        });
        // delete modal after closed
        $(modal).on('hidden.bs.modal', function () {
            modal.remove();
        });
    };

    utility.confirmAlert = function (title, msg, callback) {
               toastr.options = {
  "closeButton": true,
  "debug": true,
  "newestOnTop": false,
  "progressBar": true,
  "positionClass": "toast-top-right",
  "preventDuplicates": true,
  "showDuration": "300",
  "hideDuration": "1000",
  "timeOut": 0,
  "extendedTimeOut": 0,
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut",
  "tapToDismiss": false
}
        var modelTemplate =
      //'<div class="utility-modal modal fade" id="#id#" tabindex="-1" aria-hidden="true">' +
        '<div class="modal-dialog">' +
            '<div class="modal-content">' +
                '<div class="modal-header">' +
                    '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>' +
                    '<h4 class="modal-title">#title#</h4>' +
                '</div>' +
                '<div class="modal-body">' +
                    '#msg#' +
                '</div>' +
                '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-primary btn-ok">' + utility.text.yes + '</button>' +
                    '<button type="button" class="btn btn-default btn-cancel">' + utility.text.no + '</button>' +
                '</div>' +
            '</div>' +
        '</div>' +
    '';
        var modalId = "confirmModal" + parseInt(Date.now());
        modelTemplate = modelTemplate.replace('#id#', modalId);
        modelTemplate = modelTemplate.replace('#title#', title);
        modelTemplate = modelTemplate.replace('#msg#', msg);
        toastr.success(modelTemplate);
        
    };

    utility.choose = function (title, msg, yesLabel, noLabel, yesCallback, noCallback) {
        var modelTemplate =
      '<div class="utility-modal modal fade" id="#id#" tabindex="-1" aria-hidden="true">' +
        '<div class="modal-dialog">' +
            '<div class="modal-content">' +
                '<div class="modal-header">' +
                    '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>' +
                    '<h4 class="modal-title">#title#</h4>' +
                '</div>' +
                '<div class="modal-body">' +
                    '#msg#' +
                '</div>' +
                '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-primary btn-ok">#yesLabel#</button>' +
                    '<button type="button" class="btn btn-warning btn-no">#noLabel#</button>' +
                    '<button type="button" class="btn btn-default btn-cancel">' + utility.text.cancel + '</button>' +
                '</div>' +
            '</div>' +
        '</div>' +
    '</div>';
        var modalId = "confirmModal" + parseInt(Date.now());
        modelTemplate = modelTemplate.replace('#id#', modalId);
        modelTemplate = modelTemplate.replace('#title#', title);
        modelTemplate = modelTemplate.replace('#msg#', msg);
        modelTemplate = modelTemplate.replace('#yesLabel#', yesLabel);
        modelTemplate = modelTemplate.replace('#noLabel#', noLabel);

        $('body').append(modelTemplate);
        var modal = $('#' + modalId);
        modal.modal('show');

        $('.btn-ok', modal).on('click', function (event) {
            modal.modal('hide');
            yesCallback();
        });

        $('.btn-no', modal).on('click', function (event) {
            modal.modal('hide');
            noCallback();
        });

        $('.btn-cancel', modal).on('click', function (event) {
            modal.modal('hide');
        });

        // delete modal after closed
        $(modal).on('hidden.bs.modal', function () {
            modal.remove();
        });
    };

    utility.alert = function (title, msg, callback) {

        var modelTemplate =
      '<div class="modal fade" id="#id#" tabindex="-1" aria-hidden="true">' +
        '<div class="modal-dialog">' +
            '<div class="modal-content">' +
                '<div class="modal-header">' +
                    '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>' +
                    '<h4 class="modal-title">#title#</h4>' +
                '</div>' +
                '<div class="modal-body">' +
                    '#msg#' +
                '</div>' +
                '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-primary btn-ok">' + utility.text.Ok + '</button>' +
                '</div>' +
            '</div>' +
        '</div>' +
    '</div>';
        var modalId = "alertModal" + parseInt(Date.now());
        modelTemplate = modelTemplate.replace('#id#', modalId);
        modelTemplate = modelTemplate.replace('#title#', title);
        modelTemplate = modelTemplate.replace('#msg#', msg);
        
        $('body').append(modelTemplate);
       var modal = $('#' + modalId);
        modal.modal('show');
        $('.btn-ok', modal).on('click', function (event) {
            modal.modal('hide');
            callback();
        });
    };
    utility.message1 = function(title, msg, callback,time){
        var modelTemplate =
      '<div class="modal fade" id="#id#" tabindex="1" aria-hidden="true">' +
        '<div class="modal-dialog">' +
            '<div class="modal-content">' +
                '<div class="modal-header">' +
                    '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>' +
                    '<h4 class="modal-title">#title#</h4>' +
                '</div>' +
                '<div class="modal-body">' +
                    '#msg#' +
                '</div>' +
            '</div>' +
        '</div>' +
    '</div>';
        var modalId = "alertModal" + parseInt(Date.now());
        modelTemplate = modelTemplate.replace('#id#', modalId);
        modelTemplate = modelTemplate.replace('#title#', title);
        modelTemplate = modelTemplate.replace('#msg#', msg);
        $('body').append(modelTemplate);
        var modal = $('#' + modalId);
        modal.modal('show');
        setTimeout(function () {
            modal.modal('hide');
            if(callback!=null){
                callback();
            }
        }, time);
    };
    utility.messagehide = function(id, mess, type, time){
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
    };
    utility.scrollTo = function (el) {
        $('html, body').animate({
            scrollTop: el.offset().top
        }, 'slow');
    };
    utility.message = function (title, msg, callback,time='5000',type=0) {
       toastr.options = {
                "closeButton": true,
                "debug": false,
                "progressBar": true,
                "positionClass": "toast-top-center",
                "onclick": null,
                "showDuration": "3000",
                "hideDuration": "3000",
                "timeOut": time,
                "extendedTimeOut": "3000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
        if(type === 0){
            toastr.success(msg);
        }else{
            toastr.warning(msg);
        }
    };

} (window.utility = window.utility || {}, jQuery));
