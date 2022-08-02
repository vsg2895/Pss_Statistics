$(document).ready(function () {
    $('.tags-select').select2();
});

$('.delete-department').click(function () {
    let action = $('#departments_delete_form').attr('action');
    action = action.replace(':id', $(this).attr('data-id'));
    $('#departments_delete_form').attr('action', action);
});

function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    location.search
        .substr(1)
        .split("&")
        .forEach(function (item) {
            tmp = item.split("=");
            if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
        });
    return result;
}

$('.departmets-tab-link').click(function (e) {

    let self = $(this);
    let page = findGetParameter('page');
    if (page != null) {
        window.location.href = window.location.href.split('?')[0] + e.target.hash;
        console.log(window.location.href)
    }

});
