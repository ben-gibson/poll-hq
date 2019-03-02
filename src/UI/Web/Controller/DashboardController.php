<?php

declare(strict_types=1);

namespace App\UI\Web\Controller;

use App\Application\ReadModel\Poll\PollRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    private $pollRepository;


    public function __construct(PollRepository $pollRepository)
    {
        $this->pollRepository = $pollRepository;
    }


    /**
     * @Route("", name="dashboard")
     */
    public function indexAction() : Response
    {
        return $this->render('dashboard/index.html.twig', ['recentPolls' => $this->pollRepository->fetchAll(5)]);
    }
}
