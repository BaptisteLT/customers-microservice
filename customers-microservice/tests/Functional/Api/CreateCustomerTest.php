<?php

namespace App\Tests\Functional\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreateCustomerTest extends WebTestCase
{
    public function testCreateCustomerWithValidData(): void
    {
        $client = static::createClient();

        $client->request('POST', '/clients', [
            'json' => [
                'username' => 'valid_user_1',
                'firstName' => 'Alice',
                'lastName' => 'Durand',
                'email' => 'alice@example.com',
            ],
            'headers' => [
                'Authorization' => 'Bearer '.$this->getValidAdminToken()
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);

        $data = $client->getResponse()->toArray();
        $this->assertArrayHasKey('id', $data);
        $this->assertSame('valid_user_1', $data['username']);
    }

    public function testCreateCustomerWithMissingUsername(): void
    {
        $client = static::createClient();

        $client->request('POST', '/clients', [
            'json' => [
                'firstName' => 'Alice',
                'lastName' => 'Durand',
                'email' => 'alice@example.com',
            ],
            'headers' => [
                'Authorization' => 'Bearer '.$this->getValidAdminToken()
            ]
        ]);

        $this->assertResponseStatusCodeSame(400);
        $data = $client->getResponse()->toArray();
        $this->assertArrayHasKey('errors', $data);
        $this->assertStringContainsString('username', json_encode($data));
    }

    public function testUnauthorizedAccess(): void
    {
        $client = static::createClient();

        $client->request('POST', '/clients', [
            'json' => [
                'username' => 'user123',
                'firstName' => 'Alice',
                'lastName' => 'Durand',
                'email' => 'alice@example.com',
            ]
            // Pas de token
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    private function getValidAdminToken(): string
    {
        // Tu peux soit mocker, soit appeler un vrai endpoint pour obtenir un token JWT
        // Exemple simple (à adapter selon ton système d’authentification JWT)
        $client = static::createClient();
        $client->request('POST', '/login', [
            'json' => [
                'username' => 'admin',
                'password' => 'adminpass'
            ]
        ]);

        $response = $client->getResponse()->toArray();
        return $response['token'] ?? '';
    }

    public function testUpdateCustomer(): void
    {
        $client = static::createClient();
        $token = $this->getValidAdminToken();

        // Crée un client d’abord
        $client->request('POST', '/clients', [
            'json' => [
                'username' => 'user_update',
                'firstName' => 'Initial',
                'lastName' => 'Name',
                'email' => 'initial@example.com'
            ],
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $data = $client->getResponse()->toArray();
        $id = $data['id'];

        // Modifier ce client
        $client->request('PUT', "/clients/$id", [
            'json' => [
                'firstName' => 'Updated',
                'lastName' => 'Name'
            ],
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);

        $this->assertResponseIsSuccessful();
        $updatedData = $client->getResponse()->toArray();
        $this->assertSame('Updated', $updatedData['firstName']);
    }

    public function testDeleteCustomer(): void
    {
        $client = static::createClient();
        $token = $this->getValidAdminToken();

        // Créer un client à supprimer
        $client->request('POST', '/clients', [
            'json' => [
                'username' => 'user_to_delete',
                'firstName' => 'Delete',
                'lastName' => 'Me',
                'email' => 'delete@example.com'
            ],
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $data = $client->getResponse()->toArray();
        $id = $data['id'];

        // Supprimer le client
        $client->request('DELETE', "/clients/$id", [
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);

        $this->assertResponseStatusCodeSame(204);
    }

    public function testAdminCanFilterCustomers(): void
    {
        $client = static::createClient();
        $client->request('GET', '/clients?username=user', [
            'headers' => ['Authorization' => 'Bearer ' . $this->getValidAdminToken()]
        ]);

        $this->assertResponseIsSuccessful();
        $data = $client->getResponse()->toArray();
        $this->assertIsArray($data);
    }

    public function testUserCannotAccessCustomerList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/clients', [
            'headers' => ['Authorization' => 'Bearer ' . $this->getValidUserToken()]
        ]);

        $this->assertResponseStatusCodeSame(403); // ou 401 selon ton security.yaml
    }

}
