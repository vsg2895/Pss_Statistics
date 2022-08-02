<?php

namespace App\Contracts;

interface ExcelInterface
{
    public function getDataFromExport();

    public function getDataFromImport($company, $start, $end);

    public function updateViaImport($company, $start, $end, $data, $oldValuesFee, $updated, $chat_fee, $p_chat_fee);

    public function delBillingDataByIds($delIds): void;

    public function ImportData($company, $file);

    public function ExportData();

    public function getPath($type): string;

}
