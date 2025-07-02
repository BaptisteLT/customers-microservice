<?php

namespace App\Tests\Integration\State;


use App\Entity\Customer;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;



abstract class AbstractTest extends ApiTestCase
{
    private ?string $token = null;
    protected ?Client $client = null;


    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    protected function getResolvedToken($token = null): ?string
    {
        return $token = $token ?: $this->getToken();
    }


    /**
     * Use other credentials if needed.
     */
    protected function getToken($body = []): string
    {
        $container = static::getContainer();

        if ($this->token) {
            return $this->token;
        }
        $passwordHasher = $container->get('security.password_hasher');

        $customer = new Customer();
        $customer->setUsername('admin@example.com');
        $customer->setFirstName('first');
        $customer->setLastName('last');
        $customer->setPostalCode('10');
        $customer->setCity('Chartres');
        $customer->setCompanyName('cci');
        $customer->setRoles(['ROLE_ADMIN']);
        $customer->setPassword($passwordHasher->hashPassword($customer, '$3cr3t'));


        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->persist($customer);
        $entityManager->flush();


        $response = $this->client->request('POST', '/auth', ['json' => $body ?: [
            'username' => 'admin@example.com',
            'password' => '$3cr3t',
        ], 'headers' => [
            'Content-Type' => 'application/json',
            ]]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($response->getContent());
        $this->token = $data->token;

        return $data->token;
    }
}
class RegisterStateProcessorTest extends AbstractTest
{


    public function testGetCustomer(): void
    {

        // This calls KernelTestCase::bootKernel(), and creates a
        // "client" that is acting as the browser
        $response = $this->client->request('GET', '/api/customers', [
            'headers' => [
                'authorization' => 'Bearer '.$this->getResolvedToken()
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $data = json_decode($response->getContent());
        $this->assertEquals(1, count($data->member));
    }

    public function testUpdateCustomer(): void
    {
        $response = $this->client->request('POST', '/api/customers', [
            'json' => [
                "username"=> "string",
                "firstName"=> "string",
                "lastName"=> "string",
                "postalCode"=> "string",
                "city"=> "string",
                "companyName"=> "string",
                "plainPassword"=> "k~s6\\l2IIN9",
            ],
            'headers' => [
                'authorization' => 'Bearer '.$this->getResolvedToken(),
                'Content-Type' => 'application/ld+json'
            ]
        ]);
        $this->assertResponseIsSuccessful();
    }
}
