DROP VIEW task_view;
DROP VIEW thread_view;

UPDATE thread SET state='opened' WHERE state='done';

CREATE VIEW task_view
  AS
    SELECT
      task.*,
      task_program.name AS task_program_name,
      thread.coordinator AS coordinator,
      CASE	WHEN task.state = 'done' THEN NULL
      WHEN task.plan_date >= date('now', '+1 month') THEN '0'
      WHEN task.plan_date >= date('now') THEN '1'
      ELSE '2' END AS priority
    FROM task
      LEFT JOIN task_program ON task.task_program_id = task_program.id
      LEFT JOIN thread ON task.thread_id = thread.id;

CREATE VIEW thread_view
  AS
    SELECT thread.id, thread.original_poster, thread.coordinator, thread.closed_by,
      thread.private, thread.lock, thread.type,
      thread.create_date, thread.last_activity_date, thread.last_modification_date, thread.close_date,
      thread.title, thread.content, thread.content_html,
      thread.task_count, thread.task_count_closed, thread.task_sum_cost,
      label.id AS label_id,
      label.name AS label_name,
      (SELECT MAX(priority) FROM task_view WHERE task_view.thread_id = thread.id) AS priority,
      CASE WHEN thread.state = 'proposal' THEN 0
      WHEN thread.state = 'opened' AND thread.task_count = 0 THEN 1
      WHEN thread.state = 'opened' THEN 2
      WHEN thread.state = 'closed' THEN 3
      WHEN thread.state = 'rejected' THEN 4 END AS sort,
      CASE WHEN thread.state = 'opened' AND thread.task_count > 0 AND thread.task_count = thread.task_count_closed THEN 'done'
      ELSE thread.state END AS state
    FROM thread
      LEFT JOIN thread_label ON thread.id = thread_label.thread_id
      LEFT JOIN label ON label.id = thread_label.label_id;
