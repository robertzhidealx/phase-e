<?php
    include 'open.php';

    $order = $_POST['order'];

    echo "<h2>Rank repositories by percentage of fixing issues</h2><br>";

    $dataPoints = array();

    if (!empty($order)) {
        if ($result = $conn->query("CALL FixesPercentage('".$order."');")) {
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>repository ID</td><td>repository name</td><td>number of fixes</td><td>number of open issues</td><td>percentage of fixing issues</td></tr>";

            foreach($result as $row) {
                echo "<tr><td>".$row["repoID"]."</td><td>".$row["repoName"]."</td><td>".$row["fixesCount"]."</td><td>".$row["openIssuesCount"]."</td><td>".$row["fixesPercentage"]."</td></tr>";
                array_push($dataPoints, array( "label"=> $row["repoName"], "y"=> $row["fixesPercentage"]));
            }

            echo "</table>";
        } else {
            echo "Call to AveragePackageDownloads failed<br>";
        }
    } else {
        echo "not set";
    }
    $conn->close();
?>

<html>
    <head>
        <title>Rank repositories by percentage of fixing issues</title>
        <script>
        window.onload = function () {
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                exportEnabled: true,
                theme: "light1", // "light1", "light2", "dark1", "dark2"
                title:{
                    text: "Rank repositories by percentage of fixing issues"
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
