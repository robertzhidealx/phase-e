<head><title>PackageVersion</title></head>
<body>
<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $type = $_POST['type'];

    echo "<h2>Calculate average stars and scores for selected package versions, and list how many package in each category</h2><br>";

    if (!empty($type)) {
        $result = $conn->query("CALL PackageVersion('".$type."');");
        if ($result) {
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>Package Version</td><td>Average Stars</td><td>Average Score</td><td>Package Count</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["packageVersion"]."</td><td>".$row["average stars"]."</td><td>".$row["average score"]."</td><td>".$row["count"]."</td></tr>";
            }
            
            echo "</table>";
        } else {
            echo "Call to PackageVersion failed<br>";
        }
    } else {
        echo "invalid input";
    }
    $conn->close();

?>
</body>