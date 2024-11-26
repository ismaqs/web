<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soft Paws Wellness</title>
    <style>
        body {
            background-color: #ADD8E6;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .imagen-reducida {
            max-width: 300px;
            height: auto;
            display: block;
            margin: 20px auto;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-container {
            max-width: 300px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        table {
            width: 70%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #FFFFFF;
        }
        table, th, td {
            border: 1px solid #000000;
        }
        th {
            background-color: #FFD1DC;
            color: #333;
        }
        td {
            background-color: #FFFFFF;
            color: #333;
            padding: 10px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="images/medicine-pharmacy-hospital-set-of-medicines-with-labels-the-concept-of-medical-subjects-illustration-in-cartoon-style-vector.jpg" alt="CONSULTOR DE MEDICAMENTO" class="imagen-reducida">
    </div>

    <h2>Bienvenidos a CONSULTOR DE MEDICAMENTOS</h2>

    <?php
    // Configuración de la base de datos
    $servername = "localhost";   
    $username = "root";
    $password = "";
    $dbname = "hospital"; 

    // Crear la conexión con la base de datos
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica la conexión con la base de datos
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Eliminar medicamento
    if (isset($_GET['delete_id'])) {
        $delete_id = $_GET['delete_id'];
        $delete_sql = "DELETE FROM medicamentos WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $delete_id);
        
        if ($stmt->execute()) {
            echo "<p style='text-align:center; color: green;'>Medicamento eliminado exitosamente.</p>";
        } else {
            echo "<p style='text-align:center; color: red;'>Error al eliminar medicamento.</p>";
        }
        $stmt->close();
    }

    // Procesar la venta de medicamentos
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_vender']) && isset($_POST['cantidad_vender'])) {
        $id_vender = $_POST['id_vender'];
        $cantidad_vender = $_POST['cantidad_vender'];

        if ($cantidad_vender > 0) {
            // Actualizar la cantidad de medicamento en la base de datos
            $update_sql = "UPDATE medicamentos SET cantidad = cantidad - ? WHERE id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ii", $cantidad_vender, $id_vender);

            if ($stmt->execute()) {
                echo "<p style='text-align:center; color: green;'>Venta realizada correctamente.</p>";
            } else {
                echo "<p style='text-align:center; color: red;'>Error al realizar la venta.</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='text-align:center; color: red;'>La cantidad a vender debe ser mayor que 0.</p>";
        }
    }

    // Procesar la adición de nuevos medicamentos
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre']) && isset($_POST['cantidad']) && isset($_POST['precio'])) {
        $nombre = $_POST['nombre'];
        $cantidad = $_POST['cantidad'];
        $precio = $_POST['precio'];
        $vender = isset($_POST['vender']) ? 1 : 0; // Si el checkbox "vender" está marcado, el valor será 1, de lo contrario será 0

        // Verificar si la cantidad y el precio son números válidos
        if (is_numeric($cantidad) && is_numeric($precio)) {
            // Insertar el medicamento en la base de datos
            $insert_sql = "INSERT INTO medicamentos (Nombre, cantidad, precio, vender) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("sidi", $nombre, $cantidad, $precio, $vender);

            if ($stmt->execute()) {
                echo "<p style='text-align:center; color: green;'>Medicamento agregado exitosamente.</p>";
            } else {
                echo "<p style='text-align:center; color: red;'>Error al agregar medicamento.</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='text-align:center; color: red;'>La cantidad y el precio deben ser números válidos.</p>";
        }
    }

    // Procesar la adición de cantidades
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_agregar']) && isset($_POST['cantidad_agregar'])) {
        $id_agregar = $_POST['id_agregar'];
        $cantidad_agregar = $_POST['cantidad_agregar'];

        if ($cantidad_agregar > 0) {
            // Actualizar la cantidad del medicamento en la base de datos
            $update_sql = "UPDATE medicamentos SET cantidad = cantidad + ? WHERE id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ii", $cantidad_agregar, $id_agregar);

            if ($stmt->execute()) {
                echo "<p style='text-align:center; color: green;'>Cantidad actualizada correctamente.</p>";
            } else {
                echo "<p style='text-align:center; color: red;'>Error al actualizar la cantidad.</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='text-align:center; color: red;'>La cantidad a agregar debe ser mayor que 0.</p>";
        }
    }

    // Mostrar los datos de la base de datos en una tabla
    $sql = "SELECT id, Nombre, cantidad, precio, vender FROM medicamentos"; 
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table><tr><th>ID</th><th>Nombre</th><th>Cantidad</th><th>Precio</th><th>Vender</th><th>Cantidad a vender</th><th>Cantidad a agregar</th><th>Acciones</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["id"] . "</td>
                    <td>" . $row["Nombre"] . "</td>
                    <td>" . $row["cantidad"] . "</td>
                    <td>" . $row["precio"] . "</td>
                    <td>" . ($row["vender"] == 1 ? "Sí" : "No") . "</td>  
                    <td>
                        <form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>
                            <input type='hidden' name='id_vender' value='" . $row["id"] . "'>
                            <input type='number' name='cantidad_vender' min='1' max='" . $row["cantidad"] . "' required>
                            <input type='submit' value='Vender'>
                        </form>
                    </td>
                    <td>
                        <form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>
                            <input type='hidden' name='id_agregar' value='" . $row["id"] . "'>
                            <input type='number' name='cantidad_agregar' min='1' required>
                            <input type='submit' value='Agregar'>
                        </form>
                    </td>
                    <td><a href='?delete_id=" . $row["id"] . "' onclick='return confirm(\"¿Estás seguro de eliminar este medicamento?\");'>Eliminar</a></td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='text-align:center; color: red;'>No se encontraron registros.</p>";
    }

    // Cerrar conexión
    $conn->close();
    ?>

    <!-- Formulario para agregar medicamentos -->
    <div class="form-container">
        <h3>Agregar nuevo medicamento</h3>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="nombre">Nombre:</label><br>
            <input type="text" name="nombre" required><br><br>
            
            <label for="cantidad">Cantidad disponible:</label><br>
            <input type="number" name="cantidad" required><br><br>
            
            <label for="precio">Precio:</label><br>
            <input type="number" step="0.01" name="precio" required><br><br>
            
            <label for="vender">¿Disponible para vender?</label><br>
            <input type="checkbox" name="vender"><br><br>

            <input type="submit" value="Agregar medicamento">
        </form>
    </div>

</body>
</html>
z