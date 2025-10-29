<?php

require_once __DIR__.'/vendor/autoload.php';

echo "=== Teste de Classes do Módulo User ===\n";

try {
    echo "1. Verificando UserResource...\n";
    if (class_exists('Modules\\User\\Http\\Resources\\UserResource')) {
        echo "   ✅ UserResource encontrada\n";
    } else {
        echo "   ❌ UserResource NÃO encontrada\n";
    }

    echo "2. Verificando UserService...\n";
    if (class_exists('Modules\\User\\Services\\UserService')) {
        echo "   ✅ UserService encontrada\n";
    } else {
        echo "   ❌ UserService NÃO encontrada\n";
    }

    echo "3. Verificando UserType enum...\n";
    if (enum_exists('Modules\\User\\Enums\\UserType')) {
        echo "   ✅ UserType enum encontrada\n";
    } else {
        echo "   ❌ UserType enum NÃO encontrada\n";
    }

    echo "4. Verificando CreateUserRequest...\n";
    if (class_exists('Modules\\User\\Http\\Requests\\CreateUserRequest')) {
        echo "   ✅ CreateUserRequest encontrada\n";
    } else {
        echo "   ❌ CreateUserRequest NÃO encontrada\n";
    }

    echo "\n=== Resultado ===\n";
    echo "Classes do módulo User verificadas!\n";

} catch (Exception $e) {
    echo '❌ Erro: '.$e->getMessage()."\n";
}
