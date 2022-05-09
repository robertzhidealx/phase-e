<head><title>Display Repository</title></head>
<body>
<?php

    include 'open.php';

    echo "<h2>Display Repository</h2>";

    if ($result = $conn->query("SELECT * FROM Repository")) {
        if (($result) && ($result->num_rows != 0)) {
            echo "<h2>Repository<h2>";
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>repository ID</td><td>repository name</td><td>description</td><td>url</td><td>forks count</td><td>stargazers count</td><td>watchers count</td><td>open issues count</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["repoID"]."</td><td>".$row["name"]."</td><td>".$row["description"]."</td><td>".$row["url"]."</td><td>".$row["forksCount"]."</td><td>".$row["stargazersCount"]."</td><td>".$row["watchersCount"]."</td><td>".$row["openIssuesCount"]."</td></tr>";
            }
            echo "</table><br>";
        } else {
            echo "No data found";
        }
    } else {
        echo "Call to view Repository failed<br>";
    }
    $conn->close();

?>
</body>
