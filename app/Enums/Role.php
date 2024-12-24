<?php

namespace App\Enums;

use Illuminate\Support\Collection;

enum Role: string
{
    case ADMINISTRATOR = 'administrator';
    case OWNER = 'owner';
    case MANAGER = 'manager';
    case FRONT_DESK = 'front_desk';

    public static function getAdministratorRoles(): array
    {
        return [
            self::ADMINISTRATOR,
            self::OWNER,
            self::MANAGER,
            self::FRONT_DESK,
        ];
    }

    public static function getOwnerRoles(): array
    {
        return [
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

    public static function getRoles(Role $role): array
    {
        return match ($role) {
            self::ADMINISTRATOR => self::getAdministratorRoles(),
            self::OWNER => self::getOwnerRoles(),
            self::MANAGER => self::getManagerRoles(),
        };
    }

    public static function getRoleOptions(Role $role): Collection
    {
        return collect(self::getRoles($role))->mapWithKeys(fn ($role) => [$role->value => $role->getLabel()]);
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::ADMINISTRATOR => 'Administrator',
            self::OWNER => 'Owner',
            self::MANAGER => 'Manager',
            self::FRONT_DESK => 'Front Desk',
        };
    }
}
