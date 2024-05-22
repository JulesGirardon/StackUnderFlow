<?php
session_start();

if (isset($_POST['email'], $_POST['mot_de_passe'])) {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    try {
        $conn = new PDO("mysql:host=localhost;dbname=stackunderflow", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT * FROM USERS WHERE mail = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($mot_de_passe, $row['pwd'])) {
                $_SESSION['user_id'] = $row['idUser'];
                header("Location: ../index/index.php");
                exit();
            } else {
                header("Location: ../index/connexion.php?error=incorrect_password");
                exit();
            }
        } else {
            header("Location: ../index/connexion.php?error=user_nofind");
            exit();
        }
    } catch (PDOException $e) {
        die("Échec de la connexion à la base de données : " . $e->getMessage());
    }
}
?>
