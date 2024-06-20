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
        <link rel="stylesheet" href="qsdfqsdf.css">
        <title>StackUnderFlow</title>
    </head>
    <body>
        <?php include "header.php"; ?>
        <?php include "aside.php"; ?>

        <main id="list-post">
            <?php
            if (isset($_SESSION['user_id'])) {
                $stmt = $bdd->prepare("SELECT p.idPost, p.title, m.textMessage, m.dateMessage, t.nomTheme
                                             FROM posts AS p 
                                             LEFT JOIN post_messages AS pm ON p.idPost = pm.idPost
                                             LEFT JOIN messages AS m ON pm.idMessage = m.idMessage
                                             LEFT JOIN themes AS t ON p.idTheme = t.idTheme
                                             WHERE p.author = :author 
                                             ORDER BY m.dateMessage ASC");
                $stmt->bindParam(':author', $_SESSION['user_id']);
                $stmt->execute();

                $data = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if (!isset($data[$row['idPost']])) {
                        $data[$row['idPost']] = $row;
                    }
                }

                if (!empty($data)) {
                    $firstRow = true;
                    foreach ($data as $index => $post) {
                        $firstColumn = $index % 2 == 0;
                        $firstRowClass = $firstRow ? ' first-row' : '';
                        $firstColumnClass = $firstColumn ? ' first-column' : '';
                        echo "<article class='post $firstRowClass $firstColumnClass'>";
                        echo "<a href='../index/post.php?id=" . $post['idPost'] . "'><p class='post-title'>" . htmlspecialchars($post['title']) . "</p></a>";
                        if (!empty($post['textMessage'])) {
                            echo "<p class='post-message'>Premier message : <br><br>" . htmlspecialchars($post['textMessage']) . "</p>";
                            echo "<p class='post-message'>Thème: " . htmlspecialchars($post['nomTheme']) . "</p>";
                            echo "<p class='post-message'>Date: " . htmlspecialchars($post['dateMessage']) . "</p>";
                        } else {
                            echo "<p class='post-message'>Aucun message trouvé pour ce post.</p>";
                        }
                        echo "</article>";

                        if ($firstColumn) {
                            $firstRow = false;
                        }
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