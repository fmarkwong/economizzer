<?php

namespace app\helpers;

/**
 * Class cashBookHelper
 * @author yourname
 */
class CashBookHelper
{
    public static function color($value)
    {
        $green = "#18bc9c";
        $red = "#e74c3c";
        $color = $value >= 0 ? $green : $red;

        if ($value == 0) $color = "#000000";
        return $color;
    }

    public static function balanceColor($value)
    {
        return self::color($value);
    }
    
}
