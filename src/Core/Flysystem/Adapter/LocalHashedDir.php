<?php

namespace Core\Flysystem\Adapter;

use League\Flysystem\Adapter\Local;

class LocalHashedDir extends Local
{
    public function applyPathPrefix($path)
    {
        $additionalPrefix = str_split(
            substr(
                sha1($path),
                0,
                3
            )
        );

        return parent::applyPathPrefix(
            implode('/', $additionalPrefix) . '/' . ltrim($path, '/')
        );
    }
}
