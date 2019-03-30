<?php
/**
 * dev 不会打包
 * dev 存在时会覆盖原有的配置
 */
return array(
    'is_debug' => false,
    'path_root' => realpath(__DIR__ . '/../'),
    'app_name' => 'Application',
    'admin_uid' => 1,
    'io_secret' => '96BKT0GFG7ddWInuWhn3psdE6D9EQGlwRvy19liyLpwsB7wN92zZNoE356naWsqpPaZncE7c8Y8MYWUQC768mRXCnItU1eaxiDbj',
    'crypto_key' => 'HXas12Vx',
    'timezone' => 'PRC',
    'db' => array(
        'default' => array(
            'type' => 'mysql',
            'host' => '127.0.0.1',
            'name' => 'hunzsig',
            'user' => 'root',
            'pwd' => '123456',
            'port' => '3306',
            'charset' => 'utf8mb4',
        ),
    ),
    'redis' => array(
        'project_name' => 'HZS_V_1',
        'active' => true,
        'host' => '127.0.0.1',
        'port' => '6379',
    ),
    // todo cover
    'dev' => array(
        'is_debug' => false,
        'db' => array(
            'default' => array(
                'type' => 'mysql',
                'host' => '127.0.0.1',
                'name' => 'hunzsig',
                'user' => 'root',
                'pwd' => '123456',
                'port' => '3306',
                'charset' => 'utf8mb4',
            ),
        ),
        'package_key' => 'Zc7VNh23yusFxus0',
        'package_iv' => '658as2weG56dH1sa',
    ),
);