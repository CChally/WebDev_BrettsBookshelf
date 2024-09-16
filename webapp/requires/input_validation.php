<?php
// validate username and assign errors
function checkUsername(&$errors, $user)
{
    $user = htmlentities($user);
    $user = trim($user);

    // conditions : isset, not zero length.
    if (!isset($user) || strlen($user) <= 3) {
        $errors['username'] = "Incorrect username formatting. Must be at least 4 characters!";
    }
    // less than 20 chars
    if (strlen($user) >= 20) {
        $errors['username'] = "Username length outside of range! (Between 5-20 characters).";
    }
    // whitespace check
    if (!(strpos($user, ' ') === false)) {
        $errors['username'] = "Username cannot contain whitespace!";
    }
}
// validate email and assign errors
function checkEmail(&$errors, $email)
{
    $email = htmlentities($email);
    $email = trim($email);

    // conditions : isset, not zero length.
    if (!isset($email) || strlen($email) === 0) {
        $errors['email'] = "Please enter a valid email.";
        return; // return at this point. If the name is not set, the string wont contain a whitespace and the 
        // error will be overwritten.
    }
    // whitespace check
    if (!(strpos($email, ' ') === false)) {
        $errors['email'] = "Email cannot contain whitespace!";
    }
    if (strpos($email, '@') === false) {
        $errors['email'] = "Invalid email format! Missing @";
    }
    if (strpos($email, '.') === false) {
        $errors['email'] = "Incorrect email formatting!";
    }
}

// validate password and assign errors
function checkPassword(&$errors, $pwd)
{
    $pwd = htmlentities($pwd);
    $pwd = trim($pwd);

    // conditions : isset, not zero length.
    if (!isset($pwd) || strlen($pwd) === 0) {
        $errors['password'] = "Must have a value!";
        return;
    }
    // test for at least 8 chars in length
    if (strlen($pwd) <= 7) {
        $errors['password'] = "Password is too short!";
        return;
    }

    // test for password requirements
    // Regex pattern -> CHATGPT generated
    /*
    /^ - start of the string
    (?=.*[A-Z]) - positive lookahead to ensure there is at least one uppercase letter in the string
    (?=.*[a-z]+) - positive lookahead to ensure there is one or more lowercase letters in the string
    (?=.*\d) - positive lookahead to ensure there is at least one digit in the string
    .+ - matches any character one or more times
    $/ - end of the string*/

    // I tried to use the regex pattern I used straight from the HTML pattern attribute that I came up with originally,
    // however I didnt want to dive deep into the preg documentation.
    if (!(preg_match('/^(?=.*[A-Z])(?=.*[a-z]+)(?=.*\d).+$/', $pwd))) {
        $errors['password'] = "Password does not meet the requirements!";
    }
}
// validate name and assign errors
function checkName(&$errors, $name)
{
    $name = htmlentities($name);
    $name = trim($name);

    // conditions : isset, not zero length.
    if (!isset($name) || strlen($name) === 0) {
        $errors['fullname'] = "Please enter a name!";
        return; // return at this point. If the name is not set, the string wont contain a whitespace and the 
        // error will be overwritten.
    }
    // whitespace check -> needs a space!
    if (strpos($name, ' ') === false) {
        $errors['fullname'] = "Enter full name! First and last.";
    }
}
function checkBookTitle(&$errors, $title)
{
    $title = htmlentities($title);
    $title = trim($title);

    if (!isset($title) || strlen($title) === 0) {
        $errors['title'] = "The book requires a title!";
    }
}
function checkAuthor(&$errors, $author)
{
    $author = htmlentities($author);
    $author = trim($author);

    if (!isset($author) || strlen($author) === 0) {
        $errors['author'] = "Must have an author!";
    }
}
function checkISBN(&$errors, $isbn)
{
    $isbn = htmlentities($isbn);
    $isbn = trim($isbn);

    if (!isset($isbn) || strlen($isbn) === 0) {
        $errors['isbn'] = "Must have an ISBN!";
    }
}
function checkGenre(&$errors, $genre)
{
    $genre = htmlentities($genre);
    $genre = trim($genre);

    if ($genre === "0") { // default value
        $errors['genre'] = "Must select a valid genre!";
    }
}
function checkDesc(&$errors, $desc)
{
    $desc = htmlentities($desc);
    $desc = trim($desc);
}
function checkDates(&$errors, $date)
{
    $date = htmlentities($date);
    $date = trim($date);
    if (!isset($date) || strlen($date) === 0) {
        $errors['date'] = "Must choose a valid date!";
    }
}
function checkFormat(&$errors, $format)
{
    $format = htmlentities($format);
    $format = trim($format);

    if (!isset($format) || strlen($format) === 0) {
        $errors['format'] = "Must choose a valid book format!";
    }
}
function createFilename($file, $path, $prefix, $uniqueID)
{
    $filename = $_FILES[$file]['name'];
    $exts = explode(".", $filename);
    $ext = $exts[count($exts) - 1];
    $filename = $prefix . $uniqueID . "." . $ext;
    $newname = $path . $filename;
    return $newname;
}
function checkFile($file, $limit)
{
    //modified from http://www.php.net/manual/en/features.file-upload.php
    try {
        // Undefined | Multiple Files | $_FILES Corruption Attack
        // If this request falls under any of them, treat it invalid.
        if (!isset($_FILES[$file]['error']) || is_array($_FILES[$file]['error'])) {
            throw new RuntimeException('Invalid parameters.');
        }

        // Check Error value.
        switch ($_FILES[$file]['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded filesize limit.');
            default:
                throw new RuntimeException('Unknown errors.');
        }

        // You should also check filesize here.
        if ($_FILES[$file]['size'] > $limit) {
            throw new RuntimeException('Exceeded filesize limit.');
        }

        // Check the File type
        if (
            exif_imagetype($_FILES[$file]['tmp_name']) != IMAGETYPE_GIF
            and exif_imagetype($_FILES[$file]['tmp_name']) != IMAGETYPE_JPEG
            and exif_imagetype($_FILES[$file]['tmp_name']) != IMAGETYPE_PNG
        ) {

            throw new RuntimeException('Invalid file format.');
        }

        return "";
    } catch (RuntimeException $e) {

        return $e->getMessage();
    }
}

function upload_book_file(&$errors, &$pdo)
{
    //use database autonumber for unique value?
    $path = WEBROOT . "www_data/book_files/"; //location file should go
    $fileroot = "book"; //base filename

    if (is_uploaded_file($_FILES['book_file']['tmp_name'])) {

        $results = checkFile('book_file', 102400);
        if (strlen($results) > 0) {
            $errors['book_file'] = $results;
        } else {
            $query = "insert into files values (NULL)"; // iterate autonumber field (generate new unique number)
            $stmt = $pdo->query($query);
            $uniqueID = $pdo->lastInsertId(); // get autonumber field for unique value

            $query = "delete from files"; // delete created entry so files table doesnt get clogged up with records.
            $stmt = $pdo->query($query);

            $newname = createFilename('book_file', $path, $fileroot, $uniqueID);
            if (!move_uploaded_file($_FILES['book_file']['tmp_name'], $newname)) {
                $errors['book_file'] = "Error moving file.";
            }
            $arr = explode('/', $newname);
            $filename = $arr[count($arr) - 1]; // return only the filename
            return $filename;
        }
    } else {
        $results = checkFile('book_file', 102400);
        $errors['book_file'] = $results;
    }
}

function upload_book_cover(&$errors, &$pdo)
{
    //use database autonumber for unique value?
    $path = WEBROOT . "www_data/covers/"; //location file should go
    $fileroot = "cover"; //base filename

    if (is_uploaded_file($_FILES['book_cover_file']['tmp_name'])) {

        $results = checkFile('book_cover_file', 102400);
        if (strlen($results) > 0) {
            $errors['book_cover'] = $results;
        } else {
            $query = "insert into files values (NULL)"; // iterate autonumber field (generate new unique number)
            $stmt = $pdo->query($query);
            $uniqueID = $pdo->lastInsertId(); // get autonumber field for unique value

            $query = "delete from files"; // delete created entry so files table doesnt get clogged up with records.
            $stmt = $pdo->query($query);

            $newname = createFilename('book_cover_file', $path, $fileroot, $uniqueID);
            if (!move_uploaded_file($_FILES['book_cover_file']['tmp_name'], $newname)) {
                $errors['book_cover'] = "Error moving cover file.";
            }
            $arr = explode('/', $newname);
            $filename = $arr[count($arr) - 1]; // return only the filename
            return $filename;
        }
    } else {
        $results = checkFile('book_cover_file', 102400);
        $errors['book_cover'] = $results;
    }
}
