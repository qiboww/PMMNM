<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-custom p-4 shadow-sm">
                <h3 class="fw-bold mb-4 text-center">Đăng ký tài khoản</h3>
                <form id="register-form">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Họ và tên</label>
                        <input type="text" id="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mật khẩu</label>
                        <input type="password" id="password" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Xác nhận mật khẩu</label>
                        <input type="password" id="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Đăng ký</button>
                </form>
                <div class="mt-3 text-center">
                    Đã có tài khoản? <a href="/Auth/login" class="text-decoration-none fw-bold">Đăng nhập</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('register-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirm_password = document.getElementById('confirm_password').value;

    fetch('/api/user/register', {
        method: 'POST',
        body: JSON.stringify({ name, email, password, confirm_password })
    })
    .then(res => res.json())
    .then(result => {
        alert(result.message);
        if (result.status === 'success') {
            window.location.href = '/Auth/login';
        }
    })
    .catch(err => {
        alert('Lỗi hệ thống khi đăng ký!');
        console.error(err);
    });
});
</script>
<?php include 'app/views/shares/footer.php'; ?>