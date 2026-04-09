<?php
// Controllers/AdminController.php
require_once 'Models/DevolucionModel.php';
require_once 'Models/ProductoModel.php';
require_once 'Config/EmailHelper.php';

class AdminController {
    private $model;
    private $prodModel;
    private $emailHelper;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['logged_in']) || $_SESSION['grado'] != 1) {
            header('Location: index.php?url=auth/index');
            exit;
        }

        $this->model       = new DevolucionModel();
        $this->prodModel   = new ProductoModel();
        $this->emailHelper = new EmailHelper();
    }

    public function index() {
        $titulo     = "Panel Administrador - DevolutionSync";
        $pendientes = $this->model->obtenerPendientes();
        $historial  = $this->model->obtenerHistorial(50);
        $productos  = $this->prodModel->listarTodos();
        require_once 'Views/admin/panel_administrador.php';
    }

    public function revisar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                http_response_code(403);
                die('Token CSRF inválido');
            }

            try {
                $id      = intval($_POST['id_devolucion'] ?? 0);
                $accion  = trim($_POST['accion'] ?? '');
                $codigo  = trim($_POST['codigo_admin'] ?? '');
                $obs     = trim($_POST['observacion_admin'] ?? '');
                $revisor = $_SESSION['user'] ?? $_SESSION['nombre'];

                if ($id <= 0) {
                    throw new Exception('ID de devolución inválido');
                }

                if (!in_array($accion, ['aprobado', 'rechazado'])) {
                    throw new Exception('Acción inválida');
                }

                if (empty($codigo)) {
                    throw new Exception('El código de autorización es obligatorio');
                }

                if (empty($obs)) {
                    throw new Exception('Las observaciones son obligatorias');
                }

                $devolucion = $this->model->obtenerPorId($id);

                if (!$devolucion) {
                    throw new Exception('Devolución no encontrada');
                }

                $resultado = $this->model->procesarRevision($id, $accion, $codigo, $obs, $revisor);

                if ($resultado) {
                    if (!empty($devolucion['correo_solicitante'])) {
                        $this->emailHelper->notificarEstadoDevolucion($devolucion, $accion, $obs);
                    }
                    header('Location: index.php?url=admin/index&msg=success');
                } else {
                    throw new Exception('Error al procesar la revisión en la base de datos');
                }

            } catch (Exception $e) {
                error_log("Error en AdminController::revisar - " . $e->getMessage());
                header('Location: index.php?url=admin/index&msg=error');
            }
            exit;
        } else {
            header('Location: index.php?url=admin/index');
            exit;
        }
    }

    public function estadisticas() {
        $titulo = "Estadísticas - Panel Administrador";
        $stats  = $this->model->obtenerEstadisticas();
        require_once 'Views/admin/estadisticas.php';
    }

    // ========== CRUD PRODUCTOS ==========

    public function crearProducto() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=admin/index');
            exit;
        }

        header('Content-Type: application/json');

        // Validar token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'msg' => 'Token CSRF inválido']);
            exit;
        }

        try {
            $item        = intval($_POST['item'] ?? 0);
            $descripcion = trim($_POST['descripcion'] ?? '');
            $minimo      = floatval($_POST['minimo'] ?? 0);
            $maximo      = floatval($_POST['maximo'] ?? 0);
            $pesoProm    = floatval($_POST['pesoProm'] ?? 0);

            if ($item <= 0) {
                throw new Exception('El código del producto debe ser un número mayor a 0');
            }

            if (empty($descripcion)) {
                throw new Exception('La descripción es obligatoria');
            }

            $existente = $this->prodModel->obtenerPorItem($item);
            if ($existente) {
                throw new Exception('Ya existe un producto con el código ' . $item);
            }

            $resultado = $this->prodModel->crear($item, $descripcion, $minimo, $maximo, $pesoProm);

            if ($resultado) {
                echo json_encode(['success' => true, 'msg' => 'Producto creado correctamente']);
            } else {
                throw new Exception('Error al guardar el producto');
            }

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
        }
        exit;
    }

    public function editarProducto() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=admin/index');
            exit;
        }

        header('Content-Type: application/json');

        // Validar token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'msg' => 'Token CSRF inválido']);
            exit;
        }

        try {
            $itemActual  = intval($_POST['item_actual'] ?? 0);
            $descripcion = trim($_POST['descripcion'] ?? '');
            $minimo      = floatval($_POST['minimo'] ?? 0);
            $maximo      = floatval($_POST['maximo'] ?? 0);
            $pesoProm    = floatval($_POST['pesoProm'] ?? 0);

            if ($itemActual <= 0) {
                throw new Exception('Código de producto inválido');
            }

            if (empty($descripcion)) {
                throw new Exception('La descripción es obligatoria');
            }

            $resultado = $this->prodModel->actualizar($itemActual, $descripcion, $minimo, $maximo, $pesoProm);

            if ($resultado) {
                echo json_encode(['success' => true, 'msg' => 'Producto actualizado correctamente']);
            } else {
                throw new Exception('Error al actualizar el producto');
            }

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
        }
        exit;
    }

    public function eliminarProducto() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=admin/index');
            exit;
        }

        header('Content-Type: application/json');

        // Validar token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'msg' => 'Token CSRF inválido']);
            exit;
        }

        try {
            $item = intval($_POST['item'] ?? 0);

            if ($item <= 0) {
                throw new Exception('Código de producto inválido');
            }

            $stmt = $this->model->getDb()->prepare("SELECT COUNT(*) as total FROM devoluciones WHERE codigo_producto = ?");
            $stmt->execute([$item]);
            $resultado = $stmt->fetch();

            if ($resultado && $resultado['total'] > 0) {
                throw new Exception('No se puede eliminar: este producto tiene ' . $resultado['total'] . ' devolución(es) asociada(s)');
            }

            $exito = $this->prodModel->eliminar($item);

            if ($exito) {
                echo json_encode(['success' => true, 'msg' => 'Producto eliminado correctamente']);
            } else {
                throw new Exception('Error al eliminar el producto');
            }

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
        }
        exit;
    }

    public function buscarProductos() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            header('Location: index.php?url=admin/index');
            exit;
        }

        header('Content-Type: application/json');

        try {
            $texto = trim($_GET['q'] ?? '');

            if (strlen($texto) < 1) {
                $productos = $this->prodModel->listarTodos();
            } else {
                $productos = $this->prodModel->buscar($texto);
            }

            echo json_encode(['success' => true, 'productos' => $productos]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
        }
        exit;
    }
}
