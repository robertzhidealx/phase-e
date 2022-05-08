-- Jiaxuan Zhang, jzhan239
-- Jessie Luo, jluo30
DROP TABLE IF EXISTS DownloadsOnDate;

DROP TABLE IF EXISTS Downloads;

DROP TABLE IF EXISTS HasPackage;

DROP TABLE IF EXISTS Package;

DROP TABLE IF EXISTS OwnsRepo;

DROP TABLE IF EXISTS InOrg;

DROP TABLE IF EXISTS Organization;

DROP TABLE IF EXISTS _User;

DROP TABLE IF EXISTS _Commit;

DROP TABLE IF EXISTS CommitStats;

DROP TABLE IF EXISTS IssueCreator;

DROP TABLE IF EXISTS Issue;

DROP TABLE IF EXISTS Repository;

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

LOAD DATA LOCAL INFILE './db_small/organization-small.txt' INTO TABLE Organization FIELDS TERMINATED BY ',';

-- User(userID, login, url, type)
CREATE TABLE _User(
  userID INT NOT NULL,
  login VARCHAR(100),
  url VARCHAR(100),
  type VARCHAR(200),
  PRIMARY KEY(userID)
);

LOAD DATA LOCAL INFILE './db_small/user-small.txt' INTO TABLE _User FIELDS TERMINATED BY ',';

-- Package (packageName, version, star, score)
CREATE TABLE Package(
  packageName varchar(100) NOT NULL,
  version varchar(100),
  stars INT,
  score FLOAT,
  PRIMARY KEY(packageName)
);

LOAD DATA LOCAL INFILE './db_small/package-small.txt' INTO TABLE Package FIELDS TERMINATED BY ',';

-- HasPackage(orgID, packageName)
CREATE TABLE HasPackage(
  orgID INT NOT NULL,
  packageName varchar(100) NOT NULL,
  PRIMARY KEY(orgID, packageName),
  FOREIGN KEY(orgID) REFERENCES Organization(orgID) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(packageName) REFERENCES Package(packageName) ON DELETE CASCADE ON UPDATE CASCADE
);

LOAD DATA LOCAL INFILE './db_small/hasPackage-small.txt' INTO TABLE HasPackage FIELDS TERMINATED BY ',';

-- Repository(repoID, name, description, url, forksCount, stargazersCount, watchersCount, openIssuesCount)
CREATE TABLE Repository(
  repoID INT,
  name varchar(100),
  description varchar(100),
  url varchar(100),
  forksCount INT,
  stargazersCount INT,
  watchersCount INT,
  openIssuesCount INT,
  PRIMARY KEY(repoID)
);

LOAD DATA LOCAL INFILE './db_small/repository-small.txt' INTO TABLE Repository FIELDS TERMINATED BY ',';

-- Downloads(packageName, startDate, endDate, downloadsCount) 
CREATE TABLE Downloads(
  packageName varchar(100),
  startDate DATE,
  endDate DATE,
  downloadsCount INT,
  PRIMARY KEY(packageName),
  FOREIGN KEY(packageName) REFERENCES Package(packageName) ON DELETE CASCADE ON UPDATE CASCADE
);

LOAD DATA LOCAL INFILE './db_small/downloads-small.txt' INTO TABLE Downloads FIELDS TERMINATED BY ',';

-- DownloadsOnDate(packageName, day, downloads)
CREATE TABLE DownloadsOnDate(
  packageName varchar(100),
  _day DATE,
  downloads INT,
  PRIMARY KEY(packageName, _day, downloads),
  FOREIGN KEY(packageName) REFERENCES Package(packageName) ON DELETE CASCADE ON UPDATE CASCADE
);

LOAD DATA LOCAL INFILE './db_small/downloadsOnDate-small.txt' INTO TABLE DownloadsOnDate FIELDS TERMINATED BY ',';

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

LOAD DATA LOCAL INFILE './db_small/commit-small.txt' INTO TABLE _Commit FIELDS TERMINATED BY ',';

-- CommitStats(commitID, additions, deletions, total) // multivalued attribute of Commit
CREATE TABLE CommitStats(
  commitID varchar(200),
  additions INT,
  deletions INT,
  total INT,
  PRIMARY KEY(commitID)
);

LOAD DATA LOCAL INFILE './db_small/commitStats-small.txt' INTO TABLE CommitStats FIELDS TERMINATED BY ',';

-- Issue(issueID, repoID, title, state) 
CREATE TABLE Issue(
  issueID INT,
  repoURL varchar(500),
  title varchar(500),
  state varchar(500),
  PRIMARY KEY(issueID, repoURL)
);

LOAD DATA LOCAL INFILE './db_small/issue-small.txt' INTO TABLE Issue FIELDS TERMINATED BY ',';

-- IssueCreator(issueID, creatorID, creatorLogin)
CREATE TABLE IssueCreator(
  issueID INT,
  assigneeID INT,
  assigneeLogin varchar(100),
  PRIMARY KEY(issueID, assigneeID, assigneeLogin),
  FOREIGN KEY(issueID) REFERENCES Issue(issueID) ON DELETE CASCADE ON UPDATE CASCADE
);

LOAD DATA LOCAL INFILE './db_small/issueCreator-small.txt' INTO TABLE IssueCreator FIELDS TERMINATED BY ',';

-- InOrg(userID, orgID) 
CREATE TABLE InOrg(
  userID INT NOT NULL,
  orgID INT NOT NULL,
  PRIMARY KEY(userID, orgID),
  FOREIGN KEY(orgID) REFERENCES Organization(orgID) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(userID) REFERENCES _User(userID) ON DELETE CASCADE ON UPDATE CASCADE
);

LOAD DATA LOCAL INFILE './db_small/inOrg-small.txt' INTO TABLE InOrg FIELDS TERMINATED BY ',';

-- OwnsRepo(repoID, userID)
CREATE TABLE OwnsRepo(
  repoID INT NOT NULL,
  userID INT NOT NULL,
  PRIMARY KEY(repoID, userID),
  FOREIGN KEY(repoID) REFERENCES Repository(repoID) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(userID) REFERENCES _User(userID) ON DELETE CASCADE ON UPDATE CASCADE
);

LOAD DATA LOCAL INFILE './db_small/ownsRepo-small.txt' INTO TABLE OwnsRepo FIELDS TERMINATED BY ',';

-- Stored procedures
