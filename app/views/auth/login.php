<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-custom p-4 shadow-sm">
                <h3 class="fw-bold mb-4 text-center">Đăng nhập</h3>
                <form method="POST" action="/Auth/login">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <label class="form-label fw-bold">Mật khẩu</label>
                        <a href="/Auth/forgotPassword" class="text-decoration-none small">Quên mật khẩu?</a>
                    </div>
                    <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Đăng nhập</button>
                    <div class="mt-3 text-center">
                    Chưa có tài khoản? <a href="/Auth/register" class="text-decoration-none fw-bold">Đăng ký ngay</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'app/views/shares/footer.php'; ?>