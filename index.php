<?php
include_once 'config/database.php';
include_once 'classes/User.php';
include_once 'classes/Pagination.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

// Search term
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$records_per_page = 5;

// Sorting
$sort_field = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Get total count
$total_count = $user->countAll($search);
$pagination = new Pagination($page, $records_per_page, $total_count);

// Get users
$stmt = $user->readAll($page, $records_per_page, $search, $sort_field, $sort_order);
$num = $stmt->rowCount();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sort-asc:after { content: ' ↑'; }
        .sort-desc:after { content: ' ↓'; }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container mt-5">
        <h1>User Management</h1>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <a href="create.php" class="btn btn-primary">Create New User</a>
            </div>
            <div class="col-md-6">
                <form method="get" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-outline-success">Search</button>
                </form>
            </div>
        </div>
        
        <?php if($num > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>
                            <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page; ?>&sort=id&order=<?php echo ($sort_field == 'id' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>" class="<?php echo ($sort_field == 'id') ? ($sort_order == 'ASC' ? 'sort-asc' : 'sort-desc') : ''; ?>">
                                ID
                            </a>
                        </th>
                        <th>
                            <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page; ?>&sort=name&order=<?php echo ($sort_field == 'name' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>" class="<?php echo ($sort_field == 'name') ? ($sort_order == 'ASC' ? 'sort-asc' : 'sort-desc') : ''; ?>">
                                Name
                            </a>
                        </th>
                        <th>
                            <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page; ?>&sort=email&order=<?php echo ($sort_field == 'email' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>" class="<?php echo ($sort_field == 'email') ? ($sort_order == 'ASC' ? 'sort-asc' : 'sort-desc') : ''; ?>">
                                Email
                            </a>
                        </th>
                        <th>Phone</th>
                        <th>Profile Pic</th>
                        <th>Resume</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td>
                            <?php if($row['profile_pic']): ?>
                                <img src="<?php echo htmlspecialchars($row['profile_pic']); ?>" width="50" height="50">
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($row['resume']): ?>
                                <a href="<?php echo htmlspecialchars($row['resume']); ?>" target="_blank">Download</a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php if($pagination->has_previous_page()): ?>
                        <li class="page-item">
                            <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $pagination->previous_page(); ?>&sort=<?php echo $sort_field; ?>&order=<?php echo $sort_order; ?>">Previous</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for($i=1; $i<=$pagination->total_pages(); $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>&sort=<?php echo $sort_field; ?>&order=<?php echo $sort_order; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if($pagination->has_next_page()): ?>
                        <li class="page-item">
                            <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $pagination->next_page(); ?>&sort=<?php echo $sort_field; ?>&order=<?php echo $sort_order; ?>">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            
            <div class="mb-3">
                <a href="export.php?type=csv&search=<?php echo urlencode($search); ?>" class="btn btn-success">Export to CSV</a>
                <a href="export.php?type=pdf&search=<?php echo urlencode($search); ?>" class="btn btn-danger">Export to PDF</a>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-warning">No users found.</div>
        <?php endif; ?>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>