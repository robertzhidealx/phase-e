<?php
    include 'open.php';

    $keyword = $_POST['keyword'];

    echo "<h2>Average Package Score</h2>";

    $dataPoints = array();

    if (!empty($keyword)) {
        echo "<p style='margin-bottom: 10px'>You have selected <span style='font-weight: bold'>".$keyword."</span> as the search term.</p><br>";

        $len = strlen($keyword);
        if ($len > 15) {
            echo "Bad input. please make sure the keyword is between 1 and 15 characters long.";
        } else {
            if ($result = $conn->query("CALL AveragePackageScore('".$keyword."');")) {
                echo "<table border=\"2px solid black\">";
                echo "<tr><td>organization ID</td><td>organization name</td><td>average package score</td></tr>";

                foreach($result as $row) {
                    echo "<tr><td>".$row["orgID"]."</td><td>".$row["orgName"]."</td><td>".$row["averageScore"]."</td></tr>";
                    array_push($dataPoints, array( "label"=> $row["orgName"], "y"=> $row["averageScore"]));
                }

                echo "</table>";
            } else {
                echo "Call to AveragePackageScore failed<br>";
            }
        }
    } else {
        echo "not set";
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
                    text: "Average Package Score"
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
