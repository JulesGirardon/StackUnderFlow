<?php
session_start();

if (isset($_POST['post_title'], $_POST['post_theme'], $_SESSION['user_id']) && $_POST['post_title'] != "") {
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=lbde','root','');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e) {
        exit("Echec de la connection." . $e->getMessage());
    }

    $stmt2 = $bdd->prepare("SELECT title FROM posts WHERE posts.author = :author2 AND posts.title= :title2");
    $stmt2->bindParam(':author2', $_SESSION['user_id']);
    $stmt2->bindParam(':title2', $_POST['post_title']);
    $stmt2->execute();

    if($stmt2->rowCount() > 0) {
        echo "Le post existe déjà.";
    } else {
        $stmt = $bdd->prepare("INSERT INTO posts (title, idTheme, author) VALUES (:title, :idTheme, :author)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':idTheme', $idTheme);
        $stmt->bindParam(':author', $author);

        $title = $_POST['post_title'];
        $idTheme = $_POST['post_theme'];
        $author = $_SESSION['user_id'];
        $stmt->execute();
    }
} else {
    echo "Il n'y a pas de titre !";
}