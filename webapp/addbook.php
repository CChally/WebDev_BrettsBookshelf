<?php
$errors = array();  // array to store errors

if (isset($_POST['submit'])) {  // if submitted
  exit(0);
}
//https://loki.trentu.ca/~brettchallice/3420/assignments/assn3/addbook.php
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include("includes/metadata.php") ?>
  <title>Add to your collection!</title>
</head>

<body>
  <?php include("includes/header.php") ?>
  <nav id="sitelinks">
    <ul>
      <li>
        <a href="logout.php"><i class="fa-solid fa-door-open"></i>Logout</a>
      </li>
      <li>
        <a href="editprofile.php"><i class="fa-solid fa-user-pen"></i>Edit Profile</a>
      </li>
      <li>
        <a href="search.php"><i class="fa-solid fa-magnifying-glass">Magnifying glass</i>Book
          Search</a>
      </li>
    </ul>
  </nav>
  </header>

  <main id="content">
    <!-- Form section -->
    <section id="book_form">
      <!-- Forms section header -->
      <header id="add_book">
        <div class="icon" id="add_book_icon">
          <i class="fa-regular fa-square-plus main_icon" title="Add books here"></i>
        </div>
        <h2>Add to your collection!</h2>
      </header>
      <!--  Form Content -->
      <form method="POST" id="add_book_form" enctype="multipart/form-data" novalidate>
        <div class="input_container">
          <!-- Back to index button -->
          <a href="index.php" id="back_to_home"><i class="fa-solid fa-arrow-left"></i></a>
          <!-- Book Title Field -->
          <div>
            <label for="book_title"></label>
            <input type="text" name="book_title" id="book_title" placeholder="Title" />
          </div>

          <!-- Book Author Field -->
          <div>
            <label for="book_author"></label>
            <input type="text" id="book_author" name="book_author" placeholder="Author" />
          </div>
          <!-- ISBN Field -->
          <div>
            <label for="isbn"></label>
            <input type="text" name="isbn" id="isbn" placeholder="ISBN" />
          </div>
          <!-- Description Text Area field -->
          <div>
            <label for="book_description"></label>
            <textarea name="book_description" id="book_description" cols="30" rows="10" placeholder="Description"></textarea>
          </div>
          <!-- Book rating field -->
          <div>
            <label for="star_rating">Rating:</label>
            <input type="range" name="star_rating" id="star_rating" min="0" max="10" step="1" />
          </div>

          <!-- Book Genre Dropdown List Field -->
          <div>
            <label for="genre"></label>
            <select name="genre" id="genre">
              <option value="0">Genre</option>
              <option value="mystery">Mystery</option>
              <option value="true_crime">True Crime</option>
              <option value="science_fiction">Science Fiction</option>
              <option value="journal">Journal</option>
            </select>
          </div>

          <!-- Publication Date Field -->
          <div>
            <label for="publication_date">Publication Date:</label>
            <input type="date" name="publication_date" id="publication_date" />
          </div>

          <!-- Book Format Radio Fieldset -->
          <fieldset id="format">
            <legend>Book Format</legend>
            <!-- Hardcover format radio option -->
            <div>
              <label for="hardcover">Hardcover</label>
              <input type="radio" name="book_format" id="hardcover" value="hardcover" />
            </div>

            <!--  Paperback format radio option-->
            <div>
              <label for="paperback">Paperback</label>
              <input type="radio" name="book_format" id="paperback" value="paperback" />
            </div>

            <!-- EPub format radio option -->
            <div>
              <label for="epub">Epub</label>
              <input type="radio" id="epub" name="book_format" value="epub" />
            </div>
            <!-- Mobi format radio option -->
            <div>
              <label for="mobi">Mobi</label>
              <input type="radio" id="mobi" name="book_format" value="mobi" />
            </div>
            <!-- PDF format radio option -->
            <div>
              <label for="pdf">PDF</label>
              <input type="radio" id="pdf" name="book_format" value="pdf" />
            </div>
          </fieldset>
          <div id="form_buttons">
            <button type="button" name="auto_complete" id="auto_complete">
              Auto-Complete
            </button>
            <button type="reset" name="reset" id="reset">Clear</button>
          </div>
          <button type="submit" name="submit" id="submit">Add Book</button>
        </div>

        <div class="file_container">
          <!-- URL to book cover image -->
          <div>
            <label for="book_cover_url">URL to Book Cover:</label>
            <input type="url" name="book_cover_url" id="book_cover_url" />
          </div>
          <!-- Book Cover Upload Field -->
          <div class="file_upload">
            <label for="book_cover_file">Browse for Book Cover:</label>
            <input type="file" name="book_cover_file" id="book_cover_file" />
          </div>
          <!-- eBook File Upload Field -->
          <div class="file_upload">
            <label for="book_file">Browse for eBook File:</label>
            <input type="file" name="book_file" id="book_file" />
          </div>
        </div>
      </form>
    </section>
  </main>
  <?php include("includes/footer.php") ?>

</html>