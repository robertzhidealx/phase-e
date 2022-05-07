-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- PackageDownloadsGained

DROP PROCEDURE IF EXISTS InsertUser //

CREATE PROCEDURE InsertUser(IN u_id VARCHAR(150), IN u_login VARCHAR(150), IN u_url VARCHAR(150), IN u_type VARCHAR(150))
BEGIN
    insert into _User(userID, login, url, type) values (u_id, u_login, u_url, u_type);
END; //

DELIMITER ;
