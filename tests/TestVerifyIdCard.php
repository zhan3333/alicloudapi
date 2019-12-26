<?php


namespace Tests;


use Zhan3333\AliCloudApi\AliVerifyIdCardService;

class TestVerifyIdCard extends TestCase
{
    public function testVerifyIdCard()
    {
        $service = app(AliVerifyIdCardService::class);
        $result = $service->verify('詹光', '420222199212041057');
        $this->assertEquals(true, $result['valid']);
        $result = $service->verify('詹光', '420222199212041056');
        $this->assertEquals(false, $result['valid']);
    }
}
