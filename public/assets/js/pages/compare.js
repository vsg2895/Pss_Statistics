const dataTableInit = {
    "searching": true,
    "bInfo": false,
    "pageLength": 200,
    "lengthChange": false,
    "order": [[3, "desc"]],
    // "order": [[ $('tr.red-column').index(),  'asc' ]],
    language: {
        'paginate': {
            'previous': "<i class='ni ni-bold-left'></i>",
            'next': "<i class='ni ni-bold-right'></i>"
        }
    }
};

if (!$('#compareDataTable1').hasClass('empty-compare')) {
    $(document).find('#compareDataTable1').DataTable(dataTableInit);
}
if (!$('#compareDataTable2').hasClass('empty-compare')) {
    $(document).find('#compareDataTable2').DataTable(dataTableInit);
}
$('#export_excel_compare').submit(function (e) {
    let s_start = $('#compare-s-start').val()
    let s_end = $('#compare-s-end').val()
    let start = $('#compare-start').val()
    let end = $('#compare-end').val()
    let callsCount = $('#calls_count').val() ? $('#calls_count').val() : 0;
    console.log(callsCount)
    $(this).append('<input type="hidden" name="s_start" value=' + s_start + ' />');
    $(this).append('<input type="hidden" name="s_end" value=' + s_end + ' />');
    $(this).append('<input type="hidden" name="start" value=' + start + ' />');
    $(this).append('<input type="hidden" name="end" value=' + end + ' />');
    $(this).append('<input type="hidden" name="calls_count" value=' + callsCount + ' />');


})
