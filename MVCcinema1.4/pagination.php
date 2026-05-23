<?php if (isset($total_pages) && $total_pages > 1): ?>
<nav aria-label="Page navigation" class="mt-5 mb-5 w-100 d-flex justify-content-center">
    <ul class="pagination pagination-modern">
        <?php
        $qs = $_GET;
        unset($qs['page']);
        $qs_base = http_build_query($qs);
        $prefix = $qs_base ? '&' : '';
        ?>
        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
            <a class="page-link btn-prev" href="?<?php echo $qs_base . $prefix; ?>page=<?php echo $page - 1; ?>">
                <i class="bi bi-arrow-left me-2"></i> Trước
            </a>
        </li>
        
        <?php 
        $start = max(1, $page - 2);
        $end = min($total_pages, $page + 2);
        
        if ($start > 1): ?>
            <li class="page-item"><a class="page-link" href="?<?php echo $qs_base . $prefix; ?>page=1">1</a></li>
            <?php if ($start > 2): ?><li class="page-item dots disabled"><span class="page-link">...</span></li><?php endif; ?>
        <?php endif; ?>
        
        <?php for($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                <a class="page-link" href="?<?php echo $qs_base . $prefix; ?>page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
        
        <?php if ($end < $total_pages): ?>
            <?php if ($end < $total_pages - 1): ?><li class="page-item dots disabled"><span class="page-link">...</span></li><?php endif; ?>
            <li class="page-item"><a class="page-link" href="?<?php echo $qs_base . $prefix; ?>page=<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a></li>
        <?php endif; ?>

        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
            <a class="page-link btn-next" href="?<?php echo $qs_base . $prefix; ?>page=<?php echo $page + 1; ?>">
                Sau <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </li>
    </ul>
</nav>

<style>
.pagination-modern {
    display: flex;
    gap: 12px;
    align-items: center;
    margin: 0;
    padding: 0;
}
.pagination-modern .page-item .page-link {
    border-radius: 50% !important;
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    background-color: #ffffff;
    color: #495057;
    font-weight: 700;
    font-size: 1rem;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}
.pagination-modern .page-item .page-link.btn-prev,
.pagination-modern .page-item .page-link.btn-next {
    border-radius: 30px !important;
    width: auto;
    padding: 0 24px;
    font-weight: 600;
}
.pagination-modern .page-item.active .page-link {
    background-image: linear-gradient(135deg, var(--primary-color, #dc3545) 0%, #ff4b5c 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 8px 15px rgba(220, 53, 69, 0.4);
    transform: translateY(-3px);
}
.pagination-modern .page-item:not(.active):not(.disabled):hover .page-link {
    background-color: #f8f9fa;
    color: var(--primary-color, #dc3545);
    transform: translateY(-3px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.1);
}
.pagination-modern .page-item.disabled .page-link {
    background-color: #f8f9fa;
    color: #ced4da;
    box-shadow: none;
    cursor: not-allowed;
}
.pagination-modern .page-item.dots .page-link {
    background: transparent;
    box-shadow: none;
    pointer-events: none;
    color: #adb5bd;
    width: auto;
}
</style>
<?php endif; ?>
