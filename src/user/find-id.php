<?php 
include_once __DIR__ . '/../includes/header.php';
?>

<main class="login-container">
    <div class="login-box">
        <h1>아이디 찾기</h1>
        <form id="findIdForm" method="post" action="../action/find_id_action.php">
            <div class="login-form-group">
                <label for="username">이름</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="이름을 입력하세요" required
                           pattern="^[a-zA-Z가-힣\s]{2,20}$" title="이름은 2~20자의 한글 또는 영문으로 입력하세요.">
                </div>
            </div>
            <div class="login-form-group">
                <label for="email">이메일</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="이메일을 입력하세요" required
                           maxlength="100">
                </div>
            </div>
            <button type="submit" class="login-btn">아이디 찾기</button>
        </form>
        <div class="signup-link">
            <a href="login.php">로그인</a>
            <span class="separator">|</span>
            <a href="find-pw.php">비밀번호 찾기</a>
            <span class="separator">|</span>
            <a href="signup.php">회원가입</a>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
