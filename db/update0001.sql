CREATE TABLE thread (
  id                     INTEGER NOT NULL PRIMARY KEY,

  original_poster        TEXT    NOT NULL,
  coordinator            TEXT    NULL, -- NULL - proposal

  private                BOOLEAN NOT NULL  DEFAULT 0, -- 0 - public, 1 - private
  lock                   BOOLEAN NOT NULL  DEFAULT 0, -- 0 - unlocked, 1 - locked

  type                   INTEGER NOT NULL  DEFAULT 0, -- 0 - project, 1 - issue
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

  type                   INTEGER NOT NULL DEFAULT 0, -- 0 -comment, 1 - real cause, 2 - potential cause, 10 - closing comment

  author                 TEXT    NOT NULL,
  create_date            TEXT    NOT NULL, -- ISO8601
  last_modification_date TEXT    NOT NULL, -- ISO8601

  content                TEXT    NOT NULL,
  content_html           TEXT    NOT NULL
);

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

-- we cannot delete tasks (so not triggers provided)
CREATE TABLE task (
  id                     INTEGER NOT NULL PRIMARY KEY,

  original_poster        TEXT    NOT NULL,
  assignee               TEXT    NOT NULL,

  private                BOOLEAN NOT NULL DEFAULT 0, -- 0 - public, 1 - private
  lock                   BOOLEAN NOT NULL DEFAULT 0, -- 0 - unlocked, 1 - locked

  state                  TEXT    NOT NULL DEFAULT 'opened', -- opened, closed, rejected

  create_date            TEXT    NOT NULL, -- ISO8601
  last_activity_date     TEXT    NOT NULL, -- -- ISO8601
  last_modification_date TEXT    NOT NULL, -- ISO8601
  close_date             TEXT, -- ISO8601

  cost                   REAL,
  plan_date              TEXT    NOT NULL, -- -- ISO8601

  content                TEXT    NOT NULL,
  content_html           TEXT    NOT NULL,

  thread_id              INTEGER REFERENCES thread (id), --may be null
  cause_id               INTEGER REFERENCES thread_cause (id) --may be null
);

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
  WHEN old.state = 'opened' AND new.state IN ('closed', 'rejected')
BEGIN
  UPDATE thread
  SET task_count_closed = task_count_closed + 1
  WHERE id = new.thread_id;
END;

CREATE TRIGGER task_tr_update_state_closed_opened
  UPDATE OF thread_id, state
  ON task
  WHEN old.state IN ('closed', 'rejected') AND new.state = 'opened'
BEGIN
  UPDATE thread
  SET task_count_closed = task_count_closed - 1
  WHERE id = old.thread_id;
END;

CREATE TRIGGER task_tr_update_state_closed_closed
  UPDATE OF thread_id, state
  ON task
  WHEN old.state IN ('closed', 'rejected') AND new.state IN ('closed', 'rejected')
BEGIN
  UPDATE thread
  SET task_count_closed = task_count_closed - 1
  WHERE id = old.thread_id;
  UPDATE thread
  SET task_count_closed = task_count_closed + 1
  WHERE id = new.thread_id;
END;

CREATE TABLE task_participant (
  thread_id       INTEGER NOT NULL REFERENCES thread (id),
  user_id         TEXT    NOT NULL,

  original_poster BOOLEAN NOT NULL DEFAULT 0,
  assignee        BOOLEAN NOT NULL DEFAULT 0,

  commentator     BOOLEAN NOT NULL DEFAULT 0,
  subscribent     BOOLEAN NOT NULL DEFAULT 0,

  added_by        TEXT    NOT NULL, -- user who added the participant. Equals user_id when user subscribed himself
  added_date      TEXT    NOT NULL, -- ISO8601

  PRIMARY KEY (thread_id, user_id)
);

CREATE TABLE task_comment (
  id                     INTEGER NOT NULL PRIMARY KEY,

  task_id                INTEGER NOT NULL REFERENCES task (id),

  type                   INTEGER NOT NULL DEFAULT 0, -- 0 -comment, 1 - closing comment

  author                 TEXT    NOT NULL,
  create_date            TEXT    NOT NULL, -- ISO8601
  last_modification_date TEXT    NOT NULL, -- ISO8601

  content                TEXT    NOT NULL,
  content_html           TEXT    NOT NULL
);

CREATE TABLE authentication_token (
  page_id         TEXT PRIMARY KEY,
  token           TEXT NOT NULL,

  generated_by    TEXT NOT NULL,
  generation_date TEXT NOT NULL,
  expire_date     TEXT
);