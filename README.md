# 🧪 ERP Estoque API – Backend

API REST do sistema ERP de estoque desenvolvida em **Laravel**, responsável pelo gerenciamento de:

- Produtos
- Compras
- Vendas
- Controle de estoque
- Cálculo de custo médio ponderado
- Cálculo de lucro
- Histórico operacional

---

# 🏗️ Arquitetura

O backend foi desenvolvido seguindo arquitetura desacoplada via API REST.

```txt
Frontend (Vue)
↓ HTTP REST
Laravel API
↓
MySQL
```

Estrutura principal:

```txt
app/
├── Http/
│   ├── Controllers/
│   └── Requests/
│
├── Models/
│
└── Services/
```

---

# 🚀 Tecnologias

- Laravel
- PHP 8.3
- MySQL 8
- Docker
- Docker Compose
- Eloquent ORM

---

# ⚙️ Como Rodar

## 1. Subir containers

```bash
docker compose up -d --build
```

---

## 2. Entrar no container backend

```bash
docker exec -it erp_backend bash
```

---

## 3. Instalar dependências

```bash
composer install
```

---

## 4. Executar migrations

```bash
php artisan migrate
```

---

# 🌐 API Base URL

```txt
http://localhost:8000/api
```

---

# 📡 Endpoints

## Produtos

### Listar produtos

```http
GET /api/produtos
```

### Criar produto

```http
POST /api/produtos
```

Payload:

```json
{
    "nome": "Mouse Gamer",
    "preco_venda": 150
}
```

---

## Compras

### Listar compras

```http
GET /api/compras
```

### Registrar compra

```http
POST /api/compras
```

Payload:

```json
{
    "fornecedor": "Fornecedor XPTO",
    "produtos": [
        {
            "id": 1,
            "quantidade": 10,
            "preco_unitario": 20
        }
    ]
}
```

---

## Vendas

### Listar vendas

```http
GET /api/vendas
```

### Registrar venda

```http
POST /api/vendas
```

Payload:

```json
{
    "cliente": "Fulano da Silva",
    "produtos": [
        {
            "id": 1,
            "quantidade": 2,
            "preco_unitario": 50
        }
    ]
}
```

---

# 🧠 Regras de Negócio

## Produtos

- Nome obrigatório com mínimo de 3 caracteres
- Preço de venda obrigatório e positivo
- Estoque inicial igual a `0`
- Custo médio inicial igual a `0`

---

## Compras

Ao registrar uma compra:

- Atualiza estoque
- Recalcula custo médio ponderado
- Salva histórico da compra
- Executa transaction para garantir consistência

### Fórmula do custo médio

```txt
((estoque atual × custo atual)
+
(nova quantidade × novo custo))
/
(estoque atual + nova quantidade)
```

---

## Vendas

Ao registrar uma venda:

- Valida estoque suficiente
- Baixa estoque
- Calcula receita
- Calcula lucro
- Salva histórico da venda
- Utiliza rollback automático em falhas

### Fórmula do lucro

```txt
(receita da venda)
-
(custo dos produtos vendidos)
```

---

# 🔒 Consistência

As operações de compra e venda utilizam:

```txt
Database Transactions
```

Garantindo:

- Integridade dos dados
- Rollback automático
- Consistência do estoque

---

# 📌 Decisões Técnicas

- Arquitetura desacoplada via API REST
- Service Layer para regras de negócio
- Form Requests para validações
- Transactions para consistência
- Docker para padronização do ambiente

---

# 👨‍💻 Autor

Desenvolvido por **Rangel Dheo**.
