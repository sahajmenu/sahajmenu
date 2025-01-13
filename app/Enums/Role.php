<?php

namespace App\Enums;

use Illuminate\Support\Collection;

enum Role: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case OWNER = 'owner';
    case MANAGER = 'manager';
    case FRONT_DESK = 'front_desk';

    public static function getSuperAdminRoles(): array
    {
        return [
            self::SUPER_ADMIN,
            self::ADMIN,
            self::OWNER,
            self::MANAGER,
            self::FRONT_DESK,
        ];
    }

    public static function getAllClientRoles(): array
    {
        return [
            self::OWNER,
            self::MANAGER,
            self::FRONT_DESK,
        ];
    }

    public static function getManagerRoles(): array
    {
        return [
            self::FRONT_DESK,
        ];
    }

    public static function getClientRoles(Role $role): array
    {
        return match ($role) {
            self::SUPER_ADMIN, self::ADMIN, self::OWNER => self::getAllClientRoles(),
            self::MANAGER => self::getManagerRoles(),
        };
    }

    public static function getUserRoles(Role $role): array
    {
        return match ($role) {
            self::SUPER_ADMIN => self::getSuperAdminRoles(),
            self::ADMIN, self::OWNER => self::getAllClientRoles(),
            self::MANAGER => self::getManagerRoles(),
        };
    }

    public static function getClientRoleOptions(Role $role): Collection
    {
        return collect(self::getClientRoles($role))->mapWithKeys(fn ($role) => [$role->value => $role->getLabel()]);
    }

    public static function getUserRoleOptions(Role $role): Collection
    {
        return collect(self::getUserRoles($role))->mapWithKeys(fn ($role) => [$role->value => $role->getLabel()]);
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN => 'Admin',
            self::OWNER => 'Owner',
            self::MANAGER => 'Manager',
            self::FRONT_DESK => 'Front Desk',
        };
    }
}
