<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';

$inquiry_id = isset($_GET['inquiry_id']) ? (int)$_GET['inquiry_id'] : 0;

if ($inquiry_id < 1) {
    echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
    exit;
}

// 문의 조회
$query = "
    SELECT i.*, u.username 
    FROM inquiries i
    JOIN users u ON i.user_id = u.user_id
    WHERE i.inquiry_id = $inquiry_id
";
$result = mysqli_query($conn, $query);
$inquiry = mysqli_fetch_assoc($result);

if (!$inquiry) {
    echo "<script>alert('존재하지 않는 문의입니다.'); history.back();</script>";
    exit;
}

// 비밀글 접근 제한
$current_user_id = $_SESSION['user_id'] ?? null;
$is_admin = $_SESSION['is_admin'] ?? false;

if ($inquiry['is_secret']) {
    if (!$current_user_id || ($inquiry['user_id'] != $current_user_id && !$is_admin)) {
        echo "<script>alert('비밀글은 작성자 또는 관리자만 열람할 수 있습니다.'); history.back();</script>";
        exit;
    }
}

// 답변 조회
$res_query = "
    SELECT content, created_at 
    FROM inquiry_responses 
    WHERE inquiry_id = $inquiry_id 
    ORDER BY response_id ASC LIMIT 1
";
$res_result = mysqli_query($conn, $res_query);
$response = mysqli_fetch_assoc($res_result);

// 파일 조회
$file_query = "
    SELECT file_name, file_path 
    FROM inquiry_files 
    WHERE inquiry_id = $inquiry_id
";
$file_result = mysqli_query($conn, $file_query);

$files = [];
while ($file_row = mysqli_fetch_assoc($file_result)) {
    $files[] = $file_row;
}

// 뷰에서 사용하도록 전역 변수에 할당
$GLOBALS['inquiry'] = $inquiry;
$GLOBALS['response'] = $response;
$GLOBALS['files'] = $files;
