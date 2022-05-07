-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- FixesPercentage

DROP PROCEDURE IF EXISTS FixesPercentage //

CREATE PROCEDURE FixesPercentage(IN orderBy VARCHAR(4))
BEGIN
    IF orderBy = 'desc' THEN
        SELECT repoID, name AS 'repoName', COUNT(issueID) AS 'fixesCount', openIssuesCount, CAST((COUNT(issueID) / openIssuesCount * 100) AS DECIMAL(10, 2)) AS 'fixesPercentage'
        FROM Issue AS I JOIN Repository AS R ON repoURL = url
        WHERE title LIKE '%fix%'
        GROUP BY repoURL
        ORDER BY fixesPercentage DESC, fixesCount DESC, openIssuesCount DESC;
    ELSE
        SELECT repoID, name AS 'repoName', COUNT(issueID) AS 'fixesCount', openIssuesCount, CAST((COUNT(issueID) / openIssuesCount * 100) AS DECIMAL(10, 2)) AS 'fixesPercentage'
        FROM Issue AS I JOIN Repository AS R ON repoURL = url
        WHERE title LIKE '%fix%'
        GROUP BY repoURL
        ORDER BY fixesPercentage ASC, fixesCount DESC, openIssuesCount DESC;
    END IF;
END; //

DELIMITER ;
