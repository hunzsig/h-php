##更新日志 <a href="https://github.com/hunzsig/h-php" target="_blank">GitHub</a>
###2019/04/03
    修复了db核心的break warning
###2019/03/30
    优化了打包，增加了-c参数指定config路径
    现在打包的dist目录会生成在config中设定的path_root处
###2019/03/23
    补回了config自定义的方式
    你可以在根目录构建一个config.php
    然后于index.php中宏定义：
    <?php
        define('CONFIG_PATH', __DIR__ . '/config.php');
        $hphpPath = realpath(__DIR__ . '/h-php');
        require __DIR__ . "/h-php/hHttp.php";
        $hphp = new Main();
        $hphp->run();
    ?>
###2019/03/21
    重构了external，在hhttp模式下才支持external
    重构了config获取的流程:
    你可以在根目录构建一个index.php
    <?php
        $hphpPath = realpath(__DIR__ . '/h-php');
        require __DIR__ . "/h-php/hHttp.php";
        $hphp = new Main();
        // 访问 http://127.0.0.1:port/external/helloWorld
        $hphp->external('helloWorld', __DIR__ . '/myExternal/helloWorld.php');
        $hphp->external('sql', __DIR__ . '/myExternal/sql.php');
        $hphp->run();
    ?>
###2019/03/11
    修复了factoryData在数据为false时出错的问题
###2019/02/24
    修复了Mysql核心，toMyArray方法会令数组数据丢失的问题
###2019/02/12
    system_tips_translate 更名为 system_tips_i18n
###2019/02/01
    swoole对CYGWIN/linux环境做出区分
###2019/01/31
    优化错误提示国际化
    修复了在CYGWIN环境中，没有判断为linux环境的bug
###2019/01/30
    新增windows打包语音提示
###2019/01/16
    mysql数据库核心增加两种搜索方式：containsAnd/notContainsAnd
    与contains/notContains的区别在于，每项间是and条件而不是or
    删除了mysql核心库里面的残留pg格式强制转换
###2019/01/09
    数据库核心增加加密参数
###2018/12/19
    修复了所有数据库核心在groupby时分页错误的bug
    去除了setIsUsedRedis的相关设定，改为set redis的type，可以设forever(默认)、disabled、和一个数字
    当setRedisType是一个数字时，代表自动缓存X秒，不会被自动clear掉（主动一样会清理）
###2018/12/18
    打包修正
###2018/11/29
    修正了所有SQL between条件数值为空时，查询无数据的bug
###2018/11/28
    增加setUseRedis用于dbModel不使用数据缓存（但表结构是自动使用的，保持600秒过期）
    修正了MSSQL 0整数位缺失的问题
###2018/11/27
    新增MSSQL
###2018/11/20
    现在hPackage的加密key可以在config处自定义
    service不再内置，改为在外部自行编写，service内的文件可随意调用core的所有函数
###2018/11/19
    修复了where条件值为0时，会被忽略的bug
###2018/11/14
    MYSQL 重新加入套餐，并支持 array json
###2018/10/21
    打包优化，隐藏h-php默认结构，并对配置及核心部分进行阻扰加密
###2018/10/18
    现在打包同时支持网页访问版和终端版，终端版会显示详情路径
    现在打包会自动对应用层加密
###2018/10/17
    修复了Pgsql不会自动转换字符串类型的问题
    修复了Redis在无key情况下可能会出错的bug
    优化了打包，现在不打包md文件
###2018/10/16
    新增打包器 hPackage.php，去除注释及压缩，含有example/svn/phpunit/test/uploads的会被忽略
###2018/10/15
    现在函数名包含__的全部视为内部方法，不能在IO调用
    新增了external 对外地址
###2018/10/12
    修复pgsql 表字段有时替换使得报错的bug
###2018/10/09
    支持array字段类型使用like
###2018/09/28
    新增固定资源路径 PATH_RESOURCE
    添加并定义了上传路径
    application 添加默认路径 /Application
    修复了websocket下无法获取到host的问题
###2018/09/27
    config现在支持自定义application目录路径
    library新增DataBase，新增一个函数redisClear($table),清空缓存
###2018/09/26
    - start -
