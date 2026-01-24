<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case COD = 'cod';
    case ONLINE = 'online';
}
