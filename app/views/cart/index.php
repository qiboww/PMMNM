<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-4 pb-5">
    <h3 class="fw-bold mb-3">Giỏ hàng của bạn</h3>
    <table class="table table-bordered">
        <thead>
            <tr><th>Sản phẩm</th><th>Giá</th><th>SL</th><th>Thành tiền</th><th>Hành động</th></tr>
        </thead>
        <tbody id="cart-list"></tbody>
    </table>
    <h4 class="text-end text-danger fw-bold" id="cart-total">Tổng: 0đ</h4>
    
    <hr>
    <h4>Thông tin đặt hàng</h4>
    <div class="row">
        <div class="col-md-4"><input type="text" id="cus_name" class="form-control mb-2" placeholder="Tên người nhận"></div>
        <div class="col-md-4"><input type="text" id="cus_phone" class="form-control mb-2" placeholder="Số điện thoại"></div>
        <div class="col-md-4"><input type="text" id="cus_address" class="form-control mb-2" placeholder="Địa chỉ giao hàng"></div>
    </div>
    <button onclick="checkout()" class="btn btn-primary mt-2">Xác nhận đặt hàng</button>
</div>
<?php include 'app/views/shares/footer.php'; ?>

<script>
const USER_ID = <?php echo isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 'null'; ?>;

document.addEventListener("DOMContentLoaded", function() {
    if (!USER_ID) {
        alert("Vui lòng đăng nhập để xem giỏ hàng!");
        window.location.href = '/Auth/login';
        return;
    }
    loadCart();
});

function loadCart() {
    fetch('/api/cart?user_id=' + USER_ID)
        .then(res => res.json())
        .then(result => {
            if (result.status === 'success') {
                let html = '';
                if (result.data.length === 0) {
                    html = '<tr><td colspan="5" class="text-center text-muted py-4">Giỏ hàng trống</td></tr>';
                } else {
                    result.data.forEach(item => {
                        html += `<tr>
                            <td>${item.name}</td>
                            <td>${Number(item.price).toLocaleString('vi-VN')}đ</td>
                            <td>
                                <input type="number" value="${item.quantity}" min="1" onchange="updateCart(${item.cart_id}, this.value)" style="width: 60px;">
                            </td>
                            <td>${Number(item.total_item_price).toLocaleString('vi-VN')}đ</td>
                            <td><button onclick="deleteCartItem(${item.cart_id})" class="btn btn-sm btn-danger">Xóa</button></td>
                        </tr>`;
                    });
                }
                document.getElementById('cart-list').innerHTML = html;
                document.getElementById('cart-total').innerText = `Tổng: ${Number(result.total_price).toLocaleString('vi-VN')}đ`;
            }
        });
}

function updateCart(id, qty) {
    fetch('/api/cart/' + id, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ quantity: qty })
    }).then(() => loadCart());
}

function deleteCartItem(id) {
    fetch('/api/cart/' + id, { method: 'DELETE' }).then(() => loadCart());
}

function checkout() {
    if (!USER_ID) {
        alert("Vui lòng đăng nhập!");
        return;
    }

    let data = {
        user_id: USER_ID,
        customer_name: document.getElementById('cus_name').value,
        customer_phone: document.getElementById('cus_phone').value,
        customer_address: document.getElementById('cus_address').value
    };

    fetch('/api/order', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(result => {
        alert(result.message);
        if (result.status === 'success') {
            updateCartBadge();
            loadCart();
        }
    });
}
</script>
