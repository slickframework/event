<?php

/**
 * This file is part of slick/event package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Event\Application;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;
use Slick\Event\Application\Attribute\AsEventListener;
use Slick\Event\EventListener;

/**
 * ListenerClassFile
 *
 * @package Slick\Event\Application
 */
final class ListenerClassFile
{
    private readonly string $className;
    /** @var array<array{source: string, event: string, method: string, priority: int}> */
    private array $listenerBindings = [];

    public function __construct(
        private readonly string $filePath,
        private ?ContainerInterface $container = null
    ) {
        $this->analyze();
    }

    /**
     * Sets the dependency injection container
     *
     * @param ContainerInterface $container The container to be set
     * @return self Returns an instance of the class with the updated container
     */
    public function withContainer(ContainerInterface $container): self
    {
        $this->container = $container;
        return $this;
    }

    /**
     * Check if the object has any listener bindings.
     *
     * @return bool True if the object has listener bindings, false otherwise.
     */
    public function isListener(): bool
    {
        return !empty($this->listenerBindings);
    }

    /**
     * Returns the listener bindings of the Symfony application.
     *
     * @return array<array{source: string, event: string, method: string, priority: int}> The listener bindings array
     */
    public function bindings(): array
    {
        return $this->listenerBindings;
    }

    /**
     * Resolves the listeners for the Symfony application.
     *
     * @return array<array{event: string, listener: EventListener|CallableEventListener, priority: int}>
     *     The resolved listeners array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RuntimeException When the container is not available
     */
    public function resolveListeners(): array
    {
        if (!$this->container) {
            throw new RuntimeException("Container not available.");
        }

        $instance = $this->container->get($this->className);

        return array_map(function ($binding) use ($instance) {
            $method = $binding['method'];

            $listener = $instance instanceof EventListener
                ? $instance
                : new CallableEventListener(fn(object $event) => $instance->{$method}($event));

            return [
                'event' => $binding['event'],
                'listener' => $listener,
                'priority' => $binding['priority'],
            ];
        }, $this->listenerBindings);
    }

    public function className(): ?string
    {
        return $this->className;
    }

    public function filePath(): string
    {
        return $this->filePath;
    }

    public function __serialize(): array
    {
        return [
            'className' => $this->className,
            'listenerBindings' => $this->listenerBindings,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->listenerBindings = $data['listenerBindings'] ?? [];
        $this->className = $data['className'] ?? null;
    }

    private function analyze(): void
    {
        $className = $this->getClassFromFile($this->filePath);
        if (!$className || !class_exists($className)) {
            return;
        }

        $this->className = $className;
        $refClass = new ReflectionClass($className);

        // Class-level attributes
        $attributes = $refClass->getAttributes(AsEventListener::class, ReflectionAttribute::IS_INSTANCEOF);
        foreach ($attributes as $attr) {
            /** @var AsEventListener $instance */
            $instance = $attr->newInstance();
            $isInterface = $refClass->implementsInterface(EventListener::class);
            $this->listenerBindings[] = [
                'event' => $instance->event,
                'method' => $isInterface ? 'handle' : $instance->method ?? '__invoke',
                'priority' => $instance->priority ?? 0,
                'source' => $isInterface ? 'interface' : 'class'
            ];
        }

        // Method-level attributes
        foreach ($refClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            foreach ($method->getAttributes(AsEventListener::class, ReflectionAttribute::IS_INSTANCEOF) as $attr) {
                /** @var AsEventListener $instance */
                $instance = $attr->newInstance();
                $this->listenerBindings[] = [
                    'event' => $instance->event,
                    'method' => $method->getName(),
                    'priority' => $instance->priority ?? 0,
                    'source' => 'method'
                ];
            }

            // EventListener interface
            if (count($attributes) <= 0 && $refClass->implementsInterface(EventListener::class)) {
                $this->listenerBindings[] = [
                    'event' => $className,
                    'method' => 'handle',
                    'priority' => 0,
                    'source' => 'interface'
                ];
            }
        }
    }

    /**
     * Retrieves the class name from a PHP file based on its namespace declaration.
     *
     * @param string $filePath The path to the PHP file
     * @return string|null The fully qualified class name or null if class is not found
     */
    private function getClassFromFile(string $filePath): ?string
    {
        $src = file_get_contents($filePath);
        $tokens = token_get_all($src);

        $namespace = '';
        $class = '';

        for ($i = 0, $count = count($tokens); $i < $count; $i++) {
            $token = $tokens[$i];

            // Namespace (T_NAME_QUALIFIED)
            if (is_array($token) && $token[0] === T_NAMESPACE) {
                $i++;
                while (isset($tokens[$i]) && is_array($tokens[$i]) && $tokens[$i][0] === T_WHITESPACE) {
                    $i++;
                }
                if (isset($tokens[$i]) && is_array($tokens[$i]) && $tokens[$i][0] === T_NAME_QUALIFIED) {
                    $namespace = $tokens[$i][1];
                }
            }

            // Class name (after T_CLASS)
            if (is_array($token) && $token[0] === T_CLASS) {
                $i++;
                while (isset($tokens[$i]) && is_array($tokens[$i]) && $tokens[$i][0] === T_WHITESPACE) {
                    $i++;
                }
                if (isset($tokens[$i]) && is_array($tokens[$i]) && $tokens[$i][0] === T_STRING) {
                    $class = $tokens[$i][1];
                    break;
                }
            }
        }

        return $class ? ($namespace ? $namespace . '\\' . $class : $class) : null;
    }
}
