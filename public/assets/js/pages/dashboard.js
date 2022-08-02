const interval = 1000 * 60 * 5;//5 mins
if (new URL(window.location.href).searchParams.get('start_date')) {
    $('#compare-tab').addClass('active show');
    $('#compare').addClass('active show');
    $('#range-tab').removeClass('active show');
    $('#range').removeClass('active show');
}
initDataTable();

setTimeout(function () {
    setInterval(function () {
        fetch('/home', {
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            },
        })
            .then(response => response.json())
            .then(data => {
                $('#dashboard_content').empty()
                $('#dashboard_content').append(data);
                initDataTable();
            });
    }, interval)
}, interval);

function initDataTable()
{
    $(document).find('#user_stat_table').DataTable({
        "searching": false,
        "bInfo": false,
        "pageLength": 25,
        "lengthChange": false,
        "order": [[ 1, "desc" ]],
        language: {
            'paginate': {
                'previous': "<i class='ni ni-bold-left'></i>",
                'next': "<i class='ni ni-bold-right'></i>"
            }
        }
    });
}

$('#export_pdf').click(function (event) {
    let url = window.location.href;
    url = new URL(url);
    $('#export_start_date').val(url.searchParams.get('start_date'));
    $('#export_compare_date').val(url.searchParams.get('compare_date'));
    $('#export_start').val(url.searchParams.get('start'));
    $('#export_end').val(url.searchParams.get('end'));

    if (!url.searchParams.get('start') && !url.searchParams.get('end')) {
        $('#export_date_range').val(false);
    }

    $('#export_form').submit();
});
