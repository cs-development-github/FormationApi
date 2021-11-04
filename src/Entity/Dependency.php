<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Ramsey\Uuid\Uuid;

/**
 * @ApiResource(
 *     collectionOperations={"get","post"},
 *     itemOperations={
 *      "get",
 *      "delete",
 *      "put"={
 *          "denormalization_context"= {
 *              "groups"={"put:Dependency"}
 *          }
 *      }
 *      },
 *     paginationEnabled=false
 * )
 */

class Dependency
{
    /**
     * @ApiProperty(identifier=true,description="L'identifiant de la dépendence")
     */
    private  $uuid;
    /**
     * @ApiProperty(
     *  description="Nom de la dépendence"
     * ),
     * @Assert\Length(
     *     min = 5,
     *     max = 50,
     * ),
     * @Assert\NotBlank
     */
    private  $name;
    /**
     * @ApiProperty(description="Version de la dépendence", example="5.2.*"),
     * @Assert\Length(
     *     min = 5,
     *     max = 50,
     * ),
     * @Assert\NotBlank
     * @Groups({"put:Dependency"})
     */
    private  $version;

    public function __construct(
        string $name,
        string $version
    ){
        $this->uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, $name)->toString();
        $this->name = $name;
        $this->version = $version;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version):void
    {
        $this->version = $version;
    }

}