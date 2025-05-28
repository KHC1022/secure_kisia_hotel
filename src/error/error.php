<?php
include_once __DIR__ . '/../includes/session.php';
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno] $errstr on line $errline in file $errfile");
    return true;
}

// 에러 페이지 HTML은 그대로 유지
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>페이지를 찾을 수 없습니다</title>
    <style>
        body {
            background-color: #f8f8f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .error-container {
            text-align: center;
        }
        .error-code {
            font-size: 8em;
            font-weight: bold;
            color: #e74c3c;
        }
        .error-message {
            font-size: 1.5em;
            color: #333;
        }
        .home-link {
            margin-top: 20px;
            display: inline-block;
            text-decoration: none;
            color: #3498db;
            font-size: 1em;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <div class="error-message">요청하신 페이지를 찾을 수 없습니다.</div>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
            <a class="home-link" href="/admin/admin.php">관리자 페이지로 돌아가기</a>
        <?php else: ?>
            <a class="home-link" href="/">홈으로 돌아가기</a>
        <?php endif; ?>
    </div>
</body>
</html>
