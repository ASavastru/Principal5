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
    public function __construct(protected View $view, protected EntityManager $db, protected Auth $auth)
    {
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $insertedDate = $request->getQueryParams()['insertedDate'] ?? false;
        if($insertedDate){
            $appointments = $this->db->getRepository(Appointment::class)->matching(
                Criteria::create()->where(Criteria::expr()->eq('date', new \DateTime($insertedDate)))
            )->getValues();
//            dd($appointments);
            return $this->view->render(new Response, 'templates/calendar.twig', ["appointments"=>$appointments]);
        }
        return $this->view->render(new Response, 'templates/calendar.twig');
    }

    public function createAppointment(ServerRequestInterface $request): ResponseInterface
    {
        dd($request);
        $appointment = new Appointment();

        $appointment->fill([
            'user' => $this->auth->user(),
            'location' => /* find by id */ ,
            'date' => $request->getParsedBody()['insertedDate']
        ]);

        $this->db->persist($appointment);
        $this->db->flush();

        return $appointment;
    }
}