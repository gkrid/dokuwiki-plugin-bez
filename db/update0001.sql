CREATE TABLE thread (
  id                     INTEGER NOT NULL PRIMARY KEY,

  original_poster        TEXT    NOT NULL,
  coordinator            TEXT    NULL, -- NULL - proposal
  closed_by              TEXT    NULL, -- who closed or rejected the thread


  private                BOOLEAN NOT NULL  DEFAULT 0, -- 0 - public, 1 - private
  lock                   BOOLEAN NOT NULL  DEFAULT 0, -- 0 - unlocked, 1 - locked

  type                   TEXT    NOT NULL  DEFAULT 'issue', -- issue, project
  state                  TEXT    NOT NULL  DEFAULT 'proposal', -- proposal,opened,done,closed,rejected

  create_date            TEXT    NOT NULL, -- ISO8601
  last_activity_date     TEXT    NOT NULL, -- ISO8601
  last_modification_date TEXT    NOT NULL, -- ISO8601
  close_date             TEXT, -- ISO8601

  title                  TEXT    NOT NULL,
  content                TEXT    NOT NULL,
  content_html           TEXT    NOT NULL,

  priority               INTEGER NOT NULL  DEFAULT -1, -- dependent on tasks

  task_count             INTEGER NOT NULL  DEFAULT 0,
  task_count_closed      INTEGER NOT NULL  DEFAULT 0,
  task_sum_cost          REAL
);

CREATE INDEX thread_ix_last_activity_date
  ON thread (last_activity_date); -- to speedup order by

CREATE TABLE thread_participant (
  thread_id       INTEGER NOT NULL REFERENCES thread (id),
  user_id         TEXT    NOT NULL,

  original_poster BOOLEAN NOT NULL DEFAULT 0,
  coordinator     BOOLEAN NOT NULL DEFAULT 0,

  commentator     BOOLEAN NOT NULL DEFAULT 0,
  task_assignee   BOOLEAN NOT NULL DEFAULT 0,
  subscribent     BOOLEAN NOT NULL DEFAULT 0,

  added_by        TEXT    NOT NULL, -- user who added the participant. Equals user_id when user subscribed himself
  added_date      TEXT    NOT NULL, -- ISO8601

  PRIMARY KEY (thread_id, user_id)
);

CREATE TABLE thread_comment (
  id                     INTEGER NOT NULL PRIMARY KEY,

  thread_id              INTEGER NOT NULL REFERENCES thread (id),

  type                   TEXT NOT NULL DEFAULT 'comment', -- comment, cause_real, cause_potential -- will be: comment, cause, risk

  author                 TEXT    NOT NULL,
  create_date            TEXT    NOT NULL, -- ISO8601
  last_modification_date TEXT    NOT NULL, -- ISO8601

  content                TEXT    NOT NULL,
  content_html           TEXT    NOT NULL,

  task_count             INTEGER NOT NULL  DEFAULT 0
);

CREATE INDEX thread_comment_ix_thread_id
  ON thread_comment (thread_id);

CREATE TABLE label (
  id         INTEGER     NOT NULL PRIMARY KEY,
  name       TEXT UNIQUE NOT NULL,
  color      TEXT        NULL, -- color of the label, hex RGB: xxxxxx
  count      INTEGER     NOT NULL DEFAULT 0,

  added_by   TEXT        NOT NULL, -- user who added the label
  added_date TEXT        NOT NULL -- ISO8601
);

CREATE INDEX label_ix_name
  ON label (name);

CREATE TABLE thread_label (
  thread_id INTEGER NOT NULL,
  label_id  INTEGER NOT NULL,
  PRIMARY KEY (thread_id, label_id)
);

CREATE TRIGGER thread_label_tr_insert
  INSERT
  ON thread_label
BEGIN
  UPDATE label
  SET count = count + 1
  WHERE id = new.label_id;
END;

CREATE TRIGGER thread_label_tr_delete
  DELETE
  ON thread_label
BEGIN
  UPDATE label
  SET count = count - 1
  WHERE id = old.label_id;
END;

CREATE TRIGGER thread_label_tr_update_label_id
  UPDATE OF label_id
  ON thread_label
BEGIN
  UPDATE label
  SET count = count - 1
  WHERE id = old.label_id;

  UPDATE label
  SET count = count + 1
  WHERE id = new.label_id;
END;

CREATE TABLE task_program (
  id         INTEGER     NOT NULL PRIMARY KEY,
  name       TEXT UNIQUE NOT NULL,
  count      INTEGER     NOT NULL DEFAULT 0,

  added_by   TEXT        NOT NULL, -- user who added the label
  added_date TEXT        NOT NULL -- ISO8601
);

-- we cannot delete tasks (so not triggers provided)
CREATE TABLE task (
  id                     INTEGER NOT NULL PRIMARY KEY,

  original_poster        TEXT    NOT NULL,
  assignee               TEXT    NOT NULL,
  closed_by              TEXT    NULL, -- who closed the task

  private                BOOLEAN NOT NULL DEFAULT 0, -- 0 - public, 1 - private
  lock                   BOOLEAN NOT NULL DEFAULT 0, -- 0 - unlocked, 1 - locked

  state                  TEXT    NOT NULL DEFAULT 'opened', -- opened, done
  type                   TEXT    NOT NULL DEFAULT 'correction', -- correction, corrective, preventive, program

  create_date            TEXT    NOT NULL, -- ISO8601
  last_activity_date     TEXT    NOT NULL, -- -- ISO8601
  last_modification_date TEXT    NOT NULL, -- ISO8601
  close_date             TEXT, -- ISO8601

  cost                   REAL,
  plan_date              TEXT    NOT NULL, -- -- ISO8601
  all_day_event          INTEGER NOT NULL DEFAULT 0, -- 0 - false, 1 - true
  start_time             TEXT    NULL, -- HH:MM
  finish_time            TEXT    NULL, -- HH:MM

  content                TEXT    NOT NULL,
  content_html           TEXT    NOT NULL,

  thread_id              INTEGER REFERENCES thread (id), --may be null
  thread_comment_id      INTEGER REFERENCES thread_comment (id), --may be null
  task_program_id        INTEGER REFERENCES task_program (id) --may be null
);

CREATE INDEX task_ix_thread_id_thread_comment_id
  ON task (thread_id, thread_comment_id);

CREATE INDEX task_ix_task_program_id
  ON task(task_program_id);

CREATE VIEW task_view
  AS
    SELECT
      task.*,
      task_program.name AS task_program_name,
      thread.coordinator AS coordinator,
      CASE	WHEN task.state = 'done' THEN NULL
       WHEN task.plan_date >= date('now', '+1 month') THEN '2'
       WHEN task.plan_date >= date('now') THEN '1'
       ELSE '0' END AS priority
    FROM task
      LEFT JOIN task_program ON task.task_program_id = task_program.id
      LEFT JOIN thread ON task.thread_id = thread.id;

CREATE TRIGGER task_tr_insert
  INSERT
  ON task
  WHEN new.thread_id IS NOT NULL
BEGIN
  UPDATE thread
  SET task_count = task_count + 1
  WHERE id = new.thread_id;
  UPDATE thread
  SET task_sum_cost = task_sum_cost + new.cost
  WHERE id = new.thread_id;
END;

CREATE TRIGGER task_tr_update_cost_count
  UPDATE OF thread_id, cost
  ON task
BEGIN
  UPDATE thread
  SET task_sum_cost = task_sum_cost - old.cost
  WHERE id = old.thread_id;
  UPDATE thread
  SET task_sum_cost = task_sum_cost + new.cost
  WHERE id = new.thread_id;

  UPDATE thread
  SET task_count = task_count - 1
  WHERE id = old.thread_id;
  UPDATE thread
  SET task_count = task_count + 1
  WHERE id = new.thread_id;
END;

CREATE TRIGGER task_tr_update_state_opened_closed
  UPDATE OF thread_id, state
  ON task
  WHEN old.state = 'opened' AND new.state = 'done'
BEGIN
  UPDATE thread
  SET task_count_closed = task_count_closed + 1
  WHERE id = new.thread_id;
END;

CREATE TRIGGER task_tr_update_state_closed_opened
  UPDATE OF thread_id, state
  ON task
  WHEN old.state = 'done' AND new.state = 'opened'
BEGIN
  UPDATE thread
  SET task_count_closed = task_count_closed - 1
  WHERE id = old.thread_id;
END;

CREATE TRIGGER task_tr_update_state_closed_closed
  UPDATE OF thread_id, state
  ON task
  WHEN old.state = 'done' AND new.state = 'done'
BEGIN
  UPDATE thread
  SET task_count_closed = task_count_closed - 1
  WHERE id = old.thread_id;
  UPDATE thread
  SET task_count_closed = task_count_closed + 1
  WHERE id = new.thread_id;
END;

-- thread_comment triggers

CREATE TRIGGER thread_comment_tr_insert
  INSERT
  ON task
BEGIN
  UPDATE thread_comment
  SET task_count = task_count + 1
  WHERE id = new.thread_comment_id;
END;

CREATE TRIGGER thread_comment_tr_delete
  DELETE
  ON task
BEGIN
  UPDATE thread_comment
  SET task_count = task_count - 1
  WHERE id = old.thread_comment_id;
END;

CREATE TRIGGER thread_comment_tr_thread_comment_id
  UPDATE OF thread_comment_id
  ON task
BEGIN
  UPDATE thread_comment
  SET task_count = task_count - 1
  WHERE id = old.thread_comment_id;

  UPDATE thread_comment
  SET task_count = task_count + 1
  WHERE id = new.thread_comment_id;
END;

-- end of thread_comment triggers

-- task_program triggers
CREATE TRIGGER task_program_tr_insert
  INSERT
  ON task
BEGIN
  UPDATE task_program
  SET count = count + 1
  WHERE id = new.task_program_id;
END;

CREATE TRIGGER task_program_tr_delete
  DELETE
  ON task
BEGIN
  UPDATE task_program
  SET count = count - 1
  WHERE id = old.task_program_id;
END;

CREATE TRIGGER task_program_tr_update_task_program_id
  UPDATE OF task_program_id
  ON task
BEGIN
  UPDATE task_program
  SET count = count - 1
  WHERE id = old.task_program_id;

  UPDATE task_program
  SET count = count + 1
  WHERE id = new.task_program_id;
END;
-- end of task_program triggres

CREATE TABLE task_participant (
  task_id       INTEGER NOT NULL REFERENCES thread (id),
  user_id         TEXT    NOT NULL,

  original_poster BOOLEAN NOT NULL DEFAULT 0,
  assignee        BOOLEAN NOT NULL DEFAULT 0,

  commentator     BOOLEAN NOT NULL DEFAULT 0,
  subscribent     BOOLEAN NOT NULL DEFAULT 0,

  added_by        TEXT    NOT NULL, -- user who added the participant. Equals user_id when user subscribed himself
  added_date      TEXT    NOT NULL, -- ISO8601

  PRIMARY KEY (task_id, user_id)
);

CREATE TABLE task_comment (
  id                     INTEGER NOT NULL PRIMARY KEY,

  task_id                INTEGER NOT NULL REFERENCES task (id),

  author                 TEXT    NOT NULL,
  create_date            TEXT    NOT NULL, -- ISO8601
  last_modification_date TEXT    NOT NULL, -- ISO8601

  content                TEXT    NOT NULL,
  content_html           TEXT    NOT NULL
);

CREATE TABLE authentication_token (
  page_id         TEXT NOT NULL,
  token           TEXT NOT NULL,

  generated_by    TEXT NOT NULL,
  generation_date TEXT NOT NULL,
  expire_date     TEXT,

  PRIMARY KEY (page_id, token)
);