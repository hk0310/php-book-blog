<nav>
    <ul>
        <li><a href="<?= BASE ?>">Home</a></li>
        <li><a href="<?= BASE ?>/books">Books</a></li>
        <li><a href="<?= BASE ?>/genres">Genres</a></li>
    </ul>

    <form action="#" method="post">
        <input type="text" id="bookSearch" name="search" placeholder="Search for books">
    </form>

    <ul>
        <?php if(!isset($_SESSION['username'])): ?>
            <li><a href="<?= BASE ?>/auth/login.php">Login</a></li>
            <li><a href="<?= BASE ?>/auth/register.php">Register</a></li>
        <?php else: ?>
            <?php if($_SESSION['role_id'] == 3): ?>
                <li><a href="<?= BASE ?>/Users/">Users</a></li>
            <?php endif ?>
            <li><a href="<?= BASE ?>/auth/logout.php">Logout</a></li>
        <?php endif ?>
    </ul>
</nav>