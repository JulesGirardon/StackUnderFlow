<header>
    <div id="header-left">
        <a href="index.php"><img id="header-left-logo" src="icon.png" alt="logo_stack"></img></a>
    </div>

    <div id="header-center">
        <?php
        if (isset($_SESSION["user_id"])) {
            echo "<a href='profil.php?id={$_SESSION['user_id']}'>Profil</a>";
        } else {
            echo "<a href='connexion.php' id='header-center-profile'>Profil</a>";
        }
        ?>
    </div>

    <div id="header-right">
        <?php
        if(isset($_SESSION["user_id"])) {
            echo "<button onclick='MyNotif()'>
             <img src='notifbell.png' alt='notifbell' id='header-right-bell'>
             </button>
             <div id='header-left-deconnexion'>
             <a href='../php/deconnexion_process.php'>Déconnexion</a>
             </div>";
        } else {
            echo "
             <a href='inscription.php'>Inscription</a>
             <a href='connexion.php'>Connexion</a>";
        }
        ?>
    </div>
</header>
