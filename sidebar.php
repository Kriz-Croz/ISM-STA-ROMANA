<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<aside id="sidebar" class="js-sidebar">
    <div class="h-100">
        <div class="sidebar-logo text-center py-3">
            <img src="logo3.png" alt="">
        </div>
        <ul class="sidebar-nav list-unstyled px-3">
            <li class="sidebar-item">
                <a href="dashboard.php" class="sidebar-link d-flex align-items-center py-2">
                    <i class="fa-solid fa-list pe-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="sidebar-item">
                <a href="notification.php" class="sidebar-link d-flex align-items-center py-2">
                    <i class="fa-solid fa-list pe-2"></i>
                    Notification
                    <span id="notification-badge" class="badge bg-danger ms-2" style="display: none;">0</span>
                </a>
            </li>
            <?php
            $current_page = basename($_SERVER['PHP_SELF']);
            $product_pages = ['product.php', 'brand.php', 'category.php'];
            $is_product_active = in_array($current_page, $product_pages);
            ?>
            <li class="sidebar-item">
                <a href="#" class="sidebar-link d-flex align-items-center py-2 <?= $is_product_active ? '' : 'collapsed' ?>" 
                   data-bs-toggle="collapse" 
                   data-bs-target="#product-menu" 
                   aria-expanded="<?= $is_product_active ? 'true' : 'false' ?>">
                    <i class="fa-solid fa-file-lines pe-2"></i>
                    Product
                </a>
                <ul id="product-menu" class="collapse list-unstyled ps-4 <?= $is_product_active ? 'show' : '' ?>">
                    <li>
                        <a href="product.php" class="sidebar-link d-block py-1 <?= $current_page === 'product.php' ? 'active' : '' ?>">Product Details</a>
                    </li>
                    <li>
                        <a href="brand.php" class="sidebar-link d-block py-1 <?= $current_page === 'brand.php' ? 'active' : '' ?>">Brands</a>
                    </li>
                    <li>
                        <a href="category.php" class="sidebar-link d-block py-1 <?= $current_page === 'category.php' ? 'active' : '' ?>">Category</a>
                    </ul>
                </li>
            <li class="sidebar-item">
                <a href="supplier.php" class="sidebar-link d-flex align-items-center py-2">
                    <i class="fa-solid fa-sliders pe-2"></i>
                    Supplier
                </a>
            </li>
            <li class="sidebar-item">
                <a href="addsale.php" class="sidebar-link d-flex align-items-center py-2">
                    <i class="fa-solid fa-sliders pe-2"></i>
                    Add Sale
                </a>
                <a href="sales.php" class="sidebar-link d-flex align-items-center py-2">
                    <i class="fa-solid fa-sliders pe-2"></i>
                    Sales
                </a>
                <a href="salesreport.php" class="sidebar-link d-flex align-items-center py-2">
                    <i class="fa-solid fa-sliders pe-2"></i>
                    Sales Report
                </a>
            </li>
            <li class="sidebar-item" style="margin-top: 20px;">
                <a href="db/logout.php" class="sidebar-link d-flex align-items-center py-2" 
                   style="color: darkred; font-weight: bold;" 
                   onmouseover="this.style.color='red';" 
                   onmouseout="this.style.color='darkred';">
                    <i class="fa-solid fa-right-from-bracket pe-2"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>
</aside>

<script>
    function updateNotificationBadge() {
        fetch('notification_count.php') 
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notification-badge');
                if (data.notificationCount > 0) {
                    badge.textContent = data.notificationCount;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            })
            .catch(error => console.error('Error updating notification badge:', error));
    }

    setInterval(updateNotificationBadge, 1000);

    document.addEventListener('DOMContentLoaded', updateNotificationBadge);
</script>
