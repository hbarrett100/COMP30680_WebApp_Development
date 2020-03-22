<html>

<head>
    <meta charset="utf-8">
    <title>Orders</title>
    <meta name="author" content="Your Name">
    <link rel="stylesheet" href="./styles.css">
</head>

</html>

<?php

// Include the nav bar and footer, showing an error if the files can't be found
if(!file_exists("nav.php")){
    echo "'nav.php' could not be found, cannot display navigation bar";
} else {
    include 'nav.php';
}

if(!file_exists("footer.php")){
    echo "'footer.php' could not be found, cannot display footer";
} else {
    include 'footer.php';
}

// Server login details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "classicmodels";

// Try to connect to the server, show an error and exit if fail
try {
    $conn = @mysqli_connect($servername, $username, $password, $dbname);
    if(!$conn){
        throw new Exception('<p>Page cannot be displayed</p>');
    }
}
catch(Exception $e){
    echo $e->getMessage();
    echo "<p>Failed to connect to server: " . mysqli_connect_error($conn) . "</p>";
    exit;
}

// Query to get orders that are in process or cancelled
$sql = "SELECT orders.orderNumber, orders.orderDate, orders.status, orders.comments,
        products.productCode, products.productName, products.productLine
        FROM orders
        INNER JOIN orderdetails 
        ON orders.orderNumber = orderdetails.orderNumber
        INNER JOIN products
        ON products.productCode = orderdetails.productCode
        WHERE orders.status = 'In process'
        OR orders.status = 'Cancelled'" ;


$result = mysqli_query($conn, $sql);
$orders_array = array();

// Add results to an array, or show an error and exit if query failed
if (@mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        array_push($orders_array, $row);
    }
} else {
    echo "<p>Tables cannot be displayed due to an error in query 1: " . mysqli_error($conn) . "</p>";
    mysqli_close($conn);
    exit;
}

// Query to get 20 most recent orders
$sql = "SELECT orders.orderNumber, orders.orderDate, orders.status, orders.comments,
        products.productCode, products.productName, products.productLine
        FROM orders
        INNER JOIN orderdetails 
        ON orders.orderNumber = orderdetails.orderNumber
        INNER JOIN products
        ON products.productCode = orderdetails.productCode
        ORDER BY orders.orderDate DESC";

// Add results to an array, or show an error and exit if query failed
$result = mysqli_query($conn, $sql);
if (@mysqli_num_rows($result) > 0) {
    $num_orders = 0;
    $last_order_number = "";
    while($num_orders < 20){
        $row = mysqli_fetch_assoc($result);
        if ($row["orderNumber"] != $last_order_number){
            $num_orders++;
        }
        // Add a key in the array that indicated these orders are recent
        $row["recent"] = true;
        array_push($orders_array, $row);
        $last_order_number = $row["orderNumber"];
    }
    
} else {
    echo "<p>Tables cannot be displayed due to an error in query 2: " . mysqli_error($conn) . "</p>";
    mysqli_close($conn);
    exit;
}

mysqli_close($conn);
?>

<html>
<div id="orders">
    <div id="order-table-div"></div>
    <div id="order-info"></div>
</div>

</html>
<script>
    // Get the array of orders from the php
    var ordDict = <?php echo json_encode($orders_array); ?>;
    let recentDict = {};
    let cancelDict = {};
    let inProcessDict = {};

    // Separate the orders in to 3 seperate dicts
    for (var order in ordDict) {

        if (ordDict[order]["recent"]) {
            recentDict[ordDict[order]["orderNumber"]] = {
                "orderDate": ordDict[order]["orderDate"],
                "status": ordDict[order]["status"]
            }
        }

        if (ordDict[order]["status"] === "Cancelled") {
            cancelDict[ordDict[order]["orderNumber"]] = {
                "orderDate": ordDict[order]["orderDate"],
                "status": ordDict[order]["status"]
            }
        }

        if (ordDict[order]["status"] === "In Process") {
            inProcessDict[ordDict[order]["orderNumber"]] = {
                "orderDate": ordDict[order]["orderDate"],
                "status": ordDict[order]["status"]
            }
        }
    }

    // Display the tables from the dicts
    let recentHtml = "<h3>Recent Orders</h3><table><tr><th>Order Number</th><th>Date</th><th>Status</th></tr>"
    for (let order in recentDict) {
        recentHtml += "<tr onclick='showOrder(\"" + order + "\")'><td>" + order + "</td><td>" + recentDict[order]["orderDate"] + "</td><td>" + recentDict[order]["status"] + "</td></tr>";
    }
    recentHtml += "</table>";


    let cancelHtml = "<h3>Cancelled Orders</h3><table><tr><th>Order Number</th><th>Date</th><th>Status</th></tr>"
    for (let order in cancelDict) {
        cancelHtml += "<tr onclick='showOrder(\"" + order + "\")'><td>" + order + "</td><td>" + cancelDict[order]["orderDate"] + "</td><td>" + cancelDict[order]["status"] + "</td></tr>";
    }
    cancelHtml += "</table>";

    let inProcessHtml = "<h3>Orders In Process</h3><table><tr><th>Order Number</th><th>Date</th><th>Status</th></tr>"
    for (let order in inProcessDict) {
        inProcessHtml += "<tr onclick='showOrder(\"" + order + "\")'><td>" + order + "</td><td>" + inProcessDict[order]["orderDate"] + "</td><td>" + inProcessDict[order]["status"] + "</td></tr>";
    }
    inProcessHtml += "</table>";


    tabDiv = document.getElementById("order-table-div");
    tabDiv.innerHTML = inProcessHtml + cancelHtml + recentHtml;


    // This function is called when a table row is clicked.
    // It displays the info about the clicked order
    function showOrder(orderNum) {
        orderDetails = {
            orderNumber: orderNum,
            products: []
        }

        // Create a dict of the info needed
        for (let order in ordDict) {
            if (ordDict[order]["orderNumber"] === orderNum) {

                orderDetails["orderDate"] = ordDict[order]["orderDate"]

                orderDetails["status"] = ordDict[order]["status"]

                orderDetails["comments"] = ordDict[order]["comments"];

                let products = {
                    "productCode": ordDict[order]["productCode"],
                    "productName": ordDict[order]["productName"],
                    "productLine": ordDict[order]["productLine"]
                }
                orderDetails["products"].push(products);
            }
        }

        ordDiv = document.getElementById("order-info")

        // Create the table
        let ordHtml = "<h3>" + orderDetails["orderNumber"] + "</h3>";
        ordHtml += "<p>" + orderDetails["orderDate"] + "</p>";
        ordHtml += "<p>" + orderDetails["status"] + "</p>";
        let comment = orderDetails["comments"] === null ? "No comments" : orderDetails["comments"];
        ordHtml += "<p>" + comment + "</p>";
        ordHtml += "<table id='order-info-table'>";
        ordHtml += "<tr><th>Product Code</th><th>Product Name</th><th>Product Line</th></tr>";
        for (let prod in orderDetails["products"]) {
            ordHtml += "<tr><td>" + orderDetails["products"][prod]["productCode"] + "</td><td>" + orderDetails["products"][prod]["productName"] + "</td><td>" + orderDetails["products"][prod]["productLine"] + "</td></tr>";
        }
        ordHtml += "</table>";
        ordDiv.innerHTML = ordHtml;
    }

</script>
