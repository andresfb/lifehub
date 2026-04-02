<?php

declare(strict_types=1);

namespace App\Enums;

enum ModuleAccessLevel: string
{
    case NONE = 'none';
    case ADMIN = 'admin';
    case READ = 'read';
    case WRITE = 'write';

    public static function fromString(string $value): self
    {
        return self::from($value);
    }

    public function rank(): int
    {
        return match ($this) {
            self::NONE => 0,
            self::READ => 1,
            self::WRITE => 2,
            self::ADMIN => 3,
        };
    }

    public function allows(self $required): bool
    {
        return $this->rank() >= $required->rank();
    }
}
