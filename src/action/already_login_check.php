<?php
include_once __DIR__ . '/../includes/session.php';

if (isset($_SESSION['user_id'])) {
    echo "<script>
            alert('이미 로그인 한 상태입니다.');
            window.location.href = '../index.php';
        </script>";
}

?>