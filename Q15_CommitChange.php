<head><title>Rank GitHub Organizations</title></head>
<body>
<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $type = $_POST['type'];

    echo "<h2>For orgnization located in San Francisco, list their ID, name, create date, and name of package they own</h2><br>";

    if (!empty($type)) {
        $result = $conn->query("CALL CommitChange('".$type."');");
        if ($result) {
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>commit ID</td><td>commit additions</td><td>commit deletions</td><td>repository name</td><td>author</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["commitID"]."</td><td>".$row["commit additions"]."</td><td>".$row["commit deletions"]."</td><td>".$row["repository name"]."</td><td>".$row["author"]."</td></tr>";
            }
            
            echo "</table>";
        } else {
            echo "Call to CommitChange failed<br>";
        }
    } else {
        echo "invalid input";
    }
    $conn->close();

?>
</body>