<?php

require_once __DIR__.'/vendor/autoload.php';

use Modules\User\Enums\UserType;

echo "=== Teste Manual do Módulo User ===\n";

try {
    echo "1. Testando enum UserType...\n";

    echo '   - SuperAdmin value: '.UserType::SuperAdmin->value."\n";
    echo '   - SuperAdmin label: '.UserType::SuperAdmin->label()."\n";

    echo '   - Admin canCreateAdminUsers: '.(UserType::Admin->canCreateAdminUsers() ? 'true' : 'false')."\n";
    echo '   - Student canCreateAdminUsers: '.(UserType::Student->canCreateAdminUsers() ? 'true' : 'false')."\n";

    echo "2. Testando criação de enum a partir de valor...\n";
    $userType = UserType::from('admin');
    echo "   - UserType::from('admin'): ".$userType->label()."\n";

    echo "\n✅ Todos os testes manuais passaram!\n";

} catch (Exception $e) {
    echo "\n❌ Erro: ".$e->getMessage()."\n";
    echo "Stack trace:\n".$e->getTraceAsString()."\n";
}
