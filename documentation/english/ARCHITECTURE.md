# Project Architecture

## Architecture Rationale

The project uses a layered Laravel architecture organized into **Controllers**, **Services**, **Repositories**, and **Eloquent Models**. This separation reduces coupling between HTTP transport, application rules, and persistence while still relying on Laravel's native capabilities.

This choice addresses three core needs of this project:

1. **Resource organization**: districts, UBS units, users, patients, assessments, risks, and reports follow the same controller, service, repository, and model flow.
2. **Application rule reuse**: UUID validation, email lookup, pagination, and deletion rules live in services instead of being repeated in controllers.
3. **Gradual evolution**: the codebase still uses raw Requests and Eloquent Models directly, but the current separation allows Form Requests, Resources, Policies, and targeted tests to be added without rewriting the API.

On the web interface side, the architecture uses **Blade templates** with a base layout, simple pages, and public assets. Vite is configured to compile `resources/css/app.css` and `resources/js/app.js`, while some screens use CSS under `public/css`.

---

## Architecture Diagram (Backend)

```text
┌─────────────────────────────────────────────────────────────┐
│                         HTTP / API                          │
│  routes/routes.php -> RouteServiceProvider -> /api prefix    │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│                        Controllers                          │
│  Receive Request, per_page query string, and route params    │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│                          Services                           │
│  Validate UUID/email, normalize pagination, orchestrate CRUD │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│                        Repositories                         │
│  Encapsulate Eloquent queries and record creation            │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│                       Eloquent Models                       │
│  Tables, fillable fields, casts, and relationships           │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│                          Database                           │
│  Default SQLite configuration; Laravel pgsql support         │
└─────────────────────────────────────────────────────────────┘
```

## Architecture Diagram (Web Interface)

```text
┌─────────────────────────────────────────────────────────────┐
│                      resources/views                        │
│  home.blade.php | register.blade.php | contact.blade.php     │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│                 resources/views/layouts/main.blade.php       │
│  Base HTML, Bootstrap CDN, Roboto font, and public assets     │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│                    public/css and public/js                  │
│  styles.css and scripts.js; register.styles.css is in resources/css │
└─────────────────────────────────────────────────────────────┘
```

---

## Data Flow — Typical Request

### API: Patient Creation

```text
1. Client sends POST /api/patients with JSON body or form data.
2. Laravel routes the request to PatientController@store.
3. Controller passes $request->all() to PatientService::createPatient().
4. Service delegates to PatientRepository::createPatient().
5. Repository creates the record through PatientModel::newQuery()->create($data).
6. Eloquent applies fillable fields and casts from PatientModel.
7. Controller returns JSON with HTTP 201.
```

### API: Lookup by ID

```text
1. Client sends GET /api/users/{user}.
2. UserController@show calls UserService::getUserById($id).
3. ValidateUtils::validateId() requires a valid UUID.
4. UserRepository::findUserById($id) queries the record with Eloquent.
5. If missing, the service throws ModelNotFoundException.
6. If found, the model is serialized as JSON.
```

### Web: Registration Form

```text
1. Client opens GET /api/register/{id?}.
2. The route renders resources/views/register.blade.php.
3. The form submits POST to the named route web, exposed as /api/login.
4. The current route dumps the submitted payload with dd($data).
```

---

## Dependency Inversion

The project uses Laravel container constructor injection:

```php
class UserController extends Controller
{
    public function __construct(
        protected UserService $service,
    ) {
    }
}
```

Each service receives its corresponding repository, and each repository receives its corresponding Eloquent model:

```php
class UserService
{
    public function __construct(
        protected UserRepository $repository,
    ) {
    }
}
```

There are no formal repository interfaces at the moment. The current separation still helps keep query changes away from controllers, but replacing repositories with mocks requires manual bindings or test doubles.

---

## System Modules

| Module | Responsibility |
| --- | --- |
| `District` | District registration and lookup for UBS units. |
| `Ubs` | UBS unit registration with contact data, neighborhood, address, and active status. |
| `User` | System user registration, including `admin` or `user` role, personal data, and UBS linkage. |
| `Patient` | Patient registration linked to a UBS unit. |
| `Assessment` | Assessment created by a user for a patient in a UBS unit, with symptoms and answers. |
| `Risk` | Risk record associated with an assessment, including percentage, score, and `low`, `moderate`, or `high` classification. |
| `Report` | Report associated with an assessment, including title, description, and comment. |

---

## Data Relationships

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

These relationships are declared in the models under `application/app/Models`. The versioned migrations currently cover only `users`, `password_reset_tokens`, `sessions`, `cache`, and `cache_locks`; therefore, there is a gap between the current domain model and the versioned schema.
