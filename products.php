<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$success = $error = "";

// Handle form submission for adding products
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $customer_id = $_POST['customer_id'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO products (product_name, price, stock, customer_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdii", $product_name, $price, $stock, $customer_id);

    if ($stmt->execute()) {
        $success = "Product added successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch existing products
$query = "SELECT * FROM products";
$result = $conn->query($query);

// Fetch customers for the dropdown
$customer_query = "SELECT customer_id, customer_name FROM customers";
$customer_result = $conn->query($customer_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            background-image: url('https://img.freepik.com/premium-photo/flat-lay-various-technology-items-office-supplies-accessories-blue-background_36682-205936.jpg');
            background-size: cover; /* Ensure the image covers the entire body */
            background-position: center; /* Center the image */
        }
        header {
            background: rgba(0, 0, 0, 0.7); /* Semi-transparent background to make text readable */
            color: white;
            padding: 15px;
            text-align: center;
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
            background: white;
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
        form {
            background: rgba(255, 255, 255, 0.8); /* Semi-transparent background for the form */
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        input, select {
            width: calc(100% - 22px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <header>
        <h1>Products Management</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Products</a>
            <a href="customers.php">Customers</a>
            <a class="logout" href="logout.php">Logout</a>
        </nav>
    </header>
    
    <main>
        <form method="POST" action="">
            <?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>
            <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <input type="text" name="product_name" placeholder="Product Name" required>
            <input type="number" name="price" placeholder="Price" step="0.01" required>
            <input type="number" name="stock" placeholder="Quantity" required>
            <select name="customer_id" required>
                <option value="">Select Customer</option>
                <?php if ($customer_result): ?>
                    <?php while ($customer_row = $customer_result->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($customer_row['customer_id']); ?>">
                            <?php echo htmlspecialchars($customer_row['customer_name']); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <button type="submit">Add Product</button>
        </form>

        <h2>Existing Products</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Customer ID</th>
            </tr>
            <?php if ($result): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['product_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['price']); ?></td>
                    <td><?php echo htmlspecialchars($row['stock']); ?></td>
                    <td><?php echo htmlspecialchars($row['customer_id']); ?></td>
                </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </table>
    </main>
</body>
</html>
