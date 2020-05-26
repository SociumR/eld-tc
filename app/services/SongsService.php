<?php


namespace App\Services;

use App\Models\Song;

class SongsService extends ApiService
{
    /**
     * @param object $raw
     */
    public function create($raw): Song
    {
        $model = new Song();
        $model->load($raw);
        $errors = $model->validation();

        if (count($errors) > 0) {
            $this->buildErrorsFromValidator($errors);
        }

        $model->save();

        return $model;
    }

    public function delete(string $id): bool
    {
        $song = Song::findById($id);

        if (!$song) {
            $this->sendException("Song not found", 404);
        } 
            
        return $song->delete();
    }


    public function update(string $id, $raw): bool
    {
        $model = Song::findById($id);

        if (!$model) {
            $this->sendException("Song not found", 404);
        }
            
        $model->load($raw);

        $errors = $model->validation();

        if (count($errors) > 0) {
            $this->buildErrorsFromValidator($errors);
        }

        $model->save();

        return $model;
    }
}
