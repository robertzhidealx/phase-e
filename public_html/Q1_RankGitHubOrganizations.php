<?php
    include 'open.php';

    $keyword = $_POST['keyword'];

    echo "<h2>Rank GitHub organizations</h2>";

	$dataPoints = array();

    if (!empty($keyword)) {
        echo "<p style='margin-bottom: 10px'>You have selected <span style='font-weight: bold'>".$keyword."</span> as the search term.</p><br>";

        $len = strlen($keyword);
        if ($len > 15) {
            echo "Bad input. please make sure the keyword is between 1 and 15 characters long.";
        } else {
            if ($stmt = $conn->prepare("CALL RankGitHubOrganizations(?)")) {
                $stmt->bind_param("s", $keyword);
                if ($stmt->execute()) {
                    $result = $stmt->get_result();
                    if (($result) && ($result->num_rows != 0)) {
                        echo "<table border=\"2px solid black\">";
                        echo "<tr><td>user ID</td><td>user name</td><td>total stars</td></tr>";

                        foreach($result as $row) {
                            echo "<tr><td>".$row["userID"]."</td><td>".$row["username"]."</td><td>".$row["totalStars"]."</td></tr>";
                            array_push($dataPoints, array( "label"=> $row["username"], "y"=> $row["totalStars"]));
                        }
                        
                        echo "</table>";
                    } else {
                        echo "No data found for this keyword";
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
        }
    } else {
        echo "You need to enter a nonempty keyword";
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
                    text: "Organization Total Stars"
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
