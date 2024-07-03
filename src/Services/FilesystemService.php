<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Services;

use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;

final class FilesystemService implements Filesystem
{
    public function __construct(
        private readonly string $dir,
    ) {
    }

    public function path($path)
    {
        return $this->dir . $path;
    }

    public function makeDirectory($path)
    {
        return @mkdir($this->path($path), 0o755, true);
    }

    public function exists($path)
    {
        throw new Exception('not implemented');
    }

    public function get($path)
    {
        throw new Exception('not implemented');
    }

    public function readStream($path)
    {
        throw new Exception('not implemented');
    }

    /**
     * @param mixed $path
     * @param mixed $contents
     * @param mixed $options
     */
    public function put($path, $contents, $options = [])
    {
        throw new Exception('not implemented');
    }

    /**
     * @param mixed $path
     * @param mixed $file
     * @param mixed $options
     */
    public function putFile($path, $file = null, $options = [])
    {
        throw new Exception('not implemented');
    }

    /**
     * @param mixed $path
     * @param mixed $file
     * @param mixed $name
     * @param mixed $options
     */
    public function putFileAs($path, $file, $name = null, $options = [])
    {
        throw new Exception('not implemented');
    }

    /**
     * @param array<mixed> $options
     */
    public function writeStream($path, $resource, array $options = [])
    {
        throw new Exception('not implemented');
    }

    public function getVisibility($path)
    {
        throw new Exception('not implemented');
    }

    public function setVisibility($path, $visibility)
    {
        throw new Exception('not implemented');
    }

    public function prepend($path, $data)
    {
        throw new Exception('not implemented');
    }

    public function append($path, $data)
    {
        throw new Exception('not implemented');
    }

    /**
     * @param mixed $paths
     */
    public function delete($paths)
    {
        throw new Exception('not implemented');
    }

    public function copy($from, $to)
    {
        throw new Exception('not implemented');
    }

    public function move($from, $to)
    {
        throw new Exception('not implemented');
    }

    public function size($path)
    {
        throw new Exception('not implemented');
    }

    public function lastModified($path)
    {
        throw new Exception('not implemented');
    }

    /**
     * @return array<mixed>
     */
    public function files($directory = null, $recursive = false)
    {
        throw new Exception('not implemented');
    }

    /**
     * @return array<mixed>
     */
    public function allFiles($directory = null)
    {
        throw new Exception('not implemented');
    }

    /**
     * @return array<mixed>
     */
    public function directories($directory = null, $recursive = false)
    {
        throw new Exception('not implemented');
    }

    /**
     * @return array<mixed>
     */
    public function allDirectories($directory = null)
    {
        throw new Exception('not implemented');
    }

    public function deleteDirectory($directory)
    {
        throw new Exception('not implemented');
    }
}
