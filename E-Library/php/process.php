<?php
session_start();

$tns = "(DESCRIPTION=
(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521)))
(CONNECT_DATA=(SERVICE_NAME=XE)))";
$dsn = "oci:dbname=".$tns.";charset=utf8";
$username = 'c##moon';
$password = 'dayeon';

$cno = $_SESSION['cno'];
$nullVar = null;

echo $cno;
try{
    $conn = new PDO($dsn, $username, $password);
} catch(PDOException $e){
    echo("error contents : ".$e -> getMessage());
}

switch($_GET['mode']){
    case 'rent':
        $ISBN = $_GET['ISBN'];
        $stmt = $conn->prepare("UPDATE EBOOK SET CNO = :cno, EXT_TIMES = 0, DATE_RENTED = TO_DATE(SYSDATE, 'yyyy mm dd'), DATE_DUE = TO_DATE(SYSDATE+10, 'yyyy mm dd') where ISBN = :ISBN");
        $stmt->bindParam(':ISBN',$ISBN);
        $stmt->bindParam(':cno',$cno);
        $stmt -> execute();
        header("Location: searchEbook.php?cno=$cno");
        break;
    case 'reserve':
        $ISBN = $_GET['ISBN'];
        $stmt = $conn->prepare("INSERT INTO RESERVE(ISBN, CNO, DATE_TIME) VALUES (:ISBN, :cno, TO_DATE(SYSDATE, 'yyyy mm dd'))");
        $stmt->bindParam(':ISBN',$ISBN);
        $stmt->bindParam(':cno',$cno);
        $stmt -> execute();
        header("Location: searchEbook.php?cno=$cno");
        break;
    case 'return': // 반납
        $ISBN = $_GET['ISBN'];
        $date_rented = $_GET['date_rented'];
        // 도서 정보에 현재의 대출내역 삭제
        $stmt = $conn->prepare("UPDATE EBOOK SET CNO = :nullVar, EXT_TIMES = 0, DATE_RENTED = :nullVar, DATE_DUE = :nullVar where ISBN =:ISBN");
        $stmt->bindParam(':ISBN', $ISBN);
        $stmt->bindParam(':nullVar', $nullVar);
        $stmt->execute();
        // 지난 대출 기록(previous_Rental) 업데이트
        $stmt = $conn->prepare("INSERT INTO PREVIOUS_RENTAL(ISBN, DATE_RENTED, DATE_RETURNED, CNO) VALUES (:ISBN, :date_rented, TO_DATE(SYSDATE, 'yyyy mm dd'), :cno)");
        $stmt->bindParam(':ISBN', $ISBN);
        $stmt->bindParam(':cno',$cno);
        $stmt->bindParam(':date_rented',$date_rented);
        $stmt->execute();
        header("Location: searchEbook.php");
        break;
    case 'extend': // 대출 연장
        // 예약자가 있는지 먼저 확인
        $ISBN = $_GET['ISBN'];
        $stmt = $conn->prepare("SELECT ISBN, COUNT(*) CNT FROM RESERVE GROUP BY ISBN HAVING ISBN = :ISBN");
        $stmt->execute(array($ISBN));   
        if($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
            if($row['CNT'] > 0){
                echo "<script>alert('예약자가 있어 대출 연장이 불가합니다.');</script>";
                header("Location: searchEbook.php");
                break;
            }
        }
        $ext_times = $_GET['ext_times'];
        $date_due = $_GET['date_due'];
        $stmt = $conn->prepare("UPDATE EBOOK SET EXT_TIMES=:ext_times+1, DATE_DUE = TO_DATE(:date_due, 'YYYY-MM-DD HH24:MI:SS') + INTERVAL '10' DAY WHERE ISBN =:ISBN");
        $stmt->bindParam(':ISBN', $ISBN);
        $stmt->bindParam(':ext_times', $ext_times);
        $stmt->bindParam(':date_due', $date_due);
        $stmt->execute();
        header("Location: viewRent.php");
        break;
    case 'delete': // 예약 취소
        $ISBN = $_GET['ISBN'];
        $stmt = $conn->prepare("DELETE FROM RESERVE WHERE ISBN = :ISBN AND CNO = :cno");
        $stmt->bindParam(':ISBN', $ISBN);
        $stmt->bindParam(':cno', $cno);
        $stmt->execute();
        header("Location: viewReserve.php");
        break;
    case 'modify': // 회원 정보 수정
        $newemail = $_GET['newemail'];
        $current_pw = $_GET['current_pw']; // 사용자가 확인용으로 입력한 비밀번호
        $newpw = $_GET['newpw']; // 새 비밀번호
        $newpw_chk = $_GET['newpw_chk']; // 새 비밀번호 확인용
        $pw = $_GET['pw']; // 현재 비밀번호
        $email = $_GET['email'];

        if(($newpw != $newpw_chk) or ($current_pw != $pw)){
            echo "<script>alert('비밀번호를 확인하세요');</script>";
            header("Location: userInfo.php");
            break;
        }

        // 새 이메일을 입력하지 않았으면 기존 이메일에서 변경하지 않는 것으로 간주
        if($newemail == ''){
            $newemail = $email;
        }
        $stmt = $conn->prepare("UPDATE CUSTOMER SET EMAIL = :newemail, PW = :newpw WHERE CNO = :cno");
        $stmt->bindParam(':newemail', $newemail);
        $stmt->bindParam(':cno', $cno);
        $stmt->bindParam(':newpw', $newpw);
        $stmt->execute();
        header("Location: userInfo.php");
        break;
}
?>