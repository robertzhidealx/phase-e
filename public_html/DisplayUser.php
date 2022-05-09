<head><title>Display User</title></head>
<body>
<?php

    include 'open.php';

    echo "<h2>Display User</h2>";

    if ($result = $conn->query("SELECT * FROM _User")) {
        if (($result) && ($result->num_rows != 0)) {
            echo "<h2>User<h2>";
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>user ID</td><td>login</td><td>url</td><td>type</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["userID"]."</td><td>".$row["login"]."</td><td>".$row["url"]."</td><td>".$row["type"]."</td></tr>";
            }
            echo "</table><br>";
        } else {
            echo "No data found";
        }
    } else {
        echo "Call to view User failed<br>";
    }
    $conn->close();

?>
</body>
