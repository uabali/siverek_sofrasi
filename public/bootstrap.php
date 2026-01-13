<?php

declare(strict_types=1);

// Çok basit autoloader (Composer yok)

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
    $file = __DIR__ . '/../app/' . $relativePath;
    if (is_file($file)) {
        require $file;
    }
});
