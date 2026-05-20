<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-custom p-4">
                <h3 class="mb-4">Thêm danh mục mới</h3>
                <form method="POST" action="/Category/add">
                    <div class="mb-3">
                        <label class="form-label">Tên danh mục</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Lưu danh mục</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'app/views/shares/footer.php'; ?>