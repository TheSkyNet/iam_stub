<?php

namespace Tests\Feature;

use IamLab\Core\API\aAPI;
use PHPUnit\Framework\TestCase;
use Phalcon\Di\Di;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Encryption\Security;

class ApiKeyCsrfBypassTest extends TestCase
{
    private $di;

    protected function setUp(): void
    {
        if (!class_exists(Di::class)) {
            $this->markTestSkipped('Phalcon is not installed');
        }

        $this->di = new Di();
        Di::setDefault($this->di);

        $this->di->setShared('request', new Request());
        $this->di->setShared('response', new Response());
        $this->di->setShared('security', new Security());
        
        // Mock config
        $this->di->setShared('config', new \Phalcon\Config\Config([]));
    }

    private function setupMockAuthService($identity = null, $user = null)
    {
        $mock = $this->createMock(\IamLab\Service\Auth\AuthService::class);
        $mock->method('getIdentity')->willReturn($identity);
        $mock->method('getUser')->willReturn($user);
        $this->di->setShared('authService', $mock);
        return $mock;
    }

    public function testBypassWhenNoOriginHeader(): void
    {
        $this->setupMockAuthService(['type' => 'api_key']);

        $request = $this->createMock(Request::class);
        $request->method('getMethod')->willReturn('POST');
        $request->method('getHeader')->willReturnMap([
            ['Origin', ''],
            ['X-CSRF-Token', ''],
            ['X-CSRF-Key', '']
        ]);
        $this->di->setShared('request', $request);

        $api = new class extends aAPI {
            public $csrfVerified = false;
            protected function verifyCsrf(): void {
                parent::verifyCsrf();
                $this->csrfVerified = true;
            }
        };

        $api->initialize();
        $this->assertTrue($api->csrfVerified);
    }

    public function testBypassWhenDomainWhitelistedForUser(): void
    {
        $mockUser = $this->createMock(\IamLab\Model\User::class);
        $mockUser->method('getWhitelistDomains')->willReturn('example.com, test.org');
        
        $this->setupMockAuthService(['type' => 'api_key'], $mockUser);

        $request = $this->createMock(Request::class);
        $request->method('getMethod')->willReturn('POST');
        $request->method('getHeader')->willReturnMap([
            ['Origin', 'https://example.com'],
        ]);
        $this->di->setShared('request', $request);

        $api = new class extends aAPI {
            public $csrfVerified = false;
            protected function verifyCsrf(): void {
                parent::verifyCsrf();
                $this->csrfVerified = true;
            }
        };

        $api->initialize();
        $this->assertTrue($api->csrfVerified);
    }

    public function testBypassWhenDomainWhitelistedGlobally(): void
    {
        $this->setupMockAuthService(['type' => 'api_key'], null);

        // Set global whitelist env
        putenv('GLOBAL_API_KEY_WHITELIST=global.com,another.net');

        $request = $this->createMock(Request::class);
        $request->method('getMethod')->willReturn('POST');
        $request->method('getHeader')->willReturnMap([
            ['Origin', 'https://global.com'],
        ]);
        $this->di->setShared('request', $request);

        $api = new class extends aAPI {
            public $csrfVerified = false;
            protected function verifyCsrf(): void {
                parent::verifyCsrf();
                $this->csrfVerified = true;
            }
        };

        $api->initialize();
        
        // Clean up env
        putenv('GLOBAL_API_KEY_WHITELIST');
        
        $this->assertTrue($api->csrfVerified);
    }

    public function testNoBypassWhenDomainNotWhitelisted(): void
    {
        $mockUser = $this->createMock(\IamLab\Model\User::class);
        $mockUser->method('getWhitelistDomains')->willReturn('example.com');
        
        $this->setupMockAuthService(['type' => 'api_key'], $mockUser);

        $request = $this->createMock(Request::class);
        $request->method('getMethod')->willReturn('POST');
        $request->method('getHeader')->willReturnMap([
            ['Origin', 'https://malicious.com'],
            ['X-CSRF-Token', ''],
            ['X-CSRF-Key', '']
        ]);
        $this->di->setShared('request', $request);

        $api = new class extends aAPI {
            protected function dispatch(mixed $data, int $status = 200): void {
                throw new \Exception("CSRF FAILED: " . $status);
            }
        };

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CSRF FAILED: 403');

        $api->initialize();
    }

    public function testNoBypassWhenTypeIsAccessEvenIfWhitelisted(): void
    {
        $mockUser = $this->createMock(\IamLab\Model\User::class);
        $mockUser->method('getWhitelistDomains')->willReturn('example.com');
        
        // Identity type is 'access'
        $this->setupMockAuthService(['type' => 'access'], $mockUser);

        $request = $this->createMock(Request::class);
        $request->method('getMethod')->willReturn('POST');
        $request->method('getHeader')->willReturnMap([
            ['Origin', 'https://example.com'],
            ['X-CSRF-Token', ''],
            ['X-CSRF-Key', '']
        ]);
        $this->di->setShared('request', $request);

        $api = new class extends aAPI {
            protected function dispatch(mixed $data, int $status = 200): void {
                throw new \Exception("CSRF FAILED: " . $status);
            }
        };

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CSRF FAILED: 403');

        $api->initialize();
    }

    public function testBypassWhenGlobalBypassEnabled(): void
    {
        $this->setupMockAuthService(['type' => 'api_key']);

        // Set global bypass env
        putenv('ALLOW_API_KEY_CSRF_BYPASS=true');

        $request = $this->createMock(Request::class);
        $request->method('getMethod')->willReturn('POST');
        $request->method('getHeader')->willReturnMap([
            ['Origin', 'https://any-domain.com'],
        ]);
        $this->di->setShared('request', $request);

        $api = new class extends aAPI {
            public $csrfVerified = false;
            protected function verifyCsrf(): void {
                parent::verifyCsrf();
                $this->csrfVerified = true;
            }
        };

        $api->initialize();
        
        // Clean up env
        putenv('ALLOW_API_KEY_CSRF_BYPASS');
        
        $this->assertTrue($api->csrfVerified);
    }
}
