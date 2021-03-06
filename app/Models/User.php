<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;   // 提供一些辅助函数，用于检查已认证用户的令牌和使用范围
    use Traits\LastActivedAtHelper; // 获取用户最后活跃时间
    use Traits\ActiveUserHelper; // 获取活动用户
    use HasRoles;        
    use Notifiable {
        notify as protected laravelNotify;
    }    

    public function notify($instance)
    {
        // 如果要通知的人是当前用户，就不必通知了！
        if ($this->id == Auth::id()) {
            return;
        }
        $this->increment('notification_count');
        $this->laravelNotify($instance);
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone', 'email', 'password', 'introduction', 'avatar','weixin_openid', 'weixin_unionid', 'registration_id', 'weixin_session_key', 'weapp_openid',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    //  一对多 的关系，一个用户拥有多个主题
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }
    // 一个用户可以拥有多条评论
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }
    // 将消息标记为已读
    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }
    // 用户表的 id 应当等于传入的模型的表的 user_id
    public function isAuthorOf($model)
    {
        return $this->id == $model->user_id;
    }
    // 定义一个密码修改器
    public function setPasswordAttribute($value)
    {
        // 如果值的长度等于 60，即认为是已经做过加密的情况
        if (strlen($value) != 60) {

            // 不等于 60，做密码加密处理
            $value = bcrypt($value);
        }

        $this->attributes['password'] = $value;
    }
    // 定义一个头像修改器
    public function setAvatarAttribute($path)
    {
        // 如果不是 `http` 子串开头，那就是从后台上传的，需要补全 URL
        if ( ! starts_with($path, 'http')) {

            // 拼接完整的 URL
            $path = config('app.url') . "/uploads/images/avatars/$path";
        }

        $this->attributes['avatar'] = $path;
    }
    // Rest omitted for brevity json web token
    // getJWTIdentifier 返回了 User 的 id
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    // getJWTCustomClaims 是我们需要额外在 JWT 载荷中增加的自定义内容，这里返回空数组
    public function getJWTCustomClaims()
    {
        return [];
    }
    // 支持手机登录
    public function findForPassport($username)
    {
        filter_var($username, FILTER_VALIDATE_EMAIL) ?
          $credentials['email'] = $username :
          $credentials['phone'] = $username;

        return self::where($credentials)->first();
    }
}
