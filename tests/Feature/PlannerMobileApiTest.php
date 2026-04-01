<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions; // <--- This protects your data
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;

class PlannerMobileApiTest extends TestCase
{
    // This ensures data created here is rolled back at the end.
    // It does NOT wipe the database.
    use DatabaseTransactions; 

    protected $employee;
    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // 1. Create a Test Employee
        // We use insertGetId to ensure we get a fresh ID and don't conflict with existing data
        $empId = DB::table('employees')->insertGetId([
            'title' => 'Test Tech',
            'name' => 'Mobile',
            'lastname' => 'Tester',
            'status' => 'Active',
            // Passcode is hashed, simulating what your app should do
            'passcode' => Hash::make('1234'), 
            'email' => 'mobile.test.' . rand(1000,9999) . '@example.com',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        $this->employee = Employee::find($empId);

        // 2. Create/Link User for Sanctum
        // We check if a user exists first to be safe, though insertGetId guarantees unique employee
        $this->user = User::create([
            'name' => (string) $this->employee->id, 
            'email' => $this->employee->email,
            'password' => Hash::make('password'),
        ]);
    }

    /** @test */
    public function it_allows_login_with_correct_pin()
    {
        $response = $this->postJson('/api/planner/auth/login', [
            'employee_id' => $this->employee->id,
            'passcode' => '1234',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['ok' => true]);
                 
        $this->assertArrayHasKey('token', $response->json());
    }

    /** @test */
    public function it_denies_login_with_wrong_pin()
    {
        $response = $this->postJson('/api/planner/auth/login', [
            'employee_id' => $this->employee->id,
            'passcode' => '0000',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['ok' => false]);
    }

    /** @test */
    public function it_fetches_my_tasks()
    {
        // 1. Create Plan
        $planId = DB::table('planner_plans')->insertGetId([
            'customer_id' => 1, // Assumes at least one customer exists, usually safe
            'stage' => 'Test', 
            'title' => 'Mobile Test Plan',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 2. Create Item
        $itemId = DB::table('planner_items')->insertGetId([
            'plan_id' => $planId,
            'title' => 'Mobile Task Verify',
            'status' => 'open',
            // Ensure date matches the "Today" logic in controller
            'planned_start_at' => Carbon::now()->format('Y-m-d 08:00:00'), 
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 3. Assign Employee (Pivot)
        DB::table('planner_item_employees')->insert([
            'planner_item_id' => $itemId,
            'employee_id' => $this->employee->id,
            'role' => 'lead',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 4. Call API
        $response = $this->actingAs($this->user)
                         ->getJson('/api/planner/tasks/my');

        $response->assertStatus(200)
                 ->assertJsonPath('ok', true);
                 
        // Verify we see the task title in the output
        $this->assertTrue(
            collect($response->json('tasks'))->contains('title', 'Mobile Task Verify'),
            "The created task was not returned in the API response."
        );
    }

    /** @test */
    public function it_updates_task_status()
    {
        // Setup Item
        $planId = DB::table('planner_plans')->insertGetId(['customer_id'=>1, 'title'=>'S', 'created_at'=>now()]);
        $itemId = DB::table('planner_items')->insertGetId([
            'plan_id' => $planId, 
            'title' => 'Status Task', 
            'status' => 'open',
            'created_at' => now()
        ]);

        // Call API
        $response = $this->actingAs($this->user)
                         ->postJson('/api/planner/tasks/status', [
            'item_id' => $itemId,
            'status' => 'start',
            'lat' => 50.0,
            'lng' => 10.0
        ]);

        $response->assertStatus(200)
                 ->assertJson(['ok' => true]);

        // Check Database directly
        $this->assertDatabaseHas('planner_items', [
            'id' => $itemId,
            'status' => 'in_progress' // Controller maps 'start' -> 'in_progress'
        ]);
    }
}