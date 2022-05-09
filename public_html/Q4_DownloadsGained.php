<?php

    // open a connection to dbase server 
    include 'open.php';

    $start = $_POST['start'];
    $end = $_POST['end'];

    echo "<h2>Rank packages by downloads gained</h2>";

    $dataPoints = array();

    $pattern = "/[0-9]{4}-[0-9]{2}-[0-9]{2}/i";    

    if (!empty($start) && !empty($end)) {
        if (strlen($start) > 10 || strlen($end) > 10 || !preg_match($pattern, $start) || !preg_match($pattern, $end)) {
            echo "Invalid date. Please format into YYYY-MM-DD.";
            exit();
        }
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
                            array_push($dataPoints, array( "label"=> $row["packageName"], "y"=> $row["downloadsGained"]));
                        }
                        echo "</table><br>";
                    } else {
                        echo "No package fits the requirement.";
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
        echo "You have to enter the start and end dates";
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
                    dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
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
