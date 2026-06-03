<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-4 pb-5">
    <h3 class="fw-bold mb-4">Giỏ hàng của bạn</h3>
    <div class="card card-custom p-0 overflow-hidden shadow-sm">
        <?php if(!empty($cart)): ?>
            <table class="table table-custom mb-0">
                <thead>
                    <tr><th>Ảnh</th><th>Sản phẩm</th><th>Đơn giá</th><th>Số lượng</th><th>Thành tiền</th><th>Hành động</th></tr>
                </thead>
                <tbody class="bg-white">
                    <?php $total = 0; foreach($cart as $id => $item): $total += $item['price'] * $item['quantity']; ?>
                    <tr>
                        <td><img src="/public/images/<?php echo $item['image']; ?>" width="60" class="rounded"></td>
                        <td class="fw-bold align-middle"><?php echo htmlspecialchars($item['name']); ?></td>
                        <td class="text-danger align-middle"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</td>
                        <td class="align-middle">
                        <div class="d-flex align-items-center">
                            <a href="/Cart/update/<?php echo $id; ?>?type=decrease" class="btn btn-sm btn-outline-secondary px-2">-</a>
                            <span class="mx-3 fw-bold"><?php echo $item['quantity']; ?></span>
                            <a href="/Cart/update/<?php echo $id; ?>?type=increase" class="btn btn-sm btn-outline-secondary px-2">+</a>
                        </div>
                    </td>
                        <td class="text-danger fw-bold align-middle"><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ</td>
                        <td class="align-middle"><a href="/Cart/delete/<?php echo $id; ?>" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="card-footer bg-white text-end p-4">
                <h4>Tổng tiền: <span class="text-danger fw-bold"><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</span></h4>
                <a href="/Cart/checkout" class="btn btn-primary mt-2 px-4"><i class="bi bi-credit-card me-2"></i>Thanh toán</a>
        <?php else: ?>
            <div class="p-5 text-center">
                <h5 class="text-muted">Giỏ hàng đang trống</h5>
                <a href="/Product/list" class="btn btn-primary mt-3">Tiếp tục mua sắm</a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include 'app/views/shares/footer.php'; ?>