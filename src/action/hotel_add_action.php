<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

function generateSafeFilename($originalName) {
    $randomPrefix = bin2hex(random_bytes(4)); // 8자리 난수
    return $randomPrefix . '_' . basename($originalName);
}

// 관리자가 아닌 경우 CSRF 토큰 검증
if (!isset($_SESSION['is_admin'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        echo "<script>alert('잘못된 요청입니다.'); history.back();</script>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $conn->begin_transaction();

    try {
        // 기본 정보
        $name = $_POST['name'];
        $location = $_POST['location'];
        $description = $_POST['description'];
        $price_per_night = (int)$_POST['price_per_night'];
        $facilities = $_POST['facilities'] ?? [];

        $stmt = $conn->prepare("INSERT INTO hotels (name, location, description, price_per_night) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $location, $description, $price_per_night);
        $stmt->execute();
        $hotel_id = $stmt->insert_id;

        // 부대시설
        $pool = in_array('pool', $facilities) ? 1 : 0;
        $spa = in_array('spa', $facilities) ? 1 : 0;
        $fitness = in_array('fitness', $facilities) ? 1 : 0;
        $restaurant = in_array('restaurant', $facilities) ? 1 : 0;
        $parking = in_array('parking', $facilities) ? 1 : 0;
        $wifi = in_array('wifi', $facilities) ? 1 : 0;

        $stmt = $conn->prepare("INSERT INTO hotel_facilities (hotel_id, pool, spa, fitness, restaurant, parking, wifi) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiiiii", $hotel_id, $pool, $spa, $fitness, $restaurant, $parking, $wifi);
        $stmt->execute();

        $upload_dir = __DIR__ . '/../image/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // 메인 이미지
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
            $main_image_name = generateSafeFilename($_FILES['main_image']['name']);
            move_uploaded_file($_FILES['main_image']['tmp_name'], $upload_dir . $main_image_name);

            $stmt = $conn->prepare("UPDATE hotels SET main_image = ? WHERE hotel_id = ?");
            $path = '/image/' . $main_image_name;
            $stmt->bind_param("si", $path, $hotel_id);
            $stmt->execute();
        }

        // 상세 이미지
        for ($i = 1; $i <= 4; $i++) {
            $key = "detail_image_$i";
            if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                $filename = generateSafeFilename($_FILES[$key]['name']);
                move_uploaded_file($_FILES[$key]['tmp_name'], $upload_dir . $filename);

                $stmt = $conn->prepare("UPDATE hotels SET $key = ? WHERE hotel_id = ?");
                $path = '/image/' . $filename;
                $stmt->bind_param("si", $path, $hotel_id);
                $stmt->execute();
            }
        }

        // 디럭스 룸
        if (isset($_POST['deluxe_max_person'], $_POST['deluxe_price'])) {
            $max_person = (int)$_POST['deluxe_max_person'];
            $price = (int)$_POST['deluxe_price'];
            $image_path = '';

            if (isset($_FILES['deluxe_image']) && $_FILES['deluxe_image']['error'] === UPLOAD_ERR_OK) {
                $filename = generateSafeFilename($_FILES['deluxe_image']['name']);
                move_uploaded_file($_FILES['deluxe_image']['tmp_name'], $upload_dir . $filename);
                $image_path = '/image/' . $filename;
            }

            $stmt = $conn->prepare("INSERT INTO rooms (hotel_id, room_type, max_person, price, rooms_image, status) VALUES (?, 'deluxe', ?, ?, ?, 'available')");
            $stmt->bind_param("iiis", $hotel_id, $max_person, $price, $image_path);
            $stmt->execute();
        }

        // 스위트 룸
        if (isset($_POST['suite_max_person'], $_POST['suite_price'])) {
            $max_person = (int)$_POST['suite_max_person'];
            $price = (int)$_POST['suite_price'];
            $image_path = '';

            if (isset($_FILES['suite_image']) && $_FILES['suite_image']['error'] === UPLOAD_ERR_OK) {
                $filename = generateSafeFilename($_FILES['suite_image']['name']);
                move_uploaded_file($_FILES['suite_image']['tmp_name'], $upload_dir . $filename);
                $image_path = '/image/' . $filename;
            }

            $stmt = $conn->prepare("INSERT INTO rooms (hotel_id, room_type, max_person, price, rooms_image, status) VALUES (?, 'suite', ?, ?, ?, 'available')");
            $stmt->bind_param("iiis", $hotel_id, $max_person, $price, $image_path);
            $stmt->execute();
        }

        $conn->commit();

        echo "<script>
            alert('호텔이 성공적으로 추가되었습니다.');
            window.location.href = '../admin/admin.php?tab=hotels';
        </script>";
    } catch (Throwable $e) {
        $conn->rollback();
        error_log("[호텔 추가 오류] " . $e->getMessage());
        echo "<script>alert('호텔 추가 중 오류가 발생했습니다.'); history.back();</script>";
    }
}
?>