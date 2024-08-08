<?php

include '../components/authenticate.php';

$hostname = 'mysql-database';
$username = 'user';
$password = 'supersecretpw';
$database = 'password_manager';

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add Vault
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vaultName'])) {
    $vaultName = $_POST['vaultName'];
    $userId = 1; // Replace with the actual user ID

    $query = "INSERT INTO vaults (vault_name) VALUES ('$vaultName')";
    $result = $conn->query($query);

    if (!$result) {
        die("Error adding vault: " . $conn->error);
    }

    // Redirect to the current page after adding the vault
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

// Edit Vault
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editVaultName']) && isset($_POST['editVaultId'])) {
    $editVaultName = $_POST['editVaultName'];
    $editVaultId = $_POST['editVaultId'];

    $query = "UPDATE vaults SET vault_name = '$editVaultName' WHERE vault_id = $editVaultId";
    $result = $conn->query($query);

    if (!$result) {
        die("Error editing vault: " . $conn->error);
    }

    // Redirect to the current page after editing the vault
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

// Delete Vault
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteVaultId']) && !empty($_POST['deleteVaultId'])) {
    $deleteVaultId = $_POST['deleteVaultId'];

    $query = "DELETE FROM vaults WHERE vault_id = $deleteVaultId";

    $result = $conn->query($query);

    if (!$result) {
        die("Error deleting vault: " . $conn->error);
    }

    // Redirect to the current page after deleting the vault
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

// Retrieve vaults from the database
$query = "SELECT vaults.vault_id, vaults.vault_name
          FROM vaults";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Vaults</title>
    <!-- Add Bootstrap CSS link here -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #2c2c2c; /* Dark background color */
            color: #ffffff; /* White text */
            font-family: Arial, sans-serif;
        }
        .navbar {
            background-color: #343a40; /* Dark navbar background */
        }
        .navbar-brand, .navbar-nav .nav-link {
            color: #ffffff !important; /* White text */
        }
        .navbar-nav .nav-link:hover {
            color: #6f42c1 !important; /* Purple on hover */
        }
        .container {
            background: rgba(0, 0, 0, 0.7); /* Dark semi-transparent background */
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            margin-top: 2rem;
        }
        .container h2 {
            color: #ffffff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }
        .container p {
            color: #dddddd; /* Light grey text */
            line-height: 1.6;
        }
        footer {
            background-color: #343a40; /* Dark footer background */
            color: #ffffff;
            padding: 1rem 0;
        }
        footer span {
            font-size: 0.9rem;
        }
        .modal-content {
            background: #2c2c2c; /* Dark modal background */
            color: #ffffff; /* White text in modals */
        }
        .modal-header {
            border-bottom: 1px solid #6f42c1; /* Purple border on header */
        }
        .modal-footer {
            border-top: 1px solid #6f42c1; /* Purple border on footer */
        }
        .btn-primary {
            background-color: #6f42c1; /* Purple button */
            border: none;
        }
        .btn-primary:hover {
            background-color: #5a2e91; /* Darker purple on hover */
        }
        .btn-warning {
            background-color: #ffc107; /* Yellow button */
            border: none;
        }
        .btn-warning:hover {
            background-color: #e0a800; /* Darker yellow on hover */
        }
        .btn-danger {
            background-color: #dc3545; /* Red button */
            border: none;
        }
        .btn-danger:hover {
            background-color: #c82333; /* Darker red on hover */
        }
    </style>
</head>

<body>

<?php include '../components/nav-bar.php'; ?>

<div class="container mt-4">
    <h2>Password Vaults</h2>

    <!-- Add button to open a modal for adding a new vault -->
    <button type="button" class="btn btn-primary mb-2" data-toggle="modal" data-target="#addVaultModal">
        Add Vault
    </button>

    <!-- Table to display vaults -->
    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for vaults..."
        class="form-control mb-3">
    <table class="table table-bordered" id="vaultTable">
        <thead>
            <tr>
                <th>Vault Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['vault_name']; ?></td>
                    <td>
                        <a href="vault_details.php?vault_id=<?php echo $row['vault_id']; ?>" class="btn btn-primary btn-sm" role="button" aria-disabled="true">View Vault</a>
                        <button class="btn btn-warning btn-sm edit-btn" data-toggle="modal"
                            data-target="#editVaultModal" data-vault-name="<?php echo $row['vault_name']; ?>"
                            data-vault-id="<?php echo $row['vault_id']; ?>">Edit</button>
                        <button class="btn btn-danger btn-sm delete-btn" data-toggle="modal"
                            data-target="#deleteVaultModal" data-vault-name="<?php echo $row['vault_name']; ?>"
                            data-vault-id="<?php echo $row['vault_id']; ?>">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal for adding a new vault -->
<div class="modal" id="addVaultModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Add New Vault</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form method="POST" id="addVaultForm">
                    <div class="form-group">
                        <label for="vaultName">Vault Name:</label>
                        <input type="text" class="form-control" id="vaultName" name="vaultName" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Vault</button>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- Modal for editing a vault -->
<div class="modal" id="editVaultModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Edit Vault</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form method="POST" id="editVaultForm">
                    <div class="form-group">
                        <input type="hidden" id="editVaultId" name="editVaultId">
                        <label for="editVaultName">Vault Name:</label>
                        <input type="text" class="form-control" id="editVaultName" name="editVaultName" required>
                    </div>
                    <button type="submit" class="btn btn-warning">Update Vault</button>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- Modal for deleting a vault -->
<div class="modal" id="deleteVaultModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Delete Vault</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <p>Are you sure you want to delete <span id="vaultNameToDelete"></span>?</p>
                <form method="POST" id="deleteVaultForm">
                    <input type="hidden" id="deleteVaultId" name="deleteVaultId">
                    <button type="submit" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- Footer -->
<footer class="text-center">
    <span>&copy; 2024 Password Manager</span>
</footer>

<!-- Add Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
    // Fill edit vault modal with the selected vault data
    $('.edit-btn').on('click', function() {
        $('#editVaultName').val($(this).data('vault-name'));
        $('#editVaultId').val($(this).data('vault-id'));
    });

    // Fill delete vault modal with the selected vault data
    $('.delete-btn').on('click', function() {
        $('#vaultNameToDelete').text($(this).data('vault-name'));
        $('#deleteVaultId').val($(this).data('vault-id'));
    });

    // Search function for vaults
    function searchTable() {
        let input = document.getElementById('searchInput').value.toLowerCase();
        let table = document.getElementById('vaultTable');
        let rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            let cells = rows[i].getElementsByTagName('td');
            let found = false;

            for (let j = 0; j < cells.length; j++) {
                if (cells[j].innerText.toLowerCase().includes(input)) {
                    found = true;
                    break;
                }
            }

            rows[i].style.display = found ? '' : 'none';
        }
    }
</script>

</body>
</html>
