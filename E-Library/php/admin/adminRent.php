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
    <title>e-book 대여 관리</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="../../img/CNU_logo.jpg" alt="" class="logoImg">
            <h1 class="text-start">E-BOOK LIBRARY</h1>
            <h5 class="pageTitle">e-book ADMIN Rent</h5>
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
        <p></p>
        <p>현재 대출 중인 도서 목록</p>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>ISBN</th>
                    <th>Title</th>
                    <th>CNO</th>
                    <th>DATE RENTED</th>
                    <th>DATE DUE</th>
                    <th>EXT TIMES</th>
                </tr>
            </thead>
            <tbody>
                <?php
            $stmt = $conn -> prepare("SELECT ISBN, TITLE, EXT_TIMES, DATE_RENTED, DATE_DUE, CNO FROM EBOOK WHERE CNO IS NOT NULL");
            $stmt -> execute();
            while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                ?>
                <tr>
                    <td><?= $row['ISBN']?></td>
                    <td><?= $row['TITLE']?></td>
                    <td><?= $row['CNO']?></td>
                    <td><?= $row['DATE_RENTED']?></td>
                    <td><?= $row['DATE_DUE']?></td>
                    <td><?= $row['EXT_TIMES']?>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <p></p>
        <p>지난 대출 내역</p>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>ISBN</th>
                    <th>Title</th>
                    <th>대출 회원</th>
                    <th>대출일자</th>
                    <th>반납일자</th>
                </tr>
            </thead>
            <tbody>
                <?php
            $stmt = $conn -> prepare("SELECT P.CNO, P.ISBN, E.TITLE, P.DATE_RENTED, P.DATE_RETURNED FROM EBOOK E, PREVIOUS_RENTAL P WHERE E.ISBN = P.ISBN");
            $stmt -> execute();
            while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                ?>
                <tr>
                    <td><?= $row['ISBN']?></td>
                    <td><?= $row['TITLE']?></td>
                    <td><?= $row['CNO']?></td>
                    <td><?= $row['DATE_RENTED']?></td>
                    <td><?= $row['DATE_RETURNED']?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</body>

</html>