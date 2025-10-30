# WE Move Backend

Sistema backend para gerenciamento de transporte escolar.

## Recursos Implementados

### 🔐 Sistema ACL (Access Control List)

Sistema robusto de controle de acesso baseado em permissões e políticas do Laravel.

#### Tipos de Usuário e Permissões

| Tipo de Usuário | Permissões |
|----------------|------------|
| **Super Admin** | Todas as permissões (7) |
| **Admin** | 6 permissões (todas exceto criar super admins) |
| **Driver** | Sem permissões administrativas |
| **Student** | Sem permissões administrativas |

#### Permissões Disponíveis

- `view-users` - Visualizar usuários
- `create-users` - Criar usuários
- `update-users` - Atualizar usuários
- `delete-users` - Deletar usuários
- `update-user-type` - Atualizar tipo de usuário
- `create-admin-users` - Criar usuários Admin/Driver
- `create-super-admin` - Criar Super Administrador

#### Uso Rápido

```php
// Verificar permissão
$user->hasPermission(Permission::ViewUsers);

// Usar policy
if ($user->can('viewAny', User::class)) {
    // ...
}

// Middleware em rotas
Route::middleware(['permission:view-users'])->group(function () {
    // ...
});
```

#### Documentação Completa

- [ACL - Documentação Completa](docs/ACL.md)
- [ACL - Referência Rápida](docs/ACL-QuickReference.md)
- [ACL - Exemplos de Uso](docs/ACL-Examples.md)

## Instalação

```bash
# Instalar dependências
composer install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Executar migrações
php artisan migrate

# Executar seeders (opcional)
php artisan db:seed
```

## Testes

```bash
# Executar todos os testes
php artisan test

# Testes de módulos específicos
php artisan test --testsuite=Modules

# Testes de ACL
php artisan test --filter=Acl
php artisan test --filter=Permission
php artisan test --filter=UserPolicy
```

## Desenvolvimento

```bash
# Executar servidor de desenvolvimento
php artisan serve

# Executar com watch (requer npm)
composer dev

# Executar linter
./vendor/bin/pint

# Executar linter (apenas verificação)
./vendor/bin/pint --test
```

## Documentação da API

- [Documentação da API](docs/API_DOCUMENTATION.md)
- [Exemplos de Uso](docs/USAGE_EXAMPLES.md)

## Arquitetura

O projeto utiliza Laravel Modules para organização modular:

```
Modules/
├── Auth/          # Autenticação e autorização
├── User/          # Gerenciamento de usuários
└── ...
```

### Módulo User

- **Models**: User
- **Enums**: UserType, Permission
- **Policies**: UserPolicy
- **Middleware**: CheckPermission
- **Controllers**: UserController
- **Services**: UserService

## Tecnologias

- PHP 8.2+
- Laravel 12
- Laravel Sanctum (autenticação API)
- Laravel Modules
- PHPUnit (testes)
- Laravel Pint (linting)

## Contribuindo

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## Licença

Este projeto está sob a licença MIT.
