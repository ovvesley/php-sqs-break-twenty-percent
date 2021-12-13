<?php

namespace App\Service;
use DOMDocument;
use DOMElement;

class EncontrarResposta {
    public function __construct()
    {

    }
    public function handle(): string
    {
        return $this->index();
    }

    private function getUrl(): string
    {
        $url = "http://applicant-test.us-east-1.elasticbeanstalk.com/";
        return $url;
    }

    private function getCookiesFromResponse($http_response_header)
    {
        $cookies = null;
        if (preg_match_all('/Set-Cookie:[\s]([^;]+)/', $http_response_header[3], $matches)) {
            $cookies = $matches[1][0];
        }

        return $cookies;
    }

    private function getTokenInitialPage($page)
    {
        $tokenId = 'token';
        $tokenElement = $page->getElementById($tokenId);
        $tokenValue = $tokenElement->getAttribute('value');
        return $tokenValue;
    }

    private function replacementToken($token)
    {
        $newTokenValue = null;
        $replacement = json_decode('{"0":"9","1":"8","2":"7","3":"6","4":"5","5":"4","6":"3","7":"2","8":"1","9":"0","a":"z","b":"y","c":"x","d":"w","e":"v","f":"u","g":"t","h":"s","i":"r","j":"q","k":"p","l":"o","m":"n","n":"m","o":"l","p":"k","q":"j","r":"i","s":"h","t":"g","u":"f","v":"e","w":"d","x":"c","y":"b","z":"a"}', true);

        for ($i = 0; $i < strlen($token); $i++) {
            $newTokenValue .= $replacement[$token[$i]];
        }
        return $newTokenValue;
    }

    private function handleDataToRequest($data)
    {
        return http_build_query($data);
    }


    private function handleInitialPage()
    {

        $urlInitialPage = $this->getUrl();

        $rawHtmlInitialPage = file_get_contents($urlInitialPage);
        $cookies = $this->getCookiesFromResponse($http_response_header);

        $pageHtmlInitial = new DOMDocument();
        $pageHtmlInitial->loadHTML($rawHtmlInitialPage);

        $token  = $this->getTokenInitialPage($pageHtmlInitial);

        return [
            'token' => $token,
            'cookies' => $cookies
        ];
    }

    private function handleAnswerResponse($rawHtml)
    {
        $pageHtmlInitial = new DOMDocument();
        $pageHtmlInitial->loadHTML($rawHtml);

        $answer = $pageHtmlInitial->getElementById("answer")->textContent;

        return $answer;
    }

    private function handleAnswerPage($token, $cookies)
    {
        $url = $this->getUrl();

        $data = $this->handleDataToRequest(['token' => $token]);

        error_log($token);
        $response = file_get_contents($url, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header'  => [
                    "Cookie: {$cookies}",
                    'Referer: http://applicant-test.us-east-1.elasticbeanstalk.com/',
                    "Content-Type: application/x-www-form-urlencoded",
                ],
                'content' => $data,
            ]
        ]));

        $answer = $this->handleAnswerResponse($response);

        return $answer;
    }


    private function index(): string
    {

        $initialPage = $this->handleInitialPage();

        $tokenInitialPage = $initialPage['token'];
        $cookies = $initialPage['cookies'];

        $newToken = $this->replacementToken($tokenInitialPage);

        $answer = $this->handleAnswerPage($newToken, $cookies);

        return $answer;
    }


}
