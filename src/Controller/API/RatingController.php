<?php

namespace App\Controller\API;

use App\Entity\Rating;
use App\Utils\FormatData;
use App\Service\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RatingController extends AbstractController
{
    /**
     * Get rating list
     *
     * @Route("/ratings", name = "get_rating_list", methods={"GET"})
     */
    public function getRatingList(Request $request, FormatData $formatData, ApiResponse $apiResponse): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        try {
            //Fetch the authority object from authority code
            $ratingList = $entityManager->getRepository(Rating::class)->findAll();
            if (count($ratingList)) {
                $data = $formatData->objectToArrayNormalize($ratingList);
            } else {
                $data = [
                    "message" => "No result found"
                ];
            }
        } catch (\Exception $e) {
            $data = [
                "message" => $e->getMessage()
            ];
        }

        return $apiResponse->response($data);
    }
}
