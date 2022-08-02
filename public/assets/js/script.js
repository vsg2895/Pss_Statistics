$('.collapse-sidebar').click(function () {
    const sidebar = $('.sidebar');
    sidebar.toggleClass('hide');
    sidebar.toggleClass('show');
})

$(document).ready(function () {
    if (window.innerWidth > 600) {
        $('.sidebar').addClass('show');
    } else {
        $('.sidebar').addClass('hide');
    }
})
$('[data-toggle="tooltip"]').tooltip();

$('.billing-datapicker').datepicker({
    format: "yyyy-mm-dd", autoclose: true, todayHighlight: true, startDate: new Date('2022-01-24')
});

$('.companies-compare-datepicker').datepicker({
    format: "yyyy-mm-dd", autoclose: true, todayHighlight: true,
});

$('.ps-datepicker').datepicker({
    format: "yyyy-mm-dd", autoclose: true, todayHighlight: true,
});
var start = $('.current_change').filter('[name="start"]').val();
var end = $('.current_change').filter('[name="end"]').val();


$('.nav-tabs a').click(function (e) {
    e.preventDefault();
    $(this).tab('show');
});

// store the currently selected tab in the hash value
$("ul.nav-tabs > li > a").on("shown.bs.tab", function (e) {
    var id = $(e.target).attr("href").substr(1);
    window.location.hash = id;
});

// on load of the page: switch to the currently selected tab
var hash = window.location.hash;
$('.nav-tabs a[href="' + hash + '"]').tab('show');

// Default click select corresponding inputs value
$('.label_fee').on('click', function () {
    $length = $(this).val().length;
    $id = $(this).attr('for');
    $('#' + $id).select();
})

setTimeout(function () {
    if ($('.custom-alert').hasClass('show') && !$('.custom-alert').hasClass('update-realtime-alert')) {
        $('.custom-alert').fadeOut(1000);
    }
}, 5000);


$(document).on('change', '.fee_input_value', function () {
    let self = $(this);
    self.parents('tr').find('.fee-checkbox-div').find('.custom-control-input-fee').val(self.val())
});

$('#companies-import').on('change', function (e) {
    showUploadedName($(this))
})

function showUploadedName(elem) {
    let name = elem.val().replace(/C:\\fakepath\\/i, '')
    elem.prev().text(name);
}

// prepare page in pdf report
var pdf;
var pdfWithout;
var myImage;
var myImage2;

function getGeneralPDF(elem, removeStyleElem = null, dNone = null) {
    html2canvas(elem, {
        scale: 1, onclone: function () {
            if (removeStyleElem !== null) {
                removeStyleElem.find('.shadow').removeClass('importantNoShadowPdf');
            }
        }
    }).then((canvas) => {
        console.log("done ... ");
        if (dNone === null) {
            myImage = canvas.toDataURL("pdf,1.0");
            pdf = new jsPDF('p', 'mm', 'a2');
        } else {
            myImage2 = canvas.toDataURL("pdf,1.0");
            pdfWithout = new jsPDF('p', 'mm', 'a2');
        }
        if (dNone !== null) {
            dNone.addClass('d-none');
        }

    });
}

function generateGeneralPDF(text, start, end, company = '') {

    company === '' ? pdf.addImage(myImage, 'png', 15, 2)
        : pdfWithout.addImage(myImage2, 'png', 15, 2); // 2: 19
    let pdfName = company === '' ? text + " " + start + " - " + end + '.pdf'
        : text + " - " + company + " " + start + " - " + end + '.pdf';
    company === '' ? pdf.save(pdfName) : pdfWithout.save(pdfName)
}
