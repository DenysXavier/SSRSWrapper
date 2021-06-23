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

use Exception;

/**
 * URL handler responsible for calling the exposed URL from SQL Server Reporting Services.
 */
class SSRSWrapper
{
    /** 
     * @var string $host 
     *      The host address where an instance of a Report Server application is located on your network.
     */
    private string $host;

    /** 
     * @var string $virtualDirectory
     *      Name of the virtual directory that corresponds to the application that gets the request. The default value is <strong>ReportServer<strong>.
     */
    private string $virtualDirectory;

    /**
     * @var AuthenticationInterface $auth
     *      Method of authentication to the SQL Server Reporting Services.
     */
    private AuthenticationInterface $auth;

    /**
     * Creates a new instance of SSRSWrapper
     * 
     * @param string $host             The host address where an instance of a Report Server application is located on your network.
     * @param string $virtualDirectory Name of the virtual directory that corresponds to the application that gets the request.
     */
    public function __construct(string $host, string $virtualDirectory = "ReportServer")
    {
        $this->host = $host;
        $this->virtualDirectory = $virtualDirectory;
    }

    /** 
     * Set the host address where an instance of a Report Server application is located on your network.
     * 
     * @param string $host The host address where an instance of a Report Server application is located on your network.
     * 
     * @return void
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /**
     * Gets the host address where an instance of a Report Server application is located on your network.
     * 
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Sets the name of the virtual directory that corresponds to the application that gets the request.
     * 
     * @param string $virtualDirectory Name of the virtual directory that corresponds to the application that gets the request.
     * 
     * @return void
     */
    public function setVirtualDirectory(string $virtualDirectory): void
    {
        $this->virtualDirectory = $virtualDirectory;
    }

    /**
     * Gets the name of the virtual directory that corresponds to the application that gets the request.
     * 
     * @return string
     */
    public function getVirtualDirectory(): string
    {
        return $this->virtualDirectory;
    }

    /**
     * Sets the method of authentication to the SQL Server Reporting Services.
     * 
     * @param AuthenticationInterface $auth The method of authentication to the SQL Server Reporting Services, for example, an instance of NTLMAuth.
     * 
     * @return void
     */
    public function setAuth(AuthenticationInterface $auth): void
    {
        $this->auth = $auth;
    }

    /**
     * Gets the method of authentication to the SQL Server Reporting Services.
     * 
     * @return AuthenticationInterface
     */
    public function getAuth(): AuthenticationInterface
    {
        return $this->auth;
    }

    /**
     * Builds an URL for the report specific for this SSRS host and append extra arguments if they are provided.
     * 
     * @param Report $report         The report that will be called by the service.
     * @param array  $extraArguments An assossiative array that will be appended to the end of the URL as query string.
     * 
     * @return string
     */
    public function buildURL(Report $report, array $extraArguments = null): string
    {
        $server = $this->host . '/' . urlencode($this->virtualDirectory);
        $encodedReportPath = urlencode($report->getPath());
        $extraQueryString = $this->generateExtraQueryStrings(
            $report->getParams(),
            $extraArguments
        );

        return $server . '?' . $encodedReportPath . $extraQueryString;
    }

    /**
     * Generates extra query strings for buildURL method if needed.
     * 
     * @param (null|array)[] ...$map Any number of arguments comprised of associative arrays.
     * 
     * @return string
     */
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

    /**
     * Interacts with SQL Server Reporting Services and retrieves the report in a specific format. How the report will be handled depends on the export behavior provided.
     * 
     * @param Report                  $report         The report to be exported.
     * @param ExportBehaviorInterface $exportBehavior How the export will handle the request to retrieve the report, for example, a SaveOnDisk instance.
     * @param string                  $format         A string defining an output format for the report. The default value is PDF, but others options depends on the renderers available in SSRS. (@See https://docs.microsoft.com/en-us/sql/reporting-services/report-builder/export-reports-report-builder-and-ssrs?view=sql-server-ver15#ExportFormats)
     * 
     * @return void
     */
    public function export(
        Report $report,
        ExportBehaviorInterface $exportBehavior,
        string $format = "PDF"
    ): void {
        $config = [];

        $config[CURLOPT_URL] = $this->buildURL($report, ['rs:Format' => $format]);

        $this->setupAuthentication($config);

        $exportBehavior->setup($config);

        try {
            $this->tryToExecuteCURLResquest($config);
        } catch (Exception $e) {
            throw $e;
        } finally {
            $exportBehavior->dispose();
        }
    }

    /**
     * Sets up authentication if any authentication method is set in the SSRSWrapper instance.
     * 
     * @param array $options The cURL configuration array itself.
     * 
     * @return void
     */
    private function setupAuthentication(array &$options): void
    {
        if (isset($this->auth)) {
            $this->auth->configure($config);
        }
    }

    /**
     * Try to execute a request using a cURL resource. Throws an Exception if a problem occurs.
     * 
     * @param array $options Options to be used by the cURL handler.
     * 
     * @return void
     */
    private function tryToExecuteCURLResquest($options): void
    {
        $curlHandler = curl_init();
        curl_setopt_array($curlHandler, $options);
        curl_exec($curlHandler);

        if (curl_errno($curlHandler) > 0) {
            $error['code'] = curl_errno($curlHandler);
            $error['message'] = curl_error($curlHandler);
        }

        curl_close($curlHandler);

        if (isset($error)) {
            throw new Exception($error['message'], $error['code']);
        }
    }
}
