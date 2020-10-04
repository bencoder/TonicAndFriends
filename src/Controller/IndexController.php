<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Index Controller
 * @Route("/",name="index")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="get", methods={"GEt"})
     */
    public function index(Request $request): Response
    {      
      return new Response("API methods are under /pet path");
    }
}
