<head><title>Organizations Info</title></head>
<body>
<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $dataType = $_POST['type'];

    echo "<h2>List organization ID, name, description, and the total number of stars in its packages, by descending number of stars.</h2>";

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
                    echo "Foreign key constraints: make sure the organization(orgID) and package(packageName) is in current database.<br>";
                }
            } else {
                //Call to execute failed, e.g. because server is no longer reachable,
                //or because supplied values are of the wrong type
                echo "Call to InsertHasPackage failed.<br>";
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