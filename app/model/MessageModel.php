<?php 
namespace app\model; 

use Flight;
use PDO;

Class MessageModel{
    private $db ; 
    public function __construct($db)
    {
        $this->db=$db ; 
    } 

    /**
     * Récupérer tous les messages avec infos users
     */
    public function getAllmessageUser($receiver_id) {
        $sql = "SELECT 
                    m.id,
                    m.sender_id,
                    m.receiver_id,
                    m.content,
                    m.timestamp,
                    u_sender.name AS sender_name,
                    u_receiver.name AS receiver_name
                FROM message m
                LEFT JOIN users u_sender ON m.sender_id = u_sender.id
                LEFT JOIN users u_receiver ON m.receiver_id = u_receiver.id
                WHERE m.receiver_id = ? 
                ORDER BY m.timestamp DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$receiver_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer une conversation entre deux utilisateurs
     */
    public function getConversation($user_id, $other_user_id) {
        $sql = "SELECT 
                    m.id,
                    m.sender_id,
                    m.receiver_id,
                    m.content,
                    m.timestamp,
                    u_sender.name AS sender_name,
                    u_receiver.name AS receiver_name
                FROM message m
                LEFT JOIN users u_sender ON m.sender_id = u_sender.id
                LEFT JOIN users u_receiver ON m.receiver_id = u_receiver.id
                WHERE (m.sender_id = ? AND m.receiver_id = ?) 
                   OR (m.sender_id = ? AND m.receiver_id = ?)
                ORDER BY m.timestamp ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id, $other_user_id, $other_user_id, $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer tous les contacts avec dernier message
     */
    
    public function getContacts($user_id) {
        $sql = "SELECT 
                    u.id,
                    u.name AS username,
                    u.email,
                    u.photo,
                    (SELECT content FROM message 
                     WHERE (sender_id = ? AND receiver_id = u.id) 
                        OR (sender_id = u.id AND receiver_id = ?)
                     ORDER BY timestamp DESC LIMIT 1) as last_message,
                    (SELECT timestamp FROM message 
                     WHERE (sender_id = ? AND receiver_id = u.id) 
                        OR (sender_id = u.id AND receiver_id = ?)
                     ORDER BY timestamp DESC LIMIT 1) as last_timestamp
                FROM users u
                WHERE u.id != ?
                AND (u.id IN (SELECT sender_id FROM message WHERE receiver_id = ?)
                  OR u.id IN (SELECT receiver_id FROM message WHERE sender_id = ?))
                ORDER BY last_timestamp DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Créer un nouveau message
     */
    public function addMessage($sender_id, $receiver_id, $content) {
        if (empty($content) || empty($receiver_id)) {
            return false;
        }
        $sql = "INSERT INTO message (sender_id, receiver_id, content, timestamp) 
                VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        if ($stmt->execute([$sender_id, $receiver_id, $content])) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Supprimer un message
     */
    public function deleteMessage($message_id, $user_id) {
        $sql = "DELETE FROM message 
                WHERE id = ? AND (sender_id = ? OR receiver_id = ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$message_id, $user_id, $user_id]);
    }

    /**
     * Mettre à jour un message
     */
    public function updateMessage($message_id, $content, $user_id) {
        if (empty($content)) {
            return false;
        }
        $sql = "UPDATE message 
                SET content = ? 
                WHERE id = ? AND sender_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$content, $message_id, $user_id]);
    }

    /**
     * Récupérer un message spécifique avec infos users
     */
    public function getMessageById($message_id) {
        $sql = "SELECT 
                    m.id,
                    m.sender_id,
                    m.receiver_id,
                    m.content,
                    m.timestamp,
                    u_sender.name AS sender_name,
                    u_receiver.name AS receiver_name
                FROM message m
                LEFT JOIN users u_sender ON m.sender_id = u_sender.id
                LEFT JOIN users u_receiver ON m.receiver_id = u_receiver.id
                WHERE m.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$message_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}