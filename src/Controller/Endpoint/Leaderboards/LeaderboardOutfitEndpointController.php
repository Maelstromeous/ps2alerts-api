<?php

namespace Ps2alerts\Api\Controller\Endpoint\Leaderboards;

use League\Fractal\Manager;
use Ps2alerts\Api\Controller\Endpoint\AbstractEndpointController;
use Ps2alerts\Api\Controller\Endpoint\Leaderboards\AbstractLeaderboardEndpointController;
use Ps2alerts\Api\Exception\CensusEmptyException;
use Ps2alerts\Api\Exception\CensusErrorException;
use Ps2alerts\Api\Repository\Metrics\OutfitTotalRepository;
use Ps2alerts\Api\Transformer\Leaderboards\OutfitLeaderboardTransformer;

class LeaderboardOutfitEndpointController extends AbstractLeaderboardEndpointController
{
    protected $repository;

    /**
     * Construct
     *
     * @param League\Fractal\Manager $fractal
     */
    public function __construct(
        Manager                $fractal,
        OutfitTotalRepository  $repository
    ) {

        $this->fractal = $fractal;
        $this->repository = $repository;
    }

    /**
     * Get Outfit Leaderboard
     *
     * @return \League\Fractal\Manager
     */
    public function outfits()
    {
        $valid = $this->validateRequestVars();

        // If validation didn't pass, chuck 'em out
        if ($valid !== true) {
            return $this->errorWrongArgs($valid->getMessage());
        }

        $server = $_GET['server'];
        $limit  = $_GET['limit'];
        $offset = $_GET['offset'];

        // Translate field into table specific columns

        if (isset($_GET['field'])) {
            $field = $this->getField('outfits', $_GET['field']);
        }

        if (! isset($field)) {
            return $this->errorWrongArgs('Field wasn\'t provided and is required.');
        }

        // Perform Query
        $query = $this->outfitTotalRepository->newQuery();
        $query->cols(['*']);
        $query->orderBy(["{$field} desc"]);
        $query->where('outfitID > 0');

        if (isset($server)) {
            $query->where('outfitServer = ?', $server);
        }

        if (isset($limit)) {
            $query->limit($limit);
        } else {
            $query->limit(10); // Set default limit
        }

        if (isset($offset)) {
            $query->offset($offset);
        }

        return $this->respond(
            'collection',
            $this->outfitTotalRepository->fireStatementAndReturn($query),
            new OutfitLeaderboardTransformer
        );
    }

    /**
     * Gets the appropiate field for the table and handles some table naming oddities
     * @param  string $input Field to look at
     * @return string
     */
    public function getField($input) {
        $field = null;
        switch ($input) {
            case 'kills':
                $field = 'outfitKills';
                break;
            case 'deaths':
                $field = 'outfitDeaths';
                break;
            case 'teamkills':
                $field = 'outfitTKs';
                break;
            case 'suicides':
                $field = 'outfitSuicides';
                break;
            case 'captures':
                $field = 'outfitCaptures';
                break;
        }

        return $field;
    }
}
