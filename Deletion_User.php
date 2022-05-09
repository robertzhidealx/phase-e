<head><title>Delete a User</title></head>
<body>
<?php
    include 'open.php';

    $userID = $_POST['userID'];

    echo "<h2>Delete a user</h2><br>";

    if (!empty($userID)) {
        $stmt = $conn->prepare('CALL DeleteUser(?)');
        if (!$stmt) {
            echo 'mysqli prepare() failed';
            exit();
        }
         
        $bind = $stmt->bind_param('s', $userID);
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
            echo "<p style='margin: 0'>Successfully deleted user <span style='font-weight: bold'>".$userID."</span>.</p>";
        } else {
            echo "<p style='margin: 0'>User <span style='font-weight: bold'>".$userID."</span> not found.</p>";
        }
         
        $result->free_result();
        $stmt->close();
    } else {
        echo "You have to enter a user ID.";
    }
    $conn->close();
?>
</body>
