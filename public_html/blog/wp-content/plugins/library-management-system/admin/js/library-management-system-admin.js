jQuery(function () {

    var owt_lib_prefix = owt_lib.owt_lib_prefix;

    if (jQuery('#owt-tbl-book-list').length > 0) {
        jQuery('#owt-tbl-book-list').DataTable();
    }

    if (jQuery('#owt-tbl-return-book-list').length > 0) {
        jQuery('#owt-tbl-return-book-list').DataTable();
    }

    if (jQuery('#owt-tbl-book-issue-list').length > 0) {
        jQuery('#owt-tbl-book-issue-list').DataTable();
    }

    // upload profile image from here
    jQuery("#btnUploadImage").on("click", function () {

        var image = wp.media({
            title: "Upload Profile Image",
            multiple: false
        }).open().on("select", function () {
            var uploaded_image = image.state().get('selection').first();
            var image_url = uploaded_image.toJSON().url;
            var ext = image_url.split('.').pop().toLowerCase();
            if (jQuery.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {

            } else {
                jQuery(".wpowt-lib-img-prev").attr('src', image_url);
                jQuery("#stu_profile_image").val(image_url);
            }
        });
    });

    // create student from here...
    jQuery("#wpowt-lib-frm-create-new-student").validate({
        submitHandler: function () {
            jQuery("#wpowt-lib-frm-create-new-student").find("button[type='submit']").text('Processing...').css("cursor", "progress");
            var formdata = jQuery("#wpowt-lib-frm-create-new-student").serialize();
            var postdata = formdata + "&action=owt_lib_handler&param=owt_lib_create_student";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    jQuery("#wpowt-lib-frm-create-new-student").find("button[type='submit']").text('Submitted, please wait...').css("cursor", "progress");
                    wpowt_lib_toastr(data.msg, "success");
                    setTimeout(function () {
                        location.reload();
                    }, 1200);
                } else {
                    jQuery("#wpowt-lib-frm-create-new-student").find("button[type='submit']").html('<i class="mdi mdi-check-outline"></i> Submit').css("cursor", "pointer");
                    wpowt_lib_toastr(data.msg, 'error')
                }
            });
        }
    });
    // delete student from here... 
    jQuery(document).on("click", ".wpowt-lib-del-student", function () {

        var conf = confirm("Are you sure want to delete, It will delete all data of book Issues as well ?");
        if (conf) {
            var student_id = jQuery(this).attr("data-id");
            var postdata = "st=" + student_id + "&action=owt_lib_handler&param=owt_lib_delete_student";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    wpowt_lib_toastr(data.msg, "success");
                    setTimeout(function () {
                        location.reload();
                    }, 1200);
                } else {
                    wpowt_lib_toastr(data.msg, "error");
                }
            });
        }
    });
    // add branch from here...
    jQuery("#wpowt-frm-add-frm").validate({
        submitHandler: function () {
            jQuery("#wpowt-frm-add-frm").find("button[type='submit']").text('Processing...').css("cursor", "progress");
            var postdata = jQuery("#wpowt-frm-add-frm").serialize() + "&action=owt_lib_handler&param=owt_lib_add_branch";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    jQuery("#wpowt-frm-add-frm").find("button[type='submit']").text('Submitted, please wait...').css("cursor", "progress");
                    wpowt_lib_toastr(data.msg, "success");
                    setTimeout(function () {
                        location.reload();
                    }, 1200);
                } else {
                    jQuery("#wpowt-frm-add-frm").find("button[type='submit']").html('<i class="mdi mdi-check-outline"></i> Submit').css("cursor", "pointer");
                    wpowt_lib_toastr(data.msg, "error");
                }
            });
        }
    });

    //create staff from here...
    jQuery("#wpowt-lib-frm-create-new-staff").validate({
        submitHandler: function () {
            jQuery("#wpowt-lib-frm-create-new-staff").find("button[type='submit']").text('Processing...').css("cursor", "progress");
            var formdata = jQuery("#wpowt-lib-frm-create-new-staff").serialize();
            var postdata = formdata + "&action=owt_lib_handler&param=owt_lib_create_staff";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    jQuery("#wpowt-lib-frm-create-new-staff").find("button[type='submit']").text('Submitted, please wait...').css("cursor", "progress");
                    wpowt_lib_toastr(data.msg, "success");
                    setTimeout(function () {
                        location.reload();
                    }, 1200);
                } else {
                    jQuery("#wpowt-lib-frm-create-new-staff").find("button[type='submit']").html('<i class="mdi mdi-check-outline"></i> Submit').css("cursor", "pointer");
                    wpowt_lib_toastr(data.msg, 'error')
                }
            });
        }
    });

    jQuery(document).on("click", ".btn-book-return", function () {

        var conf = confirm("Are you sure want to return ?");
        if (conf) {
            var issue_id = jQuery(this).attr("data-id");
            var formdata = "issue_id=" + issue_id;
            var postdata = formdata + "&action=owt_lib_handler&param=return_book_by_issue_id";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    wpowt_lib_toastr(data.msg, "success");
                    setTimeout(function () {
                        location.reload();
                    }, 1200);
                } else {
                    wpowt_lib_toastr(data.msg, 'error');
                }
            });
        }
    });

    // delete staff from here... 
    jQuery(document).on("click", ".wpowt-lib-del-staff", function () {

        var conf = confirm("Are you sure want to delete, it will delete all book issues of this faculty?");
        if (conf) {
            var staff_id = jQuery(this).attr("data-id");
            var postdata = "st=" + staff_id + "&action=owt_lib_handler&param=owt_lib_delete_staff";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    wpowt_lib_toastr(data.msg, "success");
                    setTimeout(function () {
                        location.reload();
                    }, 1200);
                } else {
                    wpowt_lib_toastr(data.msg, "error");
                }
            });
        }
    });

    // add staff type from here...
    jQuery("#wpowt-frm-add-type").validate({
        submitHandler: function () {
            jQuery("#wpowt-frm-add-type").find("button[type='submit']").text('Processing...').css("cursor", "progress");
            var postdata = jQuery("#wpowt-frm-add-type").serialize() + "&action=owt_lib_handler&param=owt_lib_add_staff_type";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    jQuery("#wpowt-frm-add-type").find("button[type='submit']").text('Submitted, please wait...').css("cursor", "progress");
                    wpowt_lib_toastr(data.msg, "success");
                    setTimeout(function () {
                        location.reload();
                    }, 1200);
                } else {
                    jQuery("#wpowt-frm-add-type").find("button[type='submit']").html('<i class="mdi mdi-check-outline"></i> Submit').css("cursor", "pointer");
                    wpowt_lib_toastr(data.msg, "error");
                }
            });
        }
    });

    // create book from here...
    jQuery("#wpowt-lib-frm-create-new-book").validate({
        submitHandler: function () {
            jQuery("#wpowt-lib-frm-create-new-book").find("button[type='submit']").text('Processing...').css("cursor", "progress");
            var formdata = jQuery("#wpowt-lib-frm-create-new-book").serialize();
            var postdata = formdata + "&action=owt_lib_handler&param=owt_lib_create_book";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    jQuery("#wpowt-lib-frm-create-new-book").find("button[type='submit']").text('Submitted, please wait...').css("cursor", "progress");
                    wpowt_lib_toastr(data.msg, "success");
                    setTimeout(function () {
                        location.reload();
                    }, 1200);
                } else {
                    jQuery("#wpowt-lib-frm-create-new-book").find("button[type='submit']").html('<i class="mdi mdi-check-outline"></i> Submit').css("cursor", "pointer");
                    wpowt_lib_toastr(data.msg, 'error')
                }
            });
        }
    });

    // create book category from here...
    jQuery("#wpowt-frm-book-category").validate({
        submitHandler: function () {
            jQuery("#wpowt-frm-book-category").find("button[type='submit']").text('Processing...').css("cursor", "progress");
            var postdata = jQuery("#wpowt-frm-book-category").serialize() + "&action=owt_lib_handler&param=owt_lib_add_book_category";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    jQuery("#wpowt-frm-book-category").find("button[type='submit']").text('Submitted, please wait...').css("cursor", "progress");
                    wpowt_lib_toastr(data.msg, "success");
                    setTimeout(function () {
                        location.reload();
                    }, 1200);
                } else {
                    jQuery("#wpowt-frm-book-category").find("button[type='submit']").html('<i class="mdi mdi-check-outline"></i> Submit').css("cursor", "pointer");
                    wpowt_lib_toastr(data.msg, "error");
                }
            });
        }
    });

    // delete book from here... 
    jQuery(document).on("click", ".wpowt-lib-del-book", function () {

        var conf = confirm("Are you sure want to delete?");
        if (conf) {
            var staff_id = jQuery(this).attr("data-id");
            var postdata = "st=" + staff_id + "&action=owt_lib_handler&param=owt_lib_delete_book";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    wpowt_lib_toastr(data.msg, "success");
                    setTimeout(function () {
                        location.reload();
                    }, 1200);
                } else {
                    wpowt_lib_toastr(data.msg, "error");
                }
            });
        }
    });

    // filter book from here...
    jQuery(document).on("change", "#wpowt-dd-category", function () {

        var cat_id = jQuery(this).val();

        if (cat_id == -1) {
            wpowt_lib_toastr("Invalid category, please select other", "error");
            jQuery("#wpowt-dd-books").html('<option value="-1">Choose book</option>');
        } else {
            jQuery("#wpowt-dd-books").html("<option>Finding books...</option>");
            var postdata = "ct=" + cat_id + "&action=owt_lib_handler&param=owt_lib_filter_books";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                var bookHtml = '<option value="-1">Choose book</option>';
                if (data.sts == 1) {
                    var books = data.arr;
                    if (books.length > 0) {
                        jQuery.each(books, function (index, item) {
                            bookHtml += '<option value="' + item.id + '">' + item.name + '</option>';
                        });
                    }
                    jQuery("#wpowt-dd-books").html(bookHtml);
                } else {
                    jQuery("#wpowt-dd-books").html(bookHtml);
                    wpowt_lib_toastr(data.msg, "error");
                }
            });
        }
    });

    // filter by user type
    jQuery(document).on("change", "#wpowt-dd-types", function () {

        var user_type = jQuery(this).val();

        if (user_type == -1) {
            wpowt_lib_toastr("Invalid user type, please select a user", "error");
            jQuery(".wpowt-lib-student-section").css("display", "none");
            jQuery(".wpowt-lib-staff-section").css("display", "none");
        } else {
            var postdata = "uid=" + user_type + "&action=owt_lib_handler&param=owt_lib_filter_users";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    if (data.arr.show == "student") {
                        jQuery(".wpowt-lib-student-section").css("display", "block");
                        jQuery(".wpowt-lib-staff-section").css("display", "none");
                    } else if (data.arr.show == "staff") {
                        var stafflist = '<option value="-1">Choose staff</option>';
                        var staffs = data.arr.data;
                        if (staffs.length > 0) {
                            jQuery.each(staffs, function (index, item) {
                                stafflist += '<option value="' + item.id + '">' + item.name + ' ( ' + item.staff_id + ' )</option>';
                            });
                        }
                        jQuery("#wpowt-lib-stafflist").html(stafflist);
                        jQuery(".wpowt-lib-student-section").css("display", "none");
                        jQuery(".wpowt-lib-staff-section").css("display", "block");
                    }
                    jQuery("#wpowt-hidden-type").val(data.arr.show);
                } else {
                    wpowt_lib_toastr(data.msg, "error");
                }
            });
        }
    });

    // filter by branch
    jQuery(document).on("change", "#wpowt-student-branch-dd", function () {

        var branch_id = jQuery(this).val();

        if (branch_id == -1) {
            wpowt_lib_toastr("Invalid branch, please select branch", "error");
            jQuery(".wpowt-lib-dd-student-list").css("display", "none");
        } else {
            var postdata = "bid=" + branch_id + "&action=owt_lib_handler&param=owt_lib_filter_branch";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                var studentsHtml = '<option value="-1">Choose student</option>';
                if (data.sts == 1) {
                    var students = data.arr.students;
                    jQuery.each(students, function (index, item) {
                        studentsHtml += '<option value="' + item.id + '">' + item.name + ' ( ' + item.student_id + ' )</option>';
                    });
                    jQuery("#wpowt-students-dd-list").html(studentsHtml);
                    jQuery(".wpowt-lib-dd-student-list").css("display", "block");
                } else {
                    wpowt_lib_toastr(data.msg, "error");
                    jQuery("#wpowt-students-dd-list").html(studentsHtml);
                    jQuery(".wpowt-lib-dd-student-list").css("display", "block");
                }
            });
        }
    });

    // validate selected student
    jQuery(document).on("change", "#wpowt-students-dd-list", function () {
        var student_id = jQuery(this).val();
        if (student_id == -1) {
            wpowt_lib_toastr("Please select student", "error");
        }
    });

    // book issue
    jQuery("#wpowt-frm-book-issue").validate({
        submitHandler: function () {
            jQuery("#wpowt-frm-book-issue").find("button[type='submit']").text('Processing...').css("cursor", "progress");
            var formdata = jQuery("#wpowt-frm-book-issue").serialize();
            var postdata = formdata + "&action=owt_lib_handler&param=owt_lib_issue_book";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    jQuery("#wpowt-frm-book-issue").find("button[type='submit']").text('Submitted, please wait...').css("cursor", "progress");
                    wpowt_lib_toastr(data.msg, "success");
                    setTimeout(function () {
                        location.reload();
                    }, 1200);
                } else {
                    jQuery("#wpowt-frm-book-issue").find("button[type='submit']").html('<i class="mdi mdi-check-outline"></i> Submit').css("cursor", "pointer");
                    wpowt_lib_toastr(data.msg, "error");
                }
            });
        }
    });

    jQuery(document).on("change", "select[name='wpowt_return_dd_student_id']", function () {
        var student_id = jQuery(this).val();
        var postdata = "stid=" + student_id + "&action=owt_lib_handler&param=owt_lib_student_issued_books";
        jQuery("body").addClass("wpowt-pl-processing");
        jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
            jQuery("body").removeClass("wpowt-pl-processing");
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                // book(s) found
                var returnHtml = '';
                var books = data.arr.books;
                if (books.length > 0) {
                    jQuery.each(books, function (index, item) {
                        returnHtml += '<label class="owt-lib-label"><input type="checkbox" name="book_return_list[]" value="' + item.issue_id + '" data-book="' + item.id + '"/> <span>' + item.book_name + '</span></label>';
                    });
                    jQuery(".wpowt-lib-books-issued-area").html(returnHtml);
                } else {
                    jQuery(".wpowt-lib-books-issued-area").html('<i>-- No Books Issued --</i>');
                }

            } else {
                wpowt_lib_toastr(data.msg, "error");
                jQuery(".wpowt-lib-books-issued-area").html('<i>-- No Books Issued --</i>');
            }
        });
    });

    jQuery(document).on("change", "select[name='wpowt_return_dd_staff_id']", function () {
        var staff_id = jQuery(this).val();
        var postdata = "stfid=" + staff_id + "&action=owt_lib_handler&param=owt_lib_staff_issued_books";
        jQuery("body").addClass("wpowt-pl-processing");
        jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
            jQuery("body").removeClass("wpowt-pl-processing");
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                // book(s) found
                var returnHtml = '';
                var books = data.arr.books;
                if (books.length > 0) {
                    jQuery.each(books, function (index, item) {
                        returnHtml += '<label class="owt-lib-label"><input type="checkbox" name="book_return_list[]" value="' + item.issue_id + '" data-book="' + item.id + '"/> <span>' + item.book_name + '</span></label>';
                    });
                    jQuery(".wpowt-lib-books-issued-area").html(returnHtml);
                } else {
                    jQuery(".wpowt-lib-books-issued-area").html('<i>-- No Books Issued --</i>');
                }

            } else {
                wpowt_lib_toastr(data.msg, "error");
                jQuery(".wpowt-lib-books-issued-area").html('<i>-- No Books Issued --</i>');
            }
        });
    });

    jQuery("#wpowt-frm-book-return").validate({
        submitHandler: function () {
            jQuery("#wpowt-frm-book-return").find("button[type='submit']").text('Processing...').css("cursor", "progress");
            var postdata = jQuery("#wpowt-frm-book-return").serialize() + "&action=owt_lib_handler&param=owt_lib_student_return_book";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    jQuery("#wpowt-frm-book-return").find("button[type='submit']").text('Submitted, please wait...').css("cursor", "progress");
                    wpowt_lib_toastr(data.msg, "success");
                    setTimeout(function () {
                        location.reload();
                    }, 1200);
                } else {
                    jQuery("#wpowt-frm-book-return").find("button[type='submit']").html('<i class="mdi mdi-check-outline"></i> Submit').css("cursor", "pointer");
                    wpowt_lib_toastr(data.msg, "error");
                }
            });
        }
    });

    jQuery(document).on("click", ".wpowt-lib-fine-modal", function () {
        var return_id = jQuery(this).attr("data-id");
        var postdata = "return_id=" + return_id + "&action=owt_lib_handler&param=owt_lib_student_fine_details";
        jQuery("body").addClass("wpowt-pl-processing");
        jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
            jQuery("body").removeClass("wpowt-pl-processing");
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                jQuery("#wpowt-txt-extra-days").val(data.arr.extra_days);
                jQuery("#wpowt-txt-total-fine").val(data.arr.fine_amount);
                jQuery("#wpowt-lib-return-id").val(return_id);
                jQuery("#wpowt-fine-details-modal").modal("show");
            } else {
                wpowt_lib_toastr(data.msg, "error");
            }
        });
    });

    jQuery("#wpowt-lib-pay-fine").validate({
        submitHandler: function () {
            jQuery("#wpowt-lib-pay-fine").find("button[type='submit']").text('Processing...').css("cursor", "progress");
            var postdata = jQuery("#wpowt-lib-pay-fine").serialize() + "&action=owt_lib_handler&param=owt_lib_pay_late_fine";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    jQuery("#wpowt-lib-pay-fine").find("button[type='submit']").text('Submitted, please wait...').css("cursor", "progress")
                    wpowt_lib_toastr(data.msg, "success");
                    setTimeout(function () {
                        location.reload();
                    }, 1200);
                } else {
                    jQuery("#wpowt-lib-pay-fine").find("button[type='submit']").html('<i class="mdi mdi-check-outline"></i> Submit').css("cursor", "pointer");
                    wpowt_lib_toastr(data.msg, "error");
                }
            });
        }
    });

    jQuery(document).on("click", ".wpowt-lib-del-return-book", function () {

        var conf = confirm("Are you sure want to delete ?");
        if (conf) {
            var return_id = jQuery(this).attr("data-id");
            var postdata = "return_id=" + return_id + "&action=owt_lib_handler&param=owt_lib_delete_return";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    wpowt_lib_toastr(data.msg, "success");
                    setTimeout(function () {
                        location.reload();
                    }, 1200);
                } else {
                    wpowt_lib_toastr(data.msg, "error");
                }
            });
        }
    });

    /********* EDIT NAME FROM MODAL *****************/
    // edit for name of branch
    jQuery(document).on("click", ".wpowt-edit-name", function () {
        var branch_name = jQuery(this).parents(".wpowt-branch-tr-row").find(".wpowt-branch-edit-name").text();
        jQuery(this).parents(".wpowt-branch-tr-row").find(".wpowt-branch-edit-name").html('<input type="text" class="form-control" name="txt_branch_update_title" value="' + branch_name + '"/>');
        jQuery(this).parents(".wpowt-branch-tr-row").find(".wpowt-branch-action-btns").css("display", "none");
        jQuery(this).parents(".wpowt-branch-tr-row").find(".wpowt-branch-save-btn").css("display", "block");
    });
    // edit for name of staff type
    jQuery(document).on("click", ".wpowt-edit-staff-name", function () {
        var staff_name = jQuery(this).parents(".wpowt-stafftypes-tr-row").find(".wpowt-stafftypes-edit-name").text();
        jQuery(this).parents(".wpowt-stafftypes-tr-row").find(".wpowt-stafftypes-edit-name").html('<input type="text" class="form-control" name="txt_staff_update_title" value="' + staff_name + '"/>');
        jQuery(this).parents(".wpowt-stafftypes-tr-row").find(".wpowt-stafftypes-action-btns").css("display", "none");
        jQuery(this).parents(".wpowt-stafftypes-tr-row").find(".wpowt-stafftypes-save-btn").css("display", "block");
    });
    // edit for name of staff type
    jQuery(document).on("click", ".wpowt-edit-category-name", function () {
        var cat_name = jQuery(this).parents(".wpowt-category-tr-row").find(".wpowt-category-edit-name").text();
        jQuery(this).parents(".wpowt-category-tr-row").find(".wpowt-category-edit-name").html('<input type="text" class="form-control" name="txt_category_update_title" value="' + cat_name + '"/>');
        jQuery(this).parents(".wpowt-category-tr-row").find(".wpowt-category-action-btns").css("display", "none");
        jQuery(this).parents(".wpowt-category-tr-row").find(".wpowt-category-save-btn").css("display", "block");
    });
    /********** EDIT NAME MODAL ENDS ********************/

    /************ CANCEL EDIT FOR MODAL *************************/
    // cancel edit for the branch
    jQuery(document).on("click", ".wpowt-cancel-update", function () {
        var branch_name = jQuery(this).parents(".wpowt-branch-tr-row").find(".wpowt-branch-edit-name input[name='txt_branch_update_title']").val();
        jQuery(this).parents(".wpowt-branch-tr-row").find(".wpowt-branch-edit-name").html(branch_name);
        jQuery(this).parents(".wpowt-branch-tr-row").find(".wpowt-branch-action-btns").css("display", "block");
        jQuery(this).parents(".wpowt-branch-tr-row").find(".wpowt-branch-save-btn").css("display", "none");
    });
    // cancel edit for the staff type
    jQuery(document).on("click", ".wpowt-cancel-stafftypes-update", function () {
        var staff_name = jQuery(this).parents(".wpowt-stafftypes-tr-row").find(".wpowt-stafftypes-edit-name input[name='txt_staff_update_title']").val();
        jQuery(this).parents(".wpowt-stafftypes-tr-row").find(".wpowt-stafftypes-edit-name").html(staff_name);
        jQuery(this).parents(".wpowt-stafftypes-tr-row").find(".wpowt-stafftypes-action-btns").css("display", "block");
        jQuery(this).parents(".wpowt-stafftypes-tr-row").find(".wpowt-stafftypes-save-btn").css("display", "none");
    });
    // cancel edit for the category
    jQuery(document).on("click", ".wpowt-cancel-category-update", function () {
        var staff_name = jQuery(this).parents(".wpowt-category-tr-row").find(".wpowt-category-edit-name input[name='txt_category_update_title']").val();
        jQuery(this).parents(".wpowt-category-tr-row").find(".wpowt-category-edit-name").html(staff_name);
        jQuery(this).parents(".wpowt-category-tr-row").find(".wpowt-category-action-btns").css("display", "block");
        jQuery(this).parents(".wpowt-category-tr-row").find(".wpowt-category-save-btn").css("display", "none");
    });
    /********** CANCEL EDIT MODAL ENDS *******************/

    /************ SAVE NAME BUTTON MODAL **************************/
    // save updated title of branch
    jQuery(document).on("click", ".wpowt-save-branch", function () {
        var current = this;
        var branch_name = jQuery(this).parents(".wpowt-branch-tr-row").find(".wpowt-branch-edit-name input[name='txt_branch_update_title']").val();
        var branch_id = jQuery(this).attr("data-id");
        var postdata = "branch_id=" + branch_id + "&branch_name=" + branch_name + "&action=owt_lib_handler&param=owt_lib_update_branch";
        jQuery("body").addClass("wpowt-pl-processing");
        jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
            jQuery("body").removeClass("wpowt-pl-processing");
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                wpowt_lib_toastr(data.msg, "success");
                jQuery(current).parents(".wpowt-branch-tr-row").find(".wpowt-branch-edit-name").html(branch_name);
                jQuery(current).parents(".wpowt-branch-tr-row").find(".wpowt-branch-action-btns").css("display", "block");
                jQuery(current).parents(".wpowt-branch-tr-row").find(".wpowt-branch-save-btn").css("display", "none");
            } else {
                wpowt_lib_toastr(data.msg, "error");
            }
        });
    });
    // save updated title of staff type
    jQuery(document).on("click", ".wpowt-save-stafftypes", function () {
        var current = this;
        var staff_name = jQuery(this).parents(".wpowt-stafftypes-tr-row").find(".wpowt-stafftypes-edit-name input[name='txt_staff_update_title']").val();
        var staff_id = jQuery(this).attr("data-id");
        var postdata = "staff_id=" + staff_id + "&staff_name=" + staff_name + "&action=owt_lib_handler&param=owt_lib_update_stafftype";
        jQuery("body").addClass("wpowt-pl-processing");
        jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
            jQuery("body").removeClass("wpowt-pl-processing");
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                wpowt_lib_toastr(data.msg, "success");
                jQuery(current).parents(".wpowt-stafftypes-tr-row").find(".wpowt-stafftypes-edit-name").html(staff_name);
                jQuery(current).parents(".wpowt-stafftypes-tr-row").find(".wpowt-stafftypes-action-btns").css("display", "block");
                jQuery(current).parents(".wpowt-stafftypes-tr-row").find(".wpowt-stafftypes-save-btn").css("display", "none");
            } else {
                wpowt_lib_toastr(data.msg, "error");
            }
        });
    });
    // save updated title of category
    jQuery(document).on("click", ".wpowt-save-category", function () {
        var current = this;
        var category_name = jQuery(this).parents(".wpowt-category-tr-row").find(".wpowt-category-edit-name input[name='txt_category_update_title']").val();
        var category_id = jQuery(this).attr("data-id");
        var postdata = "category_id=" + category_id + "&category_name=" + category_name + "&action=owt_lib_handler&param=owt_lib_update_category_title";
        jQuery("body").addClass("wpowt-pl-processing");
        jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
            jQuery("body").removeClass("wpowt-pl-processing");
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                wpowt_lib_toastr(data.msg, "success");
                jQuery(current).parents(".wpowt-category-tr-row").find(".wpowt-category-edit-name").html(category_name);
                jQuery(current).parents(".wpowt-category-tr-row").find(".wpowt-category-action-btns").css("display", "block");
                jQuery(current).parents(".wpowt-category-tr-row").find(".wpowt-category-save-btn").css("display", "none");
            } else {
                wpowt_lib_toastr(data.msg, "error");
            }
        });
    });
    /*********** SAVE NAME BUTTON MODAL ENDS ************************/

    /***************** DELETE NAME BUTTON MODAL **********************************/
    // delete branch 
    jQuery(document).on("click", ".wpowt-delete-branch", function () {
        var conf = confirm("Are you sure want to delete?")
        if (conf) {
            var current = this;
            var branch_id = jQuery(this).attr("data-id");
            var postdata = "branch_id=" + branch_id + "&action=owt_lib_handler&param=owt_lib_delete_branch";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    wpowt_lib_toastr(data.msg, "success");
                    jQuery(current).parents(".wpowt-branch-tr-row").remove();
                } else {
                    wpowt_lib_toastr(data.msg, "error");
                }
            });
        }
    });

    // delete staff type 
    jQuery(document).on("click", ".wpowt-delete-staff", function () {
        var conf = confirm("Are you sure want to delete?")
        if (conf) {
            var current = this;
            var staff_id = jQuery(this).attr("data-id");
            var postdata = "staff_id=" + staff_id + "&action=owt_lib_handler&param=owt_lib_delete_stafftype";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    wpowt_lib_toastr(data.msg, "success");
                    jQuery(current).parents(".wpowt-stafftypes-tr-row").remove();
                } else {
                    wpowt_lib_toastr(data.msg, "error");
                }
            });
        }
    });
    // delete category 
    jQuery(document).on("click", ".wpowt-delete-category", function () {
        var conf = confirm("Are you sure want to delete?")
        if (conf) {
            var current = this;
            var category_id = jQuery(this).attr("data-id");
            var postdata = "category_id=" + category_id + "&action=owt_lib_handler&param=owt_lib_delete_category";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    wpowt_lib_toastr(data.msg, "success");
                    jQuery(current).parents(".wpowt-category-tr-row").remove();
                } else {
                    wpowt_lib_toastr(data.msg, "error");
                }
            });
        }
    });
    /*********** DELETE NAME BUTTON MODAL **********************/

    var listItems = jQuery("#toplevel_page_owt-lib-manage ul > li");
    listItems.each(function (idx, li) {
        var is_text = jQuery(this).find("a").text();
        if (is_text == "") {
            jQuery(this).css("display", "none")
        }
    });

    jQuery("#wpowt-frm-settings-panel").validate({
        submitHandler: function () {
            jQuery("#wpowt-frm-settings-panel").find("button[type='submit']").text('Processing...').css("cursor", "progress");
            var postdata = jQuery("#wpowt-frm-settings-panel").serialize() + "&action=owt_lib_handler&param=owt_lib_settings_panel";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function (response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    jQuery("#wpowt-frm-settings-panel").find("button[type='submit']").text('Submitted, please wait...').css("cursor", "progress")
                    wpowt_lib_toastr(data.msg, "success");
                    setTimeout(function () {
                        location.reload();
                    }, 1200);
                } else {
                    jQuery("#wpowt-frm-settings-panel").find("button[type='submit']").html('<i class="mdi mdi-check-outline"></i> Submit').css("cursor", "pointer");
                    wpowt_lib_toastr(data.msg, "error");
                }
            });
        }
    });
});

function wpowt_lib_toastr(message, type) {
    if (type == "success") {
        toastr.success(message, 'Success')
    } else if (type == "error") {
        toastr.error(message, 'Error')
    }
}