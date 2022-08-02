// import {showUploadedName} from "../script";

$('.edit-provider').click(function () {
    $.get(`/admin/service-providers/${$(this).attr('data-id')}/edit`, function (data, status) {
        let provider = data.provider;
        let action = $('#provider_update_form').attr('action');
        $('#provider_name_update').val(provider.name);
        $('#provider_email_update').val(provider.email);
        action = action.replace(':id', provider.id);
        $('#provider_update_form').attr('action', action);
    });
})

$('.delete-provider').click(function () {
    let action = $('#provider_delete_form').attr('action');
    action = action.replace(':id', $(this).attr('data-id'));
    $('#provider_delete_form').attr('action', action);
})

$('.delete-provider-file').click(function () {
    let action = $('#provider_file_delete_form').attr('action');
    if ($(this).has)
        action = action.replace(':id', $(this).attr('data-id'));
    $('#provider_file_delete_form').attr('action', action);
})
$('.delete-provider-media').parent().click(function () {
    let action = $('#provider_media_delete_form').attr('action');
    if ($(this).has)
        action = action.replace(':id', $(this).find('.delete-provider-media').attr('data-id'));
    $('#provider_media_delete_form').attr('action', action);
})

$('.delete-provider-file-all').click(function () {
    console.log($(this).data('ids'));
    $('#idsAll').val($(this).data('ids'))
})
$('.delete-provider-media-all').parent().click(function () {
    console.log($(this));
    console.log($(this).find('.delete-provider-media-all').data('ids'));
    $('#idsMediaAll').val($(this).find('.delete-provider-media-all').data('ids'))
})
$('.delete-data').click(function () {
    let _this = $(this);
    _this.parents('td').find('.update-from-provider').val('');
    _this.parents('form').submit();
})

$('.submit_download').click(function (e) {
    e.preventDefault();
    $(this).parent().submit();
});

$('#file-upload').on('change', function (e) {
    showUploadedName($(this))
})
$('#file-upload-for-all').on('change', function (e) {
    showUploadedName($(this))
})


