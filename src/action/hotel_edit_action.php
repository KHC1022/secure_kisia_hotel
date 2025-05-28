<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

function getSafeFilename($originalName) {
    $originalName = basename($originalName); // 경로 제거
    $randomPrefix = bin2hex(random_bytes(5)); // 랜덤값
    return $randomPrefix . '_' . $originalName;
}

// 관리자가 아닌 경우 CSRF 토큰 검증
if (!isset($_SESSION['is_admin'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        echo "<script>alert('잘못된 요청입니다.'); history.back();</script>";
        exit;
    }
}

$hotel_id = (int)$_POST['hotel_id'];

// 호텔 기본 정보 업데이트
$name = $_POST['name'];
$location = $_POST['location'];
$description = $_POST['description'];
$price_per_night = (int)$_POST['price_per_night'];

$stmt = $conn->prepare("UPDATE hotels SET name = ?, location = ?, description = ?, price_per_night = ? WHERE hotel_id = ?");
$stmt->bind_param("sssii", $name, $location, $description, $price_per_night, $hotel_id);
if (!$stmt->execute()) {
    echo "<script>alert('호텔 기본 정보 업데이트 실패'); history.back();</script>";
    exit;
}

$upload_dir = "../image/";

// 메인 이미지
if (isset($_FILES['main_image']) && $_FILES['main_image']['size'] > 0) {
    $old = $conn->prepare("SELECT main_image FROM hotels WHERE hotel_id = ?");
    $old->bind_param("i", $hotel_id);
    $old->execute();
    $result = $old->get_result()->fetch_assoc();
    if ($result && $result['main_image']) {
        $path = __DIR__ . '/..' . $result['main_image'];
        if (file_exists($path)) unlink($path);
    }

    $safeName = getSafeFilename($_FILES['main_image']['name']);
    move_uploaded_file($_FILES['main_image']['tmp_name'], $upload_dir . $safeName);

    $img_path = '/image/' . $safeName;
    $stmt = $conn->prepare("UPDATE hotels SET main_image = ? WHERE hotel_id = ?");
    $stmt->bind_param("si", $img_path, $hotel_id);
    $stmt->execute();
}

// 상세 이미지 1~4
for ($i = 1; $i <= 4; $i++) {
    $key = "detail_image_$i";
    if (isset($_FILES[$key]) && $_FILES[$key]['size'] > 0) {
        $old = $conn->prepare("SELECT $key FROM hotels WHERE hotel_id = ?");
        $old->bind_param("i", $hotel_id);
        $old->execute();
        $result = $old->get_result()->fetch_assoc();
        if ($result && $result[$key]) {
            $path = __DIR__ . '/..' . $result[$key];
            if (file_exists($path)) unlink($path);
        }

        $safeName = getSafeFilename($_FILES[$key]['name']);
        move_uploaded_file($_FILES[$key]['tmp_name'], $upload_dir . $safeName);

        $img_path = '/image/' . $safeName;
        $stmt = $conn->prepare("UPDATE hotels SET $key = ? WHERE hotel_id = ?");
        $stmt->bind_param("si", $img_path, $hotel_id);
        $stmt->execute();
    }
}

// 부대시설
$facilities = $_POST['facilities'] ?? [];
$pool = in_array('pool', $facilities) ? 1 : 0;
$spa = in_array('spa', $facilities) ? 1 : 0;
$fitness = in_array('fitness', $facilities) ? 1 : 0;
$restaurant = in_array('restaurant', $facilities) ? 1 : 0;
$parking = in_array('parking', $facilities) ? 1 : 0;
$wifi = in_array('wifi', $facilities) ? 1 : 0;

$stmt = $conn->prepare("UPDATE hotel_facilities SET pool = ?, spa = ?, fitness = ?, restaurant = ?, parking = ?, wifi = ? WHERE hotel_id = ?");
$stmt->bind_param("iiiiiii", $pool, $spa, $fitness, $restaurant, $parking, $wifi, $hotel_id);
$stmt->execute();

// 디럭스 룸
$deluxe_max = (int)$_POST['deluxe_max_person'];
$deluxe_price = (int)$_POST['deluxe_price'];
$stmt = $conn->prepare("UPDATE rooms SET max_person = ?, price = ? WHERE hotel_id = ? AND room_type = 'deluxe'");
$stmt->bind_param("iii", $deluxe_max, $deluxe_price, $hotel_id);
$stmt->execute();

if (isset($_FILES['deluxe_image']) && $_FILES['deluxe_image']['size'] > 0) {
    $old = $conn->prepare("SELECT rooms_image FROM rooms WHERE hotel_id = ? AND room_type = 'deluxe'");
    $old->bind_param("i", $hotel_id);
    $old->execute();
    $result = $old->get_result()->fetch_assoc();
    if ($result && $result['rooms_image']) {
        $path = __DIR__ . '/..' . $result['rooms_image'];
        if (file_exists($path)) unlink($path);
    }

    $safeName = getSafeFilename($_FILES['deluxe_image']['name']);
    move_uploaded_file($_FILES['deluxe_image']['tmp_name'], $upload_dir . $safeName);

    $img_path = '/image/' . $safeName;
    $stmt = $conn->prepare("UPDATE rooms SET rooms_image = ? WHERE hotel_id = ? AND room_type = 'deluxe'");
    $stmt->bind_param("si", $img_path, $hotel_id);
    $stmt->execute();
}

// 스위트 룸
$suite_max = (int)$_POST['suite_max_person'];
$suite_price = (int)$_POST['suite_price'];
$stmt = $conn->prepare("UPDATE rooms SET max_person = ?, price = ? WHERE hotel_id = ? AND room_type = 'suite'");
$stmt->bind_param("iii", $suite_max, $suite_price, $hotel_id);
$stmt->execute();

if (isset($_FILES['suite_image']) && $_FILES['suite_image']['size'] > 0) {
    $old = $conn->prepare("SELECT rooms_image FROM rooms WHERE hotel_id = ? AND room_type = 'suite'");
    $old->bind_param("i", $hotel_id);
    $old->execute();
    $result = $old->get_result()->fetch_assoc();
    if ($result && $result['rooms_image']) {
        $path = __DIR__ . '/..' . $result['rooms_image'];
        if (file_exists($path)) unlink($path);
    }

    $safeName = getSafeFilename($_FILES['suite_image']['name']);
    move_uploaded_file($_FILES['suite_image']['tmp_name'], $upload_dir . $safeName);

    $img_path = '/image/' . $safeName;
    $stmt = $conn->prepare("UPDATE rooms SET rooms_image = ? WHERE hotel_id = ? AND room_type = 'suite'");
    $stmt->bind_param("si", $img_path, $hotel_id);
    $stmt->execute();
}

echo "<script>alert('호텔 정보가 수정되었습니다.'); window.location.href = '../admin/admin.php?tab=hotels';</script>";
exit;
?>