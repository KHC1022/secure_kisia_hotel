<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';

$user_id = $_SESSION['user_id'];

$password=$_GET['password'];
$new_password=$_GET['new_password'];
$new_password_check=$_GET['new_password_check'];

$user_sql="select * from users where user_id='$user_id'";
$user_result = mysqli_query($conn, $user_sql);
$users = mysqli_fetch_assoc($user_result);

$GLOBALS['users'] = $users;
if($password==$users['password']){
    if($new_password==$new_password_check){
        $update_sql = "
            UPDATE users
            SET password = '$new_password'
            WHERE user_id = '$user_id'
        ";
        mysqli_query($conn, $update_sql);
        echo "<script>alert('비밀번호가 변경되었습니다.'); location.href='../user/mypage.php';</script>";
        exit;
    } else {
        echo "<script>alert('새 비밀번호가 일치하지 않습니다.'); history.back();</script>";
        exit;
    }
} else {
    echo "<script>alert('기존 비밀번호가 틀렸습니다.'); history.back();</script>";
    exit;
}
