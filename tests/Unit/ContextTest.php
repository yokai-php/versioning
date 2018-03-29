<?php

namespace Yokai\Versioning\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Yokai\Versioning\Context;
use Yokai\Versioning\Tests\Acme\Domain\Admin;

class ContextTest extends TestCase
{
    /**
     * @test
     */
    public function it_contains_empty_context_at_construct(): void
    {
        $context = new Context();

        self::assertNull($context->getEntryPoint());
        self::assertSame([], $context->getParameters());
        self::assertNull($context->getAuthor());
    }

    /**
     * @test
     */
    public function it_allow_setting_context(): void
    {
        $context = new Context();
        $context->setEntryPoint('route_to_update_product');
        $context->setParameters(['id' => 1]);
        $context->setAuthor($admin = new Admin('1'));

        self::assertSame('route_to_update_product', $context->getEntryPoint());
        self::assertSame(['id' => 1], $context->getParameters());
        self::assertSame($admin, $context->getAuthor());
    }
}
