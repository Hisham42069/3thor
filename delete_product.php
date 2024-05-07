<?php
session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['product_id'])) {
    require 'contc.php';

    try {
        // Start a transaction
        $conn->beginTransaction();

        // Delete associated images
        $stmt_delete_images = $conn->prepare("DELETE FROM image WHERE ProductID = ?");
        $stmt_delete_images->execute([$_POST['product_id']]);

        // Delete the product
        $stmt_delete_product = $conn->prepare("DELETE FROM product WHERE ProductID = ? AND UserID = ?");
        $stmt_delete_product->execute([$_POST['product_id'], $_SESSION['UserID']]);

        // Commit the transaction
        $conn->commit();

        header("Location: about.php");
        exit();
    } catch (PDOException $e) {
        // Roll back the transaction on error
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: about.php");
    exit();
}
?>
