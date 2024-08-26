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