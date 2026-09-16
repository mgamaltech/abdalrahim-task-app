<?php

namespace Database\Seeders;

use App\Models\Label;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(5)->create();

        $labels = collect(['bug', 'feature', 'chore', 'urgent'])
            ->map(fn (string $name) => Label::factory()->create(['name' => $name]));

        $openTasks = Task::factory(15)
            ->recycle($users)
            ->create();

        $doneTasks = Task::factory(5)
            ->recycle($users)
            ->done()
            ->create();

        $openTasks->concat($doneTasks)->each(
            fn (Task $task) => $task->labels()->attach(
                $labels->random(rand(1, 2))->pluck('id')
            )
        );
    }
}
