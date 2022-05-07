<?php

    // open a connection to dbase server 
    include 'open.php';

    // collect the posted value in a variable called $item
    $user = $_POST['committer'];

    echo "<h2>Out of all commits whose committer is ".$user.", what percentage is verified?</h2>";

    $percentage = 0;

    if (!empty($user)) {
        if ($stmt = $conn->prepare("CALL VerifiedCommitsPercentage(?)")) {
            $stmt->bind_param("s", $user);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result) {
                    echo "<table border=\"2px solid black\">";
                    echo "<tr><td>Percentage of verified commits</td></tr>";

                    foreach($result as $row) {
                        echo "<tr><td>".$row["Percentage of verified commits committed by by Github"]."</td></tr>";
                        $percentage = $row["Percentage of verified commits committed by by Github"];
                    }
            
                    echo "</table><br>";
                    $dataPoints = array( 
                        array("label"=>"Verified commits", "y"=>$percentage),
                        array("label"=>"Unverified commits", "y"=>(100-$percentage))
                    );
                } else {
                    echo "Call to VerifiedCommitsPercentage failed<br>";
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
<script>
window.onload = function() {
 
 
var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	title: {
		text: "Verified commit vs. Unverified commit"
	},
	data: [{
		type: "pie",
		yValueFormatString: "#,##0.00\"%\"",
		indexLabel: "{label} ({y})",
		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();
 
}
</script>
</head>
<body>
<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html>