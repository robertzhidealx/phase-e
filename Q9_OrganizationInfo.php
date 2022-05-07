<head><title>Rank GitHub Organizations</title></head>
<body>
<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $orgName = $_POST['orgName'];

    echo "<h2>List organization ID, name, description, and the total number of stars in its packages, by descending number of stars.</h2><br>";

    if (!empty($orgName)) {
        $result = $conn->query("CALL OrganizationInfo('".$orgName."');");
        if ($result) {
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>Orgnization ID</td><td>name</td><td>description</td><td>total stars</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["orgnization ID"]."</td><td>".$row["name"]."</td><td>".$row["description"]."</td><td>".$row["total stars"]."</td></tr>";
            }
            
            echo "</table>";
        } else {
            echo "Call to OrganizationInfo failed<br>";
        }
    } else {
        echo "invalid input";
    }
    $conn->close();

?>
</body>