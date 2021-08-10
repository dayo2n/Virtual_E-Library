<?php

session_start(); 
$tns = "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521)))
(CONNECT_DATA=(SERVICE_NAME=XE)))";
$dsn = "oci:dbname=".$tns.";charset=utf8";
$username = 'c##moon';
$password = 'dayeon';
$cno = $_SESSION['cno'];
$pw = '';
try{
    $conn = new PDO($dsn, $username, $password);
} catch(PDOException $e){
    echo("error contents : ".$e -> getMessage());
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
    <link rel="stylesheet" href="../css/style.css">
    <title>e-book main</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="../img/CNU_logo.jpg" alt="" class="logoImg">
            <h1 class="text-start">E-BOOK LIBRARY</h1>
            <h5 class="pageTitle">e-book MAIN</h5>
        </div>
        <div class="category">
            <table>
                <thead>
                    <tr>
                        <th><button class="btn category_btn" onclick="location.href='searchEbook.php'">도서
                                대출</button></th>
                        <th><button class="btn category_btn" onclick="location.href='viewRent.php'">대출
                                내역</button></th>
                        <th><button class="btn category_btn" onclick="location.href='viewReserve.php'">예약
                                현황</button></th>
                        <th><button class="btn category_btn" onclick="location.href='userInfo.php'">회원
                                정보</button></th>
                        <th><button class="btn category_btn" onclick="location.href='login.php'">Logout</button></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</body>

</html>