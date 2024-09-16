<?php
//Delete all data associated with the logged in user (movies and user data). Destroy the session, then redirect to login.

session_start(); // grab existing session / create new session
if (!isset($_SESSION['id'])) { // check if session is set (if session has logged in)
  header("Location:login.php");
  exit();
}
// session exists

require_once "requires/db_helper.php";
$pdo = connectDB();
//delete all books associated with user

$query = "delete from books where uid = ?";
$stmt= $pdo->prepare($query);
$stmt->execute([$_SESSION['id']]);

$query = "delete from users where id = ?"; // uid is foriegn key in books for user id 
$stmt = $pdo->prepare($query);
$stmt->execute([$_SESSION['id']]); // use session id from existing session


session_destroy(); // destroy session
header("Location:login.php"); // redirect
?>