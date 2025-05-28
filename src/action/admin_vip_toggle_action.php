<?php
include_once __DIR__ . '/../includes/db_connection.php';

$user_id = $_POST['user_id'] ?? null;
$vip_status = $_POST['vip_status'] ?? null;

if ($user_id !== null && $vip_status !== null) {
    $sql = "UPDATE users SET vip = $vip_status, vip_status = 'manual' WHERE user_id = $user_id";
    mysqli_query($conn, $sql);

    echo "<script>alert('회원 등급이 변경되었습니다.');
    location.href = '../admin/admin.php?tab=users';
    </script>";
}

?>
