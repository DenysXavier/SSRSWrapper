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

    /** 
     * Set the host address where an instance of a Report Server application is located on your network
     * 
     * @param string $host The host address where an instance of a Report Server application is located on your network
     * 
     * @return void
     */
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

    public function buildURL(Report $report, array $extraArguments = null): string
    {
        $server = $this->host . '/' . urlencode($this->virtualDirectory);
        $encodedReportPath = urlencode($report->getPath());
        $extraQueryString = $this->generateExtraQueryStrings($report->getParams(), $extraArguments);

        return $server . '?' . $encodedReportPath . $extraQueryString;
    }

    private function generateExtraQueryStrings(?array ...$map): string
    {
        $values = [];
        $extraQueryString = '';

        foreach ($map as $element) {
            if (is_array($element)) {
                $values = array_merge($values, $element);
            }
        }

        if (count($values) > 0) {
            $extraQueryString = '&' . http_build_query($values);
        }

        return $extraQueryString;
    }

    public function export(Report $report, ExportBehaviorInterface $exportBehavior, string $format = "PDF"): void
    {
        $config = [];

        $parameters['rs:Format'] = $format;

        $config[CURLOPT_URL] = $this->buildURL($report, $parameters);

        $this->auth->configure($config);

        $exportBehavior->setup($config);

        $curlHandler = curl_init();
        curl_setopt_array($curlHandler, $config);
        curl_exec($curlHandler);

        $exportBehavior->dispose();

        curl_close($curlHandler);
    }

    public function download(Report $report, string $downloadName, string $format = "PDF"): void
    {
        $config = [];

        $parameters['rs:Format'] = $format;

        $config[CURLOPT_URL] = $this->buildURL($report, $parameters);

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
