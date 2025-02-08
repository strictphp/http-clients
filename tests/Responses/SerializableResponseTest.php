<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Tests\Responses;

use Closure;
use Exception;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Responses\SerializableResponse;

final class SerializableResponseTest extends TestCase
{
    /**
     * @return array<string|int, array{0: Closure(static):void}>
     */
    public static function data(): array
    {
        return [
            [
                static function (self $self) {
                    $self->assert(
                        'O:52:"StrictPhp\HttpClients\Responses\SerializableResponse":7:{s:5:"class";s:24:"GuzzleHttp\Psr7\Response";s:15:"protocolVersion";s:3:"1.1";s:7:"headers";a:0:{}s:4:"code";i:200;s:6:"reason";s:2:"OK";s:4:"body";s:0:"";s:4:"file";s:0:"";}',
                        new Response(),
                    );
                },
            ],
            [
                static function (self $self) {
                    $expected = <<<JSON
                        O:52:"StrictPhp\HttpClients\Responses\SerializableResponse":7:{s:5:"class";s:24:"GuzzleHttp\Psr7\Response";s:15:"protocolVersion";s:3:"2.0";s:7:"headers";a:1:{s:8:"Redirect";a:1:{i:0;s:17:"http://localhost/";}}s:4:"code";i:400;s:6:"reason";s:7:"reason!";s:4:"body";s:248:"{
                          "menu": {
                            "id": "file",
                            "value": "File",
                            "popup": {
                              "menuitem": [
                                {
                                  "value": "New"
                                },
                                {
                                  "value": "Open"
                                },
                                {
                                  "value": "Close"
                                }
                              ]
                            }
                          }
                        }
                        ";s:4:"file";s:0:"";}
                        JSON;

                    $body = (new HttpFactory())->createStreamFromFile(__DIR__ . '/dummy.json');

                    $self->assert(
                        $expected,
                        new Response(400, [
                            'Redirect' => 'http://localhost/',
                        ], $body, '2.0', 'reason!'),
                    );
                },
            ],
            [
                static function (self $self) {
                    $expected = 'O:52:"StrictPhp\HttpClients\Responses\SerializableResponse":7:{s:5:"class";s:24:"GuzzleHttp\Psr7\Response";s:15:"protocolVersion";s:3:"2.0";s:7:"headers";a:1:{s:8:"Redirect";a:1:{i:0;s:17:"http://localhost/";}}s:4:"code";i:400;s:6:"reason";s:7:"reason!";s:4:"body";s:0:"";s:4:"file";s:4:"json";}';

                    $file = __DIR__ . '/dummy.json';
                    $body = (new HttpFactory())->createStreamFromFile($file);

                    $self->assert(
                        $expected,
                        new Response(400, [
                            'Redirect' => 'http://localhost/',
                        ], $body, '2.0', 'reason!'),
                        'json',
                    );
                },
            ],
        ];
    }

    /**
     * @param Closure(static):void $assert
     */
    #[DataProvider('data')]
    public function test(Closure $assert): void
    {
        $assert($this);
    }

    public function assert(string|Exception $expected, ResponseInterface $response, string $file = ''): void
    {
        $serialize = serialize(new SerializableResponse($response, $file));

        if ($expected instanceof Exception) {
            $this->expectExceptionObject($expected);
        } else {
            Assert::assertSame($expected, $serialize);
        }

        $serializableResponse = unserialize($serialize);
        assert($serializableResponse instanceof SerializableResponse);
        $response2 = $serializableResponse->response;

        Assert::assertSame($response->getReasonPhrase(), $response2->getReasonPhrase());
        Assert::assertSame($response->getStatusCode(), $response2->getStatusCode());
        Assert::assertSame($response->getHeaders(), $response2->getHeaders());
        Assert::assertSame($response->getProtocolVersion(), $response2->getProtocolVersion());
        Assert::assertSame($response->getProtocolVersion(), $response2->getProtocolVersion());
        Assert::assertSame($file === '' ? ((string) $response->getBody()) : '', (string) $response2->getBody());
    }
}
