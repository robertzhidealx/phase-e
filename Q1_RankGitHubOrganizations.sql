-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- RankGitHubOrganizations

DROP PROCEDURE IF EXISTS RankGitHubOrganizations //

CREATE PROCEDURE RankGitHubOrganizations(IN keyword VARCHAR(15))
BEGIN
    WITH JSRepo AS (
            SELECT O.userID, U.login AS 'username', stargazersCount
            FROM Repository AS R JOIN OwnsRepo AS O ON R.repoID = O.repoID JOIN _User AS U ON O.userID = U.userID
            WHERE type = 'Organization' AND (description LIKE CONCAT('%', keyword, '%') OR name LIKE CONCAT('%', keyword, '%'))
    )
    SELECT userID, username, SUM(stargazersCount) AS 'totalStars'
    FROM JSRepo
    GROUP BY userID
    ORDER BY totalStars DESC, username ASC;
END; //

DELIMITER ;
