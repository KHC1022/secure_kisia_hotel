<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

if (isset($_GET['review_id']) && isset($_GET['action']) && isset($_GET['hotel_id'])) {
    $review_id = $_GET['review_id'];
    $action = $_GET['action'];
    $hotel_id = $_GET['hotel_id'];
    
    // 로그인 상태 확인
    if (!isset($_SESSION['user_id'])) {
        echo "<script>
            alert('로그인 후 이용해 주세요.');
            window.location.href='../user/login.php';
        </script>";
        exit();
    }
    
    $user_id = $_SESSION['user_id'];
    
    $check_sql = "SELECT * FROM review_helpful WHERE review_id = $review_id AND user_id = $user_id";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<script>
            alert('이미 평가한 리뷰입니다.');
            window.location.href='../hotel/hotel-detail.php?id=" . $hotel_id . "';
        </script>";
        exit();
    }
    
    $is_helpful = ($action === 'helpful') ? 1 : 0;
    $not_helpful = ($action === 'not_helpful') ? 1 : 0;
    
    $sql = "INSERT INTO review_helpful (review_id, user_id, is_helpful, not_helpful) 
            VALUES ($review_id, $user_id, $is_helpful, $not_helpful)";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute()) {
        echo "<script>
            alert('소중한 의견 감사합니다!');
            window.location.href='../hotel/hotel-detail.php?id=" . $hotel_id . "';
        </script>";
    } else {
        echo "<script>
            alert('오류가 발생했습니다.');
            window.location.href='../hotel/hotel-detail.php?id=" . $hotel_id . "';
        </script>";
    }
}
?>