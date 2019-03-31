<?php

declare(strict_types=1);

namespace App\UI\Web\Controller;

use App\Application\Command\CommandBus;
use App\Application\ReadModel\Poll\PollRepository;
use App\UI\Common\Form\Poll\CreatePollType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/polls", name="polls_")
 */
class PollController extends AbstractController
{
    private $commandBus;
    private $pollRepository;


    public function __construct(CommandBus $commandBus, PollRepository $pollRepository)
    {
        $this->commandBus     = $commandBus;
        $this->pollRepository = $pollRepository;
    }


    /**
     * @Route("/{id<%uuid%>}", name="overview")
     */
    public function overviewAction(string $id) : Response
    {
        $poll = $this->pollRepository->fetchById($id);

        if (!$poll) {
            throw new NotFoundHttpException('Poll could not be found');
        }

        return $this->render('poll/overview.html.twig', ['poll' => $poll]);
    }


    /**
     * @Route("/create", name="create", methods={"GET", "POST"})
     */
    public function createAction(Request $request) : Response
    {
        $form = $this->createForm(CreatePollType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->commandBus->execute($form->getData());
            $this->addFlash('success', 'Please wait whilst your poll is configured!');

            return $this->redirectToRoute('polls_list');
        }

        return $this->render('poll/create.html.twig', ['createPollForm' => $form->createView()]);
    }


    /**
     * @Route("", name="list")
     */
    public function listAction() : Response
    {
        return $this->render('poll/list.html.twig', ['polls' => $this->pollRepository->fetchAll()]);
    }
}
