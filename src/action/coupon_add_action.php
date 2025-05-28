<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

// 관리자가 아닌 경우 CSRF 토큰 검증
if (!isset($_SESSION['is_admin'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        echo "<script>alert('잘못된 요청입니다.'); history.back();</script>";
        exit;
    }
}

if (function_exists('customErrorHandler')) {
    set_error_handler('customErrorHandler');
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // GET 방식 제한
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        exit('허용되지 않은 요청 방식입니다.');
    }

    // 필수 입력값 필터링 및 검증
    $required_fields = ['code', 'name', 'discount_type', 'discount_value', 'start_date', 'end_date', 'minimum_purchase'];
    foreach ($required_fields as $field) {
        if (!isset($_GET[$field]) || trim($_GET[$field]) === '') {
            throw new Exception("필수 입력값이 누락되었습니다: $field");
        }
    }

    // 입력값 정리 및 타입 안전화
    $code = trim($_GET['code']);
    $name = trim($_GET['name']);
    $discount_type = $_GET['discount_type'];
    $discount_value = (int)$_GET['discount_value'];
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
    $minimum_purchase = (int)$_GET['minimum_purchase'];
    $maximum_discount = isset($_GET['maximum_discount']) ? (int)$_GET['maximum_discount'] : null;
    $usage_limit = isset($_GET['usage_limit']) ? (int)$_GET['usage_limit'] : null;
    $is_active = isset($_GET['is_active']) ? 1 : 0;

    // 할인 유형과 값 검증
    if (!in_array($discount_type, ['percentage', 'fixed'])) {
        throw new Exception("할인 유형이 유효하지 않습니다.");
    }
    if ($discount_type === 'percentage' && ($discount_value < 1 || $discount_value > 100)) {
        throw new Exception("퍼센트 할인 값은 1~100 사이여야 합니다.");
    }

    // 날짜 유효성 검사
    if (strtotime($start_date) === false || strtotime($end_date) === false) {
        throw new Exception("날짜 형식이 잘못되었습니다.");
    }
    if ($start_date > $end_date) {
        throw new Exception("종료일은 시작일 이후여야 합니다.");
    }

    // 쿠폰 코드 중복 확인
    $check_stmt = $conn->prepare("SELECT 1 FROM coupons WHERE code = ?");
    $check_stmt->bind_param("s", $code);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows > 0) {
        throw new Exception("이미 존재하는 쿠폰 코드입니다.");
    }

    // 쿠폰 삽입
    $stmt = $conn->prepare("
        INSERT INTO coupons 
        (code, name, discount_type, discount_value, start_date, end_date, minimum_purchase, maximum_discount, usage_limit, is_active, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param(
        "sssisiiiii",
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
    $stmt->execute();

    echo "<script>alert('쿠폰이 성공적으로 추가되었습니다.'); location.href='../admin/admin.php?tab=coupons';</script>";
    exit;

} catch (Throwable $e) {
    error_log("[쿠폰 추가 오류] " . $e->getMessage() . " in " . $e->getFile() . " line " . $e->getLine());
    $safeMessage = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    echo "<script>alert('$safeMessage'); history.back();</script>";
    exit;
}
