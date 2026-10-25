<?php
declare(strict_types=1);

require_once __DIR__ . '/clases/clases.php';
require_once __DIR__ . '/funciones/funciones.php';

echo "=== SISTEMA DE LIQUIDACIÓN DE PERSONAL - ALCALDÍA ===\n";
echo "====================================================\n";

// 1. Arreglo donde se guardarán todos los funcionarios y contratistas
$usuarios = [];

while (true) {
    echo "\nSeleccione una opción del menú:\n";
    echo "1. Registrar Funcionario de Planta\n";
    echo "2. Registrar Contratista\n";
    echo "3. Salir del Programa\n";
    echo "4. Mostrar todos los usuarios registrados\n";
    $tipo = leer("Opción (1, 2, 3 o 4): ");

    // Opción 3: Salir del programa
    if ($tipo === '3') {
        echo "\nSaliendo es Saliendooo....\n\n";
        break;
    }

    // Opción 4: Mostrar todos los usuarios guardados hasta el momento
    if ($tipo === '4') {
        echo "\n====================================================\n";
        echo "LISTADO CONSOLIDADO DE USUARIOS REGISTRADOS (" . count($usuarios) . ")\n";
        echo "====================================================\n";

        if (empty($usuarios)) {
            echo "Aún no hay usuarios registrados en el sistema.\n";
        } else {
            foreach ($usuarios as $indice => $u) {
                echo "\n[Registro #" . ($indice + 1) . "]\n";
                echo $u->informacion();
                echo "Neto a Pagar / Girar: $" . number_format($u->liquidarSalario(), 2, '.', ',') . "\n";
                echo "----------------------------------------------------\n";
            }
        }
        continue; // Regresa al menú principal
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
        }

        // 2. Guardamos la persona creada dentro del arreglo $usuarios
        $usuarios[] = $persona;

        echo "\n====================================================\n";
        echo "USUARIO GUARDADO - RESULTADO DE LIQUIDACIÓN\n";
        echo "====================================================\n";
        echo $persona->informacion() . "\n";
        echo "Neto a Pagar / Girar: $" . number_format($persona->liquidarSalario(), 2, '.', ',') . "\n";
        echo "====================================================\n";

    } catch (Exception $e) {
        echo "\n[ERROR DE VALIDACIÓN]: " . $e->getMessage() . "\n";
    }
}