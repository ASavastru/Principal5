<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Auth\Auth;
use App\Entities\Appointment;
use App\Views\View;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CalendarController
{
    public function __construct(protected View $view, protected EntityManager $db)
    {
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $insertedDate = $request->getQueryParams()['insertedDate'];
        $appointments = $this->db->getRepository(Appointment::class)->matching(
            Criteria::create()->where(Criteria::expr()->eq('date', new \DateTime($insertedDate)))
        )->getValues();

        dd($appointments[0]->getUser());
        return $this->view->render(new Response, 'templates/calendar.twig');
    }
}