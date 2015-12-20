<?php

use League\Container\Container;
use League\Container\ContainerInterface;
use League\Route\RouteCollection;
use League\Route\Strategy\RequestResponseStrategy;
use League\Route\Strategy\RestfulStrategy;

// Load the route collection. If container is not ready, generate one here now.
$route = new RouteCollection(
    (isset($container) && $container instanceof ContainerInterface) ? $container : new Container
);

/**
 * Routes
 */
$route->get('/', 'Ps2alerts\Api\Controller\MainController::index');

// Alert Endpoint
$route->get(
    '/v2/alert/latest',
    'Ps2alerts\Api\Controller\Alerts\ResultsEndpointController::listLatest',
    new RestfulStrategy
);
$route->get(
    '/v2/alert/latest/{serverID}',
    'Ps2alerts\Api\Controller\Alerts\ResultsEndpointController::listLatest',
    new RestfulStrategy
);
$route->get(
    '/v2/alert/latest/{serverID}/{limit}',
    'Ps2alerts\Api\Controller\Alerts\ResultsEndpointController::listLatest',
    new RestfulStrategy
);

$route->get(
    '/v2/alert/active',
    'Ps2alerts\Api\Controller\Alerts\ResultsEndpointController::listActive',
    new RestfulStrategy
);
$route->get(
    '/v2/alert/active/{serverID}',
    'Ps2alerts\Api\Controller\Alerts\ResultsEndpointController::listActive',
    new RestfulStrategy
);

$route->get(
    '/v2/alert/{resultID}',
    'Ps2alerts\Api\Controller\Alerts\ResultsEndpointController::readSingle',
    new RestfulStrategy
);

// Metrics Routes
// - Map
$route->get(
    '/v2/metrics/map/{resultID}',
    'Ps2alerts\Api\Controller\Metrics\MapMetricsEndpoint::readSingle',
    new RestfulStrategy
);

$route->get(
    '/v2/metrics/map/{resultID}/latest',
    'Ps2alerts\Api\Controller\Metrics\MapMetricsEndpoint::readLatest',
    new RestfulStrategy
);

$route->get(
    '/v2/metrics/mapInitial/{resultID}',
    'Ps2alerts\Api\Controller\Metrics\MapInitialMetricsEndpoint::readSingle',
    new RestfulStrategy
);

// - Outfits
$route->get(
    '/v2/metrics/outfit/{resultID}',
    'Ps2alerts\Api\Controller\Metrics\OutfitMetricsEndpoint::readSingle',
    new RestfulStrategy
);

// - Populations
$route->get(
    '/v2/metrics/population/{resultID}',
    'Ps2alerts\Api\Controller\Metrics\PopulationMetricsEndpoint::readSingle',
    new RestfulStrategy
);

// - Combat History
$route->get(
    '/v2/metrics/combathistory/{resultID}',
    'Ps2alerts\Api\Controller\Metrics\CombatHistoryMetricsEndpoint::readSingle',
    new RestfulStrategy
);

// - Factions
$route->get(
    '/v2/metrics/faction/{resultID}',
    'Ps2alerts\Api\Controller\Metrics\FactionMetricsEndpoint::readSingle',
    new RestfulStrategy
);

// - Players
$route->get(
    '/v2/metrics/player/{resultID}',
    'Ps2alerts\Api\Controller\Metrics\PlayerMetricsEndpoint::readSingle',
    new RestfulStrategy
);

// Statistics routes
// - Outfit Totals
$route->post(
    '/v2/statistics/outfitTotals',
    'Ps2alerts\Api\Controller\Statistics\OutfitTotalsMetricsEndpoint::readStatistics',
    new RestfulStrategy
);

/**
 * Return the dispatcher to the app loader
 */
return $route->getDispatcher();
