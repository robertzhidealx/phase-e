<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $countType = $_POST['count'];

    echo "<h2>For repo that has the most ".$countType.", list its userID, userLogin, userURL, and repo name and description.</h2><br>";

    $dataPoints = array();

    $show = true;

    if (!empty($countType)) {
        if ($stmt = $conn->prepare("CALL MostCount(?)")) {
            $stmt->bind_param("s", $countType);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result) {
                    if ($result->num_rows > 0){
                        echo "<table border=\"2px solid black\">";
                        echo "<tr><td>user ID</td><td>user login</td><td>user URL</td><td>repository name</td><td>repository description</td></tr>";

                        foreach($result as $row) {
                            echo "<tr><td>".$row["user ID"]."</td><td>".$row["user login"]."</td><td>".$row["user URL"]."</td><td>".$row["repository name"]."</td><td>".$row["repository description"]."</td></tr>";
                            array_push($dataPoints, array( "label"=> "Maximum", "y"=> $row["maxCount"]));
                            array_push($dataPoints, array( "label"=> "Average", "y"=> $row["average"]));
                        }
            
                        echo "</table>";
                    } else {
                        $show = false;
                        echo "No result fits the requirement.";
                    }
                } else {
                    echo "Invalid input, call to MostCount failed<br>";
                }
            } else {
                //Call to execute failed, e.g. because server is no longer reachable,
                //or because supplied values are of the wrong type
                echo "Execute failed.<br>";
            }

            //Close down the prepared statement
            $stmt->close();
        }
    } else {
        echo "invalid input";
    }
    $conn->close();

?>

<html>
    <head>
        <title>Most Count</title>
        <script>
        var show = <?php echo json_encode($show); ?>;

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
