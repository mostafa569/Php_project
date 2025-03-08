<?php



class db{
    private $host = "localhost";  
    private $dbname = "cafeteria"; 
    // private $dbname = "test project"; 
    private $username = "root"; 
    private $password = "";  
    private $pdo = null;

    function __construct()
    {
        try {
        $this->pdo = new PDO("mysql:host=$this->host;dbname=$this->dbname;charset=utf8", $this->username, $this->password);
        }catch (PDOException $e) {
        
            die("Database connection failed: " . $e->getMessage());
        }
    }
    function connection(){
            return $this->pdo;
    }
    function insertProduct($name,$des,$price,$category,$img){
        $stm=$this->pdo->prepare("insert into products (name,description,price,category_id,image_url,created_at) values (?,?,?,?,?,NOW())");
        $stm->execute([$name,$des,(int)$price,(int)$category,$img]);
    }
    function getCategory(){
        return $this->pdo->query("SELECt * FROM categories");
    }
    function getProducts(){
        return $this->pdo->query("SELECT products.id,products.name,description,price,category_id,image_url,categories.name as cateName FROM products join categories on products.category_id = categories.id");
    }
    // function getProducts(){
    //     return $this->pdo->query("SELECT * FROM products");
    // }
    function addCategory($name){
        $stm=$this->pdo->prepare("INSERt INTO categories (name,created_at) VALUEs (?,NOW())");
        $stm->execute([$name]);
    }
    function deleteProduct($id){
        $this->pdo->query("DELETe FROM products WHERE id = '$id'");
    }
    function getOneProduct($id){
        return $this->pdo->query("SELECt * FROM products WHERE id = '$id'");
    }
    function getImgProduct($id){
        return $this->pdo->query("Select image_url from products where id = '$id'");
    }
    function UpdateProduct($name, $description, $price, $category_id, $new_image,$id){
        $stm = $this->pdo->prepare("UPDATe products SET name = ?, description = ?, price = ?, category_id = ?, image_url = ? WHERE id = '$id'");
        $stm->execute([$name, $description, $price, $category_id, $new_image]);
    }

}




?>