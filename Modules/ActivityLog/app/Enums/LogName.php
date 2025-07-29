<?php

namespace Modules\ActivityLog\Enums;

enum LogName: string
{
    case UNVERIFIED_EMAIL = 'unverified-email';
    case AUTH = 'auth';
    case SECURITY = 'security';
    case FAILED_VALIDATION = 'failed-validation';
    case MODEL = 'model';
    case DEFAULT = 'default';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
