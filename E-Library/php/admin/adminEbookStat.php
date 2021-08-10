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
    <link rel="stylesheet" href="../../css/style.css">
    <title>e-book ADMIN</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="../../img/CNU_logo.jpg" alt="" class="logoImg">
            <h1 class="text-start">E-BOOK LIBRARY</h1>
            <h5 class="pageTitle">e-book ADMIN</h5>
        </div>
        <div class="category">
            <table>
                <thead>
                    <tr>
                        <th><button class="btn category_btn" onclick="location.href='adminEbook.php'">도서
                                관리</button></th>
                        <th><button class="btn category_btn" onclick="location.href='adminEbookStat.php'">도서
                                통계</button></th>
                        <th><button class="btn category_btn" onclick="location.href='adminRent.php'">대출
                                통계</button></th>
                        <th><button class="btn category_btn" onclick="location.href='adminRanking.php'">독서 랭킹</button>
                        </th>
                        <th><button class="btn category_btn" onclick="location.href='adminReserve.php'">예약 통계</button>
                        </th>
                        <th><button class="btn category_btn" onclick="location.href='adminUser.php'">회원
                                정보 관리</button></th>
                        <th><button class="btn category_btn" onclick="location.href='../login.php'">Logout</button></th>
                    </tr>
                </thead>
            </table>
        </div>
        <p>
            출판사별, 저자별 도서 통계
        </p>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>출판사</th>
                    <th>저자</th>
                    <th>권 수</th>
                </tr>
            </thead>
            <tbody>
                <?php // 대출 기록에 대한 조건2) 그룹합수 :: TP5 - 2
            $stmt = $conn -> prepare("SELECT NVL(PUBLISHER, ' ') PUB, NVL(AUTHOR, ' ') AUTH, COUNT(*) CNT 
            FROM EBOOK JOIN AUTHORS ON EBOOK.ISBN = AUTHORS.ISBN GROUP BY ROLLUP(PUBLISHER, AUTHOR) 
            ORDER BY 3 DESC");

            $stmt -> execute();
            while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                ?>
                <tr>
                    <td><?= $row['PUB']?></td>
                    <td><?= $row['AUTH']?></td>
                    <td><?= $row['CNT']?></td>
                </tr>
                <?php
            }
            ?>

            </tbody>
        </table>
    </div>
</body>

</html>