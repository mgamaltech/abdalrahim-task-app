<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\TaskController;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Proves the task HTTP contract from the Week 3 API design (docs/api.md).
 */
class TaskRoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string, list<string>, string, string}>
     */
    public static function taskRoutes(): array
    {
        return [
            'list' => ['tasks.index', ['GET', 'HEAD'], 'api/tasks', 'index'],
            'create' => ['tasks.store', ['POST'], 'api/tasks', 'store'],
            'show' => ['tasks.show', ['GET', 'HEAD'], 'api/tasks/{task}', 'show'],
            'update' => ['tasks.update', ['PUT', 'PATCH'], 'api/tasks/{task}', 'update'],
            'delete' => ['tasks.destroy', ['DELETE'], 'api/tasks/{task}', 'destroy'],
        ];
    }

    /**
     * @param  list<string>  $methods
     */
    #[DataProvider('taskRoutes')]
    public function test_route_uses_the_correct_verb_and_controller_action(
        string $name,
        array $methods,
        string $uri,
        string $action,
    ): void {
        $route = Route::getRoutes()->getByName($name);

        $this->assertNotNull($route, "Route [{$name}] is not registered.");
        $this->assertSame($methods, $route->methods());
        $this->assertSame($uri, $route->uri());
        $this->assertSame(TaskController::class.'@'.$action, $route->getActionName());
    }

    public function test_listing_tasks_returns_200_with_every_task(): void
    {
        $tasks = Task::factory(3)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertOk()
            ->assertJsonCount(3)
            ->assertJsonPath('0.id', $tasks[0]->id)
            ->assertJsonPath('0.title', $tasks[0]->title);
    }

    public function test_listing_tasks_returns_200_with_an_empty_list(): void
    {
        $this->getJson('/api/tasks')
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_showing_a_task_returns_200_with_the_bound_task(): void
    {
        $task = Task::factory()->create();

        $this->getJson("/api/tasks/{$task->id}")
            ->assertOk()
            ->assertJsonPath('id', $task->id)
            ->assertJsonPath('title', $task->title)
            ->assertJsonPath('priority', $task->priority);
    }

    public function test_showing_a_missing_task_returns_404(): void
    {
        $this->get('/api/tasks/999999')
            ->assertNotFound()
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_creating_a_task_is_routed_but_not_implemented_yet(): void
    {
        $this->postJson('/api/tasks', ['title' => 'Write the docs'])
            ->assertStatus(501);

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_replacing_a_task_is_routed_but_not_implemented_yet(): void
    {
        $task = Task::factory()->create(['title' => 'Original']);

        $this->putJson("/api/tasks/{$task->id}", ['title' => 'Replaced'])
            ->assertStatus(501);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Original']);
    }

    public function test_patching_a_task_is_routed_but_not_implemented_yet(): void
    {
        $task = Task::factory()->create();

        $this->patchJson("/api/tasks/{$task->id}", ['done' => true])
            ->assertStatus(501);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'done' => false]);
    }

    public function test_deleting_a_task_is_routed_but_not_implemented_yet(): void
    {
        $task = Task::factory()->create();

        $this->deleteJson("/api/tasks/{$task->id}")
            ->assertStatus(501);

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function taskBoundWriteMethods(): array
    {
        return [
            'PUT' => ['putJson'],
            'PATCH' => ['patchJson'],
            'DELETE' => ['deleteJson'],
        ];
    }

    #[DataProvider('taskBoundWriteMethods')]
    public function test_writing_to_a_missing_task_returns_404_before_the_action_runs(string $method): void
    {
        $this->{$method}('/api/tasks/999999')->assertNotFound();
    }

    public function test_an_unsupported_verb_returns_405(): void
    {
        $task = Task::factory()->create();

        $this->postJson("/api/tasks/{$task->id}")
            ->assertMethodNotAllowed();
    }
}
