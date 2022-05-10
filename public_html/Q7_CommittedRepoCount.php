<?php
    include 'open.php';

    $order = $_POST['order'];
    $repoCount = $_POST['repoCount'];

    echo "<h2>Rank select users by number of repositories committed to</h2><br>";

    $repoCountData = array();
    
    $show = true;

    $pattern = "/[0-9]+/i";    

    if (!empty($order) && !empty($repoCount)) {
        if (strlen($repoCount) > 10 || !preg_match($pattern, $repoCount)) {
            echo "Invalid input. Please enter integers within 10 digits.";
            exit();
        }
        if ($stmt = $conn->prepare("CALL CommittedRepoCount(?, ?)")) {
            $stmt->bind_param("ss", $order, $repoCount);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if (($result) && ($result->num_rows != 0)) {
                    foreach($result as $row) {
                        echo "<p style='margin-bottom: 10px'>The average number of issues per repository is ".$row["avgIssues"].".</p>";
                        break;
                    }
                    echo "<br>";

                    echo "<table border=\"2px solid black\">";
                    echo "<tr><td>user ID</td><td>user login</td><td>number of repositories committed to</td><td>total number of user comments</td></tr>";

                    foreach($result as $row) {
                        echo "<tr><td>".$row["userID"]."</td><td>".$row["userLogin"]."</td><td>".$row["commitedRepoCount"]."</td><td>".$row["commentCount"]."</td></tr>";
                        array_push($repoCountData, array( "label"=> $row["userLogin"], "y"=> $row["commitedRepoCount"]));
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
        echo "You have to give a nonempty input.";
    }
    $conn->close();
?>

<html>
    <head>
        <title>Rank select users by number of repositories committed to</title>
        <script>
        var show = <?php echo json_encode($show); ?>;

        window.onload = function () {
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                exportEnabled: true,
                theme: "light1", // "light1", "light2", "dark1", "dark2"
                title:{
                    text: "Rank select users by number of repositories committed to"
                },
                data: [{
                    type: "column", //change type to column, bar, line, area, pie, etc
                    dataPoints: <?php echo json_encode($repoCountData, JSON_NUMERIC_CHECK); ?>
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
