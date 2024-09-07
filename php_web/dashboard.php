<?php 
session_start();
if (!isset($_SESSION['users_first_name']) || !isset($_SESSION['users_last_name'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli("127.0.0.1", "riad", "1234", "myphptutedb", 3307);

$users_first_name = $_SESSION['users_first_name'];
$users_last_name = $_SESSION['users_last_name'];
$bmi_result = '';
$bmimsg = '';

// Check if form is submitted
if (isset($_POST['submit_bmi'])) {
    $age = $_POST['age'];
    $height = $_POST['height'];
    $weight = $_POST['weight'];

    if (!empty($age) && !empty($height) && !empty($weight)) {
        // Calculate BMI
        $height_in_meters = $height / 100;
        $bmi = $weight / ($height_in_meters * $height_in_meters);
        $bmi = round($bmi, 2);

        // Update user data with age and BMI result
        $sql = "UPDATE users SET age = '$age', bmi_result = '$bmi' WHERE user_first_name = '$users_first_name' AND user_last_name = '$users_last_name'";

        if ($conn->query($sql) === TRUE) {
            $bmimsg = "Your BMI is $bmi";
        } else {
            $bmimsg = "Error updating BMI: " . $conn->error;
        }
    } else {
        $bmimsg = "Please fill in all fields.";
    }
}

// Retrieve the user's BMI and age if available
$sql = "SELECT age, bmi_result FROM users WHERE user_first_name = '$users_first_name' AND user_last_name = '$users_last_name'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $age = $row['age'];
    $bmi_result = $row['bmi_result'];
} else {
    $age = $bmi_result = '';
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="container" style="margin-top:50px">
            <h1>Welcome <?php echo $users_first_name . ' ' . $users_last_name; ?></h1>
        </div>
        <div class="container" style="margin-top:50px">
            <h3>BMI Calculator</h3>
            <form action="dashboard.php" method="POST">
                <div class="mb-3">
                    <label for="age" class="form-label">Age:</label>
                    <input type="number" name="age" class="form-control" placeholder="Enter your age" value="<?php echo $age; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="height" class="form-label">Height (in cm):</label>
                    <input type="number" name="height" class="form-control" placeholder="Enter your height in cm" required>
                </div>
                <div class="mb-3">
                    <label for="weight" class="form-label">Weight (in kg):</label>
                    <input type="number" name="weight" class="form-control" placeholder="Enter your weight in kg" required>
                </div>
                <button type="submit" name="submit_bmi" class="btn btn-primary">Calculate BMI</button>
            </form>
            <p class="mt-3 text-success"><?php echo $bmimsg; ?></p>

            <!-- Table to display user's information -->
            <div class="mt-4">
                <h4>Your Information:</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Age</th>
                            <th>BMI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $users_first_name; ?></td>
                            <td><?php echo !empty($age) ? $age : 'N/A'; ?></td>
                            <td><?php echo !empty($bmi_result) ? $bmi_result : 'N/A'; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
        <p><a href="logout.php" class="btn btn-danger">Log Out</a></p>
    </div>
</body>
</html>
