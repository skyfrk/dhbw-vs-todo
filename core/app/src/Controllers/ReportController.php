<?php

namespace App\Controllers;

use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use RedBeanPHP\R as R;
use App\Utils\Validations as Validation;
use Respect\Validation\Validator as v;

class ReportController 
{
    private $reportService;
    private $validator;
    
    public function __construct(ContainerInterface $container)
    {
        $this->reportService = $container->get('reportService');
        $this->validator = $container->get('validator');
    }

    public function getReport(Request $request, Response $response)
    {
        $uid = $request->getAttribute('token')['scope']->uid;
        $params = $request->getQueryParams();

        $validation = $this->validator->validate($params, [
            'days' => Validation::reportDays()
        ]);

        if(!$validation->isValid())
        {
            return $response->withJson($validation->getErrors(), 400);
        }

        try
        {
            $report = $this->reportService->getReport($uid, $params['days']);
            return $response->withJson($report, 200);
        }
        catch (\Throwable $th)
        {
            return $response->withStatus(500);
        }
    }
}