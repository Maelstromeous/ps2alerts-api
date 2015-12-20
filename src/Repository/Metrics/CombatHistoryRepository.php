<?php

namespace Ps2alerts\Api\Repository\Metrics;

use Ps2alerts\Api\Repository\AbstractEndpointRepository;
use Ps2alerts\Api\QueryObjects\QueryObject;

class CombatHistoryRepository extends AbstractEndpointRepository
{
    /**
     * {@inheritdoc}
     */
    public function getTable()
    {
        return 'ws_combat_history';
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimaryKey()
    {
        return 'dataID';
    }

    /**
     * {@inheritdoc}
     */
    public function getResultKey()
    {
        return 'resultID';
    }
}
