-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- AveragePackageScore

DROP PROCEDURE IF EXISTS AveragePackageScore //

CREATE PROCEDURE AveragePackageScore(IN keyword VARCHAR(15))
BEGIN
    WITH JSOrg AS (
            SELECT U.userID AS 'orgID', name AS 'orgName'
            FROM Repository AS R JOIN OwnsRepo AS O ON R.repoID = O.repoID JOIN _User AS U ON O.userID = U.userID
            WHERE type = 'Organization' AND (description LIKE CONCAT('%', keyword, '%') OR name LIKE CONCAT('%', keyword, '%'))
            GROUP BY U.userID
    )
    SELECT HP.orgID, orgName, CAST(SUM(score) / COUNT(*) AS DECIMAL(10, 2)) AS 'averageScore'
    FROM HasPackage AS HP JOIN JSOrg ON HP.orgID = JSOrg.orgID JOIN Package AS P ON HP.packageName = P.packageName
    GROUP BY HP.orgID, orgName
    ORDER BY averageScore DESC;
END; //

DELIMITER ;
