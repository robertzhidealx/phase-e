<head><title>Display Organization</title></head>
<body>
<?php

    include 'open.php';

    echo "<h2>Display Organization</h2>";

    if ($result = $conn->query("SELECT * FROM Organization")) {
        if (($result) && ($result->num_rows != 0)) {
            echo "<h2>Organization<h2>";
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>orgnization ID</td><td>login</td><td>name</td><td>description</td><td>email</td><td>created date</td><td>updated date</td><td>location</td><td>type</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["orgID"]."</td><td>".$row["login"]."</td><td>".$row["name"]."</td><td>".$row["description"]."</td><td>".$row["email"]."</td><td>".$row["createdAt"]."</td><td>".$row["updatedAt"]."</td><td>".$row["location"]."</td><td>".$row["type"]."</td></tr>";
            }
            echo "</table><br>";
        } else {
            echo "No data found";
        }
    } else {
        echo "Call to view Organization failed<br>";
    }
    $conn->close();

?>
</body>
