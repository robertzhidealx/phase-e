<head><title>Display Package</title></head>
<body>
<?php

    include 'open.php';

    echo "<h2>Display Package</h2>";

    if ($result = $conn->query("SELECT * FROM Package")) {
        if (($result) && ($result->num_rows != 0)) {
            echo "<h2>Package<h2>";
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>package name</td><td>version</td><td>stars</td><td>score</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["packageName"]."</td><td>".$row["version"]."</td><td>".$row["stars"]."</td><td>".$row["score"]."</td></tr>";
            }
            echo "</table><br>";
        } else {
            echo "No data found";
        }
    } else {
        echo "Call to view Package failed<br>";
    }
    $conn->close();

?>
</body>
