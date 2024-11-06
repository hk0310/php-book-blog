<header class=" text-bg-dark">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-lg-start">
            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-start mb-md-0 flex-fill">
                <li><a href="<?= BASE ?>" class="nav-link px-2">Home</a></li>
                <li><a href="<?= BASE ?>/books" class="nav-link px-2">Books</a></li>
                <li><a href="<?= BASE ?>/genres" class="nav-link px-2">Genres</a></li>
            </ul>

            <form action="#" method="post" class="search-form d-flex col-12 align-items-center col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search">
                <input type="text" id="bookSearch" class="form-control" name="search" placeholder="Search for books" aria-label="Search">
            </form>

            <?php if(!isset($_SESSION['username'])): ?>
                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                    <li><a href="<?= BASE ?>/auth/login.php" class="nav-link px-2">Log In</a></li>
                    <li><a href="<?= BASE ?>/auth/register.php" class="nav-link px-2">Sign Up</a></li>
                </ul>
            <?php else: ?>
                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                    <?php if($_SESSION['role_id'] == 3): ?>
                        <li><a href="<?= BASE ?>/Users/" class="nav-link px-2">Users</a></li>
                    <?php endif ?>
                    <li><a href="<?= BASE ?>/auth/logout.php" class="nav-link px-2">Logout</a></li>
                </ul>
            <?php endif ?>

        </div>
    </div>
</header>