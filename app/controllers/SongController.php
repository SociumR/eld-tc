<?php


namespace App\Controllers;


use App\Models\Song;
use App\Services\SongsService;
use App\Services\MainService;
use App\Services\MongoService;

class SongController extends AbstractController
{

    /**
     *
     *
     * @OA\Post(
     *     path="/songs",
     *     tags={"Songs"},
     *     summary="Create song",
     *      security={{"api_key":{}}},
     *       @OA\RequestBody(
     *       required=true,
     *      @OA\JsonContent(ref="#/components/schemas/Song"),
     *
     *   ),
     *     @OA\Response(
     *     response=200,
     *     description="Song created",
     *     @OA\JsonContent(ref="#/components/schemas/PartnerDocument"),
     *     ),
     * )
     *
     * @return array
     * @throws \Phalcon\Http\Request\Exception
     */


    public function post()
    {

        $service = new SongsService();

        $result = $service->create($this->request->getJsonRawBody());

        // $this->story($this->request->getJsonRawBody(), 'songs', 'Created song');

        return MainService::structuralMongoObjectToJson($result);

    }


    /**
     * @OA\Get(
     *     path="/songs",
     *     summary="Get all songs",
     *     tags={"Songs"},
     *     description="List of all songs",
     *     operationId="getSong",
     *      security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         description="Title of song",
     *         required=false,
     *         example="asdasds",
     *         @OA\Schema(
     *           type="string"
     *         ),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="author",
     *         in="query",
     *         description="Author of song",
     *         required=false,
     *         @OA\Schema(
     *           type="string"
     *         ),
     *         style="form"
     *     ),
     *      OA\Parameter(
     *       name="sort",
     *         in="query",
     *         description="Sort of songs",
     *         required=false,
     *         example="sort=title:ASC",
     *         @OA\Schema(
     *           type="string"
     *         ),
     *         style="form"
     *     ),
     *     
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="object",
     *              ref="#/components/schemas/Song"
     *         ),
     *     ),
     *     @OA\Response(
     *     description="All errors in response boby",
     *         response="400",
     *     )
     * )
     */

    public function get()
    {
        $service = new SongsService();
        $service->setFilters( $this->request->getQuery(), Song::class);
        return $service->pageResponse($service->find(Song::class, 'songs'));
    }


    public function put($id)
    {

        if (!MongoService::validateMongoId($id)) {
            throw new Exception("Invalid id", 400);
        }

        $service = new SongsService();

        $result = $service->update($id, $this->request->getJsonRawBody());

        $this->story($this->request->getJsonRawBody(), 'songs', 'Updated song');

        return MainService::structuralMongoObjectToJson($result);
    }

    public function delete($id)
    {
        if (!MongoService::validateMongoId($id)) {
            throw new Exception("Invalid id", 400);
        }

        $this->story($this->request->getJsonRawBody(), 'songs', 'Delete song');

        return (new SongsService)->delete($id);
    }

}