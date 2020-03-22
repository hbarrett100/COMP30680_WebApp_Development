<html>
  <head>
	<meta charset="utf-8">
	<title>Index</title>
	<link rel="stylesheet" href="./styles.css">
</head>

</html>

<?php

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


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "classicModels";


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

$sql = "SELECT productLine, textDescription FROM productlines";
$result = @mysqli_query($conn, $sql);

if (@mysqli_num_rows($result) > 0) {
    // output data of each row
    echo "<table id='index-table'>";
    echo "<tr><th>Product Line</th><th>Description</th></tr>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr onclick='showTable(\"" . $row["productLine"] . "\")'><td>" . $row["productLine"]. "</td><td>" . $row["textDescription"]. "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>This table cannot be displayed due to an error: " . mysqli_error($conn) . "</p>";
}


$sql = "SELECT * FROM products";

$result = mysqli_query($conn, $sql);



if (mysqli_num_rows($result) > 0) {

    $products_array = array();
    
    while($row = mysqli_fetch_assoc($result)) {
    
        
        $products_array[$row['productCode']] = array(
            'productName' => $row["productName"], 
            'productLine' => $row["productLine"], 
            'productScale' => $row["productScale"],
            'productVendor' => $row["productVendor"],
            'productDescription' => $row["productDescription"],
            'quantityInStock' => $row["quantityInStock"],
            'buyPrice' => $row["buyPrice"],
            'MSRP' => $row["MSRP"],
        );
    }
} else {
    echo "0 results";
}

mysqli_close($conn);

echo "<div id='table-div'></div>"

?>


<script>
    var prodDict = <?php echo json_encode($products_array); ?>;

    function showTable(pline) {
        
        let html = "<table id='product-table'><tr>"
        html += "<th>Name</th>"
        html += "<th>Product Line</th>"
        html += "<th>Scale</th>"
        html += "<th>Vendor</th>"
        html += "<th>Description</th>"
        html += "<th>Quantity in Stock</th>"
        html += "<th>Price</th>"
        html += "<th>MSRP</th>"
        html += "</tr>"
        
        for (var prod in prodDict) {
            if (prodDict[prod]["productLine"] === pline) {
                html += "<tr>"
                for (cell in prodDict[prod]){
                    html += "<td>" + prodDict[prod][cell] + "</td>"   ;
                }     
                html += "</tr>"
            }
        }
        
        html += "</table>"
        
        tabDiv = document.getElementById("table-div");
        tabDiv.innerHTML = html;
    }
</script>

