<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Sebastian Obando">
    <meta name="copyright" content="Sebastian Obando">
    <title><?php echo $titulo ?? 'DevolutionSync'; ?></title>
    <link rel="icon" type="image/png" href="assets/img/icono.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/panel.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<!-- ══ OVERLAY (oscurece el fondo al abrir el sidebar en móvil) ══ -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="cerrarSidebar()"></div>

<!-- ══ BOTÓN HAMBURGUESA (visible solo en móvil/tablet) ══ -->
<button class="hamburger-btn" id="hamburgerBtn" onclick="toggleSidebar()" aria-label="Abrir menú">
    <i class="fas fa-bars" id="hamburgerIcon"></i>
</button>

<!-- ══ SIDEBAR ══ -->
<div class="sidebar" id="sidebar">

    <!-- Botón cerrar dentro del sidebar (móvil) -->
    <button class="sidebar-close-btn" onclick="cerrarSidebar()" aria-label="Cerrar menú">
        <i class="fas fa-times"></i>
    </button>

    <div class="logo-sidebar">
        <img src="assets/img/logo.png" alt="DevolutionSync Logo">
    </div>

    <div class="sidebar-menu">

        <?php if (isset($_SESSION['grado']) && $_SESSION['grado'] == 1): ?>
        <a href="index.php?url=home/index" class="menu-button <?php echo (strpos($_SERVER['QUERY_STRING'] ?? '', 'home/index') !== false) ? 'active' : ''; ?>" onclick="cerrarSidebar()">
            <div class="menu-icon"><i class="fas fa-chart-line"></i></div>
            <div class="menu-text"><strong>DASHBOARD</strong><br>Principal</div>
        </a>
        <?php endif; ?>

        <?php if (isset($_SESSION['grado']) && ($_SESSION['grado'] == 1 || $_SESSION['grado'] == 2)): ?>
        <a href="index.php?url=panel/auxiliar" class="menu-button <?php echo (strpos($_SERVER['QUERY_STRING'] ?? '', 'panel/auxiliar') !== false) ? 'active' : ''; ?>" onclick="cerrarSidebar()">
            <div class="menu-icon"><i class="fas fa-boxes"></i></div>
            <div class="menu-text"><strong>GESTIÓN</strong><br>DEVOLUCIONES</div>
        </a>
        <?php endif; ?>

        <?php if (isset($_SESSION['grado']) && $_SESSION['grado'] == 1): ?>
        <a href="index.php?url=admin/index" class="menu-button <?php echo (strpos($_SERVER['QUERY_STRING'] ?? '', 'admin/index') !== false) ? 'active' : ''; ?>" onclick="cerrarSidebar()">
            <div class="menu-icon"><i class="fas fa-tasks"></i></div>
            <div class="menu-text"><strong>PANEL</strong><br>ADMINISTRADOR</div>
        </a>
        <?php endif; ?>

        <a href="index.php?url=consulta/index" class="menu-button <?php echo (strpos($_SERVER['QUERY_STRING'] ?? '', 'consulta/') !== false) ? 'active' : ''; ?>" onclick="cerrarSidebar()">
            <div class="menu-icon"><i class="fas fa-history"></i></div>
            <div class="menu-text"><strong>CONSULTAR</strong><br>HISTORIAL</div>
        </a>

        <?php if (isset($_SESSION['grado']) && $_SESSION['grado'] == 1): ?>
        <a href="index.php?url=usuario/crear" class="menu-button <?php echo (strpos($_SERVER['QUERY_STRING'] ?? '', 'usuario/crear') !== false) ? 'active' : ''; ?>" onclick="cerrarSidebar()">
            <div class="menu-icon"><i class="fas fa-user-plus"></i></div>
            <div class="menu-text"><strong>CREAR</strong><br>USUARIO</div>
        </a>
        <?php endif; ?>

        <div class="user-info">
            <div class="user-label">
                <?php 
                if (isset($_SESSION['grado'])) {
                    switch ($_SESSION['grado']) {
                        case 1: echo '<i class="fas fa-crown"></i> ADMINISTRADOR'; break;
                        case 2: echo '<i class="fas fa-tools"></i> AUXILIAR'; break;
                        case 3: echo '<i class="fas fa-eye"></i> CONSULTA'; break;
                        default: echo 'USUARIO';
                    }
                }
                ?>
            </div>
            <div class="user-name"><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></div>
        </div>

        <button class="logout-btn" onclick="logout()">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </button>

    </div>
</div>

<!-- ══ CONTENIDO PRINCIPAL ══ -->
<div class="main-content" id="mainContent">

<script>
function logout() {
    if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
        window.location.href = 'index.php?url=auth/logout';
    }
}

function toggleSidebar() {
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const icon     = document.getElementById('hamburgerIcon');
    const isOpen   = sidebar.classList.contains('sidebar-open');

    if (isOpen) {
        cerrarSidebar();
    } else {
        sidebar.classList.add('sidebar-open');
        overlay.classList.add('active');
        icon.className = 'fas fa-times';
        document.body.style.overflow = 'hidden';
    }
}

function cerrarSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const icon    = document.getElementById('hamburgerIcon');

    sidebar.classList.remove('sidebar-open');
    overlay.classList.remove('active');
    icon.className = 'fas fa-bars';
    document.body.style.overflow = '';
}

// Cerrar sidebar al redimensionar a desktop
window.addEventListener('resize', function() {
    if (window.innerWidth > 768) {
        cerrarSidebar();
    }
});
</script>

<style>
/* ── Botón hamburguesa ── */
.hamburger-btn {
    display: none;
    position: fixed;
    top: 15px;
    left: 15px;
    z-index: 1100;
    background: linear-gradient(135deg, #ff8c00, #ff6b00);
    color: white;
    border: none;
    width: 44px;
    height: 44px;
    border-radius: 10px;
    font-size: 18px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(255,140,0,0.4);
    transition: all 0.3s ease;
    align-items: center;
    justify-content: center;
}

.hamburger-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(255,140,0,0.5);
}

/* ── Overlay ── */
.sidebar-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 999;
    backdrop-filter: blur(2px);
}

.sidebar-overlay.active { display: block; }

/* ── Botón cerrar dentro del sidebar ── */
.sidebar-close-btn {
    display: none;
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(255,255,255,0.2);
    color: white;
    border: none;
    width: 32px; height: 32px;
    border-radius: 50%;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
    align-items: center;
    justify-content: center;
}

.sidebar-close-btn:hover { background: rgba(255,255,255,0.35); }

/* ── Botón activo en sidebar ── */
.menu-button.active {
    background: rgba(255,255,255,1) !important;
    border-left: 4px solid #ff6b00;
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.menu-button.active .menu-text strong { color: #ff6b00; }

/* ══ RESPONSIVE: tablet y móvil (≤ 768px) ══ */
@media (max-width: 768px) {

    /* Mostrar botón hamburguesa */
    .hamburger-btn {
        display: flex;
    }

    /* Mostrar botón cerrar dentro del sidebar */
    .sidebar-close-btn {
        display: flex;
    }

    /* El sidebar se oculta fuera de pantalla por defecto */
    .sidebar {
        transform: translateX(-100%);
        transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1050;
        height: 100vh;
        overflow-y: auto;
    }

    /* Cuando está abierto */
    .sidebar.sidebar-open {
        transform: translateX(0);
    }

    /* El contenido ocupa todo el ancho */
    .main-content {
        margin-left: 0 !important;
        width: 100% !important;
        padding: 20px 15px;
        padding-top: 70px; /* espacio para el botón hamburguesa */
    }

    /* Ajustes de grids para tablet */
    .indicators-grid,
    .dashboard-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .stats-grid,
    .summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    /* Crear usuario: stack vertical */
    div[style*="grid-template-columns: 1fr 1.6fr"] {
        display: flex !important;
        flex-direction: column !important;
    }

    /* Admin header */
    .admin-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .admin-stats {
        width: 100%;
        justify-content: space-between;
    }

    .card-header {
        flex-direction: column;
        align-items: flex-start;
    }
}

/* ══ RESPONSIVE: solo móvil (≤ 480px) ══ */
@media (max-width: 480px) {

    .main-content {
        padding: 15px 10px;
        padding-top: 70px;
    }

    .indicators-grid,
    .dashboard-grid {
        grid-template-columns: 1fr;
    }

    .stats-grid,
    .summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .admin-header-content h1 {
        font-size: 20px;
    }

    .dashboard-title {
        font-size: 20px;
    }

    .panel-header h1 {
        font-size: 20px;
    }

    .form-grid {
        grid-template-columns: 1fr !important;
    }

    .form-actions {
        flex-direction: column;
    }

    .form-actions .btn {
        width: 100%;
        justify-content: center;
    }

    .modal-dialog {
        width: 98%;
        margin: 5px;
    }

    .modal-body {
        padding: 15px;
    }

    .stat-badge {
        padding: 10px 15px;
    }

    .stat-badge .stat-number {
        font-size: 24px;
    }
}
</style>
