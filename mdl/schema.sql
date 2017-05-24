CREATE TABLE tokens (
				id INTEGER PRIMARY KEY,
				token TEXT NOT NULL,
				page TEXT NOT NULL,
				date INTEGER NOT NULL);

CREATE TABLE issuetypes (
				id INTEGER PRIMARY KEY,
				pl VARCHAR(100) NOT NULL,
				en VARCHAR(100) NOT NULL);

CREATE TABLE tasks (
				id INTEGER PRIMARY KEY,
				task TEXT NOT NULL,
				state INTEGER NOT NULL,
				tasktype INTEGER NULL,
				executor TEXT NOT NULL,
				cost INTEGER NULL,
				reason TEXT NULL,
				reporter TEXT NOT NULL,
				date INTEGER NOT NULL,
				close_date INTEGER NULL,
				cause INTEGER NULL,
				plan_date TEXT NOT NULL,
				all_day_event INTEGET DEFAULT 0,
				start_time TEXT NULL,
				finish_time TEXT NULL,
				issue INTEGER NULL,
				task_cache TEXT NOT NULL,
                reason_cache TEXT NULL);

CREATE TABLE tasktypes (
				id INTEGER PRIMARY KEY,
				pl VARCHAR(100) NOT NULL,
				en VARCHAR(100) NOT NULL);

CREATE TABLE issues (
				id INTEGER PRIMARY KEY,
				title TEXT NOT NULL,
				description TEXT NOT NULL,
				description_cache TEXT NULL,
				state INTEGER NOT NULL,
				opinion TEXT,
				opinion_cache NULL,
				type INTEGER NULL,
				coordinator TEXT NOT NULL,
				reporter TEXT NOT NULL,
				date INTEGER NOT NULL,
				last_mod INTEGER NULL,
				last_activity TEXT NOT NULL,
				participants TEXT NOT NULL,
				subscribents TEXT NULL);
    
CREATE TABLE commcauses (
				id INTEGER PRIMARY KEY NOT NULL,
				issue INTEGER NOT NULL,
				datetime TEXT NOT NULL,
				reporter TEXT NOT NULL,
				type INTEGER NOT NULL DEFAULT 0,
				content TEXT NOT NULL,
				content_cache TEXT NOT NULL);