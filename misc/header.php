<nav>
    <ul>
        <li><a href="/Project/index.php">Home</a></li>
        <li>Browse</li>
        <li>About Us</li>
    </ul>

    <form action="#" method="post">
        <input type="text" id="bookSearch" name="search" placeholder="Search for books">
    </form>

    <ul>
        <?php if(!isset($_SESSION['username'])): ?>
            <li><a href="/Project/auth/login.php">Login</a></li>
            <li><a href="/Project/auth/register.php">Register</a></li>
        <?php else: ?>
            <?php if($_SESSION['role_id'] == 3): ?>
                <li><a href="/Project/Users/">Users</a></li>
            <?php endif ?>
            <li><a href="/Project/auth/logout.php">Logout</a></li>
        <?php endif ?>
    </ul>
</nav>