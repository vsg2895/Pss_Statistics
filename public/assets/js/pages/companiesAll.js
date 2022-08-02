var start = $('.current_change').filter('[name="start"]').val();
var end = $('.current_change').filter('[name="end"]').val();

$(document).ready(function () {
    $('.tags-select').select2();
});
if (new URL(window.location.href).searchParams.get('start_date')) {
    $('#compare-tab').addClass('active show');
    $('#compare').addClass('active show');
    $('#range-tab').removeClass('active show');
    $('#range').removeClass('active show');
}
initDataTable();

function initDataTable() {
    $(document).find('#companies_table').DataTable({
        "bInfo": false,
        "pageLength": 25,
        "lengthChange": false,
        "order": [[3, "desc"]],
        language: {
            'paginate': {
                'previous': "<i class='ni ni-bold-left'></i>",
                'next': "<i class='ni ni-bold-right'></i>"
            }
        }
    });
}

// Ignore html2canvas area elements which don,t print in pdf report
$('#companies_table_filter').attr('data-html2canvas-ignore', 'true');
$('#companies_table_paginate').attr('data-html2canvas-ignore', 'true');

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

$('.default-date-buttons').click(function () {
    let _this = $(this);
    let form = _this.parents('form');

    form.find('input[name="start"]').val(_this.attr('data-start'));
    form.find('input[name="end"]').val(_this.attr('data-end'));

    form.submit();
});

$("#statistics-all-companies-pdf").find('.shadow').addClass('importantNoShadowPdf');

getGeneralPDF($("#statistics-all-companies-pdf")[0], $("#statistics-all-companies-pdf"))

$('#export_companies_pdf').click(function () {
    generateGeneralPDF($('.header-name').text(), start, end)
})

$('#companies_table_paginate .paginate_button').on('click', function () {
    getGeneralPDF($("#statistics-all-companies-pdf")[0], $("#statistics-all-companies-pdf"), myImage, pdf)
})

$('.update-all').on('click', function (e) {
    e.preventDefault();
    var checkedCount = 0;
    let url = route('admin.company.updateFeesByDateAll');
    let availableCompanies = [];
    let allDatatable = $('#companies_table').DataTable();
    // get datatables search data
    allDatatable.rows({search: 'applied'}).data().map((row) => {
        availableCompanies.push(row[1])
    })
    console.log(availableCompanies.length)
    $('.current_change').each(function () {
        if ($(this).prop('checked') == true) {
            checkedCount += 1;
        }
    })
    console.log(checkedCount)
    if (availableCompanies.length >= 15 && availableCompanies.length <= 50 && checkedCount > 0) {
        var longTimeCount = confirm("Historical Update may be extend little.You can reduce updated companies count");
    }
    if (longTimeCount || (availableCompanies.length <= 15 && checkedCount > 0)) {
        url = url + `?ids=${availableCompanies}` + `&start=${start}` + `&end=${end}`
        $('#update-fees-form-all').attr('action', url);
        $('#update-fees-form-all').submit();
    }
    if (availableCompanies.length > 50 && checkedCount > 0) {
        let redirect = confirm("Historical update could not be performed. Please reduce the number of companies being updated")
    }
})

$('#companies_table_filter').find('input[type="search"]').on('input', function () {
    // Get datatable current count
    var tableRowCount = $('#companies_table').DataTable().page.info().recordsDisplay;
    $('.dashboard_companies_count').text(" - " + tableRowCount);
    $('.dashboard_companies_count').prepend('\xa0')
})
$('.provider_filter').on('change', function () {
    let countChecked = 0;
    let self = $(this)
    $('.provider_filter').each(function () {
        if ($(this).is(':checked')) {
            countChecked++;
        }
    })
    if (countChecked > 1) {
        $('.provider_filter').prop('checked', false)
    }
    if (self.is(':checked')) {
        countChecked === 1 ? self.prop('checked', true) : self.prop('checked', false)
    } else {
        countChecked === 0 ? self.prop('checked', false) : self.prop('checked', true)
    }

})
