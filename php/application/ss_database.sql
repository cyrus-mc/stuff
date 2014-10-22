DROP TABLE u_groups;
DROP TABLE u_applications;
DROP TABLE ss_users;
DROP TABLE ss_groups;
DROP TABLE ss_applications;

CREATE TABLE ss_groups (
	gid		SERIAL		NOT NULL,
	g_name	VARCHAR(10)	NOT NULL,
	PRIMARY KEY (gid)
);

CREATE TABLE ss_users (
	uid 		SERIAL 		NOT NULL,
	u_name 	VARCHAR(10)	NOT NULL,
	p_word	CHAR(32)		NOT NULL,
	gecos	VARCHAR(50)	NOT NULL default '',
	PRIMARY KEY (uid)
);

CREATE TABLE u_groups (
	uid		INTEGER		NOT NULL,
	gid		INTEGER		NOT NULL,
	PRIMARY KEY (uid, gid),
	FOREIGN KEY (uid) REFERENCES ss_users (uid),
	FOREIGN KEY (gid) REFERENCES ss_groups (gid)
);

CREATE TABLE ss_applications (
	aid		SERIAL		NOT NULL,
	a_name	VARCHAR(10)	NOT NULL,
	full_name	VARCHAR(30)	NOT NULL,
	comment	VARCHAR(100)	NOT NULL default '',
	PRIMARY KEY (aid)
);

CREATE TABLE u_applications (
	uid		INTEGER		NOT NULL,
	aid		INTEGER		NOT NULL,
	PRIMARY KEY (uid, aid),
	FOREIGN KEY (uid) REFERENCES ss_users (uid),
	FOREIGN KEY (aid) REFERENCES ss_applications (aid)
);

INSERT INTO ss_applications (a_name, full_name, comment) VALUES('test_app', 'Test Application 1', 'Sample Test Application used during development of PHP framework');

INSERT INTO ss_groups (g_name) VALUES('t_group');

INSERT INTO ss_users (u_name, p_word, gecos) VALUES('t_user', 'test_password', 'Test User 1');

INSERT INTO u_groups VALUES(1, 1);

INSERT INTO u_applications VALUES(1, 1);
