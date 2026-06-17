<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-4 pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold m-0" id="order-title">Chi tiết đơn hàng</h3>
        <a href="/Order/list" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Quay lại</a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card card-custom p-3 shadow-sm" id="order-info-card">
                <div class="text-center text-muted py-3">Đang tải...</div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card card-custom p-0 overflow-hidden shadow-sm">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>SL</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white" id="order-details-list"></tbody>
                </table>
                <div class="card-footer bg-light text-end p-3">
                    <h4 class="m-0">Tổng cộng: <span class="text-danger fw-bold" id="order-total">...</span></h4>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'app/views/shares/footer.php'; ?>

<script>
const ORDER_ID = <?php echo $id ?? 0; ?>;

document.addEventListener("DOMContentLoaded", function() {
    fetch('/api/order/' + ORDER_ID)
        .then(res => res.json())
        .then(result => {
            if (result.status !== 'success') {
                alert('Không tìm thấy đơn hàng!');
                window.location.href = '/Order/list';
                return;
            }

            const o = result.data;
            document.getElementById('order-title').textContent = `Chi tiết đơn hàng #${o.id}`;

            const date = new Date(o.created_at);
            const dateStr = `${String(date.getDate()).padStart(2,'0')}/${String(date.getMonth()+1).padStart(2,'0')}/${date.getFullYear()} ${String(date.getHours()).padStart(2,'0')}:${String(date.getMinutes()).padStart(2,'0')}`;

            document.getElementById('order-info-card').innerHTML = `
                <h5 class="fw-bold border-bottom pb-2">Thông tin khách hàng</h5>
                <p class="mb-1"><strong>Tên:</strong> ${o.customer_name}</p>
                <p class="mb-1"><strong>SĐT:</strong> ${o.customer_phone}</p>
                <p class="mb-1"><strong>Địa chỉ:</strong> ${o.customer_address}</p>
                <p class="mb-1"><strong>Ngày đặt:</strong> ${dateStr}</p>
                <div class="mt-3 border-top pt-3">
                    <label class="fw-bold mb-2">Cập nhật trạng thái:</label>
                    <div class="input-group">
                        <select id="status-select" class="form-select">
                            <option value="pending" ${o.status == 'pending' ? 'selected' : ''}>Chờ xử lý</option>
                            <option value="processing" ${o.status == 'processing' ? 'selected' : ''}>Đang giao</option>
                            <option value="completed" ${o.status == 'completed' ? 'selected' : ''}>Hoàn thành</option>
                            <option value="canceled" ${o.status == 'canceled' ? 'selected' : ''}>Đã hủy</option>
                        </select>
                        <button class="btn btn-primary" onclick="updateStatus()">Lưu</button>
                    </div>
                </div>
            `;

            // Render chi tiết sản phẩm
            let detailHtml = '';
            o.details.forEach(item => {
                detailHtml += `<tr>
                    <td class="align-middle">
                        <img src="/public/images/${item.image}" width="50" class="rounded me-2">
                        ${item.name}
                    </td>
                    <td class="align-middle text-danger">${Number(item.price).toLocaleString('vi-VN')}đ</td>
                    <td class="align-middle">${item.quantity}</td>
                    <td class="align-middle fw-bold text-danger">${Number(item.price * item.quantity).toLocaleString('vi-VN')}đ</td>
                </tr>`;
            });
            document.getElementById('order-details-list').innerHTML = detailHtml;
            document.getElementById('order-total').textContent = Number(o.total_price).toLocaleString('vi-VN') + ' VNĐ';
        });
});

function updateStatus() {
    const status = document.getElementById('status-select').value;
    fetch('/api/order/' + ORDER_ID, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ status: status })
    })
    .then(res => res.json())
    .then(result => {
        alert(result.message);
    });
}
</script>