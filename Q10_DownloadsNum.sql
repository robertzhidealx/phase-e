-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- DownloadsNum

DROP PROCEDURE IF EXISTS DownloadsNum //

CREATE PROCEDURE DownloadsNum(IN num INT)
BEGIN
    WITH Download AS (SELECT packageName, SUM(downloads) AS total_downloads
    FROM DownloadsOnDate
    WHERE _day = '2020-10-01' OR _day = '2020-10-02' OR _day = '2020-10-03' OR _day = '2020-10-04' OR _day = '2020-10-05'
    GROUP BY packageName)
    SELECT Download.packageName, Package.score, Download.total_downloads AS "total downloads"
    FROM Download, Package
    WHERE Download.packageName = Package.packageName AND Download.total_downloads > num
    ORDER BY Download.total_downloads DESC;
END; //

DELIMITER ;