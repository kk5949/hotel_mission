<?php
namespace App\Enum;

use Spatie\Enum\Enum;

final class ReservationStatus extends Enum
{
    //10:진행중 20:예약완료 30:예약반려 40:예약취소
    const PROGRESSING = '10';
    const CANCELLED = '40';
    const CONFIRMED = '20';
    const REJECTED = '30';
}
