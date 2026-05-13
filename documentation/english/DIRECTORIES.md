# Directory Mapping

## Full Structure

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

Directories ignored by `.gitignore`, such as `application/vendor/`, `application/node_modules/`, `application/.env`, caches, logs, and generated files under `storage/`, are not part of the operational documentation.

---

## Backend — Directory Details

### `application/app/Http/Controllers/`

HTTP controllers for the API. They receive `Illuminate\Http\Request`, extract `per_page` when needed, delegate to services, and return `JsonResponse`.

| File | Base routes |
| --- | --- |
| `DistrictController.php` | `/api/districts` |
| `UbsController.php` | `/api/ubs` |
| `UserController.php` | `/api/users` |
| `PatientController.php` | `/api/patients` |
| `AssessmentController.php` | `/api/assessments` |
| `RiskController.php` | `/api/risks` |
| `ReportController.php` | `/api/reports` |

Each controller exposes the standard `Route::apiResource` CRUD actions plus an additional `DELETE /api/{resource}/{id}/delete-self` route.

### `application/app/Services/`

Application layer. Services centralize UUID validation, email validation where email lookup exists, pagination normalization, and update/delete orchestration.

| File | Responsibility |
| --- | --- |
| `DistrictService.php` | District CRUD and pagination limited between 1 and 20 items. |
| `UbsService.php` | UBS CRUD and email lookup. |
| `UserService.php` | User CRUD and email lookup. |
| `PatientService.php` | Patient CRUD. |
| `AssessmentService.php` | Assessment CRUD. |
| `RiskService.php` | Risk CRUD. |
| `ReportService.php` | Report CRUD. |

### `application/app/Repositories/`

Data access layer. Repositories use `newQuery()` on Eloquent models and encapsulate queries reused by services.

| File | Defined operations |
| --- | --- |
| `DistrictRepository.php` | `paginateDistricts`, `findDistrictById`, `createDistrict` |
| `UbsRepository.php` | `paginateUbs`, `findUbsById`, `findUbsByEmail`, `createUbs` |
| `UserRepository.php` | `paginateUsers`, `findUserById`, `findUserByEmail`, `createUser` |
| `PatientRepository.php` | `paginatePatients`, `findPatientById`, `createPatient` |
| `AssessmentRepository.php` | `paginateAssessments`, `findAssessmentById`, `createAssessment` |
| `RiskRepository.php` | `paginateRisks`, `findRiskById`, `createRisk` |
| `ReportRepository.php` | `paginateReports`, `findReportById`, `createReport` |

### `application/app/Models/`

Eloquent models with `fillable`, casts, explicit table names, and relationships.

| File | Table | Main relationships |
| --- | --- | --- |
| `DistrictModel.php` | `districts` | `hasMany(UbsModel)` |
| `UbsModel.php` | `ubs` | `belongsTo(DistrictModel)`, `hasMany(UserModel)`, `hasMany(PatientModel)`, `hasMany(AssessmentModel)` |
| `UserModel.php` | `users` | `belongsTo(UbsModel)`, `hasMany(AssessmentModel)` |
| `PatientModel.php` | `patients` | `belongsTo(UbsModel)`, `hasMany(AssessmentModel)` |
| `AssessmentModel.php` | `assessments` | `belongsTo(PatientModel)`, `belongsTo(UserModel)`, `belongsTo(UbsModel)`, `hasOne(RiskModel)`, `hasOne(ReportModel)` |
| `RiskModel.php` | `risks` | `belongsTo(AssessmentModel)` |
| `ReportModel.php` | `reports` | `belongsTo(AssessmentModel)` |

### `application/app/Enums/`

Native PHP enums used as model casts.

| File | Values |
| --- | --- |
| `UserRole.php` | `admin`, `user` |
| `RiskClassification.php` | `low`, `moderate`, `high` |

### `application/app/Utils/`

| File | Responsibility |
| --- | --- |
| `ValidateUtils.php` | Trait with `validateId()` for UUIDs and `validateEmail()` for RFC email up to 255 characters. |

### `application/app/Providers/`

| File | Responsibility |
| --- | --- |
| `AppServiceProvider.php` | Loads migrations from the main directory and subdirectories inside `database/migrations`. |
| `RouteServiceProvider.php` | Loads every PHP file in `routes/`, except `console.php`, with `api` middleware and `/api` prefix. |

---

## Routes

### `application/routes/routes.php`

Single route file for the project. Since `RouteServiceProvider` applies the `/api` prefix to every file under `routes/`, even Blade views are exposed under this prefix.

| Route | Type | Responsibility |
| --- | --- | --- |
| `GET /api` | Web view | Renders `home.blade.php`. |
| `GET /api/register/{id?}` | Web view | Renders the registration form. |
| `POST /api/login` | Web action | Receives the form and currently runs `dd($data)`. |
| `apiResource` | REST JSON | CRUD for `districts`, `ubs`, `users`, `patients`, `assessments`, `risks`, `reports`. |
| `DELETE /api/{resource}/{id}/delete-self` | REST JSON | Alternative deletion route for each resource. |

---

## Database

### `application/database/migrations/`

| File | Created tables |
| --- | --- |
| `2026_01_23_143151_create_users_table.php` | `users` |
| `2026_01_23_150700_password_reset_tokens.php` | `password_reset_tokens` |
| `2026_04_27_135537_create_sessions_table.php` | `sessions` |
| `2026_04_27_145038_create_cache_table.php` | `cache`, `cache_locks` |

The versioned `users` migration does not yet match every field expected by `UserModel`, and there are no versioned migrations for `districts`, `ubs`, `patients`, `assessments`, `risks`, and `reports`.

### `application/database/seeders/`

| File | Responsibility |
| --- | --- |
| `DatabaseSeeder.php` | Creates a test user with `name = Test User` and `email = test@example.com`. |

### `application/database/factories/`

| File | Responsibility |
| --- | --- |
| `UserFactory.php` | Default user factory for tests and seeders. |

---

## Web Interface and Assets

### `application/resources/views/`

| File | Responsibility |
| --- | --- |
| `layouts/main.blade.php` | Base HTML layout with Bootstrap CDN, Google Fonts Roboto, `public/css/styles.css`, and `public/js/scripts.js`. |
| `home.blade.php` | Simple "Sistema UBS" home screen. |
| `register.blade.php` | Registration form displaying the "Glicodata" name. |
| `contact.blade.php` | Simple contact page. |

### `application/public/`

| Path | Responsibility |
| --- | --- |
| `public/index.php` | Laravel front controller. |
| `public/css/styles.css` | Simple global style for font and `h1` color. |
| `public/js/scripts.js` | Current public script with a functioning log. |

### `application/resources/css` and `application/resources/js`

Vite entry files configured in `vite.config.js`: `resources/css/app.css` and `resources/js/app.js`. The file `resources/css/register.styles.css` also exists and contains registration form styles, but the current view references `/css/register.styles.css`, a path that would point to `public/css/register.styles.css`.

---

## Tests

| Path | Responsibility |
| --- | --- |
| `tests/Feature/ExampleTest.php` | Tests that `GET /api` returns HTTP 200. |
| `tests/Unit/ExampleTest.php` | Basic `assertTrue(true)` unit test. |
| `phpunit.xml` | Configures Unit and Feature suites with in-memory SQLite for tests. |
