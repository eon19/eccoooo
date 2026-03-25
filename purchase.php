<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

 
    $product_id = $_POST['product_id'] ?? '';
    $quantity = $_POST['quantity'] ?? 0;
    $client_name = $_POST['client_name'] ?? 'Unknown';
    $client_contact = $_POST['client_contact'] ?? 'Unknown';

   
    $sql = "SELECT product_id, name, img, price, qty FROM products WHERE product_id = '$product_id'";
    $result = mysqli_query($conn, $sql);
    $product = mysqli_fetch_assoc($result);

  
    if (!$product) {
        die("Product not found.");
    }

    $product_name = $product['name'];
    $product_img = $product['img'];
    $price = $product['price'];
    $current_stock = $product['qty'];

  
    $total_price = $price * $quantity;

    
    if ($quantity > $current_stock) {
        echo "<script>
        alert('Order exceeds available stocks!');
        window.location.href = 'product.php';
        </script>";
        exit();
    }

   
    $insert = "INSERT INTO orders (order_name, order_img, quantity, total, client_name, client_contact)
               VALUES ('$product_name', '$product_img', '$quantity', '$total_price', '$client_name', '$client_contact')";

    mysqli_query($conn, $insert);

    
    $new_stock = $current_stock - $quantity;

    $sql_update = "UPDATE products SET qty = '$new_stock' WHERE product_id = '$product_id'";
    mysqli_query($conn, $sql_update);

    
    echo "<script>
    alert('Order placed successfully!');
    window.location.href = 'product.php';
    </script>";
}
?>