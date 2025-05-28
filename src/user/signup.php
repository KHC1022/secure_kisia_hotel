<?php 
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../action/already_login_check.php';
?>

<main class="login-container">
    <div class="login-box">
        <h1>회원가입</h1>
        <form id="signupForm" method="post" action="../action/signup_action.php" autocomplete="off">
            <!-- 이름 -->
            <div class="login-form-group">
                <label for="username">이름</label>
                <div class="input-group">
                    <i class="fas fa-user-circle"></i>
                    <input type="text" id="username" name="username" placeholder="이름을 입력하세요" required>
                </div>
            </div>

            <!-- 아이디 -->
            <div class="login-form-group">
                <label for="real_id">아이디</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="real_id" name="real_id" placeholder="아이디를 입력하세요"
                        pattern="[a-zA-Z0-9]{5,20}" title="영문 또는 숫자로 5~20자 이내로 입력" required>
                </div>
            </div>

            <!-- 비밀번호 -->
            <div class="login-form-group">
                <label for="password">비밀번호</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password"
                        placeholder="8자 이상, 특수문자 포함" pattern="(?=.*[\W_]).{8,}"
                        title="8자 이상이며 특수문자를 포함해야 합니다." required autocomplete="new-password">
                </div>
            </div>

            <!-- 비밀번호 확인 -->
            <div class="login-form-group">
                <label for="passwordConfirm">비밀번호 확인</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="passwordConfirm" name="passwordConfirm" placeholder="비밀번호를 다시 입력하세요" required>
                </div>
            </div>

            <!-- 이메일 -->
            <div class="login-form-group">
                <label for="email">이메일</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="이메일을 입력하세요" required autocomplete="email">
                </div>
            </div>

            <!-- 전화번호 -->
            <div class="login-form-group">
                <label for="phone">전화번호</label>
                <div class="input-group">
                    <i class="fas fa-phone"></i>
                    <input type="tel" id="phone" name="phone" placeholder="숫자만 입력" pattern="[0-9]{10,11}" title="숫자만 입력 (10~11자리)" required>
                </div>
            </div>

            <!-- 약관 동의 -->
            <div class="login-form-check-options">
                <label class="terms-agree">
                    <input type="checkbox" name="terms" required>
                    <span>(필수) 이용약관 및 개인정보 처리방침에 동의합니다</span>
                </label>
            </div>

            <!-- 마케팅 수신 동의 -->
            <div class="login-form-check-options">
                <label class="terms-agree">
                    <input type="checkbox" name="marketing">
                    <span>(선택) 마케팅 정보 수신에 동의합니다.</span>
                </label>
            </div>

            <!-- CSRF 토큰 -->
            <?php if (!empty($_SESSION['csrf_token'])): ?>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <?php endif; ?>

            <button type="submit" class="login-btn">회원가입</button>

        </form>
        <div class="signup-link">
            이미 회원이신가요? <a href="login.php">로그인</a>
        </div>
    </div>
</main>

<script>
// 비밀번호 확인 JS 검사
document.getElementById("signupForm").addEventListener("submit", function(e) {
    const pw = document.getElementById("password").value;
    const confirm = document.getElementById("passwordConfirm").value;
    if (pw !== confirm) {
        alert("비밀번호가 일치하지 않습니다.");
        e.preventDefault();
    }
});
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
