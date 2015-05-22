CREATE TABLE withings(
	userid INTEGER, 
	meastype INTEGER, 
	category INTEGER, 
	time INTEGER,
	value FLOAT 
);
CREATE INDEX userid_time ON withings(userid, time);
CREATE TABLE users(
	id INTEGER PRIMARY KEY,
	name TEXT,
	password TEXT,
	super INTEGER
);
CREATE INDEX id ON users(id);
CREATE INDEX name ON users(name);
CREATE TABLE user_session_tokens(
	user_id INTEGER,
	token TEXT,
	created INTEGER
);
CREATE INDEX user_id ON user_session_tokens(user_id);
