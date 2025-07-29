<?php

namespace Modules\ActivityLog\Enums;

enum LogEvent: string
{
    case LOGIN = 'login';
    case FAILED_LOGIN = 'failed-login';
    case REGISTERD = 'registerd';
    case LOGOUT = 'logout';
    case VEREFICATION_EMAIL = 'verefication-email';
    case VERIFIED_EMAIL = 'verified-email';
    case RESET_LINK_SENT = 'reset-link-sent';
    case RESET_LINK_FAILED = 'reset-link-failed';
    case PASSWORD_RESET = 'password-reset';
    case PASSWORD_RESET_FAILED = 'password-reset-failed';
    case FORBIDDEN = 'forbidden';
    case THROTTLE = 'throttle';
    case MODEL_NOT_FOUND = 'model-not-found';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
