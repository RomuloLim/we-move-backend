# We Move — Backend

Backend da aplicação **We Move**, um sistema de gerenciamento de transporte universitário. A plataforma conecta estudantes, motoristas e administradores, facilitando o cadastro de rotas, controle de viagens, embarque de passageiros e gerenciamento de solicitações de transporte.

## Objetivo

Oferecer uma API RESTful robusta para gerenciar todas as operações de transporte universitário: desde a solicitação de um estudante até o embarque na van/ônibus, passando pelo controle de rotas, paradas, veículos e comunicados institucionais.

## Tecnologias

- **PHP 8.2** + **Laravel 12**
- **PostgreSQL 17** — banco de dados relacional
- **MinIO** — armazenamento de arquivos compatível com S3
- **Laravel Sanctum** — autenticação via tokens de API
- **Laravel Sail** — ambiente de desenvolvimento baseado em Docker
- Arquitetura de **Monolito Modular** com [`nwidart/laravel-modules`](https://nwidart.com/laravel-modules)

## Módulos e Funcionalidades

| Módulo | Responsabilidade |
|---|---|
| **Auth** | Registro e login de usuários |
| **User** | Gerenciamento de usuários e controle de permissões por perfil |
| **Operation** | Instituições, cursos, estudantes e solicitações de transporte |
| **Logistics** | Veículos, rotas, paradas, viagens e embarques |
| **Communication** | Comunicados institucionais |

### Perfis de Usuário

| Perfil | Descrição |
|---|---|
| `super-admin` | Acesso total ao sistema |
| `admin` | Gerencia usuários, rotas, viagens, veículos e solicitações |
| `driver` | Visualiza suas rotas e gerencia o status das viagens |
| `student` | Solicita transporte e acompanha suas solicitações |

## Setup Local com Laravel Sail

### Pré-requisitos

- [Docker](https://www.docker.com/get-started) e Docker Compose instalados
- [PHP 8.2+](https://www.php.net/downloads) e [Composer](https://getcomposer.org/) instalados localmente (apenas para a instalação inicial)

### Passo a passo

**1. Clone o repositório**

```bash
git clone https://github.com/RomuloLim/we-move-backend.git
cd we-move-backend
```

**2. Instale as dependências PHP**

```bash
composer install
```

**3. Configure as variáveis de ambiente**

```bash
cp .env.example .env
```

> Ajuste as variáveis conforme necessário. As configurações padrão já são compatíveis com o ambiente Sail.

**4. Gere a chave da aplicação**

```bash
php artisan key:generate
```

**5. Suba os containers com o Sail**

```bash
./vendor/bin/sail up -d
```

> Os serviços disponíveis são: aplicação Laravel (porta `80`), PostgreSQL (porta `5432`) e MinIO (porta `9000`, console na `8900`).

**6. Execute as migrations e os seeders**

```bash
./vendor/bin/sail artisan migrate --seed
```

**7. Acesse a API**

A API estará disponível em: `http://localhost/api/v1/`

A documentação da API (Scramble) estará disponível em: `http://localhost/docs/api`

### Comandos úteis

```bash
# Parar os containers
./vendor/bin/sail down

# Executar os testes
./vendor/bin/sail artisan test

# Formatar o código
./vendor/bin/sail composer pint
```

## Licença

Este projeto está licenciado sob a [MIT License](https://opensource.org/licenses/MIT).
