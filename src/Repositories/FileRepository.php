<?php

namespace Mlantz\Quarx\Repositories;

use Auth;
use Config;
use Request;
use CryptoService;
use Mlantz\Quarx\Models\Files;
use Mlantz\Quarx\Services\FileService;
use Illuminate\Support\Facades\Schema;

class FileRepository
{

    /**
     * Returns all Files
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return Files::orderBy('created_at', 'desc')->all();
    }

    public function paginated()
    {
        return Files::orderBy('created_at', 'desc')->paginate(Config::get('quarx.pagination', 25));
    }

    public function search($input)
    {
        $query = Files::orderBy('created_at', 'desc');

        $columns = Schema::getColumnListing('files');

        foreach ($columns as $attribute) {
            $query->orWhere($attribute, 'LIKE', '%'.$input['term'].'%');
        };

        return [$query, $input['term'], $query->paginate(Config::get('quarx.pagination', 25))->render()];

    }

    /**
     * Stores Files into database
     *
     * @param array $input
     *
     * @return Files
     */
    public function store($input)
    {
        $result = false;

        foreach ($input['location'] as $_file) {
            $fileInput = $input;
            $fileInput['name'] = $_file['original'];
            $fileInput['location'] = CryptoService::decrypt($_file['name']);
            $fileInput['mime'] = $_file['mime'];
            $fileInput['size'] = $_file['size'];
            $fileInput['order'] = 0;
            $fileInput['user'] = Auth::id();
            $fileInput['is_published'] = (isset($input['is_published'])) ? 1 : 0;
            $result = Files::create($fileInput);
        }

        if ($result) {
            return true;
        }

        return false;
    }

    /**
     * Find Files by given id
     *
     * @param int $id
     *
     * @return \Illuminate\Support\Collection|null|static|Files
     */
    public function findFilesById($id)
    {
        return Files::find($id);
    }

    /**
     * Updates Files into database
     *
     * @param Files $files
     * @param array $input
     *
     * @return Files
     */
    public function update($files, $input)
    {
        if (isset($input['location'])) {
            $savedFile = FileService::saveFile($input['location'], 'files/');
            $_file = $input['location'];

            $fileInput = $input;
            $fileInput['name'] = $savedFile['original'];
            $fileInput['location'] = $savedFile['name'];
            $fileInput['mime'] = $_file->getClientMimeType();
            $fileInput['size'] = $_file->getClientSize();
        } else {
            $fileInput = $input;
        }

        $fileInput['is_published'] = (isset($input['is_published'])) ? 1 : 0;

        $files->fill($fileInput);
        $files->save();

        return $files;
    }

    public function apiPrepared()
    {
        $files = Files::orderBy('created_at', 'desc')->get();
        $allFiles = [];

        foreach ($files as $file) {
            array_push($allFiles, [
                'file_identifier' => CryptoService::encrypt($file->name).'/'.CryptoService::encrypt($file->location),
                'file_name' => $file->name,
                'file_date' => $file->created_at->format('F jS, Y'),
            ]);
        }

        return $allFiles;
    }
}