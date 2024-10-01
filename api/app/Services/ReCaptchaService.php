<?php
// App/Services/ReCaptchaService.php
namespace App\Services;

use GuzzleHttp\Client;

class ReCaptchaService
{
    private string $secretKey;
    private Client $httpClient;

    public function __construct(string $secretKey)
    {
        if (empty($secretKey)) {
            throw new \Exception('RECAPTCHA_SECRET_KEY is not set in the environment variables.');
        }
        $this->secretKey = $secretKey;
        $this->httpClient = new Client();
    }

    public function verifyToken(string $token, string $remoteIp = null): bool
    {
        $response = $this->httpClient->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => $this->secretKey,
                'response' => $token,
                'remoteip' => $remoteIp,
            ],
        ]);

        $body = json_decode($response->getBody(), true);
        return $body['success'] ?? false;
    }
}
