-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- MostCount

DROP PROCEDURE IF EXISTS MostCount //

CREATE PROCEDURE MostCount(IN countType VARCHAR(100))
BEGIN
    WITH maxTypeCount AS (
    SELECT MAX(countType) AS maxCount
    FROM Repository)
    SELECT _User.userID AS "user ID", _User.login AS "user login",  _User.url AS "user URL", Repository.name AS "repository name", Repository.description AS "repository description"
    FROM Repository, maxTypeCount, OwnsRepo, _User
    WHERE Repository.countType = maxTypeCount.maxCount AND OwnsRepo.repoID = Repository.repoID AND _User.userID = OwnsRepo.userID;
END; //

DELIMITER ;