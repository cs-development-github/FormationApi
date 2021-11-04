<?php 

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;
use ArrayObject;

class OpenApiFactory implements OpenApiFactoryInterface
{
    private $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);
        /** @var PathItem $path */
        foreach($openApi->getPaths()->getPaths() as $key => $path){
            if($path->getGet() && $path->getGet()->getSummary() === 'hidden'){
                $openApi->getPaths()->addPath($key, $path->withGet(null));
            }
        }
        // $openApi->getPaths()->addPath('/ping', new PathItem(null, 'Ping', null, new Operation('ping-id', [], [], 'Répond' )));
        $schema = $openApi->getComponents()->getSecuritySchemes();
        $schema['cookieAuth'] = new ArrayObject([
            'type' => "apiKey",
            "in" => "cookie",
            "name" => "PHPSESSID"
        ]);
        // $openApi = $openApi->withSecurity(['cookieAuth' => [""]])

        $schema = $openApi->getComponents()->getSchemas();
        $schema['Credentials'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'chris.vermersch@hotmail.com',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => '210499',
                ]
            ]
        ]);

        $pathItem = new PathItem(
            'JWT Token',  // Ref
            null,                // Summary
            null,                // Description
            null,                // Operation GET
            null,                // Operation PUT
            new Operation( // Operation POST
                'postCredentialsItem', // OperationId
                ['Auth'],    // Tags
                [                      // Responses
                    '200' => [
                        'description' => 'Utilisateur connecté',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/User-read.User',
                                ],
                            ],
                        ],
                    ],
                ],
                'Return JWT token to login', // Summary
                '',                        // Description
                null,                      // External Docs
                [],                        // Parameters
            ),
        );
        $openApi->getPaths()->addPath('/api/login', $pathItem);

        $meOperation =$openApi->getPaths()->getPath('/api/me')->getGet()->withParameters([]);
        $mePathItem = $openApi->getPaths()->getPath('/api/me')->withGet($meOperation);
        $openApi->getPaths()->addPath('/api/me', $mePathItem);

        $pathItem = new PathItem(
            'postApiLogout',  // Ref
            null,                // Summary
            null,                // Description
            null,                // Operation GET
            null,                // Operation PUT
            new Operation( // Operation POST
                'postApiLogout', // OperationId
                ['Auth'],    // Tags
                [                      // Responses
                    '204' => [],
                ],
                '', // Summary
                '',                        // Description
                null,                      // External Docs
                [],                        // Parameters
            ),
        );
        $openApi->getPaths()->addPath('/logout', $pathItem);

        return $openApi;
    }
}