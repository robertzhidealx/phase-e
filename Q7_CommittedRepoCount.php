<?php
    include 'open.php';

    $order = $_POST['order'];
    $repoCount = $_POST['repoCount'];

    echo "<h2>Rank select users by number of repositories committed to</h2><br>";


    $repoCountData = array();

    if (!empty($order) && !empty($repoCount)) {
        if ($result = $conn->query("CALL CommittedRepoCount('".$order."', ".$repoCount.");")) {
            foreach($result as $row) {
                echo "The average number of issues per repository is ".$row["avgIssues"].".";
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
            echo "Call to CommittedRepoCount failed<br>";
        }
    } else {
        echo "not set";
    }
    $conn->close();
?>

<html>
    <head>
        <title>Rank select users by number of repositories committed to</title>
        <script>
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
            chart.render();
        }
        </script>
    </head>
    <body>
        <div id="chartContainer" style="height: 400px; width: 100%;"></div>
        <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    </body>
</html>
