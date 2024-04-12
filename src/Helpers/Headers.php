<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Helpers;

use Generator;

final class Headers
{
    public const Eol = "\n";

    /**
     * @param array<array<string>> $headers
     *
     * @return Generator<int, string>
     */
    public static function toIterable(array $headers): Generator
    {
        $replace = 10;
        $suffix = str_repeat('.', $replace);
        foreach ($headers as $name => $values) {
            $value = implode(', ', $values);
            if (str_contains(strtolower($name), 'bearer')) {
                $value = substr($value, 0, -1 * $replace) . $suffix;
            }

            yield sprintf('%s: %s', $name, $value);
        }
    }
}
