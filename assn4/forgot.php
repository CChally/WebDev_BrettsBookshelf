<?php
// 
// ------------------------------------------------------------------------------------------------
require('requires/input_validation.php');
$errors = array();  // array to store errors

$valid = false; // assume non valid entry

$email = $_POST['email'] ?? null;

if (isset($_POST['submit'])) {  // if submitted

  checkEmail($errors, $email); // validate email

  if (!count($errors)) { // no input conditions validated
    require('requires/db_helper.php');
    $pdo = connectDB();
    // verify there is a record pointed to by the email
    $query = "select 1 from users where email = ?";  // still prepare ever after validation; just to be safe
    $stmt = $pdo->prepare($query); // cant chain cause select returns results.
    $stmt->execute([$email]);

    if ($stmt->rowCount() == 0) {  // no rows returned, no record associated, prompt error
      $valid = false;
    } else if ($stmt->rowCount() > 0) { // row returned, valid record associated.
      $valid = true;
    } else { // unknown error
      exit("Statement object error");
    }
    if ($valid) {
      // success message displayed, -> email sent to verified email address.
      // Send email to email accessed in POST array


    } else {
      $errors['account'] = "No account is registered under the specified address.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include("includes/metadata.php") ?>
  <title>Account Recovery - Forgotten Credentials</title>
</head>

<body>
  <?php include("includes/header.php") ?>
  <!-- Main content -->
  <main id="content">
    <!-- Forgot password form section -->
    <section id="form">
      <header id="form_header">
        <!-- Lock/Password img -->
        <img src="img/page_unique/forgot/lock.png" alt="Computer with password lock." width="128" height="128" />
        <h2>Trouble remembering your credentials?</h2>
      </header>
      <hr />
      <section id="form_content">
        <h2>Account Recovery:</h2>
        <!-- Form element -->
        <form method="POST" id="forgot_form" novalidate>
          <div>
            <!-- Email field -->
            <label for="email">Email</label>
            <input type="text" name="email" id="email" placeholder="Email" value="<?= $email ?>" />
          </div>

          <!-- Success icon/message -->
          <?php if ($valid) : ?>
            <span class="success" id="valid_email">
              <i class="fa-solid fa-check">Check Mark</i>
              <span>A recovery email has been sent.</span>
            </span>
          <?php endif ?>

          <?php if (isset($errors['account'])) : ?>
            <!-- Invalid email icon/message -->
            <span class="error" id="invalid_email">
              <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
              <span><?= $errors['account'] ?></span>
            </span>
          <?php endif ?>

          <?php if (isset($errors['email'])) : ?>
            <!-- Short email icon/message -->
            <span class="error" id="short_email">
              <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
              <span><?= $errors['email'] ?></span>
            </span>
          <?php endif ?>

          </div>
          <!-- Submit form button -->
          <button type="submit" name="submit" id="submit">
            Request Reset
          </button>
        </form>
      </section>
    </section>
  </main>
  <!-- Copyright Footer -->
  <?php include("includes/footer.php") ?>

</html>