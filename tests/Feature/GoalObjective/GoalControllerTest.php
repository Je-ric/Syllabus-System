<?php

namespace Tests\Feature\GoalObjective;

use App\Models\College;
use App\Models\CollegeGoal;
use App\Models\Role;
use App\Models\User;
use App\Models\UserAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

// Covers GoalController: goal_index(), goal_store(), goal_update(), goal_destroy()
//
// Groups:
//   A. goal_index  - page rendering & college-selection logic
//   B. goal_store  - validation, authorization, creation & code assignment
//   C. goal_update - validation, authorization, text update
//   D. goal_destroy - authorization, deletion & code resequencing
//   E. Access control - guests and unauthorized roles are blocked
class GoalControllerTest extends TestCase
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

    private function makeCollege(string $name = 'College of Engineering'): College
    {
        return College::create(['name' => $name]);
    }

    private function assignDean(User $user, College $college): UserAssignment
    {
        return UserAssignment::create([
            'user_id'    => $user->id,
            'college_id' => $college->id,
            'context'    => 'dean',
        ]);
    }

    private function makeGoal(College $college, string $code, string $text = 'A sample goal.'): CollegeGoal
    {
        return CollegeGoal::create([
            'college_id'         => $college->id,
            'college_goals_code' => $code,
            'goal_text'          => $text,
        ]);
    }

    // -- A. goal_index ---------------------------------------------------------

    #[Test]
    public function admin_can_view_goal_index_and_sees_all_colleges(): void
    {
        $admin   = $this->makeUser('admin');
        $college = $this->makeCollege('College A');
        $this->makeGoal($college, 'a', 'First goal');

        $response = $this->actingAs($admin)->get(route('goal.index'));

        $response->assertOk();
        $response->assertSee('College Goals');
        $response->assertSee('College A');
    }

    #[Test]
    public function admin_index_auto_selects_first_college_when_none_specified(): void
    {
        $admin   = $this->makeUser('admin');
        $college = $this->makeCollege('Only College');
        $this->makeGoal($college, 'a', 'Goal one');

        $response = $this->actingAs($admin)->get(route('goal.index'));

        $response->assertOk();
        $response->assertSee('Goal one');
    }

    #[Test]
    public function admin_can_filter_goals_by_college_id(): void
    {
        $admin    = $this->makeUser('admin');
        $college1 = $this->makeCollege('College One');
        $college2 = $this->makeCollege('College Two');
        $this->makeGoal($college1, 'a', 'Goal for College One');
        $this->makeGoal($college2, 'a', 'Goal for College Two');

        $response = $this->actingAs($admin)->get(route('goal.index', ['college_id' => $college2->id]));

        $response->assertOk();
        $response->assertSee('Goal for College Two');
        $response->assertDontSee('Goal for College One');
    }

    #[Test]
    public function dean_with_assignment_sees_only_their_college(): void
    {
        $dean    = $this->makeUser('dean');
        $college = $this->makeCollege('Dean College');
        $this->assignDean($dean, $college);
        $this->makeGoal($college, 'a', 'Dean College goal');

        $response = $this->actingAs($dean)->get(route('goal.index'));

        $response->assertOk();
        $response->assertSee('Dean College goal');
    }

    #[Test]
    public function dean_without_assignment_sees_no_assignment_warning(): void
    {
        $dean = $this->makeUser('dean');

        $response = $this->actingAs($dean)->get(route('goal.index'));

        $response->assertOk();
        $response->assertSee('No college assigned');
    }

    #[Test]
    public function index_shows_empty_state_when_college_has_no_goals(): void
    {
        $admin   = $this->makeUser('admin');
        $college = $this->makeCollege();

        $response = $this->actingAs($admin)->get(route('goal.index', ['college_id' => $college->id]));

        $response->assertOk();
        $response->assertSee('No goals yet');
    }

    // -- B. goal_store ---------------------------------------------------------

    #[Test]
    public function admin_can_store_a_new_goal(): void
    {
        $admin   = $this->makeUser('admin');
        $college = $this->makeCollege();

        $response = $this->actingAs($admin)->post(route('goal.store'), [
            'college_id' => $college->id,
            'goal_text'  => 'Promote research excellence.',
        ]);

        $response->assertRedirect(route('goal.index', ['college_id' => $college->id]));
        $this->assertDatabaseHas('college_goals', [
            'college_id'         => $college->id,
            'goal_text'          => 'Promote research excellence.',
            'college_goals_code' => 'a',
        ]);
    }

    #[Test]
    public function dean_can_store_goal_for_their_assigned_college(): void
    {
        $dean    = $this->makeUser('dean');
        $college = $this->makeCollege();
        $this->assignDean($dean, $college);

        $response = $this->actingAs($dean)->post(route('goal.store'), [
            'college_id' => $college->id,
            'goal_text'  => 'Foster community engagement.',
        ]);

        $response->assertRedirect(route('goal.index', ['college_id' => $college->id]));
        $this->assertDatabaseHas('college_goals', ['goal_text' => 'Foster community engagement.']);
    }

    #[Test]
    public function goal_codes_are_assigned_sequentially_on_store(): void
    {
        $admin   = $this->makeUser('admin');
        $college = $this->makeCollege();

        foreach (['First goal', 'Second goal', 'Third goal'] as $text) {
            $this->actingAs($admin)->post(route('goal.store'), [
                'college_id' => $college->id,
                'goal_text'  => $text,
            ]);
        }

        $codes = CollegeGoal::where('college_id', $college->id)
            ->orderBy('id')
            ->pluck('college_goals_code')
            ->toArray();

        $this->assertSame(['a', 'b', 'c'], $codes);
    }

    #[Test]
    public function store_fails_validation_when_goal_text_is_missing(): void
    {
        $admin   = $this->makeUser('admin');
        $college = $this->makeCollege();

        $response = $this->actingAs($admin)->post(route('goal.store'), [
            'college_id' => $college->id,
        ]);

        $response->assertSessionHasErrors(['goal_text']);
        $this->assertDatabaseCount('college_goals', 0);
    }

    #[Test]
    public function store_fails_validation_when_college_id_is_missing(): void
    {
        $admin = $this->makeUser('admin');

        $response = $this->actingAs($admin)->post(route('goal.store'), [
            'goal_text' => 'Some text',
        ]);

        $response->assertSessionHasErrors(['college_id']);
    }

    #[Test]
    public function store_fails_validation_when_college_id_does_not_exist(): void
    {
        $admin = $this->makeUser('admin');

        $response = $this->actingAs($admin)->post(route('goal.store'), [
            'college_id' => 999999,
            'goal_text'  => 'Some text',
        ]);

        $response->assertSessionHasErrors(['college_id']);
    }

    #[Test]
    public function dean_cannot_store_goal_for_another_college(): void
    {
        $dean         = $this->makeUser('dean');
        $myCollege    = $this->makeCollege('My College');
        $otherCollege = $this->makeCollege('Other College');
        $this->assignDean($dean, $myCollege);

        $response = $this->actingAs($dean)->post(route('goal.store'), [
            'college_id' => $otherCollege->id,
            'goal_text'  => 'Unauthorized goal.',
        ]);

        $response->assertRedirect(route('goal.index'));
        $response->assertSessionHas('toast.type', 'warning');
        $this->assertDatabaseMissing('college_goals', ['goal_text' => 'Unauthorized goal.']);
    }

    // -- C. goal_update --------------------------------------------------------

    #[Test]
    public function admin_can_update_a_goal(): void
    {
        $admin   = $this->makeUser('admin');
        $college = $this->makeCollege();
        $goal    = $this->makeGoal($college, 'a', 'Original text.');

        $response = $this->actingAs($admin)->put(route('goal.update', $goal->id), [
            'goal_text' => 'Updated text.',
        ]);

        $response->assertRedirect(route('goal.index', ['college_id' => $college->id]));
        $this->assertDatabaseHas('college_goals', [
            'id'        => $goal->id,
            'goal_text' => 'Updated text.',
        ]);
    }

    #[Test]
    public function dean_can_update_goal_for_their_college(): void
    {
        $dean    = $this->makeUser('dean');
        $college = $this->makeCollege();
        $this->assignDean($dean, $college);
        $goal = $this->makeGoal($college, 'a', 'Old text.');

        $response = $this->actingAs($dean)->put(route('goal.update', $goal->id), [
            'goal_text' => 'New dean text.',
        ]);

        $response->assertRedirect(route('goal.index', ['college_id' => $college->id]));
        $this->assertDatabaseHas('college_goals', ['goal_text' => 'New dean text.']);
    }

    #[Test]
    public function update_fails_validation_when_goal_text_is_missing(): void
    {
        $admin   = $this->makeUser('admin');
        $college = $this->makeCollege();
        $goal    = $this->makeGoal($college, 'a', 'Original.');

        $response = $this->actingAs($admin)->put(route('goal.update', $goal->id), []);

        $response->assertSessionHasErrors(['goal_text']);
        $this->assertDatabaseHas('college_goals', ['goal_text' => 'Original.']);
    }

    #[Test]
    public function dean_cannot_update_goal_of_another_college(): void
    {
        $dean         = $this->makeUser('dean');
        $myCollege    = $this->makeCollege('Mine');
        $otherCollege = $this->makeCollege('Other');
        $this->assignDean($dean, $myCollege);
        $goal = $this->makeGoal($otherCollege, 'a', 'Another college goal.');

        $response = $this->actingAs($dean)->put(route('goal.update', $goal->id), [
            'goal_text' => 'Tampered.',
        ]);

        $response->assertRedirect(route('goal.index'));
        $response->assertSessionHas('toast.type', 'warning');
        $this->assertDatabaseMissing('college_goals', ['goal_text' => 'Tampered.']);
    }

    // -- D. goal_destroy -------------------------------------------------------

    #[Test]
    public function admin_can_delete_a_goal(): void
    {
        $admin   = $this->makeUser('admin');
        $college = $this->makeCollege();
        $goal    = $this->makeGoal($college, 'a', 'To be deleted.');

        $response = $this->actingAs($admin)->delete(route('goal.destroy', $goal->id));

        $response->assertRedirect(route('goal.index', ['college_id' => $college->id]));
        $response->assertSessionHas('toast.type', 'success');
        $this->assertDatabaseMissing('college_goals', ['id' => $goal->id]);
    }

    #[Test]
    public function deleting_a_goal_resequences_remaining_codes(): void
    {
        $admin = $this->makeUser('admin');
        $college = $this->makeCollege();
        $goalA   = $this->makeGoal($college, 'a', 'Goal A');
        $goalB   = $this->makeGoal($college, 'b', 'Goal B');
        $goalC   = $this->makeGoal($college, 'c', 'Goal C');

        // Delete the middle goal
        $this->actingAs($admin)->delete(route('goal.destroy', $goalB->id));

        $codes = CollegeGoal::where('college_id', $college->id)
            ->orderBy('id')
            ->pluck('college_goals_code')
            ->toArray();

        $this->assertSame(['a', 'b'], $codes, 'Remaining goals should be resequenced to a, b');
        $this->assertDatabaseMissing('college_goals', ['id' => $goalB->id]);
    }

    #[Test]
    public function deleting_the_only_goal_leaves_no_goals(): void
    {
        $admin   = $this->makeUser('admin');
        $college = $this->makeCollege();
        $goal    = $this->makeGoal($college, 'a');

        $this->actingAs($admin)->delete(route('goal.destroy', $goal->id));

        $this->assertDatabaseCount('college_goals', 0);
    }

    #[Test]
    public function dean_cannot_delete_goal_of_another_college(): void
    {
        $dean         = $this->makeUser('dean');
        $myCollege    = $this->makeCollege('Mine');
        $otherCollege = $this->makeCollege('Other');
        $this->assignDean($dean, $myCollege);
        $goal = $this->makeGoal($otherCollege, 'a', 'Protected goal.');

        $response = $this->actingAs($dean)->delete(route('goal.destroy', $goal->id));

        $response->assertRedirect(route('goal.index'));
        $response->assertSessionHas('toast.type', 'warning');
        $this->assertDatabaseHas('college_goals', ['id' => $goal->id]);
    }

    // -- E. Access control -----------------------------------------------------

    #[Test]
    public function guest_cannot_access_goal_index(): void
    {
        $this->get(route('goal.index'))->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_post_to_goal_store(): void
    {
        $college = $this->makeCollege();

        $this->post(route('goal.store'), [
            'college_id' => $college->id,
            'goal_text'  => 'Sneaky',
        ])->assertRedirect(route('login'));
    }

    #[Test]
    public function faculty_role_is_forbidden_from_goal_index(): void
    {
        $faculty = $this->makeUser('faculty');

        $this->actingAs($faculty)->get(route('goal.index'))->assertForbidden();
    }

    #[Test]
    public function chair_role_is_forbidden_from_goal_store(): void
    {
        $chair   = $this->makeUser('chair');
        $college = $this->makeCollege();

        $this->actingAs($chair)->post(route('goal.store'), [
            'college_id' => $college->id,
            'goal_text'  => 'Unauthorized',
        ])->assertForbidden();
    }
}
