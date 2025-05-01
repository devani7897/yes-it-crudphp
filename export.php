<?php
include_once 'config/database.php';
include_once 'classes/User.php';
include_once 'classes/Export.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

// Search term
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get all users for export
$stmt = $user->readAll(1, 1000, $search); // Large number to get all records
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for export
$export_data = array();
foreach($users as $user) {
    $export_data[] = array(
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'phone' => $user['phone'],
        'profile_pic' => $user['profile_pic'],
        'resume' => $user['resume']
    );
}

// Export based on type
$type = isset($_GET['type']) ? $_GET['type'] : 'csv';

if($type == 'pdf') {
    Export::toPDF($export_data, 'users_export.pdf');
} else {
    Export::toCSV($export_data, 'users_export.csv');
}
?>