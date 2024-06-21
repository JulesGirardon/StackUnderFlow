<?php
session_start();


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_id']) isset($_SESSION['user_id'])) {
    $post_id = $_POST['post_id'];
    $message_id = $_POST['message_id'];

    try {
        $conn = new PDO("mysql:host=localhost;dbname=stackunderflow", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $stmt = $conn->prepare("DELETE FROM likes WHERE idUser = :user_id AND idMessage = :message_id");
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':message_id', $message_id, PDO::PARAM_INT);
        $stmt->execute();
        header("Location: ../index/post.php?id=$post_id");
        exit();
    } catch (PDOException $e) {
        die("Échec de la connexion à la base de données : " . $e->getMessage());
    }

} else {
    echo "Requête invalide.";
}

