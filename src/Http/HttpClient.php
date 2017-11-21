<?php declare(strict_types = 1);

namespace Markette\Fio\Http;

use CURLFile;
use Markette\Fio\Exceptions\IOException;

/**
 * HttpClient using curl
 *
 * @author Filip Suska <vody105@gmail.com>
 */
class HttpClient implements IHttpClient
{

    /**
     * @param Request $request
     * @return string
     */
    public function sendRequest(Request $request): string
    {
        // Init
        $ch = curl_init();

        // Url
        curl_setopt($ch, CURLOPT_URL, $request->getUrl());

        // Custom request
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request->getRequestType());

        // If request is send as form post fields
        if ($request->getRequestType() === Request::POST) {
            // POST vars
            $postFiels = [
                'token' => $request->getToken(),
                'lng'   => $request->getLang(),
            ];

            // If file
            if ($request->hasFile()) {

                // Create tempfile
                $xmlFile = tmpfile();
                if (!$xmlFile) {
                    throw new IOException('Could not create temporary file.');
                }

                // Write data to file
                if (!fwrite($xmlFile, $request->getFileContents())) {
                    throw new IOException('Could not write to temporary file.');
                }

                $metaData = stream_get_meta_data($xmlFile);
                $filename = $metaData['uri'];

                $postFiels['file'] = new CURLFile($filename);
                $postFiels['type'] = $request->getFileType();
            }

            curl_setopt($ch, CURLOPT_POST, count($postFiels));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFiels);
        }

        // Receive response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        // Execute
        $result = curl_exec($ch);

        // If curl fail to exec we throw exception with error
        if ($result === FALSE) {
            throw new IOException(curl_strerror(curl_errno($ch)));
        }

        // Close
        curl_close($ch);

        // Close temp file
        if (isset($xmlFile)) {
            fclose($xmlFile);
        }

        return $result;
    }

}
