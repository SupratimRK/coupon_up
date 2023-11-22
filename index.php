<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: radial-gradient(circle at top left,transparent 9%, #b6e7ff 10% ,#b6e7ff 15% , transparent 16%) , radial-gradient(circle at bottom left,transparent 9%, #b6e7ff 10% ,#b6e7ff 15% , transparent 16%), radial-gradient(circle at top right ,transparent 9%, #b6e7ff 10% ,#b6e7ff 15% , transparent 16%) , radial-gradient(circle at bottom right,transparent 9%, #b6e7ff 10% ,#b6e7ff 15% , transparent 16%),radial-gradient(circle, transparent 25%, #FAFAFa  26%),linear-gradient(45deg, transparent 46%, #b6e7ff 47%, #b6e7ff 52%, transparent 53%), linear-gradient(135deg, transparent 46%, #b6e7ff 47%, #b6e7ff 52%, transparent 53%);
            background-size: 1em 1em;
            background-color: #FAFAFa;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            display: flex;
            background-color: #ecf0f1;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .left-panel,
        .right-panel {
            flex: 1;
            padding: 20px;
            text-align: center;
        }

        .left-panel {
            background-color: #3498db;
            color: #fff;
        }

        .right-panel {
            background-color: #fff;
            color: #444; /* Consistent font color for paragraph text */
        }

        h2 {
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            margin-bottom: 8px;
            color: #444;
        }

        /* Improved styling for input fields */
        input {
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #3498db; /* Use a color similar to the left panel background color */
            border-radius: 4px;
            box-sizing: border-box;
            width: 100%;
        }

        .generate-button {
            padding: 10px;
            background-color: #2ecc71;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .generate-button:hover {
            background-color: #27ae60;
        }

        .used-button {
            margin-top: 10px;
            padding: 10px;
            background-color: #e74c3c;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .used-button:hover {
            background-color: #c0392b;
        }

        .coupon-details {
            margin-top: 20px;
            border-top: 2px solid #ddd;
            padding-top: 20px;
        }

        .coupon-details h3 {
            color: #e74c3c;
        }

        p {
            color: #444;
        }

        .qr-code {
            margin-top: 20px;
        }

        .validate-button {
            margin-top: 10px;
            padding: 10px;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .validate-button:hover {
            background-color: #2980b9;
        }
    </style>
    <title>Coupon System</title>
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <h2>Coupon System</h2>

            <!-- Coupon Generation Form -->
            <h3>Generate Coupon</h3>
            <?php
            // Handle Coupon Generation
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['discount_amount'])) {
                // Database connection
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "coupon_system";

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Generate unique coupon code
                $code = generateCouponCode();

                // Get form data
                $discountAmount = $_POST['discount_amount'] ?? 0; // Default to 0 if not set
                $generationDate = date('Y-m-d');
                $expiryDate = date('Y-m-d', strtotime('+6 months', strtotime($generationDate)));
                $isUsed = 0;

                // Insert coupon into the database
                $sql = "INSERT INTO coupons (code, discount_amount, generation_date, expiry_date, is_used)
                        VALUES ('$code', $discountAmount, '$generationDate', '$expiryDate', $isUsed)";

                if ($conn->query($sql) === TRUE) {
                    // Display coupon details
                    echo '<div class="coupon-details">';
                    echo '<h3>Coupon Details</h3>';
                    echo '<p>Discount Amount: ₹' . $discountAmount . '</p>';
                    echo '<p>Expiry Date: ' . $expiryDate . '</p>';
                    echo '<p>Unique ID: ' . $code . '</p>';

                    // Include QR code library
                    require 'phpqrcode/qrlib.php';

                    // Generate QR code with the coupon code
                    $qrCodePath = 'qrcodes/coupon_' . $code . '.png';
                    QRcode::png($code, $qrCodePath);

                    // Display QR code image
                    echo '<div class="qr-code">';
                    echo '<p><b>QR Code</b></p>';
                    echo '<img src="' . $qrCodePath . '" alt="Coupon QR Code">';
                    echo '</div>';

                    echo '</div>';
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }

                $conn->close();
            }
            ?>
            
            <form action="" method="post">
                <label for="discount_amount">Discount Amount:</label>
                <input type="number" name="discount_amount" required>

                <button class="generate-button" type="submit">Generate Coupon</button>
            </form>
        </div>

        <div class="right-panel">
            <h2>Validate Coupon</h2>

            <!-- Coupon Validation Form -->
            <form action="" method="post">
                <label for="coupon_code">Enter Coupon Code:</label>
                <input type="text" name="coupon_code" required>

                <button class="validate-button" type="submit">Validate Coupon</button>
            </form>

            <?php
            // Handle Coupon Validation
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['coupon_code'])) {
                // Database connection
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "coupon_system";

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Get the entered coupon code
                $enteredCode = $_POST['coupon_code'];

                // Check if the coupon code exists in the database and is not used
                $sql = "SELECT * FROM coupons WHERE code = '$enteredCode' AND is_used = 0";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $couponId = $row['id'];

                    // Check if the coupon is expired
                    $currentDate = date('Y-m-d');
                    if ($currentDate > $row['expiry_date']) {
                        echo "<p>Expired coupon. Please try again with a valid coupon.</p>";
                    } else {
                        // Coupon code is valid and not expired
                        // Mark the coupon as used
                        $updateSql = "UPDATE coupons SET is_used = 1 WHERE id = $couponId";
                        if ($conn->query($updateSql) === TRUE) {
                            // Display coupon details
                            echo '<div class="coupon-details">';
                            echo '<h3>Coupon Details</h3>';
                            echo '<p>Discount Amount: ₹' . $row['discount_amount'] . '</p>';
                            echo '<p>Expiry Date: ' . $row['expiry_date'] . '</p>';
                            echo '<p>Unique ID: ' . $row['id'] . '</p>';

                            // Mark as Used button
                            echo '<button class="used-button" onclick="markAsUsed(' . $couponId . ')">Mark as Used</button>';

                            echo '</div>';
                        } else {
                            echo "Error updating record: " . $conn->error;
                        }
                    }
                } else {
                    // Coupon code is either invalid or already used
                    echo "<p>Invalid or used coupon code. Please try again.</p>";
                }

                $conn->close();
            }

            // Function to generate a unique coupon code
            function generateCouponCode() {
                $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $code = '';

                for ($i = 0; $i < 8; $i++) {
                    $code .= $characters[rand(0, strlen($characters) - 1)];
                }

                return $code;
            }
            ?>
        </div>
    </div>

    <script>
        function markAsUsed(couponId) {
            // You can add AJAX functionality here to update the server/database
            alert('Coupon marked as used!');
        }
    </script>
</body>
</html>
