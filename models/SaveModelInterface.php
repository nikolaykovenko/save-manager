<?php

namespace nikolaykovenko\savemanager\models;

interface SaveModelInterface
{
    public function save();
    public function getUniqueId();
    public function validate();
    public function toArray();
}
