<?php

namespace App\Contracts;

interface StatisticServiceInterface
{
    public function getDailyStats($settings): array;

    public function getUserStats($settings);

    public function getMedianValues($dailyStats, $settings);
}
