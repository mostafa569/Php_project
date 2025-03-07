<?php
// Café Retreat
$host = "localhost:3306";  
$dbname = "cafeteria"; 
$username = "root";
$dbType="mysql";
$password = "root";  

try {
    
    $connection = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    //   echo "Connected successfully!";
} catch (PDOException $e) {
 
    die("Database connection failed: " . $e->getMessage());
}





// insert data into rooms  
// $roomQuery="insert into rooms(name,created_at)values('sanaa',NOW()),('yasmeen',NOW()),('asmaa',NOW()),('alaa',NOW()),('mohamed',NOW()),('mostafa',NOW())";
// $roomQuery=$connection->prepare($roomQuery);
// $roomQuery->execute();

// insert data into category 
// $categoriesQuery="insert into categories(name,created_at)values('coffe',NOW()),('hot drink',NOW()),('ice coffe',NOW()),('Tea',NOW()),('cold drinks',NOW()),('ice Tea',NOW())";
// $categoriesQuery=$connection->prepare($categoriesQuery);
// $categoriesQuery->execute();


// insert data into prouduct
// $prouductQuery="insert into products(name,description,price,category_id,image_url,created_at)values('coffee black','hot and black',30.00,1,'cat.jpg',NOW()),('coffee mix','hot',35.00,2,'cat.jpg',NOW()),('coffee milk','hot and milk',40.00,1,'cat.jpg',NOW()) , ('Tea','hot',15.00,6,'cat.jpg',NOW()),('Ice Tea','hot and cold',20.00,5,'cat.jpg',NOW())";
// $prouductQuery=$connection->prepare($prouductQuery);
// $prouductQuery->execute();



// insert data into users 


// $usesrQuery="insert into users(username,email,password,role,room_id,image_url,verification_code,verification_expiry,created_at)values
// ('sanaa','sanaa@gmail.com','1234','user','1','cat.jpg','66',NOW()+ interval 1 day,NOW()),
// ('yasmeen','yasmeen@gmail.com','567','user','2','cat.jpg','68',NOW()+ interval 1 day,NOW()),
// ('asmaa','asmaa@gmail.com','478','admin','3','cat.jpg','77',NOW()+ interval 1 day,NOW()),
// ('alaa','alaa@gmail.com','888','admin','4','cat.jpg','126',NOW()+ interval 1 day,NOW()),
// ('mohamed','mohamed@gmail.com','98','admin','5','cat.jpg','33',NOW()+ interval 1 day,NOW()),
// ('mostafa','mostafa@gmail.com','1234','user','6','cat.jpg','77',NOW()+ interval 1 day,NOW())";
// $usesrQuery=$connection->prepare($usesrQuery);
// $usesrQuery->execute();


// $usesrQuery="insert into users(username,email,password,role,room_id,image_url,verification_code,verification_expiry,created_at)values
// ('omar','omar@gmail.com','77','user','5','cat.jpg','15',DATE_ADD(NOW(), INTERVAL 4 WEEK),NOW())";
// $usesrQuery=$connection->prepare($usesrQuery);
// $usesrQuery->execute();




// insert data into orders 
// $orderQuery="insert into orders(user_id,room_id,product_id,quantity,total_price,status,created_at)values(1,1,1,2,10.00,'Processing',NOW()),
// (2,2,5,1,50.00,'Out for Delivery',NOW()),
// (3,3,1,3,90.00,'Done',NOW()),
// (4,4,4,2,60.00,'Out for Delivery',NOW()),
// (5,5,4,3,100.00,'Done',NOW()),
// (6,6,2,1,80.00,'Processing',NOW())";
// $orderSql=$connection->prepare($orderQuery);
// $orderSql->execute();


// $orderQuery="insert into orders(user_id,room_id,product_id,quantity,total_price,status,created_at)values
// (15,3,2,1,3.00,'Out for Delivery',NOW())";
// $orderSql=$connection->prepare($orderQuery);
// $orderSql->execute();
?>