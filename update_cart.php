<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$item_id = $data['item_id'];
$action = $data['action'];

// Get current quantity
$stmt = $conn->prepare("SELECT quantity FROM cart_items WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $item_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    echo json_encode(['success' => false, 'message' => 'Item not found']);
    exit;
}

$new_quantity = $item['quantity'];

if ($action === 'increase') {
    $new_quantity++;
} elseif ($action === 'decrease') {
    $new_quantity = max(1, $item['quantity'] - 1);
}

// Update quantity
$stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
$stmt->bind_param("ii", $new_quantity, $item_id);
$stmt->execute();

echo json_encode(['success' => true]);
?>