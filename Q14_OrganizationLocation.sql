-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- OrganizationLocation

DROP PROCEDURE IF EXISTS OrganizationLocation //

CREATE PROCEDURE OrganizationLocation(IN _location VARCHAR(150))
BEGIN
    SELECT Org.orgID AS "orgnization ID", Org.name, Org.createdAt, HasPackage.packageName AS "package name"
    FROM Organization AS Org, HasPackage
    WHERE location LIKE CONCAT('%',  _location, '%') AND Org.orgID = HasPackage.orgID
    ORDER BY Org.createdAt ASC;
END; //

DELIMITER ;