<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-custom p-4 shadow-sm">
                <h3 class="fw-bold mb-4 text-center">Đổi mật khẩu</h3>
                <form id="change-pwd-form">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mật khẩu cũ</label>
                        <input type="password" id="old_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mật khẩu mới</label>
                        <input type="password" id="new_password" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Xác nhận mật khẩu mới</label>
                        <input type="password" id="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Cập nhật mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('change-pwd-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const old_password = document.getElementById('old_password').value;
    const new_password = document.getElementById('new_password').value;
    const confirm_password = document.getElementById('confirm_password').value;

    fetch('/api/user/change-password', {
        method: 'PUT',
        body: JSON.stringify({ old_password, new_password, confirm_password })
    })
    .then(res => res.json())
    .then(result => {
        alert(result.message);
        if (result.status === 'success') {
            window.location.href = '/Auth/profile';
        }
    })
    .catch(err => {
        alert('Lỗi hệ thống khi đổi mật khẩu!');
        console.error(err);
    });
});
</script>
<?php include 'app/views/shares/footer.php'; ?>