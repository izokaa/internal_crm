<?php

namespace App\Traits;

trait HasActiveIcon
{
    public static function getActiveNavigationIcon(): ?string
    {
        return self::$navigationActiveIcon;
    }
}
