<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

$hotel_id = $_POST['hotel_id'];

// 호텔 기본 정보 업데이트
$name = $_POST['name'];
$location = $_POST['location'];
$description = $_POST['description'];
$price_per_night = $_POST['price_per_night'];

$sql = "UPDATE hotels SET 
        name = '$name',
        location = '$location',
        description = '$description',
        price_per_night = $price_per_night
        WHERE hotel_id = $hotel_id";

if (!$conn->query($sql)) {
    echo "호텔 정보 업데이트 실패: " . $conn->error;
    exit;
}

// 이미지 파일 처리
$upload_dir = "../image/";

// 메인 이미지 업데이트
if (isset($_FILES['main_image']) && $_FILES['main_image']['size'] > 0) {
    // 기존 파일 정보 가져오기
    $old_image_sql = "SELECT main_image FROM hotels WHERE hotel_id = $hotel_id";
    $old_image_result = $conn->query($old_image_sql);
    $old_image = $old_image_result->fetch_assoc();
    
    // 기존 파일이 있다면 삭제
    if ($old_image && $old_image['main_image']) {
        $old_file_path = __DIR__ . '/..' . $old_image['main_image'];
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }
    }
    
    $main_image = $_FILES['main_image'];
    $main_image_name = time() . '_' . $main_image['name'];
    move_uploaded_file($main_image['tmp_name'], $upload_dir . $main_image_name);
    $sql = "UPDATE hotels SET main_image = '/image/$main_image_name' WHERE hotel_id = $hotel_id";
    
    if (!$conn->query($sql)) {
        echo "호텔 메인 이미지 업데이트 실패: " . $conn->error;
        exit;
    }
}

// 상세 이미지 업데이트
for ($i = 1; $i <= 4; $i++) {
    $image_key = "detail_image_$i";
    if (isset($_FILES[$image_key]) && $_FILES[$image_key]['size'] > 0) {
        // 기존 파일 정보 가져오기
        $old_image_sql = "SELECT detail_image_$i FROM hotels WHERE hotel_id = $hotel_id";
        $old_image_result = $conn->query($old_image_sql);
        $old_image = $old_image_result->fetch_assoc();
        
        // 기존 파일이 있다면 삭제
        if ($old_image && $old_image["detail_image_$i"]) {
            $old_file_path = __DIR__ . '/..' . $old_image["detail_image_$i"];
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }
        }
        
        $detail_image = $_FILES[$image_key];
        $detail_image_name = time() . '_' . $detail_image['name'];
        move_uploaded_file($detail_image['tmp_name'], $upload_dir . $detail_image_name);
        $sql = "UPDATE hotels SET detail_image_$i = '/image/$detail_image_name' WHERE hotel_id = $hotel_id";
        
        if (!$conn->query($sql)) {
            echo "호텔 상세 이미지 업데이트 실패: " . $conn->error;
            exit;
        }
    }
}

// 부대시설 업데이트
$facilities = isset($_POST['facilities']) ? $_POST['facilities'] : [];
$pool = in_array('pool', $facilities) ? 1 : 0;
$spa = in_array('spa', $facilities) ? 1 : 0;
$fitness = in_array('fitness', $facilities) ? 1 : 0;
$restaurant = in_array('restaurant', $facilities) ? 1 : 0;
$parking = in_array('parking', $facilities) ? 1 : 0;
$wifi = in_array('wifi', $facilities) ? 1 : 0;

$facility_sql = "UPDATE hotel_facilities SET 
                 pool = $pool,
                 spa = $spa,
                 fitness = $fitness,
                 restaurant = $restaurant,
                 parking = $parking,
                 wifi = $wifi
                 WHERE hotel_id = $hotel_id";

if (!$conn->query($facility_sql)) {
    echo "호텔 부대시설 업데이트 실패: " . $conn->error;
    exit;
}

// 디럭스 룸 정보 업데이트
$deluxe_max_person = $_POST['deluxe_max_person'];
$deluxe_price = $_POST['deluxe_price'];

$deluxe_sql = "UPDATE rooms SET 
               max_person = $deluxe_max_person,
               price = $deluxe_price
               WHERE hotel_id = $hotel_id AND room_type = 'deluxe'";

if (!$conn->query($deluxe_sql)) {
    echo "디럭스 룸 정보 업데이트 실패: " . $conn->error;
    exit;
}

if (isset($_FILES['deluxe_image']) && $_FILES['deluxe_image']['size'] > 0) {
    $old_image_sql = "SELECT rooms_image FROM rooms WHERE hotel_id = $hotel_id AND room_type = 'deluxe'";
    $old_image_result = $conn->query($old_image_sql);
    $old_image = $old_image_result->fetch_assoc();
    
    // 기존 파일 삭제
    if ($old_image && $old_image['rooms_image']) {
        $old_file_path = __DIR__ . '/..' . $old_image['rooms_image'];
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }
    }
    
    $deluxe_image = $_FILES['deluxe_image'];
    $deluxe_image_name = time() . '_' . $deluxe_image['name'];
    move_uploaded_file($deluxe_image['tmp_name'], $upload_dir . $deluxe_image_name);
    $deluxe_sql = "UPDATE rooms SET rooms_image = '/image/$deluxe_image_name' WHERE hotel_id = $hotel_id AND room_type = 'deluxe'";
    
    if (!$conn->query($deluxe_sql)) {
        echo "디럭스 룸 이미지 업데이트 실패: " . $conn->error;
        exit;
    }
}

// 스위트 룸 정보 업데이트
$suite_max_person = $_POST['suite_max_person'];
$suite_price = $_POST['suite_price'];

$suite_sql = "UPDATE rooms SET 
              max_person = $suite_max_person,
              price = $suite_price
              WHERE hotel_id = $hotel_id AND room_type = 'suite'";

if (!$conn->query($suite_sql)) {
    echo "스위트 룸 정보 업데이트 실패: " . $conn->error;
    exit;
}

if (isset($_FILES['suite_image']) && $_FILES['suite_image']['size'] > 0) {
    $old_image_sql = "SELECT rooms_image FROM rooms WHERE hotel_id = $hotel_id AND room_type = 'suite'";
    $old_image_result = $conn->query($old_image_sql);
    $old_image = $old_image_result->fetch_assoc();
    
    // 기존 파일 삭제
    if ($old_image && $old_image['rooms_image']) {
        $old_file_path = __DIR__ . '/..' . $old_image['rooms_image'];
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }
    }
    
    $suite_image = $_FILES['suite_image'];
    $suite_image_name = time() . '_' . $suite_image['name'];
    move_uploaded_file($suite_image['tmp_name'], $upload_dir . $suite_image_name);
    $suite_sql = "UPDATE rooms SET rooms_image = '/image/$suite_image_name' WHERE hotel_id = $hotel_id AND room_type = 'suite'";
    
    if (!$conn->query($suite_sql)) {
        echo "스위트 룸 이미지 업데이트 실패: " . $conn->error;
        exit;
    }
}

echo "<script>alert('호텔 정보가 수정되었습니다.'); window.location.href = '../admin/admin.php?tab=hotels';</script>";
exit;
?>