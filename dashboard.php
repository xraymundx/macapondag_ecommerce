<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Default join type is LEFT JOIN if not selected
$join_type = isset($_POST['join_type']) ? $_POST['join_type'] : 'LEFT JOIN';

// Set up the query based on the join type selected
switch ($join_type) {
    case 'INNER JOIN':
        $query = "
            SELECT p.product_id, p.product_name, p.price, p.stock, c.customer_name, c.email
            FROM products p
            INNER JOIN customers c ON p.customer_id = c.customer_id
        ";
        break;
    case 'RIGHT JOIN':
        $query = "
            SELECT p.product_id, p.product_name, p.price, p.stock, c.customer_name, c.email
            FROM products p
            RIGHT JOIN customers c ON p.customer_id = c.customer_id
        ";
        break;
    case 'FULL OUTER JOIN':
        // FULL OUTER JOIN simulation using UNION
        $query = "
            SELECT p.product_id, p.product_name, p.price, p.stock, c.customer_name, c.email
            FROM products p
            LEFT JOIN customers c ON p.customer_id = c.customer_id
            UNION
            SELECT p.product_id, p.product_name, p.price, p.stock, c.customer_name, c.email
            FROM products p
            RIGHT JOIN customers c ON p.customer_id = c.customer_id
            WHERE p.customer_id IS NULL OR c.customer_id IS NULL
        ";
        break;
    case 'LEFT JOIN':
    default:
        $query = "
            SELECT p.product_id, p.product_name, p.price, p.stock, c.customer_name, c.email
            FROM products p
            LEFT JOIN customers c ON p.customer_id = c.customer_id
        ";
        break;
}

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('https://img.freepik.com/premium-photo/flat-lay-various-technology-items-office-supplies-accessories-blue-background_36682-205936.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            margin: 0;
            padding: 0;
        }
        header {
            background: rgba(0, 0, 0, 0.7); /* Semi-transparent background */
            color: white;
            padding: 15px;
            text-align: center;
        }
        nav {
            margin: 10px 0;
        }
        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.8); /* Semi-transparent table background */
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: black;
            color: white;
        }
        h2 {
            text-align: center;
        }
        form {
            text-align: center;
            margin: 20px 0;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 5px;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <h1>Gadget Store</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Products</a>
            <a href="customers.php">Customers</a>
            <a class="logout" href="logout.php">Logout</a>
        </nav>
    </header>
    
    <main>

        <!-- Join selection form -->
        <form method="POST" action="">
            <button type="submit" name="join_type" value="INNER JOIN">Inner Join</button>
            <button type="submit" name="join_type" value="LEFT JOIN">Left Join</button>
            <button type="submit" name="join_type" value="RIGHT JOIN">Right Join</button>
            <button type="submit" name="join_type" value="FULL OUTER JOIN">Full Outer Join</button>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Customer Name</th>
                <th>Customer Email</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['product_id']); ?></td>
                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                <td><?php echo htmlspecialchars($row['price']); ?></td>
                <td><?php echo htmlspecialchars($row['stock']); ?></td>
                <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </main>
</body>
</html>
