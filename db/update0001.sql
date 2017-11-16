
create table thread (
    id INTEGER NOT NULL PRIMARY KEY,

    original_poster TEXT NOT NULL,
    coordinator TEXT NULL, -- NULL - proposal

    private BOOLEAN NOT NULL DEFAULT 0, -- 0 - public, 1 - private
    lock BOOLEAN  NOT NULL  DEFAULT 0, -- 0 - unlocked, 1 - locked

    type INTEGER NOT NULL DEFAULT 0, -- 0 - project, 1 - issue
    state INTEGER TEXT NOT NULL DEFAULT 'proposal', -- proposal,opened,done,closed,rejected

    create_date TEXT NOT NULL, -- ISO8601
    last_activity_date TEXT NOT NULL, -- ISO8601
    close_date TEXT, -- ISO8601

    title TEXT NOT NULL,
    content TEXT NOT NULL,
    content_html TEXT NOT NULL,

    priority INTEGER NOT NULL DEFAULT -1, -- dependent on tasks

    task_count INTEGER NOT NULL DEFAULT 0,
    task_count_open INTEGER NOT NULL DEFAULT 0,
    task_sum_cost REAL
);

create table thread_participant (
    thread_id INTEGER NOT NULL REFERENCES thread (id),
    user_id TEXT NOT NULL,

    original_poster BOOLEAN NOT NULL DEFAULT 0,
    coordinator BOOLEAN NOT NULL DEFAULT 0,

    commentator  BOOLEAN NOT NULL DEFAULT 0,
    task_assignee BOOLEAN NOT NULL DEFAULT 0,
    subscribent BOOLEAN NOT NULL DEFAULT 0,

    added_by TEXT NOT NULL, -- user who added the participant. Equals user_id when user subscribed himself
    added_date TEXT NOT NULL, -- ISO8601

    PRIMARY KEY (thread_id, user_id)
);

create table thread_comment (
    id INTEGER NOT NULL PRIMARY KEY,

    thread_id INTEGER NOT NULL REFERENCES thread (id),

    type INTEGER NOT NULL DEFAULT 0, -- 0 -comment, 1 - closing comment, 2 - real cause, 3 - potential cause

    author TEXT NOT NULL,
    create_date TEXT NOT NULL, -- ISO8601

    content TEXT NOT NULL,
    content_html TEXT NOT NULL
);

create table label (
    id INTEGER NOT NULL PRIMARY KEY,
    name TEXT UNIQUE NOT NULL,
    count INTEGER NOT NULL DEFAULT 0
);

create index label_ix_name ON label (name);

create table thread_label (
    thread_id INTEGER NOT NULL,
    label_id INTEGER NOT NULL,
    PRIMARY KEY (thread_id, label_id)
);

create table task (
    id INTEGER NOT NULL PRIMARY KEY,

    original_poster TEXT NOT NULL,
    assignee TEXT NOT NULL,

    private BOOLEAN NOT NULL DEFAULT 0, -- 0 - public, 1 - private
    lock BOOLEAN NOT NULL DEFAULT 0, -- 0 - unlocked, 1 - locked

    state INTEGER NOT NULL DEFAULT 0, -- 0 - opened, 5 - done, 10 - closed, 15 - rejected

    create_date TEXT NOT NULL, -- ISO8601
    last_activity_date TEXT NOT NULL, -- -- ISO8601
    close_date TEXT, -- ISO8601

    content TEXT NOT NULL,
    content_html TEXT NOT NULL,

    cause_id INTEGER REFERENCES thread_cause (id) --may be null
);

create table task_participant (
    thread_id INTEGER NOT NULL REFERENCES thread (id),
    user_id TEXT NOT NULL,

    original_poster BOOLEAN NOT NULL DEFAULT 0,
    assignee BOOLEAN NOT NULL DEFAULT 0,

    commentator  BOOLEAN NOT NULL DEFAULT 0,
    subscribent BOOLEAN NOT NULL DEFAULT 0,

    added_by TEXT NOT NULL, -- user who added the participant. Equals user_id when user subscribed himself
    added_date TEXT NOT NULL, -- ISO8601

    PRIMARY KEY (thread_id, user_id)
);

create table task_comment (
    id INTEGER NOT NULL PRIMARY KEY,

    task_id INTEGER NOT NULL REFERENCES task (id),

    type INTEGER NOT NULL DEFAULT 0, -- 0 -comment, 1 - closing comment

    author TEXT NOT NULL,
    create_date TEXT NOT NULL, -- ISO8601

    content TEXT NOT NULL,
    content_html TEXT NOT NULL
);