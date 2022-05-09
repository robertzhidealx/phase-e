<head><title>Display HasPackage</title></head>
<body>
<?php

    include 'open.php';

    echo "<h2>Display HasPackage</h2>";

    if ($result = $conn->query("SELECT * FROM HasPackage")) {
        if (($result) && ($result->num_rows != 0)) {
            echo "<h2>HasPackage<h2>";
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>orgnization ID</td><td>package name</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["orgID"]."</td><td>".$row["packageName"]."</td></tr>";
            }
            echo "</table><br>";
        } else {
            echo "No data found";
        }
    } else {
        echo "Call to view HasPackage failed<br>";
    }
    $conn->close();

?>
</body>
