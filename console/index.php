<?php

use Phalcon\Cli\Console as ConsoleApp;
use Phalcon\Config;
use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Loader;

// Using the CLI factory default services container
$di = new CliDI();

/**
 * Register the autoloader and tell it to register the tasks directory
 */
$loader = new Loader();

$loader->registerDirs(
    [
        __DIR__ . '/tasks',
    ]
);

$loader->register();

// Load the configuration file (if any)
$configFile = __DIR__ . '/../app/config/config-local.php';


if (is_readable($configFile)) {
    $config = new Config(require __DIR__ . '/../app/config/config.php');

    $di->set('config', $config);
} else {
    fwrite(STDERR, 'File ' . $configFile . ' is not readable' . PHP_EOL);
    exit(1);

}

// Create a console application
$console = new ConsoleApp();

$console->setDI($di);

/**
 * Process the console arguments
 */

$arguments = [];


foreach ($argv as $k => $arg) {
    if ($k === 1) {
        $arguments['task'] = $arg;
    } elseif ($k === 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

try {
    // Handle incoming arguments
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    // Do Phalcon related stuff here
    // ..
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
} catch (\Throwable $throwable) {
    fwrite(STDERR, $throwable->getMessage() . PHP_EOL);
    exit(1);
} catch (\Exception $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    exit(1);
}