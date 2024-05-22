<?php
session_start();

try {
    $conn = new PDO("mysql:host=localhost;dbname=stackunderflow", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['nom'], $_POST['email'], $_POST['mot_de_passe'])) {
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $mot_de_passe = $_POST['mot_de_passe'];

        $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        $sql_check = "SELECT * FROM USERS WHERE pseudo = :nom OR mail = :email";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt_check->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {
            header("Location: ../index/inscription.php?error=pseudo_used");
            exit();
        } else {
            $sql = "INSERT INTO USERS (pseudo, mail, pwd) VALUES (:nom, :email, :mot_de_passe_hache)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':mot_de_passe_hache', $mot_de_passe_hache, PDO::PARAM_STR);
            $stmt->execute();

            $user_id = $conn->lastInsertId();
            $_SESSION['user_id'] = $user_id;

            header("Location: ../index/index.php");
            exit();
        }
    }
} catch (PDOException $e) {
    die("Échec de la connexion à la base de données : " . $e->getMessage());
}
?>
