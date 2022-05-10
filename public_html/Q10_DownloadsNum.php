<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $num = $_POST['num'];

    echo "<h2>List package name, score, and total downloads for package that gets more than ".$num." downloads from 2020-10-01 to 2020-10-05</h2>";

    $dataPoints = array();

    $pattern = "/[0-9]+/i";    

    if (!empty($num)) {
        if (strlen($num) > 15 || !preg_match($pattern, $num)) {
            echo "Invalid input. Please enter integers within 15 digits.";
            exit();
        }
        if ($stmt = $conn->prepare("CALL DownloadsNum(?)")) {
            $stmt->bind_param("i", $num);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result) {
                    if ($result->num_rows > 0){
                        echo "<table border=\"2px solid black\">";
                        echo "<tr><td>package name</td><td>score</td><td>downloads count</td></tr>";
                        foreach($result as $row) {
                            echo "<tr><td>".$row["packageName"]."</td><td>".$row["score"]."</td><td>".$row["total downloads"]."</td></tr>";
                            array_push($dataPoints, array( "label"=> $row["packageName"], "y"=> $row["score"]));
                        }
                        echo "</table><br>";
                    } else {
                        echo "No package fit the requirement.";
                    }
            
                } else {
                    echo "Call to DownloadsNum failed<br>";
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
        echo "You have to enter a number.";
    }
    $conn->close();

?>

<html>
    <head>
        <title>Downloads Num</title>
        <script>
        window.onload = function () {
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                exportEnabled: true,
                theme: "light1", // "light1", "light2", "dark1", "dark2"
                title:{
                    text: "Package Score"
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
