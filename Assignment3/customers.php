<html>

<head>
    <meta charset="utf-8">
    <title>Customers</title>
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

// Query to get customers sorted by country
$sql = "SELECT customerName, country, city, phone FROM customers ORDER BY country";
$result = mysqli_query($conn, $sql);


if (@mysqli_num_rows($result) > 0) {
    // output data of each row
    echo "<table id='cust-table'>";
    echo "<tr><th>Customer Name</th><th>Country</th><th>City</th><th>Phone</th></tr>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>" . $row["customerName"]. "</td><td>" . $row["country"]. "</td><td>" . $row["city"]. "</td><td>" . $row["phone"]. "</td></tr>";
    }
} else {
    echo "<p>This table cannot be displayed due to an error: " . mysqli_error($conn) . "</p>";
}

mysqli_close($conn);
?>
