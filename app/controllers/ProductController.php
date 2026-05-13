<?php
require_once 'app/models/ProductModel.php';

class ProductController
{
    private $products = [];

    public function __construct()
    {
        session_start();
        if (isset($_SESSION['products'])) {
            $this->products = $_SESSION['products'];
        }
    }

    public function index()
    {
        $this->list();
    }

    public function list()
    {
        $products = $this->products;
        include 'app/views/product/list.php';
    }

    public function add()
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];

            if (empty($name)) {
                $errors[] = 'Tên sản phẩm là bắt buộc.';
            } elseif (strlen($name) < 10 || strlen($name) > 100) {
                $errors[] = 'Tên sản phẩm phải có từ 10 đến 100 ký tự.';
            }

            if (!is_numeric($price) || $price <= 0) {
                $errors[] = 'Giá phải là một số dương lớn hơn 0.';
            }

            if (empty($errors)) {
                $imageName = 'default.png';
                
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/public/images/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $imageName = time() . '_' . basename($_FILES['image']['name']);
                    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
                }

                $id = count($this->products) + 1;
                $product = new ProductModel($id, $name, $description, $price, $imageName);
                $this->products[] = $product;
                $_SESSION['products'] = $this->products;
                header('Location: /Product/list');
                exit();
            }
        }
        include 'app/views/product/add.php';
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            foreach ($this->products as $key => $product) {
                if ($product->getID() == $id) {
                    $this->products[$key]->setName($_POST['name']);
                    $this->products[$key]->setDescription($_POST['description']);
                    $this->products[$key]->setPrice($_POST['price']);

                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/public/images/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $imageName = time() . '_' . basename($_FILES['image']['name']);
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName)) {
                            $this->products[$key]->setImage($imageName);
                        }
                    }
                    break;
                }
            }
            $_SESSION['products'] = $this->products;
            header('Location: /Product/list');
            exit();
        }
        foreach ($this->products as $product) {
            if ($product->getID() == $id) {
                include 'app/views/product/edit.php';
                return;
            }
        }
        die('Product not found');
    }

    public function delete($id)
    {
        foreach ($this->products as $key => $product) {
            if ($product->getID() == $id) {
                unset($this->products[$key]);
                break;
            }
        }
        $this->products = array_values($this->products);
        $_SESSION['products'] = $this->products;
        header('Location: /Product/list');
        exit();
    }
}
?>