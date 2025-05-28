<?php 
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../includes/session.php'; // CSRF 토큰 사용하려면 필요
?>

<main class="login-container">
    <div class="login-box">
        <h1>비밀번호 찾기</h1>
        <form id="findPwForm" method="post" action="../action/find_pw_action.php">
            <div class="login-form-group">
                <label for="id">아이디</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="real_id" name="real_id" placeholder="아이디를 입력하세요" required>
                </div>
            </div>
            <div class="login-form-group">
                <label for="name">이름</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="이름을 입력하세요" required>
                </div>
            </div>
            <div class="login-form-group">
                <label for="email">이메일</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="이메일을 입력하세요" required>
                </div>
            </div>

            <!-- CSRF Token 필드 -->
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <button type="submit" class="login-btn">비밀번호 찾기</button>
        </form>
        <div class="signup-link">
            <a href="login.php">로그인</a>
            <span class="separator">|</span>
            <a href="find-id.php">아이디 찾기</a>
            <span class="separator">|</span>
            <a href="signup.php">회원가입</a>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?> 
