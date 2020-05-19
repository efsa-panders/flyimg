<?php

namespace Core\StorageProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use WyriHaximus\SliFly\FlysystemServiceProvider;

class LocalHashedStorageProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Container $pimple)
    {
        $pimple->register(
            new FlysystemServiceProvider(),
            [
                'flysystem.filesystems' => [
                    'upload_dir' => [
                        'adapter' => 'Core\Flysystem\Adapter\LocalHashedDir',
                        'args' => [UPLOAD_DIR],
                    ],
                ],
            ]
        );

        $pimple['flysystems']['file_path_resolver'] = function () use ($pimple) {
            return function($path) use ($pimple) {
                $hostname = getenv('HOSTNAME_URL');

                if (empty($hostname)) {
                    $schema = $pimple['request_context']->getScheme();
                    $host = $pimple['request_context']->getHost();
                    $port = $pimple['request_context']->getHttpPort();
                    $hostname = $schema.'://'.$host.($port == '80' ? '' : ':'.$port);
                }

                $additionalPrefix = str_split(
                    substr(
                        sha1($path),
                        0,
                        3
                    )
                );

                $path = implode('/', $additionalPrefix) . '/' . ltrim($path, '/');

                return sprintf(
                    $hostname . '/' . UPLOAD_WEB_DIR . '%s',
                    $path
                );
            };
        };
    }
}
