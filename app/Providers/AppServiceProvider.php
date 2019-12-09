<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
	{
		\App\Models\User::observe(\App\Observers\UserObserver::class);
		\App\Models\Reply::observe(\App\Observers\ReplyObserver::class);
		\App\Models\Topic::observe(\App\Observers\TopicObserver::class);
        \App\Models\Link::observe(\App\Observers\LinkObserver::class);
        //

        \Carbon\Carbon::setLocale('zh');
        Horizon::auth(function ($request) {
            // 这里进行判断，根据需求自行选择

            // 通过认证可以访问
            if (Auth::check()) {
                return true;
            }

            // 指定参数可以访问
            if ($request->input('q') == 'horizon') {
                return true;
            }

        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (app()->isLocal()) {
            $this->app->register(\VIACreative\SudoSu\ServiceProvider::class);
        }
        // 手动处理 DingoApi 默认返回 500 异常 转为 404
        \API::error(function (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            abort(404);
        });
        // 手动处理 DingoApi 默认返回 500 用户权限异常 转为 403
        \API::error(function (\Illuminate\Auth\Access\AuthorizationException $exception) {
            abort(403, $exception->getMessage());
        });
    }
}
