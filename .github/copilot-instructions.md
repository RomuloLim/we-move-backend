<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4.12
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11
- tailwindcss (TAILWINDCSS) - v4

## Development Environment
- This project runs in a Docker container using **Laravel Sail**
- All commands must be executed through Sail: `./vendor/bin/sail artisan ...`
- Use `./vendor/bin/sail` prefix for artisan, composer, npm, and test commands
- The application runs inside the `laravel.test` container


## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Code Comments
- Avoid excessive comments in the code - write self-documenting code with clear variable and method names
- Only add comments for complex logic that is not immediately obvious
- When comments are necessary, write them in **English**
- Example of acceptable comment: `// Calculate discount based on user tier and purchase history`

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.


=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## URLs
- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation specific for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The 'search-docs' tool is perfect for all Laravel related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries - package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms


=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.


=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] <name>` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.


=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure
- No middleware files in `app/Http/Middleware/`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.


=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.


=== phpunit/core rules ===

## PHPUnit Core

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit <name>` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should test all of the happy paths, failure paths, and weird paths.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files, these are core to the application.

### Running Tests
- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).


=== tailwindcss/core rules ===

## Tailwind Core

- Use Tailwind CSS classes to style HTML, check and use existing tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions (i.e. Blade, JSX, Vue, etc..)
- Think through class placement, order, priority, and defaults - remove redundant classes, add classes to parent or child carefully to limit repetition, group elements logically
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing
- When listing items, use gap utilities for spacing, don't use margins.

    <code-snippet name="Valid Flex Gap Spacing Example" lang="html">
        <div class="flex gap-8">
            <div>Superior</div>
            <div>Michigan</div>
            <div>Erie</div>
        </div>
    </code-snippet>


### Dark Mode
- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.


=== tailwindcss/v4 rules ===

## Tailwind 4

- Always use Tailwind CSS v4 - do not use the deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.
- In Tailwind v4, you import Tailwind using a regular CSS `@import` statement, not using the `@tailwind` directives used in v3:

<code-snippet name="Tailwind v4 Import Tailwind Diff" lang="diff">
   - @tailwind base;
   - @tailwind components;
   - @tailwind utilities;
   + @import "tailwindcss";
</code-snippet>


### Replaced Utilities
- Tailwind v4 removed deprecated utilities. Do not use the deprecated option - use the replacement.
- Opacity values are still numeric.

| Deprecated |	Replacement |
|------------+--------------|
| bg-opacity-* | bg-black/* |
| text-opacity-* | text-black/* |
| border-opacity-* | border-black/* |
| divide-opacity-* | divide-black/* |
| ring-opacity-* | ring-black/* |
| placeholder-opacity-* | placeholder-black/* |
| flex-shrink-* | shrink-* |
| flex-grow-* | grow-* |
| overflow-ellipsis | text-ellipsis |
| decoration-slice | box-decoration-slice |
| decoration-clone | box-decoration-clone |
</laravel-boost-guidelines>


=== modular monolith architecture ===

## Modular Monolith Patterns

This application follows a **Modular Monolith** architecture using the `nwidart/laravel-modules` package. Each module is a self-contained bounded context with its own responsibilities.

### Module Structure
- Modules are located in the `Modules/` directory
- Each module follows the standard Laravel directory structure within its scope
- Core modules: `Auth`, `User`, `Operation`, `Logistics`, `Communication`, `Core`

### Separation of Concerns

#### 1. **Contracts (Interfaces)**
- Always create interfaces for Services and Repositories
- Place interfaces in: `Modules/{Module}/app/Services/{ServiceName}Interface.php`
- Place repository interfaces in: `Modules/{Module}/app/Repositories/{Entity}/{RepositoryName}Interface.php`
- Example:
  ```php
  namespace Modules\Logistics\Services;
  
  interface BoardingServiceInterface
  {
      public function boardStudent(BoardingDto $data): Boarding;
  }
  ```

#### 2. **DTOs (Data Transfer Objects)**
- Use DTOs to transfer data between layers
- DTOs must implement `App\Contracts\DtoContract`
- DTOs must be `readonly` classes
- DTOs must implement `toArray()` and `collection()` methods
- Place in: `Modules/{Module}/app/DTOs/{EntityName}Dto.php`
- Example:
  ```php
  readonly class BoardingDto implements DtoContract
  {
      public function __construct(
          public int $tripId,
          public int $studentId,
          public int $stopId,
      ) {}
      
      public function toArray(): array { ... }
      public static function collection(array $data): Collection { ... }
  }
  ```

#### 3. **Repositories**
- Repositories handle all data access logic
- Always create an interface and implementation
- Place interface in: `Modules/{Module}/app/Repositories/{Entity}/{EntityName}RepositoryInterface.php`
- Place implementation in: `Modules/{Module}/app/Repositories/{Entity}/{EntityName}Repository.php`
- Repositories should only contain data access methods, no business logic
- Example:
  ```php
  class BoardingRepository implements BoardingRepositoryInterface
  {
      public function create(BoardingDto $data): Boarding
      {
          return Boarding::create($data->toArray());
      }
  }
  ```

#### 4. **Services**
- Services contain all business logic and orchestrate repositories
- Always create an interface and implementation
- Place interface in: `Modules/{Module}/app/Services/{ServiceName}Interface.php`
- Place implementation in: `Modules/{Module}/app/Services/{ServiceName}.php`
- Services validate business rules and throw exceptions with clear messages in Portuguese
- Example:
  ```php
  class BoardingService implements BoardingServiceInterface
  {
      public function __construct(
          protected BoardingRepositoryInterface $repository
      ) {}
      
      public function boardStudent(BoardingDto $data): Boarding
      {
          // Business logic validation
          if ($trip->status !== TripStatus::InProgress) {
              throw new \Exception('A viagem não está em progresso.');
          }
          
          return $this->repository->create($data);
      }
  }
  ```

#### 5. **Form Requests**
- Always use Form Request classes for validation, never inline validation in controllers
- Include both `rules()` and `messages()` methods
- Messages must be in Portuguese
- For DTOs, add a `toDto()` method to convert validated data
- Place in: `Modules/{Module}/app/Http/Requests/{ActionName}Request.php`
- Example:
  ```php
  class BoardRequest extends FormRequest
  {
      public function rules(): array { ... }
      public function messages(): array { ... }
      public function toDto(): BoardingDto { ... }
  }
  ```

#### 6. **Controllers**
- Controllers should be thin - only handle HTTP concerns
- Inject service interfaces via constructor using dependency injection
- Use try-catch to handle service exceptions and return appropriate HTTP responses
- Always use status codes from `Symfony\Component\HttpFoundation\Response`
- Use Resource classes to format responses
- Place in: `Modules/{Module}/app/Http/Controllers/{EntityName}Controller.php`
- Example:
  ```php
  class BoardingController extends Controller
  {
      public function __construct(protected BoardingServiceInterface $service) {}
      
      public function board(BoardRequest $request): JsonResponse
      {
          try {
              $boarding = $this->service->boardStudent($request->toDto());
              return BoardingResource::make($boarding)
                  ->response()
                  ->setStatusCode(StatusCode::HTTP_CREATED);
          } catch (\Exception $e) {
              return response()->json([
                  'message' => $e->getMessage(),
              ], StatusCode::HTTP_BAD_REQUEST);
          }
      }
  }
  ```

#### 7. **API Resources**
- Always use Eloquent API Resources to format JSON responses
- Use `whenLoaded()` for relationships to avoid N+1 queries
- Format dates consistently using `format()` method
- Place in: `Modules/{Module}/app/Http/Resources/{EntityName}Resource.php`

#### 8. **Service Providers**
- Create dedicated service providers for each major component (per entity or group)
- Register interfaces to implementations in the `register()` method
- Place in: `Modules/{Module}/app/Providers/{EntityName}ServiceProvider.php`
- Register the provider in the module's main service provider
- Example:
  ```php
  class BoardingServiceProvider extends ServiceProvider
  {
      public function register(): void
      {
          $this->app->bind(
              BoardingRepositoryInterface::class,
              BoardingRepository::class
          );
          $this->app->bind(
              BoardingServiceInterface::class,
              BoardingService::class
          );
      }
  }
  ```

### Models & Relationships

#### Model Conventions
- Use constructor property promotion in models when appropriate
- Always define explicit return types for relationships
- Use `protected function casts(): array` method for type casting
- Use `HasFactory` trait and implement `newFactory()` method
- Place in: `Modules/{Module}/app/Models/{EntityName}.php`

#### Factories
- Create factories for all models to support testing
- Factories should provide realistic default data
- Use factory states for variations (e.g., `landed()`, `active()`)
- Place in: `Modules/{Module}/database/factories/{EntityName}Factory.php`

#### Migrations
- Use Artisan to generate migrations with `--path` option for modules
- Always use foreign key constraints with appropriate `onDelete` actions
- Use `cascadeOnDelete()` for parent-child relationships
- Use appropriate column types (e.g., `timestamp` with `useCurrent()`)
- Place in: `Modules/{Module}/database/migrations/`

### Routes
- API routes should be versioned (e.g., `/api/v1/`)
- Group routes by resource and apply middleware appropriately
- Use named routes for all API endpoints
- Follow RESTful conventions when applicable
- Use permission middleware from `Modules\User\Enums\Permission` for authorization
- Place in: `Modules/{Module}/routes/api.php`

### Testing Strategy

#### Feature Tests
- Most tests should be feature tests that test the full request-response cycle
- Use `RefreshDatabase` trait to reset database between tests
- Use Laravel Sanctum's `Sanctum::actingAs()` for authentication
- Create helper methods for common setup (e.g., `createDriver()`, `createActiveTrip()`)
- Test all happy paths, failure paths, and edge cases
- Use descriptive test method names with `test_` prefix
- Place in: `Modules/{Module}/tests/Feature/{EntityName}Test.php`

#### Test Coverage Expectations
- Test successful operations (200/201 responses)
- Test validation failures (400 responses)
- Test authorization failures (403 responses)
- Test not found scenarios (404 responses)
- Test business rule validations
- Test relationships and side effects

### Exception Handling
- Throw exceptions with clear Portuguese messages in services
- Handle exceptions in controllers and return appropriate HTTP responses
- Use specific exception types when appropriate
- Always include descriptive error messages for the end user

### Cross-Module Communication
- Modules can reference models from other modules when necessary
- Use fully qualified class names for cross-module references
- Example: `\Modules\Operation\Models\Student` in Logistics module
- Keep cross-module dependencies minimal and explicit

### Development Workflow

#### When Creating New Features:
1. Create migration with `artisan make:migration --path=Modules/{Module}/database/migrations`
2. Create Model with factory
3. Define relationships in models
4. Create DTO implementing `DtoContract`
5. Create Repository interface and implementation
6. Create Service interface and implementation with business logic
7. Create Form Request(s) with validation rules and messages
8. Create Controller with thin methods
9. Create API Resource for response formatting
10. Create Service Provider and register bindings
11. Add routes in module's `routes/api.php`
12. Create comprehensive feature tests
13. Run migrations: `artisan migrate`
14. Run tests: `artisan test --filter={TestName}`
15. Format code: `vendor/bin/pint`

### Code Quality Standards
- Run `vendor/bin/pint` before committing changes
- Ensure all tests pass before finalizing features
- Write clear, descriptive commit messages
- Follow SOLID principles in service and repository design
- Keep methods focused on a single responsibility
- Use dependency injection extensively
- Error messages must be in Portuguese and user-friendly
