<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Retrieve selected filters from the POST request
$selectedFilters = json_decode(file_get_contents('php://input'), true);

// Map 'os' to 'operating_system'
if (isset($selectedFilters['os'])) {
    $selectedFilters['operating_system'] = $selectedFilters['os'];
    unset($selectedFilters['os']);
}

// Debug: Log selected filters
error_log('Selected Filters: ' . json_encode($selectedFilters));

// Here you can write the logic to connect to your database
// Replace these variables with your actual database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "smartphones_db";
$port = 3306;

// Create connection
$connection = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Debug: Log connection status
error_log('Connected to database successfully');

// Build the SQL query based on the selected filters
$sql = "SELECT * FROM new_search_history WHERE ";
$whereConditions = [];

foreach ($selectedFilters as $key => $value) {
    if (!empty($value) && $value !== "Select") {
        // Escape the values to prevent SQL injection
        $escapedValue = $connection->real_escape_string($value);
        // Add the condition to the array
        $whereConditions[] = "$key = '$escapedValue'";
    }
}

// Combine all the conditions with AND operator
if (!empty($whereConditions)) {
    $sql .= implode(" AND ", $whereConditions);
} else {
    $sql = "SELECT * FROM new_search_history";
}

// Debug: Log SQL query
error_log('SQL Query: ' . $sql);

// Execute the query
$result = $connection->query($sql);

// Check if any rows were returned
if ($result->num_rows > 0) {
    // Fetch the rows
    $data = $result->fetch_all(MYSQLI_ASSOC);

    // Debug: Log matching data
    error_log('Matching data from search history: ' . json_encode($data));

    // Encode the matching data as JSON and print to console
    echo json_encode($data);
} else {
    // No matching records found
    error_log('No matching records found');
    echo json_encode([]);
}

// Close the database connection
$connection->close();
?>
