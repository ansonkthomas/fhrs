<?php

namespace App\Service;

use App\Entity\Establishment;
use App\Entity\Authority;
use App\Entity\Rating;
use App\Utils\FormatData;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\ApiResponse;

class Vault
{
    /*
     * Check the rating value, authority code in DB and save
     */
    public function pushEstablishment(Request $request, $entityManager, ApiResponse $apiResponse, FormatData $formatData, Establishment $establishment)
    {
        //Fetch the authority object from authority code
        $authority = $entityManager->getRepository(Authority::class)->findOneBy(['code' => $request->get("authorityCode")]);

        //Fetch all ratings
        $ratings = $entityManager->getRepository(Rating::class)->findAll();

        //Get the rating object by rating value
        $rating = $this->getRatingByValue($ratings, $request->get("ratingValue"));

        //Check the json has valid authority code and rating value
        if ($authority && $rating) {
            //Insert establishment to DB
            $establishment = $this->saveEstablishment($request, $entityManager, $rating, $authority, $establishment);
            $data = $formatData->objectToArrayNormalize($establishment);
        } else {
            //Set validation message
            $apiResponse->setValidationStatusCode();
            $data = $this->setValidationMessage($authority, $rating);
        }

        return $data;
    }

    private function saveEstablishment(Request $request, $entityManager, Rating $rating, Authority $authority, Establishment $establishment)
    {
        //Create an instance of Establishment entity
        $establishment->setFHRSID($request->get("FHRSID"));
        $establishment->setLocalAuthorityBusinessID($request->get("localAuthorityBusinessId"));
        $establishment->setName($request->get("name"));
        $establishment->setAddressLine1($request->get("addressLine1"));
        $establishment->setAddressLine2($request->get("addressLine2"));
        $establishment->setAddressLine3($request->get("addressLine3"));
        $establishment->getAddressLine4($request->get("addressLine4"));
        $establishment->setPostCode($request->get("postcode"));
        $establishment->setPhone($request->get("phone"));
        $establishment->setRating($rating);
        $establishment->setRatingDate(new \DateTime($request->get("ratingDate")));
        $establishment->setAuthority($authority);
        $entityManager->persist($establishment);
        $entityManager->flush();

        return $establishment;
    }

    private function setValidationMessage($authority, $rating): array
    {
        $validate = array();
        if (!$authority) {
            array_push($validate, array("authorityCode" => "Invalid authority code"));
        }
        if (!$rating) {
            array_push($validate, array("ratingValue" => "Invalid rating value"));
        }
        $data = [
            "message" => $validate
        ];

        return $data;
    }

    private function getRatingByValue($ratings, $ratingValue)
    {
        $rating = array_filter($ratings,
            function ($obj) use ($ratingValue) {
                return $obj->getRatingKeyName() == $ratingValue;
            }
        );

        return reset($rating);
    }
}
