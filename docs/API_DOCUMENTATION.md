# API de Autenticação - We Move Backend

Este documento descreve as rotas disponíveis para autenticação na API usando Laravel Sanctum.

## Base URL
```
http://localhost:8000/api
```

## Rotas de Autenticação

### 1. Registro de Usuário
**POST** `/v1/auth/register`

Realiza o cadastro de um novo usuário e retorna um token de acesso.

**Corpo da Requisição:**
```json
{
    "name": "Nome do Usuário",
    "email": "usuario@exemplo.com",
    "password": "senha123456",
    "password_confirmation": "senha123456"
}
```

**Resposta de Sucesso (201):**
```json
{
    "message": "Usuário cadastrado com sucesso.",
    "data": {
        "user": {
            "id": 2,
            "name": "Nome do Usuário",
            "email": "usuario@exemplo.com",
            "email_verified_at": null,
            "created_at": "2025-09-26T05:15:00.000000Z",
            "updated_at": "2025-09-26T05:15:00.000000Z"
        },
        "token": "2|laravel_sanctum_token_here",
        "token_type": "Bearer"
    }
}
```

**Resposta de Validação (422):**
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "name": ["O campo nome é obrigatório."],
        "email": ["O email deve ter um formato válido.", "Este email já está sendo usado por outro usuário."],
        "password": ["O campo senha é obrigatório.", "A senha deve ter pelo menos 8 caracteres.", "A confirmação da senha não confere."]
    }
}
```

### 2. Login
**POST** `/v1/auth/login`

Realiza o login do usuário e retorna um token de acesso.

**Corpo da Requisição:**
```json
{
    "email": "teste@exemplo.com",
    "password": "senha123"
}
```

**Resposta de Sucesso (200):**
```json
{
    "message": "Login realizado com sucesso.",
    "data": {
        "user": {
            "id": 1,
            "name": "Usuário Teste",
            "email": "teste@exemplo.com",
            "email_verified_at": "2025-09-26T04:32:59.000000Z",
            "created_at": "2025-09-26T04:32:59.000000Z",
            "updated_at": "2025-09-26T04:32:59.000000Z"
        },
        "token": "1|laravel_sanctum_token_here",
        "token_type": "Bearer"
    }
}
```

**Resposta de Erro (401):**
```json
{
    "message": "Erro de autenticação.",
    "errors": ["Credenciais inválidas."]
}
```

**Resposta de Validação (422):**
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["O campo email é obrigatório."],
        "password": ["O campo senha é obrigatório."]
    }
}
```

### 3. Informações do Usuário Autenticado
**GET** `/v1/auth/me`

Retorna as informações do usuário autenticado.

**Headers Obrigatórios:**
```
Authorization: Bearer {token}
```

**Resposta de Sucesso (200):**
```json
{
    "data": {
        "id": 1,
        "name": "Usuário Teste",
        "email": "teste@exemplo.com",
        "email_verified_at": "2025-09-26T04:32:59.000000Z",
        "created_at": "2025-09-26T04:32:59.000000Z",
        "updated_at": "2025-09-26T04:32:59.000000Z"
    }
}
```

### 4. Logout
**POST** `/v1/auth/logout`

Realiza o logout do usuário, revogando o token atual.

**Headers Obrigatórios:**
```
Authorization: Bearer {token}
```

**Resposta de Sucesso (200):**
```json
{
    "message": "Logout realizado com sucesso."
}
```

### 5. Logout de Todos os Dispositivos
**POST** `/v1/auth/logout-all`

Realiza o logout de todos os dispositivos, revogando todos os tokens do usuário.

**Headers Obrigatórios:**
```
Authorization: Bearer {token}
```

**Resposta de Sucesso (200):**
```json
{
    "message": "Logout de todos os dispositivos realizado com sucesso."
}
```

## Usuários de Teste

O sistema inclui dois usuários pré-configurados para teste:

1. **Usuário Teste:**
   - Email: `teste@exemplo.com`
   - Senha: `senha123`

2. **Administrador:**
   - Email: `admin@exemplo.com`
   - Senha: `admin123`

## Estrutura do Projeto

O projeto segue uma arquitetura de monolito modular usando `nwidart/laravel-modules`:

```
Modules/
├── Auth/
    ├── app/
    │   ├── Classes/
    │   │   └── Services/
    │   │       └── AuthService.php
    │   ├── Http/
    │   │   ├── Controllers/
    │   │   │   └── AuthController.php
    │   │   └── Requests/
    │   │       └── LoginRequest.php
    │   └── Transformers/
    │       └── UserResource.php
    ├── routes/
    │   └── api.php
    └── tests/
```

## Configuração do Laravel Sanctum

O projeto utiliza Laravel Sanctum para autenticação de API com as seguintes configurações:

- **Guard**: `api` usando driver `sanctum`
- **Tokens**: Sem expiração por padrão
- **Middleware**: `auth:sanctum` para rotas protegidas

## Testes

O sistema inclui testes abrangentes que cobrem:

- ✅ Registro com dados válidos
- ✅ Registro com email já existente
- ✅ Validação de campos obrigatórios no registro
- ✅ Login com credenciais válidas
- ✅ Login com credenciais inválidas  
- ✅ Validação de campos obrigatórios no login
- ✅ Endpoint `/me` com usuário autenticado
- ✅ Endpoint `/me` sem autenticação
- ✅ Logout com usuário autenticado
- ✅ Logout sem autenticação
- ✅ Logout de todos os dispositivos

Para executar os testes:
```bash
./vendor/bin/sail test tests/Feature/AuthenticationTest.php
```