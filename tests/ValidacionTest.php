<?php

require_once __DIR__ . '/TestCase.php';

class ValidacionTest extends TestCase {

    private function validarCodigo($codigo) {
        return !empty($codigo) && strlen($codigo) >= 2 && strlen($codigo) <= 5 && preg_match('/^B[a-zA-Z0-9]+$/', $codigo);
    }

    private function validarNombre($nombre) {
        return !empty($nombre) && strlen($nombre) <= 100;
    }

    private function validarDireccion($direccion) {
        $direccionesValidas = ['Estación Central', 'Pudahuel', 'Las Condes'];
        return !empty($direccion) && in_array($direccion, $direccionesValidas);
    }

    private function validarDotacion($dotacion) {
        return !empty($dotacion) && is_numeric($dotacion) && (int)$dotacion >= 1;
    }

    private function validarEstado($estado) {
        $estadosValidos = ['Activada', 'Desactivada'];
        return in_array($estado, $estadosValidos);
    }

    public function testCodigoValidos() {
        $this->assertTrue($this->validarCodigo('B1'), 'B1 debería ser válido');
        $this->assertTrue($this->validarCodigo('B12'), 'B12 debería ser válido');
        $this->assertTrue($this->validarCodigo('B123'), 'B123 debería ser válido');
        $this->assertTrue($this->validarCodigo('B1234'), 'B1234 debería ser válido');
        $this->assertTrue($this->validarCodigo('B1A2'), 'B1A2 debería ser válido');
        $this->assertTrue($this->validarCodigo('BCDEF'), 'BCDEF debería ser válido');
    }

    public function testCodigoInvalidos() {
        $this->assertFalse($this->validarCodigo('B'), 'B solo debería ser inválido (menos de 2 caracteres)');
        $this->assertFalse($this->validarCodigo('b123'), 'b123 debería ser inválido (minúscula)');
        $this->assertFalse($this->validarCodigo('B-123'), 'B-123 debería ser inválido (guión)');
        $this->assertFalse($this->validarCodigo('B12345'), 'B12345 debería ser inválido (más de 5 caracteres)');
        $this->assertFalse($this->validarCodigo('A123'), 'A123 debería ser inválido (no comienza con B)');
        $this->assertFalse($this->validarCodigo(''), 'Cadena vacía debería ser inválida');
        $this->assertFalse($this->validarCodigo('12345'), 'Sin B debería ser inválido');
    }

    public function testNombreValidos() {
        $this->assertTrue($this->validarNombre('Bodega Central'), 'Nombre normal debería ser válido');
        $this->assertTrue($this->validarNombre('A'), 'Una letra debería ser válido');
        $this->assertTrue($this->validarNombre(str_repeat('a', 100)), '100 caracteres debería ser válido');
    }

    public function testNombreInvalidos() {
        $this->assertFalse($this->validarNombre(''), 'Nombre vacío debería ser inválido');
        $this->assertFalse($this->validarNombre(str_repeat('a', 101)), '101 caracteres debería ser inválido');
    }

    public function testDireccionValidas() {
        $this->assertTrue($this->validarDireccion('Estación Central'), 'Estación Central válida');
        $this->assertTrue($this->validarDireccion('Pudahuel'), 'Pudahuel válida');
        $this->assertTrue($this->validarDireccion('Las Condes'), 'Las Condes válida');
    }

    public function testDireccionInvalidas() {
        $this->assertFalse($this->validarDireccion(''), 'Dirección vacía inválida');
        $this->assertFalse($this->validarDireccion('Santiago'), 'Dirección no en lista inválida');
        $this->assertFalse($this->validarDireccion('estacion central'), 'Case sensitive - inválida');
    }

    public function testDotacionValidas() {
        $this->assertTrue($this->validarDotacion('1'), 'Dotación 1 válida');
        $this->assertTrue($this->validarDotacion('10'), 'Dotación 10 válida');
        $this->assertTrue($this->validarDotacion(100), 'Dotación como entero válida');
    }

    public function testDotacionInvalidas() {
        $this->assertFalse($this->validarDotacion(''), 'Dotación vacía inválida');
        $this->assertFalse($this->validarDotacion('0'), 'Cero inválido');
        $this->assertFalse($this->validarDotacion('-1'), 'Negativo inválido');
        $this->assertFalse($this->validarDotacion('abc'), 'Texto inválido');
    }

    public function testEstadoValidos() {
        $this->assertTrue($this->validarEstado('Activada'), 'Activada válido');
        $this->assertTrue($this->validarEstado('Desactivada'), 'Desactivada válido');
    }

    public function testEstadoInvalidos() {
        $this->assertFalse($this->validarEstado(''), 'Estado vacío inválido');
        $this->assertFalse($this->validarEstado('Inactivo'), 'Inactivo inválido');
        $this->assertFalse($this->validarEstado('Activo'), 'Activo inválido');
        $this->assertFalse($this->validarEstado('otro'), 'Otro valor inválido');
    }
}

echo "=== TESTS DE VALIDACIÓN ===\n\n";

$test = new ValidacionTest();
$test->testCodigoValidos();
$test->testCodigoInvalidos();
$test->testNombreValidos();
$test->testNombreInvalidos();
$test->testDireccionValidas();
$test->testDireccionInvalidas();
$test->testDotacionValidas();
$test->testDotacionInvalidas();
$test->testEstadoValidos();
$test->testEstadoInvalidos();

TestCase::printResults();