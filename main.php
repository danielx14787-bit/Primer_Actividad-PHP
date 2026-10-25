<?php
declare(strict_types=1);

require_once __DIR__ . '/clases/clases.php';
require_once __DIR__ . '/funciones/funciones.php';

echo "=== SISTEMA DE LIQUIDACIÓN DE PERSONAL - ALCALDÍA ===\n";
echo "====================================================\n";

while (true) {
    echo "\nSeleccione el tipo de vinculacion a registrar:\n";
    echo "1. Funcionario de Planta\n";
    echo "2. Contratista\n";
    echo "3. Salir del Programa\n";
    $tipo = leer("opcion (1, 2 o 3): ");

    if ($tipo === '3') {
        echo "\nSaliendo es Saliendooo....\n\n";
        break;
    }

    if ($tipo !== '1' && $tipo !== '2') {
        echo "\n[ERROR]: Opción no válida. Seleccione 1, 2 o 3.\n";
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

        echo "\n====================================================\n";
        echo "RESULTADO DEL PROCESO DE LIQUIDACIÓN\n";
        echo "====================================================\n";
        echo $persona->informacion() . "\n";
        echo "Neto a Pagar / Girar: $" . number_format($persona->liquidarSalario(), 2, '.', ',') . "\n";
        echo "====================================================\n";

    } catch (Exception $e) {
        echo "\n[ERROR DE VALIDACIÓN]: " . $e->getMessage() . "\n";
    }
}