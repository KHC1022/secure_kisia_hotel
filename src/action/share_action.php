<?php
$current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>

<div class="spi_overlay" id="shareOverlay"></div>
<div class="spi_copyurl" id="shareBox">
    <a href="#" class="_spi_copyurl_txt" id="shareUrl"><?php echo $current_url; ?></a>
    <a href="#" class="_spi_btn_copyurl" onclick="copyUrl(event)">복사</a>
</div>

<script>
function showShareBox() {
    const overlay = document.getElementById('shareOverlay');
    const shareBox = document.getElementById('shareBox');
    const shareUrl = document.getElementById('shareUrl');
    
    // 현재 페이지 URL 가져오기
    const currentUrl = window.location.href;
    shareUrl.textContent = currentUrl;
    shareUrl.href = currentUrl;
    
    overlay.classList.add('show');
    shareBox.classList.add('show');
}

function copyUrl(event) {
    event.preventDefault();
    const url = document.getElementById('shareUrl').textContent;
    navigator.clipboard.writeText(url)
        .then(() => {
            alert('링크가 복사되었습니다.');
            hideShareBox();
        })
        .catch(err => {
            alert('링크 복사에 실패했습니다.');
        });
}

function hideShareBox() {
    const overlay = document.getElementById('shareOverlay');
    const shareBox = document.getElementById('shareBox');
    
    overlay.classList.remove('show');
    shareBox.classList.remove('show');
}

// 오버레이 클릭 시 닫기
document.getElementById('shareOverlay').addEventListener('click', hideShareBox);
</script> 