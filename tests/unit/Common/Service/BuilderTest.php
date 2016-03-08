<?php

namespace OpenCloud\Test\Common\Service;

use GuzzleHttp\ClientInterface;
use OpenCloud\Common\Service\Builder;
use OpenCloud\Identity\v2\Models\Token;
use OpenCloud\Identity\v2\Service as IdentityV2;
use OpenCloud\Identity\v3\Service as IdentityV3;
use OpenCloud\Compute\v2\Service as ComputeV2;
use OpenCloud\Test\Common\Auth\FakeToken;
use OpenCloud\Test\TestCase;
use Prophecy\Argument;

class BuilderTest extends TestCase
{
    private $builder;
    private $opts;

    public function setUp()
    {
        $this->builder = new Builder([]);

        $this->opts = [
            'username' => '1',
            'password' => '2',
            'tenantId' => '3',
            'authUrl' => '4',
            'region' => '5',
            'catalogName' => '6',
            'catalogType' => '7',
        ];
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_username_is_missing()
    {
        $this->builder->createService('Compute', 2, []);
    }

    /**
     * @expectedException \Throwable
     */
    public function test_it_throws_exception_if_password_is_missing()
    {
        $this->builder->createService('Compute', 2, ['username' => 1]);
    }

    /**
     * @expectedException \Throwable
     */
    public function test_it_throws_exception_if_both_tenantId_and_tenantName_is_missing()
    {
        $this->builder->createService('Compute', 2, [
            'username' => 1, 'password' => 2, 'authUrl' => 4, 'region' => 5, 'catalogName' => 6, 'catalogType' => 7,
        ]);
    }

    /**
     * @expectedException \Throwable
     */
    public function test_it_throws_exception_if_authUrl_is_missing()
    {
        $this->builder->createService('Compute', 2, ['username' => 1, 'password' => 2, 'tenantId' => 3]);
    }

    /**
     * @expectedException \Throwable
     */
    public function test_it_throws_exception_if_region_is_missing()
    {
        $this->builder->createService('Compute', 2, [
            'username' => 1, 'password' => 2, 'tenantId' => 3, 'authUrl' => 4,
        ]);
    }

    /**
     * @expectedException \Throwable
     */
    public function test_it_throws_exception_if_catalogName_is_missing()
    {
        $this->builder->createService('Compute', 2, [
            'username' => 1, 'password' => 2, 'tenantId' => 3, 'authUrl' => 4,
        ]);
    }

    /**
     * @expectedException \Throwable
     */
    public function test_it_throws_exception_if_catalogType_is_missing()
    {
        $this->builder->createService('Compute', 2, [
            'username' => 1, 'password' => 2, 'tenantId' => 3, 'authUrl' => 4, 'region' => 5, 'catalogName' => 6,
        ]);
    }

//    public function test_it_builds_services_with_custom_identity_service()
//    {
//        $this->rootFixturesDir = dirname(dirname(__DIR__)) . '/Identity/v2/';
//
//        $token = $this->prophesize(FakeToken::class)->reveal();
//        $service = $this->prophesize(IdentityService::class);
//        $service->authenticate(Argument::type('array'))->shouldBeCalled()->willReturn([$token, '']);
//
//        $this->opts += [
//            'identityService' => $service->reveal(),
//            'catalogName'     => 'nova',
//            'catalogType'     => 'compute',
//            'region'          => 'RegionOne',
//        ];
//
//        $service = $this->builder->createService('Compute', 2, $this->opts);
//        $this->assertInstanceOf(ComputeV2::class, $service);
//    }

    private function setupHttpClient()
    {
        $this->rootFixturesDir = dirname(dirname(__DIR__)) . '/Identity/v3/';

        $response = $this->getFixture('token-get');

        $expectedJson = [
            'auth' => [
                'identity' => [
                    'methods'  => ['password'],
                    'password' => ['user' => ['id' => '0ca8f6', 'password' => 'secretsecret']]
                ]
            ]
        ];

        $httpClient = $this->prophesize(ClientInterface::class);
        $httpClient->request('POST', 'tokens', ['json' => $expectedJson])->shouldBeCalled()->willReturn($response);

        return $httpClient;
    }

    public function it_builds_services_with_default_identity()
    {
        $httpClient = $this->setupHttpClient();

        $options = [
            'httpClient'  => $httpClient->reveal(),
            'catalogName' => 'nova',
            'catalogType' => 'compute',
            'region'      => 'RegionOne',
            'user'        => [
                'id'       => '0ca8f6',
                'password' => 'secretsecret',
            ]
        ];

        $service = $this->builder->createService('Compute', 2, $options);
        $this->assertInstanceOf(ComputeV2::class, $service);
    }

//    public function test_it_does_not_authenticate_when_creating_identity_services()
//    {
//        $this->assertInstanceOf(IdentityV3::class, $this->builder->createService('Identity', 3, [
//            'authUrl'    => 'foo.com',
//        ]));
//    }
}