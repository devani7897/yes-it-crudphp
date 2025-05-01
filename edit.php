<?php
include_once 'config/database.php';
include_once 'classes/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$user->id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');
$user->readOne();

if($_POST){
    $user->name = $_POST['name'];
    $user->email = $_POST['email'];
    $user->phone = $_POST['phone'];
    
    // Handle profile picture update
    if($_FILES['profile_pic']['name']) {
        // Delete old file if exists
        if($user->profile_pic && file_exists($user->profile_pic)) {
            unlink($user->profile_pic);
        }
        
        // Upload new file
        $target_dir = "uploads/profile_pics/";
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
        if($check !== false) {
            $profile_pic = $target_dir . uniqid() . '.' . $imageFileType;
            move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $profile_pic);
            $user->profile_pic = $profile_pic;
        }
    }
    
    // Handle resume update
    if($_FILES['resume']['name']) {
        // Delete old file if exists
        if($user->resume && file_exists($user->resume)) {
            unlink($user->resume);
        }
        
        // Upload new file
        $target_dir = "uploads/resumes/";
        $target_file = $target_dir . basename($_FILES["resume"]["name"]);
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $allowed = array('pdf', 'doc', 'docx');
        if(in_array($fileType, $allowed)) {
            $resume = $target_dir . uniqid() . '.' . $fileType;
            move_uploaded_file($_FILES["resume"]["tmp_name"], $resume);
            $user->resume = $resume;
        }
    }
    
    if($user->update()){
        echo "<div class='alert alert-success'>User was updated.</div>";
    } else {
        echo "<div class='alert alert-danger'>Unable to update user.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container mt-5">
        <h1>Edit User</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id={$user->id}"); ?>" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user->name); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user->email); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user->phone); ?>" required>
            </div>
            <div class="mb-3">
                <label for="profile_pic" class="form-label">Profile Picture</label>
                <?php if($user->profile_pic): ?>
                    <div>
                        <img src="<?php echo htmlspecialchars($user->profile_pic); ?>" width="100" class="mb-2">
                        <br>
                        <a href="#" onclick="document.getElementById('remove_profile_pic').value='1'; document.getElementById('current_profile_pic').style.display='none'; return false;">Remove</a>
                        <input type="hidden" id="remove_profile_pic" name="remove_profile_pic" value="0">
                    </div>
                    <div id="current_profile_pic">
                        Current: <?php echo basename($user->profile_pic); ?>
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control" id="profile_pic" name="profile_pic" accept="image/*">
            </div>
            <div class="mb-3">
                <label for="resume" class="form-label">Resume</label>
                <?php if($user->resume): ?>
                    <div>
                        Current: <?php echo basename($user->resume); ?>
                        <br>
                        <a href="#" onclick="document.getElementById('remove_resume').value='1'; document.getElementById('current_resume').style.display='none'; return false;">Remove</a>
                        <input type="hidden" id="remove_resume" name="remove_resume" value="0">
                    </div>
                    <div id="current_resume">
                        <a href="<?php echo htmlspecialchars($user->resume); ?>" target="_blank">Download</a>
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="index.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>