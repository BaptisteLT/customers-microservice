<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Customer;
use PHPUnit\Framework\TestCase;

class CustomerValidationTest extends TestCase
{
    /*public function testValidNameCharacters()
    {
        $customer = new Customer();

        // Test pour firstName avec des caractères valides
        $customer->setFirstName('Jean-Étienne');
        $this->assertTrue($this->hasValidNameCharacters($customer->getFirstName()));

        // Test pour lastName avec des caractères valides
        $customer->setLastName('Dupont-Martìn');
        $this->assertTrue($this->hasValidNameCharacters($customer->getLastName()));
    }*/

    public function testInvalidNameCharacters()
    {
        $customer = new Customer();

        // Test pour firstName avec des caractères invalides
        $customer->setFirstName('John123');
        $this->assertFalse($this->hasValidNameCharacters($customer->getFirstName()));

        // Test pour lastName avec des caractères invalides
        $customer->setLastName('Doe@');
        $this->assertFalse($this->hasValidNameCharacters($customer->getLastName()));
    }

    public function testFieldLengthLimits()
    {
        $customer = new Customer();

        // Test pour firstName avec une longueur valide
        $customer->setFirstName(str_repeat('a', 255));
        $this->assertTrue($this->hasValidLength($customer->getFirstName(), 255));

        // Test pour lastName avec une longueur valide
        $customer->setLastName(str_repeat('b', 255));
        $this->assertTrue($this->hasValidLength($customer->getLastName(), 255));

        // Test pour firstName avec une longueur invalide
        $customer->setFirstName(str_repeat('a', 256));
        $this->assertFalse($this->hasValidLength($customer->getFirstName(), 255));

        // Test pour lastName avec une longueur invalide
        $customer->setLastName(str_repeat('b', 256));
        $this->assertFalse($this->hasValidLength($customer->getLastName(), 255));
    }

    public function testNonEmptyCompanyName()
    {
        $customer = new Customer();

        // Test pour companyName non vide
        $customer->setCompanyName('Tech Corp');
        $this->assertFalse($customer->getCompanyName() === '');

        // Test pour companyName vide
        $customer->setCompanyName('');
        $this->assertTrue($customer->getCompanyName() === '');
    }

    public function testValidPassword()
    {
        $customer = new Customer();

        // Test pour un mot de passe valide
        $customer->setPassword('ValidPass1!');
        $this->assertTrue($this->isPasswordValid($customer->getPassword()));
    }

    public function testInvalidPassword()
    {
        $customer = new Customer();

        // Test pour un mot de passe trop court
        $customer->setPassword('Short1!');
        $this->assertFalse($this->isPasswordValid($customer->getPassword()));

        // Test pour un mot de passe sans majuscule
        $customer->setPassword('invalidpass1!');
        $this->assertFalse($this->isPasswordValid($customer->getPassword()));

        // Test pour un mot de passe sans minuscule
        $customer->setPassword('INVALIDPASS1!');
        $this->assertFalse($this->isPasswordValid($customer->getPassword()));

        // Test pour un mot de passe sans chiffre
        $customer->setPassword('InvalidPass!');
        $this->assertFalse($this->isPasswordValid($customer->getPassword()));

        // Test pour un mot de passe sans caractère spécial
        $customer->setPassword('InvalidPass1');
        $this->assertFalse($this->isPasswordValid($customer->getPassword()));
    }

    // Méthode utilitaire pour vérifier les caractères valides dans les noms
    private function hasValidNameCharacters($name)
    {
        return preg_match('/^[a-zA-ZàâäéèêëîïôöùûüÿçæœÀÂÄÉÈÊËÎÏÔÖÙÛÜŸÇÆŒ\-\s]+$/', $name) === 1;
    }

    // Méthode utilitaire pour vérifier la longueur des champs
    private function hasValidLength($field, $maxLength)
    {
        return strlen($field) <= $maxLength;
    }

    // Méthode utilitaire pour vérifier la validité du mot de passe
    private function isPasswordValid($password)
    {
        // Vérifier la longueur minimale
        if (strlen($password) < 8) {
            return false;
        }

        // Vérifier la présence d'au moins une majuscule
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }

        // Vérifier la présence d'au moins une minuscule
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }

        // Vérifier la présence d'au moins un chiffre
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }

        // Vérifier la présence d'au moins un caractère spécial
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            return false;
        }

        return true;
    }
}
