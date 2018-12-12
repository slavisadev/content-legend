$ = jQuery;
var connector = {
  getData: function (methodType, route, dataType, dataBlock, callbackFunction, context) {
    if (route === '')
      route = 'http://toposiguranje.rs/wp-admin/admin-ajax.php',
        $.ajax({
          async: true,
          type: methodType,
          dataType: dataType,
          url: GLOBALROUTE,
          data: dataBlock,
        }).then(function (data) {
          if (callbackFunction != null && context != null) {
            callbackFunction(context, data);
          }
          return data;
        });
  },
};
$('.content-blocks').sortable();
$('.content-blocks').disableSelection();
var blockItemTemplate =
  $('<div class="block-item">' +
    '<span class="dragme">' +
    '<i class="fa fa-arrows"></i>' +
    '</span>' +
    '<strong>' +
    '</strong>' +
    '<div class="optionsme">' +
    '<a href=""><i class="fa fa-external-link"></i></a>' +
    '<i class="fa fa-remove"></i>' +
    '</div>' +
    '<input type="hidden" class="contentblock-title" />' +
    '<input type="hidden" name="contentblockgroup[]" class="contentblock-id" />' +
    '</div>',
  );
var contentblock_ids = '';
$(document).on('click', '#add-content-block', function () {
  var dataBlock = {
    action: 'get_content_block_data',
    contentblockid: $('#contentblock').val(),
  };

  function callBackFunction(context, response) {
    blockItemTemplate = blockItemTemplate.clone();
    blockItemTemplate.attr('data-postid', response.data.ID);
    blockItemTemplate.find('.contentblock-title').val(response.data.post_title);
    blockItemTemplate.find('.contentblock-id').val(response.data.ID);
    blockItemTemplate.find('strong').html(response.data.post_title);
    blockItemTemplate.find('.optionsme a').attr('href', response.contentblockUID);
    $('.content-blocks').prepend(blockItemTemplate);
    contentblock_ids += $('#contentblock').val() + ',';
    $('#contentblock option[value=\'' + response.data.ID + '\']').remove();
  }

  connector.getData('POST', '', 'json', dataBlock, callBackFunction, $(this));
});
$(document).on('click', '.fa-remove', function () {
  var returnID = $(this).parent().parent().data('postid');
  var returnName = $(this).parent().parent().find('.contentblock-title').val();
  var optionObject = $('<option value=\'' + returnID + '\'>' + returnName + '</option>');
  $('#contentblock').append(optionObject);
  $(this).parent().parent().remove();
});

