-- List all tasks with their assignee's name (a JOIN, not two queries).
SELECT
    t.id,
    t.title,
    t.priority,
    t.done,
    u.name AS assignee
FROM tasks t
LEFT JOIN users u ON u.id = t.user_id
ORDER BY t.id;

-- List only one user's tasks.
SELECT id, title, priority, done
FROM tasks
WHERE user_id = 1

-- List all users with a count of their tasks, including users with zero.
SELECT
    u.id,
    u.name,
    COUNT(t.id) AS tasks_count
FROM users u
LEFT JOIN tasks t ON t.user_id = u.id
GROUP BY u.id, u.name
ORDER BY tasks_count DESC, u.name ASC;


-- Create a task, mark one done, delete one, each as its own statement.
-- 4a) Create a task
INSERT INTO tasks (title, priority, user_id)
VALUES ('Review pull request', 'medium', 2);

SELECT LAST_INSERT_ID() AS new_task_id;

-- 4b) Mark one task done
-- chk_tasks_completed_at requires completed_at when done = TRUE
UPDATE tasks
SET done = TRUE,
    completed_at = NOW()
WHERE id = 1;

SELECT id, title, done, completed_at FROM tasks WHERE id = 1;

-- 4c) Delete one task
-- FK ON DELETE CASCADE removes its label_task rows automatically
DELETE FROM tasks WHERE id = 4;

SELECT COUNT(*) AS remaining FROM tasks;

-- List all tasks carrying a specific label (if you built the labels pivot table).
SELECT
    t.id,
    t.title,
    u.name AS assignee
FROM tasks t
INNER JOIN label_task lt ON lt.task_id  = t.id
INNER JOIN labels     l  ON l.id        = lt.label_id
LEFT  JOIN users      u  ON u.id        = t.user_id
WHERE l.name = 'urgent'
ORDER BY t.id;