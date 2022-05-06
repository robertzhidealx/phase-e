<head><title>Rank GitHub Organizations</title></head>
<body>
<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $count = $_POST['count'];

    echo "<h2>list its userID, userLogin, userURL, and repo name and description</h2><br>";

    if (!empty($count)) {
        $result = $conn->query("CALL MostCount('".$count."');");
        if ($result) {
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>user ID</td><td>user login</td><td>user URL</td><td>repository name</td><td>repository description</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["user ID"]."</td><td>".$row["user login"]."</td><td>".$row["user URL"]."</td><td>".$row["repository name"]."</td><td>".$row["repository description"]."</td></tr>";
            }
            
            echo "</table>";
        } else {
            echo "Invalid input, call to MostCount failed<br>";
        }
    } else {
        echo "invalid input";
    }
    $conn->close();

?>
</body>