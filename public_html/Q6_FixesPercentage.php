<?php
    include 'open.php';

    $order = $_POST['order'];

    echo "<h2>Rank repositories by percentage of fixing issues</h2><br>";

    $dataPoints = array();

    $show = true;

    if (!empty($order)) {
        if ($stmt = $conn->prepare("CALL FixesPercentage(?)")) {
            $stmt->bind_param("s", $order);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if (($result) && ($result->num_rows != 0)) {
                    echo "<table border=\"2px solid black\">";
                    echo "<tr><td>repository ID</td><td>repository name</td><td>number of fixes</td><td>number of open issues</td><td>percentage of fixing issues</td></tr>";

                    foreach($result as $row) {
                        echo "<tr><td>".$row["repoID"]."</td><td>".$row["repoName"]."</td><td>".$row["fixesCount"]."</td><td>".$row["openIssuesCount"]."</td><td>".$row["fixesPercentage"]."</td></tr>";
                        array_push($dataPoints, array( "label"=> $row["repoName"], "y"=> $row["fixesPercentage"]));
                    }
                    
                    echo "</table>";
                } else {
                    $show = false;
                    echo "No result fits the requirement.";
                }
                $result->free_result();
            } else {
                echo "Execute failed.<br>";
            }
            $stmt->close();
        } else {
            echo "Prepare failed.<br>";
            $error = $conn->errno . ' ' . $conn->error;
            echo $error; 
        }
    } else {
        $show = false;
        echo "You have to make a selection.";
    }
    $conn->close();
?>

<html>
    <head>
        <title>Rank repositories by percentage of fixing issues</title>
        <script>
        var show = <?php echo json_encode($show); ?>;

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
            if (show) chart.render();
        }
        </script>
    </head>
    <body>
        <div id="chartContainer" style="height: 400px; width: 100%;"></div>
        <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    </body>
</html>
