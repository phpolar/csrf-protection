<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Tests\Stubs;

use Exception;
use Psr\Http\Message\StreamInterface;

final class MemoryStreamStub implements StreamInterface
{
    /**
     * @var resource $memoryStream
     */
    private $memoryStream;

    public function __construct($content = "", $mode = "+w")
    {
        $this->memoryStream = fopen("php://memory", $mode);
        $this->write($content);
        $this->rewind();
    }

    public function __toString()
    {
        return stream_get_contents($this->memoryStream);
    }

    public function close()
    {
        return fclose($this->memoryStream);
    }

    public function detach()
    {
        throw new Exception("Not Implemented");
    }

    public function eof()
    {
        return feof($this->memoryStream);
    }

    public function getContents()
    {
        return (string) $this;
    }

    public function getMetadata($key = null)
    {
        throw new Exception("Not Implemented");
    }

    public function getSize()
    {
        ["size" => $size] = fstat($this->memoryStream);
        return $size;
    }

    public function isReadable()
    {
        return true;
    }

    public function isSeekable()
    {
        ["seekable" => $isSeekable] = stream_get_meta_data($this->memoryStream);
        return $isSeekable;
    }

    public function isWritable()
    {
        return true;
    }

    public function read($length)
    {
        return fread($this->memoryStream, $length);
    }

    public function rewind()
    {
        return rewind($this->memoryStream);
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        return fseek($this->memoryStream, $offset, $whence);
    }

    public function tell()
    {
        return ftell($this->memoryStream);
    }

    public function write($string)
    {
        return fwrite($this->memoryStream, $string);
    }
}
