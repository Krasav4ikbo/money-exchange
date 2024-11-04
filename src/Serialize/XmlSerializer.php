<?php
namespace App\Serialize;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class XmlSerializer extends Serializer
{
    public function __construct(array $normalizers = [], array $encoders = [])
    {
        $normalizers = array_merge($normalizers, [
            new ArrayDenormalizer(),
            new ObjectNormalizer(
                classMetadataFactory: new ClassMetadataFactory(new AttributeLoader()),
                propertyTypeExtractor: new PhpDocExtractor(),
            ),
        ]);

        $encoders = array_merge($encoders, [
            new XmlEncoder()
        ]);

        parent::__construct($normalizers, $encoders);
    }
}