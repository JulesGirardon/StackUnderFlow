<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StackUnderFlow</title>
    <link rel="icon" type="image/vnd.icon" href="icon.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Inscription</h1>
        <form action="../php/inscription_process.php" method="post">
            <input type="text" name="nom" placeholder="Pseudo" required>
            <input type="email" name="email" placeholder="Adresse e-mail" required>
            <?php
				if (isset($_GET['error']) && $_GET['error'] == 'pseudo_used') {
		    		echo '<div id="erreur">Pseudo ou email dejà utilisé !</div>';
				}
			?>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
            <input type="submit" value="S'inscrire">
        </form>
        <p class="message">Déjà inscrit ? <a href="connexion.php">Connectez-vous</a></p>
    </div>
</body>
</html>