<?php

namespace Modules\Beneficiaries\Enums;

enum WaterQuotaStatus: string
{
    case ALLOCATED = 'allocated';
    case DELIVERED = 'delivered';
    case PENDING = 'pending';
    case CANCELLED = 'cancelled';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
