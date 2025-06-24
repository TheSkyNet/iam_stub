<?php
namespace IamLab\Service\Filepond;
use IamLab\Core\API\aAPI;
use IamLab\Service\Filepond\FilepondService;
use Phalcon\Http\Response;
use Phalcon\Http\ResponseInterface;
use Throwable;
use function App\Core\Helpers\config;

/**
 * @property FilepondService $filepond
 */
class FilepondApi extends aAPI
{

    public function process()
    {
        $this->filepond;
        // Check if chunk upload
        if ($this->request->hasHeader('upload-chunk')) {
            $this->response->setContentType('text/plain');
            $this->response->setContent($this->filepond->initChunk());
            return $this->response;
        }

        $validator = $this->filepond->validator($this->request, config('filepond')['validation_rules']);

//         if ($validator->fails()) {
//              $this->dispatchError($validator->errors());
//         }

        $this->response->setContentType('text/plain');
        $this->response->setContent($this->filepond->store($this->request));
        return $this->response;
    }

    /**
     * FilePond ./patch route logic.
     *
     * @return Response|ResponseInterface
     * @throws Throwable
     */
    public function patch()
    {
        $this->response->setHeader('upload-offset', $this->filepond->chunk($this->request));
        $this->response->setContentType('text/plain');
        $this->response->setContent('Ok');
        return $this->response;
        //   return Response::make('Ok', 200)->withHeaders(['upload-offset' => $service->chunk($request)]);
    }

    /**
     * FilePond ./head route logic.
     *
     * @param Request $request
     * @param FilepondService $service
     * @return \Illuminate\Http\Response
     * @throws Throwable
     */
    public function head(Request $request, FilepondService $service)
    {
        if ($this->request->isMethod('head')) {
            $this->response->setHeader('upload-offset', $service->offset($request->patch));
            $this->response->setContentType('text/plain');
            $this->response->setContent('Ok');
            return $this->response;
         //   return Response::make('Ok', 200)->withHeaders(['upload-offset' => $service->offset($request->patch)]);
        }

        //  return Response::make('Feature not implemented yet!', 406);
    }

    /**
     * FilePond ./revert route logic.
     *
     * @param Request $request
     * @param FilepondService $service
     * @return \Illuminate\Http\Response
     */
    public function revert(Request $request, FilepondService $service)
    {
        $filepond = $service->retrieve($request->getContent());

        $service->delete($filepond);

        //    return Response::make('Ok', 200, ['content-type' => 'text/plain']);
    }
}
