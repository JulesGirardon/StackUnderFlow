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

        if (!$is_own_profile){
            $stmt = $conn->prepare("SELECT COUNT(*) FROM FOLLOWS WHERE idUser1 = :idUser1 AND idUser2 = :idUser2");
            $stmt->bindParam(':idUser1', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':idUser2', $profile_user_id, PDO::PARAM_INT);

            $stmt->execute();

            $count = $stmt->fetchColumn();

            if ($count > 0){
                $is_followed = False;
            } else {
                $is_followed = True;
            }

        }


    } catch (PDOException $e) {
        die("Échec de la connexion à la base de données : " . $e->getMessage());
    }

    $conn = null;
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
    <link rel="stylesheet" href="styleTropBien.css">
    <script src="https://kit.fontawesome.com/5dfd5dfa22.js" crossorigin="anonymous"></script>
</head>
<body>
<?php include "header.php"; ?>
<?php include "aside.php"; ?>
<div class="container">
    <?php if (isset($profile_user)) : ?>
        <?php if ($is_own_profile) : ?>
            <a href="profilEdit.php?id=<?php echo $_SESSION['user_id']; ?>">
                <button type="button" id="profil-edit-button">
                    <i class="fa fa-edit" style="font-size: 36px"></i>
                </button>
            </a>
        <?php else : ?>
            <?php if (!$is_followed): ?>
                <form action="../php/removeFolower_process.php" method="post">
                    <input type="hidden" name="profile_user_id" value="<?php echo $profile_user_id; ?>">
                    <button type="submit" id="profil-follow-button">
                        <i class="fa fa-user-minus" style="font-size: 36px"></i>
                    </button>
                </form>
            <?php else: ?>
                <form action="../php/addFolower_process.php" method="post">
                    <input type="hidden" name="profile_user_id" value="<?php echo $profile_user_id; ?>">
                    <button type="submit" id="profil-follow-button">
                        <i class="fa fa-user-plus" style="font-size: 36px"></i>
                    </button>
                </form>
            <?php endif; ?>
        <?php endif; ?>

        <div class="profil-img">
            <img src="<?php echo getPPUser($profile_user_id); ?>" alt="Photo de profil">
        </div>
        <h1 id="profil-name">@<?php echo htmlspecialchars($profile_user['pseudo']); ?></h1>

        <div id="profil-bio">
            <p>A propos de <?php echo htmlspecialchars($profile_user['pseudo']); ?> : </p>
            <p id="profil-bio-bio"><?php echo nl2br(htmlspecialchars($profile_user['bio'])); ?></p>
        </div>
        <p id="profil-followers-count">Abonnés : <?php echo getNbOfFollowingUsers($profile_user_id); ?></p>
    <?php else : ?>
        <p>Profil non trouvé.</p>
    <?php endif; ?>
</div>
</body>
</html>