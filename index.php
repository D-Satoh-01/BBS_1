<?php

date_default_timezone_set("Asia/Tokyo");

$commentArray = array();
$errorMessages = array();
$postDate = date("Y-m-d H:i:s");

$alertEmptyNameAndComment = "<script type='text/javascript'>alert('名前とコメントを入力してください');</script>";
$alertEmptyName = "<script type='text/javascript'>alert('名前を入力してください');</script>";
$alertEmptyComment = "<script type='text/javascript'>alert('コメントを入力してください');</script>";

// DBとの接続
try {
  $pdo = new PDO('mysql:host=localhost;dbname=BBS_1', "D.Satoh", "beretta17");
} catch (PDOException $e){
  echo $e->getMessage();  // getMessageはPDOExceptionのメソッド(エラー内容を取得する)
}

// 書き込みボタンを押したときの処理
// $_POST["submitButton"]が空でない(ボタンを押した)場合trueを返す
if (!empty($_POST["submitButton"])){

  if (empty($_POST["username"]) && empty($_POST["comment"])){
    echo $alertEmptyNameAndComment;
    $errorMessages["username"] = "名前とコメント未入力";
  } else
  // 名前の入力チェック
  if (empty($_POST["username"])){
    echo $alertEmptyName;
    $errorMessages["username"] = "名前未入力";
  } else
  // コメントの入力チェック
  if (empty($_POST["comment"])){
    echo $alertEmptyComment;
    $errorMessages["comment"] = "コメント未入力";
  }

  // データの送信関連
  // errorMessagesが空(名前とコメントが入力されてる)の場合のみ以下を実行
  if (empty($errorMessages)){
    try {
      $stmt = $pdo->prepare("INSERT INTO `bbs_1_table` (`username`, `comment`, `postDate`) VALUES (:username, :comment, :postDate)");
      // bindParamで値を挿入
      $stmt->bindParam(':username', $_POST['username']);
      $stmt->bindParam(':comment', $_POST['comment']);
      $stmt->bindParam(':postDate', $postDate);
  
      // 上記処理を実行
      $stmt->execute();
    } catch (PDOException $e){
      echo $e->getMessage();  // getMessageはPDOExceptionのメソッド(エラー内容を取得する)
    }
  }
}

// DBから投稿データを取得
$sql = "SELECT * FROM `bbs_1_table`";
$commentArray = $pdo->query($sql);

// DBとの接続を閉じる
$pdo = null;

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BBS_1</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <h1 class="title">ネット掲示板（PHPにて作成）</h1>
  <hr>
  <div class="boardWrapper">
    <section>
      <?php foreach($commentArray as $comment): ?>
        <article>
          <div class="wrapper">
            <div class="nameArea">
              <span>名前：</span>
              <p class="username"><?php echo $comment["username"]; ?></p>
              <time>:<?php echo $comment["postDate"]; ?></time>
            </div>
            <p class="comment"><?php echo $comment["comment"]; ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </section>
    <form class="fromWrapper" method="POST">
      <div>
        <input type="submit" value="書き込み" name="submitButton">
        <label for="">名前：</label>
        <input type="text" name="username">
      </div>
      <div>
        <textarea class="commentTextArea" name="comment"></textarea>
      </div>
    </form>
  </div>
</body>
</html>