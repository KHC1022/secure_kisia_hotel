<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

// ✅ 로그인 체크
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='../user/login.php';</script>";
    exit;
}

$file = $_GET['file'] ?? '';

// ✅ 파일 경로 필터링 (디렉토리 트래버설 방지)
$upload_dir = realpath(__DIR__ . '/../uploads') . '/';
$real_path = realpath($upload_dir . basename($file));

// ✅ 경로 유효성 검사
if (!$real_path || strpos($real_path, $upload_dir) !== 0 || !file_exists($real_path)) {
    echo "<script>alert('유효하지 않은 파일이거나 존재하지 않습니다.'); history.back();</script>";
    exit;
}

// ✅ 파일 다운로드
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
