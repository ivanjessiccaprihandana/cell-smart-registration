# Program Feature Documentation

## Overview
Program feature is completely separated from the Authentication system. It manages user enrollment into various programs/courses.

## Database Structure

### Programs Table
- `id` (primary key)
- `name` (string)
- `description` (text, nullable)
- `category` (string, nullable) 
- `start_date` (datetime, nullable)
- `end_date` (datetime, nullable)
- `status` (enum: draft, active, inactive, completed)
- `created_at` & `updated_at`

### Program Enrollments Table
- `id` (primary key)
- `user_id` (foreign key → users)
- `program_id` (foreign key → programs)
- `enrolled_at` (datetime)
- `status` (enum: pending, active, completed, rejected)
- `created_at` & `updated_at`
- **Unique Constraint**: (user_id, program_id) - prevents duplicate enrollments

## Models

### Program Model
```php
// Relationships
$program->users() // HasMany ProgramEnrollments
$program->isActive() // Check if program is currently active
```

### ProgramEnrollment Model
```php
// Relationships
$enrollment->user() // BelongsTo User
$enrollment->program() // BelongsTo Program

// Scopes
$query->active() // Get active enrollments
$query->pending() // Get pending enrollments
```

### User Model (Updated)
```php
// Relationships
$user->programEnrollments() // HasMany
$user->programs() // BelongsToMany
$user->activePrograms() // Active enrollments only
```

## API Endpoints

### Public Endpoints
- `GET /programs` - List all active programs
- `GET /programs/{program}` - Show program details
- `GET /programs/{program}/participants` - List program participants

### Protected Endpoints (Requires Auth)
- `POST /programs/{program}/join` - Enroll in program
- `POST /programs/{program}/leave` - Leave program
- `GET /programs/my-programs` - Get user's enrolled programs

### Admin Endpoints (Requires Auth + Admin)
- `PUT /programs/{enrollment}/approve` - Approve enrollment
- `DELETE /programs/{enrollment}/reject` - Reject enrollment

## Usage Examples

### Get All Programs
```php
$programs = Program::where('status', 'active')->get();
```

### User Joins Program
```php
ProgramEnrollment::create([
    'user_id' => auth()->id(),
    'program_id' => $program->id,
    'enrolled_at' => now(),
    'status' => 'pending'
]);
```

### Get User's Programs
```php
$user = Auth::user();
$programs = $user->programs()->where('status', 'active')->get();
```

### Get Program Participants
```php
$participants = $program->users()
    ->where('status', 'active')
    ->get();
```

## Testing
Use `ProgramFactory` to create test programs:
```php
$program = Program::factory()->create();
$program = Program::factory()->inactive()->create();
$program = Program::factory()->completed()->create();
```

Populate test data:
```bash
php artisan db:seed --class=ProgramSeeder
```

## Next Steps
1. Run migrations: `php artisan migrate`
2. Seed programs: `php artisan db:seed --class=ProgramSeeder`
3. Create frontend components for:
   - Program list view
   - Program detail page
   - Join/Leave buttons
   - User's programs dashboard
4. Add authorization policies (ProgramPolicy, EnrollmentPolicy)
5. Add rate limiting for join endpoints
6. Add email notifications for enrollments
