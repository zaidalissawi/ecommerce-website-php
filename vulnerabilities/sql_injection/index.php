<?php
require_once '../../config/database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SQL Injection Training</title>
</head>
<body>
    <h2>SQL Injection Training</h2>
    
    <form method="GET" action="">
        <label for="user_id">Enter User ID:</label>
        <input type="text" name="user_id" id="user_id">
        <input type="submit" value="Search">
    </form>

    <?php
    if(isset($_GET['user_id'])) {
        // Vulnerable SQL query (DO NOT USE IN PRODUCTION!)
        $query = "SELECT * FROM users WHERE id = " . $_GET['user_id'];
        $result = $conn->query($query);
        
        if($result) {
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "User: " . $row['username'] . "<br>";
                echo "Email: " . $row['email'] . "<br>";
            }
        }
    }
    ?>

    <div class="hints">
        <h3>Training Notes:</h3>
        <ul>
            <li>Try entering: 1 OR 1=1</li>
            <li>Try entering: 1 UNION SELECT username, password FROM users</li>
            <li>Try entering: 1; DROP TABLE users</li>
        </ul>
    </div>
</body>
</html> 