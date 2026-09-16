<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * HTTP contract for tasks, following the Week 3 API design (docs/api.md).
 *
 * Reads are live. Writes are routed and bound but answer 501 until the
 * Week 6 CRUD and validation work fills them in with their contract codes.
 */
class TaskController extends Controller
{
    /**
     * List all tasks.
     *
     * GET /api/tasks → 200
     */
    public function index(): JsonResponse
    {
        return response()->json(Task::all(), Response::HTTP_OK);
    }

    /**
     * Create a task.
     *
     * POST /api/tasks → 201 (Week 6), 422 on invalid data
     */
    public function store(): JsonResponse
    {
        return $this->notImplemented('Creating a task');
    }

    /**
     * Show one task.
     *
     * GET /api/tasks/{task} → 200, 404 when the task does not exist
     */
    public function show(Task $task): JsonResponse
    {
        return response()->json($task, Response::HTTP_OK);
    }

    /**
     * Replace (PUT) or partially update (PATCH) a task.
     *
     * PUT|PATCH /api/tasks/{task} → 200 (Week 6), 404 when missing, 422 on invalid data
     */
    public function update(Task $task): JsonResponse
    {
        return $this->notImplemented('Updating a task');
    }

    /**
     * Delete a task.
     *
     * DELETE /api/tasks/{task} → 204 (Week 6), 404 when missing
     */
    public function destroy(Task $task): JsonResponse
    {
        return $this->notImplemented('Deleting a task');
    }

    /**
     * Placeholder response for actions scheduled for the Week 6 CRUD work.
     */
    private function notImplemented(string $action): JsonResponse
    {
        return response()->json(
            ['message' => "{$action} is not implemented yet."],
            Response::HTTP_NOT_IMPLEMENTED
        );
    }
}
