<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location:login.php");
    exit();
}

$book_id = $_GET["id"] ?? -1; // get magic url id passed to display the contents dynamically depending on the bookID passed

if ($book_id == "-1") { // check for -1
    header("Location: index.php");
}

require('requires/db_helper.php');
$pdo = connectDB();
$query = "select * from books where booknum = ?"; // get the book with the id passed through GET
$stmt = $pdo->prepare($query);
$stmt->execute([$book_id]);

if ($row = $stmt->fetch()) { // row returned
    // see if user owns book they are trying to access
    if ($row['uid'] != $_SESSION['id']) { // if user DOES NOT own the book they are trying to access
        header("Location:index.php");
    }
    require_once("requires/input_validation.php");
    $errors = array();  // array to store errors
    // variables to make form sticky -> give them defaults
    $title = $_POST['book_title'] ?? htmlentities($row['title']);
    $author = $_POST['book_author'] ?? htmlentities($row['author']);
    $isbn = $_POST['isbn'] ?? htmlentities($row['isbn']);
    $desc = $_POST['book_description'] ?? htmlentities($row['description']);
    $rating = $_POST['star_rating'] ?? htmlentities($row['rating']); // no validation needed
    $genre = $_POST['genre'] ?? htmlentities($row['genre']);
    $date = $_POST['publication_date'] ?? htmlentities($row['publication']);
    $book_format = $_POST['book_format'] ?? htmlentities($row['format']);

    
    if (isset($_POST['submit'])) {  // if submitted
        checkBookTitle($errors, $title);
        checkAuthor($errors, $author);
        checkISBN($errors, $isbn);
        checkGenre($errors, $genre);
        checkDesc($errors, $desc);
        checkDates($errors, $date);
        checkFormat($errors, $book_format);
        
        if (!count($errors)) { // no input errors
            if ($_FILES['book_cover_file']['error'] === UPLOAD_ERR_OK){
                echo "You set a new cover file!";
                $cover = upload_book_cover($errors, $pdo);
            } else {
                $cover = $row['cover'];
            }
        
            if ($_FILES['book_file']['error'] === UPLOAD_ERR_OK) {
                echo "You set a new book file!";
                $book = upload_book_file($errors, $pdo);
            } else {
                $book = $row['bookfile'];
            }
        }
                if(!count($errors)){ // file uploads success
                    // update and redirect
                    
                    $query = "update books set title = ?, author = ?, isbn = ?, genre = ?,description = ?, publication = ?, rating = ?, format = ?, cover = ?, bookfile = ? where
                    booknum = ?";
                    $stmt = $pdo-> prepare($query);
                    $stmt->execute([$title, $author,$isbn,$genre,$desc,$date,$rating, $book_format,$cover,$book,$book_id]);
                    header("Location:details.php?id=".$book_id);
                }
        }
    }
 else { // no rows returned -> book does not exist
    header("Location:index.php");
}
// ------------------------------------------------------------------------------------------------
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("includes/metadata.php") ?>
    <title>Edit your collection!</title>
</head>

<body>
    <?php include("includes/header.php") ?>

    <main id="content">
        <!-- Form section -->
        <section id="book_form">
            <!-- Forms section header -->
            <header id="add_book">
                <div class="icon" id="add_book_icon">
                    <i class="fa-regular fa-square-plus main_icon" title="Add books here"></i>
                </div>
                <h2>Edit your collection!</h2>
            </header>
            <!--  Form Content -->
            <form method="POST" id="add_book_form" enctype="multipart/form-data" novalidate>
                <div class="input_container">
                    <!-- Back to index button -->
                    <a href="index.php" id="back_to_home"><i class="fa-solid fa-arrow-left"></i></a>
                    <!-- Book Title Field -->
                    <div>
                        <label for="book_title"></label>
                        <input type="text" name="book_title" id="book_title" placeholder="Title" value="<?= $title ?>" />
                    </div>
                    <?php if (isset($errors['title'])) : ?>
                        <span class="error">
                            <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
                            <span><?= $errors['title'] ?></span>
                        </span>
                    <?php endif ?>

                    <!-- Book Author Field -->
                    <div>
                        <label for="book_author"></label>
                        <input type="text" id="book_author" name="book_author" placeholder="Author" value="<?= $author ?>" />
                    </div>
                    <?php if (isset($errors['author'])) : ?>
                        <span class="error">
                            <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
                            <span><?= $errors['author'] ?></span>
                        </span>
                    <?php endif ?>
                    <!-- ISBN Field -->
                    <div>
                        <label for="isbn"></label>
                        <input type="text" name="isbn" id="isbn" placeholder="ISBN" value="<?= $isbn ?>" />
                    </div>
                    <?php if (isset($errors['isbn'])) : ?>
                        <span class="error">
                            <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
                            <span><?= $errors['isbn'] ?></span>
                        </span>
                    <?php endif ?>
                    <!-- Description Text Area field -->
                    <div>
                        <label for="book_description"></label>
                        <textarea name="book_description" id="book_description" cols="30" rows="10" placeholder="Description (Optional)"><?= $desc ?></textarea>
                    </div>
                    <!-- Book rating field -->
                    <div>
                        <label for="star_rating">Rating:</label>
                        <input type="range" name="star_rating" id="star_rating" min="0" max="10" step="1" value="<?= $rating ?>" />
                    </div>

                    <!-- Book Genre Dropdown List Field -->
                    <div>
                        <label for="genre"></label>
                        <select name="genre" id="genre">
                            <option value="0" <?= ($genre == null) ? 'selected' : '' ?>>Genre</option>
                            <option value="Mystery" <?= ($genre === "Mystery") ? 'selected' : '' ?>>Mystery</option>
                            <option value="True_Crime" <?= ($genre === "True_Crime") ? 'selected' : '' ?>>True Crime</option>
                            <option value="Science_Fiction" <?= ($genre === "Science_Fiction") ? 'selected' : '' ?>>Science Fiction</option>
                            <option value="Comedy" <?= ($genre === "Comedy") ? 'selected' : '' ?>>Comedy</option>
                        </select>
                    </div>
                    <?php if (isset($errors['genre'])) : ?>
                        <span class="error">
                            <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
                            <span><?= $errors['genre'] ?></span>
                        </span>
                    <?php endif ?>
                    <!-- Publication Date Field -->
                    <div>
                        <label for="publication_date">Publication Date:</label>
                        <input type="date" name="publication_date" id="publication_date" value="<?= $date ?>" />
                    </div>
                    <?php if (isset($errors['date'])) : ?>
                        <span class="error">
                            <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
                            <span><?= $errors['date'] ?></span>
                        </span>
                    <?php endif ?>
                    <!-- Book Format Radio Fieldset -->
                    <fieldset id="format">
                        <legend>Book Format</legend>
                        <!-- Hardcover format radio option -->
                        <div>
                            <label for="hardcover">Hardcover</label>
                            <input type="radio" name="book_format" id="hardcover" value="hardcover" <?= ($book_format == "hardcover") ? 'checked' : '' ?> />
                        </div>

                        <!--  Paperback format radio option-->
                        <div>
                            <label for="paperback">Paperback</label>
                            <input type="radio" name="book_format" id="paperback" value="paperback" <?= ($book_format == "paperback") ? 'checked' : '' ?> />
                        </div>

                        <!-- EPub format radio option -->
                        <div>
                            <label for="epub">Epub</label>
                            <input type="radio" id="epub" name="book_format" value="epub" <?= ($book_format == "epub") ? 'checked' : '' ?> />
                        </div>
                        <!-- Mobi format radio option -->
                        <div>
                            <label for="mobi">Mobi</label>
                            <input type="radio" id="mobi" name="book_format" value="mobi" <?= ($book_format == "mobi") ? 'checked' : '' ?> />
                        </div>
                        <!-- PDF format radio option -->
                        <div>
                            <label for="pdf">PDF</label>
                            <input type="radio" id="pdf" name="book_format" value="pdf" <?= ($book_format == "pdf") ? 'checked' : '' ?> />
                        </div>
                    </fieldset>
                    <?php if (isset($errors['format'])) : ?>
                        <span class="error">
                            <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
                            <span><?= $errors['format'] ?></span>
                        </span>
                    <?php endif ?>
                    <div id="form_buttons">
                        <button type="button" name="auto_complete" id="auto_complete">
                            Auto-Complete
                        </button>
                        <button type="reset" name="reset" id="reset">Clear</button>
                    </div>
                    <button type="submit" name="submit" id="submit">Confirm Changes</button>
                </div>

                <div class="file_container">
                    <!-- URL to book cover image -->
                    <div>
                        <label for="book_cover_url">URL to Book Cover:</label>
                        <input type="url" name="book_cover_url" id="book_cover_url" />
                    </div>
                    <!-- Book Cover Upload Field -->
                    <div class="file_upload">
                        <input type="hidden" name="MAX_FILE_SIZE" value="25000" />
                        <label for="book_cover_file">Browse for Book Cover:</label>
                        <input type="file" name="book_cover_file" id="book_cover_file" />
                    </div>
                    <?php if (isset($errors['book_cover'])) : ?>
                        <span class="error">
                            <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
                            <span><?= $errors['book_cover'] ?></span>
                        </span>
                    <?php endif ?>
                    <!-- eBook File Upload Field -->
                    <div class="file_upload">
                        <label for="book_file">Browse for eBook File:</label>
                        <input type="file" name="book_file" id="book_file" />
                    </div>
                    <?php if (isset($errors['book_file'])) : ?>
                        <span class="error">
                            <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
                            <span><?= $errors['book_file'] ?></span>
                        </span>
                    <?php endif ?>
                </div>
            </form>
        </section>
    </main>
    <?php include("includes/footer.php") ?>

</html>