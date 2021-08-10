<?php

session_start();

$tns = "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521)))
(CONNECT_DATA=(SERVICE_NAME=XE)))";
$dsn = "oci:dbname=".$tns.";charset=utf8";
$username = 'c##moon';
$password = 'dayeon';
$cno = $_SESSION['cno'];
$search = $_GET['searchFlag'] ?? '';
$nullVar = null;

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
    <title>BOOK LIST</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="../../img/CNU_logo.jpg" alt="" class="logoImg">
            <h1 class="text-start">E-BOOK LIBRARY</h1>
            <h5 class="pageTitle">search e-book</h5>
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
        <form class="row" action="adminEbook.php" method="get">
            <div class="col-auto">
                <select name="searchCategory" id="searchCategory">
                    <option value="TITLE">제목</option>
                    <option value="AUTHOR">저자</option>
                    <option value="PUBLISHER">출판사</option>
                    <option value="YEAR">발행연도</option>
                </select>
            </div>
            <div class="col-9">
                <input type="text" class="form-control" id="searchWord" name="searchWord" placeholder="검색어 입력">
            </div>
            <div class="col-auto text-end">
                <button type="submit" class="btn btn-primary mb-3">검색</button>
            </div>
            <input type="hidden" name="searchFlag" value="true">
        </form>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>ISBN</th>
                    <th>Title</th>
                    <th>Publisher</th>
                    <th>Author</th>
                    <th>Year</th>
                    <th>State</th>
                    <th>대출 회원</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($search != ''){ 
                    $searcCategory = $_GET['searchCategory'];
                    $searchWord = strtolower($_GET['searchWord']); // 대소문자 구분 없애도록
                    echo "카테고리 ".$searcCategory." ";
                    echo $searchWord."에 대한 검색 결과";
                    $stmt = $conn -> prepare("SELECT E.ISBN ISBN, E.TITLE, E.PUBLISHER, E.YEAR, E.CNO, E.DATE_RENTED, E.DATE_DUE, A.AUTHOR, SYSDATE,
                    (SELECT COUNT(*) FROM EBOOK WHERE CNO = :cno) CNT, 
                    (SELECT COUNT(*) FROM RESERVE WHERE CNO = :cno) CNT_RESERVE 
                FROM EBOOK E, AUTHORS A
                WHERE E.ISBN = A.ISBN AND LOWER(".$searcCategory.") LIKE '%".$searchWord."%'");
                }else{
                    $stmt = $conn -> prepare("SELECT E.ISBN ISBN, E.TITLE, E.PUBLISHER, E.YEAR, E.CNO, E.DATE_RENTED, E.DATE_DUE, A.AUTHOR, SYSDATE,
                    (SELECT COUNT(*) FROM EBOOK WHERE CNO = :cno) CNT, 
                    (SELECT COUNT(*) FROM RESERVE WHERE CNO = :cno) CNT_RESERVE 
                FROM EBOOK E, AUTHORS A
                WHERE E.ISBN = A.ISBN");
                }
                $stmt -> execute(array($cno));
            while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                if($row['DATE_DUE'] < $row['SYSDATE'] and $row['DATE_DUE'] != $nullVar){
                ?><form action="process.php?mode=<?=$mode?>?isbn=<?=$ISBN?>" method="get" id="autoReturn">
                    <input type="hidden" name="ISBN" value="<?=$row['ISBN']?>">
                    <input type="hidden" name="date_rented" value=<?=$row['DATE_RENTED']?>>
                    <input type="hidden" name="mode" value="return">
                    <script>
                    autoReturn.submit();
                    </script>
                </form>
                <?php
                }
                ?>
                <tr>
                    <td><?= $row['ISBN']?></td>
                    <td><?= $row['TITLE']?></td>
                    <td><?= $row['PUBLISHER']?></td>
                    <td><?=$row['AUTHOR']?></td>
                    <td><?= $row['YEAR']?></td>
                    <?php
                    if ($row['CNO'] > 0){ // 대출 중인 고객이 있으면
                        if($row['CNO'] == $cno){ // 내가 대출 중인 도서라면 예약 불가
                            ?>
                    <td class="isRented">대출 중</td>
                    <td><?=$row['CNO']?></td>
                    <?php
                        }else{ // 다른 사람이 이미 대출했다면 예약
                            if($row['CNT'] >= 3){ // 근데 이미 세 권 이상 대출했으면 예약 불가
                        ?>
                    <td class="isRented">대출 중</td>
                    <td><?=$row['CNO']?></td>
                    <?php
                            }else{ // 아니면 예약 가능
                        ?>
                    <td class="isRented">대출 중</td>
                    <td><?=$row['CNO']?></td>
                    <?php
                        }
                    }
                }else{ // 놀고있으면 대출가능
                        if($row['CNT'] >= 3){ // 근데 이미 세 권 이상 대출했으면 예약 불가
                            ?>
                    <td class="enableRent">대출 가능</td>
                    <td></td>
                    <?php
                        }
                        else{
                        ?>
                    <td class="enableRent">대출 가능</td>
                    <td></td>
                    <?php 
                    }
                }
                    ?>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</body>

</html>