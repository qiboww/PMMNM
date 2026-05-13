<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/public/css/style.css">
    <script>
        function validateForm() {
            let name = document.getElementById('name').value;
            let price = document.getElementById('price').value;
            let errors = [];
            if (name.length < 10 || name.length > 100) { errors.push('Tên sản phẩm phải có từ 10 đến 100 ký tự.'); }
            if (price <= 0 || isNaN(price)) { errors.push('Giá phải là một số dương lớn hơn 0.'); }
            if (errors.length > 0) {
                alert(errors.join('\n'));
                return false;
            }
            return true;
        }

        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('imagePreview');
                output.src = reader.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</head>
<body>
    <nav class="admin-header py-3 mb-4">
        <div class="container-xl d-flex align-items-center">
            <h4 class="m-0 fw-bold text-primary"><i class="bi bi-box-seam me-2"></i>Admin</h4>
        </div>
    </nav>

    <div class="container-xl pb-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="d-flex align-items-center mb-4">
                    <a href="/Product/list" class="btn btn-light border text-secondary me-3 px-3 rounded-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h2 class="h3 mb-0 fw-bold text-dark">Thêm sản phẩm mới</h2>
                </div>

                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger shadow-sm rounded-3">
                    <ul class="mb-0 ps-3 small">
                        <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <div class="card card-custom">
                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="/Product/add" enctype="multipart/form-data" onsubmit="return validateForm();">
                            <div class="mb-4">
                                <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="description" class="form-label">Mô tả chi tiết <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label for="price" class="form-label">Giá bán <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                                    <span class="input-group-text bg-light">VNĐ</span>
                                </div>
                            </div>

                            <div class="mb-5">
                                <label for="image" class="form-label d-block">Hình ảnh sản phẩm</label>
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <img id="imagePreview" src="#" class="img-preview" alt="Preview" style="display: none;">
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                                </div>
                            </div>
                            
                            <hr class="text-muted mb-4">
                            
                            <div class="d-flex justify-content-end gap-2">
                                <a href="/Product/list" class="btn btn-light border px-4 py-2 rounded-3 fw-medium">Hủy</a>
                                <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-medium shadow-sm">Lưu sản phẩm</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>