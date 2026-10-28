# Proposta Services
**Plataforma SaaS para criação e gestão de orçamentos/propostas comerciais.**

---

## 📁 Estrutura do Projeto (MVC)

```
proposta-services/
├── app/
│   ├── controllers/
│   │   ├── AuthController.php         # Login, registro, logout
│   │   ├── DashboardController.php    # Painel gerencial
│   │   ├── ClientController.php       # CRUD de clientes
│   │   ├── ProductController.php      # CRUD de produtos/serviços
│   │   ├── ProposalController.php     # CRUD de propostas + itens + público
│   │   └── SettingsController.php     # Configurações do usuário
│   ├── models/
│   │   ├── UserModel.php              # Usuários/tenants
│   │   ├── ClientModel.php            # Clientes
│   │   ├── ProductModel.php           # Produtos e serviços
│   │   ├── ProposalModel.php          # Propostas
│   │   └── ProposalItemModel.php      # Itens das propostas
│   └── views/
│       ├── menu.php                   # Sidebar (incluído em todas as páginas)
│       ├── layout_header.php          # Cabeçalho do layout autenticado
│       ├── layout_footer.php          # Rodapé do layout autenticado
│       ├── auth/
│       │   ├── login.php
│       │   └── register.php
│       ├── dashboard/
│       │   └── index.php
│       ├── clients/
│       │   ├── index.php
│       │   └── form.php
│       ├── products/
│       │   ├── index.php
│       │   └── form.php
│       ├── proposals/
│       │   ├── index.php
│       │   └── form.php
│       ├── settings/
│       │   └── index.php
│       └── public/
│           ├── proposal.php           # Visualização pública da proposta
│           └── 404.php
├── config/
│   ├── app.php                        # Bootstrap, helpers, sessão
│   └── database.php                   # Conexão PDO MySQL
├── database/
│   └── schema.sql                     # Schema completo do banco
└── public/                            # Document root do servidor web
    ├── index.php
    ├── login.php
    ├── register.php
    ├── logout.php
    ├── dashboard.php
    ├── clients.php
    ├── products.php
    ├── proposals.php
    ├── settings.php
    ├── .htaccess                      # URL rewriting
    ├── proposta/
    │   └── index.php                  # Handler de proposta pública
    ├── css/
    │   └── app.css
    └── js/
        └── app.js
```

---

## 🚀 Instalação

### 1. Requisitos
- PHP 8.1+
- MySQL 8.0+
- Apache com `mod_rewrite` habilitado (ou Nginx)

### 2. Banco de dados
```sql
-- Execute o arquivo schema.sql no MySQL:
mysql -u root -p < database/schema.sql
```

### 3. Configuração
Edite `config/database.php` com suas credenciais:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'proposta_services');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
```

Edite `config/app.php` com a URL do seu projeto:
```php
define('APP_URL', 'http://localhost/proposta-services/public');
```

### 4. Servidor web
Configure o **Document Root** apontando para a pasta `public/`.

**Apache** — Certifique-se que `AllowOverride All` está ativo para que o `.htaccess` funcione.

**Nginx** — Adicione regra de rewrite:
```nginx
location /proposta/ {
    rewrite ^/proposta/([a-zA-Z0-9]+)$ /proposta/index.php?token=$1 last;
}
```

### 5. Permissões
```bash
chmod 755 public/
```

---

## 🔗 Links Públicos de Propostas
Cada proposta gera um token único. O link público fica no formato:
```
https://seudominio.com/proposta/{token}
```
Qualquer pessoa pode acessar este link para visualizar e aprovar/reprovar a proposta.

---

## 💬 Variáveis do WhatsApp
Configure em **Configurações > Mensagem Padrão**:

| Variável | Substituição |
|---|---|
| `#PROPOSTA#` | Número da proposta |
| `#TITULO#` | Título da proposta |
| `#CLIENTE#` | Nome do cliente |
| `#VALOR#` | Valor total formatado |
| `#LINK#` | Link público da proposta |

---

## 🔐 Segurança
- Senhas hasheadas com `password_hash()` (bcrypt)
- CSRF token em todos os formulários POST
- Multi-tenancy: cada query filtra por `user_id`
- Sessões nativas PHP com `httponly` habilitado
- Inputs sanitizados com `htmlspecialchars()`

---

### 6. Clonando o projeto
```bash
git clone https://github.com
cd proposta-services
```

----

## 🛣️ Roadmap Futuro
- [ ] Pagamentos (Stripe/PagSeguro/Mercado Pago)
- [ ] Notificações por e-mail
- [ ] Integração WhatsApp Business API
- [ ] Assinatura digital
- [ ] PDF da proposta para download
- [ ] Planos e faturamento SaaS
