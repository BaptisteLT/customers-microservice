<?php

class ResetDatabase implements PHPUnit\Framework\TestListener
{
    public function startTestSuite(PHPUnit\Framework\TestSuite $suite): void
    {
        // Exécuter le script de réinitialisation avant chaque suite de tests
        exec('./scripts/reset_database.sh');
    }

    // Implémentez d'autres méthodes de l'interface TestListener si nécessaire
}
