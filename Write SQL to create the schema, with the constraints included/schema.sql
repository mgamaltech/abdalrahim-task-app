SELECT VERSION() AS mysql_version;

-- Children first: FKs block dropping a parent that still has children
DROP TABLE IF EXISTS label_task;
DROP TABLE IF EXISTS tasks;
DROP TABLE IF EXISTS labels;
DROP TABLE IF EXISTS users;


-- ============================================
-- users — parent of tasks
-- ============================================
CREATE TABLE users (
    id         INT          NOT NULL AUTO_INCREMENT,
    name       VARCHAR(255) NOT NULL,
    email      VARCHAR(255) NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    CONSTRAINT uq_users_email UNIQUE (email),

    CONSTRAINT chk_users_name_not_blank  CHECK (TRIM(name)  <> ''),
    CONSTRAINT chk_users_email_not_blank CHECK (TRIM(email) <> '')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================
-- labels — reusable tags, shared across tasks
-- ============================================
CREATE TABLE labels (
    id   INT         NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,

    PRIMARY KEY (id),

    CONSTRAINT uq_labels_name UNIQUE (name),
    CONSTRAINT chk_labels_name_not_blank CHECK (TRIM(name) <> '')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================
-- tasks — child of users
-- ============================================
CREATE TABLE tasks (
    id           INT          NOT NULL AUTO_INCREMENT,
    title        VARCHAR(255) NOT NULL,
    priority     VARCHAR(20)  NOT NULL DEFAULT 'medium',
    done         BOOLEAN      NOT NULL DEFAULT FALSE,

    user_id      INT          NULL,

    completed_at TIMESTAMP    NULL,
    created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    CONSTRAINT chk_tasks_title_not_blank CHECK (TRIM(title) <> ''),

    CONSTRAINT chk_tasks_priority
        CHECK (priority IN ('low', 'medium', 'high')),

    CONSTRAINT chk_tasks_completed_at
        CHECK (done = FALSE OR completed_at IS NOT NULL),

    CONSTRAINT fk_tasks_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    INDEX idx_tasks_user_done (user_id, done)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================
-- label_task — pivot for the tasks <-> labels many-to-many
-- ============================================
CREATE TABLE label_task (
    label_id INT NOT NULL,
    task_id  INT NOT NULL,

    PRIMARY KEY (label_id, task_id),

    CONSTRAINT fk_label_task_label
        FOREIGN KEY (label_id) REFERENCES labels(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_label_task_task
        FOREIGN KEY (task_id) REFERENCES tasks(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    INDEX idx_label_task_task (task_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;