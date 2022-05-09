<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    //$num = "2021-04-12";
    $start = $_POST['start'];
    $end = $_POST['end'];

    echo "<h2>Rank packages by downloads gained</h2>";

    $DataPoints = array();

    if (!empty($start) && !empty($end)) {
        if ($stmt = $conn->prepare("CALL DownloadsGained(?,?)")) {
            $stmt->bind_param("ss", $start, $end);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result) {
                    if ($result->num_rows > 0){
                        echo "<table border=\"2px solid black\">";
                        echo "<tr><td>package name</td><td>version</td><td>downloadsGained</td></tr>";
                        foreach($result as $row) {
                            echo "<tr><td>".$row["packageName"]."</td><td>".$row["version"]."</td><td>".$row["downloadsGained"]."</td></tr>";
                            array_push($DataPoints, array( "label"=> $row["packageName"], "y"=> $row["downloadsGained"]));
                        }
                        echo "</table><br>";
                    } else {
                        echo "No package fit the requirement.";
                    }
            
                } else {
                    echo "Call to DownloadsGained failed<br>";
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
        echo "invalid input";
    }
    $conn->close();

?>

<html>
    <head>
        <title>Packages Downloads Gained</title>
        <script>
        window.onload = function () {
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                exportEnabled: true,
                theme: "light1", // "light1", "light2", "dark1", "dark2"
                title:{
                    text: "Packages Downloads Gained"
                },
                data: [{
                    type: "column", //change type to column, bar, line, area, pie, etc
                    dataPoints: <?php echo json_encode($DataPoints, JSON_NUMERIC_CHECK); ?>
                }]
            });
            chart.render();
        }
        </script>
    </head>
    <body>
        <div id="chartContainer" style="height: 400px; width: 100%;"></div>
        <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    </body>
</html>