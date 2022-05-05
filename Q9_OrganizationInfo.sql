-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- OrganizationInfo

DROP PROCEDURE IF EXISTS OrganizationInfo //

CREATE PROCEDURE OrganizationInfo(IN string VARCHAR(100))
BEGIN
    WITH ID_STARS AS (SELECT HasPackage.orgID, SUM(Package.stars) AS stars
    FROM Package, HasPackage
    WHERE Package.packageName = HasPackage.packageName
    GROUP BY HasPackage.orgID)
    SELECT Org.orgID AS "orgnization ID", Org.name, Org.description, ID_STARS.stars AS "total stars"
    FROM ID_STARS, Organization AS Org
    WHERE ID_STARS.orgID = Org.orgID AND Org.name LIKE CONCAT('%', string, '%')
    ORDER BY ID_STARS.stars DESC;
END; //

DELIMITER ;