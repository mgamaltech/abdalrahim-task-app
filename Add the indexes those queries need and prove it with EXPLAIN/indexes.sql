-- Run EXPLAIN on each query from the previous task and note which ones scan the whole table.
EXPLAIN SELECT t.id, t.title, u.name
FROM tasks t LEFT JOIN users u ON u.id = t.user_id;

EXPLAIN SELECT id, title FROM tasks WHERE user_id = 3;

EXPLAIN SELECT id, title FROM tasks WHERE user_id = 3 AND done = FALSE;

EXPLAIN SELECT u.id, u.name, COUNT(t.id)
FROM users u LEFT JOIN tasks t ON t.user_id = u.id
GROUP BY u.id, u.name;

EXPLAIN SELECT t.id, t.title
FROM tasks t
INNER JOIN label_task lt ON lt.task_id = t.id
INNER JOIN labels l ON l.id = lt.label_id
WHERE l.name = 'label-7';

-- Add the index each slow query actually needs, matching the column order to how the query filters.
CREATE INDEX idx_tasks_user_done ON tasks(user_id, done);

CREATE INDEX idx_label_task_task ON label_task(task_id);

ANALYZE TABLE tasks, label_task;

-- Re-run EXPLAIN on each one and record the before/after: type, key, and rows examined.
EXPLAIN SELECT t.id, t.title, u.name
FROM tasks t LEFT JOIN users u ON u.id = t.user_id;

EXPLAIN SELECT id, title FROM tasks WHERE user_id = 3;

EXPLAIN SELECT id, title FROM tasks WHERE user_id = 3 AND done = FALSE;

EXPLAIN SELECT u.id, u.name, COUNT(t.id)
FROM users u LEFT JOIN tasks t ON t.user_id = u.id
GROUP BY u.id, u.name;

EXPLAIN SELECT t.id, t.title
FROM tasks t
INNER JOIN label_task lt ON lt.task_id = t.id
INNER JOIN labels l ON l.id = lt.label_id
WHERE l.name = 'label-7';