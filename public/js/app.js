jQuery(document).ready(function($) {

    $('.copy_report').click(function(){
      $('form#copy_report').submit();
    });

    $('.date-from,.date-to').attr('readonly', true);
    $('.date-from.no-readonly,.date-to.no-readonly').attr('readonly', false);

    $('.delete_button').click(function() {
        var name = $(this).attr('name-delete');
        var form = $(this).attr('data-form');
        
        deleteSubmitForm(name, form);
    });

    $('.delete_button_2').click(function() {
        var id = $(this).attr('id');
        var name = $(this).attr('name-delete');
        var idRemove = $(this).attr('idRemove');
        var type_post = $(this).attr('type_post');
        swal.fire({
            title: 'Bạn có muốn xóa?',
            text: name,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Có, xóa ngay!',
            cancelButtonText: 'Không!',
        }).then((result) => {
            if (result.value && id != null) {
                $.ajax({
                    type: 'POST',
                    url: $(location).attr('href'),
                    data: {type_post: type_post, id: id},
                    success: function(data) {
                        swal.fire({
                            title: 'Hoàn thành',
                            text: name,
                            icon: 'success',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok !',
                        })
                    },
                    error: function (error) {
                        swal.fire({
                            title: 'Lỗi: ' + error.statusText,
                            text: name,
                            icon: 'warning',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok !',
                        })
                    }
                });
            }

            if (idRemove != null) {
                $('#' + idRemove).remove();
            }
        })
    });

    $('.btn-save-check-time').click(function(){
      var check = false;
      var result = true;
      // Code
        // --Check clean time
        var s_clean_1 = '';
        if(typeof($("#start_clean_1")).val() != "undefined" && $("#start_clean_1").val() !== null) {
          s_clean_1 = $("#start_clean_1").val();
        }
        var e_clean_1 = '';
        if(typeof($("#end_clean_1")).val() != "undefined" && $("#end_clean_1").val() !== null) {
          e_clean_1 = $("#end_clean_1").val();
        }
        var s_clean_2 = '';
        if(typeof($("#start_clean_2")).val() != "undefined" && $("#start_clean_2").val() !== null) {
          s_clean_2 = $("#start_clean_2").val();
        }
        var e_clean_2 = '';
        if(typeof($("#end_clean_2")).val() != "undefined" && $("#end_clean_2").val() !== null) {
          e_clean_2 = $("#end_clean_2").val();
        }

        if (s_clean_1 != '' && e_clean_1 != '') {
          check = validateTime(s_clean_1, e_clean_1, 'vệ sinh máy {1}');
          if (!check && result) {result = false;}
        }

        if (s_clean_2 != '' && e_clean_2 != '') {
          check = validateTime(s_clean_2, e_clean_2, 'vệ sinh máy {2}');
          if (!check && result) {result = false;}
        }

        // --End check clean time 

        // --Check start time
        var start_1 = '';
        if(typeof($("#start_time_1")).val() != "undefined" && $("#start_time_1").val() !== null) {
          start_1 = $("#start_time_1").val();
        }
        var end_1 = '';
        if(typeof($("#end_time_1")).val() != "undefined" && $("#end_time_1").val() !== null) {
          end_1 = $("#end_time_1").val();
        }
        var start_2 = '';
        if(typeof($("#start_time_2")).val() != "undefined" && $("#start_time_2").val() !== null) {
          start_2 = $("#start_time_2").val();
        }
        var end_2 = '';
        if(typeof($("#end_time_2")).val() != "undefined" && $("#end_time_2").val() !== null) {
          end_2 = $("#end_time_2").val();
        }

        if (start_1 != '' && end_1 != '') {
          check = validateTime(start_1, end_1, 'chạy máy {1}');
          if (!check && result) {result = false;}
        }

        if (start_2 != '' && end_2 != '') {
          check = validateTime(start_2, end_2, 'chạy máy {2}');
          if (!check && result) {result = false;}
        }
        // --End check start time

        // --Check stop time
        var s_stop_1 = '';
        if(typeof($("#start_stop_1")).val() != "undefined" && $("#start_stop_1").val() !== null) {
          s_stop_1 = $("#start_stop_1").val();
        }
        var e_stop_1 = '';
        if(typeof($("#end_stop_1")).val() != "undefined" && $("#end_stop_1").val() !== null) {
          e_stop_1 = $("#end_stop_1").val();
        }
        var s_stop_2 = '';
        if(typeof($("#start_stop_2")).val() != "undefined" && $("#start_stop_2").val() !== null) {
          s_stop_2 = $("#start_stop_2").val();
        }
        var e_stop_2 = '';
        if(typeof($("#end_stop_2")).val() != "undefined" && $("#end_stop_2").val() !== null) {
          e_stop_2 = $("#end_stop_2").val();
        }

        if (s_stop_1 != '' && e_stop_1 != '') {
          check = validateTime(s_stop_1, e_stop_1, 'ngưng máy {1}');
          if (!check && result) {result = false;}
        }

        if (s_stop_2 != '' && e_stop_2 != '') {
          check = validateTime(s_stop_2, e_stop_2, 'ngưng máy {2}');
          if (!check && result) {result = false;}
        }
        // --End check stop time
      // End code

      if (result) {
        $('#form_search').attr('method', 'post');
        $('#form_search').submit();
      } else {
        $('#form_search').attr('method', 'get');
      }
    });
});

function deleteSubmitForm(name, form) {
    swal.fire({
        title: 'Bạn có muốn xóa?',
        text: name,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Có, xóa ngay!',
        cancelButtonText: 'Không!',
    }).then((result) => {
        if (result.value) {
            $('#' + form).submit();
        }
    })
}

function validateTime(start, end, message) {
    var checkSet = false;
    var start_time = new Date(start);
    var end_time = new Date(end);

    if (end_time <= start_time) {
      swal.fire({
        title: 'Lỗi thời gian '+message+' !',
        text: name,
        icon: 'warning',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Ok !',
      })
    } else {
        checkSet = true;
    }
    return checkSet;
}

function clearTime(id) {
    $('#'+id).attr('value', '');
    $('#'+id).val('');
}

function showNotification(message, type) {
    if (type == 'success') {
        toastr.success(message);
    }
    if (type == 'info') {
        toastr.info(message);
    }
    if (type == 'error') {
        toastr.error(message);
    }
    if (type == 'warning') {
        toastr.warning(message);
    }
};
$('.date-input').datepicker({ format: 'dd/mm/yyyy' });
$('.time-input').clockpicker();

$('.date-from').datepicker({ format: 'dd/mm/yyyy' });
$('.time-from').clockpicker();
$('.date-to').datepicker({ format: 'dd/mm/yyyy' });
$('.time-to').clockpicker();
$('.date-from,.time-from,.date-to,.time-to').on('change', function() {
    checkDateTimeFromTo($(this).attr('class'));
});

/**Delete image extend request */
$('.fa-trash-alt.del').click(function() {
    var img_name = $(this).attr('img-name');
    $(this).parent().parent().remove();
    if (img_name != '') {
        $.ajax({
            type: 'POST',
            url: window.location.href,
            data: { img_delete: img_name },
            success: function() {
                showNotification('Xóa thành công !', 'success');
            }
        });
    }
});


/**For button go back */
function goBack() {
    window.history.back();
}

function checkDateTimeFromTo($class) {
    var dateFrom = $('.date-from1').val();
    var timeFrom = $('.time-from').val();
    var dateTo = $('.date-to1').val();
    var timeTo = $('.time-to').val();
    if ((dateFrom != '' && timeFrom != '' && dateTo != '' && timeTo != '') && (new Date(dateFrom + ' ' + timeFrom) > new Date(dateTo + ' ' + timeTo))) {
        showNotification('Thời gian sau không được nhỏ hơn thời gian trước !', 'error');
        $('.' + ($class.replace(' ', '.'))).val('');
    }
    if ((dateFrom != '' != '' && dateTo != '') && (new Date(dateFrom + ' 00:00:00') > new Date(dateTo + ' 23:59:59'))) {
        showNotification('Thời gian sau không được nhỏ hơn thời gian trước !', 'error');
        $('.' + ($class.replace(' ', '.'))).val('');
    }
}

function clearNotification() {
    $('#navbar_badge').text('');
    $('#navbar_badge_body').empty();
    $('#navbar_badge_header').text('');
    setTimeout(getNotification, 100);
}

function getNotification() {
    var protocol = '';
    if (window.location.protocol == 'https:') {
        protocol = 'https://';
    } else {
        protocol = 'http://';
    }
    var port = window.location.port != '' ? ':' + window.location.port : '';
    protocol = protocol + window.location.hostname + port + '/';
    $.ajax({
        type: 'POST',
        url: protocol + 'notification',
        success: function(data) {
            if (data != null) {
                $('#navbar_badge').text(data.total);
                $('#navbar_badge_header').text(data.total + ' Thông báo');
                var html = '';
                data.notification.forEach(function(item) {
                    html += '<div class="dropdown-divider"></div> ' +
                        '<a onclick="clearNotification()" target="_blank" href="' + protocol + 'notification/view/' + item.noti_id + '" class="dropdown-item link-noti"><i class="fas fa-envelope mr-2"></i> ' + item.noti_name + '</a>';
                });
                $('#navbar_badge_body').append(html);
            }
        }
    });
}

function getAccess() {
    var protocol = '';
    if (window.location.protocol == 'https:') {
        protocol = 'https://';
    } else {
        protocol = 'http://';
    }
    var port = window.location.port != '' ? ':' + window.location.port : '';
    protocol = protocol + window.location.hostname + port + '/';
    $.ajax({
        type: 'POST',
        url: protocol + 'roles/check'
    });
}

function checkNewNotification() {
    var url = '';
    if (window.location.protocol == 'https:') {
        url = 'https://';
    } else {
        url = 'http://';
    }
    var port = window.location.port != '' ? ':' + window.location.port : '';
    url = url + window.location.hostname + port + '/notification/';
    $.ajax({
        type: 'POST',
        cache: false,
        url: url + 'checkNewNoti',
        success: function(data) {
            if (!jQuery.isEmptyObject(data.notification)) {
                $('span.refresh-noti').trigger('click');
                data.notification.forEach(function(item) {
                    notifyMe(url + 'view/' + item.noti_id, item.noti_type == 'item' ? 'Thiết bị máy móc' : 'Thông báo cá nhân', item.noti_name);
                });
            }
        }
    });
}

function notifyMe(url, title, shortContent) {
    if (window.Notification) {
        // check if permission is already granted
        if (Notification.permission === 'granted') {
            // show notification here
            notiContent(url, title, shortContent);
        } else {
            // request permission from user
            Notification.requestPermission().then(function(p) {
                if (p === 'granted') {
                    // show notification here
                    notiContent(url, title, shortContent);
                } else {
                    // alert('User blocked notifications.');
                }
            });
        }
    }
}

function notiContent(url, title, shortContent) {
    var notify = new Notification(title, {
        body: shortContent,
        icon: 'http://localhost/DWM/public/img/layout/DOF_Logo_32x32.jpg',
    });
    notify.onclick = (e) => {
        window.location.href = url;
    };
}

function modifyCard(type) {
    if ($(".card-" + type).hasClass('d-none')) {
        $(".card-" + type).removeClass('d-none');
        $(".btn-open-" + type).trigger("click");
    } else {
        $(".btn-open-" + type).trigger("click");
        setTimeout(function() { $(".card-" + type).addClass('d-none') }, 500);
    }
}

function modifyCardOpen(param) {
    $("." + param).trigger("click");
}

function openPopup(className) {
    if ($("." + className).length > 0) {
        $("." + className).removeClass("d-none");
    }
}

function closePopup(className) {
    if ($("." + className).length > 0) {
        $("." + className).addClass("d-none");
    }
}


/**
 * jQuery Multifield plugin
 *
 * https://github.com/maxkostinevich/jquery-multifield
 */


// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;
(function($, window, document, undefined) {

    /*
     * Plugin Options
     * section (string) -  selector of the section which is located inside of the parent wrapper
     * max (int) - Maximum sections
     * btnAdd (string) - selector of the "Add section" button - can be located everywhere on the page
     * btnRemove (string) - selector of the "Remove section" button - should be located INSIDE of the "section"
     * locale (string) - language to use, default is english
     */

    // our plugin constructor
    var multiField = function(elem, options) {
        this.elem = elem;
        this.$elem = $(elem);
        this.options = options;
        // Localization
        this.localize_i18n = {
            "multiField": {
                "messages": {
                    "removeConfirmation": "Are you sure you want to remove this section?"
                }
            }
        };

        // This next line takes advantage of HTML5 data attributes
        // to support customization of the plugin on a per-element
        // basis. For example,
        // <div class=item' data-mfield-options='{"section":".group"}'></div>
        this.metadata = this.$elem.data('mfield-options');
    };

    // the plugin prototype
    multiField.prototype = {

        defaults: {
            max: 0,
            locale: 'default'
        },


        init: function() {
            var $this = this; //Plugin object
            // Introduce defaults that can be extended either
            // globally or using an object literal.
            this.config = $.extend({}, this.defaults, this.options,
                this.metadata);

            // Load localization object
            if (this.config.locale !== 'default') {
                $this.localize_i18n = this.config.locale;
            }

            // Hide 'Remove' buttons if only one section exists
            if (this.getSectionsCount() < 2) {
                $(this.config.btnRemove, this.$elem).hide();
            }

            // Add section
            this.$elem.on('click', this.config.btnAdd, function(e) {
                e.preventDefault();
                $this.cloneSection();
            });

            // Remove section
            this.$elem.on('click', this.config.btnRemove, function(e) {
                e.preventDefault();
                var currentSection = $(e.target.closest($this.config.section));
                $this.removeSection(currentSection);
            });

            return this;
        },


        /*
         * Add new section
         */
        cloneSection: function() {
            // Allow to add only allowed max count of sections
            if ((this.config.max !== 0) && (this.getSectionsCount() + 1) > this.config.max) {
                return false;
            }

            // Clone last section
            var newChild = $(this.config.section, this.$elem).last().clone().attr('style', '').attr('id', '').fadeIn('fast');


            // Clear input values
            $('input[type!="radio"],textarea', newChild).each(function() {
                $(this).val('');
            });

            // Fix radio buttons: update name [i] to [i+1]
            newChild.find('input[type="radio"]').each(function() {
                var name = $(this).attr('name');
                $(this).attr('name', name.replace(/([0-9]+)/g, 1 * (name.match(/([0-9]+)/g)) + 1));
            });
            newChild.find('input[type="text"]').each(function() {
                var id = $(this).attr('id');
                $(this).attr('id', id.replace(/([0-9]+)/g, 1 * (id.match(/([0-9]+)/g)) + 1));
            });

            // Reset radio button selection
            $('input[type=radio]', newChild).attr('checked', false);

            // Clear images src with reset-image-src class
            $('img.reset-image-src', newChild).each(function() {
                $(this).attr('src', '');
            });

            // Append new section
            this.$elem.append(newChild);

            // Show 'remove' button
            $(this.config.btnRemove, this.$elem).show();
        },

        /*
         * Remove existing section
         */
        removeSection: function(section) {
            if (confirm(this.localize_i18n.multiField.messages.removeConfirmation)) {
                var sectionsCount = this.getSectionsCount();

                if (sectionsCount <= 2) {
                    $(this.config.btnRemove, this.$elem).hide();
                }
                section.slideUp('fast', function() { $(this).detach(); });
            }
        },

        /*
         * Get sections count
         */
        getSectionsCount: function() {
            return this.$elem.children(this.config.section).length;
        }

    };

    multiField.defaults = multiField.prototype.defaults;

    $.fn.multifield = function(options) {
        return this.each(function() {
            new multiField(this, options).init();
        });
    };



})(jQuery, window, document);