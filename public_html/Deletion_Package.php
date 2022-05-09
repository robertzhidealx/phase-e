<head><title>Delete a Package</title></head>
<body>
<?php
    include 'open.php';

    $packageName = $_POST['packageName'];

    echo "<h2>Delete a package</h2><br>";

    if (!empty($packageName)) {
        $stmt = $conn->prepare('CALL DeletePackage(?)');
        if (!$stmt) {
            echo 'mysqli prepare() failed';
            exit();
        }
         
        $bind = $stmt->bind_param('s', $packageName);
        if (!$bind) {
            echo 'bind_param() failed';
            exit();
        }
         
        $exec = $stmt->execute();
        if (!$exec) {
            echo 'mysqli execute() failed';
        }

        $result = $stmt->get_result();
        if (!($result)) {
            echo "<p style='margin: 0'>Successfully deleted package <span style='font-weight: bold'>".$packageName."</span>.</p>";
        } else {
            echo "<p style='margin: 0'>Package <span style='font-weight: bold'>".$packageName."</span> not found.</p>";
        }
         
        $result->free_result();
        $stmt->close();
    } else {
        echo "You have to enter a package name.";
    }
    $conn->close();
?>
</body>
