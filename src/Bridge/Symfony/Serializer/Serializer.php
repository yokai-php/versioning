<?php declare(strict_types=1);

namespace Yokai\Versioning\Bridge\Symfony\Serializer;

use Symfony\Component\Serializer\Serializer as BaseSerializer;

class Serializer extends BaseSerializer
{
    public function __construct(iterable $normalizers, iterable $encoders)
    {
        $normalizersArray = [];
        foreach ($normalizers as $normalizer) {
            $normalizersArray[] = $normalizer;
        }

        $encodersArray = [];
        foreach ($encoders as $encoder) {
            $encodersArray[] = $encoder;
        }

        parent::__construct($normalizersArray, $encodersArray);
    }
}
