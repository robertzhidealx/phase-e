<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $num = $_POST['num'];

    echo "<h2>List package name, score, and total downloads for package that gets more than ".$num." downloads from 2020-10-01 to 2020-10-05</h2><br>";

    $DataPoints = array();

    if (!empty($num)) {
        $result = $conn->query("CALL DownloadsNum('".$num."');");
        if ($result) {
            if ($result->num_rows > 0){
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>package name</td><td>score</td><td>downloads count</td></tr>";
                foreach($result as $row) {
                    echo "<tr><td>".$row["packageName"]."</td><td>".$row["score"]."</td><td>".$row["total downloads"]."</td></tr>";
                    array_push($DataPoints, array( "label"=> $row["packageName"], "y"=> $row["score"]));
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

<html>
    <head>
        <title>Rank GitHub Organizations</title>
        <script>
        window.onload = function () {
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                exportEnabled: true,
                theme: "light1", // "light1", "light2", "dark1", "dark2"
                title:{
                    text: "Package Score"
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