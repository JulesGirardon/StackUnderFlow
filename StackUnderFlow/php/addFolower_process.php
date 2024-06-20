<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['profile_user_id'])) {
    $profile_user_id = $_POST['profile_user_id'];

    try {
        $conn = new PDO("mysql:host=localhost;dbname=stackunderflow", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "user id =" . $_SESSION['user_id'];
        echo "follower_id = " . $profile_user_id;
        $stmt = $conn->prepare("INSERT INTO follows (idUser1, idUser2) VALUES (:user_id, :follower_id)");
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':follower_id', $profile_user_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: ../index/profil.php?id=$profile_user_id");
    }

    catch (PDOException $e) {
        die("Échec de la connexion à la base de données : " . $e->getMessage());
    }

    $conn = null;
} else {
    echo "Requête invalide.";
}