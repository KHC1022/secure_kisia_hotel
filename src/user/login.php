<?php 
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../action/already_login_check.php';
?>

<main class="login-container">
    <div class="login-box">
        <h1>로그인</h1>
        <form id="loginForm" method="post" action="../action/login_action.php" autocomplete="off">
            <div class="login-form-group">
                <label for="real_id">아이디</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="real_id" name="real_id" placeholder="아이디를 입력하세요" required autocomplete="username">
                </div>
            </div>
            <div class="login-form-group">
                <label for="password">비밀번호</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="비밀번호를 입력하세요" required autocomplete="current-password">
                </div>
            </div>
            <div class="login-form-options">
                <a href="find-id.php" class="find-id">아이디 찾기</a>
                <span class="separator">|</span>
                <a href="find-pw.php" class="forgot-password">비밀번호 찾기</a>
            </div>
            <button type="submit" class="login-login-btn">로그인</button>
        </form>
        <div class="signup-link">
            아직 회원이 아니신가요? <a href="signup.php">회원가입</a>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
