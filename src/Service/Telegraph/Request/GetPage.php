<?php

namespace Ig0rbm\Memo\Service\Telegraph\Request;

class GetPage extends BaseRequest
{
    /** @var string */
    protected $path;

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }
}
