<?php
include_once __DIR__ . '/../includes/db_connection.php';

// POST 방식으로 수신
$name = trim($_POST['username'] ?? '');
$id = trim($_POST['real_id'] ?? '');
$password = $_POST['password'] ?? '';
$passwordConfirm = $_POST['passwordConfirm'] ?? '';
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$terms = isset($_POST['terms']) ? 1 : 0;
$marketing = isset($_POST['marketing']) ? 1 : 0;

// 필수 입력 확인
if (!$name || !$id || !$password || !$passwordConfirm || !$email || !$phone) {
    echo "<script>alert('모든 항목을 입력해주세요.'); history.back();</script>";
    exit;
}

// 비밀번호 확인
if ($password !== $passwordConfirm) {
    echo "<script>alert('비밀번호가 일치하지 않습니다.'); history.back();</script>";
    exit;
}

// 이용약관 동의 확인
if ($terms !== 1) {
    echo "<script>alert('이용약관 및 개인정보처리방침에 동의하셔야 합니다.'); history.back();</script>";
    exit;
}

// 아이디 중복 검사
$check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE real_id = ?");
$check_stmt->bind_param("s", $id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$row = $check_result->fetch_assoc();

if ($row['count'] > 0) {
    echo "<script>alert('이미 사용 중인 아이디입니다.'); history.back();</script>";
    exit;
}

// 비밀번호 해시화
$hashed_pw = password_hash($password, PASSWORD_DEFAULT);

// 회원 등록
$insert_stmt = $conn->prepare("INSERT INTO users (username, real_id, password, email, phone, terms, marketing, point)
                               VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
$insert_stmt->bind_param("ssssssi", $name, $id, $hashed_pw, $email, $phone, $terms, $marketing);

if ($insert_stmt->execute()) {
    echo "<script>alert('회원가입 성공! {$name}님 환영합니다.'); location.href = '../index.php';</script>";
} else {
    echo "<script>alert('회원가입 실패: 서버 오류'); history.back();</script>";
}
?>
