<aside>
    <nav>
        <ul>
            <div class="aside-nav-ul-div" onclick="toggleList(this)">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="#">Thème le plus populaire</a></li>
            </div>

            <div class="aside-nav-ul-div" onclick="toggleList(this)">
                <?php
                    include "../php/abonnement.php";

                    if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION['user_id'])) {
                        echo "<h3>Vos abonnements - (" . getNbOfFollowedUsers($_SESSION['user_id']) . " Abonnements)</h3>";
                        echo "<ul class='abonnements' style='display: none;'>";
                        echo ShowFollowedUsers($_SESSION['user_id']);
                        echo "</ul>";
                    } else {
                        echo "<h3>Connectez-vous pour voir vos abonnements.</h3>";
                    }
                ?>
            </div>

            <div class="aside-nav-ul-div" onclick="toggleList(this)">
                <h3>Posts</h3>
                <ul style="display: none;">
                    <li><a href="create_post.php">Créer un post</a></li>
                    <li><a href="liste_post.php">Accédez à ses posts</a></li>
                </ul>
            </div>

            <div class="aside-nav-ul-div" onclick="toggleList(this)">
                <h3>Autres</h3>
                <ul style="display: none;">
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
                ul.style.display = (ul.style.display === 'none') ? 'block' : 'none';
            }
        }
    }
</script>