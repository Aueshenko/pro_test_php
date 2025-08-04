<?php

namespace App\Helper;

class FlashMessageHelper
{
    public function getStatusMessage(array $query): ?string
    {
        return match ($query['status'] ?? null) {
            'updated' => 'Товар успешно обновлён!',
            'deleted' => 'Товар успешно удалён!',
            'error' => 'Ошибка!',
            default => null,
        };
    }
}