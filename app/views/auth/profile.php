<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-custom p-4 shadow-sm">
                <h3 class="fw-bold mb-4 text-center">Hồ sơ cá nhân</h3>
                <form method="POST" action="/Auth/profile" enctype="multipart/form-data">
                    <div class="text-center mb-4">
                        <img src="/public/uploads/avatars/<?php echo htmlspecialchars($user_info['avatar']); ?>" class="rounded-circle border" width="120" height="120" style="object-fit: cover;">
                        <div class="mt-3">
                            <label class="btn btn-sm btn-outline-secondary">
                                Thay đổi ảnh đại diện
                                <input type="file" name="avatar" class="d-none" accept="image/*">
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email (Không thể đổi)</label>
                        <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($user_info['email']); ?>" readonly>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Họ và tên</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user_info['name']); ?>" required>
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
<?php include 'app/views/shares/footer.php'; ?>