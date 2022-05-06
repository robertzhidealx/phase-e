<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $count = $_POST['count'];

    echo "<h2>For repo that has the most ".$count.", list its userID, userLogin, userURL, and repo name and description.</h2><br>";

    $DataPoints = array();

    if (!empty($count)) {
        $result = $conn->query("CALL MostCount('".$count."');");
        if ($result) {
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>user ID</td><td>user login</td><td>user URL</td><td>repository name</td><td>repository description</td><td>$count</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["user ID"]."</td><td>".$row["user login"]."</td><td>".$row["user URL"]."</td><td>".$row["repository name"]."</td><td>".$row["repository description"]."</td><td>".$row["maxCount"]."</td></tr>";
                array_push($DataPoints, array( "label"=> "Maximum", "y"=> $row["maxCount"]));
                array_push($DataPoints, array( "label"=> "Average", "y"=> $row["average"]));
            }
            
            echo "</table>";
        } else {
            echo "Invalid input, call to MostCount failed<br>";
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
                    text: "Maximum Repository Count vs. Average Count"
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
