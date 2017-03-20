<?php
namespace Application\Model;

use Exception;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Json;

class LoginUser
{
    /** @var User|null */
    private static $loggedUser = null;

    public static function login(array $params)
    {
        $credentials = Arrays::getValue($params, 'credentials', []);
        $login = Arrays::getValue($credentials, 'login');
        $password = Arrays::getValue($credentials, 'password');

        if ($login && $password) {
            self::$loggedUser = User::where([
                'login' => $login,
                'password' => $password
            ])->fetch();
        }
    }

    public static function ifLogged(callable $function)
    {
        if (LoginUser::isLogged()) {
            try {
                $result = Functions::call($function, self::$loggedUser);
                echo Json::encode(
                    ['success' => true] + ($result ?: [])
                );
            } catch (Exception $e) {
                echo Json::encode([
                    'success' => false,
                    'message' => 'Exception: ' . $e->getMessage()
                ]);
            }
        } else {
            echo Json::encode([
                'success' => false,
                'message' => 'Invalid login or password'
            ]);
        }
    }

    public static function isLogged()
    {
        return !is_null(self::$loggedUser);
    }

    public static function getLoggedUser()
    {
        return self::$loggedUser;
    }
}