<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../action/login_check.php';

// 후기 작성 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $rating = (int)$_POST['rating'];
    $content = trim($_POST['content']);
    $travel_type = trim($_POST['travel_type']);
    $user_id = (int)$_SESSION['user_id'];
    $hotel_id = (int)$_POST['hotel_id'];

    if (strlen($content) > 5000) {
        echo "<script>alert('내용은 5000자 이내로 입력해주세요.'); history.back();</script>";
        exit;
    }

    $image_path = '';
    if (isset($_FILES['review_image']) && $_FILES['review_image']['error'] === 0) {
        $target_dir = __DIR__ . "/../uploads/reviews/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        $filename = time() . '_' . basename($_FILES['review_image']['name']);
        $target_file = $target_dir . $filename;

        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mime = mime_content_type($_FILES['review_image']['tmp_name']);
        $allowed_mime = ['image/jpeg', 'image/png', 'image/gif'];

        if (in_array($ext, $allowed_ext) && in_array($mime, $allowed_mime)) {
            if (move_uploaded_file($_FILES['review_image']['tmp_name'], $target_file)) {
                $image_path = "/uploads/reviews/" . $filename;
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO reviews (user_id, hotel_id, rating, content, image_url, travel_type, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiisss", $user_id, $hotel_id, $rating, $content, $image_path, $travel_type);
    $stmt->execute();

    // 호텔 평점 갱신
    $update_stmt = $conn->prepare("
        UPDATE hotels h
        SET h.rating = (
            SELECT ROUND(AVG(r.rating), 1) FROM reviews r WHERE r.hotel_id = h.hotel_id
        )
        WHERE h.hotel_id = ?
    ");
    $update_stmt->bind_param("i", $hotel_id);
    $update_stmt->execute();

    header("Location: ../hotel/hotel-detail.php?id=$hotel_id");
    exit;
}

// 리뷰 삭제 처리
if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['review_id']) && isset($_POST['hotel_id'])) {
    $review_id = (int)$_POST['review_id'];
    $hotel_id = (int)$_POST['hotel_id'];

    $conn->query("DELETE FROM review_helpful WHERE review_id = $review_id");
    $conn->query("DELETE FROM review_images WHERE review_id = $review_id");
    $conn->query("DELETE FROM reviews WHERE review_id = $review_id");

    $stmt = $conn->prepare("
        UPDATE hotels h
        SET h.rating = (
            SELECT ROUND(AVG(r.rating), 1) FROM reviews r WHERE r.hotel_id = h.hotel_id
        )
        WHERE h.hotel_id = ?
    ");
    $stmt->bind_param("i", $hotel_id);
    $stmt->execute();

    echo "<script>alert('리뷰가 삭제되었습니다.'); location.href='../hotel/hotel-detail.php?id=$hotel_id';</script>";
    exit;
}

// 도움이 됨/안됨 처리
if (
    isset($_POST['review_id'], $_POST['action'], $_POST['hotel_id']) &&
    isset($_SESSION['user_id'])
) {
    $review_id = (int)$_POST['review_id'];
    $action = $_POST['action'];
    $hotel_id = (int)$_POST['hotel_id'];
    $user_id = (int)$_SESSION['user_id'];

    // 중복 확인
    $check = $conn->prepare("SELECT 1 FROM review_helpful WHERE review_id = ? AND user_id = ?");
    if (!$check) {
        die("리뷰 중복 확인 쿼리 실패: " . $conn->error);
    }
    $check->bind_param("ii", $review_id, $user_id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('이미 참여하셨습니다.'); location.href='../hotel/hotel-detail.php?id=$hotel_id';</script>";
        exit;
    }

    // 삽입
    $is_helpful = $action === 'helpful' ? 1 : 0;
    $not_helpful = $action === 'not_helpful' ? 1 : 0;

    $insert = $conn->prepare("INSERT INTO review_helpful (review_id, user_id, is_helpful, not_helpful) VALUES (?, ?, ?, ?)");
    if (!$insert) {
        die("도움 등록 쿼리 준비 실패: " . $conn->error);
    }
    $insert->bind_param("iiii", $review_id, $user_id, $is_helpful, $not_helpful);
    $insert->execute();

    echo "<script>alert('소중한 의견 감사합니다!'); location.href='../hotel/hotel-detail.php?id=$hotel_id';</script>";
    exit;
}
?>
