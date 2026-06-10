<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-4 pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Lịch sử đơn hàng</h3>
        <a href="/Product/list" class="btn btn-outline-primary btn-sm"><i class="bi bi-bag me-1"></i>Tiếp tục mua sắm</a>
    </div>

    <div id="history-container">
        <div class="text-center py-5 text-muted">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Đang tải lịch sử...</p>
        </div>
    </div>

</div>
<?php include 'app/views/shares/footer.php'; ?>

<script>
const USER_ID = <?php echo isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 'null'; ?>;

document.addEventListener("DOMContentLoaded", function() {
    if (!USER_ID) {
        alert("Vui lòng đăng nhập để xem lịch sử đơn hàng!");
        window.location.href = '/Auth/login';
        return;
    }
    loadHistory();
});

function loadHistory() {
    fetch('/api/order?user_id=' + USER_ID)
        .then(res => res.json())
        .then(result => {
            const container = document.getElementById('history-container');
            if (result.status !== 'success' || result.data.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-bag-x text-muted" style="font-size: 4rem;"></i>
                        <p class="mt-3 text-muted fs-5">Bạn chưa có đơn hàng nào.</p>
                        <a href="/Product/list" class="btn btn-primary mt-2">Mua sắm ngay</a>
                    </div>`;
                return;
            }

            const badges = {
                'pending':    '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Chờ xử lý</span>',
                'processing': '<span class="badge bg-primary"><i class="bi bi-truck me-1"></i>Đang giao</span>',
                'completed':  '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Hoàn thành</span>',
                'canceled':   '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Đã hủy</span>'
            };

            let html = '';
            result.data.forEach(o => {
                const date = new Date(o.created_at);
                const dateStr = `${String(date.getDate()).padStart(2,'0')}/${String(date.getMonth()+1).padStart(2,'0')}/${date.getFullYear()} ${String(date.getHours()).padStart(2,'0')}:${String(date.getMinutes()).padStart(2,'0')}`;

                html += `
                <div class="card card-custom shadow-sm mb-3 border-0 overflow-hidden">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <div>
                            <span class="fw-bold text-primary fs-6">#ĐH${String(o.id).padStart(4,'0')}</span>
                            <span class="text-muted small ms-3"><i class="bi bi-calendar3 me-1"></i>${dateStr}</span>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            ${badges[o.status] ?? o.status}
                            <button class="btn btn-sm btn-outline-secondary" onclick="toggleDetail(${o.id})">
                                <i class="bi bi-chevron-down" id="icon-${o.id}"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body pb-0" id="summary-${o.id}">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <p class="mb-1 text-muted small">Người nhận</p>
                                <p class="fw-bold mb-0">${o.customer_name}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1 text-muted small">Điện thoại</p>
                                <p class="fw-bold mb-0">${o.customer_phone}</p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <p class="mb-1 text-muted small">Tổng tiền</p>
                                <p class="fw-bold fs-5 text-danger mb-0">${Number(o.total_price).toLocaleString('vi-VN')} VNĐ</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-3 d-none" id="detail-${o.id}">
                        <hr class="mt-0">
                        <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i>${o.customer_address}</p>
                        <div id="items-${o.id}" class="text-center text-muted py-2">
                            <div class="spinner-border spinner-border-sm"></div> Đang tải sản phẩm...
                        </div>
                        ${o.status === 'pending' ? `
                        <div class="mt-3 text-end">
                            <button onclick="cancelOrder(${o.id})" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-x-circle me-1"></i>Hủy đơn hàng
                            </button>
                        </div>` : ''}
                    </div>
                </div>`;
            });
            container.innerHTML = html;
        });
}

function toggleDetail(orderId) {
    const detailEl = document.getElementById('detail-' + orderId);
    const iconEl   = document.getElementById('icon-' + orderId);
    const isHidden = detailEl.classList.contains('d-none');

    detailEl.classList.toggle('d-none', !isHidden);
    iconEl.classList.toggle('bi-chevron-down', !isHidden);
    iconEl.classList.toggle('bi-chevron-up', isHidden);

    // Load sản phẩm lần đầu mở
    if (isHidden && document.getElementById('items-' + orderId).querySelector('.spinner-border')) {
        fetch('/api/order/' + orderId)
            .then(res => res.json())
            .then(result => {
                if (result.status !== 'success') return;
                let html = '<table class="table table-sm table-bordered mb-0"><thead><tr><th>Sản phẩm</th><th>Đơn giá</th><th>SL</th><th>Thành tiền</th></tr></thead><tbody>';
                result.data.details.forEach(item => {
                    html += `<tr>
                        <td><img src="/public/images/${item.image}" width="40" class="rounded me-2">${item.name}</td>
                        <td>${Number(item.price).toLocaleString('vi-VN')}đ</td>
                        <td>${item.quantity}</td>
                        <td class="fw-bold text-danger">${Number(item.price * item.quantity).toLocaleString('vi-VN')}đ</td>
                    </tr>`;
                });
                html += '</tbody></table>';
                document.getElementById('items-' + orderId).innerHTML = html;
            });
    }
}

function cancelOrder(orderId) {
    if (!confirm('Bạn chắc chắn muốn hủy đơn hàng này?')) return;
    fetch('/api/order/' + orderId, { method: 'DELETE' })
        .then(res => res.json())
        .then(result => {
            alert(result.message);
            if (result.status === 'success') loadHistory();
        });
}
</script>
