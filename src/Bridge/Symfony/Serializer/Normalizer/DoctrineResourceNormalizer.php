<?php declare(strict_types=1);

namespace Yokai\Versioning\Bridge\Symfony\Serializer\Normalizer;

use DateTimeInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Yokai\Versioning\VersionableResourceInterface;

class DoctrineResourceNormalizer implements NormalizerInterface
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    public function __construct(ManagerRegistry $doctrine, PropertyAccessorInterface $propertyAccessor)
    {
        $this->doctrine = $doctrine;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return is_object($data) && $data instanceof VersionableResourceInterface
            && $this->getClassMetadata($data) !== null;
    }

    /**
     * @inheritDoc
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $metadata = $this->getClassMetadata($object);
        $identifiers = $metadata->getIdentifierFieldNames();

        $normalized = [];

        foreach ($metadata->getFieldNames() as $property) {
            if (in_array($property, $identifiers)) {
                continue; //property is part of the identifier, not versioning it
            }

            $value = $this->propertyAccessor->getValue($object, $property);

            switch ($metadata->getTypeOfField($property)) {
                case Type::DATE:
                    if ($value instanceof DateTimeInterface) {
                        $value = $value->format('Y-m-d');
                    }
                    break;
                case Type::DATETIME:
                    if ($value instanceof DateTimeInterface) {
                        $value = $value->format('Y-m-d H:i:s');
                    }
                    break;
            }

            $normalized[$property] = $value;
        }

        foreach ($metadata->getAssociationMappings() as $property => $mapping) {
            if ($mapping['type'] & ClassMetadata::TO_MANY) {
                continue;
            }

            $value = $this->propertyAccessor->getValue($object, $property);

            if (is_object($value)) {
                $associationMetadata = $this->getClassMetadata($value);
                $identifiers = $associationMetadata->getIdentifierValues($value);
                array_walk(
                    $identifiers,
                    function (&$identifier) {
                        $identifier = is_numeric($identifier) ? intval($identifier) : $identifier;
                    }
                );

                switch (count($identifiers)) {
                    case 0:
                        $value = null;
                        break;
                    case 1:
                        $value = reset($identifiers);
                        break;
                    default:
                        $value = $identifiers;
                        break;
                }
            }

            $normalized[$property] = $value;
        }

        return $normalized;
    }

    /**
     * @param object $object
     *
     * @return ClassMetadata|null
     */
    private function getClassMetadata(object $object): ?ClassMetadata
    {
        $class = ClassUtils::getClass($object);

        $manager = $this->doctrine->getManagerForClass($class);

        try {
            $metadata = $manager->getMetadataFactory()->getMetadataFor($class);
            if (!$metadata instanceof ClassMetadata) {
                throw new \LogicException('Expecting ORM manager.');
            }

            return $metadata;
        } catch (MappingException $exception) {
            return null;
        }
    }
}
