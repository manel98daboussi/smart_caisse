<?php

namespace App\Service;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\TableQR;
use App\Entity\CommandeTable;

/**
 * Websocket service for handling real-time communication between frontend and backend
 */
class WebsocketService implements MessageComponentInterface
{
    protected \SplObjectStorage $clients;
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->clients = new \SplObjectStorage;
        $this->entityManager = $entityManager;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        
        if (isset($data['type'])) {
            switch ($data['type']) {
                case 'subscribe_establishment':
                    // Client wants to subscribe to updates for a specific establishment
                    $this->handleEstablishmentSubscription($from, $data);
                    break;
                    
                case 'table_status_update':
                    // Table status has been updated
                    $this->handleTableStatusUpdate($data);
                    break;
                    
                case 'order_status_update':
                    // Order status has been updated
                    $this->handleOrderStatusUpdate($data);
                    break;
                    
                default:
                    $from->send(json_encode(['error' => 'Unknown message type']));
            }
        } else {
            // Broadcast the message to all connected clients
            foreach ($this->clients as $client) {
                if ($from !== $client) {
                    $client->send($msg);
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    /**
     * Handle subscription to establishment updates
     */
    private function handleEstablishmentSubscription(ConnectionInterface $from, array $data)
    {
        if (isset($data['establishment_id'])) {
            // In a real implementation, you would track which connections
            // are subscribed to which establishments
            $from->send(json_encode([
                'type' => 'subscription_confirmed',
                'establishment_id' => $data['establishment_id']
            ]));
        }
    }

    /**
     * Handle table status updates and broadcast to interested clients
     */
    private function handleTableStatusUpdate(array $data)
    {
        if (isset($data['table_id'], $data['status'])) {
            // Update table status in database
            $table = $this->entityManager->getRepository(TableQR::class)->find($data['table_id']);
            
            if ($table) {
                $table->setStatus($data['status']);
                $this->entityManager->flush();
                
                // Broadcast update to all connected clients
                $message = json_encode([
                    'type' => 'table_status_updated',
                    'table_id' => $data['table_id'],
                    'status' => $data['status'],
                    'timestamp' => date('c')
                ]);
                
                foreach ($this->clients as $client) {
                    $client->send($message);
                }
            }
        }
    }

    /**
     * Handle order status updates and broadcast to interested clients
     */
    private function handleOrderStatusUpdate(array $data)
    {
        if (isset($data['order_id'], $data['status'])) {
            // Update order status in database
            $order = $this->entityManager->getRepository(CommandeTable::class)->find($data['order_id']);
            
            if ($order) {
                $order->setStatut($data['status']);
                $this->entityManager->flush();
                
                // Broadcast update to all connected clients
                $message = json_encode([
                    'type' => 'order_status_updated',
                    'order_id' => $data['order_id'],
                    'status' => $data['status'],
                    'table_id' => $order->getTable()->getId(),
                    'timestamp' => date('c')
                ]);
                
                foreach ($this->clients as $client) {
                    $client->send($message);
                }
                
                // Also update table status if needed
                $this->updateTableStatus($order->getTable());
            }
        }
    }

    /**
     * Update table status based on active orders
     */
    private function updateTableStatus(TableQR $table)
    {
        $isActive = $table->isActiveOrder();
        $newStatus = $isActive ? 'occupied' : 'free';
        
        if ($table->getStatus() !== $newStatus) {
            $table->setStatus($newStatus);
            $this->entityManager->flush();
            
            // Broadcast table status update
            $message = json_encode([
                'type' => 'table_status_updated',
                'table_id' => $table->getId(),
                'status' => $newStatus,
                'timestamp' => date('c')
            ]);
            
            foreach ($this->clients as $client) {
                $client->send($message);
            }
        }
    }

    /**
     * Send real-time dashboard data to clients
     */
    public function sendDashboardUpdate(int $establishmentId, array $dashboardData)
    {
        $message = json_encode([
            'type' => 'dashboard_update',
            'establishment_id' => $establishmentId,
            'data' => $dashboardData,
            'timestamp' => date('c')
        ]);
        
        foreach ($this->clients as $client) {
            $client->send($message);
        }
    }
}