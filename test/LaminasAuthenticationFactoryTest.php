<?php

/**
 * @see       https://github.com/mezzio/mezzio-authentication-laminasauthentication for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-authentication-laminasauthentication/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-authentication-laminasauthentication/blob/master/LICENSE.md New BSD License
 */

namespace MezzioTest\Authentication\LaminasAuthentication;

use Laminas\Authentication\AuthenticationService;
use Mezzio\Authentication\Exception\InvalidConfigException;
use Mezzio\Authentication\LaminasAuthentication\LaminasAuthentication;
use Mezzio\Authentication\LaminasAuthentication\LaminasAuthenticationFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

class LaminasAuthenticationFactoryTest extends TestCase
{
    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->factory = new LaminasAuthenticationFactory();
        $this->authService = $this->prophesize(AuthenticationService::class);
        $this->responsePrototype = $this->prophesize(ResponseInterface::class);
    }

    public function testInvokeWithEmptyContainer()
    {
        $this->expectException(InvalidConfigException::class);
        $laminasAuthentication = ($this->factory)($this->container->reveal());
    }

    public function testInvokeWithContainerEmptyConfig()
    {
        $this->container
            ->has(AuthenticationService::class)
            ->willReturn(true);
        $this->container
            ->get(AuthenticationService::class)
            ->willReturn($this->authService->reveal());
        $this->container
            ->has(ResponseInterface::class)
            ->willReturn(true);
        $this->container
            ->get(ResponseInterface::class)
            ->willReturn($this->responsePrototype->reveal());
        $this->container
            ->get('config')
            ->willReturn([]);

        $this->expectException(InvalidConfigException::class);
        $laminasAuthentication = ($this->factory)($this->container->reveal());
    }

    public function testInvokeWithContainerAndConfig()
    {
        $this->container
            ->has(AuthenticationService::class)
            ->willReturn(true);
        $this->container
            ->get(AuthenticationService::class)
            ->willReturn($this->authService->reveal());
        $this->container
            ->has(ResponseInterface::class)
            ->willReturn(true);
        $this->container
            ->get(ResponseInterface::class)
            ->willReturn($this->responsePrototype->reveal());
        $this->container
            ->get('config')
            ->willReturn([
                'authentication' => ['redirect' => '/login'],
            ]);

        $laminasAuthentication = ($this->factory)($this->container->reveal());
        $this->assertInstanceOf(LaminasAuthentication::class, $laminasAuthentication);
    }
}