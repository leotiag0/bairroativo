Sistema Bairro Ativo - Mapeamento de Serviços Públicos e Privados

1. Requisitos:
- PHP 7.4+
- MySQL / MariaDB
- Conexão com internet para geolocalização (OpenCage API)

2. Configuração:
- Edite `conexao.php` com seus dados de acesso ao banco
- Crie o banco usando `sql/estrutura_banco.sql`
- Cadastre categorias básicas manualmente ou via CSV

3. Funcionalidades:
- Cadastro com geolocalização automática
- Múltiplas categorias por serviço
- Busca por nome, bairro, tipo, horário
- Localização do usuário no mapa
- Traçar rotas via Google Maps
- Importar serviços via CSV
- Painel de administração com login protegido

4. Acesso:
- Página inicial: `index.php`
- Mapa: `mapa.php`
- Admin: `admin_login.php`
  Usuário: `admin`, Senha: `senha123`
2923ef94f739425b96ec104bd6613eb5
