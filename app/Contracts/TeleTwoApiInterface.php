<?php

namespace App\Contracts;

interface TeleTwoApiInterface
{
    public function getApiData($params = '', $middlePart = '', $queryParams = '');
}
