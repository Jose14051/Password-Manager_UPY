<?php
// Replace with your database connection details
$hostname = 'mysql-database';
$username = 'user';
$password = 'supersecretpw';
$database = 'password_manager';

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addUsername']) && isset($_POST['addWebsite']) && isset($_POST['addPassword']) && isset($_POST['vaultId'])) {
    $addUsername = $_POST['addUsername'];
    $addWebsite = $_POST['addWebsite'];
    $addPassword = $_POST['addPassword'];
    $addNotes = $_POST['addNotes'];
    $vaultId = $_POST['vaultId'];

    $queryAddPassword = "INSERT INTO vault_passwords (vault_id, username, website, password, notes) 
                         VALUES ($vaultId, '$addUsername', '$addWebsite', '$addPassword', '$addNotes')";
    $resultAddPassword = $conn->query($queryAddPassword);

    if (!$resultAddPassword) {
        die("Error adding password: " . $conn->error);
    }

    // Redirect to the current page after adding the password
    header("Location: {$_SERVER['PHP_SELF']}?vault_id=$vaultId");
    exit();
}

// Edit Password
if ($_SERVER['REQUEST_METHOD'] === 'POST'  && isset($_POST['editPasswordId']) && isset($_POST['editUsername']) && isset($_POST['editPassword']) && isset($_POST['editWebsite']) && isset($_POST['vaultId'])) {
    $editUsername = $_POST['editUsername'];
    $editWebsite = $_POST['editWebsite'];
    $editPassword = $_POST['editPassword'];
    $editNotes = $_POST['editNotes'];
    $editPasswordId = $_POST['editPasswordId'];
    $vaultId = $_POST['vaultId'];

    $queryEditPassword = "UPDATE vault_passwords 
                          SET username = '$editUsername', website = '$editWebsite', 
                          password = '$editPassword', notes = '$editNotes' 
                          WHERE password_id = $editPasswordId";
    $resultEditPassword = $conn->query($queryEditPassword);

    if (!$resultEditPassword) {
        die("Error updating password: " . $conn->error);
    }

    // Redirect to the current page after updating the password
    header("Location: {$_SERVER['PHP_SELF']}?vault_id=$vaultId");
    exit();
}

// Delete Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deletePasswordId']) && isset($_POST['vaultId'])) {
    $deletePasswordId = $_POST['deletePasswordId'];
    $vaultId = $_POST['vaultId'];

    $queryDeletePassword = "DELETE FROM vault_passwords WHERE password_id = $deletePasswordId";
    $resultDeletePassword = $conn->query($queryDeletePassword);

    if (!$resultDeletePassword) {
        die("Error deleting password: " . $conn->error);
    }

    // Redirect to the current page after deleting the password
    header("Location: {$_SERVER['PHP_SELF']}?vault_id=$vaultId");
    exit();
}

// Retrieve vault information
$vaultId = isset($_GET['vault_id']) ? $_GET['vault_id'] : 0;

$query = "SELECT vault_name FROM vaults WHERE vault_id = $vaultId";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$row = $result->fetch_assoc();
$vaultName = $row['vault_name'];

// Retrieve passwords for the vault
$queryPasswords = "SELECT * FROM vault_passwords WHERE vault_id = $vaultId";
$resultPasswords = $conn->query($queryPasswords);

if (!$resultPasswords) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $vaultName; ?> Vault</title>
    <!-- Add Bootstrap CSS link here -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #343a40; /* Dark background for the whole page */
            color: #ffffff; /* White text for readability */
        }
        .navbar {
            background-color: #212529; /* Dark background for navbar */
        }
        .navbar a {
            color: #ffffff; /* White text for navbar links */
        }
        .navbar a:hover {
            color: #6f42c1; /* Purple color on hover */
        }
        .container {
            background-color: rgba(0, 0, 0, 0.8); /* Dark semi-transparent background for content */
            border-radius: 10px; /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5); /* Shadow for emphasis */
            padding: 20px; /* Padding around content */
        }
        .container h2 {
            color: #ffffff; /* White text for headers */
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5); /* Shadow for header text */
        }
        .footer {
            background-color: #212529; /* Dark background for footer */
            color: #ffffff; /* White text for footer */
            padding: 10px 0; /* Padding for footer */
            text-align: center; /* Centered text */
        }
        .btn-primary, .btn-warning, .btn-danger {
            margin: 0 5px; /* Spacing around buttons */
        }
    </style>
</head>
<body>

<?php include '../components/nav-bar.php'; ?>

<div class="container mt-4">
    <h2><?php echo $vaultName; ?> Vault Passwords</h2>
    <button type="button" class="btn btn-primary mb-2" data-toggle="modal" data-target="#addPasswordModal">
        Add Password
    </button>
    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for passwords..." class="form-control mb-3">
    <table class="table table-bordered" id="passwordTable">
        <thead>
            <tr>
                <th>Username</th>
                <th>Website</th>
                <th>Password</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($rowPassword = $resultPasswords->fetch_assoc()) : ?>
                <tr data-password-id="<?php echo $rowPassword['password_id']; ?>">                
                    <td><?php echo $rowPassword['username']; ?></td>
                    <td><?php echo $rowPassword['website']; ?></td>
                    <td>
                        <input type="password" class="password-field" value="<?php echo $rowPassword['password']; ?>" disabled>
                    </td>
                    <td><?php echo $rowPassword['notes']; ?></td>
                    <td>
                        <button class="btn btn-primary btn-sm show-password-btn">Show Password</button>
                        <button class="btn btn-warning btn-sm edit-password-btn" data-toggle="modal" data-target="#editPasswordModal" data-password-notes="<?php echo $rowPassword['notes']; ?>" data-password-password="<?php echo $rowPassword['password']; ?>" data-password-website="<?php echo $rowPassword['website']; ?>" data-password-username="<?php echo $rowPassword['username']; ?>" data-password-id="<?php echo $rowPassword['password_id']; ?>">Edit</button>
                        <button class="btn btn-danger btn-sm delete-password-btn" data-toggle="modal" data-target="#deletePasswordModal" data-password-id="<?php echo $rowPassword['password_id']; ?>">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var showPasswordButtons = document.querySelectorAll('.show-password-btn');
        showPasswordButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var passwordField = button.closest('tr').querySelector('.password-field');
                passwordField.type = (passwordField.type === 'password') ? 'text' : 'password';
                if (button.textContent == 'Show Password') {
                    button.textContent = 'Hide Password';
                } else {
                    button.textContent = 'Show Password';
                }
            });
        });
    });

    function searchTable() {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("passwordTable");
        tr = table.getElementsByTagName("tr");

        for (i = 0; i < tr.length; i++) {
            if (i === 0) {
                continue;
            }

            var shouldDisplay = false;

            for (j = 0; j < tr[i].getElementsByTagName("td").length; j++) {
                td = tr[i].getElementsByTagName("td")[j];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        shouldDisplay = true;
                        break;
                    }
                }
            }

            tr[i].style.display = shouldDisplay ? "" : "none";
        }
    }
</script>

<!-- Add Password Modal -->
<div class="modal fade" id="addPasswordModal" tabindex="-1" role="dialog" aria-labelledby="addPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPasswordModalLabel">Add Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>?vault_id=<?php echo $vaultId; ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="addUsername">Username</label>
                        <input type="text" class="form-control" id="addUsername" name="addUsername" required>
                    </div>
                    <div class="form-group">
                        <label for="addWebsite">Website</label>
                        <input type="text" class="form-control" id="addWebsite" name="addWebsite" required>
                    </div>
                    <div class="form-group">
                        <label for="addPassword">Password</label>
                        <input type="password" class="form-control" id="addPassword" name="addPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="addNotes">Notes</label>
                        <textarea class="form-control" id="addNotes" name="addNotes"></textarea>
                    </div>
                    <input type="hidden" name="vaultId" value="<?php echo $vaultId; ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Password Modal -->
<div class="modal fade" id="editPasswordModal" tabindex="-1" role="dialog" aria-labelledby="editPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPasswordModalLabel">Edit Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>?vault_id=<?php echo $vaultId; ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editUsername">Username</label>
                        <input type="text" class="form-control" id="editUsername" name="editUsername" required>
                    </div>
                    <div class="form-group">
                        <label for="editWebsite">Website</label>
                        <input type="text" class="form-control" id="editWebsite" name="editWebsite" required>
                    </div>
                    <div class="form-group">
                        <label for="editPassword">Password</label>
                        <input type="password" class="form-control" id="editPassword" name="editPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="editNotes">Notes</label>
                        <textarea class="form-control" id="editNotes" name="editNotes"></textarea>
                    </div>
                    <input type="hidden" name="editPasswordId" id="editPasswordId">
                    <input type="hidden" name="vaultId" value="<?php echo $vaultId; ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Password Modal -->
<div class="modal fade" id="deletePasswordModal" tabindex="-1" role="dialog" aria-labelledby="deletePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePasswordModalLabel">Delete Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>?vault_id=<?php echo $vaultId; ?>">
                <div class="modal-body">
                    <p>Are you sure you want to delete this password?</p>
                    <input type="hidden" name="deletePasswordId" id="deletePasswordId">
                    <input type="hidden" name="vaultId" value="<?php echo $vaultId; ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add jQuery and Bootstrap JS links here -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<script>
    $('#editPasswordModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var username = button.data('password-username');
        var website = button.data('password-website');
        var password = button.data('password-password');
        var notes = button.data('password-notes');
        var passwordId = button.data('password-id');
        
        var modal = $(this);
        modal.find('#editUsername').val(username);
        modal.find('#editWebsite').val(website);
        modal.find('#editPassword').val(password);
        modal.find('#editNotes').val(notes);
        modal.find('#editPasswordId').val(passwordId);
    });

    $('#deletePasswordModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var passwordId = button.data('password-id');
        
        var modal = $(this);
        modal.find('#deletePasswordId').val(passwordId);
    });
</script>

<footer class="footer">
    <p>&copy; 2024 Password Manager. All rights reserved.</p>
</footer>

</body>
</html>

<?php
$conn->close();
?>
