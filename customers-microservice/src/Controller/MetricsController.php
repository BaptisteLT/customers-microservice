<?php
namespace App\Controller;

use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\Redis;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MetricsController extends AbstractController
{
    #[Route('/metrics', name: 'metrics')]
    public function metrics(): Response
    {
        // Connexion Redis (host = 'redis' car c’est le nom du service Docker dans docker-compose)
        $redisAdapter = new Redis([
            'host' => 'redis',
            'port' => 6379,
        ]);

        // Instanciation du registry avec Redis
        $registry = new CollectorRegistry($redisAdapter);

        // Exemple : compteur simple
        $counter = $registry->getOrRegisterCounter('app', 'requests_total', 'Total des requêtes');
        $counter->inc();

        $renderer = new RenderTextFormat();
        $metrics = $renderer->render($registry->getMetricFamilySamples());

        return new Response($metrics, 200, ['Content-Type' => RenderTextFormat::MIME_TYPE]);
    }
}
