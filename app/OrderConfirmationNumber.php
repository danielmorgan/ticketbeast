<?php

namespace App;

class OrderConfirmationNumber
{
    const LENGTH = 16;

    public function generate()
    {
        $pool = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, self::LENGTH)), 0, self::LENGTH);
    }
}
