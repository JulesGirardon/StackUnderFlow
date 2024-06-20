<?php
session_start();

function redirectToProfile($userId = null)
{
    header("Location: profil.php" . ($userId ? "?id=" . $userId : ""));
    exit();
}

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
        <link rel="stylesheet" href="qsdfqsdf.css">
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
                <form action="../php/profil_process.php" method="POST" enctype="multipart/form-data">
                    <div class="profil-img-container">
                        <div class="profil-img">
                            <label for="ppUser">Photo de profil :</label>
                            <input type="file" name="ppUser" id="ppUser">
                            <div>
                                <img src="<?php echo getPPUser($_SESSION["user_id"]); ?>" alt="Preview" id="previewImage">
                                <i class="fa fa-file-image-o" id="iconImage"></i>
                            </div>
                        </div>
                    </div>
                    <input type="text" name="pseudo" id="container-profil-edit-pseudo" value="<?php echo htmlspecialchars($profile_user['pseudo']); ?>">
                    <textarea name="bio" id="container-profil-edit-bio"><?php echo htmlspecialchars($profile_user["bio"]); ?></textarea>
                    <select name="status" id="container-profil-edit-statut">
                        <option value="En ligne" <?php echo $profile_user['statut'] == 'online' ? 'selected' : ''; ?>>En ligne</option>
                        <option value="Ne pas déranger" <?php echo $profile_user['statut'] == 'dnd' ? 'selected' : ''; ?>>Ne pas déranger</option>
                        <option value="Invisible" <?php echo $profile_user['statut'] == 'invisible' ? 'selected' : ''; ?>>Invisible</option>
                    </select>
                    <button type="submit" id="container-profil-edit-submit">Mettre à jour</button>
                </form>
            <?php else : ?>
                <?php redirectToProfile($profile_user_id); ?>
            <?php endif; ?>
        <?php else : ?>
            <?php redirectToProfile(); ?>
        <?php endif; ?>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ppUserInput = document.getElementById('ppUser');
            var previewImage = document.getElementById('previewImage');

            ppUserInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                    };
                    reader.readAsDataURL(this.files[0]);
                } else {
                    previewImage.src = "<?php echo getPPUser($_SESSION["user_id"]); ?>";
                }
            });
        });

        document.getElementById('previewImage').addEventListener('mouseover', function() {
            document.getElementById('iconImage').style.opacity = "1";
        });

        document.getElementById('previewImage').addEventListener('mouseout', function() {
            document.getElementById('iconImage').style.opacity = "0";
        });
    </script>
    </body>

</html>