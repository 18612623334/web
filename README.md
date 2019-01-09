laravel5.5 Component_login
### 通过Composer安装包。
#### API
#### 从终端运行Composer update命令：
```
"wangliang/laravel-web":"^v1.0"
```
#### 在config/app  providers数组中添加一个新行：
```
Wangliang\Web\TestServiceProvider::class,
```
#### 从终端运行发布服务 命令：
```
php artisan vendor:publish --  
```
#### 运行数据库迁移
```
php artisan migrate (先删除框架自带的user数据迁移文件)(关掉laravel config/database 下的mysql 严格模式 strict:false)
```
#### 在 app/Providers/RouteServiceProvider 修改路由
##### mapWebRoutes(方法)
```
foreach (glob(base_path('routes/Web') . '/*.php') as $file) {
    Route::middleware('web')
        ->namespace($this->namespace)
        ->group($file);
}
```

#### 配置文件 config/auth.php 
```
'web' => [
    'driver' => 'session',
    'provider' => 'users',
],
'users' => [
    'driver' => 'eloquent',
    'model' => App\Models\Api\User::class,
],
```
OK 按照步骤走下来  项目已经基本上跑通  如果跑不通  去百度上  好好学习一下
