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
  // user owns the book -> no issues
} else { // no rows returned -> book does not exist
  header("Location:index.php");
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <?php include("includes/metadata.php"); ?>
  <title> <?= htmlentities($row['title']) ?>- Details</title>
</head>
<!-- Purpose: Display details for a particuar book, nicely formatted.  -->

<body>
  <?php include("includes/header.php") ?>
  <!-- Main Content -->
  <main id="content">
    <!-- Book Display Section -->
    <section id="details">
      <!-- Book Cover Figure -->
      <figure id="book_cover">
        <h2><?= htmlentities($row['title']) ?></h2>
        <img src="/~brettchallice/www_data/covers/<?= $row['cover'] ?>" alt="<?= $row['description'] ?>" width="400" height="600" />
      </figure>

      <div class="book_details">
        <!-- Book Details Section -->
        <h2>Book Information</h2>

        <!-- Title DB Output -->
        <div id="title">
          <p>Title: <?= htmlentities($row['title']) ?></p>
        </div>
        <!-- Author DB Output -->
        <div id="author">
          <p>Author: <?= htmlentities($row['author']) ?></p>
        </div>


        <!-- Rating DB Output -->
        <div id="rating">
          <p>Rating: <?php for ($i = 0; $i <= $row['rating']; $i++) : ?> <i class="fa-solid fa-star" id="stars"></i>

        <?php endfor ?>
        </p>
        </div>
        <!-- Genre DB Output -->
        <div id="genre">
          <p>Genre: <?=(str_contains(htmlentities($row['genre']),'_') ? str_replace("_"," ",htmlentities($row['genre'])): htmlentities($row['genre'])) ?>    </p>
        </div>
        <!-- Publication Date DB Output -->
        <div id="publication_date">
          <p>Publication Date: <?= htmlentities($row['publication']) ?></p>
        </div>
        <!-- ISBN DB Output -->
        <div id="isbn">
          <p>ISBN: <?= htmlentities($row['isbn']) ?></p>
        </div>
        <!-- Description DB Output -->
        <div id="description">
          <p> Description:
            <?= htmlentities($row['description']) ?>
          </p>
        </div>
        <!-- File Format DB Output -->
        <div id="format">
          <p>Save File Format: <?= htmlentities($row['format']) ?></p>
        </div>
      </div>
      <!-- Book Control -->
      <div class="book_controls">
        <a class="edit" href="edit.php?id=<?= $book_id ?>"><i class="fa-solid fa-pen-to-square" style="font-size: 3rem">Pen drawing a square</i></a>
      </div>
    </section>
  </main>

  <!-- Copyright Footer -->
  <?php include("includes/footer.php") ?>

</html>