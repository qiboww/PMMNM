<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-4 pb-5">
    <h3 class="fw-bold mb-3">Thêm sản phẩm</h3>
    <form id="form-add-product">
        <div class="mb-3">
            <label>Tên sản phẩm</label>
            <input type="text" id="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Giá</label>
            <input type="number" id="price" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Danh mục</label>
            <select id="category_id" class="form-control" required></select>
        </div>
        <div class="mb-3">
            <label>Mô tả</label>
            <textarea id="description" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label>Hình ảnh (Tên file)</label>
            <input type="text" id="image" class="form-control" placeholder="example.png">
        </div>
        <button type="submit" class="btn btn-success">Lưu sản phẩm</button>
    </form>
</div>
<?php include 'app/views/shares/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Load danh mục vào thẻ select
    fetch('/api/category')
        .then(res => res.json())
        .then(result => {
            let html = '';
            result.data.forEach(c => html += `<option value="${c.id}">${c.name}</option>`);
            document.getElementById('category_id').innerHTML = html;
        });
});

document.getElementById('form-add-product').addEventListener('submit', function(e) {
    e.preventDefault();
    let data = {
        name: document.getElementById('name').value,
        price: Number(document.getElementById('price').value),
        category_id: document.getElementById('category_id').value,
        description: document.getElementById('description').value,
        image: document.getElementById('image').value || 'default.png'
    };

    fetch('/api/product', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(result => {
        alert(result.message);
        if (result.status === 'success') window.location.href = '/Product/list';
    });
});
</script>
