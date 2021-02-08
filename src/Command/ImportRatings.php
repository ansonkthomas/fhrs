<?php

namespace App\Command;

use App\Entity\Rating;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;


class ImportRatings extends Command
{
    private $client;
    protected static $defaultName = 'app:import-ratings';
    const API_URL_FHRS_RATINGS = 'https://api.ratings.food.gov.uk/ratings';

    public function __construct(HttpClientInterface $client, EntityManagerInterface $entityManager)
    {
        $this->client = $client;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Import FHRS ratings')->setHelp('This command fetch the ratings from FHRS by an API and saves to the DB');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Import Ratings',
            '=============='
        ]);

        //Fetch the ratings by FHRS API
        $content = $this->import($this->client);

        //Verify the result and write to DB
        $status = Command::SUCCESS;
        if (array_key_exists('meta', $content) && $content['meta']['itemCount'] != 0) {
            $this->saveRatings($this->entityManager, $content['ratings']);
            $output->write('FHRS ratings has been successfully imported to the DB');
        }
        else {
            $output->write('Unable to fetch the API or empty result');
            $status = Command::FAILURE;
        }

        return $status;
    }

    /*
     * Save ratings to the DB
     */
    private function saveRatings($entityManager, $ratings): void
    {
        //Create an instance of Rating entity
        foreach($ratings as $value) {
            $rating = new Rating();
            $rating->setName($value['ratingName']);
            $rating->setRatingKey($value['ratingKey']);
            $rating->setRatingKeyName($value['ratingKeyName']);
            $entityManager->persist($rating);
        }
        $entityManager->flush();
    }

    /*
     * Fetch ratings details by FHRS API
     */
    private function import($client): array
    {
        try {
            $response = $client->request(
                'GET', self::API_URL_FHRS_RATINGS, [
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
