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
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th colspan="2">회원정보 수정</th>
                </tr>
            </thead>
            <tbody>
                <?php
            $stmt = $conn -> prepare("SELECT * FROM CUSTOMER WHERE CNO = :cno");
            $stmt -> execute(array($cno));
            if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                ?>
                <form action="process.php?mode=<?=$mode?>" method="get">
                    <tr>
                        <td>Name</td>
                        <td><?= $row['NAME']?></td>
                    </tr>
                    <tr>
                        <td>CNO</td>
                        <td><?= $row['CNO']?></td>
                    </tr>
                    <tr>
                        <td>E-mail</td>
                        <td><input type="text" name="newemail" id="newemail" placeholder=<?=$row['EMAIL']?>></td>
                    </tr>
                    <tr>
                        <td>현재 PW</td>
                        <td><input type="text" name="current_pw" id="current_pw"></td>
                    </tr>
                    <tr>
                        <td>new PW</td>
                        <td><input type="text" name="newpw" id="newpw"></td>
                    </tr>
                    <tr>
                        <td>check new PW</td>
                        <td><input type="text" name="newpw_chk" id="newpw_chk"></td>
                    </tr>
                    <?php
            }
            ?>
            </tbody>
        </table>
        <input type="hidden" name="mode" value="modify">
        <input type="hidden" name="email" value=<?=$row['EMAIL']?>>
        <input type="hidden" name="pw" value=<?=$row['PW']?>>
        <button type="submit" class="btn btn_on_info">정보수정</button>
        </form>
    </div>
</body>

</html>