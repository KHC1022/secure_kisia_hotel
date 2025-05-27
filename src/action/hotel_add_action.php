<?php
include_once __DIR__ . '/../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 호텔 기본 정보
    $name = $_POST['name'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $price_per_night = $_POST['price_per_night'];
    $facilities = isset($_POST['facilities']) ? $_POST['facilities'] : [];

    $sql = "INSERT INTO hotels (name, location, description, price_per_night) 
            VALUES ('$name', '$location', '$description', $price_per_night)";
    
    if (!$conn->query($sql)) {
        echo "호텔 정보 저장 실패: " . $conn->error;
        exit;
    }
    
    $hotel_id = $conn->insert_id;
    
    // 부대시설
    $pool = in_array('pool', $facilities) ? 1 : 0;
    $spa = in_array('spa', $facilities) ? 1 : 0;
    $fitness = in_array('fitness', $facilities) ? 1 : 0;
    $restaurant = in_array('restaurant', $facilities) ? 1 : 0;
    $parking = in_array('parking', $facilities) ? 1 : 0;
    $wifi = in_array('wifi', $facilities) ? 1 : 0;

    $facility_sql = "INSERT INTO hotel_facilities (hotel_id, pool, spa, fitness, restaurant, parking, wifi) 
                    VALUES ($hotel_id, $pool, $spa, $fitness, $restaurant, $parking, $wifi)";
    
    if (!$conn->query($facility_sql)) {
        echo "부대시설 저장 실패: " . $conn->error;
        exit;
    }

    // 이미지 업로드 디렉토리 설정
    $upload_dir = __DIR__ . '/../image/';
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            echo "업로드 디렉토리 생성 실패";
            exit;
        }
    }

    // 호텔 메인 이미지 업로드
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $main_image = $_FILES['main_image'];
        $main_image_name = time() . '_' . $main_image['name'];
        if (!move_uploaded_file($main_image['tmp_name'], $upload_dir . $main_image_name)) {
            echo "메인 이미지 업로드 실패";
            exit;
        }
        
        $sql = "UPDATE hotels SET main_image = '/image/$main_image_name' WHERE hotel_id = $hotel_id";
        if (!$conn->query($sql)) {
            echo "메인 이미지 정보 저장 실패: " . $conn->error;
            exit;
        }
    }

    // 호텔 상세 이미지 업로드
    for ($i = 1; $i <= 4; $i++) {
        $image_key = "detail_image_$i";
        if (isset($_FILES[$image_key]) && $_FILES[$image_key]['error'] === UPLOAD_ERR_OK) {
            $detail_image = $_FILES[$image_key];
            $detail_image_name = time() . '_' . $detail_image['name'];
            if (!move_uploaded_file($detail_image['tmp_name'], $upload_dir . $detail_image_name)) {
                echo "상세 이미지 $i 업로드 실패";
                exit;
            }
            
            $sql = "UPDATE hotels SET $image_key = '/image/$detail_image_name' WHERE hotel_id = $hotel_id";
            if (!$conn->query($sql)) {
                echo "상세 이미지 $i 정보 저장 실패: " . $conn->error;
                exit;
            }
        }
    }

    // 디럭스 룸 정보
    if (isset($_POST['deluxe_max_person']) && isset($_POST['deluxe_price'])) {
        $deluxe_image_name = '';
        if (isset($_FILES['deluxe_image']) && $_FILES['deluxe_image']['error'] === UPLOAD_ERR_OK) {
            $deluxe_image = $_FILES['deluxe_image'];
            $deluxe_image_name = time() . '_' . $deluxe_image['name'];
            if (!move_uploaded_file($deluxe_image['tmp_name'], $upload_dir . $deluxe_image_name)) {
                echo "디럭스 룸 이미지 업로드 실패";
                exit;
            }
        }

        $deluxe_price = (int)$_POST['deluxe_price'];
        $deluxe_sql = "INSERT INTO rooms (hotel_id, room_type, max_person, price, rooms_image, status) 
                      VALUES ($hotel_id, 'deluxe', {$_POST['deluxe_max_person']}, $deluxe_price, '/image/$deluxe_image_name', 'available')";
        if (!$conn->query($deluxe_sql)) {
            echo "디럭스 룸 정보 저장 실패: " . $conn->error;
            exit;
        }
    }

    // 스위트 룸 정보
    if (isset($_POST['suite_max_person']) && isset($_POST['suite_price'])) {
        $suite_image_name = '';
        if (isset($_FILES['suite_image']) && $_FILES['suite_image']['error'] === UPLOAD_ERR_OK) {
            $suite_image = $_FILES['suite_image'];
            $suite_image_name = time() . '_' . $suite_image['name'];
            if (!move_uploaded_file($suite_image['tmp_name'], $upload_dir . $suite_image_name)) {
                echo "스위트 룸 이미지 업로드 실패";
                exit;
            }
        }

        $suite_price = (int)$_POST['suite_price'];
        $suite_sql = "INSERT INTO rooms (hotel_id, room_type, max_person, price, rooms_image, status) 
                     VALUES ($hotel_id, 'suite', {$_POST['suite_max_person']}, $suite_price, '/image/$suite_image_name', 'available')";
        if (!$conn->query($suite_sql)) {
            echo "스위트 룸 정보 저장 실패: " . $conn->error;
            exit;
        }
    }

    echo "<script>
            alert('호텔이 성공적으로 추가되었습니다.');
            window.location.href = '../admin/admin.php?tab=hotels';
          </script>";
}
?>