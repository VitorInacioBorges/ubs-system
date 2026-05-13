# Best Practices

## SOLID Principles

### Single Responsibility (SRP)

Each class tends to have one main responsibility:

- **Controllers**: receive requests and return JSON.
- **Services**: validate IDs, emails, pagination, and coordinate create/update/delete operations.
- **Repositories**: encapsulate Eloquent queries.
- **Models**: declare table names, fillable fields, casts, and relationships.
- **Enums**: isolate allowed values for roles and risk classification.

### Open/Closed (OCP)

New resources can be added by replicating the controller, service, repository, and model pattern without changing existing resources. To evolve this practice, shared behavior should go into traits, policies, form requests, or dedicated classes instead of large conditionals in controllers.

### Liskov Substitution (LSP)

Because services depend on concrete repositories, replacement with alternative implementations is not automatic yet. If the application starts requiring frequent mocks or multiple persistence mechanisms, introduce contracts and container bindings.

### Interface Segregation (ISP)

There are no interfaces in the current design. Practical segregation happens through small classes per resource. If contracts are added, keep one interface per resource and avoid generic repositories with methods not every model uses.

### Dependency Inversion (DIP)

Laravel's container injects controllers, services, repositories, and models. Dependencies still point to concrete classes, which is pragmatic for the current size of the project. For more complex rules, use interfaces under `app/Contracts` or `app/Repositories/Contracts`.

---

## Error Handling

### Backend

| Layer | Current strategy |
| --- | --- |
| **Controllers** | Return `JsonResponse` and delegate errors to Laravel's default handler. |
| **Services** | Throw `ValidationException` for invalid UUID/email values and `ModelNotFoundException` for missing records. |
| **Repositories** | Propagate Eloquent and database errors. |
| **Models** | Use casts to normalize types during serialization and persistence. |

### Areas to Strengthen

| Area | Recommendation |
| --- | --- |
| **Input validation** | Create Form Requests per resource to replace `$request->all()` in controllers. |
| **Error format** | Standardize JSON responses for validation, not found, and conflict errors. |
| **Authentication** | Apply authentication middleware once endpoints are no longer public. |
| **Authorization** | Add Policies to separate regular user and administrator permissions. |
| **Transactions** | Use `DB::transaction()` when an operation writes to multiple tables. |

---

## Testing

### Configured Test Types

| Type | Framework | Configuration |
| --- | --- | --- |
| **Unit** | PHPUnit 11 | `tests/Unit` suite in `phpunit.xml`. |
| **Feature** | Laravel TestCase + PHPUnit | `tests/Feature` suite with in-memory SQLite. |

### Test Structure

```bash
application/tests/
├── Feature/
│   └── ExampleTest.php
├── Unit/
│   └── ExampleTest.php
└── TestCase.php
```

### Current Coverage

The current checkout contains example tests:

- `GET /api` must return HTTP 200.
- One basic unit test asserts that `true` is true.

To cover the real API, prioritize:

1. Feature tests for each resource CRUD.
2. Validation tests for invalid UUID and invalid email.
3. Pagination tests for `per_page` below 1 and above 20.
4. Serialization tests for enums and casts.
5. Error tests for `ModelNotFoundException`.

---

## Security

### Authentication

`UserModel` extends `Authenticatable` and has the `password => hashed` cast, but the current code does not implement real login, tokens, guards, or authentication middleware on API routes. The `POST /api/login` route only runs `dd($data)` on the submitted form payload.

### Authorization

The `UserRole` enum defines `admin` and `user`, but there are no Policies, Gates, or role checks in controllers yet. User role is modeled but not applied as access control.

### Validation and Sanitization

| Aspect | Current implementation |
| --- | --- |
| **UUID** | `ValidateUtils::validateId()` uses `Str::isUuid()`. |
| **Email** | `ValidateUtils::validateEmail()` uses Laravel validator with `email:rfc` and `max:255`. |
| **Mass assignment** | Models use `fillable`, reducing exposure of disallowed fields. |
| **Password** | `UserModel` hides `password` and applies the `hashed` cast. |
| **CSRF** | The Blade form uses `@csrf`. |

### Environment Variables

`.env` is not versioned. The `.env.example` template defines:

```env
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=sqlite
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

In production:

- `APP_DEBUG` must be `false`.
- `APP_KEY` must be generated and protected.
- Database credentials must live only in `.env`.
- If PostgreSQL is used, define `DB_CONNECTION=pgsql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`.

---

## Persistence and Integrity

### Strengths

- Eloquent relationships are declared in models.
- Native enums reduce invalid values for `role` and `classification`.
- Services validate UUIDs before lookup by ID.
- Pagination has an upper limit to reduce large responses.

### Current Risks

| Risk | Impact |
| --- | --- |
| Incomplete migrations for the current models | `php artisan migrate` does not create every table expected by the API. |
| Divergence between `UserModel` and `users` migration | User creation/lookup may fail depending on the migrated database. |
| No Form Requests | Invalid fields may reach models until rejected by the database or casts. |
| Endpoints without auth | Any client with server access can call CRUD endpoints. |
| Default hard delete | Deletions remove records without a logical recycle bin. |

---

## Recommended Best Practices for Next Changes

1. Create migrations for `districts`, `ubs`, `patients`, `assessments`, `risks`, and `reports`.
2. Align the `users` migration to `UserModel` or adjust the model to the real schema.
3. Introduce Form Requests for each resource `store` and `update`.
4. Add API Resources to control response shape.
5. Implement authentication before exposing CRUDs outside the local environment.
6. Cover services and controllers with feature tests.
