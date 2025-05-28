<?php
include_once __DIR__ . '/../includes/db_connect.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$message = '';
$success = false;

try {
    if ($action === 'add') {
        // 필수 필드 유효성 검증
        $required = ['code', 'name', 'discount_type', 'discount_value', 'start_date', 'end_date'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("필수 항목을 모두 입력해주세요.");
            }
        }

        $data = [
            ':code' => $_POST['code'],
            ':name' => $_POST['name'],
            ':description' => $_POST['description'] ?? '',
            ':discount_type' => $_POST['discount_type'],
            ':discount_value' => (int)$_POST['discount_value'],
            ':start_date' => $_POST['start_date'],
            ':end_date' => $_POST['end_date'],
            ':minimum_purchase' => (int)($_POST['minimum_purchase'] ?? 0),
            ':maximum_discount' => !empty($_POST['maximum_discount']) ? (int)$_POST['maximum_discount'] : null,
            ':usage_limit' => !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null,
        ];

        if (addCoupon($pdo, $data)) {
            $message = '쿠폰이 성공적으로 추가되었습니다.';
            $success = true;
        } else {
            throw new Exception('쿠폰 추가 실패');
        }

    } elseif ($action === 'update') {
        if (empty($_POST['coupon_id'])) {
            throw new Exception('수정할 쿠폰 ID가 누락되었습니다.');
        }

        $data = [
            ':coupon_id' => (int)$_POST['coupon_id'],
            ':code' => $_POST['code'],
            ':name' => $_POST['name'],
            ':description' => $_POST['description'] ?? '',
            ':discount_type' => $_POST['discount_type'],
            ':discount_value' => (int)$_POST['discount_value'],
            ':start_date' => $_POST['start_date'],
            ':end_date' => $_POST['end_date'],
            ':minimum_purchase' => (int)($_POST['minimum_purchase'] ?? 0),
            ':maximum_discount' => !empty($_POST['maximum_discount']) ? (int)$_POST['maximum_discount'] : null,
            ':usage_limit' => !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null,
            ':is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        if (updateCoupon($pdo, $data)) {
            $message = '쿠폰이 성공적으로 수정되었습니다.';
            $success = true;
        } else {
            throw new Exception('쿠폰 수정 실패');
        }

    } elseif ($action === 'delete') {
        $couponId = $_GET['coupon_id'] ?? 0;
        if (!$couponId) {
            throw new Exception('삭제할 쿠폰 ID가 누락되었습니다.');
        }

        if (deleteCoupon($pdo, $couponId)) {
            $message = '쿠폰이 성공적으로 삭제되었습니다.';
            $success = true;
        } else {
            throw new Exception('쿠폰 삭제 실패');
        }

    } else {
        throw new Exception('잘못된 요청입니다.');
    }

} catch (Throwable $e) {
    $message = $e->getMessage();
    error_log("[쿠폰 처리 오류] " . $message);
}

$redirectUrl = 'coupon-list.php?message=' . urlencode($message) . '&success=' . ($success ? '1' : '0');
header("Location: $redirectUrl");
exit;


// 함수 정의

function addCoupon($pdo, $data) {
    $sql = "INSERT INTO coupons (code, name, description, discount_type, discount_value,
            start_date, end_date, minimum_purchase, maximum_discount, usage_limit, is_active)
            VALUES (:code, :name, :description, :discount_type, :discount_value,
            :start_date, :end_date, :minimum_purchase, :maximum_discount, :usage_limit, 1)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

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

function deleteCoupon($pdo, $couponId) {
    $stmt = $pdo->prepare("DELETE FROM coupons WHERE coupon_id = :id");
    $stmt->bindParam(':id', $couponId, PDO::PARAM_INT);
    return $stmt->execute();
}
