<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

class SecurityController extends AbstractFOSRestController
{
    /**
     * Get a JWT token to authenticate queries.
     *
     * @Rest\Post(
     *      path = "/api/login_check",
     *      name = "api_login_check"
     * )
     *
     * @SWG\Post(
     *     description="Authentication : Get a JWT token to authenticate queries",
     *     tags = {"Authentication"},
     *     @SWG\Response(
     *          response=200,
     *          description="Successful operation: JWT returned",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(property="token", type="string")
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="401",
     *          description="Unauthorized: Invalid credentials"),
     *     @SWG\Parameter(
     *          name="Body",
     *          required= true,
     *          in="body",
     *          type="string",
     *          description="Client username and password",
     *          @SWG\Schema(
     *              type="array",
     *              example={"username": "user@bilemo.com", "password": "user"},
     *              @SWG\Items()
     *          )
     *     )
     * )
     */
    public function login()
    {
    }
}
