-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- PackageVersion

DROP PROCEDURE IF EXISTS PackageVersion //

CREATE PROCEDURE PackageVersion(IN v1 INT, IN v2 INT,IN v2 INT)
BEGIN
    SELECT CONCAT(v1, '.0.0') AS packageVersion, AVG(stars) AS "average stars", AVG(score) AS "average score", count(*) AS "count"
    FROM Package
    Where version LIKE CONCAT(v1, '.%')
    UNION
    SELECT CONCAT(v2, '.0.0') AS packageVersion, AVG(stars) AS "average stars", AVG(score) AS "average score", count(*) AS "count"
    FROM Package
    Where version LIKE CONCAT(v2, '.%')
    UNION
    SELECT CONCAT(v3, '.0.0') AS packageVersion, AVG(stars) AS "average stars", AVG(score) AS "average score", count(*) AS "count"
    FROM Package
    Where version LIKE CONCAT(v3, '.%')
    ORDER BY packageVersion DESC;
END; //

DELIMITER ;