<?php
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    echo "<h2>Rank packages by downloads gained</h2>";

    $dataPoints = array();

    if (!empty($startDate) && !empty($endDate)) {
        echo "<p style='margin-bottom: 10px'>You have selected <span style='font-weight: bold'>".$startDate."</span> as the start date and <span style='font-weight: bold'>".$endDate."</span> as the end date.</p><br>";

        $startDateLen = strlen($startDate);
        $endDateLen = strlen($endDate);
        if ($startDateLen > 10 || $endDateLen > 10) {
            echo "Bad input. please make sure the date format is YYYY-MM-DD.";
        } else {
            if ($result = $conn->query("CALL PackageDownloadGained('".$startDate."', '".$endDate."');")) {
echo "yo";
                echo "<table border=\"2px solid black\">";
                echo "<tr><td>package name</td><td>package version</td><td>downloads gained</td></tr>";

                foreach($result as $row) {
                    echo "<tr><td>".$row["packageName"]."</td><td>".$row["version"]."</td><td>".$row["downloadsGained"]."</td></tr>";
                    array_push($dataPoints, array( "label"=> $row["packageName"], "y"=> $row["downloadsGained"]));
                }

                echo "</table>";
            } else {
                echo "Call to PackageDownloadsGained failed<br>";
            }
        }
    } else {
        echo "You have to indicate the start and end dates";
    }
    $conn->close();
?>

<html>
    <head>
        <title>Rank Packages by Downloads Gained</title>
        <script>
        window.onload = function () {
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                exportEnabled: true,
                theme: "light1", // "light1", "light2", "dark1", "dark2"
                title:{
                    text: "Rank Packages by Downloads Gained"
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
