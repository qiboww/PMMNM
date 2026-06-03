<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-4 pb-5">
    <h3 class="fw-bold mb-4">Quản lý người dùng</h3>
    <div class="card card-custom p-0 overflow-hidden shadow-sm">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                <?php foreach($users as $u): ?>
                <tr>
                    <td class="align-middle"><?php echo $u->id; ?></td>
                    <td class="align-middle fw-bold"><?php echo htmlspecialchars($u->name); ?></td>
                    <td class="align-middle"><?php echo htmlspecialchars($u->email); ?></td>
                    <td class="align-middle">
                        <?php if($u->role == 'admin'): ?>
                            <span class="badge bg-danger">Admin</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">User</span>
                        <?php endif; ?>
                    </td>
                    <td class="align-middle">
                        <?php if($u->is_locked): ?>
                            <span class="badge bg-danger">Bị khóa</span>
                        <?php elseif(!$u->is_verified): ?>
                            <span class="badge bg-warning text-dark">Chưa xác thực</span>
                        <?php else: ?>
                            <span class="badge bg-success">Đang hoạt động</span>
                        <?php endif; ?>
                    </td>
                    <td class="align-middle">
                        <?php if($u->role != 'admin'): ?>
                            <a href="/User/toggleLock/<?php echo $u->id; ?>" class="btn btn-sm <?php echo $u->is_locked ? 'btn-success' : 'btn-danger'; ?>" onclick="return confirm('Xác nhận thay đổi trạng thái?');">
                                <i class="bi <?php echo $u->is_locked ? 'bi-unlock' : 'bi-lock'; ?>"></i>
                                <?php echo $u->is_locked ? 'Mở khóa' : 'Khóa'; ?>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'app/views/shares/footer.php'; ?>