<?php
session_start();
if (!isset($_SESSION['id'])) {
  header("Location:login.php");
  exit();
}

$title = $_GET['title_search'] ?? null;
$errors = array();

if (isset($_GET['submit'])) {  // if submitted
  require("requires/input_validation.php");
  checkBookTitle($errors, $title);

  if (!count($errors)) {

    require("requires/db_helper.php");
    $pdo = connectDB();
    $query = "select booknum,title,author,cover from books where title like ? and uid like ?"; // confirm owner of book
    $stmt = $pdo->prepare($query);
    $search = "%" . $title . "%"; // wilcard
    $stmt->execute([$search, $_SESSION['id']]);
  }
  else{
    $stmt = array(); // to make sure $stmt is defined in the instance where no value is submitted for title.
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include("includes/metadata.php") ?>
  <title>My Archive - Search</title>
</head>

<body>
  <!-- Header Banner -->
  <?php include("includes/header.php") ?>
  <!-- Form Display -->
  <main id="content">
    <section id="form">
      <header id="form_header">
        <div id="search_icon">
          <i class="fa-solid fa-magnifying-glass main_icon" title="Search for a book">Magnifying glass</i>
        </div>
        <h2>What will you discover?</h2>
      </header>
      <hr />

      <!-- Book search form -->
      <form method="GET">
        <div>
          <label for="title_search"></label>
          <input type="text" id="title_search" name="title_search" placeholder="Title" value="<?= $title ?>" />
        </div>
        <?php if (isset($errors['title'])) : ?>
          <span class="error" id="short_username">
            <i class="fa-solid fa-triangle-exclamation">A warning/error icon</i>
            <span><?= $errors['title'] ?></span>
          </span>
        <?php endif ?>
        <button type="submit" name="submit" id="submit">Search</button>
      </form>
    </section>

    <!-- Book Results Display -->
    <section id="display_results">
      <div class="container_buttons">
        <!-- Next button, populates the view with a new grid of books of specified dimensons, (If books displayed per page = 3, then a new 3 books)
          should come from the database and rendered -->
        <button type="button" name="next_books" id="next_books">
          <i class="fa-solid fa-arrow-right"></i>
        </button>
      </div>
      <h2>Search Results:</h2>
      <div class="book_container">

        <?php if (isset($_GET['title_search'])) : ?>
          <?php foreach ($stmt as $row) : ?>
            <!-- Book 1 Display -->
            <div class="book_display">
              <div class="book_details">
                <p><?= htmlentities($row['title']) ?></p>
                <p><?= htmlentities($row['author']) ?></p>
              </div>
              <img src="/~brettchallice/www_data/covers/<?= $row['cover'] ?>" width="155" height="225" alt="<?= $row['title'] ?>">
              <!-- Book 1 Controls -->
              <span class="book_controls">
                <a href="details.php?id=<?= $row['booknum'] ?>"><i class="fa-solid fa-circle-info" title="Show details">Book
                    information</i></a>
                <a href="edit.php?id=<?= ($row['booknum']) ?>"><i class="fa-solid fa-pen-to-square">Pen drawing a square</i></a>
                <a href="delete.php?id=<?= ($row['booknum']) ?>"><i class="fa-solid fa-delete-left">Deletion icon</i></a>
              </span>
            </div>
          <?php endforeach ?>
        <?php endif ?>



      </div>
    </section>
  </main>
  <!-- Copyright Footer -->
  <?php include("includes/footer.php") ?>

</html>