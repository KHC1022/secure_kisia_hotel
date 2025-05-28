<?php
include_once __DIR__ . '/../includes/session.php';

// 로그인 여부 확인
if (empty($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    echo "<script>
        alert('로그인 후 이용해주세요.');
        window.location.href = '../user/login.php';
    </script>";
    exit;
}
?>
