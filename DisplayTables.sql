-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- DisplayTables

DROP PROCEDURE IF EXISTS DisplayTables //

CREATE PROCEDURE DisplayTables()
BEGIN
    SELECT *
    FROM Organization;

    SELECT *
    FROM _User;

    SELECT *
    FROM Package;

    SELECT *
    FROM HasPackage;
END; //

DELIMITER ;