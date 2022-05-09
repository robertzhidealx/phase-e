<head><title>Organizations Info</title></head>
<body>
<?php

    // open a connection to dbase server 
    include 'open.php';
    // collect the posted value in a variable called $item
    $dataType = $_POST['type'];

    if (!empty($dataType)) {
        if ($dataType == "Organization") {
            echo "Organization";
        } else if ($dataType == "User") {
            $userID = $_POST['p1'];
            $login = $_POST['p2'];
            $url = $_POST['p3'];
            $type = $_POST['p4'];
            if ($stmt = $conn->prepare("CALL InsertUser(?,?,?,?)")) {
                $stmt->bind_param("ssss", $userID, $login, $url, $type);
                if ($stmt->execute()) {
                    echo "User insertion success";
                } else {
                    echo "Error: Wrong data type or data already exists<br>";
                }
            } else {
                //Call to execute failed, e.g. because server is no longer reachable,
                //or because supplied values are of the wrong type
                echo "Call to InsertUser failed.<br>";
            }
            //Close down the prepared statement
            $stmt->close();
        } else if ($dataType == "Package") {
            $packageName = $_POST['p1'];
            $version = $_POST['p2'];
            $stars = $_POST['p3'];
            $score = $_POST['p4'];
            if ($stmt = $conn->prepare("CALL InsertPackage(?,?,?,?)")) {
                $stmt->bind_param("ssss", $packageName, $version, $stars, $score);
                if ($stmt->execute()) {
                    echo "Package insertion success";
                } else {
                    echo "Error: Wrong data type or data already exists<br>";
                }
            } else {
                //Call to execute failed, e.g. because server is no longer reachable,
                //or because supplied values are of the wrong type
                echo "Call to InsertPackage failed.<br>";
            }
            //Close down the prepared statement
            $stmt->close();
        } else if ($dataType == "HasPackage") {
            $orgID = $_POST['p1'];
            $packageName = $_POST['p2'];
            if ($stmt = $conn->prepare("CALL InsertHasPackage(?,?)")) {
                $stmt->bind_param("ss", $orgID, $packageName);
                if ($stmt->execute()) {
                    echo "HasPackage insertion success";
                } else {
                    echo "Error: Wrong data type or foreign key constraints failed or data already exists<br>";
                    echo "Foreign key constraints: make sure the organization (orgID) and package (packageName) are in the current database.<br>";
                }
            } else {
                //Call to execute failed, e.g. because server is no longer reachable,
                //or because supplied values are of the wrong type
                echo "Call to InsertHasPackage failed.<br>";
            }
            //Close down the prepared statement
            $stmt->close();
        } else if ($dataType == "Repository") {
            $repoID = $_POST['p1'];
            $name = $_POST['p2'];
            $description = $_POST['p3'];
            $url = $_POST['p4'];
            $forksCount = $_POST['p5'];
            $stargazersCount = $_POST['p6'];
            $watchersCount = $_POST['p7'];
            $openIssuesCount = $_POST['p8'];
            if ($stmt = $conn->prepare("CALL InsertRepository(?,?,?,?,?,?,?,?)")) {
                $stmt->bind_param("ssssssss", $repoID, $name, $description, $url, $forksCount, $stargazersCount, $watchersCount, $openIssuesCount);
                if ($stmt->execute()) {
                    echo "Repository insertion success";
                } else {
                    echo "Error: Wrong data type or data already exists<br>";
                }
            } else {
                //Call to execute failed, e.g. because server is no longer reachable,
                //or because supplied values are of the wrong type
                echo "Call to InsertRepository failed.<br>";
            }
            //Close down the prepared statement
            $stmt->close();
        } else if ($dataType == "OwnsRepo") {
            $repoID = $_POST['p1'];
            $userID = $_POST['p2'];
            if ($stmt = $conn->prepare("CALL InsertOwnsRepo(?,?)")) {
                $stmt->bind_param("ss", $repoID, $userID);
                if ($stmt->execute()) {
                    echo "OwnsRepo insertion success";
                } else {
                    echo "Error: Wrong data type or foreign key constraints failed or data already exists<br>";
                    echo "Foreign key constraints: make sure the repository (repoID) and user (userID) are in the current database.<br>";
                }
            } else {
                //Call to execute failed, e.g. because server is no longer reachable,
                //or because supplied values are of the wrong type
                echo "Call to InsertOwnsRepo failed.<br>";
            }
            //Close down the prepared statement
            $stmt->close();
        }
    } else {
        echo "invalid input";
    }
    $conn->close();

?>
</body> 
