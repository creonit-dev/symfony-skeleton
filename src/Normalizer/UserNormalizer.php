<?php


namespace App\Normalizer;


use App\Model\User;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;

class UserNormalizer extends AbstractNormalizer
{
    const GROUP_USER_CONTACT = 'user_contact';
    const GROUP_USER_AUTHOR = 'user_author';

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param User $object Object to normalize
     * @param string $format Format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     *
     * @return array|string|int|float|bool|\ArrayObject|null \ArrayObject is used to make sure an empty object is encoded as an object not an array
     *
     * @throws InvalidArgumentException   Occurs when the object given is not a supported type for the normalizer
     * @throws CircularReferenceException Occurs when the normalizer detects a circular reference when no circular
     *                                    reference handler can fix it
     * @throws LogicException             Occurs when the normalizer is not called in an expected context
     * @throws ExceptionInterface         Occurs for all the other cases of errors
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if ($this->hasGroup($context, self::GROUP_USER_AUTHOR)) {
            $data = [
                'id' => $object->getId(),
                'name' => $object->getFirstName() . ' ' . $object->getLastName(),
                'institute' => $object->getInstitute(),
                'position' => $object->getPosition(),
            ];

        } else {
            $data = [
                'id' => $object->getId(),
                'position' => $object->getPosition(),
                'title' => $object->getTitleFull(),
                'firstName' => $object->getFirstName(),
                'middleName' => $object->getMiddleName(),
                'lastName' => $object->getLastName(),
                'avatar' => $object->getImage(),
            ];

            if ($this->hasGroup($context, self::GROUP_USER_CONTACT)) {
                $data += [
                    'academicRank' => $object->getAcademicRank(),
                    'academicDegree' => $object->getAcademicDegree(),
                    'contactEmail' => $object->getContactEmail(),
                    'contactPhone' => $object->getContactPhone(),
                    'contactInternalPhone' => $object->getContactInternalPhone(),
                ];
            }
        }

        return $this->serializer->normalize($data, $format, $context);
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed $data Data to normalize
     * @param string $format The format being (de-)serialized from or into
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof User;
    }
}