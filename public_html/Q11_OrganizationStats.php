<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $year = $_POST['year'];

    echo "<h2> list Orgnization ID, Orgnization Name, created, updated date, and users num, by created date ascending order </h2>";

    $dataPoints = array();

    $show = true;

    $pattern = "/[0-9]{4}/i";

    if (!empty($year)) {
        if (strlen($year) > 4 || !preg_match($pattern, $year)) {
            echo "Invalid year. Please format into YYYY.";
            exit();
        }
        if ($stmt = $conn->prepare("CALL OrganizationStats(?)")) {
            $stmt->bind_param("s", $year);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result) {
                    if ($result->num_rows > 0){
                        echo "<table border=\"2px solid black\">";
                        echo "<tr><td>Orgnization ID</td><td>Orgnization Name</td><td>created date</td><td>updated date</td><td>users num</td></tr>";

                        foreach($result as $row) {
                            echo "<tr><td>".$row["orgnization ID"]."</td><td>".$row["name"]."</td><td>".$row["createdAt"]."</td><td>".$row["updatedAt"]."</td><td>".$row["userNum"]."</td></tr>";
                            array_push($dataPoints, array( "label"=> $row["name"], "y"=> $row["userNum"]));
                        }
                        echo "</table><br>";
                    } else {
                        $show = false;
                        echo "No result fits the requirement.";
                    }
                } else {
                    echo "Call to OrganizationStats failed<br>";
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
        $show = false;
        echo "You have to enter a valid year.";
    }
    $conn->close();

?>

<html>
    <head>
        <title>Organizations Stats</title>
        <script>
        window.onload = function () {
        var show = <?php echo json_encode($show); ?>;

            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                exportEnabled: true,
                theme: "light1", // "light1", "light2", "dark1", "dark2"
                title:{
                    text: "How many users does the organization have in our database?"
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
