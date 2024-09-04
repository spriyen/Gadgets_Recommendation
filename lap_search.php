<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// Database configuration
$host = 'localhost';
$port = 3306; // Specify your MySQL port number here
$db   = 'smartphones_db';
$user = 'root';
$pass = '';
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
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

// Define a mapping between form field names and table column names
$fieldMappings = [
    'ramSize' => 'ramSize',
    'ramType' => 'ramType',
    'internalStorage' => 'internalStorage',
    'processor' => 'processor',
    'cores' => 'cores',
    'os' => 'os',
    'gpuBrand' => 'gpuBrand',
    // Updated to match the column in the laptops table
];

$conditions = [];
$params = [];

// Function to add a condition if the field is set and not empty
function addCondition(&$conditions, &$params, $field, $column) {
    if (!empty($_GET[$field])) {
        $conditions[] = "$column LIKE :$field";
        $params[$field] = '%' . $_GET[$field] . '%';
    }
}

// List of fields to filter by
$fields = array_keys($fieldMappings);

// Add conditions for each field
foreach ($fields as $field) {
    if (isset($fieldMappings[$field])) {
        addCondition($conditions, $params, $field, $fieldMappings[$field]);
    }
}

// Handle the price range filter
if (!empty($_GET['price'])) {
    $priceRange = explode(' - ', $_GET['price']);
    if (count($priceRange) == 2) {
        $minPrice = (int)str_replace(',', '', $priceRange[0]);
        $maxPrice = (int)str_replace(',', '', $priceRange[1]);
        $conditions[] = "CAST(REPLACE(price, ',', '') AS UNSIGNED) BETWEEN :minPrice AND :maxPrice";
        $params['minPrice'] = $minPrice;
        $params['maxPrice'] = $maxPrice;
    } elseif (strpos($_GET['price'], 'Above') !== false) {
        $minPrice = (int)str_replace(',', '', str_replace('Above ', '', $_GET['price']));
        $conditions[] = "CAST(REPLACE(price, ',', '') AS UNSIGNED) >= :minPrice";
        $params['minPrice'] = $minPrice;
    }
}

// Handle the screen size filter
if (!empty($_GET['screenSize'])) {
    switch ($_GET['screenSize']) {
        case 'lessThan14':
            $conditions[] = "screensize < 14";
            break;
        case '14to15.9':
            $conditions[] = "screensize BETWEEN 14 AND 15.9";
            break;
        case '16to17.9':
            $conditions[] = "screensize BETWEEN 16 AND 17.9";
            break;
        case '18AndAbove':
            $conditions[] = "screensize >= 18";
            break;
        default:
            break;
    }
}

// Build the query
$query = "SELECT * FROM laptops";
if (count($conditions) > 0) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$laptops = $stmt->fetchAll();

// Store the fetched data in a JSON file
$file = 'laptops_data.json';
file_put_contents($file, json_encode($laptops));

$insert_sql = "INSERT INTO lap_search_history 
    (ramSize, ramType, os, internalStorage, screenSize, processor, cores, gpuBrand, price) 
    VALUES (:ramSize, :ramType, :os, :internalStorage, :screenSize, :processor, :cores, :gpuBrand, :price)";

$stmt = $pdo->prepare($insert_sql);

// Bind parameters
$stmt->bindParam(':ramSize', $_GET['ramSize'], PDO::PARAM_STR);
$stmt->bindParam(':ramType', $_GET['ramType'], PDO::PARAM_STR);
$stmt->bindParam(':os', $_GET['os'], PDO::PARAM_STR);
$stmt->bindParam(':internalStorage', $_GET['internalStorage'], PDO::PARAM_STR);
$stmt->bindParam(':screenSize', $_GET['screenSize'], PDO::PARAM_STR);
$stmt->bindParam(':processor', $_GET['processor'], PDO::PARAM_STR);
$stmt->bindParam(':cores', $_GET['cores'], PDO::PARAM_STR);
$stmt->bindParam(':gpuBrand', $_GET['gpuBrand'], PDO::PARAM_STR);
$stmt->bindParam(':price', $_GET['price'], PDO::PARAM_STR);

// Execute the statement
try {
    $stmt->execute();
    // Return the JSON data to the HTML file
    echo json_encode($laptops);
} catch (\PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
