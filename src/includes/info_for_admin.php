<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

// 현재 탭 종류 확인
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'users';

// 페이징 설정
$items_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// 회원 관리 탭
if ($current_tab === 'users') {
    // 검색어가 있는 경우
    if (isset($_GET['user_name_search']) && trim($_GET['user_name_search']) !== '') {
        $keyword = trim($_GET['user_name_search']);
        
        $count_sql = "SELECT COUNT(*) as total FROM users WHERE username LIKE '%$keyword%'";
        $count_result = $conn->query($count_sql);
        $total_items = $count_result->fetch_assoc()['total'];
        $total_pages = ceil($total_items / $items_per_page);
        
        $sql = "SELECT * FROM users WHERE username LIKE '%$keyword%' ORDER BY user_id ASC LIMIT $offset, $items_per_page";
        $result = $conn->query($sql);
        
        $users = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
    } 
    // 검색어 없는 경우 전체 목록
    else {
        $count_sql = "SELECT COUNT(*) as total FROM users";
        $count_result = $conn->query($count_sql);
        $total_items = $count_result->fetch_assoc()['total'];
        $total_pages = ceil($total_items / $items_per_page);
        
        $sql = "SELECT * FROM users ORDER BY user_id ASC LIMIT $offset, $items_per_page";
        $result = $conn->query($sql);
        
        $users = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
    }
}

// 호텔 관리 탭
else if ($current_tab === 'hotels') {
    // 검색어가 있는 경우
    if (isset($_GET['hotel_name_search']) && trim($_GET['hotel_name_search']) !== '') {
        $keyword = trim($_GET['hotel_name_search']);
        
        $count_sql = "SELECT COUNT(DISTINCT h.hotel_id) as total FROM hotels h WHERE h.name LIKE '%$keyword%'";
        $count_result = $conn->query($count_sql);
        $total_items = $count_result->fetch_assoc()['total'];
        $total_pages = ceil($total_items / $items_per_page);
        
        $sql = "SELECT h.hotel_id, h.name, h.location, 
                COUNT(r.room_id) as room_count,
                SUM(CASE WHEN r.status = 'available' THEN 1 ELSE 0 END) as available_room_count
                FROM hotels h 
                LEFT JOIN rooms r ON h.hotel_id = r.hotel_id 
                WHERE h.name LIKE '%$keyword%' 
                GROUP BY h.hotel_id 
                ORDER BY h.hotel_id ASC 
                LIMIT $offset, $items_per_page";
        $result = $conn->query($sql);
        
        $hotels = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $hotels[] = $row;
            }
        }
    } 
    // 검색어 없는 경우 전체 목록
    else {
        $count_sql = "SELECT COUNT(*) as total FROM hotels";
        $count_result = $conn->query($count_sql);
        $total_items = $count_result->fetch_assoc()['total'];
        $total_pages = ceil($total_items / $items_per_page);
        
        $sql = "SELECT h.hotel_id, h.name, h.location, 
                COUNT(r.room_id) as room_count,
                SUM(CASE WHEN r.status = 'available' THEN 1 ELSE 0 END) as available_room_count
                FROM hotels h 
                LEFT JOIN rooms r ON h.hotel_id = r.hotel_id 
                GROUP BY h.hotel_id 
                ORDER BY h.hotel_id ASC 
                LIMIT $offset, $items_per_page";
        $result = $conn->query($sql);
        
        $hotels = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $hotels[] = $row;
            }
        }
    }
}

// 예약 관리 탭
else if ($current_tab === 'reservations') {
    // 검색어가 있는 경우
    if (isset($_GET['reservation_number_search']) && trim($_GET['reservation_number_search']) !== '') {
        $keyword = (int)trim($_GET['reservation_number_search']);
        
        $count_sql = "SELECT COUNT(*) as total FROM reservations WHERE reservation_id = $keyword";
        $count_result = $conn->query($count_sql);
        $total_items = $count_result->fetch_assoc()['total']; 
        $total_pages = ceil($total_items / $items_per_page);
        
        $sql = "SELECT r.*, h.name, u.username 
                FROM reservations r 
                JOIN rooms rm ON r.room_id = rm.room_id 
                JOIN hotels h ON rm.hotel_id = h.hotel_id 
                JOIN users u ON r.user_id = u.user_id 
                WHERE r.reservation_id = $keyword 
                ORDER BY r.reservation_id ASC 
                LIMIT $offset, $items_per_page";
        $result = $conn->query($sql);
        
        $reservations = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $reservations[] = $row;
            }
        }
    }
    // 검색어 없는 경우 전체 목록
    else {
        $count_sql = "SELECT COUNT(*) as total FROM reservations";
        $count_result = $conn->query($count_sql);
        $total_items = $count_result->fetch_assoc()['total'];
        $total_pages = ceil($total_items / $items_per_page);
        
        $sql = "SELECT r.*, h.name, u.username 
                FROM reservations r 
                JOIN rooms rm ON r.room_id = rm.room_id 
                JOIN hotels h ON rm.hotel_id = h.hotel_id 
                JOIN users u ON r.user_id = u.user_id 
                ORDER BY r.reservation_id ASC 
                LIMIT $offset, $items_per_page";
        $result = $conn->query($sql);
        
        $reservations = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $reservations[] = $row;
            }
        }
    }
}

// 후기 관리 탭
else if ($current_tab === 'reviews') {
    // 검색어가 있는 경우
    if (isset($_GET['review_hotel_search']) && trim($_GET['review_hotel_search']) !== '') {
        $keyword = trim($_GET['review_hotel_search']);
        
        $count_sql = "SELECT COUNT(*) as total 
                      FROM reviews r 
                      LEFT JOIN hotels h ON r.hotel_id = h.hotel_id 
                      WHERE h.name LIKE '%$keyword%'";
        $count_result = $conn->query($count_sql);
        $total_items = $count_result->fetch_assoc()['total'];
        $total_pages = ceil($total_items / $items_per_page);
        
        $sql = "SELECT r.*, h.name as hotel_name, u.username 
                FROM reviews r 
                LEFT JOIN hotels h ON r.hotel_id = h.hotel_id 
                LEFT JOIN users u ON r.user_id = u.user_id 
                WHERE h.name LIKE '%$keyword%' 
                ORDER BY r.review_id ASC 
                LIMIT $offset, $items_per_page";
        $result = $conn->query($sql);
        
        $reviews = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $reviews[] = $row;
            }
        }
    } 
    // 검색어 없는 경우 전체 목록
    else {
        $count_sql = "SELECT COUNT(*) as total FROM reviews";
        $count_result = $conn->query($count_sql);
        $total_items = $count_result->fetch_assoc()['total'];
        $total_pages = ceil($total_items / $items_per_page);
        
        $sql = "SELECT r.*, h.name as hotel_name, u.username 
                FROM reviews r 
                LEFT JOIN hotels h ON r.hotel_id = h.hotel_id 
                LEFT JOIN users u ON r.user_id = u.user_id 
                ORDER BY r.review_id ASC 
                LIMIT $offset, $items_per_page";
        $result = $conn->query($sql);
        
        $reviews = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $reviews[] = $row;
            }
        }
    }
}

// 문의 관리 탭
else if ($current_tab === 'inquiries') {
    // 검색어가 있는 경우
    if (isset($_GET['inquiry_number_search']) && trim($_GET['inquiry_number_search']) !== '') {
        $keyword = (int)trim($_GET['inquiry_number_search']);
        
        $count_sql = "SELECT COUNT(*) as total FROM inquiries WHERE inquiry_id = $keyword";
        $count_result = $conn->query($count_sql);
        $total_items = $count_result->fetch_assoc()['total'];
        $total_pages = ceil($total_items / $items_per_page);
        
        $sql = "SELECT i.*, u.username, 
                CASE WHEN ir.response_id IS NOT NULL THEN 1 ELSE 0 END as is_answered 
                FROM inquiries i 
                LEFT JOIN users u ON i.user_id = u.user_id 
                LEFT JOIN inquiry_responses ir ON i.inquiry_id = ir.inquiry_id 
                WHERE i.inquiry_id = $keyword 
                ORDER BY i.created_at DESC 
                LIMIT $offset, $items_per_page";
        $result = $conn->query($sql);
        
        $inquiries = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $inquiries[] = $row;
            }
        }
    } 
    // 검색어 없는 경우 전체 목록
    else {
        $count_sql = "SELECT COUNT(*) as total FROM inquiries";
        $count_result = $conn->query($count_sql);
        $total_items = $count_result->fetch_assoc()['total'];
        $total_pages = ceil($total_items / $items_per_page);
        
        $sql = "SELECT i.*, u.username, 
                CASE WHEN ir.response_id IS NOT NULL THEN 1 ELSE 0 END as is_answered 
                FROM inquiries i 
                LEFT JOIN users u ON i.user_id = u.user_id 
                LEFT JOIN inquiry_responses ir ON i.inquiry_id = ir.inquiry_id 
                ORDER BY i.inquiry_id ASC 
                LIMIT $offset, $items_per_page";
        $result = $conn->query($sql);
        
        $inquiries = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $inquiries[] = $row;
            }
        }
    }
}

// 공지 관리 탭
else if ($current_tab === 'notices') {
    // 검색어가 있는 경우
    if (isset($_GET['notice_title_search']) && trim($_GET['notice_title_search']) !== '') {
        $keyword = trim($_GET['notice_title_search']);

        $count_sql = "SELECT COUNT(*) as total FROM notices WHERE title LIKE '%$keyword%'";
        $count_result = $conn->query($count_sql);
        $total_items = $count_result->fetch_assoc()['total'];
        $total_pages = ceil($total_items / $items_per_page);

        $sql = "SELECT n.*, u.username 
                FROM notices n 
                JOIN users u ON n.user_id = u.user_id 
                WHERE n.title LIKE '%$keyword%' 
                ORDER BY n.notice_id DESC 
                LIMIT $offset, $items_per_page";
        $result = $conn->query($sql);

        $notices = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $notices[] = $row;
            }
        }
    }
    // 검색어 없는 경우 전체 목록
    else {
        $count_sql = "SELECT COUNT(*) as total FROM notices";
        $count_result = $conn->query($count_sql);
        $total_items = $count_result->fetch_assoc()['total'];
        $total_pages = ceil($total_items / $items_per_page);

        $sql = "SELECT n.*, u.username 
                FROM notices n 
                JOIN users u ON n.user_id = u.user_id 
                ORDER BY n.notice_id DESC 
                LIMIT $offset, $items_per_page";
        $result = $conn->query($sql);

        $notices = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $notices[] = $row;
            }
        }
    }
}

// 쿠폰 관리 데이터 가져오기
if (isset($_GET['tab']) && $_GET['tab'] == 'coupons' || isset($_GET['search']) && $_GET['search'] == 'coupon_code_search') {
    $items_per_page = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $page = max(1, $page);
    $offset = ($page - 1) * $items_per_page;

    if (isset($_GET['search']) && $_GET['search'] == 'coupon_code_search' && !empty($_GET['coupon_code_search'])) {
        $search = trim($_GET['coupon_code_search']);
        $count_sql = "SELECT COUNT(*) as total FROM coupons WHERE code LIKE '%$search%' OR name LIKE '%$search%'";
        $sql = "SELECT * FROM coupons WHERE code LIKE '%$search%' OR name LIKE '%$search%' ORDER BY created_at DESC LIMIT $offset, $items_per_page";
    } else {
        $count_sql = "SELECT COUNT(*) as total FROM coupons";
        $sql = "SELECT * FROM coupons ORDER BY created_at DESC LIMIT $offset, $items_per_page";
    }

    $count_result = mysqli_query($conn, $count_sql);
    $total_items = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total_items / $items_per_page);

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die('쿠폰 정보 조회 오류: ' . mysqli_error($conn));
    }
    $coupons = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// 검색 타입에 따른 탭 결정
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search_type = $_GET['search'];
    $keyword = isset($_GET[$search_type]) ? trim($_GET[$search_type]) : '';
    
    if ($keyword === '') {
        echo "<script>
                alert('검색어를 입력하세요');
                history.back();
              </script>";
        exit;
    }
    
    $tab = '';
    switch ($search_type) {
        case 'user_name_search':
            $tab = 'users';
            break;
        case 'hotel_name_search':
            $tab = 'hotels';
            break;
        case 'reservation_number_search':
            $tab = 'reservations';
            break;
        case 'review_hotel_search':
            $tab = 'reviews';
            break;
        case 'inquiry_number_search':
            $tab = 'inquiries';
            break;
        case 'notice_title_search':
            $tab = 'notices';
            break;
    }
    
    if ($tab !== '') {
        header("Location: ../admin/admin.php?tab=$tab&page=1&$search_type=$keyword");
        exit;
    }
}

?> 