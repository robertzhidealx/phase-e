<?php
    include 'open.php';

    $order = $_POST['order'];

    echo "<h2>Rank organizations by total commits</h2><br>";

    $dataPoints = array();
    
    $show = true;

    if (!empty($order)) {
        if ($stmt = $conn->prepare("CALL OrganizationCommits(?)")) {
            $stmt->bind_param("s", $order);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if (($result) && ($result->num_rows != 0)) {
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
        echo "invalid input";
    }
    $conn->close();
?>

<html>
    <head>
        <title>Rank organizations by total commits</title>
        <script>
        var show = <?php echo json_encode($show); ?>;

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
            if (show) chart.render();
        }
        </script>
    </head>
    <body>
        <div id="chartContainer" style="height: 400px; width: 100%;"></div>
        <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    </body>
</html>
