<?php namespace Modules\User\Entities\Sentinel;

use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Support\Facades\Config;
use Laracasts\Presenter\PresentableTrait;
use Modules\User\Entities\UserInterface;

class User extends EloquentUser implements UserInterface
{
    use PresentableTrait;

    protected $fillable = [
        'email',
        'password',
        'permissions',
        'first_name',
        'last_name',
    ];

    protected $presenter = 'Modules\User\Presenters\UserPresenter';

    public function hasRole($roleId)
    {
        return $this->roles()->whereId($roleId)->count() >= 1;
    }

    public function __call($method, $parameters)
    {
        $class_name = class_basename($this);

        #i: Convert array to dot notation
        $config = implode('.', ['relations', $class_name, $method]);

        #i: Relation method resolver
        if (Config::has($config)) {
            $function = Config::get($config);
            return $function($this);
        }

        #i: No relation found, return the call to parent (Eloquent) to handle it.
        return parent::__call($method, $parameters);
    }
}
