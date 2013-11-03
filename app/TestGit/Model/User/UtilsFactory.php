<?php
namespace TestGit\Model\User;

use Fwk\Security\Password\Generator;
use Fwk\Security\Authentication\Adapters\PasswordAdapter;
use Fwk\Security\User\Provider;

class UtilsFactory
{
    public static function newPasswordGenerator()
    {
        $generator = new Generator('pbkdf2', array(
            'outputSize' => 256
        ));
        
        return $generator;
    }
    
    public static function newSaltClosure()
    {
        return function(User $user) {
            return sha256(strrev(strtolower($user->getUsername())));
        };
    }
    
    public static function newLoginFormAdapter($username, $password, 
        Provider $provider
    ) {
        return new PasswordAdapter(
            $username, 
            $password, 
            self::newPasswordGenerator(), 
            $provider, 
            self::newSaltClosure()
        );
    }
}