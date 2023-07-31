<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require __DIR__ . '/../vendor/autoload.php'; // Chemin vers l'autoloader de Ratchet

class EcollydayWebSocket implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Nouvelle connexion : {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // Lorsqu'un message est reçu, on le retransmet à tous les autres clients connectés
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connexion {$conn->resourceId} fermée\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Erreur : {$e->getMessage()}\n";
        $conn->close();
    }
}

$server = new \Ratchet\App('localhost', 8080);
$server->route('/ecollyday', new EcollydayWebSocket());
$server->run();
