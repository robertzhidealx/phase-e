<head><title>Rank GitHub Organizations</title></head>
<body>
<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $num = $_POST['num'];

    echo "<h2>List package name, score, and total downloads for package that gets more than 100000 downloads on $date</h2><br>";

    if (!empty($num)) {
        $result = $conn->query("CALL DownloadsNum('".$num."');");
        if ($result) {
            if ($result->num_rows > 0){
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>package name</td><td>score</td><td>downloads count</td></tr>";
                foreach($result as $row) {
                    echo "<tr><td>".$row["packageName"]."</td><td>".$row["score"]."</td><td>".$row["total downloads"]."</td></tr>";
                }
                echo "</table>";
            } else {
                echo "No package fit the requirement.";
            }
            
        } else {
            echo "Call to DownloadsNum failed<br>";
        }
    } else {
        echo "invalid input";
    }
    $conn->close();

?>
</body>