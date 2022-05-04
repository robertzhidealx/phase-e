<head><title>Rank GitHub Organizations</title></head>
<body>
<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $year = $_POST['year'];

    echo "<h2> list Orgnization ID, Orgnization Name, created, updated date, and users num,
    -- by created date ascending order </h2><br>";

    if (!empty($year)) {
        $result = $conn->query("CALL OrganizationStats('".$year."');");
        if ($result) {
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>Orgnization ID</td><td>Orgnization Nam</td><td>created date</td><td>updated date</td><td>users num</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["orgnization ID"]."</td><td>".$row["name"]."</td><td>".$row["createdAt"]."</td><td>".$row["updatedAt"]."</td><td>".$row["userNum"]."</td></tr>";
            }
            
            echo "</table>";
        } else {
            echo "Call to OrganizationStats<br>";
        }
    } else {
        echo "invalid input";
    }
    $conn->close();

?>
</body>