-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- OrganizationCommits

DROP PROCEDURE IF EXISTS OrganizationCommits //

CREATE PROCEDURE OrganizationCommits(IN orderBy VARCHAR(4))
BEGIN
    IF orderBy = 'desc' THEN
        WITH Org AS (
                SELECT IO.orgID, name AS 'orgName', email, userID
                FROM InOrg AS IO JOIN Organization AS O ON IO.orgID = O.orgID
        ),
        UserCommit AS (
                SELECT IO.userID, U.login AS 'userLogin', COUNT(*) AS 'commitCount'
                FROM InOrg AS IO JOIN _User AS U ON IO.userID = U.userID JOIN _Commit AS C ON U.login = C.author
                GROUP BY IO.userID, U.login
        )
        SELECT orgID, orgName, email AS 'orgEmail', SUM(commitCount) AS 'orgCommits'
        FROM Org AS O JOIN UserCommit AS UC ON O.userID = UC.userID
        GROUP BY orgID, orgName, email
        ORDER BY orgCommits DESC, orgName ASC;
    ELSE
        WITH Org AS (
                SELECT IO.orgID, name AS 'orgName', email, userID
                FROM InOrg AS IO JOIN Organization AS O ON IO.orgID = O.orgID
        ),
        UserCommit AS (
                SELECT IO.userID, U.login AS 'userLogin', COUNT(*) AS 'commitCount'
                FROM InOrg AS IO JOIN _User AS U ON IO.userID = U.userID JOIN _Commit AS C ON U.login = C.author
                GROUP BY IO.userID, U.login
        )
        SELECT orgID, orgName, email AS 'orgEmail', SUM(commitCount) AS 'orgCommits'
        FROM Org AS O JOIN UserCommit AS UC ON O.userID = UC.userID
        GROUP BY orgID, orgName, email
        ORDER BY orgCommits ASC, orgName ASC;
    END IF;
END; //

DELIMITER ;
