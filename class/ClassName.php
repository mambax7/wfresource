<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

/**
 * Copyright (c) Laudir Bispo  (laudirbispo@outlook.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     (c) Laudir Bispo  (laudirbispo@outlook.com)
 * @license           https://opensource.org/licenses/mit-license.php MIT License
 * @version           1.2.1
 */
final class ClassName
{
    /**
     * Full name the class
     * @param object|string $object
     * @return string
     */
    public static function full($object): ?string
    {
        if (\is_string($object)) {
            return \str_replace('.', '\\', $object);
        }

        if (\is_object($object)) {
            return \trim(\get_class($object), '\\');
        }

        throw new \InvalidArgumentException(
            \sprintf('[%s]: Esperavamos um objeto ou uma string, recebemos um(a) %s.', __CLASS__, \gettype($object))
        );
    }

    public static function namespace($object): string
    {
        if (null === $object || empty($object)) {
            throw new \InvalidArgumentException(
                \sprintf('[%s]: Esperavamos um objeto ou uma string, recebemos um(a) %s.', __CLASS__, \gettype($object))
            );
        }

        $parts = \explode('\\', self::full($object));
        \array_pop($parts);

        return \implode('\\', $parts);
    }

    /**
     * Canonical class name of an object, of the form "My.Namespace.MyClass"
     * @param object|string $object
     */
    public static function canonical($object): string
    {
        if (null === $object || empty($object)) {
            throw new \InvalidArgumentException(
                \sprintf('[%s]: Esperavamos um objeto ou uma string, recebemos um(a) %s.', __CLASS__, \gettype($object))
            );
        }

        return \str_replace('\\', '.', self::full($object));
    }

    /**
     * @param $object
     * @return false|mixed|string
     */
    public static function short($object)
    {
        if (null === $object || empty($object)) {
            throw new \InvalidArgumentException(
                \sprintf('[%s]: Esperavamos um objeto ou uma string, recebemos um(a) %s.', __CLASS__, \gettype($object))
            );
        }

        $parts = \explode('\\', self::full($object));

        return \end($parts);
    }

    /**
     * Path to class file "namespace1/namespace2/MyClass"
     * @param object|string $object
     */
    public static function path($object): string
    {
        if (null === $object || empty($object)) {
            throw new \InvalidArgumentException(
                \sprintf('[%s]: Esperavamos um objeto ou uma string, recebemos um(a) %s.', __CLASS__, \gettype($object))
            );
        }

        return \str_replace('\\', '/', self::full($object));
    }

    /**
     * Retrieves the name of the parent class for object or class
     * @param object|string $object
     */
    public static function getParent($object, string $returns = 'full'): ?string
    {
        if (null === $object || empty($object)) {
            throw new \InvalidArgumentException(
                \sprintf('[%s]: Esperavamos um objeto ou uma string, recebemos um(a) %s.', __CLASS__, \gettype($object))
            );
        }

        $parent = \get_parent_class($object);
        if (!$parent) {
            return null;
        }

        $parent = trim($parent, '\\');

        if ('full' === $returns) {
            return self::full($parent);
        }

        if ('short' === $returns) {
            return self::short($parent);
        } elseif ('canonical' === $returns) {
            return self::canonical($parent);
        } elseif ('namespace' === $returns) {
            return self::namespace($object);
        }

        return $parent;
    }
}
