<?php
function insert($data, $pdo)
{
	var_dump($data);
	$sql = "INSERT INTO books (title, author, isbn, salesDate, price, stock)
			VALUES (:title, :author, :isbn, :salesDate, :price, :stock);";
	$stmt = $pdo->prepare($sql);
	return $stmt->execute($data);
}

function check($posts)
{
    if (empty($posts)) return;
    foreach ($posts as $column => $post) {
        $posts[$column] = htmlspecialchars($post, ENT_QUOTES);
    }
    return $posts;
}

function validate($data)
{
    $errors = [];
    if (empty($data['title'])) $errors['title'] = '書籍名を入力してください。';
    if (empty($data['author'])) $errors['author'] = '著者名を入力してください。';
    if (empty($data['isbn'])) $errors['isbn'] = 'ISBNを入力してください。';
    if (empty($data['salesDate'])) $errors['salesDate'] = '発売日を入力してください。';
    if (empty($data['price'])) $errors['price'] = '価格を入力してください。';
    if (!is_numeric($data['price'])) $errors['price'] = '価格が正しくありません。';
    if ($data['stock'] < 0) $errors['stock'] = '在庫数を入力してください。';
    return $errors;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

session_start();

if (empty($_SESSION['login'])) {
	$_SESSION['error2'] = 'ログインしてください';
	header('Location: login.php');
	exit;
}

//TODO
$db_name = 'zaiko2021_yse';
$db_host = 'localhost';
$db_port = '3306';
$db_user = 'zaiko2021_yse';
$db_password = '2021zaiko';

$dsn = "mysql:dbname={$db_name};host={$db_host};charset=utf8;port={$db_port}";
try {
	$pdo = new PDO($dsn, $db_user, $db_password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
	echo "接続失敗: " . $e->getMessage();
	exit;
}

$posts = check($_POST);
$errors = validate($posts);

$_SESSION['posts'] = $posts;
$_SESSION['errors'] = $errors;

if ($errors) {
	header('Location: new_product.php');
} else {
	insert($posts, $pdo);
	unset($_SESSION['posts']);
	header('Location: zaiko_ichiran.php');
}

?>