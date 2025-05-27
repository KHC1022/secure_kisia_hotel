<?php

// tab용 페이징 함수
function Tabpagination($total_items, $items_per_page, $tab) {
    if ($total_items <= 0) return;

    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $current_page = max(1, $current_page);

    $total_pages = ceil($total_items / $items_per_page);

    $start_page = max(1, $current_page - 2);
    $end_page = min($total_pages, $start_page + 4);
    
    if ($end_page - $start_page < 4) {
        $start_page = max(1, $end_page - 4);
    }

    $params = $_GET;
    unset($params['page']);
    $query_string = http_build_query($params);
    $query_string = $query_string ? '&' . $query_string : '';
    ?>
    <div class="pagination">
        <a href="?tab=<?= $tab ?>&page=<?= max(1, $current_page - 1) . $query_string ?>" class="arrow" aria-label="이전 페이지">
            <i class="fas fa-angle-left"></i>
        </a>
        
        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="?tab=<?= $tab ?>&page=<?= $i . $query_string ?>" class="<?= (int)$i === (int)$current_page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
        
        <a href="?tab=<?= $tab ?>&page=<?= min($total_pages, $current_page + 1) . $query_string ?>" class="arrow" aria-label="다음 페이지">
            <i class="fas fa-angle-right"></i>
        </a>
    </div>
    <?php
}


// 일반 페이징 함수
function pagination($total_items, $items_per_page) {
    if ($total_items <= 0) return; 

    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $current_page = max(1, $current_page);

    $total_pages = ceil($total_items / $items_per_page);

    $start_page = max(1, $current_page - 2);
    $end_page = min($total_pages, $start_page + 4);
    
    if ($end_page - $start_page < 4) {
        $start_page = max(1, $end_page - 4);
    }

    $params = $_GET;
    unset($params['page']);
    $query_string = http_build_query($params);
    $query_string = $query_string ? '&' . $query_string : '';
    ?>
    <div class="pagination">
        <a href="?page=<?= max(1, $current_page - 1) . $query_string ?>" class="arrow" aria-label="이전 페이지">
            <i class="fas fa-angle-left"></i>
        </a>
        
        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="?page=<?= $i . $query_string ?>" class="<?= (int)$i === (int)$current_page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
        
        <a href="?page=<?= min($total_pages, $current_page + 1) . $query_string ?>" class="arrow" aria-label="다음 페이지">
            <i class="fas fa-angle-right"></i>
        </a>
    </div>
    <?php
}


// admin 페이지 - 일반 페이징 함수
function Adminpagination($current_page, $total_pages, $tab) {
    if ($total_pages <= 0) return; 

    $start_page = max(1, $current_page - 2);
    $end_page = min($total_pages, $start_page + 4);
    
    if ($end_page - $start_page < 4) {
        $start_page = max(1, $end_page - 4);
    }

    $params = $_GET;
    unset($params['page']); 
    $query_string = http_build_query($params);
    $query_string = $query_string ? '&' . $query_string : '';
    ?>
    <div class="admin-pagination">
        <a href="?tab=<?= $tab ?>&page=<?= max(1, $current_page - 1) . $query_string ?>" class="arrow" aria-label="이전 페이지">
            <i class="fas fa-angle-left"></i>
        </a>
        

        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="?tab=<?= $tab ?>&page=<?= $i . $query_string ?>" class="<?= (int)$i === (int)$current_page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
        
        <a href="?tab=<?= $tab ?>&page=<?= min($total_pages, $current_page + 1) . $query_string ?>" class="arrow" aria-label="다음 페이지">
            <i class="fas fa-angle-right"></i>
        </a>
    </div>
    <?php
}

// admin 페이지 - 검색용 페이징 함수
function searchPagination($current_page, $total_pages, $tab, $search_keyword) {
    if ($total_pages <= 0) return;   

    $start_page = max(1, $current_page - 2);
    $end_page = min($total_pages, $start_page + 4);
    
    if ($end_page - $start_page < 4) {
        $start_page = max(1, $end_page - 4);
    }

    // 검색 파라미터 이름 결정
    $search_param = '';
    switch ($tab) {
        case 'users':
            $search_param = 'user_name_search';
            break;
        case 'hotels':
            $search_param = 'hotel_name_search';
            break;
        case 'reservations':
            $search_param = 'reservation_number_search';
            break;
        case 'reviews':
            $search_param = 'review_number_search';
            break;
        case 'inquiries':
            $search_param = 'inquiry_number_search';
            break;
    }

    $params = $_GET;
    unset($params['page']); 
    $query_string = http_build_query($params);
    $query_string = $query_string ? '&' . $query_string : '';
    ?>
    <div class="admin-pagination">
        <a href="?tab=<?= $tab ?>&page=<?= max(1, $current_page - 1) ?>&<?= $search_param ?>=<?= urlencode($search_keyword) . $query_string ?>" class="arrow" aria-label="이전 페이지">
            <i class="fas fa-angle-left"></i>
        </a>
        
        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="?tab=<?= $tab ?>&page=<?= $i ?>&<?= $search_param ?>=<?= urlencode($search_keyword) . $query_string ?>" class="<?= (int)$i === (int)$current_page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
        
        <a href="?tab=<?= $tab ?>&page=<?= min($total_pages, $current_page + 1) ?>&<?= $search_param ?>=<?= urlencode($search_keyword) . $query_string ?>" class="arrow" aria-label="다음 페이지">
            <i class="fas fa-angle-right"></i>
        </a>
    </div>
    <?php
}

?> 