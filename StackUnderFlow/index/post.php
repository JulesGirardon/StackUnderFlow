<?php
session_start();

if(isset($_GET['id'])) {
    $post_id = $_GET['id'];

    try {
        $conn = new PDO("mysql:host=localhost;dbname=stackunderflow", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT * FROM posts WHERE idPost = :idPost");
        $stmt->bindParam(':idPost', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        $post_info = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$post_info) {
            echo "Aucun post trouvé avec cet ID.";
            exit;
        }

        $stmt = $conn->prepare("SELECT * FROM users WHERE idUser = :idUser");
        $stmt->bindParam(':idUser', $post_info['author'], PDO::PARAM_INT);
        $stmt->execute();
        $user_info = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $conn->prepare("SELECT nomTheme FROM themes WHERE idTheme = :idTheme");
        $stmt->bindParam(':idTheme', $post_info['idTheme'], PDO::PARAM_INT);
        $stmt->execute();
        $theme_name = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $conn->prepare("SELECT MIN(m.dateMessage) AS date
                                      FROM messages AS m
                                      JOIN post_messages AS pm ON pm.idMessage = m.idMessage
                                      JOIN posts AS p on p.idPost = pm.idPost
                                      WHERE p.idPost = :idPost");
        $stmt->bindParam(':idPost', $post_info['idPost'], PDO::PARAM_INT);
        $stmt->execute();
        $date_post = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $conn->prepare("SELECT *
                                      FROM messages AS m
                                      JOIN post_messages AS pm ON pm.idMessage = m.idMessage
                                      WHERE pm.idPost = :idPost");
        $stmt->bindParam(':idPost', $post_info['idPost'], PDO::PARAM_INT);
        $stmt->execute();
        $liste_message = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $conn->prepare("SELECT idMessage FROM likes WHERE idUser = :userId");
        $stmt->bindParam(':userId', $_SESSION['user_id'], PDO::PARAM_INT); 
        $stmt->execute();
        $likes = $stmt->fetchAll(PDO::FETCH_COLUMN, 0); 


    } catch (PDOException $e) {
        die("Échec de la connexion à la base de données : " . $e->getMessage());
    }
}

?>

    <!doctype html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Post</title>
        <link rel="stylesheet" type="text/css" href="styleTropBien.css">
        <script src="https://kit.fontawesome.com/5dfd5dfa22.js" crossorigin="anonymous"></script>
    </head>
<body>
<?php include "header.php"; ?>
<?php include "aside.php"; ?>

<main id="main-post">

    <div id="profil-createur">
        <div id="profil-createur-left">
            <h2><?php echo htmlspecialchars($post_info['title']) ?></h2>
            <p>Thème : <?php echo htmlspecialchars($theme_name['nomTheme']) ?></p>
            <p>Date de création : <?php echo htmlspecialchars($date_post['date']) ?></p>
        </div>
        <div id="profil-createur-right">
            <a href="../index/profil.php?id=<?php echo htmlspecialchars($user_info['idUser']) ?>">
                <img src="<?php echo htmlspecialchars($user_info['pdp']) ?>" alt="photo_profil">
                <p>@<?php echo htmlspecialchars($user_info['pseudo']) ?></p>
            </a>
        </div>
    </div>

    <h2 class="main-post-section">Réponses</h2>

    <div id="liste-message-post">
    <?php
    if (!empty($liste_message)) {
        $firstMessage = true; 
        foreach ($liste_message as $message) {
            echo "<div class='liste-message-post-n'><div class='liste-message-post-n-left'>";

            $stmt = $conn->prepare("SELECT * FROM users WHERE idUser = :userId");
            $stmt->bindParam(':userId', $message['authorMessage'], PDO::PARAM_INT);
            $stmt->execute();
            $author_info = $stmt->fetch(PDO::FETCH_ASSOC);

            echo "<a href='../index/profil.php?id=" . htmlspecialchars($author_info['idUser']) . "'><img class='liste-message-post-img' src='" . htmlspecialchars($author_info['pdp']) . "' alt='photo_profil'>";
            echo "<p class='liste-message-post-pseudo'>@" . htmlspecialchars($author_info['pseudo']) . "</p></a>";
            echo "<p class='liste-message-post-date'>Le : " . htmlspecialchars($message['dateMessage']) . "</p>";
            echo "</div>";

            echo "<div id='liste-message-post-n-right'>";
            echo "<p>" . htmlspecialchars($message['textMessage']) . "</p>";

            if (!empty($message['imagePath'])) {
                if ($firstMessage) {
                    echo "<img class='nulachier' src='../uploads/posts/" . htmlspecialchars($post_info['imagePath']) . "' alt='Message Image'>";
                } else {
                    echo "<img class='nulachier' src='../uploads/messages/" . htmlspecialchars($message['imagePath']) . "' alt='Message Image'>";
                }
            }
            echo "</div>";
            $firstMessage = false;

            $stmt = $conn->prepare("SELECT idMessage FROM likes WHERE idUser = :userId");
            $stmt->bindParam(':userId', $_SESSION['user_id'], PDO::PARAM_INT); 
            $stmt->execute();
            $likes = $stmt->fetchAll(PDO::FETCH_COLUMN, 0); 
            ?>
            <?php if (in_array($message["idMessage"], $likes)) : ?>
                <form method="post" action="../php/unlike_message_process.php">
                    <input type="hidden" name="message_id" value="<?php echo $message['idMessage']; ?>">
                    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                    <button type="submit" class="like-button">
                        <i class="fa-solid fa-heart"></i>
                    </button>
                </form>
            <?php else : ?>
                <form method="post" action="../php/like_message_process.php">
                    <input type="hidden" name="message_id" value="<?php echo $message['idMessage']; ?>">
                    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>"> 
                    <button type="submit" class="like-button">
                        <i class="fa-regular fa-heart"></i>
                    </button>
                </form>
            <?php endif; ?>
            <?php
            echo "</div>";
        }
    }
    ?>
</div>



    <?php
    if(isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE idUser = :userId");
        $stmt->bindParam(':userId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $my_info = $stmt->fetch(PDO::FETCH_ASSOC);

        echo '<h2 class="main-post-section">Répondre</h2>';
        echo "<form action='../php/post_message_process.php' method='POST' enctype='multipart/form-data'>";
        echo "<div class='reponse-post-n'><div class='reponse-post-n-left'>";
        echo "<a href='../index/profil.php?id=" . htmlspecialchars($my_info['idUser']) . "'><img class='reponse-post-n-img' src='" . htmlspecialchars($my_info['pdp']) . "' alt='photo_profil'>";
        echo "<p class='reponse-post-pseudo'>@" . htmlspecialchars($my_info['pseudo']) . "</p></a>";
        echo "<button type='submit' id='reponse-post-submit'>Envoyer</button>";
        echo "</div>";
        echo "<div id='reponse-post-n-right'>";
        echo "<textarea name='post_message' required></textarea>";
        echo "<input type='hidden' name='post_id' value='" . htmlspecialchars($post_id) . "' />";
        echo "<input type='file' name='message_image' accept='image/*'><br><br>";
        echo "</div></div></form>";
    }
    ?>
</main>
</body>
</html>