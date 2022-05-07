-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- OrganizationLocation

DROP PROCEDURE IF EXISTS CommitChange //

CREATE PROCEDURE CommitChange(IN changeType VARCHAR(150))
BEGIN
    IF changeType = "total changes" THEN
        WITH AverageChange AS (SELECT AVG(total) AS num
        FROM CommitStats)
        SELECT CommitStats.commitID, CommitStats.additions AS "commit additions", CommitStats.deletions AS "commit deletions", Repository.name AS "repository name", _Commit.author, CommitStats.total
        FROM CommitStats, AverageChange, _Commit, Repository
        WHERE total > AverageChange.num AND CommitStats.commitID = _Commit.commitID AND Repository.repoID = _Commit.repoID
        ORDER BY CommitStats.additions DESC;
    ELSEIF changeType = "additions" THEN
        WITH AverageChange AS (SELECT AVG(additions) AS num
        FROM CommitStats)
        SELECT CommitStats.commitID, CommitStats.additions AS "commit additions", CommitStats.deletions AS "commit deletions", Repository.name AS "repository name", _Commit.author, CommitStats.total
        FROM CommitStats, AverageChange, _Commit, Repository
        WHERE additions > AverageChange.num AND CommitStats.commitID = _Commit.commitID AND Repository.repoID = _Commit.repoID
        ORDER BY CommitStats.additions DESC;
    ELSEIF changeType = "deletions" THEN
        WITH AverageChange AS (SELECT AVG(deletions) AS num
        FROM CommitStats)
        SELECT CommitStats.commitID, CommitStats.additions AS "commit additions", CommitStats.deletions AS "commit deletions", Repository.name AS "repository name", _Commit.author, CommitStats.total
        FROM CommitStats, AverageChange, _Commit, Repository
        WHERE deletions > AverageChange.num AND CommitStats.commitID = _Commit.commitID AND Repository.repoID = _Commit.repoID
        ORDER BY CommitStats.additions DESC;
    ELSE
        WITH AverageChange AS (SELECT AVG(badinput) AS num
        FROM CommitStats)
        SELECT CommitStats.commitID, CommitStats.additions AS "commit additions", CommitStats.deletions AS "commit deletions", Repository.name AS "repository name", _Commit.author, CommitStats.total
        FROM CommitStats, AverageChange, _Commit, Repository
        WHERE badinput > AverageChange.num AND CommitStats.commitID = _Commit.commitID AND Repository.repoID = _Commit.repoID
        ORDER BY CommitStats.additions DESC;
    END IF;
END; //

DELIMITER ;