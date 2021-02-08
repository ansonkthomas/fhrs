<?php

namespace App\Command;

use App\Entity\Authority;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;


class ImportAuthorities extends Command
{
    private $client;
    protected static $defaultName = 'app:import-authorities';
    const API_URL_FHRS_AUTHORITIES = 'https://api.ratings.food.gov.uk/authorities';

    public function __construct(HttpClientInterface $client, EntityManagerInterface $entityManager)
    {
        $this->client = $client;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Import FHRS authorities')->setHelp('This command fetch the authorities from FHRS by an API and saves to the DB');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Import Authorities',
            '=================='
        ]);

        //Fetch the authorities by FHRS API
        $content = $this->import($this->client);

        //Verify the result and write to DB
        $status = Command::SUCCESS;
        if (array_key_exists('meta', $content) && $content['meta']['itemCount'] != 0) {
            $this->saveAuthorities($this->entityManager, $content['authorities']);
            $output->write('FHRS authorities has been successfully imported to the DB');
        }
        else {
            $output->write('Unable to fetch the API or empty result');
            $status = Command::FAILURE;
        }

        return $status;
    }

    /*
     * Save authorities to the DB
     */
    private function saveAuthorities($entityManager, $authorities): void
    {
        //Create an instance of Rating entity
        foreach($authorities as $value) {
            $authority = new Authority();
            $authority->setCode($value['LocalAuthorityIdCode']);
            $authority->setName($value['Name']);
            $authority->setUrl($value['Url']);
            $authority->setEmail($value['Email']);
            $entityManager->persist($authority);
        }
        $entityManager->flush();
    }

    /*
     * Fetch authorities details by FHRS API
     */
    private function import($client): array
    {
        try {
            $response = $client->request(
                'GET', self::API_URL_FHRS_AUTHORITIES, [
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
