<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item

    echo "<h2>Calculate average stars and scores for package versions, and list how many package in each category</h2><br>";
    
    $DataPoints = array();

    //if (!empty($v1)) {
        $result = $conn->query("CALL PackageVersion();");
        if ($result) {
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>Package Version</td><td>Average Stars</td><td>Average Score</td><td>Package Count</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["packageVersion"]."</td><td>".$row["average stars"]."</td><td>".$row["average score"]."</td><td>".$row["count"]."</td></tr>";
                array_push($DataPoints, array( "label"=> $row["packageVersion"], "y"=> $row["average stars"]));
            }
            
            echo "</table>";
        } else {
            echo "Call to PackageVersion failed<br>";
        }
    // } else {
    //     echo "invalid input";
    // }
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
                    text: "Average stars for Package of different versions"
                },
                data: [{
                    type: "line", //change type to column, bar, line, area, pie, etc
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
