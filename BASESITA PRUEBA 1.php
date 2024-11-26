<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soft Paws Wellness</title>
    <style>
        body {
            background-color: #ADD8E6; /* Fondo azul claro */
            margin: 0; /* Elimina márgenes predeterminados */
            font-family: Arial, sans-serif; /* Fuente básica */
        }
        .imagen-reducida {
            max-width: 300px; /* Ajusta según necesites */
            height: auto; /* Mantiene la proporción */
            display: block; /* Centrar imagen */
            margin: 20px auto; /* Centrar imagen y añadir margen */
        }
        h2 {
            text-align: center; /* Centrar el texto del encabezado */
            color: #333; /* Color del texto */  
        }
        .form-container {
            max-width: 300px; 
            margin: 20px auto; /* Centrar formulario */
            padding: 20px; /* Espaciado interno */
            background-color: white; /* Fondo blanco para el formulario */
            border-radius: 5px; /* Bordes redondeados */
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Sombra */
        }
        table {
            width: 70%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            text-align: center;
        }
        th, td {
            padding: 10px;
        }
    </style>
</head>

<body>
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="images/soft_paws_wellness.jpg" alt="Bienvenido a Soft Paws Wellness" class="imagen-reducida">
    </div>

    <h2>Gestión de Medicamentos</h2>

    <?php
    // Configuración de la base de datos
    $servidor = "localhost";
    $usuario = "root";
    $contrasena = ""; // Cambiar si tu usuario tiene contraseña
    $base_datos = "registro_medicamentos";

    // Crear conexión
    $conexion = new mysqli($servidor, $usuario, $contrasena, $base_datos);

    // Verificar conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Insertar datos
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
        $nombre = trim($_POST['nombre']);
        $cantidad = filter_var($_POST['cantidad_disponible'], FILTER_VALIDATE_INT);
        $precio = filter_var($_POST['precio'], FILTER_VALIDATE_FLOAT);

        if ($nombre && $cantidad !== false && $precio !== false) {
            $stmt = $conexion->prepare("INSERT INTO medicamentos (nombre, cantidad_disponible, precio) VALUES (?, ?, ?)");
            $stmt->bind_param("sid", $nombre, $cantidad, $precio);

            if ($stmt->execute()) {
                echo "<p style='text-align:center; color: green;'>Nuevo medicamento agregado exitosamente.</p>";
            } else {
                echo "<p style='text-align:center; color: red;'>Error al agregar el medicamento: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='text-align:center; color: red;'>Por favor, completa todos los campos correctamente.</p>";
        }
    }

    // Eliminar registros
    if (isset($_GET['eliminar_id'])) {
        $eliminar_id = filter_var($_GET['eliminar_id'], FILTER_VALIDATE_INT);
        if ($eliminar_id) {
            $stmt = $conexion->prepare("DELETE FROM medicamentos WHERE id = ?");
            $stmt->bind_param("i", $eliminar_id);
            if ($stmt->execute()) {
                echo "<p style='text-align:center; color: green;'>Medicamento eliminado exitosamente.</p>";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                echo "<p style='text-align:center; color: red;'>Error al eliminar el medicamento: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='text-align:center; color: red;'>ID inválido.</p>";
        }
    }

    // Mostrar tabla de medicamentos
    $consulta = "SELECT id, nombre, cantidad_disponible, precio FROM medicamentos ORDER BY id ASC";
    $resultado = $conexion->query($consulta);

    if ($resultado->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Eliminar</th>
                </tr>";
        while ($fila = $resultado->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($fila['id']) . "</td>
                    <td>" . htmlspecialchars($fila['nombre']) . "</td>
                    <td>" . htmlspecialchars($fila['cantidad_disponible']) . "</td>
                    <td>$" . htmlspecialchars($fila['precio']) . "</td>
                    <td><a href='?eliminar_id=" . htmlspecialchars($fila['id']) . "' style='color: red;'>Eliminar</a></td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='text-align:center;'>No hay medicamentos registrados.</p>";
    }

    // Cerrar conexión
    $conexion->close();
    ?>

    <div class="form-container">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <label for="nombre">Nombre:</label><br>
            <input type="text" name="nombre" required><br><br>
            
            <label for="cantidad_disponible">Cantidad Disponible:</label><br>
            <input type="number" name="cantidad_disponible" required><br><br>
            
            <label for="precio">Precio:</label><br>
            <input type="text" name="precio" required><br><br>
            
            <input type="submit" value="Agregar Medicamento">
        </form>
    </div>
</body>
</html>
