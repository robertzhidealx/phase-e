<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $year = $_POST['year'];

    echo "<h2> list Orgnization ID, Orgnization Name, created, updated date, and users num,
    -- by created date ascending order </h2><br>";

    $DataPoints = array();

    if (!empty($year)) {
        $result = $conn->query("CALL OrganizationStats('".$year."');");
        if ($result) {
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>Orgnization ID</td><td>Orgnization Nam</td><td>created date</td><td>updated date</td><td>users num</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["orgnization ID"]."</td><td>".$row["name"]."</td><td>".$row["createdAt"]."</td><td>".$row["updatedAt"]."</td><td>".$row["userNum"]."</td></tr>";
                array_push($DataPoints, array( "label"=> $row["name"], "y"=> $row["userNum"]));
            }
            
            echo "</table>";
        } else {
            echo "Call to OrganizationStats<br>";
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
                    text: "How many users does the organization have in our database?"
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