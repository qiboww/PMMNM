<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-4 pb-5">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="fw-bold">Quản lý danh mục</h3>
        <a href="/Category/add" class="btn btn-primary">Thêm danh mục</a>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr><th>ID</th><th>Tên danh mục</th><th>Hành động</th></tr>
        </thead>
        <tbody id="category-list"></tbody>
    </table>
</div>
<?php include 'app/views/shares/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", loadCategories);

function loadCategories() {
    fetch('/api/category')
        .then(res => res.json())
        .then(result => {
            if (result.status === 'success') {
                let html = '';
                result.data.forEach(c => {
                    html += `<tr>
                        <td>${c.id}</td>
                        <td>${c.name}</td>
                        <td>
                            <a href="/Category/edit/${c.id}" class="btn btn-sm btn-warning">Sửa</a>
                            <button onclick="deleteCategory(${c.id})" class="btn btn-sm btn-danger">Xóa</button>
                        </td>
                    </tr>`;
                });
                document.getElementById('category-list').innerHTML = html;
            }
        });
}

function deleteCategory(id) {
    if (confirm('Chắc chắn xóa?')) {
        fetch('/api/category/' + id, { method: 'DELETE' })
            .then(res => res.json())
            .then(result => {
                alert(result.message);
                if (result.status === 'success') loadCategories();
            });
    }
}
</script>