<?php

namespace App\Contracts;

interface LogInterface
{
    public function actionArrayInfo(string $key, array $data);

    public function actionStringInfo(string $key, string $data);

}
