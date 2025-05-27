<?php include_once __DIR__ . '/session.php'; ?>
    <?php if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] == 0) { ?>
    <footer class="style-footer">
        <div class="style-footer-content">
            <div class="style-footer-section">
                <h3>회사 소개</h3>
                <p>경쟁력 있는 가격과 우수한 고객 서비스로 최고의 호텔 예약 경험을 제공합니다.</p>
            </div>
            <div class="style-footer-section">
                <h3>바로가기</h3>
                <ul>
                    <li><a href="../index.php">홈</a></li>
                    <li><a href="../hotel/hotels.php">호텔</a></li>
                    <li><a href="../inquiry/inquiry.php">문의</a></li>
                    <li><a href="../notice/notice.php">공지사항</a></li>
                </ul>
            </div>
            <div class="style-footer-section">
                <h3>연락처</h3>
                <p><i class="fas fa-phone"></i> +82 02-1234-5678</p>
                <p><i class="fas fa-envelope"></i> info@kisiahotel.com</p>
            </div>
        </div>
        <div class="style-footer-bottom">
            <p>&copy; 2025 KISIA HOTEL. All rights reserved.</p>
        </div>
    </footer>
    <?php } ?>
</body>
</html> 