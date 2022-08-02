<?php

if (!function_exists('get_random_string')) {
    function get_random_string($length = 9): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('get_hour_format')) {
    function get_hour_format($seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds / 60) % 60);

        return sprintf("%02d:%02d", $hours, $minutes);
    }
}

if (!function_exists('get_median_value')) {
    function get_median_value($points, $workTimeInSeconds, $mainPoint): string
    {
        return round(($points * 100)
            / (max($workTimeInSeconds, 1) * $mainPoint / 3600));
    }
}

if (!function_exists('get_working_hours')) {
    function get_working_hours(): array
    {
        $hours = [];
        foreach (range(7, 17) as $time) {
            $key = strlen($time) === 1 ? "0$time:00" : "$time:00";
            $hour = strlen($time) === 1 && $time !== 9 ? "0" . ($time + 1) . ":00" : ($time + 1) . ":00";
            $hours[$key] = $hour;
        }

        return $hours;
    }
}

if (!function_exists('sort_by_calls')) {
    function sort_by_calls($a, $b)
    {
        if ($a['daily_calls'] == $b['daily_calls']) {
            return 0;
        }
        return ($a['daily_calls'] > $b['daily_calls']) ? -1 : 1;
    }
}

if (!function_exists('replace_unicode_escape_sequence')) {
    function replace_unicode_escape_sequence($match)
    {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
    }
}

if (!function_exists('unicode_decode')) {
    function unicode_decode($str)
    {
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $str);
    }
}
if (!function_exists('multiple_replace')) {
    function multiple_replace_youtube($arr, $str)
    {
        foreach ($arr as $key => $value) {
            $str = str_replace($key, $value, $str);
        }

        return stripos($str, '&') !== false ? explode('&', $str)[0] : $str;

    }
}
if (!function_exists('sum_arrays')) {
    function sum_arrays($array1, $array2)
    {//todo:optimize, find better solution
        $res = [];
        foreach ($array1 as $index => $value) {
            $res[$index] = isset($array2[$index]) ? $array2[$index] + $value : $value;
        }
        foreach ($array2 as $index => $value) {
            if (!isset($res[$index])) {
                $res[$index] = $value;
            }
        }
        return $res;
    }
}
