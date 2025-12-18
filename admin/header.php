<?php  
if (!isset($_SESSION)) session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
require_login();
if (!is_admin()) {
    header('Location: login.php');
    exit;
}
$user = current_user();
?>
<nav class="navbar navbar-light bg-white shadow-sm p-3">
    <div class="container-fluid">

        <button class="btn btn-outline-primary" id="toggleSidebar">
            <i class="bi bi-list"></i>
        </button>

        <span class="ms-auto fw-semibold text-primary">
            Hallo, admin <?= htmlspecialchars($user['nama']); ?>!
        </span>

        <a href="../logout.php" class="btn btn-outline-danger btn-sm ms-3">
            Logout
        </a>

    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let sidebar = document.getElementById('sidebar');
    let toggle = document.getElementById('toggleSidebar');
    let body = document.body;

    toggle.onclick = () => {
        sidebar.classList.toggle('collapsed');
        body.classList.toggle('sidebar-collapsed');
    };
});
</script>
