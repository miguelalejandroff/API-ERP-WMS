<?php

namespace App\Enums;

enum SaldoBodegaEnum: string
{
    case INCREMENT = 'increment';
    case DECREMENT = 'decrement';

    /**
     * Obtiene la descripción legible de la acción.
     *
     * @return string
     */
    public function description(): string
    {
        return match ($this) {
            self::INCREMENT => 'Incremento de saldo',
            self::DECREMENT => 'Decremento de saldo',
        };
    }

    /**
     * Verifica si un valor pertenece al enum.
     *
     * @param string $value
     * @return bool
     */
    public static function isValid(string $value): bool
    {
        return in_array($value, array_column(self::cases(), 'value'));
    }

    /**
     * Devuelve el signo asociado con la acción.
     *
     * @return int
     */
    public function sign(): int
    {
        return match ($this) {
            self::INCREMENT => 1,
            self::DECREMENT => -1,
        };
    }
}
