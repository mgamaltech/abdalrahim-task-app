<?php

namespace Tests\Feature;

use App\Models\Label;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Proves the migrations reproduce the Week 4 schema's data rules.
 *
 * Inserts go through the query builder, not Eloquent, so every rejection
 * comes from the database itself. Each expected message names the exact
 * constraint from schema.sql, so the right rule is the one that fired.
 */
class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_produces_valid_sample_data(): void
    {
        $this->seed();

        $this->assertSame(5, User::count());
        $this->assertSame(4, Label::count());
        $this->assertSame(20, Task::count());
        $this->assertSame(5, Task::where('done', true)->whereNotNull('completed_at')->count());
        $this->assertSame(0, Task::doesntHave('labels')->count());
    }

    public function test_relationships_resolve_in_both_directions(): void
    {
        $user = User::factory()->create();
        $label = Label::factory()->create();
        $task = Task::factory()->for($user)->hasAttached($label)->create();

        $this->assertTrue($task->user->is($user));
        $this->assertTrue($user->tasks->first()->is($task));
        $this->assertTrue($task->labels->first()->is($label));
        $this->assertTrue($label->tasks->first()->is($task));
    }

    public function test_task_columns_have_the_approved_defaults(): void
    {
        $id = DB::table('tasks')->insertGetId(['title' => 'Defaults only']);

        $task = Task::find($id);

        $this->assertSame('medium', $task->priority);
        $this->assertFalse($task->done);
        $this->assertNull($task->user_id);
        $this->assertNull($task->completed_at);
        $this->assertNotNull($task->created_at);
    }

    public function test_user_email_must_be_unique(): void
    {
        DB::table('users')->insert(['name' => 'First', 'email' => 'same@example.com']);

        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('uq_users_email');

        DB::table('users')->insert(['name' => 'Second', 'email' => 'same@example.com']);
    }

    public function test_user_name_cannot_be_blank(): void
    {
        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('chk_users_name_not_blank');

        DB::table('users')->insert(['name' => '   ', 'email' => 'blank@example.com']);
    }

    public function test_user_email_cannot_be_blank(): void
    {
        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('chk_users_email_not_blank');

        DB::table('users')->insert(['name' => 'No Email', 'email' => '  ']);
    }

    public function test_label_name_must_be_unique(): void
    {
        DB::table('labels')->insert(['name' => 'bug']);

        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('uq_labels_name');

        DB::table('labels')->insert(['name' => 'bug']);
    }

    public function test_label_name_cannot_be_blank(): void
    {
        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('chk_labels_name_not_blank');

        DB::table('labels')->insert(['name' => ' ']);
    }

    public function test_task_title_cannot_be_blank(): void
    {
        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('chk_tasks_title_not_blank');

        DB::table('tasks')->insert(['title' => '  ']);
    }

    public function test_task_priority_must_be_low_medium_or_high(): void
    {
        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('chk_tasks_priority');

        DB::table('tasks')->insert(['title' => 'Bad priority', 'priority' => 'urgent']);
    }

    public function test_done_task_requires_a_completion_time(): void
    {
        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('chk_tasks_completed_at');

        DB::table('tasks')->insert(['title' => 'Done too early', 'done' => true, 'completed_at' => null]);
    }

    public function test_task_cannot_reference_a_missing_user(): void
    {
        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('fk_tasks_user');

        DB::table('tasks')->insert(['title' => 'Orphan', 'user_id' => 999999]);
    }

    public function test_deleting_a_user_unassigns_their_tasks(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        DB::table('users')->where('id', $user->id)->delete();

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'user_id' => null]);
    }

    public function test_changing_a_user_id_cascades_to_their_tasks(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        DB::table('users')->where('id', $user->id)->update(['id' => 424242]);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'user_id' => 424242]);
    }

    public function test_deleting_a_task_removes_its_label_links(): void
    {
        $label = Label::factory()->create();
        $task = Task::factory()->hasAttached($label)->create();

        DB::table('tasks')->where('id', $task->id)->delete();

        $this->assertDatabaseMissing('label_task', ['task_id' => $task->id]);
        $this->assertDatabaseHas('labels', ['id' => $label->id]);
    }

    public function test_deleting_a_label_removes_its_task_links(): void
    {
        $label = Label::factory()->create();
        $task = Task::factory()->hasAttached($label)->create();

        DB::table('labels')->where('id', $label->id)->delete();

        $this->assertDatabaseMissing('label_task', ['label_id' => $label->id]);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function test_a_label_cannot_be_attached_to_the_same_task_twice(): void
    {
        $label = Label::factory()->create();
        $task = Task::factory()->create();

        DB::table('label_task')->insert(['label_id' => $label->id, 'task_id' => $task->id]);

        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('Duplicate entry');

        DB::table('label_task')->insert(['label_id' => $label->id, 'task_id' => $task->id]);
    }

    public function test_label_link_cannot_reference_a_missing_task(): void
    {
        $label = Label::factory()->create();

        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('fk_label_task_task');

        DB::table('label_task')->insert(['label_id' => $label->id, 'task_id' => 999999]);
    }
}
