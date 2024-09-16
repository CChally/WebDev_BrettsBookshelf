<?php
// Get the acutal document and webroot path for virtual directories
$direx = explode('/', getcwd());
define('DOCROOT', "/$direx[1]/$direx[2]/"); // /home/username/
define('WEBROOT', "/$direx[1]/$direx[2]/$direx[3]/"); //home/username/public_html

/*############################################################
Function for connecting to the database
##############################################################*/

function connectDB()
{
    // Load configuration as an array.
    $config = parse_ini_file(DOCROOT . "pwd/config.ini");
    $dsn = "mysql:host=$config[domain];dbname=$config[dbname];charset=utf8mb4";

    try {
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int) $e->getCode());
    }

    return $pdo;
}
// INSERT USER
function insertUser(&$user, &$name, &$email, &$pwd, &$pdo)
{

    // hash users password and insert into database 

    $hash = password_hash($pwd, PASSWORD_DEFAULT);   // hash the password using bcrypt and generated salt.

    $query = "insert into users (id, username,email,password,fullname) values (NULL,?,?,?,?)";
    $stmt = $pdo->prepare($query)->execute([$user, $email, $hash, $name]); // insert into database

}
//UPDATE USER
function updateUser($id, &$updates, &$pdo)
{
    if (isset($updates['fullname'])) {
        $query = "update users set fullname = ? where id = ?";
        $stmt = $pdo->prepare($query)->execute([$updates['fullname'], $id]);
    }
    if (isset($updates['username'])) {
        $query = "update users set username = ? where id = ?";
        $stmt = $pdo->prepare($query)->execute([$updates['username'], $id]);
    }
    if (isset($updates['email'])) {
        $query = "update users set email = ? where id = ?";
        $stmt = $pdo->prepare($query)->execute([$updates['email'], $id]);
    }
}
