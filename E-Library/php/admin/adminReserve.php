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
    <title>e-book 예약 통계</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="../../img/CNU_logo.jpg" alt="" class="logoImg">
            <h1 class="text-start">E-BOOK LIBRARY</h1>
            <h5 class="pageTitle">e-book ADMIN Reserve</h5>
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
                        <th><button class="btn category_btn" onclick="location.href='adminReserve.php'">예약 관리</button>
                        </th>
                        <th><button class="btn category_btn" onclick="location.href='adminUser.php'">회원
                                정보 관리</button></th>
                        <th><button class="btn category_btn" onclick="location.href='../login.php'">Logout</button></th>
                    </tr>
                </thead>
            </table>
        </div>
        <p></p>
        <p>예약 대기자 목록</p>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>ISBN</th>
                    <th>TITLE</th>
                    <th>회원</th>
                    <th>예약 일자</th>
                    <th>우선순위</th>
                    <th>STATE</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php // 텀프로젝트 대출 기록 조건 1) 조인/표준조인 사용 :: TP5 - 1
            $stmt = $conn -> prepare("SELECT P.ISBN ISBN, P.DATE_TIME DATE_TIME, E.TITLE TITLE, P.CNO CNO, C.NAME, C.EMAIL, E.CNO FLAG_RENTED, P.PRIORITY PRIORITY 
            FROM (SELECT ISBN, CNO, DATE_TIME, ROW_NUMBER() OVER(PARTITION BY ISBN ORDER BY DATE_TIME DESC) PRIORITY FROM RESERVE) P JOIN EBOOK E
            ON P.ISBN = E.ISBN JOIN CUSTOMER C
            ON P.CNO = C.CNO 
            ORDER BY E.ISBN, PRIORITY");

            $stmt -> execute();
            while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                ?>
                <tr>
                    <td><?= $row['ISBN']?></td>
                    <td><?= $row['TITLE']?></td>
                    <td><?= $row['CNO']." | ".$row['NAME']?></td>
                    <td><?= $row['DATE_TIME']?>
                    <td><?= $row['PRIORITY']?>
                        <?php
                    if($row['FLAG_RENTED'] > 0){ // 현재 대출자가 반납을 완료하면 대출 신청 가능
                    ?>
                    <td><?=$row['EMAIL']?></td>
                    <td><button>이메일 발송</button></td>
                    <?php
                    }else{
                    ?>
                    <td>대기</td>
                    <?php
                    }
                    ?>
                </tr>
                <?php
            }
            ?>

            </tbody>
        </table>
        <!-- <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>ISBN</th>
                    <th>TITLE</th>
                    <th>CNO</th>
                    <th>이름</th>
                    <th>이메일</th>
                    <th>예약일자</th>
                </tr>
            </thead>
            <tbody>
                <?php
           $stmt = $conn -> prepare("SELECT E.ISBN, E.TITLE, R.CNO, C.NAME, C.EMAIL, R.DATE_TIME 
            FROM EBOOK E JOIN RESERVE R ON E.ISBN = R.ISBN JOIN PREVIOUS_RENTAL PREV ON E.ISBN = PREV.ISBN JOIN CUSTOMER C ON R.CNO = C.CNO
            WHERE PREV.DATE_RETURNED <= SYSDATE ORDER BY R.DATE_TIME");

            $stmt -> execute();
            while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                ?>
                <tr>
                    <td><?= $row['ISBN']?></td>
                    <td><?= $row['TITLE']?></td>
                    <td><?= $row['CNO']?></td>
                    <td><?= $row['NAME']?>
                    <td><?= $row['EMAIL']?>
                    <td><?= $row['DATE_TIME']?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table> -->
    </div>
</body>

</html>