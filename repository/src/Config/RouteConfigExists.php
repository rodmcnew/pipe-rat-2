<?php

namespace Reliv\PipeRat2\Repository\Config;

use Reliv\PipeRat2\Acl\Api\IsAllowedNotConfigured;
use Reliv\PipeRat2\Acl\Http\RequestAclMiddleware;
use Reliv\PipeRat2\Core\Config\RouteConfig;
use Reliv\PipeRat2\Core\Config\RouteConfigAbstract;
use Reliv\PipeRat2\DataExtractor\Api\ExtractPropertyGetter;
use Reliv\PipeRat2\DataExtractor\Api\ResponseDataExtractor;
use Reliv\PipeRat2\Repository\Api\ExistsNotConfigured;
use Reliv\PipeRat2\Repository\Http\RepositoryExists;
use Reliv\PipeRat2\RequestFormat\Http\RequestFormatJson;
use Reliv\PipeRat2\ResponseFormat\Http\ResponseFormatJson;
use Reliv\PipeRat2\ResponseHeaders\Http\ResponseHeadersAdd;

/**
 * @author James Jervis - https://github.com/jerv13
 */
class RouteConfigExists extends RouteConfigAbstract implements RouteConfig
{
    protected static function defaultConfig(): array
    {
        return [
            'name' => '[--{root-path}--].[--{resource-name}--].{id}.exists',

            'path' => '[--{root-path}--]/[--{resource-name}--]/{id}/exists',

            'middleware' => [
                RequestFormatJson::configKey()
                => RequestFormatJson::class,

                RequestAclMiddleware::configKey()
                => RequestAclMiddleware::class,

                /** <response-mutators> */
                ResponseHeadersAdd::configKey()
                => ResponseHeadersAdd::class,

                ResponseFormatJson::configKey()
                => ResponseFormatJson::class,

                ResponseDataExtractor::configKey()
                => ResponseDataExtractor::class,
                /** </response-mutators> */

                RepositoryExists::configKey()
                => RepositoryExists::class,
            ],

            'options' => [
                RequestFormatJson::configKey() => [
                    RequestFormatJson::OPTION_VALID_CONTENT_TYPES => ['application/json'],
                ],

                RequestAclMiddleware::configKey() => [
                    RequestAclMiddleware::OPTION_SERVICE_NAME
                    => IsAllowedNotConfigured::class,

                    RequestAclMiddleware::OPTION_SERVICE_OPTIONS => [
                        IsAllowedNotConfigured::OPTION_MESSAGE
                        => IsAllowedNotConfigured::DEFAULT_MESSAGE
                            . ' for pipe-rat-2 resource: "[--{resource-name}--]"'
                            . ' in file: "[--{source-config-file}--]"',
                    ],
                ],

                /** <response-mutators> */
                ResponseHeadersAdd::configKey() => [
                    ResponseHeadersAdd::OPTION_HEADERS
                    => [],
                ],

                ResponseFormatJson::configKey() => [
                    ResponseFormatJson::OPTION_JSON_ENCODING_OPTIONS => JSON_PRETTY_PRINT,
                ],

                ResponseDataExtractor::configKey() => [
                    ResponseDataExtractor::OPTION_SERVICE_NAME => ExtractPropertyGetter::class,
                    ResponseDataExtractor::OPTION_SERVICE_OPTIONS => [
                        ExtractPropertyGetter::OPTION_PROPERTY_LIST => null,
                        ExtractPropertyGetter::OPTION_PROPERTY_DEPTH_LIMIT => 1,
                    ],
                ],
                /** </response-mutators> */

                RepositoryExists::configKey() => [
                    RepositoryExists::OPTION_SERVICE_NAME
                    => ExistsNotConfigured::class,

                    RepositoryExists::OPTION_SERVICE_OPTIONS => [
                        ExistsNotConfigured::OPTION_MESSAGE
                        => ExistsNotConfigured::DEFAULT_MESSAGE
                            . ' for pipe-rat-2 resource: "[--{resource-name}--]"'
                            . ' in file: "[--{source-config-file}--]"',
                    ],
                ],
            ],

            'allowed_methods' => ['GET'],
        ];
    }

    protected static function defaultPriorities(): array
    {
        return [
            RequestFormatJson::configKey() => 600,
            RequestAclMiddleware::configKey() => 500,

            /** <response-mutators> */
            ResponseHeadersAdd::configKey() => 400,
            ResponseFormatJson::configKey() => 300,
            ResponseDataExtractor::configKey() => 200,
            /** </response-mutators> */

            RepositoryExists::configKey() => 100,
        ];
    }
}
