<?php
session_start();

try {
    $bdd = new PDO('mysql:host=localhost;dbname=stackunderflow','root','');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit("Echec de la connexion: " . $e->getMessage());
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="putaincestchiantla.css" type="text/css" rel="stylesheet">
    <title>Création post</title>
</head>
    <body>
    <?php include "header.php"; ?>
    <?php include "aside.php"; ?>

        <main>
            <div id="main-creation-post">
                <form action="../php/create_post_process.php" method="post">
                    <input type="text" name="post_title" placeholder="Titre du post" id="main-creation-post-title" required>

                    <?php
                    try {
                        $stmt = $bdd->query("SELECT idTheme, nomTheme FROM themes");
                        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        die("Erreur lors de la récupération des données: " . $e->getMessage());
                    }
                    ?>

                    <select name="post_theme" id="main-creation-post-theme" required>
                            <option value="" disabled selected>Choisir un thème</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['idTheme']); ?>">
                                <?php echo htmlspecialchars($category['nomTheme']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <textarea id="main-creation-post-message" name="post_message" rows="10" cols="50" required></textarea><br><br>

                    <button type="submit" id="main-creation-post-button">Créer le post</button>
                </form>
            </div>
        </main>
    </body>
</html>
