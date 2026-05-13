<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <nav class="admin-header py-3 mb-4">
        <div class="container-xl d-flex align-items-center">
            <h4 class="m-0 fw-bold text-primary"><i class="bi bi-box-seam me-2"></i>Admin</h4>
        </div>
    </nav>

    <div class="container-xl pb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 fw-bold text-dark">Danh sách sản phẩm</h2>
            </div>
            <a href="/Product/add" class="btn btn-primary shadow-sm rounded-3 px-4 py-2 fw-medium">
                <i class="bi bi-plus-lg me-2"></i>Thêm sản phẩm
            </a>
        </div>

        <div class="row g-4">
            <?php if (empty($products)): ?>
                <div class="col-12">
                    <div class="alert alert-secondary text-center py-4">Chưa có sản phẩm nào.</div>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                    <div class="card card-custom h-100">
                        <img src="/public/images/<?php echo htmlspecialchars($product->getImage() ?: 'default.png', ENT_QUOTES, 'UTF-8'); ?>" class="card-img-top" alt="Product Image">
                        
                        <div class="card-body d-flex flex-column p-4">
                            <h5 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($product->getName(), ENT_QUOTES, 'UTF-8'); ?></h5>
                            <p class="text-secondary small flex-grow-1 product-desc mb-3">
                                <?php echo htmlspecialchars($product->getDescription(), ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                            <h5 class="text-success fw-bold mb-4">
                                <?php echo number_format($product->getPrice(), 0, ',', '.'); ?> ₫
                            </h5>
                            
                            <div class="d-flex gap-2 mt-auto">
                                <a href="/Product/edit/<?php echo $product->getID(); ?>" class="btn btn-light text-primary border flex-fill fw-medium">
                                    <i class="bi bi-pencil-square me-1"></i> Sửa
                                </a>
                                <a href="/Product/delete/<?php echo $product->getID(); ?>" 
                                   class="btn btn-light text-danger border flex-fill fw-medium" 
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa?');">
                                    <i class="bi bi-trash3 me-1"></i> Xóa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>