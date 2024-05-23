<aside>
    <nav>
        <ul>
            <div class="aside-nav-ul-div" onclick="toggleList(this)">
                <a href="index.php" class="aside-nav-ul-div"><h3>Accueil</h3></a>
            </div>

            <div class="aside-nav-ul-div" onclick="toggleList(this)">
                <a href="#"><h3>Thème le plus populaire</h3></a>
            </div>

            <div class="aside-nav-ul-div" onclick="toggleList(this)">
                <?php
                include "../php/abonnement.php";

                if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION['user_id'])) {
                    echo "<h3>Vos abonnements - (" . getNbOfFollowedUsers($_SESSION['user_id']) . " Abonnements)</h3>";
                    echo "<ul class='abonnements'>";
                    echo ShowFollowedUsers($_SESSION['user_id']);
                    echo "</ul>";
                } else {
                    echo "<h3>Connectez-vous pour voir vos abonnements.</h3>";
                }
                ?>
            </div>

            <div class="aside-nav-ul-div" onclick="toggleList(this)">
            <?php
                if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION['user_id'])) {
                    echo '
                            <h3>Posts</h3>
                            <ul>
                                <li><a href="create_post.php">Créer un post</a></li>
                                <li><a href="liste_post.php">Accédez à ses posts</a></li>
                            </ul>';
                } else {
                    echo "<h3>Connectez-vous pour voir vos posts.</h3>";
                }
            ?>
            </div>

            <div class="aside-nav-ul-div" onclick="toggleList(this)">
                <h3>Autres</h3>
                <ul>
                    <li><a href="#">À propos</a></li>
                    <li><a href="#">Aide</a></li>
                    <li><a href="#">Politique de confidentialité</a></li>
                </ul>
            </div>
        </ul>
    </nav>
</aside>
<script>
    function toggleList(element) {
        var ul = element.querySelector('ul');
        if (ul) {
            if (!event.target.closest('li')) {
                if (ul.classList.contains('show')) {
                    ul.classList.remove('show');
                    setTimeout(function() {
                        ul.style.display = 'none';
                    }, 500);
                } else {
                    ul.style.display = 'block';
                    setTimeout(function() {
                        ul.classList.add('show');
                    }, 10);
                }
            }
        }
    }
</script>