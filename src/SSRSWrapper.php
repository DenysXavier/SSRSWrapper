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
 * URL handler responsible for calling the exposed URL from SQL Server Reporting Service
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

    public function setHost(string $host)
    {
        $this->host = $host;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setVirtualDirectory(string $virtualDirectory)
    {
        $this->virtualDirectory = $virtualDirectory;
    }

    public function getVirtualDirectory(): string
    {
        return $this->virtualDirectory;
    }
}
