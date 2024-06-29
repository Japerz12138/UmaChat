CREATE DATABASE umachat;
USE umachat;

CREATE TABLE messages (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          name VARCHAR(50),
                          avatar VARCHAR(255),
                          message TEXT,
                          mood VARCHAR(50), -- 新增心情列
                          timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          user_code VARCHAR(255),
                          parent_id INT DEFAULT NULL
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

CREATE TABLE likes (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       message_id INT,
                       user_code VARCHAR(255),
                       timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE messages ADD COLUMN likes INT DEFAULT 0;


INSERT INTO avatars (name, avatar) VALUES ('星云天空', '.\\resources\\img\\icon\\1020.png');
INSERT INTO avatars (name, avatar) VALUES ('特别周', '.\\resources\\img\\icon\\1001.png');
INSERT INTO avatars (name, avatar) VALUES ('无声铃鹿', '.\\resources\\img\\icon\\1002.png');
INSERT INTO avatars (name, avatar) VALUES ('米浴', '.\\resources\\img\\icon\\1030.png');
INSERT INTO avatars (name, avatar) VALUES ('第一红宝石', '.\\resources\\img\\icon\\1085.png');
INSERT INTO avatars (name, avatar) VALUES ('樱花千代王', '.\\resources\\img\\icon\\1069.png');
INSERT INTO avatars (name, avatar) VALUES ('曼城茶座', '.\\resources\\img\\icon\\1025.png');
INSERT INTO avatars (name, avatar) VALUES ('东海帝王', '.\\resources\\img\\icon\\1003.png');
INSERT INTO avatars (name, avatar) VALUES ('好歌剧', '.\\resources\\img\\icon\\1015.png');
INSERT INTO avatars (name, avatar) VALUES ('目白麦昆', '.\\resources\\img\\icon\\1013.png');
INSERT INTO avatars (name, avatar) VALUES ('丸善斯基', '.\\resources\\img\\icon\\1004.png');
INSERT INTO avatars (name, avatar) VALUES ('富士奇石', '.\\resources\\img\\icon\\1005.png');
INSERT INTO avatars (name, avatar) VALUES ('小栗帽', '.\\resources\\img\\icon\\1006.png');
INSERT INTO avatars (name, avatar) VALUES ('黄金船', '.\\resources\\img\\icon\\1007.png');
INSERT INTO avatars (name, avatar) VALUES ('伏特加', '.\\resources\\img\\icon\\1008.png');
INSERT INTO avatars (name, avatar) VALUES ('大和赤骥', '.\\resources\\img\\icon\\1009.png');
INSERT INTO avatars (name, avatar) VALUES ('大树快车', '.\\resources\\img\\icon\\1010.png');
INSERT INTO avatars (name, avatar) VALUES ('草上飞', '.\\resources\\img\\icon\\1011.png');
INSERT INTO avatars (name, avatar) VALUES ('鲁道夫象征', '.\\resources\\img\\icon\\1017.png');
INSERT INTO avatars (name, avatar) VALUES ('玛雅重炮', '.\\resources\\img\\icon\\1024.png');
