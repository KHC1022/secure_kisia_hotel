<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../action/login_check.php';

$file = $_GET['file'] ?? '';

// 파일 경로 필터링
$upload_dir = realpath(__DIR__ . '/../uploads') . '/';
$real_path = realpath($upload_dir . basename($file));

// 경로 유효성 검사
if (!$real_path || strpos($real_path, $upload_dir) !== 0 || !file_exists($real_path)) {
    echo "<script>alert('유효하지 않은 파일이거나 존재하지 않습니다.'); history.back();</script>";
    exit;
}

// 파일 다운로드
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($real_path) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($real_path));
readfile($real_path);
exit;
?>
