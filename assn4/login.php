<?php
// On page load: if a cookie exists, pre-populate username box with the value of the cookie.
if (isset($_COOKIE['user'])) {
  $user = $_COOKIE['user'];
} else {
  $user = $_POST['username'] ?? "";
}
// ------------------------------------------------------------------------------------------------
require('requires/input_validation.php');
$errors = array();  // array to store errors

$pwd = $_POST['password'] ??  "";
$remember_me = $_POST['remember_me'] ?? null; // remember me checkbox -> optional parameter

if (isset($_POST['submit'])) {  // if submitted

  $user = $_POST['username'];
  checkUsername($errors, $user); // validate username
  checkPassword($errors, $pwd); // validate password


  if (!count($errors)) { // No input conditions validated,
    require('requires/db_helper.php');
    $pdo = connectDB(); // open db connection
    $valid = false; // assume invalid entry initially

    $query = "select id,password from users where username = ?";  // already guarenteed username to be unique in register.php
    $stmt = $pdo->prepare($query); // cant chain cause select returns results.
    $stmt->execute([$user]);
    if ($data = $stmt->fetch()) { // row returned, retrieve results.
      $hash = $data['password']; // assign hash

      if (password_verify($pwd, $hash)) // compare password input hash and db hash
        $valid = true;  // valid login
      else {
        $valid = false; // hashes dont match
        $errors['account_validation'] = "Incorrect password!";
      }
    } else {
      $errors['account_validation'] = "Username does not exist!"; // assign error message
    }

    if ($valid) { // valid login
      session_start(); // -> create a session to store username and database id
      $_SESSION['id'] = $data['id']; // autonumber db id
      $_SESSION['username'] = $user;
      //set cookie if box checked
      if (isset($_POST['remember_me']))
        setcookie("user", $user, time() + 60 * 60 * 24 * 30 * 12); // create a cookie to store username
      header("Location: index.php");
      exit(0);
    }
  }
}
//https://loki.trentu.ca/~brettchallice/3420/assignments/assn3/addbook.php
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include("includes/metadata.php") ?>
  <title>Login Page - Bretty's Bookshelf</title>
</head>

<body>
  <?php include("includes/header.php") ?>
  <main id="content">
    <!-- Login submission section -->
    <section id="login_form">
      <header>
        <!-- User Profile Icon -->
        <img src="img/page_unique/login/account.png" width="128" height="128" alt="User profile icon" />
        <h2>Login!</h2>
      </header>
      <hr>
      <h3>Existing User:</h3>
      <!-- Login Form -->
      <form method="POST" id="login" novalidate>

        <!-- Username field -->
        <?php if (isset($errors['account_validation'])) : ?>
          <span class="error" id="incorrect_username">
            <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
            <span><?= $errors['account_validation'] ?></span>
          </span>
        <?php endif ?>

        <div>
          <label for="username">Username</label>
          <input type="text" placeholder="Username" name="username" id="username" value="<?= $user ?>" />
        </div>

        <!-- Incorrect username error icon/message -->
        <?php if (isset($errors['username'])) : ?>
          <span class="error" id="incorrect_username">
            <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
            <span><?= $errors['username'] ?></span>
          </span>
        <?php endif ?>

        <!-- Password field -->
        <div>
          <label for="pwd">Password</label>
          <input type="password" placeholder="Password" name="password" id="pwd" />
        </div>

        <!-- Incorrect password error icon/message -->
        <?php if (isset($errors['password'])) : ?>
          <span class="error" id="incorrect_password">
            <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
            <span><?= $errors['password'] ?></span>
          </span>
        <?php endif ?>

        <!-- See password icon/message -->
        <span class="form_icon" id="see_password">
          <i class="fa-solid fa-eye">See password</i>
          <span>See your password!</span>
        </span>
        <!-- Remember me checkbox -->
        <div>
          <label for="remember_me">Remember Me</label>
          <input type="checkbox" id="remember_me" name="remember_me" value="Y" <?= (isset($remember_me)) ? "checked" : "" ?> />
        </div>
        <!-- Submit and clear buttons -->
        <div>
          <button type="submit" name="submit" id="submit">Log In</button>
          <button type="reset" name="reset" id="reset">Clear Form</button>
        </div>
      </form>
    </section>
  </main>
  <!-- Copyright Footer -->
  <?php include("includes/footer.php") ?>

</html>