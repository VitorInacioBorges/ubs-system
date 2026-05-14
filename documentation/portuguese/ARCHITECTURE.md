# Arquitetura do Projeto

## Justificativa da Arquitetura

O projeto adota uma arquitetura Laravel em camadas, organizada em **Controllers**, **Services**, **Repositories** e **Eloquent Models**. Essa separacao reduz o acoplamento entre HTTP, regras de aplicacao e persistencia sem abandonar os recursos nativos do framework.

Essa escolha resolve tres necessidades centrais deste projeto:

1. **Organizacao por recurso**: distritos, UBS, usuarios, pacientes, avaliacoes, riscos e relatorios seguem o mesmo fluxo de controller, service, repository e model.
2. **Reuso de regras de aplicacao**: validacoes de UUID, busca por email, paginacao e delecao ficam nos services e nao precisam ser repetidas nos controllers.
3. **Evolucao gradual**: a base ainda usa Request direto e Eloquent Models, mas a separacao atual permite adicionar Form Requests, Resources, Policies e testes especificos sem reescrever a API.

Na interface web, a arquitetura usa **Blade templates** com um layout base, paginas simples e assets publicos. O Vite esta configurado para compilar `resources/css/app.css` e `resources/js/app.js`, enquanto algumas telas usam CSS em `public/css`.

---

## Visualizacao da Arquitetura (Backend)

```text
┌─────────────────────────────────────────────────────────────┐
│                         HTTP / API                          │
│  routes/web.php e api.php -> RouteServiceProvider           │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│                        Controllers                          │
│  Recebem Request, query string per_page e parametros de rota│
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│                          Services                           │
│  Validam UUID/email, normalizam paginacao e orquestram CRUD │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│                        Repositories                         │
│  Encapsulam consultas Eloquent e criacao de registros       │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│                       Eloquent Models                       │
│  Tabelas, fillable, casts e relacionamentos                 │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│                           Banco                             │
│  PostgreSQL como padrao; SQLite somente em testes           │
└─────────────────────────────────────────────────────────────┘
```

## Visualizacao da Arquitetura (Interface Web)

```text
┌─────────────────────────────────────────────────────────────┐
│                      resources/views                        │
│  home.blade.php | register.blade.php | contact.blade.php    │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│                 resources/views/layouts/main.blade.php      │
│  HTML base, Bootstrap via CDN, fonte Roboto e assets publicos│
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│                    public/css e public/js                   │
│  styles.css e scripts.js; register.styles.css fica em resources/css│
└─────────────────────────────────────────────────────────────┘
```

---

## Fluxo de Dados — Requisicao Tipica

### API: Criacao de Paciente

```text
1. Cliente envia POST /api/patients com body JSON ou form data.
2. Laravel roteia para PatientController@store.
3. Controller repassa $request->all() para PatientService::createPatient().
4. Service delega para PatientRepository::createPatient().
5. Repository cria o registro via PatientModel::newQuery()->create($data).
6. Eloquent aplica fillable e casts do PatientModel.
7. Controller retorna JSON com status 201.
```

### API: Consulta por ID

```text
1. Cliente envia GET /api/users/{id}.
2. UserController@show chama UserService::getUserById($id).
3. ValidateUtils::validateId() exige UUID valido.
4. UserRepository::findUserById($id) busca o registro via Eloquent.
5. Se nao encontrar, o service lanca ModelNotFoundException.
6. Se encontrar, o model e serializado em JSON.
```

### Web: Formulario de Registro

```text
1. Cliente acessa GET /register/{id?}.
2. A rota renderiza resources/views/register.blade.php.
3. O formulario envia POST para a rota nomeada web, exposta como /login.
4. A rota atual faz dump dos dados recebidos com dd($data).
```

---

## Inversao de Dependencia

O projeto usa injecao de dependencia do container do Laravel por construtor:

```php
class UserController extends Controller
{
    public function __construct(
        protected UserService $service,
    ) {
    }
}
```

Cada service recebe seu repository correspondente, e cada repository recebe o model Eloquent correspondente:

```php
class UserService
{
    public function __construct(
        protected UserRepository $repository,
    ) {
    }
}
```

Nao ha interfaces formais para repositories neste momento. A separacao atual ainda ajuda a trocar ou especializar consultas sem mover logica para controllers, mas a substituicao por mocks exige binding manual ou doubles nos testes.

---

## Modulos do Sistema

| Modulo       | Responsabilidade                                                                                            |
| ------------ | ----------------------------------------------------------------------------------------------------------- |
| `District`   | Cadastro e consulta de distritos aos quais UBS pertencem.                                                   |
| `Ubs`        | Cadastro de unidades basicas de saude com dados de contato, bairro, endereco e status ativo.                |
| `User`       | Cadastro de usuarios do sistema, incluindo role `admin` ou `user`, dados pessoais e vinculo com UBS.        |
| `Patient`    | Cadastro de pacientes vinculados a uma UBS.                                                                 |
| `Assessment` | Registro de avaliacao feita por usuario para paciente em uma UBS, com sintomas e respostas.                 |
| `Risk`       | Registro de risco associado a avaliacao, com percentual, score e classificacao `low`, `moderate` ou `high`. |
| `Report`     | Relatorio associado a uma avaliacao, com titulo, descricao e comentario.                                    |

---

## Relacionamentos de Dados

```text
District 1 ── N Ubs
Ubs      1 ── N User
Ubs      1 ── N Patient
Ubs      1 ── N Assessment
User     1 ── N Assessment
Patient  1 ── N Assessment
Assessment 1 ── 1 Risk
Assessment 1 ── 1 Report
```

Os relacionamentos acima estao declarados nos models em `application/app/Models`. As migrations versionadas ainda cobrem apenas `users`, `password_reset_tokens`, `sessions`, `cache` e `cache_locks`; portanto, ha uma lacuna entre o modelo de dominio atual e o schema versionado.
