<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

// 관리자가 아닌 경우 CSRF 토큰 검증
if (!isset($_SESSION['is_admin'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        echo "<script>alert('잘못된 요청입니다.'); history.back();</script>";
        exit;
    }
}

$action_type = null;
$table_name = null;
$id_name = null;

foreach ($_POST as $key => $value) {
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
    $safe_table = htmlspecialchars($table_name);
    $safe_id = htmlspecialchars($id);

    if ($table_name === 'reservations') {
        if (!isset($_POST['room_id'])) {
            echo "<script>
                alert('방 정보가 없습니다.');
                window.location.href = '../admin/admin.php?tab={$safe_table}';
            </script>";
            exit;
        }

        $room_id = intval($_POST['room_id']);

        $stmt = $conn->prepare("UPDATE reservations SET status = 'cancel' WHERE reservation_id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();

        $stmt2 = $conn->prepare("UPDATE rooms SET status = 'available' WHERE room_id = ?");
        $stmt2->bind_param("i", $room_id);
        $result2 = $stmt2->execute();

        if ($result && $result2) {
            echo "<script>
                alert('{$safe_id} 번 예약이 취소되었습니다.');
                window.location.href = '../admin/admin.php?tab={$safe_table}';
            </script>";
        }

    } else if ($table_name === 'hotels') {
        $stmt = $conn->prepare("SELECT main_image, detail_image_1, detail_image_2, detail_image_3, detail_image_4 FROM hotels WHERE hotel_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $hotel = $result->fetch_assoc();

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

        $stmt = $conn->prepare("DELETE FROM $table_name WHERE $id_name = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();

        if ($result) {
            echo "<script>
                alert('{$safe_id} 번이 삭제되었습니다.');
                window.location.href = '../admin/admin.php?tab={$safe_table}';
            </script>";
        } else {
            echo "<script>
                alert('존재하지 않는 {$safe_table} 입니다.');
                window.location.href = '../admin/admin.php?tab={$safe_table}';
            </script>";
        }

    } else if ($table_name === 'coupons') {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM user_coupons WHERE coupon_id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $check_result = $stmt->get_result();
        $check_row = $check_result->fetch_assoc();

        if ($check_row['count'] > 0) {
            echo "<script>
                alert('사용 중인 쿠폰은 삭제할 수 없습니다.');
                window.location.href = '../admin/admin.php?tab={$safe_table}';
            </script>";
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM $table_name WHERE $id_name = ?");
        $stmt->bind_param("s", $id);
        $result = $stmt->execute();

        if ($result) {
            echo "<script>
                alert('쿠폰이 삭제되었습니다.');
                window.location.href = '../admin/admin.php?tab={$safe_table}';
            </script>";
        } else {
            echo "<script>
                alert('쿠폰 삭제 중 오류가 발생했습니다.');
                window.location.href = '../admin/admin.php?tab={$safe_table}';
            </script>";
        }

    } else {
        $param_type = is_numeric($id) ? "i" : "s";
        $stmt = $conn->prepare("DELETE FROM $table_name WHERE $id_name = ?");
        $stmt->bind_param($param_type, $id);
        $result = $stmt->execute();

        if ($result) {
            echo "<script>
                alert('{$safe_id} 번이 삭제되었습니다.');
                window.location.href = '../admin/admin.php?tab={$safe_table}';
            </script>";
        } else {
            echo "<script>
                alert('존재하지 않는 {$safe_table} 입니다.');
                window.location.href = '../admin/admin.php?tab={$safe_table}';
            </script>";
        }
    }
}
?>