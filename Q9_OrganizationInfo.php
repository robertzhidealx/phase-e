<head><title>Rank GitHub Organizations</title></head>
<body>
<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $name = $_POST['name'];

    echo "<h2>List package name, score, and total downloads for package that gets more than 100000 downloads on $date</h2><br>";

    if (!empty($date)) {
        $result = $conn->query("CALL OrganizationInfo('".$name."');");
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