<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

if (isset($_GET['id'])) {
    $profile_user_id = $_GET['id'];

    try {
        $conn = new PDO("mysql:host=localhost;dbname=stackunderflow", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT * FROM USERS WHERE idUser = :idUser");
        $stmt->bindParam(':idUser', $profile_user_id, PDO::PARAM_INT);
        $stmt->execute();
        $profile_user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($profile_user) {
            $is_own_profile = ($profile_user_id == $_SESSION['user_id']);
        } else {
            echo "Aucun utilisateur trouvé avec cet ID.";
            $profile_user = null;
        }

    } catch (PDOException $e) {
        die("Échec de la connexion à la base de données : " . $e->getMessage());
    }

    $conn = null; // close the connection
} else {
    echo "ID utilisateur non spécifié dans l'URL.";
    $profile_user = null;
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@<?php echo isset($profile_user) ? htmlspecialchars($profile_user['pseudo']) : 'Profil'; ?> - Profil</title>
        <link rel="stylesheet" href="putaincestchiantla.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>

    <body>
        <?php
        include "header.php";
        ?>

        <?php
        include "aside.php";
        ?>
        <div class="container">
            <?php if (isset($profile_user)) : ?>
                <?php if ($is_own_profile) : ?>
                <a href="profilEdit.php?id=<?php echo $_SESSION['user_id']; ?>">
                    <button type="button" class="profil-edit-button">
                        <i class="fa fa-edit" style="font-size: 36px"></i>
                    </button>
                </a>
            <?php else : ?>
                <button type="button" class="profil-edit-button" id="profil-follow-button">
                    <i class="fa fa-user-plus"></i>
                </button>
                <script>
                    document.getElementById('profil-follow-button').addEventListener('click', function() {
                        var currentIconClass = this.querySelector('.fa').classList.contains('fa-user-plus') ? 'fa-user-o' : 'fa-user-plus';
                        this.querySelector('.fa').className = currentIconClass;
                    });
                </script>
            <?php endif; ?>

                <div class="profil-img">
                    <img src="<?php echo getPPUser($profile_user_id); ?>" alt="Photo de profil">
                </div>
                <h1>@<?php echo htmlspecialchars($profile_user['pseudo']); ?></h1>

                <p id="profil-bio">A propos de <?php echo htmlspecialchars($profile_user['pseudo']); ?> <br> <div id="profil_bio"> <?php echo htmlspecialchars($profile_user['bio']); ?> </div></p>
                <p id="profil-followers-count">Abonnés : <?php echo getNbOfFollowingUsers($profile_user_id); ?></p>
            <?php else : ?>
                <p>Profil non trouvé.</p>
            <?php endif; ?>
        </div>
    </body>

</html>