<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';
$id = $_GET['real_id'];
$password = $_GET['password'];

$sql = "SELECT user_id, real_id, username, password, is_admin FROM users WHERE real_id='$id' AND password='$password'";
$result = $conn->query($sql);
$row = $result->fetch_array(MYSQLI_ASSOC);

if ($row!=null) {
    $_SESSION['is_login'] = true;
    $_SESSION['username'] = $row['username'];
    $_SESSION['real_id'] = $row['real_id'];
    $_SESSION['user_id'] = $row['user_id'];
    $_SESSION['is_admin'] = $row['is_admin'];

    if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']):
        echo "<script>
                alert('관리자님 안녕하세요.');
                window.location.href = '../admin/admin.php';
        </script>";
    else:
        echo "<script>
                alert('로그인 되었습니다.');
                window.location.href = '../index.php';
        </script>";
    endif;
    exit;
}
else {
    echo "<script>
            alert('아이디 또는 패스워드가 틀렸습니다.');
            history.back();
    </script>";
    exit;
}
?>