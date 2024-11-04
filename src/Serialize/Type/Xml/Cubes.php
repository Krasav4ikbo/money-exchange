<?php
namespace App\Serialize\Type\Xml;

use Symfony\Component\Serializer\Attribute\SerializedPath;

/**
 * @Serializer\XmlRoot('ValCurs')
 */
class Cubes
{
    /** @var list<Cube> */
    #[SerializedPath('[Cube][Cube][Cube]')]
    private array $cubes = [];

    public function getCubes(): array
    {
        return $this->cubes;
    }

    public function setCubes(array $cubes): void
    {
        $this->cubes = $cubes;
    }
}