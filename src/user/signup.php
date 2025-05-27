<?php 
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../action/already_login_check.php';
?>

    <main class="login-container">
        <div class="login-box">
            <h1>회원가입</h1>
            <form id="signupForm" method ="get" action="../action/signup_action.php">
                <div class="login-form-group">
                    <label for="name">이름</label>
                    <div class="input-group">
                        <i class="fas fa-user-circle"></i>
                        <input type="text" id="username" name="username" placeholder="이름을 입력하세요" required>
                    </div>
                </div>
                <div class="login-form-group">
                    <label for="id">아이디</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" id="real_id" name="real_id" placeholder="아이디를 입력하세요" required>
                    </div>
                </div>
                <div class="login-form-group">
                    <label for="password">비밀번호</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="비밀번호를 입력하세요" required>
                    </div>
                </div>
                <div class="login-form-group">
                    <label for="passwordConfirm">비밀번호 확인</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="passwordConfirm" name="passwordConfirm" placeholder="비밀번호를 다시 입력하세요" required>
                    </div>
                </div>
                <div class="login-form-group">
                    <label for="email">이메일</label>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="이메일을 입력하세요" required>
                    </div>
                </div>
                <div class="login-form-group">
                    <label for="phone">전화번호</label>
                    <div class="input-group">
                        <i class="fas fa-phone"></i>
                        <input type="tel" id="phone" name="phone" placeholder="전화번호를 입력하세요" required>
                    </div>
                </div>
                <div class="login-form-check-options">
                    <label class="terms-agree">
                        <input type="checkbox" name="terms" required>
                        <span>(필수) 이용약관 및 개인정보 처리방침에 동의합니다</span>
                    </label>
                </div>
                <div class="login-form-check-options">
                    <label class="terms-agree">
                        <input type="checkbox" name="marketing">
                        <span>(선택) 마케팅 정보 수신에 동의합니다.</span>
                    </label>
                </div>
                <button type="submit" class="login-btn">회원가입</button>
            </form>
            <div class="signup-link">
                이미 회원이신가요? <a href="login.php">로그인</a>
            </div>
        </div>
    </main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?> 