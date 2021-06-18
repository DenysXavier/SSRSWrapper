<?php

/**
 * Copyright 2021 Denys Xavier
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace DenysXavier\SSRSWrapper;

/**
 * URL handler responsible for calling the exposed URL from SQL Server Reporting Services
 */
class SSRSWrapper
{
    /** 
     * @var string $host 
     *      The host address where an instance of a Report Server application is located on your network
     */
    private string $host;

    /** 
     * @var string $virtualDirectory
     *      Name of the virtual directory that corresponds to the application that gets the request. The default value is <strong>ReportServer<strong>.
     */
    private string $virtualDirectory;

    /**
     * @var AuthenticationInterface $auth
     *      Method of authentication to the SQL Server Reporting Services
     */
    private AuthenticationInterface $auth;

    /**
     * Creates a new instance of SSRSWrapper
     * 
     * @param string $host             The host address where an instance of a Report Server application is located on your network
     * @param string $virtualDirectory Name of the virtual directory that corresponds to the application that gets the request
     */
    public function __construct(string $host, string $virtualDirectory = "ReportServer")
    {
        $this->host = $host;
        $this->virtualDirectory = $virtualDirectory;
    }

    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setVirtualDirectory(string $virtualDirectory): void
    {
        $this->virtualDirectory = $virtualDirectory;
    }

    public function getVirtualDirectory(): string
    {
        return $this->virtualDirectory;
    }

    public function setAuth(AuthenticationInterface $auth): void
    {
        $this->auth = $auth;
    }

    public function getAuth(): AuthenticationInterface
    {
        return $this->auth;
    }

    public function buildURL(Report $report)
    {
        return $this->host . '/' . $this->virtualDirectory . '?' . urlencode($report->getPath());
    }

    public function export(Report $report, string $filename, string $format = "PDF"): void
    {
        $config = [];

        $parameters = $report->getParams();
        $parameters['rs:Format'] = $format;

        $config[CURLOPT_URL] = $this->host . '/' . $this->virtualDirectory . '?' . urlencode($report->getPath()) . "&" . http_build_query($parameters);
        $fileHandler = fopen($filename, 'w');

        $this->auth->configure($config);

        $config[CURLOPT_RETURNTRANSFER] = true;
        $config[CURLOPT_FILE] = $fileHandler;

        $curlHandler = curl_init();
        curl_setopt_array($curlHandler, $config);

        curl_exec($curlHandler);

        fclose($fileHandler);
        curl_close($curlHandler);
    }

    public function download(Report $report, string $downloadName, string $format = "PDF"): void
    {
        $config = [];

        $parameters = $report->getParams();
        $parameters['rs:Format'] = $format;

        $config[CURLOPT_URL] = $this->host . '/' . $this->virtualDirectory . '?' . urlencode($report->getPath()) . "&" . http_build_query($parameters);

        $this->auth->configure($config);

        $streamFunction = function ($ch, $data) {
            echo $data;

            ob_flush();
            flush();
            return strlen($data);
        };

        $config[CURLOPT_WRITEFUNCTION] = $streamFunction;

        $curlHandler = curl_init();
        curl_setopt_array($curlHandler, $config);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');

        curl_exec($curlHandler);
        curl_close($curlHandler);
    }
}
