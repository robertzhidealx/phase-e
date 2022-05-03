-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- DownloadsNum

DROP PROCEDURE IF EXISTS DownloadsNum //

CREATE PROCEDURE DownloadsNum(IN createdYear VARCHAR(15))
BEGIN
    WITH userCount AS (SELECT InOrg.orgID AS orgID, COUNT(InOrg.userID) AS userNum
    FROM Organization AS Org, InOrg
    WHERE Org.orgID = InOrg.orgID AND Org.createdAt > '2015-01-01'
    GROUP BY InOrg.orgID)
    SELECT userCount.orgID AS "orgnization ID", Org.name, Org.createdAt, Org.updatedAt, userCount.userNum
    FROM userCount, Organization AS Org
    WHERE userCount.orgID = Org.orgID AND Org.name LIKE "%js%"
    ORDER BY Org.createdAt ASC;
END; //

DELIMITER ;