-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- CommittedRepoCount

DROP PROCEDURE IF EXISTS CommittedRepoCount //

CREATE PROCEDURE CommittedRepoCount(IN orderBy VARCHAR(4), IN repoCount INT)
BEGIN
    IF orderBy = 'desc' THEN
        WITH Issuer AS (
                SELECT repoID, assigneeLogin
                FROM IssueCreator AS IC JOIN Issue AS I ON IC.issueID = I.issueID JOIN Repository AS R ON repoURL = url
        ),
        AVG AS (
                SELECT repoID, COUNT(*) AS issuesCount
                FROM Issuer
                GROUP BY repoID
        ),
        ResAssignees AS (
                SELECT author, (
                        SELECT COUNT(repoID)
                        FROM Issuer
                        WHERE assigneeLogin = author
                        GROUP BY assigneeLogin
                ) AS 'commitedRepoCount', (
                        SELECT CAST((SUM(issuesCount) / COUNT(repoID)) AS DECIMAL(10, 2))
                        FROM AVG
                ) AS 'avgIssues', SUM(commentCount) AS 'commentCount'
                FROM _Commit AS C JOIN Issuer AS I ON author = assigneeLogin
                WHERE isVerified = 'true'
                GROUP BY author
        )
        SELECT userID, login AS 'userLogin', commitedRepoCount, avgIssues, commentCount
        FROM ResAssignees JOIN _User ON author = login
        WHERE commitedRepoCount >= repoCount
        ORDER BY commitedRepoCount DESC, commentCount DESC, userLogin ASC;
    ELSE
        WITH Issuer AS (
                SELECT repoID, assigneeLogin
                FROM IssueCreator AS IC JOIN Issue AS I ON IC.issueID = I.issueID JOIN Repository AS R ON repoURL = url
        ),
        AVG AS (
                SELECT repoID, COUNT(*) AS issuesCount
                FROM Issuer
                GROUP BY repoID
        ),
        ResAssignees AS (
                SELECT author, (
                        SELECT COUNT(repoID)
                        FROM Issuer
                        WHERE assigneeLogin = author
                        GROUP BY assigneeLogin
                ) AS 'commitedRepoCount', (
                        SELECT CAST((SUM(issuesCount) / COUNT(repoID)) AS DECIMAL(10, 2))
                        FROM AVG
                ) AS 'avgIssues', SUM(commentCount) AS 'commentCount'
                FROM _Commit AS C JOIN Issuer AS I ON author = assigneeLogin
                WHERE isVerified = 'true'
                GROUP BY author
        )
        SELECT userID, login AS 'userLogin', commitedRepoCount, avgIssues, commentCount
        FROM ResAssignees JOIN _User ON author = login
        WHERE commitedRepoCount >= repoCount
        ORDER BY commitedRepoCount ASC, commentCount DESC, userLogin ASC;
    END IF;
END; //

DELIMITER ;
