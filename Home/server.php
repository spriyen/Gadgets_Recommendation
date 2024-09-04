<?php
require __DIR__ . '/../vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use PDO;

class FeedbackForm implements MessageComponentInterface {
    protected $pdo;

    public function __construct() {
        $dbHost = 'localhost';
        $dbName = 'smartphones_db';
        $dbUser = 'root';
        $dbPass = '';
        $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8";

        try {
            $this->pdo = new PDO($dsn, $dbUser, $dbPass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connected to the database\n";
        } catch (PDOException $e) {
            echo "Database connection failed: " . $e->getMessage() . "\n";
            exit;
        }
    }

    public function onOpen(ConnectionInterface $conn) {
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo sprintf('Connection %d sending message "%s"' . "\n", $from->resourceId, $msg);
        $feedbackData = json_decode($msg, true);
        $productName = $feedbackData['product_name'];
        $feedback = $feedbackData['feedback'];

        $stmt = $this->pdo->prepare("INSERT INTO feedback (product_name, feedback) VALUES (:product_name, :feedback)");
        $stmt->execute(['product_name' => $productName, 'feedback' => $feedback]);
        $stmt->execute();

        foreach ($from->WebSocket->connections as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}

$server = \Ratchet\Server\IoServer::factory(
    new \Ratchet\Http\HttpServer(
        new \Ratchet\WebSocket\WsServer(
            new FeedbackForm()
        )
    ),
    8080
);

$server->run();
?>
