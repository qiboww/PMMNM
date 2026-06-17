<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-5 pb-5">
    <div class="row g-4" id="product-detail-container">
        <div class="text-center py-5 text-muted">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Đang tải chi tiết sản phẩm...</p>
        </div>
    </div>
</div>
<?php include 'app/views/shares/footer.php'; ?>

<script>
const pathParts = window.location.pathname.split('/');
const productId = pathParts[pathParts.length - 1];

document.addEventListener("DOMContentLoaded", function() {
    fetch('/api/product/' + productId)
        .then(res => res.json())
        .then(result => {
            const container = document.getElementById('product-detail-container');
            if (result.status !== 'success') {
                container.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <h4 class="text-danger">Không tìm thấy sản phẩm hoặc có lỗi xảy ra!</h4>
                        <a href="/Product/list" class="btn btn-primary mt-3">Quay lại danh sách</a>
                    </div>`;
                return;
            }

            const p = result.data;
            container.innerHTML = `
                <div class="col-md-6 text-center">
                    <img src="/public/images/${p.image}" class="img-fluid rounded shadow-sm border" style="max-height: 450px; object-fit: contain;">
                </div>
                <div class="col-md-6 d-flex flex-column justify-content-center">
                    <h2 class="fw-bold mb-3">${p.name}</h2>
                    <h3 class="text-danger fw-bold mb-4">${Number(p.price).toLocaleString('vi-VN')} VNĐ</h3>
                    <div class="mb-4">
                        <h5 class="fw-bold text-muted border-bottom pb-2">Mô tả sản phẩm</h5>
                        <p class="text-secondary" style="white-space: pre-line;">${p.description || 'Chưa có mô tả cho sản phẩm này.'}</p>
                    </div>
                    <div class="d-flex gap-3 mt-3">
                        <a href="javascript:void(0)" onclick="addToCart(${p.id})" class="btn btn-warning btn-lg fw-bold px-4 py-3"><i class="bi bi-cart-plus me-2"></i>Thêm vào giỏ hàng</a>
                        <a href="/Product/list" class="btn btn-outline-secondary btn-lg px-4 py-3">Quay lại</a>
                    </div>
                </div>
            `;
        });
});

function addToCart(productId) {
    const userStr = localStorage.getItem('user');
    const user = userStr ? JSON.parse(userStr) : null;
    if(!user) {
        alert("Vui lòng đăng nhập để mua hàng!");
        window.location.href = '/Auth/login';
        return;
    }
    const USER_ID = user.id;

    fetch('/api/cart', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId, quantity: 1 })
    })
    .then(res => res.json())
    .then(result => {
        alert(result.message);
        if(result.status === 'success') updateCartBadge();
    });
}
</script>
