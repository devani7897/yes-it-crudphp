<?php
include_once 'config/database.php';
include_once 'classes/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$user->id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');

// Read the user first to get file paths
$user->readOne();

if($user->delete()){
    // Delete profile picture if exists
    if($user->profile_pic && file_exists($user->profile_pic)) {
        unlink($user->profile_pic);
    }
    
    // Delete resume if exists
    if($user->resume && file_exists($user->resume)) {
        unlink($user->resume);
    }
    
    header("Location: index.php?action=deleted");
} else {
    die('Unable to delete user.');
}
?>