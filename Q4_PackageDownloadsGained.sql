-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- PackageDownloadsGained

DROP PROCEDURE IF EXISTS PackageDownloadsGained //

CREATE PROCEDURE PackageDownloadsGained(IN startDate VARCHAR(10), IN endDate VARCHAR(10))
BEGIN
    WITH D AS (
            SELECT packageName, SUM(downloads) AS 'downloadsGained'
            FROM DownloadsOnDate
            WHERE _day > startDate AND _day <= endDate
            GROUP BY packageName
    )
    SELECT D.packageName, version, downloadsGained
    FROM D JOIN Package AS P ON D.packageName = P.packageName
    ORDER BY downloadsGained DESC, D.packageName ASC;
END; //

DELIMITER ;
