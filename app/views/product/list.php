<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-4 pb-5">
    
    <div class="mb-4">
        <img src="/public/images/banner.jpg" alt="Banner" class="img-fluid rounded-3 shadow-sm w-100" style="max-height: 300px; object-fit: cover;">
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h3 mb-0 fw-bold text-dark">
            <?php 
                $current_cat = isset($_GET['category_id']) ? $_GET['category_id'] : '';
                $title = "Tất cả sản phẩm";
                if (!empty($current_cat) && !empty($categories)) {
                    foreach($categories as $cat) {
                        if($cat->id == $current_cat) { $title = $cat->name; break; }
                    }
                }
                echo htmlspecialchars($title);
            ?>
        </h2>
    </div>
    
    <div class="card card-custom p-3 mb-4 shadow-sm border-0 bg-white">
        <form action="/Product/list" method="GET" class="row g-3 align-items-end">
            <?php if (!empty($current_cat)): ?>
                <input type="hidden" name="category_id" value="<?php echo $current_cat; ?>">
            <?php endif; ?>
            
            <div class="col-md-4">
                <label class="form-label text-muted small mb-1 fw-bold">Tìm kiếm tên</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0 bg-light" name="keyword" placeholder="Nhập tên sản phẩm..." value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
                </div>
            </div>
            
            <div class="col-md-3">
                <label class="form-label text-muted small mb-1 fw-bold">Giá thấp nhất</label>
                <input type="number" class="form-control bg-light" name="min_price" placeholder="VD: 500000" value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">
            </div>
            
            <div class="col-md-3">
                <label class="form-label text-muted small mb-1 fw-bold">Giá cao nhất</label>
                <input type="number" class="form-control bg-light" name="max_price" placeholder="VD: 5000000" value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
            </div>
            
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="bi bi-funnel-fill me-1"></i> Lọc</button>
                <a href="/Product/list<?php echo !empty($current_cat) ? '?category_id='.$current_cat : ''; ?>" class="btn btn-light border px-3 text-secondary" title="Xóa bộ lọc"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>
    </div>

    <div class="row g-4">
        <?php if(!empty($products)): foreach ($products as $product): ?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm card-custom overflow-hidden">
                <a href="/Product/show/<?php echo $product->id; ?>">
                    <img src="/public/images/<?php echo $product->image; ?>" class="card-img-top w-100">
                </a>
                <div class="card-body d-flex flex-column pb-0">
                    <a href="/Product/show/<?php echo $product->id; ?>" class="text-decoration-none text-dark">
                        <h5 class="card-title fw-bold text-truncate" title="<?php echo htmlspecialchars($product->name); ?>"><?php echo htmlspecialchars($product->name); ?></h5>
                    </a>
                    <p class="text-danger fw-bold fs-5 mb-3"><?php echo number_format($product->price, 0, ',', '.'); ?> VNĐ</p>
                </div>
                
                <div class="card-footer bg-white border-top-0 p-3 pt-0">
                    <a href="/Product/show/<?php echo $product->id; ?>" class="btn btn-outline-primary w-100 fw-medium btn-sm mb-2">
                        <i class="bi bi-eye me-1"></i> Xem chi tiết
                    </a>
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="/Product/edit/<?php echo $product->id; ?>" class="btn btn-outline-secondary w-100 fw-medium btn-sm">
                                <i class="bi bi-pencil-square me-1"></i> Sửa
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="/Product/delete/<?php echo $product->id; ?>" class="btn btn-outline-danger w-100 fw-medium btn-sm" onclick="return confirm('Chắc chắn xóa?');">
                                <i class="bi bi-trash3 me-1"></i> Xóa
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; else: ?>
        <div class="col-12"><div class="alert alert-light text-center border text-muted py-5">Không tìm thấy sản phẩm nào phù hợp với bộ lọc.</div></div>
        <?php endif; ?>
    </div>
</div>
<?php include 'app/views/shares/footer.php'; ?>