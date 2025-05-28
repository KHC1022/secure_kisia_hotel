<?php
// 현재 페이지 URL 계산 (HTML 엔티티로 이스케이프)
$current_url = htmlspecialchars("http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}", ENT_QUOTES, 'UTF-8');
?>

<!-- 공유 오버레이 UI -->
<div class="spi_overlay" id="shareOverlay"></div>
<div class="spi_copyurl" id="shareBox">
    <a href="#" class="_spi_copyurl_txt" id="shareUrl" target="_blank"><?= $current_url ?></a>
    <a href="#" class="_spi_btn_copyurl" onclick="copyUrl(event)">복사</a>
</div>

<!-- 공유 스크립트 -->
<script>
function showShareBox() {
    const overlay = document.getElementById('shareOverlay');
    const shareBox = document.getElementById('shareBox');
    const shareUrl = document.getElementById('shareUrl');

    const currentUrl = window.location.href;
    shareUrl.textContent = currentUrl;
    shareUrl.href = currentUrl;

    overlay.classList.add('show');
    shareBox.classList.add('show');
}

function hideShareBox() {
    document.getElementById('shareOverlay').classList.remove('show');
    document.getElementById('shareBox').classList.remove('show');
}

function copyUrl(event) {
    event.preventDefault();
    const url = document.getElementById('shareUrl').textContent;

    navigator.clipboard.writeText(url).then(() => {
        alert('링크가 복사되었습니다.');
        hideShareBox();
    }).catch(() => {
        alert('링크 복사에 실패했습니다.');
    });
}

// 오버레이 클릭 시 닫기
document.getElementById('shareOverlay').addEventListener('click', hideShareBox);
</script>
