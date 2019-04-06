ALTER TABLE task_comment
  ADD closing BOOLEAN NOT NULL DEFAULT 0;

UPDATE task_comment SET closing=1 WHERE id IN (SELECT MAX(id) FROM task_comment GROUP BY task_id);