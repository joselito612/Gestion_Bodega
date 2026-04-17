<?php

class TestCase {
    protected static $passed = 0;
    protected static $failed = 0;
    protected static $results = [];

    public static function assertTrue($condition, $message = '') {
        if ($condition) {
            self::$passed++;
            self::$results[] = ['status' => 'PASS', 'message' => $message];
        } else {
            self::$failed++;
            self::$results[] = ['status' => 'FAIL', 'message' => $message];
        }
    }

    public static function assertFalse($condition, $message = '') {
        self::assertTrue(!$condition, $message);
    }

    public static function assertEquals($expected, $actual, $message = '') {
        self::assertTrue($expected === $actual, $message . " (expected: $expected, got: $actual)");
    }

    public static function assertNotEquals($expected, $actual, $message = '') {
        self::assertTrue($expected !== $actual, $message);
    }

    public static function printResults() {
        echo "\n=== RESULTADOS DE TESTS ===\n";
        echo "Pasados: " . self::$passed . "\n";
        echo "Fallidos: " . self::$failed . "\n";
        echo "Total: " . (self::$passed + self::$failed) . "\n\n";

        foreach (self::$results as $result) {
            $status = $result['status'];
            $message = $result['message'];
            $color = $status === 'PASS' ? "\033[32m" : "\033[31m";
            $reset = "\033[0m";
            echo "{$color}[{$status}]{$reset} {$message}\n";
        }

        echo "\n";
        if (self::$failed > 0) {
            echo "\033[31m" . "ALGUNOS TESTS FALLARON" . "\033[0m\n";
            exit(1);
        } else {
            echo "\033[32m" . "TODOS LOS TESTS PASARON" . "\033[0m\n";
            exit(0);
        }
    }
}