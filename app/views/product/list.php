<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-4 pb-5">
    
    <div class="mb-4">
        <img src="/public/images/banner.jpg" alt="Banner" class="img-fluid rounded-3 shadow-sm w-100" style="max-height: 300px; object-fit: cover;">
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h3 mb-0 fw-bold text-dark" id="page-title">Tất cả sản phẩm</h2>
    </div>
    
    <div class="card card-custom p-3 mb-4 shadow-sm border-0 bg-white">
        <form id="filter-form" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-muted small mb-1 fw-bold">Tìm kiếm tên</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="keyword" class="form-control border-start-0 ps-0 bg-light" placeholder="Nhập tên sản phẩm...">
                </div>
            </div>
            
            <div class="col-md-3">
                <label class="form-label text-muted small mb-1 fw-bold">Giá thấp nhất</label>
                <input type="number" id="min_price" class="form-control bg-light" placeholder="VD: 500000">
            </div>
            
            <div class="col-md-3">
                <label class="form-label text-muted small mb-1 fw-bold">Giá cao nhất</label>
                <input type="number" id="max_price" class="form-control bg-light" placeholder="VD: 5000000">
            </div>
            
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="bi bi-funnel-fill me-1"></i> Lọc</button>
                <button type="button" onclick="resetFilter()" class="btn btn-light border px-3 text-secondary" title="Xóa bộ lọc"><i class="bi bi-arrow-clockwise"></i></button>
            </div>
        </form>
    </div>

    <div class="row g-4" id="product-list">
        </div>
    
</div>
<?php include 'app/views/shares/footer.php'; ?>

<script>
const IS_ADMIN = <?php echo (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') ? 'true' : 'false'; ?>;

document.addEventListener("DOMContentLoaded", function() {
    let urlParams = new URLSearchParams(window.location.search);
    let categoryId = urlParams.get('category_id');
    
    // Gán giá trị từ URL vào form nếu có
    if(urlParams.get('keyword')) document.getElementById('keyword').value = urlParams.get('keyword');
    if(urlParams.get('min_price')) document.getElementById('min_price').value = urlParams.get('min_price');
    if(urlParams.get('max_price')) document.getElementById('max_price').value = urlParams.get('max_price');

    loadProducts(categoryId);
});

document.getElementById('filter-form').addEventListener('submit', function(e) {
    e.preventDefault();
    let urlParams = new URLSearchParams(window.location.search);
    let categoryId = urlParams.get('category_id');
    loadProducts(categoryId);
});

function resetFilter() {
    document.getElementById('keyword').value = '';
    document.getElementById('min_price').value = '';
    document.getElementById('max_price').value = '';
    let urlParams = new URLSearchParams(window.location.search);
    loadProducts(urlParams.get('category_id'));
}

function loadProducts(categoryId = null) {
    let keyword = document.getElementById('keyword').value;
    let minPrice = document.getElementById('min_price').value;
    let maxPrice = document.getElementById('max_price').value;

    let url = '/api/product?';
    if (categoryId) url += `category_id=${categoryId}&`;
    if (keyword) url += `search=${encodeURIComponent(keyword)}&`;
    if (minPrice) url += `min_price=${minPrice}&`;
    if (maxPrice) url += `max_price=${maxPrice}&`;

    fetch(url)
        .then(res => res.json())
        .then(result => {
            if (result.status === 'success') {
                let html = '';
                if (result.data.length === 0) {
                    html = '<div class="col-12"><div class="alert alert-light text-center border text-muted py-5">Không tìm thấy sản phẩm nào phù hợp với bộ lọc.</div></div>';
                } else {
                    result.data.forEach(product => {
                        let adminButtons = IS_ADMIN ? `
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="/Product/edit/${product.id}" class="btn btn-outline-secondary w-100 fw-medium btn-sm"><i class="bi bi-pencil-square"></i></a>
                                </div>
                                <div class="col-6">
                                    <button onclick="deleteProduct(${product.id})" class="btn btn-outline-danger w-100 fw-medium btn-sm"><i class="bi bi-trash3"></i></button>
                                </div>
                            </div>
                        ` : '';

                        html += `
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card h-100 shadow-sm card-custom overflow-hidden">
                                <a href="/Product/show/${product.id}">
                                    <img src="/public/images/${product.image}" class="card-img-top w-100">
                                </a>
                                <div class="card-body d-flex flex-column pb-0">
                                    <a href="/Product/show/${product.id}" class="text-decoration-none text-dark">
                                        <h5 class="card-title fw-bold text-truncate" title="${product.name}">${product.name}</h5>
                                    </a>
                                    <p class="text-danger fw-bold fs-5 mb-3">${Number(product.price).toLocaleString('vi-VN')} VNĐ</p>
                                </div>
                                <div class="card-footer bg-white border-top-0 p-3 pt-0">
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <a href="/Product/show/${product.id}" class="btn btn-outline-primary w-100 fw-medium btn-sm"><i class="bi bi-eye"></i> Xem</a>
                                        </div>
                                        <div class="col-6">
                                            <a href="javascript:void(0)" onclick="addToCart(${product.id})" class="btn btn-warning w-100 fw-bold btn-sm"><i class="bi bi-cart-plus"></i> Mua</a>
                                        </div>
                                    </div>
                                    ${adminButtons}
                                </div>
                            </div>
                        </div>`;
                    });
                }
                document.getElementById('product-list').innerHTML = html;
            }
        });

    // Cập nhật Title trang nếu có categoryId
    if (categoryId) {
        fetch('/api/category/' + categoryId)
            .then(res => res.json())
            .then(result => {
                if(result.status === 'success') document.getElementById('page-title').innerText = result.data.name;
            });
    } else {
        document.getElementById('page-title').innerText = "Tất cả sản phẩm";
    }
}

function deleteProduct(id) {
    if (confirm('Chắc chắn xóa?')) {
        fetch('/api/product/' + id, { method: 'DELETE' })
            .then(res => res.json())
            .then(result => {
                alert(result.message);
                if (result.status === 'success') loadProducts(new URLSearchParams(window.location.search).get('category_id'));
            });
    }
}

function addToCart(productId) {
    const USER_ID = <?php echo isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 'null'; ?>;
    if(!USER_ID) {
        alert("Vui lòng đăng nhập để mua hàng!");
        window.location.href = '/Auth/login';
        return;
    }

    fetch('/api/cart', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: USER_ID, product_id: productId, quantity: 1 })
    })
    .then(res => res.json())
    .then(result => {
        alert(result.message);
        if(result.status === 'success') updateCartBadge();
    });
}
</script>