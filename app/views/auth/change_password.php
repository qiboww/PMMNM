<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-custom p-4 shadow-sm">
                <h3 class="fw-bold mb-4 text-center">Đổi mật khẩu</h3>
                <form method="POST" action="/Auth/changePassword">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mật khẩu cũ</label>
                        <input type="password" name="old_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mật khẩu mới</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Xác nhận mật khẩu mới</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Cập nhật mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'app/views/shares/footer.php'; ?>