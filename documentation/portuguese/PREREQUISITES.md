# Pre-requisitos e Desempenho

## Dependencias de Sistema

### Runtime

| Dependencia | Versao minima | Verificacao |
| --- | --- | --- |
| **PHP** | `>=8.2` | `php -v` |
| **Composer** | `>=2.x` | `composer --version` |
| **Node.js** | Recomendado `>=20 LTS` para Vite moderno | `node --version` |
| **npm** | Compativel com a versao do Node | `npm --version` |

Versoes observadas durante a geracao desta documentacao:

| Ferramenta | Versao observada |
| --- | --- |
| PHP | `8.3.6` |
| Composer | `2.7.1` |
| Laravel | `12.37.0` |
| Node.js | `24.14.0` |
| npm | `11.9.0` |

### Banco de Dados

| Banco | Status no projeto | Verificacao |
| --- | --- | --- |
| **SQLite** | Default em `.env.example` e em `phpunit.xml`. | `php -m | grep sqlite` |
| **PostgreSQL** | Suportado por `config/database.php` usando `DB_CONNECTION=pgsql`. | `psql --version` |
| **MySQL/MariaDB/SQL Server** | Conexoes padrao do Laravel mantidas em `config/database.php`. | Cliente correspondente |

Para desenvolvimento rapido, SQLite e suficiente para rotas e testes basicos. Para ambientes compartilhados ou producao, use PostgreSQL ou outro banco relacional suportado e mantenha migrations alinhadas aos models.

### Extensoes PHP Relevantes

| Extensao | Motivo |
| --- | --- |
| `pdo` | Acesso a banco de dados via Laravel. |
| `pdo_sqlite` | Necessario para SQLite local e testes em memoria. |
| `pdo_pgsql` | Necessario se `DB_CONNECTION=pgsql`. |
| `mbstring` | Dependencia comum do Laravel e Symfony. |
| `openssl` | Criptografia, chaves e operacoes seguras. |
| `fileinfo` | Validacao e manipulacao de arquivos. |

---

## Dependencias do Projeto

### PHP — Dependencias Diretas

| Pacote | Versao instalada | Categoria |
| --- | --- | --- |
| `laravel/framework` | `v12.37.0` | Framework principal |
| `laravel/tinker` | `v2.10.1` | REPL |
| `fakerphp/faker` | `v1.24.1` | Dados falsos para testes/factories |
| `laravel/pail` | `v1.2.3` | Logs em desenvolvimento |
| `laravel/pint` | `v1.25.1` | Formatacao |
| `laravel/sail` | `v1.47.0` | Docker opcional |
| `mockery/mockery` | `1.6.12` | Test doubles |
| `nunomaduro/collision` | `v8.8.2` | Erros CLI |
| `phpunit/phpunit` | `11.5.43` | Testes |

### JavaScript — Dependencias Diretas

| Pacote | Versao instalada | Categoria |
| --- | --- | --- |
| `vite` | `7.3.2` | Build/dev server |
| `laravel-vite-plugin` | `2.1.0` | Integracao Laravel/Vite |
| `tailwindcss` | `4.2.4` | CSS utilitario |
| `@tailwindcss/vite` | `4.2.4` | Plugin Tailwind |
| `axios` | `1.15.2` | Cliente HTTP |
| `concurrently` | `9.2.1` | Execucao paralela de processos |

---

## Hardware Sugerido

### Desenvolvimento Local

| Recurso | Minimo | Recomendado |
| --- | --- | --- |
| **RAM** | 4 GB | 8 GB |
| **CPU** | 2 cores | 4 cores |
| **Disco** | 2 GB livres sem `vendor`/`node_modules`; 5 GB com dependencias | 10 GB |
| **SO** | Linux, macOS ou Windows com WSL2 | Ubuntu 22.04+ |

### Servidor de Producao

| Recurso | Minimo | Recomendado |
| --- | --- | --- |
| **RAM** | 1 GB para API pequena | 2 GB+ |
| **CPU** | 1 vCPU | 2 vCPU |
| **Disco** | 10 GB SSD | 20 GB SSD+ |
| **SO** | Ubuntu 22.04 LTS | Ubuntu 24.04 LTS |

### Portas Utilizadas

| Porta | Servico | Ambiente |
| --- | --- | --- |
| `8000` | `php artisan serve` | Desenvolvimento |
| `5173` | Vite dev server | Desenvolvimento |
| `5432` | PostgreSQL, quando usado | Desenvolvimento/producao |
| `80` | HTTP via Nginx/Apache | Producao |
| `443` | HTTPS via Nginx/Apache | Producao |

---

## Requisitos de Ambiente

Antes de executar a aplicacao:

1. Instale dependencias PHP com Composer.
2. Instale dependencias JS com npm.
3. Copie `.env.example` para `.env`.
4. Gere `APP_KEY`.
5. Configure `DB_CONNECTION` e credenciais do banco.
6. Rode migrations.

Para testes automatizados, `phpunit.xml` ja define SQLite em memoria:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

Esse banco em memoria e adequado para testes rapidos, mas depende de migrations coerentes com os models usados nos testes.
