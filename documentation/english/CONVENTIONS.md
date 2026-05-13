# Organization and Naming Standards

## Naming Conventions

### Backend (PHP / Laravel)

| Element | Current convention | Example |
| --- | --- | --- |
| **Namespaces** | PSR-4 under `App\` | `App\Services\UserService` |
| **Classes** | `PascalCase` | `UserService`, `PatientRepository` |
| **Controllers** | Singular resource + `Controller` | `RiskController` |
| **Services** | Singular resource + `Service` | `AssessmentService` |
| **Repositories** | Singular resource + `Repository` | `DistrictRepository` |
| **Models** | Singular resource + `Model` | `UbsModel`, `ReportModel` |
| **Enums** | `PascalCase` | `UserRole`, `RiskClassification` |
| **Enum values** | Persisted values in `lowercase` | `admin`, `user`, `low` |
| **Methods** | `camelCase` | `getUserById()`, `createRisk()` |
| **Variables** | `camelCase` | `$perPage`, `$assessment` |
| **Tables** | Plural `snake_case` | `users`, `assessments` |
| **Columns** | Mostly `snake_case` | `ubs_id`, `assessment_id` |

### Views and Assets

| Element | Current convention | Example |
| --- | --- | --- |
| **Blade views** | Lowercase simple names or kebab-style names | `home.blade.php`, `register.blade.php` |
| **Blade layouts** | `layouts/` subdirectory | `layouts/main.blade.php` |
| **Screen CSS** | Descriptive names with dots | `register.styles.css` |
| **Public JS** | Simple lowercase names | `scripts.js` |
| **Vite entries** | `resources/css/app.css`, `resources/js/app.js` | Configured in `vite.config.js` |

---

## File Type Suffix Standard

| Suffix / Pattern | Type | Layer |
| --- | --- | --- |
| `*Controller.php` | HTTP Controller | Entry point |
| `*Service.php` | Application service | Rules and orchestration |
| `*Repository.php` | Eloquent repository | Persistence |
| `*Model.php` | Eloquent model | Data and relationships |
| `*.blade.php` | Blade template | Server-side interface |
| `*.css` | Styles | Assets |
| `*.js` | JavaScript | Assets |
| `*Test.php` | PHPUnit test | Tests |

---

## Design Patterns Used

### Service Layer

Services encapsulate rules that do not belong directly to HTTP transport. Example:

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

Repositories encapsulate Eloquent queries and record creation. The current pattern uses concrete classes, without interfaces:

```text
UserService -> UserRepository -> UserModel
```

### Shared Validation Trait

`ValidateUtils` centralizes UUID and email validation to avoid repetition across services.

### Active Record / Eloquent Model

Models centralize fillable fields, casts, and relationships. This is the native Laravel approach and is used by every main resource.

### Resource Routing

`Route::apiResource()` generates predictable REST routes for index, store, show, update, and destroy. The project applies this pattern to seven resources.

### Provider Pattern

`RouteServiceProvider` and `AppServiceProvider` customize framework bootstrapping: route loading with the `/api` prefix and migration loading from subdirectories.

---

## Resource-Based Organization

Each main resource has parallel files across layers:

```text
app/Http/Controllers/UserController.php
app/Services/UserService.php
app/Repositories/UserRepository.php
app/Models/UserModel.php
```

The same pattern exists for:

| Resource | Controller | Service | Repository | Model |
| --- | --- | --- | --- | --- |
| District | `DistrictController` | `DistrictService` | `DistrictRepository` | `DistrictModel` |
| UBS | `UbsController` | `UbsService` | `UbsRepository` | `UbsModel` |
| User | `UserController` | `UserService` | `UserRepository` | `UserModel` |
| Patient | `PatientController` | `PatientService` | `PatientRepository` | `PatientModel` |
| Assessment | `AssessmentController` | `AssessmentService` | `AssessmentRepository` | `AssessmentModel` |
| Risk | `RiskController` | `RiskService` | `RiskRepository` | `RiskModel` |
| Report | `ReportController` | `ReportService` | `ReportRepository` | `ReportModel` |

---

## Operational Conventions

| Area | Convention |
| --- | --- |
| **Pagination** | Controllers read `per_page`; services limit it between 1 and 20. |
| **Deletion** | Models comment on hard delete usage; there is no `SoftDeletes` trait in the current models. |
| **Routes** | Every file under `routes/` receives the `/api` prefix. |
| **Responses** | Controllers return JSON for the API; `store` uses status 201 and delete uses 204. |
| **HTTP validation** | There are no Form Requests yet; controllers pass `$request->all()`. |
| **Authorization** | There are no Policies, Gates, or auth middleware in the current routes. |

---

## Known Inconsistencies

- `UserModel` uses `HasUuids`, but the versioned `users` migration uses `$table->id()`.
- `UserModel` expects fields such as `ubs_id`, `cpf`, `address`, `phone`, `password`, and `role`; the current `users` migration defines another set of columns, including `hashPassword`, `weight`, `risk`, and `hasRisk`.
- The models for `districts`, `ubs`, `patients`, `assessments`, `risks`, and `reports` do not have matching versioned migrations in the current checkout.
- The register view references `/css/register.styles.css`, but the checkout only versions `public/css/styles.css`; `register.styles.css` is present under `resources/css`.
