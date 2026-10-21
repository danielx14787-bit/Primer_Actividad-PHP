<?php
declare(strict_types=1);

function leer(string $mensaje): string
{
    echo $mensaje;
    $resultado = readline();
    return trim($resultado !== false ? $resultado : '');
}