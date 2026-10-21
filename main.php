<?php
declare(strict_types=1);

// Importar clases y funciones requeridas
require_once __DIR__ . '/clases/clases.php';
require_once __DIR__ . '/funciones/funciones.php';

echo "=== SISTEMA DE LIQUIDACIÓN DE PERSONAL - ALCALDÍA ===\n";
echo "====================================================\n\n";

echo "Seleccione el tipo de vinculación a registrar:\n";
echo "1. Funcionario de Planta\n";
echo "2. Contratista\n";
$tipo = leer("Opción (1 o 2): ");

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
    } elseif ($tipo === '2') {
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
    } else {
        throw new Exception("Opción de vinculación no válida.");
    }

    echo "\n====================================================\n";
    echo "RESULTADO DEL PROCESO DE LIQUIDACIÓN\n";
    echo "====================================================\n";
    echo $persona->informacion() . "\n";
    echo "Neto a Pagar / Girar: $" . number_format($persona->liquidarSalario(), 2, '.', ',') . "\n";
    echo "====================================================\n\n";

} catch (Exception $e) {
    echo "\n[ERROR DE VALIDACIÓN]: " . $e->getMessage() . "\n\n";
}