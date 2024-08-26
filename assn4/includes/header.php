<?php
$curr_path = $_SERVER['PHP_SELF'];  // path to parse in string form
$args = explode('/', $curr_path);  // convert to array
$page = $args[count($args) - 1];  // taking just the page path ex. login.php, index.php

if (isset($_SESSION['id'])) {    // if active session from login
    $logged_in = true;
} else {
    $logged_in = false;
}
?>

<header id="banner">
    <img src="img/assets/logo.png" alt="Book logo icon" width="100" height="100" />
    <h1>Bretty's Bookshelf</h1>

    <!-- Conditional Navigation -->
    <?php if ($logged_in) : ?>
        <nav id="sitelinks">
            <ul>
                <li>
                    <a href="index.php" class="<?= ($page == "index.php") ? "selected" : "" ?>"><i class="fa-solid fa-house-chimney"></i>Home</a>
                </li>
                <li>
                    <a href="search.php" class="<?= ($page == "search.php") ? "selected" : "" ?>"><i class="fa-solid fa-magnifying-glass" title="Search for a book"></i>Book Search</a>
                </li>
                <li>
                    <a href="logout.php" class="<?= ($page == "logout.php") ? "selected" : "" ?>"><i class="fa-solid fa-door-open" title="Log out"></i>Logout</a>
                </li>
                <li>
                    <a href="editprofile.php" class="<?= ($page == "editprofile.php") ? "selected" : "" ?>"><i class="fa-solid fa-user-pen" title="Edit profile"></i>Edit Profile</a>
                </li>
            </ul>
        </nav>
    <?php else : ?>
        <nav id="sitelinks">
            <ul>
                <li>
                    <a href="login.php" class="<?= ($page == "login.php") ? "selected" : "" ?>"><i class="fa-solid fa-door-open" title="Log in"></i>Login</a>
                </li>
                <li>
                    <a href="register.php" class="<?= ($page == "register.php") ? "selected" : "" ?>"><i class="fa-solid fa-user-plus" title="Create a new account"></i>
                        Create Account</a>
                </li>
                <li>
                    <a href="forgot.php" class="<?= ($page == "forgot.php") ? "selected" : "" ?>">
                        <i class="fa-solid fa-question"></i> Forgotten Credentials
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif ?>
</header>