-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30

DELIMITER //

-- AveragePackageDownloads

DROP PROCEDURE IF EXISTS AveragePackageDownloads //

CREATE PROCEDURE AveragePackageDownloads(IN orderBy VARCHAR(4))
BEGIN
    IF orderBy = 'desc' THEN
        WITH PopularPackage AS (
                SELECT P.packageName, downloadsCount
                FROM Package AS P JOIN Downloads AS D ON P.packageName = D.packageName
                WHERE downloadsCount > 100000 AND downloadsCount <= 850000
        )
        SELECT O.orgID, O.name AS 'orgName', CAST(SUM(downloadsCount) / COUNT(*) AS DECIMAL(10, 2)) AS 'averageDownloads'
        FROM HasPackage AS HP JOIN Organization AS O ON HP.orgID = O.orgID JOIN PopularPackage AS PP ON HP.packageName = PP.packageName
        GROUP BY O.orgID, O.name
        ORDER BY averageDownloads DESC;
    ELSE
        WITH PopularPackage AS (
                SELECT P.packageName, downloadsCount
                FROM Package AS P JOIN Downloads AS D ON P.packageName = D.packageName
                WHERE downloadsCount > 100000 AND downloadsCount <= 850000
        )
        SELECT O.orgID, O.name AS 'orgName', CAST(SUM(downloadsCount) / COUNT(*) AS DECIMAL(10, 2)) AS 'averageDownloads'
        FROM HasPackage AS HP JOIN Organization AS O ON HP.orgID = O.orgID JOIN PopularPackage AS PP ON HP.packageName = PP.packageName
        GROUP BY O.orgID, O.name
        ORDER BY averageDownloads ASC;
    END IF;
END; //

DELIMITER ;
