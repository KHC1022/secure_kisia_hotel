<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

// ✅ CSRF 토큰 검증
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    die("<script>alert('잘못된 접근입니다.'); history.back();</script>");
}

// ✅ 사용자 인증 여부 확인
if (!isset($_SESSION['user_id'])) {
    die("<script>alert('로그인이 필요합니다.'); location.href='../user/login.php';</script>");
}

$user_id = $_SESSION['user_id'];
$category = $_POST['category'] ?? '';
$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';
$is_secret = isset($_POST['is_secret']) ? 1 : 0;

// 필수 값 검사
if (!$category || !$title || !$content) {
    echo "<script>alert('모든 항목을 입력해주세요.'); history.back();</script>";
    exit;
}

// ✅ 파일 유효성 검사 (확장자 및 MIME)
$upload_dir = __DIR__ . '/../uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$files_to_upload = [];
$allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt', 'doc', 'docx'];
$allowed_mime = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'text/plain', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

if (!empty($_FILES['files']['name'][0])) {
    foreach ($_FILES['files']['name'] as $i => $original_name) {
        $tmp_name = $_FILES['files']['tmp_name'][$i];
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        $mime = mime_content_type($tmp_name);

        if (!in_array($ext, $allowed_ext) || !in_array($mime, $allowed_mime)) {
            echo "<script>alert('허용되지 않은 파일 형식입니다.'); history.back();</script>";
            exit;
        }

        $safe_name = uniqid() . '_' . basename($original_name);
        $files_to_upload[] = [
            'original_name' => $original_name,
            'safe_name' => $safe_name,
            'tmp_name' => $tmp_name,
            'target_path' => $upload_dir . $safe_name,
            'relative_path' => 'uploads/' . $safe_name
        ];
    }
}

// ✅ 글 등록
$stmt = $conn->prepare("INSERT INTO inquiries (user_id, category, title, content, is_secret) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("isssi", $user_id, $category, $title, $content, $is_secret);
$stmt->execute();
$inquiry_id = $stmt->insert_id;
$stmt->close();

// ✅ 파일 저장 및 DB 등록
foreach ($files_to_upload as $file) {
    if (move_uploaded_file($file['tmp_name'], $file['target_path'])) {
        $file_stmt = $conn->prepare("INSERT INTO inquiry_files (inquiry_id, file_name, file_path) VALUES (?, ?, ?)");
        $file_stmt->bind_param("iss", $inquiry_id, $file['original_name'], $file['relative_path']);
        $file_stmt->execute();
        $file_stmt->close();
    }
}

echo "<script>alert('등록 완료'); location.href='../inquiry/inquiry.php';</script>";
exit;
