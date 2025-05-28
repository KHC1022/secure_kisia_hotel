<?php 
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../includes/event_comments.php';

// 로그인 확인
$logged_in = isset($_SESSION['user_id']);

// CSRF 토큰 생성
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<main class="event-container">
    <div class="event-header">
        <h1>의견 남기고 선물 받자!</h1>
        <span class="event-period">2025년 5월 1일 ~ 5월 30일</span>
    </div>

    <div class="event-content">
        <div class="event-image">
            <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1600&q=80" alt="호텔 리뷰 이벤트">
        </div>

        <div class="event-details">
            <h2>이벤트 내용</h2>
            <p class="event-description">KISIA HOTEL을 이용해 주시는 고객님들의 소중한 의견을 기다립니다!</p>
            
            <div class="event-section">
                <h3>참여 방법</h3>
                <ol>
                    <li>아래 댓글란에 KISIA HOTEL에 대한 의견을 작성해주세요</li>
                    <li>호텔 시설, 서비스에 대한 상세한 의견을 남겨주시면 당첨 확률이 높아집니다</li>
                </ol>
            </div>

            <div class="event-section">
                <h3>경품 안내</h3>
                <ul>
                    <li>1등(1명): 5성급 호텔 숙박권 2박</li>
                    <li>2등(3명): 5성급 호텔 숙박권 1박</li>
                    <li>3등(5명): 스타벅스 기프티콘 5만원권</li>
                    <li>4등(10명): 스타벅스 기프티콘 3만원권</li>
                </ul>
            </div>

            <div class="event-section">
                <h3>유의사항</h3>
                <ul class="notice-list">
                    <li>당첨자 발표: 2025년 6월 10일</li>
                    <li>숙박권의 유효기간은 발급일로부터 6개월입니다.</li>
                    <li>경품은 당첨자 본인에 한해 사용 가능합니다.</li>
                    <li>부적절한 댓글이나 허위 댓글은 이벤트 대상에서 제외됩니다.</li>
                    <li>로그인 후 참여 가능합니다.</li>
                </ul>
            </div>

            <div class="comment-section">
                <h3>의견 남기기</h3>
                <form id="commentForm" class="comment-form" method="POST" action="../action/event_review_action.php">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                    <textarea id="comment" name="comment" placeholder="KISIA HOTEL에 대한 의견을 자유롭게 작성해주세요." required></textarea>
                    <button type="submit" class="event-submit-btn">등록하기</button>
                </form>
            </div>

            <div class="comments-list">
                <h3>등록된 의견</h3>
                <?php foreach ($event_comments as $event_comment): ?>
                <div class="comment-item">
                    <div class="comment-header">
                        <span class="comment-author"><?= htmlspecialchars($event_comment['username'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="comment-date"><?= htmlspecialchars($event_comment['created_at'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <p class="comment-text"><?= nl2br(htmlspecialchars($event_comment['comment'], ENT_QUOTES, 'UTF-8')) ?></p>
                </div>
                <?php endforeach; ?>

                <?php 
                    include_once __DIR__ . '/../includes/pagination.php';
                    pagination($total_event_comments, 5);
                ?>
            </div>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
