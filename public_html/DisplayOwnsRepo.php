<head><title>Display OwnsRepo</title></head>
<body>
<?php

    include 'open.php';

    echo "<h2>Display OwnsRepo</h2>";

    if ($result = $conn->query("SELECT * FROM OwnsRepo")) {
        if (($result) && ($result->num_rows != 0)) {
            echo "<h2>OwnsRepo<h2>";
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>repository ID</td><td>user ID</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["repoID"]."</td><td>".$row["userID"]."</td></tr>";
            }
            echo "</table><br>";
        } else {
            echo "No data found";
        }
    } else {
        echo "Call to view OwnsRepo failed<br>";
    }
    $conn->close();

?>
</body>
