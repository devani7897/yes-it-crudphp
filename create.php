<?php
include_once 'config/database.php';
include_once 'classes/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

if($_POST){
    $user->name = $_POST['name'];
    $user->email = $_POST['email'];
    $user->phone = $_POST['phone'];
    
    // Upload profile picture
    $profile_pic = '';
    if($_FILES['profile_pic']['name']) {
        $target_dir = "uploads/profile_pics/";
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
        if($check !== false) {
            // Generate unique filename
            $profile_pic = $target_dir . uniqid() . '.' . $imageFileType;
            move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $profile_pic);
            $user->profile_pic = $profile_pic;
        }
    }
    
    // Upload resume
    $resume = '';
    if($_FILES['resume']['name']) {
        $target_dir = "uploads/resumes/";
        $target_file = $target_dir . basename($_FILES["resume"]["name"]);
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Allow certain file formats
        $allowed = array('pdf', 'doc', 'docx');
        if(in_array($fileType, $allowed)) {
            // Generate unique filename
            $resume = $target_dir . uniqid() . '.' . $fileType;
            move_uploaded_file($_FILES["resume"]["tmp_name"], $resume);
            $user->resume = $resume;
        }
    }
    
    if($user->create()){
        echo "<div class='alert alert-success'>User was created.</div>";
    } else {
        echo "<div class='alert alert-danger'>Unable to create user.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container mt-5">
        <h1>Create User</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="mb-3">
                <label for="profile_pic" class="form-label">Profile Picture</label>
                <input type="file" class="form-control" id="profile_pic" name="profile_pic" accept="image/*">
            </div>
            <div class="mb-3">
                <label for="resume" class="form-label">Resume</label>
                <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="index.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>