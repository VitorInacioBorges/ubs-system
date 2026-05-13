# Mapeamento de Diretorios

## Estrutura Completa

```bash
ubs-system/
├── application/
│   ├── app/
│   │   ├── Enums/
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   ├── Models/
│   │   ├── Providers/
│   │   ├── Repositories/
│   │   ├── Services/
│   │   └── Utils/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   │   ├── factories/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── public/
│   │   ├── css/
│   │   └── js/
│   ├── resources/
│   │   ├── css/
│   │   ├── js/
│   │   └── views/
│   │       └── layouts/
│   ├── routes/
│   ├── storage/
│   ├── tests/
│   │   ├── Feature/
│   │   └── Unit/
│   ├── artisan
│   ├── composer.json
│   ├── package.json
│   ├── phpunit.xml
│   └── vite.config.js
├── documentation/
│   ├── english/
│   └── portuguese/
├── .gitignore
└── README.md
```

Diretorios ignorados por `.gitignore`, como `application/vendor/`, `application/node_modules/`, `application/.env`, caches, logs e arquivos gerados em `storage/`, nao fazem parte da documentacao operacional.

---

## Backend — Detalhamento por Diretorio

### `application/app/Http/Controllers/`

Controllers HTTP da API. Eles recebem `Illuminate\Http\Request`, extraem `per_page` quando necessario, delegam para services e retornam `JsonResponse`.

| Arquivo | Rotas base |
| --- | --- |
| `DistrictController.php` | `/api/districts` |
| `UbsController.php` | `/api/ubs` |
| `UserController.php` | `/api/users` |
| `PatientController.php` | `/api/patients` |
| `AssessmentController.php` | `/api/assessments` |
| `RiskController.php` | `/api/risks` |
| `ReportController.php` | `/api/reports` |

Cada controller expõe o CRUD padrao do `Route::apiResource` e uma rota adicional `DELETE /api/{resource}/{id}/delete-self`.

### `application/app/Services/`

Camada de aplicacao. Os services concentram validacao de UUID, validacao de email quando existe busca por email, normalizacao de paginacao e orquestracao de update/delete.

| Arquivo | Responsabilidade |
| --- | --- |
| `DistrictService.php` | CRUD de distritos e paginacao limitada entre 1 e 20 itens. |
| `UbsService.php` | CRUD de UBS e busca por email. |
| `UserService.php` | CRUD de usuarios e busca por email. |
| `PatientService.php` | CRUD de pacientes. |
| `AssessmentService.php` | CRUD de avaliacoes. |
| `RiskService.php` | CRUD de riscos. |
| `ReportService.php` | CRUD de relatorios. |

### `application/app/Repositories/`

Camada de acesso a dados. Repositories usam `newQuery()` sobre os models Eloquent e encapsulam as consultas reutilizadas pelos services.

| Arquivo | Operacoes definidas |
| --- | --- |
| `DistrictRepository.php` | `paginateDistricts`, `findDistrictById`, `createDistrict` |
| `UbsRepository.php` | `paginateUbs`, `findUbsById`, `findUbsByEmail`, `createUbs` |
| `UserRepository.php` | `paginateUsers`, `findUserById`, `findUserByEmail`, `createUser` |
| `PatientRepository.php` | `paginatePatients`, `findPatientById`, `createPatient` |
| `AssessmentRepository.php` | `paginateAssessments`, `findAssessmentById`, `createAssessment` |
| `RiskRepository.php` | `paginateRisks`, `findRiskById`, `createRisk` |
| `ReportRepository.php` | `paginateReports`, `findReportById`, `createReport` |

### `application/app/Models/`

Models Eloquent com `fillable`, casts, tabela explicita e relacionamentos.

| Arquivo | Tabela | Relacionamentos principais |
| --- | --- | --- |
| `DistrictModel.php` | `districts` | `hasMany(UbsModel)` |
| `UbsModel.php` | `ubs` | `belongsTo(DistrictModel)`, `hasMany(UserModel)`, `hasMany(PatientModel)`, `hasMany(AssessmentModel)` |
| `UserModel.php` | `users` | `belongsTo(UbsModel)`, `hasMany(AssessmentModel)` |
| `PatientModel.php` | `patients` | `belongsTo(UbsModel)`, `hasMany(AssessmentModel)` |
| `AssessmentModel.php` | `assessments` | `belongsTo(PatientModel)`, `belongsTo(UserModel)`, `belongsTo(UbsModel)`, `hasOne(RiskModel)`, `hasOne(ReportModel)` |
| `RiskModel.php` | `risks` | `belongsTo(AssessmentModel)` |
| `ReportModel.php` | `reports` | `belongsTo(AssessmentModel)` |

### `application/app/Enums/`

Enums nativos do PHP usados como casts nos models.

| Arquivo | Valores |
| --- | --- |
| `UserRole.php` | `admin`, `user` |
| `RiskClassification.php` | `low`, `moderate`, `high` |

### `application/app/Utils/`

| Arquivo | Responsabilidade |
| --- | --- |
| `ValidateUtils.php` | Trait com `validateId()` para UUID e `validateEmail()` para email RFC ate 255 caracteres. |

### `application/app/Providers/`

| Arquivo | Responsabilidade |
| --- | --- |
| `AppServiceProvider.php` | Carrega migrations do diretorio principal e de subdiretorios dentro de `database/migrations`. |
| `RouteServiceProvider.php` | Carrega todos os arquivos PHP em `routes/`, exceto `console.php`, com middleware `api` e prefixo `/api`. |

---

## Rotas

### `application/routes/routes.php`

Arquivo unico de rotas do projeto. Como o `RouteServiceProvider` aplica prefixo `/api` a todos os arquivos em `routes/`, ate as views Blade ficam expostas com esse prefixo.

| Rota | Tipo | Responsabilidade |
| --- | --- | --- |
| `GET /api` | Web view | Renderiza `home.blade.php`. |
| `GET /api/register/{id?}` | Web view | Renderiza o formulario de registro. |
| `POST /api/login` | Web action | Recebe formulario e executa `dd($data)` atualmente. |
| `apiResource` | REST JSON | CRUD para `districts`, `ubs`, `users`, `patients`, `assessments`, `risks`, `reports`. |
| `DELETE /api/{resource}/{id}/delete-self` | REST JSON | Delecao alternativa para cada recurso. |

---

## Banco de Dados

### `application/database/migrations/`

| Arquivo | Tabelas criadas |
| --- | --- |
| `2026_01_23_143151_create_users_table.php` | `users` |
| `2026_01_23_150700_password_reset_tokens.php` | `password_reset_tokens` |
| `2026_04_27_135537_create_sessions_table.php` | `sessions` |
| `2026_04_27_145038_create_cache_table.php` | `cache`, `cache_locks` |

A migration versionada de `users` ainda nao acompanha todos os campos esperados por `UserModel`, e nao ha migrations versionadas para `districts`, `ubs`, `patients`, `assessments`, `risks` e `reports`.

### `application/database/seeders/`

| Arquivo | Responsabilidade |
| --- | --- |
| `DatabaseSeeder.php` | Cria um usuario de teste com `name = Test User` e `email = test@example.com`. |

### `application/database/factories/`

| Arquivo | Responsabilidade |
| --- | --- |
| `UserFactory.php` | Factory padrao de usuarios para testes e seeders. |

---

## Interface Web e Assets

### `application/resources/views/`

| Arquivo | Responsabilidade |
| --- | --- |
| `layouts/main.blade.php` | Layout HTML base com Bootstrap via CDN, Roboto via Google Fonts, `public/css/styles.css` e `public/js/scripts.js`. |
| `home.blade.php` | Tela inicial simples do "Sistema UBS". |
| `register.blade.php` | Formulario de cadastro exibindo o nome "Glicodata". |
| `contact.blade.php` | Pagina simples de contatos. |

### `application/public/`

| Caminho | Responsabilidade |
| --- | --- |
| `public/index.php` | Front controller do Laravel. |
| `public/css/styles.css` | Estilo global simples para fonte e cor de `h1`. |
| `public/js/scripts.js` | Script publico atual com log de funcionamento. |

### `application/resources/css` e `application/resources/js`

Arquivos de entrada do Vite configurados em `vite.config.js`: `resources/css/app.css` e `resources/js/app.js`. O arquivo `resources/css/register.styles.css` tambem existe e contem estilos do formulario de registro, mas a view atual referencia `/css/register.styles.css`, caminho que apontaria para `public/css/register.styles.css`.

---

## Testes

| Caminho | Responsabilidade |
| --- | --- |
| `tests/Feature/ExampleTest.php` | Testa se `GET /api` retorna status 200. |
| `tests/Unit/ExampleTest.php` | Teste unitario basico `assertTrue(true)`. |
| `phpunit.xml` | Configura suite Unit e Feature com SQLite em memoria no ambiente de teste. |
