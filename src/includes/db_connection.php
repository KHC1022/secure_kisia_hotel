<?php
// DB 접속 정보
$host = 'mysql';
$user = 'kisia';
$password = 'kisia';
$database = 'kisia_hotel';


// 기본 에러 보고 중지
mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli($host, $user, $password, $database);

// 접속 실패 처리
if ($conn->connect_error) {
    error_log("DB 연결 실패: " . $conn->connect_error);
    echo "<script>alert('데이터베이스 연결 중 문제가 발생했습니다.'); history.back();</script>";
    exit;
}

// 시간대 및 문자셋 설정
if (!$conn->query("SET time_zone = '+09:00'")) {
    error_log("시간대 설정 실패: " . $conn->error);
}
if (!mysqli_set_charset($conn, "utf8mb4")) {
    error_log("문자셋 설정 실패: " . $conn->error);
}
?>
