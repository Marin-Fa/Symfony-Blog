<?php
// config/routes.php
use App\Controller\SecurityController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->add('security_registration', '/inscription')
        // the controller value has the format [controller_class, method_name]
        ->controller([SecurityController::class, 'registration'])

        // if the action is implemented as the __invoke() method of the
        // controller class, you can skip the ', method_name]' part:
        // ->controller([BlogController::class])
    ;
};
