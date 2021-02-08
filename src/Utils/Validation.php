<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Request;

class Validation
{

    /**
     * Validate establishment parameters
     *
     * @param Request $request
     *
     * @return array $validate
     */
    public function validateEstablishment(Request $request): array
    {
        $validate = array();
        if (empty($request->get("ratingValue"))) {
            array_push($validate, array("ratingValue" => "A rating value is required"));
        }
        $fhrsId = $request->get("FHRSID");
        if (empty($fhrsId)) {
            array_push($validate, array("FHRSID" => "A FHRSID is required"));
        } elseif (!is_int($fhrsId)) {
            array_push($validate, array("FHRSID" => "A FHRSID should be an integer"));
        }
        if (empty($request->get("localAuthorityBusinessId"))) {
            array_push($validate, array("localAuthorityBusinessId" => "A local authority business id is required"));
        }
        if (empty($request->get("name"))) {
            array_push($validate, array("name" => "An establishment name is required"));
        }
        $date = $request->get("ratingDate");
        if (empty($date)) {
            array_push($validate, array("ratingDate" => "A rating date is required"));
        } elseif (!$this->validateDate($date)) {
            array_push($validate, array("ratingDate" => "Rating date is not valid"));
        }
        if (empty($request->get("authorityCode"))) {
            array_push($validate, array("authorityCode" => "A local authority code is required"));
        }

        return $validate;
    }

    /*
     * Validate the date string
     */
    function validateDate(string $dateString, $format = 'Y-m-d'): bool
    {
        $date = \DateTime::createFromFormat($format, $dateString);
        return $date && $date->format($format) === $dateString;
    }
}
