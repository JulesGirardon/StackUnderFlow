<?php
session_start();

$conn = new PDO("mysql:host=localhost;dbname=stackunderflow", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_SESSION["user_id"];

if (isset($_FILES['ppUser'])){

    $extension = strrchr($_FILES['ppUser']['name'], '.');
    $extensions = array('.png', '.gif', '.jpg', '.jpeg');
    $taille_maxi = 10000000000;
    $taille = filesize($_FILES['ppUser']['tmp_name']);
    if(!in_array($extension, $extensions)){
        $erreur = 'Vous devez uploader un fichier de type png, gif, jpg, jpeg...';
        echo $erreur;
    }
    else if($taille>$taille_maxi){
        $erreur = 'Le fichier est trop gros...';
    }

    else{
        $dossier = '../profilePicture/';
        $fichier = basename($_FILES['ppUser']['name']);
        $fichier = strtr($fichier,
            'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
            'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
        $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
        $pdp = $dossier . $fichier;
        if (move_uploaded_file($_FILES['ppUser']['tmp_name'], $dossier . $fichier)){
            $sql = "UPDATE USERS SET pdp = :pdp WHERE idUser = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':pdp', $pdp, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $stmt->execute();
        }
        else{
            echo 'Echec de l\'upload';
        }
    }
}

if (isset($_POST['pseudo'])){
    $pseudo = $_POST['pseudo'];
    $sql = "UPDATE USERS SET pseudo = :pseudo WHERE idUser = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    $stmt->execute();
}

if (isset($_POST['bio'])){
    $bio = $_POST['bio'];
    $sql = "UPDATE USERS SET bio = :bio WHERE idUser = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':bio', $bio, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    $stmt->execute();
}

if (isset($_POST['status'])){
    $statut = $_POST['status'];
    $sql = "UPDATE USERS SET statut = :statut WHERE idUser = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':statut', $statut, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}

header("Location: ../index/profil.php?id=" . $id);
exit();
?>
