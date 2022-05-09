<head><title>Organization Location</title></head>
<body>
<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $location = $_POST['location'];

    echo "<h2>For orgnization located in ".$location.", list their ID, name, create date, and name of package they own</h2><br>";

    if (!empty($location)) {
        if ($stmt = $conn->prepare("CALL OrganizationLocation(?)")) {
            $stmt->bind_param("s", $location);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result) {
                    if ($result->num_rows > 0){
                        echo "<table border=\"2px solid black\">";
                        echo "<tr><td>orgnization ID</td><td>name</td><td>created date</td><td>package name</td></tr>";

                        foreach($result as $row) {
                            echo "<tr><td>".$row["orgnization ID"]."</td><td>".$row["name"]."</td><td>".$row["createdAt"]."</td><td>".$row["package name"]."</td></tr>";
                        }
            
                        echo "</table>";
                    } else {
                        echo "No result fit the requirement.";
                    }
                } else {
                    echo "Call to OrganizationLocation failed<br>";
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