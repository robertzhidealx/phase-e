-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- PackageVersion

DROP PROCEDURE IF EXISTS PackageVersion //

CREATE PROCEDURE PackageVersion()
BEGIN
    SELECT '1.0.0' AS packageVersion, AVG(stars) AS "average stars", AVG(score) AS "average score", count(*) AS "count"
    FROM Package
    Where version LIKE "1.%"
    UNION
    SELECT '2.0.0' AS packageVersion, AVG(stars) AS "average stars", AVG(score) AS "average score", count(*) AS "count"
    FROM Package
    Where version LIKE "2.%"
    UNION
    SELECT '3.0.0' AS packageVersion, AVG(stars) AS "average stars", AVG(score) AS "average score", count(*) AS "count"
    FROM Package
    Where version LIKE "3.%"
    UNION
    SELECT '4.0.0' AS packageVersion, AVG(stars) AS "average stars", AVG(score) AS "average score", count(*) AS "count"
    FROM Package
    Where version LIKE "4.%"
    ORDER BY packageVersion ASC;
END; //

DELIMITER ;