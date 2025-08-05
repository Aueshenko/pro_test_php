<?php

namespace App\Helper;

class FlashMessageHelper
{
    public static function getStatusMessage(array $query): ?string
    {
        return match ($query['status'] ?? null) {
            'added' => 'Товар успешно добавлен!',
            'updated' => 'Товар успешно обновлён!',
            'deleted' => 'Товар успешно удалён!',
            'error' => 'Ошибка!',
            default => null,
        };
    }
}