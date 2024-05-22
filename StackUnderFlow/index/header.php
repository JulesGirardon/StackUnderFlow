<style>
    header {
        background-color: red;
        width: 100%;

        display: flex;
        align-items: center;

        margin-right: 50%;

    }

    #header-left {
        width: 33%;
    }

    #header-left-logo {
        width: 100%;
    }

    #header-center {
        background-color: blue;
        width: 33%;
    }

    #header-right {
        background-color: green;
        width: 33%;
    }

    #header-right-bell {
        align-items: center;
        height: 30px;
        width: 30px;
    }

</style>

<header>
    <div id="header-left">
        <a href="index.php"><img id="header-left-logo" src="icon.png" alt="logo_stack"></img></a>
    </div>

    <div id="header-center">
        <?php
        if (isset($_SESSION["user_id"])) {
            echo "<a href='profil.php?id={$_SESSION['user_id']}'>Profil</a>";
        } else {
            echo "<a href='connexion.php'>Profil</a>";
        }
        ?>
    </div>

    <div id="header-right">
        <?php
        if(isset($_SESSION["user_id"])) {
            echo "<div id='header-left-deconnexion'>
             <a href='../php/deconnexion_process.php'>Déconnexion</a>
             </div>";
        } else {
            echo "<button onclick='MyNotif()'>
             <img src='notifbell.png' alt='notifbell' id='header-right-bell'>
             </button>
             <a href='inscription.php'>Inscription</a>
             <a href='connexion.php'>Connexion</a>";
        }
        ?>
    </div>
</header>
