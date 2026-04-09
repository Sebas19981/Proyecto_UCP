<?php 
// Generar token CSRF si no existe
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
 $csrf_token = $_SESSION['csrf_token'];

 $titulo = "Panel Administrador - DevolutionSync";
include 'Views/layouts/header.php'; 
?>

<div class="admin-container">
    <!-- Header del Panel -->
    <div class="admin-header">
        <div class="admin-header-content">
            <h1><i class="fa-solid fa-shield-halved"></i> Panel de Administración</h1>
            <p class="subtitle">Revisa y gestiona las devoluciones pendientes</p>
        </div>
        <div class="admin-stats">
            <div class="stat-badge stat-pending">
                <span class="stat-number"><?php echo count($pendientes); ?></span>
                <span class="stat-label">Pendientes</span>
            </div>
            <div class="stat-badge stat-recent">
                <span class="stat-number"><?php echo count($historial); ?></span>
                <span class="stat-label">Recientes</span>
            </div>
            <button class="btn btn-product-crud" onclick="abrirModalProductos()">
                <i class="fa-solid fa-boxes-stacked"></i> Gestionar Productos
            </button>
        </div>
    </div>

    <!-- Mensajes de Alerta -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-<?php echo ($_GET['msg'] == 'success') ? 'success' : 'error'; ?>">
            <i class="fa-solid fa-<?php echo ($_GET['msg'] == 'success') ? 'circle-check' : 'circle-exclamation'; ?>"></i>
            <strong><?php echo ($_GET['msg'] == 'success') ? 'Éxito:' : 'Error:'; ?></strong>
            <?php echo ($_GET['msg'] == 'success') ? 'Revisión procesada correctamente' : 'Error al procesar la revisión'; ?>
        </div>
    <?php endif; ?>

    <!-- Sección: Pendientes de Revisión -->
    <div class="admin-card pending-card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-clock"></i>
                <h2>Pendientes de Revisión</h2>
                <span class="badge badge-warning"><?php echo count($pendientes); ?></span>
            </div>
            <?php if (!empty($pendientes)): ?>
                <div class="card-actions">
                    <button class="btn-filter" onclick="filtrarTabla('pendientes')">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <div class="card-body">
            <?php if (empty($pendientes)): ?>
                <div class="empty-state">
                    <i class="fa-solid fa-circle-check"></i>
                    <h3>¡Todo al día!</h3>
                    <p>No hay devoluciones pendientes por revisar</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="admin-table" id="tablaPendientes">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Producto</th>
                                <th>Motivo</th>
                                <th>Cantidad</th>
                                <th>Fecha</th>
                                <th>Creado por</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendientes as $p): ?>
                            <tr data-id="<?php echo $p['id']; ?>">
                                <td>
                                    <strong class="id-badge">#<?php echo $p['id']; ?></strong>
                                </td>
                                <td>
                                    <div class="cliente-info">
                                        <strong><?php echo htmlspecialchars($p['nombre_cliente']); ?></strong>
                                        <small>NIT: <?php echo htmlspecialchars($p['nit'] ?? 'N/A'); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <div class="producto-info">
                                        <strong><?php echo htmlspecialchars($p['item_producto'] ?? $p['codigo_producto'] ?? 'N/A'); ?></strong>
                                        <small><?php echo htmlspecialchars($p['descripcion_producto'] ?? ''); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="motivo-badge motivo-<?php echo strtolower($p['motivo']); ?>">
                                        <?php 
                                        $iconos = [
                                            'devolucion' => '<i class="fa-solid fa-rotate"></i>',
                                            'faltante' => '<i class="fa-solid fa-triangle-exclamation"></i>',
                                            'sobrante' => '<i class="fa-solid fa-circle-plus"></i>'
                                        ];
                                        echo ($iconos[strtolower($p['motivo'])] ?? '<i class="fa-solid fa-box"></i>') . ' ' . ucfirst($p['motivo']); 
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="cantidad-info">
                                        <span><?php echo $p['cantidad_und'] ?? 0; ?> UND</span>
                                        <small><?php echo number_format($p['cantidad_kg'] ?? 0, 2); ?> KG</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="fecha-info">
                                        <strong><?php echo date('d/m/Y', strtotime($p['fecha_creacion'])); ?></strong>
                                        <small><?php echo date('H:i', strtotime($p['fecha_creacion'])); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="usuario-badge">
                                        <i class="fa-solid fa-user"></i>
                                        <?php echo htmlspecialchars($p['usuario_creador']); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-review" onclick="abrirRevision(<?php echo $p['id']; ?>)">
                                        <i class="fa-solid fa-magnifying-glass"></i> Revisar
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sección: Historial Reciente -->
    <div class="admin-card history-card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <h2>Historial Reciente</h2>
                <span class="badge badge-info"><?php echo count($historial); ?></span>
            </div>
            <div class="card-actions">
                <button class="btn-export" onclick="exportarHistorial()">
                    <i class="fa-solid fa-download"></i> Exportar
                </button>
            </div>
        </div>

        <div class="card-body">
            <?php if (empty($historial)): ?>
                <div class="empty-state">
                    <i class="fa-solid fa-inbox"></i>
                    <h3>No hay historial</h3>
                    <p>Aún no hay devoluciones revisadas</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="admin-table" id="tablaHistorial">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Estado</th>
                                <th>Cliente</th>
                                <th>Producto</th>
                                <th>Fecha Revisión</th>
                                <th>Revisado por</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historial as $h): ?>
                            <tr>
                                <td><strong class="id-badge">#<?php echo $h['id']; ?></strong></td>
                                <td>
                                    <span class="estado-badge estado-<?php echo strtolower($h['estado']); ?>">
                                        <?php 
                                        $iconosEstado = [
                                            'aprobado' => '<i class="fa-solid fa-circle-check"></i>',
                                            'rechazado' => '<i class="fa-solid fa-circle-xmark"></i>',
                                            'pendiente' => '<i class="fa-solid fa-hourglass-half"></i>'
                                        ];
                                        echo ($iconosEstado[strtolower($h['estado'])] ?? '<i class="fa-solid fa-clipboard-list"></i>') . ' ' . ucfirst($h['estado']); 
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($h['nombre_cliente']); ?></strong>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($h['item_producto'] ?? $h['codigo_producto'] ?? 'N/A'); ?>
                                </td>
                                <td>
                                    <?php 
                                    if (!empty($h['fecha_revision'])) {
                                        echo date('d/m/Y H:i', strtotime($h['fecha_revision']));
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class="usuario-badge">
                                        <?php echo $h['usuario_revisor'] ? htmlspecialchars($h['usuario_revisor']) : '-'; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-view" onclick="verSoloDetalles(<?php echo $h['id']; ?>)">
                                        <i class="fa-solid fa-eye"></i> Ver
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL: GESTIONAR PRODUCTOS (CRUD) -->
<div id="modalProductos" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fa-solid fa-boxes-stacked"></i> Gestionar Productos</h2>
                <button class="modal-close" onclick="cerrarModalProductos()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="productos-toolbar">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="buscarProducto" placeholder="Buscar por código o descripción..." oninput="buscarProductos(this.value)">
                    </div>
                    <button class="btn btn-success" onclick="mostrarFormProducto('crear')">
                        <i class="fa-solid fa-plus"></i> Nuevo Producto
                    </button>
                </div>
                <div class="table-responsive" id="contenedorTablaProductos">
                    <table class="admin-table" id="tablaProductos">
                        <thead>
                            <tr>
                                <th>Código (Item)</th>
                                <th>Descripción</th>
                                <th>Mínimo</th>
                                <th>Máximo</th>
                                <th>Peso Prom. (KG)</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyProductos">
                            <?php foreach ($productos as $prod): ?>
                            <tr>
                                <td><strong class="id-badge"><?php echo intval($prod['item']); ?></strong></td>
                                <td><?php echo htmlspecialchars($prod['descripcion']); ?></td>
                                <td><?php echo number_format($prod['minimo'] ?? 0, 2); ?></td>
                                <td><?php echo number_format($prod['maximo'] ?? 0, 2); ?></td>
                                <td><?php echo number_format($prod['kg'] ?? 0, 2); ?></td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm" onclick="mostrarFormProducto('editar', <?php echo intval($prod['item']); ?>, '<?php echo htmlspecialchars(addslashes($prod['descripcion'])); ?>', <?php echo $prod['minimo'] ?? 0; ?>, <?php echo $prod['maximo'] ?? 0; ?>, <?php echo $prod['kg'] ?? 0; ?>)">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="eliminarProducto(<?php echo intval($prod['item']); ?>)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div id="formProductoContainer" class="form-producto-container" style="display:none;">
                    <div class="form-producto-header">
                        <h3 id="formProductoTitulo"><i class="fa-solid fa-plus"></i> Nuevo Producto</h3>
                        <button class="btn btn-secondary btn-sm" onclick="ocultarFormProducto()">
                            <i class="fa-solid fa-xmark"></i> Cancelar
                        </button>
                    </div>
                    <form id="formProducto" onsubmit="guardarProducto(event)">
                        <input type="hidden" id="prodAccion" value="crear">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="prodItem"><i class="fa-solid fa-barcode"></i> Código (Item) *</label>
                                <input type="number" id="prodItem" name="item" class="form-control" placeholder="Ej: 1001" required>
                            </div>
                            <div class="form-group">
                                <label for="prodDescripcion"><i class="fa-solid fa-tag"></i> Descripción *</label>
                                <input type="text" id="prodDescripcion" name="descripcion" class="form-control" placeholder="Nombre del producto" required maxlength="50">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="prodMinimo"><i class="fa-solid fa-arrow-down"></i> Mínimo</label>
                                <input type="number" id="prodMinimo" name="minimo" class="form-control" placeholder="0.00" step="0.01" value="0">
                            </div>
                            <div class="form-group">
                                <label for="prodMaximo"><i class="fa-solid fa-arrow-up"></i> Máximo</label>
                                <input type="number" id="prodMaximo" name="maximo" class="form-control" placeholder="0.00" step="0.01" value="0">
                            </div>
                            <div class="form-group">
                                <label for="prodPeso"><i class="fa-solid fa-weight-hanging"></i> Peso Prom. (KG)</label>
                                <input type="number" id="prodPeso" name="pesoProm" class="form-control" placeholder="0.00" step="0.01" value="0">
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-success">
                                <i class="fa-solid fa-floppy-disk"></i> <span id="btnGuardarTexto">Guardar Producto</span>
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="ocultarFormProducto()">
                                <i class="fa-solid fa-ban"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: REVISAR DEVOLUCIÓN -->
<div id="revisionModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fa-solid fa-magnifying-glass-plus"></i> Revisar Devolución</h2>
                <button class="modal-close" onclick="cerrarModal('revisionModal')">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="infoDevolucion" class="loading-state">
                    <div class="loader"></div>
                    <p>Cargando información...</p>
                </div>
                <hr class="modal-divider">
                <div class="revision-form">
                    <h3><i class="fa-solid fa-pen-to-square"></i> Formulario de Revisión</h3>
                    <form action="index.php?url=admin/revisar" method="POST" id="formRevision">
                        <!-- TOKEN CSRF -->
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="hidden" name="id_devolucion" id="idDevolucionInput">
                        <div class="form-group">
                            <label for="accion"><i class="fa-solid fa-check-double"></i> Decisión *</label>
                            <select name="accion" id="accion" class="form-control" required>
                                <option value="">-- Seleccione una acción --</option>
                                <option value="aprobado">APROBAR Devolución</option>
                                <option value="rechazado">RECHAZAR Devolución</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="codigo_admin"><i class="fa-solid fa-key"></i> Código de Autorización *</label>
                            <input type="text" id="codigo_admin" name="codigo_admin" class="form-control" placeholder="Ingrese el código de autorización" required>
                        </div>
                        <div class="form-group">
                            <label for="observacion_admin"><i class="fa-solid fa-comment-dots"></i> Observaciones del Administrador *</label>
                            <textarea id="observacion_admin" name="observacion_admin" class="form-control" rows="4" placeholder="Ingrese sus observaciones sobre esta devolución" required></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-success">
                                <i class="fa-solid fa-paper-plane"></i> Enviar Revisión
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="cerrarModal('revisionModal')">
                                <i class="fa-solid fa-xmark"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: VER DETALLES -->
<div id="detallesModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fa-solid fa-circle-info"></i> Detalles de la Devolución</h2>
                <button class="modal-close" onclick="cerrarModal('detallesModal')">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="detallesContenido" class="loading-state">
                    <div class="loader"></div>
                    <p>Cargando detalles...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="cerrarModal('detallesModal')">
                    <i class="fa-solid fa-xmark"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.modal-lg { max-width: 900px; width: 95%; }
.btn-product-crud {
    background: linear-gradient(135deg, #1F4E79, #2E86C1);
    color: #fff; border: none; padding: 12px 24px; border-radius: 8px;
    font-size: 14px; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; gap: 8px;
    transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(31,78,121,0.3);
}
.btn-product-crud:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(31,78,121,0.4); }
.productos-toolbar { display: flex; justify-content: space-between; align-items: center; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
.search-box { flex: 1; min-width: 250px; position: relative; }
.search-box i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #999; }
.search-box input { width: 100%; padding: 10px 14px 10px 42px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; transition: border-color 0.3s; box-sizing: border-box; }
.search-box input:focus { outline: none; border-color: #2E86C1; box-shadow: 0 0 0 3px rgba(46,134,193,0.15); }
#tablaProductos { max-height: 400px; overflow-y: auto; display: block; }
#tablaProductos thead { position: sticky; top: 0; z-index: 2; }
#tablaProductos tbody tr:hover { background-color: #f0f7ff; }
.form-producto-container {
    margin-top: 20px; padding: 20px; background: #f8f9fa;
    border-radius: 10px; border: 2px solid #e0e0e0; animation: slideDown 0.3s ease;
}
@keyframes slideDown { from { opacity: 0; transform: translateY(-15px); } to { opacity: 1; transform: translateY(0); } }
.form-producto-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
.form-producto-header h3 { margin: 0; color: #1F4E79; }
.form-row { display: flex; gap: 15px; margin-bottom: 12px; flex-wrap: wrap; }
.form-row .form-group { flex: 1; min-width: 150px; }
.form-producto-container .form-group { margin-bottom: 12px; }
.form-producto-container .form-group label { display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px; }
.form-producto-container .form-control { width: 100%; padding: 9px 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; transition: border-color 0.3s; box-sizing: border-box; }
.form-producto-container .form-control:focus { outline: none; border-color: #2E86C1; box-shadow: 0 0 0 3px rgba(46,134,193,0.15); }
.form-producto-container .form-actions { display: flex; gap: 10px; margin-top: 15px; justify-content: flex-end; }
.btn-sm { padding: 6px 12px !important; font-size: 12px !important; }
.btn-success { background: #27ae60; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
.btn-success:hover { background: #219a52; }
.btn-warning { background: #f39c12; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
.btn-warning:hover { background: #d68910; }
.btn-danger { background: #e74c3c; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
.btn-danger:hover { background: #c0392b; }
.btn-secondary { background: #95a5a6; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
.btn-secondary:hover { background: #7f8c8d; }
.admin-stats { display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
@media (max-width: 768px) {
    .form-row { flex-direction: column; gap: 0; }
    .admin-stats { flex-direction: column; align-items: stretch; }
    .btn-product-crud { justify-content: center; }
    .modal-lg { width: 98%; }
    .productos-toolbar { flex-direction: column; }
    .search-box { min-width: 100%; }
}
</style>

<script>
var CSRF_TOKEN = '<?php echo htmlspecialchars($csrf_token); ?>';

let timerBusqueda = null;

function abrirModalProductos() {
    document.getElementById('modalProductos').style.display = 'flex';
    ocultarFormProducto();
    document.getElementById('buscarProducto').value = '';
    document.getElementById('buscarProducto').focus();
}

function cerrarModalProductos() {
    document.getElementById('modalProductos').style.display = 'none';
    ocultarFormProducto();
}

function buscarProductos(texto) {
    clearTimeout(timerBusqueda);
    timerBusqueda = setTimeout(function() {
        if (texto.length < 1) { location.reload(); return; }
        fetch('index.php?url=admin/buscarProductos&q=' + encodeURIComponent(texto))
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) renderProductos(data.productos);
            })
            .catch(function(error) { console.error('Error:', error); });
    }, 300);
}

function renderProductos(productos) {
    var tbody = document.getElementById('tbodyProductos');
    if (!productos || productos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#999;"><i class="fa-solid fa-box-open" style="font-size:24px;display:block;margin-bottom:8px;"></i>No se encontraron productos</td></tr>';
        return;
    }
    var html = '';
    productos.forEach(function(p) {
        html += '<tr><td><strong class="id-badge">' + parseInt(p.item) + '</strong></td><td>' + escapeHtml(p.descripcion) + '</td><td>' + parseFloat(p.minimo||0).toFixed(2) + '</td><td>' + parseFloat(p.maximo||0).toFixed(2) + '</td><td>' + parseFloat(p.kg||0).toFixed(2) + '</td><td class="text-center"><button class="btn btn-warning btn-sm" onclick="mostrarFormProducto(\'editar\',' + parseInt(p.item) + ',\'' + escapeHtml(p.descripcion).replace(/'/g, "\\'") + '\',' + parseFloat(p.minimo||0) + ',' + parseFloat(p.maximo||0) + ',' + parseFloat(p.kg||0) + ')"><i class="fa-solid fa-pen-to-square"></i></button> <button class="btn btn-danger btn-sm" onclick="eliminarProducto(' + parseInt(p.item) + ')"><i class="fa-solid fa-trash"></i></button></td></tr>';
    });
    tbody.innerHTML = html;
}

function mostrarFormProducto(accion, item, descripcion, minimo, maximo, kg) {
    var container = document.getElementById('formProductoContainer');
    var titulo = document.getElementById('formProductoTitulo');
    var btnTexto = document.getElementById('btnGuardarTexto');
    var campoItem = document.getElementById('prodItem');
    document.getElementById('prodAccion').value = accion;
    document.getElementById('formProducto').reset();
    document.querySelector('#formProducto input[name="csrf_token"]').value = CSRF_TOKEN;
    if (accion === 'crear') {
        titulo.innerHTML = '<i class="fa-solid fa-plus"></i> Nuevo Producto';
        btnTexto.textContent = 'Guardar Producto';
        campoItem.disabled = false;
        campoItem.value = '';
        document.getElementById('prodMinimo').value = 0;
        document.getElementById('prodMaximo').value = 0;
        document.getElementById('prodPeso').value = 0;
    } else {
        titulo.innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Editar Producto #' + item;
        btnTexto.textContent = 'Actualizar Producto';
        campoItem.disabled = true;
        campoItem.value = item;
        document.getElementById('prodDescripcion').value = descripcion;
        document.getElementById('prodMinimo').value = minimo;
        document.getElementById('prodMaximo').value = maximo;
        document.getElementById('prodPeso').value = kg;
    }
    container.style.display = 'block';
    container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function ocultarFormProducto() {
    document.getElementById('formProductoContainer').style.display = 'none';
    document.getElementById('formProducto').reset();
    document.querySelector('#formProducto input[name="csrf_token"]').value = CSRF_TOKEN;
    document.getElementById('prodItem').disabled = false;
}

function guardarProducto(event) {
    event.preventDefault();
    var accion = document.getElementById('prodAccion').value;
    var item = parseInt(document.getElementById('prodItem').value);
    var descripcion = document.getElementById('prodDescripcion').value.trim();
    var minimo = parseFloat(document.getElementById('prodMinimo').value);
    var maximo = parseFloat(document.getElementById('prodMaximo').value);
    var pesoProm = parseFloat(document.getElementById('prodPeso').value);
    if (!item || item <= 0) { alert('El código del producto debe ser un número mayor a 0'); return; }
    if (!descripcion) { alert('La descripción es obligatoria'); return; }
    var url, params;
    if (accion === 'crear') {
        url = 'index.php?url=admin/crearProducto';
        params = new URLSearchParams({ item: item, descripcion: descripcion, minimo: minimo, maximo: maximo, pesoProm: pesoProm, csrf_token: CSRF_TOKEN });
    } else {
        url = 'index.php?url=admin/editarProducto';
        params = new URLSearchParams({ item_actual: item, descripcion: descripcion, minimo: minimo, maximo: maximo, pesoProm: pesoProm, csrf_token: CSRF_TOKEN });
    }
    fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: params.toString() })
        .then(function(res) { return res.json(); })
        .then(function(data) { if (data.success) { alert(data.msg); location.reload(); } else { alert('Error: ' + data.msg); } })
        .catch(function(error) { console.error('Error:', error); alert('Error de conexión'); });
}

function eliminarProducto(item) {
    if (!confirm('¿Está seguro de ELIMINAR el producto #' + item + '?\n\nEsta acción no se puede deshacer.')) return;
    var params = new URLSearchParams({ item: item, csrf_token: CSRF_TOKEN });
    fetch('index.php?url=admin/eliminarProducto', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: params.toString() })
        .then(function(res) { return res.json(); })
        .then(function(data) { if (data.success) { alert(data.msg); location.reload(); } else { alert('Error: ' + data.msg); } })
        .catch(function(error) { console.error('Error:', error); alert('Error de conexión'); });
}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function abrirRevision(id) {
    var modal = document.getElementById('revisionModal');
    modal.style.display = 'flex';
    document.getElementById('idDevolucionInput').value = id;
    document.querySelector('#formRevision input[name="csrf_token"]').value = CSRF_TOKEN;
    document.getElementById('infoDevolucion').innerHTML = '<div class="loading-state"><div class="loader"></div><p>Cargando información...</p></div>';
    fetch('index.php?url=consulta/detalles&id=' + id)
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) { document.getElementById('infoDevolucion').innerHTML = data.html; }
            else { document.getElementById('infoDevolucion').innerHTML = '<div class="error-state"><i class="fa-solid fa-triangle-exclamation"></i><p>Error al cargar los detalles</p></div>'; }
        })
        .catch(function(error) { console.error('Error:', error); document.getElementById('infoDevolucion').innerHTML = '<div class="error-state"><i class="fa-solid fa-triangle-exclamation"></i><p>Error de conexión</p></div>'; });
}

function verSoloDetalles(id) {
    var modal = document.getElementById('detallesModal');
    modal.style.display = 'flex';
    document.getElementById('detallesContenido').innerHTML = '<div class="loading-state"><div class="loader"></div><p>Cargando detalles...</p></div>';
    fetch('index.php?url=consulta/detalles&id=' + id)
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) { document.getElementById('detallesContenido').innerHTML = data.html; }
            else { document.getElementById('detallesContenido').innerHTML = '<div class="error-state"><i class="fa-solid fa-triangle-exclamation"></i><p>Error al cargar los detalles</p></div>'; }
        })
        .catch(function(error) { console.error('Error:', error); });
}

function cerrarModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    if (modalId === 'revisionModal') document.getElementById('formRevision').reset();
}

document.getElementById('formRevision').addEventListener('submit', function(e) {
    var accion = document.getElementById('accion').value;
    var codigo = document.getElementById('codigo_admin').value;
    var obs = document.getElementById('observacion_admin').value;
    if (!accion || !codigo || !obs) { e.preventDefault(); alert('Por favor complete todos los campos obligatorios'); return false; }
    var confirmar = confirm('¿Está seguro de ' + (accion === 'aprobado' ? 'APROBAR' : 'RECHAZAR') + ' esta devolución?\n\nEsta acción no se puede deshacer.');
    if (!confirmar) { e.preventDefault(); return false; }
});

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) event.target.style.display = 'none';
}

function filtrarTabla(tabla) { alert('Función de filtrado en desarrollo'); }
function exportarHistorial() { alert('Función de exportación en desarrollo'); }
</script>

<?php include 'Views/layouts/footer.php'; ?>
