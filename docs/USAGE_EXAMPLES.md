# Exemplos de Uso da API de Autenticação

## 1. Registrar um Novo Usuário

```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "João Silva",
    "email": "joao.silva@exemplo.com",
    "password": "minhasenha123",
    "password_confirmation": "minhasenha123"
  }'
```

**Resposta:**
```json
{
  "message": "Usuário cadastrado com sucesso.",
  "data": {
    "user": {
      "id": 3,
      "name": "João Silva",
      "email": "joao.silva@exemplo.com",
      "email_verified_at": null,
      "created_at": "2025-09-26T05:30:00.000000Z",
      "updated_at": "2025-09-26T05:30:00.000000Z"
    },
    "token": "3|abcdef1234567890",
    "token_type": "Bearer"
  }
}
```

## 2. Fazer Login

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "joao.silva@exemplo.com",
    "password": "minhasenha123"
  }'
```

## 3. Acessar Perfil do Usuário

```bash
# Salve o token da resposta anterior
TOKEN="3|abcdef1234567890"

curl -X GET http://localhost:8000/api/v1/auth/me \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

## 4. Fazer Logout

```bash
curl -X POST http://localhost:8000/api/v1/auth/logout \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

## Fluxo Completo de Autenticação

### Cenário: Novo usuário se cadastra e usa a aplicação

1. **Registro:**
   - O usuário envia seus dados para `/register`
   - Recebe um token de acesso imediatamente

2. **Uso da aplicação:**
   - Utiliza o token em todas as requisições autenticadas
   - Pode consultar seu perfil com `/me`

3. **Logout:**
   - Faz logout com `/logout` (remove apenas o token atual)
   - Ou `/logout-all` (remove todos os tokens do usuário)

### Cenário: Usuário existente faz login

1. **Login:**
   - Envia email e senha para `/login`
   - Recebe um novo token de acesso

2. **Resto do fluxo é igual ao anterior**

## Tratamento de Erros

### Email já cadastrado:
```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Outro Usuário",
    "email": "teste@exemplo.com",
    "password": "senha123456",
    "password_confirmation": "senha123456"
  }'
```

**Resposta (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["Este email já está sendo usado por outro usuário."]
  }
}
```

### Senha muito curta:
```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Usuário Teste",
    "email": "usuario@exemplo.com",
    "password": "123",
    "password_confirmation": "123"
  }'
```

**Resposta (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "password": ["A senha deve ter pelo menos 8 caracteres."]
  }
}
```

### Senhas não conferem:
```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Usuário Teste",
    "email": "usuario@exemplo.com",
    "password": "senha123456",
    "password_confirmation": "outrasenha"
  }'
```

**Resposta (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "password": ["A confirmação da senha não confere."]
  }
}
```
