<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-custom p-4 shadow-sm">
                <h3 class="fw-bold mb-4 text-center">Thông tin giao hàng</h3>
                <form method="POST" action="/Cart/checkout">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Họ tên người nhận</label>
                        <input type="text" name="customer_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Số điện thoại</label>
                        <input type="text" name="customer_phone" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Địa chỉ giao hàng chi tiết</label>
                        <textarea name="customer_address" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Xác nhận đặt hàng</button>
                    <a href="/Cart/index" class="btn btn-light border w-100 mt-2">Quay lại giỏ hàng</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'app/views/shares/footer.php'; ?>