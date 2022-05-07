<?php
    include 'open.php';

    $order = $_POST['order'];

    echo "<h2>Rank organizations by total commits</h2><br>";

    $dataPoints = array();

    if (!empty($order)) {
        if ($result = $conn->query("CALL OrganizationCommits('".$order."');")) {
            echo "<table border=\"2px solid black\">";
            echo "<tr><td>organization ID</td><td>organization name</td><td>organization email</td><td>organization commits</td></tr>";

            foreach($result as $row) {
                $curEmail = "";
                if ($row["orgEmail"] != "null") $curEmail = $row["orgEmail"];
                echo "<tr><td>".$row["orgID"]."</td><td>".$row["orgName"]."</td><td>".$curEmail."</td><td>".$row["orgCommits"]."</td></tr>";
                array_push($dataPoints, array( "label"=> $row["orgName"], "y"=> $row["orgCommits"]));
            }

            echo "</table>";
        } else {
            echo "Call to OrganizationCommits failed<br>";
        }
    } else {
        echo "not set";
    }
    $conn->close();
?>

<html>
    <head>
        <title>Rank organizations by total commits</title>
        <script>
        window.onload = function () {
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                exportEnabled: true,
                theme: "light1", // "light1", "light2", "dark1", "dark2"
                title:{
                    text: "Rank organizations by total commits"
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
