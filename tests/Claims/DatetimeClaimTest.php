<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Sean Tymon <tymon148@gmail.com>
 * (c) PHP Open Source Saver
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPOpenSourceSaver\JWTAuth\Test\Claims;

use Carbon\Carbon;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use PHPOpenSourceSaver\JWTAuth\Claims\Collection;
use PHPOpenSourceSaver\JWTAuth\Claims\Expiration;
use PHPOpenSourceSaver\JWTAuth\Claims\IssuedAt;
use PHPOpenSourceSaver\JWTAuth\Claims\Issuer;
use PHPOpenSourceSaver\JWTAuth\Claims\JwtId;
use PHPOpenSourceSaver\JWTAuth\Claims\NotBefore;
use PHPOpenSourceSaver\JWTAuth\Claims\Subject;
use PHPOpenSourceSaver\JWTAuth\Exceptions\InvalidClaimException;
use PHPOpenSourceSaver\JWTAuth\Payload;
use PHPOpenSourceSaver\JWTAuth\Test\AbstractTestCase;
use PHPOpenSourceSaver\JWTAuth\Validators\PayloadValidator;

class DatetimeClaimTest extends AbstractTestCase
{
    protected LegacyMockInterface $validator;

    protected array $claimsTimestamp;

    /**
     * @throws InvalidClaimException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->validator = Mockery::mock(PayloadValidator::class);
        $this->validator->shouldReceive('setRefreshFlow->check');

        $this->claimsTimestamp = [
            'sub' => new Subject(1),
            'iss' => new Issuer('http://example.com'),
            'exp' => new Expiration($this->testNowTimestamp + 3600),
            'nbf' => new NotBefore($this->testNowTimestamp),
            'iat' => new IssuedAt($this->testNowTimestamp),
            'jti' => new JwtId('foo'),
        ];
    }

    /** @test
     * @throws InvalidClaimException
     */
    public function it_should_handle_carbon_claims()
    {
        $testCarbon = Carbon::createFromTimestampUTC($this->testNowTimestamp);
        $testCarbonCopy = clone $testCarbon;

        $this->assertInstanceOf(Carbon::class, $testCarbon);
        $this->assertInstanceOf(Datetime::class, $testCarbon);
        $this->assertInstanceOf(DatetimeInterface::class, $testCarbon);

        $claimsDatetime = [
            'sub' => new Subject(1),
            'iss' => new Issuer('http://example.com'),
            'exp' => new Expiration($testCarbonCopy->addHour()),
            'nbf' => new NotBefore($testCarbon),
            'iat' => new IssuedAt($testCarbon),
            'jti' => new JwtId('foo'),
        ];

        $payloadTimestamp = new Payload(Collection::make($this->claimsTimestamp), $this->validator);
        $payloadDatetime = new Payload(Collection::make($claimsDatetime), $this->validator);

        $this->assertEquals($payloadTimestamp, $payloadDatetime);
    }

    /** @test */
    public function it_should_handle_datetime_claims()
    {
        $testDateTime = DateTime::createFromFormat('U', $this->testNowTimestamp);
        $testDateTimeCopy = clone $testDateTime;

        $this->assertInstanceOf(DateTime::class, $testDateTime);
        $this->assertInstanceOf(DatetimeInterface::class, $testDateTime);

        $claimsDatetime = [
            'sub' => new Subject(1),
            'iss' => new Issuer('http://example.com'),
            'exp' => new Expiration($testDateTimeCopy->modify('+3600 seconds')),
            'nbf' => new NotBefore($testDateTime),
            'iat' => new IssuedAt($testDateTime),
            'jti' => new JwtId('foo'),
        ];

        $payloadTimestamp = new Payload(Collection::make($this->claimsTimestamp), $this->validator);
        $payloadDatetime = new Payload(Collection::make($claimsDatetime), $this->validator);

        $this->assertEquals($payloadTimestamp, $payloadDatetime);
    }

    /** @test */
    public function it_should_handle_datetime_immutable_claims()
    {
        $testDateTimeImmutable = DateTimeImmutable::createFromFormat('U', (string)$this->testNowTimestamp);

        $this->assertInstanceOf(DateTimeImmutable::class, $testDateTimeImmutable);
        $this->assertInstanceOf(DatetimeInterface::class, $testDateTimeImmutable);

        $claimsDatetime = [
            'sub' => new Subject(1),
            'iss' => new Issuer('http://example.com'),
            'exp' => new Expiration($testDateTimeImmutable->modify('+3600 seconds')),
            'nbf' => new NotBefore($testDateTimeImmutable),
            'iat' => new IssuedAt($testDateTimeImmutable),
            'jti' => new JwtId('foo'),
        ];

        $payloadTimestamp = new Payload(Collection::make($this->claimsTimestamp), $this->validator);
        $payloadDatetime = new Payload(Collection::make($claimsDatetime), $this->validator);

        $this->assertEquals($payloadTimestamp, $payloadDatetime);
    }

    /** @test
     * @throws InvalidClaimException
     */
    public function it_should_handle_datetinterval_claims()
    {
        $testDateInterval = new DateInterval('PT1H');

        $this->assertInstanceOf(DateInterval::class, $testDateInterval);

        $claimsDateInterval = [
            'sub' => new Subject(1),
            'iss' => new Issuer('http://example.com'),
            'exp' => new Expiration($testDateInterval),
            'nbf' => new NotBefore($this->testNowTimestamp),
            'iat' => new IssuedAt($this->testNowTimestamp),
            'jti' => new JwtId('foo'),
        ];

        $payloadTimestamp = new Payload(Collection::make($this->claimsTimestamp), $this->validator);
        $payloadDateInterval = new Payload(Collection::make($claimsDateInterval), $this->validator);

        $this->assertEquals($payloadTimestamp, $payloadDateInterval);
    }
}
