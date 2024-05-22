<?php
function ShowFollowedUsers($user_id)
{
    try {
        $conn = new PDO("mysql:host=localhost;dbname=stackunderflow", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT users.pseudo, users.idUser FROM users JOIN follows ON users.idUser = follows.idUser2 WHERE follows.idUser1 = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $url = "profil.php?id=" . urlencode($row['idUser']);
            echo '<li><a href="' . $url . '">' . htmlspecialchars($row["pseudo"]) . '</a></li>';
        }

    } catch (PDOException $e) {
        die("Échec de la connexion à la base de données : " . $e->getMessage());
    }
}

function getNbOfFollowedUsers($user_id)
{
    try {
        $conn = new PDO("mysql:host=localhost;dbname=stackunderflow", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT COUNT(*) as count FROM users JOIN follows ON users.idUser = follows.idUser2 WHERE follows.idUser1 = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];

    } catch (PDOException $e) {
        die("Échec de la connexion à la base de données : " . $e->getMessage());
    }
}

function getNbOfFollowingUsers($user_id)
{
    try {
        $conn = new PDO("mysql:host=localhost;dbname=stackunderflow", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT COUNT(*) as count FROM users JOIN follows ON users.idUser = follows.idUser1 WHERE follows.idUser2 = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];

    } catch (PDOException $e) {
        die("Échec de la connexion à la base de données : " . $e->getMessage());
    }
}

function getPPUser($user_id)
{
    try {
        $conn = new PDO("mysql:host=localhost;dbname=stackunderflow", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT pdp FROM users WHERE idUser = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['pdp'];

    } catch (PDOException $e) {
        die("Échec de la connexion à la base de données : " . $e->getMessage());
    }
}
?>
