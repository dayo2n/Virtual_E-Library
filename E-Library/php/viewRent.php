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
    <title>e-book 대출내역</title>
</head>


<body>

    <div class="container">
        <div class="header">
            <img src="../img/CNU_logo.jpg" alt="" class="logoImg" onclick="">
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
        <p></p>
        <p>현재 대출 중인 도서 목록</p>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>ISBN</th>
                    <th>Title</th>
                    <th>DATE RENTED</th>
                    <th>DATE DUE</th>
                    <th>EXT TIMES</th>
                    <th>RETURN</th>
                </tr>
            </thead>
            <tbody>
                <?php
            $stmt = $conn -> prepare("SELECT ISBN, TITLE, EXT_TIMES, DATE_RENTED, DATE_DUE FROM EBOOK WHERE CNO = :cno");
            $stmt -> execute(array($cno));
            while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                ?>
                <tr>
                    <td><?= $row['ISBN']?></td>
                    <td><?= $row['TITLE']?></td>
                    <td><?= $row['DATE_RENTED']?></td>
                    <td><?= $row['DATE_DUE']?></td>
                    <td><?= $row['EXT_TIMES']?>
                        <?php
                    if ($row['EXT_TIMES'] >= 2){ // 이미 두 번 연장시 연장 불가
                        ?>
                        <button type="button" disabled>연장가능횟수초과</button>
                    </td>
                    <?php
                    }else{
                        ?>
                    <form action="process.php?mode=<?=$mode?>?isbn=<?=$ISBN?>" method="get">
                        <input type="hidden" name="ISBN" value="<?=$row['ISBN']?>">
                        <input type="hidden" name="ext_times" value=<?=$row['EXT_TIMES']?>>
                        <input type="hidden" name="date_due" value=<?=$row['DATE_DUE']?>>
                        <input type="hidden" name="mode" value="extend">
                        <button type="submit" class="btn btn_on_rent">연장하기</button></td>
                    </form>
                    <?php 
                    }
                    ?>
                    <form action="process.php?mode=<?=$mode?>?isbn=<?=$ISBN?>" method="get">
                        <input type="hidden" name="ISBN" value="<?=$row['ISBN']?>">
                        <input type="hidden" name="date_rented" value=<?=$row['DATE_RENTED']?>>
                        <input type="hidden" name="mode" value="return">
                        <td><button type="submit" class="btn btn_on_rent">반납하기</button></td>
                    </form>
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
                    <th>대출일자</th>
                    <th>반납일자</th>
                </tr>
            </thead>
            <tbody>
                <?php
            $stmt = $conn -> prepare("SELECT P.ISBN, E.TITLE, P.DATE_RENTED, P.DATE_RETURNED FROM EBOOK E, PREVIOUS_RENTAL P WHERE E.ISBN = P.ISBN AND P.CNO = :cno");
            $stmt -> execute(array($cno));
            while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                ?>
                <tr>
                    <td><?= $row['ISBN']?></td>
                    <td><?= $row['TITLE']?></td>
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