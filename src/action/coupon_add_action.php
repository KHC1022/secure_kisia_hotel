<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $code = $_GET['code'];
    $name = $_GET['name'];
    $discount_type = $_GET['discount_type'];
    $discount_value = $_GET['discount_value'];
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
    $minimum_purchase = $_GET['minimum_purchase'];
    $maximum_discount = !empty($_GET['maximum_discount']) ? $_GET['maximum_discount'] : null;
    $usage_limit = !empty($_GET['usage_limit']) ? $_GET['usage_limit'] : null;
    $is_active = isset($_GET['is_active']) ? 1 : 0;

    // 쿠폰 코드 중복 체크
    $check_sql = "SELECT code FROM coupons WHERE code = '$code'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if ($check_result && $check_result->num_rows > 0) {
        echo "<script>alert('이미 존재하는 쿠폰 코드입니다.'); history.back();</script>";
        exit;
    }

    // 할인 유형에 따른 유효성 검사
    if ($discount_type === 'percentage' && $discount_value > 100) {
        echo "<script>alert('퍼센트 할인은 100%를 초과할 수 없습니다.'); history.back();</script>";
        exit;
    }

    // 날짜 유효성 검사
    if ($start_date > $end_date) {
        echo "<script>alert('종료일은 시작일보다 이후여야 합니다.'); history.back();</script>";
        exit;
    }

    $sql = "INSERT INTO coupons (code, name, discount_type, discount_value, start_date, end_date, 
            minimum_purchase, maximum_discount, usage_limit, is_active, created_at) 
            VALUES ('$code', '$name', '$discount_type', $discount_value, '$start_date', '$end_date', 
            $minimum_purchase, " . ($maximum_discount ? $maximum_discount : "NULL") . ", 
            " . ($usage_limit ? $usage_limit : "NULL") . ", $is_active, NOW())";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('쿠폰이 성공적으로 추가되었습니다.'); location.href='../admin/admin.php?tab=coupons';</script>";
    } else {
        echo "<script>alert('쿠폰 추가 중 오류가 발생했습니다.'); history.back();</script>";
    }
} else {
    header("Location: ../admin/admin.php?tab=coupons");
}
?> 