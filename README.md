##测试接口地址
###http://xxxx/test/index.php

```
如果不想public接口暴露在scope列表中，函数包含“__”即可
如：edit__ 参考 User\Model\InfoModel
```

###Map展示地址
```
http://127.0.0.1:xx/test/map.php
```

###生成默认准备数据地址
```
http://127.0.0.1:xx/test/sql.php
```

#####搜索例子
```
select * from "default".system_data where (data#>>'{project_name,name}')::text like '%系统%';
select * from "default".system_data where ("data"->'server_pre_alert_limit'->'value')::text::int > 5;
```

###前端json搜索语法
```
{project_name,name} % #TT || (({project_name,name} % #系统 || {project_name,name} != #xxx) && {server_pre_alert_limit,value} > #1 && ({server_pre_alert_limit,value} > #0 || {server_pre_alert_limit,value} < #100000000))
```
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

###后端sql模型讲解
> ##### 连贯写法 schemas table 必须在前，否则field等自动失效处理
> ##### select one update insert insertAll delete count 等作为终结语，后续连贯断开
```
$this->db()->schemas('default')->table('system_data')
    ->field('key,value')
    ->in('key', $key)
    ->multi();
```
> ##### 字段闭包（默认）
> ##### 等于 ( "a" = 1 or "b" = 1 ) and( "c" = 1 or "d" = 1 or "e" = 1 )
```
...
->equalTo('a',1)
->equalTo('b',1)
->closure('or')
->equalTo('c',1)
->equalTo('d',1)
->equalTo('e',1)
->closure('or')
...
```
> ##### 全局闭包
> ##### 等于 (( "a" = 1 or "b" = 1 ) or "c" = 1 or "d" = 1 or "e" = 1 ) 
```
...
->equalTo('a',1)
->equalTo('b',1)
->closure('or')
->equalTo('c',1)
->equalTo('d',1)
->equalTo('e',1)
->closure('or',true)
...
```
> ##### 直接插入bean写法（insert update delete需要try）
```
try {
    $this->db()->schemas('default')->table('system_data')->insert($bean->toArray());
} catch (\Exception $e) {
    return $this->error($e->getMessage());
}
```

> ##### 插入不会自动返回lastID（在无序列表中会产生严重的错误）
> ##### 需要手动获取lastID
```
try {
    $this->db()->schemas('default')->table('system_data')->insert($bean->toArray());
    $lastId = $this->db()->lastInsertId();
} catch (\Exception $e) {
    return $this->error($e->getMessage());
}
```