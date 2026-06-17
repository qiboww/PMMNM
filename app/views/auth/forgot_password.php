<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-custom p-4 shadow-sm">
                <h3 class="fw-bold mb-3 text-center">Quên mật khẩu</h3>
                <p class="text-muted text-center small mb-4">Nhập email đăng ký tài khoản để nhận liên kết khôi phục.</p>
                <form method="POST" action="/Auth/forgotPassword">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Email của bạn</label>
                        <input type="email" name="email" class="form-control" required placeholder="example@gmail.com">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Gửi yêu cầu</button>
                    <div class="mt-3 text-center">
                        <a href="/Auth/login" class="text-decoration-none small">Quay lại đăng nhập</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'app/views/shares/footer.php'; ?>