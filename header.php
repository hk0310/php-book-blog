<nav>
    <ul>
        <li>Home</li>
        <li>Browse</li>
        <li>About Us</li>
    </ul>

    <form action="#" method="post">
        <input type="text" id="bookSearch" name="search" placeholder="Search for books">
    </form>

    <ul>
        <?php if(!isset($_SESSION['user'])): ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php else: ?>
            <?php if($_SESSION['user']['role_id'] == 3): ?>
                <li><a href="users.php">Users</a></li>
            <?php endif ?>
            <li><a href="logout.php">Logout</a></li>
        <?php endif ?>
    </ul>
</nav>