-- 0. 데이터베이스 생성 및 사용
CREATE DATABASE IF NOT EXISTS kisia_hotel DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kisia_hotel;

-- 1. users (회원 정보)
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    real_id VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_admin BOOLEAN DEFAULT FALSE,
    terms BOOLEAN DEFAULT FALSE,
    marketing BOOLEAN DEFAULT FALSE,
    profile_image VARCHAR(255) DEFAULT '/image/default_profile.jpg',
    point INT DEFAULT 0,
    vip BOOLEAN DEFAULT FALSE,
    vip_status ENUM('auto', 'manual') DEFAULT 'auto',
    login_attempts INT DEFAULT 0,
    last_failed_at DATETIME DEFAULT NULL
);


-- 2. hotels (호텔 정보)
CREATE TABLE hotels (
    hotel_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    location VARCHAR(200) NOT NULL,
    description TEXT,
    price_per_night INT NOT NULL,
    rating DECIMAL(2,1),
    main_image VARCHAR(255),
    detail_image_1 VARCHAR(255),
    detail_image_2 VARCHAR(255),
    detail_image_3 VARCHAR(255),
    detail_image_4 VARCHAR(255)
);

-- 3. hotel_facilities (호텔 부대시설)
CREATE TABLE hotel_facilities (
    hotel_id INT PRIMARY KEY,
    pool BOOLEAN DEFAULT FALSE,
    spa BOOLEAN DEFAULT FALSE,
    fitness BOOLEAN DEFAULT FALSE,
    restaurant BOOLEAN DEFAULT FALSE,
    parking BOOLEAN DEFAULT FALSE,
    wifi BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id) ON DELETE CASCADE
);

-- 4. rooms (객실 정보)
CREATE TABLE rooms (
    room_id INT PRIMARY KEY AUTO_INCREMENT,
    hotel_id INT,
    room_type VARCHAR(50) NOT NULL,
    max_person INT NOT NULL,
    price INT NOT NULL,
    rooms_image VARCHAR(255),
    status ENUM('available', 'reserved') DEFAULT 'available',
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id) ON DELETE CASCADE
);

-- 5. reservations (예약)
CREATE TABLE reservations (
    reservation_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    room_id INT,
    coupon_id INT,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    total_price INT NOT NULL,
    guests INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('done', 'cancel') DEFAULT 'done',
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE
);

-- 6. reviews (후기)
CREATE TABLE reviews (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    hotel_id INT,
    reservation_id INT,
    rating DECIMAL(2,1) NOT NULL CHECK (rating BETWEEN 0.0 AND 5.0),
    content TEXT NOT NULL,
    image_url VARCHAR(255),
    room_type VARCHAR(50),
    travel_type ENUM('solo', 'couple', 'friend', 'family', 'business') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id) ON DELETE CASCADE
);

-- 7. review_helpful (후기 도움돼요 기록)
CREATE TABLE review_helpful (
    helpful_id INT PRIMARY KEY AUTO_INCREMENT,
    review_id INT,
    user_id INT,
    is_helpful INT DEFAULT 0,
    not_helpful INT DEFAULT 0,
    FOREIGN KEY (review_id) REFERENCES reviews(review_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- 8. inquiries (문의)
CREATE TABLE inquiries (
    inquiry_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    category ENUM('reservation', 'payment', 'room', 'other') NOT NULL,
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    is_secret TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- 9. inquiry_responses (문의 답변)
CREATE TABLE inquiry_responses (
    response_id INT PRIMARY KEY AUTO_INCREMENT,
    inquiry_id INT,
    admin_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (inquiry_id) REFERENCES inquiries(inquiry_id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- 10. wishlist (찜 목록)
CREATE TABLE wishlist (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    hotel_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id) ON DELETE CASCADE
);

-- 11. event_comments (이벤트 댓글)
CREATE TABLE event_comments (
    comment_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    comment TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- 12. 문의 게시판(파일 업로드)
CREATE TABLE inquiry_files (
    file_id INT AUTO_INCREMENT PRIMARY KEY,
    inquiry_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (inquiry_id) REFERENCES inquiries(inquiry_id) ON DELETE CASCADE
);

-- 13. 후기 게시판(사진 업로드)
CREATE TABLE review_images (
    image_id INT PRIMARY KEY AUTO_INCREMENT,
    review_id INT,
    image_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (review_id) REFERENCES reviews(review_id) ON DELETE CASCADE
);

-- 14. 공지사항 게시판
CREATE TABLE notices (
    notice_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_released BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
-- 15. 쿠폰 테이블
CREATE TABLE coupons (
    coupon_id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    discount_type ENUM('percentage', 'fixed') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    minimum_purchase DECIMAL(10,2),
    maximum_discount DECIMAL(10,2),
    usage_limit INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 16. 쿠폰 사용 기록 테이블
CREATE TABLE coupon_usage (
    usage_id INT PRIMARY KEY AUTO_INCREMENT,
    coupon_id INT,
    user_id INT,
    reservation_id INT,
    is_used BOOLEAN NOT NULL DEFAULT 0,
    used_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coupon_id) REFERENCES coupons(coupon_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (reservation_id) REFERENCES reservations(reservation_id) ON DELETE CASCADE
);

-- 17. 유저 쿠폰 테이블
CREATE TABLE user_coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    is_used BOOLEAN NOT NULL DEFAULT 0,
    coupon_id INT NOT NULL,
    received_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, coupon_id)
);
SET NAMES utf8mb4;

-- 사용자 계정 추가
INSERT INTO users (username, real_id, password, email, phone, created_at, is_admin, terms, marketing) VALUES 
('관리자', 'admin', '$2y$10$ddnQzxr5YtqhJ1kj9nmI2uKYk4l4Bw2oGeZg9/nec8RBSxE2Wx9HG', 'admin@naver.com', '010-1234-1234', '2024-01-01 00:00:00', 1, 1, 0),
('김서연', 'kimsy', '$2y$10$QFs/HG81eby9pDh9nHrfquj9K9KHrhVdBD4E7jDOm14aTUCeYbVIC', 'kimsy@naver.com', '010-1111-2222', '2024-01-02 10:30:00', 0, 1, 0),
('이준호', 'leejh', '$2y$10$kTMGVe4u3eW2ctMN/xDQoO1TcXaA5y1mGK9S36eaaSbB0lEK5nlzC', 'leejh@naver.com', '010-1111-3333', '2024-01-03 14:15:00', 0, 1, 0),
('박지민', 'parkjm', '$2y$10$n7/RK1YDQ9AmQgfZFKia1e1ICvcRdO2gBfnIQO/Bew3ptLiMn/uhO', 'parkjm@naver.com', '010-1111-4444', '2024-01-04 09:45:00', 0, 1, 0),
('최민준', 'choimj', '$2y$10$1H6lTPBPed/xC0lv7QhFwuCKjez2FSVU8zN.YeGfadCUJcUsdgbcC', 'choimj@naver.com', '010-1111-5555', '2024-01-05 16:20:00', 0, 1, 0),
('정서아', 'jeongsa', '$2y$10$SDNU236k2dWLeSY2xEmQq.wnWjSr8Dq7bStCVg5yWdG8yEGMtuwj.', 'jeongsa@naver.com', '010-1111-6666', '2024-01-06 11:10:00', 0, 1, 0),
('황도현', 'hwangdh', '$2y$10$keuAxNK9dymMBm45b.UtoO9.56DjuDKTAt4hYXHyAcmaSfIiIzfI.', 'hwangdh@naver.com', '010-1111-7777', '2024-01-07 13:25:00', 0, 1, 0),
('강수아', 'kangsa', '$2y$10$Zy7BDuozoxjIAeNaj4pjf.Lefpd.HitTyqnKJoP.9dQ2z30oZ6iFK', 'kangsa@naver.com', '010-1111-8888', '2024-01-08 15:40:00', 0, 1, 0),
('조민서', 'joms', '$2y$10$nxNXM2ayEwof27kTHuZHOu4oq1MnfZHaCh6OrUB8zjWSh6J3aqmSe', 'joms@naver.com', '010-1111-9999', '2024-01-09 10:55:00', 0, 1, 0),
('윤지우', 'yoonjw', '$2y$10$Q/4E0gInETv5hXObMh/5jO6uwsfCAzv0Iy2d2u/7kEjt0nILB48z6', 'yoonjw@naver.com', '010-1111-0000', '2024-01-10 14:30:00', 0, 1, 0),
('장하준', 'janghj', '$2y$10$ml8QbVV.Jf.fX56qCiUG6.KrDtiQUdMRIB3VXImYMpmudG49qwI2W', 'janghj@naver.com', '010-2222-1111', '2024-01-11 09:15:00', 0, 1, 0),
('임서준', 'limsj', '$2y$10$Tz79yqhE/eceYzfvMufN4eTqWhBQy4MviDPGgn.0rlG2y8eHWdxci', 'limsj@naver.com', '010-2222-2222', '2024-01-12 16:45:00', 0, 1, 0),
('한지안', 'hanja', '$2y$10$CzMIoUPjZPLCRWrCKShwD.RKrY6Y1nEskHEwew2f3l9aBsR.88xLu', 'hanja@naver.com', '010-2222-3333', '2024-01-13 11:20:00', 0, 1, 0),
('신하윤', 'shinhy', '$2y$10$qFwF/x3/9Z7m7Sdn083JsOWi39TRqq8TGoeP.VAWQW70jfmOg9n46', 'shinhy@naver.com', '010-2222-4444', '2024-01-14 13:35:00', 0, 1, 0),
('오지호', 'ohjh', '$2y$10$xbZ9T8gBWjxryO8DOUfvR.B.VIHRKZtExPDvp7rJ1PXu7gjr3A/Pi', 'ohjh@naver.com', '010-2222-5555', '2024-01-15 15:50:00', 0, 1, 0),
('서연우', 'seoyw', '$2y$10$ASUnZ0PypEI9cEKU4d5kGe0rQN9kju8/ZtEQev6Fu1i/FqetV/E8W', 'seoyw@naver.com', '010-2222-6666', '2024-01-16 10:05:00', 0, 1, 0),
('권서현', 'kwonsh', '$2y$10$zIIy1KVDvS0vKeazBD6NhuXE6VTlrsm5NkWs7hdriO4zlJg7aCC4K', 'kwonsh@naver.com', '010-2222-7777', '2024-01-17 14:40:00', 0, 1, 0),
('김민수', 'minsoo', '$2y$10$gXGlcAP2AM.4YtwSvX3X0uXEBIfLR/TsZom9qrRO8G1WM67XOiO.W', 'minsoo@gmail.com', '010-3333-1111', '2024-01-18 09:25:00', 0, 1, 1),
('이지은', 'jieun', '$2y$10$bTaTWESyLtHxJl0zbd02oe5y1TLLnjALLvzwdv1xfXRODJS1ZPCJa', 'jieun@daum.net', '010-3333-2222', '2024-01-19 16:55:00', 0, 1, 0),
('박준호', 'junho', '$2y$10$UWsRiFgNHP04jZofQx0LeuhtmpBtZUnVJhAgF2UXtVW6kPfwnDyWS', 'junho@naver.com', '010-3333-3333', '2024-01-20 11:30:00', 0, 1, 1),
('최수진', 'sujin', '$2y$10$3i5gsuNNAdhupY4SiO6ak.VMb.dW.K77a749gzHRuaO9o6xur572y', 'sujin@gmail.com', '010-3333-4444', '2024-01-21 13:45:00', 0, 1, 0),
('정다은', 'daeun', '$2y$10$PFz/JNQhzdRn0thNl78JC.MLN18jOhUQ60u5IRC0AUn6U/lH7Sq06', 'daeun@daum.net', '010-3333-5555', '2024-01-22 15:00:00', 0, 1, 1),
('황민지', 'minji', '$2y$10$qSmzTUIMNj2DaJj6/E2zteaQAxUOG0oEpE.E87uagvFsTS0mYeZLy', 'minji@naver.com', '010-3333-6666', '2024-01-23 10:15:00', 0, 1, 0),
('강현우', 'hyunwoo', '$2y$10$Yv0Oy.OvT9jdO5VKtkO5QOYgDQJQA0C4R6Z3Sl9zBQGoMlTmiRFFS', 'hyunwoo@gmail.com', '010-3333-7777', '2024-01-24 14:50:00', 0, 1, 1),
('조서연', 'seoyeon', '$2y$10$fURjUs4RoDLR1bxR.rzTm.lAcy7l07V8NCPzC3FwIYwLrmTU9iIkC', 'seoyeon@daum.net', '010-3333-8888', '2024-01-25 09:35:00', 0, 1, 0),
('윤지훈', 'jihun', '$2y$10$s81V.Wo7q6z65AixQS/mv.SNqP3/sRqVUZpbk2mfDsHNqXkSrCMTC', 'jihun@naver.com', '010-3333-9999', '2024-01-26 16:05:00', 0, 1, 1),
('장수아', 'sua', '$2y$10$IedAc8GnC8SHpmH5krSn2OCbMuz1qsyKcaEqBDEQOyuv5WzSLh/oq', 'sua@gmail.com', '010-3333-0000', '2024-01-27 11:40:00', 0, 1, 0);

UPDATE users SET profile_image='/uploads/discord.png' WHERE user_id=3;
UPDATE users SET profile_image='/uploads/discord.png' WHERE user_id=18;

-- 호텔 데이터 추가
INSERT INTO hotels (name, location, description, price_per_night, rating, main_image, detail_image_1, detail_image_2, detail_image_3, detail_image_4) VALUES
('그랜드 인터컨티넨탈 서울', '한국, 서울', '남산의 전망과 도심의 활기를 모두 누릴 수 있는 럭셔리 호텔입니다. 최고급 시설과 맞춤형 서비스로 잊지 못할 특별한 경험을 제공합니다. 5성급 레스토랑과 스파, 피트니스 센터를 갖추고 있어 도심 속에서도 완벽한 휴식을 즐길 수 있습니다.', 250000, 0.0, '/image/grand_hotel.jpg', '/image/grand_hotel_1.jpg', '/image/grand_hotel_2.jpg', '/image/grand_hotel_3.jpg', '/image/grand_hotel_4.jpg'),
('리츠칼튼 뉴욕', '미국, 뉴욕', '센트럴파크의 아름다운 전망을 자랑하는 뉴욕의 대표적인 럭셔리 호텔입니다. 클래식한 디자인과 현대적인 편의시설이 조화를 이루며, 미슐랭 스타 레스토랑과 세계적인 스파 브랜드가 입점해 있습니다. 뉴욕의 화려한 도시 생활과 고급스러운 휴식을 동시에 경험할 수 있습니다.', 450000, 0.0, '/image/newyork_central.jpg', '/image/newyork_1.jpg', '/image/newyork_2.jpg', '/image/newyork_3.jpg', '/image/newyork_4.jpg'),
('파라다이스 호텔 해운대', '한국, 부산', '해운대 해변과 불과 몇 걸음 거리에 위치한 프리미엄 호텔입니다. 모든 객실에서 아름다운 동해의 전망을 감상할 수 있으며, 실내외 수영장과 스파 시설을 갖추고 있습니다. 신선한 해산물을 맛볼 수 있는 레스토랑과 바가 있어 부산의 맛과 멋을 모두 즐길 수 있습니다.', 680000, 0.0, '/image/signature_busan.jpg', '/image/signature_busan_1.jpg', '/image/signature_busan_2.jpg', '/image/signature_busan_3.jpg', '/image/signature_busan_4.jpg'),
('포시즌스 파리', '프랑스, 파리', '에펠탑이 바로 눈앞에 펼쳐지는 파리의 대표적인 럭셔리 호텔입니다. 18세기 건축물을 현대적으로 리모델링하여 고급스러움과 역사적 가치를 모두 갖추고 있습니다. 미슐랭 스타 셰프가 운영하는 레스토랑과 전통적인 프랑스 스파를 경험할 수 있는 최고의 휴식 공간을 제공합니다.', 520000, 0.0, '/image/paris_eiffel.jpg', '/image/paris_1.jpg', '/image/paris_2.jpg', '/image/paris_3.jpg', '/image/paris_4.jpg'),
('신라스테이 제주', '한국, 제주', '제주의 아름다운 바다와 한라산의 전망을 모두 감상할 수 있는 프리미엄 호텔입니다. 현대적인 디자인과 제주 특유의 자연을 담은 인테리어가 특징이며, 제주 특산물을 활용한 다이닝과 스파 시설을 제공합니다. 제주의 자연과 문화를 모두 경험할 수 있는 최적의 휴식 공간입니다.', 150000, 0.0, '/image/jeju_ocean.jpg', '/image/jeju_ocean_1.jpg', '/image/jeju_ocean_2.jpg', '/image/jeju_ocean_3.jpg', '/image/jeju_ocean_4.jpg'),
('만다린 오리엔탈 도쿄', '일본, 도쿄', '도쿄의 스카이라인을 한눈에 볼 수 있는 최고급 호텔입니다. 일본의 전통 미학과 현대적인 럭셔리를 조화롭게 결합한 디자인이 특징이며, 미슐랭 스타 레스토랑과 전통적인 일본 스파를 제공합니다. 도쿄의 활기와 고요함을 동시에 경험할 수 있는 특별한 공간입니다.', 750000, 0.0, '/image/tokyo_view.jpg', '/image/tokyo_1.jpg', '/image/tokyo_2.jpg', '/image/tokyo_3.jpg', '/image/tokyo_4.jpg'),
('하얏트 리젠시 인천', '한국, 인천', '인천국제공항과 인접한 비즈니스 호텔로, 출장객들에게 최적의 편의를 제공합니다. 현대적인 시설과 효율적인 서비스가 특징이며, 피트니스 센터와 비즈니스 라운지를 갖추고 있습니다. 공항 셔틀 서비스와 24시간 룸서비스를 제공하여 출장객들의 편의를 최우선으로 생각합니다.', 120000, 0.0, '/image/incheon_sky.jpg', '/image/incheon_sky_1.jpg', '/image/incheon_sky_2.jpg', '/image/incheon_sky_3.jpg', '/image/incheon_sky_4.jpg'),
('페닌슐라 홍콩', '중국, 홍콩', '빅토리아 하버의 환상적인 전망을 자랑하는 홍콩의 상징적인 호텔입니다. 1928년에 지어진 역사적인 건물을 현대적으로 리노베이션하여 고급스러움과 전통을 모두 갖추고 있습니다. 미슐랭 스타 레스토랑과 전통적인 중국 스파를 제공하며, 홍콩의 화려한 야경을 감상할 수 있는 루프탑 바가 있습니다.', 460000, 0.0, '/image/hongkong_harbor.jpg', '/image/hongkong_1.jpg', '/image/hongkong_2.jpg', '/image/hongkong_3.jpg', '/image/hongkong_4.jpg'),
('메리어트 대구', '한국, 대구', '대구 도심의 중심에 위치한 비즈니스 호텔입니다. 현대적인 시설과 효율적인 서비스로 출장객들에게 최적의 편의를 제공합니다. 피트니스 센터와 비즈니스 라운지를 갖추고 있으며, 대구의 전통 음식을 맛볼 수 있는 레스토랑이 있습니다. 도심 속에서도 편안한 휴식을 취할 수 있는 공간을 제공합니다.', 100000, 0.0, '/image/daegu_central.jpg', '/image/daegu_central_1.jpg', '/image/daegu_central_2.jpg', '/image/daegu_central_3.jpg', '/image/daegu_central_4.jpg'),
('버즈 알 아랍', '아랍에미리트, 두바이', '세계 최초의 7성급 호텔로 알려진 두바이의 상징적인 호텔입니다. 인공섬 위에 세워진 돛 모양의 건물이 특징이며, 모든 객실이 2층 구조의 스위트룸으로 구성되어 있습니다. 24시간 버틀러 서비스와 헬리콥터 전용 착륙장을 갖추고 있으며, 세계 최고의 럭셔리를 경험할 수 있는 공간을 제공합니다.', 890000, 0.0, '/image/dubai_burj.jpg', '/image/dubai_1.jpg', '/image/dubai_2.jpg', '/image/dubai_3.jpg', '/image/dubai_4.jpg'),
('라마다 프라자 광주', '한국, 광주', '광주천과 인접한 쾌적한 호텔로, 도심 속에서도 자연을 느낄 수 있는 공간을 제공합니다. 현대적인 시설과 친절한 서비스가 특징이며, 피트니스 센터와 사우나 시설을 갖추고 있습니다. 광주의 전통 음식을 맛볼 수 있는 레스토랑이 있어 지역의 맛과 멋을 모두 경험할 수 있습니다.', 90000, 0.0, '/image/gwangju_riverside.jpg', '/image/gwangju_riverside_1.jpg', '/image/gwangju_riverside_2.jpg', '/image/gwangju_riverside_3.jpg', '/image/gwangju_riverside_4.jpg'),
('래플스 싱가포르', '싱가포르, 싱가포르', '1887년에 지어진 싱가포르의 역사적인 호텔로, 식민지 시대의 건축 양식을 그대로 보존하고 있습니다. 전통적인 팬하우스 스타일의 객실과 정원이 특징이며, 싱가포르 슬링이 탄생한 롱 바가 유명합니다. 식민지 시대의 고급스러움과 현대적인 편의시설이 조화를 이루는 특별한 공간을 제공합니다.', 420000, 0.0, '/image/singapore_raffles.jpg', '/image/singapore_1.jpg', '/image/singapore_2.jpg', '/image/singapore_3.jpg', '/image/singapore_4.jpg'),
('노보텔 대전', '한국, 대전', '대덕연구단지와 인접한 비즈니스 호텔로, 연구원과 출장객들에게 최적의 편의를 제공합니다. 현대적인 시설과 효율적인 서비스가 특징이며, 피트니스 센터와 비즈니스 라운지를 갖추고 있습니다. 대전의 특산물을 활용한 레스토랑이 있어 지역의 맛을 경험할 수 있습니다.', 110000, 0.0, '/image/daejeon_techno.jpg', '/image/daejeon_techno_1.jpg', '/image/daejeon_techno_2.jpg', '/image/daejeon_techno_3.jpg', '/image/daejeon_techno_4.jpg'),
('베네치안 마카오', '중국, 마카오', '베네치아의 운하와 건축 양식을 재현한 대규모 리조트 호텔입니다. 실내 운하에서 곤돌라를 타고 이동할 수 있으며, 세계 최대 규모의 카지노와 쇼핑몰을 갖추고 있습니다. 미슐랭 스타 레스토랑과 럭셔리 스파를 제공하며, 베네치아의 낭만을 아시아에서 경험할 수 있는 특별한 공간을 제공합니다.', 380000, 0.0, '/image/macau_venetian.jpg', '/image/macau_1.jpg', '/image/macau_2.jpg', '/image/macau_3.jpg', '/image/macau_4.jpg'),
('롯데호텔 울산', '한국, 울산', '울산항과 인접한 해양 리조트 호텔로, 바다 전망을 감상할 수 있는 최적의 위치에 자리잡고 있습니다. 현대적인 시설과 쾌적한 서비스가 특징이며, 실내외 수영장과 스파 시설을 갖추고 있습니다. 신선한 해산물을 맛볼 수 있는 레스토랑이 있어 울산의 맛을 경험할 수 있습니다.', 130000, 0.0, '/image/ulsan_marina.jpg', '/image/ulsan_marina_1.jpg', '/image/ulsan_marina_2.jpg', '/image/ulsan_marina_3.jpg', '/image/ulsan_marina_4.jpg'),
('플라자 뉴욕', '미국, 뉴욕', '센트럴파크 사우스에 위치한 뉴욕의 역사적인 호텔로, 1907년에 지어진 건물이 특징입니다. 클래식한 디자인과 현대적인 편의시설이 조화를 이루며, 미슐랭 스타 레스토랑과 전통적인 티 룸을 제공합니다. 뉴욕의 역사와 현대를 모두 경험할 수 있는 특별한 공간을 제공합니다.', 470000, 0.0, '/image/newyork_plaza.jpg', '/image/plaza_1.jpg', '/image/plaza_2.jpg', '/image/plaza_3.jpg', '/image/plaza_4.jpg'),
('힐튼 수원', '한국, 수원', '수원화성과 인접한 관광 호텔로, 수원의 역사와 문화를 경험할 수 있는 최적의 위치에 자리잡고 있습니다. 현대적인 시설과 친절한 서비스가 특징이며, 피트니스 센터와 사우나 시설을 갖추고 있습니다. 수원의 전통 음식을 맛볼 수 있는 레스토랑이 있어 지역의 맛을 경험할 수 있습니다.', 95000, 0.0, '/image/suwon_paldal.jpg', '/image/suwon_paldal_1.jpg', '/image/suwon_paldal_2.jpg', '/image/suwon_paldal_3.jpg', '/image/suwon_paldal_4.jpg'),
('웨스틴 로마', '이탈리아, 로마', '바티칸과 가까운 로마의 대표적인 럭셔리 호텔입니다. 로마의 역사적인 건축 양식을 현대적으로 재해석한 디자인이 특징이며, 미슐랭 스타 레스토랑과 전통적인 이탈리아 스파를 제공합니다. 로마의 역사와 문화를 모두 경험할 수 있는 최적의 위치에 자리잡고 있습니다.', 350000, 0.0, '/image/rome_westin.jpg', '/image/rome_1.jpg', '/image/rome_2.jpg', '/image/rome_3.jpg', '/image/rome_4.jpg'),
('하얏트 리젠시 시드니', '호주, 시드니', '오페라하우스와 하버브릿지가 보이는 시드니의 대표적인 호텔입니다. 현대적인 디자인과 쾌적한 서비스가 특징이며, 미슐랭 스타 레스토랑과 루프탑 바를 제공합니다. 시드니의 상징적인 전망을 감상할 수 있는 최적의 위치에 자리잡고 있습니다.', 420000, 0.0, '/image/sydney_hyatt.jpg', '/image/sydney_1.jpg', '/image/sydney_2.jpg', '/image/sydney_3.jpg', '/image/sydney_4.jpg'),
('W 바르셀로나', '스페인, 바르셀로나', '지중해가 보이는 바르셀로나의 현대적인 호텔입니다. 디자이너 호텔로 유명하며, 현대적인 디자인과 트렌디한 시설이 특징입니다. 루프탑 바와 미슐랭 스타 레스토랑을 제공하며, 바르셀로나의 활기와 지중해의 휴식을 동시에 경험할 수 있습니다.', 380000, 0.0, '/image/barcelona_w.jpg', '/image/barcelona_1.jpg', '/image/barcelona_2.jpg', '/image/barcelona_3.jpg', '/image/barcelona_4.jpg'),
('아만 도쿄', '일본, 도쿄', '도쿄의 전망을 감상할 수 있는 프라이빗 부티크 호텔입니다. 일본의 전통 미학과 현대적인 럭셔리를 조화롭게 결합한 디자인이 특징이며, 프라이빗 스파와 미슐랭 스타 레스토랑을 제공합니다. 도쿄의 활기와 고요함을 동시에 경험할 수 있는 특별한 공간을 제공합니다.', 680000, 0.0, '/image/tokyo_aman.jpg', '/image/aman_1.jpg', '/image/aman_2.jpg', '/image/aman_3.jpg', '/image/aman_4.jpg'),
('포시즌스 방콕', '태국, 방콕', '차오프라야 강변에 위치한 방콕의 대표적인 럭셔리 호텔입니다. 태국의 전통 미학과 현대적인 럭셔리를 조화롭게 결합한 디자인이 특징이며, 미슐랭 스타 레스토랑과 전통적인 태국 스파를 제공합니다. 방콕의 활기와 차오프라야 강의 휴식을 동시에 경험할 수 있습니다.', 320000, 0.0, '/image/bangkok_fourseasons.jpg', '/image/bangkok_1.jpg', '/image/bangkok_2.jpg', '/image/bangkok_3.jpg', '/image/bangkok_4.jpg'),
('샹그릴라 런던', '영국, 런던', '템즈강이 보이는 런던의 대표적인 럭셔리 호텔입니다. 영국의 전통적인 고급스러움과 현대적인 편의시설이 조화를 이루며, 미슐랭 스타 레스토랑과 전통적인 티 룸을 제공합니다. 런던의 역사와 현대를 모두 경험할 수 있는 특별한 공간을 제공합니다.', 480000, 0.0, '/image/london_shangri.jpg', '/image/london_1.jpg', '/image/london_2.jpg', '/image/london_3.jpg', '/image/london_4.jpg'),
('벨라지오 라스베가스', '미국, 라스베가스', '라스베가스의 상징적인 분수쇼로 유명한 럭셔리 호텔입니다. 이탈리아의 베네치아를 모티브로 한 디자인이 특징이며, 세계 최대 규모의 카지노와 쇼핑몰을 갖추고 있습니다. 미슐랭 스타 레스토랑과 럭셔리 스파를 제공하며, 라스베가스의 화려한 밤을 경험할 수 있습니다.', 390000, 0.0, '/image/vegas_bellagio.jpg', '/image/bellagio_1.jpg', '/image/bellagio_2.jpg', '/image/bellagio_3.jpg', '/image/bellagio_4.jpg'),
('파크 하얏트 비엔나', '오스트리아, 비엔나', '비엔나의 역사적인 건물을 현대적으로 리모델링한 럭셔리 호텔입니다. 오스트리아의 전통적인 고급스러움과 현대적인 편의시설이 조화를 이루며, 미슐랭 스타 레스토랑과 전통적인 카페를 제공합니다. 비엔나의 역사와 문화를 모두 경험할 수 있는 특별한 공간을 제공합니다.', 420000, 0.0, '/image/vienna_hyatt.jpg', '/image/vienna_1.jpg', '/image/vienna_2.jpg', '/image/vienna_3.jpg', '/image/vienna_4.jpg'),
('인터컨티넨탈 몰디브', '몰디브, 말레', '에메랄드빛 바다 위에 떠있는 워터빌라 리조트입니다. 모든 빌라가 프라이빗 수영장과 해변을 갖추고 있으며, 스노클링과 다이빙을 즐길 수 있는 산호초가 바로 앞에 있습니다. 미슐랭 스타 레스토랑과 오버워터 스파를 제공하며, 몰디브의 아름다운 자연을 만끽할 수 있습니다.', 780000, 0.0, '/image/maldives_ic.jpg', '/image/maldives_1.jpg', '/image/maldives_2.jpg', '/image/maldives_3.jpg', '/image/maldives_4.jpg'),
('만다린 오리엔탈 방콕', '태국, 방콕', '차오프라야 강변에 위치한 방콕의 전통적인 럭셔리 호텔입니다. 태국의 전통 미학과 현대적인 럭셔리를 조화롭게 결합한 디자인이 특징이며, 미슐랭 스타 레스토랑과 전통적인 태국 스파를 제공합니다. 방콕의 활기와 차오프라야 강의 휴식을 동시에 경험할 수 있습니다.', 350000, 0.0, '/image/bangkok_mandarin.jpg', '/image/mandarin_1.jpg', '/image/mandarin_2.jpg', '/image/mandarin_3.jpg', '/image/mandarin_4.jpg'),
('리츠칼튼 마이애미', '미국, 마이애미', '마이애미 비치에 위치한 럭셔리 리조트 호텔입니다. 아트 데코 스타일의 디자인이 특징이며, 프라이빗 비치와 수영장을 갖추고 있습니다. 미슐랭 스타 레스토랑과 럭셔리 스파를 제공하며, 마이애미의 화려한 해변을 경험할 수 있습니다.', 420000, 0.0, '/image/miami_ritz.jpg', '/image/miami_1.jpg', '/image/miami_2.jpg', '/image/miami_3.jpg', '/image/miami_4.jpg'),
('아틀란티스 두바이', '아랍에미리트, 두바이', '팜 주메이라의 해양 테마 리조트 호텔입니다. 세계 최대 규모의 수족관과 워터파크를 갖추고 있으며, 해양 생물과 함께 수영할 수 있는 특별한 경험을 제공합니다. 미슐랭 스타 레스토랑과 럭셔리 스파를 제공하며, 두바이의 화려한 리조트 라이프를 경험할 수 있습니다.', 520000, 0.0, '/image/dubai_atlantis.jpg', '/image/atlantis_1.jpg', '/image/atlantis_2.jpg', '/image/atlantis_3.jpg', '/image/atlantis_4.jpg'),
('웨스틴 발리', '인도네시아, 발리', '발리의 열대 해변에 위치한 럭셔리 리조트 호텔입니다. 발리의 전통적인 건축 양식과 현대적인 편의시설이 조화를 이루며, 프라이빗 비치와 수영장을 갖추고 있습니다. 미슐랭 스타 레스토랑과 전통적인 발리 스파를 제공하며, 발리의 자연과 문화를 모두 경험할 수 있습니다.', 380000, 0.0, '/image/bali_westin.jpg', '/image/bali_1.jpg', '/image/bali_2.jpg', '/image/bali_3.jpg', '/image/bali_4.jpg'),
('페어몬트 밴프', '캐나다, 밴프', '로키 산맥의 성같은 리조트 호텔입니다. 캐나다의 전통적인 고급스러움과 현대적인 편의시설이 조화를 이루며, 미슐랭 스타 레스토랑과 전통적인 스파를 제공합니다. 로키 산맥의 장관을 감상할 수 있는 최적의 위치에 자리잡고 있습니다.', 450000, 0.0, '/image/banff_fairmont.jpg', '/image/banff_1.jpg', '/image/banff_2.jpg', '/image/banff_3.jpg', '/image/banff_4.jpg'),
('샹그릴라 파리', '프랑스, 파리', '에펠탑 전망의 팔레스 호텔로, 파리의 대표적인 럭셔리 호텔입니다. 프랑스의 전통적인 고급스러움과 현대적인 편의시설이 조화를 이루며, 미슐랭 스타 레스토랑과 전통적인 프랑스 스파를 제공합니다. 파리의 역사와 현대를 모두 경험할 수 있는 특별한 공간을 제공합니다.', 580000, 0.0, '/image/paris_shangri.jpg', '/image/shangri_1.jpg', '/image/shangri_2.jpg', '/image/shangri_3.jpg', '/image/shangri_4.jpg'),
('콘래드 몰디브', '몰디브, 랑갈리', '인도양의 럭셔리 리조트 호텔입니다. 모든 빌라가 프라이빗 수영장과 해변을 갖추고 있으며, 스노클링과 다이빙을 즐길 수 있는 산호초가 바로 앞에 있습니다. 미슐랭 스타 레스토랑과 오버워터 스파를 제공하며, 몰디브의 아름다운 자연을 만끽할 수 있습니다.', 690000, 0.0, '/image/maldives_conrad.jpg', '/image/conrad_1.jpg', '/image/conrad_2.jpg', '/image/conrad_3.jpg', '/image/conrad_4.jpg'),
('세인트 레지스 보라보라', '프랑스령폴리네시아, 보라보라', '남태평양의 럭셔리 리조트 호텔입니다. 모든 빌라가 프라이빗 수영장과 해변을 갖추고 있으며, 오버워터 빌라에서 산호초를 감상할 수 있습니다. 미슐랭 스타 레스토랑과 오버워터 스파를 제공하며, 보라보라의 아름다운 자연을 만끽할 수 있습니다.', 820000, 0.0, '/image/borabora_regis.jpg', '/image/regis_1.jpg', '/image/regis_2.jpg', '/image/regis_3.jpg', '/image/regis_4.jpg'),
('아만풀로 베니스', '이탈리아, 베니스', '그랜드 운하의 16세기 궁전을 리모델링한 럭셔리 호텔입니다. 베네치아의 전통적인 고급스러움과 현대적인 편의시설이 조화를 이루며, 미슐랭 스타 레스토랑과 전통적인 이탈리아 스파를 제공합니다. 베네치아의 역사와 문화를 모두 경험할 수 있는 특별한 공간을 제공합니다.', 750000, 0.0, '/image/venice_aman.jpg', '/image/amanvenice_1.jpg', '/image/amanvenice_2.jpg', '/image/amanvenice_3.jpg', '/image/amanvenice_4.jpg'),
('포시즌스 보라보라', '프랑스령폴리네시아, 보라보라', '산호초 위의 럭셔리 리조트 호텔입니다. 모든 빌라가 프라이빗 수영장과 해변을 갖추고 있으며, 오버워터 빌라에서 산호초를 감상할 수 있습니다. 미슐랭 스타 레스토랑과 오버워터 스파를 제공하며, 보라보라의 아름다운 자연을 만끽할 수 있습니다.', 780000, 0.0, '/image/borabora_four.jpg', '/image/fourseasons_1.jpg', '/image/fourseasons_2.jpg', '/image/fourseasons_3.jpg', '/image/fourseasons_4.jpg'),
('리츠칼튼 몰디브', '몰디브, 파리', '인도양의 프라이빗 아일랜드 리조트 호텔입니다. 모든 빌라가 프라이빗 수영장과 해변을 갖추고 있으며, 스노클링과 다이빙을 즐길 수 있는 산호초가 바로 앞에 있습니다. 미슐랭 스타 레스토랑과 오버워터 스파를 제공하며, 몰디브의 아름다운 자연을 만끽할 수 있습니다.', 850000, 0.0, '/image/maldives_ritz.jpg', '/image/ritzmaldives_1.jpg', '/image/ritzmaldives_2.jpg', '/image/ritzmaldives_3.jpg', '/image/ritzmaldives_4.jpg'),
('만다린 오리엔탈 마라케시', '모로코, 마라케시', '아틀라스 산맥이 보이는 마라케시의 럭셔리 리조트 호텔입니다. 모로코의 전통적인 건축 양식과 현대적인 편의시설이 조화를 이루며, 미슐랭 스타 레스토랑과 전통적인 모로코 스파를 제공합니다. 마라케시의 역사와 문화를 모두 경험할 수 있는 특별한 공간을 제공합니다.', 420000, 0.0, '/image/marrakech_mandarin.jpg', '/image/marrakech_1.jpg', '/image/marrakech_2.jpg', '/image/marrakech_3.jpg', '/image/marrakech_4.jpg'),
('페닌슐라 파리', '프랑스, 파리', '샹젤리제 근처의 팔레스 호텔로, 파리의 대표적인 럭셔리 호텔입니다. 프랑스의 전통적인 고급스러움과 현대적인 편의시설이 조화를 이루며, 미슐랭 스타 레스토랑과 전통적인 프랑스 스파를 제공합니다. 파리의 역사와 현대를 모두 경험할 수 있는 특별한 공간을 제공합니다.', 620000, 0.0, '/image/paris_peninsula.jpg', '/image/peninsula_1.jpg', '/image/peninsula_2.jpg', '/image/peninsula_3.jpg', '/image/peninsula_4.jpg'),
('래플스 이스탄불', '터키, 이스탄불', '보스포러스 해협이 보이는 이스탄불의 럭셔리 호텔입니다. 터키의 전통적인 고급스러움과 현대적인 편의시설이 조화를 이루며, 미슐랭 스타 레스토랑과 전통적인 터키 스파를 제공합니다. 이스탄불의 역사와 문화를 모두 경험할 수 있는 특별한 공간을 제공합니다.', 380000, 0.0, '/image/istanbul_raffles.jpg', '/image/raffles_1.jpg', '/image/raffles_2.jpg', '/image/raffles_3.jpg', '/image/raffles_4.jpg'),
('카페 로얄 런던', '영국, 런던', '리젠트 거리의 역사적인 럭셔리 호텔입니다. 영국의 전통적인 고급스러움과 현대적인 편의시설이 조화를 이루며, 미슐랭 스타 레스토랑과 전통적인 티 룸을 제공합니다. 런던의 역사와 현대를 모두 경험할 수 있는 특별한 공간을 제공합니다.', 450000, 0.0, '/image/london_cafe.jpg', '/image/cafe_1.jpg', '/image/cafe_2.jpg', '/image/cafe_3.jpg', '/image/cafe_4.jpg'),
('아만 베니스', '이탈리아, 베니스', '그랜드 운하의 16세기 팔라조를 리모델링한 럭셔리 호텔입니다. 베네치아의 전통적인 고급스러움과 현대적인 편의시설이 조화를 이루며, 미슐랭 스타 레스토랑과 전통적인 이탈리아 스파를 제공합니다. 베네치아의 역사와 문화를 모두 경험할 수 있는 특별한 공간을 제공합니다.', 720000, 0.0, '/image/venice_aman2.jpg', '/image/amanvenice2_1.jpg', '/image/amanvenice2_2.jpg', '/image/amanvenice2_3.jpg', '/image/amanvenice2_4.jpg'),
('이터널 부산', '한국, 부산', '부산의 명물 호텔로, 해운대 해변과 가까운 위치에 자리잡고 있습니다. 현대적인 디자인과 쾌적한 서비스가 특징이며, 실내외 수영장과 스파 시설을 갖추고 있습니다. 신선한 해산물을 맛볼 수 있는 레스토랑이 있어 부산의 맛을 경험할 수 있습니다.', 550000, 0.0, '/image/busan_eternal.jpg', '/image/eternal_1.jpg', '/image/eternal_2.jpg', '/image/eternal_3.jpg', '/image/eternal_4.jpg'),
('아만 아무안', '인도네시아, 발리', '인도양 전망의 프라이빗 빌라 리조트입니다. 발리의 전통적인 건축 양식과 현대적인 편의시설이 조화를 이루며, 프라이빗 수영장과 해변을 갖추고 있습니다. 미슐랭 스타 레스토랑과 전통적인 발리 스파를 제공하며, 발리의 자연과 문화를 모두 경험할 수 있습니다.', 850000, 0.0, '/image/amankila.jpg', '/image/amankila_1.jpg', '/image/amankila_2.jpg', '/image/amankila_3.jpg', '/image/amankila_4.jpg'),
('포시즌스 하노이', '베트남, 하노이', '호안끼엠 호수가 보이는 하노이의 럭셔리 호텔입니다. 베트남의 전통적인 고급스러움과 현대적인 편의시설이 조화를 이루며, 미슐랭 스타 레스토랑과 전통적인 베트남 스파를 제공합니다. 하노이의 역사와 문화를 모두 경험할 수 있는 특별한 공간을 제공합니다.', 320000, 0.0, '/image/hanoi_fs.jpg', '/image/hanoi_1.jpg', '/image/hanoi_2.jpg', '/image/hanoi_3.jpg', '/image/hanoi_4.jpg'),
('콘래드 도쿄', '일본, 도쿄', '도쿄만 전망의 현대적 럭셔리 호텔입니다. 일본의 전통 미학과 현대적인 럭셔리를 조화롭게 결합한 디자인이 특징이며, 미슐랭 스타 레스토랑과 전통적인 일본 스파를 제공합니다. 도쿄의 활기와 고요함을 동시에 경험할 수 있는 특별한 공간을 제공합니다.', 580000, 0.0, '/image/tokyo_conrad.jpg', '/image/conradtokyo_1.jpg', '/image/conradtokyo_2.jpg', '/image/conradtokyo_3.jpg', '/image/conradtokyo_4.jpg'),
('리츠칼튼 몬테카를로', '모나코, 몬테카를로', '지중해 전망의 카지노 리조트 호텔입니다. 모나코의 전통적인 고급스러움과 현대적인 편의시설이 조화를 이루며, 미슐랭 스타 레스토랑과 전통적인 카지노를 제공합니다. 몬테카를로의 화려한 밤을 경험할 수 있는 특별한 공간을 제공합니다.', 650000, 0.0, '/image/montecarlo_ritz.jpg', '/image/montecarlo_1.jpg', '/image/montecarlo_2.jpg', '/image/montecarlo_3.jpg', '/image/montecarlo_4.jpg'),
('페닌슐라 베이루트', '레바논, 베이루트', '지중해 전망의 중동 럭셔리 호텔입니다. 레바논의 전통적인 고급스러움과 현대적인 편의시설이 조화를 이루며, 미슐랭 스타 레스토랑과 전통적인 중동 스파를 제공합니다. 베이루트의 역사와 문화를 모두 경험할 수 있는 특별한 공간을 제공합니다.', 100000, 0.0, '/image/beirut_peninsula.jpg', '/image/beirut_1.jpg', '/image/beirut_2.jpg', '/image/beirut_3.jpg', '/image/beirut_4.jpg'),
('홀리데이 인 부산', '한국, 부산', '부산의 명물 호텔로, 해운대 해변과 가까운 위치에 자리잡고 있습니다. 현대적인 디자인과 쾌적한 서비스가 특징이며, 실내외 수영장과 스파 시설을 갖추고 있습니다. 신선한 해산물을 맛볼 수 있는 레스토랑이 있어 부산의 맛을 경험할 수 있습니다.', 70000, 0.0, '/image/holiday_inn.jpg', '/image/holiday_inn_1.jpg', '/image/holiday_inn_2.jpg', '/image/holiday_inn_3.jpg', '/image/holiday_inn_4.jpg');

-- 리뷰 데이터 추가
INSERT INTO reviews (user_id, hotel_id, reservation_id, rating, content, room_type, travel_type, created_at) VALUES
(2, 1, NULL, 4.5, '시설이 깔끔하고 직원들이 친절했습니다. 위치도 좋아요.', 'suite', 'couple', '2025-03-15 14:30:00'),
(3, 1, NULL, 2.5, '방은 괜찮았지만 조식이 기대에 못 미쳤어요.', 'deluxe', 'friend', '2025-02-20 09:15:00'),
(4, 1, NULL, 3.0, '전반적으로 평범한 숙박이었습니다.', 'suite', 'couple', '2025-01-10 16:45:00'),
(5, 2, NULL, 5.0, '완벽한 숙박이었습니다. 모든 것이 최고였어요!', 'deluxe', 'family', '2025-04-05 11:20:00'),
(6, 2, NULL, 3.5, '가격 대비 만족스러웠습니다.', 'deluxe', 'couple', '2025-03-22 13:10:00'),
(7, 2, NULL, 1.5, '방음이 너무 안 되어서 잠을 잘 수 없었어요.', 'deluxe', 'family', '2025-02-18 10:30:00'),
(8, 3, NULL, 4.0, '시설과 서비스 모두 만족스러웠습니다.', 'suite', 'couple', '2025-01-25 15:40:00'),
(9, 3, NULL, 4.5, '뷰가 정말 아름다웠어요. 추천합니다!', 'suite', 'solo', '2025-04-12 12:15:00'),
(10, 3, NULL, 2.0, '에어컨이 고장나서 매우 불편했습니다.', 'deluxe', 'business', '2025-03-08 14:20:00'),
(11, 4, NULL, 3.5, '전반적으로 만족스러운 숙박이었습니다.', 'deluxe', 'couple', '2025-02-15 09:30:00'),
(12, 4, NULL, 2.5, '시설은 좋았지만 가격이 좀 비쌌어요.', 'deluxe', 'family', '2025-01-20 16:10:00'),
(13, 4, NULL, 4.0, '직원들이 매우 친절했어요.', 'deluxe', 'business', '2025-04-18 11:45:00'),
(14, 5, NULL, 3.0, '시설과 서비스 모두 평범했습니다.', 'suite', 'couple', '2025-03-05 13:25:00'),
(15, 5, NULL, 4.5, '가격 대비 매우 만족스러웠습니다.', 'deluxe', 'family', '2025-02-10 10:15:00'),
(16, 5, NULL, 2.0, '청결 상태가 좋지 않았습니다.', 'deluxe', 'couple', '2025-01-15 15:30:00'),
(17, 6, NULL, 5.0, '모든 것이 완벽했습니다!', 'suite', 'family', '2025-04-20 12:40:00'),
(18, 6, NULL, 3.0, '좋은 숙박이었습니다.', 'deluxe', 'couple', '2025-03-12 14:50:00'),
(19, 6, NULL, 1.5, '시설은 좋았지만 직원들이 매우 불 친절했습니다.', 'deluxe', 'family', '2025-02-25 09:20:00'),
(20, 7, NULL, 4.0, '뷰가 정말 아름다웠어요!', 'suite', 'couple', '2025-01-30 16:35:00'),
(21, 7, NULL, 2.5, '가격 대비 만족스럽지 않았습니다.', 'deluxe', 'family', '2025-04-08 11:10:00'),
(22, 7, NULL, 3.5, '방음이 좀 아쉬웠지만 전반적으로 괜찮았어요.', 'deluxe', 'family', '2025-03-18 13:45:00'),
(23, 8, NULL, 3.0, '전반적으로 평범한 숙박이었습니다.', 'deluxe', 'couple', '2025-02-22 10:25:00'),
(24, 8, NULL, 4.5, '시설이 매우 좋았습니다.', 'deluxe', 'family', '2025-01-28 15:15:00'),
(25, 8, NULL, 2.0, '청소가 제대로 되지 않았습니다.', 'deluxe', 'business', '2025-04-15 12:30:00'),
(2, 9, NULL, 5.0, '완벽한 숙박이었습니다!', 'suite', 'family', '2025-03-10 14:40:00'),
(3, 9, NULL, 3.5, '좋은 경험이었습니다.', 'deluxe', 'couple', '2025-02-05 09:50:00'),
(4, 9, NULL, 1.0, '에어컨이 고장나서 매우 불편했습니다.', 'deluxe', 'business', '2025-01-12 16:20:00'),
(5, 10, NULL, 4.0, '시설과 서비스 모두 좋았습니다.', 'suite', 'couple', '2025-04-22 11:35:00'),
(6, 10, NULL, 2.5, '평범한 숙박이었습니다.', 'deluxe', 'family', '2025-03-20 13:55:00'),
(7, 10, NULL, 3.0, '가격 대비 만족스러웠어요.', 'deluxe', 'business', '2025-02-15 10:45:00'),
(8, 11, NULL, 3.5, '전반적으로 만족스러운 숙박이었습니다.', 'suite', 'couple', '2025-01-18 15:25:00'),
(9, 11, NULL, 1.5, '시설은 좋았지만 직원 서비스가 매우 아쉬웠어요.', 'deluxe', 'family', '2025-04-10 12:50:00'),
(10, 12, NULL, 4.5, '모든 것이 완벽했습니다!', 'suite', 'couple', '2025-03-25 14:15:00'),
(11, 12, NULL, 2.0, '좋은 숙박이었습니다.', 'deluxe', 'friend', '2025-02-28 09:35:00'),
(12, 13, NULL, 3.0, '평범한 숙박이었습니다.', 'suite', 'business', '2025-01-22 16:55:00'),
(13, 13, NULL, 4.0, '시설은 좋았어요.', 'deluxe', 'couple', '2025-04-25 11:25:00'),
(14, 14, NULL, 2.5, '전반적으로 만족스러웠습니다.', 'suite', 'family', '2025-03-15 13:35:00'),
(15, 14, NULL, 3.5, '가격 대비 괜찮았어요.', 'deluxe', 'couple', '2025-02-10 10:55:00'),
(16, 15, NULL, 4.0, '시설과 서비스 모두 좋았습니다.', 'suite', 'business', '2025-01-05 15:45:00'),
(17, 15, NULL, 1.5, '평범한 숙박이었습니다.', 'deluxe', 'solo', '2025-04-28 12:05:00'),
(18, 16, NULL, 5.0, '완벽한 숙박이었습니다!', 'suite', 'couple', '2025-03-22 14:25:00'),
(19, 16, NULL, 2.5, '좋은 경험이었습니다.', 'deluxe', 'family', '2025-02-18 09:45:00'),
(20, 17, NULL, 3.5, '시설은 좋았지만 직원들이 좀 아쉬웠어요.', 'suite', 'business', '2025-01-15 16:15:00'),
(21, 17, NULL, 4.0, '전반적으로 만족스러웠습니다.', 'deluxe', 'couple', '2025-04-15 11:55:00'),
(22, 18, NULL, 2.5, '시설과 서비스 모두 좋았습니다.', 'suite', 'family', '2025-03-10 13:15:00'),
(23, 18, NULL, 3.0, '평범한 숙박이었습니다.', 'deluxe', 'couple', '2025-02-05 10:35:00'),
(24, 19, NULL, 4.5, '모든 것이 완벽했습니다!', 'suite', 'business', '2025-01-20 15:55:00'),
(25, 19, NULL, 1.5, '좋은 숙박이었습니다.', 'deluxe', 'family', '2025-04-20 12:25:00'),
(2, 20, NULL, 2.5, '시설은 좋았지만 가격이 좀 비쌌어요.', 'suite', 'couple', '2025-03-18 14:45:00'),
(3, 20, NULL, 4.0, '전반적으로 만족스러웠습니다.', 'deluxe', 'family', '2025-02-12 09:15:00'),
(4, 21, NULL, 2.5, '시설과 서비스 모두 좋았습니다.', 'suite', 'business', '2025-01-08 16:35:00'),
(5, 21, NULL, 3.5, '평범한 숙박이었습니다.', 'deluxe', 'couple', '2025-04-10 11:45:00'),
(6, 22, NULL, 4.0, '완벽한 숙박이었습니다!', 'suite', 'family', '2025-03-05 13:55:00'),
(7, 22, NULL, 1.0, '좋은 경험이었습니다.', 'deluxe', 'couple', '2025-02-28 10:15:00'),
(8, 23, NULL, 3.0, '시설은 좋았지만 직원들이 좀 아쉬웠어요.', 'suite', 'business', '2025-01-25 15:35:00'),
(9, 23, NULL, 4.5, '전반적으로 만족스러웠습니다.', 'deluxe', 'family', '2025-04-22 12:55:00'),
(10, 24, NULL, 2.0, '시설과 서비스 모두 좋았습니다.', 'suite', 'couple', '2025-03-15 14:15:00'),
(11, 24, NULL, 3.5, '평범한 숙박이었습니다.', 'deluxe', 'family', '2025-02-10 09:35:00'),
(12, 25, NULL, 4.0, '모든 것이 완벽했습니다!', 'suite', 'business', '2025-01-05 16:55:00'),
(13, 25, NULL, 1.5, '좋은 숙박이었습니다.', 'deluxe', 'couple', '2025-04-25 11:25:00'),
(14, 26, NULL, 3.0, '시설은 좋았지만 가격이 좀 비쌌어요.', 'suite', 'family', '2025-03-20 13:35:00'),
(15, 26, NULL, 4.5, '전반적으로 만족스러웠습니다.', 'deluxe', 'couple', '2025-02-15 10:55:00'),
(16, 27, NULL, 2.5, '시설과 서비스 모두 좋았습니다.', 'suite', 'business', '2025-01-10 15:15:00'),
(17, 27, NULL, 3.5, '평범한 숙박이었습니다.', 'deluxe', 'family', '2025-04-18 12:35:00'),
(18, 28, NULL, 4.0, '완벽한 숙박이었습니다!', 'suite', 'solo', '2025-03-12 14:55:00'),
(19, 28, NULL, 1.0, '좋은 경험이었습니다.', 'deluxe', 'family', '2025-02-08 09:15:00'),
(20, 29, NULL, 3.0, '시설은 좋았지만 직원들이 좀 아쉬웠어요.', 'suite', 'business', '2025-01-22 16:35:00'),
(21, 29, NULL, 4.5, '전반적으로 만족스러웠습니다.', 'deluxe', 'couple', '2025-04-15 11:55:00'),
(22, 30, NULL, 2.0, '시설과 서비스 모두 좋았습니다.', 'suite', 'family', '2025-03-08 13:15:00'),
(23, 30, NULL, 3.5, '평범한 숙박이었습니다.', 'deluxe', 'couple', '2025-02-25 10:35:00'),
(24, 31, NULL, 4.0, '모든 것이 완벽했습니다!', 'suite', 'business', '2025-01-18 15:55:00'),
(25, 31, NULL, 1.5, '좋은 숙박이었습니다.', 'deluxe', 'family', '2025-04-28 12:25:00'),
(2, 32, NULL, 3.0, '시설은 좋았지만 가격이 좀 비쌌어요.', 'suite', 'couple', '2025-03-22 14:45:00'),
(3, 32, NULL, 4.5, '전반적으로 만족스러웠습니다.', 'deluxe', 'family', '2025-02-18 09:15:00'),
(4, 33, NULL, 2.5, '시설과 서비스 모두 좋았습니다.', 'suite', 'business', '2025-01-15 16:35:00'),
(5, 33, NULL, 3.5, '평범한 숙박이었습니다.', 'deluxe', 'couple', '2025-04-15 11:45:00'),
(6, 34, NULL, 4.0, '완벽한 숙박이었습니다!', 'suite', 'family', '2025-03-10 13:55:00'),
(7, 34, NULL, 1.0, '좋은 경험이었습니다.', 'deluxe', 'couple', '2025-02-05 10:15:00'),
(8, 35, NULL, 3.0, '시설은 좋았지만 직원들이 좀 아쉬웠어요.', 'suite', 'business', '2025-01-20 15:35:00'),
(9, 35, NULL, 4.5, '전반적으로 만족스러웠습니다.', 'deluxe', 'family', '2025-04-20 12:55:00'),
(10, 36, NULL, 2.0, '시설과 서비스 모두 좋았습니다.', 'suite', 'couple', '2025-03-18 14:15:00'),
(11, 36, NULL, 3.5, '평범한 숙박이었습니다.', 'deluxe', 'family', '2025-02-12 09:35:00'),
(12, 37, NULL, 4.0, '모든 것이 완벽했습니다!', 'suite', 'business', '2025-01-08 16:55:00'),
(13, 37, NULL, 1.5, '좋은 숙박이었습니다.', 'deluxe', 'couple', '2025-04-10 11:25:00'),
(14, 38, NULL, 3.0, '시설은 좋았지만 가격이 좀 비쌌어요.', 'suite', 'family', '2025-03-05 13:35:00'),
(15, 38, NULL, 4.5, '전반적으로 만족스러웠습니다.', 'deluxe', 'couple', '2025-02-28 10:55:00'),
(16, 39, NULL, 2.5, '시설과 서비스 모두 좋았습니다.', 'suite', 'business', '2025-01-25 15:15:00'),
(17, 39, NULL, 3.5, '평범한 숙박이었습니다.', 'deluxe', 'family', '2025-04-22 12:35:00'),
(18, 40, NULL, 4.0, '완벽한 숙박이었습니다!', 'suite', 'couple', '2025-03-15 14:55:00'),
(19, 40, NULL, 1.0, '좋은 경험이었습니다.', 'deluxe', 'family', '2025-02-10 09:15:00'),
(20, 41, NULL, 3.0, '시설은 좋았지만 직원들이 좀 아쉬웠어요.', 'suite', 'business', '2025-01-05 16:35:00'),
(21, 41, NULL, 4.5, '전반적으로 만족스러웠습니다.', 'deluxe', 'couple', '2025-04-25 11:55:00'),
(22, 42, NULL, 2.0, '시설과 서비스 모두 좋았습니다.', 'suite', 'family', '2025-03-20 13:15:00'),
(23, 42, NULL, 3.5, '평범한 숙박이었습니다.', 'deluxe', 'couple', '2025-02-15 10:35:00'),
(24, 43, NULL, 4.0, '모든 것이 완벽했습니다!', 'suite', 'business', '2025-01-10 15:55:00'),
(25, 43, NULL, 1.5, '좋은 숙박이었습니다.', 'deluxe', 'family', '2025-04-18 12:25:00'),
(2, 44, NULL, 3.0, '시설은 좋았지만 가격이 좀 비쌌어요.', 'suite', 'couple', '2025-03-12 14:45:00'),
(3, 44, NULL, 4.5, '전반적으로 만족스러웠습니다.', 'deluxe', 'family', '2025-02-08 09:15:00'),
(4, 45, NULL, 2.5, '시설과 서비스 모두 좋았습니다.', 'suite', 'business', '2025-01-22 16:35:00'),
(5, 45, NULL, 3.5, '평범한 숙박이었습니다.', 'deluxe', 'couple', '2025-04-15 11:45:00'),
(6, 46, NULL, 4.0, '완벽한 숙박이었습니다!', 'suite', 'family', '2025-03-08 13:55:00'),
(7, 46, NULL, 1.0, '좋은 경험이었습니다.', 'deluxe', 'couple', '2025-02-25 10:15:00'),
(8, 47, NULL, 3.0, '시설은 좋았지만 직원들이 좀 아쉬웠어요.', 'suite', 'business', '2025-01-18 15:35:00'),
(9, 47, NULL, 4.5, '전반적으로 만족스러웠습니다.', 'deluxe', 'family', '2025-04-28 12:55:00'),
(10, 1, NULL, 4.0, '혼자 여행하기 좋은 호텔이었어요. 조용하고 편안했습니다.', 'deluxe', 'solo', '2025-04-01 11:20:00'),
(11, 2, NULL, 3.5, '친구들과 함께 즐거운 시간 보냈습니다. 수영장이 특히 좋았어요.', 'suite', 'friend', '2025-03-25 14:30:00'),
(12, 3, NULL, 4.5, '비즈니스 출장에 최적화된 호텔이었습니다. 회의실 시설이 훌륭했어요.', 'suite', 'business', '2025-02-15 09:45:00'),
(13, 4, NULL, 3.0, '가족 여행에 적합한 호텔이었습니다. 키즈룸이 있어서 아이들이 좋아했어요.', 'deluxe', 'family', '2025-01-28 16:10:00'),
(14, 5, NULL, 4.0, '커플 여행에 추천합니다. 룸서비스가 특히 만족스러웠어요.', 'suite', 'couple', '2025-04-10 12:40:00'),
(15, 6, NULL, 3.5, '혼자 여행하기 좋은 호텔이었습니다. 조식이 다양하고 맛있었어요.', 'deluxe', 'solo', '2025-03-18 15:20:00'),
(16, 7, NULL, 4.5, '친구들과 함께한 여행이 즐거웠습니다. 바가 특히 좋았어요.', 'suite', 'friend', '2025-02-22 10:30:00'),
(17, 8, NULL, 3.0, '비즈니스 미팅에 적합한 호텔이었습니다. 와이파이가 빠르고 안정적이었어요.', 'deluxe', 'business', '2025-01-15 13:50:00'),
(18, 9, NULL, 4.0, '가족 여행에 추천합니다. 수영장과 키즈클럽이 있어서 아이들이 좋아했어요.', 'suite', 'family', '2025-04-05 11:15:00'),
(19, 10, NULL, 3.5, '커플 여행에 좋은 호텔이었습니다. 룸이 깔끔하고 조용했어요.', 'deluxe', 'couple', '2025-03-28 14:25:00'),
(20, 11, NULL, 4.5, '혼자 여행하기 좋은 호텔이었습니다. 스파 시설이 특히 좋았어요.', 'suite', 'solo', '2025-02-20 09:40:00'),
(21, 12, NULL, 3.0, '친구들과 함께한 여행이 즐거웠습니다. 레스토랑이 다양하고 맛있었어요.', 'deluxe', 'friend', '2025-01-12 16:30:00'),
(22, 13, NULL, 4.0, '비즈니스 출장에 추천합니다. 비즈니스 센터가 잘 갖춰져 있었어요.', 'suite', 'business', '2025-04-22 12:50:00'),
(23, 14, NULL, 3.5, '가족 여행에 적합한 호텔이었습니다. 놀이방이 있어서 아이들이 좋아했어요.', 'deluxe', 'family', '2025-03-15 15:10:00'),
(24, 15, NULL, 4.0, '커플 여행에 좋은 호텔이었습니다. 룸 뷰가 아름다웠어요.', 'suite', 'couple', '2025-02-08 10:20:00'),
(25, 16, NULL, 3.5, '혼자 여행하기 좋은 호텔이었습니다. 피트니스 센터가 잘 갖춰져 있었어요.', 'deluxe', 'solo', '2025-01-30 13:40:00'),
(2, 17, NULL, 4.0, '친구들과 함께한 여행이 즐거웠습니다. 수영장과 스파가 특히 좋았어요.', 'suite', 'friend', '2025-04-18 11:55:00'),
(3, 18, NULL, 3.5, '비즈니스 미팅에 적합한 호텔이었습니다. 회의실이 잘 갖춰져 있었어요.', 'deluxe', 'business', '2025-03-10 14:15:00'),
(4, 19, NULL, 4.0, '가족 여행에 추천합니다. 키즈클럽이 있어서 아이들이 좋아했어요.', 'suite', 'family', '2025-02-25 09:30:00'),
(5, 20, NULL, 3.5, '커플 여행에 좋은 호텔이었습니다. 룸서비스가 만족스러웠어요.', 'deluxe', 'couple', '2025-01-18 16:50:00'),
(6, 21, NULL, 4.0, '혼자 여행하기 좋은 호텔이었습니다. 조용하고 편안했어요.', 'suite', 'solo', '2025-04-15 12:10:00'),
(7, 22, NULL, 3.5, '친구들과 함께한 여행이 즐거웠습니다. 바와 레스토랑이 좋았어요.', 'deluxe', 'friend', '2025-03-08 15:30:00'),
(8, 23, NULL, 4.0, '비즈니스 출장에 추천합니다. 비즈니스 센터가 잘 갖춰져 있었어요.', 'suite', 'business', '2025-02-22 10:40:00'),
(9, 24, NULL, 3.5, '가족 여행에 적합한 호텔이었습니다. 수영장과 놀이방이 있어서 아이들이 좋아했어요.', 'deluxe', 'family', '2025-01-15 13:20:00'),
(10, 25, NULL, 4.0, '커플 여행에 좋은 호텔이었습니다. 룸이 깔끔하고 조용했어요.', 'suite', 'couple', '2025-04-28 11:40:00'),
(11, 26, NULL, 3.5, '혼자 여행하기 좋은 호텔이었습니다. 스파 시설이 특히 좋았어요.', 'deluxe', 'solo', '2025-03-22 14:50:00'),
(12, 27, NULL, 4.0, '친구들과 함께한 여행이 즐거웠습니다. 레스토랑이 다양하고 맛있었어요.', 'suite', 'friend', '2025-02-18 09:10:00'),
(13, 28, NULL, 3.5, '비즈니스 미팅에 적합한 호텔이었습니다. 회의실이 잘 갖춰져 있었어요.', 'deluxe', 'business', '2025-01-12 16:30:00'),
(14, 29, NULL, 4.0, '가족 여행에 추천합니다. 키즈클럽이 있어서 아이들이 좋아했어요.', 'suite', 'family', '2025-04-20 12:50:00'),
(15, 30, NULL, 3.5, '커플 여행에 좋은 호텔이었습니다. 룸 뷰가 아름다웠어요.', 'deluxe', 'couple', '2025-03-15 15:10:00'),
(16, 31, NULL, 4.0, '혼자 여행하기 좋은 호텔이었습니다. 피트니스 센터가 잘 갖춰져 있었어요.', 'suite', 'solo', '2025-02-10 10:20:00'),
(17, 32, NULL, 3.5, '친구들과 함께한 여행이 즐거웠습니다. 수영장과 스파가 특히 좋았어요.', 'deluxe', 'friend', '2025-01-05 13:40:00'),
(18, 33, NULL, 4.0, '비즈니스 출장에 추천합니다. 비즈니스 센터가 잘 갖춰져 있었어요.', 'suite', 'business', '2025-04-25 11:55:00'),
(19, 34, NULL, 3.5, '가족 여행에 적합한 호텔이었습니다. 놀이방이 있어서 아이들이 좋아했어요.', 'deluxe', 'family', '2025-03-20 14:15:00'),
(20, 35, NULL, 4.0, '커플 여행에 좋은 호텔이었습니다. 룸서비스가 만족스러웠어요.', 'suite', 'couple', '2025-02-15 09:30:00'),
(21, 36, NULL, 3.5, '혼자 여행하기 좋은 호텔이었습니다. 조용하고 편안했어요.', 'deluxe', 'solo', '2025-01-10 16:50:00'),
(22, 37, NULL, 4.0, '친구들과 함께한 여행이 즐거웠습니다. 바와 레스토랑이 좋았어요.', 'suite', 'friend', '2025-04-18 12:10:00'),
(23, 38, NULL, 3.5, '비즈니스 미팅에 적합한 호텔이었습니다. 회의실이 잘 갖춰져 있었어요.', 'deluxe', 'business', '2025-03-12 15:30:00'),
(24, 39, NULL, 4.0, '가족 여행에 추천합니다. 수영장과 놀이방이 있어서 아이들이 좋아했어요.', 'suite', 'family', '2025-02-08 10:40:00'),
(25, 40, NULL, 3.5, '커플 여행에 좋은 호텔이었습니다. 룸이 깔끔하고 조용했어요.', 'deluxe', 'couple', '2025-01-22 13:20:00'),
(2, 41, NULL, 4.0, '혼자 여행하기 좋은 호텔이었습니다. 스파 시설이 특히 좋았어요.', 'suite', 'solo', '2025-04-15 11:40:00'),
(3, 42, NULL, 3.5, '친구들과 함께한 여행이 즐거웠습니다. 레스토랑이 다양하고 맛있었어요.', 'deluxe', 'friend', '2025-03-08 14:50:00'),
(4, 43, NULL, 4.0, '비즈니스 출장에 추천합니다. 비즈니스 센터가 잘 갖춰져 있었어요.', 'suite', 'business', '2025-02-25 09:10:00'),
(5, 44, NULL, 3.5, '가족 여행에 적합한 호텔이었습니다. 키즈클럽이 있어서 아이들이 좋아했어요.', 'deluxe', 'family', '2025-01-18 16:30:00'),
(6, 45, NULL, 4.0, '커플 여행에 좋은 호텔이었습니다. 룸 뷰가 아름다웠어요.', 'suite', 'couple', '2025-04-28 12:50:00'),
(7, 46, NULL, 3.5, '혼자 여행하기 좋은 호텔이었습니다. 피트니스 센터가 잘 갖춰져 있었어요.', 'deluxe', 'solo', '2025-03-22 15:10:00'),
(8, 47, NULL, 4.0, '친구들과 함께한 여행이 즐거웠습니다. 수영장과 스파가 특히 좋았어요.', 'suite', 'friend', '2025-02-18 10:20:00');

-- hotels 테이블의 rating 업데이트
UPDATE hotels h
SET h.rating = (
    SELECT ROUND(AVG(r.rating), 1)
    FROM reviews r
    WHERE r.hotel_id = h.hotel_id
);

-- 호텔 부대시설 데이터 추가
INSERT INTO hotel_facilities (hotel_id, pool, spa, fitness, restaurant, parking, wifi) VALUES
(1, 1, 1, 0, 1, 0, 1),
(2, 0, 1, 1, 1, 0, 1),
(3, 1, 0, 1, 0, 1, 1),
(4, 1, 1, 0, 1, 1, 1),
(5, 0, 1, 1, 1, 0, 1),
(6, 1, 1, 1, 1, 1, 1),
(7, 1, 1, 0, 0, 1, 1),
(8, 0, 1, 1, 1, 0, 1),
(9, 1, 0, 1, 1, 1, 1),
(10, 1, 1, 0, 1, 0, 1),
(11, 0, 1, 1, 0, 1, 1),
(12, 1, 0, 1, 1, 0, 1),
(13, 1, 1, 0, 1, 1, 1),
(14, 0, 0, 1, 1, 0, 1),
(15, 1, 0, 1, 1, 1, 1),
(16, 1, 1, 0, 0, 1, 1),
(17, 0, 1, 1, 1, 0, 1),
(18, 1, 0, 1, 1, 1, 1),
(19, 1, 1, 0, 1, 1, 1),
(20, 0, 1, 1, 0, 0, 1),
(21, 1, 0, 1, 1, 0, 1),
(22, 1, 0, 0, 1, 1, 1),
(23, 0, 1, 1, 1, 0, 1),
(24, 1, 0, 1, 1, 1, 1),
(25, 1, 1, 0, 0, 1, 1),
(26, 0, 1, 1, 1, 0, 1),
(27, 1, 0, 1, 1, 1, 1),
(28, 1, 1, 0, 1, 0, 1),
(29, 0, 1, 1, 0, 1, 1),
(30, 1, 0, 1, 1, 0, 1),
(31, 1, 1, 0, 1, 1, 1),
(32, 0, 1, 1, 1, 0, 1),
(33, 1, 0, 1, 1, 1, 1),
(34, 1, 1, 0, 0, 1, 1),
(35, 0, 1, 1, 1, 0, 1),
(36, 1, 0, 1, 1, 1, 1),
(37, 1, 1, 0, 1, 0, 1),
(38, 0, 1, 1, 0, 1, 1),
(39, 1, 0, 1, 1, 0, 1),
(40, 1, 1, 0, 1, 1, 1),
(41, 0, 1, 1, 1, 0, 1),
(42, 1, 0, 1, 1, 1, 1),
(43, 1, 1, 0, 0, 1, 1),
(44, 0, 1, 1, 1, 0, 1),
(45, 1, 0, 1, 1, 1, 1),
(46, 1, 1, 0, 1, 0, 1),
(47, 0, 1, 1, 0, 1, 1),
(48, 0, 1, 1, 0, 1, 1),
(49, 1, 1, 0, 0, 1, 1);

-- 이벤트 댓글 더미 데이터
INSERT INTO event_comments (user_id, comment, created_at) VALUES
(2, '호텔 시설이 정말 깔끔했고, 직원들의 친절한 서비스가 인상적이었어요. 홈페이지에서 예약도 간편하게 할 수 있었습니다.', '2025-05-01 10:15:23'),
(3, '홈페이지에서 부산 호텔 타임딜을 발견했는데, 정말 좋은 가격에 예약할 수 있었어요. 호텔 수영장도 너무 좋았습니다.', '2025-05-01 14:30:45'),
(4, '일본 호텔 예약이 홈페이지에서 너무 간편했어요. 위치도 좋고 호텔 레스토랑의 음식이 정말 맛있었습니다.', '2025-05-01 09:20:12'),
(5, '홈페이지의 리뷰 시스템이 정말 도움이 되었어요. 다른 이용자들의 후기를 보고 호텔을 선택할 수 있어서 좋았습니다.', '2025-05-02 16:45:30'),
(6, '호텔 뷰가 정말 멋졌고, 홈페이지에서 제공하는 사진과 실제가 거의 동일했어요. 예약 과정도 매우 간단했습니다.', '2025-05-02 11:10:15'),
(7, '홈페이지에서 이벤트 알림을 받아 참여했는데, 정말 좋은 경험이었어요. 호텔 스파 시설도 최고였습니다.', '2025-05-02 13:25:40'),
(8, '호텔 직원분들이 정말 친절했고, 홈페이지의 실시간 예약 확인 기능이 매우 편리했습니다.', '2025-05-03 15:40:22'),
(9, '홈페이지의 필터 기능으로 원하는 조건의 호텔을 쉽게 찾을 수 있었어요. 부산 호텔의 가성비가 정말 좋았습니다.', '2025-05-03 10:55:18'),
(10, '일본 호텔 예약이 홈페이지에서 너무 간편했어요. 호텔의 위치가 관광지와 가까워서 정말 좋았습니다.', '2025-05-03 14:20:33'),
(11, '홈페이지의 디자인이 깔끔하고 보기 좋았어요. 호텔의 조식이 정말 맛있었습니다.', '2025-05-01 09:35:27'),
(12, '호텔 시설이 정말 깔끔했고, 홈페이지에서 제공하는 상세 정보가 매우 도움이 되었어요.', '2025-05-01 16:50:42'),
(13, '홈페이지의 모바일 버전이 정말 잘 만들어져 있어서 이동 중에도 편하게 예약할 수 있었어요.', '2025-05-02 11:15:55'),
(14, '호텔 직원분들의 서비스가 최고였고, 홈페이지의 실시간 문의 기능이 매우 유용했습니다.', '2025-05-02 13:30:10'),
(15, '홈페이지에서 부산 호텔의 특가 정보를 확인하고 예약했는데, 정말 만족스러운 숙박이었어요.', '2025-05-03 15:45:25'),
(16, '일본 호텔의 위치가 정말 좋았고, 홈페이지의 지도 기능으로 주변 관광지를 쉽게 찾을 수 있었어요.', '2025-05-03 10:00:38'),
(17, '홈페이지의 예약 취소 정책이 명확해서 좋았어요. 호텔의 피트니스 센터 시설도 최고였습니다.', '2025-05-01 14:15:50'),
(18, '호텔의 뷰가 정말 멋졌고, 홈페이지에서 제공하는 가상 투어가 실제와 거의 동일했어요.', '2025-05-01 09:30:15'),
(19, '홈페이지의 이벤트 알림을 받아 참여했는데, 정말 좋은 경험이었어요. 호텔의 와이파이 속도도 빠르고 안정적이었습니다.', '2025-05-02 16:45:30'),
(20, '호텔 직원분들이 정말 친절했고, 홈페이지의 실시간 체크인/체크아웃 기능이 매우 편리했습니다.', '2025-05-02 11:10:45'),
(21, '홈페이지가 직관적이고 사용하기 편리했어요. 특히 호텔 검색 기능이 정말 좋았습니다.', '2025-05-03 13:25:20'),
(22, '호텔의 위치가 정말 좋았어요. 주변에 관광지가 많아서 여행하기 편했어요.', '2025-05-03 15:40:35'),
(23, '호텔의 조식이 정말 맛있었어요. 다양한 메뉴가 있어서 매일 다른 것을 먹을 수 있었어요.', '2025-05-01 10:55:50'),
(24, '호텔의 수영장이 정말 깔끔했어요. 특히 야간 조명이 아름다웠습니다.', '2025-05-02 14:20:15'),
(25, '호텔의 스파 시설이 최고였어요. 피로가 확 풀렸어요.', '2025-05-02 09:35:30'),
(26, '호텔의 룸서비스가 정말 빠르고 맛있었어요. 특히 피자와 파스타가 인상적이었어요.', '2025-05-03 16:50:45');


-- 객실 데이터 추가
INSERT INTO rooms (hotel_id, room_type, max_person, price, rooms_image) VALUES
-- 호텔 1
(1, 'deluxe', 2, 250000, '/image/deluxe.jpg'),
(1, 'suite', 4, 350000, '/image/suite.jpg'),
-- 호텔 2
(2, 'deluxe', 2, 450000, '/image/deluxe.jpg'),
(2, 'suite', 4, 550000, '/image/suite.jpg'),
-- 호텔 3
(3, 'deluxe', 2, 680000, '/image/deluxe.jpg'),
(3, 'suite', 4, 780000, '/image/suite.jpg'),
-- 호텔 4
(4, 'deluxe', 2, 520000, '/image/deluxe.jpg'),
(4, 'suite', 4, 620000, '/image/suite.jpg'),
-- 호텔 5
(5, 'deluxe', 2, 150000, '/image/deluxe.jpg'),
(5, 'suite', 4, 250000, '/image/suite.jpg'),
-- 호텔 6
(6, 'deluxe', 2, 750000, '/image/deluxe.jpg'),
(6, 'suite', 4, 850000, '/image/suite.jpg'),
-- 호텔 7
(7, 'deluxe', 2, 120000, '/image/deluxe.jpg'),
(7, 'suite', 4, 220000, '/image/suite.jpg'),
-- 호텔 8
(8, 'deluxe', 2, 460000, '/image/deluxe.jpg'),
(8, 'suite', 4, 560000, '/image/suite.jpg'),
-- 호텔 9
(9, 'deluxe', 2, 100000, '/image/deluxe.jpg'),
(9, 'suite', 4, 200000, '/image/suite.jpg'),
-- 호텔 10
(10, 'deluxe', 2, 890000, '/image/deluxe.jpg'),
(10, 'suite', 4, 990000, '/image/suite.jpg'),
-- 호텔 11
(11, 'deluxe', 2, 90000, '/image/deluxe.jpg'),
(11, 'suite', 4, 190000, '/image/suite.jpg'),
-- 호텔 12
(12, 'deluxe', 2, 420000, '/image/deluxe.jpg'),
(12, 'suite', 4, 520000, '/image/suite.jpg'),
-- 호텔 13
(13, 'deluxe', 2, 110000, '/image/deluxe.jpg'),
(13, 'suite', 4, 210000, '/image/suite.jpg'),
-- 호텔 14
(14, 'deluxe', 2, 380000, '/image/deluxe.jpg'),
(14, 'suite', 4, 480000, '/image/suite.jpg'),
-- 호텔 15
(15, 'deluxe', 2, 130000, '/image/deluxe.jpg'),
(15, 'suite', 4, 230000, '/image/suite.jpg'),
-- 호텔 16
(16, 'deluxe', 2, 470000, '/image/deluxe.jpg'),
(16, 'suite', 4, 570000, '/image/suite.jpg'),
-- 호텔 17
(17, 'deluxe', 2, 95000, '/image/deluxe.jpg'),
(17, 'suite', 4, 195000, '/image/suite.jpg'),
-- 호텔 18
(18, 'deluxe', 2, 350000, '/image/deluxe.jpg'),
(18, 'suite', 4, 450000, '/image/suite.jpg'),
-- 호텔 19
(19, 'deluxe', 2, 420000, '/image/deluxe.jpg'),
(19, 'suite', 4, 520000, '/image/suite.jpg'),
-- 호텔 20
(20, 'deluxe', 2, 380000, '/image/deluxe.jpg'),
(20, 'suite', 4, 480000, '/image/suite.jpg'),
-- 호텔 21
(21, 'deluxe', 2, 680000, '/image/deluxe.jpg'),
(21, 'suite', 4, 780000, '/image/suite.jpg'),
-- 호텔 22
(22, 'deluxe', 2, 320000, '/image/deluxe.jpg'),
(22, 'suite', 4, 420000, '/image/suite.jpg'),
-- 호텔 23
(23, 'deluxe', 2, 480000, '/image/deluxe.jpg'),
(23, 'suite', 4, 580000, '/image/suite.jpg'),
-- 호텔 24
(24, 'deluxe', 2, 390000, '/image/deluxe.jpg'),
(24, 'suite', 4, 490000, '/image/suite.jpg'),
-- 호텔 25
(25, 'deluxe', 2, 420000, '/image/deluxe.jpg'),
(25, 'suite', 4, 520000, '/image/suite.jpg'),
-- 호텔 26
(26, 'deluxe', 2, 780000, '/image/deluxe.jpg'),
(26, 'suite', 4, 880000, '/image/suite.jpg'),
-- 호텔 27
(27, 'deluxe', 2, 350000, '/image/deluxe.jpg'),
(27, 'suite', 4, 450000, '/image/suite.jpg'),
-- 호텔 28
(28, 'deluxe', 2, 420000, '/image/deluxe.jpg'),
(28, 'suite', 4, 520000, '/image/suite.jpg'),
-- 호텔 29
(29, 'deluxe', 2, 380000, '/image/deluxe.jpg'),
(29, 'suite', 4, 480000, '/image/suite.jpg'),
-- 호텔 30
(30, 'deluxe', 2, 380000, '/image/deluxe.jpg'),
(30, 'suite', 4, 480000, '/image/suite.jpg'),
-- 호텔 31
(31, 'deluxe', 2, 780000, '/image/deluxe.jpg'),
(31, 'suite', 4, 880000, '/image/suite.jpg'),
-- 호텔 32
(32, 'deluxe', 2, 350000, '/image/deluxe.jpg'),
(32, 'suite', 4, 450000, '/image/suite.jpg'),
-- 호텔 33
(33, 'deluxe', 2, 420000, '/image/deluxe.jpg'),
(33, 'suite', 4, 520000, '/image/suite.jpg'),
-- 호텔 34
(34, 'deluxe', 2, 380000, '/image/deluxe.jpg'),
(34, 'suite', 4, 480000, '/image/suite.jpg'),
-- 호텔 35
(35, 'deluxe', 2, 380000, '/image/deluxe.jpg'),
(35, 'suite', 4, 480000, '/image/suite.jpg'),
-- 호텔 36
(36, 'deluxe', 2, 780000, '/image/deluxe.jpg'),
(36, 'suite', 4, 880000, '/image/suite.jpg'),
-- 호텔 37
(37, 'deluxe', 2, 350000, '/image/deluxe.jpg'),
(37, 'suite', 4, 450000, '/image/suite.jpg'),
-- 호텔 38
(38, 'deluxe', 2, 420000, '/image/deluxe.jpg'),
(38, 'suite', 4, 520000, '/image/suite.jpg'),
-- 호텔 39
(39, 'deluxe', 2, 380000, '/image/deluxe.jpg'),
(39, 'suite', 4, 480000, '/image/suite.jpg'),
-- 호텔 40
(40, 'deluxe', 2, 380000, '/image/deluxe.jpg'),
(40, 'suite', 4, 480000, '/image/suite.jpg'),
-- 호텔 41
(41, 'deluxe', 2, 780000, '/image/deluxe.jpg'),
(41, 'suite', 4, 880000, '/image/suite.jpg'),
-- 호텔 42
(42, 'deluxe', 2, 350000, '/image/deluxe.jpg'),
(42, 'suite', 4, 450000, '/image/suite.jpg'),
-- 호텔 43
(43, 'deluxe', 2, 550000, '/image/deluxe.jpg'),
(43, 'suite', 4, 650000, '/image/suite.jpg'),
-- 호텔 44
(44, 'deluxe', 2, 380000, '/image/deluxe.jpg'),
(44, 'suite', 4, 480000, '/image/suite.jpg'),
-- 호텔 45
(45, 'deluxe', 2, 380000, '/image/deluxe.jpg'),
(45, 'suite', 4, 480000, '/image/suite.jpg'),
-- 호텔 46
(46, 'deluxe', 2, 580000, '/image/deluxe.jpg'),
(46, 'suite', 4, 680000, '/image/suite.jpg'),
-- 호텔 47
(47, 'deluxe', 2, 350000, '/image/deluxe.jpg'),
(47, 'suite', 4, 450000, '/image/suite.jpg'),
-- 호텔 48
(48, 'deluxe', 2, 100000, '/image/deluxe.jpg'),
(48, 'suite', 4, 200000, '/image/suite.jpg'),
-- 호텔 49
(49, 'deluxe', 2, 70000, '/image/deluxe.jpg'),
(49, 'suite', 4, 170000, '/image/suite.jpg');


-- 후기 도움돼요 기록 추가
INSERT INTO review_helpful (review_id, user_id, is_helpful, not_helpful) VALUES
(1, 3, 1, 0),
(1, 4, 1, 0),
(1, 5, 1, 0),
(1, 6, 1, 0),
(1, 7, 1, 0),
(1, 8, 0, 1),
(2, 6, 1, 0),
(2, 7, 0, 1),
(2, 8, 1, 0),
(2, 9, 0, 1),
(2, 10, 1, 0),
(3, 8, 1, 0),
(3, 9, 1, 0),
(3, 10, 1, 0),
(3, 11, 0, 1),
(3, 12, 1, 0),
(4, 10, 1, 0),
(4, 11, 0, 1),
(4, 12, 1, 0),
(4, 13, 1, 0),
(4, 14, 1, 0),
(5, 12, 1, 0),
(5, 13, 1, 0),
(5, 14, 1, 0),
(5, 15, 0, 1),
(5, 16, 1, 0),
(6, 14, 0, 1),
(6, 15, 0, 1),
(6, 16, 0, 1),
(6, 17, 1, 0),
(6, 18, 0, 1),
(7, 16, 1, 0),
(7, 17, 1, 0),
(7, 18, 1, 0),
(7, 19, 1, 0),
(7, 20, 0, 1),
(8, 18, 1, 0),
(8, 19, 0, 1),
(8, 20, 1, 0),
(8, 21, 1, 0),
(8, 22, 1, 0),
(9, 20, 1, 0),
(9, 21, 1, 0),
(9, 22, 1, 0),
(9, 23, 0, 1),
(9, 24, 1, 0),
(10, 22, 0, 1),
(10, 23, 0, 1),
(10, 24, 0, 1),
(10, 25, 1, 0),
(10, 2, 0, 1),
(11, 3, 1, 0),
(11, 4, 1, 0),
(11, 5, 0, 1),
(11, 6, 1, 0),
(11, 7, 1, 0),
(12, 8, 0, 1),
(12, 9, 1, 0),
(12, 10, 1, 0),
(12, 11, 1, 0),
(12, 12, 0, 1),
(13, 14, 1, 0),
(13, 15, 1, 0),
(13, 16, 0, 1),
(13, 17, 1, 0),
(13, 18, 1, 0),
(14, 20, 1, 0),
(14, 21, 0, 1),
(14, 22, 1, 0),
(14, 23, 1, 0),
(14, 24, 1, 0),
(15, 2, 1, 0),
(15, 3, 1, 0),
(15, 4, 0, 1),
(15, 5, 1, 0),
(15, 6, 1, 0),
(16, 8, 1, 0),
(16, 9, 1, 0),
(16, 10, 1, 0),
(16, 11, 0, 1),
(16, 12, 1, 0),
(17, 14, 0, 1),
(17, 15, 1, 0),
(17, 16, 1, 0),
(17, 17, 1, 0),
(17, 18, 0, 1),
(18, 20, 1, 0),
(18, 21, 1, 0),
(18, 22, 0, 1),
(18, 23, 1, 0),
(18, 24, 1, 0),
(19, 2, 1, 0),
(19, 3, 0, 1),
(19, 4, 1, 0),
(19, 5, 1, 0),
(19, 6, 1, 0),
(20, 8, 1, 0),
(20, 9, 1, 0),
(20, 10, 0, 1),
(20, 11, 1, 0),
(20, 12, 1, 0),
(21, 14, 1, 0),
(21, 15, 1, 0),
(21, 16, 0, 1),
(21, 17, 1, 0),
(21, 18, 1, 0),
(22, 20, 1, 0),
(22, 21, 0, 1),
(22, 22, 1, 0),
(22, 23, 1, 0),
(22, 24, 1, 0),
(23, 2, 1, 0),
(23, 3, 1, 0),
(23, 4, 0, 1),
(23, 5, 1, 0),
(23, 6, 1, 0),
(24, 8, 1, 0),
(24, 9, 1, 0),
(24, 10, 1, 0),
(24, 11, 0, 1),
(24, 12, 1, 0),
(25, 14, 0, 1),
(25, 15, 1, 0),
(25, 16, 1, 0),
(25, 17, 1, 0),
(25, 18, 0, 1),
(26, 20, 1, 0),
(26, 21, 1, 0),
(26, 22, 0, 1),
(26, 23, 1, 0),
(26, 24, 1, 0),
(27, 2, 1, 0),
(27, 3, 0, 1),
(27, 4, 1, 0),
(27, 5, 1, 0),
(27, 6, 1, 0),
(28, 8, 1, 0),
(28, 9, 1, 0),
(28, 10, 0, 1),
(28, 11, 1, 0),
(28, 12, 1, 0),
(29, 14, 1, 0),
(29, 15, 1, 0),
(29, 16, 0, 1),
(29, 17, 1, 0),
(29, 18, 1, 0),
(30, 20, 1, 0),
(30, 21, 0, 1),
(30, 22, 1, 0),
(30, 23, 1, 0),
(30, 24, 1, 0),
(31, 2, 1, 0),
(31, 3, 1, 0),
(31, 4, 0, 1),
(31, 5, 1, 0),
(31, 6, 1, 0),
(32, 8, 1, 0),
(32, 9, 1, 0),
(32, 10, 1, 0),
(32, 11, 0, 1),
(32, 12, 1, 0),
(33, 14, 0, 1),
(33, 15, 1, 0),
(33, 16, 1, 0),
(33, 17, 1, 0),
(33, 18, 0, 1),
(34, 20, 1, 0),
(34, 21, 1, 0),
(34, 22, 0, 1),
(34, 23, 1, 0),
(34, 24, 1, 0),
(35, 2, 1, 0),
(35, 3, 0, 1),
(35, 4, 1, 0),
(35, 5, 1, 0),
(35, 6, 1, 0),
(36, 8, 1, 0),
(36, 9, 1, 0),
(36, 10, 0, 1),
(36, 11, 1, 0),
(36, 12, 1, 0),
(37, 14, 1, 0),
(37, 15, 1, 0),
(37, 16, 0, 1),
(37, 17, 1, 0),
(37, 18, 1, 0),
(38, 20, 1, 0),
(38, 21, 0, 1),
(38, 22, 1, 0),
(38, 23, 1, 0),
(38, 24, 1, 0),
(39, 2, 1, 0),
(39, 3, 1, 0),
(39, 4, 0, 1),
(39, 5, 1, 0),
(39, 6, 1, 0),
(40, 8, 1, 0),
(40, 9, 1, 0),
(40, 10, 1, 0),
(40, 11, 0, 1),
(40, 12, 1, 0);

-- 문의글 추가
INSERT INTO inquiries (user_id, category, title, content, is_secret) VALUES
(12, 'reservation', '예약 취소 문의드립니다', '다음 주 예약을 취소하고 싶은데 어떻게 해야 하나요?', 0),
(2, 'payment', '결제 수단 변경 가능한가요?', '신용카드로 결제했는데 현금으로 변경하고 싶습니다.', 0),
(3, 'room', '객실 뷰 문의', '오션뷰 객실이 정확히 어느 방향을 보고 있나요?', 0),
(4, 'other', '수영장 이용 시간', '수영장 이용 가능 시간을 알려주세요.', 0),
(5, 'reservation', '체크인 시간 문의', '체크인 시간이 꼭 3시인가요?', 0),
(6, 'payment', '영수증 재발급', '영수증을 분실했는데 재발급 받을 수 있나요?', 1),
(7, 'room', '객실 청소 시간', '객실 청소는 하루에 몇 번 이루어지나요?', 0),
(8, 'other', '주차 공간 문의', '호텔 주차장이 어디에 있나요?', 0),
(9, 'reservation', '조기 체크인 가능한가요?', '오전 11시에 체크인 가능한가요?', 0),
(10, 'payment', '환불 정책 문의', '예약 취소 시 환불 정책을 알려주세요.', 1);

-- 문의 답변 (관리자 ID: 1번 사용자)
INSERT INTO inquiry_responses (inquiry_id, admin_id, content) VALUES
(1, 1, '예약 취소는 마이페이지 > 예약 내역에서 가능합니다. 취소 수수료는 체크인 24시간 전까지는 없습니다.'),
(2, 1, '결제 수단 변경은 체크인 24시간 전까지 가능합니다. 호텔 프론트로 연락 주시면 도와드리겠습니다.'),
(3, 1, '오션뷰 객실은 남쪽 방향을 바라보고 있으며, 바다가 한눈에 들어오는 전망을 제공합니다.'),
(4, 1, '수영장은 오전 7시부터 오후 10시까지 이용 가능합니다. 단, 정기 점검 시간(오후 2시~4시)에는 이용이 제한됩니다.'),
(5, 1, '체크인 시간은 오후 3시부터 오후 11시까지입니다. 단, 객실 상황에 따라 조기 체크인이 가능할 수 있습니다.'),
(6, 1, '영수증 재발급은 호텔 프론트에서 가능합니다. 신분증을 지참해 주시면 도와드리겠습니다.');

-- 공지사항 게시판 데이터 추가 (관리자 ID: 1번 사용자)
INSERT INTO notices (user_id, title, content, created_at, is_released) VALUES
(1, '[시스템 점검] 예약 시스템 점검 안내', '더 나은 서비스 제공을 위해 예약 시스템 점검이 진행됩니다. 점검 시간: 5월 9일 오전 2시 ~ 4시. 점검 중에는 예약 서비스 이용이 제한됩니다. 불편을 드려 죄송합니다.', '2025-05-01 10:00:00', 0),
(1, '[예약 안내] 조기 예약 할인 프로모션', '여름 시즌을 맞이하여 조기 예약 할인 프로모션이 진행됩니다. - 3개월 전 예약: 30% 할인. - 2개월 전 예약: 20% 할인. - 1개월 전 예약: 10% 할인. 자세한 사항은 예약 페이지를 참고해 주세요.', '2025-05-02 14:00:00', 1),
(1, '[예약 정책] 예약 취소 및 변경 정책 안내', '예약 취소 및 변경 정책이 다음과 같이 변경되었습니다. - 체크인 7일 전: 전액 환불. - 체크인 3일 전: 70% 환불. - 체크인 1일 전: 30% 환불. - 당일 취소: 환불 불가.', '2025-05-03 16:00:00', 1),
(1, '[이벤트] 첫 예약 고객 특별 혜택', 'KISIA HOTEL 첫 예약 고객을 위한 특별 혜택이 준비되었습니다. - 웰컴 드링크 2잔 제공. - 레이트 체크아웃 (오후 2시까지). - 호텔 레스토랑 10% 할인 쿠폰. * 중복 예약 고객은 제외됩니다.', '2025-05-04 11:00:00', 1),
(1, '[시스템] 모바일 예약 서비스 개선', '모바일 예약 서비스가 개선되었습니다. - 더 빠른 예약 처리. - 실시간 객실 현황 확인. - 간편 결제 시스템 도입. 앱스토어에서 최신 버전을 다운로드 받으세요.', '2025-05-05 09:00:00', 1),
(1, '[예약 안내] 연박 예약 특별 혜택', '연박 예약 시 다음과 같은 특별 혜택을 제공합니다. - 2박 이상: 조식 1인 무료. - 3박 이상: 수영장 이용권 2매. - 4박 이상: 스파 이용권 1매. * 모든 혜택은 객실당 1회 제공됩니다.', '2025-05-06 15:00:00', 1),
(1, '[시스템] 회원 등급 혜택 안내', '회원 등급별 혜택이 다음과 같이 변경되었습니다. - 골드: 객실 업그레이드 1회. - 실버: 레이트 체크아웃. - 브론즈: 웰컴 드링크. 자세한 사항은 마이페이지에서 확인하세요.', '2025-05-07 13:00:00', 1),
(1, '[예약 안내] 비수기 특별 프로모션', '비수기 특별 프로모션이 진행됩니다. - 평일 예약 시 20% 추가 할인. - 주말 예약 시 10% 추가 할인. - 연박 예약 시 1박 무료. * 다른 프로모션과 중복 적용 불가.', '2025-05-08 16:00:00', 0),
(1, '[시스템] 포인트 적립 정책 변경', '포인트 적립 정책이 다음과 같이 변경됩니다. - 객실 요금 1만원당 100포인트. - 레스토랑 이용 1만원당 50포인트. - 스파 이용 1만원당 30포인트. * 포인트는 1년간 유효합니다.', '2025-05-09 10:00:00', 1),
(1, '[예약 안내] 단체 예약 특별 프로모션', '단체 예약 시 다음과 같은 특별 혜택을 제공합니다. - 10명 이상: 15% 할인. - 20명 이상: 20% 할인. - 30명 이상: 25% 할인. * 단체 예약은 전화 문의 바랍니다.', '2025-05-10 14:00:00', 0);

INSERT INTO coupons 
(code, name, description, discount_type, discount_value, start_date, end_date, minimum_purchase, maximum_discount, usage_limit, is_active)
VALUES 
('WELCOME10', '신규 회원 할인', '신규 가입 회원 대상 10% 할인 쿠폰', 'percentage', 10.00, '2025-05-01', '2025-12-31', 5000, 20000, 1, TRUE),
('MAY5000', '5월 한정 할인', '5월 한정 5,000원 할인 쿠폰', 'fixed', 5000.00, '2025-05-01', '2025-05-31', 20000, NULL, 100, TRUE),
('VIP20', 'VIP 전용 할인', 'VIP 회원 전용 20% 할인 쿠폰', 'percentage', 20.00, '2025-05-01', '2025-12-31', 10000, 50000, NULL, TRUE);