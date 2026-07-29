<?php

namespace Tests\Feature\GoalObjective;

use App\Models\College;
use App\Models\Department;
use App\Models\DepartmentObjective;
use App\Models\Role;
use App\Models\User;
use App\Models\UserAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

// Covers ObjectiveController: objective_index(), objective_store(), objective_update(), objective_destroy()
//
// Groups:
//   A. objective_index  - page rendering & college/department-selection logic
//   B. objective_store  - validation, authorization, creation & code assignment
//   C. objective_update - validation, authorization, text update
//   D. objective_destroy - authorization, deletion & code resequencing
//   E. Access control   - guests and unauthorized roles are blocked
class ObjectiveControllerTest extends TestCase
{
    use RefreshDatabase;

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

    private function makeDepartment(College $college, string $name = 'Test Department'): Department
    {
        return Department::create(['college_id' => $college->id, 'name' => $name]);
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

    private function makeObjective(Department $dept, string $code, string $text = 'A sample objective.'): DepartmentObjective
    {
        return DepartmentObjective::create([
            'department_id'  => $dept->id,
            'dept_obj_code'  => $code,
            'objective_text' => $text,
        ]);
    }

    // -- A. objective_index ----------------------------------------------------

    #[Test]
    public function admin_can_view_objective_index(): void
    {
        $admin = $this->makeUser('admin');

        $response = $this->actingAs($admin)->get(route('objective.index'));

        $response->assertOk();
        $response->assertSee('Department Objectives');
    }

    #[Test]
    public function admin_can_filter_objectives_by_college_and_department(): void
    {
        $admin      = $this->makeUser('admin');
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);
        $this->makeObjective($department, 'a', 'Target objective');

        $response = $this->actingAs($admin)->get(route('objective.index', [
            'college_id'    => $college->id,
            'department_id' => $department->id,
        ]));

        $response->assertOk();
        $response->assertSee('Target objective');
    }

    #[Test]
    public function admin_index_shows_empty_state_when_department_has_no_objectives(): void
    {
        $admin      = $this->makeUser('admin');
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);

        $response = $this->actingAs($admin)->get(route('objective.index', [
            'college_id'    => $college->id,
            'department_id' => $department->id,
        ]));

        $response->assertOk();
        $response->assertSee('No objectives yet');
    }

    #[Test]
    public function chair_with_assignment_auto_selects_their_department(): void
    {
        $chair      = $this->makeUser('chair');
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college, 'Chair Dept');
        $this->assignChair($chair, $department);
        $this->makeObjective($department, 'a', 'Chair dept objective');

        $response = $this->actingAs($chair)->get(route('objective.index'));

        $response->assertOk();
        $response->assertSee('Chair dept objective');
    }

    #[Test]
    public function chair_without_assignment_sees_no_assignment_warning(): void
    {
        $chair = $this->makeUser('chair');

        $response = $this->actingAs($chair)->get(route('objective.index'));

        $response->assertOk();
        $response->assertSee('No department assigned');
    }

    #[Test]
    public function index_shows_no_college_selected_state_when_no_college_given(): void
    {
        $admin = $this->makeUser('admin');
        // No colleges exist, so admin sees no-college-selected empty state.
        $response = $this->actingAs($admin)->get(route('objective.index'));

        $response->assertOk();
    }

    // -- B. objective_store ----------------------------------------------------

    #[Test]
    public function admin_can_store_a_new_objective(): void
    {
        $admin      = $this->makeUser('admin');
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);

        $response = $this->actingAs($admin)->post(route('objective.store'), [
            'college_id'     => $college->id,
            'department_id'  => $department->id,
            'objective_text' => 'Develop critical thinking skills.',
        ]);

        $response->assertRedirect(route('objective.index', [
            'college_id'    => $college->id,
            'department_id' => $department->id,
        ]));
        $this->assertDatabaseHas('department_objectives', [
            'department_id'  => $department->id,
            'objective_text' => 'Develop critical thinking skills.',
            'dept_obj_code'  => 'a',
        ]);
    }

    #[Test]
    public function chair_can_store_objective_for_their_department(): void
    {
        $chair      = $this->makeUser('chair');
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);
        $this->assignChair($chair, $department);

        $response = $this->actingAs($chair)->post(route('objective.store'), [
            'college_id'     => $college->id,
            'department_id'  => $department->id,
            'objective_text' => 'Improve research output.',
        ]);

        $response->assertRedirect(route('objective.index', [
            'college_id'    => $college->id,
            'department_id' => $department->id,
        ]));
        $this->assertDatabaseHas('department_objectives', ['objective_text' => 'Improve research output.']);
    }

    #[Test]
    public function objective_codes_are_assigned_sequentially_on_store(): void
    {
        $admin      = $this->makeUser('admin');
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);

        foreach (['First', 'Second', 'Third'] as $text) {
            $this->actingAs($admin)->post(route('objective.store'), [
                'college_id'     => $college->id,
                'department_id'  => $department->id,
                'objective_text' => $text,
            ]);
        }

        $codes = DepartmentObjective::where('department_id', $department->id)
            ->orderBy('id')
            ->pluck('dept_obj_code')
            ->toArray();

        $this->assertSame(['a', 'b', 'c'], $codes);
    }

    #[Test]
    public function store_fails_validation_when_objective_text_is_missing(): void
    {
        $admin      = $this->makeUser('admin');
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);

        $response = $this->actingAs($admin)->post(route('objective.store'), [
            'college_id'    => $college->id,
            'department_id' => $department->id,
        ]);

        $response->assertSessionHasErrors(['objective_text']);
        $this->assertDatabaseCount('department_objectives', 0);
    }

    #[Test]
    public function store_fails_validation_when_department_id_is_missing(): void
    {
        $admin   = $this->makeUser('admin');
        $college = $this->makeCollege();

        $response = $this->actingAs($admin)->post(route('objective.store'), [
            'college_id'     => $college->id,
            'objective_text' => 'Some text',
        ]);

        $response->assertSessionHasErrors(['department_id']);
    }

    #[Test]
    public function store_fails_validation_when_department_does_not_belong_to_college(): void
    {
        $admin      = $this->makeUser('admin');
        $college1   = $this->makeCollege('College One');
        $college2   = $this->makeCollege('College Two');
        $department = $this->makeDepartment($college2); // belongs to college2, not college1

        $response = $this->actingAs($admin)->post(route('objective.store'), [
            'college_id'     => $college1->id,
            'department_id'  => $department->id,
            'objective_text' => 'Cross-college attempt',
        ]);

        $response->assertSessionHasErrors(['department_id']);
        $this->assertDatabaseCount('department_objectives', 0);
    }

    #[Test]
    public function chair_cannot_store_objective_for_another_department(): void
    {
        $chair       = $this->makeUser('chair');
        $college     = $this->makeCollege();
        $myDept      = $this->makeDepartment($college, 'My Dept');
        $otherDept   = $this->makeDepartment($college, 'Other Dept');
        $this->assignChair($chair, $myDept);

        $response = $this->actingAs($chair)->post(route('objective.store'), [
            'college_id'     => $college->id,
            'department_id'  => $otherDept->id,
            'objective_text' => 'Unauthorized objective.',
        ]);

        $response->assertRedirect(route('objective.index'));
        $response->assertSessionHas('toast.type', 'warning');
        $this->assertDatabaseMissing('department_objectives', ['objective_text' => 'Unauthorized objective.']);
    }

    // -- C. objective_update ---------------------------------------------------

    #[Test]
    public function admin_can_update_an_objective(): void
    {
        $admin      = $this->makeUser('admin');
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);
        $objective  = $this->makeObjective($department, 'a', 'Old objective text.');

        $response = $this->actingAs($admin)->put(route('objective.update', $objective->id), [
            'objective_text' => 'Updated objective text.',
        ]);

        $response->assertRedirect(route('objective.index', [
            'college_id'    => $college->id,
            'department_id' => $department->id,
        ]));
        $this->assertDatabaseHas('department_objectives', [
            'id'             => $objective->id,
            'objective_text' => 'Updated objective text.',
        ]);
    }

    #[Test]
    public function chair_can_update_objective_for_their_department(): void
    {
        $chair      = $this->makeUser('chair');
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);
        $this->assignChair($chair, $department);
        $objective  = $this->makeObjective($department, 'a', 'Old text.');

        $response = $this->actingAs($chair)->put(route('objective.update', $objective->id), [
            'objective_text' => 'New chair text.',
        ]);

        $response->assertRedirect(route('objective.index', [
            'college_id'    => $college->id,
            'department_id' => $department->id,
        ]));
        $this->assertDatabaseHas('department_objectives', ['objective_text' => 'New chair text.']);
    }

    #[Test]
    public function update_fails_validation_when_objective_text_is_missing(): void
    {
        $admin      = $this->makeUser('admin');
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);
        $objective  = $this->makeObjective($department, 'a', 'Original.');

        $response = $this->actingAs($admin)->put(route('objective.update', $objective->id), []);

        $response->assertSessionHasErrors(['objective_text']);
        $this->assertDatabaseHas('department_objectives', ['objective_text' => 'Original.']);
    }

    #[Test]
    public function chair_cannot_update_objective_of_another_department(): void
    {
        $chair     = $this->makeUser('chair');
        $college   = $this->makeCollege();
        $myDept    = $this->makeDepartment($college, 'Mine');
        $otherDept = $this->makeDepartment($college, 'Other');
        $this->assignChair($chair, $myDept);
        $objective = $this->makeObjective($otherDept, 'a', 'Protected objective.');

        $response = $this->actingAs($chair)->put(route('objective.update', $objective->id), [
            'objective_text' => 'Tampered.',
        ]);

        $response->assertRedirect(route('objective.index'));
        $response->assertSessionHas('toast.type', 'warning');
        $this->assertDatabaseMissing('department_objectives', ['objective_text' => 'Tampered.']);
    }

    // -- D. objective_destroy --------------------------------------------------

    #[Test]
    public function admin_can_delete_an_objective(): void
    {
        $admin      = $this->makeUser('admin');
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);
        $objective  = $this->makeObjective($department, 'a', 'To be deleted.');

        $response = $this->actingAs($admin)->delete(route('objective.destroy', $objective->id));

        $response->assertRedirect(route('objective.index', [
            'college_id'    => $college->id,
            'department_id' => $department->id,
        ]));
        $response->assertSessionHas('toast.type', 'success');
        $this->assertDatabaseMissing('department_objectives', ['id' => $objective->id]);
    }

    #[Test]
    public function deleting_an_objective_resequences_remaining_codes(): void
    {
        $admin      = $this->makeUser('admin');
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);
        $objA       = $this->makeObjective($department, 'a', 'Obj A');
        $objB       = $this->makeObjective($department, 'b', 'Obj B');
        $objC       = $this->makeObjective($department, 'c', 'Obj C');

        // Delete the middle objective
        $this->actingAs($admin)->delete(route('objective.destroy', $objB->id));

        $codes = DepartmentObjective::where('department_id', $department->id)
            ->orderBy('id')
            ->pluck('dept_obj_code')
            ->toArray();

        $this->assertSame(['a', 'b'], $codes, 'Remaining objectives should be resequenced to a, b');
        $this->assertDatabaseMissing('department_objectives', ['id' => $objB->id]);
    }

    #[Test]
    public function deleting_the_only_objective_leaves_no_objectives(): void
    {
        $admin      = $this->makeUser('admin');
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);
        $objective  = $this->makeObjective($department, 'a');

        $this->actingAs($admin)->delete(route('objective.destroy', $objective->id));

        $this->assertDatabaseCount('department_objectives', 0);
    }

    #[Test]
    public function chair_cannot_delete_objective_of_another_department(): void
    {
        $chair     = $this->makeUser('chair');
        $college   = $this->makeCollege();
        $myDept    = $this->makeDepartment($college, 'Mine');
        $otherDept = $this->makeDepartment($college, 'Other');
        $this->assignChair($chair, $myDept);
        $objective = $this->makeObjective($otherDept, 'a', 'Protected objective.');

        $response = $this->actingAs($chair)->delete(route('objective.destroy', $objective->id));

        $response->assertRedirect(route('objective.index'));
        $response->assertSessionHas('toast.type', 'warning');
        $this->assertDatabaseHas('department_objectives', ['id' => $objective->id]);
    }

    // -- E. Access control -----------------------------------------------------

    #[Test]
    public function guest_cannot_access_objective_index(): void
    {
        $this->get(route('objective.index'))->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_post_to_objective_store(): void
    {
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);

        $this->post(route('objective.store'), [
            'college_id'     => $college->id,
            'department_id'  => $department->id,
            'objective_text' => 'Sneaky',
        ])->assertRedirect(route('login'));
    }

    #[Test]
    public function faculty_role_is_forbidden_from_objective_index(): void
    {
        $faculty = $this->makeUser('faculty');

        $this->actingAs($faculty)->get(route('objective.index'))->assertForbidden();
    }

    #[Test]
    public function dean_role_is_forbidden_from_objective_store(): void
    {
        $dean       = $this->makeUser('dean');
        $college    = $this->makeCollege();
        $department = $this->makeDepartment($college);

        $this->actingAs($dean)->post(route('objective.store'), [
            'college_id'     => $college->id,
            'department_id'  => $department->id,
            'objective_text' => 'Dean should not post here',
        ])->assertForbidden();
    }
}
