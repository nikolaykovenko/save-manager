<?php

namespace nikolaykovenko\savemanager;

use nikolaykovenko\savemanager\models\SaveModelInterface;
use nikolaykovenko\savemanager\repository\SaveModelRepositoryInterface;

class SaveManager implements SaveManagerInterface
{
    /**
     * @var SaveModelRepositoryInterface
     */
    private $repository;

    /**
     * @var int
     */
    private $delayTime;

    /**
     * @var int
     */
    private $maxAttempts;

    public function __construct(SaveModelRepositoryInterface $repository, $delayTimeMs = 500000, $maxAttempts = 10)
    {
        $this->delayTime = $delayTimeMs;
        $this->maxAttempts = $maxAttempts;
        $this->repository = $repository;
    }

    public function save(SaveModelInterface $model): SaveModelInterface
    {
        if (!$model->validate()) {
            return $model;
        }

        $model->save();

        return $this->waitWhileModelIsIndexing($model);
    }

    private function waitWhileModelIsIndexing(SaveModelInterface $model): SaveModelInterface
    {
        $attempt = 0;

        do {
            if (++$attempt > $this->maxAttempts) {
                throw new \RuntimeException(
                    'Model is not indexed or models comparison errors: ' . $model->getUniqueId()
                );
            }

            usleep($this->delayTime);
            $updatedModel = $this->repository->findByUniqueId($model->getUniqueId());
        } while (!$updatedModel || $this->arrayDiffAssocRecursive($updatedModel->toArray(), $model->toArray()));

        return $updatedModel;
    }

    private function arrayDiffAssocRecursive(array $array1, array $array2)
    {
        $difference = [];
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key]) || !is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = $this->arrayDiffAssocRecursive($value, $array2[$key]);
                    if (!empty($new_diff)) {
                        $difference[$key] = $new_diff;
                    }
                }
            } elseif (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
                $difference[$key] = $value;
            }
        }
        return $difference;
    }
}
