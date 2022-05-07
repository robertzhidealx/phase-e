-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- InsertPackage

DROP PROCEDURE IF EXISTS InsertPackage //

CREATE PROCEDURE InsertPackage(IN p_name VARCHAR(150), IN p_version VARCHAR(150), IN p_stars VARCHAR(150), IN p_score VARCHAR(150))
BEGIN
    insert into Package(packageName, version, stars, score) values (p_name, p_version, p_stars, p_score);
END; //

DELIMITER ;