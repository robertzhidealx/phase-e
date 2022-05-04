<head><title>Rank GitHub Organizations</title></head>
<body>
<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $user = $_POST['committer'];

    echo "<h2>List package name, score, and total downloads for package that gets more than 100000 downloads on $date</h2><br>";

    if (!empty($user)) {
        $result = $conn->query("CALL VerifiedCommitsPercentage('".$user."');");
        if ($result) {
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>Percentage of verified commits committed by by Github</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["Percentage of verified commits committed by by Github"]."</td></tr>";
            }
            
            echo "</table>";
        } else {
            echo "Call to VerifiedCommitsPercentage failed<br>";
        }
    } else {
        echo "invalid input";
    }
    $conn->close();

?>
</body>