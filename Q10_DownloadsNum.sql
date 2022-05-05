-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- DownloadsNum

DROP PROCEDURE IF EXISTS DownloadsNum //

CREATE PROCEDURE DownloadsNum(IN downloadDate DATE)
BEGIN
    WITH Downloads AS (SELECT packageName, SUM(downloads) AS total_downloads
    FROM DownloadsOnDate
    WHERE _day = downloadDate
    GROUP BY packageName)
    SELECT Downloads.packageName, Package.score, Downloads.total_downloads AS "total downloads"
    FROM Downloads, Package
    WHERE Downloads.packageName = Package.packageName AND Downloads.total_downloads > 100000
    ORDER BY Downloads.total_downloads DESC;
END; //

DELIMITER ;