<?php

namespace App\Contracts;

interface LiveagentApiInterface
{
    public function getData(string $endpoint, string $filters = '');
}
