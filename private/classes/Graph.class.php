<?php

class Graph
{
    public $token;

    protected $expiration;

    public function __construct()
    {
        $this->get_token();
    }

    private function initialize()
    {
        $url = 'https://login.microsoftonline.com/57368c21-b8cf-42cf-bd0b-43ecd4bc62ae/oauth2/v2.0/token';

        $body = http_build_query([
            'grant_type' => 'client_credentials',
            'scope' => 'https://graph.microsoft.com/.default',
            'client_id' => '402f295b-eacf-46ae-97be-f5e564641bc3',
            'client_secret' => 'j5r[Gh6-dy6lyDbmHDfk/anIxH.SZuh5',
        ]);

        $headers =  ['content-type' => 'application/x-www-form-urlencoded'];

        $client = new \GuzzleHttp\Client(compact('headers'));

        $response = $client->post($url , compact('body'));

        $decoded = json_decode($response->getBody(), true);

        $this->token = $decoded["access_token"];
        $this->expiration = now()->addSeconds($decoded["expires_in"]);
    }

    protected function get_token()
    {
        if( now()->greaterThan($this->expiration)) {
            $this->initialize();
        }
        return $this->token;
    }

    public function send_email($recipient, $subject, $body)
    {
        $url = "https://graph.microsoft.com/v1.0/users/beryl@cargill.com/sendMail";

        $body = [
            "message" => [
                "subject" => $subject,
                "body" => [
                    "contentType" => "HTML",
                    "content" => $body,
                ],
                "toRecipients" => [
                    [
                        "emailAddress" => [
                            "address" => $recipient
                        ]
                    ]
                ]
            ]
        ];

        $body = json_encode($body);

        $headers =  [
            'Authorization' => "Bearer $this->token",
            'content-type' => 'application/json'
        ];

        $client = new \GuzzleHttp\Client(compact('headers'));

        $response = $client->post($url , compact('body'));

        return $response->getStatusCode();
    }
}
