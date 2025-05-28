<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../action/login_check.php';

// 후기 작성 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $rating = $_POST['rating'];
    $content = $_POST['content'];
    $travel_type = $_POST['travel_type'];
    $user_id = $_SESSION['user_id'];
    $hotel_id = $_POST['hotel_id'];

    $image_path = '';
    if (isset($_FILES['review_image']) && $_FILES['review_image']['error'] === 0) {
        $target_dir = __DIR__ . "/../uploads/reviews/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        $filename = time() . '_' . basename($_FILES['review_image']['name']);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES['review_image']['tmp_name'], $target_file)) {
            $image_path = "/uploads/reviews/" . $filename;
        }
    }

    $sql = "INSERT INTO reviews (user_id, hotel_id, rating, content, image_url, travel_type, created_at) 
            VALUES ('$user_id', '$hotel_id', '$rating', '$content', '$image_path', '$travel_type', NOW())";
    mysqli_query($conn, $sql);

    // 호텔 평점 업데이트
    $update_rating_sql = "UPDATE hotels h 
                         SET h.rating = (
                             SELECT ROUND(AVG(r.rating), 1)
                             FROM reviews r
                             WHERE r.hotel_id = h.hotel_id
                         )
                         WHERE h.hotel_id = '$hotel_id'";
    mysqli_query($conn, $update_rating_sql);

    header("Location: ../hotel/hotel-detail.php?id=$hotel_id");
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['review_id']) && isset($_GET['hotel_id'])) {
    $review_id = (int)$_GET['review_id'];
    $hotel_id = (int)$_GET['hotel_id'];

    // 연결된 이미지와 helpful 먼저 삭제 (CASCADE가 없는 경우 수동 삭제)
    mysqli_query($conn, "DELETE FROM review_helpful WHERE review_id = $review_id");
    mysqli_query($conn, "DELETE FROM review_images WHERE review_id = $review_id");

    // 리뷰 삭제
    $delete_sql = "DELETE FROM reviews WHERE review_id = $review_id";
    if (!mysqli_query($conn, $delete_sql)) {
        die("리뷰 삭제 실패: " . mysqli_error($conn));
    }

    // 호텔 평점 재계산
    $update_rating_sql = "UPDATE hotels h 
                         SET h.rating = (
                             SELECT ROUND(AVG(r.rating), 1)
                             FROM reviews r
                             WHERE r.hotel_id = h.hotel_id
                         )
                         WHERE h.hotel_id = $hotel_id";
    mysqli_query($conn, $update_rating_sql);

    echo "<script>alert('리뷰가 삭제되었습니다.'); location.href='../hotel/hotel-detail.php?id=$hotel_id';</script>";
    exit;
}
// 도움이 됨/안됨 처리
if (isset($_GET['review_id']) && isset($_GET['action'])) {
    $review_id = $_GET['review_id'];
    $action = $_GET['action'];
    $hotel_id = $_GET['hotel_id'];
    $user_id = $_SESSION['user_id'];

    // 이미 눌렀는지 확인
    $check = mysqli_query($conn, "SELECT * FROM review_helpful WHERE review_id = '$review_id' AND user_id = '$user_id'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('이미 참여하셨습니다.'); location.href='../hotel/hotel-detail.php?id=$hotel_id';</script>";
        exit;
    }

    // 아직 안눌렀음 → 추가
    if ($action === 'helpful') {
        mysqli_query($conn, "INSERT INTO review_helpful (review_id, user_id, is_helpful, not_helpful) VALUES ('$review_id', '$user_id', 1, 0)");
    } elseif ($action === 'not_helpful') {
        mysqli_query($conn, "INSERT INTO review_helpful (review_id, user_id, is_helpful, not_helpful) VALUES ('$review_id', '$user_id', 0, 1)");
    }

    // 후기 테이블 카운트 업데이트
    mysqli_query($conn, "UPDATE reviews 
        SET count_is_helpful = (SELECT COUNT(*) FROM review_helpful WHERE review_id = reviews.review_id AND is_helpful = 1),
            count_is_not_helpful = (SELECT COUNT(*) FROM review_helpful WHERE review_id = reviews.review_id AND not_helpful = 1)
        WHERE review_id = '$review_id'");

    echo "<script>alert('소중한 의견 감사합니다!'); location.href='../hotel/hotel-detail.php?id=$hotel_id';</script>";
    exit;
}


?>
