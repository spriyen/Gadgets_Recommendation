<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// Database configuration
$host = 'localhost';
$port = 3306; // Change this to your MySQL port number if different
$db   = 'smartphones_db'; // Replace with your database name
$user = 'root';
$pass = ''; // Replace with your database password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['error' => 'Database connection error: ' . $e->getMessage()]);
    exit;
}

// Function to add a condition if the field is set and not empty
function addCondition(&$conditions, &$params, $field, $value) {
    if (!empty($value)) {
        $conditions[] = "`$field` = :$field";
        $params[$field] = $value;
    }
}

$conditions = [];
$params = [];

// Collect values from $_GET for selected filters
foreach ($_GET as $filter => $value) {
    // Ensure the filter exists in your database table columns
    if (!empty($value) && isValidColumn($filter, $pdo)) {
        addCondition($conditions, $params, $filter, $value);
    }
}

// Build the query to fetch matching records
$query = "SELECT * FROM lap_search_history";
if (!empty($conditions)) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$searchHistory = $stmt->fetchAll();

// Return the JSON data to the client
echo json_encode($searchHistory);

// Function to validate if a column exists in the table
function isValidColumn($column, $pdo) {
    $stmt = $pdo->query("DESCRIBE lap_search_history");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    return in_array($column, $columns);
}
?>
