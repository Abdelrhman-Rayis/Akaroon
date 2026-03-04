jQuery(function () {

    if (jQuery("#owt-lms-tabs").length > 0) {
        jQuery("#owt-lms-tabs").tabs();
    }

    jQuery("#frm-add-student").validate({
        submitHandler: function () {
            var formdata = jQuery("#frm-add-student").serialize();
            var postdata = formdata + "&action=owt_lib_frontend_handler&param=owt_lib_create_student";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    swal("Success", data.msg, "success");
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    swal("Field Error", data.msg, "error");
                }
            });
        }
    });

    jQuery("#dd_category").on("change", function () {
        var formdata = jQuery("#dd_category").serialize();
        var postdata = formdata + "&action=owt_lib_frontend_handler&param=owt_lib_filter_book";
        jQuery("body").addClass("wpowt-pl-processing");
        jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
            jQuery("body").removeClass("wpowt-pl-processing");
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                jQuery("#lib-load-book").html(data.arr.template);
            } else {
                jQuery("#lib-load-book").html(data.msg);
            }
        });
    });
});