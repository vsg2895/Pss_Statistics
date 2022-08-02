$(document).ready(function () {
    $('.default-date-buttons').click(function () {
        let _this = $(this);
        let form = _this.parents('form');

        form.find('input[name="start"]').val(_this.attr('data-start'));
        form.find('input[name="end"]').val(_this.attr('data-end'));

        form.submit();
    });
});
