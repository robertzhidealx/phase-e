-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- VerifiedCommitsPercentage

DROP PROCEDURE IF EXISTS VerifiedCommitsPercentage //

CREATE PROCEDURE VerifiedCommitsPercentage(IN user VARCHAR(100))
BEGIN
    WITH YES AS (SELECT count(*) AS verified
    FROM _Commit
    WHERE committer = user AND isVerified = 'true')
    SELECT YES.verified / count(*)*100 AS "Percentage of verified commits committed by by Github"
    FROM _Commit, YES
    WHERE committer = user;
END; //

DELIMITER ;