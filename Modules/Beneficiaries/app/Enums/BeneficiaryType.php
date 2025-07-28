<?php

namespace Modules\Beneficiaries\Enums;

enum BeneficiaryType: string
{
    case NETWORK = 'network';
    case TANKER = 'tanker';
    case OTHER = 'other';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
