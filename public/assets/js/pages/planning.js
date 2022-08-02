$('.planning-datepicker').datepicker({
    format: "yyyy-mm-dd",
    autoclose: true,
    todayHighlight: true
});

$("#total_calls").text(calcTotal('.column-calls'));
$("#total_missed").text(calcTotal('.column-missed-calls'));
$("#total_bookings").text(calcTotal('.column-bookings'));
$("#total_chats").text(calcTotal('.column-chats'));
$("#avg_answer_time").text(calcTotal('.column-answer-time', true));
$("#avg_progress").text(calcTotal('.column-progress', true));
$("#avg_theory_count").text(calcTotal('.column-theory-count', true, true));
$("#avg_agents_count").text(calcTotal('.column-agents-count', true, true));
$("#avg_difference").text(calcTotal('.column-difference', true, true));

function calcTotal(className, getAverage = false, getDecimal = false) {
    let sum = 0;
    let existingCount = 0;
    let items = $(`${className}`);

    items.each(function () {
        let number = parseFloat($(this).text());
        sum += number;

        if (number !== 0) existingCount++;
    });

    if (getDecimal) {
        return Math.round(sum * 10 / existingCount) / 10;
    } else if (getAverage) {
        return Math.round(sum / existingCount);
    } else {
        return sum;
    }
}

//export via frontend with help html5 canvas

var pdf;
var myImage;
var imgWidth;
var imgHeight;

$('#export_pdf_planing').attr('data-html2canvas-ignore', 'true');
$("#planning-pdf-content").find('.shadow').addClass('importantNoShadowPdf');

var url = new URL(window.location.href);
var date = new Date();

getGeneralPDF($("#planning-pdf-content")[0], $("#planning-pdf-content"))

function generatePlanningPDF() {
    var twoDigitMonth = ((date.getMonth().length + 1) === 1) ? (date.getMonth() + 1) : '0' + (date.getMonth() + 1);
    let start = url.searchParams.get('start_date') !== null ? url.searchParams.get('start_date') + " - " : date.getFullYear() + "-" + (twoDigitMonth) + "-" + '01' + " - ";
    let end = url.searchParams.get('end_date') !== null ? url.searchParams.get('end_date') : date.getFullYear() + "-" + (twoDigitMonth) + "-" + date.getDate();
    console.log(start, end)
    pdf.addImage(myImage, 'png', 15, 2, imgWidth, imgHeight);
    let pdfName = $('.header-name').text() + " " + start + " - " + end + '.pdf';
    pdf.save(pdfName);
}


// export via backend domPdf
// $('#export_pdf_planing').click(function (event) {
//     let url = window.location.href;
//     url = new URL(url);
//     $('#export_start_date').val(url.searchParams.get('start_date'));
//     // $('#export_compare_date').val(url.searchParams.get('compare_date'));
//     // $('#export_start').val(url.searchParams.get('start_date'));
//     $('#export_end_date').val(url.searchParams.get('end_date'));
// // alert($('#export_start').val())
//     if (!url.searchParams.get('start_date') && !url.searchParams.get('end_date')) {
//         $('#export_date_range').val(false);
//     }
//
//     $('#export_form_planing').submit();
// });
