<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../action/login_check.php';

// CSRF 토큰 검증
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    echo "<script>alert('잘못된 요청입니다.'); history.back();</script>";
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$inquiry_id = isset($_POST['inquiry_id']) ? (int)$_POST['inquiry_id'] : 0;
$category = trim($_POST['category'] ?? '');
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

// 필수 값 검증
if (!$user_id || $inquiry_id < 1 || !$category || !$title || !$content) {
    echo "<script>alert('입력 값이 유효하지 않습니다.'); history.back();</script>";
    exit;
}

// 작성자 본인 확인
$check_stmt = $conn->prepare("SELECT user_id FROM inquiries WHERE inquiry_id = ?");
$check_stmt->bind_param("i", $inquiry_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$inquiry_data = $check_result->fetch_assoc();

if (!$inquiry_data || (int)$inquiry_data['user_id'] !== (int)$user_id) {
    echo "<script>alert('수정 권한이 없습니다.'); history.back();</script>";
    exit;
}

// 게시글 수정
$stmt = $conn->prepare("UPDATE inquiries SET category = ?, title = ?, content = ?, created_at = NOW() WHERE inquiry_id = ?");
$stmt->bind_param("sssi", $category, $title, $content, $inquiry_id);
$update_result = $stmt->execute();

if (!$update_result) {
    echo "<script>alert('수정 실패'); history.back();</script>";
    exit;
}

// 파일 업로드 처리
if (!empty($_FILES['files']['name'][0])) {
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
    $allowed_mime = ['image/jpeg', 'image/png', 'application/pdf', 'text/plain', 'application/zip', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $max_file_size = 700 * 1024 * 1024; // 700MB

    // 기존 파일 삭제
    $select_stmt = $conn->prepare("SELECT file_path FROM inquiry_files WHERE inquiry_id = ?");
    $select_stmt->bind_param("i", $inquiry_id);
    $select_stmt->execute();
    $result = $select_stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $abs_path = realpath(__DIR__ . '/../' . $row['file_path']);
        if ($abs_path && file_exists($abs_path)) {
            unlink($abs_path);
        }
    }

    // DB 내 기존 파일 삭제
    $del_stmt = $conn->prepare("DELETE FROM inquiry_files WHERE inquiry_id = ?");
    $del_stmt->bind_param("i", $inquiry_id);
    $del_stmt->execute();

    // 업로드 디렉터리 확인
    $upload_dir = __DIR__ . '/../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $files_to_upload = [];
    foreach ($_FILES['files']['name'] as $i => $original_name) {
        $tmp_name = $_FILES['files']['tmp_name'][$i];
        $file_size = $_FILES['files']['size'][$i];

        // 파일 크기 체크
        if ($file_size > $max_file_size) {
            echo "<script>alert('파일 크기가 너무 큽니다. (최대 700MB)'); history.back();</script>";
            exit;
        }

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

    // 파일 저장 및 DB 등록
    foreach ($files_to_upload as $file) {
        if (move_uploaded_file($file['tmp_name'], $file['target_path'])) {
            $file_stmt = $conn->prepare("INSERT INTO inquiry_files (inquiry_id, file_name, file_path) VALUES (?, ?, ?)");
            $file_stmt->bind_param("iss", $inquiry_id, $file['original_name'], $file['relative_path']);
            $file_stmt->execute();
            $file_stmt->close();
        }
    }
}

echo "<script>alert('수정되었습니다.'); location.href='../inquiry/inquiry_detail.php?inquiry_id={$inquiry_id}';</script>";
exit;
?>