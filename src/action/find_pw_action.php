<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

$username = $_GET['username'];
$email = $_GET['email'];
$real_id = $_GET['real_id'];

$sql = "SELECT real_id, username, password, email, is_admin FROM users WHERE real_id='$real_id' AND username='$username' AND email='$email'";
$result = $conn->query($sql);
$row = $result->fetch_array(MYSQLI_ASSOC);

if ($row!=null) {
    echo "<script>
            alert('$username 님의 password는 ". $row['password'] ." 입니다.');
            window.location.href = '../user/login.php';
    </script>";
    exit;
}
else {
    echo "<script>
            alert('아이디 또는 이름 또는 이메일이 틀렸습니다.');
            history.back();
    </script>";
    exit;
}
?>