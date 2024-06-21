<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    exit("Vous devez être connecté pour créer un post.");
}

if (isset($_POST['post_title'], $_POST['post_theme'], $_POST['post_message']) && !empty($_POST['post_title'])) {
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=stackunderflow', 'root', '');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        exit("Echec de la connexion: " . $e->getMessage());
    }

    $title = $_POST['post_title'];
    $idTheme = $_POST['post_theme'];
    $message = $_POST['post_message'];
    $author = $_SESSION['user_id'];
    $photoName = null;

    // Check and create directories if not exist
    $uploadDir = '../uploads/posts/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // create directory with recursive flag
    }

    // Handle file upload
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] == UPLOAD_ERR_OK) {
        $photoName = basename($_FILES['post_image']['name']);
        $photoPath = $uploadDir . $photoName;
        if (!move_uploaded_file($_FILES['post_image']['tmp_name'], $photoPath)) {
            exit("Erreur lors du téléchargement de l'image.");
        }
    }

    try {
        // Start transaction
        $bdd->beginTransaction();

        // Insert message into messages table
        $stmt = $bdd->prepare("INSERT INTO messages (textMessage, imagePath, authorMessage, dateMessage) VALUES (:message, :photo, :author, NOW())");
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':photo', $photoName); // Store only the filename in the database
        $stmt->bindParam(':author', $author);
        $stmt->execute();
        $messageId = $bdd->lastInsertId();

        // Insert post into posts table
        $stmt = $bdd->prepare("INSERT INTO posts (title, idTheme, author, imagePath) VALUES (:title, :idTheme, :author, :imagePath)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':idTheme', $idTheme);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':imagePath', $photoName); // Store only the filename in the database
        $stmt->execute();
        $postId = $bdd->lastInsertId();

        // Insert relation into post_messages table
        $stmt = $bdd->prepare("INSERT INTO post_messages (idPost, idMessage) VALUES (:post, :message)");
        $stmt->bindParam(':post', $postId);
        $stmt->bindParam(':message', $messageId);
        $stmt->execute();

        // Commit transaction
        $bdd->commit();

        header("Location: ../index/post.php?id=$postId");
    } catch (PDOException $e) {
        // Rollback in case of error
        $bdd->rollBack();

        if ($e->getCode() == '45000') {
            echo "Erreur: Le message contient des mots interdits.";
        } else {
            echo "Erreur lors de la création du post: " . $e->getMessage();
        }
    }
} else {
    echo "Veuillez remplir tous les champs.";
}
?>
