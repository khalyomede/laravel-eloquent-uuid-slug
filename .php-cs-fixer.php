<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->exclude('vendor')
    ->in(__DIR__);

$config = new Config();

return $config->setRules([
    '@PSR12' => true,
    'strict_param' => true,
    'array_syntax' => ['syntax' => 'short'],
    'ordered_imports' => true,
    'ordered_traits' => true,
    'ordered_class_elements' => true,
])
    ->setFinder($finder);
