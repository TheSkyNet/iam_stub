<?php

namespace IamLab\Service\Filepond;

use Carbon\Carbon;
use ErrorException;
use IamLab\Model\Filepond;
use Illuminate\Contracts\Validation\Validator;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Phalcon\Http\Request;
use SodiumException;
use Throwable;
use function App\Core\Helpers\collect;
use function App\Core\Helpers\config;
use function App\Core\Helpers\dd;
use function App\Core\Helpers\decrypt;
use function App\Core\Helpers\di;
use function App\Core\Helpers\moveTo;

/**
 * @property Filesystem $tmp Filesystem
 */
class FilepondService
{
    private $disk;
    private $tempDisk;
    private $tempFolder;

    public function __construct()
    {
        $this->disk = config('filepond', 'public')['disk'];
        $this->tempDisk = TMP_DISK;
        $this->tempFolder = TMP_PATH;
    }

    /**
     * Get the file from request
     *
     * @param Request $request
     * @return mixed
     */
    protected function getUploadedFile(Request $request)
    {
        return collect($request->getUploadedFiles())->first();
    }

    /**
     * Validate the filepond file
     *
     * @param Request $request
     * @param array $rules
     * @return Validator
     */
    public function validator(Request $request, array $rules)
    {
        // $field = array_key_first($request->all());
        return $this->getUploadedFile($request);
        //return Validator::make([$field => $this->getUploadedFile($request)], [$field => $rules]);
    }

    /**
     * Store the uploaded file in the fileponds table
     *
     * @param Request $request
     * @return string
     */
    public function store(Request $request): string
    {
        $file = $this->getUploadedFile($request);
        $name = uniqid() . '.' . $file->getExtension();
        try{
            $mimetypes = $file->getRealType();
            $file->moveTo(TMP_DISK . '/' . $name);
        }catch ( ErrorException $exception){
            dd($exception);
        }
        $filepond = (new Filepond)->assign([
            'filepath' => $this->tempDisk,
            'filename' => $name, // $file->getOriginalName(),
            'mimetypes' => $mimetypes,
            'disk' => $this->tempDisk,
            'expires_at' => Carbon::now()->addMinutes(config('expiration', 30))
        ]);

        $filepond->create();
        return \App\Core\Helpers\crypt(
            json_encode(['id' => $filepond->id])
        );
    }

    /**
     * Retrieve the filepond file from encrypted text
     *
     * @param string $content
     * @return mixed
     */
    public function retrieve(string $content)
    {
        $input = decrypt($content);

        return Filepond::where('id', $input['id'])
            ->firstOrFail();
    }

    /**
     * Initialize and make a slot for chunk upload
     *
     * @return string
     * @throws SodiumException
     * @throws FilesystemException
     */
    public function initChunk()
    {
        $filepond = (new Filepond)->assign(
            [
                'filepath' => '',
                'filename' => '',
                'extension' => '',
                'mimetypes' => '',
                'disk' => $this->disk,
                'expires_at' => Carbon::now()->addMinutes(30)
            ]
        );
        $filepond->create();

        $this->tmp->createDirectory($this->tempFolder . '/' . $filepond->id);

        // Storage::disk($this->tempDisk)->makeDirectory($this->tempFolder . '/' . $filepond->id);

        return \App\Core\Helpers\crypt(
            json_encode(['id' => $filepond->id])
        );// Crypt::encrypt(['id' => $filepond->id]);
    }

    /**
     * Merge chunks
     *
     * @param Request $request
     * @return string
     * @throws Throwable
     */
    public function chunk(Request $request)
    {
        $id = decrypt(
            json_decode($request->getPatch())['id']
        );
        // $id = Crypt::decrypt($request->patch)['id'];

        $dir = Storage::disk($this->tempDisk)->path($this->tempFolder . '/' . $id . '/');

        $filename = $request->header('upload-name');
        $length = $request->header('upload-length');
        $offset = $request->header('upload-offset');

        file_put_contents($dir . $offset, $request->getContent());

        $size = 0;
        $chunks = glob($dir . '*');
        foreach ($chunks as $chunk) {
            $size += filesize($chunk);
        }

        if ($length == $size) {
            $file = fopen($dir . $filename, 'w');
            foreach ($chunks as $chunk) {
                $offset = basename($chunk);

                $chunkFile = fopen($chunk, 'r');
                $chunkContent = fread($chunkFile, filesize($chunk));
                fclose($chunkFile);

                fseek($file, $offset);
                fwrite($file, $chunkContent);

                unlink($chunk);
            }
            fclose($file);

            $filepond = $this->retrieve($request->patch);
            $filepond->update([
                'filepath' => $this->tempFolder . '/' . $id . '/' . $filename,
                'filename' => $filename,
                'extension' => pathinfo($filename, PATHINFO_EXTENSION),
                'mimetypes' => Storage::disk($this->tempDisk)->mimeType($this->tempFolder . '/' . $id . '/' . $filename),
                'disk' => $this->disk,
                'created_by' => auth()->id(),
                'expires_at' => now()->addMinutes(config('filepond.expiration', 30))
            ]);
        }

        return $size;
    }

    /**
     * Get the offset of the last uploaded chunk for resume
     *
     * @param string $content
     * @return false|int
     */
    public function offset(string $content)
    {
        $filepond = $this->retrieve($content);

        $dir = Storage::disk($this->tempDisk)->path($this->tempFolder . '/' . $filepond->id . '/');
        $size = 0;
        $chunks = glob($dir . '*');
        foreach ($chunks as $chunk) {
            $size += filesize($chunk);
        }

        return $size;
    }

    /**
     * Delete the filepond file and record respecting soft delete
     *
     * @param Filepond $filepond
     * @return bool|null
     */
    public function delete(Filepond $filepond)
    {
        if (config('filepond.soft_delete', true)) {
            return $filepond->delete();
        }

        Storage::disk($this->tempDisk)->delete($filepond->filepath);
        Storage::disk($this->tempDisk)->deleteDirectory($this->tempFolder . '/' . $filepond->id);

        return $filepond->forceDelete();
    }
}