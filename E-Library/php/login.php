<?php

session_start();

$tns = "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521)))
(CONNECT_DATA=(SERVICE_NAME=XE)))";
$dsn = "oci:dbname=".$tns.";charset=utf8";
$username = 'c##moon';
$password = 'dayeon';
$cno = $_POST['id'] ?? '';
$pwd = $_POST['pwd'] ?? '';
$chkPwd = '';
try{
    $conn = new PDO($dsn, $username, $password);
} catch(PDOException $e){
    echo("error contents : ".$e -> getMessage());
}
if($cno != ''){

    $stmt = $conn->prepare("SELECT PW FROM CUSTOMER WHERE CNO = :cno");
    $stmt->execute(array($cno));

    if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $chkPwd = $row['PW'];
    }

    if($pwd == $chkPwd){ // 로그인 성공 시 도서 검색 화면으로
        $_SESSION['cno'] = $cno; // 로그인한 계정 유지를 위해 세션 이용
        if($cno == 999){
            header("Location: ./admin/admin.php");
        }else{
            header("Location: searchEbook.php?cno=$cno");
        }
    }
}


?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
    <link rel="stylesheet" href="../../css/style.css">
    <title>e-book login</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="../../img/CNU_logo.jpg" alt="" class="logoImg">
            <h1 class="text-start">E-BOOK LIBRARY</h1>
            <h5 class="pageTitle">e-book Login</h5>
        </div>
        <form action="login.php" method="post">
            <div class="inputDiv">
                <input type="text" class="loginInput" id="login" name="id" placeholder="ID">
                <input type="password" class="loginInput" id="login" name="pwd" placeholder="PWD">
            </div>
            <button type="submit" class="btn login-btn">Login</button>
        </form>
        <?php
        ?>
    </div>
</body>

</html>