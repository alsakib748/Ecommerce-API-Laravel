<?php 

namespace App\Helper;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Monolog\Handler\NullHandler;

class JWTToken{

    public static function CreateToken($userEmail,$userID): string{
    
       $key = env('JWT_KEY');

       $payload = [
           'iss' => 'laravel-token',
           'iat' => time(),
           'exp' => time()+60*60,
           'userEmail' => $userEmail,
           'userID' => $userID
       ];
 
       return JWT::encode($payload,$key,'HS256');

    }

    public static function ReadToken($token):string|object{
        try{
            if($token==null){
                return 'unauthorized';
            }
            else{
                $key = env('JWT_KEY');
                return JWT::decode($token,new Key($key,'HS256'));
            }
        }
        catch(Exception $e){
            return 'unauthorized';
        }
    }

}