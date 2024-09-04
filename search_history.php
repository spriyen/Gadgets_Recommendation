<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "smartphones_db";
$port = 3306;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch search history data
$sql = "SELECT * FROM new_search_history";
$result = $conn->query($sql);

$searchHistory = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $searchHistory[] = $row;
    }
} 

// Convert data to JSON format and output it
header('Content-Type: application/json');
echo json_encode($searchHistory);

// Close connection
$conn->close();
?>
