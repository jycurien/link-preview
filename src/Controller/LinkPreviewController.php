<?php

namespace App\Controller;

use App\Service\PreviewService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LinkPreviewController extends AbstractController
{
    #[Route('/preview/{url}', name: 'preview', requirements: ['url' => '.+'])]
    public function preview(PreviewService $previewService, string $url)
    {
        return $this->json($previewService->getPreview($url));
    }
}