<?php

namespace App\Utils;

class Permissions extends BitField
{
    public static array $flags = [
        'ADMINISTRATOR' => 1 << 0,

        'VIEW_CLIENTS' => 1 << 1,
        'EDIT_CLIENTS' => 1 << 2,
        'CREATE_CLIENTS' => 1 << 3,
        'DELETE_CLIENTS' => 1 << 4,

        'VIEW_INVOICES' => 1 << 5,
        'EDIT_INVOICES' => 1 << 6,
        'CREATE_INVOICES' => 1 << 7,
        'DELETE_INVOICES' => 1 << 8,

        'VIEW_ORDERS' => 1 << 9,
        'EDIT_ORDERS' => 1 << 10,
        'CREATE_ORDERS' => 1 << 11,
        'DELETE_ORDERS' => 1 << 12,

        'VIEW_PRODUCTS' => 1 << 13,
        'EDIT_PRODUCTS' => 1 << 14,
        'CREATE_PRODUCTS' => 1 << 15,
        'DELETE_PRODUCTS' => 1 << 16,

        'VIEW_CATEGORIES' => 1 << 17,
        'EDIT_CATEGORIES' => 1 << 18,
        'CREATE_CATEGORIES' => 1 << 19,
        'DELETE_CATEGORIES' => 1 << 20,

        'VIEW_TICKETS' => 1 << 21,
        'EDIT_TICKETS' => 1 << 22,
        'CREATE_TICKETS' => 1 << 23,
        'DELETE_TICKETS' => 1 << 24,

        'VIEW_SETTINGS' => 1 << 25,
        'EDIT_SETTINGS' => 1 << 26,

        'VIEW_EXTENSIONS' => 1 << 27,
        'EDIT_EXTENSIONS' => 1 << 28,

        'VIEW_COUPONS' => 1 << 29,
        'EDIT_COUPONS' => 1 << 30,
        'CREATE_COUPONS' => 1 << 31,
        'DELETE_COUPONS' => 1 << 32,

        'VIEW_ANNOUNCEMENTS' => 1 << 33,
        'EDIT_ANNOUNCEMENTS' => 1 << 34,
        'CREATE_ANNOUNCEMENTS' => 1 << 35,
        'DELETE_ANNOUNCEMENTS' => 1 << 36,

        'VIEW_ROLES' => 1 << 37,
        'EDIT_ROLES' => 1 << 38,
        'CREATE_ROLES' => 1 << 39,
        'DELETE_ROLES' => 1 << 40,

        'VIEW_CONFIGURABLE_OPTIONS' => 1 << 41,
        'EDIT_CONFIGURABLE_OPTIONS' => 1 << 42,
        'CREATE_CONFIGURABLE_OPTIONS' => 1 << 43,
        'DELETE_CONFIGURABLE_OPTIONS' => 1 << 44,

        'VIEW_EMAIL' => 1 << 45,
        'EDIT_EMAIL' => 1 << 46,

        'VIEW_TAXES' => 1 << 47,
        'EDIT_TAXES' => 1 << 48,
        'CREATE_TAXES' => 1 << 49,

        'VIEW_LOGS' => 1 << 50,
    ];

    public function __construct(int $bits)
    {
        parent::__construct($bits);
    }

    /**
     * Check if permission flag equals bit-field
     *
     * @param string $permission
     * @return bool
     */
    public function has(string $permission): bool
    {
        return $this->hasBit(self::$flags[$permission]);
    }

    /**
     * Check available permissions with bit-field
     *
     * @return array
     */
    public function available(): array
    {
        $available = [];
        foreach (self::$flags as $key => $flag) {
            if ($this->hasBit($flag))
                $available[] = $key;
        }

        return $available;
    }

    public static function create(array $permissions): int
    {
        $value = 0;
        foreach ($permissions as $permission) {
            $value += (self::$flags[$permission]);
        }

        return $value;
    }
}
