<?php

namespace App\Scope;

use App\Entity\User;

class Scope
{
    public const USER_CREATE = 'USER_CREATE';
    public const USER_READ = 'USER_READ';
    public const USER_EDIT = 'USER_EDIT';
    public const USER_DELETE = 'USER_DELETE';

    public static function generate(User $user): array
    {
        // determine the user scopes based on user role

        return [
            self::USER_CREATE,
            self::USER_READ,
            self::USER_EDIT ,
            self::USER_DELETE,
        ];
    }

}