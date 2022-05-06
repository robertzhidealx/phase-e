-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- MostCount

DROP PROCEDURE IF EXISTS MostCount //

CREATE PROCEDURE MostCount(IN countType VARCHAR(100))
BEGIN
    IF countType = "stargazersCount" THEN
        WITH maxTypeCount AS (
        SELECT MAX(stargazersCount) AS maxCount
        FROM Repository)
        SELECT _User.userID AS "user ID", _User.login AS "user login",  _User.url AS "user URL", Repository.name AS "repository name", Repository.description AS "repository description"
        FROM Repository, maxTypeCount, OwnsRepo, _User
        WHERE Repository.stargazersCount = maxTypeCount.maxCount AND OwnsRepo.repoID = Repository.repoID AND _User.userID = OwnsRepo.userID;
    ELSEIF countType = "forksCount" THEN
        WITH maxTypeCount AS (
        SELECT MAX(forksCount) AS maxCount
        FROM Repository)
        SELECT _User.userID AS "user ID", _User.login AS "user login",  _User.url AS "user URL", Repository.name AS "repository name", Repository.description AS "repository description"
        FROM Repository, maxTypeCount, OwnsRepo, _User
        WHERE Repository.forksCount = maxTypeCount.maxCount AND OwnsRepo.repoID = Repository.repoID AND _User.userID = OwnsRepo.userID;
    ELSEIF countType = "watchersCount" THEN
        WITH maxTypeCount AS (
        SELECT MAX(watchersCount) AS maxCount
        FROM Repository)
        SELECT _User.userID AS "user ID", _User.login AS "user login",  _User.url AS "user URL", Repository.name AS "repository name", Repository.description AS "repository description"
        FROM Repository, maxTypeCount, OwnsRepo, _User
        WHERE Repository.watchersCount = maxTypeCount.maxCount AND OwnsRepo.repoID = Repository.repoID AND _User.userID = OwnsRepo.userID;
    ELSE
        WITH maxTypeCount AS (
        SELECT MAX(nothing) AS maxCount
        FROM Repository)
        SELECT _User.userID AS "user ID", _User.login AS "user login",  _User.url AS "user URL", Repository.name AS "repository name", Repository.description AS "repository description"
        FROM Repository, maxTypeCount, OwnsRepo, _User
        WHERE Repository.nothing = maxTypeCount.maxCount AND OwnsRepo.repoID = Repository.repoID AND _User.userID = OwnsRepo.userID;
    END IF;
END; //

DELIMITER ;