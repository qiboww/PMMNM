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
            <tbody class="bg-white" id="user-list"></tbody>
        </table>
    </div>
</div>
<?php include 'app/views/shares/footer.php'; ?>

<script>
const userStr = localStorage.getItem('user');
const user = userStr ? JSON.parse(userStr) : null;
const CURRENT_USER_ID = user ? user.id : null;

document.addEventListener("DOMContentLoaded", loadUsers);

function loadUsers() {
    fetch('/api/user')
        .then(res => res.json())
        .then(result => {
            if (result.status === 'success') {
                let html = '';
                result.data.forEach(u => {
                    const roleBadge = u.role == 'admin'
                        ? '<span class="badge bg-danger">Admin</span>'
                        : '<span class="badge bg-secondary">User</span>';

                    let statusBadge = '';
                    if (u.is_locked == 1) {
                        statusBadge = '<span class="badge bg-danger">Bị khóa</span>';
                    } else if (u.is_verified == 0) {
                        statusBadge = '<span class="badge bg-warning text-dark">Chưa xác thực</span>';
                    } else {
                        statusBadge = '<span class="badge bg-success">Đang hoạt động</span>';
                    }

                    let actionBtn = '';
                    if (u.role != 'admin') {
                        const btnClass = u.is_locked == 1 ? 'btn-success' : 'btn-danger';
                        const btnIcon  = u.is_locked == 1 ? 'bi-unlock' : 'bi-lock';
                        const btnText  = u.is_locked == 1 ? 'Mở khóa' : 'Khóa';
                        actionBtn = `<button onclick="toggleLock(${u.id})" class="btn btn-sm ${btnClass}"><i class="bi ${btnIcon}"></i> ${btnText}</button>`;
                    }

                    html += `<tr>
                        <td class="align-middle">${u.id}</td>
                        <td class="align-middle fw-bold">${u.name}</td>
                        <td class="align-middle">${u.email}</td>
                        <td class="align-middle">${roleBadge}</td>
                        <td class="align-middle">${statusBadge}</td>
                        <td class="align-middle">${actionBtn}</td>
                    </tr>`;
                });
                document.getElementById('user-list').innerHTML = html || '<tr><td colspan="6" class="text-center text-muted py-4">Không có người dùng nào.</td></tr>';
            }
        });
}

function toggleLock(id) {
    if (!confirm('Xác nhận thay đổi trạng thái?')) return;

    fetch('/api/user/' + id + '/toggleLock', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(res => res.json())
    .then(result => {
        alert(result.message);
        if (result.status === 'success') loadUsers();
    });
}
</script>