<?php

namespace Modules\Beneficiaries\Enums;

enum BeneficiaryType: string
{
    case NETWORK = 'network';
    case TANKER = 'tanker';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
