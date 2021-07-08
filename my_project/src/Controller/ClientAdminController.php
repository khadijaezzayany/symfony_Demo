<?php


namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Client;





class ClientAdminController extends AbstractController
{
    /**
     * @Route("/client/{id}", name="client_show")
     */
    public function show(int $id): Response
    {
        $client = $this->getDoctrine()
            ->getRepository(Client::class)
            ->find($id);

        if (!$client) {
            throw $this->createNotFoundException(
                'No Client found for id ' . $id
            );
        }

        return new Response('Information for Client ID:' .$client->getId().' Name :' . $client->getName().'   Email :'.$client->getEmail());
    }
    
     /**
     * @Route("/client/", name="client_show_all")
     */

     public function getAll() {
         $clients = $this->getDoctrine()->getRepository(Client::class)->findAll();
        //  echo '<pre>';
        //  print_r($client);
        //  echo '<pre>';
        //  exit();
         return $this->render("getAll.html.twig",['clients'=> $clients]);
        // $client = $repository->findAll();
        // return $this->render("templates/getall.html.twig"),['post'=>$post]         
     }
}