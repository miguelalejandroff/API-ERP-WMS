<?php

namespace App\Enums;

enum PedidosEnum: string
{
    case AUTORIZADO = 'A';
    case EN_MATRIZ = 'M';
    case NO_AUTORIZADO = 'N';
    case POR_DESPACHAR = 'C';
    case DESPACHADO = 'D';

    /**
     * Obtener la descripción legible para cada estado.
     *
     * @return string
     */
    public function description(): string
    {
        return match ($this) {
            self::AUTORIZADO => 'Autorizado',
            self::EN_MATRIZ => 'En Matriz',
            self::NO_AUTORIZADO => 'No Autorizado',
            self::POR_DESPACHAR => 'Por Despachar',
            self::DESPACHADO => 'Despachado',
        };
    }

    /**
     * Obtener una lista de todas las claves y valores del enum.
     *
     * @return array
     */
    public static function toArray(): array
    {
        return array_combine(
            array_map(fn($case) => $case->name, self::cases()),
            array_map(fn($case) => $case->value, self::cases())
        );
    }

    /**
     * Verificar si un valor pertenece al enum.
     *
     * @param string $value
     * @return bool
     */
    public static function isValid(string $value): bool
    {
        return in_array($value, array_column(self::cases(), 'value'));
    }
}
