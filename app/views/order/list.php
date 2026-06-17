<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-4 pb-5">
    <h3 class="fw-bold mb-4">Quản lý đơn hàng</h3>
    <div class="card card-custom p-0 overflow-hidden shadow-sm">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Mã ĐH</th>
                    <th>Khách hàng</th>
                    <th>Điện thoại</th>
                    <th>Tổng tiền</th>
                    <th>Ngày đặt</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody class="bg-white" id="order-list"></tbody>
        </table>
    </div>
</div>
<?php include 'app/views/shares/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", loadOrders);

function loadOrders() {
    fetch('/api/order')
        .then(res => res.json())
        .then(result => {
            if (result.status === 'success') {
                let html = '';
                if (result.data.length === 0) {
                    html = '<tr><td colspan="7" class="text-center text-muted py-4">Chưa có đơn hàng nào.</td></tr>';
                } else {
                    const badges = {
                        'pending':    '<span class="badge bg-warning text-dark">Chờ xử lý</span>',
                        'processing': '<span class="badge bg-primary">Đang giao</span>',
                        'completed':  '<span class="badge bg-success">Hoàn thành</span>',
                        'canceled':   '<span class="badge bg-danger">Đã hủy</span>'
                    };
                    result.data.forEach(o => {
                        const date = new Date(o.created_at);
                        const dateStr = `${String(date.getDate()).padStart(2,'0')}/${String(date.getMonth()+1).padStart(2,'0')}/${date.getFullYear()} ${String(date.getHours()).padStart(2,'0')}:${String(date.getMinutes()).padStart(2,'0')}`;
                        html += `<tr>
                            <td class="align-middle fw-bold">#${o.id}</td>
                            <td class="align-middle">${o.customer_name}</td>
                            <td class="align-middle">${o.customer_phone}</td>
                            <td class="align-middle text-danger fw-bold">${Number(o.total_price).toLocaleString('vi-VN')}đ</td>
                            <td class="align-middle">${dateStr}</td>
                            <td class="align-middle">${badges[o.status] ?? o.status}</td>
                            <td class="align-middle">
                                <a href="/Order/detail/${o.id}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Xem</a>
                            </td>
                        </tr>`;
                    });
                }
                document.getElementById('order-list').innerHTML = html;
            }
        });
}
</script>
