<?php
session_start();

try {
    $bdd = new PDO('mysql:host=localhost;dbname=stackunderflow', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit("Echec de la connexion: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/vnd.icon" href="icon.png">
        <link rel="stylesheet" href="encore.css">
        <title>StackUnderFlow</title>
    </head>
    <body>
        <?php include "header.php"; ?>
        <?php include "aside.php"; ?>
    <main>
        <?php
        if (isset($_SESSION['user_id'])) {
            $stmt = $bdd->prepare("SELECT * FROM posts WHERE posts.author = :author");
            $stmt->bindParam(':author', $_SESSION['user_id']);
            $stmt->execute();

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($data)) {
                foreach ($data as $post) {
                    echo "<article>";
                    echo "<h2 id='test'>" . htmlspecialchars($post['title']) . "</h2>";
                    echo "</article>";
                }
            } else {
                echo "<p>Aucun post trouvé pour cet utilisateur.</p>";
            }
        } else {
            echo "<p>Veuillez vous connecter pour voir vos posts.</p>";
        }
        ?>
    </main>

    </body>
</html>
