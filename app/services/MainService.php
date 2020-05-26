<?php


namespace App\Services;


use App\Models\Countries;
use App\Models\Settings;
use Phalcon\Http\Request\Exception;
use PhpOffice\PhpWord\Shared\ZipArchive;

class MainService extends ApiService
{
    /**
     * @OA\Schema(
     *     schema="MainSettings",
     *     type="object",
     *              @OA\Property(
     *                  property="legalDetails",
     *                  type="object",
     *                  ref="#/components/schemas/LegalDetails"
     *              )
     *      )
     * )
     */

    /**
     * @param $raw
     * @return array
     * @throws Exception
     * @throws \Phalcon\Mvc\Collection\Exception
     */

    public static $countries;

    public function saveSettings($raw)
    {
        if (!$raw) {
            $this->sendException('empty request body');
        }

        $model = Settings::findFirst() ?: new Settings();

        if (!$model->load($raw) || !$model->save()) {
            $this->buildErrorsFromModel($model);
        }

        return self::settings();
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function settings()
    {
        /** @var Settings $model */
        $model = Settings::findFirst();

        if (!$model) {
            (new MainService)->sendException('settings not installed');
        }

        return [
            'legalDetails' => $model->getLegalDetails()
        ];
    }

    /**
     * @param $model
     * @param array $skipKeys
     * @return array
     */
    public static function structuralMongoObjectToJson($model, $skipKeys = [])
    {
        $response = [];

        if (is_array($model)) {

            foreach ($model as $item) {
                $response[] = self::structural($item, $skipKeys);
            }
        } else {
            $response = self::structural($model, $skipKeys);
        }

        return $response;
    }

    public static function structural($model, $skipKeys)
    {
        $response = [];

        foreach ($model as $k => $value) {

            if (in_array($k, $skipKeys)) {
                continue;
            }



            if ($k == '_id') {
                $response['id'] = (string) $value;
                continue;
            }


            $response[$k] = $value;
        }
        return $response;
    }


    public static function countries($key = null)
    {
        $countries = Countries::find([]);

        if($key && property_exists(new Countries(), $key)) {
            $arr = [];

            foreach ($countries as $country) {
                $arr[] = $country->$key;
            }

            $countries = $arr;
        }

        return $countries;
    }

    /**
     * @param array $files
     * @param bool $removeFiles
     * @return string
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public static function zip($files, $removeFiles = false)
    {
        $zip = new ZipArchive();
        $dir = __DIR__ . '/../../temp/';
        $zip_name = $dir . time().".zip"; // Zip name
        $zip->open($zip_name,  ZipArchive::CREATE);
        foreach ($files as $file) {
            if(file_exists($file)){
                $zip->addFromString(basename($file),  file_get_contents($file));
            }
        }

        if($zip->close() && $removeFiles) {
            FilesService::deleteFiles($files);
        }

        return $zip->filename;
    }


}