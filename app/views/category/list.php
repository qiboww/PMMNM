<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-5 pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 fw-bold">Danh sách danh mục</h2>
        <a href="/Category/add" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Thêm danh mục</a>
    </div>
    <div class="card card-custom p-0 overflow-hidden">
        <table class="table table-custom mb-0 w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên danh mục</th>
                    <th>Mô tả</th>
                    <th class="text-end">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($categories)): foreach ($categories as $cat): ?>
                <tr>
                    <td><?php echo $cat->id; ?></td>
                    <td class="fw-bold"><?php echo htmlspecialchars($cat->name); ?></td>
                    <td><?php echo htmlspecialchars($cat->description); ?></td>
                    <td class="text-end">
                        <a href="/Category/edit/<?php echo $cat->id; ?>" class="btn btn-sm btn-warning btn-action text-white"><i class="bi bi-pencil"></i></a>
                        <a href="/Category/delete/<?php echo $cat->id; ?>" class="btn btn-sm btn-danger btn-action" onclick="return confirm('Bạn có chắc muốn xóa?');"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="4" class="text-center py-4">Chưa có danh mục nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'app/views/shares/footer.php'; ?>