-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- OrganizationLocation

DROP PROCEDURE IF EXISTS CommitChange //

CREATE PROCEDURE CommitChange(IN changeType VARCHAR(150))
BEGIN
    WITH AverageChange AS (SELECT AVG(changeType) AS num
    FROM CommitStats)
    SELECT CommitStats.commitID, CommitStats.additions AS "commit additions", CommitStats.deletions AS "commit deletions", Repository.name AS "repository name", _Commit.author
    FROM CommitStats, AverageChange, _Commit, Repository
    WHERE changeType > AverageChange.num AND CommitStats.commitID = _Commit.commitID AND Repository.repoID = _Commit.repoID
    ORDER BY CommitStats.additions DESC;
END; //

DELIMITER ;