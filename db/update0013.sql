UPDATE thread_comment SET type='cause' WHERE type != 'comment';

DROP VIEW thread_view;

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
            ELSE thread.state END AS state,
       (SELECT COUNT(*) FROM thread_comment WHERE type = 'cause' AND thread_id=thread.id) AS cause_count
FROM thread
         LEFT JOIN thread_label ON thread.id = thread_label.thread_id
         LEFT JOIN label ON label.id = thread_label.label_id;