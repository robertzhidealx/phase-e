<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $type = $_POST['type'];

    echo "<h2>List commitID, amount of additions and deletions, its author and the name of repo committed to order by descending additions.</h2><br>";

    $dataPoints = array();

    $show = true;

    if (!empty($type)) {
        if ($stmt = $conn->prepare("CALL CommitChange(?)")) {
            $stmt->bind_param("s", $type);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result) {
                    if ($result->num_rows > 0){
                        echo "<table border=\"2px solid black\">";
                        echo "<tr><td>commit ID</td><td>commit additions</td><td>commit deletions</td><td>repository name</td><td>author</td></tr>";

                        foreach($result as $row) {
                            echo "<tr><td>".$row["commitID"]."</td><td>".$row["commit additions"]."</td><td>".$row["commit deletions"]."</td><td>".$row["repository name"]."</td><td>".$row["author"]."</td></tr>";
                            array_push($dataPoints, array( "label"=> $row["repository name"], "y"=> $row["total"]));
                        }
            
                        echo "</table>";
                    } else {
                        $show = false;
                        echo "No result fits the requirement.";
                    }
                } else {
                    echo "Call to CommitChange failed<br>";
                }
            } else {
                //Call to execute failed, e.g. because server is no longer reachable,
                //or because supplied values are of the wrong type
                echo "Execute failed.<br>";
            }

            //Close down the prepared statement
            $stmt->close();
        }
    } else {
        $show = false;
        echo "invalid input";
    }
    $conn->close();

?>

<html>
    <head>
        <title>Commit Change</title>
        <script>
        window.onload = function () {
        var show = <?php echo json_encode($show); ?>;

            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                exportEnabled: true,
                theme: "light1", // "light1", "light2", "dark1", "dark2"
                title:{
                    text: "Repository Commits Total (Additions + Deletions)"
                },
                data: [{
                    type: "column", //change type to column, bar, line, area, pie, etc
                    dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                }]
            });
            if (show) chart.render();
        }
        </script>
    </head>
    <body>
        <div id="chartContainer" style="height: 400px; width: 100%;"></div>
        <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    </body>
</html>
