<?php

session_start();

$ISBN = $_GET['ISBN'] ?? '';
$mode = $_GET['mode'] ?? '';
$bookName = '';
$publisher = '';
$tns = "
(DESCRIPTION=
(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521)))
(CONNECT_DATA=(SERVICE_NAME=XE)))";
$url = "oci:dbname=".$tns.";charset-uft8";
$username = 'c##moon';
$password = 'dayeon';
try{
    $conn = new PDO($url, $username, $password);
}catch(PDOException $e){
    echo("error contents : ". $e->getMessage());
}
$stmt = $conn->prepare("SELECT TITLE, PUBLISHER FROM EBOOK WHERE ISBN = :ISBN");
$stmt->execute(array($ISBN));

if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $bookName = $row['TITLE'];
    $publisher = $row['PUBLISHER'];
}

?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
    <title>e-book 대출/예약</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="../img/CNU_logo.jpg" alt="" class="logoImg">
            <h1 class="text-start">E-BOOK LIBRARY</h1>
            <h5 class="pageTitle">e-book <?=$mode == 'rent'?'책 대여' : '책 예약'?></h5>
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
        <form class="row g-3 needs-validation" method="get" action="process.php?mode=<?=$mode?>?isbn=<?=$ISBN?>"
            novalidate>
            <div class="">
                <label for="bookName" class="form-label">제목</label>
                <p class="selectedBook"><?=$bookName?></p>
            </div>
            <div class="">
                <label for="publisher" class="form-label">출판사</label>
                <p class="selectedBook"><?=$publisher?></p>
            </div>
            <div class="mb-3">
                <input type="hidden" name="mode" value="<?=$mode?>">
                <input type="hidden" name="ISBN" value="<?=$ISBN?>">
                <button class="btn btn-primary" type="submit"
                    onclink="alert('success')"><?=$mode == 'rent' ? 'RENT' : 'RESERVE'?></button>
            </div>
        </form>
    </div>
</body>
<script src="main.js"></script>

</html>