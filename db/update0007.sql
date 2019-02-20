CREATE TABLE subscription (
  user_id         TEXT    NOT NULL PRIMARY KEY,
  mute            BOOLEAN NOT NULL DEFAULT 0,
  token           TEXT    NOT NULL
);
