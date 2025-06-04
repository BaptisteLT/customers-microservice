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
}
