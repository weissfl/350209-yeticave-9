<?php

function format_price(float $number): string
{
    $number = ceil($number);

    if ($number >= 1000) {
        $number = number_format($number, 0, '', ' ');
    }

    $number .= '<b class="rub">р</b>';

    return $number;
}

function warning_finishing($time_end){
    $time_diff = strtotime($time_end) - time();

    if($time_diff <= 3600){
        return true;
    }
    else {
        return false;
    }
}

function lifetime_lot($time_end){
    $time_diff = strtotime($time_end) - time();

    $hours = floor($time_diff / 3600);
    $minutes = floor(($time_diff % 3600) / 60);

    $time = $hours.':'.$minutes;

    return $time;
}