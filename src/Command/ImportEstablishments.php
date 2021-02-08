<?php

namespace App\Command;

use App\Entity\Establishment;
use App\Entity\Authority;
use App\Entity\Rating;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;

class ImportEstablishments extends Command
{
    private $client;
    protected static $defaultName = 'app:import-establishments';
    const API_URL_FHRS_ESTABLISHMENTS = 'https://api.ratings.food.gov.uk/Establishments?localAuthorityId=180&businessTypeId=1';

    public function __construct(HttpClientInterface $client, EntityManagerInterface $entityManager)
    {
        $this->client = $client;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Import FHRS establishments')->setHelp('This command fetch the establishments from FHRS by an API and saves to the DB');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Import Establishments',
            '====================='
        ]);

        //Fetch the ratings by FHRS API
        $content = $this->import($this->client);

        //Verify the result and write to DB
        $status = Command::SUCCESS;
        if (array_key_exists('meta', $content) && $content['meta']['itemCount'] != 0) {
            $this->saveEstablishments($this->entityManager, $content['establishments']);
            $output->write('FHRS establishments has been successfully imported to the DB');
        }
        else {
            $output->write('Unable to fetch the API or empty result');
            $status = Command::FAILURE;
        }

        return $status;
    }

    /*
     * Save establishments to the DB
     */
    private function saveEstablishments($entityManager, $establishments): void
    {
        //Fetch the authority object of Manchester
        //ToDo: Rework to make it general
        $authority = $entityManager->getRepository(Authority::class)->findOneBy(['code' => 415]);

        //Fetch all ratings
        $ratings = $entityManager->getRepository(Rating::class)->findAll();

        //Create an instance of Establishment entity
        foreach($establishments as $value) {
            $establishment = new Establishment();
            $establishment->setFHRSID($value['FHRSID']);
            $establishment->setLocalAuthorityBusinessID($value['LocalAuthorityBusinessID']);
            $establishment->setName($value['BusinessName']);
            $establishment->setAddressLine1($value['AddressLine1']);
            $establishment->setAddressLine2($value['AddressLine2']);
            $establishment->setAddressLine3($value['AddressLine3']);
            $establishment->getAddressLine4($value['AddressLine4']);
            $establishment->setPostCode($value['PostCode']);
            $establishment->setPhone($value['Phone']);

            //Get the rating object by rating value
            $ratingValue = $value['RatingValue'];
            $rating = array_filter($ratings,
                function ($obj) use ($ratingValue) {
                    return $obj->getRatingKeyName() == $ratingValue;
                }
            );

            $establishment->setRating(reset($rating));
            $establishment->setRatingDate(new \DateTime($value['RatingDate']));
            $establishment->setAuthority($authority);
            $entityManager->persist($establishment);
        }
        $entityManager->flush();
    }

    /*
     * Fetch establishment details by FHRS API
     */
    private function import($client): array
    {
        try {
            $response = $client->request(
                'GET', self::API_URL_FHRS_ESTABLISHMENTS, [
                    'headers' => [
                        'x-api-version' => 2,
                    ],
                ]
            );
            $statusCode = $response->getStatusCode();

            if ($response->getStatusCode() == 200) {
                $content = $response->toArray();
            }
        } catch (\Exception $e) {
            $content = [];
        }

        return $content;
    }
}
