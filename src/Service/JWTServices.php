<?php

namespace App\Service;

use DateTimeImmutable;



class JWTServices
{
    public function generate(array $header, array $paylodad, string $secret, int $validity = 18000): string
    {
        if ($validity > 0) {
            $now = new DateTimeImmutable();
            $exp = $now->getTimestamp() + $validity;

            $paylodad['iat'] = $now->getTimestamp();
            $paylodad['exp'] = $exp;
        }



        // on encode en base64
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($paylodad));

        // on nettoie les valeurs encodées (retrait des + ,/ et =)
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);

        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);
        //On génere la signature 
        $secret = base64_encode($secret);

        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);

        $base64signature = base64_encode($signature);

        $base64signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64signature);

        //on cree le token

        $jwt = $base64Header . '.' . $base64Payload . '.' . $base64signature;



        return $jwt;
    }

    public function isValid(string $token): bool
    {
        return preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token
        ) === 1;
    }
    // on recupere  le payload 
    public function getPayload(string $token): array
    {
        // on demonte le token
        $array = explode('.', $token);
        // on decode le payload 
        $payload = json_decode(base64_decode($array[1]), true);


        return $payload;

    }
    public function getHeader(string $token): array
    {
        // on demonte le token
        $array = explode('.', $token);
        // on decode le payload 
        $header = json_decode(base64_decode($array[0]), true);


        return $header;

    }

    // on verifie si le token a expirer 

    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);
        $now = new DateTimeImmutable();
        return $payload['exp'] < $now->getTimestamp();
    }

    // on verifie la signature du token 

    public function check(string $token, string $secret)
    {

        // on recupere le header et le payload 
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);
        // on regenerer 

        $verifToken = $this->generate($header, $payload, $secret, 0);

        return $token === $verifToken;

    }


}