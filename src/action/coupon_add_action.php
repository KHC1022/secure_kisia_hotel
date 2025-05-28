<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

if (function_exists('customErrorHandler')) {
    set_error_handler('customErrorHandler');
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $code = htmlspecialchars($_GET['code'], ENT_QUOTES, 'UTF-8');
        $name = htmlspecialchars($_GET['name'], ENT_QUOTES, 'UTF-8');
        $discount_type = htmlspecialchars($_GET['discount_type'], ENT_QUOTES, 'UTF-8');
        $discount_value = (int)$_GET['discount_value'];
        $start_date = htmlspecialchars($_GET['start_date'], ENT_QUOTES, 'UTF-8');
        $end_date = htmlspecialchars($_GET['end_date'], ENT_QUOTES, 'UTF-8');
        $minimum_purchase = (int)$_GET['minimum_purchase'];
        $maximum_discount = !empty($_GET['maximum_discount']) ? (int)$_GET['maximum_discount'] : null;
        $usage_limit = !empty($_GET['usage_limit']) ? (int)$_GET['usage_limit'] : null;
        $is_active = isset($_GET['is_active']) ? 1 : 0;

        // 중복 확인
        $stmt = $conn->prepare("SELECT code FROM coupons WHERE code = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            throw new Exception("이미 존재하는 쿠폰 코드입니다.");
        }

        // 할인 타입 검사
        if ($discount_type === 'percentage' && $discount_value > 100) {
            throw new Exception("퍼센트 할인은 100%를 초과할 수 없습니다.");
        }

        // 날짜 검사
        if ($start_date > $end_date) {
            throw new Exception("종료일은 시작일보다 이후여야 합니다.");
        }

        $stmt = $conn->prepare("INSERT INTO coupons (code, name, discount_type, discount_value, start_date, end_date, 
            minimum_purchase, maximum_discount, usage_limit, is_active, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        $stmt->bind_param("sssisiiiii", 
            $code, 
            $name, 
            $discount_type, 
            $discount_value, 
            $start_date, 
            $end_date, 
            $minimum_purchase, 
            $maximum_discount, 
            $usage_limit, 
            $is_active
        );

        if ($stmt->execute()) {
            echo "<script>alert('쿠폰이 성공적으로 추가되었습니다.'); location.href='../admin/admin.php?tab=coupons';</script>";
        } else {
            throw new Exception("쿠폰 추가 중 오류가 발생했습니다. 관리자에게 문의해주세요.");
        }

    } else {
        header("Location: ../admin/admin.php?tab=coupons");
        exit;
    }

} catch (Throwable $e) {
    // 내부 로깅
    error_log("[쿠폰 추가 오류] " . $e->getMessage() . " in " . $e->getFile() . " line " . $e->getLine());

    // 사용자 알림
    $safeMessage = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    echo "<script>alert('$safeMessage'); history.back();</script>";
    exit;
}
