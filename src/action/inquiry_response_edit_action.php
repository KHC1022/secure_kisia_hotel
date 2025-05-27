<?php
include_once __DIR__ . '/../includes/db_connection.php';

$inquiry_id = $_GET['inquiry_id'];
$content = $_GET['content'];

$check_sql = "SELECT * FROM inquiry_responses WHERE inquiry_id = $inquiry_id";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) > 0) {
    $update_sql = "
        UPDATE inquiry_responses
        SET content = '$content'
        WHERE inquiry_id = $inquiry_id
    ";
    mysqli_query($conn, $update_sql);
} else {
    $insert_sql = "
        INSERT INTO inquiry_responses (inquiry_id, content)
        VALUES ($inquiry_id, '$content')
    ";
    mysqli_query($conn, $insert_sql);
}

echo "<script>alert('답변이 수정되었습니다.'); location.href='../inquiry/inquiry_detail.php?inquiry_id=$inquiry_id';</script>";
exit;
