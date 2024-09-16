<?php

session_start();
if (!isset($_SESSION['id'])) {
  header("Location:login.php");
  exit();
}
$id = $_SESSION['id'];
$user = $_SESSION['username'];

require_once('requires/db_helper.php');
$pdo = connectDB();
$query = "select booknum,title,author,cover from books where uid = ?"; // retrieve all books corresponding to a specific uid.
$stmt = $pdo->prepare($query);
$stmt->execute([$id]);


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include("includes/metadata.php") ?>
  <title>Your Archive - Home</title>
</head>

<body>
  <?php include("includes/header.php") ?>
  <!-- Main content -->
  <main id="content">
    <section id="index">
      <!-- Homepage section header -->
      <header>
        <span id="home_icon"><i title="Homepage" class="fa-solid fa-house-user main_icon">User in a house, symbolizing homepage</i></span>
        <h2>HOME ARCHIVE</h2>
      </header>
      <hr />
      <div class="search_options">
        <h2>Welcome, <?= $user ?>!</h2>
        <!-- New book icon, fixed size to the page itself -->
        <!-- Link to add a new book -->
        <div id="add_book_icon">
          <a href="addbook.php"><i class="fa-solid fa-address-book"></i> Add a new book</a>
        </div>
      </div>
      <div class="container_buttons">
        <!-- Next button, populates the view with a new grid of books of specified dimensons, (If books displayed per page = 3, then a new 3 books)
          should come from the database and rendered -->
        <button type="button" name="next_books" id="next_books">
          <i class="fa-solid fa-arrow-right"></i>
        </button>
      </div>
      <div class="book_container">

        <!-- Book display section -->
        <?php foreach ($stmt as $row) : ?>
          <div class="book_display">
            <div class="book_details">
              <p><?= htmlentities($row['title']) ?></p>
              <p><?= htmlentities($row['author']) ?></p>
            </div>
            <img src="/~brettchallice/www_data/covers/<?= $row['cover'] ?>" width="155" height="225" alt="<?= $row['title'] ?>">
            <span class="book_controls">
              <a href="details.php?id=<?= ($row['booknum']) ?>"><i class="fa-solid fa-circle-info">Circle with the character "I", symbolizing book
                  information</i></a>
              <a href="edit.php?id=<?= ($row['booknum']) ?>"><i class="fa-solid fa-pen-to-square">Pen drawing a square</i></a>
              <a href="delete.php?id=<?= ($row['booknum']) ?>"><i class="fa-solid fa-delete-left">Deletion icon</i></a>
            </span>
          </div>
        <?php endforeach ?>
      </div>
    </section>
  </main>
  <!-- Copyright footer -->
  <?php include("includes/footer.php") ?>

</html>