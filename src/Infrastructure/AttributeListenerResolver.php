<?php

/**
 * This file is part of Event
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Event\Infrastructure;

use Generator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use RuntimeException;
use Slick\Event\Application\ListenerClassFile;
use Slick\Event\EventListener;
use Slick\Event\ListenerResolver;
use Slick\FsWatch\Directory;

/**
 * AttributeListenerResolver
 *
 * @package Slick\Event\Infrastructure
 */
final readonly class AttributeListenerResolver implements ListenerResolver
{

    private string $cacheFile;
    private Directory $directoryWatch;

    /**
     * Creates a AttributeListenerResolver
     *
     * @param string $directory
     * @param ContainerInterface $container
     * @param string|null $cacheFile
     */
    public function __construct(
        private string $directory,
        private ContainerInterface $container,
        ?string $cacheFile = null
    ) {
        $this->cacheFile = $cacheFile ?? sys_get_temp_dir() . '/listeners.cache.phpser';
        $this->directoryWatch = new Directory($this->directory);
    }

    /**
     * @inheritDoc
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RuntimeException When the container is not available
     */
    public function resolve(): array
    {
        $files = $this->loadOrScan();

        $resolved = [];
        foreach ($files as $file) {
            if (!$file instanceof ListenerClassFile || !$file->isListener()) {
                continue;
            }

            $file->withContainer($this->container);

            foreach ($file->resolveListeners() as $binding) {
                if (!$binding['listener'] instanceof EventListener) {
                    continue;
                }

                $resolved[] = [
                    'event' => $binding['event'],
                    'listener' => $binding['listener'],
                    'priority' => $binding['priority'],
                ];
            }
        }

        return $resolved;
    }

    /**
     * Loads or scans files and returns an array of ListenerClassFile objects
     *
     * @return array<ListenerClassFile> Array of ListenerClassFile objects
     */
    private function loadOrScan(): array
    {

        if (file_exists($this->cacheFile)) {
            $contents = file_get_contents($this->cacheFile);
            $data = unserialize($contents, ['allowed_classes' => true]) ?: [];
            if (!$this->directoryWatch->hasChanged($data['snapshot'])) {
                return $data['files'];
            }
        }

        $this->clearCache();
        $files = [];
        foreach ($this->findPhpFiles($this->directory) as $filePath) {
            $listenerFile = new ListenerClassFile($filePath);
            if ($listenerFile->isListener()) {
                $files[] = $listenerFile;
            }
        }

        file_put_contents($this->cacheFile, serialize([
            'snapshot' => $this->directoryWatch->snapshot(),
            'files' => $files,
        ]));

        return $files;
    }

    /**
     * Finds PHP files in a given directory and yields them
     *
     * @param string $directory The directory in which to search for PHP files
     * @return Generator<string> Yields PHP file paths one by one
     */
    private function findPhpFiles(string $directory): Generator
    {
        $dirIterator = new RecursiveDirectoryIterator($directory);
        $iterator = new RecursiveIteratorIterator($dirIterator);
        $phpFiles = new RegexIterator($iterator, '/^.+\.php$/i', RegexIterator::GET_MATCH);

        foreach ($phpFiles as $matches) {
            yield $matches[0];
        }
    }

    /**
     * Clears the cache file if it exists
     */
    public function clearCache(): void
    {
        if (file_exists($this->cacheFile)) {
            unlink($this->cacheFile);
        }
    }
}
