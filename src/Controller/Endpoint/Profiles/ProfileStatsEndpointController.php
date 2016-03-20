<?php

namespace Ps2alerts\Api\Controller\Endpoint\Profiles;

use League\Fractal\Manager;
use Ps2alerts\Api\Controller\Endpoint\AbstractEndpointController;
use Ps2alerts\Api\Exception\InvalidArgumentException;
use Ps2alerts\Api\Transformer\Profiles\PlayerTransformer;
use Ps2alerts\Api\Transformer\Profiles\OutfitTransformer;
use Ps2alerts\Api\Repository\Metrics\PlayerTotalRepository;
use Ps2alerts\Api\Repository\Metrics\OutfitTotalRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileStatsEndpointController extends AbstractEndpointController
{
    /**
     * Construct
     *
     * @param League\Fractal\Manager                     $fractal
     */
    public function __construct(
        Manager               $fractal,
        PlayerTotalRepository $playerTotalRepo,
        OutfitTotalRepository $outfitTotalRepo,
        PlayerTransformer     $playerTransformer,
        OutfitTransformer     $outfitTransformer
    ) {
        $this->fractal           = $fractal;
        $this->playerRepository  = $playerTotalRepo;
        $this->outfitRepository  = $outfitTotalRepo;
        $this->playerTransformer = $playerTransformer;
        $this->outfitTransformer = $outfitTransformer;
    }

    /**
     * Endpoint to return potential players based on search term
     *
     * @param  Symfony\Component\HttpFoundation\Request  $request
     * @param  Symfony\Component\HttpFoundation\Response $response
     * @param  array                                     $args
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getPlayersByTerm(Request $request, Response $response, array $args)
    {
        // If a valid player name we're searching on
        if ($this->parsePlayerName($args['term'])) {
            $players = $this->searchForPlayer($args['term']);

            if (! empty($players)) {
                return $this->respond('collection', $players, $this->playerTransformer, $request, $response);
            }

            return $this->errorEmpty($response);
        }
    }

    /**
     * Endpoint to return potential players based on search term
     *
     * @param  Symfony\Component\HttpFoundation\Request  $request
     * @param  Symfony\Component\HttpFoundation\Response $response
     * @param  array                                     $args
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getOutfitsByTerm(Request $request, Response $response, array $args)
    {
        $name = urldecode($args['term']); // Spaces will have to URL encoded

        // If a valid outfit name we're searching on
        if ($this->parseOutfitName($name)) {
            $outfits = $this->searchForOutfit($name);

            if (! empty($outfits)) {
                return $this->respond('collection', $outfits, $this->outfitTransformer, $request, $response);
            }

            return $this->errorEmpty($response);
        }
    }

    /**
     * Takes a player name and searches for it
     *
     * @param  string $term
     *
     * @todo SQL injection prevention
     *
     * @return array
     */
    public function searchForPlayer($term)
    {
        $query = $this->playerRepository->newQuery();
        $query->cols(['*']);
        $query->where("playerName LIKE '%{$term}%'");

        return $this->playerRepository->readRaw($query->getStatement());
    }

    /**
     * Takes a outfit name and searches for it
     *
     * @param  string $term
     *
     * @todo SQL injection prevention
     *
     * @return array
     */
    public function searchForOutfit($term)
    {
        $query = $this->outfitRepository->newQuery();
        $query->cols(['*']);
        $query->where("outfitName LIKE '%{$term}%'");

        return $this->outfitRepository->readRaw($query->getStatement());
    }

    /**
     * Parses a player name and makes sure it's valid
     *
     * @param  String $name
     *
     * @return boolean
     */
    public function parsePlayerName($name)
    {
        if (empty($name)) {
            return $this->errorWrongArgs($response, 'Player name needs to be present.');
        }

        if (strlen($name > 24)) {
            return $this->errorWrongArgs($response, 'Player names cannot be longer than 24 characters.');
        }

        return true;
    }

    /**
     * Parses a outfit name and makes sure it's valid
     *
     * @param  String $name
     *
     * @return boolean
     */
    public function parseOutfitName($name)
    {
        if (empty($name)) {
            return $this->errorWrongArgs($response, 'Outfit name needs to be present.');
        }

        if (strlen($name > 32)) {
            return $this->errorWrongArgs($response, 'Outfit names cannot be longer than 32 characters.');
        }

        return true;
    }

    /**
     * Runs checks on the player ID
     *
     * @param  string $id
     *
     * @return boolean
     */
    public function parsePlayerID($id)
    {
        if (empty($id)) {
            return $this->errorWrongArgs($response, 'Player ID needs to be present.');
        }

        if (strlen($id > 19)) {
            return $this->errorWrongArgs($response, 'Player ID cannot be longer than 19 characters.');
        }

        if (! is_numeric($id)) {
            return $this->errorWrongArgs($response, 'Player ID must be numeric.');
        }

        return true;
    }
}
