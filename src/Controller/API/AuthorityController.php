<?php

namespace App\Controller\API;

use App\Entity\Authority;
use App\Utils\FormatData;
use App\Service\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthorityController extends AbstractController
{
    /**
     * Get authority list
     *
     * @Route("/authorities", name = "get_authority_list", methods={"GET"})
     */
    public function getAuthorityList(Request $request, FormatData $formatData, ApiResponse $apiResponse): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        try {
            //Fetch the authority object from authority code
            $authorityList = $entityManager->getRepository(Authority::class)->findAll();
            if (count($authorityList)) {
                $data = $formatData->objectToArrayNormalize($authorityList);
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
