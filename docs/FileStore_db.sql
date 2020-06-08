CREATE DATABASE FileStore_app;

USE FileStore_app;

CREATE TABLE user_info
(
	user_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	nickname VARCHAR(32),
	email VARCHAR(64),
	storage_limit BIGINT,
	used BIGINT DEFAULT 0
);


CREATE TABLE access
(
	user_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	login VARCHAR(32),
	password VARCHAR(60),
	FOREIGN KEY (user_id)
	REFERENCES user_info (user_id)
	ON DELETE CASCADE
);

CREATE TABLE sessions
(
	session_id VARCHAR(32) NOT NULL,
	user_id INT NOT NULL,
	expire_date DATE,
	PRIMARY KEY (session_id),
	FOREIGN KEY (user_id)
	REFERENCES access (user_id)
	ON DELETE CASCADE
);

CREATE TABLE image
(
	hash VARCHAR(8) NOT NULL PRIMARY KEY,
	url VARCHAR(1024) NOT NULL
);

CREATE TABLE file_info
(
	hash VARCHAR(8) NOT NULL,
	owner INT NOT NULL,
	name VARCHAR(64),
	size BIGINT,
	type VARCHAR(64),
	date_upload  DATETIME,
	date_create DATETIME,
	FOREIGN KEY (owner)
	REFERENCES user_info (user_id)
	ON DELETE CASCADE,
	FOREIGN KEY (hash)
	REFERENCES image (hash)
	ON DELETE CASCADE
);

CREATE TABLE viewers
(
	hash VARCHAR(8) NOT NULL,
	user_id INT NOT NULL,
	path VARCHAR(128) NOT NULL DEFAULT '/',
	FOREIGN KEY (hash)
	REFERENCES image (hash)
	ON DELETE CASCADE,
	FOREIGN KEY (user_id)
	REFERENCES user_info (user_id)
	ON DELETE CASCADE
);

CREATE TABLE folders
(
	user_id INT NOT NULL,
	path VARCHAR(128) NOT NULL DEFAULT '/',
	folder_name VARCHAR(16),
	UNIQUE (user_id, path, folder_name),
	FOREIGN KEY (user_id)
	REFERENCES user_info (user_id)
	ON DELETE CASCADE
);

CREATE TABLE comment
(
	comment_id VARCHAR(8) NOT NULL,
	author INT NOT NULL,
	date DATETIME,
	text VARCHAR(200),
	PRIMARY KEY (comment_id),
	FOREIGN KEY (author)
	REFERENCES user_info (user_id)
	
);

CREATE TABLE comments
(
	hash VARCHAR(8) NOT NULL,
	comment_id VARCHAR(8) NOT NULL,
	PRIMARY KEY (hash, comment_id),
	
	FOREIGN KEY (hash)
	REFERENCES image (hash)
	ON DELETE CASCADE,

	FOREIGN KEY (comment_id)
	REFERENCES comment (comment_id)
	ON DELETE CASCADE
	
);

CREATE TABLE share_link
(
	link VARCHAR(32) NOT NULL,
	contributor INT NOT NULL,
	date DATETIME,
	expire_date DATETIME,
	PRIMARY KEY (link),
	FOREIGN KEY (contributor)
	REFERENCES user_info (user_id)
);

CREATE TABLE shares
(
	link VARCHAR(32) NOT NULL,
	hash VARCHAR(8) NOT NULL,
	PRIMARY KEY (link, hash),
	FOREIGN KEY (link)
	REFERENCES share_link (link)
	ON DELETE CASCADE,
	FOREIGN KEY (hash)
	REFERENCES image (hash)
	ON DELETE CASCADE

);


