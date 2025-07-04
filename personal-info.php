<?php 
include 'includes/auth.php';
requireAuth();
include 'config/db.php';

$employee_id = $_SESSION['employee_id'];
$info = [];

$stmt = $pdo->prepare("SELECT * FROM personal_info WHERE employee_id = ?");
$stmt->execute([$employee_id]);
$info = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $dob = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $contact = $_POST['contact_number'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    if ($info) {
        $sql = "UPDATE personal_info SET full_name=?, date_of_birth=?, gender=?, 
                contact_number=?, email=?, address=? WHERE employee_id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$full_name, $dob, $gender, $contact, $email, $address, $employee_id]);
    } else {
        $sql = "INSERT INTO personal_info (employee_id, full_name, date_of_birth, gender, 
                contact_number, email, address) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$employee_id, $full_name, $dob, $gender, $contact, $email, $address]);
    }
    
    header('Location: personal-info.php?success=1');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrewSync | Personal Information</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/personal-info.css">
</head>
<body>
    <?php include 'sidebar.php' ?>
    
    <div class="main-content">
        <header class="topbar">
            <div>
                <h2>Personal Information</h2>
            </div>
           
        </header>
        
        <div class="content-area">
            <div class="page-header">
                <h1>Personal Information</h1>
                <p>Manage your personal details and contact information</p>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i> Information saved successfully!
                </div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" value="<?= $info['full_name'] ?? '' ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" name="date_of_birth" value="<?= $info['date_of_birth'] ?? '' ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender" required>
                                <option value="Male" <?= ($info['gender'] ?? '') == 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= ($info['gender'] ?? '') == 'Female' ? 'selected' : '' ?>>Female</option>
                                <option value="Other" <?= ($info['gender'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="tel" name="contact_number" value="<?= $info['contact_number'] ?? '' ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?= $info['email'] ?? '' ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" rows="4" required><?= $info['address'] ?? '' ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Save Information
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>