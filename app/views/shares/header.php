<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa Hàng Cầu Lông</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/public/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow">
        <div class="container-xl">
            <a class="navbar-brand fw-bold fs-3" href="/Product/list"><i class="bi bi-lightning-fill me-1"></i>BADMINTON</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
               <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php $current_cat = isset($_GET['category_id']) ? $_GET['category_id'] : ''; ?>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo empty($current_cat) ? 'text-white fw-bold' : 'text-white-50'; ?>" href="/Product/list">Tất cả sản phẩm</a>
                    </li>
                    
                    <?php if(!empty($categories)): foreach($categories as $cat): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_cat == $cat->id) ? 'text-white fw-bold' : 'text-white-50'; ?>" href="/Product/list?category_id=<?php echo $cat->id; ?>">
                            <?php echo htmlspecialchars($cat->name); ?>
                        </a>
                    </li>
                    <?php endforeach; endif; ?>
                </ul>
                <div class="d-flex gap-2">
                    <a href="/Category/list" class="btn btn-light btn-sm fw-bold text-primary"><i class="bi bi-folder me-1"></i>Danh mục</a>
                    <a href="/Product/add" class="btn btn-outline-light btn-sm fw-bold"><i class="bi bi-plus-lg me-1"></i>Sản phẩm</a>
                </div>
            </div>
        </div>
    </nav>