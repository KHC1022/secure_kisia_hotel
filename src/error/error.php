<?php
include_once __DIR__ . '/../includes/session.php';

function customErrorHandler($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno] $errstr on line $errline in file $errfile");
    return true;
}

// 에러 코드 설정
$code = 404; // 기본값 설정

// GET 파라미터로 전달된 에러 코드가 있으면 사용
if (isset($_GET['code']) && is_numeric($_GET['code'])) {
    $code = (int)$_GET['code'];
} 
// 현재 HTTP 상태 코드가 200이 아닌 경우 사용
else if (http_response_code() !== 200) {
    $code = http_response_code();
}

// 유효한 HTTP 에러 코드인지 확인
$validCodes = [400, 401, 403, 404, 500, 503];
if (!in_array($code, $validCodes)) {
    $code = 500; // 유효하지 않은 코드는 500으로 처리
}

// HTTP 상태 코드 설정
http_response_code($code);

$errorMessages = [
    404 => '요청하신 페이지를 찾을 수 없습니다.',
    400 => '잘못된 요청입니다.',
    401 => '로그인이 필요한 페이지입니다.',
    403 => '접근 권한이 없습니다.',
    500 => '서버 내부 오류가 발생했습니다. 잠시 후 다시 시도해주세요.',
    503 => '서비스가 일시적으로 이용 불가능합니다.'
];

$errorMessage = $errorMessages[$code] ?? '알 수 없는 오류가 발생했습니다.';

// 에러 로그 기록
error_log("Error page displayed: HTTP $code - $errorMessage");
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>오류 <?= htmlspecialchars($code) ?></title>
    <link rel="stylesheet" href="/../style/error.css">
</head>
<body>
    <div class="error-container">
        <div class="error-code"><?= htmlspecialchars($code) ?></div>
        <div class="error-message"><?= htmlspecialchars($errorMessage) ?></div>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
            <a class="home-link" href="/admin/admin.php">관리자 페이지로 돌아가기</a>
        <?php else: ?>
            <a class="home-link" href="/">홈으로 돌아가기</a>
        <?php endif; ?>
    </div>
</body>
</html>
