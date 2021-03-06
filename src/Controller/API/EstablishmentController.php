<?php

namespace App\Controller\API;

use App\Entity\Establishment;
use App\Entity\Authority;
use App\Entity\Rating;
use App\Utils\FormatData;
use App\Utils\Validation;
use App\Service\ApiResponse;
use App\Service\Vault;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class EstablishmentController extends AbstractController
{
    /**
     * Create an establishment
     *
     * @Route("/establishments", name="create_establishment", methods={"POST"})
     */
    public function createEstablishment(Request $request, FormatData $formatData, Validation $validation, ApiResponse $apiResponse, Vault $vault): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $request = $formatData->transformJsonBody($request);
        try {
            if (!$request) {
                $apiResponse->throwBadRequest();
            }

            //Validate establishment properties
            $validate = $validation->validateEstablishment($request);
            if (count($validate)) {
                //Validation is false. Have some validation messages
                $apiResponse->setValidationStatusCode();
                $data = [
                    "message" => $validate
                ];
            } else {
                //Check the rating value, authority code in DB and save
                $establishment = new Establishment();
                $data = $vault->pushEstablishment($request, $entityManager, $apiResponse, $formatData, $establishment);
            }
        } catch (\Exception $e) {
            $data = [
                "message" => $e->getMessage()
            ];
        }

        return $apiResponse->response($data);
    }

    /**
     * Update establishment details
     *
     * @Route("/establishments/{id}", name = "update_establishment", requirements={"number"="\d+"}, methods = {"PUT"})
     */
    public function updateEstablishment(Request $request, FormatData $formatData, Validation $validation, ApiResponse $apiResponse, Vault $vault, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $request = $formatData->transformJsonBody($request);
        try {
            if (!$request) {
                $apiResponse->throwBadRequest();
            }
            $establishment = $entityManager->getRepository(Establishment::class)->find($id);
            if (!$establishment) {
                $apiResponse->throwResourceNotFound("The establishment does not exists");
            }

            //Validate establishment properties
            $validate = $validation->validateEstablishment($request);
            if (count($validate)) {
                //Validation is false. Have some validation messages
                $apiResponse->setValidationStatusCode();
                $data = [
                    "message" => $validate
                ];
            } else {
                //Check the rating value, authority code in DB and save
                $data = $vault->pushEstablishment($request, $entityManager, $apiResponse, $formatData, $establishment);
            }
        } catch (\Exception $e) {
            $data = [
                "message" => $e->getMessage()
            ];
        }

        return $apiResponse->response($data);
    }

    /**
     * Get establishment list
     *
     * @Route("/establishments", name = "get_establishment_list", methods={"GET"})
     */
    public function getEstablishmentList(Request $request, FormatData $formatData, ApiResponse $apiResponse): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        try {
            //Check for authority code as a parameter
            if ($request->query->has("authorityCode")) {
                //Fetch the authority object from authority code
                $authority = $entityManager->getRepository(Authority::class)->findOneBy(['code' => $request->query->get("authorityCode")]);

                //Check the $authority is valid
                if ($authority) {
                    $postCode = ($request->query->has("postCode")) ? $request->query->get("postCode") : '';
                    $establishmentList = $this->getDoctrine()
                        ->getRepository(Establishment::class)
                        ->findEstablishments($authority, $postCode, $request->query->get("sortBy"));

                    if (count($establishmentList)) {
                        $data = $formatData->objectToArrayNormalize($establishmentList);
                    } else {
                        $data = [
                            "message" => "No result found"
                        ];
                    }
                } else {
                    $apiResponse->setValidationStatusCode();
                    $data = [
                        "message" => "Invalid authority code"
                    ];
                }
            } else {
                $data = [
                    "message" => "Local authority code is required as a query parameter"
                ];
            }
        } catch (\Exception $e) {
            $data = [
                "message" => $e->getMessage()
            ];
        }

        return $apiResponse->response($data);
    }

    /**
     * Get establishment details
     *
     * @Route("/establishments/{id}", name = "get_establishment", requirements={"number"="\d+"}, methods={"GET"})
     */
    public function getEstablishment(FormatData $formatData, ApiResponse $apiResponse, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        try {
            $establishment = $entityManager->getRepository(Establishment::class)->find($id);
            if (!$establishment) {
                $apiResponse->throwResourceNotFound("The establishment does not exists");
            }
            $data = $formatData->objectToArrayNormalize($establishment);
        } catch (\Exception $e) {
            $data = [
                "message" => $e->getMessage()
            ];
        }

        return $apiResponse->response($data);
    }

    /**
     * Delete an establishment
     *
     * @Route("/establishments/{id}", name = "delete_establishment", requirements={"number"="\d+"}, methods = {"DELETE"})
     */
    public function deleteEstablishment(ApiResponse $apiResponse, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        try {
            $establishment = $entityManager->getRepository(Establishment::class)->find($id);
            if (!$establishment) {
               $apiResponse->throwResourceNotFound("The establishment does not exists");
            }
            $entityManager->remove($establishment);
            $entityManager->flush();
            $data = [
                "message" => "The establishment has been deleted"
            ];
        } catch (\Exception $e) {
            $data = [
                "message" => $e->getMessage()
            ];
        }

        return $apiResponse->response($data);
    }
}
