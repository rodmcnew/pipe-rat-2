<?php

namespace Reliv\PipeRat2\XampleRepositoryDoctrine\Config;

use Reliv\PipeRat2\Acl\Api\IsAllowedRcmUser;
use Reliv\PipeRat2\Acl\Http\RequestAcl;
use Reliv\PipeRat2\Core\Config\RouteConfig;
use Reliv\PipeRat2\Core\Config\RouteConfigAbstract;
use Reliv\PipeRat2\Core\DataResponse;
use Reliv\PipeRat2\DataExtractor\Api\ExtractPropertyGetter;
use Reliv\PipeRat2\DataExtractor\Http\ResponseDataExtractor;
use Reliv\PipeRat2\DataValidate\Api\Validate;
use Reliv\PipeRat2\DataValidate\Http\RequestDataValidate;
use Reliv\PipeRat2\Repository\Http\RepositoryFindById;
use Reliv\PipeRat2\RequestAttribute\Http\RequestAttributeUrlEncodedFiltersWhere;
use Reliv\PipeRat2\RequestAttribute\Http\RequestAttributeWhere;
use Reliv\PipeRat2\RequestFormat\Api\WithParsedBodyJson;
use Reliv\PipeRat2\RequestFormat\Http\RequestFormat;
use Reliv\PipeRat2\ResponseFormat\Api\WithFormattedResponseJson;
use Reliv\PipeRat2\ResponseFormat\Http\ResponseFormat;
use Reliv\PipeRat2\ResponseHeaders\Http\ResponseHeadersAdd;
use Reliv\PipeRat2\XampleRepositoryDoctrine\Entity\XampleEntity;

/**
 * @author James Jervis - https://github.com/jerv13
 */
class RouteConfigExample extends RouteConfigAbstract implements RouteConfig
{
    protected static function defaultParams(): array
    {
        $defaultParams = parent::defaultParams();
        $defaultParams['entity-class'] = XampleEntity::class;

        return $defaultParams;
    }

    protected static function defaultConfig(): array
    {
        return [
            /* Use standard route names for client simplicity */
            'name' => '{pipe-rat-2-config.root-path}.{pipe-rat-2-config.resource-name}.example',

            /* Use standard route paths for client simplicity */
            'path' => '{pipe-rat-2-config.root-path}/{pipe-rat-2-config.resource-name}/example',

            /* Wire each API independently */
            'middleware' => [
                /*'{config-key}' => '{service-name}',*/
                RequestFormat::configKey()
                => RequestFormat::class,

                RequestAcl::configKey()
                => RequestAcl::class,

                RequestAttributeUrlEncodedFiltersWhere::configKey()
                => RequestAttributeUrlEncodedFiltersWhere::class,

                RequestDataValidate::configKey()
                => RequestDataValidate::class,

                /** <response-mutators> */
                ResponseHeadersAdd::configKey()
                => ResponseHeadersAdd::class,

                ResponseFormat::configKey()
                => ResponseFormat::class,

                ResponseDataExtractor::configKey()
                => ResponseDataExtractor::class,
                /** </response-mutators> */

                RepositoryFindById::configKey()
                => RepositoryFindById::class,
            ],

            /* Use route to find options at runtime */
            'options' => [
                /*'{config-key}' => ['{optionKey}'=>'{optionValue}'],*/
                RequestFormat::configKey() => [
                    RequestFormat::OPTION_SERVICE_NAME
                    => WithParsedBodyJson::class,

                    RequestFormat::OPTION_SERVICE_OPTIONS => [],

                    RequestFormat::OPTION_VALID_CONTENT_TYPES => ['application/json'],
                    RequestFormat::OPTION_NOT_ACCEPTABLE_STATUS_CODE => 406,
                    RequestFormat::OPTION_NOT_ACCEPTABLE_STATUS_MESSAGE => "Not a JSON request",
                ],

                RequestAcl::configKey() => [
                    RequestAcl::OPTION_SERVICE_NAME
                    => IsAllowedRcmUser::class,

                    RequestAcl::OPTION_SERVICE_OPTIONS => [
                        IsAllowedRcmUser::OPTION_RESOURCE_ID => 'admin',
                        IsAllowedRcmUser::OPTION_PRIVILEGE => null,
                    ],

                    RequestAcl::OPTION_NOT_ALLOWED_STATUS_CODE => 401,
                    RequestAcl::OPTION_NOT_ALLOWED_STATUS_MESSAGE => 'No way man!',
                ],

                RequestAttributeUrlEncodedFiltersWhere::configKey() => [
                    RequestAttributeWhere::OPTION_ALLOW_DEEP_WHERES => false,
                ],

                RequestDataValidate::configKey() => [
                    RequestDataValidate::OPTION_SERVICE_NAME
                    => Validate::class,

                    RequestDataValidate::OPTION_SERVICE_OPTIONS => [
                        Validate::OPTION_PRIMARY_MESSAGE => 'Well, that is not good!'
                    ],
                    RequestDataValidate::OPTION_FAIL_STATUS_CODE => 400,
                ],

                /** <response-mutators> */
                ResponseHeadersAdd::configKey() => [
                    ResponseHeadersAdd::OPTION_HEADERS => ['header-name' => 'header-value'],
                ],

                ResponseFormat::configKey() => [
                    ResponseFormat::OPTION_SERVICE_NAME => WithFormattedResponseJson::class,
                    ResponseFormat::OPTION_SERVICE_OPTIONS => [
                        WithFormattedResponseJson::OPTION_JSON_ENCODING_OPTIONS => JSON_PRETTY_PRINT,
                        WithFormattedResponseJson::OPTION_CONTENT_TYPE => 'application/json',
                        WithFormattedResponseJson::OPTION_FORMATTABLE_RESPONSE_CLASSES => [DataResponse::class]
                    ],
                ],

                ResponseDataExtractor::configKey() => [
                    ResponseDataExtractor::OPTION_SERVICE_NAME => ExtractPropertyGetter::class,
                    ResponseDataExtractor::OPTION_SERVICE_OPTIONS => [
                        ExtractPropertyGetter::OPTION_PROPERTY_LIST => [],
                        ExtractPropertyGetter::OPTION_PROPERTY_DEPTH_LIMIT => 1,
                    ],
                ],
                /** </response-mutators> */

                RepositoryFindById::configKey() => [
                    RepositoryFindById::OPTION_SERVICE_NAME
                    => \Reliv\PipeRat2\RepositoryDoctrine\Api\FindById::class,

                    RepositoryFindById::OPTION_SERVICE_OPTIONS => [
                        \Reliv\PipeRat2\RepositoryDoctrine\Api\FindById::OPTION_ENTITY_CLASS_NAME
                        => '{pipe-rat-2-config.entity-class}',
                    ],
                ],
            ],

            /* Use expressive to define allowed methods */
            'allowed_methods' => ['GET'],
        ];
    }

    protected static function defaultPriorities(): array
    {
        return [
            RequestFormat::configKey() => 800,
            RequestAcl::configKey() => 700,
            RequestAttributeUrlEncodedFiltersWhere::configKey() => 600,
            RequestDataValidate::configKey() => 500,
            /** <response-mutators> */
            ResponseHeadersAdd::configKey() => 400,
            ResponseFormat::configKey() => 300,
            ResponseDataExtractor::configKey() => 200,
            /** </response-mutators> */
            RepositoryFindById::configKey() => 100,
        ];
    }
}
