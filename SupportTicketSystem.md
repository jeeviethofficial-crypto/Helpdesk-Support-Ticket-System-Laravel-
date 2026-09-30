# Laravel Support Ticket System

This document outlines the complete implementation of the Role-Based Support Ticket System using Laravel 11/12, Tailwind CSS, and strict MVC architecture.

## 1. Terminal Setup Commands

Run these commands in your terminal to initialize the project, configure authentication, and set up the foundation.

```bash
# 1. Create a new Laravel project
composer create-project laravel/laravel support-ticket-system
cd support-ticket-system

# 2. Configure Database (SQLite for quick start)
# Open .env and set DB_CONNECTION=sqlite, then remove other DB_* variables.
touch database/database.sqlite

# 3. Install Laravel Breeze for Authentication (Blade stack)
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build

# 4. Generate Models, Migrations, Seeders, and Factories
php artisan make:model Category -mfs
php artisan make:model Ticket -mfs
php artisan make:model Comment -mfs

# 5. Generate Enums
php artisan make:enum Enums/UserRole --string
php artisan make:enum Enums/TicketStatus --string
php artisan make:enum Enums/TicketPriority --string

# 6. Generate Form Requests & Controllers
php artisan make:request StoreTicketRequest
php artisan make:request UpdateTicketRequest
php artisan make:controller TicketController --model=Ticket
php artisan make:request StoreCommentRequest
php artisan make:controller CommentController

# 7. Generate Policies
php artisan make:policy TicketPolicy --model=Ticket

# 8. Generate Events and queued Listeners
php artisan make:event TicketUpdated
php artisan make:listener SendTicketUpdatedNotification --event=TicketUpdated --queued
php artisan make:event CommentPosted
php artisan make:listener SendCommentPostedNotification --event=CommentPosted --queued
```

---

## 2. Enums

**`app/Enums/UserRole.php`**
```php
namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Agent = 'agent';
    case Customer = 'customer';
}
```

**`app/Enums/TicketStatus.php`**
```php
namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Closed = 'closed';
}
```

**`app/Enums/TicketPriority.php`**
```php
namespace App\Enums;

enum TicketPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
}
```

---

## 3. Database Migrations

**Create Users Table Modification (add role)**
```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('customer')->after('email');
    });
}
```

**Create Categories Table**
```php
public function up(): void
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique();
        $table->timestamps();
    });
}
```

**Create Tickets Table**
```php
public function up(): void
{
    Schema::create('tickets', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description');
        $table->string('status')->default('open')->index();
        $table->string('priority')->default('medium')->index();
        
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
        $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
        
        $table->timestamps();
    });
}
```

**Create Comments Table**
```php
public function up(): void
{
    Schema::create('comments', function (Blueprint $table) {
        $table->id();
        $table->text('body');
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
        $table->timestamps();
    });
}
```

---

## 4. Eloquent Models

**`app/Models/User.php`**
```php
namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $casts = [
        'password' => 'hashed',
        'role' => UserRole::class,
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function isAdmin(): bool { return $this->role === UserRole::Admin; }
    public function isAgent(): bool { return $this->role === UserRole::Agent; }
    public function isCustomer(): bool { return $this->role === UserRole::Customer; }
}
```

**`app/Models/Ticket.php`**
```php
namespace App\Models;

use App\Enums\TicketStatus;
use App\Enums\TicketPriority;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'status', 'priority', 'user_id', 'assigned_to', 'category_id'];

    protected $casts = [
        'status' => TicketStatus::class,
        'priority' => TicketPriority::class,
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function comments(): HasMany { return $this->hasMany(Comment::class); }
}
```

**`app/Models/Category.php`**
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'slug'];

    public function tickets(): HasMany { return $this->hasMany(Ticket::class); }
}
```

**`app/Models/Comment.php`**
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['body', 'user_id', 'ticket_id'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function ticket(): BelongsTo { return $this->belongsTo(Ticket::class); }
}
```

---

## 5. Seeders and Factories (For Demo Setup)

**`database/factories/CategoryFactory.php`**
```php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
        ];
    }
}
```

**`database/factories/TicketFactory.php`**
```php
namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use App\Enums\TicketStatus;
use App\Enums\TicketPriority;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraphs(3, true),
            'status' => $this->faker->randomElement(TicketStatus::cases())->value,
            'priority' => $this->faker->randomElement(TicketPriority::cases())->value,
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
        ];
    }
}
```

**`database/factories/CommentFactory.php`**
```php
namespace Database\Factories;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'body' => $this->faker->paragraph(),
            'user_id' => User::factory(),
            'ticket_id' => Ticket::factory(),
        ];
    }
}
```

**`database/seeders/DatabaseSeeder.php`**
```php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\Comment;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create specific users for testing
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => UserRole::Admin->value,
        ]);

        $agent = User::factory()->create([
            'name' => 'Agent Smith',
            'email' => 'agent@example.com',
            'role' => UserRole::Agent->value,
        ]);

        $customer = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'customer@example.com',
            'role' => UserRole::Customer->value,
        ]);

        // 2. Create standard categories
        $categories = collect(['Billing', 'Technical Support', 'Sales', 'General Inquiry'])->map(function ($name) {
            return Category::factory()->create(['name' => $name, 'slug' => str()->slug($name)]);
        });

        // 3. Generate random tickets for the customer
        Ticket::factory(10)->create([
            'user_id' => $customer->id,
            'category_id' => $categories->random()->id,
            'assigned_to' => $agent->id,
        ])->each(function ($ticket) use ($agent, $customer) {
            // Add some comments to each ticket
            Comment::factory(3)->create([
                'ticket_id' => $ticket->id,
                'user_id' => rand(0, 1) ? $customer->id : $agent->id,
            ]);
        });
    }
}
```
*(Run `php artisan migrate:fresh --seed` to populate the database for demo purposes)*

---

## 6. Form Requests

**`app/Http/Requests/StoreTicketRequest.php`**
```php
namespace App\Http\Requests;

use App\Enums\TicketPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'priority' => ['required', new Enum(TicketPriority::class)],
        ];
    }
}
```

**`app/Http/Requests/UpdateTicketRequest.php`**
```php
namespace App\Http\Requests;

use App\Enums\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $rules = [];
        if ($this->has('status')) $rules['status'] = ['required', new Enum(TicketStatus::class)];
        if ($this->has('assigned_to')) $rules['assigned_to'] = ['nullable', 'exists:users,id'];
        return $rules;
    }
}
```

**`app/Http/Requests/StoreCommentRequest.php`**
```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string'],
        ];
    }
}
```

---

## 7. Controllers (with Route Model Binding)

**`app/Http/Controllers/TicketController.php`**
```php
namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\User;
use App\Enums\UserRole;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Events\TicketUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Ticket::with(['category', 'user', 'assignee'])->latest();

        if ($user->isCustomer()) $query->where('user_id', $user->id);
        elseif ($user->isAgent()) $query->where('assigned_to', $user->id)->orWhereNull('assigned_to');

        $tickets = $query->paginate(10);
        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('tickets.create', compact('categories'));
    }

    public function store(StoreTicketRequest $request)
    {
        $ticket = $request->user()->tickets()->create($request->validated());
        return redirect()->route('tickets.show', $ticket)->with('status', 'Ticket created successfully.');
    }

    public function show(Ticket $ticket)
    {
        Gate::authorize('view', $ticket);
        
        $ticket->load(['user', 'category', 'assignee', 'comments.user']);
        $agents = User::where('role', UserRole::Agent)->get();

        return view('tickets.show', compact('ticket', 'agents'));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        Gate::authorize('update', $ticket);
        if ($request->has('assigned_to')) Gate::authorize('assign', $ticket);

        // Check if status actually changed
        $statusChanged = $request->has('status') && $ticket->status->value !== $request->status;

        $ticket->update($request->validated());

        if ($statusChanged) {
            TicketUpdated::dispatch($ticket);
        }

        return redirect()->route('tickets.show', $ticket)->with('status', 'Ticket updated successfully.');
    }
}
```

**`app/Http/Controllers/CommentController.php`**
```php
namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Http\Requests\StoreCommentRequest;
use App\Events\CommentPosted;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Ticket $ticket)
    {
        Gate::authorize('view', $ticket); // Ensure user can view the ticket

        $comment = $ticket->comments()->create([
            'body' => $request->body,
            'user_id' => $request->user()->id,
        ]);

        // Dispatch queued event
        CommentPosted::dispatch($comment);

        return redirect()->route('tickets.show', $ticket)->with('status', 'Comment posted.');
    }
}
```

---

## 8. Authorization (Policies)

**`app/Policies/TicketPolicy.php`**
```php
namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool { return true; }

    public function view(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin() || $user->isAgent() || $user->id === $ticket->user_id;
    }

    public function create(User $user): bool { return true; }

    public function update(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin() || $user->isAgent();
    }
    
    public function assign(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin();
    }
}
```

---

## 9. Queued Events & Listeners

Ensure `.env` has `QUEUE_CONNECTION=database`. Run `php artisan queue:table` and `php artisan migrate`.

**`app/Events/TicketUpdated.php`** & **`app/Events/CommentPosted.php`**
```php
namespace App\Events;
use App\Models\Ticket;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketUpdated
{
    use Dispatchable, SerializesModels;
    public function __construct(public Ticket $ticket) {}
}

// In app/Events/CommentPosted.php
namespace App\Events;
use App\Models\Comment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentPosted
{
    use Dispatchable, SerializesModels;
    public function __construct(public Comment $comment) {}
}
```

**`app/Listeners/SendTicketUpdatedNotification.php`**
```php
namespace App\Listeners;
use App\Events\TicketUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendTicketUpdatedNotification implements ShouldQueue
{
    public function handle(TicketUpdated $event): void
    {
        $ticket = $event->ticket;
        Log::info("Ticket #{$ticket->id} status changed to {$ticket->status->value}. Notify User #{$ticket->user_id}.");
    }
}
```

**`app/Listeners/SendCommentPostedNotification.php`**
```php
namespace App\Listeners;
use App\Events\CommentPosted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendCommentPostedNotification implements ShouldQueue
{
    public function handle(CommentPosted $event): void
    {
        $comment = $event->comment;
        $ticket = $comment->ticket;
        Log::info("New comment on Ticket #{$ticket->id} by User #{$comment->user_id}. Notify stakeholders.");
    }
}
```

---

## 10. Web Routes (`routes/web.php`)

```php
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
    Route::resource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/comments', [CommentController::class, 'store'])->name('comments.store');
});

require __DIR__.'/auth.php';
```

---

*(Blade views remain exactly the same as previously defined, leveraging full Tailwind CSS and standard Laravel Breeze structures).*
