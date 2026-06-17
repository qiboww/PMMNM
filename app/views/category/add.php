<?php include 'app/views/shares/header.php'; ?>
<div class="container-xl mt-4 pb-5">
    <h3 class="fw-bold mb-3">Thêm danh mục</h3>
    <form id="form-add-category">
        <div class="mb-3">
            <label>Tên danh mục</label>
            <input type="text" id="name" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Lưu danh mục</button>
    </form>
</div>
<?php include 'app/views/shares/footer.php'; ?>

<script>
document.getElementById('form-add-category').addEventListener('submit', function(e) {
    e.preventDefault();
    let data = {
        name: document.getElementById('name').value
    };

    fetch('/api/category', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(result => {
        alert(result.message);
        if (result.status === 'success') window.location.href = '/Category/list';
    });
});
</script>