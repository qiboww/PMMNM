<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-5 pb-5"><div class="row justify-content-center"><div class="col-md-8">
    <div class="card card-custom p-4"><h3 class="mb-4">Sửa sản phẩm</h3>
        <form method="POST" action="/Product/edit/<?php echo $product->id; ?>" enctype="multipart/form-data">
            <div class="mb-3"><label class="form-label">Tên</label><input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($product->name); ?>" required></div>
            <div class="mb-3"><label class="form-label">Giá</label><input type="number" class="form-control" name="price" value="<?php echo $product->price; ?>" required></div>
            <div class="mb-3"><label class="form-label">Danh mục</label>
                <select class="form-select" name="category_id">
                    <?php if(!empty($categories)): foreach($categories as $cat): ?>
                    <option value="<?php echo $cat->id; ?>" <?php echo ($cat->id == $product->category_id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat->name); ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="mb-3"><label class="form-label">Mô tả</label><textarea class="form-control" name="description" rows="3" required><?php echo htmlspecialchars($product->description); ?></textarea></div>
            <div class="mb-3"><label class="form-label">Ảnh mới (để trống nếu không đổi)</label><input type="file" class="form-control" name="image"></div>
            <button type="submit" class="btn btn-primary w-100">Cập nhật</button>
        </form>
    </div>
</div></div></div>
<?php include 'app/views/shares/footer.php'; ?>