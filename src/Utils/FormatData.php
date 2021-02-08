<?php

namespace App\Utils;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;

class FormatData
{
    /**
     * Convert object to json
     */
    public function objectToJsonSerialize($object)
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $data = $serializer->serialize($object, 'json');

        return $data;
    }

    /**
     * Convert object to array
     *
     * @return array $data
     */
    public function objectToArrayNormalize($object)
    {
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers);
        $data = $serializer->normalize($object);

        return $data;
    }

    public function transformJsonBody(Request $request): Request
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }
        $request->request->replace($data);

        return $request;
    }
}
