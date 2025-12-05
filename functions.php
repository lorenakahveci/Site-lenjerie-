<?php
// Functii utilitare pentru cos
require_once 'db.php';


function getUserCart(){
    global $conn;
    $user_id = $_SESSION['user']['id'] ?? 0;
    if(!$user_id){ return []; }

    $stmt = $conn->prepare("
        SELECT p.id, p.name, p.price, ci.quantity, ci.id AS cart_id
        FROM cart_items ci
        JOIN products p ON p.id = ci.product_id
        WHERE ci.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    $cart = [];
    while($row = $res->fetch_assoc()){
        $cart[] = $row;
    }
    return $cart;
}

function getCartTotal(){
    $cart = getUserCart();
    $total = 0;
    foreach($cart as $item){
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

function getUserByEmail($email){
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}