<head><title>Rank GitHub Organizations</title></head>
<body>
<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $location = $_POST['location'];

    echo "<h2>For orgnization located in San Francisco, list their ID, name, create date, and name of package they own</h2><br>";

    if (!empty($location)) {
        $result = $conn->query("CALL OrganizationLocation('".$location."');");
        if ($result) {
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>orgnization ID</td><td>name</td><td>created date</td><td>package name</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["orgnization ID"]."</td><td>".$row["name"]."</td><td>".$row["createdAt"]."</td><td>".$row["package name"]."</td></tr>";
            }
            
            echo "</table>";
        } else {
            echo "Call to OrganizationLocation failed<br>";
        }
    } else {
        echo "invalid input";
    }
    $conn->close();

?>
</body>