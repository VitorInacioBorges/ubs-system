# Padroes de Organizacao e Nomeacao

## Naming Conventions

### Backend (PHP / Laravel)

| Elemento | Convencao atual | Exemplo |
| --- | --- | --- |
| **Namespaces** | PSR-4 sob `App\` | `App\Services\UserService` |
| **Classes** | `PascalCase` | `UserService`, `PatientRepository` |
| **Controllers** | Recurso singular + `Controller` | `RiskController` |
| **Services** | Recurso singular + `Service` | `AssessmentService` |
| **Repositories** | Recurso singular + `Repository` | `DistrictRepository` |
| **Models** | Recurso singular + `Model` | `UbsModel`, `ReportModel` |
| **Enums** | `PascalCase` | `UserRole`, `RiskClassification` |
| **Valores de Enum** | Valores persistidos em `lowercase` | `admin`, `user`, `low` |
| **Metodos** | `camelCase` | `getUserById()`, `createRisk()` |
| **Variaveis** | `camelCase` | `$perPage`, `$assessment` |
| **Tabelas** | `snake_case` plural | `users`, `assessments` |
| **Colunas** | Predominantemente `snake_case` | `ubs_id`, `assessment_id` |

### Views e Assets

| Elemento | Convencao atual | Exemplo |
| --- | --- | --- |
| **Views Blade** | `kebab` ou nome simples em minusculas | `home.blade.php`, `register.blade.php` |
| **Layouts Blade** | Subdiretorio `layouts/` | `layouts/main.blade.php` |
| **CSS de tela** | Nome descritivo com pontos | `register.styles.css` |
| **JS publico** | Nome simples em minusculas | `scripts.js` |
| **Entradas Vite** | `resources/css/app.css`, `resources/js/app.js` | Configuradas em `vite.config.js` |

---

## Padrao de Sufixos por Tipo de Arquivo

| Sufixo / Padrao | Tipo | Camada |
| --- | --- | --- |
| `*Controller.php` | Controller HTTP | Entrada |
| `*Service.php` | Service de aplicacao | Regras e orquestracao |
| `*Repository.php` | Repository Eloquent | Persistencia |
| `*Model.php` | Model Eloquent | Dados e relacionamentos |
| `*.blade.php` | Template Blade | Interface server-side |
| `*.css` | Estilos | Assets |
| `*.js` | JavaScript | Assets |
| `*Test.php` | Teste PHPUnit | Testes |

---

## Design Patterns Utilizados

### Service Layer

Os services encapsulam regras que nao pertencem diretamente ao transporte HTTP. Exemplos:

```php
public function getUserById(string $id): UserModel
{
    $this->validateId($id);

    $user = $this->repository->findUserById($id);

    if ($user === null) {
        throw (new ModelNotFoundException())->setModel(UserModel::class, [$id]);
    }

    return $user;
}
```

### Repository Pattern

Repositories encapsulam consultas Eloquent e criacao de registros. O padrao atual usa classes concretas, sem interfaces:

```text
UserService -> UserRepository -> UserModel
```

### Trait de Validacao Compartilhada

`ValidateUtils` centraliza validacao de UUID e email para evitar repeticao nos services.

### Active Record / Eloquent Model

Os models concentram fillable, casts e relacionamentos. Essa e a abordagem nativa do Laravel e e usada em todos os recursos principais.

### Resource Routing

`Route::apiResource()` gera rotas REST previsiveis para index, store, show, update e destroy. O projeto aplica esse padrao para sete recursos.

### Provider Pattern

`RouteServiceProvider` e `AppServiceProvider` customizam bootstrapping do framework: carregamento de rotas com prefixo `/api` e carregamento de migrations em subdiretorios.

---

## Organizacao por Recurso

Cada recurso principal possui arquivos paralelos nas camadas:

```text
app/Http/Controllers/UserController.php
app/Services/UserService.php
app/Repositories/UserRepository.php
app/Models/UserModel.php
```

O mesmo padrao existe para:

| Recurso | Controller | Service | Repository | Model |
| --- | --- | --- | --- | --- |
| Distrito | `DistrictController` | `DistrictService` | `DistrictRepository` | `DistrictModel` |
| UBS | `UbsController` | `UbsService` | `UbsRepository` | `UbsModel` |
| Usuario | `UserController` | `UserService` | `UserRepository` | `UserModel` |
| Paciente | `PatientController` | `PatientService` | `PatientRepository` | `PatientModel` |
| Avaliacao | `AssessmentController` | `AssessmentService` | `AssessmentRepository` | `AssessmentModel` |
| Risco | `RiskController` | `RiskService` | `RiskRepository` | `RiskModel` |
| Relatorio | `ReportController` | `ReportService` | `ReportRepository` | `ReportModel` |

---

## Convencoes Operacionais

| Area | Convencao |
| --- | --- |
| **Paginacao** | Controllers leem `per_page`; services limitam entre 1 e 20. |
| **Delecao** | Models comentam uso de hard delete; nao ha SoftDeletes nos models atuais. |
| **Rotas** | Todos os arquivos em `routes/` recebem prefixo `/api`. |
| **Respostas** | Controllers retornam JSON para API; `store` usa status 201 e delete usa 204. |
| **Validacao HTTP** | Ainda nao ha Form Requests; controllers repassam `$request->all()`. |
| **Autorizacao** | Ainda nao ha Policies, Gates ou middleware de auth nas rotas atuais. |

---

## Inconsistencias Conhecidas

- `UserModel` usa `HasUuids`, mas a migration versionada de `users` usa `$table->id()`.
- `UserModel` espera campos como `ubs_id`, `cpf`, `address`, `phone`, `password` e `role`; a migration atual de `users` define outro conjunto de colunas, incluindo `hashPassword`, `weight`, `risk` e `hasRisk`.
- Os models de `districts`, `ubs`, `patients`, `assessments`, `risks` e `reports` nao possuem migrations versionadas correspondentes no checkout atual.
- O layout referencia `/css/register.styles.css` em `register.blade.php`, mas esse arquivo esta em `resources/css/register.styles.css`; em `public/css` existe apenas `styles.css` no checkout versionado.
