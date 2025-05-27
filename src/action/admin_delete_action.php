<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

$action_type = null;
$table_name = null;
$id_name = null;

foreach ($_GET as $key => $value) {
    if (strpos($key, '_') !== false) {
        list($prefix, $action) = explode('_', $key);
        
        switch ($prefix) {
            case 'user':
                $table_name = 'users';
                $id_name = 'user_id';
                break;
            case 'hotel':
                $table_name = 'hotels';
                $id_name = 'hotel_id';
                break;
            case 'reservation':
                $table_name = 'reservations';
                $id_name = 'reservation_id';
                break;
            case 'review':
                $table_name = 'reviews';
                $id_name = 'review_id';
                break;
            case 'inquiry':
                $table_name = 'inquiries';
                $id_name = 'inquiry_id';
                break;
            case 'notice':
                $table_name = 'notices';
                $id_name = 'notice_id';
                break;
            case 'coupon':
                $table_name = 'coupons';
                $id_name = 'code';
                break;
        }
        
        if ($action === 'edit' || $action === 'delete') {
            $action_type = $action;
            $id = $value;
            break;
        }
    }
}

if ($action_type === 'delete') {
    if ($table_name === 'reservations') {
        if (!isset($_GET['room_id'])) {
            echo "<script>
                alert('방 정보가 없습니다.');
                window.location.href = '../admin/admin.php?tab=$table_name';
            </script>";
            exit;
        }
        
        $room_id = $_GET['room_id'];
        $sql = "UPDATE reservations SET status = 'cancel' WHERE reservation_id = $id";
        $sql2 = "UPDATE rooms SET status = 'available' WHERE room_id = $room_id";
        $result = mysqli_query($conn, $sql);
        $result2 = mysqli_query($conn, $sql2);

        if ($result && $result2) {
            echo "<script>
                alert('$id 번 예약이 취소되었습니다.');
                window.location.href = '../admin/admin.php?tab=$table_name';
            </script>";
        }
    }
    else if ($table_name === 'hotels') {
        $sql = "SELECT main_image, detail_image_1, detail_image_2, detail_image_3, detail_image_4 FROM hotels WHERE hotel_id = $id";
        $result = mysqli_query($conn, $sql);
        $hotel = mysqli_fetch_assoc($result);

        $images = [
            $hotel['main_image'],
            $hotel['detail_image_1'],
            $hotel['detail_image_2'],
            $hotel['detail_image_3'],
            $hotel['detail_image_4']
        ];

        foreach ($images as $image) {
            if (!empty($image)) {
                $file_path = __DIR__ . '/..' . $image;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }

        $sql = "DELETE FROM $table_name WHERE $id_name = $id";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            echo "<script>
                alert('$id 번이 삭제되었습니다.');
                window.location.href = '../admin/admin.php?tab=$table_name';
            </script>";
        } else {
            echo "<script>
                alert('존재하지 않는 $table_name 입니다.');
                window.location.href = '../admin/admin.php?tab=$table_name';
            </script>";
        }
    }
    else if ($table_name === 'coupons') {
        // 쿠폰 삭제 전 사용 중인 쿠폰인지 확인
        $check_sql = "SELECT COUNT(*) as count FROM user_coupons WHERE coupon_id = '$id'";
        $check_result = mysqli_query($conn, $check_sql);
        $check_row = mysqli_fetch_assoc($check_result);
        
        if ($check_row['count'] > 0) {
            echo "<script>
                    alert('사용 중인 쿠폰은 삭제할 수 없습니다.');
                    window.location.href = '../admin/admin.php?tab=$table_name';
                  </script>";
            exit;
        }
        
        $sql = "DELETE FROM $table_name WHERE $id_name = '$id'";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            echo "<script>
                    alert('쿠폰이 삭제되었습니다.');
                    window.location.href = '../admin/admin.php?tab=$table_name';
                  </script>";
        } else {
            echo "<script>
                    alert('쿠폰 삭제 중 오류가 발생했습니다.');
                    window.location.href = '../admin/admin.php?tab=$table_name';
                  </script>";
        }
    }
    else {
        $sql = "DELETE FROM $table_name WHERE $id_name = $id";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            echo "<script>
                alert('$id 번이 삭제되었습니다.');
                window.location.href = '../admin/admin.php?tab=$table_name';
            </script>";
        } else {
            echo "<script>
                alert('존재하지 않는 $table_name 입니다.');
                window.location.href = '../admin/admin.php?tab=$table_name';
            </script>";
        }
    }
}
?>