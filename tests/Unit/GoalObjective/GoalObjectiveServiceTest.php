<?php

namespace Tests\Unit\GoalObjective;

use App\Models\College;
use App\Models\CollegeGoal;
use App\Models\Department;
use App\Models\DepartmentObjective;
use App\Models\Role;
use App\Models\User;
use App\Models\UserAssignment;
use App\Services\GoalObjectiveService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

// Covers GoalObjectiveService:
//   getAccessibleGoalColleges(), getAccessibleObjectiveColleges(), getAccessibleDepartments()
//   canManageGoal(), canManageObjective()
//   storeGoal(), updateGoal(), destroyGoal()
//   storeObjective(), updateObjective(), destroyObjective()
//
// Groups:
//   A. Access helpers (getAccessible*)
//   B. Authorization helpers (canManage*)
//   C. Goal CRUD with code generation & resequencing
//   D. Objective CRUD with code generation & resequencing
class GoalObjectiveServiceTest extends TestCase
{
    use RefreshDatabase;

    private GoalObjectiveService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GoalObjectiveService();
    }

    // -- Helpers ---------------------------------------------------------------

    private function makeRole(string $name): Role
    {
        return Role::firstOrCreate(['name' => $name]);
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create(['account_status' => 'active']);
        $user->roles()->attach($this->makeRole($role));
        return $user;
    }

    private function makeCollege(string $name = 'Test College'): College
    {
        return College::create(['name' => $name]);
    }

    private function makeDepartment(College $college, string $name = 'Test Dept'): Department
    {
        return Department::create(['college_id' => $college->id, 'name' => $name]);
    }

    private function assignDean(User $user, College $college): UserAssignment
    {
        return UserAssignment::create([
            'user_id'    => $user->id,
            'college_id' => $college->id,
            'context'    => 'dean',
        ]);
    }

    private function assignChair(User $user, Department $department): UserAssignment
    {
        return UserAssignment::create([
            'user_id'       => $user->id,
            'department_id' => $department->id,
            'college_id'    => $department->college_id,
            'context'       => 'chair',
        ]);
    }

    private function makeGoal(College $college, string $code, string $text = 'Goal text'): CollegeGoal
    {
        return CollegeGoal::create([
            'college_id'         => $college->id,
            'college_goals_code' => $code,
            'goal_text'          => $text,
        ]);
    }

    private function makeObjective(Department $dept, string $code, string $text = 'Obj text'): DepartmentObjective
    {
        return DepartmentObjective::create([
            'department_id'  => $dept->id,
            'dept_obj_code'  => $code,
            'objective_text' => $text,
        ]);
    }

    // -- A. Access helpers -----------------------------------------------------

    #[Test]
    public function getAccessibleGoalColleges_returns_assigned_college_for_dean_with_assignment(): void
    {
        $dean    = $this->makeUser('dean');
        $college = $this->makeCollege('My College');
        $this->assignDean($dean, $college);

        $result = $this->service->getAccessibleGoalColleges($dean);

        $this->assertCount(1, $result);
        $this->assertEquals($college->id, $result->first()->id);
    }

    #[Test]
    public function getAccessibleGoalColleges_returns_all_colleges_for_dean_without_assignment(): void
    {
        $dean = $this->makeUser('dean');
        $this->makeCollege('College A');
        $this->makeCollege('College B');

        $result = $this->service->getAccessibleGoalColleges($dean);

        $this->assertCount(2, $result);
    }

    #[Test]
    public function getAccessibleGoalColleges_returns_empty_for_non_dean_without_assignment(): void
    {
        // A user with no dean role and no assignment (e.g., faculty)
        $faculty = $this->makeUser('faculty');
        $this->makeCollege();

        $result = $this->service->getAccessibleGoalColleges($faculty);

        $this->assertCount(0, $result);
    }

    #[Test]
    public function getAccessibleObjectiveColleges_returns_assigned_college_for_chair_with_assignment(): void
    {
        $chair      = $this->makeUser('chair');
        $college    = $this->makeCollege('Chair College');
        $department = $this->makeDepartment($college);
        $this->assignChair($chair, $department);

        $result = $this->service->getAccessibleObjectiveColleges($chair);

        $this->assertCount(1, $result);
        $this->assertEquals($college->id, $result->first()->id);
    }

    #[Test]
    public function getAccessibleObjectiveColleges_returns_all_colleges_for_chair_without_assignment(): void
    {
        $chair = $this->makeUser('chair');
        $this->makeCollege('A');
        $this->makeCollege('B');

        $result = $this->service->getAccessibleObjectiveColleges($chair);

        $this->assertCount(2, $result);
    }

    #[Test]
    public function getAccessibleDepartments_returns_only_assigned_department_for_chair_with_assignment(): void
    {
        $chair      = $this->makeUser('chair');
        $college    = $this->makeCollege();
        $myDept     = $this->makeDepartment($college, 'Mine');
        $otherDept  = $this->makeDepartment($college, 'Other');
        $this->assignChair($chair, $myDept);

        $result = $this->service->getAccessibleDepartments($chair, $college->id);

        $ids = $result->pluck('id')->toArray();
        $this->assertContains($myDept->id, $ids);
        $this->assertNotContains($otherDept->id, $ids);
    }

    // -- B. Authorization helpers ----------------------------------------------

    #[Test]
    public function canManageGoal_returns_true_for_admin(): void
    {
        $admin   = $this->makeUser('admin');
        $college = $this->makeCollege();

        $this->assertTrue($this->service->canManageGoal($admin, $college));
    }

    #[Test]
    public function canManageGoal_returns_true_for_dean_assigned_to_that_college(): void
    {
        $dean    = $this->makeUser('dean');
        $college = $this->makeCollege();
        $this->assignDean($dean, $college);

        $this->assertTrue($this->service->canManageGoal($dean, $college));
    }

    #[Test]
    public function canManageGoal_returns_false_for_dean_assigned_to_different_college(): void
    {
        $dean         = $this->makeUser('dean');
        $myCollege    = $this->makeCollege('Mine');
        $otherCollege = $this->makeCollege('Other');
        $this->assignDean($dean, $myCollege);

        $this->assertFalse($this->service->canManageGoal($dean, $otherCollege));
    }

    #[Test]
    public function canManageGoal_returns_true_for_unassigned_dean(): void
    {
        // An unassigned dean falls through to hasRole('dean') check and returns true
        $dean    = $this->makeUser('dean');
        $college = $this->makeCollege();

        $this->assertTrue($this->service->canManageGoal($dean, $college));
    }

    #[Test]
    public function canManageObjective_returns_true_for_admin(): void
    {
        $admin      = $this->makeUser('admin');
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);

        $this->assertTrue($this->service->canManageObjective($admin, $department));
    }

    #[Test]
    public function canManageObjective_returns_true_for_chair_assigned_to_that_department(): void
    {
        $chair      = $this->makeUser('chair');
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);
        $this->assignChair($chair, $department);

        $this->assertTrue($this->service->canManageObjective($chair, $department));
    }

    #[Test]
    public function canManageObjective_returns_false_for_chair_assigned_to_different_department(): void
    {
        $chair     = $this->makeUser('chair');
        $college   = $this->makeCollege();
        $myDept    = $this->makeDepartment($college, 'Mine');
        $otherDept = $this->makeDepartment($college, 'Other');
        $this->assignChair($chair, $myDept);

        $this->assertFalse($this->service->canManageObjective($chair, $otherDept));
    }

    // -- C. Goal CRUD ----------------------------------------------------------

    #[Test]
    public function storeGoal_creates_goal_with_correct_code_and_text(): void
    {
        $college = $this->makeCollege();

        $goal = $this->service->storeGoal($college, 'Enhance faculty development.');

        $this->assertInstanceOf(CollegeGoal::class, $goal);
        $this->assertEquals($college->id, $goal->college_id);
        $this->assertEquals('a', $goal->college_goals_code);
        $this->assertEquals('Enhance faculty development.', $goal->goal_text);
        $this->assertDatabaseHas('college_goals', ['id' => $goal->id]);
    }

    #[Test]
    public function storeGoal_assigns_b_as_second_code(): void
    {
        $college = $this->makeCollege();
        $this->makeGoal($college, 'a');

        $second = $this->service->storeGoal($college, 'Second goal text.');

        $this->assertEquals('b', $second->college_goals_code);
    }

    #[Test]
    public function updateGoal_changes_goal_text(): void
    {
        $college = $this->makeCollege();
        $goal    = $this->makeGoal($college, 'a', 'Old text.');

        $this->service->updateGoal($goal, 'New text.');

        $this->assertDatabaseHas('college_goals', [
            'id'        => $goal->id,
            'goal_text' => 'New text.',
        ]);
    }

    #[Test]
    public function destroyGoal_removes_the_goal_from_database(): void
    {
        $college = $this->makeCollege();
        $goal    = $this->makeGoal($college, 'a');

        $this->service->destroyGoal($goal);

        $this->assertDatabaseMissing('college_goals', ['id' => $goal->id]);
    }

    #[Test]
    public function destroyGoal_resequences_remaining_goals(): void
    {
        $college = $this->makeCollege();
        $goalA   = $this->makeGoal($college, 'a', 'A');
        $goalB   = $this->makeGoal($college, 'b', 'B');
        $goalC   = $this->makeGoal($college, 'c', 'C');

        $this->service->destroyGoal($goalB);

        $codes = CollegeGoal::where('college_id', $college->id)
            ->orderBy('id')
            ->pluck('college_goals_code')
            ->toArray();

        $this->assertSame(['a', 'b'], $codes);
    }

    // -- D. Objective CRUD -----------------------------------------------------

    #[Test]
    public function storeObjective_creates_objective_with_correct_code_and_text(): void
    {
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);

        $objective = $this->service->storeObjective($department, 'Foster collaboration.');

        $this->assertInstanceOf(DepartmentObjective::class, $objective);
        $this->assertEquals($department->id, $objective->department_id);
        $this->assertEquals('a', $objective->dept_obj_code);
        $this->assertEquals('Foster collaboration.', $objective->objective_text);
        $this->assertDatabaseHas('department_objectives', ['id' => $objective->id]);
    }

    #[Test]
    public function storeObjective_assigns_b_as_second_code(): void
    {
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);
        $this->makeObjective($department, 'a');

        $second = $this->service->storeObjective($department, 'Second objective.');

        $this->assertEquals('b', $second->dept_obj_code);
    }

    #[Test]
    public function updateObjective_changes_objective_text(): void
    {
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);
        $objective  = $this->makeObjective($department, 'a', 'Old objective.');

        $this->service->updateObjective($objective, 'New objective text.');

        $this->assertDatabaseHas('department_objectives', [
            'id'             => $objective->id,
            'objective_text' => 'New objective text.',
        ]);
    }

    #[Test]
    public function destroyObjective_removes_the_objective_from_database(): void
    {
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);
        $objective  = $this->makeObjective($department, 'a');

        $this->service->destroyObjective($objective);

        $this->assertDatabaseMissing('department_objectives', ['id' => $objective->id]);
    }

    #[Test]
    public function destroyObjective_resequences_remaining_objectives(): void
    {
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);
        $objA       = $this->makeObjective($department, 'a', 'A');
        $objB       = $this->makeObjective($department, 'b', 'B');
        $objC       = $this->makeObjective($department, 'c', 'C');

        $this->service->destroyObjective($objB);

        $codes = DepartmentObjective::where('department_id', $department->id)
            ->orderBy('id')
            ->pluck('dept_obj_code')
            ->toArray();

        $this->assertSame(['a', 'b'], $codes);
    }
}
