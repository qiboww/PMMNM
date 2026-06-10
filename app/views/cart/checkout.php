<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-custom p-4 shadow-sm">
                <h3 class="fw-bold mb-4 text-center">Thông tin giao hàng</h3>
                <form id="form-checkout">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Họ tên người nhận</label>
                        <input type="text" id="customer_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Số điện thoại</label>
                        <input type="text" id="customer_phone" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Địa chỉ giao hàng chi tiết</label>
                        <textarea id="customer_address" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Xác nhận đặt hàng</button>
                    <a href="/Cart/index" class="btn btn-light border w-100 mt-2">Quay lại giỏ hàng</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'app/views/shares/footer.php'; ?>

<script>
const USER_ID = <?php echo isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 'null'; ?>;

document.addEventListener("DOMContentLoaded", function() {
    if (!USER_ID) {
        alert("Vui lòng đăng nhập để đặt hàng!");
        window.location.href = '/Auth/login';
    }
});

document.getElementById('form-checkout').addEventListener('submit', function(e) {
    e.preventDefault();
    let data = {
        user_id: USER_ID,
        customer_name: document.getElementById('customer_name').value,
        customer_phone: document.getElementById('customer_phone').value,
        customer_address: document.getElementById('customer_address').value
    };

    fetch('/api/order', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(result => {
        alert(result.message);
        if (result.status === 'success') {
            updateCartBadge();
            window.location.href = '/Product/list';
        }
    });
});
</script>
