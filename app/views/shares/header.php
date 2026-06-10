<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa Hàng Cầu Lông</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/public/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow">
        <div class="container-xl">
            <a class="navbar-brand fw-bold fs-3" href="/Product/list"><i class="bi bi-lightning-fill me-1"></i>BADMINTON</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0" id="nav-categories">
                    <li class="nav-item">
                        <a class="nav-link text-white fw-bold" href="/Product/list">Tất cả sản phẩm</a>
                    </li>
                </ul>
                <div class="d-flex gap-2 align-items-center">
                    <a href="/Cart/index" class="btn btn-warning btn-sm fw-bold me-2">
                        <i class="bi bi-cart3 me-1"></i>Giỏ hàng (<span class="text-danger" id="cart-badge">0</span>)
                    </a>
                    
                    <?php if (isset($_SESSION['user'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm fw-bold dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['user']['name']); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/Auth/profile">Hồ sơ cá nhân</a></li>
                                <li><a class="dropdown-item" href="/Order/history"><i class="bi bi-clock-history me-1 text-primary"></i>Lịch sử đơn hàng</a></li>
                                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-primary" href="/Category/list">Quản lý danh mục</a></li>
                                    <li><a class="dropdown-item text-primary" href="/Product/add">Thêm sản phẩm</a></li>
                                    <li><a class="dropdown-item text-primary" href="/User/list">Quản lý người dùng</a></li>
                                    <li><a class="dropdown-item text-primary" href="/Order/list">Quản lý đơn hàng</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="/Auth/logout">Đăng xuất</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="/Auth/login" class="btn btn-outline-light btn-sm fw-bold">Đăng nhập</a>
                        <a href="/Auth/register" class="btn btn-primary btn-sm fw-bold">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

<script>
document.addEventListener("DOMContentLoaded", function() {
    loadNavCategories();
    updateCartBadge();
});

function loadNavCategories() {
    let urlParams = new URLSearchParams(window.location.search);
    let currentCat = urlParams.get('category_id');

    fetch('/api/category')
        .then(res => res.json())
        .then(result => {
            if (result.status === 'success') {
                let html = `<li class="nav-item"><a class="nav-link ${!currentCat ? 'text-white fw-bold' : 'text-white-50'}" href="/Product/list">Tất cả sản phẩm</a></li>`;
                result.data.forEach(cat => {
                    let isActive = (currentCat == cat.id) ? 'text-white fw-bold' : 'text-white-50';
                    html += `<li class="nav-item"><a class="nav-link ${isActive}" href="/Product/list?category_id=${cat.id}">${cat.name}</a></li>`;
                });
                document.getElementById('nav-categories').innerHTML = html;
            }
        });
}

function updateCartBadge() {
    const USER_ID = <?php echo isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 'null'; ?>;
    if(!USER_ID) return;

    fetch('/api/cart?user_id=' + USER_ID)
        .then(res => res.json())
        .then(result => {
            if (result.status === 'success') {
                let totalQty = result.data.reduce((sum, item) => sum + parseInt(item.quantity), 0);
                document.getElementById('cart-badge').innerText = totalQty;
            }
        });
}
</script>