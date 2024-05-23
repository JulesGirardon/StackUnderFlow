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

    $stmt = $bdd->prepare("SELECT title FROM posts WHERE author = :author AND title = :title");
    $stmt->bindParam(':author', $author);
    $stmt->bindParam(':title', $title);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "Le post existe déjà.";
    } else {
        try {
            $stmt = $bdd->prepare("INSERT INTO messages (textMessage, authorMessage, dateMessage) VALUES (:message, :author, NOW())");
            $stmt->bindParam(':message', $message);
            $stmt->bindParam(':author', $author);
            $stmt->execute();
            $messageId = $bdd->lastInsertId();

            $stmt = $bdd->prepare("INSERT INTO posts (title, idTheme, author) VALUES (:title, :idTheme, :author)");
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':idTheme', $idTheme);
            $stmt->bindParam(':author', $author);
            $stmt->execute();
            $postId = $bdd->lastInsertId();

            $stmt = $bdd->prepare("INSERT INTO post_messages (idPost, idMessage) VALUES (:post, :message)");
            $stmt->bindParam(':post', $postId);
            $stmt->bindParam(':message', $messageId);
            $stmt->execute();

            echo "Post créé avec succès.";
        } catch (PDOException $e) {
            if ($e->getCode() == '45000') {
                echo "Erreur: Le message contient des mots interdits.";
            } else {
                echo "Erreur lors de la création du post: " . $e->getMessage();
            }
        }
    }
} else {
    echo "Veuillez remplir tous les champs.";
}
?>
