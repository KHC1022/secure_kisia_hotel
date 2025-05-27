<?php
include_once __DIR__ . '/../includes/db_connection.php';

$name = $_GET['username'];
$id = $_GET['real_id'];
$password = $_GET['password'];
$passwordConfirm = $_GET['passwordConfirm'];
$email = $_GET['email'];
$phone = $_GET['phone'];
$terms = isset($_GET['terms']) ? 1 : 0;
$marketing = isset($_GET['marketing']) ? 1 : 0;

if ($name && $id && $password && $passwordConfirm && $email && $phone) {
    // 아이디 중복 체크
    $check_sql = "SELECT COUNT(*) as count FROM users WHERE real_id = '$id'";
    $result = $conn->query($check_sql);
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        echo "<script>
            alert('이미 사용 중인 아이디입니다.');
            history.back();
        </script>";
        exit;
    }

    if ($password !== $passwordConfirm) {
        echo "<script>
            alert('비밀번호 확인이 일치하지 않습니다.');
            history.back();
        </script>";
        exit;
    }

    if ($terms == 0) {
        echo "<script>
            alert('이용약관 및 개인정보 처리방침에 동의하지 않으셨습니다.');
            history.back();
        </script>";
        exit;
    }

    if (($password == $passwordConfirm) && ($terms == 1)) {
        $sql = "INSERT INTO users (username, real_id, password, email, phone, terms, marketing, point) 
                VALUES ('$name', '$id', '$password', '$email', '$phone', $terms, $marketing, 0)";
    }

    if ($conn->query($sql)) {
        echo "<script>
            alert('회원가입 성공! $name 님 환영합니다!');
            window.location.href = '../index.php';
        </script>";
    } else {
        $error = $conn->error;
        echo "<script>
            alert('회원가입 실패');
            history.back();
        </script>";
    }
}

?>