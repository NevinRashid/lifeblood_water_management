<?php

namespace Modules\Beneficiaries\Enums;

enum BeneficiaryStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
    case RELOCATED = 'relocated';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
