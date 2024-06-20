<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - StackUnderFlow</title>
    <link rel="icon" type="image/vnd.icon" href="icon.png">
    <link rel="stylesheet" href="qsdfqsdf.css">
</head>
<body>
    <?php
        include "header.php";
    ?>

    <?php
        include "aside.php"
    ?>

    <div class="container">
        <h1>Connexion</h1>
        <form action="../php/connexion_process.php" method="post">
            <input type="email" name="email" placeholder="Adresse mail" required class="container-input-pseudo">
            <?php
				if (isset($_GET['error']) && $_GET['error'] == 'user_nofind') {
		    		echo '<div id="erreur">Utilisateur non trouvé</div>';
				}
			?>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required class="container-input-pseudo">
            <?php
				if (isset($_GET['error']) && $_GET['error'] == 'incorrect_password') {
		    		echo '<div id="erreur">Mot de passe incorrect !</div>';
				}
			?>
            <input type="submit" value="Se connecter" class="container-submit">
        </form>
        <p class="container-other-solution">Pas encore inscrit ? <a href="inscription.php">Inscrivez-vous</a></p>
    </div>
</body>
</html>
