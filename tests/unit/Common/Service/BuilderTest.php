<?php

namespace OpenStack\Test\Common\Service;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Event\Emitter;
use GuzzleHttp\Message\Request;
use OpenStack\Common\Service\Builder;
use OpenStack\Identity\v2\Models\Token;
use OpenStack\Identity\v2\Service;
use OpenStack\Test\TestCase;
use Prophecy\Argument;

class BuilderTest extends TestCase
{
    private $builder;
    private $opts;

    function setUp()
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
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_password_is_missing()
    {
        $this->builder->createService('Compute', 2, ['username' => 1]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_both_tenantId_and_tenantName_is_missing()
    {
        $this->builder->createService('Compute', 2, [
            'username' => 1, 'password' => 2, 'authUrl' => 4, 'region' => 5, 'catalogName' => 6, 'catalogType' => 7,
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_authUrl_is_missing()
    {
        $this->builder->createService('Compute', 2, ['username' => 1, 'password' => 2, 'tenantId' => 3]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_region_is_missing()
    {
        $this->builder->createService('Compute', 2, [
            'username' => 1, 'password' => 2, 'tenantId' => 3, 'authUrl' => 4,
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_catalogName_is_missing()
    {
        $this->builder->createService('Compute', 2, [
            'username' => 1, 'password' => 2, 'tenantId' => 3, 'authUrl' => 4,
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_catalogType_is_missing()
    {
        $this->builder->createService('Compute', 2, [
            'username' => 1, 'password' => 2, 'tenantId' => 3, 'authUrl' => 4, 'region' => 5, 'catalogName' => 6,
        ]);
    }

    public function test_it_builds_services_with_v2_identity()
    {
        $this->rootFixturesDir = dirname(dirname(__DIR__)) . '/Identity/v2/';

        $token = $this->prophesize(Token::class)->reveal();
        $service = $this->prophesize(Service::class);
        $service->authenticate(Argument::type('array'))->shouldBeCalled()->willReturn([$token, '']);

        $this->opts['identityService'] = $service->reveal();
        $this->opts['catalogName'] = 'nova';
        $this->opts['catalogType'] = 'compute';
        $this->opts['region'] = 'RegionOne';

        $service = $this->builder->createService('Compute', 2, $this->opts);
        $this->assertInstanceOf('OpenStack\Compute\v2\Service', $service);
    }

    public function it_builds_services_with_v3_identity()
    {
        $this->rootFixturesDir = dirname(dirname(__DIR__)) . '/Identity/v3/';

        $response = $this->getFixture('token-get');
        $request  = new Request('POST', 'tokens');

        $expectedJson = [
            'auth' => [
                'identity' => [
                    'methods'  => ['password'],
                    'password' => ['user' => ['id' => '0ca8f6', 'password' => 'secretsecret']]
                ]
            ]
        ];

        $httpClient = $this->prophesize(ClientInterface::class);
        $httpClient->getEmitter()->willReturn(new Emitter());
        $httpClient->createRequest('POST', 'tokens', ['json' => $expectedJson])->shouldBeCalled()->willReturn($request);
        $httpClient->send($request)->shouldBeCalled()->willReturn($response);

        $options = [
            'httpClient' => $httpClient->reveal(),
            'catalogName' => 'nova',
            'catalogType' => 'compute',
            'region'      => 'RegionOne',
            'user'        => [
                'id'       => '0ca8f6',
                'password' => 'secretsecret',
            ]
        ];

        $service = $this->builder->createService('Compute', 2, $options);
        $this->assertInstanceOf('OpenStack\Compute\v3\Service', $service);
    }
}