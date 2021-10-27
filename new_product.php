<?php
session_start();

if (empty($_SESSION['login'])) {
	$_SESSION['error2'] = 'ログインしてください';
	header('Location: login.php');
	exit;
}

if (isset($_SESSION['posts'])) {
	$posts = $_SESSION['posts'];
}
if (isset($_SESSION['errors'])) {
	$errors = $_SESSION['errors'];
	unset($_SESSION['errors']);
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

function getLatestID($con)
{
	$sql = "SELECT max(id) AS max_id FROM books;";
	$row = $con->query($sql)->fetch(PDO::FETCH_ASSOC);
	$id = $row['max_id'] + 1;
	return $id;
}

function getId($id, $con)
{
	$sql = "SELECT * FROM books WHERE id = {$id}";
	return $con->query($sql)->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>商品追加</title>
	<link rel="stylesheet" href="css/ichiran.css" type="text/css" />
</head>

<body>
	<div id="header">
		<h1>商品追加</h1>
	</div>

	<div id="menu">
		<nav>
			<ul>
				<li><a href="zaiko_ichiran.php?page=1">書籍一覧</a></li>
			</ul>
		</nav>
	</div>

	<form action="add_product.php" method="post">
		<div id="pagebody">
			<div id="error">
				<?php foreach ($errors as $error) : ?>
					<p><?= $error ?></p>
				<?php endforeach ?>
			</div>
			<div id="center">
				<table>
					<thead>
						<tr>
							<th id="id">ID</th>
							<th id="book_name">書籍名</th>
							<th id="author">著者名</th>
							<th id="salesDate">発売日</th>
							<th id="isbn">ISBN</th>
							<th id="itemPrice">金額(円)</th>
							<th id="stock">在庫数</th>
						</tr>
					</thead>
					<tr>
						<td><?= getLatestID($pdo) ?></td>
						<td><input type="text" name="title" value="<?= @$posts['title'] ?>"></td>
						<td><input type="text" name="author" value="<?= @$posts['author'] ?>"></td>
						<td><input type="text" name="salesDate" value="<?= @$posts['salesDate'] ?>"</td>
						<td><input type="text" name="isbn" value="<?= @$posts['isbn'] ?>"</td>
						<td><input type="text" name="price" value="<?= @$posts['price'] ?>"</td>
						<td><input type="text" name="stock" value="<?= @$posts['stock'] ?>"</td>
					</tr>
				</table>
				<button type="submit" id="kakutei">確定</button>
			</div>
		</div>
	</form>
	<div id="footer">
		<footer>株式会社アクロイト</footer>
	</div>
</body>

</html>