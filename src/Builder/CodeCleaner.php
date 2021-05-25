<?php declare(strict_types=1);

namespace Mapsred\MangadexSDK\Builder;

use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class CodeCleaner
{
    public function execute(): void
    {
        $finder = new Finder();
        $paths = [
            __DIR__."/../../src/",
            //__DIR__."/../../tests/" // Tests are not implemented by default
        ];

        $files = $finder->in($paths)->exclude('Builder')->name('*.php')->files();

        foreach ($files as $file) {
            $this->cleanClassPhpDoc($file);
            $this->cleanExcessDoc($file);
            $this->cleanGuzzleDeprecated($file);
        }
    }

    private function cleanClassPhpDoc(SplFileInfo $file): void
    {
        $class = $this->getFQN($file);
        $reflection = new ReflectionClass($class);

        if (false !== $doc = $reflection->getDocComment()) {
            $content = str_replace($doc, '', $file->getContents());
            file_put_contents($file->getPathname(), $content);
        }
    }

    private function cleanExcessDoc(SplFileInfo $file): void
    {
        $content = explode(PHP_EOL, $file->getContents());
        $last = ' */' === $content[26] ? 26 : 27;
        if ('/**' !== $content[1]) {
            return;
        }
        if (' */' !== $content[$last]) {
            return;
        }

        for ($i = 1; $i <= $last; $i++) {
            unset($content[$i]);
        }

        $content = implode(PHP_EOL, $content);
        file_put_contents($file->getPathname(), $content);
    }

    private function cleanGuzzleDeprecated(SplFileInfo $file): void
    {
        $content = str_replace([
            '\GuzzleHttp\Psr7\build_query',
            '\GuzzleHttp\Psr7\try_fopen'
        ], [
            '\GuzzleHttp\Psr7\Query::build',
            '\GuzzleHttp\Psr7\Query::tryFopen'
        ], $file->getContents());

        file_put_contents($file->getPathname(), $content);
    }
    private function getFQN(SplFileInfo $file): string
    {
        $class = str_replace(['.php', '/'], ['', '\\'], $file->getRelativePathname());
        $test = substr_compare($class, 'Test', -strlen('Test')) === 0 ? 'Test\\' : '';
        $namespace = 'Mapsred\\MangadexSDK\\'.$test;

        return $namespace.$class;
    }
}
