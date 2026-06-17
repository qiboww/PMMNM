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
                    
                    <div id="auth-nav"></div>
                </div>
            </div>
        </div>
    </nav>

<script>
// Intercept all fetch calls globally to inject the Bearer Token
const originalFetch = window.fetch;
window.fetch = function(url, options = {}) {
    const token = localStorage.getItem('jwt_token');
    
    // Check if the URL belongs to our API endpoints
    const isApi = (typeof url === 'string') && (
        url.startsWith('/api') || 
        url.startsWith(window.location.origin + '/api')
    );
    
    if (isApi) {
        if (!options.headers) {
            options.headers = {};
        }
        if (token) {
            options.headers['Authorization'] = 'Bearer ' + token;
        }
        if (!options.headers['Content-Type'] && !(options.body instanceof FormData)) {
            options.headers['Content-Type'] = 'application/json';
        }
    }
    
    return originalFetch(url, options).then(response => {
        if (response.status === 401 && isApi) {
            localStorage.removeItem('jwt_token');
            localStorage.removeItem('user');
            if (!window.location.pathname.toLowerCase().includes('/auth/login')) {
                alert('Phiên làm việc đã hết hạn hoặc chưa đăng nhập. Vui lòng đăng nhập lại.');
                window.location.href = '/Auth/login';
            }
        }
        return response;
    });
};

document.addEventListener("DOMContentLoaded", function() {
    renderAuthNav();
    loadNavCategories();
    updateCartBadge();
});

function renderAuthNav() {
    const token = localStorage.getItem('jwt_token');
    const userStr = localStorage.getItem('user');
    const authNav = document.getElementById('auth-nav');
    if (!authNav) return;

    if (token && userStr) {
        const user = JSON.parse(userStr);
        let adminMenus = '';
        if (user.role === 'admin') {
            adminMenus = `
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-primary" href="/Category/list">Quản lý danh mục</a></li>
                <li><a class="dropdown-item text-primary" href="/Product/add">Thêm sản phẩm</a></li>
                <li><a class="dropdown-item text-primary" href="/User/list">Quản lý người dùng</a></li>
                <li><a class="dropdown-item text-primary" href="/Order/list">Quản lý đơn hàng</a></li>
            `;
        }
        
        authNav.innerHTML = `
            <div class="dropdown">
                <button class="btn btn-light btn-sm fw-bold dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i> ${user.name}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="/Auth/profile">Hồ sơ cá nhân</a></li>
                    <li><a class="dropdown-item" href="/Order/history"><i class="bi bi-clock-history me-1 text-primary"></i>Lịch sử đơn hàng</a></li>
                    ${adminMenus}
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="logout()">Đăng xuất</a></li>
                </ul>
            </div>
        `;
    } else {
        authNav.innerHTML = `
            <a href="/Auth/login" class="btn btn-outline-light btn-sm fw-bold">Đăng nhập</a>
            <a href="/Auth/register" class="btn btn-primary btn-sm fw-bold">Đăng ký</a>
        `;
    }
}

function logout() {
    localStorage.removeItem('jwt_token');
    localStorage.removeItem('user');
    // Also hit server logout if using any PHP session fallbacks
    window.location.href = '/Auth/login';
}

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
    const token = localStorage.getItem('jwt_token');
    if(!token) return;

    fetch('/api/cart')
        .then(res => res.json())
        .then(result => {
            if (result.status === 'success') {
                let totalQty = result.data.reduce((sum, item) => sum + parseInt(item.quantity), 0);
                document.getElementById('cart-badge').innerText = totalQty;
            }
        });
}
</script>