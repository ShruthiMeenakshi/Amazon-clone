<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "electronica";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create tables if they don't exist
$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
)";

$conn->query($sql);

// Sample products (run once)
// $sample_products = [
//     ['name' => 'Wireless Earbuds', 'price' => 49.99, 'image' => 'https://m.media-amazon.com/images/I/71Swqqe7XAL._AC_SX466_.jpg'],
//     ['name' => 'Bluetooth Speaker', 'price' => 89.99, 'image' => 'https://m.media-amazon.com/images/I/61+Q6RhJD6L._AC_SL1500_.jpg'],
//     ['name' => 'Smart Watch', 'price' => 199.99, 'image' => 'https://m.media-amazon.com/images/I/61BGE6iu4AL._AC_SL1500_.jpg']
// ];

// foreach ($sample_products as $product) {
//     $stmt = $conn->prepare("INSERT INTO products (name, price, image) VALUES (?, ?, ?)");
//     $stmt->bind_param("sds", $product['name'], $product['price'], $product['image']);
//     $stmt->execute();
// }
?>