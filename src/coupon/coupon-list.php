<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/coupon_info.php';
include_once __DIR__ . '/../includes/header.php';

// 로그인 확인
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='/user/login.php';</script>";
    exit;
}

// CSRF 토큰 생성
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_id = $_SESSION['user_id'];
?>

<main class="user-coupon-container">
    <h2>나의 쿠폰</h2>

    <?php if (count($coupons) === 0): ?>
        <p>현재 사용 가능한 쿠폰이 없습니다.</p>
    <?php else: ?>
        <?php foreach ($coupons as $coupon): ?>
            <?php 
            $already_received = hasCoupon($conn, $user_id, $coupon['coupon_id']);
            $is_used = isCouponUsed($conn, $user_id, $coupon['coupon_id']);
            ?>
            <div class="coupon-card">
                <div class="coupon-details">
                    <div class="coupon-header">
                        <i class="fas fa-ticket-alt"></i> 
                        <?= htmlspecialchars($coupon['name'], ENT_QUOTES, 'UTF-8') ?> 
                        (<?= htmlspecialchars($coupon['code'], ENT_QUOTES, 'UTF-8') ?>)
                    </div>
                    <p style="color:red; font-weight:bold;">
                        <?= $coupon['discount_type'] === 'percentage'
                            ? (int)htmlspecialchars($coupon['discount_value'], ENT_QUOTES, 'UTF-8') . '% 할인'
                            : number_format((int)$coupon['discount_value']) . '원 할인'; ?>
                        <?php if ($is_used): ?>
                            <span style="color:red; font-weight:bold;">[사용 완료]</span>
                        <?php endif; ?>
                    </p>
                    <p>
                        사용 기간: <?= htmlspecialchars($coupon['start_date'], ENT_QUOTES, 'UTF-8') ?> ~ 
                        <?= htmlspecialchars($coupon['end_date'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
                <div class="coupon-action">
                    <?php 
                    if ($coupon['code'] === 'VIP20' && !$is_vip):
                    ?>
                        <button disabled title="VIP 전용 쿠폰입니다."><i class="fas fa-lock"></i></button>
                    <?php 
                    elseif ($coupon['code'] === 'WELCOME10' && !$is_new_user):
                    ?>
                        <button disabled title="신규 회원 전용 쿠폰입니다."><i class="fas fa-user-plus"></i></button>
                    <?php 
                    elseif ($is_used):
                    ?>
                        <button disabled><i class="fas fa-check-double"></i></button>
                    <?php 
                    elseif ($already_received):
                    ?>
                        <button disabled><i class="fas fa-check"></i></button>
                    <?php 
                    else:
                    ?>
                        <form action="receive_coupon_action.php" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="coupon_id" value="<?= htmlspecialchars($coupon['coupon_id'], ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit"><i class="fas fa-gift"></i></button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
