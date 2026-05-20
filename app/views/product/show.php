<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-4 pb-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/Product/list" class="text-decoration-none text-primary fw-bold">Sản phẩm</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product->name); ?></li>
        </ol>
    </nav>

    <div class="card card-custom p-4 p-md-5 border-0 shadow-sm">
        <div class="row">
            <div class="col-md-5 mb-4 mb-md-0 text-center">
                <img src="/public/images/<?php echo $product->image; ?>" class="img-fluid rounded border p-3 bg-white w-100" alt="<?php echo htmlspecialchars($product->name); ?>" style="max-height: 450px; object-fit: contain;">
            </div>
            
            <div class="col-md-7 ps-md-5 d-flex flex-column">
                <div>
                    <span class="badge bg-primary mb-3 px-3 py-2 fs-6"><?php echo htmlspecialchars($product->category_name ?? 'Chưa phân loại'); ?></span>
                    <h2 class="fw-bold text-dark mb-3"><?php echo htmlspecialchars($product->name); ?></h2>
                    <h3 class="text-danger fw-bold mb-4 fs-2"><?php echo number_format($product->price, 0, ',', '.'); ?> VNĐ</h3>

                    <div class="mb-4">
                        <h5 class="fw-bold mb-2 text-dark">Mô tả sản phẩm:</h5>
                        <p class="text-secondary lh-lg" style="white-space: pre-line; text-align: justify;">
                            <?php echo htmlspecialchars($product->description); ?>
                        </p>
                    </div>
                </div>

                <hr class="text-muted mt-auto mb-4">

                <div class="d-flex gap-3">
                    <a href="/Product/edit/<?php echo $product->id; ?>" class="btn btn-warning px-4 py-2 text-dark fw-bold"><i class="bi bi-pencil-square me-2"></i>Sửa sản phẩm</a>
                    <a href="/Product/list" class="btn btn-light border px-4 py-2 text-dark fw-bold"><i class="bi bi-arrow-left me-2"></i>Quay lại</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'app/views/shares/footer.php'; ?>