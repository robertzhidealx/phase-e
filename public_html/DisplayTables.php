<head><title>Organizations Info</title></head>
<body>
<?php

    // open a connection to dbase server 
    include 'open.php';

    //echo "<h2>Display tables.</h2>";

    if ($conn->multi_query("CALL DisplayTables();")) {
        $result = $conn->store_result();
        if ($result) {
            echo "<h2>Organization<h2>";
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>orgnization ID</td><td>login</td><td>name</td><td>description</td><td>email</td><td>created date</td><td>updated date</td><td>location</td><td>type</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["orgID"]."</td><td>".$row["login"]."</td><td>".$row["name"]."</td><td>".$row["description"]."</td><td>".$row["email"]."</td><td>".$row["createdAt"]."</td><td>".$row["updatedAt"]."</td><td>".$row["location"]."</td><td>".$row["type"]."</td></tr>";
            }
            echo "</table><br>";
        } else {
            echo "Call to Organization failed<br>";
        }
        $conn->next_result();
        $result = $conn->store_result();

        if ($result) {
            echo "<h2>User<h2>";
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>user ID</td><td>login</td><td>url</td><td>type</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["userID"]."</td><td>".$row["login"]."</td><td>".$row["url"]."</td><td>".$row["type"]."</td></tr>";
            }
            echo "</table><br>";
        } else {
            echo "Call to User failed<br>";
        }

        $conn->next_result();
        $result = $conn->store_result();

        if ($result) {
            echo "<h2>Package<h2>";
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>package name</td><td>version</td><td>stars</td><td>score</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["packageName"]."</td><td>".$row["version"]."</td><td>".$row["stars"]."</td><td>".$row["score"]."</td></tr>";
            }
            echo "</table><br>";
        } else {
            echo "Call to User failed<br>";
        }

        $conn->next_result();
        $result = $conn->store_result();

        if ($result) {
            echo "<h2>HasPackage<h2>";
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>orgnization ID</td><td>package name</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["orgID"]."</td><td>".$row["packageName"]."</td></tr>";
            }
            echo "</table><br>";
        } else {
            echo "Call to User failed<br>";
        }

   
     } else {
        echo "Call to DisplayTables() failed<br>";
     }

    $conn->close();

?>
</body>