<?php
include 'includes/auth.php';
requireAuth();
include 'config/db.php';

$employee_id = $_SESSION['employee_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO experience 
                (employee_id, company_name, role, duration_from, duration_to, description)
                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $employee_id,
                $_POST['company_name'],
                $_POST['role'],
                $_POST['duration_from'],
                $_POST['duration_to'],
                $_POST['description']
            ]);
            $exp_id = $pdo->lastInsertId();

            if (!empty($_POST['technologies'])) {
                foreach ($_POST['technologies'] as $tech_name) {
                    $tech_name = trim($tech_name);
                    if (!empty($tech_name)) {
                        // Check if technology exists
                        $tech_stmt = $pdo->prepare("SELECT id FROM technologies WHERE name = ?");
                        $tech_stmt->execute([$tech_name]);
                        $tech_id = $tech_stmt->fetchColumn();

                        if (!$tech_id) {
                            $insert_tech = $pdo->prepare("INSERT INTO technologies (name) VALUES (?)");
                            $insert_tech->execute([$tech_name]);
                            $tech_id = $pdo->lastInsertId();
                        }

                        $link_stmt = $pdo->prepare("INSERT INTO experience_technologies (experience_id, technology_id) VALUES (?, ?)");
                        $link_stmt->execute([$exp_id, $tech_id]);
                    }
                }
            }

            $pdo->commit();
            $message = 'Experience added successfully!';
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = 'Error: ' . $e->getMessage();
        }
    }
    elseif (isset($_POST['update'])) {
        try {
            $pdo->beginTransaction();
            $exp_id = $_POST['exp_id'];

            $stmt = $pdo->prepare("UPDATE experience SET 
                company_name = ?, role = ?, duration_from = ?, duration_to = ?, 
                description = ? 
                WHERE id = ? AND employee_id = ?");
            $stmt->execute([
                $_POST['company_name'],
                $_POST['role'],
                $_POST['duration_from'],
                $_POST['duration_to'],
                $_POST['description'],
                $exp_id,
                $employee_id
            ]);

            // Remove existing technologies
            $del_stmt = $pdo->prepare("DELETE FROM experience_technologies WHERE experience_id = ?");
            $del_stmt->execute([$exp_id]);

            if (!empty($_POST['technologies'])) {
                foreach ($_POST['technologies'] as $tech_name) {
                    $tech_name = trim($tech_name);
                    if (!empty($tech_name)) {
                        // Check if technology exists
                        $tech_stmt = $pdo->prepare("SELECT id FROM technologies WHERE name = ?");
                        $tech_stmt->execute([$tech_name]);
                        $tech_id = $tech_stmt->fetchColumn();

                        // Create if doesn't exist
                        if (!$tech_id) {
                            $insert_tech = $pdo->prepare("INSERT INTO technologies (name) VALUES (?)");
                            $insert_tech->execute([$tech_name]);
                            $tech_id = $pdo->lastInsertId();
                        }

                        // Link to experience
                        $link_stmt = $pdo->prepare("INSERT INTO experience_technologies (experience_id, technology_id) VALUES (?, ?)");
                        $link_stmt->execute([$exp_id, $tech_id]);
                    }
                }
            }

            $pdo->commit();
            $message = 'Experience updated successfully!';
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = 'Error: ' . $e->getMessage();
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Experience will be deleted via CASCADE
    $stmt = $pdo->prepare("DELETE FROM experience WHERE id = ? AND employee_id = ?");
    $stmt->execute([$id, $employee_id]);
    $message = 'Experience deleted successfully!';
}

$experiences = [];
$exp_stmt = $pdo->prepare("
    SELECT e.*, 
    GROUP_CONCAT(t.name SEPARATOR ', ') AS tech_list
    FROM experience e
    LEFT JOIN experience_technologies et ON e.id = et.experience_id
    LEFT JOIN technologies t ON et.technology_id = t.id
    WHERE e.employee_id = ?
    GROUP BY e.id
    ORDER BY e.duration_from DESC
");
$exp_stmt->execute([$employee_id]);
$experiences = $exp_stmt->fetchAll(PDO::FETCH_ASSOC);

$editing = false;
$editData = ['technologies' => []];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $exp_stmt = $pdo->prepare("SELECT * FROM experience WHERE id = ? AND employee_id = ?");
    $exp_stmt->execute([$id, $employee_id]);
    $editData = $exp_stmt->fetch();

    if ($editData) {
        $editing = true;

        $tech_stmt = $pdo->prepare("
            SELECT t.name 
            FROM experience_technologies et
            JOIN technologies t ON et.technology_id = t.id
            WHERE et.experience_id = ?
        ");
        $tech_stmt->execute([$id]);
        $editData['technologies'] = $tech_stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrewSync | Work Experience</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/experience.css">
</head>
<body>
    <?php include 'sidebar.php' ?>
    
    <div class="main-content">
        <header class="topbar">
            <div>
                <h2>Work Experience</h2>
            </div>
            
        </header>
        
        <div class="content-area">
            <div class="page-header">
                <h1>Work Experience</h1>
                <p>Manage your professional work history and skills</p>
            </div>
            
            <?php if ($message): ?>
                <div class="alert <?= strpos($message, 'Error') === false ? 'success' : 'error' ?>">
                    <i class="fas <?= strpos($message, 'Error') === false ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
                    <?= $message ?>
                </div>
            <?php endif; ?>
            
            <div class="form-container">
                <h3><?= $editing ? 'Edit Experience' : 'Add New Experience' ?></h3>
                <form method="POST" id="experience-form">
                    <?php if ($editing): ?>
                        <input type="hidden" name="exp_id" value="<?= $editData['id'] ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" name="company_name"
                            value="<?= $editing ? htmlspecialchars($editData['company_name']) : '' ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Role</label>
                        <input type="text" name="role"
                            value="<?= $editing ? htmlspecialchars($editData['role']) : '' ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>From Date</label>
                            <input type="date" name="duration_from"
                                value="<?= $editing ? $editData['duration_from'] : '' ?>" required>
                        </div>

                        <div class="form-group">
                            <label>To Date</label>
                            <input type="date" name="duration_to"
                                value="<?= $editing ? $editData['duration_to'] : '' ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Technologies Used</label>
                        <div id="technologies-container">
                            <?php if ($editing && !empty($editData['technologies'])): ?>
                                <?php foreach ($editData['technologies'] as $tech): ?>
                                    <div class="tech-input">
                                        <input type="text" name="technologies[]"
                                            value="<?= htmlspecialchars($tech) ?>"
                                            placeholder="Technology name">
                                        <button type="button" class="remove-tech">×</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="tech-input">
                                    <input type="text" name="technologies[]" placeholder="Technology name">
                                    <button type="button" class="remove-tech">×</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="add-tech" class="btn small">
                            <i class="fas fa-plus"></i> Add Technology
                        </button>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4" required><?= $editing ? htmlspecialchars($editData['description']) : '' ?></textarea>
                    </div>

                    <button type="submit" name="<?= $editing ? 'update' : 'add' ?>" class="btn">
                        <i class="fas <?= $editing ? 'fa-edit' : 'fa-plus' ?>"></i>
                        <?= $editing ? 'Update Experience' : 'Add Experience' ?>
                    </button>

                    <?php if ($editing): ?>
                        <a href="experience.php" class="btn cancel">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="experience-list">
                <h3>Your Experiences</h3>

                <?php if (count($experiences) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Role</th>
                                <th>Duration</th>
                                <th>Technologies</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($experiences as $exp): ?>
                                <tr>
                                    <td><?= htmlspecialchars($exp['company_name']) ?></td>
                                    <td><?= htmlspecialchars($exp['role']) ?></td>
                                    <td>
                                        <?= date('M Y', strtotime($exp['duration_from'])) ?> -
                                        <?= date('M Y', strtotime($exp['duration_to'])) ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($exp['tech_list'])): ?>
                                            <div class="tech-tags">
                                                <?php
                                                $techs = explode(', ', $exp['tech_list']);
                                                foreach ($techs as $tech): ?>
                                                    <span class="tech-tag"><?= htmlspecialchars($tech) ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="no-tech">No technologies listed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions">
                                        <a href="experience.php?edit=<?= $exp['id'] ?>" class="btn edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="experience.php?delete=<?= $exp['id'] ?>"
                                            class="btn delete"
                                            onclick="return confirm('Are you sure you want to delete this experience?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No experiences added yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('add-tech').addEventListener('click', function() {
                const container = document.getElementById('technologies-container');
                const div = document.createElement('div');
                div.className = 'tech-input';
                div.innerHTML = `
                    <input type="text" name="technologies[]" placeholder="Technology name">
                    <button type="button" class="remove-tech">×</button>
                `;
                container.appendChild(div);

                div.querySelector('.remove-tech').addEventListener('click', function() {
                    div.remove();
                });
            });

            document.querySelectorAll('.remove-tech').forEach(button => {
                button.addEventListener('click', function() {
                    this.parentElement.remove();
                });
            });

            document.getElementById('experience-form').addEventListener('submit', function(e) {
                const techInputs = document.querySelectorAll('input[name="technologies[]"]');
                let hasValue = false;

                techInputs.forEach(input => {
                    if (input.value.trim() !== '') {
                        hasValue = true;
                    }
                });

                if (!hasValue) {
                    e.preventDefault();
                    alert('Please add at least one technology');
                }
            });
        });
    </script>
</body>
</html>