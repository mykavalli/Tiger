


function copyData(element) {
    var data = element.val(); // Get the text from the element

    var tempInput = $('<input>'); // Create a temporary input element
    $('body').append(tempInput); // Append it to the body

    tempInput.val(data); // Set the value of the temporary input element
    tempInput[0].select(); // Select the input element
    document.execCommand('copy'); // Copy the selected text to the clipboard
    tempInput.remove(); // Remove the temporary input element
    showNotification('Copy data success', 'success');
};

//user is "finished typing," do something
  function doneTyping () {
    var input_code = $('#change_item_code').val();
    var input_name = $('#change_item_name').val();
    var branch = $('#change_item_branch').val();
    var protocol = '';
    if (window.location.protocol == 'https:') {
        protocol = 'https://';
    } else {
        protocol = 'http://';
    }
    var port = window.location.port != '' ? ':' + window.location.port : '';
    protocol = protocol + window.location.hostname + port + '/';
    if (input_code.length == 0 && input_name.length == 0) {
      $('#mainItem').empty();
    }

    if (input_code.length > 0 || input_name.length > 0){
      $.ajax({
          type: 'POST',
          url: protocol + 'products/product-find-item',
          data: {input_code: input_code, input_name: input_name, branch: branch},
          success: function(data) {
              if (data != null) {
                  $('#mainItem').empty();
                  $('#mainItem').addClass('border-4');
                  $('#mainItem').append(data);
              }
          }
      });
    }
  }

  function receiveAction (receive_stt, user, receive_id) {
    var userR = '';
    if (receive_stt) {
      userR = user;
    }
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
      url: protocol + 'shipping-services/receive-checking',
      data: {receive_stt: receive_stt, receive_id: receive_id, receive_user: userR},
      success: function(data) {
        showNotification('Cập nhật thành công !', 'success');
      }
    });
    
  }

  function getItem(autoCode, nav_code, itemName) {
    var classChoose = $('.type-change').val();

    //Reset input hidden name 
    if ($('.'+classChoose+'_name').length > 0) {
      $('.'+classChoose+'_name').val(itemName);
    }

    //Change input code 
    if ($('#'+classChoose+'_nav').length > 0) {
      $('#'+classChoose+'_nav').val(nav_code);
    }

    //Change item code
    if ($('#'+classChoose).length > 0) {
      $('#'+classChoose).val(autoCode);
    }

    //Change text display name
    if ($('.'+classChoose).length > 0) {
      $('.'+classChoose).text(itemName);
    }

    //empty main display item
    $('#mainItem').empty();

    //Reset input type item code NAV
    if ($('#change_item_code').length > 0) {
      $('#change_item_code').val('');
    }

    //Reset input type item name NAV
    if ($('#change_item_name').length > 0) {
      $('#change_item_name').val('');
    }

    //Final hide panel cho item
    $('.change-items').addClass('d-none');
  }

  // Get export
  function getReport(idForm, id, type_post, module) {
    $('#download_div').empty();
    $('#download_div').append('<div class="col-12 text-center mt-2 mb-2 pt-3 pb-3 border-3"><h4 class="text-danger font-italic"><i class="fa fa-fan fa-spin"></i> Vui lòng đợi trong giây lát <i class="fa fa-fan fa-spin"></i> </h4></div>');
    var protocol = '';
    if (window.location.protocol == 'https:') {
        protocol = 'https://';
    } else {
        protocol = 'http://';
    }
    var port = window.location.port != '' ? ':' + window.location.port : '';
    protocol = protocol + window.location.hostname + port + '/';

    var dataPost = null;
    if (type_post == 'single') {
      dataPost = id;
    }
    if ((type_post == 'index' || type_post == 'schedule') && idForm != '') {
      dataPost = $('#'+idForm).serializeArray();
    }
    
    $.ajax({
      type: 'POST',
      url: protocol + 'products/product-export-data',
      data: {type_export: type_post, dataPost: dataPost, module: module},
      success: function(data) {
        if (data != null) {
          $('#download_div').empty();
          $('#download_div').append(data);
          if (document.getElementById('btn_download_report')){
            setTimeout(function(){
              document.getElementById('btn_download_report').click();
            }, 500);
          }
        }
      },
      error: function () {
        $('#download_div').empty();
        $('#download_div').append('<div class="col-12 text-center mt-2 mb-2 pt-3 pb-3 border-3"><h4 class="text-danger font-italic"><i class="fa fa-times fa-spin"></i> Đã xảy ra lỗi trong quá trình thực hiện <i class="fa fa-times fa-spin"></i> </h4></div>');
      }
    });
  }

  // Get export
  function getReportV2(type_post) {
    $('#download_div').empty();
    $('#download_div').append('<div class="col-12 text-center mt-2 mb-2 pt-3 pb-3 border-3"><h4 class="text-danger font-italic"><i class="fa fa-fan fa-spin"></i> Vui lòng đợi trong giây lát <i class="fa fa-fan fa-spin"></i> </h4></div>');
    var url = $(location).attr('href');
    var formData = null;
    if ($('#form_search').length) {
      // The element with ID "myElement" exists on the page
      formData = $('#form_search').serializeArray();
    }
        
    $.ajax({
      type: 'POST',
      url: url,
      data: {post_type: type_post, formData: formData},
      success: function(data) {
        if (data.fileName != null || data.listFileName != null) {
          $('#download_div').empty();
          if (data.fileName != null) {
            $('#download_div').append('<div class="col-12 text-center mt-2 mb-2 pt-3 pb-3 border-3"><a class="btn btn-custom-warning " id="btn_download_report" download href="/public/downloads/production/'+ data.fileName + '"><i class="fa fa-download pr-2"></i> Download</a></div>');
          } else if (data.listFileName != null) {
            var html = '<div class="col-12 text-center mt-2 mb-2 pt-3 pb-3 border-3">';
            $.each(data.listFileName, function(index, item) {
              html += '<a class="btn btn-custom-warning" download href="/public/downloads/production/'+ item.fileName + '"><i class="fa fa-download pr-2"></i> Download file ' + index + '</a>';
            });
            html += '</div>'

            $('#download_div').append(html);
          }
          
          if (document.getElementById('btn_download_report')){
            setTimeout(function(){
              document.getElementById('btn_download_report').click();
            }, 500);
          }
        } else {
          $('#download_div').empty();
          $('#download_div').append('<div class="col-12 text-center mt-2 mb-2 pt-3 pb-3 border-3"><h4 class="text-danger font-italic"><i class="fa fa-fan fa-spin"></i> Không có file download <i class="fa fa-fan fa-spin"></i> </h4></div>');
        }
      },
      error: function () {
        $('#download_div').empty();
        $('#download_div').append('<div class="col-12 text-center mt-2 mb-2 pt-3 pb-3 border-3"><h4 class="text-danger font-italic"><i class="fa fa-times fa-spin"></i> Đã xảy ra lỗi trong quá trình thực hiện <i class="fa fa-times fa-spin"></i> </h4></div>');
      }
    });
  }

  // Get export current url
  function getReportV3(type_post, arg_2, arg_3, arg_4) {
    $('#download_div').empty();
    $('#download_div').append('<div class="col-12 text-center mt-2 mb-2 pt-3 pb-3 border-3"><h4 class="text-danger font-italic"><i class="fa fa-fan fa-spin"></i> Vui lòng đợi trong giây lát <i class="fa fa-fan fa-spin"></i> </h4></div>');
    var url = $(location).attr('href');
    var formData = null;
    if ($('#form_search').length) {
      // The element with ID "myElement" exists on the page
      formData = $('#form_search').serializeArray();
    }
        
    $.ajax({
      type: 'POST',
      url: url,
      data: {type_post: type_post, arg_2: arg_2, arg_3: arg_3, arg_4: arg_4, formData: formData},
      success: function(data) {
        console.log(data);
        if (data.fileName != null || data.listFileName != null) {
          $('#download_div').empty();
          if (data.fileName != null) {
            $('#download_div').append('<div class="col-12 text-center mt-2 mb-2 pt-3 pb-3 border-3"><a class="btn btn-custom-warning " id="btn_download_report" download href="/public/downloads/'+ data.fileName + '"><i class="fa fa-download pr-2"></i> Download</a></div>');
          } else if (data.listFileName != null) {
            var html = '<div class="col-12 text-center mt-2 mb-2 pt-3 pb-3 border-3">';
            $.each(data.listFileName, function(index, item) {
              html += '<a class="btn btn-custom-warning mr-1" download href="/public/downloads/'+ item + '"><i class="fa fa-download pr-2"></i> Download file ' + index + '</a>';
            });
            html += '</div>'

            $('#download_div').append(html);
          }
          
          if (document.getElementById('btn_download_report')){
            setTimeout(function(){
              document.getElementById('btn_download_report').click();
            }, 500);
          }
        } else {
          $('#download_div').empty();
          $('#download_div').append('<div class="col-12 text-center mt-2 mb-2 pt-3 pb-3 border-3"><h4 class="text-danger font-italic"><i class="fa fa-fan fa-spin"></i> Không có file download <i class="fa fa-fan fa-spin"></i> </h4></div>');
        }
      },
      error: function () {
        $('#download_div').empty();
        $('#download_div').append('<div class="col-12 text-center mt-2 mb-2 pt-3 pb-3 border-3"><h4 class="text-danger font-italic"><i class="fa fa-times fa-spin"></i> Đã xảy ra lỗi trong quá trình thực hiện <i class="fa fa-times fa-spin"></i> </h4></div>');
      }
    });
  }

  // POST current form
  function postCurrentForm(type_post) {
    $('#download_div').empty();
    $('#download_div').append('<div class="col-12 text-center mt-2 mb-2 pt-3 pb-3 border-3"><h4 class="text-danger font-italic"><i class="fa fa-fan fa-spin"></i> Đang thực hiện, vui lòng chờ trong giây lát. <i class="fa fa-fan fa-spin"></i> </h4></div>');
  
    var url = $(location).attr('href');
        
    $.ajax({
      type: 'POST',
      url: url,
      data: {type_post: type_post},
      success: function(data) {
    
        $('#download_div').empty();

        Swal.fire({
          position: 'top-end',
          icon: 'success',
          title: 'Your work has been saved',
          showConfirmButton: false,
          timer: 800
        });
      },
      error: function () {
    
        $('#download_div').empty();
        
        swal.fire({
          title: 'Error: ' + error.statusText,
          text: name,
          icon: 'warning',
          confirmButtonColor: '#3085d6',
          confirmButtonText: 'Ok !',
        })
      }
    });
  }
  
  function addItem() {
    var qty = $('#super_item_quantity').val();

    if (qty == 0){
      window.location.href = window.location;
    } else {
      window.location.href = window.location + '?add-item=' + qty;
    }
  };

  function clearChangeTime() {
    $('#change_time').empty();
  }

  function clearAction(id) {
    $('#' + id).empty();
  }
 
  function addLog(comming, btnText, link) {
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
      data: {comming: comming, button: btnText, link: link},
      url: protocol + 'logger'
    });
  }

  function QCConfirm(id, fullname) {
    var url = $(location).attr('href');
        
    $.ajax({
      type: 'POST',
      url: url,
      data: {type_post: 'qc_confirm', id: id, fullname: fullname},
      success: function(data) {
        
        if (data != null) {
          
          $('.qc-confirm-' + id).empty();
          $('.qc-confirm-' + id).append(fullname);
          Swal.fire({
              position: 'top-end',
              icon: 'success',
              title: 'Your work has been saved',
              showConfirmButton: false,
              timer: 800
          });
        }
      },
      error: function (error) {
        swal.fire({
            title: 'Error: ' + error.statusText,
            text: name,
            icon: 'warning',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok !',
        })
      }
    });
  }

  function closePopupByClass(className) {
    $('.' + className).addClass('d-none');
  }
  
  function closePopup(params) {
    $('.notification_all.' + params).hide();
  }
  
  jQuery(document).ready(function($){

    $(document).keyup(function(e) {
      if (e.key === "Escape" || e.keyCode === 27) {
        // Code to be executed when ESC key is pressed
        if ($('.action_js').length) {
          $('.action_js').empty();
          if ($('#action_js').length <= 0) {
            $('.action_js').attr('id', 'action_js');
          }
        }
        if ($('.popup').length) {
          $('.popup').addClass('d-none');
        }
      }
    });

    $('.wait-for-click').click(function(){
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
        } else {
            $(this).addClass('active');
        }
    });

    //Oil filter
    $('.report-oil-filter').click(function(){
      $('#action_js').empty();
      if (window.location.protocol == 'https:') {
        protocol = 'https://';
      } else {
          protocol = 'http://';
      }
      var port = window.location.port != '' ? ':' + window.location.port : '';
      protocol = protocol + window.location.hostname + port + '/';

      $.ajax({
          type: 'GET',
          url: protocol + 'oil-filter',
          success: function(data) {
              if (data != null) {
                  $('#action_js').empty();
                  $('#action_js').append(data);
              }
          }
      });
    })

    //Lock report
    $('.btn-lock-report').click(function(){
      var link = $(this).attr('report-link');
      $(this).parent().empty().append('<span class="text-danger text-bold"><i class="fas fa-lock"></i></span>');

      $.ajax({
          type: 'POST',
          url: link,
          data: {type_post: 'lock_report'}
      });

      addLog('frontend', $(this).attr('title'), link);
    });

    //Change time function
    $('.btn-change-time').click(function(){
      var type = $(this).attr('btn-type');
      var code = $(this).attr('report-code');

      $('#change_time').empty();
      if (window.location.protocol == 'https:') {
        protocol = 'https://';
      } else {
          protocol = 'http://';
      }
      var port = window.location.port != '' ? ':' + window.location.port : '';
      protocol = protocol + window.location.hostname + port + '/';

      $.ajax({
          type: 'POST',
          url: protocol + 'change-time',
          data: {type: type, code: code},
          success: function(data) {
              if (data != null) {
                  $('#change_time').empty();
                  $('#change_time').append(data);
              }
          }
      });
    });

    $('.btn').click(function(e){
        var btnText = $(this).text();
        if (btnText == '') {
          btnText = $(this).attr('title');
        }

        addLog('frontend', btnText, window.location.pathname);
    });

    //Append quantity add item
    $('.add_super_item').click(function(){
      $('#add_super_item').show();
      $('#add_super_item').addClass('supper_item_qty box');
      $('#add_super_item').append('<div class="card card-info"><div class="card-header h4 text-center">Số dòng muốn thêm/Qty of rows </div><div class="card-body text-center"><input type="number" min="0" value="0" class="form-control" id="super_item_quantity"/><span onclick="addItem()" class="btn btn-custom-success mt-2 accept-quantity">Ok</span></div></div>');
    });

    //Clear item to rename
    $('.clear-item').click(function(){
      var item = $(this).attr('for-item');
      $('.'+item).attr('readonly', false);
      $('.'+item).val('');
    });

    // Show report clean
    $('.report-clean').click(function(){
      
      if ($('.popup.report-clean').hasClass('d-none')) {
        $('.popup.report-clean.d-none').removeClass('d-none');
      } else {
        $('.popup.report-clean').show();
      }
    });

    //Close notification all
    $('.notification_all.popup .btn-close').click(function(){
      $('.notification_all.popup').hide();
    });

    // Export data
    $('.btn-export-single').click(function() {
      getReport(0, $(this).attr('current-id'),'single', $(this).attr('current-module'));
    });
    
    $('.btn-export-index').click(function() {
      getReport($(this).attr('current-form'), 0,'index', $(this).attr('current-module'));
    });
    
    $('.btn-export-schedule').click(function() {
      getReport('', 0,'schedule', $(this).attr('current-module'));
    });

    $('.delete-item').click(function(){
      var type = $(this).attr('type-change');
      $("#"+type).val('');
      $("."+type+"_name").val('-----------');
      $("."+type).text('-----------');
    })

    $('.change-items .btn-close').click(function(){
      $('#change_item_code').val('');
      $('#change_item_name').val('');
      $('#mainItem').empty();
    });

    //setup before functions
    var typingTimer;
    var doneTypingInterval = 500;
    var input_code = $('#change_item_code');
    var input_name = $('#change_item_name');

    //on keyup, start the countdown
    input_code.on('keyup', function () {
      clearTimeout(typingTimer);
      typingTimer = setTimeout(doneTyping, doneTypingInterval);
    });

    //on keydown, clear the countdown 
    input_code.on('keydown', function () {
      clearTimeout(typingTimer);
    });

    //on keyup, start the countdown
    input_name.on('keyup', function () {
      clearTimeout(typingTimer);
      typingTimer = setTimeout(doneTyping, doneTypingInterval);
    });

    //on keydown, clear the countdown 
    input_name.on('keydown', function () {
      clearTimeout(typingTimer);
    });

    $('#change_item_branch').on('change', function () {
      clearTimeout(typingTimer);
      typingTimer = setTimeout(doneTyping, doneTypingInterval);
    });

    $('.change-item').click(function(){
      $('.change-items').removeClass('d-none');
      $('.type-change').val($(this).attr('type-change'));
      $('.title-header').text($('.'+$(this).attr('type-change')+'_name').val());
    });

    $('.edit-time').click(function(){
      var type_edit = $(this).attr('type-edit');
      $('.edit-times').removeClass('d-none');
      
      $('#type_edit').attr('value', type_edit);
      $('#time_1').val($('.'+type_edit+'_1').val());
      $('#time_2').val($('.'+type_edit+'_2').val());
      $('#time_3').val($('.'+type_edit+'_3').val());
      $('#time_4').val($('.'+type_edit+'_4').val());

      if (type_edit == 'clean' || type_edit == 'clean_machine') {
        $('.type-modify').text(' vệ sinh máy');
      }

      if (type_edit == 'time' || type_edit == 'time_machine') {
        $('.type-modify').text(' chạy máy');
      }

      if (type_edit == 'stop' || type_edit == 'stop_machine') {
        $('.type-modify').text(' ngưng máy');
      }
    });

    $('.popup-update-time').click(function(){
      var typeEdit = $('#type_edit').val();
      var space = '';
      var time_1 = $('#time_1').val();
      var time_2 = $('#time_2').val();
      var time_3 = $('#time_3').val();
      var time_4 = $('#time_4').val();
      if (typeof(time_1) === 'undefined'){
        time_1 = '';
      }
      if (typeof(time_2) === 'undefined'){
        time_2 = '';
      }
      if (typeof(time_3) === 'undefined'){
        time_3 = '';
      }
      if (typeof(time_4) === 'undefined'){
        time_4 = '';
      }
      
      if ((time_1 != '' || time_2 != '') && (time_3 != '' || time_4 != '')){ 
        space = '---&---';
      }

      $('.'+typeEdit+'_1').attr('value', time_1);
      $('.'+typeEdit+'_2').attr('value', time_2);

      var connect_1 = '';
      if (time_2 != ''){
        if (time_1 == '') {
          connect_1 = ' ... - ';
        } else {
          connect_1 = ' - ';
        }
      }
      $('.'+typeEdit+'-1').text(time_1+connect_1+time_2);

      $('.'+typeEdit+'-space').text(space);

      $('.'+typeEdit+'_3').attr('value', time_3);
      $('.'+typeEdit+'_4').attr('value', time_4);

      var connect_2 = '';
      if (time_4 != ''){
        if (time_3 == '') {
          connect_2 = ' ... - ';
        } else {
          connect_2 = ' - ';
        }
      }
      $('.'+typeEdit+'-2').text(time_3+connect_2+time_4);

      // Reset value
      $('#time_1,#time_2,#time_3,#time_4').val('');
      $('#type_edit').val('');
      $('.edit-times').addClass('d-none');

    });
      
    $('#lock_report').click(function(){
      $('#type_post').val('lock_report');
      $('form#form_search').submit();
    })
    $('#unlock_report').click(function(){
      $('#type_post').val('unlock_report');
      $('form#form_search').submit();
    })
  })