<?php

namespace App\Services;

use Interop\Container\ContainerInterface;
use App\Utils\Validations as Validation;
use Respect\Validation\Validator as v;
use RedBeanPHP\R as R;
use App\Utils\RedBeanPHPExtensions as RE;

class AuthService
{
    private $jwtSettings;
    private $validator;

    public function __construct(ContainerInterface $container)
    {
        $this->jwtSettings = $container->get('settings')['jwt'];
        $this->validator = $container->get('validator');
    }

    public function getJwt(string $mail, string $password, bool $rememberMe)
    {
        $user = R::findOne('user', 'mail = ?', [$mail]);

        if(is_null($user))
        {
            throw new \Exception("Mail not registered", 404);
        }

        if(!filter_var($user->mail_confirmed, FILTER_VALIDATE_BOOLEAN)){ 
            throw new \Exception("Mail not confirmed", 403);
        }

        if(!password_verify($password, $user->password))
        {
            throw new \Exception("Credentials incorrect", 401);
        }

        return [
            'displayName' => $user->display_name,
            'jwt' => $this->issueJwt(['uid' => $user->id], $this->getTtl($rememberMe), $user->id, null)
        ];
    }

    public function extendJwtTtl($oldToken, $userId)
    {
        return [
            'jwt' => $this->issueJwt(json_decode(json_encode($oldToken['scope']), true), $oldToken['ttl'], $userId, $oldToken['id'])
        ];
    }

    public function getValidJwts(array $params, int $userId)
    {
        $userBean = RE::load('user', $userId);

        $jwtBeans = $userBean
            ->withCondition(' ( is_valid = true AND date_expire > :now ) LIMIT :limit OFFSET :offset ', [
                ':now' => date("Y-m-d H:i:s", date_timestamp_get(new \DateTime())),
                ':limit' => $params['limit'],
                ':offset' => $params['offset']
            ])
            ->ownJwtList;

        $result = [];

        foreach($jwtBeans as $jwtBean)
        {
            array_push($result, [
                'id' => $jwtBean->id,
                'dateCreated' => $jwtBean->date_created,
                'dateLastUsed' => $jwtBean->date_last_used
            ]);
        }

        return $result;
    }

    public function invalidateJwt(int $tokenId, int $userId, string $password)
    {
        if(!password_verify($password, R::load('user', $userId)->password))
        {
            throw new \Exception("Password incorrect", 401);
        }

        $jwtBean = R::load('jwt', $tokenId);

        if($jwtBean->id != $tokenId)
        {
            throw new \Exception("JWT not found", 404);
        }

        if($jwtBean->user_id != $userId)
        {
            throw new \Exception("JWT access denied", 403);
        }

        $jwtBean->is_valid = false;
        R::store($jwtBean);
    }

    private function issueJwt(array $scope, int $ttl, int $userId, $tokenId)
    {
        $dateExpire = new \DateTime();
        $dateExpire->add(new \DateInterval('PT' . $ttl . 'S'));

        if(is_null($tokenId))
        {
            $userBean = R::load('user', $userId);
            
            $tokenBean = R::dispense([
                '_type' => 'jwt',
                'is_valid' => true,
                'date_created' => new \DateTime(),
                'date_expire' => $dateExpire
            ]);

            $userBean->ownJwtList[] = $tokenBean;

            R::store($userBean);

            $tokenId = $tokenBean->id;
        }
        else
        {
            $tokenBean = R::load('jwt', $tokenId);

            if($tokenBean->id != $tokenId)
            {
                throw new \Exception("JWT not found", 404);
            }

            if($tokenBean->user_id != $userId)
            {
                throw new \Exception("JWT access denied", 403);
            }

            $tokenBean->date_expire = $dateExpire;
            $tokenId = R::store($tokenBean);
        }

        return $this->generateJwt($tokenId, $scope, $ttl);
    }

    private function generateJwt(int $tokenId, array $scope, int $ttl)
    {
        $header_json = json_encode([
            'alg' => 'HS512',
            'typ' => 'JWT'
        ]);

        $header_base64url = $this->urlsafeB64Encode($header_json);

        $payload_json = json_encode([
            'id' => $tokenId,
            'scope' => $scope,
            'iat' => time(),
            'exp' => time() + $ttl,
            'ttl' => $ttl
        ]);

        $payload_base64url = $this->urlsafeB64Encode($payload_json);

        $signature = hash_hmac(
            'SHA512',
            $header_base64url . '.' . $payload_base64url,
            $this->jwtSettings['secret'],
            true
        );

        $signature_base64url = $this->urlsafeB64Encode($signature);
        
        return $header_base64url . '.' . $payload_base64url . '.' . $signature_base64url;
    }

    private function urlsafeB64Encode($input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    private function getTtl(bool $rememberMe)
    {
        if($rememberMe)
        {
            return $this->jwtSettings['ttl'];
        }
        
        return $this->jwtSettings['ttlShort'];
    }
}