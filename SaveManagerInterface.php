<?php

namespace nikolaykovenko\savemanager;

use nikolaykovenko\savemanager\models\SaveModelInterface;

interface SaveManagerInterface
{
    public function save(SaveModelInterface $model): SaveModelInterface;
}
