<?php 
include "./includes/auth.php";
requireAuth();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrewSync | Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
        <?php include 'sidebar.php' ?>
    <div class="main-content">
        <header class="topbar">
            <div>
                <h2>Employee Dashboard</h2>
            </div>
           
        </header>
        
        <div class="content-area">
            <div class="page-header">
                <h1>Welcome to CrewSync</h1>
                <p>Manage your professional profile and work experience</p>
            </div>
            
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Personal Information</h3>
                    <p>Manage your personal details</p>
                    <a href="personal-info.php" class="btn primary-btn">
                        <i class="fas fa-edit"></i> Manage
                    </a>
                </div>

                <div class="card">
                    <h3>Professional Information</h3>
                    <p>Update your work details</p>
                    <a href="professional-info.php" class="btn primary-btn">
                        <i class="fas fa-edit"></i> Manage
                    </a>
                </div>

                <div class="card">
                    <h3>Work Experience</h3>
                    <p>Add your past experiences</p>
                    <a href="experience.php" class="btn primary-btn">
                        <i class="fas fa-edit"></i> Manage
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>