<?php
    include 'open.php';

    $issueCount = $_POST['issueCount'];
    $repoCount = $_POST['repoCount'];

    echo "<h2>Count user commits</h2><br>";

    $dataPoints = array();

    $show = true;

    $pattern = "/[0-9]+/i";    

    if (!empty($issueCount) && !empty($repoCount)) {
        if (strlen($issueCount) > 10 || strlen($repoCount) > 10 || !preg_match($pattern, $issueCount) || !preg_match($pattern, $repoCount)) {
            echo "Invalid input. Please enter integers within 10 digits.";
            exit();
        }
        if ($stmt = $conn->prepare("CALL CountUserCommits(?, ?)")) {
            $stmt->bind_param("ss", $issueCount, $repoCount);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if (($result) && ($result->num_rows != 0)) {
                    echo "<table border=\"2px solid black\">";
                    echo "<tr><td>user ID</td><td>user login</td><td>commits count</td></tr>";

                    foreach($result as $row) {
                        echo "<tr><td>".$row["userID"]."</td><td>".$row["userLogin"]."</td><td>".$row["commitsCount"]."</td></tr>";
                        array_push($dataPoints, array( "label"=> $row["userLogin"], "y"=> $row["commitsCount"]));
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
        echo "You have to give a number of issues and a number of repositories.";
    }
    $conn->close();
?>

<html>
    <head>
        <title>Rank Select users by number of commits</title>
        <script>
        var show = <?php echo json_encode($show); ?>;

        window.onload = function () {
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                exportEnabled: true,
                theme: "light1", // "light1", "light2", "dark1", "dark2"
                title:{
                    text: "Rank Select users by number of commits"
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
