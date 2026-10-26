<?php
declare(strict_types=1);

require_once __DIR__ . '/clases/clases.php';
require_once __DIR__ . '/funciones/funciones.php';

echo "=== SISTEMA DE LIQUIDACIÓN DE PERSONAL - ALCALDÍA ===\n";
echo "====================================================\n";

// Arreglo de objetos para mostrar en consola
$usuarios = [];

// Arreglo asociativo para exportar al archivo datos.json
$datosJson = [];

// Ruta donde se creará el archivo JSON
$rutaArchivoJson = __DIR__ . '/datos.json';

while (true) {
    echo "\nSeleccione una opción del menú:\n";
    echo "1. Registrar Funcionario de Planta\n";
    echo "2. Registrar Contratista\n";
    echo "3. Salir del Programa (Mostrar consolidado y guardar JSON)\n";
    echo "4. Mostrar usuarios y ver formato JSON\n";
    $tipo = leer("Opción (1, 2, 3 o 4): ");

    // Opción 3: Desplegar consolidado final, asegurar guardado en datos.json y salir
    if ($tipo === '3') {
        echo "\n====================================================\n";
        echo "CONSOLIDADO FINAL DE PERSONAL REGISTRADO (" . count($usuarios) . ")\n";
        echo "====================================================\n";

        if (empty($usuarios)) {
            echo "No se registraron usuarios en esta sesión.\n";
        } else {
            foreach ($usuarios as $indice => $u) {
                echo "\n[Registro #" . ($indice + 1) . "]\n";
                echo $u->informacion();
                echo "Neto a Pagar / Girar: $" . number_format($u->liquidarSalario(), 2, '.', ',') . "\n";
                echo "----------------------------------------------------\n";
            }

            // Convertimos a formato JSON legible y guardamos en datos.json
            $jsonFinal = json_encode($datosJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            file_put_contents($rutaArchivoJson, $jsonFinal);

            echo "\n[OK]: La información se guardó correctamente en 'datos.json'.\n";
        }

        echo "\nSaliendo es Saliendooo....\n\n";
        break;
    }

    // Opción 4: Ver en consola tanto el listado como la estructura JSON actual
    if ($tipo === '4') {
        echo "\n====================================================\n";
        echo "DATOS EN FORMATO JSON (Archivo: datos.json)\n";
        echo "====================================================\n";

        if (empty($datosJson)) {
            echo "Aún no hay usuarios registrados para mostrar en JSON.\n";
        } else {
            $jsonTexto = json_encode($datosJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            echo $jsonTexto . "\n";
        }
        continue;
    }

    // Validar que sea 1 o 2 antes de pedir datos
    if ($tipo !== '1' && $tipo !== '2') {
        echo "\n[ERROR]: Opción no válida. Seleccione 1, 2, 3 o 4.\n";
        continue;
    }

    try {
        $tipoDoc = leer("Tipo de Documento (CC, CE, NIT): ");
        $numDoc = leer("Número de Documento: ");
        $nombres = leer("Nombres: ");
        $apellidos = leer("Apellidos: ");
        $correo = leer("Correo Electrónico: ");

        if ($tipo === '1') {
            $salarioBasico = (float)leer("Salario Básico Mensual ($): ");
            $dias = (int)leer("Días Trabajados en el mes (1-30): ");
            $auxTransporte = (float)leer("Valor Auxilio de Transporte Legal Vigente ($): ");

            $persona = new Funcionario(
                $tipoDoc,
                $numDoc,
                $nombres,
                $apellidos,
                $correo,
                $salarioBasico,
                $dias,
                $auxTransporte
            );

            // Estructura del Funcionario para el archivo datos.json
            $datosJson[] = [
                'tipoVinculacion'        => 'Funcionario de Planta',
                'tipoDocumento'          => $persona->getTipoDocumento(),
                'numeroDocumento'        => $persona->getNumeroDocumento(),
                'nombres'                => $persona->getNombres(),
                'apellidos'              => $persona->getApellidos(),
                'correo'                 => $persona->getCorreo(),
                'salarioBasico'          => $persona->getSalarioBasico(),
                'diasTrabajados'         => $persona->getDiasTrabajados(),
                'auxilioTransporteLegal' => $persona->getAuxilioTransporteLegal(),
                'netoPagar'              => $persona->liquidarSalario()
            ];

        } else {
            $honorarios = (float)leer("Honorarios Mensuales Pactados ($): ");
            $retefuente = (float)leer("Porcentaje de Retención en la Fuente (%): ");

            $persona = new Contratista(
                $tipoDoc,
                $numDoc,
                $nombres,
                $apellidos,
                $correo,
                $honorarios,
                $retefuente
            );

            // Estructura del Contratista para el archivo datos.json
            $datosJson[] = [
                'tipoVinculacion'           => 'Contratista',
                'tipoDocumento'             => $persona->getTipoDocumento(),
                'numeroDocumento'           => $persona->getNumeroDocumento(),
                'nombres'                   => $persona->getNombres(),
                'apellidos'                 => $persona->getApellidos(),
                'correo'                    => $persona->getCorreo(),
                'honorariosMensuales'       => $persona->getHonorariosMensuales(),
                'porcentajeRetencionFuente' => $persona->getPorcentajeRetencionFuente(),
                'netoPagar'                 => $persona->liquidarSalario()
            ];
        }

        // Guardamos el objeto en el arreglo de usuarios
        $usuarios[] = $persona;

        // Guardamos/actualizamos inmediatamente el archivo datos.json en el disco
        $jsonActualizado = json_encode($datosJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($rutaArchivoJson, $jsonActualizado);

        echo "\n====================================================\n";
        echo "RESULTADO DEL PROCESO DE LIQUIDACIÓN\n";
        echo "====================================================\n";
        echo $persona->informacion() . "\n";
        echo "Neto a Pagar / Girar: $" . number_format($persona->liquidarSalario(), 2, '.', ',') . "\n";
        echo "[Guardado automáticamente en datos.json]\n";
        echo "====================================================\n";

    } catch (Exception $e) {
        echo "\n[ERROR DE VALIDACIÓN]: " . $e->getMessage() . "\n";
    }
}