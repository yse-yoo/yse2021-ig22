<?php
/* 
【機能】
入荷で入力された個数を表示する。入荷を実行した場合は対象の書籍の在庫数に入荷数を加
えた数でデータベースの書籍の在庫数を更新する。

【エラー一覧（エラー表示：発生条件）】
なし
*/

//①セッションを開始する
session_start();

function getByid($id, $con)
{
	$sql = "SELECT * FROM books WHERE id = {$id}";
	$stmt = $con->query($sql);
	return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateByid($id, $con, $total)
{
	$sql = "UPDATE books SET stock = :stock WHERE id = :id";
	$stmt = $con->prepare($sql);
	$stmt->execute(['id' => $id, 'stock' => $total]);
}

//⑤SESSIONの「login」フラグがfalseか判定する。「login」フラグがfalseの場合はif文の中に入る。
if (empty($_SESSION['login'])){
	$_SESSION['error2'] = 'ログインしてください';
	header('Location: login.php');
	exit;
}


//⑧データベースへ接続し、接続情報を変数に保存する
//⑨データベースで使用する文字コードを「UTF8」にする
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

//⑩書籍数をカウントするための変数を宣言し、値を0で初期化する
//⑪POSTの「books」から値を取得し、変数に設定する。
$book_ids = $_POST['books'];

foreach ($book_ids as $index => $book_id) {
		/*
		 * ⑫POSTの「stock」について⑩の変数の値を使用して値を取り出す。
		 * 半角数字以外の文字が設定されていないかを「is_numeric」関数を使用して確認する。
		 * 半角数字以外の文字が入っていた場合はif文の中に入る。
		 */
		$stock = $_POST['stock'][$index];
		if (!is_numeric($stock)) {
			//⑬SESSIONの「error」に「数値以外が入力されています」と設定する。
			$_SESSION['error'] = '数値以外が入力されています';
			//⑭「include」を使用して「nyuka.php」を呼び出す。
			include 'nyuka.php';
			//⑮「exit」関数で処理を終了する。
			exit;
		}

		//⑯「getByid」関数を呼び出し、変数に戻り値を入れる。その際引数に⑪の処理で取得した値と⑧のDBの接続情報を渡す。
		$book = getByid($book_id, $pdo);

		//⑰ ⑯で取得した書籍の情報の「stock」と、⑩の変数を元にPOSTの「stock」から値を取り出し、足した値を変数に保存する。
		$total_stock = $book['stock'] + $stock;

		//⑱ ⑰の値が100を超えているか判定する。超えていた場合はif文の中に入る。
		if($total_stock > 100){
			//⑲SESSIONの「error」に「最大在庫数を超える数は入力できません」と設定する。
			$_SESSION['error'] = '最大在庫数を超える数は入力できません';
			//⑳「include」を使用して「nyuka.php」を呼び出す。
			include 'nyuka.php';
			//㉑「exit」関数で処理を終了する。
			exit;
		}

	//㉒ ⑩で宣言した変数をインクリメントで値を1増やす。
	// $index++;
}

/*
 * ㉓POSTでこの画面のボタンの「add」に値が入ってるか確認する。
 * 値が入っている場合は中身に「ok」が設定されていることを確認する。
 */
if(isset($_POST['add']) && $_POST['add'] == 'ok'){
	//㉔書籍数をカウントするための変数を宣言し、値を0で初期化する。
	//㉕POSTの「books」から値を取得し、変数に設定する。
	foreach($_POST['books'] as $index => $book_id){
		//㉖「getByid」関数を呼び出し、変数に戻り値を入れる。その際引数に㉕の処理で取得した値と⑧のDBの接続情報を渡す。
		$book = getByid($book_id, $pdo);
		//㉗ ㉖で取得した書籍の情報の「stock」と、㉔の変数を元にPOSTの「stock」から値を取り出し、足した値を変数に保存する。
		$total_stock = $book['stock'] + $_POST['stock'][$index];
		//㉘「updateByid」関数を呼び出す。その際に引数に㉕の処理で取得した値と⑧のDBの接続情報と㉗で計算した値を渡す。
		updateByid($book_id, $pdo, $total_stock);
		//㉙ ㉔で宣言した変数をインクリメントで値を1増やす。
	}

	//㉚SESSIONの「success」に「入荷が完了しました」と設定する。
	$_SESSION['success'] = '入荷が完了しました';
	//㉛「header」関数を使用して在庫一覧画面へ遷移する。
	header('Location: zaiko_ichiran.php');
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<title>入荷確認</title>
	<link rel="stylesheet" href="css/ichiran.css" type="text/css" />
</head>

<body>
	<div id="header">
		<h1>入荷確認</h1>
	</div>
	<form action="nyuka_kakunin.php" method="post" id="test">
		<div id="pagebody">
			<div id="center">
				<table>
					<thead>
						<tr>
							<th id="book_name">書籍名</th>
							<th id="stock">在庫数</th>
							<th id="stock">入荷数</th>
						</tr>
					</thead>
					<tbody>
						<?php
						//㉜書籍数をカウントするための変数を宣言し、値を0で初期化する。
						//㉝POSTの「books」から値を取得し、変数に設定する。
						foreach ($book_ids as $index => $book_id) {
							//㉞「getByid」関数を呼び出し、変数に戻り値を入れる。その際引数に㉜の処理で取得した値と⑧のDBの接続情報を渡す。
							$book = getById($book_id, $pdo);
						?>
							<tr>
								<td><?= $book['title'] ?></td>
								<td><?= $book['stock'] ?></td>
								<td><?= $stock = $_POST['stock'][$index] ?></td>
							</tr>
							<input type="hidden" name="books[]" value="<?= $book_id ?>">
							<input type="hidden" name="stock[]" value='<?= $stock ?>'>
						<?php
							//㊴ ㉜で宣言した変数をインクリメントで値を1増やす。
						}
						?>
					</tbody>
				</table>
				<div id="kakunin">
					<p>
						上記の書籍を入荷します。<br>
						よろしいですか？
					</p>
					<button type="submit" id="message" formmethod="POST" name="add" value="ok">はい</button>
					<button type="submit" id="message" formaction="nyuka.php">いいえ</button>
				</div>
			</div>
		</div>
	</form>
	<div id="footer">
		<footer>株式会社アクロイト</footer>
	</div>
</body>

</html>