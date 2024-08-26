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
$query = "select booknum,uid,cover,bookfile from books where booknum = ?"; // get the book with the id passed through GET
$stmt = $pdo->prepare($query);
$stmt->execute([$book_id]);

if ($row = $stmt->fetch()) { // row returned
  // verify uid matches session id
  if ($row['uid'] === $_SESSION['id']) {
    
    // delete from webroot www_data using unlink
    $cover_path = "/~brettchallice/www_data/covers/" . $row['cover'];
    $book_path = "/~brettchallice/www_data/book_files/" . $row['bookfile'];

    // delete book from database
    $query = "delete from books where booknum = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$row['booknum']]);



    if (file_exists($cover_path)) { // delete cover
      unlink($cover_path);
    }
    if (file_exists($book_path)) { // delete book
      unlink($book_path);
    }

    // redirect to index where the book wont be prepopulated -> user sees they actual deleted the book
    header("Location:index.php");
  } else { // user does not own book they are trying to access
    header("Location:index.php");
  }
} else { // no row returned
  header("Location:index.php");
}
