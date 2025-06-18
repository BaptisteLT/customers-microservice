<?php

namespace App\Tests\Functional\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreateCustomerTest extends WebTestCase
{
    private $client;
    private $adminToken;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        // Authentification admin pour récupérer le token JWT, une seule fois par test
        $this->client->request(
            'POST',
            '/auth',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json', 'ACCEPT' => 'application/ld+json'],
            json_encode([
                'username' => 'admin',
                'password' => 'adminpass'
            ])
        );

        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->adminToken = $data['token'] ?? null;

        if (!$this->adminToken) {
            throw new \Exception("Impossible de récupérer le token admin pour les tests.");
        }
    }

    private function getAuthHeaders(string $token): array
    {
        return [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/ld+json',
            'ACCEPT' => 'application/ld+json',
        ];
    }

    public function testCreateCustomerWithValidData(): void
    {
        $this->client->request(
            'POST',
            '/api/customers',
            [],
            [],
            $this->getAuthHeaders($this->adminToken),
            json_encode([
                'username' => 'valid_user_1',
                'firstName' => 'Alice',
                'lastName' => 'Durand',
                'email' => 'alice@example.com',
            ])
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertSame('valid_user_1', $data['username']);
    }

    public function testCreateCustomerWithMissingUsername(): void
    {
        $this->client->request(
            'POST',
            '/api/customers',
            [],
            [],
            $this->getAuthHeaders($this->adminToken),
            json_encode([
                'firstName' => 'Alice',
                'lastName' => 'Durand',
                'email' => 'alice@example.com',
            ])
        );

        $this->assertResponseStatusCodeSame(400);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('errors', $data);
        $this->assertStringContainsString('username', json_encode($data));
    }

    public function testUnauthorizedAccess(): void
    {
        $this->client->request(
            'POST',
            '/api/customers',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json', 'ACCEPT' => 'application/ld+json'], // pas de token
            json_encode([
                'username' => 'user123',
                'firstName' => 'Alice',
                'lastName' => 'Durand',
                'email' => 'alice@example.com',
            ])
        );

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUpdateCustomer(): void
    {
        // Crée un client d’abord
        $this->client->request(
            'POST',
            '/api/customers',
            [],
            [],
            $this->getAuthHeaders($this->adminToken),
            json_encode([
                'username' => 'user_update',
                'firstName' => 'Initial',
                'lastName' => 'Name',
                'email' => 'initial@example.com'
            ])
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $id = $data['id'];

        // Modifier ce client
        $this->client->request(
            'PUT',
            "/api/customers/$id",
            [],
            [],
            $this->getAuthHeaders($this->adminToken),
            json_encode([
                'firstName' => 'Updated',
                'lastName' => 'Name'
            ])
        );

        $this->assertResponseIsSuccessful();
        $updatedData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('Updated', $updatedData['firstName']);
    }

    public function testDeleteCustomer(): void
    {
        // Créer un client à supprimer
        $this->client->request(
            'POST',
            '/api/customers',
            [],
            [],
            $this->getAuthHeaders($this->adminToken),
            json_encode([
                'username' => 'user_to_delete',
                'firstName' => 'Delete',
                'lastName' => 'Me',
                'email' => 'delete@example.com'
            ])
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $id = $data['id'];

        // Supprimer le client
        $this->client->request(
            'DELETE',
            "/api/customers/$id",
            [],
            [],
            $this->getAuthHeaders($this->adminToken)
        );

        $this->assertResponseStatusCodeSame(204);
    }

    public function testAdminCanFilterCustomers(): void
    {
        $this->client->request(
            'GET',
            '/api/customers?username=user',
            [],
            [],
            $this->getAuthHeaders($this->adminToken)
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
    }

    // Ici il faut implémenter une méthode getValidUserToken() similaire si tu veux tester un utilisateur non admin.
    // Pour l'exemple, je mets un token vide qui devrait provoquer une erreur 403 ou 401.
    public function testUserCannotAccessCustomerList(): void
    {
        $userToken = ''; // ou récupère un token utilisateur

        $this->client->request(
            'GET',
            '/api/customers',
            [],
            [],
            $this->getAuthHeaders($userToken)
        );

        $this->assertResponseStatusCodeSame(403); // ou 401 selon ta config de sécurité
    }
}
