<?php


namespace Zhan3333\AliCloudApi;


use GuzzleHttp\Client;
use Illuminate\Log\Logger;
use Log;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class AliVerifyIdCardService
{
    public const REQUEST_FAIL = '请求失败';
    public const MISMATCH = '不匹配';
    private const HOST = 'https://idenauthen.market.alicloudapi.com';
    private const PATH = '/idenAuthentication';

    /**
     * @var Logger $logging
     */
    private $logging;

    /** @var Client $client */
    private $client;
    /** @var string */
    private $appCode;
    /**
     * none, success, failed
     * @var string
     */
    private $fakeType;

    public function __construct($appCode, $logChannel = 'single', $fakeType = 'none')
    {
        $this->appCode = $appCode;
        $this->fakeType = $fakeType;
        $this->client = new Client([
            'base_uri' => self::HOST,
            'http_errors' => false,
        ]);
        if ($logChannel) {
            $this->logging = Log::channel($logChannel);
        }

    }

    /**
     * 发起验证
     *
     * ## example
     *     - name       姓名
     *     - idCard     身份证号
     *
     * ## result
     *     - valid: bool 是否验证通过
     *     - response       http返回结果
     *     - reason         原因
     * @param string $name
     * @param string $idCard
     * @return array
     */
    public function verify(string $name, string $idCard)
    {
        if (!is_string($name) || !is_string($idCard)) {
            $this->exception('name or idCard must be string');
        }
        if ($this->fakeType === 'success') {
            $result = $this->fakeVerifySuccess();
        } elseif ($this->fakeType === 'failed') {
            $result = $this->fakeVerifyFailed();
        } else {
            $result = $this->appCodeVerify($name, $idCard);
        }
        $this->log($name, $idCard, $result);
        return $result;
    }

    private function fakeVerifySuccess()
    {
        return [
            'valid' => true,
            'response' => null,
            'reason' => 'ok'
        ];
    }

    private function fakeVerifyFailed()
    {
        return [
            'valid' => false,
            'response' => null,
            'reason' => 'fake failed'
        ];
    }

    private function appCodeVerify($name, $idCard)
    {
        $response = $this->client->post(self::PATH, [
            'headers' => [
                'Authorization' => "APPCODE {$this->appCode}",
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
            ],
            'form_params' => [
                'idNo' => $idCard,
                'name' => $name,
            ],
        ]);
        $body = json_decode($response->getBody(), true);
        // 请求失败
        if (!$this->httpIsOK($response->getStatusCode()) || !$this->hasCheckCode($body)) {
            return [
                'valid' => false,
                'reason' => $this->getHttpFailMessage($response),
                'response' => $body,
            ];
        }

        $isOk = $this->checkCodeIsOK($body);
        return [
            'valid' => $isOk,
            'response' => $body,
            'reason' => $isOk ? 'ok' : $this->getCheckFailMessage($body),
        ];
    }

    private function httpIsOK($statusCode)
    {
        return $statusCode === 200;
    }

    private function hasCheckCode($body)
    {
        $respCode = $body['respCode'] ?? null;
        return $respCode !== null;
    }

    private function checkCodeIsOK($body)
    {
        $respCode = $body['respCode'] ?? null;
        return $respCode === '0000';
    }

    private function getCheckFailMessage($body)
    {
        return $body['respMessage'] ?? '系统错误';
    }

    private function getHttpFailMessage(ResponseInterface $response)
    {
        $status = $response->getStatusCode();
        if ($status !== 200) {
            return "请求状态为 $status";
        }
        return '';
    }

    private function log($name, $idCard, $data)
    {
        if ($this->logging) {
            $this->logging->info("ali:verify_id_card:{$name}:{$idCard}", [$data]);
        }
    }

    private function exception($message)
    {
        throw new RuntimeException(__CLASS__ . ': ' . 'verify_id_card' . ':' . $message);
    }
}
