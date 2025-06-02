<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

// 날짜 유효성 검사 함수
function isValidDateTime($dt) {
    return $dt !== null && $dt !== '' && $dt !== '0000-00-00 00:00:00';
}

// 시간대 명시 (Asia/Seoul)
$tz = new DateTimeZone('Asia/Seoul');

$id = trim($_POST['real_id'] ?? '');
$password = $_POST['password'] ?? '';

if ($id === '' || $password === '') {
    echo "<script>alert('아이디 또는 비밀번호를 입력해주세요.'); history.back();</script>";
    exit;
}

// 사용자 조회
$stmt = $conn->prepare("SELECT user_id, real_id, username, password, is_admin, login_attempts, last_failed_at FROM users WHERE real_id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    $user_id = $user['user_id'];
    $attempts = (int)$user['login_attempts'];
    $last_failed = $user['last_failed_at'];

    // 로그인 실패 5회 이상일 경우 제한 확인
    if ($attempts >= 5 && isValidDateTime($last_failed)) {
        try {
            $now = new DateTime('now', $tz);
            $last = new DateTime($last_failed, $tz);
            $diff = $now->getTimestamp() - $last->getTimestamp();

            if ($diff < 300) {
                $remain = 300 - $diff;
                $minutes = floor($remain / 60);
                $seconds = $remain % 60;
                $msg = "로그인을 5회 이상 실패하였습니다. 약 {$minutes}분 {$seconds}초 뒤 다시 시도해주세요.";
                echo "<script>alert('$msg'); history.back();</script>";
                exit;
            } else {
                // 제한 시간 경과 → 실패 기록 초기화
                $stmt = $conn->prepare("UPDATE users SET login_attempts = 0, last_failed_at = NULL WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $attempts = 0;
            }
        } catch (Exception $e) {
            echo "<script>alert('시간 처리 오류 발생: {$e->getMessage()}'); history.back();</script>";
            exit;
        }
    }

    // 로그인 성공 처리
    if (password_verify($password, $user['password'])) {
        session_regenerate_id(true);

        // 로그인 성공 → 실패 기록 초기화
        $stmt = $conn->prepare("UPDATE users SET login_attempts = 0, last_failed_at = NULL WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $_SESSION['is_login'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['real_id'] = $user['real_id'];
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['is_admin'] = $user['is_admin'];

        if ($user['is_admin']) {
            echo "<script>alert('관리자님 안녕하세요.'); location.href = '../admin/admin.php';</script>";
        } else {
            echo "<script>alert('로그인 되었습니다.'); location.href = '../index.php';</script>";
        }
        exit;
    } else {
        // 로그인 실패 처리
        if ($attempts < 5) {
            $stmt = $conn->prepare("UPDATE users SET login_attempts = login_attempts + 1, last_failed_at = NOW() WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
        }

        // 실패 이후 최신 상태 재조회
        $stmt = $conn->prepare("SELECT login_attempts, last_failed_at FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $updated = $stmt->get_result()->fetch_assoc();
        $updated_attempts = (int)$updated['login_attempts'];
        $updated_last_failed = $updated['last_failed_at'];

        // 제한 메시지 구성
        if ($updated_attempts >= 5 && isValidDateTime($updated_last_failed)) {
            $now = new DateTime('now', $tz);
            $last = new DateTime($updated_last_failed, $tz);
            $diff = $now->getTimestamp() - $last->getTimestamp();
            $remain = max(0, 300 - $diff);
            $minutes = floor($remain / 60);
            $seconds = $remain % 60;

            if ($remain > 0) {
                $msg = "로그인 시도가 제한되었습니다. 약 {$minutes}분 {$seconds}초 뒤 다시 시도해주세요.";
            } else {
                $msg = "로그인 시도가 제한되었으나 제한 시간이 경과되었습니다. 다시 시도해주세요.";
            }
        } else {
            $msg = "아이디 또는 비밀번호가 일치하지 않습니다.";
        }

        echo "<script>alert('$msg'); history.back();</script>";
        exit;
    }

} else {
    echo "<script>alert('존재하지 않는 계정입니다.'); history.back();</script>";
    exit;
}
?>