<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Privatization\Rector\Class_\ChangeReadOnlyVariableWithDefaultValueToConstantRector;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    // paths to refactor; solid alternative to CLI arguments
    $parameters->set(Option::PATHS, [
        __DIR__.'/src',
        //__DIR__.'/tests'
    ]);

    // do you need to include constants, class aliases or custom autoloader? files listed will be executed
    $parameters->set(Option::BOOTSTRAP_FILES, [
        __DIR__.'/vendor/autoload.php',
    ]);

    // auto import fully qualified class names? [default: false]
    $parameters->set(Option::AUTO_IMPORT_NAMES, true);

    // skip root namespace classes, like \DateTime or \Exception [default: true]
    //$parameters->set(Option::IMPORT_SHORT_CLASSES, false);

    $rules = [
        SetList::PSR_4,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::PHP_74,
        //SetList::PHP_80,
        SetList::TYPE_DECLARATION,
        SetList::TYPE_DECLARATION_STRICT,
        SetList::EARLY_RETURN,
        SetList::ORDER,
        SetList::PRIVATIZATION,

        // PHPUnit
        PHPUnitSetList::PHPUNIT80_DMS,
        PHPUnitSetList::PHPUNIT_80,
        PHPUnitSetList::PHPUNIT_MOCK,
        PHPUnitSetList::PHPUNIT_SPECIFIC_METHOD,
    ];

    foreach ($rules as $rule) {
        $containerConfigurator->import($rule);
    }

    $parameters->set(Option::SKIP, [
        ChangeReadOnlyVariableWithDefaultValueToConstantRector::class => [__DIR__."/src/Configuration.php"]
    ]);

};
