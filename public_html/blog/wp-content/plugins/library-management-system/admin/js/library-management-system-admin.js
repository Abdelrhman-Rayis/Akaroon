jQuery(function() {

    // Show Loading Icon - Only for owt_lib_handler AJAX Calls
    if (owt7_library.active) {
        jQuery(document).ajaxSend(function(event, xhr, settings) {
            // Check if the AJAX request belongs to this plugin
            if (settings.data && settings.data.indexOf('action=owt_lib_handler') !== -1) {
                jQuery('.owt7-lms').addClass('owt7_loader');
            }
        });

        jQuery(document).ajaxComplete(function(event, xhr, settings) {
            // Remove loader only for this plugin's AJAX requests
            if (settings.data && settings.data.indexOf('action=owt_lib_handler') !== -1) {
                jQuery('.owt7-lms').removeClass('owt7_loader');
            }
        });
    }

    /**
     * Form Validation & Submit Handler
     */
    jQuery(`
		#owt7_lms_branch_form, 
		#owt7_lms_user_form,
        #owt7_lms_category_form,
        #owt7_lms_bookcase_form,
        #owt7_lms_section_form,
        #owt7_lms_category_form,
        #owt7_lms_book_form,
        #owt7_lms_borrow_book,
        #owt7_lms_return_book,
        #owt7_lms_data_settings
	`).validate({
        submitHandler: function(form) {
            var formID = jQuery(form).attr('id');
            var paramID = formID;
            formID = "#" + formID;
            jQuery(formID).find("button[type='submit']").text('Processing...').css("cursor", "progress");
            var formdata = jQuery(formID).serialize();
            var postdata = formdata + "&action=owt_lib_handler&param=" + paramID;
            jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    jQuery(formID).find("button[type='submit']").text(owt7_library.messages.message_1 + '...').css("cursor", "progress");
                    owt7_lms_toastr(data.msg, "success");
                } else {
                    jQuery(formID).find("button[type='submit']").html('<i class="mdi mdi-check-outline"></i> ' + owt7_library.messages.message_2).css("cursor", "pointer");
                    owt7_lms_toastr(data.msg, 'error');
                }
            });
        }
    });

    /**
     * Delete Function Handler
     */
    jQuery(document).on("click", ".action-btn-delete", function() {
        if (confirm(owt7_library.messages.message_12)) { // True
            var dataId = jQuery(this).data("id");
            var dataModule = jQuery(this).data("module");
            var postdata = "id=" + dataId + "&module=" + dataModule + "&action=owt_lib_handler&param=owt7_lms_delete_function&owt7_lms_nonce=" + owt7_library.ajax_nonce;
            jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    owt7_lms_toastr(data.msg, "success");
                } else {
                    owt7_lms_toastr(data.msg, 'error');
                }
            });
        }
    });

    /**
     * DataTable
     */
    jQuery(`
        #tbl_branches_list, 
        #tbl_users_list,
        #tbl_bookcases_list,
        #tbl_sections_list,
        #tbl_branches_list,
        #tbl_books_list,
        #tbl_books_borrow_history,
        #tbl_books_return_history
    `).DataTable({
        dom: 'Bfrtip',
        buttons: [
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ]
    });

    /**
     * Upload Profile Image
     */
    jQuery("#owt7_upload_image").on("click", function() {
        var image = wp.media({
            title: owt7_library.messages.message_5,
            multiple: false
        }).open().on("select", function() {
            var uploaded_image = image.state().get('selection').first();
            var image_url = uploaded_image.toJSON().url;
            var ext = image_url.split('.').pop().toLowerCase();
            if (jQuery.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                // Error
            } else {
                jQuery("#owt7_library_image_preview").removeClass("hide-input");
                jQuery("#owt7_library_image_preview").attr('src', image_url);
                jQuery("#owt7_image_url").val(image_url);
            }
        });
    });

    /**
     * Add Book: Filter "Sections" by "Bookcase"
     */
    jQuery(document).on("change", "#owt7_dd_bookcase_id", function() {
        var bookcaseId = jQuery(this).val();
        var postdata = "bkcase_id=" + bookcaseId + "&action=owt_lib_handler&param=owt7_lms_filter_section&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = jQuery.parseJSON(response);
            var sectionHtml = '<option> -- ' + owt7_library.messages.message_6 + ' --</option>';
            if (data.sts == 1) {
                jQuery.each(data.arr.sections, function(index, item) {
                    sectionHtml += `
                        <option value="` + item.id + `">` + item.name + `</option>
                    `;
                });
            }
            jQuery("#owt7_dd_section_id").html(sectionHtml);
        });
    });

    /**
     * Add Book: Filter "Users" by "Branch"
     */
    jQuery(document).on("change", "#owt7_dd_branch_id", function() {
        var branch_id = jQuery(this).val();
        var postdata = "branch_id=" + branch_id + "&action=owt_lib_handler&param=owt7_lms_filter_user&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = jQuery.parseJSON(response);
            var userHtml = '<option> -- ' + owt7_library.messages.message_7 + ' --</option>';
            if (data.sts == 1) {
                jQuery.each(data.arr.users, function(index, item) {
                    userHtml += `
                        <option value="` + item.id + `">` + item.name + `</option>
                    `;
                });
            }
            jQuery("#owt7_dd_u_id").html(userHtml);
            if (jQuery("#owt7_dd_borrow_u_id").length > 0) {
                jQuery("#owt7_dd_borrow_u_id").html(userHtml);
            }
        });
    });

    /**
     * Add Book: Filter "Books" by "Category"
     */
    jQuery(document).on("change", "#owt7_dd_category_id", function() {
        var category_id = jQuery(this).val();
        var postdata = "category_id=" + category_id + "&action=owt_lib_handler&param=owt7_lms_filter_book&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = jQuery.parseJSON(response);
            var bookHtml = '<option> -- ' + owt7_library.messages.message_8 + ' -- </option>';
            if (data.sts == 1) {
                jQuery.each(data.arr.books, function(index, item) {
                    bookHtml += `
                            <option value="` + item.id + `">` + item.name + `</option>
                        `;
                });
            }
            jQuery("#owt7_dd_book_id").html(bookHtml);
        });
    });

    /**
     * List of Borrowed Books By User (Return Book Page)
     */
    jQuery(document).on("change", "#owt7_dd_borrow_u_id", function() {
        var u_id = jQuery(this).val();
        var postdata = "u_id=" + u_id + "&action=owt_lib_handler&param=owt7_lms_filter_borrow_book&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = jQuery.parseJSON(response);
            var bookHtml = "";
            if (data.sts == 1) {
                jQuery.each(data.arr.books, function(index, item) {
                    bookHtml += `
                        <label>
                        <input type="checkbox" name="owt7_borrow_books_id[]" value="` + item.id + `">` + owt7_lms_toTitleCase(item.book_name) + `</label>
                        `;
                });
                jQuery(".form-books-borrow").parent(".form-row").removeClass("hide-input");
                jQuery("#owt7_chk_books_list").html(bookHtml);
            } else {
                jQuery(".form-books-borrow").parent(".form-row").removeClass("hide-input");
                jQuery("#owt7_chk_books_list").html("<span style='font-size: 15px;'><i>No Book(s) Borrowed.</i></span>");
            }
        });
    });

    /**
     * Book Return on Click
     */
    jQuery(document).on("click", ".owt7_lms_btn_return", function() {
        if (confirm(owt7_library.messages.message_13)) {
            var dataId = jQuery(this).data("id");
            var formdata = jQuery("#owt7_lms_return_book_" + dataId).serialize();
            var postdata = formdata + "&action=owt_lib_handler&param=owt7_lms_return_book";
            jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    owt7_lms_toastr(data.msg, "success");
                } else {
                    owt7_lms_toastr(data.msg, 'error');
                }
            });
        }
    });

    /**
     * Transactions (Borrow, Return): DataTable Filter Dropdowns
     */
    jQuery(document).on("change", ".owt7_lms_dd_data_filter", function() {

        var dataTableBodyId = jQuery(this).data("table");
        var dataTableId = jQuery("#" + dataTableBodyId).parent("table").attr("id");
        var dataOption = jQuery(this).data("option");
        var listType = jQuery(this).data("list");
        var optionId = jQuery(this).val();
        jQuery("#" + dataTableId).DataTable().destroy();
        var postdata = "filterby=" + dataOption + "&id=" + optionId + "&list=" + listType + "&action=owt_lib_handler&param=owt7_lms_data_filters&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                jQuery("#" + dataTableBodyId).html(data.arr.template);
            } else {
                jQuery("#" + dataTableBodyId).html("");
            }
            jQuery("#" + dataTableId).DataTable();
        });
    });

    /**
     * Modal Settings
     */
    var owt7_lms_modal = jQuery('#owt7_lms_mdl_settings');

    /** 
     * Late Fine Modal
     */
    jQuery(`
        #owt7_lms_fine_modal,
        #owt7_lms_country_modal
    `).on('click', function() {
        owt7_lms_modal.show();
    });

    /** 
     * Modal Close Button Action
     */
    jQuery('.close').on('click', function() {
        owt7_lms_modal.hide();
    });

    /**
     *  Filters: 
     *  - "Books by Category", 
     *  - "Sections by Bookcase", 
     *  - "Users by Branch"
     */
    jQuery(document).on("change", "#owt7_lms_data_filter", function() {
        var module = jQuery(this).data("module");
        var filterBy = jQuery(this).data("filter-by");
        var tableId = "#tbl_" + module + "_list";
        var filterValue = jQuery(this).val();
        var tableBody = jQuery("table#tbl_" + module + "_list tbody");
        jQuery(tableId).DataTable().destroy();
        var postdata = "module=" + module + "&filterBy=" + filterBy + "&value=" + filterValue + "&action=owt_lib_handler&param=owt7_lms_data_option_filters&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                tableBody.html(data.arr.template);
            } else {
                tableBody.html("");
            }
            jQuery(tableId).DataTable();
        });
    });

    /**
     * Run Test Data Importer
     */
    jQuery(document).on("click", "#owt7_lms_run_data_importer, #owt7_lms_refresh_test_data", function() {
        if (confirm(owt7_library.messages.message_9)) {
            var postdata = "action=owt_lib_handler&param=owt7_lms_import_test_data&owt7_lms_nonce=" + owt7_library.ajax_nonce;
            jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    owt7_lms_toastr(data.msg, "success");
                } else {
                    owt7_lms_toastr(data.msg, 'error');
                }
            });
        }
    });

    /**
     * Remove Test Data
     */
    jQuery(document).on("click", "#owt7_lms_remove_test_data", function() {
        if (confirm(owt7_library.messages.message_10)) {
            var postdata = "action=owt_lib_handler&param=owt7_lms_remove_test_data&owt7_lms_nonce=" + owt7_library.ajax_nonce;
            jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    owt7_lms_toastr(data.msg, "success");
                } else {
                    owt7_lms_toastr(data.msg, 'error');
                }
            });
        }
    });

    /**
     * Pay Late Fine
     */
    jQuery(document).on("click", ".owt7_pay_late_fine", function() {
        if (confirm(owt7_library.messages.message_11)) {
            var return_id = jQuery(this).data("id");
            var postdata = "return_id=" + return_id + "&action=owt_lib_handler&param=owt7_pay_late_fine&owt7_lms_nonce=" + owt7_library.ajax_nonce;
            jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    owt7_lms_toastr(data.msg, "success");
                } else {
                    owt7_lms_toastr(data.msg, 'error');
                }
            });
        }
    });

    /**
     * Auto Generate ID
     */
    jQuery(document).on("click", "#owt7_btn_ids_auto_generate", function() {
        var systemId = owt7_lms_generateRandomString(6);
        var module = jQuery(this).data("module");
        if (module == "book") {
            jQuery("#owt7_txt_book_id").val(systemId);
        } else if (module == "user") {
            jQuery("#owt7_txt_u_id").val(systemId);
        }
    });

    /**
     * Copy to Clipboard
     */
    jQuery(document).on("click", "#owt7_lib_shortcode_copy", function() {
        var valueToCopy = jQuery(this).data("value");
        navigator.clipboard.writeText(valueToCopy);
        owt7_lms_toastr("Successfully, Shortcode Copied", "success");
    });
});

/**
 * Activity Notification
 */
function owt7_lms_toastr(message, type) {
    if (type == "success") {
        toastr.success(message, owt7_library.messages.message_3);
        setTimeout(function() {
            location.reload();
        }, 3000);
    } else if (type == "error") {
        toastr.error(message, owt7_library.messages.message_4)
    }
}

/**
 *jQuery function to convert the input text to Title Case
 */
function owt7_lms_toTitleCase(str) {
    return str.replace(/\b\w+/g, function(txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}

/**
 * Auto Generate IDs
 */
function owt7_lms_generateRandomString(length) {
    var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    var result = '';
    for (var i = 0; i < length; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return result;
}