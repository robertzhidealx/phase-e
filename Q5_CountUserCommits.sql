-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- CountCommits

DROP PROCEDURE IF EXISTS CountUserCommits //

CREATE PROCEDURE CountUserCommits(IN issueCount INT, IN repoCount INT)
BEGIN
    WITH IC AS (
            SELECT assigneeID, assigneeLogin
            FROM IssueCreator AS IC JOIN Issue AS I ON IC.issueID = I.issueID
            GROUP BY assigneeID, IC.assigneeLogin
            HAVING COUNT(assigneeID) > issueCount AND COUNT(repoURL) > repoCount
    )
    SELECT assigneeID AS 'userID', assigneeLogin AS 'userLogin', COUNT(commitID) AS 'commitsCount'
    FROM IC JOIN _Commit AS C ON assigneeLogin = author
    GROUP BY assigneeID, assigneeLogin
    ORDER BY commitsCount DESC, assigneeLogin ASC, assigneeID ASC;
END; //

DELIMITER ;
