-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- OrganizationStats

DROP PROCEDURE IF EXISTS OrganizationStats //

CREATE PROCEDURE OrganizationStats(IN createdYear VARCHAR(15))
BEGIN
    WITH userCount AS (SELECT InOrg.orgID AS orgID, COUNT(InOrg.userID) AS userNum
    FROM Organization AS Org, InOrg
    WHERE Org.orgID = InOrg.orgID AND Org.createdAt > CONCAT('',createdYear, '-01-01')
    GROUP BY InOrg.orgID)
    SELECT userCount.orgID AS "orgnization ID", Org.name, Org.createdAt, Org.updatedAt, userCount.userNum
    FROM userCount, Organization AS Org
    WHERE userCount.orgID = Org.orgID AND Org.name LIKE "%js%"
    ORDER BY Org.createdAt ASC;
END; //

DELIMITER ;