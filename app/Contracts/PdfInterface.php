<?php

namespace App\Contracts;

use Dompdf\Dompdf;

interface PdfInterface
{
    public function getPdf(): Dompdf;

    public function setParams($inDateRange = false, $start = null, $end = null, $startDate = null, $compareDate = null);

    public function setData($type = 'daily');

    public function savePdf();

    public function downloadPdf();

    public function getPath($type): string;
}
