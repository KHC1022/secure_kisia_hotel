<?php
include_once __DIR__ . '/../includes/session.php';
?>

<?php if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true): ?>
    <!-- 로그인되지 않은 사용자 -->
    <a href="../user/login.php" class="style-auth-btn">로그인</a>
    <a href="../user/signup.php" class="style-auth-btn">회원가입</a>
<?php else: ?>
    <!-- 로그인된 사용자 -->
    <span class="style-username">
        <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?>님
    </span>

    <?php if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
        <!-- 관리자일 경우 -->
        <a href="../action/logout_action.php" class="style-auth-btn">로그아웃</a>
    <?php else: ?>
        <!-- 일반 사용자일 경우 -->
        <a href="../user/mypage.php" class="style-auth-btn">마이페이지</a>
        <a href="../action/logout_action.php" class="style-auth-btn">로그아웃</a>
    <?php endif; ?>
<?php endif; ?>
