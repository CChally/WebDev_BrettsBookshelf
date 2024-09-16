<?php
// 
// ------------------------------------------------------------------------------------------------
require('requires/input_validation.php');
$errors = array();  // array to store errors


$user = $_POST['username'] ?? null;
$name = $_POST['fullname'] ?? null;
$email = $_POST['email'] ?? null;
$pwd = $_POST['pwd'] ?? null;
$verify_password = $_POST['verify_password'] ?? null;

if (isset($_POST['submit'])) {  // if submitted

  checkUsername($errors, $user);  // validate username

  checkName($errors, $name); // validate fullname

  checkEmail($errors, $email); // validate email

  checkPassword($errors, $pwd); // validate password

  if ($pwd != $verify_password && !isset($errors['password'])) { // verified and password do not match, given a valid password
    $errors['verify_password'] = "Passwords do not match!";
  }

  if (!count($errors)) { // no submission errors
    require('requires/db_helper.php');
    $pdo = connectDB(); // open db connection

    $query = "select * from users where username = ? or email = ? ";  // still prepare ever after validation; just to be safe
    $stmt = $pdo->prepare($query); // cant chain cause select returns results.
    $stmt->execute([$user, $email]);

    $row = $stmt->fetch(); // fetch row

    if (!$row) { // No rows returned, unique info given.
      $valid = true;
    } else {
      $valid = false;  // conflicting data -> username or email already exist -> abtract : Account already exists.
      if ($row['username'] == $user) {
        $errors['account'] = "This username is already in use.";
      } else if ($row['email'] == $email) {
        $errors['account'] = "This email is already in use.";
      } else {
        die("Something went terribly wrong...");
      }
    }

    if ($valid) { // Valid for insertion
      insertUser($user, $name, $email, $pwd, $pdo);   // hash password for db insertion
      header("Location: login.php");
      exit(0);

    }
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include("includes/metadata.php") ?>
  <title>Register Account</title>
</head>
<!-- Header Banner -->

<body>
  <?php include("includes/header.php") ?>
  <!-- Navigation links-->

  

  <!-- Main content -->
  <main id="content">
    <section id="register-form">
      <div>
        <header id="form_header">
          <img src="img/page_unique/register/add-group.png" alt="Boy and a girl with plus sign symbolizing location to create an account." width="128" height="128" />
          <h2>Join Today!</h2>
        </header>
        <hr /> <!-- Fullname too short error icon/message -->
        <?php if (isset($errors['account'])) : ?>
          <span class="error" id="short_full">
            <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
            <span><?= $errors['account'] ?></span>
          </span>
        <?php endif ?>
        <!-- Account Register Form -->
        <form method="POST" id="register" novalidate>
          <!-- Username field -->
          <!-- Pattern behavior: Any letters (caps or lower), numbers, with NO whitespace -->
          <div>
            <label for="username">Username</label>
            <input type="text" placeholder="Username" id="username" name="username" value="<?= $user ?>" />
          </div>

          <!-- Username too short icon/error message -->
          <?php if (isset($errors['username'])) : ?>
            <span class="error" id="short_username">
              <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
              <span><?= $errors['username'] ?></span>
            </span>
          <?php endif ?>

          <!-- Name field, with regex pattern validation -->
          <!-- Pattern behavior: One or more letters (caps or lower) followed by a space, and another sequence of chars. -->
          <!-- No error message -->
          <div>
            <label for="fullname">Full Name</label>
            <input type="text" placeholder="Full Name" id="fullname" name="fullname" value="<?= $name ?>" />
          </div>

          <!-- Fullname too short error icon/message -->
          <?php if (isset($errors['fullname'])) : ?>
            <span class="error" id="short_full">
              <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
              <span><?= $errors['fullname'] ?></span>
            </span>
          <?php endif ?>

          <!-- Email field -->
          <div>
            <label for="email">Email</label>
            <input type="text" placeholder="Email Address" id="email" name="email" value="<?= $email ?>" />
          </div>

          <!-- Email already in use error icon/message -->
          <?php if (isset($errors['email'])) : ?>
            <span class="error" id="email_in_use">
              <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
              <span><?= $errors['email'] ?></span>
            </span>
          <?php endif ?>

          <!-- Password Field -->
          <div>
            <label for="pwd">Password</label>
            <input type="password" placeholder="Password" id="pwd" name="pwd" maxlength="30" required />
          </div>
          <div>
            <?php if (isset($errors['password'])) : ?>
              <!-- Invalid password error icon/message -->
              <span class="error" id="invalid_password">
                <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
                <span><?= $errors['password'] ?></span>
              </span>
          </div>
        <?php endif ?>
        <div>
          <!-- See password icon/message -->
          <span class="form_icon" id="see_password">
            <i class="fa-solid fa-eye">Eye symbol, see your password if you click</i>
            <span>See your password!</span>
          </span>
        </div>
        <!-- Verify password field -->
        <div>
          <label for="verify_password">Verify Password</label>
          <input type="password" placeholder="Verify Password" id="verify_password" name="verify_password" />
        </div>

        <!-- Passwords do not match error -->
        <?php if (isset($errors['verify_password'])) : ?>
          <span class="error" id="unmatching_passwords">
            <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
            <span><?= $errors['verify_password'] ?></span>
          </span>
        <?php endif ?>

        <!-- Submit form and clear form buttons -->
        <div id="form_buttons">
          <button type="submit" name="submit" id="submit">
            Create account
          </button>
          <button type="reset" name="reset" id="reset">Clear Form</button>
        </div>
        </form>
      </div>
      </div>
      </div>
      <div>
        <!-- Password requirements aside portion of page -->
        <aside id="password_requirements">
          <h3>Password Requirements:</h3>
          <!-- Unordered list of attributes the password must possess. -->
          <ul>
            <li>Must be 8 or more characters in length.</li>
            <li>At least one upper-case character</li>
            <li>At least one lower-case character</li>
            <li>Must contain at least one numeric value (0-9)</li>
          </ul>
        </aside>
      </div>
    </section>
  </main>

  <!-- Copyright footer -->
  <?php include("includes/footer.php") ?>

</html>