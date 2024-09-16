<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location:login.php");
    exit();
}
$user = $_SESSION['username']; // set user using session to use in my query.

require('requires/db_helper.php'); // prepopulate fields
$pdo = connectDB(); // connect to db
$query = "select * from users where username = ?"; // select entire row
$stmt = $pdo->prepare($query); // generate statement
$stmt->execute([$user]);

$row = $stmt->fetch(); // get user data : assoc

// assign database information to variables to make them sticky
// they cannot be null, user is already logged in with valid session

$name = htmlentities($row['fullname']);
$email = htmlentities($row['email']);

require("requires/input_validation.php");
$errors = array(); // error array
$updates = array(); // store updates with key->new value pairs
if (isset($_POST['submit'])) {
    if ($_POST['submit'] == "profile") {  // profile info submit = username, fullname, email
        // if the field is set, set it to a variable, otherwise replace with database info.
        $user = $_POST['username'] ?? $user;
        if ($user === "") {
            $user = $_SESSION['username'];
        } // if the field is blank, replace with the session ID instead of empty.
        $name = $_POST['fullname'] ?? $name;
        if ($name === "") {
            $name = $row['fullname'];
        } // reset to database value if submitted with blank
        $email = $_POST['email'] ?? $email;
        if ($email === "") {
            $email = $row['email'];
        }

        // Password stays blank
        // compare all values to their database results to identify what changed
        $valid = false; // assume dirty input

        if ($user != $row['username']) { // username changed
            checkUsername($errors, $user); //check new username

            $query = "select 1 from users where username = ?";  // test if email exists in database   
            $stmt = $pdo->prepare($query);
            $stmt->execute([$user]);

            if ($stmt->fetch()) { // row returned, non unique information given
                $errors['account'] = "This username is already in use."; // non unique username
            } else {
                $updates['username'] = $user; // no rows returned, unique username
            }
        }
        if ($name != $row['fullname']) { // fullname changed
            checkName($errors, $name); // check new fullname, no need to validate with database
            $updates['fullname'] = $name;
        }
        if ($email != $row['email']) { // email changed

            checkEmail($errors, $email); // check and parse new email

            $query = "select 1 from users where email = ?";  // test if email exists in database   
            $stmt = $pdo->prepare($query);
            $stmt->execute([$email]);

            if ($stmt->fetch()) { // row returned, non unique information given
                $errors['account'] = "This email is already in use."; // non unique email
            } else {
                $updates['email'] = $email; // unique email
            }
        } //--  IF INVALID INPUT IN SANITIZATION PROCESS, OR IF THE VALUES ARE NOT UNIQUE -- 
        if (!count($errors)) {
            $valid = true;
        }
        if ($valid) { // Valid for update
            updateUser($_SESSION['id'], $updates, $pdo); // update user
            $_SESSION['username'] = $user;  // update session username value if changed
            header("Location:index.php"); // redirect to main
            exit(0);
            // redirect somewhere else?
        }
    }
    if ($_POST['submit'] == "password") { // password change submit
        $pwd = $_POST['pwd'] ?? null;
        $verify = $_POST['verify_password'] ?? null;

        checkPassword($errors, $pwd); // check password for errors
        if ($pwd != $verify) {
            $errors['password'] = "Passwords do not match!";
        } else if (!count($errors)) { // no errors
            // hash 
            $pwd = password_hash($pwd, PASSWORD_DEFAULT);

            // update
            $query = "update users set password = ? where id = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$pwd, $_SESSION['id']]);
            session_destroy(); // destory session to make the user have to reenter the password they just created
            header("Location:login.php"); // redirect to main
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
                    <h2>Edit Your Profile!</h2>
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
                    <?php if (isset($errors['full'])) : ?>
                        <span class="error" id="short_full">
                            <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
                            <span><?= $errors['full'] ?></span>
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

                    <!-- Submit form and clear form buttons -->
                    <div id="form_buttons">
                        <button type="submit" name="submit" id="submit" value="profile">
                            Save Changes
                        </button>
                    </div>
                </form>

            </div>
            <form method="POST" novalidate>
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
            <div id="password_buttons">
                <button type="submit" name="submit" id="submit" value="password">
                    Change Password
                </button>
            </div>
            </form>
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
                <aside>
                    
                <div>
            <a href="deleteaccount.php" id="destroy">Delete account</a>
        </div>
        </aside>
            </div>
        </section>
    </main>

    <!-- Copyright footer -->
    <?php include("includes/footer.php") ?>

</html>