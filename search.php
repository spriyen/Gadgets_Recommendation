<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establish database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "smartphones_db";
$port = 3306;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    $error = array("error" => "Connection failed: " . $conn->connect_error);
    echo json_encode($error);
    exit();
}

// Initialize an empty array to store filter conditions
$conditions = array();

// Initialize an empty array to store search data
$search_data = array(
    'ram' => null,
    'operating_system' => null,
    'rom' => null,
    'screen_size' => null,
    'front_camera' => null,
    'back_camera' => null,
    'display' => null,
    'price' => null,
    'battery_capacity' => null,
    'cellular' => null,
    'processor' => null
);

// Process each filter parameter
if (!empty($_GET['ram'])) {
    $conditions[] = "ram = '" . $conn->real_escape_string($_GET['ram']) . "'";
    $search_data['ram'] = $_GET['ram'];
}

if (!empty($_GET['os'])) {
    $conditions[] = "operating_system = '" . $conn->real_escape_string($_GET['os']) . "'";
    $search_data['operating_system'] = $_GET['os'];
}

if (!empty($_GET['rom'])) {
    $conditions[] = "rom = '" . $conn->real_escape_string($_GET['rom']) . "'";
    $search_data['rom'] = $_GET['rom'];
}

if (!empty($_GET['screen_size'])) {
    $range = explode(" - ", $_GET['screen_size']);
    $min_size = floatval($range[0]);
    $max_size = floatval($range[1]);
    $conditions[] = "screen_size BETWEEN " . $min_size . " AND " . $max_size;
    $search_data['screen_size'] = $_GET['screen_size'];
}

if (!empty($_GET['front_camera'])) {
    $conditions[] = "front_camera LIKE '%" . $conn->real_escape_string($_GET['front_camera']) . "%'";
    $search_data['front_camera'] = $_GET['front_camera'];
}

if (!empty($_GET['back_camera'])) {
    $conditions[] = "back_camera LIKE '%" . $conn->real_escape_string($_GET['back_camera']) . "%'";
    $search_data['back_camera'] = $_GET['back_camera'];
}

if (!empty($_GET['display'])) {
    $conditions[] = "display LIKE '%" . $conn->real_escape_string($_GET['display']) . "%'";
    $search_data['display'] = $_GET['display'];
}

if (!empty($_GET['price'])) {
    $range = explode(" - ", $_GET['price']);
    $min_price = floatval($range[0]);
    $max_price = floatval($range[1]);
    $conditions[] = "price BETWEEN " . $min_price . " AND " . $max_price;
    $search_data['price'] = $_GET['price'];
}

if (!empty($_GET['battery_capacity'])) {
    $range = explode(" - ", $_GET['battery_capacity']);
    $min_capacity = intval($range[0]);
    $max_capacity = intval($range[1]);
    $conditions[] = "CAST(SUBSTRING_INDEX(battery_capacity, ' ', 1) AS UNSIGNED) BETWEEN " . $min_capacity . " AND " . $max_capacity;
    $search_data['battery_capacity'] = $_GET['battery_capacity'];
}

if (!empty($_GET['cellular'])) {
    $conditions[] = "cellular LIKE '%" . $conn->real_escape_string($_GET['cellular']) . "%'";
    $search_data['cellular'] = $_GET['cellular'];
}

if (!empty($_GET['processor'])) {
    $processor = $conn->real_escape_string($_GET['processor']);
    $conditions[] = "processor = '{$processor}'";
    $search_data['processor'] = $processor;
}





// Add similar blocks for other filter parameters...

// Additional conditions to exclude entries with unspecified details or price = 0
$conditions[] = "price > 0";
$conditions[] = "ram IS NOT NULL AND ram != ''";
$conditions[] = "operating_system IS NOT NULL AND operating_system != ''";
$conditions[] = "rom IS NOT NULL AND rom != ''";
$conditions[] = "screen_size IS NOT NULL";
$conditions[] = "back_camera IS NOT NULL AND back_camera != ''";
$conditions[] = "display IS NOT NULL AND display != ''";
$conditions[] = "battery_capacity IS NOT NULL";
$conditions[] = "cellular IS NOT NULL AND cellular != ''";
$conditions[] = "front_camera IS NOT NULL AND front_camera != ''";
$conditions[] = "processor IS NOT NULL AND processor != ''";

// Construct the SQL query
$sql = "SELECT * FROM smartphones";
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

// Debug flag
$debug = isset($_GET['debug']) ? true : false;

// Output the SQL query if debug flag is set
if ($debug) {
    echo "Constructed SQL query: " . $sql . "<br>";
}

// Execute the query
$result = $conn->query($sql);

if (!$result) {
    $error = array("error" => "Error executing query: " . $conn->error);
    echo json_encode($error);
    exit();
}

// Prepare and execute the INSERT statement for search history
$search_sql = "INSERT INTO new_search_history (ram, operating_system, rom, screen_size, front_camera, back_camera, display, price, battery_capacity, cellular, processor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($search_sql);
$stmt->bind_param("sssssssssss", 
    $search_data['ram'], 
    $search_data['operating_system'], 
    $search_data['rom'], 
    $search_data['screen_size'], 
    $search_data['front_camera'], 
    $search_data['back_camera'], 
    $search_data['display'], 
    $search_data['price'], 
    $search_data['battery_capacity'], 
    $search_data['cellular'], 
    $search_data['processor']
);
$stmt->execute();

// Check if any rows are returned
if ($result->num_rows > 0) {
    // Initialize an empty array to store smartphone data
    $smartphones = array();

    // Fetch data from each row and add it to the array
    while ($row = $result->fetch_assoc()) {
        $smartphones[] = $row;
    }

    // Save the data to a JSON file
    $json_data = json_encode($smartphones);
    file_put_contents('smartphones.json', $json_data);

    // Return the smartphone data as JSON
    echo $json_data;
} else {
    // If no smartphones match the filter criteria, return an empty array
    echo json_encode(array());
}
$_SESSION['selected_filters'] = $search_data;
// Close the database connection
$conn->close();

?>

