$('.employee-datepicker').datepicker({
    format: "yyyy-mm-dd",
    autoclose: true,
    todayHighlight: true
});

var pdf;
var myImage;
var imgWidth;
var imgHeight;
var url = new URL(window.location.href)
var date = new Date();
var twoDigitMonth = ((date.getMonth().length + 1) === 1) ? (date.getMonth() + 1) : '0' + (date.getMonth() + 1);
var start = url.searchParams.get('start_date') !== null ? url.searchParams.get('start_date') + " - " : date.getFullYear() + "-" + (twoDigitMonth) + "-" + '01' + " - ";
var end = url.searchParams.get('end_date') !== null ? url.searchParams.get('end_date') : date.getFullYear() + "-" + (twoDigitMonth) + "-" + date.getDate();

function getPDF() {
    html2canvas($("#cards-block")[0], {scale: 1}).then((canvas) => {
        console.log("done ... ");
        myImage = canvas.toDataURL("pdf,1.0");
        // Adjust width and height
        // imgWidth = (canvas.width * 60) / 220;
        // imgHeight = (canvas.height * 65) / 220;
        // jspdf changes
        pdf = new jsPDF('p', 'mm', 'a2');
    });
}

getPDF();

function generateEmployeePDF() {
    pdf.addImage(myImage, 'png', 15, 2, imgWidth, imgHeight); // 2: 19
    let headerText = $('.header-name').text();
    let pdfName = headerText.includes(':')
        ? $('.header-name').text() + " " + start + end + '.pdf'
        : "Statistics_" + $('.userName').text().trim() + " " + start + end + '.pdf';
    pdf.save(pdfName);
}
