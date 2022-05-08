-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- PackageDownloadsGained

DROP PROCEDURE IF EXISTS PackageDownloadsGained //

CREATE PROCEDURE PackageDownloadsGained(IN startDate DATE, IN endDate DATE)
BEGIN
    WITH D AS (
            SELECT packageName, SUM(downloads) AS 'downloadsGained'
            FROM DownloadsOnDate
            WHERE _day BETWEEN startDate AND endDate
            GROUP BY packageName
    )
    SELECT D.packageName, version, downloadsGained
    FROM D JOIN Package AS P ON D.packageName = P.packageName
    ORDER BY downloadsGained DESC, D.packageName ASC;
END; //

DELIMITER ;
