<?php
session_start();

if (isset($_SESSION['error'])) unset($_SESSION['error']);
if (isset($_SESSION['success'])) {
	$message = $_SESSION['success'];
	unset($_SESSION['success']);
}

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

//TODO
$limit = 0;
$offset = 0;
$sql = "SELECT * FROM books";
if ($limit > 0) $sql .= " LIMIT {$limit} OFFSET {$offset}";
$stmt = $pdo->query($sql);

?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<title>書籍一覧</title>
	<link rel="stylesheet" href="css/ichiran.css" type="text/css" />
</head>

<body>
	<div id="header">
		<h1>書籍一覧</h1>
	</div>
	<form action="zaiko_ichiran.php" method="post" id="myform" name="myform">
		<div id="pagebody">
			<!-- エラーメッセージ表示 -->
			<div id="error"><?= @$message ?></div>

			<!-- 左メニュー -->
			<div id="left">
				<p id="ninsyou_ippan">
					<?php echo @$_SESSION["account_name"];?>
					<br>
					<button type="button" id="logout" onclick="location.href='logout.php'">ログアウト</button>
				</p>
				<button type="submit" id="btn1" formmethod="POST" name="decision" value="3" formaction="nyuka.php">入荷</button>

				<button type="submit" id="btn1" formmethod="POST" name="decision" value="4" formaction="syukka.php">出荷</button>

				<button type="submit" id="btn1" formmethod="POST" name="decision" value="5" formaction="new_product.php">新商品追加</button>

				<button type="submit" id="btn1" formmethod="POST" name="decision" value="6" formaction="delete_product.php">商品削除</button>
			</div>
			<!-- 中央表示 -->
			<div id="center">

				<!-- 書籍一覧の表示 -->
				<table>
					<thead>
						<tr>
							<th id="check"></th>
							<th id="id">ID</th>
							<th id="book_name">書籍名</th>
							<th id="author">著者名</th>
							<th id="salesDate">発売日</th>
							<th id="itemPrice">金額</th>
							<th id="stock">在庫数</th>
						</tr>
					</thead>
					<tbody>
						<?php while ($book = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
							<tr>
								<td><input type="checkbox" name="books[]" value="<?= $book['id'] ?>"></td>
								<td><?= $book['id'] ?></td>
								<td><?= $book['title'] ?></td>
								<td><?= $book['author'] ?></td>
								<td><?= $book['salesDate'] ?></td>
								<td><?= $book['price'] ?></td>
								<td><?= $book['stock'] ?></td>
							</tr>
						<?php endwhile ?>
					</tbody>
				</table>
			</div>
		</div>
	</form>
	<div id="footer">
		<footer>株式会社アクロイト</footer>
	</div>
</body>

</html>