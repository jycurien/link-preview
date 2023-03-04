<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LinkPreviewController
{
    #[Route('/', name: 'preview')]
    public function preview()
    {
        return new Response('TODO');
    }
}