<?php
// config.php
<?php
// database connection settings
$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'supermarket_storage';
/ create a connection to the database
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<?php        
require_once 'config.php';
//API endpoints 
//apply product 
$app->get('/products', function() use ($conn){
    $sql = "SELECT * FROM STORAGESHOP";
    $result = $conn->query($sql);
    $products = array();
    while($row = $result->fetch_assoc()){
        $products[] = $row;
    }
    echo json_encode($products);
});
// get product information with the id
/ get product information with the id
$app->get('/products/:id', function($request, $response, $args) use ($conn){
    $productID = $args['id'];
    $sql = "SELECT * FROM STORAGESHOP WHERE PRODUCT_ID_NATIONAL = '$productID'";
    $result = $conn->query($sql);
    $product = $result->fetch_assoc();
    echo json_encode($product);
});

// post request inset product data 
$app->post('/products',function() use ($conn){
    $data = json_decode(file_get_contents('php//input'),true);
    if(!isset($data['PRODUCT_ID_NATIONAL'])|| !isset($data['PRODUCT_NAME']) || !isset($data['BRAND_REFERENT']) || !isset($data['PRODUCT_AMOUNT_UNIT']) || !isset($data['WEIGHT']) || !isset($data['BUY_PRICE']) || !isset($data['SOLD_PRICE']) || !isset($data['TAX'])) {
        http_response_code(400);// bad request 
        echo json_encode(array('error'->'Invalid input data'));
        return;
    }
    $productID = $data['PRODUCT_ID_NATIONAL'];
    $productName = $data['PRODUCT_NAME'];
    $brandReferent =$data['BRAND_REFERENT'];
    $productAmountUnit = $data['PRODUCT_AMOUNT_UNIT'];
    $weight = $data['WEIGHT'];
    $buyPrice = $data['BUY_PRICE'];
    $soldPrice = $data['SOLD_PRICE'];
    $tax = $data['TAX'];

    $sql = "INSERT INTO STORAGESHOP(PRODUCT_ID_NATIONAL,PRODUCT_NAME ,BRAND_REFERENT,PRODUCT_AMOUNT_UNIT,WEIGHT,BUY_PRICE ,SOLD_PRICE,TAX ) VALUES(?,?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiii",$productID,$productName,$brandReferent,$productAmountUnit, $weight,$buyPrice,$soldPrice,$tax);
    $stmt->execute();

    http_response_code(201);// created 
    echo json_encode(array('message' => 'Product created successfully'));                
}); 
// put update the product information 
// put update the product information 
$app->put('/products/:id', function ($request, $response, $args) use ($conn) {
    $productID = $args['id'];
    $data = json_decode(file_get_contents('php://input'), true);
    if(!isset($data['PRODUCT_NAME']) || !isset($data['BRAND_REFERENT']) || !isset($data['PRODUCT_AMOUNT_UNIT']) || !isset($data['WEIGHT']) || !isset($data['BUY_PRICE']) || !isset($data['SOLD_PRICE']) || !isset($data['TAX'])) {
        http_response_code(400);// bad request 
        echo json_encode(array('error' => 'Invalid input data'));
        return;
    }
    $productName = $data['PRODUCT_NAME'];
    $brandReferent = $data['BRAND_REFERENT'];
    $productAmountUnit = $data['PRODUCT_AMOUNT_UNIT'];
    $weight = $data['WEIGHT'];
    $buyPrice = $data['BUY_PRICE'];
    $soldPrice = $data['SOLD_PRICE'];
    $tax = $data['TAX'];

    $sql = "UPDATE STORAGESHOP SET PRODUCT_NAME =?, BRAND_REFERENT =?, PRODUCT_AMOUNT_UNIT =?, WEIGHT =?, BUY_PRICE =?, SOLD_PRICE =?, TAX =? WHERE PRODUCT_ID_NATIONAL = '$productID'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiiii", $productName, $brandReferent, $productAmountUnit, $weight, $buyPrice, $soldPrice, $tax);
    $stmt->execute();
    echo json_encode(array('message' => 'Product updated successfully'));
});
// remove the product
$app->delete('/products/{id}', function (Request $request, Response $response, array $args) {
    $productID = intval($args['id']);
    $result = deleteProduct($productID);

    if ($result > 0) {
        $response->getBody()->write(json_encode(['message' => 'Product deleted successfully']));
        return $response->withHeader('Content-Type', 'application/json');
    } else {
        $response->getBody()->write(json_encode(['error' => 'Product not found']));
        return $response->withHeader('Content-Type', 'application/json');
    }
});
// apply distributor 
// apply distributor 
$app->get('/distributors', function() use ($conn){
    $sql = "SELECT * FROM DISTRIBUTORS";
    $result = $conn->query($sql);
    $distributors = array();
    while($row = $result->fetch_assoc()){
        $distributors[] = $row;
    }
    echo json_encode($distributors);
});

// get distributor information with the id
$app->get('/distributors/:id', function($request, $response, $args) use ($conn){
    $distributorID = $args['id'];
    $sql = "SELECT * FROM DISTRIBUTORS WHERE DISTRIBUTOR_ID = '$distributorID'";
    $result = $conn->query($sql);
    $distributor = $result->fetch_assoc();
    echo json_encode($distributor);
});
// post request inset distributor data
// post request inset distributor data
$app->post('/distributors', function() use ($conn){
    $data = json_decode(file_get_contents('php://input'), true);
    if(!isset($data['DISTRIBUTOR_ID']) || !isset($data['DISTRIBUTOR_NAME']) || !isset($data['BRAND_REFERENT']) || !isset($data['PRODUCT_AMOUNT_UNIT_SALE']) || !isset($data['WEIGHT_SALE']) || !isset($data['SOLD_PRICE']) || !isset($data['COST_TRANSPORT']) || !isset($data['TAX_OPERATIVE'])) {
        http_response_code(400);// bad request 
        echo json_encode(array('error' => 'Invalid input data'));
        return;
    }
    $distributorID = $data['DISTRIBUTOR_ID'];
    $distributorName = $data['DISTRIBUTOR_NAME'];
    $brandReferent = $data['BRAND_REFERENT'];
    $productAmountSale = $data['PRODUCT_AMOUNT_UNIT_SALE'];
    $weightSale = $data['WEIGHT_SALE'];
    $soldPrice = $data['SOLD_PRICE'];
    $costTransport = $data['COST_TRANSPORT'];
    $operatiTax = $data['TAX_OPERATIVE'];

    $sql = "INSERT INTO DISTRIBUTORS (DISTRIBUTOR_ID, DISTRIBUTOR_NAME, BRAND_REFERENT, PRODUCT_AMOUNT_UNIT_SALE, WEIGHT_SALE, SOLD_PRICE, COST_TRANSPORT, TAX_OPERATIVE) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiiiii", $distributorID, $distributorName, $brandReferent, $productAmountSale, $weightSale, $soldPrice, $costTransport, $operatiTax);
    $stmt->execute();

    http_response_code(201);// created 
    echo json_encode(array('message' => 'Distributor created successfully'));                
}); 

// put update the distributor information 
// put update the distributor information 
$app->put('/distributors/:id', function ($request, $response, $args) use ($conn) {
    $distributorID = $args['id'];
    $data = json_decode(file_get_contents('php://input'), true);
    if(!isset($data['DISTRIBUTOR_NAME']) || !isset($data['BRAND_REFERENT']) || !isset($data['PRODUCT_AMOUNT_UNIT_SALE']) || !isset($data['WEIGHT_SALE']) || !isset($data['SOLD_PRICE']) || !isset($data['COST_TRANSPORT']) || !isset($data['TAX_OPERATIVE'])) {
        http_response_code(400);// bad request 
        echo json_encode(array('error' => 'Invalid input data'));
        return;
    }
    $distributorName = $data['DISTRIBUTOR_NAME'];
    $brandReferent = $data['BRAND_REFERENT'];
    $productAmountSale = $data['PRODUCT_AMOUNT_UNIT_SALE'];
    $weightSale = $data['WEIGHT_SALE'];
    $soldPrice = $data['SOLD_PRICE'];
    $costTransport = $data['COST_TRANSPORT'];
    $operatiTax = $data['TAX_OPERATIVE'];

    $sql = "UPDATE DISTRIBUTORS SET DISTRIBUTOR_NAME =?, BRAND_REFERENT =?, PRODUCT_AMOUNT_UNIT_SALE =?, WEIGHT_SALE =?, SOLD_PRICE =?, COST_TRANSPORT =?, TAX_OPERATIVE =? WHERE DISTRIBUTOR_ID = '$distributorID'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiiiii", $distributorName, $brandReferent, $productAmountSale, $weightSale, $soldPrice, $costTransport, $operatiTax);
    $stmt->execute();
    echo json_encode(array('message' => 'Distributor updated successfully'));
});
// remove the distributor
$app->delete('/distributors/{id}', function (Request $request, Response $response, array $args) {
    $distributorID = intval($args['id']);
    $result = deleteProduct($distributorID);

    if ($result > 0) {
        $response->getBody()->write(json_encode(['message' => 'Distributor deleted successfully']));
        return $response->withHeader('Content-Type', 'application/json');
    } else {
        $response->getBody()->write(json_encode(['error' => 'Distributor not found']));
        return $response->withHeader('Content-Type', 'application/json');
    }
});

///Additional functions for easy CRUD operations with database in mysql
//product case
function createProduct($productName, $brandReferent, $productAmountUnit, $weight, $buyPrice, $soldPrice, $tax){
    global $conn;
    $stmt = $conn->prepare("INSERT INTO STORAGESHOP (PRODUCT_NAME, BRAND_REFERENT, PRODUCT_AMOUNT_UNIT, WEIGHT, BUY_PRICE, SOLD_PRICE, TAX) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiddi", $productName, $brandReferent, $productAmountUnit, $weight, $buyPrice, $soldPrice, $tax);
    $stmt->execute();        
    return $stmt->insert_id;
}

function readProducts(){
    global $conn;
    $result = $conn->query("SELECT * FROM STORAGESHOP");
    $products = array();
    while($row = $result->fetch_assoc()){
        $products[] = $row;
    }
    return $products;
}

function updateProduct($productID, $productName, $brandReferent, $productAmountUnit, $weight, $buyPrice, $soldPrice, $tax){
    global $conn;
    $stmt = $conn->prepare("UPDATE STORAGESHOP SET PRODUCT_NAME=?, BRAND_REFERENT=?, PRODUCT_AMOUNT_UNIT=?, WEIGHT=?, BUY_PRICE=?, SOLD_PRICE=?, TAX=? WHERE PRODUCT_ID_NATIONAL=?");
    $stmt->bind_param("ssiddi", $productName, $brandReferent, $productAmountUnit, $weight, $buyPrice, $soldPrice, $tax, $productID);
    $stmt->execute();
    return $stmt->affected_rows;
}

function deleteProduct($productID){
    global $conn;
    $stmt = $conn->prepare("DELETE FROM STORAGESHOP WHERE PRODUCT_ID_NATIONAL=?");
    $stmt->bind_param("i", $productID);
    $stmt->execute();
    return $stmt->affected_rows;
}

//distributor case
function createDistributor($distributorName, $brandReferent, $productAmountUnitSale, $weightSale, $sellPrice, $costTransport, $taxOperative){
    global $conn;
    $stmt = $conn->prepare("INSERT INTO DISTRIBUTORS (DISTRIBUTOR_NAME, BRAND_REFERENT, PRODUCT_AMOUNT_UNIT_SALE, WEIGHT_SALE, SOLD_PRICE, COST_TRANSPORT, TAX_OPERATIVE) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param("ssiiddd", $distributorName, $brandReferent, $productAmountUnitSale, $weightSale, $sellPrice, $costTransport, $taxOperative);
    $stmt->execute();        
    return $stmt->insert_id;
}

function readDistributors(){
    global $conn;
    $result = $conn->query("SELECT * FROM DISTRIBUTORS");
    $distributors = array();
    while($row = $result->fetch_assoc()){
        $distributors[] = $row;
    }
    return $distributors;
}

function updateDistributor($distributorID, $distributorName, $brandReferent, $productAmountSale, $weightSale, $sellPrice, $costTransport, $taxOperative){
    global $conn;
    $stmt = $conn->prepare("UPDATE DISTRIBUTORS SET DISTRIBUTOR_NAME =?, BRAND_REFERENT =?, PRODUCT_AMOUNT_UNIT_SALE =?, WEIGHT_SALE =?, SOLD_PRICE =?, COST_TRANSPORT =?, TAX_OPERATIVE =? WHERE DISTRIBUTOR_ID =?");
    $stmt->bind_param("ssiidddi", $distributorName, $brandReferent, $productAmountSale, $weightSale, $sellPrice, $costTransport, $taxOperative, $distributorID);
    $stmt->execute();
    return $stmt->affected_rows;
}

function deleteDistributor($distributorId){
    global $conn;
    $stmt = $conn->prepare("DELETE FROM DISTRIBUTORS WHERE DISTRIBUTOR_ID=?");
    $stmt->bind_param("i", $distributorId);
    $stmt->execute();
    return $stmt->affected_rows;
}

// create the products views
// get price view
$app->get('/prices', function () use ($conn) {
    $query = "SELECT * FROM VW_PRICES";
    $result = mysqli_query($conn, $query);
    $prices = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $prices[] = $row;
    }
    echo json_encode($prices);
});

// get storage view
$app->get('/storage', function () use ($conn) {
    $query = "SELECT * FROM VW_STORAGE";
    $result = mysqli_query($conn, $query);
    $storage = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $storage[] = $row;
    }
    echo json_encode($storage);
});

// get order view
$app->get('/order', function () use ($conn) {
    $query = "SELECT * FROM VW_ORDER";
    $result = mysqli_query($conn, $query);
    $order = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $order[] = $row;
    }
    echo json_encode($order);
});
//get order service view
$app->get('/order_service', function () use ($conn) {
    $query = "SELECT * FROM VW_ORDER_SERVICE";
    $result = mysqli_query($conn, $query);
    $order_service = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $order_service[] = $row;
    }
    echo json_encode($order_service);
});
// create the distributor views
// get distributor view
$app->get('/distributor', function () use ($conn) {
    $query = "SELECT * FROM VW_DISTRIBUTOR";
    $result = mysqli_query($conn, $query);
    $distributor = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $distributor[] = $row;
    }
    echo json_encode($distributor);
});

// get distributor product view
$app->get('/distributor_product', function () use ($conn) {
    $query = "SELECT * FROM VW_DISTRIBUTOR_PRODUCT";
    $result = mysqli_query($conn, $query);
    $distributor_product = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $distributor_product[] = $row;
    }
    echo json_encode($distributor_product);
});

// get distributor service view
$app->get('/distributor_service', function () use ($conn) {
    $query = "SELECT * FROM VW_SERVICE_DISTRIBUTOR";
    $result = mysqli_query($conn, $query);
    $distributor_service = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $distributor_service[] = $row;
    }
    echo json_encode($distributor_service);
});  