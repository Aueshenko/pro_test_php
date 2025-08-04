<?php

namespace App\Helper;

class FlashMessageHelper
{
    public function getStatusMessage(array $query): ?string
    {
        return match ($query['status'] ?? null) {
            'deleted' => 'Товар успешно удалён!',
            'error' => 'Ошибка при удалении товара.',
            default => null,
        };
    }
}