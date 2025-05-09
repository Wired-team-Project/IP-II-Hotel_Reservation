<?php
include_once('../partials/header.php');

$errors = [
    'fullname' => '',
    'email' => '',
    'password' => '',
    'phone' => '',
    'address' => '',
    'country' => '',
    'id_passport' => '',
    'dob' => ''
];

$old = [
    'fullname' => '',
    'email' => '',
    'phone' => '',
    'address' => '',
    'country' => '',
    'id_passport' => '',
    'dob' => ''
];

if (isset($_GET['errors'])) {
    $errors = json_decode(urldecode($_GET['errors']), true);
}
if (isset($_GET['old'])) {
    $old = json_decode(urldecode($_GET['old']), true);
}
?>

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="container">

        <h2 class="text-center mb-4">Create an Account</h2>
        <form action="../php/register_process.php" method="POST" class="w-50 mx-auto" novalidate>
            
            <div class="form-group mb-3">
                <label for="fullname">Full Name:</label>
                <input type="text" name="fullname" id="fullname" class="form-control <?php echo $errors['fullname'] ? 'is-invalid' : ''; ?>" 
                    value="<?php echo htmlspecialchars($old['fullname']); ?>" style="max-width: 90%; width: 350px;">
                <div class="invalid-feedback" style="color: red;"><?php echo $errors['fullname']; ?></div>
            </div>

            <div class="form-group mb-3">
                <label for="email">Email Address:</label>
                <input type="email" name="email" id="email" class="form-control <?php echo $errors['email'] ? 'is-invalid' : ''; ?>" 
                    value="<?php echo htmlspecialchars($old['email']); ?>" style="max-width: 90%; width: 350px;">
                <div class="invalid-feedback" style="color: red;"><?php echo $errors['email']; ?></div>
            </div>

            <div class="form-group mb-3">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" class="form-control <?php echo $errors['password'] ? 'is-invalid' : ''; ?>" 
                    style="max-width: 90%; width: 350px;">
                <div class="invalid-feedback" style="color: red;"><?php echo $errors['password']; ?></div>
            </div>

            <div class="form-group mb-3">
                <label for="phone">Phone Number:</label>
                <input type="text" name="phone" id="phone" class="form-control <?php echo $errors['phone'] ? 'is-invalid' : ''; ?>" 
                    value="<?php echo htmlspecialchars($old['phone']); ?>" style="max-width: 90%; width: 350px;">
                <div class="invalid-feedback" style="color: red;"><?php echo $errors['phone']; ?></div>
            </div>

            <div class="form-group mb-3">
                <label for="address">Address:</label>
                <input type="text" name="address" id="address" class="form-control <?php echo $errors['address'] ? 'is-invalid' : ''; ?>" 
                    value="<?php echo htmlspecialchars($old['address']); ?>" style="max-width: 90%; width: 350px;">
                <div class="invalid-feedback" style="color: red;"><?php echo $errors['address']; ?></div>
            </div>

            <div class="form-group mb-3">
                <label for="country">Country:</label>
                <select name="country" id="country" class="form-control <?php echo $errors['country'] ? 'is-invalid' : ''; ?>" style="max-width: 90%; width: 350px;">
                    <option value="">Select your country</option>
                    <?php
                    $countries = [
                        "Argentina", "Australia", "Brazil", "Canada", "Chile", "China", 
                        "Colombia", "Denmark", "Egypt", "Ethiopia", "France", "Germany", 
                        "Greece", "India", "Indonesia", "Italy", "Japan", "Kenya", 
                        "Malaysia", "Mexico", "Netherlands", "New Zealand", "Nigeria", 
                        "Norway", "Philippines", "Portugal", "Russia", "Saudi Arabia", 
                        "Singapore", "South Africa", "South Korea", "Spain", "Sweden", 
                        "Switzerland", "Thailand", "Turkey", "United Arab Emirates", 
                        "United Kingdom", "United States", "Vietnam"
                    ];
                    foreach ($countries as $country) {
                        $isSelected = $old['country'] === $country ? 'selected' : '';
                        echo "<option value='$country' $isSelected>$country</option>";
                    }
                    ?>
                </select>
                <div class="invalid-feedback" style="color: red;"><?php echo $errors['country']; ?></div>
            </div>

            <div class="form-group mb-3">
                <label for="id_passport">ID/Passport Number:</label>
                <input type="text" name="id_passport" id="id_passport" class="form-control <?php echo $errors['id_passport'] ? 'is-invalid' : ''; ?>" 
                    value="<?php echo htmlspecialchars($old['id_passport']); ?>" style="max-width: 90%; width: 350px;">
                <div class="invalid-feedback" style="color: red;"><?php echo $errors['id_passport']; ?></div>
            </div>

            <div class="form-group mb-3">
                <label for="dob">Date of Birth:</label>
                <input type="date" name="dob" id="dob" class="form-control <?php echo $errors['dob'] ? 'is-invalid' : ''; ?>" 
                    value="<?php echo htmlspecialchars($old['dob']); ?>" style="max-width: 90%; width: 350px;">
                <div class="invalid-feedback" style="color: red;"><?php echo $errors['dob']; ?></div>
            </div>


            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>

        <p class="text-center mt-3">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </div>

<?php
include_once('../partials/footer.php');
?>
