<?php

namespace App\Controller;

class BaseController
{
    protected function render(string $view, array $data = [], bool $isXml = false): void
    {
        extract($data);
        $templatePath = __DIR__ . '/../../templates/' . $view . '.phtml';

        if (!file_exists($templatePath)) {
            http_response_code(500);
            echo "Шаблон не найден: $templatePath";
            exit;
        }

        if ($isXml) {
            require $templatePath;
            exit;
        }

        $content = $templatePath;
        require __DIR__ . '/../../templates/admin_layout.phtml';
    }

    protected function redirect(string $path, array $params = [], int $statusCode = 302): void
    {
        if (!empty($params)) {
            $safeParams = [];
            foreach ($params as $key => $value) {
                $safeParams[$key] = is_string($value)
                    ? rawurlencode($value)
                    : $value;
            }
            $query = http_build_query($safeParams);
            $path .= (str_contains($path, '?') ? '&' : '?') . $query;
        }

        header("Location: $path", true, $statusCode);
        exit;
    }
}