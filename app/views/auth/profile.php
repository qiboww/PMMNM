<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-custom p-4 shadow-sm">
                <h3 class="fw-bold mb-4 text-center">Hồ sơ cá nhân</h3>
                <form id="profile-form">
                    <div class="text-center mb-4">
                        <img id="profile-avatar" src="/public/uploads/avatars/default.png" class="rounded-circle border" width="120" height="120" style="object-fit: cover;">
                        <div class="mt-3">
                            <label class="btn btn-sm btn-outline-secondary">
                                Thay đổi ảnh đại diện (Tên file)
                                <input type="text" id="avatar-filename" class="form-control form-control-sm mt-1" placeholder="avatar.png">
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email (Không thể đổi)</label>
                        <input type="email" id="email" class="form-control bg-light" readonly>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Họ và tên</label>
                        <input type="text" id="name" class="form-control" required>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Lưu thay đổi</button>
                        <a href="/Auth/changePassword" class="btn btn-warning w-100 fw-bold py-2">Đổi mật khẩu</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Tải thông tin cá nhân
    fetch('/api/user/profile')
        .then(res => res.json())
        .then(result => {
            if (result.status === 'success') {
                document.getElementById('email').value = result.data.email;
                document.getElementById('name').value = result.data.name;
                document.getElementById('avatar-filename').value = result.data.avatar || 'default.png';
                if(result.data.avatar) {
                    document.getElementById('profile-avatar').src = "/public/uploads/avatars/" + result.data.avatar;
                }
            }
        });
});

document.getElementById('profile-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const name = document.getElementById('name').value;
    const avatar = document.getElementById('avatar-filename').value;

    fetch('/api/user/profile', {
        method: 'PUT',
        body: JSON.stringify({ name, avatar })
    })
    .then(res => res.json())
    .then(result => {
        alert(result.message);
        if (result.status === 'success') {
            // Cập nhật lại user trong localStorage
            localStorage.setItem('user', JSON.stringify(result.user));
            renderAuthNav();
        }
    })
    .catch(err => {
        alert('Lỗi hệ thống khi cập nhật hồ sơ!');
        console.error(err);
    });
});
</script>
<?php include 'app/views/shares/footer.php'; ?>