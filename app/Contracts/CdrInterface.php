<?php

namespace App\Contracts;

interface CdrInterface
{
    public function setParams($company = [], $type = 'company', $start = null, $end = null);

    public function setBillingFee();

    public function getMoreData($page);

    public function getFixedFee($obj);

    public function getFixedForAll();

}
