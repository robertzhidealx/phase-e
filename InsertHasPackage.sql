-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- InsertHasPackage

DROP PROCEDURE IF EXISTS InsertHasPackage //

CREATE PROCEDURE InsertHasPackage(IN o_id VARCHAR(150), IN p_name VARCHAR(150))
BEGIN
    insert into HasPackage(orgID, packageName) values (o_id, p_name);
END; //

DELIMITER ;
