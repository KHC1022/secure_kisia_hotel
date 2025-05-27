<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

$inquiry_id = $_POST['inquiry_id'];
$category = $_POST['category'];
$title = $_POST['title'];
$content = $_POST['content'];

// 문의 내용 업데이트
$update_sql = "UPDATE inquiries SET category='$category', title='$title', content='$content' WHERE inquiry_id=$inquiry_id";
$update_result = mysqli_query($conn, $update_sql);

if ($update_result) {
    // 새 파일이 업로드된 경우
    if (isset($_FILES['files']) && $_FILES['files']['error'] == 0) {
        // 기존 파일 정보 조회
        $select_sql = "SELECT file_path FROM inquiry_files WHERE inquiry_id=$inquiry_id";
        $result = mysqli_query($conn, $select_sql);
        
        // 기존 파일 물리적 삭제
        while($row = mysqli_fetch_assoc($result)) {
            $file_name = basename($row['file_path']);
            $absolute_path = dirname(__DIR__) . '/uploads/' . $file_name;
            
            if (file_exists($absolute_path)) {
                if (@unlink($absolute_path)) {
                    echo "<script>console.log('파일 삭제 성공: " . $absolute_path . "');</script>";
                } else {
                    echo "<script>console.log('파일 삭제 실패: " . $absolute_path . "');</script>";
                }
            } else {
                echo "<script>console.log('파일이 존재하지 않음: " . $absolute_path . "');</script>";
            }
        }
        
        // 기존 파일 DB 삭제
        $delete_sql = "DELETE FROM inquiry_files WHERE inquiry_id=$inquiry_id";
        mysqli_query($conn, $delete_sql);
        
        // 새 파일 업로드
        $upload_dir = dirname(__DIR__) . '/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777);
        }
        
        $file_name = $_FILES['files']['name'];
        $file_tmp = $_FILES['files']['tmp_name'];
        $file_path = $upload_dir . $file_name;
        $db_file_path = 'uploads/' . $file_name;
        
        if (move_uploaded_file($file_tmp, $file_path)) {
            $insert_sql = "INSERT INTO inquiry_files (inquiry_id, file_name, file_path) VALUES ($inquiry_id, '$file_name', '$db_file_path')";
            mysqli_query($conn, $insert_sql);
        }
    }
    
    echo "<script>alert('수정되었습니다.'); location.href='../inquiry/inquiry_detail.php?inquiry_id=$inquiry_id';</script>";
} else {
    echo "<script>alert('수정 실패'); history.back();</script>";
}
?>