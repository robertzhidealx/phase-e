<head><title>Organizations Info</title></head>
<body>
<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $orgName = $_POST['orgName'];

    echo "<h2>List organization ID, name, description, and the total number of stars in its packages, by descending number of stars.</h2>";

    if (!empty($orgName)) {
        if ($stmt = $conn->prepare("CALL OrganizationInfo(?)")) {
            $stmt->bind_param("s", $orgName);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result) {
                    echo "<table border=\"2px solid black\">";
                    echo "<tr><td>Orgnization ID</td><td>name</td><td>description</td><td>total stars</td></tr>";

                    foreach($result as $row) {
                        echo "<tr><td>".$row["orgnization ID"]."</td><td>".$row["name"]."</td><td>".$row["description"]."</td><td>".$row["total stars"]."</td></tr>";
                    }
            
                    echo "</table><br>";
                } else {
                    echo "Call to OrganizationInfo failed<br>";
                }
            } else {
                //Call to execute failed, e.g. because server is no longer reachable,
                //or because supplied values are of the wrong type
                echo "Execute failed.<br>";
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