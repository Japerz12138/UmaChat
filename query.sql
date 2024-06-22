CREATE DATABASE umachat;
USE umachat;

CREATE TABLE messages (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          name VARCHAR(50),
                          avatar VARCHAR(255),
                          message TEXT,
                          timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE avatars (
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         name VARCHAR(255) NOT NULL,
                         avatar VARCHAR(255) NOT NULL
);

CREATE TABLE banned_users (
                              id INT AUTO_INCREMENT PRIMARY KEY,
                              user_code VARCHAR(255) NOT NULL
);


ALTER TABLE messages ADD COLUMN user_code VARCHAR(255);
ALTER TABLE messages ADD COLUMN parent_id INT DEFAULT NULL;

INSERT INTO avatars (name, avatar) VALUES ('星云天空', '.\resources\img\icon\1020.png');
INSERT INTO avatars (name, avatar) VALUES ('特别周', '.\resources\img\icon\1001.png');
INSERT INTO avatars (name, avatar) VALUES ('无声铃鹿', '.\resources\img\icon\1002.png');
INSERT INTO avatars (name, avatar) VALUES ('米浴', '.\resources\img\icon\1030.png');
INSERT INTO avatars (name, avatar) VALUES ('第一红宝石', '.\resources\img\icon\1085.png');
INSERT INTO avatars (name, avatar) VALUES ('樱花千代王', '.\resources\img\icon\1069.png');
INSERT INTO avatars (name, avatar) VALUES ('曼城茶座', '.\resources\img\icon\1069.png');
INSERT INTO avatars (name, avatar) VALUES ('东海帝王', '.\resources\img\icon\1003.png');