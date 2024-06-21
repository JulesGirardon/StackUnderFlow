<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    exit("Vous devez être connecté pour envoyer un message.");
}

if (isset($_POST['post_message']) && isset($_POST['post_id'])) {
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=stackunderflow', 'root', '');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $message = $_POST['post_message'];
        $postId = $_POST['post_id'];
        $author = $_SESSION['user_id'];
        $photo = null;

        // Check and create directories if not exist
        $uploadDir = '../uploads/messages/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Handle file upload
        if (isset($_FILES['message_image']) && $_FILES['message_image']['error'] == UPLOAD_ERR_OK) {
            $photo = basename($_FILES['message_image']['name']);
            $targetPath = $uploadDir . $photo;
            if (!move_uploaded_file($_FILES['message_image']['tmp_name'], $targetPath)) {
                exit("Erreur lors du téléchargement de l'image.");
            }
        }

        // Insert message into database
        $stmt = $bdd->prepare("INSERT INTO messages (textMessage, imagePath, authorMessage, dateMessage) VALUES (:message, :photo, :author, NOW())");
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':photo', $photo); // Store only the image name
        $stmt->bindParam(':author', $author);
        $stmt->execute();
        $messageId = $bdd->lastInsertId();

        // Link message to post
        $stmt = $bdd->prepare("INSERT INTO post_messages (idPost, idMessage) VALUES (:post, :message)");
        $stmt->bindParam(':post', $postId);
        $stmt->bindParam(':message', $messageId);
        $stmt->execute();

        header("Location: ../index/post.php?id=$postId");
    } catch (PDOException $e) {
        if ($e->getCode() == '45000') {
            echo "Erreur: Le message contient des mots interdits.";
        } else {
            echo "Erreur lors de l'ajout du message: " . $e->getMessage();
        }
    }
} else {
    echo "Veuillez remplir tous les champs.";
}
?>
