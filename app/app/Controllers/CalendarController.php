<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Auth\Auth;
use App\Entities\Location;
use App\Entities\Appointment;
use App\Views\View;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response;
use League\Route\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CalendarController
{
    private string $DEFAULT_LOCATION_ID = "1";

    public function __construct(protected View $view, protected EntityManager $db, protected Auth $auth, protected Router $router)
    {
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $locations = $this->db->getRepository(Location::class)->findAll(); // pulls locations
        $defaultLocation = $this->db->getRepository(Location::class)->findOneBy([
            'id' => $request->getQueryParams()['locationFilterGet'] ?? $this->DEFAULT_LOCATION_ID,
        ]);
        $insertedDate = $request->getQueryParams()['insertedDate'] ?? false;
        if(isset($insertedDate)){
            $appointments = $this->db->getRepository(Appointment::class)->findBy([
                'date' => new \DateTime($insertedDate),
                'location' => $request->getQueryParams()['locationFilterGet'],
            ]);
            //when clicking on calendar from home, lines 36->39 must be commented

            return $this->view->render(new Response, 'templates/calendar.twig',
                [
                    "appointments"=>$appointments, //when clicking on calendar from home, must be commented
                    "locations"=>$locations,
                    "defaultLocation"=>$defaultLocation
                ]);
        }
        return $this->view->render(new Response, 'templates/calendar.twig',
            [
                "locations"=>$locations,
                "defaultLocation"=>$defaultLocation
            ]);
    }

    public function createAppointment(ServerRequestInterface $request): ResponseInterface
    {
        $location = $this->db->getRepository(Location::class)->findOneBy([
            'id' => $request->getParsedBody()['locationFilter']
        ]); // pulls locations
        $appointment = new Appointment();

        $sameDayValidator = $this->db->getRepository(Appointment::class)->count([
            'user' => $this->auth->user(),
            'date' => \DateTime::createFromFormat('Y-m-d', $request->getParsedBody()['insertedDate']),
        ]);

        if($sameDayValidator > 0){
            return new Response\JsonResponse("You already have an appointment for " . $request->getParsedBody()['insertedDate'] . " " . $sameDayValidator);
        }

        $appointment->fill([
            'user' => $this->auth->user(), // ??
            'location' => $location,
            'date' => \DateTime::createFromFormat('Y-m-d', $request->getParsedBody()['insertedDate'])
        ]);

        $this->db->persist($appointment);
        $this->db->flush();

        return new Response\JsonResponse([
            'appointment' => $appointment->user->name,
        ]);
    }
}