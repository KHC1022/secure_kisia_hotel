<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

$inquiry_id=$_GET['inquiry_id'];
$sql="select inquiry_id from inquiries where inquiry_id= $inquiry_id";
$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_array($result);

$delete_sql = "delete from inquiries where inquiry_id= $inquiry_id";
$delete_result = mysqli_query($conn, $delete_sql);

if($delete_result){
    echo "<script> alert(\"삭제되었습니다.\");
    location.href='../inquiry/inquiry.php';</script>";
}else{
    echo "<script> alert(\"삭제 실패했습니다.\");history.back();</script>";
}
header("Location: ../inquiry/inquiry.php");

mysqli_close($conn);