<head><title>Rank GitHub Organizations</title></head>
<body>
<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $keyword = $_POST['keyword'];

    echo "<h2>Rank GitHub organizations</h2><br>";

    if (!empty($keyword)) {
        $result = $conn->query("CALL RankGitHubOrganizations('".$keyword."');");
        if ($result) {
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>user ID</td><td>user name</td><td>total stars</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["userID"]."</td><td>".$row["username"]."</td><td>".$row["totalStars"]."</td></tr>";
            }
            
            echo "</table>";
        } else {
            echo "Call to RankGitHubOrganizations failed<br>";
        }
    } else {
        echo "not set";
    }
    $conn->close();

?>
</body>
