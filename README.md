## 如何开始
### 你可以通过git下载：`git clone git@gitlab.com:h-web/h-php.git`
### 也可以通过composer：`composer install`
## 
### 建造一个index.php
##### 你可以在根目录构建一个index.php
###### 如果是composer你应该把 **/h-php/hHttp.php** 修改为 **/verdor/hunzsig/h-php/hHttp.php**
```php
<?php
    define('CONFIG_PATH', __DIR__ . '/config.php'); // 可宏定义一个配置文件路径，覆盖原有的config
    $hphpPath = realpath(__DIR__ . '/h-php');
    require __DIR__ . "/h-php/hHttp.php";
    $hphp = new Main();
    // 访问 http://127.0.0.1:port/external/helloWorld
    $hphp->external('helloWorld', __DIR__ . '/myExternal/helloWorld.php');
    $hphp->external('sql', __DIR__ . '/myExternal/sql.php');
    $hphp->run();
?>
```

### 打包
#### 打包会在设定的根目录下生成一个**dist**目录，加密混淆并压缩优化
##### 打包需要 **-c** 参数来定义你的**config文件**
##### 根目录情况下：
```php
php ./h-php/hPackage.php -c ./config.php
php ./h-php/hPackage.php -c ./h-php/hPhp.config.php // 默认情况下
php ./h-php/hPackage.php -c ./verdor/hunzsig/h-php/hPhp.config.php // 默认composer情况下
```
#####composer下：
```php
php ./verdor/hunzsig/h-php/hPackage.php -c ./config.php
```

### 测试接口地址
`
http://host/external/test
如果不想public接口暴露在scope列表中，函数包含“__”即可
如：edit__ 参考 User\Model\InfoModel
`

### Map展示地址
`
http://host/external/map
`
### 测试接口地址

### 你可以在index使用external方法，绑定自定义的php执行路径
`http://host/external/sql`

#### 搜索例子1
```sql
select * from "default".system_data where (data#>>'{project_name,name}')::text like '%系统%';
```
#### 搜索例子2
```sql
select * from "default".system_data where ("data"->'server_pre_alert_limit'->'value')::text::int > 5;
```

### 前端json搜索语法
`
{project_name,name} % #TT || (({project_name,name} % #系统 || {project_name,name} != #xxx) && {server_pre_alert_limit,value} > #1 && ({server_pre_alert_limit,value} > #0 || {server_pre_alert_limit,value} < #100000000))
`
```
[  n   ] isNull
         {?,?,?} n #
[  !n  ] isNotNull
         {?,?,?} !n #
[  %   ] like
         {?,?,?} % #???
[  !%  ] notLike
         {?,?,?} !% #???
[  =   ] equalTo
         {?,?,?} = #???
[  >   ] greaterThan
         {?,?,?} > #???
[  <   ] lessThan
         {?,?,?} < #???
[  >=  ] greaterThanOrEqualTo
         {?,?,?} >= #???
[  <=  ] lessThanOrEqualTo
         {?,?,?} <= #???
[  <> , != ] notEqualTo
         {?,?,?} <> #???
         {?,?,?} != #???
[  ><  ] between
         {?,?,?} >< #???,???
[  !>< ] notBetween
         {?,?,?} !>< #???,???
[  *   ] any
         {?,?,?} * #???,???,???...
[  ^   ] in
         {?,?,?} ^ #???,???,???...
[  !^  ] notIn
         {?,?,?} !^ #???,???,???...
```

### 后端sql模型讲解
##### 连贯写法 schemas table 必须在前，否则field等自动失效处理
##### select one update insert insertAll delete count 等作为终结语，后续连贯断开
```php
$this->db()->schemas('default')->table('system_data')
    ->field('key,value')
    ->in('key', $key)
    ->multi();
```
##### 字段闭包（默认）
##### 等于 ( "a" = 1 or "b" = 1 ) and( "c" = 1 or "d" = 1 or "e" = 1 )
```php
$this->db()->table('test')->equalTo('a',1)
    ->equalTo('b',1)
    ->closure('or')
    ->equalTo('c',1)
    ->equalTo('d',1)
    ->equalTo('e',1)
    ->closure('or');
```
##### 全局闭包
##### 等于 (( "a" = 1 or "b" = 1 ) or "c" = 1 or "d" = 1 or "e" = 1 ) 
```php
$this->db()->table('test')
    ->equalTo('a',1)
    ->equalTo('b',1)
    ->closure('or')
    ->equalTo('c',1)
    ->equalTo('d',1)
    ->equalTo('e',1)
    ->closure('or',true);
```
##### 直接插入bean写法（insert update delete需要try）
```php
try {
    $this->db()->schemas('default')->table('system_data')->insert($bean->toArray());
} catch (\Exception $e) {
    return $this->error($e->getMessage());
}
```

##### 插入可以手动获取lastID（在无序列表中自动获取可能会产生严重的错误）
```php
try {
    $this->db()->schemas('default')->table('system_data')->insert($bean->toArray());
    $lastId = $this->db()->lastInsertId();
} catch (\Exception $e) {
    return $this->error($e->getMessage());
}
```