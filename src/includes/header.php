<?php 
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php'; 
include_once __DIR__ . '/../includes/pagination.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KISIA HOTEL</title>
    <link rel="stylesheet" href="../style/styles.css">
    <link rel="stylesheet" href="../style/login.css">
    <link rel="stylesheet" href="../style/mypage.css">
    <link rel="stylesheet" href="../style/inquiry.css">
    <link rel="stylesheet" href="../style/review.css">
    <link rel="stylesheet" href="../style/event.css">
    <link rel="stylesheet" href="../style/event-timedeal.css">
    <link rel="stylesheet" href="../style/hotels.css">
    <link rel="stylesheet" href="../style/admin.css">
    <link rel="stylesheet" href="../style/payment.css">
    <link rel="stylesheet" href="../style/hotel-detail.css">
    <link rel="stylesheet" href="../style/hotel-add.css">
    <link rel="stylesheet" href="../style/inquiry.css">
    <link rel="stylesheet" href="../style/coupon.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<?php if ($_SESSION['is_admin'] ?? false): ?>
    <body>
    <div class="style-header">
        <div class="style-nav">
            <a class="style-logo">KISIA <span>HOTEL</span></a>
            <div class="style-auth-buttons">
                <?php include __DIR__ . '/../action/auto_buttons.php'; ?>
            </div>
        </div>
    </div>
</body>
<?php else: ?>
<body>
    <div class="style-header">
        <div class="style-nav">
            <a href="../index.php" class="style-logo">KISIA <span>HOTEL</span></a>
            <ul class="style-nav-links">
                <li><a href="../index.php">홈</a></li>
                <li><a href="../hotel/hotels.php">호텔</a></li>
                <li><a href="../inquiry/inquiry.php">문의</a></li>
                <li><a href="../notice/notice.php">공지사항</a></li>
                <li><a href="../coupon/coupon-list.php">쿠폰</a></li>
            </ul>
            <div class="style-auth-buttons">
                <?php include __DIR__ . '/../action/auto_buttons.php'; ?>
            </div>
        </div>
    </div>
</body>
<?php endif; ?>
</html> 