<?php
session_start();

try {
    $bdd = new PDO('mysql:host=localhost;dbname=stackunderflow','root','');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    exit("Echec de la connection." . $e->getMessage());
}
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link href="style.css" type="text/css" rel="stylesheet">
        <title>Création post</title>

        <style>
            #creation_post {
                background: #e2d1c1;
                margin: auto;
                width: 400px;
                height: 300px;
                border-radius: 25px;
            }

            #title {
                margin: 50px 50px 0 50px;
            }

            #category {
                width: 100px;
                height: 25px;
                text-align: center;
                display: flex;
                margin: 50px auto;
            }

            #button {
                display: flex;
                margin: 50px auto;
                height: 50px;
                width: 100px;
                justify-content: center;
                align-items: center;
                border: 2px solid white;
                color: black;
                border-radius: 15px;
                background-color: #e2d1c1;
            }

            #button:hover {
                background-color: white;
            }
        </style>

    </head>

    <body>
    <?php
    include "header.php";
    ?>

    <?php
    include "aside.php";
    ?>

    <div id="creation_post">
        <form action="../php/create_post_process.php" method="post" id="title">
            <input type="text" name="post_title" placeholder="Titre du post">

            <?php
            try {
                $stmt = $bdd->query("SELECT idTheme, nomTheme FROM themes");
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Erreur lors de la récupération des données : " . $e->getMessage());
            }
            ?>

            <select name="post_theme" id="category">
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category['idTheme']); ?>">
                        <?php echo htmlspecialchars($category['nomTheme']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" id="button">Créer</button>
        </form>
    </div>

    </body>

</html>
