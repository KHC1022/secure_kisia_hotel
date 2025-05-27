<?php
include_once __DIR__ . '/../includes/db_connect.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// 쿠폰 추가
function addCoupon($pdo, $data) {
    $sql = "INSERT INTO coupons (code, name, description, discount_type, discount_value, 
            start_date, end_date, minimum_purchase, maximum_discount, usage_limit, is_active) 
            VALUES (:code, :name, :description, :discount_type, :discount_value, 
            :start_date, :end_date, :minimum_purchase, :maximum_discount, :usage_limit, 1)";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

// 쿠폰 수정
function updateCoupon($pdo, $data) {
    $sql = "UPDATE coupons SET 
            code = :code,
            name = :name,
            description = :description,
            discount_type = :discount_type,
            discount_value = :discount_value,
            start_date = :start_date,
            end_date = :end_date,
            minimum_purchase = :minimum_purchase,
            maximum_discount = :maximum_discount,
            usage_limit = :usage_limit,
            is_active = :is_active
            WHERE coupon_id = :coupon_id";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

// 쿠폰 삭제
function deleteCoupon($pdo, $couponId) {
    $stmt = $pdo->prepare("DELETE FROM coupons WHERE coupon_id = :id");
    $stmt->bindParam(':id', $couponId);
    return $stmt->execute();
}

// 응답 메시지 설정
$message = '';
$success = false;


$redirectUrl = 'coupon-list.php';
if ($message) {
    $redirectUrl .= '?message=' . urlencode($message) . '&success=' . ($success ? '1' : '0');
}
header('Location: ' . $redirectUrl);
exit; 