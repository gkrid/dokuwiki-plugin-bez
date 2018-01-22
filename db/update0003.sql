CREATE TRIGGER task_tr_delete
  DELETE ON task
BEGIN
  UPDATE thread
  SET task_count = task_count - 1
  WHERE id = old.thread_id;

  UPDATE thread
  SET task_sum_cost = task_sum_cost - coalesce(old.cost, 0)
  WHERE id = old.thread_id;

  DELETE FROM task_comment WHERE task_id=old.id;
END;

CREATE TRIGGER task_tr_delete_state_done
  DELETE ON task
  WHEN old.state = 'done'
BEGIN
  UPDATE thread
  SET task_count_closed = task_count_closed - 1
  WHERE id = old.thread_id;
END;