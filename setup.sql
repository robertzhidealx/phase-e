-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30
DROP TABLE IF EXISTS DownloadsOnDate;

DROP TABLE IF EXISTS Downloads;

DROP TABLE IF EXISTS HasPackage;

DROP TABLE IF EXISTS Package;

DROP TABLE IF EXISTS OwnsRepo;

DROP TABLE IF EXISTS InOrg;

DROP TABLE IF EXISTS Organization;

DROP TABLE IF EXISTS _Commit;

DROP TABLE IF EXISTS CommitStats;

DROP TABLE IF EXISTS IssueCreator;

DROP TABLE IF EXISTS Issue;

DROP TABLE IF EXISTS Repository;
DROP TABLE IF EXISTS _User;

CREATE TABLE Organization (
  orgID INT NOT NULL,
  login VARCHAR(100),
  name VARCHAR(100),
  description VARCHAR(10000),
  email VARCHAR(100),
  createdAt DATETIME,
  updatedAt DATETIME,
  location VARCHAR(100),
  type VARCHAR(100),
  PRIMARY KEY(orgID)
);

LOAD DATA LOCAL INFILE './db/organization.txt' INTO TABLE Organization FIELDS TERMINATED BY ',';

-- User(userID, login, url, type)
CREATE TABLE _User(
  userID INT NOT NULL,
  login VARCHAR(100),
  url VARCHAR(100),
  type VARCHAR(200),
  PRIMARY KEY(userID)
);

LOAD DATA LOCAL INFILE './db/user.txt' INTO TABLE _User FIELDS TERMINATED BY ',';

-- Package (packageName, version, star, score)
CREATE TABLE Package(
  packageName varchar(100) NOT NULL,
  version varchar(100),
  stars INT,
  score FLOAT,
  PRIMARY KEY(packageName)
);

LOAD DATA LOCAL INFILE './db/package.txt' INTO TABLE Package FIELDS TERMINATED BY ',';

-- HasPackage(orgID, packageName)
CREATE TABLE HasPackage(
  orgID INT NOT NULL,
  packageName varchar(100) NOT NULL,
  PRIMARY KEY(orgID, packageName),
  FOREIGN KEY(orgID) REFERENCES Organization(orgID) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(packageName) REFERENCES Package(packageName) ON DELETE CASCADE ON UPDATE CASCADE
);

LOAD DATA LOCAL INFILE './db/hasPackage.txt' INTO TABLE HasPackage FIELDS TERMINATED BY ',';

-- Repository(repoID, name, description, url, forksCount, stargazersCount, watchersCount, openIssuesCount)
CREATE TABLE Repository(
  repoID INT,
  name varchar(100),
  description varchar(600),
  url varchar(100),
  forksCount INT,
  stargazersCount INT,
  watchersCount INT,
  openIssuesCount INT,
  PRIMARY KEY(repoID)
);

LOAD DATA LOCAL INFILE './db/repository.txt' -- LOAD DATA LOCAL INFILE '/Users/jessieluo/Desktop/phase-c/db/repository.txt'
INTO TABLE Repository FIELDS TERMINATED BY ',';

-- Downloads(packageName, startDate, endDate, downloadsCount) 
CREATE TABLE Downloads(
  packageName varchar(100),
  startDate DATE,
  endDate DATE,
  downloadsCount BIGINT,
  PRIMARY KEY(packageName),
  FOREIGN KEY(packageName) REFERENCES Package(packageName) ON DELETE CASCADE ON UPDATE CASCADE
);

LOAD DATA LOCAL INFILE './db/downloads.txt' INTO TABLE Downloads FIELDS TERMINATED BY ',';

-- DownloadsOnDate(packageName, day, downloads)
CREATE TABLE DownloadsOnDate(
  packageName varchar(100),
  _day DATE,
  downloads INT,
  PRIMARY KEY(packageName, _day, downloads),
  FOREIGN KEY(packageName) REFERENCES Package(packageName) ON DELETE CASCADE ON UPDATE CASCADE
);

LOAD DATA LOCAL INFILE './db/downloadsOnDate.txt' INTO TABLE DownloadsOnDate FIELDS TERMINATED BY ',';

-- Commit(commitID, repoID, author, committer, message, commentCount, isVerified) 
CREATE TABLE _Commit(
  commitID varchar(200),
  repoID INT,
  author varchar(200),
  committer varchar(200),
  commentCount INT,
  isVerified varchar(10),
  PRIMARY KEY(commitID),
  FOREIGN KEY(repoID) REFERENCES Repository(repoID) ON DELETE CASCADE ON UPDATE CASCADE
);

LOAD DATA LOCAL INFILE './db/commit.txt' INTO TABLE _Commit FIELDS TERMINATED BY ',';

-- CommitStats(commitID, additions, deletions, total)
CREATE TABLE CommitStats(
  commitID varchar(200),
  additions BIGINT,
  deletions BIGINT,
  total BIGINT,
  PRIMARY KEY(commitID)
);

LOAD DATA LOCAL INFILE './db/commitStats.txt' INTO TABLE CommitStats FIELDS TERMINATED BY ',';

-- Issue(issueID, repoID, title, state) 
CREATE TABLE Issue(
  issueID INT,
  repoURL varchar(500),
  title varchar(500),
  state varchar(500),
  PRIMARY KEY(issueID, repoURL)
);

LOAD DATA LOCAL INFILE './db/issue.txt' INTO TABLE Issue FIELDS TERMINATED BY ',';

-- IssueCreator(issueID, creatorID, creatorLogin)
CREATE TABLE IssueCreator(
  issueID INT,
  assigneeID INT,
  assigneeLogin varchar(100),
  PRIMARY KEY(issueID, assigneeID, assigneeLogin),
  FOREIGN KEY(issueID) REFERENCES Issue(issueID) ON DELETE CASCADE ON UPDATE CASCADE
);

LOAD DATA LOCAL INFILE './db/issueCreator.txt' INTO TABLE IssueCreator FIELDS TERMINATED BY ',';

-- InOrg(userID, orgID) 
CREATE TABLE InOrg(
  userID INT NOT NULL,
  orgID INT NOT NULL,
  PRIMARY KEY(userID, orgID),
  FOREIGN KEY(orgID) REFERENCES Organization(orgID) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(userID) REFERENCES _User(userID) ON DELETE CASCADE ON UPDATE CASCADE
);

LOAD DATA LOCAL INFILE './db/inOrg.txt' INTO TABLE InOrg FIELDS TERMINATED BY ',';

-- OwnsRepo(repoID, userID)
CREATE TABLE OwnsRepo(
  repoID INT NOT NULL,
  userID INT NOT NULL,
  PRIMARY KEY(repoID, userID),
  FOREIGN KEY(repoID) REFERENCES Repository(repoID) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(userID) REFERENCES _User(userID) ON DELETE CASCADE ON UPDATE CASCADE
);

LOAD DATA LOCAL INFILE './db/ownsRepo.txt' INTO TABLE OwnsRepo FIELDS TERMINATED BY ',';

-- Stored procedures

-- RankGitHubOrganizations

DELIMITER //

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

-- AveragePackageScore

DELIMITER //

DROP PROCEDURE IF EXISTS AveragePackageScore //

CREATE PROCEDURE AveragePackageScore(IN keyword VARCHAR(15))
BEGIN
    WITH JSOrg AS (
            SELECT U.userID AS 'orgID', name AS 'orgName'
            FROM Repository AS R JOIN OwnsRepo AS O ON R.repoID = O.repoID JOIN _User AS U ON O.userID = U.userID
            WHERE type = 'Organization' AND (description LIKE CONCAT('%', keyword, '%') OR name LIKE CONCAT('%', keyword, '%'))
            GROUP BY U.userID
    )
    SELECT HP.orgID, orgName, CAST(SUM(score) / COUNT(*) AS DECIMAL(10, 2)) AS 'averageScore'
    FROM HasPackage AS HP JOIN JSOrg ON HP.orgID = JSOrg.orgID JOIN Package AS P ON HP.packageName = P.packageName
    GROUP BY HP.orgID, orgName
    ORDER BY averageScore DESC;
END; //

DELIMITER ;

-- AveragePackageDownloads

DELIMITER //

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

-- RankGitHubOrganizations

DELIMITER //

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

-- AveragePackageScore

DELIMITER //

DROP PROCEDURE IF EXISTS AveragePackageScore //

CREATE PROCEDURE AveragePackageScore(IN keyword VARCHAR(15))
BEGIN
    WITH JSOrg AS (
            SELECT U.userID AS 'orgID', name AS 'orgName'
            FROM Repository AS R JOIN OwnsRepo AS O ON R.repoID = O.repoID JOIN _User AS U ON O.userID = U.userID
            WHERE type = 'Organization' AND (description LIKE CONCAT('%', keyword, '%') OR name LIKE CONCAT('%', keyword, '%'))
            GROUP BY U.userID
    )
    SELECT HP.orgID, orgName, CAST(SUM(score) / COUNT(*) AS DECIMAL(10, 2)) AS 'averageScore'
    FROM HasPackage AS HP JOIN JSOrg ON HP.orgID = JSOrg.orgID JOIN Package AS P ON HP.packageName = P.packageName
    GROUP BY HP.orgID, orgName
    ORDER BY averageScore DESC;
END; //

DELIMITER ;

-- AveragePackageDownloads

DELIMITER //

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

-- CountCommits

DELIMITER //

DROP PROCEDURE IF EXISTS CountUserCommits //

CREATE PROCEDURE CountUserCommits(IN issueCount INT, IN repoCount INT)
BEGIN
    WITH IC AS (
            SELECT assigneeID, assigneeLogin
            FROM IssueCreator AS IC JOIN Issue AS I ON IC.issueID = I.issueID
            GROUP BY assigneeID, IC.assigneeLogin
            HAVING COUNT(assigneeID) > issueCount AND COUNT(repoURL) > repoCount
    )
    SELECT assigneeID AS 'userID', assigneeLogin AS 'userLogin', COUNT(commitID) AS 'commitsCount'
    FROM IC JOIN _Commit AS C ON assigneeLogin = author
    GROUP BY assigneeID, assigneeLogin
    ORDER BY commitsCount DESC, assigneeLogin ASC, assigneeID ASC;
END; //

DELIMITER ;

-- FixesPercentage

DELIMITER //

DROP PROCEDURE IF EXISTS FixesPercentage //

CREATE PROCEDURE FixesPercentage(IN orderBy VARCHAR(4))
BEGIN
    IF orderBy = 'desc' THEN
        SELECT repoID, name AS 'repoName', COUNT(issueID) AS 'fixesCount', openIssuesCount, CAST((COUNT(issueID) / openIssuesCount * 100) AS DECIMAL(10, 2)) AS 'fixesPercentage'
        FROM Issue AS I JOIN Repository AS R ON repoURL = url
        WHERE title LIKE '%fix%'
        GROUP BY repoURL
        ORDER BY fixesPercentage DESC, fixesCount DESC, openIssuesCount DESC;
    ELSE
        SELECT repoID, name AS 'repoName', COUNT(issueID) AS 'fixesCount', openIssuesCount, CAST((COUNT(issueID) / openIssuesCount * 100) AS DECIMAL(10, 2)) AS 'fixesPercentage'
        FROM Issue AS I JOIN Repository AS R ON repoURL = url
        WHERE title LIKE '%fix%'
        GROUP BY repoURL
        ORDER BY fixesPercentage ASC, fixesCount DESC, openIssuesCount DESC;
    END IF;
END; //

DELIMITER ;

-- CommittedRepoCount

DELIMITER //

DROP PROCEDURE IF EXISTS CommittedRepoCount //

CREATE PROCEDURE CommittedRepoCount(IN orderBy VARCHAR(4), IN repoCount INT)
BEGIN
    IF orderBy = 'desc' THEN
        WITH Issuer AS (
                SELECT repoID, assigneeLogin
                FROM IssueCreator AS IC JOIN Issue AS I ON IC.issueID = I.issueID JOIN Repository AS R ON repoURL = url
        ),
        AVG AS (
                SELECT repoID, COUNT(*) AS issuesCount
                FROM Issuer
                GROUP BY repoID
        ),
        ResAssignees AS (
                SELECT author, (
                        SELECT COUNT(repoID)
                        FROM Issuer
                        WHERE assigneeLogin = author
                        GROUP BY assigneeLogin
                ) AS 'commitedRepoCount', (
                        SELECT CAST((SUM(issuesCount) / COUNT(repoID)) AS DECIMAL(10, 2))
                        FROM AVG
                ) AS 'avgIssues', SUM(commentCount) AS 'commentCount'
                FROM _Commit AS C JOIN Issuer AS I ON author = assigneeLogin
                WHERE isVerified = 'true'
                GROUP BY author
        )
        SELECT userID, login AS 'userLogin', commitedRepoCount, avgIssues, commentCount
        FROM ResAssignees JOIN _User ON author = login
        WHERE commitedRepoCount >= repoCount
        ORDER BY commitedRepoCount DESC, commentCount DESC, userLogin ASC;
    ELSE
        WITH Issuer AS (
                SELECT repoID, assigneeLogin
                FROM IssueCreator AS IC JOIN Issue AS I ON IC.issueID = I.issueID JOIN Repository AS R ON repoURL = url
        ),
        AVG AS (
                SELECT repoID, COUNT(*) AS issuesCount
                FROM Issuer
                GROUP BY repoID
        ),
        ResAssignees AS (
                SELECT author, (
                        SELECT COUNT(repoID)
                        FROM Issuer
                        WHERE assigneeLogin = author
                        GROUP BY assigneeLogin
                ) AS 'commitedRepoCount', (
                        SELECT CAST((SUM(issuesCount) / COUNT(repoID)) AS DECIMAL(10, 2))
                        FROM AVG
                ) AS 'avgIssues', SUM(commentCount) AS 'commentCount'
                FROM _Commit AS C JOIN Issuer AS I ON author = assigneeLogin
                WHERE isVerified = 'true'
                GROUP BY author
        )
        SELECT userID, login AS 'userLogin', commitedRepoCount, avgIssues, commentCount
        FROM ResAssignees JOIN _User ON author = login
        WHERE commitedRepoCount >= repoCount
        ORDER BY commitedRepoCount ASC, commentCount DESC, userLogin ASC;
    END IF;
END; //

DELIMITER ;

-- OrganizationCommits

DELIMITER //

DROP PROCEDURE IF EXISTS OrganizationCommits //

CREATE PROCEDURE OrganizationCommits(IN orderBy VARCHAR(4))
BEGIN
    IF orderBy = 'desc' THEN
        WITH Org AS (
                SELECT IO.orgID, name AS 'orgName', email, userID
                FROM InOrg AS IO JOIN Organization AS O ON IO.orgID = O.orgID
        ),
        UserCommit AS (
                SELECT IO.userID, U.login AS 'userLogin', COUNT(*) AS 'commitCount'
                FROM InOrg AS IO JOIN _User AS U ON IO.userID = U.userID JOIN _Commit AS C ON U.login = C.author
                GROUP BY IO.userID, U.login
        )
        SELECT orgID, orgName, email AS 'orgEmail', SUM(commitCount) AS 'orgCommits'
        FROM Org AS O JOIN UserCommit AS UC ON O.userID = UC.userID
        GROUP BY orgID, orgName, email
        ORDER BY orgCommits DESC, orgName ASC;
    ELSE
        WITH Org AS (
                SELECT IO.orgID, name AS 'orgName', email, userID
                FROM InOrg AS IO JOIN Organization AS O ON IO.orgID = O.orgID
        ),
        UserCommit AS (
                SELECT IO.userID, U.login AS 'userLogin', COUNT(*) AS 'commitCount'
                FROM InOrg AS IO JOIN _User AS U ON IO.userID = U.userID JOIN _Commit AS C ON U.login = C.author
                GROUP BY IO.userID, U.login
        )
        SELECT orgID, orgName, email AS 'orgEmail', SUM(commitCount) AS 'orgCommits'
        FROM Org AS O JOIN UserCommit AS UC ON O.userID = UC.userID
        GROUP BY orgID, orgName, email
        ORDER BY orgCommits ASC, orgName ASC;
    END IF;
END; //

DELIMITER ;

-- OrganizationInfo

DELIMITER //

DROP PROCEDURE IF EXISTS OrganizationInfo //

CREATE PROCEDURE OrganizationInfo(IN OrgName VARCHAR(100))
BEGIN
    WITH ID_STARS AS (SELECT HasPackage.orgID, SUM(Package.stars) AS stars
    FROM Package, HasPackage
    WHERE Package.packageName = HasPackage.packageName
    GROUP BY HasPackage.orgID)
    SELECT Org.orgID AS "orgnization ID", Org.name, Org.description, ID_STARS.stars AS "total stars"
    FROM ID_STARS, Organization AS Org
    WHERE ID_STARS.orgID = Org.orgID AND Org.name = OrgName
    ORDER BY ID_STARS.stars DESC;
END; //

DELIMITER ;

-- DownloadsNum

DELIMITER //

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

-- OrganizationStats

DELIMITER //

DROP PROCEDURE IF EXISTS OrganizationStats //

CREATE PROCEDURE OrganizationStats(IN createdYear VARCHAR(15))
BEGIN
    WITH userCount AS (SELECT InOrg.orgID AS orgID, COUNT(InOrg.userID) AS userNum
    FROM Organization AS Org, InOrg
    WHERE Org.orgID = InOrg.orgID AND Org.createdAt > CONCAT('',createdYear, '-01-01')
    GROUP BY InOrg.orgID)
    SELECT userCount.orgID AS "orgnization ID", Org.name, Org.createdAt, Org.updatedAt, userCount.userNum
    FROM userCount, Organization AS Org
    WHERE userCount.orgID = Org.orgID AND Org.name LIKE "%js%"
    ORDER BY Org.createdAt ASC;
END; //

DELIMITER ;

-- VerifiedCommitsPercentage

DELIMITER //

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

-- MostCount

DELIMITER //

DROP PROCEDURE IF EXISTS MostCount //

CREATE PROCEDURE MostCount(IN countType VARCHAR(100))
BEGIN
    IF countType = "stargazersCount" THEN
        WITH maxTypeCount AS (
        SELECT MAX(stargazersCount) AS maxCount, AVG(stargazersCount) AS average
        FROM Repository)
        SELECT _User.userID AS "user ID", _User.login AS "user login",  _User.url AS "user URL", Repository.name AS "repository name", Repository.description AS "repository description", maxTypeCount.average, maxTypeCount.maxCount
        FROM Repository, maxTypeCount, OwnsRepo, _User
        WHERE Repository.stargazersCount = maxTypeCount.maxCount AND OwnsRepo.repoID = Repository.repoID AND _User.userID = OwnsRepo.userID;
    ELSEIF countType = "forksCount" THEN
        WITH maxTypeCount AS (
        SELECT MAX(forksCount) AS maxCount, AVG(forksCount) AS average
        FROM Repository)
        SELECT _User.userID AS "user ID", _User.login AS "user login",  _User.url AS "user URL", Repository.name AS "repository name", Repository.description AS "repository description", maxTypeCount.average, maxTypeCount.maxCount
        FROM Repository, maxTypeCount, OwnsRepo, _User
        WHERE Repository.forksCount = maxTypeCount.maxCount AND OwnsRepo.repoID = Repository.repoID AND _User.userID = OwnsRepo.userID;
    ELSEIF countType = "watchersCount" THEN
        WITH maxTypeCount AS (
        SELECT MAX(watchersCount) AS maxCount, AVG(watchersCount) AS average
        FROM Repository)
        SELECT _User.userID AS "user ID", _User.login AS "user login",  _User.url AS "user URL", Repository.name AS "repository name", Repository.description AS "repository description", maxTypeCount.average, maxTypeCount.maxCount
        FROM Repository, maxTypeCount, OwnsRepo, _User
        WHERE Repository.watchersCount = maxTypeCount.maxCount AND OwnsRepo.repoID = Repository.repoID AND _User.userID = OwnsRepo.userID;
    ELSE
        WITH maxTypeCount AS (
        SELECT MAX(nothing) AS maxCount, AVG(nothing) AS average
        FROM Repository)
        SELECT _User.userID AS "user ID", _User.login AS "user login",  _User.url AS "user URL", Repository.name AS "repository name", Repository.description AS "repository description", maxTypeCount.average, maxTypeCount.maxCount
        FROM Repository, maxTypeCount, OwnsRepo, _User
        WHERE Repository.nothing = maxTypeCount.maxCount AND OwnsRepo.repoID = Repository.repoID AND _User.userID = OwnsRepo.userID;
    END IF;
END; //

DELIMITER ;

-- OrganizationLocation

DELIMITER //

DROP PROCEDURE IF EXISTS OrganizationLocation //

CREATE PROCEDURE OrganizationLocation(IN _location VARCHAR(150))
BEGIN
    SELECT Org.orgID AS "orgnization ID", Org.name, Org.createdAt, HasPackage.packageName AS "package name"
    FROM Organization AS Org, HasPackage
    WHERE location LIKE CONCAT('%',  _location, '%') AND Org.orgID = HasPackage.orgID
    ORDER BY Org.createdAt ASC;
END; //

DELIMITER ;

-- OrganizationLocation

DELIMITER //

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

-- PackageVersion

DELIMITER //

DROP PROCEDURE IF EXISTS PackageVersion //

CREATE PROCEDURE PackageVersion()
BEGIN
    SELECT '1.0.0' AS packageVersion, AVG(stars) AS "average stars", AVG(score) AS "average score", count(*) AS "count"
    FROM Package
    Where version LIKE "1.%"
    UNION
    SELECT '2.0.0' AS packageVersion, AVG(stars) AS "average stars", AVG(score) AS "average score", count(*) AS "count"
    FROM Package
    Where version LIKE "2.%"
    UNION
    SELECT '3.0.0' AS packageVersion, AVG(stars) AS "average stars", AVG(score) AS "average score", count(*) AS "count"
    FROM Package
    Where version LIKE "3.%"
    UNION
    SELECT '4.0.0' AS packageVersion, AVG(stars) AS "average stars", AVG(score) AS "average score", count(*) AS "count"
    FROM Package
    Where version LIKE "4.%"
    ORDER BY packageVersion ASC;
END; //

DELIMITER ;

-- DisplayTables

DELIMITER //

DROP PROCEDURE IF EXISTS DisplayTables //

CREATE PROCEDURE DisplayTables()
BEGIN
    SELECT *
    FROM Organization;

    SELECT *
    FROM _User;

    SELECT *
    FROM Package;

    SELECT *
    FROM HasPackage;
END; //

DELIMITER ;

-- InsertHasPackage

DELIMITER //

DROP PROCEDURE IF EXISTS InsertHasPackage //

CREATE PROCEDURE InsertHasPackage(IN o_id VARCHAR(150), IN p_name VARCHAR(150))
BEGIN
    INSERT INTO HasPackage(orgID, packageName) VALUES (o_id, p_name);
END; //

DELIMITER ;

-- InsertRepository

DELIMITER //

DROP PROCEDURE IF EXISTS InsertRepository //

CREATE PROCEDURE InsertRepository(IN repoIDParam VARCHAR(150), IN nameParam VARCHAR(100), IN descriptionParam VARCHAR(600), IN urlParam VARCHAR(100), IN forksCountParam VARCHAR(150), IN stargazersCountParam VARCHAR(150), IN watchersCountParam VARCHAR(150), IN openIssuesCountParam VARCHAR(150))
BEGIN
    INSERT INTO Repository(repoID, name, description, url, forksCount, stargazersCount, watchersCount, openIssuesCount) VALUES (repoIDParam, nameParam, descriptionParam, urlParam, forksCountParam, stargazersCountParam, watchersCountParam, openIssuesCountParam);
END; //

DELIMITER ;

-- InsertOwnsRepo

DELIMITER //

DROP PROCEDURE IF EXISTS InsertOwnsRepo //

CREATE PROCEDURE InsertOwnsRepo(IN repoIDParam VARCHAR(150), IN userIDParam VARCHAR(150))
BEGIN
    INSERT INTO OwnsRepo(repoID, userID) VALUES (repoIDParam, userIDParam);
END; //

DELIMITER ;


-- InsertPackage

DELIMITER //

DROP PROCEDURE IF EXISTS InsertPackage //

CREATE PROCEDURE InsertPackage(IN p_name VARCHAR(150), IN p_version VARCHAR(150), IN p_stars VARCHAR(150), IN p_score VARCHAR(150))
BEGIN
    INSERT INTO Package(packageName, version, stars, score) VALUES (p_name, p_version, p_stars, p_score);
END; //

DELIMITER ;

-- InsertUser

DELIMITER //

DROP PROCEDURE IF EXISTS InsertUser //

CREATE PROCEDURE InsertUser(IN u_id VARCHAR(150), IN u_login VARCHAR(150), IN u_url VARCHAR(150), IN u_type VARCHAR(150))
BEGIN
    INSERT INTO _User(userID, login, url, type) VALUES (u_id, u_login, u_url, u_type);
END; //

DELIMITER ;

-- FindPackage

DELIMITER //

DROP FUNCTION IF EXISTS FindPackage //

CREATE FUNCTION FindPackage(packageNameParam VARCHAR(10))
RETURNS BOOLEAN
BEGIN
      RETURN (SELECT COUNT(*) FROM Package WHERE packageName = packageNameParam);
END; //

DELIMITER ;

-- DeletePackage

DELIMITER //

DROP PROCEDURE IF EXISTS DeletePackage //

CREATE PROCEDURE DeletePackage(IN packageNameParam VARCHAR(10))
BEGIN
    IF FindPackage(packageNameParam) THEN
        DELETE FROM Package WHERE packageName = packageNameParam;
    ELSE
        SELECT 'ERROR: package not found' AS error;
    END IF;
END; //

DELIMITER ;

-- FindUser

DELIMITER //

DROP FUNCTION IF EXISTS FindUser //

CREATE FUNCTION FindUser(userIDParam INT)
RETURNS BOOLEAN
BEGIN
      RETURN (SELECT COUNT(*) FROM _User WHERE userID = userIDParam);
END; //

DELIMITER ;

-- DeleteUser

DELIMITER //

DROP PROCEDURE IF EXISTS DeleteUser //

CREATE PROCEDURE DeleteUser(IN userIDParam VARCHAR(10))
BEGIN
    IF FindUser(userIDParam) THEN
        DELETE FROM _User WHERE userID = userIDParam;
    ELSE
        SELECT 'ERROR: user not found' AS error;
    END IF;
END; //

DELIMITER ;

-- FindPackageDownloads

DELIMITER //

DROP FUNCTION IF EXISTS FindPackageDownloads //

CREATE FUNCTION FindPackageDownloads(packageNameParam VARCHAR(10))
RETURNS BOOLEAN
BEGIN
      RETURN (SELECT COUNT(*) FROM Downloads WHERE packageName = packageNameParam);
END; //

DELIMITER ;

-- DeletePackageDownloads

DELIMITER //

DROP PROCEDURE IF EXISTS DeletePackageDownloads //

CREATE PROCEDURE DeletePackageDownloads(IN packageNameParam VARCHAR(10))
BEGIN
    IF FindPackageDownloads(packageNameParam) THEN
        DELETE FROM Downloads WHERE packageName = packageNameParam;
    ELSE
        SELECT 'ERROR: package not found' AS error;
    END IF;
END; //

DELIMITER ;

DELIMITER //

-- DownloadsGained

DROP PROCEDURE IF EXISTS DownloadsGained //

CREATE PROCEDURE DownloadsGained(IN d_start VARCHAR(100),IN d_end VARCHAR(100))
BEGIN
    WITH D AS (
        SELECT packageName, SUM(downloads) AS 'downloadsGained'
        FROM DownloadsOnDate
        WHERE _day > d_start AND _day <= d_end
        GROUP BY packageName
    )
    SELECT D.packageName, version, downloadsGained
    FROM D JOIN Package AS P ON D.packageName = P.packageName
    ORDER BY downloadsGained DESC, D.packageName ASC;
END; //

DELIMITER ;
