<?php 
include 'includes/auth.php';
requireAuth();
include 'config/db.php';

$employee_id = $_SESSION['employee_id'];
$info = [];

$stmt = $pdo->prepare("SELECT * FROM professional_info WHERE employee_id = ?");
$stmt->execute([$employee_id]);
$info = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_id = $_POST['employee_id_number'];
    $dept = $_POST['department'];
    $designation = $_POST['designation'];
    $joining_date = $_POST['joining_date'];
    $location = $_POST['work_location'];

    if ($info) {
        $sql = "UPDATE professional_info SET 
                employee_id_number = ?, department = ?, designation = ?, 
                joining_date = ?, work_location = ? 
                WHERE employee_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$emp_id, $dept, $designation, $joining_date, $location, $employee_id]);
    } else {
        $sql = "INSERT INTO professional_info 
                (employee_id, employee_id_number, department, designation, joining_date, work_location) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$employee_id, $emp_id, $dept, $designation, $joining_date, $location]);
    }
    
    header('Location: professional-info.php?success=1');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrewSync | Professional Information</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <link rel="stylesheet" href="assets/css/professional-info.css">
</head>
<body>
   <?php include 'sidebar.php'?>
    
    <div class="main-content">
        <header class="topbar">
            <div>
                <h2>Professional Information</h2>
            </div>
           
        </header>
        
        <div class="content-area">
            <div class="page-header">
                <h1>Professional Information</h1>
                <p>Manage your professional details and employment information</p>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i> Information saved successfully!
                </div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="POST">
                    <div class="form-group">
                        <label>Employee ID</label>
                        <input type="text" name="employee_id_number" 
                            value="<?= $info['employee_id_number'] ?? '' ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Department</label>
                            <input type="text" name="department" 
                                value="<?= $info['department'] ?? '' ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Designation</label>
                            <input type="text" name="designation" 
                                value="<?= $info['designation'] ?? '' ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Joining Date</label>
                            <input type="date" name="joining_date" 
                                value="<?= $info['joining_date'] ?? '' ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Work Location</label>
                            <input type="text" name="work_location" 
                                value="<?= $info['work_location'] ?? '' ?>" required>
                        </div>
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