<?php

namespace nikolaykovenko\savemanager\repository;

use nikolaykovenko\savemanager\models\SaveModelInterface;

interface SaveModelRepositoryInterface
{
    /**
     * @param $id
     * @return SaveModelInterface|null
     */
    public function findByUniqueId($id);
}
