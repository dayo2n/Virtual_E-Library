<?php

session_start();

$tns = "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521)))
(CONNECT_DATA=(SERVICE_NAME=XE)))";
$dsn = "oci:dbname=".$tns.";charset=utf8";
$username = 'c##moon';
$password = 'dayeon';
$cno = $_SESSION['cno'];

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
    <title>e-book 예약현황</title>
</head>


<body>

    <div class="container">
        <div class="header">
            <img src="../img/CNU_logo.jpg" alt="" class="logoImg" onclick="">
            <h1 class="text-start">E-BOOK LIBRARY</h1>
            <h5 class="pageTitle">e-book Reserve</h5>
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
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>ISBN</th>
                    <th>Title</th>
                    <th>예약 일자</th>
                    <th>대기 순위</th>
                    <th>예약 취소</th>
                </tr>
            </thead>
            <tbody>
                <?php // row_number()를 이용하여 예약 대기 순위를 계산한다. 계산된 순위는 PRIORITY로 표현.
            $stmt = $conn -> prepare("SELECT P.ISBN ISBN, P.DATE_TIME DATE_TIME, E.TITLE TITLE, P.CNO CNO, P.PRIORITY PRIORITY 
            FROM (SELECT ISBN, CNO, DATE_TIME, ROW_NUMBER() OVER(PARTITION BY ISBN ORDER BY DATE_TIME DESC) PRIORITY FROM RESERVE) P, EBOOK E 
            WHERE P.ISBN = E.ISBN AND P.CNO=:cno");
            $stmt -> execute(array($cno));
            while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                ?>
                <tr>
                    <td><?= $row['ISBN']?></td>
                    <td><?= $row['TITLE']?></td>
                    <td><?= $row['DATE_TIME']?></td>
                    <td><?= $row['PRIORITY']?></td>

                    <form action="process.php?mode=<?=$mode?>?isbn=<?=$ISBN?>" method="get">
                        <input type="hidden" name="ISBN" value="<?=$row['ISBN']?>">
                        <input type="hidden" name="mode" value="delete">
                        <td><button type="submit" class="btn btn_on_reserve">취소</button></td>
                    </form>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</body>

</html>